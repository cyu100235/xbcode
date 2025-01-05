<?php
namespace app\admin\controller;

use support\Request;
use app\model\WebNotice;
use xbcode\XbController;

/**
 * 系统公告
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class NoticeController extends XbController
{
    /**
     * 不需要登录的方法
     * @var array
     */
    protected $noLogin = [
        'index',
        'detail',
    ];

    /**
     * 不需要服务端登录的方法
     * @var array
     */
    protected $serverLogin = [
        'index',
        'detail',
    ];

    /**
     * 获取系统公告列表
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $data = WebNotice::where('state', '20')->order('id desc')->select();
        return $this->successRes($data);
    }
    
    /**
     * 获取系统公告详情
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function detail(Request $request)
    {
        $id = $request->get('id');
        if ($id) {
            $data = WebNotice::where('id', $id)->find();
            if (!$data) {
                return $this->fail('公告不存在');
            }
            return $this->successRes($data);
        }
        return $this->view('view/admin/notice');
    }
}
