<?php
namespace plugin\crontab;

use app\common\providers\MenuProvider;

/**
 * 插件安装卸载类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Install
{
    /**
     * 安装前
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installBefore()
    {
        p('安装前');
    }
    
    /**
     * 安装
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install()
    {
        // 创建菜单
        $data = [];
        // MenuProvider::createMenu('',$data);
        // 导入SQL
        $sql = __DIR__ . '/data/install.sql';
        p('正在安装');
    }

    /**
     * 安装后
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installAfter()
    {
        p('安装后');
    }

    /**
     * 更新前
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateBefore()
    {
        // 导入SQL
        $sql = __DIR__ . '/data/update.sql';
        p('更新之前');
    }

    /**
     * 更新
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update()
    {
        p('正在更新');
    }

    /**
     * 更新后
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateAfter()
    {
        p('更新之后');
    }

    /**
     * 卸载前
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallBefore()
    {
    }

    /**
     * 卸载
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstall()
    {
        // 删除菜单
        MenuProvider::delMenu('');
        // 导入SQL
        $sql = __DIR__ . '/data/uninstall.sql';
        p('正在卸载');
    }

    /**
     * 卸载后
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallAfter()
    {
    }

    /**
     * 设置插件配置（可不提供）
     * @return array[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config()
    {
        return [
            [
                'field' => 'baseic',
                'title' => '基本配置',
                'children' => [
                    [
                        'field' => 'baseic.web_name',
                        'title' => '网站名称',
                        'value' => '',
                        'component' => 'input',
                        'extra' => [
                            'col' => 12,
                            'prompt' => '应用名称，显示在浏览器标签页',
                        ],
                    ],
                    [
                        'field' => 'baseic.web_url',
                        'title' => '网站域名',
                        'value' => '',
                        'component' => 'input',
                        'extra' => [
                            'col' => 12,
                            'prompt' => '网站链接，以斜杠结尾，如：https://xiaobai.host/',
                        ],
                    ],
                    [
                        'field' => 'baseic.web_title',
                        'title' => '网站标题',
                        'value' => '',
                        'component' => 'input',
                        'extra' => [
                            'col' => 12,
                        ],
                    ],
                    [
                        'field' => 'baseic.web_keywords',
                        'title' => '网站关键字',
                        'value' => '',
                        'component' => 'input',
                        'extra' => [
                            'col' => 12,
                        ],
                    ],
                    [
                        'field' => 'baseic.web_description',
                        'title' => '网站描述',
                        'value' => '',
                        'component' => 'textarea',
                        'extra' => [
                            'rows' => 4,
                            'resize' => 'none',
                            'prompt' => '请勿手动换行，字数在100字以内',
                        ],
                    ],
                    [
                        'field' => 'baseic.web_logo',
                        'title' => '系统图标',
                        'value' => '',
                        'component' => 'uploadify',
                        'extra' => [],
                    ],
                ],
            ],
        ];
    }
}