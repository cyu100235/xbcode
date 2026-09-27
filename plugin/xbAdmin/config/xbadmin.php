<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 *
 * 站点信息（网站名称、LOGO、备案号、组织信息、版权、登录验证码开关等）已改为读取后台配置项，
 * 字段定义见 plugin/xbAdmin/setting/system.php 与 setting/webicp.php，
 * 由 plugin/xbAdmin/api/SiteEntry.php 通过 plugin\xbCode\api\Config 读取后
 * 经 admin/Index/site 接口下发给前端，本文件不再维护这些键（版本号取自 plugin.json）。
 *
 * 说明：
 *   1. 枚举型取值统一使用字符串，'10' 表示否/关闭，'20' 表示是/开启；
 *   2. public_api / public_view 留空时由 SiteEntry 自动生成指向本插件的地址，
 *      非必要不要手工填写，避免插件标识或模块变更后地址失效；
 *   3. 修改本文件后需要重启服务才会生效（配置在进程启动时加载）。
 */

return [
    // ------------------------------ 登录设置 ------------------------------
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

    // 布局与主题默认值已抽离到同目录的 theme.php（config('plugin.xbAdmin.theme')）

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
    // 附件接口地址，留空由 SiteEntry 指向 plugin/xbAdmin/app/admin/controller/UploadController
    'upload_api' => [],
    // 附件分类接口，本插件不含分类管理，留空即可
    'upload_cate_api' => [],
    // 编辑器上传接口，留空由前端复用附件接口
    'editor_upload_api' => [],

    // ------------------------------ 远程组件 ------------------------------
    // 全局组件与图标库，留空表示不使用；
    // 工作台与顶部工具栏已做成自包含远程视图，无需在此注册
    'components' => [],
    'global_components' => [],
    'icons_links' => [],
];
