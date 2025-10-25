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
namespace plugin\xbDeveloper\app\admin\controller;

use support\Request;
use plugin\xbCode\api\DebugApi;
use plugin\xbCode\XbController;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\builder\Renders\Form;
use plugin\xbDeveloper\api\PluginsExport;
use plugin\xbDeveloper\api\PluginsCreate;
use plugin\xbCode\builder\Renders\TableCrud;

/**
 * 插件管理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 表格
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        if ($request->get('_act')) {
            $data = PluginsApi::make()->list();
            return $this->successData($data);
        }
        $builder = Builder::crud(function (TableCrud $builder) {
            $builder->setPrimaryKey('name');
            $builder->setActionConfig('width', 160);

            $builder->addHeaderDialog('创建插件', xbUrl('Index/create'))->level('primary');
            $builder->addHeaderDialog('从仓库创建', xbUrl('Index/clone'))->level('warning');

            $builder->addRightActionDialog('查看详情', xbUrl('Index/detail'), [
                'title' => '查看插件详情',
                'actions' => [],
            ])->className('text-dark');
            $builder->addRightActionDialog('导出数据', xbUrl('Index/export'), [
                'title' => '导出插件数据',
                'actions' => [],
            ]);
            
            $builder->addColumnCard('plugin', '插件信息', [
                'title' => 'title',
                'subTitle' => 'desc',
                'image' => 'preview',
            ]);
            $builder->addColumn('author', '作者名称')->minWidth(200);
            $builder->addColumn('version', '版本名称')->width(100);
            $builder->addColumnDateTime('create_at', '创建时间')->width(160);
        });
        return $this->successRes($builder);
    }

    /**
     * 创建插件
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function create(Request $request)
    {
        if ($request->method() === 'POST') {
            $data = $request->post();
            // 创建插件
            \plugin\xbDeveloper\api\PluginsApi::create($data);
            // 返回成功
            return $this->success('插件创建成功');
        }
        $builder = Builder::form(function (Form $builder) {
            $builder->addRowInput('title', '插件名称')->desc('示例：AI客服');
            $builder->addRowInput('name', '插件标识')->desc('示例：xbCode');
            $builder->addRowInput('author', '作者名称')->desc('示例：积木云');
            $builder->addRowInput('desc', '插件描述')->desc('一句话描述，3-30字以内');
        });
        $builder->setMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 导出
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function export(Request $request)
    {
        if ($request->method() === 'POST') {
            $active = $request->post('active', '');
            $name = $request->post('name', '');
            if (empty($active)) {
                return $this->fail('执行导出参数错误');
            }
            if (empty($name)) {
                return $this->fail('插件标识参数错误');
            }
            if (!class_exists(PluginsExport::class)) {
                return $this->fail('插件导出类不存在');
            }
            // 首字母转大写
            $active = ucfirst($active);
            // 拼接方法名
            $method = "export{$active}";
            $class = new PluginsExport;
            if (!method_exists($class, $method)) {
                return $this->fail('插件导出方法不存在');
            }
            // 执行导出
            call_user_func([$class, $method], $name);
            // 返回成功
            return $this->success('数据已导出至插件目录下完成');
        }
        return $this->display();
    }

    /**
     * 克隆插件仓库
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function clone(Request $request)
    {
        if ($request->method() === 'POST') {
            $data = $request->post();
            $debug = DebugApi::status();
            PluginsCreate::clone($data, $debug);
            return $this->success('仓库克隆完成');
        }
        $builder = Builder::form(function (Form $builder) {
            $builder->addRowInput('url', '仓库地址')->desc('Git仓库地址，必须是SSH地址');
            $builder->addRowInput('title', '插件名称')->desc('示例：AI客服')->showCounter(true)->maxLength(20);
            $builder->addRowInput('name', '插件标识')->desc('示例：xbCode')->showCounter(true)->maxLength(15);
            $builder->addRowInput('desc', '插件描述')->desc('填写一句话描述，30字以内')->showCounter(true)->maxLength(30);
            $builder->addRowInput('author', '作者名称')->desc('示例：贵州积木云网络科技有限公司')->showCounter(true)->maxLength(30);
        });
        $builder->setMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 获取插件详情
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function detail(Request $request)
    {
        $name = $request->get('name', '');
        if (empty($name)) {
            return $this->fail('插件标识参数错误');
        }
        $data = PluginsApi::make()->get($name);
        if (!$request->get('_replace')) {
            return $this->successRes($data);
        }
        $data['plugins'] = implode("、", $data['plugins'] ?? []);
        $data['plugins'] = empty($data['plugins']) ? '无依赖' : $data['plugins'];
        $data['composer'] = implode("\n", $data['composer'] ?? []);
        $data['composer'] = empty($data['composer']) ? '无依赖' : $data['composer'];
        $builder = Builder::form(function (Form $builder) use ($data) {
            $builder->useForm()->static(true);
            $builder->addRowGroup([
                $builder->addRowInput('title', '插件名称'),
                $builder->addRowInput('name', '插件标识'),
            ]);
            $builder->addRowGroup([
                $builder->addRowInput('author', '作者名称'),
                $builder->addRowInput('version', '版本号'),
            ]);
            $builder->addRowGroup([
                $builder->addRowInput('desc', '插件描述'),
                $builder->addRowImage('preview', '插件图标')->type('static-image'),
            ]);
            $builder->addRowGroup([
                $builder->addRowInput('plugins', '插件依赖')
            ]);
            $builder->addRowGroup([
                $builder->addRowTextarea('composer', 'Composer依赖')
            ]);
        });
        $builder->setData($data);
        return $this->successRes($builder);
    }
}