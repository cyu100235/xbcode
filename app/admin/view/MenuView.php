<?php
namespace app\admin\view;

use app\common\builder\FormBuilder;
use app\common\providers\DictProvider;
use app\common\providers\MenuProvider;

class MenuView
{
    /**
     * 获取渲染视图
     * @return FormBuilder
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function formView()
    {
        $builder = new FormBuilder;
        $rule = self::menuTypeRule();
        $builder->addControl('type', 'radio', '菜单类型', '10', $rule, [
            'options' => DictProvider::get('menuTypeText')->options(),
        ]);
        $builder->addRow('pid', 'cascader', '父级菜单', [0], [
            'options' => MenuProvider::getCascaderOptions(),
            'prompt' => '如不选择，则是顶级菜单',
            'props' => [
                'props' => [
                    'checkStrictly' => true,
                ],
            ],
        ]);
        $builder->addRow('title', 'input', '菜单名称', '', [
            'prompt' => '左侧菜单名称，顶级菜单时，尽可能2个字'
        ]);
        $rule = self::menuComponentType();
        $builder->addControl('component', 'select', '菜单组件', 'none/index', $rule, [
            'options' => DictProvider::get('componentText')->options(),
            'prompt' => '选择顶级菜单时，默认即可',
        ]);
        $builder->addRow('plugin_name', 'input', '插件标识', '', [
            'prompt' => '不填写则为系统菜单，填写插件标识，区分大小写',
        ]);
        $builder->addRow('module_name', 'input', '模块名称', '', [
            'prompt' => 'app目录下模块名称，不填写则无模块，自动转小写',
        ]);
        $builder->addRow('path', 'input', '菜单地址', '', [
            'prompt' => '示例：Index/index，顶级菜单任意，区分大小写',
        ]);
        $builder->addRow('params', 'input', '附带参数', '', [
            'prompt' => '（选填）地址栏参数，示例：name=楚羽幽&sex=男',
        ]);
        $builder->addRow('remote', 'input', '远程组件', '', [
            'prompt' => '填写示例：vue/index，则自动加载http://xxx.com/vue/index.vue 作为渲染',
        ]);
        $builder->addRow('methods', 'checkbox', '请求类型', ['GET'], [
            'options' => DictProvider::get('methodsText')->options(),
        ]);
        $builder->addRow('icon', 'icons', '菜单图标', '', [
        ]);
        $builder->addRow('is_show', 'radio', '显示隐藏', '10', [
            'options' => DictProvider::get('showText')->options(),
        ]);
        return $builder;
    }

    /**
     * 菜单类型规则
     * @return array[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function menuTypeRule()
    {
        return [
            // 目录
            [
                'value' => '10',
                'rule' => [
                    'pid',
                    'title',
                    'path',
                    'icon',
                    'is_show',
                ],
            ],
            // 菜单
            [
                'value' => '20',
                'rule' => [
                    'pid',
                    'title',
                    'path',
                    'icon',
                    'is_show',
                    'component',
                    'params',
                    'plugin_name',
                    'module_name',
                    'methods',
                    'icon',
                    'is_show',
                ],
            ],
            // 按钮
            [
                'value' => '30',
                'rule' => [
                    'pid',
                    'title',
                    'path',
                    'component',
                    'params',
                    'plugin_name',
                    'module_name',
                    'methods',
                ],
            ],
        ];
    }

    /**
     * 菜单组件类型规则
     * @return array[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function menuComponentType()
    {
        return [
            // 附带参数
            [
                'value' => 'remote/index',
                'condition' => '!=',
                'rule' => [
                    'params',
                ],
            ],
            // 远程组件
            [
                'value' => 'remote/index',
                'condition' => '==',
                'rule' => [
                    'remote',
                ],
            ],
        ];
    }
}