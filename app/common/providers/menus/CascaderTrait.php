<?php
namespace app\common\providers\menus;

use app\common\utils\enum\MenuComponent;
use app\common\utils\DataUtil;

/**
 * 菜单服务提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait CascaderTrait
{
    use MenuActionTrait;

    /**
     * 获取多级选项
     * @return array<int|string>[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getCascaderOptions()
    {
        $data = self::menuList();
        $data = DataUtil::channelLevel($data, 0, '', 'id', 'pid');
        $data = self::getChildrenOptions($data);
        $data = array_merge([
            [
                'label' => '顶级权限菜单',
                'value' => 0
            ]
        ], $data);
        return $data;
    }

    /**
     * 获取子级选项
     * @param array $data
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getChildrenOptions(array $data): array
    {
        $list = [];
        $i    = 0;
        foreach ($data as $value) {
            $componentText        = MenuComponent::getLabel($value['component']);
            $title                = "{$value['title']}-{$componentText}";
            $list[$i]['label']    = $title;
            $list[$i]['value']    = $value['id'];
            $list[$i]['disabled'] = false;
            if (!empty($value['disabled'])) {
                $list[$i]['disabled'] = true;
            }
            if ($value['children']) {
                $list[$i]['children'] = self::getChildrenOptions($value['children']);
            }
            $i++;
        }
        return $list;
    }
}