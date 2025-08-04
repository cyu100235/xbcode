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
namespace plugin\xbCode\app\admin\controller;

use support\Request;
use plugin\xbCode\XbController;
use plugin\xbCode\trait\ConfigTrait;

/**
 * 系统配置控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SettingController extends XbController
{
    use ConfigTrait;

    /**
     * 配置项
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function system(Request $request)
    {
        return $this->normalConfig();
    }

    /**
     * 版权信息
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function copyright(Request $request)
    {
        return $this->normalConfig();
    }

    public function test()
    {
        // 查询全部配置
        // $config = ConfigApi::get();
        // 分组查询
        // $data = ConfigApi::get('system');
        // 配置文件查询
        // $data = ConfigApi::get('xbCode/system');
        // 获取某条配置
        // $data = ConfigApi::get('system.web_name');
        // 获取多条配置项(不做任何处理)
        // $data = ConfigApi::get('system.web_name,system.web_url');
        // 获取多条配置项(处理层级解析)
        // $data = ConfigApi::get('system.web_name,system.web_url');
        // 获取多层级配置
        // $data = ConfigApi::get('system.web_name');
        // 获取分组旗下配置
        // $data = ConfigApi::get('upload.local.*', []);
        // 解析配置多层级
        // $data = ConfigChecked::getConfigValue($data);
        // p($data);

        // 配置项数据
        // $post = [
        //     'web_name' => '测试网站',
        //     'web_url' => 'https://www.example.com',
        //     'web_logo' => 'uploads/logo.png',
        //     'web_icp' => '黔ICP备12345678号-1',
        // ];
        // 以分组名保存配置项
        // ConfigApi::set('system', $post);
        // 以配置文件路径保存配置项
        // ConfigApi::set('xbCode/system', $post);
    }
}