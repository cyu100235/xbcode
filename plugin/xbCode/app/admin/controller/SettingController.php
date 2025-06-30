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

use plugin\xbCode\api\Url;
use support\Request;
use plugin\xbCode\XbController;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\ConfigView;

/**
 * 系统配置控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SettingController extends XbController
{
    public array $noLogin = [
        'test'
    ];

    /**
     * 配置项
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(Request $request)
    {
        $type = $request->get('type', '');
        $path = $request->route->param('path', '');
        if(empty($path)) {
            return $this->fail('配置路径不能为空');
        }
        if (request()->method() === 'PUT') {
            $post = request()->post();
            // 保存配置
            ConfigApi::set($path,$post);
            // 返回数据
            return $this->success('保存成功');
        }
        $formData = ConfigApi::get($path, [], [
            'layer' => false,
            'replace' => false,
        ]);
        // 获取配置数据
        $formData = ConfigApi::get("{$path}.*", []);
        // 获取表单视图
        $builder = ConfigView::formView($path, $type);
        $builder->useForm()->wrapWithPanel(false);
        $saveAPI = Url::make("Setting/config/{$path}");
        $builder->setApi($saveAPI);
        $builder->setMethod('PUT');
        $builder->setData($formData);
        return $this->successRes($builder);
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
        $post = [
            'web_name' => '测试网站',
            'web_url' => 'https://www.example.com',
            'web_logo' => 'uploads/logo.png',
            'web_icp' => '黔ICP备12345678号-1',
        ];
        // 以分组名保存配置项
        // ConfigApi::set('system', $post);
        // 以配置文件路径保存配置项
        // ConfigApi::set('xbCode/system', $post);
    }
}