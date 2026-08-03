<?php
/**
 * 通用状态机 Trait（状态流转引擎）
 *
 * 使用方式：
 * ─────────────────────────────────────────────────────────────
 *
 * 1. 为每个流转定义独立的处理类，实现 StateHandlerInterface：
 *
 *    use plugin\xbCode\base\stateMachine\StateHandlerInterface;
 *
 *    class PayHandler implements StateHandlerInterface
 *    {
 *        public function handle(object $entity, string $oldStatus, string $newStatus, array $context): void
 *        {
 *            $entity->paid_at = date('Y-m-d H:i:s');
 *            $entity->pay_method = $context['pay_method'] ?? '';
 *        }
 *    }
 *
 * 2. 在你的类中 use StateMachine，只需定义 2 个属性：
 *
 *    // ── 订单状态示例 ──
 *    use plugin\xbCode\base\stateMachine\StateMachine;
 *
 *    class Order extends Model
 *    {
 *        use StateMachine;
 *
 *        protected static string $initialStatus = 'created';
 *
 *        protected static array $transitions = [
 *            'pay'           => ['from' => 'created',  'to' => 'paid',                     'handler' => PayHandler::class],
 *            'cancel'        => ['from' => 'created',  'to' => 'cancelled',                'handler' => CancelHandler::class],
 *            'request_refund'=> ['from' => 'paid',     'to' => 'refund_requested',         'handler' => RefundHandler::class],
 *            'ship'          => ['from' => 'paid',     'to' => 'shipped',                  'handler' => ShipHandler::class],
 *            'receive'       => ['from' => 'shipped',  'to' => 'received',                 'handler' => ReceiveHandler::class],
 *            'review'        => ['from' => 'received', 'to' => 'reviewed',                 'handler' => ReviewHandler::class],
 *            'complete'      => ['from' => 'reviewed', 'to' => 'completed',                'handler' => CompleteHandler::class],
 *            'return_refund' => [
 *                'from'    => ['shipped', 'received', 'reviewed', 'completed'],
 *                'to'      => 'return_refund_requested',
 *                'handler' => ReturnRefundHandler::class,
 *            ],
 *        ];
 *
 *        // 正向流转顺序（可选，用于 advanceForward 自动推进）
 *        protected static array $forwardTransitions = ['pay', 'ship', 'receive', 'review', 'complete'];
 *    }
 *
 *    // ── 任务状态示例 ──
 *    class TaskLog extends Model
 *    {
 *        use StateMachine;
 *
 *        protected static string $initialStatus = 'created';
 *
 *        protected static array $transitions = [
 *            'query'    => ['from' => 'created',     'to' => 'querying',    'handler' => QueryHandler::class],
 *            'download' => ['from' => 'querying',    'to' => 'downloading', 'handler' => DownloadHandler::class],
 *            'upload'   => ['from' => 'downloading', 'to' => 'uploading',   'handler' => UploadHandler::class],
 *            'complete' => ['from' => 'uploading',   'to' => 'completed',   'handler' => CompleteHandler::class],
 *            'fail'     => [
 *                'from'    => ['querying', 'downloading', 'uploading'],
 *                'to'      => 'failed',
 *                'handler' => FailHandler::class,
 *            ],
 *        ];
 *
 *        protected static array $forwardTransitions = ['query', 'download', 'upload', 'complete'];
 *    }
 *
 * 3. 调用示例：
 *
 *    $order = new Order();
 *    $order->initializeStatus();  // status = 'created'
 *    $order->save();
 *
 *    // 判断能否执行某流转
 *    if ($order->canTransition('pay')) {
 *        $order->applyTransition('pay', ['pay_method' => 'wechat']);
 *    }
 *
 *    // 获取当前可执行的流转
 *    $order->getAvailableTransitions();  // ['pay', 'cancel']
 *
 *    // 自动推进正向流转
 *    $order->advanceForward();
 *
 *    // 判断终态
 *    $order->isTerminalStatus();
 *
 * ─────────────────────────────────────────────────────────────
 *
 * 配置说明：
 * - $initialStatus  ：初始状态值，必填
 * - $transitions    ：流转定义，必填
 *     from   ：源状态，字符串或字符串数组（多源状态 → 同一目标）
 *     to     ：目标状态，字符串
 *     handler：处理类类名或当前类中的方法名（字符串），可选；
 *             类名需实现 StateHandlerInterface，方法名则为当前类中的 protected 非静态方法
 *             方法签名为 (array $context = []): void，仅传入上下文参数
 * - $forwardTransitions：正向流转顺序，可选，用于 advanceForward()
 * - $statusField    ：状态字段名，默认 'status'
 *
 * 执行顺序：dispatchHandler → beforeEnterState → 更新状态 → save()
 *
 * beforeEnterState 回调：
 * - 所有流转通用的逻辑（如统一记录变更日志、发送通知）
 * - 没有注册 handler 的流转的兜底处理
 * - 优先级低于处理类：两者都会执行，handler 先执行
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 */

namespace plugin\xbCode\base\stateMachine;

use Exception;

trait StateMachine
{
    /**
     * 状态字段名（子类可自定义，默认 status）
     * @var string
     */
    protected string $statusField = 'status';

    // ─── 内部 ─────────────────────────────────────────

    /**
     * 将 $transitions 解析为 [transition => [from => to]] 映射
     * 带类级别缓存
     * @return array<string,array<string,string>>
     */
    private static function resolveMap(): array
    {
        static $cache = [];
        $key = static::class;
        if (isset($cache[$key])) {
            return $cache[$key];
        }
        $map = [];
        foreach (static::$transitions as $name => $def) {
            $to = $def['to'] ?? null;
            if ($to === null) {
                continue;
            }
            $map[$name] = [];
            foreach ((array) ($def['from'] ?? []) as $from) {
                $map[$name][(string) $from] = (string) $to;
            }
        }
        $cache[$key] = $map;
        return $map;
    }

    // ─── 核心 ─────────────────────────────────────────

    /**
     * 判断能否执行指定流转
     */
    public function canTransition(string $transition): bool
    {
        $map  = static::resolveMap();
        $from = $map[$transition] ?? null;
        return $from !== null && isset($from[(string) $this->{$this->statusField}]);
    }

    /**
     * 执行指定流转
     * 执行顺序：处理类 → beforeEnterState → 更新状态 → save()
     *
     * 支持嵌套流转：若 handler 内部已通过 applyTransition() 将实体推进到其他状态，
     * 外层流转不会覆盖已变更的状态，仅执行一次 save() 持久化。
     *
     * @throws Exception
     */
    public function applyTransition(string $transition, array $context = []): void
    {
        if (!$this->canTransition($transition)) {
            $cur = (string) $this->{$this->statusField};
            throw new Exception("无法从状态 [{$cur}] 执行流转: {$transition}");
        }

        $map       = static::resolveMap();
        $oldStatus = (string) $this->{$this->statusField};
        $newStatus = $map[$transition][$oldStatus];

        // 1. 处理类
        $this->dispatchHandler($transition, $oldStatus, $newStatus, $context);
        // 2. 回调
        $this->beforeEnterState($transition, $oldStatus, $newStatus, $context);
        // 3. 更新状态（若 handler 已通过嵌套流转变更了状态，则不再覆盖）
        if ((string) $this->{$this->statusField} === $oldStatus) {
            $this->{$this->statusField} = $newStatus;
        }
        // 4. 持久化
        $this->save();
    }

    /**
     * 按顺序自动推进正向流转，跳过不可执行的，直到无法继续
     */
    public function advanceForward(array $context = []): void
    {
        foreach (static::$forwardTransitions as $t) {
            if ($this->canTransition($t)) {
                $this->applyTransition($t, $context);
            }
        }
    }

    // ─── 查询 ─────────────────────────────────────────

    /**
     * 获取当前状态可执行的所有流转名称
     * @return string[]
     */
    public function getAvailableTransitions(): array
    {
        $cur = (string) $this->{$this->statusField};
        $result = [];
        foreach (static::resolveMap() as $name => $fromTo) {
            if (isset($fromTo[$cur])) {
                $result[] = $name;
            }
        }
        return $result;
    }

    /**
     * 获取指定流转的目标状态（不可执行时返回 null）
     */
    public function getTargetStatus(string $transition): ?string
    {
        if (!$this->canTransition($transition)) {
            return null;
        }
        $cur = (string) $this->{$this->statusField};
        return static::resolveMap()[$transition][$cur] ?? null;
    }

    /**
     * 判断当前是否终态（无可执行流转）
     */
    public function isTerminalStatus(): bool
    {
        return empty($this->getAvailableTransitions());
    }

    /**
     * 获取所有可能的状态值
     * @return string[]
     */
    public static function getAllStatuses(): array
    {
        $list = [];
        $add  = function (string $s) use (&$list) {
            if (!in_array($s, $list, true)) {
                $list[] = $s;
            }
        };
        if (static::$initialStatus !== '') {
            $add(static::$initialStatus);
        }
        foreach (static::$transitions as $def) {
            foreach ((array) ($def['from'] ?? []) as $from) {
                $add((string) $from);
            }
            if (isset($def['to'])) {
                $add((string) $def['to']);
            }
        }
        return $list;
    }

    // ─── 初始状态 ─────────────────────────────────────

    public static function getInitialStatus(): string
    {
        return static::$initialStatus;
    }

    public function initializeStatus(): void
    {
        $this->{$this->statusField} = static::$initialStatus;
    }

    public function isInitialStatus(): bool
    {
        return (string) $this->{$this->statusField} === static::$initialStatus;
    }

    public static function getForwardTransitions(): array
    {
        return static::$forwardTransitions;
    }

    // ─── 内部钩子 ─────────────────────────────────────

    /**
     * 分发流转到对应的处理器
     *
     * 支持两种 handler 配置方式：
     * 1. 当前类中的方法名（字符串）：如 'onStart'，方法签名为
     *    (array $context = []): void，仅传入上下文参数
     * 2. 独立处理类类名（实现 StateHandlerInterface）：如 StartHandler::class
     *
     * 优先检查是否为当前类中的方法名，是则直接调用；否则作为独立处理类实例化。
     */
    protected function dispatchHandler(string $transition, string $oldStatus, string $newStatus, array $context): void
    {
        $handler = static::$transitions[$transition]['handler'] ?? null;
        if ($handler === null) {
            return;
        }
        // 优先检查是否为当前类中的方法名
        if (is_string($handler) && method_exists($this, $handler)) {
            $this->{$handler}($context);
            return;
        }
        // 兼容独立处理类方式
        $instance = new $handler();
        if (!($instance instanceof StateHandlerInterface)) {
            throw new Exception("处理类 [{$handler}] 未实现 StateHandlerInterface 接口");
        }
        $instance->handle($this, $oldStatus, $newStatus, $context);
    }

    /**
     * 进入目标状态前的回调（子类按需重写，优先级低于处理类）
     */
    protected function beforeEnterState(string $transition, string $oldStatus, string $newStatus, array $context = []): void
    {
        // 默认空实现
    }
}
