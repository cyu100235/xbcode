<?php
namespace app\backend\controller;

use Exception;
use app\model\User;
use support\Request;
use think\facade\Db;
use app\model\WebRole;
use app\model\WebSite;
use app\model\WebAdmin;
use xbcode\XbController;
use xbcode\builder\FormBuilder;
use xbcode\builder\ListBuilder;
use xbcode\service\bt\BtService;
use app\validate\WebSiteValidate;
use xbcode\providers\DictProvider;
use xbcode\providers\ConfigProvider;
use xbcode\builder\table\attrs\RowEditTrait;

/**
 * 站点管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebSiteController extends XbController
{
    // 表格行编辑
    use RowEditTrait;

    /**
     * 站点模型
     * @var WebSite
     */
    protected $model;

    /**
     * 初始化
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function init()
    {
        parent::init();
        $this->model = new WebSite;
    }

    /**
     * 表格
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        // 表格渲染
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 250,
            'params' => [
                'button' => [
                    'text' => '更多功能',
                ],
            ],
        ]);
        $builder->rowConfig([
            'height' => 50,
        ]);
        $builder->pageConfig();
        $builder->addTopButton('add', '新增站点', [
            'api' => xbUrl('WebSite/add'),
            'path' => xbUrl('WebSite/add'),
        ], [
            'title' => '新增站点',
            'customStyle' => [
                'width' => '500px',
                'height' => '65vh',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'Plus'
        ]);
        $builder->addTopButton('import', '导入站点', [
            'type' => 'upload',
            'api' => xbUrl('WebExtra/import'),
            'path' => xbUrl('WebExtra/import'),
        ], [], [
            'type' => 'success',
            'icon' => 'Upload'
        ]);
        $builder->addRightButton('home', '打开首页', [
            'group' => true,
            'type' => 'link',
            'api' => xbUrl('WebExtra/link'),
            'path' => xbUrl('WebExtra/link'),
            'queryParams' => [
                'type' => 'home',
            ],
        ], [], [
            'type' => 'success',
            'icon' => 'House'
        ]);
        $builder->addRightButton('admin', '打开后台', [
            'group' => true,
            'type' => 'link',
            'api' => xbUrl('WebExtra/link'),
            'path' => xbUrl('WebExtra/link'),
            'queryParams' => [
                'type' => 'admin',
            ],
        ], [], [
            'type' => 'success',
            'icon' => 'EditOutlined'
        ]);
        $builder->addRightButton('backend', '管理人员', [
            'group' => true,
            'api' => xbUrl('WebAdmin/index'),
            'path' => xbUrl('WebAdmin/index'),
            'aliasParams' => [
                'id' => 'site_id',
            ],
        ], [
            'title' => '管理员列表',
        ], [
            'type' => 'warning',
            'icon' => 'Document'
        ]);
        $builder->addRightButton('plugin', '插件授权', [
            'group' => true,
            'api' => xbUrl('WebPlugin/index'),
            'path' => xbUrl('WebPlugin/index'),
            'aliasParams' => [
                'id' => 'site_id',
            ],
        ], [
            'title' => '站点插件授权列表',
        ], [
            'type' => 'warning',
            'icon' => 'RocketOutlined'
        ]);
        $builder->addRightButton('export', '导出数据', [
            'group' => true,
            'type' => 'link',
            'api' => xbUrl('WebExtra/export'),
            'path' => xbUrl('WebExtra/export'),
        ], [], [
            'icon' => 'Bottom'
        ]);
        $builder->addRightButton('clear', '清空数据', [
            'group' => true,
            'type' => 'confirm',
            'api' => xbUrl('WebExtra/clear'),
            'path' => xbUrl('WebExtra/clear'),
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认清空该站点的所有数据？',
        ], [
            'icon' => 'Refresh'
        ]);
        $builder->addRightButton('edit', '修改', [
            'api' => xbUrl('WebSite/edit'),
            'path' => xbUrl('WebSite/edit'),
        ], [
            'title' => '修改站点',
            'customStyle' => [
                'width' => '500px',
                'height' => '65vh',
            ],
        ], [
            'type' => 'primary',
            'icon' => 'EditOutlined'
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => xbUrl('WebSite/del'),
            'path' => xbUrl('WebSite/del'),
            'method' => 'DELETE',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据？',
        ], [
            'type' => 'danger',
            'icon' => 'DeleteOutlined'
        ]);
        $builder->addColumn('id', '序号', [
            'width' => 80,
        ]);
        $builder->addColumn('title', '站点名称', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('local', '本地储存', [
            'width' => 120,
            'params' => [
                'type' => 'switch',
                'api' => xbUrl('WebSite/action'),
                'props' => [
                    'activeText' => '开启',
                    'inactiveText' => '关闭',
                    'activeValue' => '20',
                    'inactiveValue' => '10',
                ],
            ]
        ]);
        $builder->addColumn('state', '站点状态', [
            'width' => 120,
            'params' => [
                'type' => 'switch',
                'api' => xbUrl('WebSite/action'),
                'props' => [
                    'activeText' => '开启',
                    'inactiveText' => '关闭',
                    'activeValue' => '20',
                    'inactiveValue' => '10',
                ],
            ]
        ]);
        $builder->addColumn('user_num', '用户数量', [
            'width' => 100,
        ]);
        $builder->addColumn('remarks', '站点备注', [
            'minWidth' => 180,
        ]);
        $builder->addColumn('expire_time', '过期时间', [
            'width' => 160,
        ]);
        $builder->addColumn('create_at', '创建时间', [
            'width' => 160,
        ]);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 列表
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $data = WebSite::order('id desc')
            ->paginate()
            ->each(function ($item) {
                // 用户数量
                $userNum        = User::where('saas_appid', $item->id)->count();
                $item->user_num = $userNum;
                // 站点备注
                $item->remarks = $item->remarks ?: '--';
                // 站点域名
                $item->admin  = "http://{$item->domain}/admin/";
                $item->domain = "http://{$item->domain}";
                // 过期时间
                $item->expire_time = $item->expire_time ?: '永不过期';
            });
        return $this->successRes($data);
    }

    /**
     * 添加
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST') {
            // 获取数据
            $post = $request->post();
            // 数据验证
            xbValidate(WebSiteValidate::class, $post, 'add');
            // 验证过期时间
            if (empty($post['expire_time'])) {
                unset($post['expire_time']);
            }
            // 验证域名
            $domain = (string) $post['domain'];
            if (!preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/', $domain)) {
                return $this->fail('域名格式错误');
            }
            // 验证域名是否已经存在
            if (WebSite::where('domain', $domain)->find()) {
                return $this->fail('该域名已经存在');
            }
            if (WebAdmin::where('username', $post['username'])->find()) {
                return $this->fail('登录账号已经存在');
            }
            // 宝塔服务状态
            $btState = ConfigProvider::get('bt', 'bt_api_state', 10);

            // 开启事务
            Db::startTrans();
            try {
                if ($btState == 20) {
                    // 添加宝塔域名
                    $server = new BtService;
                    $result = $server->siteInfo();
                    $list   = $server->getDomainList($result['id']);
                    if (!in_array($domain, $list)) {
                        $result = $server->addDomain(
                            (int) $result['id'],
                            (string) $result['name'],
                            $domain
                        );
                        if (empty($result)) {
                            throw new Exception('添加宝塔域名失败');
                        }
                    }
                }
                // 创建站点
                $model = new WebSite;
                if (!$model->save($post)) {
                    throw new Exception('创建站点记录失败');
                }
                // 创建站点系统角色
                $role = WebRole::create([
                    'admin_id' => 0,
                    'saas_appid' => $model->id,
                    'title' => '系统管理员',
                    'is_system' => '20',
                ]);
                if (!$role) {
                    throw new Exception('创建站点系统角色失败');
                }
                // 创建站点系统账号
                $admin = WebAdmin::create([
                    'role_id' => $role->id,
                    'admin_id' => 0,
                    'saas_appid' => $model->id,
                    'nickname' => '系统管理员',
                    'username' => $post['username'],
                    'password' => $post['password'],
                    'state' => '20',
                    'is_system' => '20',
                ]);
                if (!$admin) {
                    throw new Exception('创建站点系统账号失败');
                }
                // 提交事务
                Db::commit();
            } catch (\Throwable $th) {
                Db::rollback();
                throw $th;
            }
            // 刷新站点字典
            $this->model->getWebSiteDict(true);
            // 返回成功
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setMethod('POST');
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 修改
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id');
        $model = WebSite::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        // 查询站点管理员
        $where = [
            'saas_appid' => $model->id,
            'is_system' => '20',
        ];
        $admin = WebAdmin::where($where)->find();
        if (!$admin) {
            return $this->fail('站点管理员不存在');
        }
        $model['username'] = $admin['username'];
        if ($request->method() == 'PUT') {
            // 获取数据
            $post = $request->post();
            // 数据验证
            xbValidate(WebSiteValidate::class, $post, 'edit');
            // 验证过期时间
            if (empty($post['expire_time'])) {
                unset($post['expire_time']);
            }
            // 验证域名是否已经存在
            $where = [
                ['domain', '=', $post['domain']],
                ['id', '<>', $model['id']],
            ];
            if (WebSite::where($where)->find()) {
                return $this->fail('该域名已经存在');
            }
            // 宝塔服务状态
            $btState = ConfigProvider::get('bt', 'bt_api_state', '10');
            if ($btState == 20) {
                // 处理域名
                if ($model['domain'] != $post['domain']) {
                    // 实例服务
                    $server = new BtService;
                    // 站点信息
                    $result = $server->siteInfo();
                    // 站点域名列表
                    $list = $server->getDomainList($result['id']);
                    // 删除域名
                    if (in_array($model['domain'], $list)) {
                        $delResult = $server->delDomain(
                            (int) $result['id'],
                            (string) $result['name'],
                            (string) $model['domain']
                        );
                        if (empty($delResult)) {
                            throw new Exception('删除域名失败');
                        }
                    }
                    // 添加域名
                    if (!in_array($post['domain'], $list)) {
                        $addResult = $server->addDomain(
                            (int) $result['id'],
                            (string) $result['name'],
                            (string) $post['domain']
                        );
                        if (empty($addResult)) {
                            throw new Exception('添加域名失败');
                        }
                    }
                }
            }
            Db::startTrans();
            try {
                // 更新管理员
                $adminData = [
                    'username' => $post['username'],
                ];
                if (!empty($post['password'])) {
                    $adminData['password'] = $post['password'];
                }
                if (!$admin->save($adminData)) {
                    throw new Exception('更新管理员失败');
                }
                // 保存数据
                if (!$model->save($post)) {
                    throw new Exception('保存失败');
                }
                // 提交事务
                Db::commit();
            } catch (\Throwable $th) {
                Db::rollback();
                return $this->fail($th->getMessage());
            }
            // 刷新站点字典
            $this->model->getWebSiteDict(true);
            // 返回成功
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setMethod('PUT');
        $builder->setData($model);
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 删除
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(Request $request)
    {
        $id    = $request->post('id');
        $model = WebSite::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        // 站点域名
        $domain = $model['domain'] ?? '';
        Db::startTrans();
        try {
            // 删除站点管理员
            WebAdmin::where('saas_appid', $model->id)->delete();
            // 删除站点角色
            WebRole::where('saas_appid', $model->id)->delete();
            // 删除站点
            if (!$model->delete()) {
                throw new Exception('删除站点失败');
            }
            // 宝塔服务状态
            $btState = ConfigProvider::get('bt', 'bt_api_state', '10');
            if ($btState == 20) {
                // 删除宝塔域名
                if ($domain) {
                    $server = new BtService;
                    $result = $server->siteInfo();
                    $list   = $server->getDomainList($result['id']);
                    if (in_array($domain, $list)) {
                        $result = $server->delDomain(
                            (int) $result['id'],
                            (string) $result['name'],
                            $domain
                        );
                        if (empty($result)) {
                            throw new Exception('删除域名失败');
                        }
                    }
                }
            }
            // 提交事务
            Db::commit();
        } catch (\Throwable $th) {
            // 回滚事务
            Db::rollback();
            return $this->fail($th->getMessage());
        }
        // 刷新站点字典
        $this->model->getWebSiteDict(true);
        // 返回成功
        return $this->success('删除成功');
    }

    /**
     * 操作站点数据
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function action(Request $request)
    {
        // 数据编辑
        $this->rowEdit($request);
        // 刷新站点字典
        $this->model->getWebSiteDict(true);
        // 返回数据
        return $this->success('操作成功');
    }

    /**
     * 表单
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $builder = new FormBuilder;
        $builder->addTitle('站点信息');
        $builder->addRow('title', 'input', '站点名称', '', [
            'col' => 12,
            'prompt' => '站点名称，示例：小白基地',
            'validate' => [
                [
                    'type' => 'string',
                    'required' => true,
                    'message' => '请填写站点名称',
                ],
            ],
        ]);
        $builder->addRow('domain', 'input', '绑定域名', '', [
            'col' => 12,
            'prompt' => '绑定域名，示例：www.xxxx.com',
            'validate' => [
                [
                    'type' => 'string',
                    'required' => true,
                    'message' => '请填写绑定域名',
                ],
            ],
        ]);
        $builder->addRow('expire_time', 'datePicker', '到期时间', '', [
            'col' => 12,
            'prompt' => '过期时间，不填写则永久，示例：2021-12-31',
        ]);
        $builder->addRow('state', 'radio', '站点状态', '20', [
            'col' => 6,
            'options' => DictProvider::get('switchState')->options(),
        ]);
        $builder->addRow('local', 'radio', '本地储存', '10', [
            'col' => 6,
            'options' => [
                ['label' => '关闭', 'value' => '10'],
                ['label' => '开启', 'value' => '20'],
            ]
        ]);
        $builder->addRow('remarks', 'textarea', '站点备注', '', [
            'resize' => 'none',
            'rows' => 3,
        ]);
        $builder->addTitle('管理员信息');
        $builder->addRow('username', 'input', '登录账号', '', [
            'col' => 12,
            'prompt' => '登录账号，建议使用手机号码',
            'validate' => [
                [
                    'type' => 'string',
                    'required' => true,
                    'message' => '请填写登录账号',
                ],
            ],
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'col' => 12,
            'prompt' => '登录密码，建议使用5-20位字符',
        ]);
        return $builder;
    }
}
