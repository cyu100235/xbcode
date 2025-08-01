<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbUpload\app\admin\controller;

use support\Request;
use plugin\xbCode\XbController;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCode\api\ConfigView;
use plugin\xbUpload\api\EngineApi;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\api\ConfigChecked;
use plugin\xbUpload\enum\UseStateEnum;
use plugin\xbCode\builder\Renders\Form;
use plugin\xbUpload\app\model\UploadEngine;
use plugin\xbCode\builder\Renders\TableCrud;

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
    public function index(Request $request)
    {
        $act = $request->get('_act', '');
        if ($act) {
            $data = EngineApi::getList();
            return $this->successData($data);
        }
        $builder = Builder::crud(function (TableCrud $builder) {
            // 设置快速编辑
            $builder->useCRUD()->quickSaveItemApi(xbUrl('Engine/rowEdit'));
            // 设置操作按钮
            $builder->setActionConfig('width', 200);
            $builder->addRightActionDialog('储存设置', xbUrl('Engine/config', ['name' => '${name}']), [
                'dialog' => [
                    'title' => '储存引擎设置',
                    'size' => 'default',
                ],
            ])->level('primary');
            $builder->addRightActionLink('文件管理', xbUrl('Upload/index', [
                'name' => '${name}',
            ]))->isBack(xbUrl('Engine/index'))->level('primary');

            // 添加表格头部介绍
            $description = <<<STR
                <div>1.引擎储存方式分为 本地储存 和 对象存储 两种方式。</div>
                <div>2.使用对象存储，需要将public/uploads目录下的资源文件重新上传至新的对象存储空间。</div>
                <div>3.需将对象存储的图片域名添加到微信小程序官方后台request合法域名和downloadFile合法域名。</div>
            STR;
            $builder->addHeaderPrompt($description)->title('温馨提示');

            // 添加表格列
            $builder->addColumn('title', '储存方式');
            $builder->addColumn('desc', '储存介绍');
            $builder->addColumnSwitch('state', '默认使用', [
                'trueValue' => '20',
            ]);
        });
        return $this->successRes($builder);
    }

    /**
     * 快速编辑
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function rowEdit(Request $request)
    {
        $name = $request->post('name', '');
        $model = UploadEngine::where('name', $name)->find();
        if (!$model) {
            return $this->fail('引擎不存在');
        }
        // 获取当前选中
        $active = ConfigApi::get('upload.active', '');
        if ($active === $name) {
            return $this->fail('不可取消，请直接启用其他引擎');
        }
        // 保存选中配置
        ConfigApi::set("upload", [
            'active' => $name,
        ]);
        // 返回数据
        return $this->success('保存成功');
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
        $name = $request->get('name', '');
        $model = UploadEngine::where('name', $name)->find();
        if (!$model) {
            return $this->fail('引擎不存在');
        }
        $fileName = 'upload';
        // 配置模板路径
        $path = "{$model['plugin']}/{$fileName}";
        if ($request->method() === 'PUT') {
            $post = $request->post();
            $post["{$name}.type"] = $name;
            $state = (string) $request->post('state', '10');
            // 删除无用数据
            unset($post['state']);
            unset($post['type']);
            // 设置默认引擎
            if ($state === '20') {
                ConfigApi::set($path, [
                    "active" => $name,
                ]);
            }
            // 保存配置
            ConfigApi::set($path, $post);
            // 返回数据
            return $this->success('保存配置成功');
        }
        // 转换数据为数组
        $data = $model->toArray();
        // 获取当前使用
        $active = ConfigApi::get("{$fileName}.active", '');
        // 获取配置数据
        $config = ConfigApi::get("{$path}.{$name}.*", []);
        // 替换键名
        $config = ConfigChecked::replaceKeys("{$name}.", $config);
        // 获取配置模板
        $template = ConfigView::getConfigTemplate($path, 'config');
        $builder = Builder::form(function (Form $builder) use ($template, $data, $active, $config) {
            // 添加表单行
            $builder->addRowInput("type", '储存方式', $data['title'], [
                'static' => true,
            ]);
            foreach ($template as $value) {
                $builder->addRow(
                    $value['type'] ?? 'InputText',
                    $value['field'],
                    $value['title'],
                    '',
                    $value['extra'],
                );
            }
            $state = $data['name'] === $active ? '20' : '10';
            $builder->addRowRadio('state', '使用状态', $state)->options(UseStateEnum::options());
        });
        $builder->setApi(xbUrl('Engine/config', ['name' => $name]));
        $builder->setMethod('PUT');
        return $this->successRes($builder);
    }
}
