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
namespace plugin\xbCode\builder\Renders\Form\rows;

use plugin\xbCode\builder\Components\Flex;

/**
 * 表单项-flex布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormRowFlex
{
    /**
     * 自定义布局
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $customLayout = [];
    
    /**
     * 添加表单项Flex布局
     * @param array $components
     * @return Flex
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowFlex(array $components)
    {
        /** @var Flex */
        $component = Flex::make();
        $component->items($components);
        $this->customLayout[] = $component;
        return $component;
    }
}
