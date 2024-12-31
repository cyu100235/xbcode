<?php
namespace xbcode\service\xbcode;

use Exception;

/**
 * 订单插件服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class OrdersPluginService extends XbBaseService
{
    /**
     * 创建订单
     * @param string $name 插件标识
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function create(string $name)
    {
        $service = static::request()->get('OrdersPlugin/create',[
            'name' => $name,
        ]);
        $result = $service->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        if (!isset($result['data']['order_no'])) {
            throw new Exception('订单数据错误');
        }
        return $result['data']['order_no'];
    }

    /**
     * 统一下单
     * @param string $order_no
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function unifiedOrder(string $order_no)
    {
        $service = static::request()->get('OrdersPlugin/unifiedOrder',[
            'order_no' => $order_no,
        ]);
        $result = $service->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        if (!isset($result['data'])) {
            throw new Exception('订单支付数据错误');
        }
        return $result;
    }
    public static function queryOrder()
    {
    }
    
    /**
     * 检查插件是否购买
     * @param string $order_no
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checked(string $order_no)
    {
        $service = static::request()->get('OrdersPlugin/detail',[
            'order_no' => $order_no,
        ]);
        $result = $service->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        $data = $result['data'] ?? [];
        if (!isset($data['state'])) {
            throw new Exception('订单状态错误');
        }
        // 过期时间
        $expireTime = strtotime($data['create_at']) + 600;
        // 检测订单是否过期
        if ($data['state'] === '10' && time() > $expireTime) {
            // 设置订单过期
            self::setExpire($data['order_no']);
            // 抛出异常
            throw new Exception('请重新点击购买');
        }
        return $data['state'] === '20' ? true : false;
    }

    /**
     * 设置订单过期
     * @param string $order_no
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function setExpire(string $order_no)
    {
        $service = static::request()->get('OrdersPlugin/orderExpire',[
            'order_no' => $order_no,
        ]);
        $result = $service->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        return $result;
    }
}