# xbAdmin 后台权限管理系统

基于 RBAC 的后台权限管理插件，为 [xbCode](../xbCode/README.md) 渲染体系提供登录鉴权、管理员管理、角色管理与菜单权限管理。

- 依赖宿主框架：Webman / workerman
- 依赖插件：`xbCode`（提供进程、路由解析、构建器、`xb:install` 等命令）
- 依赖组件：`tinywan/jwt`、`firebase/php-jwt`、`webman/captcha`、`topthink/think-orm`

---

## 一、功能范围

| 模块 | 说明 |
|---|---|
| 登录鉴权 | 账号密码登录 + JWT 令牌 + 可选图形验证码 + 令牌续签 |
| 站点配置 | 向 SPA 下发站点信息、登录页数据、布局配置、公共接口与视图地址 |
| 菜单下发 | 按角色权限过滤后下发导航菜单树，前端据此动态生成路由 |
| 管理员管理 | 列表、新增、修改、快捷编辑、删除、个人资料 |
| 角色管理 | 列表、新增、修改、删除、分配权限（穿梭框） |
| 菜单权限管理 | 菜单/按钮/接口三类权限规则的增删改查与快捷编辑 |
| 数据权限 | 以 `admin_id` 归属隔离，超级管理员可见全部数据 |

不包含：仪表盘统计、插件管理、系统配置表单、附件管理。

---

## 二、目录结构

```
plugin/xbAdmin/
├── api/                        安装与业务能力层（无状态静态类）
│   ├── Install.php             安装/升级/卸载入口：建表、同步菜单、创建超管
│   ├── SiteEntry.php           SPA 站点配置生成器（读取 config/xbadmin.php）
│   ├── Url.php                 后台 URL 生成器（插件/模块/控制器/方法）
│   ├── Menus.php               菜单规则入库与按权限下发
│   ├── MenuChecked.php         菜单数据解析（树形转换、path 前缀补全）
│   ├── MenuOption.php          菜单选择项（上级菜单、角色权限树）
│   ├── AdminApi.php            登录、令牌签发、验证码校验
│   └── AdminAuthApi.php        权限校验（比对角色 rule 与请求 path）
├── app/
│   ├── admin/                  后台模块
│   │   ├── controller/         Index / Publics / Admin / AdminRole / AdminRule
│   │   ├── middleware/         AuthMiddleware 登录态 + 权限校验
│   │   └── view/index/         toolbar.vue、workbench.vue 远程视图
│   ├── controller/             插件默认模块（IndexController::admin 备用入口）
│   ├── model/                  Admin / AdminRole / AdminRule
│   ├── validate/               三个模型对应的验证器
│   └── functions.php           xbAdminPathInfo / xbAdminValidate
├── base/ enum/ trait/ utils/   枚举、trait、密码与令牌工具
├── exception/                  Handler + business 异常族
├── config/                     app / menu / middleware / exception / xbadmin ...
├── install.sql                 三张数据表 DDL
└── public/backend/             前端 SPA 构建产物（需自行构建，见下文）
```

---

## 三、安装步骤

### 1. 安装依赖

```bash
composer require tinywan/jwt webman/captcha
```

需要 `tinywan/jwt`（连带 `firebase/php-jwt`）与 `webman/captcha`，均已在 `composer.lock` 中，正常拉取即可。`tinywan/jwt` 的配置在 `config/plugin/tinywan/jwt/app.php`，默认 `is_single_device => false`，**不需要 Redis**；只有开启单设备登录时才必须提供可用的 Redis 连接。

### 2. 配置数据库

编辑项目根目录 `.env`：

```ini
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_DATABASE = your_database
DB_USER = root
DB_PASS = your_password
DB_PREFIX = xb_
```

> `DB_PREFIX` 不能为空。安装时会以它替换 `install.sql` 中的 `` xb_ `` 前缀，为空会被 `api/Install.php` 的预检直接拦下。

### 3. 执行安装

二选一：

```bash
# 批量安装 plugin 目录下所有已注册插件
php webman xb:install

# 只安装本插件
php webman app-plugin:install xbAdmin
```

安装过程依次完成：建表 → 把 `config/menu.php` 同步写入 `xb_admin_rule` → 创建「超级管理员」角色（持有全部权限点）→ 创建 `admin` 账号。

整个过程**幂等**：表已存在则跳过、菜单按 `plugin + path` upsert、角色与账号存在则复用，可重复执行。

若只想单独重新导入表结构或菜单（例如改了 `install.sql` / `menu.php`），使用：

```bash
php webman xb:plugin:import xbAdmin --type=sql    # 仅导入表结构
php webman xb:plugin:import xbAdmin --type=menu   # 仅同步菜单
```

### 4. 构建前端产物

后台 SPA 的构建产物放在 `plugin/xbAdmin/public/backend/`，由 `IndexController` 读取 `index.html` 输出。**构建时必须把 vite 的 `base` 配成 `/app/xbAdmin/backend/`**：

```js
// vite.config.js
export default defineConfig({
    base: '/app/xbAdmin/backend/',
    build: { outDir: 'path/to/plugin/xbAdmin/public/backend' },
});
```

原因：`App::findFile()` 会把 `/app/{插件名}/{路径}` 映射到 `plugin/{插件名}/public/{路径}`，静态资源必须走这个规则才能命中；同时 `plugin/xbAdmin/config/static.php` 的 `enable` 已设为 `true`。

`base` 只影响静态资源地址，**不影响接口地址**（接口 baseURL 见第六节），两者互不干扰。

### 5. 启动并访问

```bash
# Windows
php windows.php
# Linux / macOS
php webman start
```

访问 `http://127.0.0.1:39000/xbAdmin/admin`（端口取自 `plugin/xbCode/config/process.php`，默认 `39000`）。

默认账号：

| 账号 | 密码 | 说明 |
|---|---|---|
| `admin` | `123456` | 超级管理员，`is_system=20`，跳过权限校验 |

> 上线前请立即修改密码。密码存储算法为 `md5($pwd . md5($pwd) . md5('xbadmin'))`，见 `utils/PasswdUtil.php`；入库一律走模型的 `setPasswordAttr` 修改器，**不要在外部提前加密**，否则会二次加密导致无法登录。

---

## 四、接口清单

所有接口都在 `admin` 模块下，URL 形如 `/xbAdmin/admin/{控制器}/{方法}`。

### IndexController（入口与站点）

| 方法 | 请求 | 说明 | 免登录 | 免权限 |
|---|---|---|---|---|
| `index` | GET | 输出 SPA 入口 HTML | ✓ | — |
| `admin` | GET | 输出 SPA 入口 HTML（别名） | ✓ | — |
| `site` | GET | 下发站点配置 | ✓ | — |
| `toolbar` | GET | 顶部工具栏远程视图 | | ✓ |
| `workbench` | GET | 工作台远程视图 | | ✓ |

### PublicsController（登录与公共读）

| 方法 | 请求 | 说明 |
|---|---|---|
| `login` | POST | 账号密码登录，返回令牌 |
| `captcha` | GET | 图形验证码（`captcha_state=20` 时生效） |
| `logout` | POST | 退出登录 |
| `refresh` | POST | 续签令牌 |
| `user` | GET | 当前登录管理员信息 |
| `menus` | GET | 当前管理员的导航菜单树 |
| `layouts` | GET | 布局与主题配置 |

`login`、`captcha` 免登录；`logout/user/menus/layouts/refresh` 登录后即可访问，不校验权限点。

### AdminController / AdminRoleController / AdminRuleController

标准 CRUD，方法为 `index`（列表）、`add`（新增，GET 返回表单/POST 提交）、`edit`（修改）、`del`（删除）；`AdminController`、`AdminRuleController` 另有 `rowEdit`（列表内快捷编辑），`AdminRoleController` 另有 `auth`（分配权限），`AdminController` 另有 `profile`（个人资料视图）。每个方法都是一个独立的权限点。

---

## 五、权限模型

```
xb_admin (管理员)  ──role_id──>  xb_admin_role (角色)  ──rule (path 列表)──>  xb_admin_rule (菜单权限)
```

### 数据表

| 表 | 用途 | 关键字段 |
|---|---|---|
| `xb_admin` | 管理员 | `username`(唯一)、`password`、`role_id`、`state`、`is_system`、`admin_id` |
| `xb_admin_role` | 角色 | `title`、`rule`（权限 path 的 JSON 数组）、`is_system`、`admin_id` |
| `xb_admin_rule` | 菜单权限 | `pid`、`type`、`plugin`、`path`、`method`、`is_show`、`is_default`、`is_system`、`state` |

枚举取值统一为字符串：`10` 否/关闭/隐藏，`20` 是/开启/显示；`type` 为 `10` 目录、`20` 菜单页、`30` 按钮或接口权限点。

### 校验链路

`app/admin/middleware/AuthMiddleware.php`（通过 `config/middleware.php` 的 `'admin'` 键绑定到本插件的 `admin` 模块）：

1. `OPTIONS` 预检直接放行；
2. 命中控制器 `$noLogin` 白名单则免登录；
3. 解析 `authorization` 请求头中的 JWT（裸令牌或 `Bearer <token>` 均支持），失败抛 401；
4. **回查数据库**取最新的 `state`/`role_id`/`is_system`，避免改权限后旧令牌仍生效；账号被禁用抛 403；
5. 命中 `$noAuth` 白名单则放行；
6. 超级管理员放行；
7. 其余按角色的 `rule` 列表比对当前请求的「模块/控制器/方法」，未命中抛 403。

> 未出现在 `config/menu.php`（即 `xb_admin_rule`）中的接口，默认**拒绝访问**（超管除外）。新增控制器方法时必须同步补一行权限规则，否则非超管账号一律 403。

---

## 六、前端地址约定

这一节解释了若干容易改错的地方，改动 `api/MenuChecked.php`、`api/Url.php`、`config/menu.php` 之前请先读它。

前端 SPA（构建产物）的行为如下：

- 应用启动时把 axios 的 `baseURL` 设为 `window.location.origin`（协议+域名+端口，不含路径）；
- 拿到 `public_api` / `public_view` 里的地址后直接请求，因此这些值以 `/` 开头即可（由 `Url::make()` 生成，已带前导斜杠）；
- 菜单接口的每一项，前端用 `path` 生成路由：`route.path = '/' + menu.path`；
- 对于普通菜单页，`menu.path` **同时就是**取 amis schema 的请求地址。

因此：

1. **`menu.path` 下发时必须形如 `xbAdmin/admin/Admin/index`**（`{插件}/{模块}/{控制器}/{方法}`），由 `MenuChecked::parseMenuData()` 给数据库里的 `admin/Admin/index` 补上插件名前缀。
2. **不能带 `app/` 前缀**。`app/` 是静态资源目录映射规则，不是路由；加上后请求会变成 `/app/xbAdmin/admin/Admin/index` 而 404。
3. **工作台菜单例外**。它的 `path` 恒为字面量 `workbench`（前端布局路由的 `redirect: "/workbench"` 是硬编码的），既不能被补插件前缀，取数地址也改由 `params.url` 提供（下发后 `meta.path = "xbAdmin/admin/Index/workbench"`）。
4. 数据库里的 `path` 存「模块/控制器/方法」，与 `xbAdminPathInfo()` 的返回值同一口径，权限比对用的就是它。

---

## 七、配置说明

`config/xbadmin.php` 是后台唯一的配置来源，由 `api/SiteEntry.php` 读取后经 `admin/Index/site` 下发，不依赖数据库配置表。

| 键 | 说明 |
|---|---|
| `web_name` / `web_logo` / `web_version` | 系统名称、LOGO、版本号（版本取自 `plugin.json`） |
| `web_icp` / `web_police` / `about_*` | 备案号与关于我们，留空不展示 |
| `copyright` | 版权文案，支持 `{WEB_NAME}` `{WEB_URL}` `{WEB_ICP}` `{WEB_POLICE}` 占位符 |
| `captcha_state` | `'10'` 关闭（默认）、`'20'` 开启登录验证码 |
| `login.*` | 登录页标题、副标题、背景图、广告图、注册/找回/返回入口、第三方登录 |
| `layout.*` | 布局模式、主题、菜单折叠、头部/底部/侧边栏尺寸 |
| `toolbar` | 顶部导航右侧的扩展链接列表 |
| `public_api` / `public_view` | **建议留空**，由 `SiteEntry` 自动生成指向本插件的地址 |
| `upload_api` / `upload_cate_api` / `editor_upload_api` | 本插件不含附件模块，留空 |
| `components` / `global_components` / `icons_links` | 远程组件与扩展图标库，留空表示不使用 |

> 配置在进程启动时加载，修改后需要**重启服务**才会生效。

前端对以下键做强校验，缺失会直接白屏并提示「接口未配置」，`SiteEntry` 已保证全部存在：
`public_api.login`、`public_api.user`、`public_api.menus`、`public_api.layouts`、`public_view.workbench`。

---

## 八、菜单维护

菜单的源头是 `config/menu.php`，安装（或导入）时以 `plugin + path` 为键 upsert 进 `xb_admin_rule`。`is_default` 由 `menu.php` **逐条声明**：登录链路必需的基础接口标 `'20'`（会自动授予新建角色），业务增删改类权限点标 `'10'`（需手工分配）。

新增一个后台页面的步骤：

1. 在 `app/admin/controller/` 下写控制器方法（后缀 `Controller`，`config/app.php` 已配置 `controller_suffix`）；
2. 在 `config/menu.php` 中补一条记录，`path` 填 `模块/控制器/方法`（如 `admin/News/index`），`type` 为 `20`，并带上 `icon`；
3. 把该页面的增删改等方法作为 `type=30`、`is_show=10` 的子节点补进去（作为按钮权限点）；
4. 执行 `php webman xb:plugin:import xbAdmin --type=menu` 同步入库；
5. 在角色管理里为对应角色勾上新权限。

必须遵守的约定：

- `type` / `is_show` / `state` / `is_system` / `is_default` **一律写字符串** `'10'` 或 `'20'`；
- **每一级都要显式写 `'state' => '20'`**，不填会被 `Menus::install()` 置为 `'10'`（停用），菜单会整体消失；
- `path` 在同一插件内必须唯一，upsert 以 `plugin + path` 为键，重复会互相覆盖；
- `is_default=20` 的权限点会自动授予新建角色，只应放登录链路必需的基础接口；
- 目录节点（`type=10`）的 `path` 不含 `/`，不会被补插件前缀，取一个不重复的名字即可；
- 在后台「菜单权限」里手工新增的记录，`is_default` / `is_system` 默认为 `'10'`；但只要它的 `path` 与 `menu.php` 中某条相同（同插件 + 同 `path`），下次同步就会被**覆盖**。自定义菜单请避开已占用的 `path`，删除前也先确认没有角色仍在引用。

---

## 九、远程视图（`.vue`）

`app/admin/view/index/toolbar.vue`、`workbench.vue` 是**远程视图**：内容由后端以字符串下发，前端运行时编译（`plugin/xbCode/builder/Renders/XbVue`）。编写时有三条硬约束：

1. 文件内容必须包含字面量 `<template>`；
2. 控制器 `display($vars)` 传入的 `$vars` 会**直接作为根组件的 props**，不需要再包一层；
3. **不支持** `scss`、全局注册组件、`vue-router`，视图必须自包含（只用 Element Plus 内置组件与原生能力）。

`display()` 还会把 `request()->get()` 合并进 `$vars`，便于前端通过 `_act=ajax` 之类的查询参数复用同一视图。

---

## 十、卸载与重装

```bash
php webman app-plugin:uninstall xbAdmin
```

出于数据安全，卸载钩子**只清理本插件写入的菜单记录，不删除数据表**。若确要彻底清空，手动执行：

```sql
DROP TABLE `xb_admin`;
DROP TABLE `xb_admin_role`;
DROP TABLE `xb_admin_rule`;
```

之后重新执行安装即可。

---

## 十一、常见问题

**打开页面白屏，控制台提示「菜单接口未配置」/「获取主题配置失败」**
`site` 接口下发的 `public_api` 键不全，或 `layouts` 接口没有返回对象。检查是否改动了 `SiteEntry::publicApi()`，或 `config/xbadmin.php` 的 `public_api` 被手工填了错误地址。

**登录后点任何菜单都 403**
当前角色没有被分配对应权限点。用 `admin` 登录后进「角色管理 → 分配权限」勾选，或直接以超管操作。

**列表能打开，但点进去是 JSON 而不是页面**
前端用的是 HTML5 history 路由，路由地址与接口地址同串（`/xbAdmin/admin/Admin/index`）。在浏览器地址栏**直接输入或硬刷新**该地址会命中后端接口拿到 JSON，这是渲染体系的既有行为（参考实现相同）。请从菜单点击进入，或用 `Ctrl+F5` 后回到 `/xbAdmin/admin` 入口。

**静态资源 404**
vite 的 `base` 没配成 `/app/xbAdmin/backend/`，或产物没输出到 `plugin/xbAdmin/public/backend/`，或 `config/static.php` 的 `enable` 被关掉。

**中间件没生效、未登录也能调接口**
`config/middleware.php` 必须是二级结构 `['admin' => [AuthMiddleware::class]]`。写成一级扁平列表会让框架抛 `Bad middleware config`；写成 `'@'` 键会变成全局中间件，影响其他插件。

**改了 `config/menu.php` 但后台看不到变化**
菜单是安装/导入时同步入库的，改文件不会自动生效，需要执行 `php webman xb:plugin:import xbAdmin --type=menu`。

**`captcha_state` 改成 `20` 后验证码不显示**
确认已安装 `webman/captcha`，且浏览器与后端同域（验证码文本存于 Session，跨域会导致会话不连续）。
