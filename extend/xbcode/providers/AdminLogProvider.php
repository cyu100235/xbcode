<?php

namespace xbcode\providers;

use app\model\AdminRule;
use support\Request;
use support\Response;

/**
 * 管理员日志提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminLogProvider
{
    /**
     * 添加日志
     * @param \support\Request $request
     * @param \support\Response $response
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function record(Request $request, Response $response)
    {
        $method = $request->method();
        if ($method == 'GET') {
            return;
        }
        // 获取请求地址
        $path = trim(ltrim($request->path(), '/'), '/');
        // 日志类型 10操作日志 20登录日志
        $type = '10';
        // 检测是否登录日志
        if (strpos($path, 'login') !== false) {
            $type = '20';
        }
        // 获取菜单字典
        $menus = AdminRule::getMenuDict();
        // 菜单名称
        $title = $menus[$path]['title'] ?? '未知菜单';
        // 请求参数
        $query = request()->post();
        $query = is_array($query) ? json_encode($query, 256) : $query;
        // 响应结果
        $result = $response->rawBody();
        $result = is_array($result) ? json_encode($result, 256) : $result;
        // 触发响应事件
        QueueProvider::addAsync('SystemLog', [
            'type' => $type,
            'saas_appid' => $request->saasAppid ?? null,
            'admin_id' => $request->uid,
            'admin_name' => $request->username ?? null,
            'real_ip' => $request->getRealIp(true),
            'path' => $path,
            'method' => $method,
            'title' => $title,
            'query' => $query,
            'result' => $result,
        ], '', 10);
    }
}