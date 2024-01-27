<?php

namespace app\common\service\cloud;
use app\common\model\Admin;
use app\common\model\AdminRole;
use app\common\model\Projects;
use app\common\model\UploadCate;
use app\common\validate\ProjectValidate;
use Exception;
use think\facade\Cache;
use think\facade\Db;

/**
 * 应用管理服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ProjectCloud
{
    /**
     * 创建项目
     * @param array $post
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function createdProject(array $post)
    {
        // 数据验证
        xbValidate(ProjectValidate::class, $post, 'add');
        // 开启事务
        Db::startTrans();
        try {
            // 创建项目
            $data  = [
                'title' => $post['title'],
                'app_name' => 'xbaiChat',
                'name' => $post['name'],
                'logo' => $post['logo'],
            ];
            $model = new Projects;
            if (!$model->save($data)) {
                throw new Exception('创建项目失败');
            }
            // 创建项目角色
            $data      = [
                'saas_appid' => $model->id,
                'pid' => 0,
                'title' => "{$post['title']}-超级管理员",
                'is_system' => '20',
            ];
            $roleModel = new AdminRole;
            if (!$roleModel->save($data)) {
                throw new Exception('创建项目角色失败');
            }
            // 创建管理员
            $data       = [
                'role_id' => $roleModel->id,
                'saas_appid' => $model->id,
                'username' => $post['username'],
                'password' => $post['password'],
                'nickname' => "{$post['title']}管理员",
                'status' => '20',
                'is_system' => '20',
            ];
            $adminModel = new Admin;
            if (!$adminModel->save($data)) {
                throw new Exception('创建项目管理员失败');
            }
            // 创建附件分类
            $data = [
                'saas_appid'    => $model->id,
                'title'         => '默认分类',
                'dir_name'      => 'default',
                'is_system'     => '20',
            ];
            $categoryModel = new UploadCate;
            if (!$categoryModel->save($data)) {
                throw new Exception('创建附件分类失败');
            }
            // 删除缓存
            Cache::delete($model['name']);
            // 提交事务
            Db::commit();
        } catch (\Throwable $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 删除项目
     * @param int $id
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function deleteProject(int $id)
    {
        $model = Projects::where('id', $id)->find();
        if (!$model) {
            throw new Exception('项目不存在');
        }
        Db::startTrans();
        try {
            // 删除项目管理员
            if (!Admin::where('saas_appid', $id)->delete()) {
                throw new Exception('删除项目管理员失败');
            }
            // 删除项目分类
            if (!UploadCate::where('saas_appid', $id)->delete()) {
                throw new Exception('删除项目分类失败');
            }
            // 删除项目角色
            if (!AdminRole::where('saas_appid', $id)->delete()) {
                throw new Exception('删除项目角色失败');
            }
            // 删除云端项目
            // 删除项目
            if (!$model->delete()) {
                throw new Exception('删除项目失败');
            }
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }
}
