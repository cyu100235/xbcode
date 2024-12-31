<?php
namespace app\backend\controller;

use app\model\Upload;
use xbcode\builder\FormBuilder;
use xbcode\builder\ListBuilder;
use xbcode\providers\ConfigProvider;
use xbcode\providers\DictProvider;
use xbcode\XbController;
use support\Request;

/**
 * 附件配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UploadConfController extends XbController
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
        $builder     = new ListBuilder;
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
            'lineHeight' => '25px'
        ]);
        $builder->rowConfig([
            'height' => 50,
            'keyField' => 'engine',
        ]);
        $builder->addActionOptions('操作', [
            'width' => 150
        ]);
        $builder->addRightButton('config', '设置', [
            'type' => 'modal',
            'api' => xbUrl('UploadConf/config'),
            'path' => xbUrl('UploadConf/config'),
        ], [
            'title' => '储存设置',
            'customStyle' => [
                'width' => '500px',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'Edit'
        ]);
        $builder->addColumn('title', '储存方式', [
            'minWidth' => 150,
        ]);
        $builder->addColumn('desc', '储存介绍', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('state', '使用状态', [
            'minWidth' => 180,
            'params' => [
                'type' => 'dict',
                'props' => [
                    'options' => DictProvider::get('useState')->dict(),
                    'style' => [
                        '10' => [
                            'type' => 'danger',
                        ],
                        '20' => [
                            'type' => 'success',
                        ],
                    ]
                ],
            ]
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
        $data = Upload::getEngineList();
        return $this->successRes($data);
    }
    
    /**
     * 保存引擎配置
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(Request $request)
    {
        $engine = $request->get('engine', '');
        $data   = Upload::getEngineDetail($engine);
        if ($request->method() === 'PUT') {
            $state = $request->post('state', '10');
            $post  = $request->post();
            // 删除选中状态
            if (isset($post['state'])) {
                unset($post['state']);
            }
            // 设置默认引擎
            if ($state === '20') {
                ConfigProvider::set('upload', 'active', $engine);
            }
            // 保存配置
            ConfigProvider::set('upload', $engine, $post);
            // 返回数据
            return $this->success('保存配置成功');
        }
        // 模板文件
        $config  = ConfigProvider::get('upload', $engine, []);
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        $builder->setPosition('left', 110);
        $builder->addRow('type', 'radio', '储存方式', $engine, [
            'options' => [
                [
                    'label' => $data['title'],
                    'value' => $engine,
                ],
            ],
            'prompt' => $data['prompt']
        ]);
        // 非本地引擎
        if ($engine !== 'local') {
            $template = ConfigProvider::getConfigTemplate("upload_{$engine}");
            foreach ($template as $value) {
                $dataValue = $config[$value['field']] ?? $value['value'];
                $builder->addRow(
                    $value['field'],
                    $value['type'],
                    $value['title'],
                    $dataValue,
                    $value['extra']
                );
            }
        }
        $builder->addRow('state', 'radio', '使用状态', $data['state'], [
            'options' => DictProvider::get('useState')->options(),
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }
}
