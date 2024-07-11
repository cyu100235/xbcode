<?php

namespace app\controller;

use app\common\utils\EnvironmentUtil;
use app\common\utils\InstallUtil;
use app\common\XbController;
use support\Request;

/**
 * 安装控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class InstallController extends XbController
{
    /**
     * 安装页面
     * @param \support\Request $request
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        return $this->adminView('app/common/view/install/index.html');
    }

    /**
     * 安装协议
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function protocol(Request $request)
    {
        // 检测是否已安装
        if (InstallUtil::hasInstall()) {
            return $this->success('success');
        }
        $path    = app_path('common/data/protocol.md');
        $content = file_get_contents($path);
        return $this->successRes([
            'content' => $content,
        ]);
    }

    /**
     * 环境检测
     * @param \support\Request $request
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function environment(Request $request)
    {
        $data = [
            'fun' => EnvironmentUtil::getVerifyFun(),
            'extra' => EnvironmentUtil::getVerifyExtra(),
            'dirData' => EnvironmentUtil::getDirData()
        ];
        return $this->successRes($data);
    }

    /**
     * 安装进行中
     * @param \support\Request $request
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        $step = $request->get('step', '');
        if (empty($step)) {
            return $this->fail('安装步骤参数错误');
        }
        $class = new InstallUtil;
        // 调用安装工具
        return call_user_func([$class, $step], $request);
    }

    /**
     * 安装完成
     * @param \support\Request $request
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function complete(Request $request)
    {
        return $this->success('恭喜您，安装完成...');
    }
}
