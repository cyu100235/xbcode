<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\api\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\app\model\Config;
use plugin\xbAdmin\app\BaseController;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

/**
 * 系统设置接口
 *
 * 分组模板记录在 xb_config_group，字段值读写 xb_config，均以「插件标识 + 分组标识」定位。
 * 插件部署在其他服务器时，安装过程中把 setting/*.php 解析出的分组模板通过 report 接口上报入库。
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ConfigController extends BaseController
{
    /**
     * 系统设置选项卡数据
     *
     * 每个分组渲染为独立选项卡，选项卡内的表单单独提交，仅保存该分组数据。
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $tabs = Config::tabs();
        $data = [];
        foreach ($tabs as $tab) {
            $data[$tab['name']] = Config::groupData($tab['plugin'], $tab['group']);
        }
        return $this->successRes(['tabs' => $tabs, 'data' => $data]);
    }

    /**
     * 保存指定选项卡（分组）的配置
     *
     * 提交地址携带 _tab 参数标识分组，值为「插件标识_分组标识」。
     * @param Request $request
     * @throws ExceptionBusiness
     * @return Response
     */
    public function save(Request $request)
    {
        $tab = trim((string) $request->get('_tab', ''));
        if ($tab === '') {
            throw new ExceptionBusiness('缺少配置分组标识');
        }
        $post = (array) $request->post();
        foreach (Config::entries() as $entry) {
            $plugin = (string) $entry['plugin'];
            $group = (string) $entry['group'];
            if ($plugin . '_' . $group === $tab) {
                Config::saveGroup($plugin, $group, $post);
                return $this->success('保存成功');
            }
        }
        throw new ExceptionBusiness('配置分组不存在：' . $tab);
    }

    /**
     * 接收插件上报的配置分组
     *
     * 其他服务器上的插件安装时，把 setting 目录下的分组模板上报到这里，
     * 按「插件标识 + 分组标识」幂等写入 xb_config_group，字段初始值写入 xb_config。
     * @param Request $request
     * @return Response
     */
    public function report(Request $request)
    {
        $post = (array) $request->post();
        $plugin = trim((string) ($post['plugin'] ?? ''));
        if ($plugin === '' || !preg_match('/^[\w\-]+$/', $plugin)) {
            throw new ExceptionBusiness('插件标识错误');
        }
        $groups = $post['groups'] ?? [];
        if (!is_array($groups) || !$groups) {
            throw new ExceptionBusiness('配置分组不能为空');
        }
        $count = Config::register($plugin, $groups);
        return $this->success('上报成功', ['count' => $count]);
    }

    /**
     * 读取插件配置项
     *
     * 部署在其他服务器上的插件通过该接口读取指定「插件标识 + 分组标识」的配置值。
     * @param Request $request
     * @return Response
     */
    public function detail(Request $request)
    {
        [$plugin, $group] = $this->identity($request);
        return $this->success('', Config::groupData($plugin, $group));
    }

    /**
     * 写入插件配置项
     *
     * 部署在其他服务器上的插件通过该接口写入指定「插件标识 + 分组标识 + 字段名」的配置值。
     * @param Request $request
     * @throws ExceptionBusiness
     * @return Response
     */
    public function update(Request $request)
    {
        [$plugin, $group] = $this->identity($request);
        $name = trim((string) ($request->post('name') ?? ''));
        if (!array_key_exists($name, Config::fields($plugin, $group))) {
            throw new ExceptionBusiness('配置字段不存在：' . $name);
        }
        if ($request->post('value') === null) {
            throw new ExceptionBusiness('配置值不能为空：' . $name);
        }
        Config::saveGroup($plugin, $group, [$name => $request->post('value')]);
        return $this->success('保存成功');
    }

    /**
     * 解析请求中的插件标识与分组标识，查询参数与请求体均可
     * @param Request $request
     * @throws ExceptionBusiness
     * @return array [插件标识, 分组标识]
     */
    protected function identity(Request $request): array
    {
        $plugin = trim((string) ($request->post('plugin') ?? $request->get('plugin', '')));
        if ($plugin === '' || !preg_match('/^[\w\-]+$/', $plugin)) {
            throw new ExceptionBusiness('插件标识错误');
        }
        $group = trim((string) ($request->post('group') ?? $request->get('group', '')));
        if ($group === '' || !preg_match('/^[\w\-]+$/', $group)) {
            throw new ExceptionBusiness('配置分组标识错误');
        }
        return [$plugin, $group];
    }
}