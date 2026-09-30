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
use plugin\xbAdmin\api\Url;
use plugin\xbCode\api\Gateway;
use plugin\xbAdmin\enum\UseStateEnum;
use plugin\xbAdmin\app\BaseController;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;

/**
 * 引擎管理
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
        $act = $request->get('_act', '');
        if (!$act) {
            $result = Gateway::get('xbAdmin/api/Engine/index', $request->get());
            return $this->response($result);
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
        $result = Gateway::post('xbAdmin/api/Engine/quickSave', (array) $request->post());
        return $this->response($result);
    }

    /**
     * 配置引擎
     * @param Request $request
     * @return Response
     */
    public function config(Request $request)
    {
        $name = (string) $request->get('name', '');
        if ($request->method() === 'PUT') {
            $result = Gateway::put("xbAdmin/api/Engine/config?name={$name}", (array) $request->post());
            return $this->response($result);
        }
        $result = Gateway::get('xbAdmin/api/Engine/config', ['name' => $name]);
        if ((int) ($result['status'] ?? 0) !== 0) {
            return $this->response($result);
        }
        $data = $result['data'] ?? [];
        $builder = XbForm::make();
        // 添加表单行
        $builder->addRowInput('type', '储存方式', $data['title'] ?? '', [
            'static' => true,
        ]);
        $builder->addRowRenderComponents($data['template'] ?? []);
        $builder->setSaveMethod('PUT');
        $builder->setData($data['config'] ?? []);
        return $this->successRes($builder);
    }
}