<?php
namespace plugin\xbUpload\app\admin\controller;

use support\Request;
use plugin\xbCode\XbController;
use plugin\xbConfig\api\ConfigApi;
use plugin\xbUpload\api\EngineApi;
use plugin\xbConfig\api\ConfigView;
use plugin\xbUpload\enum\UseStateEnum;
use plugin\xbCode\builder\FormBuilder;
use plugin\xbCode\builder\ListBuilder;
use plugin\xbUpload\enum\UseStateStyleEnum;
use plugin\xbUpload\app\model\UploadEngine;

/**
 * 引擎管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class EngineController extends XbController
{
    /**
     * 引擎列表
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        // 表格渲染
        $builder = new ListBuilder;
        $description = <<<STR
        <div>1.使用对象存储，需要将public目录下的资源文件保留原来目录路径传输到对象存储空间。</div>
        <div>2.需要在对象存储后台设置域名跨域，否则图片生成场景无法使用，例海报合成等。</div>
        <div>3.需将对象存储的图片域名添加到微信小程序官方后台request合法域名和downloadFile合法域名。</div>
        STR;
        $builder->addDesc($description, [
            'type' => 'warning',
            'showIcon' => true,
            'closable' => false,
        ], [
            'color' => '#f56c6c',
            'lineHeight' => '25px',
        ]);
        $builder->rowConfig([
            'height' => 50,
            'keyField' => 'name',
        ]);
        $builder->addActionOptions('操作', [
            'width' => 150,
        ]);
        $builder->addRightButton('config', '设置', [
            'type' => 'modal',
            'api' => xbUrl('Engine/config'),
            'path' => xbUrl('Engine/config'),
            'aliasParams' => [
                'name' => 'engine',
            ],
        ], [
            'title' => '储存设置',
        ], [
            'type' => 'primary',
            'icon' => 'Edit',
        ]);
        $builder->addColumn('title', '储存方式', [
            'minWidth' => 150,
        ]);
        $builder->addColumn('desc', '储存介绍', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('state', '默认使用', [
            'minWidth' => 180,
            'params' => [
                'type' => 'dict',
                'props' => [
                    'options' => UseStateEnum::dict(),
                    'style' => UseStateStyleEnum::style(),
                ],
            ],
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 引擎列表
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $data = EngineApi::getList();
        return $this->successRes($data);
    }

    /**
     * 配置引擎
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(Request $request)
    {
        $engine = $request->get('engine', '');
        $model = UploadEngine::where('name', $engine)->find();
        if (!$model) {
            return $this->fail('引擎不存在');
        }
        $fileName = 'upload';
        // 配置模板路径
        $path = "{$model['plugin']}/upload";
        if ($request->method() === 'PUT') {
            $post = $request->post();
            $post["{$fileName}.{$engine}.type"] = $engine;
            $state = (string) $request->post('state', '10');
            // 删除选中状态
            if (isset($post['state'])) {
                unset($post['state']);
            }
            // 删除文字引擎
            if (isset($post['type'])) {
                unset($post['type']);
            }
            // 设置默认引擎
            if ($state === '20') {
                ConfigApi::set([
                    "{$fileName}.active" => $engine,
                ]);
            }
            // 保存配置
            ConfigApi::set($post);
            // 返回数据
            return $this->success('保存配置成功');
        }
        // 转换数据为数组
        $data = $model->toArray();
        // 获取选中状态
        $active = (string) ConfigApi::get("{$fileName}.active", '', [
            'layer' => false
        ]);
        // 是否启用
        $state = $data['name'] === $active ? '20' : '10';
        // 获取配置数据
        $config = ConfigApi::get($path, [], [
            'layer' => false,
            'replace' => false,
        ]);
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        $builder->setPosition('left', 110);
        $builder->addRow('type', 'xbInfo', '储存方式', $data['title'], [
            'prompt' => $data['prompt'],
        ]);
        $template = ConfigView::getConfigTemplate($path);
        if ($template) {
            foreach ($template as $value) {
                $field = str_replace("{$fileName}.", "{$fileName}.{$engine}.", $value['field']);
                $dataValue = $config[$field] ?? $value['value'];
                $builder->addRow(
                    $field,
                    $value['type'],
                    $value['title'],
                    $dataValue,
                    $value['extra'],
                );
            }
        }
        $builder->addRow('state', 'radio', '使用状态', $state, [
            'options' => UseStateEnum::options(),
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }
}
