<?php

namespace app\model;

use xbcode\Model;

/**
 * 管理员日志模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminLog extends Model
{
    /**
     * 添加日志
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function addLog(array $data)
    {
        $result = [
            'admin_id' => $data['admin_id'] ?? 0,
            'admin_name' => $data['admin_name'] ?? '',
            'real_ip' => $data['real_ip'] ?? '',
            'city_name' => $data['city_name'] ?? '',
            'method' => $data['method'] ?? '',
            'menu_title' => $data['title'] ?? '',
            'path' => $data['path'] ?? '',
            'query' => $data['query'] ?? '',
            'result' => $data['result'] ?? '',
        ];
        if (!self::create($result)) {
            throw new \Exception('管理员操作日志记录失败');
        }
    }
}
