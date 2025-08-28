<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components;

/**
 * 服务组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/service
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this body(array $value) 内容容器
 * @method $this api(mixed $value) 初始化数据域接口地址
 * @method $this ws(string $value) WebScocket 地址
 * @method $this dataProvider(string $value) 数据获取函数
 * @method $this initFetch(bool $value) 是否默认拉取
 * @method $this schemaApi(mixed $value) 用来获取远程 Schema 接口地址
 * @method $this initFetchSchema(bool $value) 是否默认拉取 Schema
 * @method $this messages(array $value) 消息提示覆写，默认消息读取的是接口返回的 toast 提示文字，但是在此可以覆写它
 * @method $this interval(int $value) 轮询时间间隔，单位 ms(最低 1000)	
 * @method $this silentPolling(bool $value) 配置轮询时是否显示加载动画
 * @method $this stopAutoRefreshWhen(string $value) 配置停止轮询的条件
 * @method $this showErrorMsg(bool $value) 是否以 Alert 的形式显示 api 接口响应的错误信息，默认展示
 */
class Service extends BaseSchema
{
    public string $type = 'service';

    /**
     * 设置服务接口类型
     * @param string $method
     * @param string $field
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function method(string $method = 'GET', string $field = 'schemaApi')
    {
        $this->setVariable($field, [
            'method' => $method,
            'url' => $this->$field
        ]);
        return $this;
    }
}
