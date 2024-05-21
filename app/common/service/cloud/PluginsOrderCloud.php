<?php
namespace app\common\service\cloud;
use app\common\service\CloudSerivce;
use app\common\utils\JsonUtil;
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
        // 获取数据
        $name = $request->get("name");
        $version = $request->get("version");
        // 参数验证
        if (empty($name) || empty($version)){
            throw new Exception("参数错误");
        }
        $data = [
            'name' => $name,
            'version' => $version
        ];
        $result = HttpCloud::post('user/Order/create',$data)->array();
        if (empty($result)) {
            return self::fail('获取用户信息失败');
        }
        if (isset($result['code']) && $result['code'] === 12000) {
            return self::failFul('请登录云服务',11000);
        }
        if (isset($result['code']) && $result['code'] != 200) {
            return self::fail($result['msg'], $result['code']);
        }
        // 返回数据
        return self::successRes($result['data'] ?? []);
    }

    /**
     * 统一下订单
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function unifiedOrder(Request $request)
    {
        // 订单编号
        $order_no = $request->get("order_no");
        // 付款类型
        $pay_type = $request->get("pay_type");
        // 参数验证
        if (empty($order_no) || empty($pay_type)){
            throw new Exception("参数错误");
        }
        $data = [
            'order_no' => $order_no,
            'pay_type' => $pay_type,
        ];
        $result = HttpCloud::get('user/Order/unifiedOrder',$data);
        // 验证数据
        $data = HttpCloud::getContent($result);
        // 返回数据
        return self::successFul($result['msg'] ?? '', $data);
    }
}