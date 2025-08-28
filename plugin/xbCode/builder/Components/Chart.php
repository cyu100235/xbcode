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
 * 图表组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/chart
 * @method $this className(string $value) 设置外层 Dom 的类名
 * @method $this body(SchemaNode $value) 内容容器
 * @method $this api(ApiSchema $value) 配置项接口地址
 * @method $this source(DataMappingSchema $value) 通过数据映射获取数据链中变量值作为配置
 * @method $this initFetch(bool $value) 组件初始化时，是否请求接口
 * @method $this interval(int $value) 刷新时间(最小 1000)
 * @method $this config(mixed $value) 设置 eschars 的配置项,当为string的时候可以设置 function 等配置项
 * @method $this style(array $value) 设置根元素的 style
 * @method $this width(string $value) 设置根元素的宽度
 * @method $this height(string $value) 设置根元素的高度
 * @method $this replaceChartOption(bool $value) 每次更新是完全覆盖配置项还是追加？
 * @method $this trackExpression(string $value) 当这个表达式的值有变化时更新图表
 * @method $this dataFilter(string $value) 自定义 echart config 转换，函数签名：function(config, echarts, data) {return config;} 配置时直接写函数体。其中 config 是当前 echart 配置，echarts 就是 echarts 对象，data 为上下文数据。
 * @method $this mapURL(ApiSchema $value) 地图 geo json 地址
 * @method $this mapName(string $value) 地图名称
 * @method $this loadBaiduMap(bool $value) 加载百度地图
 */
class Chart extends BaseSchema
{
    public string $type = 'chart';
}
