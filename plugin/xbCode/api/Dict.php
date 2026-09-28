<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use RuntimeException;
use Webman\Context;

/**
 * 后台字典数据 HTTP 客户端
 *
 * 后台可能独立部署，插件安装时不再直接写 xb_dict 表，而是把 enum 目录解析出的枚举数据通过 HTTP 上报后台
 * （统一走 Gateway 出口）：
 *   - 读取接口：GET  {GATEWAY_URL}/xbAdmin/admin/Dict/detail（按「plugin + name」读取一个枚举类的字典数据）
 *   - 写入接口：POST {GATEWAY_URL}/xbAdmin/admin/Dict/report（后台按「plugin + name」幂等，一个枚举类一行；
 *     已存在的枚举只按上报的 key 集合合并常量并保留后台调整过的标题、排序与标签、样式）
 *
 * 枚举字典按「插件标识.枚举标识」缓存，缓存挂在请求上下文里，同一次请求内重复读取不再重复查询，
 * 请求结束即失效——后台改完字典，下一次请求立刻生效，无需重启工作进程。
 *
 * 读取时分两种情况：GATEWAY_URL 指向本机且端口与本应用一致时（后台与插件同进程部署，见 Gateway::selfHosted()），
 * 直读本地库；否则通过 HTTP 调用后台接口。这样既支持后台独立部署，又避免同源时请求内
 * 同步自调用造成的死锁。
 *
 * 对外 API：
 *   Dict::values('xbAdmin', 'YesEnum');   // 读取一个枚举类的字典数据
 *   Dict::install($groups, 'xbAdmin');    // 上报 enum 目录解析出的字典分组
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Dict
{
    /**
     * 读取枚举字典数据
     *
     * 按「插件标识 + 枚举标识」读取后台字典，取不到或后台不可用时返回空数组，
     * 由调用方（BaseEnum）回退到本地枚举类常量。
     * @param string $plugin 插件标识
     * @param string $name 枚举标识（枚举类名）
     * @throws RuntimeException
     * @return array 常量列表，每项含 key / label / value / style
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function values(string $plugin, string $name): array
    {
        $cacheKey = 'xb_dict.' . $plugin . '.' . $name;
        // 请求级缓存：同一次请求内多次读取同一枚举只查一次，请求结束自动失效
        if (Context::has($cacheKey)) {
            return (array) Context::get($cacheKey);
        }
        if (Gateway::selfHosted()) {
            // 后台与本应用同源：直读本地库，避免请求内同步自调用（Windows 单进程下必然死锁）
            $values = self::localValues($plugin, $name);
        } else {
            $data = Gateway::request('GET', '/xbAdmin/admin/Dict/detail', [
                'query' => ['plugin' => $plugin, 'name' => $name],
            ]);
            $values = (array) ($data['values'] ?? []);
        }
        Context::set($cacheKey, $values);
        return $values;
    }

    /**
     * 读取本地后台字典数据（网关与本应用同源时使用）
     *
     * 直接查询 xbAdmin 的字典模型，未部署后台插件或未收录该枚举时返回空数组，由调用方回退枚举常量。
     * @param string $plugin 插件标识
     * @param string $name 枚举标识（枚举类名）
     * @return array 常量列表，每项含 key / label / value / style
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function localValues(string $plugin, string $name): array
    {
        $class = 'plugin\\xbAdmin\\app\\model\\Dict';
        if (!class_exists($class)) {
            return [];
        }
        $model = $class::where('plugin', $plugin)->where('name', $name)->order('id asc')->find();
        if (!$model) {
            return [];
        }
        $values = $model['values'] ?? [];
        return is_array($values) ? $values : [];
    }

    /**
     * 上报字典分组数据
     * @param array $groups 分组数据，每项含 name / title / items
     * @param string $plugin 插件标识
     * @throws RuntimeException
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function install(array $groups, string $plugin = ''): void
    {
        $plugin = trim($plugin);
        if ($plugin === '') {
            throw new RuntimeException('上报字典失败，插件标识错误');
        }
        $groups = array_values(array_filter($groups, function ($group) {
            return is_array($group) && !empty($group['name']) && !empty($group['items']);
        }));
        if (!$groups) {
            return;
        }
        Gateway::request('POST', '/xbAdmin/admin/Dict/report', [
            'json' => ['plugin' => $plugin, 'groups' => $groups],
        ]);
    }
}