<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\api\RegionApi;
use plugin\xbCode\builder\Components\Form\NestedSelect;

/**
 * 城市表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait CityRow
{
    /**
     * 添加城市选择组件
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $config
     * @return NestedSelect
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowCity(string $field, string $title, mixed $value = '', callable|array $config = [])
    {
        /** @var NestedSelect */
        $component = $this->addRow(NestedSelect::class, $field, $title, $value, $config);
        $component->onlyLeaf(true);
        $options = $this->getCityOptions($config['options'] ?? []);
        $component->options($options);
        return $component;
    }

    /**
     * 获取城市联级选项
     * @param array $options
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getCityOptions(array $options): array
    {
        if (empty($options)) {
            $options = RegionApi::options();
            if (empty($options)) {
                throw new \Exception('城市联级选项文件不存在或内容为空');
            }
        }
        return $options;
    }
}
