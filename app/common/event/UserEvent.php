<?php
namespace app\common\event;

use app\admin\validate\UserValidate;
use Webman\Event\Event;
use app\model\User;
use Exception;

/**
 * 用户事件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UserEvent
{
    /**
     * 用户注册
     * @param array $post
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function UsernameRegister(array $post)
    {
        // 数据验证
        xbValidate(UserValidate::class, $post, 'usernameRegister');
        $model = new User;
        if (!$model->save($post)) {
            throw new Exception('添加失败');
        }
        $user = $model->toArray();
        $data = array_merge($post, $user);
        Event::dispatch('common.event.UserEvent.UsernameRegister.success', $data);
    }

    /**
     * 短信注册
     * @param array $post
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function SmsRegister(array $post)
    {
        // 数据验证
        xbValidate(UserValidate::class, $post, 'smsRegister');
        $model = new User;
        if (!$model->save($post)) {
            throw new Exception('添加失败');
        }
        $user = $model->toArray();
        $data = array_merge($post, $user);
        Event::dispatch('common.event.UserEvent.SmsRegister.success', $data);
    }

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
        xbValidate(UserValidate::class, $post, 'add');
        $model = new User;
        if (!$model->save($post)) {
            throw new Exception('添加失败');
        }
        $user = $model->toArray();
        $data = array_merge($post, $user);
        Event::dispatch('common.event.UserEvent.add.success', $data);
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
        xbValidate(UserValidate::class, $post, 'edit');
        $model = User::where('id', $post['id'])->find();
        if (!$model) {
            throw new Exception('该用户未注册');
        }
        unset($post['id']);
        if (!$model->save($post)) {
            throw new Exception('编辑失败');
        }
        $user = $model->toArray();
        $data = array_merge($post, $user);
        Event::dispatch('common.event.UserEvent.edit.success', $data);
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
        $model = User::where('id', $post['id'])->find();
        if (!$model) {
            throw new Exception('该用户未注册');
        }
        $user = $model->toArray();
        $data = array_merge($post, $user);
        if (!$model->delete()) {
            throw new Exception('删除失败');
        }
        Event::dispatch('common.event.UserEvent.del.success', $data);
    }
}
