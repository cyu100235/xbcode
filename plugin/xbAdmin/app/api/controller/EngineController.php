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
use plugin\xbAdmin\api\EngineApi;
use plugin\xbAdmin\app\model\Config;
use plugin\xbAdmin\app\BaseController;
use plugin\xbAdmin\app\model\UploadEngine;

/**
 * 引擎接口
 *
 * 引擎记录存于 xb_upload_engine，引擎参数与当前启用的引擎名存于
 * xb_config 的 upload 分组（active 键），二者共同决定实际使用的储存引擎。
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class EngineController extends BaseController
{
    /**
     * 引擎列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $data = EngineApi::make()->getList();
        return $this->successData($data);
    }

    /**
     * 快速编辑
     * @param Request $request
     * @return Response
     */
    public function quickSave(Request $request)
    {
        $name = (string) $request->post('name', '');
        $model = UploadEngine::where('name', $name)->find();
        if (!$model) {
            return $this->fail('云储存引擎不存在');
        }
        // 获取当前选中
        $active = EngineApi::make()->active();
        if ($active === $name) {
            return $this->fail('不可取消，请直接启用其他引擎');
        }
        // 保存选中配置
        Config::saveValues('xbAdmin', 'upload', [
            'active' => $name,
        ]);
        // 返回数据
        return $this->success('保存成功');
    }

    /**
     * 配置引擎
     * @param Request $request
     * @return Response
     */
    public function config(Request $request)
    {
        $name = (string) $request->get('name', '');
        $model = UploadEngine::where('name', $name)->find();
        if (!$model) {
            return $this->fail('云储存引擎不存在');
        }
        // 获取配置模板，引擎配置分组由所属插件声明
        $template = Config::template((string) $model['plugin'], 'upload');
        if ($request->method() === 'PUT') {
            $post = (array) $request->post();
            $state = (string) $request->post('state', '10');
            $validate = '';
            foreach ($template as $item) {
                if (($item['name'] ?? '') === 'xbValidate' && !empty($item['value'])) {
                    $validate = (string) $item['value'];
                    break;
                }
            }
            // 删除无用数据
            unset($post['state'], $post['type']);
            if ($validate) {
                xbValidate($validate, $post);
                unset($post['xbValidate']);
            }
            // 设置默认引擎
            if ($state === '20') {
                Config::saveValues('xbAdmin', 'upload', [
                    'active' => $name,
                ]);
            }
            $data = [
                $name => $post,
            ];
            // 保存配置
            Config::saveValues('xbAdmin', 'upload', $data);
            // 返回数据
            return $this->success('保存配置成功');
        }
        // 转换数据为数组
        $data = $model->toArray();
        // 获取配置数据
        $values = Config::groupValues('xbAdmin', 'upload');
        $config = $values[$name] ?? [];
        return $this->successRes([
            'title' => $data['title'],
            'template' => $template,
            'config' => is_array($config) ? $config : [],
        ]);
    }
}