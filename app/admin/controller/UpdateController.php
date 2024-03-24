<?php
namespace app\admin\controller;

use app\common\BaseController;
use app\common\exception\RollBackCodeException;
use app\common\exception\RollBackSqlException;
use xbCloud\CloudService;
use app\common\utils\FrameUpdateUtil;
use think\Request;

/**
 * 系统更新服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UpdateController extends BaseController
{
    /**
     * 获取框架日志
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $data = CloudService::getFrameLogList();
        if (!isset($data['code'])) {
            return $this->fail('云端返回数据格式错误');
        }
        if ($data['code'] !== 200) {
            return $this->successRes($data);
        }
        return $data;
    }

    /**
     * 获取框架授权信息
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function auth(Request $request)
    {
        $data = CloudService::getFrameAuth();
        if (!isset($data['code'])) {
            return $this->fail('云端返回数据格式错误');
        }
        if ($data['code'] !== 200) {
            return $this->successRes($data);
        }
        return $data;
    }

    /**
     * 获取框架升级信息
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getUpdate(Request $request)
    {
        $data = CloudService::getFrameUpdate();
        if (!isset($data['code'])) {
            return $this->fail('云端返回数据格式错误');
        }
        if ($data['code'] !== 200) {
            return $this->successRes($data);
        }
        return $data;
    }

    /**
     * 框架升级处理
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update(Request $request)
    {
        $step  = $request->post('step','');
        if (empty($step)) {
            return $this->fail('升级步骤参数错误');
        }
        try {
            $class = new FrameUpdateUtil($request);
            // 执行升级步骤
            return call_user_func([$class, $step]);
        }
        catch(RollBackCodeException $e){
            // 代码回滚
            throw $e;
        }
        catch(RollBackSqlException $e){
            // 数据与代码同时回滚
            throw $e;
        } catch (\Throwable $e) {
            // 普通报错
            throw $e;
        }
    }
}
