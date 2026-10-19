#Requires -Version 5.1
<#
.SYNOPSIS
    将 plugin/<name> 目录以 git subtree 方式推送到各插件自己的独立仓库。

.DESCRIPTION
    不使用 submodule：plugin/<name> 在主仓库中仍是普通目录、正常被跟踪；
    本脚本用 git subtree split 把「仅改动该目录」的提交抽成一条独立历史，
    再推送到各自的远程仓库，因此每个插件仓库拥有自己的提交记录。

    为避免每次全量重建历史，脚本会把上次 split 结果缓存为本地分支
    plugin-split/<name>，并在状态文件中记录拆分所依据的源提交；
    下次仅从该源提交增量拆分（--onto）。

.PARAMETER Plugins
    要处理的插件标识列表，如 xbDevice xbUser。
    不指定时默认进入交互式选择：列出 plugin 下的插件目录，
    可按序号（如 1 3）或名称（如 xbDevice）多选，输入 a 处理全部，直接回车取消。

.PARAMETER All
    不进入交互式选择，直接处理 plugin 下全部目录（脚本化运行时用）。

.PARAMETER RemoteUrl
    远程仓库地址模板，{name} 会被替换为插件标识。
    默认：git@gitee.com:xbcode-plugin/{name}.git

.PARAMETER Branch
    推送到的远程分支，默认 main。
    注意：Gitee 建仓时若勾选了 README 初始化，远程会先有一条与本仓库无关的
    初始提交，此时首次推送会被判为分叉而拒绝；这种情况下需用 -Force 覆盖。

.PARAMETER Ref
    作为拆分源的本仓库提交，默认 HEAD。

.PARAMETER Force
    以 --force 推送（远程已有历史且与本地重写后的历史分叉时使用）。
    注意：--force 只能覆盖已有历史，无法解决远程仓库不存在的问题。

.PARAMETER NoVerify
    跳过推送前的远程仓库预检。默认会先探测远程仓库是否存在且可访问：
    不存在或不可达时跳过该插件（不影响同批其他插件），而不是中断整批处理。

.PARAMETER NoPush
    只做拆分、不推送，用于预演（会打印各插件的 split 提交与提交数）。

.PARAMETER Clean
    清理本地的 plugin-split/* 缓存分支与状态文件后退出，不执行拆分与推送。

.EXAMPLE
    # 预演：先看拆分结果，不推送
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/sync-plugin-repos.ps1 -NoPush

    # 交互式选择要推送的插件（不传 -Plugins 即可）
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/sync-plugin-repos.ps1

    # 推送单个插件
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/sync-plugin-repos.ps1 -Plugins xbDevice

    # 脚本化运行：推送全部插件，不进入交互式选择
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/sync-plugin-repos.ps1 -All

    # 推送全部插件，并向已存在的分叉仓库强制推送
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/sync-plugin-repos.ps1 -All -Force

    # 地址已确认可靠、想省掉预检的网络往返
    powershell -ExecutionPolicy Bypass -File plugin/xbCode/script/sync-plugin-repos.ps1 -All -NoVerify
#>
[CmdletBinding(PositionalBinding = $false)]
param(
    [string[]] $Plugins = @(),
    [string]   $RemoteUrl = 'git@gitee.com:xbcode-plugin/{name}.git',
    [string]   $Branch = 'main',
    [string]   $Ref = 'HEAD',
    [switch]   $Force,
    [switch]   $NoVerify,
    [switch]   $NoPush,
    [switch]   $All,
    [switch]   $Clean
)

$ErrorActionPreference = 'Stop'

function Invoke-Git {
    param(
        [Parameter(Mandatory = $true)] [string[]] $GitArgs,
        [switch] $AllowFail
    )
    # git 的正常信息（如 split 进度）也写在 stderr，而全局的
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

function Get-Sha {
    param([string] $Repo, [string] $Rev)
    $result = Invoke-Git -GitArgs @('-C', $Repo, 'rev-parse', '--verify', '--quiet', $Rev) -AllowFail
    if ($result.Code -ne 0) { return '' }
    return $result.Output
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

# ---------- 定位主仓库根目录 ----------
$repo = (Invoke-Git -GitArgs @('rev-parse', '--show-toplevel')).Output
if ([string]::IsNullOrWhiteSpace($repo)) {
    throw '未能定位当前 git 仓库根目录'
}
$pluginRoot = Join-Path $repo 'plugin'
if (-not (Test-Path $pluginRoot)) {
    throw "未找到插件目录：$pluginRoot"
}

$stateDir = Join-Path (Join-Path $repo '.git') 'plugin-split-state'
$splitPrefix = 'plugin-split'

# ---------- 清理模式 ----------
if ($Clean) {
    $cached = (Invoke-Git -GitArgs @('-C', $repo, 'for-each-ref', '--format=%(refname:short)', "refs/heads/$splitPrefix/") -AllowFail)
    foreach ($ref in @($cached.Output -split "`n" | Where-Object { $_.Trim() })) {
        Invoke-Git -GitArgs @('-C', $repo, 'branch', '-D', $ref.Trim()) | Out-Null
        Write-Host ("已删除缓存分支：{0}" -f $ref.Trim())
    }
    if (Test-Path $stateDir) {
        Remove-Item -LiteralPath $stateDir -Recurse -Force
        Write-Host "已删除拆分状态目录：$stateDir"
    }
    Write-Host "清理完成。" -ForegroundColor Green
    return
}

# ---------- 确定插件清单 ----------
$Plugins = Split-NameList -Values $Plugins
if ($Plugins.Count -eq 0) {
    $available = @(Get-ChildItem -LiteralPath $pluginRoot -Directory | Select-Object -ExpandProperty Name)
    if ($All) {
        $Plugins = $available
    }
    # 交互式环境才询问；非交互环境（如管道重定向）无法读入选择，直接报错退出，
    # 避免「未指定插件」被静默解释成处理全部。
    elseif ([Environment]::UserInteractive -and -not [Console]::IsInputRedirected) {
        $Plugins = Select-PluginsInteractive -Available $available
        if ($Plugins.Count -eq 0) {
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

if (-not (Test-Path $stateDir)) {
    New-Item -ItemType Directory -Path $stateDir -Force | Out-Null
}

$srcSha = Get-Sha -Repo $repo -Rev $Ref
if (-not $srcSha) {
    throw "拆分源提交不存在：$Ref"
}

Write-Host "主仓库：$repo"
Write-Host "拆分源：$Ref ($srcSha)"
Write-Host "远程地址模板：$RemoteUrl"
Write-Host "目标分支：$Branch"
Write-Host ""

$summary = @()

foreach ($name in $Plugins) {
    $prefix = "plugin/$name"
    $prefixPath = Join-Path $repo $prefix
    if (-not (Test-Path $prefixPath)) {
        Write-Host ("[{0}] 跳过：目录不存在 {1}" -f $name, $prefix) -ForegroundColor Yellow
        $summary += [pscustomobject]@{ 插件 = $name; 提交数 = ''; 拆分提交 = ''; 结果 = '目录不存在' }
        continue
    }

    $splitRef = "$splitPrefix/$name"
    $tempRef = "$splitPrefix-tmp/$name"
    $stateFile = Join-Path $stateDir "$name.txt"

    try {
        # 残留临时分支先删除
        Invoke-Git -GitArgs @('-C', $repo, 'branch', '-D', $tempRef) -AllowFail | Out-Null

        # 判断能否增量拆分：缓存分支存在 且 上次拆分源是当前源的祖先
        $base = ''
        if (Test-Path $stateFile) {
            $parts = @((Get-Content -LiteralPath $stateFile -Raw).Trim() -split '\s+')
            if ($parts.Count -ge 2) {
                $lastSplit = $parts[0]
                $lastSource = $parts[1]
                $hasSplit = (Get-Sha -Repo $repo -Rev "refs/heads/$splitRef") -eq $lastSplit
                $isAncestor = $false
                if ($hasSplit) {
                    $ancestorCheck = Invoke-Git -GitArgs @('-C', $repo, 'merge-base', '--is-ancestor', $lastSource, $Ref) -AllowFail
                    $isAncestor = $ancestorCheck.Code -eq 0
                }
                if ($hasSplit -and $isAncestor) { $base = $lastSplit }
            }
        }

        $mode = if ($base) { '增量' } else { '全量' }
        Write-Host ("[{0}] 开始拆分（{1}）..." -f $name, $mode) -ForegroundColor Cyan

        $splitArgs = @('-C', $repo, 'subtree', 'split', "--prefix=$prefix")
        if ($base) { $splitArgs += "--onto=$base" }
        $splitArgs += @($Ref, '-b', $tempRef)
        Invoke-Git -GitArgs $splitArgs | Out-Null

        $newSplit = Get-Sha -Repo $repo -Rev "refs/heads/$tempRef"
        if (-not $newSplit) {
            throw '拆分未产生提交，请确认该目录在主仓库历史中存在过'
        }
        Invoke-Git -GitArgs @('-C', $repo, 'update-ref', "refs/heads/$splitRef", $newSplit) | Out-Null
        Invoke-Git -GitArgs @('-C', $repo, 'branch', '-D', $tempRef) | Out-Null
        Set-Content -LiteralPath $stateFile -Value ("{0} {1}" -f $newSplit, $srcSha) -Encoding ASCII

        $commitCount = (Invoke-Git -GitArgs @('-C', $repo, 'rev-list', '--count', $splitRef)).Output

        if ($NoPush) {
            Write-Host ("[{0}] 拆分完成：{1} 个提交，tip {2}（未推送）" -f $name, $commitCount, $newSplit.Substring(0, 8)) -ForegroundColor DarkGray
            $summary += [pscustomobject]@{ 插件 = $name; 提交数 = $commitCount; 拆分提交 = $newSplit.Substring(0, 8); 结果 = '已拆分（未推送）' }
            continue
        }

        # ---------- 远程仓库 ----------
        $remote = "plugin-$name"
        $url = $RemoteUrl.Replace('{name}', $name)
        $existingUrl = (Invoke-Git -GitArgs @('-C', $repo, 'remote', 'get-url', $remote) -AllowFail)
        if ($existingUrl.Code -ne 0) {
            Invoke-Git -GitArgs @('-C', $repo, 'remote', 'add', $remote, $url) | Out-Null
            Write-Host ("[{0}] 已添加远程：{1} -> {2}" -f $name, $remote, $url)
        }
        else {
            Write-Host ("[{0}] 使用现有远程：{1} -> {2}" -f $name, $remote, $existingUrl.Output)
        }

        # ---------- 预检：远程仓库是否存在且可访问 ----------
        # git push 无法在远程自动建仓，仓库缺失时 --force 也无效，
        # 故先探测；不可达则跳过该插件，避免中断整批处理。
        # 注意：不带 --exit-code 时，空仓库返回 0、不存在返回 128；
        # 加了 --exit-code 反而会把「空仓库（无任何引用）」判成 2，
        # 而那正是首次推送的正常场景，故此处不加该参数。
        if (-not $NoVerify) {
            $probe = Invoke-Git -GitArgs @('-C', $repo, 'ls-remote', $remote) -AllowFail
            if ($probe.Code -ne 0) {
                $reason = if ($probe.Code -eq 128) { '远程仓库不存在或不可访问' } else { "预检异常（退出码 $($probe.Code)）" }
                Write-Host ("[{0}] 跳过：{1} -> {2}" -f $name, $reason, $url) -ForegroundColor Yellow
                Write-Host ("       请先在 Gitee 创建【空仓库】(不要勾选 Readme 初始化)，再重跑本脚本。") -ForegroundColor DarkYellow
                $detail = ($probe.Output -split "`n" | Where-Object { $_ -match 'fatal|Repository not found|access rights|Permission denied|Could not read' } | Select-Object -First 1)
                if ($detail) { Write-Host ("       git 提示：{0}" -f $detail.Trim()) -ForegroundColor DarkYellow }
                $summary += [pscustomobject]@{ 插件 = $name; 提交数 = $commitCount; 拆分提交 = $newSplit.Substring(0, 8); 结果 = '跳过：远程仓库不存在' }
                continue
            }
        }

        $pushArgs = @('-C', $repo, 'push')
        if ($Force) { $pushArgs += '--force' }
        $pushArgs += @($remote, "refs/heads/$splitRef`:refs/heads/$Branch")
        Write-Host ("[{0}] 推送 {1} -> {2}/{3} ..." -f $name, $splitRef, $remote, $Branch) -ForegroundColor Cyan

        $push = Invoke-Git -GitArgs $pushArgs -AllowFail
        if ($push.Code -ne 0) {
            if ($push.Output -match 'non-fast-forward|fetch first|rejected') {
                throw ("推送被拒绝（远程已有分叉历史）。确认要覆盖远程分支时请加 -Force。原始输出：`n{0}" -f $push.Output)
            }
            throw ("推送失败：`n{0}" -f $push.Output)
        }

        $pushed = '已推送'
        if ($push.Output -match 'Everything up-to-date') { $pushed = '无变化' }
        Write-Host ("[{0}] 完成：{1} 个提交 -> {2}/{3}" -f $name, $commitCount, $remote, $Branch) -ForegroundColor Green
        $summary += [pscustomobject]@{ 插件 = $name; 提交数 = $commitCount; 拆分提交 = $newSplit.Substring(0, 8); 结果 = $pushed }
    }
    catch {
        Write-Host ("[{0}] 失败：{1}" -f $name, $_.Exception.Message) -ForegroundColor Red
        Invoke-Git -GitArgs @('-C', $repo, 'branch', '-D', $tempRef) -AllowFail | Out-Null
        # 汇总里取最能说明原因的一行（优先含 fatal/error 的行），
        # 否则首行往往只是「推送失败：」这类无信息的前缀。
        $lines = @($_.Exception.Message -split "`r?`n" | Where-Object { $_.Trim() })
        $failLine = ($lines | Where-Object { $_ -match 'fatal|error|rejected|denied|not found' } | Select-Object -First 1)
        if (-not $failLine) { $failLine = $lines | Select-Object -First 1 }
        $summary += [pscustomobject]@{ 插件 = $name; 提交数 = ''; 拆分提交 = ''; 结果 = "失败：$($failLine.Trim())" }
    }
}

Write-Host ""
Write-Host "==================== 汇总 ====================" -ForegroundColor Cyan
$summary | Format-Table -AutoSize | Out-String | Write-Host

$failed = @($summary | Where-Object { $_.结果 -like '失败*' })
if ($failed.Count -gt 0) {
    Write-Host ("有 {0} 个插件处理失败，请检查上方输出。" -f $failed.Count) -ForegroundColor Red
    exit 1
}
Write-Host "全部处理完成。" -ForegroundColor Green
