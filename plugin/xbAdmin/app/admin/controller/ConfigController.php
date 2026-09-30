<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbCode\api\Gateway;
use plugin\xbAdmin\app\BaseController;
use plugin\xbCode\builder\Renders\XbTabForm;

/**
 * 系统设置
 *
 * 分组模板记录在 xb_config_group，字段值读写 xb_config，均以「插件标识 + 分组标识」定位。
 * 插件部署在其他服务器时，安装过程中把 setting/*.php 解析出的分组模板通过 report 接口上报入库。
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ConfigController extends BaseController
{
    /**
     * 系统设置
     *
     * 每个分组渲染为独立选项卡，选项卡内的表单单独提交，仅保存该分组数据。
     * 提交地址携带 _tab 参数标识分组，值为「插件标识_分组标识」。
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->method() === 'PUT') {
            return $this->save($request);
        }
        $result = Gateway::get('xbAdmin/api/Config/index');
        $tabs = $result['data']['tabs'] ?? [];
        $data = $result['data']['data'] ?? [];
        $builder = XbTabForm::make();
        foreach ($tabs as $tab) {
            $builder->addTab($tab['name'], $tab['title'], $tab['body']);
        }
        $builder->setData($data);
        $builder->setSaveMethod('PUT');
        return $this->successRes($builder);
    }

    /**
     * 保存指定选项卡（分组）的配置
     * @param Request $request
     * @return Response
     */
    protected function save(Request $request)
    {
        $tab = trim((string) $request->get('_tab', ''));
        $result = Gateway::put('xbAdmin/api/Config/save?_tab=' . urlencode($tab), (array) $request->post());
        return $this->response($result);
    }

    /**
     * 接收插件上报的配置分组
     *
     * 其他服务器上的插件安装时，把 setting 目录下的分组模板上报到这里，
     * 按「插件标识 + 分组标识」幂等写入 xb_config_group，字段初始值写入 xb_config。
     * @param Request $request
     * @return Response
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function report(Request $request)
    {
        $result = Gateway::post('xbAdmin/api/Config/report', (array) $request->post());
        return $this->response($result);
    }

    /**
     * 读取插件配置项
     *
     * 部署在其他服务器上的插件通过该接口读取指定「插件标识 + 分组标识」的配置值。
     * @param Request $request
     * @return Response
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function detail(Request $request)
    {
        $params = array_merge((array) $request->get(), (array) $request->post());
        $result = Gateway::get('xbAdmin/api/Config/detail', $params);
        return $this->response($result);
    }

    /**
     * 写入插件配置项
     *
     * 部署在其他服务器上的插件通过该接口写入指定「插件标识 + 分组标识 + 字段名」的配置值。
     * @param Request $request
     * @return Response
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function update(Request $request)
    {
        $params = array_merge((array) $request->get(), (array) $request->post());
        $result = Gateway::post('xbAdmin/api/Config/update', $params);
        return $this->response($result);
    }
}