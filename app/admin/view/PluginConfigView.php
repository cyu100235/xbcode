<?
namespace app\admin\view;

use app\common\providers\ConfigProvider;
use app\common\builder\FormBuilder;

/**
 * 插件视图
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginConfigView
{
    /**
     * 插件配置视图
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function config()
    {
        $name = request()->get('name', '');
        // 获取插件配置
        $data = config("plugin.{$name}.settings.config", []);
        // 获取配置选中
        $active = current($data)['name'] ?? '';
        $builder = new FormBuilder;
        $builder->initTabsActive('active', $active);
        // 遍历配置
        foreach ($data as $value) {
            // 获取子配置数据
            $formView = config("plugin.{$name}.settings.{$value['name']}", []);
            if (empty($formView)) {
                continue;
            }
            // 获取配置JSON
            $builder2 = self::getFormView($formView);
            // 查询配置数据
            $model = ConfigProvider::get("{$name}_{$value['name']}", '', [], ['parse' => false]);
            // 设置配置数据
            if ($model) {
                $builder2->setFormData($model);
            }
            // 获取配置视图
            $viewJson = $builder2->getBuilder()->formRule();
            // 添加配置视图
            $builder->addPanel($value['name'], $value['title'], $viewJson);
        }
        $builder->endTabs();
        return $builder;
    }

    /**
     * 配置子表单
     * @param array $data
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getFormView(array $data)
    {
        $builder = new FormBuilder;
        foreach ($data as $value) {
            $builder->addRow($value['field'], $value['component'], $value['title'], '');
        }
        return $builder;
    }
}