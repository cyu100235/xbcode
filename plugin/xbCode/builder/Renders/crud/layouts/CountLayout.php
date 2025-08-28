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
namespace plugin\xbCode\builder\Renders\crud\layouts;

use plugin\xbCode\builder\Components\Custom\Statistic;

/**
 * 数据统计布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait CountLayout
{
    /**
     * 统计组件列数
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected int $countCol = 6;

    /**
     * 统计组件列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $count = [];

    /**
     * 添加统计组件
     * @param string $title
     * @param int|float $value
     * @return Statistic
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderCount(string $title, int|float $value)
    {
        $component = new Statistic;
        $component->title($title);
        $component->value($value);
        $this->count[] = $component;
        return $component;
    }

    /**
     * 获取统计组件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getCount()
    {
        return [
            'type' => 'xbCount',
            'col' => $this->countCol,
            'list' => $this->count,
        ];
    }
}
