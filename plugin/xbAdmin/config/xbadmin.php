<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 *
 * 本文件是后台 SPA 的唯一配置来源，由 plugin/xbAdmin/api/SiteEntry.php 读取后
 * 通过 admin/Index/site 接口下发给前端，不依赖任何数据库配置表。
 *
 * 说明：
 *   1. 枚举型取值统一使用字符串，'10' 表示否/关闭，'20' 表示是/开启；
 *   2. public_api / public_view 留空时由 SiteEntry 自动生成指向本插件的地址，
 *      非必要不要手工填写，避免插件标识或模块变更后地址失效；
 *   3. 修改本文件后需要重启服务才会生效（配置在进程启动时加载）。
 */

use plugin\xbCode\api\PluginJson;

return [
    // ------------------------------ 站点信息 ------------------------------
    // 系统名称，显示在登录页、顶部导航、工作台
    'web_name' => '后台权限管理系统',
    // 站点 LOGO 地址，留空则只显示系统名称
    'web_logo' => '',
    // 系统版本号
    'web_version' => PluginJson::get('version', '1.0.0', dirname(__DIR__) . '/plugin.json'),
    // 站点访问地址，用于版权信息中的 {WEB_URL} 占位符
    'web_url' => '',
    // ICP 备案号，留空不展示
    'web_icp' => '',
    // 公安备案号，留空不展示
    'web_police' => '',
    // 关于我们的名称与链接，两者需同时填写才会在页面底部展示
    'about_name' => '',
    'about_url' => '',
    // 版权信息，支持占位符：{WEB_NAME} {WEB_URL} {WEB_ICP} {WEB_POLICE}
    'copyright' => 'Copyright © {WEB_NAME} All Rights Reserved',

    // ------------------------------ 登录设置 ------------------------------
    // 登录验证码开关：10关闭，20开启
    // 开启后登录页会请求 public_api.captcha 获取图形验证码
    'captcha_state' => '10',
    'login' => [
        // 登录页标题
        'login_title' => '后台权限管理系统',
        // 登录页副标题
        'login_desc' => '专业的 RBAC 权限管理解决方案',
        // 登录页背景图地址，留空使用前端内置背景
        'login_bg' => '',
        // 登录页广告图地址，留空不展示
        'login_ad' => '',
        // 注册地址，留空不展示注册入口
        'register' => '',
        // 找回密码地址，留空不展示找回入口
        'findpwd' => '',
        // 返回站点地址，留空不展示返回入口
        'back_url' => '',
        // 第三方登录方式，形如 [['title'=>'企业微信','url'=>'','icon'=>'']]
        'other_login' => [],
    ],

    // ------------------------------ 布局设置 ------------------------------
    // 键名与前端 layouts 接口保持一致（小驼峰），此处缺省的键由接口自动补全
    'layout' => [
        // 布局模式：default 默认布局 | sideBar 侧边双栏 | user 用户中心
        'layoutMode' => 'sideBar',
        // 主题类型：light 浅色 | dark 深色 | OS 跟随系统
        'theme' => 'OS',
        // 主题自定义样式，留空使用内置主题
        'themeCss' => '',
        // 侧边菜单是否默认折叠
        'isCollapse' => false,
        // 底部高度
        'footerHeight' => 40,
        // 顶部高度
        'headerHeight' => 60,
        // 图标尺寸
        'logoSize' => 40,
        // 侧边栏展开宽度
        'sideMenuOrdinaryWidth' => 200,
        // 侧边栏折叠宽度
        'sideMenuCollapseWidth' => 64,
    ],

    // ------------------------------ 顶部工具栏 ------------------------------
    // 渲染在顶部导航右侧的扩展链接，用户菜单、退出登录等由前端内置提供
    // 形如：['title' => '名称', 'url' => '地址', 'target' => '_blank', 'tips' => '提示', 'sort' => 0]
    'toolbar' => [
        [
            'title' => '官方文档',
            'url' => 'http://doc.xbcode.net',
            'target' => '_blank',
            'tips' => '打开积木云开发文档',
            'sort' => 10,
        ],
    ],

    // ------------------------------ 接口与视图地址 ------------------------------
    // 公共接口地址，留空由 SiteEntry 指向 plugin/xbAdmin/app/admin/controller/PublicsController
    'public_api' => [],
    // 公共视图地址，留空由 SiteEntry 指向 plugin/xbAdmin/app/admin/view 下的远程视图
    'public_view' => [],

    // ------------------------------ 上传与编辑器 ------------------------------
    // 本插件不含附件管理，以下地址留空即可；如需上传能力由对应插件补齐
    'upload_api' => [],
    'upload_cate_api' => [],
    'editor_upload_api' => [],

    // ------------------------------ 远程组件 ------------------------------
    // 全局组件与图标库，留空表示不使用；
    // 工作台与顶部工具栏已做成自包含远程视图，无需在此注册
    'components' => [],
    'global_components' => [],
    'icons_links' => [],
];
