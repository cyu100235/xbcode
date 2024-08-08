<?php
namespace app\common\event;

use app\admin\validate\MenusValidate;
use app\common\providers\RouteProvider;
use app\common\utils\FrameUtil;
use app\model\AdminRule;
use think\facade\Db;
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
        // 获取父级ID
        if (is_array($post['pid'])) {
            $post['pid'] = end($post['pid']);
        }
        // 获取方法
        if (is_array($post['methods'])) {
            $post['methods'] = implode(',', $post['methods']);
        }
        // 保存数据
        $model = new AdminRule;
        if (!$model->save($post)) {
            throw new Exception('添加菜单失败');
        }
        // 缓存路由
        RouteProvider::cacheMenus();
        // 延迟重启
        FrameUtil::pcntlAlarm(2, function () {
            // 重启服务
            FrameUtil::reload();
        });
        // 转换数据
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
        // 获取父级ID
        if (is_array($post['pid'])) {
            $post['pid'] = end($post['pid']);
        }
        // 获取方法
        if (is_array($post['methods'])) {
            $post['methods'] = implode(',', $post['methods']);
        }
        $model = AdminRule::where('id', $post['id'])->find();
        if (!$model) {
            throw new Exception('该用户未注册');
        }
        unset($post['id']);
        if (!$model->save($post)) {
            throw new Exception('编辑失败');
        }
        // 缓存路由
        RouteProvider::cacheMenus();
        // 延迟重启
        FrameUtil::pcntlAlarm(2, function () {
            // 重启服务
            FrameUtil::reload();
        });
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
        $where = [];
        if (empty($post['id']) && empty($post['ids']) && empty($post['path'])) {
            throw new Exception('请选择要删除的菜单');
        }
        if (!empty($post['id'])) {
            $where[] = ['id', '=', $post['id']];
        }
        if (!empty($post['ids'])) {
            $where[] = ['id', 'in', $post['ids']];
        }
        if (!empty($post['path'])) {
            $where[] = ['path', '=', $post['path']];
        }
        $model = AdminRule::where($where)->find();
        if (!$model) {
            throw new Exception('该菜单不存在');
        }
        if ($model['is_system'] === '20') {
            throw new Exception('系统菜单不允许删除');
        }
        // 查询子菜单
        $childIds = AdminRule::where('pid', $model['id'])->column('id');
        // 转换数据
        $data = $model->toArray();
        // 启动事务
        Db::startTrans();
        try {
            // 批量删除子菜单
            AdminRule::destroy($childIds);
            // 删除主菜单
            if (!$model->delete()) {
                throw new Exception('删除失败');
            }
            // 提交事务
            Db::commit();
        } catch (\Throwable $th) {
            // 回滚事务
            Db::rollback();
            throw $th;
        }
        // 缓存路由
        RouteProvider::cacheMenus();
        // 重启服务
        FrameUtil::pcntlAlarm(2, function () {
            FrameUtil::reload();
        });
        Event::dispatch('common.event.MenuEvent.del.success', $data);
    }
}
