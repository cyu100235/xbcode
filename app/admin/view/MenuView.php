<?php
namespace app\admin\view;

use app\common\builder\FormBuilder;
use app\common\providers\DictProvider;
use app\common\providers\MenuProvider;
use FormBuilder\Factory\Elm;

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
        $builder->addRow('title', 'input', '菜单名称', '', [
            'col' => 12,
            'prompt' => '左侧菜单名称，顶级菜单时，尽可能2个字'
        ]);
        $builder->addRow('pid', 'cascader', '父级菜单', [0], [
            'options' => MenuProvider::getCascaderOptions(),
            'prompt' => '如不选择，则是顶级菜单',
            'col' => 12,
            'props' => [
                'props' => [
                    'checkStrictly' => true,
                ],
            ],
        ]);
        $builder->addRow('component', 'select', '菜单类型', 'none/index', [
            'options' => DictProvider::get('componentText')->options(),
            'col' => 12,
            'prompt' => '<div>选择顶级菜单时，默认即可</div>',
            // 使用联动组件
            'control' => self::control(),
        ]);
        $builder->addRow('plugin_name', 'input', '插件标识', '', [
            'col' => 12,
            'prompt' => '不填写则为系统菜单，填写插件标识，区分大小写',
        ]);
        $builder->addRow('module_name', 'input', '模块名称', '', [
            'col' => 12,
            'prompt' => 'app目录下模块名称，不填写则无模块，自动转小写',
        ]);
        $builder->addRow('path', 'input', '路由地址', '', [
            'col' => 12,
            'prompt' => '示例：Index/index，顶级菜单任意，区分大小写',
        ]);
        $builder->addRow('methods', 'checkbox', '请求类型', ['GET'], [
            'options' => DictProvider::get('methodsText')->options(),
            'col' => 12,
        ]);
        $builder->addRow('icon', 'icons', '菜单图标', '', [
            'col' => 12,
        ]);
        $builder->addRow('is_show', 'radio', '显示隐藏', '10', [
            'options' => DictProvider::get('showText')->options(),
            'col' => 12,
        ]);
        return $builder;
    }

    /**
     * 表单联动
     * @return array[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function control()
    {
        $data = [
            [
                'value' => 'remote/index',
                'where' => '==',
                'rule' => [
                    Elm::input('params', '远程组件')
                        ->appendRule('suffix', [
                            'type' => 'prompt-tip',
                            'props' => [
                                'text' => '<div>填写示例：vue/index，则会自动加载</div><div>http://www.xxx.com/vue/index.vue 文件作为渲染</div>',
                            ]
                        ])
                        ->col([
                            'span' => 12,
                        ]),
                ],
            ],
            [
                'value' => 'remote/index',
                'where' => '!=',
                'rule' => [
                    Elm::input('params', '附带参数')
                        ->appendRule('suffix', [
                            'type' => 'prompt-tip',
                            'props' => [
                                'text' => '<div>地址栏参数（选填）</div><div>格式：name=楚羽幽&sex=男</div>',
                            ]
                        ])
                        ->col([
                            'span' => 12,
                        ]),
                ],
            ],
        ];
        return $data;
    }
}