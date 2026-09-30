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
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbCode\api\Gateway;
use plugin\xbAdmin\app\BaseController;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\builder\Components\Form\InputTable;

/**
 * 字典管理
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class DictController extends BaseController
{
    /**
     * 字典列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->get('_act')) {
            $builder = XbCrud::make();

            $builder->addHeaderDialog('新增字典', Url::make('add'), [
                'title' => '新增字典',
                'size' => 'lg',
            ]);
            
            // 筛选查询
            $builder->addFilterInput('keyword', '搜索关键词')
                ->clearable(true);
            $builder->addFilterSelect('plugin', '插件标识')
                ->options($this->pluginOptions())->clearable(true);
            $builder->addFilterSelect('is_system', '系统字典')
                ->options(YesEnum::options())->clearable(true);

            $builder->addColumn('id', '序号')->width(100)->align('center');
            $builder->addColumn('plugin', '插件标识')->width(120);
            $builder->addColumn('name', '字典标识')->width(180);
            $builder->addColumn('title', '字典标题')->width(150);
            $builder->addColumnEach('values_text', '字典数据');
            $builder->addColumnMap('is_system', '系统字典', YesEnum::dict())->width(100);
            $builder->addColumn('sort', '排序')->width(80);
            $builder->setActionConfig('width', 150);
            $builder->addRightActionDialog('修改', Url::make('edit'), [
                'title' => '修改字典',
                'size' => 'lg',
            ]);
            $builder->addRightActionConfirm('删除', Url::make('del'))
                ->disabledTip('系统字典，禁止删除')
                ->disabledOn('this.is_system == 20');
            return $this->successRes($builder);
        }
        $result = Gateway::get('xbAdmin/api/Dict/index', request()->get());
        return $this->response($result);
    }

    /**
     * 添加字典
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            $result = Gateway::post('xbAdmin/api/Dict/add', (array) $request->post());
            return $this->response($result);
        }
        $builder = $this->formView();
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 修改字典
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $id = (int) $request->get('id');
        if ($request->method() === 'PUT') {
            $result = Gateway::put("xbAdmin/api/Dict/edit?id={$id}", (array) $request->post());
            return $this->response($result);
        }
        $builder = $this->formView();
        $builder->setSaveMethod('PUT');
        $builder->setData($this->findModel($id));
        return $this->successRes($builder);
    }

    /**
     * 删除字典
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $id = (int) $request->get('id');
        $result = Gateway::delete("xbAdmin/api/Dict/del?id={$id}");
        return $this->response($result);
    }

    /**
     * 接收插件上报的字典数据
     * @param Request $request
     * @return Response
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function report(Request $request)
    {
        $result = Gateway::post('xbAdmin/api/Dict/report', (array) $request->post());
        return $this->response($result);
    }

    /**
     * 读取枚举字典数据
     *
     * 部署在其他服务器上的插件通过该接口按「插件标识 + 枚举标识」读取字典，
     * 未收录该字典时返回空集合，由插件回退到本地枚举类常量。
     * @param Request $request
     * @return Response
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function detail(Request $request)
    {
        $params = array_merge((array) $request->get(), (array) $request->post());
        $result = Gateway::get('xbAdmin/api/Dict/detail', $params);
        return $this->response($result);
    }

    /**
     * 表单视图
     * @return XbForm
     */
    protected function formView(): XbForm
    {
        $disabled = request()->get('id') ? true : false;
        $builder = XbForm::make();
        $builder->useForm()->columnCount(2);
        $builder->addRowInput('plugin', '插件标识')
            ->required(true)
            ->disabled($disabled)
            ->description('字典归属插件，手工新增一般为：xbAdmin');
        $builder->addRowInput('sort', '排序', 0)->required(true);
        $builder->addRowInput('name', '枚举标识')
            ->required(true)
            ->disabled($disabled)
            ->description('即枚举类名，如 StateEnum');
        $builder->addRowInput('title', '枚举标题')
            ->required(true)
            ->description('枚举中文名，如 启用状态枚举');
        $builder->addRowTable('values', '枚举数据', function (InputTable $table) use ($disabled) {
            $table->columns([
                [
                    'label' => '常量标识',
                    'name' => 'key',
                    'type' => 'input-text',
                    'required' => true,
                    'disabled' => $disabled,
                    'placeholder' => '常量标识，示例：ENABLED'
                ],
                [
                    'label' => '显示文字',
                    'name' => 'label',
                    'type' => 'input-text',
                    'required' => true,
                    'placeholder' => '显示文字，示例：禁用'
                ],
                [
                    'label' => '字典值',
                    'name' => 'value',
                    'type' => 'input-text',
                    'required' => true,
                    'disabled' => $disabled,
                    'placeholder' => '字典值，示例：10'
                ],
                [
                    'label' => '样式',
                    'name' => 'style',
                    'type' => 'input-text',
                    'required' => true,
                    'placeholder' => '样式文本，支持HTML代码'
                ],
            ])->addable(!$disabled);
        });
        $builder->addRowHidden('is_system', YesEnum::NO['value']);
        return $builder;
    }

    /**
     * 插件标识选项
     * @return array
     */
    protected function pluginOptions(): array
    {
        return Gateway::get('xbAdmin/api/Dict/plugins')['data'] ?? [];
    }

    /**
     * 按ID查找字典
     * @param int $id
     * @return array
     */
    protected function findModel(int $id): array
    {
        $result = Gateway::get("xbAdmin/api/Dict/find?id={$id}");
        return $result['data'] ?? [];
    }
}