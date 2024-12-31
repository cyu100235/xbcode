<?php

namespace app\install\controller;

use Exception;
use support\Request;
use xbcode\utils\FrameUtil;
use xbcode\XbController;
use app\install\utils\InstallUtil;
use app\install\utils\EnvironmentUtil;

/**
 * 安装控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 构造方法
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function init()
    {
        // 请求地址
        $path = trim(request()->path(), '/');
        // 检测是否安装
        if (InstallUtil::hasInstall() && $path !== 'install/Index/complete') {
            throw new Exception('已经安装，无需重复安装', 10000);
        }
    }
    
    /**
     * 安装视图
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index()
    {
        return $this->view('public/install/index','html');
    }
    
    /**
     * 安装协议
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function protocol()
    {
        $path = dirname(__DIR__) . '/data/agreement.txt';
        if (!file_exists($path)) {
            return $this->successRes([
                'content' => ''
            ]);
        }
        $content = file_get_contents($path);
        $data = [
            'content' => $content
        ];
        return $this->successRes($data);
    }
    
    /**
     * 环境检测
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function environment()
    {
        $data = EnvironmentUtil::get();
        return $this->successRes($data);
    }
    
    /**
     * 数据库配置检测
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function checked()
    {
        $post = request()->post();
        if (empty($post)) {
            return $this->fail('参数错误');
        }
        try {
            // 验证数据
            InstallUtil::dataChecked($post);
        } catch (\Throwable $th) {
            // 检测是否数据库连接失败
            $errorCode = [
                1040,
                1041,
                1042,
                1043,
                1044,
                1045,
            ];
            if (in_array($th->getCode(), $errorCode)) {
                return $this->fail('数据库连接失败，请检查数据库配置');
            }
            // 返回错误
            return $this->fail($th->getMessage());
        }
        // 返回成功
        return $this->success('数据验证成功');
    }
    
    /**
     * 站点设置检测
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function site()
    {
        $post = request()->post();
        if (empty($post)) {
            return $this->fail('参数错误');
        }
        try {
            // 验证数据
            InstallUtil::siteChecked($post);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage());
        }
        // 返回成功
        return $this->success('数据验证成功');
    }
    
    /**
     * 安装数据
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        // 获取安装步骤
        $step = $request->get('step', '');
        if ($step === 'structure') {
            // 安装表结构
            return InstallUtil::structure($request);
        }else if($step === 'database'){
            // 安装表数据
            InstallUtil::database($request);
            // 返回数据
            return $this->successFul('安装表数据完成...', [
                'next' => 'config'
            ]);
        }else if($step === 'config'){
            // 写入文件配置
            InstallUtil::config($request);
            // 延迟1秒平滑重启框架
            FrameUtil::delayReload(1);
            // 返回数据
            return $this->success('应用安装成功，3秒后跳转...');
        }else{
            // 安装失败
            return $this->fail('安装失败...');
        }
    }
    
    /**
     * 安装完成
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function complete()
    {
        // 返回成功
        return $this->success('应用已经安装');
    }
}
