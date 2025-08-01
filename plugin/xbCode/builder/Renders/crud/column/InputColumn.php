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

use plugin\xbCode\builder\Components\Form\InputText;

/**
 * 输入框列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait InputColumn
{
    /**
     * 添加输入框列
     * @param string $name
     * @param string $label
     * @param array $quickEdit
     * @param callable|array $option
     * @throws \Exception
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnInput(string $name, string $label, array $quickEdit = [], callable|array $option = [])
    {
        if (empty($this->useCRUD()->quickSaveItemApi)) {
            throw new \Exception('请先设置【quickSaveItemApi】接口地址');
        }
        /** @var InputText */
        $component = $this->addColumn($name, $label, $option);
        $component->quickEdit([
            'type' => 'input-text',
            'saveImmediately' => true,
            ...$quickEdit,
        ]);
        return $component;
    }
}
