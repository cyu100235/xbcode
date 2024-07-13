<?php
namespace app\common\event;

use app\common\providers\ConfigProvider;
use Exception;
use Webman\Event\Event;

/**
 * 系统设置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SettingsEvent
{
    /**
     * 数据验证
     * @param mixed $validate
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function validate(mixed $validate, array $data)
    {
        if (!class_exists($validate)) {
            throw new Exception('验证器不存在');
        }
        xbValidate($validate, $data);
    }

    /**
     * 配置表单
     * @param array $post
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(array $post)
    {
        if (empty($post['group'])) {
            throw new Exception('分组参数错误');
        }
        if (empty($post['data'])) {
            throw new Exception('配置数据错误');
        }
        // 数据验证
        if (!empty($post['xbValidate'])) {
            $this->validate($post['xbValidate'], $post['data']);
            unset($post['xbValidate']);
        }
        // 保存配置项
        ConfigProvider::save($post['group'], $post['data']);
        // 配置完成后事件
        Event::dispatch('common.event.SettingsEvent.config.success', [
            'group' => $post['group'],
            'data' => $post['data'],
        ]);
    }
}
