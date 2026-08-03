<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\app\admin\controller;

use plugin\xbCode\api\Url;
use plugin\xbCode\XbController;
use plugin\xbCode\api\PluginsApi;
use plugin\xbDeveloper\api\IdRsaApi;
use plugin\xbCode\builder\Renders\XbForm;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbDeveloper\api\DepositoryApi;
use plugin\xbDeveloper\api\DevelopmentApi;
use plugin\xbDeveloper\api\PluginPreviewApi;
use plugin\xbDeveloper\api\TableStructureApi;

/**
 * 插件管理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 表格
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index()
    {
        if (request()->get('_act')) {
            $data = DevelopmentApi::make()->getList();
            return $this->successData($data);
        }
        $builder = XbCrud::make(function (XbCrud $builder) {
            $builder->setPrimaryKey('name');

            $builder->addHeaderDialog('创建插件', Url::make('create'), [
                'title' => '创建插件',
            ])->level('primary');
            $builder->addHeaderDialog('从仓库创建', Url::make('clone'), [
                'title' => '从仓库创建插件',
            ])->level('warning');

            $builder->addColumnCard('plugin', '插件信息', [
                'title' => 'title',
                'subTitle' => 'desc',
                'image' => 'preview',
            ]);
            $builder->addColumn('name', '插件标识')->minWidth(200);
            $builder->addColumn('version', '版本名称')->width(130)->align('center');
            $builder->addColumn('author', '开发者名称')->center();
            $builder->addColumnConfirm('icon', '重设图标', Url::make('icon')->query([
                'name' => '${name}',
                '_act' => 'template',
            ]), '', '温馨提示', [
                'title' => '立即重设',
            ])->confirmText('是否基于固定模板重新生成图标？')->width(100);
            $builder->addColumnDialog('icon', '设置图标', Url::make('icon'), [
                'title' => '上传图标',
            ])->width(100);
            $builder->addColumnDateTime('create_at', '创建时间')->width(160)->align('center');

            // 操作列
            $builder->setActionConfig('width', 210);
            $builder->addRightActionDialog('查看详情', Url::make('detail'))
                ->title('${title} - 查看插件详情')->cancelActions()->dark(true);
            $builder->addRightActionConfirm('更新菜单', Url::make('menus'))
                ->confirmText('是否确认将【${title}】插件文件菜单，更新至表数据？')
                ->success(true);
            $builder->addRightActionConfirm('执行SQL', Url::make('sql'))
                ->confirmText('是否确认执行【${title}】install.sql脚本？')
                ->danger(true);
            $builder->addRightActionDialog('导出数据', Url::make('export'))
                ->title('${title} - 导出插件数据')->cancelActions();
            $builder->addRightActionDialog('构建补丁', Url::make('package')->query([
                'name' => '${name}',
                'version' => '${version}',
            ]))->title('${title} - 构建插件补丁包')->warning(true)->visibleOn('this.can_package == 20');
            $builder->addRightActionDialog('推送仓库', Url::make('push')->query([
                'name' => '${name}',
                'version' => '${version}',
            ]))->title('推送代码至远程仓库')->dark(true)->visibleOn('this.can_push == 20');
        });
        return $this->successRes($builder);
    }

    /**
     * 推送代码仓库
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function push()
    {
        $name = request()->post('name');
        if (request()->method() === 'POST') {
            $commit = (string) request()->post('commit');
            DepositoryApi::make($name)->push($commit);
            return $this->success('插件代码推送成功');
        }
        $builder = XbForm::make(function (XbForm $builder) use ($name) {
            $builder->addRowInput('name', '插件标识', $name)->required(true)->disabled(true);
            $builder->addRowTextarea('commit', '提交信息')->required(true)->minRows(5);
        });
        return $this->successRes($builder);
    }

    /**
     * 重新设置图标
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function icon()
    {
        // 上传图标
        if (request()->method() === 'POST') {
            $name = (string)request()->get('name');
            $data = request()->post();
            PluginPreviewApi::make()->replace($name, $data['icon'], $data['template']);
            return $this->success('插件图标设置成功');
        }
        // 基于模板重建图标
        if (request()->get('_act') === 'template') {
            // 获取插件标识
            $name = request()->get('name');
            $plugin = PluginsApi::make()->get($name);
            if (empty($plugin)) {
                return $this->fail('插件不存在');
            }
            // 基于模板重建图标
            PluginPreviewApi::make()->create($plugin, '', '', true);
            return $this->success('图标重建成功，请刷新缓存后查看');
        }
        $builder = XbForm::make(function (XbForm $builder) {
            $builder->addRowUploadImage('icon', '插件图标')
                ->required(true)
                ->accept('.svg')
                ->description(<<<HTML
                <div>
                    必须是svg格式的图标
                    可去
                    <a href="https://www.iconfont.cn" target="_blank">《阿里巴巴图标库》</a>
                </div>
            HTML);
            $builder->addRowUploadImage('template', '模板背景')
                ->accept('.svg')
                ->description(<<<HTML
                <div>
                    <div>1. 不上传则使用模板库背景</div>
                    <div>2. 必须是svg格式的图标</div>
                    <div>3. 上传的模板文件必须预埋id="icon"的元素</div>
                    <div>4. 建议尺寸：300x300px</div>
                </div>
            HTML);
        });
        return $this->successRes($builder);
    }

    /**
     * 创建插件
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function create()
    {
        if (request()->method() === 'POST') {
            $data = request()->post();
            // 创建插件
            DevelopmentApi::make()->create($data);
            // 返回成功
            return $this->success('插件创建成功');
        }
        $builder = XbForm::make(function (XbForm $builder) {
            $builder->addRowInput('title', '插件名称')
                ->required(true)
                ->showCounter(true)
                ->maxLength(20)
                ->description('示例：AI客服');
            $builder->addRowInput('name', '插件标识')
                ->required(true)
                ->showCounter(true)
                ->maxLength(20)
                ->description('示例：xbCode');
            $builder->addRowInput('author', '开发者名称')
                ->required(true)
                ->showCounter(true)
                ->maxLength(10)
                ->description('示例：积木云');
            $builder->addRowInput('desc', '插件描述')
                ->required(true)
                ->showCounter(true)
                ->maxLength(35)
                ->description('一句话描述，3-35字以内');
        });
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 更新插件菜单数据
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function menus()
    {
        $name = request()->get('name');
        DevelopmentApi::make()->menus($name);
        return $this->success('菜单更新成功');
    }

    /**
     * 执行SQL脚本文件
     * @return \support\Response
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function sql()
    {
        $name = request()->get('name');
        DevelopmentApi::make()->sql($name);
        // 返回成功
        return $this->success('安装脚本sql执行完成，数据库更新成功');
    }

    /**
     * 定制处理菜单
     * @param array $data
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function checkMenuData(array $data)
    {
        foreach ($data as &$item) {
            $item['state'] = '20';
            if (!empty($item['children'])) {
                $item['children'] = $this->checkMenuData($item['children']);
            }
        }
        return $data;
    }


    /**
     * 打包插件版本
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function package()
    {
        if (request()->method() === 'POST') {
            $name = (string) request()->post('name');
            $version = (string) request()->post('version');
            // 构建版本补丁打包
            DevelopmentApi::make()->buildePatchPackage($name, $version);
            // 返回成功响应，并附带跳转地址
            return $this->success('插件版本补丁构建成功');
        }
        $builder = XbForm::make(function (XbForm $builder) {
            $name = (string) request()->get('name');
            $version = (string) request()->get('version');
            $builder->addRowGroup('basics', [
                $builder->addRowInput('title', '插件名称')
                    ->description('示例：AI客服')->disabled(true),
                $builder->addRowInput('name', '插件标识')
                    ->description('示例：xbCode')->disabled(true),
                $builder->addRowInput('version', '版本编号')
                    ->description('示例：1.0.0')->disabled(true),
            ]);
            $development = DevelopmentApi::make();
            $files = $development->getPackageFilesChange($name);
            $files = implode("\n", $files);
            $builder->addRowTextarea('files', '文件变化', $files)
                ->minRows(8)
                ->placeholder('当前版本无文件变化')
                ->description("版本补丁文件变化列表")->disabled(true);
            $sql = TableStructureApi::make()->getPackageSqlChange($name);
            $sqlPath = $development->getVersionPatchSql($name, $version);
            $builder->addRowTextarea('tables', '表结构变化', $sql)
                ->minRows(8)
                ->placeholder('当前版本无表结构变化')
                ->description("表结构版本补丁 SQL：{$sqlPath}")->disabled(true);
        });
        return $this->successRes($builder);
    }

    /**
     * 导出
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function export()
    {
        if (request()->method() === 'POST') {
            $active = request()->post('active', '');
            $name = request()->post('name', '');
            DevelopmentApi::make()->export($name, $active);
            // 返回成功
            return $this->successRes([], '数据已导出至插件目录下完成');
        }
        return $this->display();
    }

    /**
     * 克隆插件仓库
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function clone()
    {
        if (request()->method() === 'POST') {
            $data = request()->post();
            DevelopmentApi::make()->clone($data);
            return $this->success('仓库克隆完成');
        }
        $builder = XbForm::make(function (XbForm $builder) {
            $builder->addRowInput('url', '仓库地址')
                ->required(true)
                ->description('Git仓库地址，必须是SSH地址');
            $builder->addRowGrid([
                $builder->addRowInput('title', '插件名称')
                    ->required(true)
                    ->showCounter(true)
                    ->maxLength(20)
                    ->description('示例：AI客服'),
                $builder->addRowInput('name', '插件标识')
                    ->required(true)
                    ->showCounter(true)
                    ->maxLength(20)
                    ->description('示例：xbCode'),
            ]);
            $builder->addRowGrid([
                $builder->addRowInput('author', '开发者名称')
                    ->description('示例：积木云网络科技')
                    ->required(true)
                    ->showCounter(true)
                    ->maxLength(10),
                $builder->addRowInput('desc', '插件描述')
                    ->description('填写一句话描述，35字以内')
                    ->required(true)
                    ->showCounter(true)
                    ->maxLength(35),
            ]);
            $idRsaContent = IdRsaApi::make()->getIdRsaContent();
            $builder->addRowAlert(<<<HTML
            <div>
                <p>克隆仓库时，请确保仓库地址是SSH地址</p>
                <p>
                    <b>需要将以下密钥添加到仓库SSH密钥中</b>
                </p>
                <p>
                    <code>{$idRsaContent}</code>
                </p>
            </div>
            HTML);
        });
        $builder->setSaveMethod('POST');
        return $this->successRes($builder);
    }

    /**
     * 获取插件详情
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function detail()
    {
        $name = request()->get('name', '');
        if (empty($name)) {
            return $this->fail('插件标识参数错误');
        }
        $data = PluginsApi::make()->get($name);
        if (!request()->get('_replace')) {
            return $this->successRes($data);
        }
        $data['plugins'] = implode("、", $data['plugins'] ?? []);
        $data['plugins'] = empty($data['plugins']) ? '无依赖' : $data['plugins'];
        $data['composer'] = implode("\n", $data['composer'] ?? []);
        $data['composer'] = empty($data['composer']) ? '无依赖' : $data['composer'];
        $builder = XbForm::make(function (XbForm $builder) use ($data) {
            $builder->useForm()->static(true);
            $builder->addRowGroup('title', [
                $builder->addRowInput('title', '插件名称'),
                $builder->addRowInput('name', '插件标识'),
            ]);
            $builder->addRowGroup('author', [
                $builder->addRowInput('author', '开发者名称'),
                $builder->addRowInput('version', '版本号'),
            ]);
            $builder->addRowGroup('desc', [
                $builder->addRowInput('desc', '插件描述'),
                $builder->addRowImage('preview', '插件图标')->type('static-image'),
            ]);
            $builder->addRowGroup('plugins', [
                $builder->addRowInput('plugins', '插件依赖'),
            ]);
            $builder->addRowGroup('composer', [
                $builder->addRowTextarea('composer', 'Composer依赖'),
            ]);
        });
        $builder->setData($data);
        return $this->successRes($builder);
    }
}