<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\api\Url;
use plugin\xbAdmin\api\EngineApi;
use plugin\xbAdmin\app\model\Config;
use plugin\xbAdmin\enum\UseStateEnum;
use plugin\xbAdmin\app\model\UploadEngine;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;

/**
 * 引擎管理
 *
 * 引擎记录存于 xb_upload_engine，引擎参数与当前启用的引擎名存于
 * xb_config 的 upload 分组（active 键），二者共同决定实际使用的储存引擎。
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
        $act = $request->get('_act', '');
        if (!$act) {
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
                'name' => '${name}',
            ]))->title('${title} - 储存设置')->primary();
        $builder->addRightActionLink('文件管理', Url::make('Upload/index')
            ->query([
                'name' => '${name}',
            ]))->isBack()->warning();
        return $this->successRes($builder);
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
        // 获取配置模板，本插件未声明 upload 分组，模板为空
        $template = Config::template('xbAdmin', 'upload');
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
        $builder = XbForm::make();
        // 添加表单行
        $builder->addRowInput('type', '储存方式', $data['title'], [
            'static' => true,
        ]);
        $builder->addRowRenderComponents($template);
        $builder->setSaveMethod('PUT');
        $builder->setData(is_array($config) ? $config : []);
        return $this->successRes($builder);
    }
}