<?php
namespace app\common\event;

use app\admin\validate\MenusValidate;
use app\model\AdminRule;
use Webman\Event\Event;
use Exception;

/**
 * 菜单事件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MenuEvent
{
    /**
     * 添加
     * @param array $post
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function add(array $post)
    {
        // 数据验证
        xbValidate(MenusValidate::class, $post, 'add');
        $model = new AdminRule;
        if (!$model->save($post)) {
            throw new Exception('添加失败');
        }
        $data = $model->toArray();
        Event::dispatch('common.event.MenuEvent.add.success', $data);
    }

    /**
     * 编辑
     * @param array $post
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(array $post)
    {
        // 数据验证
        xbValidate(MenusValidate::class, $post, 'edit');
        $model = AdminRule::where('id', $post['id'])->find();
        if (!$model) {
            throw new Exception('该用户未注册');
        }
        unset($post['id']);
        if (!$model->save($post)) {
            throw new Exception('编辑失败');
        }
        $data = $model->toArray();
        Event::dispatch('common.event.MenuEvent.edit.success', $data);
    }

    /**
     * 删除
     * @param array $post
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(array $post)
    {
        if (empty($post['id'])) {
            throw new Exception('参数错误');
        }
        $model = AdminRule::where('id', $post['id'])->find();
        if (!$model) {
            throw new Exception('该菜单不存在');
        }
        $data = $model->toArray();
        if (!$model->delete()) {
            throw new Exception('删除失败');
        }
        Event::dispatch('common.event.MenuEvent.del.success', $data);
    }
}
