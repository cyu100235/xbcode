<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Form\InputText;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 输入框列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait InputColumn
{
    /**
     * 添加输入框列
     * @param string $name 字段名称
     * @param string $label 标签名称
     * @param array $quickEdit 快速编辑配置
     * - `type` 类型，默认为`input-text`
     * - `saveImmediately` 是否立即保存，默认为`true`，可以配置API地址
     * - `api` 接口地址
     * @throws \Exception
     * @return TableColumn|InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnInput(string $name, string $label, array $quickEdit = [], callable|array $option = [])
    {
        if (empty($this->useCRUD()->quickSaveItemApi)) {
            throw new \Exception('请先设置【quickSaveItemApi】接口地址');
        }
        /** @var TableColumn|InputText */
        $component = $this->addColumn($name, $label, $option);
        $component->quickEdit([
            'type' => 'input-text',
            'saveImmediately' => true,
            ...$quickEdit,
        ]);
        return $component;
    }

    /**
     * 添加输入框列
     * @param string $name 字段名称
     * @param string $label 标签名称
     * @param string $api 接口地址
     * @param array $quickEdit 快速编辑配置
     * @throws \Exception
     * @return InputText|TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnInputApi(string $name, string $label, string $api, array $quickEdit = [])
    {
        if (empty($this->useCRUD()->quickSaveItemApi)) {
            throw new \Exception('请先设置【quickSaveItemApi】接口地址');
        }
        /** @var TableColumn|InputText */
        $component = $this->addColumn($name, $label);
        $component->quickEdit([
            'type' => 'input-text',
            'saveImmediately' => [
                'api' => $api,
            ],
            ...$quickEdit,
        ]);
        return $component;
    }
}
