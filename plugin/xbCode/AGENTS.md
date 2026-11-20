## 修改范围（硬约束）

- **只允许改 `plugin/` 下的代码**。项目根目录的 `config/`、`app/`、`support/`、`public/`、`nginx.conf`、`.env` 等属于框架与项目骨架（随官方模板升级），**不得改动**。
- 需要调整框架配置或增加启动逻辑时，用插件自身能力注入：
  - 启动期覆盖配置 → `bootstrap/` 下实现 + `data/` 下放覆盖片段，用 `Config::load($dir, $excludes)` 合并（只需声明要覆盖的字段）。
  - 参考已实现的 [CacheIsolation](file:///d:/dev/project/device-management/plugin/xbCode/bootstrap/CacheIsolation.php)（按实例端口隔离缓存键，覆盖片段放在 `data/cache-isolation`）。
- 覆盖片段目录必须含 `app.php`（框架 `Config::loadFromDir` 的扫描前提；多级目录下还需 `enable` 为真），根级覆盖时用 exclude 跳过 `app`，避免覆盖项目自身的 `app` 配置。
- 可早于 bootstrap 执行的注入点：插件 `config/autoload.php` 的 `files`（`support/bootstrap.php` 会在所有 bootstrap 类之前 include），适合「必须先于连接创建」的配置改写。
- 新增插件时 `config/redis.php` 必须带 `'prefix' => FrameUtil::cacheNamespace()`（新建插件模板 `data/plugin/config/redis.tpl` 已含），否则该插件的 `redis.plugin.<插件名>.default` 连接会绕过按端口的环境隔离。

## 核心插件边界（硬约束）

核心插件仅 `xbCode`、`xbCrontab`、`xbDeveloper`、`xbUpload` 四个，属于平台基座。**禁止业务插件把业务代码写进核心插件**：

- 依赖方向单向：业务插件可 `use plugin\xbCode\...`，核心插件不得出现业务插件的标识、命名空间、路由、表名、配置分组或业务语义（含注释与示例，示例统一用 `{插件标识}` 占位）。
- 扩展走声明式：业务插件在自身 `config/xbcode.php` 声明 `toolbar` / `workbench` / `components` / `todo` / `todo_quiet` / `redirect` / `home` / `icons`，核心只做通用汇总。
- 注入走监听注册：跨插件行为由使用方在自己的 `bootstrap/` 或 `config/middleware.php` 注册；中间件可用框架原生 `plugin.<插件>.<模块>` 键为其他插件挂载（如用户插件用 `'plugin.xbUpload.api'` 为储存插件挂载用户端鉴权）。
- 可选能力插件（字典 `xbDict`、SaaS `xbSaas` 等）允许保留 `class_exists` / `installed()` 形式的集成守卫，但不得承载业务逻辑。
- 发现侵入时把逻辑搬回业务插件，不要在核心保留 `class_exists` 之类的业务兜底逻辑。
- 注释 / 示例 / 脚手架模板同样算侵入：业务插件名、业务路由、业务字段名一律换成 `{插件标识}`、`xbDemo`、`${uniq}` 等占位。
- 自检：在项目根目录执行 `Get-ChildItem -Path plugin\xbCode,plugin\xbCrontab,plugin\xbDeveloper,plugin\xbUpload -Recurse -File -Include *.php,*.vue,*.tpl,*.md | Where-Object { $_.FullName -notmatch '\\public\\|\\data\\amis-' } | Select-String -Pattern 'xb(Device|User|Invoice|Withdraw|Movie|AppVersion|UserRealName|DevDoc|Vip|Order)\b'`，结果应为空（`xbDict` / `xbSaas` 守卫除外）。详见项目根目录 `AGENTS.md` 的「核心插件边界」。

## 禁止修改核心插件（硬约束）

修改插件代码时，**禁止**修改 `plugin/xbCode`、`plugin/xbCrontab`、`plugin/xbDeveloper`、`plugin/xbUpload` 这四个核心插件的代码：

- 每个插件应在自身目录内实现自己的功能，**禁止跨插件实现功能**——不得把自身业务代码写进核心插件，也不得通过修改其他插件的代码来实现本插件的功能。
- 对核心的扩展一律通过上一节的声明式扩展点与注册机制完成；现有扩展点无法满足时，优先在本插件内实现（`config/`、`bootstrap/`、中间件、插槽等），确需改动核心时先与用户确认。

## 调试产物与验证

- 调试产物（截图、日志等）统一存放在项目根目录下的 `runtime` 目录中自行新建的子目录内，不要散落在其他位置。
- 测试 amis schema 规则时，先复制一份 `plugin/xbCode/data/amis-6.13.0` 到项目根目录下的 `runtime` 目录下再改，不要直接改动原目录。
- 调试时生成的测试文件、截图文件，在调试完成后必须清理掉（删除 `runtime` 下产生的临时调试产物及项目内临时的测试脚本/文件）。
- 每次实现任务完成后，必须自行调试并验证，确保功能达到要求后才算完成，不必等用户提示。

## 插件创建 / 安装 / 卸载

相关命令类位于 `plugin/xbDeveloper/command/`，统一通过项目根目录的 webman 控制台执行（`php webman <命令>`）。

### 创建插件 PluginCreate

- 命令：`php webman xb-plugin:create`
- 参数选项：
  - `--title` 插件标题（2-20 字）
  - `--name` 插件标识（2-20 字，字母开头，仅字母+数字）
  - `--author` 开发者名称（2-10 字符）
  - `--desc` 一句话描述（3-35 字）
  - `--gradient` 是否生成渐变色预览图，`y`/`n`，默认 `n`
- 未传的选项会转为交互式询问；全部参数齐全时为快速创建，不再二次确认。
- 校验逻辑在 [PluginsCreate.php](file:///d:/dev/project/nas-torrent/plugin/xbDeveloper/api/PluginsCreate.php)：`validate()` 校验字段与长度（可带 `xb` 前缀，校验前会先去掉首个 `xb`），`create()` 按 `plugin/xbDeveloper/data/plugin/` 下的 `.tpl` 模板生成插件骨架（目录、config、控制器、视图、`plugins.json`、`install.sql` 等），并生成 `preview.svg` 预览图、刷新插件缓存。
- 若 `plugin/{name}` 目录已存在会直接报错，不要重复创建。
- 生成的文件含模板占位符 `{PLUGIN_TITLE}`、`{PLUGIN_NAME}`、`{PLUGIN_DESC}`、`{PLUGIN_AUTHOR}`，创建时自动替换。
- 模板缺失时 `validateTpl()` 会列出缺失的 `.tpl` 路径，先补齐模板再创建。

### 安装插件 PluginInstall

- 命令：`php webman xb-plugin:install <name>`
- 复用 xbCode 后台安装流程 [PluginsInstallApi.php](file:///d:/dev/project/nas-torrent/plugin/xbCode/api/PluginsInstallApi.php)，按 `steps()` 依次执行 `preChecked` → `install` → `finish`，每步结束后刷新插件缓存。
- 已安装的插件会直接报错并提示改用 `xb-plugin:update`。
- 插件必须存在 `api/Install.php`，否则抛出“插件脚本文件不存在”。

### 卸载插件 PluginUnInstall

- 命令：`php webman xb-plugin:uninstall <name>`
- 复用 xbCode 后台卸载流程 [PluginsUninstallApi.php](file:///d:/dev/project/nas-torrent/plugin/xbCode/api/PluginsUninstallApi.php)，按 `steps()` 依次执行 `uninstall` → `finish`。
- 未安装的插件会直接报错，不会执行任何卸载脚本。

### 更新插件 PluginUpdate

- 命令：`php webman xb-plugin:update <name>`
- 复用 [PluginsUpdate.php](file:///d:/dev/project/nas-torrent/plugin/xbDeveloper/api/PluginsUpdate.php)（继承 xbCode `PluginsBaseApi`），按 `steps()` 依次执行 `update` → `finish`：`update` 调用插件 `api/Install.php` 的 `updateBefore` / `update` / `updateAfter`；`finish` 刷新插件记录（保留原启用状态）并刷新缓存。
- 未安装的插件会直接报错并提示先执行安装命令。
