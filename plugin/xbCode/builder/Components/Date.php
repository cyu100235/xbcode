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
 * 日期时间组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/date
 * @method $this type(string $value) 'date'| 'datetime'| 'time'| 'static-date'| 'static-datetime'| 'static-time'
 * @method $this placeholder(string $value) 占位符
 * @method $this valueFormat(string $value) 数据格式，默认为时间戳。更多格式类型请参考 文档
 * @method $this updateFrequency(string $value) 更新频率， 默认为 1 分钟
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this displayFormat(string $value) 展示格式
 * @method $this fromNow(bool $value) 是否显示相对时间描述，比如: 11 小时前、3 天前、1 年前等，fromNow 为 true 时，format 不生效
 * @method $this displayTimeZone(string $value) 设置日期展示时区
 */
class Date extends BaseSchema
{
    public string $type = 'date';

    public function datetime(): Date
    {
        $this->type = 'datetime';
        return $this;
    }

    public function time(): Date
    {
        $this->type = 'time';
        return $this;
    }

    public function staticDate(): Date
    {
        $this->type = 'static-date';
        return $this;
    }

    public function staticDatetime(): Date
    {
        $this->type = 'static-datetime';
        return $this;
    }

    public function staticTime(): Date
    {
        $this->type = 'static-time';
        return $this;
    }
}
