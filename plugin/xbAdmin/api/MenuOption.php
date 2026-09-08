<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use plugin\xbAdmin\app\model\AdminRule;
use plugin\xbAdmin\utils\DataUtil;

/**
 * 菜单选项接口
 *
 * 主要为「父级菜单」选择器提供级联选项数据。
 */
class MenuOption
{
    /**
     * 获取级联选项
     *
     * 按钮（type=30）只能挂在菜单下，不能作为父级，这里直接排除。
     * @return array
     */
    public static function getCascaderOptions(): array
    {
        $data = AdminRule::where('type', '<>', '30')
            ->order('sort asc,id asc')
            ->select()
            ->toArray();
        $data = DataUtil::channelLevel($data, 0, '', 'id', 'pid');
        $data = static::getChildrenOptions($data);
        return array_merge([
            [
                'label' => '顶级菜单（顶级）',
                'value' => 0,
            ],
        ], $data);
    }

    /**
     * 获取子级选项
     * @param array $data
     * @return array
     */
    public static function getChildrenOptions(array $data): array
    {
        $list = [];
        foreach ($data as $value) {
            $item = [
                'label' => $value['title'] ?? '',
                'value' => $value['id'] ?? 0,
                'disabled' => !empty($value['disabled']),
            ];
            if (!empty($value['children'])) {
                $item['children'] = static::getChildrenOptions($value['children']);
            }
            $list[] = $item;
        }
        return $list;
    }
}
