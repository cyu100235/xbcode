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
namespace plugin\xbCode\builder\Renders\crud\column;

use Exception;
use plugin\xbCode\builder\Components\Card;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 表格列组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait CardColumn
{
    /**
     * 添加卡片列
     * @param string $name 列名
     * @param string $label 列标签
     * @param array $fields 卡片字段
     * - title 标题
     * - subTitle 副标题
     * - image 图片URL
     * @param callable|array $option 列配置选项
     * @return TableColumn|Card
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnCard(string $name, string $label, array $fields, callable|array $option = [])
    {
        /** @var TableColumn|Card */
        $component = $this->useCustomColumn(Card::class, $name, $label, $option);
        // 设置卡片属性
        if (empty($fields['title'])) {
            throw new Exception('请设置单元格卡片标题');
        }
        $data = [
            'title' => "<%= this.{$fields['title']} %>",
            'avatarClassName' => 'thumb-sm',
        ];
        if (!empty($fields['subTitle'])) {
            $data['description'] = "<%= this.{$fields['subTitle']} %>";
        }
        if (!empty($fields['image'])) {
            $data['avatar'] = '${' . $fields['image'] . '|raw}';
        }
        $component->header($data);
        return $component;
    }
}
