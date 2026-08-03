<?php
/**
 * 状态流转处理接口
 * 每个流转（transition）对应的业务处理类必须实现此接口
 *
 * 使用示例：
 * ─────────────────────────────────────────────────────────────
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
 * ─────────────────────────────────────────────────────────────
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 */

namespace plugin\xbCode\base\stateMachine;

interface StateHandlerInterface
{
    /**
     * 处理状态流转的业务逻辑
     * 在状态值更新之前调用，可在此时修改 entity 的其他字段
     *
     * @param object $entity    使用状态机的实体对象
     * @param string $oldStatus 流转前的状态值
     * @param string $newStatus 流转后的目标状态值
     * @param array  $context   流转上下文参数
     * @return void
     */
    public function handle(object $entity, string $oldStatus, string $newStatus, array $context): void;
}
