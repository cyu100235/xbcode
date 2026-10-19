# 插槽系统（Slot）

插槽系统用于在**不修改宿主插件代码**的前提下，让其他插件向宿主页面（如表格列、列表行数据、操作按钮）注入内容，实现插件间的低耦合扩展。

- 核心类：[Slot.php](Slot.php)（插槽注册表）、[ListContext.php](ListContext.php)（行数据上下文）
- 宿主挂载点示例：[UserController.php](../../xbUser/app/admin/controller/UserController.php)（用户列表）

## 一、工作原理

宿主在需要扩展的位置定义插槽常量，并调用 `Slot::dispatch()` 派发：

```php
use plugin\xbCode\slot\Slot;
use plugin\xbCode\slot\ListContext;

public const SLOT_COLUMNS = 'xbUser.User.index.columns'; // 列插槽
public const SLOT_ROWS    = 'xbUser.User.index.rows';    // 行数据插槽

// 列插槽：派发可变构建器，监听器直接往其追加列
Slot::dispatch(self::SLOT_COLUMNS, $builder);

// 行数据插槽：派发可变上下文，监听器按行追加/改写数据
$context = ListContext::fromPaginator($query->paginate(), (array) $request->get());
Slot::dispatch(self::SLOT_ROWS, $context);
```

> webman 的事件监听器**无法返回值**，因此插槽统一采用「传入可变对象、监听器就地修改」的约定：
> 列插槽传入 `XbCrud` 构建器，行插槽传入 `ListContext`。

## 二、注册监听器

### 2.1 方法签名

```php
Slot::listen(string $slot, string $targetColumn, callable|array $handler, bool $after = true): int
```

| 参数              | 类型                | 说明                                                          |
| --------------- | ----------------- | ----------------------------------------------------------- |
| `$slot`         | `string`          | 插槽名称（使用宿主定义的常量，如 `UserController::SLOT_COLUMNS`）            |
| `$targetColumn` | `string`          | 目标表格列的 `name`（列键名）。**列插槽**用于指定插入到哪一列前/后；**行数据插槽**传空字符串 `''` |
| `$handler`      | `callable\|array` | 处理回调；支持闭包，也支持 `[类名::class, '方法']`（延迟实例化，见 2.3）              |
| `$after`        | `bool`            | `true`＝插入到目标列**之后**（默认），`false`＝插入到目标列**之前**                |

返回值为监听器编号 `int`，可用于后续 `Slot::forget()` 移除。

### 2.2 定位规则

列插槽的定位由框架在回调执行后自动完成：

1. 回调执行前记录当前列数量；
2. 回调执行后取出**本次新增的列**；
3. 将其整体移动到 `$targetColumn` 列的**前/后**。

若 `$targetColumn` 对应的列在**所有监听器执行完毕**后仍不存在，则新增列追加到**末尾**。

> **跨插件前向引用**：目标列可以是由**注册在后的其他插件监听器**创建的列（例如按列顺序，设备数量列要插到等级插件创建的等级列之后）。框架会先暂存这类批次，待全部监听器执行完毕后统一重定位，无需依赖插件的注册顺序。

> `$after` 与 `$targetColumn` 仅对**支持列定位的上下文**（`XbCrud`）生效。行数据插槽的上下文（`ListContext`）不具备该能力，定位会被自动跳过，因此行插槽请将 `$targetColumn` 传 `''`。

### 2.3 回调签名

```php
function (object $payload, string $slot): void
```

- 第 1 个参数 `$payload`：可变上下文对象（列插槽为 `XbCrud`，行插槽为 `ListContext`）。
- 第 2 个参数 `$slot`：当前插槽名称。
- **无返回值**，直接在回调内修改 `$payload`。

使用 `[类名::class, '方法']` 形式时，框架会通过容器实例化该类：

```php
Slot::listen($slot, $target, [MySlotHandler::class, 'handle']);
```

## 三、列插槽示例

### 3.1 在「序号」列前新增一列

```php
use plugin\xbCode\slot\Slot;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbUser\app\admin\controller\UserController;

Slot::listen(
    UserController::SLOT_COLUMNS,
    'id',                      // 目标列：序号列
    function (XbCrud $builder) {
        $builder->addColumn('vip_level', '会员等级')->width(120)->align('center');
    },
    false                      // 插到 id 列前面
);
```

### 3.2 在「创建时间」列后新增一列

```php
Slot::listen(
    UserController::SLOT_COLUMNS,
    'create_at',
    function (XbCrud $builder) {
        $builder->addColumn('last_order_at', '最近下单')->width(160);
    }
    // $after 省略，默认 true（插到 create_at 之后）
);
```

## 四、行数据插槽示例

行数据插槽用于对当前页数据行做**追加字段、整体改写、过滤**等操作，`$targetColumn` 传 `''`。

### 4.1 按行追加字段

```php
use plugin\xbCode\slot\Slot;
use plugin\xbCode\slot\ListContext;
use plugin\xbUser\app\admin\controller\UserController;

Slot::listen(
    UserController::SLOT_ROWS,
    '',
    function (ListContext $ctx) {
        // 依据分页里的用户 id 批量查询，避免逐行查库
        $ids = $ctx->column('id');
        $vipMap = /* 批量查询得到 [uid => 到期时间] */ [];

        $ctx->appendMap('vip_expire', $vipMap, 'id', null);

        // 也可自定义计算逻辑
        $ctx->append('vip_status', function (array $row) {
            return ($row['vip_expire'] ?? 0) > time() ? '有效' : '过期';
        });
    }
);
```

### 4.2 整体改写行数据

```php
$ctx->map(function (array $row) {
    $row['nickname'] = $row['nickname'] ?: $row['username'];
    return $row;
});
```

### 4.3 过滤行

```php
$ctx->filter(function (array $row) {
    return ($row['state'] ?? '') === '1';
});
```

### 4.4 读取分页上下文

```php
Slot::listen(UserController::SLOT_ROWS, '', function (ListContext $ctx) {
    $page  = $ctx->page();          // 当前页码
    $limit = $ctx->limit();         // 每页条数
    $total = $ctx->total();         // 总记录数
    $kw    = $ctx->param('username'); // 请求参数（第 2 参数可设默认值）
});
```

## 五、操作按钮定位示例

`XbCrud` 的操作列按钮（`$actionButtons`）支持排序与按标题定位（标题即按钮 `label`，也就是 `addRightActionDialog('修改', ...)` 中的「修改」）。

```php
Slot::listen(UserController::SLOT_COLUMNS, 'create_at', function (XbCrud $builder) {
    // 先追加一个按钮
    $builder->addRightActionDialog('查看详情', \plugin\xbCode\api\Url::make('detail'));

    // 再把它移动到「修改」按钮之前
    $builder->moveActionButtonBefore('修改', '查看详情');

    // 其他可用方法：
    // $builder->insertActionButtonAfter('修改', $component); // 按标题插到某按钮之后
    // $builder->sortActionButtons(fn($a, $b) => $a->getVariable('label') <=> $b->getVariable('label'));
});
```

可用方法一览：

| 方法                                                                | 说明                         |
| ----------------------------------------------------------------- | -------------------------- |
| `addActionButton(mixed $component)`                               | 按原始组件追加到操作按钮列表             |
| `sortActionButtons(callable $callback)`                           | 回调签名同 `usort`，对按钮排序        |
| `insertActionButtonBefore(string $targetLabel, mixed $component)` | 插到指定标题按钮**之前**（目标不存在则追加末尾） |
| `insertActionButtonAfter(string $targetLabel, mixed $component)`  | 插到指定标题按钮**之后**（目标不存在则追加末尾） |
| `moveActionButtonBefore(string $targetLabel, string $label)`      | 把已有按钮移动到目标按钮**之前**         |
| `moveActionButtonAfter(string $targetLabel, string $label)`       | 把已有按钮移动到目标按钮**之后**         |

## 六、插件注册入口

其他插件通过插件自身的 `bootstrap` 回调在框架启动时注册监听器，无需改动宿主。

**1. 新建** **`plugin/{插件名}/config/bootstrap.php`，返回启动类数组：**

```php
<?php

return [
    \plugin\xbVip\bootstrap\VipBootstrap::class,
];
```

**2. 在启动类中注册监听器：**

```php
<?php
namespace plugin\xbVip\bootstrap;

use Workerman\Worker;
use Webman\Bootstrap;
use plugin\xbCode\slot\Slot;
use plugin\xbCode\slot\ListContext;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbUser\app\admin\controller\UserController;

class VipBootstrap implements Bootstrap
{
    public static function start(?Worker $worker)
    {
        // 列插槽：插到「序号」列前
        Slot::listen(UserController::SLOT_COLUMNS, 'id', [static::class, 'addColumns'], false);
        // 行数据插槽：targetColumn 传空字符串
        Slot::listen(UserController::SLOT_ROWS, '', [static::class, 'decorateRows']);
    }

    public function addColumns(XbCrud $builder, string $slot)
    {
        $builder->addColumn('vip_level', '会员等级')->width(120);
    }

    public function decorateRows(ListContext $ctx, string $slot)
    {
        $ctx->append('vip_status', function (array $row) {
            return ($row['vip_expire'] ?? 0) > time() ? '有效' : '过期';
        });
    }
}
```

> 框架启动时（`vendor/workerman/webman-framework/src/support/bootstrap.php`）会遍历 `config('plugin')`，对插件的 `bootstrap` 配置逐一类名调用 `::start($worker)`，即插槽的注册时机。注册时机与生效时机不同：**所有本地插件的 `bootstrap` 都会在启动时注册监听器**，但监听器只有在**其所属插件已安装且已启用**时才会在 `Slot::dispatch()` 中执行（见第八节）。

## 七、API 参考

### 7.1 Slot

| 方法                                                                                         | 返回值      | 说明                                                   |
| ------------------------------------------------------------------------------------------ | -------- | ---------------------------------------------------- |
| `listen(string $slot, string $targetColumn, callable\|array $handler, bool $after = true)` | `int`    | 注册监听器，返回监听器编号                                        |
| `forget(string $slot, int $id)`                                                            | `void`   | 按编号移除监听器                                             |
| `has(string $slot)`                                                                        | `bool`   | 判断插槽是否存在可用监听器                                        |
| `dispatch(string $slot, object $payload)`                                                  | `object` | 派发插槽，按注册顺序依次执行监听器，返回传入的上下文对象                         |
| `getListeners(string $slot)`                                                               | `array`  | 获取插槽监听器列表（按注册顺序，元素含 `id`/`handler`/`target`/`after`/`plugin`；**未过滤插件启用状态**） |

### 7.2 ListContext

| 方法                                                                                | 返回值      | 说明                                                                   |
| --------------------------------------------------------------------------------- | -------- | -------------------------------------------------------------------- |
| `__construct(array $items, array $meta = [])`                                     | —        | `$meta` 含 `page`/`limit`/`total`/`params`                            |
| `fromPaginator(mixed $paginator, array $params = [])`                             | `static` | 从分页结果创建上下文                                                           |
| `toResult()`                                                                      | `array`  | 导出为 `successData` 所需结构（`data`/`current_page`/`total`）                |
| `items()`                                                                         | `array`  | 获取当前页数据行                                                             |
| `setItems(array $items)`                                                          | `static` | 覆盖当前页数据行                                                             |
| `each(callable $callback)`                                                        | `static` | 遍历每一行（引用），回调签名 `function(array &$row, int $index, ListContext $ctx)` |
| `append(string $field, callable $resolver)`                                       | `static` | 为每行追加字段，`$resolver(array $row, int $index, ListContext $ctx): mixed` |
| `appendMap(string $field, array $map, string $key = 'id', mixed $default = null)` | `static` | 按映射表为每行追加字段                                                          |
| `filter(callable $callback)`                                                      | `static` | 过滤数据行，回调返回 `bool`                                                    |
| `map(callable $callback)`                                                         | `static` | 整体替换数据行，回调返回 `array`                                                 |
| `column(string $field)`                                                           | `array`  | 提取某列的值（常用于批量查询聚合）                                                    |
| `page()` / `limit()` / `total()`                                                  | `int`    | 分页页码 / 每页条数 / 总记录数                                                   |
| `param(string $key = '', mixed $default = null)`                                  | `mixed`  | 获取请求参数（`$key` 为空返回全部）                                                |

## 八、注意事项

- **生效条件**：监听器仅在**其所属插件已安装且已启用**时才会生效。框架启动时会加载所有本地插件的 `bootstrap`（无论是否已启用），未安装/已禁用的插件同样会执行 `Slot::listen()` 注册监听器，因此 `Slot::dispatch()` 与 `Slot::has()` 会在执行时按插件状态过滤，跳过未安装或已禁用插件注册的监听器。插件禁用后其插槽即刻失效，无需卸载。
- **执行顺序**：可用的监听器按 `Slot::listen()` 的**注册顺序**依次执行，不再做额外的排序。
- **异常隔离**：单个监听器抛出异常时会被捕获并写入错误日志（`插槽[xxx]执行异常`），不影响其他监听器与宿主流程。
- **定位键**：列定位使用列的 `name`（如 `addColumn('vip_level', ...)` 的 `vip_level`）；按钮定位使用按钮标题（`label`）。
- **性能**：行数据插槽运行在列表接口的每次请求中，请使用 `column()` 做批量查询，避免在 `append` 回调中逐行查库。

