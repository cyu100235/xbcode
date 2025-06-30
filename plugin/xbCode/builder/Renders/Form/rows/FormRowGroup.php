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

use plugin\xbCode\builder\Components\Form\Group;

/**
 * 表单项-分组
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormRowGroup
{
    /**
     * 自定义布局
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $customLayout = [];
    
    /**
     * 添加表单项分组
     * @param array $components
     * @return Group
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowGroup(array $components)
    {
        /** @var Group */
        $component = Group::make();
        $component->body($components);
        $this->customLayout[] = $component;
        return $component;
    }
}
