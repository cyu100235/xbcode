<?php
namespace app\service\cloud;
use app\utils\JsonUtil;
use Exception;
use support\Request;

/**
 * 插件订单云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait PluginsOrderCloud
{
    use JsonUtil;

    /**
     * 创建订单
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function create(Request $request)
    {
        // 获取插件名称
        $name = $request->get("name");
        // 安装版本
        $version = $request->get("version");
        // 参数验证
        if (empty($name) || empty($version)){
            throw new Exception("参数错误");
        }
        $data = [
            'name' => $name,
            'version' => $version,
        ];
        $result = HttpCloud::get('user/Order/create',$data);
        // 验证数据
        $data = HttpCloud::getContent($result);
        // 返回数据
        return self::successRes($data);
    }
}