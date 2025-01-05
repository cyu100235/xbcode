<?php
namespace app\queue;

use Exception;
use Webman\RedisQueue\Consumer;

/**
 * 系统日志队列
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SystemLog implements Consumer
{
    /**
     * 队列消费
     * @param mixed $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function consume($data)
    {
        try {
            if (empty($data)) {
                throw new Exception('日志数据不能为空');
            }
            if (empty($data['admin_id'])) {
                throw new Exception('管理员ID参数错误');
            }
            if (empty($data['admin_name'])) {
                throw new Exception('管理员名称参数错误');
            }
            if (empty($data['real_ip'])) {
                throw new Exception('管理员IP参数错误');
            }
            if (empty($data['path'])) {
                throw new Exception('路由参数错误');
            }
            if (empty($data['method'])) {
                throw new Exception('请求类型参数错误');
            }
            if (empty($data['title'])) {
                throw new Exception('菜单标题参数错误');
            }
            if (empty($data['query'])) {
                throw new Exception('请求参数错误');
            }
            // 默认城市名称
            $data['city_name'] = '未知城市';
            // 实例定位类
            $ip2region = new \Ip2Region;
            // 获取城市名称
            $cityName = $ip2region->simple($data['real_ip']);
            if ($cityName) {
                $data['city_name'] = $cityName;
            }
            // 记录日志
            \app\model\AdminLog::addLog($data);
        } catch (\Throwable $th) {
            throw new Exception("管理员操作日志消费失败：{$th->getMessage()}");
        }
    }
}