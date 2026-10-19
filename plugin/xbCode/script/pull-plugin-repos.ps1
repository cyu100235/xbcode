#Requires -Version 5.1
<#
.SYNOPSIS
    从各插件的独立仓库重新拉取最新代码，覆盖本地 plugin/<name> 目录。

.DESCRIPTION
    扫描 plugin 目录下所有「包含 plugins.json 清单文件」的子目录（以目录名作为仓库名），
    逐个执行：探测远程仓库 -> 克隆最新代码到临时目录 -> 删除本地目录 -> 用最新代码填充。

    远程地址优先取主仓库中已配置的 remote「plugin-<name>」地址，
    没有该 remote 时回退到 -RemoteUrl 模板（{name} 替换为插件目录名）。
    克隆出来的临时目录会去掉 .git 再放入 plugin/，与主仓库「普通目录」的约定保持一致
    （即 plugin/<name> 不作为 submodule、也不作为嵌套仓库）。

    说明：脚本先克隆后删除，克隆失败或远程仓库为空时不会破坏本地目录。

    ⚠ 危险操作：会删除本地 plugin/<name> 目录，未推送到插件仓库的本地改动会丢失。
    ⚠ plugin/xbCode 目录包含本脚本自身，处理它会导致本地脚本被远程版本覆盖。

.PARAMETER Plugins
    只处理指定插件（目录名），如 xbDevice xbUser。
    不指定时默认进入交互式选择：列出「含 plugins.json」的插件目录，
    可按序号（如 1 3）或名称（如 xbDevice）多选，输入 a 处理全部，直接回车取消。

.PARAMETER All
    不进入交互式选择，直接处理 plugin 下全部含 plugins.json 的目录（脚本化运行时用）。

.PARAMETER Exclude
    排除的插件目录名（如 xbCode），可与 -Plugins 组合使用。

.PARAMETER RemoteUrl
    远程仓库地址模板，{name} 会被替换为插件目录名。
    不指定时优先使用主仓库里已有的 plugin-<name> remote 地址，
    否则回退到 git@gitee.com:xbcode-plugin/{name}.git。

.PARAMETER Branch
    拉取的分支，默认 main；传空字符串表示使用远程默认分支。

.PARAMETER DryRun
    只做扫描与远程仓库探测，打印将要执行的动作，不克隆、不删除。

.EXAMPLE
    # 预演：只看哪些插件可拉取
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/pull-plugin-repos.ps1 -DryRun

    # 交互式选择要拉取的插件（不传 -Plugins 即可）
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/pull-plugin-repos.ps1

    # 只拉取两个插件
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/pull-plugin-repos.ps1 -Plugins xbDevice,xbUser

    # 脚本化运行：拉取全部插件，不进入交互式选择
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/pull-plugin-repos.ps1 -All

    # 拉取全部插件，但排除框架自身（避免覆盖本脚本）
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/pull-plugin-repos.ps1 -All -Exclude xbCode
#>
[CmdletBinding(PositionalBinding = $false)]
param(
    [string[]] $Plugins = @(),
    [string[]] $Exclude = @(),
    [string]   $RemoteUrl = '',
    [string]   $Branch = 'main',
    [switch]   $All,
    [switch]   $DryRun
)

$ErrorActionPreference = 'Stop'

$DefaultRemoteUrl = 'git@gitee.com:xbcode-plugin/{name}.git'
$ManifestName = 'plugins.json'

function Invoke-Git {
    param(
        [Parameter(Mandatory = $true)] [string[]] $GitArgs,
        [switch] $AllowFail
    )
    # git 的正常信息（如 clone 进度）也写在 stderr，而全局的
    # ErrorActionPreference='Stop' 会把它当成终止性错误，故此处临时放宽。
    $previous = $ErrorActionPreference
    $ErrorActionPreference = 'Continue'
    try {
        $output = & git @GitArgs 2>&1 | Out-String
        $code = $LASTEXITCODE
    }
    finally {
        $ErrorActionPreference = $previous
    }
    if ($code -ne 0 -and -not $AllowFail) {
        throw ("git {0} 执行失败：`n{1}" -f ($GitArgs -join ' '), $output.Trim())
    }
    return [pscustomobject]@{ Output = $output.Trim(); Code = $code }
}

function Split-NameList {
    param([string[]] $Values)
    return @($Values |
        ForEach-Object { $_ -split ',' } |
        ForEach-Object { $_ -split '\s+' } |
        Where-Object { $_.Trim() } |
        ForEach-Object { $_.Trim() } |
        Select-Object -Unique)
}

# 交互式选择插件：支持按序号（1 3）或名称（xbDevice）多选，a=全部，回车=取消。
function Select-PluginsInteractive {
    param([string[]] $Available)

    Write-Host "可用插件：" -ForegroundColor Cyan
    for ($i = 0; $i -lt $Available.Count; $i++) {
        Write-Host ("  [{0}] {1}" -f ($i + 1), $Available[$i])
    }
    Write-Host "请输入要处理的插件（序号或名称，空格/逗号分隔；a=全部；直接回车取消）：" -ForegroundColor Cyan

    while ($true) {
        $answer = (Read-Host "选择").Trim()
        if (-not $answer) { return @() }
        if ($answer -ieq 'a' -or $answer -ieq 'all') { return $Available }

        $picked = @()
        $invalid = @()
        foreach ($token in @(Split-NameList -Values @($answer))) {
            $index = 0
            if ([int]::TryParse($token, [ref]$index)) {
                if ($index -ge 1 -and $index -le $Available.Count) {
                    $picked += $Available[$index - 1]
                    continue
                }
                $invalid += $token
                continue
            }
            $match = @($Available | Where-Object { $_ -ieq $token })
            if ($match.Count -gt 0) { $picked += $match[0] } else { $invalid += $token }
        }

        if ($invalid.Count -gt 0) {
            Write-Host ("无法识别的选择：{0}，请重新输入。" -f ($invalid -join ', ')) -ForegroundColor Yellow
            continue
        }
        if ($picked.Count -eq 0) {
            Write-Host "未选择任何插件，请重新输入，或直接回车取消。" -ForegroundColor Yellow
            continue
        }
        return @($picked | Select-Object -Unique)
    }
}

# ---------- 定位主仓库与插件目录 ----------
$repo = (Invoke-Git -GitArgs @('rev-parse', '--show-toplevel')).Output
if ([string]::IsNullOrWhiteSpace($repo)) {
    throw '未能定位当前 git 仓库根目录'
}
$gitDir = (Invoke-Git -GitArgs @('-C', $repo, 'rev-parse', '--absolute-git-dir')).Output
if ([string]::IsNullOrWhiteSpace($gitDir)) {
    throw '未能定位 git 目录'
}
$pluginRoot = Join-Path $repo 'plugin'
if (-not (Test-Path -LiteralPath $pluginRoot)) {
    throw "未找到插件目录：$pluginRoot"
}

function Resolve-RemoteUrl {
    param([string] $Name)
    if ($RemoteUrl) { return $RemoteUrl.Replace('{name}', $Name) }
    $existing = Invoke-Git -GitArgs @('-C', $repo, 'remote', 'get-url', "plugin-$Name") -AllowFail
    if ($existing.Code -eq 0 -and -not [string]::IsNullOrWhiteSpace($existing.Output)) {
        return $existing.Output
    }
    return $DefaultRemoteUrl.Replace('{name}', $Name)
}

# ---------- 确定插件清单 ----------
# 可用插件 = plugin 下含 plugins.json 的目录（与后续处理条件一致）。
function Get-AvailablePlugins {
    return @(Get-ChildItem -LiteralPath $pluginRoot -Directory -Force |
        Where-Object { Test-Path -LiteralPath (Join-Path $_.FullName $ManifestName) -PathType Leaf } |
        Select-Object -ExpandProperty Name)
}

$candidates = Split-NameList -Values $Plugins
if ($candidates.Count -eq 0) {
    $available = Get-AvailablePlugins
    if ($All) {
        $candidates = $available
    }
    # 交互式环境才询问；非交互环境（如管道重定向）无法读入选择，直接报错退出，
    # 避免「未指定插件」被静默解释成处理全部。
    elseif ([Environment]::UserInteractive -and -not [Console]::IsInputRedirected) {
        $candidates = Select-PluginsInteractive -Available $available
        if ($candidates.Count -eq 0) {
            Write-Host "已取消，未处理任何插件。" -ForegroundColor Yellow
            return
        }
    }
    else {
        Write-Host "当前为非交互式环境，无法询问要处理的插件。" -ForegroundColor Red
        Write-Host "请改用 -Plugins <名称> 指定单个/多个插件，或用 -All 处理全部。" -ForegroundColor Yellow
        exit 1
    }
}
$excludes = Split-NameList -Values $Exclude

Write-Host "主仓库：$repo"
Write-Host "插件目录：$pluginRoot"
Write-Host ("目标分支：{0}" -f $(if ($Branch) { $Branch } else { '(远程默认分支)' }))
if ($DryRun) { Write-Host '模式：预演（不克隆、不删除）' -ForegroundColor Yellow }
if ($candidates -contains 'xbCode' -and $excludes -notcontains 'xbCode') {
    Write-Host '注意：plugin/xbCode 内含本脚本自身，处理后会以远程版本覆盖本地脚本。' -ForegroundColor DarkYellow
}
Write-Host ""

$tmpRoot = Join-Path $gitDir 'plugin-pull-tmp'
$summary = @()

foreach ($name in $candidates) {
    if ($excludes -contains $name) { continue }

    $dir = Join-Path $pluginRoot $name
    $manifest = Join-Path $dir $ManifestName

    if (-not (Test-Path -LiteralPath $dir -PathType Container)) {
        Write-Host ("[{0}] 跳过：目录不存在 {1}" -f $name, $dir) -ForegroundColor Yellow
        $summary += [pscustomobject]@{ 插件 = $name; 远程仓库 = ''; 结果 = '跳过：目录不存在' }
        continue
    }
    if (-not (Test-Path -LiteralPath $manifest -PathType Leaf)) {
        Write-Host ("[{0}] 跳过：未找到 {1}，不是插件目录" -f $name, $ManifestName) -ForegroundColor Yellow
        $summary += [pscustomobject]@{ 插件 = $name; 远程仓库 = ''; 结果 = "跳过：缺少 $ManifestName" }
        continue
    }

    $url = Resolve-RemoteUrl -Name $name

    # ---------- 检测远程仓库存在且可访问 ----------
    # 不带 --exit-code：空仓库返回 0、不存在返回 128，
    # 而「空仓库」在探测阶段是合法的，只是后续会因没有内容而跳过。
    $probe = Invoke-Git -GitArgs @('ls-remote', $url) -AllowFail
    if ($probe.Code -ne 0) {
        $reason = if ($probe.Code -eq 128) { '远程仓库不存在或不可访问' } else { "探测异常（退出码 $($probe.Code)）" }
        Write-Host ("[{0}] 跳过：{1} -> {2}" -f $name, $reason, $url) -ForegroundColor Yellow
        $detail = ($probe.Output -split "`n" |
            Where-Object { $_ -match 'fatal|not found|Permission denied|Could not read|access rights' } |
            Select-Object -First 1)
        if ($detail) { Write-Host ("       git 提示：{0}" -f $detail.Trim()) -ForegroundColor DarkYellow }
        $summary += [pscustomobject]@{ 插件 = $name; 远程仓库 = $url; 结果 = "跳过：$reason" }
        continue
    }

    if ($DryRun) {
        Write-Host ("[{0}] 待更新：删除 {1} 后从 {2} 拉取最新代码" -f $name, $name, $url) -ForegroundColor Cyan
        $summary += [pscustomobject]@{ 插件 = $name; 远程仓库 = $url; 结果 = '预演：待更新' }
        continue
    }

    $tmp = Join-Path $tmpRoot $name
    try {
        # ---------- 先克隆到临时目录 ----------
        if (Test-Path -LiteralPath $tmp) { Remove-Item -LiteralPath $tmp -Recurse -Force }
        New-Item -ItemType Directory -Path $tmpRoot -Force | Out-Null

        $cloneArgs = @('clone', '--depth', '1', '--quiet')
        if ($Branch) { $cloneArgs += @('--branch', $Branch) }
        $cloneArgs += @('--', $url, $tmp)

        Write-Host ("[{0}] 拉取最新代码：{1}" -f $name, $url) -ForegroundColor Cyan
        Invoke-Git -GitArgs $cloneArgs | Out-Null

        $items = @(Get-ChildItem -LiteralPath $tmp -Force | Where-Object { $_.Name -ne '.git' })
        if ($items.Count -eq 0) {
            Write-Host ("[{0}] 跳过：远程仓库为空（未删除本地目录）" -f $name) -ForegroundColor Yellow
            $summary += [pscustomobject]@{ 插件 = $name; 远程仓库 = $url; 结果 = '跳过：远程仓库为空' }
            continue
        }

        # ---------- 删除本地目录，用最新代码填充 ----------
        Remove-Item -LiteralPath $dir -Recurse -Force
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        foreach ($item in $items) {
            Move-Item -LiteralPath $item.FullName -Destination $dir -Force
        }

        Write-Host ("[{0}] 完成：{1} 个顶层条目已更新" -f $name, $items.Count) -ForegroundColor Green
        $summary += [pscustomobject]@{ 插件 = $name; 远程仓库 = $url; 结果 = '已更新' }
    }
    catch {
        Write-Host ("[{0}] 失败：{1}" -f $name, $_.Exception.Message) -ForegroundColor Red
        $lines = @($_.Exception.Message -split "`r?`n" | Where-Object { $_.Trim() })
        $failLine = ($lines | Where-Object { $_ -match 'fatal|error|denied|not found' } | Select-Object -First 1)
        if (-not $failLine) { $failLine = $lines | Select-Object -First 1 }
        $summary += [pscustomobject]@{ 插件 = $name; 远程仓库 = $url; 结果 = "失败：$($failLine.Trim())" }
    }
    finally {
        if (Test-Path -LiteralPath $tmp) { Remove-Item -LiteralPath $tmp -Recurse -Force }
    }
}

if ((-not $DryRun) -and (Test-Path -LiteralPath $tmpRoot) -and
    (@(Get-ChildItem -LiteralPath $tmpRoot -Force).Count -eq 0)) {
    Remove-Item -LiteralPath $tmpRoot -Recurse -Force
}

Write-Host ""
Write-Host "==================== 汇总 ====================" -ForegroundColor Cyan
if ($summary.Count -eq 0) {
    Write-Host "没有匹配的插件目录。" -ForegroundColor Yellow
}
else {
    $summary | Format-Table -AutoSize | Out-String | Write-Host
}

$failed = @($summary | Where-Object { $_.结果 -like '失败*' })
if ($failed.Count -gt 0) {
    Write-Host ("有 {0} 个插件处理失败，请检查上方输出。" -f $failed.Count) -ForegroundColor Red
    exit 1
}
Write-Host "全部处理完成。" -ForegroundColor Green
