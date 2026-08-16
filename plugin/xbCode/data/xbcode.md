# CLAUDE.md — xbCode 插件开发 AI 编码指南

> **项目**: 积木云 xbCode 生态 | **框架**: Webman v2 | **PHP**: ≥ 8.2 | **ORM**: ThinkORM 3

---

## 1. 框架与依赖

| 依赖 | 版本/说明 |
|:---|:---|
| **PHP** | ≥ 8.2 |
| **框架** | Webman (webman-v2) |
| **ORM** | ThinkORM 3 (`topthink/think-orm:v3.0.34`) |
| **数据库** | MySQL 5.7+ |
| **缓存** | Redis（通过 `support\Redis`） |

### 核心插件依赖层级

```
xbCode（核心框架）— 所有插件必须依赖
  ├── xbDeveloper（插件生命周期管理）
  ├── xbUpload（文件上传/储存引擎）
  ├── xbCrontab（定时任务调度）
  └── 新插件（必须依赖 xbCode）
```

---

## 2. 编码标准

### 2.1 代码风格

- 缩进：**4 空格** | 编码：**UTF-8 without BOM** | 换行：**LF**
- 字符串：优先使用**单引号** | 数组：使用短语法 `[]`
- PHP 标签：仅 `<?php`，**不使用 `?>`**
- 文件头注释必须包含 `@copyright 贵州积木云网络科技有限公司`、`@author`、`@license`

### 2.2 命名空间

| 类型 | 命名空间 |
|:---|:---|
| 插件根 | `plugin\{Name}` |
| API 类 | `plugin\{Name}\api\XxxApi` |
| 控制器 | `plugin\{Name}\app\admin\controller\XxxController` |
| 模型 | `plugin\{Name}\app\model\Xxx` |
| 枚举 | `plugin\{Name}\enum\XxxEnum` |
| 验证器 | `plugin\{Name}\app\validate\XxxValidate` |
| 服务 | `plugin\{Name}\service\XxxService` |

### 2.3 类命名

| 类型 | 规则 | 示例 |
|:---|:---|:---|
| API 类 | `{功能}Api` | `CrontabApi`、`UploadApi` |
| 控制器 | `{功能}Controller` | `IndexController` |
| 模型 | `{表名大驼峰}` | `Crontab`、`CrontabLog` |
| 枚举 | `{功能}Enum` | `StateEnum` |
| 验证器 | `{功能}Validate` | `CrontabValidate` |
| 安装类 | `Install`（固定） | `Install` |
| 基础类 | `Base{功能}` | `BaseEnum`、`BasePlugin` |

### 2.4 数据库命名

- 表名：`xb_{小写下划线}`，如 `xb_crontab_log`
- 字段名：`小写下划线`，如 `cron_expression`、`last_time`
- 索引名：`idx_{字段名}`

---

## 3. 必须遵循的架构模式

### 3.1 控制器模式

```php
class IndexController extends XbController
{
    public function index()
    {
        if (request()->get('_act')) {
            return $this->successData(Model::paginate());
        }
        $builder = XbCrud::make();
        $builder->addColumn('id', 'ID');
        $builder->addRightActionDialog('修改', Url::make('edit'), ['title' => '修改']);
        $builder->addRightActionConfirm('删除', Url::make('del'));
        return $this->successRes($builder);
    }
}
```

### 3.2 API 类模式（对外接口）

```php
class XxxApi
{
    public static function make()
    {
        return new static;
    }

    public function add($data)
    {
        xbValidate(XxxValidate::class, $data, 'add');
        // 业务逻辑...
        return $result;
    }
}
```

### 3.3 响应格式

- `$this->success('操作成功')` — 操作成功消息
- `$this->successData($data)` — 返回列表/详情数据
- `$this->successRes($builder)` — 返回 XbCrud / XbForm / XbTabForm 渲染器
- `$this->display([...])` — Vue 自定义页面渲染

### 3.4 模型

```php
class Xxx extends Model  // 继承 plugin\xbCode\Model
{
    // 自动获得：saas_appid 数据隔离、create_at/update_at 时间戳
}
```

### 3.5 枚举

```php
class StatusEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '禁用',
        'value' => '10',
        'style' => '<span class="label label-danger">禁用</span>'
    ];
    const STATE20 = [
        'label' => '启用',
        'value' => '20',
        'style' => '<span class="label label-success">启用</span>'
    ];
}
```

**枚举值约定**：`'10'` = 禁用/关闭/否/隐藏 | `'20'` = 启用/开启/是/显示 | `'30'` = 已通过/已停止 | `'40'` = 已拒绝

### 3.6 验证器

```php
class XxxValidate extends Validate  // 继承 taoser\Validate
{
    protected $rule = [
        'field' => 'require'
    ];
    protected $message = [
        'field.require' => '请填写字段'
    ];
}
// 调用：xbValidate(XxxValidate::class, $data, 'scene')
```

### 3.7 数据库表设计

```sql
CREATE TABLE `xb_example` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间',
  `saas_appid` int(11) DEFAULT NULL COMMENT '站点ID',
  -- 业务字段...
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
```

**必须字段**：`id`、`create_at`、`update_at`、`saas_appid`（多站点隔离）

---

## 4. 禁止的 API 与模式

### 4.1 禁止直接操作其他插件的数据表

```php
// ❌ 禁止
Db::table('xb_upload')->insert($data);
Db::table('xb_crontab')->where('id', 1)->update($data);

// ✅ 正确：通过目标插件 API 调用
use plugin\xbUpload\api\UploadApi;
UploadApi::make()->upload('file');

use plugin\xbCrontab\api\CrontabApi;
CrontabApi::make()->add($data);
```

### 4.2 禁止绕过 SaaS 数据隔离

- 所有数据表必须包含 `saas_appid` 字段
- 模型继承 `plugin\xbCode\Model` 自动实现隔离
- **不得在查询中手动拼接 where 绕过 `saas_appid` 过滤**

### 4.3 禁止在控制器中直接输出 HTML

```php
// ❌ 禁止
echo '<html>...';
return response('<div>...</div>');

// ✅ 正确
return $this->display(['data' => $data]);           // Vue 自定义页面
return $this->successRes($builder);                  // 渲染器返回
return $this->viewPage();                            // 纯静态 HTML
```

### 4.4 禁止的操作

- 不得删除或修改已有菜单项（`config/menu.php`）
- 不得删除或修改已有数据表字段
- 不得修改现有 API 的方法签名
- 不得在错误信息中暴露数据库结构、SQL、系统路径
- 不得直接读取 `$_GET`、`$_POST` 等超全局变量（应使用 `request()->get()` / `request()->post()`）

---

## 5. 核心 API 速查

| 能力 | 调用入口 |
|:---|:---|
| 文件上传 | `plugin\xbUpload\api\UploadApi::make()` |
| 文件 URL | `plugin\xbUpload\api\Files::make()->url($uri)` |
| 定时任务 | `plugin\xbCrontab\api\CrontabApi::make()` |
| 系统配置 | `plugin\xbCode\api\ConfigApi::make($name)` |
| 插件信息 | `plugin\xbCode\api\PluginsApi::make()` |
| 后台 URL | `plugin\xbCode\api\Url::make($path)` |

---

## 6. UI 渲染器

| 场景 | 渲染器 |
|:---|:---|
| 列表页 | `XbCrud::make()` + `$this->successRes($builder)` |
| 表单页 | `XbForm::make()` + `$this->successRes($builder)` |
| 选项卡表单 | `XbTabForm::make()` + `$this->successRes($builder)` |
| Vue 自定义 | `$this->display([...])` |
| 纯 HTML | `$this->viewPage()` |

常用表单字段：`addRowInput`、`addRowTextarea`、`addRowSelect`、`addRowSwitch`、`addRowNumberInput`、`addRowUploadImage`、`addRowRadioButton`、`addRowDate`、`addRowWangEditor`、`addRowIconPicker` 等。

---

## 7. 错误处理

- API 层：`throw new Exception('中文错误描述');`
- 验证：`xbValidate(Validate::class, $data, 'scene');` 失败自动抛异常
- 日志：`Log::error("描述：{$th->getMessage()}");` 或 `Log::info('描述', $data);`
- 敏感信息不得出现在错误消息中

---

## 8. 参考插件

开发前必须参考以下官方插件的实现方式：

- `plugin/xbCode` — 核心框架（基类、Builder、Traits）
- `plugin/xbCrontab` — 定时任务（进程、事件、跨进程通信）
- `plugin/xbDeveloper` — 开发工具（插件生命周期、模板生成）
- `plugin/xbUpload` — 储存引擎（策略模式、分片上传）

---

> **硬约束**：所有代码必须遵循本指南。未充分理解参考插件实现的情况下，禁止直接编写新插件代码。