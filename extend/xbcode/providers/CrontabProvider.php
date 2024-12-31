<?php

namespace xbcode\providers;

use app\model\Crontab;
use app\validate\CrontabValidate;

/**
 * 定时任务服务提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class CrontabProvider
{
    /**
     * 获取定时任务
     * @param string $name 任务标识
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get(string $name)
    {
        $model = Crontab::where('name', $name)->find();
        return $model;
    }

    /**
     * 添加定时任务
     * @param array $data
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function add(array $data)
    {
        xbValidate(CrontabValidate::class, $data, 'add');
        $model = new Crontab;
        return $model->save($data);
    }

    /**
     * 修改定时任务
     * @param string $name
     * @param array $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function edit(string $name, array $data)
    {
        xbValidate(CrontabValidate::class, $data, 'edit');
        $model = Crontab::where('name', $name)->find();
        if (!$model) {
            throw new \Exception('定时任务不存在');
        }
        return $model->save($data);
    }

    /**
     * 删除定时任务
     * @param string $name
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function delete(string $name)
    {
        $model = Crontab::where('name', $name)->find();
        if (!$model) {
            throw new \Exception('定时任务不存在');
        }
        return $model->delete();
    }
}