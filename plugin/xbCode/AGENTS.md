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
