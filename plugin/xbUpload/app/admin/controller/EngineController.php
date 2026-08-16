<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\app\admin\controller;

use Exception;
use plugin\xbCode\api\Url;
use plugin\xbCode\XbController;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\ConfigView;
use plugin\xbCode\api\PluginsApi;
use plugin\xbUpload\api\EngineApi;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\api\ConfigChecked;
use plugin\xbUpload\enum\UseStateEnum;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbUpload\app\model\UploadEngine;

/**
 * 引擎管理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class EngineController extends XbController
{
    /**
     * 引擎列表
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index()
    {
        $act = request()->get('_act', '');
        if ($act) {
            $data = EngineApi::make()->getList();
            return $this->successData($data);
        }
        $builder = XbCrud::make();
        // 设置快速编辑
        $builder->useCRUD()->quickSaveItemApi(Url::make('quickSave'));

        // 添加表格头部介绍
        $description = <<<HTML
            <div style="line-height:2;">
                <div>1.引擎储存方式分为 本地储存 和 对象存储 两种方式。</div>
                <div>2.如重新切换对象存储，需要将 public/attachment 目录下的资源文件重新上传至新的对象存储空间。</div>
                <div>3.需将对象存储的图片域名添加到微信小程序官方后台request合法域名和downloadFile合法域名。</div>
            </div>
            HTML;
        $builder->addHeaderPrompt($description)->title('温馨提示');

        // 添加表格列
        $builder->addColumn('title', '储存方式');
        $builder->addColumnHtml('desc', '储存介绍');
        $builder->addColumnHtml('prompt', '储存提示词');
        $builder->addColumnSwitch('state', '默认使用', UseStateEnum::switch());

        // 设置操作按钮
        $builder->setActionConfig('width', 200);
        $builder->addRightActionDialog('储存设置', Url::make('config')
            ->query([
                'name' => '${name}'
            ]))->title('${title} - 储存设置')->primary();
        $builder->addRightActionLink('文件管理', Url::make('Upload/index')
            ->query([
                'name' => '${name}',
            ]))->isBack()->warning();
        return $this->successRes($builder);
    }

    /**
     * 快速编辑
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function quickSave()
    {
        $name = request()->post('name', '');
        $model = UploadEngine::where('name', $name)->find();
        if (!$model) {
            return $this->fail('云储存引擎不存在');
        }
        // 获取当前选中
        $active = ConfigApi::make('upload')->get('active');
        if ($active === $name) {
            return $this->fail('不可取消，请直接启用其他引擎');
        }
        // 检测插件是否启用
        if (!PluginsApi::make()->hasEnabled($model['plugin'])) {
            return $this->fail('插件未启用，请先启用插件');
        }
        // 保存选中配置
        ConfigApi::make('upload')->set([
            'active' => $name,
        ]);
        // 返回数据
        return $this->success('保存成功');
    }

    /**
     * 配置引擎
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function config()
    {
        $name = request()->get('name', '');
        $model = UploadEngine::where('name', $name)->find();
        if (!$model) {
            return $this->fail('云储存引擎不存在');
        }
        $fileName = 'upload';
        // 配置模板路径
        $path = "{$model['plugin']}/{$fileName}";
        // 获取配置模板
        $template = ConfigView::getConfigTemplate($path, 'config');
        if (request()->method() === 'PUT') {
            $post = request()->post();
            $state = (string) request()->post('state', '10');
            // 删除无用数据
            unset($post['state']);
            unset($post['type']);
            // 获取验证器
            $validate = array_find($template, function ($item) {
                return $item['name'] === 'xbValidate' && !empty($item['value']);
            });
            $validate = $validate['value'] ?? '';
            if ($validate) {
                xbValidate($validate, $post);
                unset($post['xbValidate']);
            }
            // 设置默认引擎
            if ($state === '20') {
                ConfigApi::make($path)->set([
                    "active" => $name,
                ]);
            }
            $data = [
                $name => $post
            ];
            // 保存配置
            ConfigApi::make($path)->set($data);
            // 返回数据
            return $this->success('保存配置成功');
        }
        // 转换数据为数组
        $data = $model->toArray();
        // 获取配置数据
        $config = ConfigApi::make($path)->level(true)->get($name, []);
        $builder = XbForm::make();
        // 添加表单行
        $builder->addRowInput("type", '储存方式', $data['title'], [
            'static' => true,
        ]);
        $builder->addRowRenderComponents($template);
        $builder->setSaveMethod('PUT');
        $builder->setData($config);
        return $this->successRes($builder);
    }
}
