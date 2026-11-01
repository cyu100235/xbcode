<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\admin\controller;

use support\Request;
use support\Response;
use plugin\xbCode\api\Url;
use plugin\xbCode\api\AdminSession;
use plugin\xbCode\enum\LoginModeEnum;
use plugin\xbCode\builder\Renders\XbCrud;
use plugin\xbCode\exception\business\ExceptionBadRequest;

/**
 * 登录令牌
 *
 * 展示当前在线的后台登录会话，并支持将已登录的管理员踢出登录。
 * 会话数据全部来自缓存（按 CACHE_TYPE 走 file / redis），不逐条查询数据库。
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminSessionController extends BaseController
{
    /**
     * 在线令牌列表
     * @param Request $request
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        if ($request->get('_act')) {
            $builder = XbCrud::make();
            $builder->addHeaderConfirm('全部踢出', Url::make('kickAll'), [
                'title' => '全部踢出',
                'content' => '确定要踢出全部在线管理员吗？此操作不可恢复！',
            ])->danger();
            $builder->addFilterInput('username', '管理员账号');
            $builder->addFilterInput('ip', '登录IP');
            $builder->addColumn('username', '管理员账号')->width(130);
            $builder->addColumn('nickname', '用户昵称')->width(120);
            $builder->addColumn('role_name', '所属角色')->width(140);
            $builder->addColumnMap('single', '登录模式', LoginModeEnum::dict())->width(110);
            $builder->addColumn('ip', '登录IP')->width(130);
            $builder->addColumn('city', '所在城市')->width(120);
            $builder->addColumnDateTime('login_time', '登录时间')->width(160)->align('center');
            $builder->addColumnDateTime('last_time', '最后活跃')->width(160)->align('center');
            $builder->setActionConfig('width', 110);
            // 会话标识由列表行数据注入，URL 中的 ${session} 交给 amis 替换
            $builder->addRightActionConfirm('踢出登录', Url::make('kick')->get() . '?session=${session}', [
                'title' => '踢出登录',
                'content' => '踢出后该管理员需重新登录，是否确认？',
            ])->primary(true);
            return $this->successRes($builder);
        }
        $name = trim((string) $request->get('username', ''));
        $ip = trim((string) $request->get('ip', ''));
        $list = [];
        foreach (AdminSession::list() as $item) {
            if ($name !== '' && !str_contains((string) $item['username'], $name)) {
                continue;
            }
            if ($ip !== '' && !str_contains((string) $item['ip'], $ip)) {
                continue;
            }
            $item['login_time'] = date('Y-m-d H:i:s', (int) $item['login_time']);
            $item['last_time'] = date('Y-m-d H:i:s', (int) $item['last_time']);
            $list[] = $item;
        }
        $page = max(1, (int) $request->get('page', 1));
        $limit = max(1, (int) $request->get('limit', 30));
        return $this->successData([
            'data' => array_slice($list, ($page - 1) * $limit, $limit),
            'total' => count($list),
            'current_page' => $page,
        ]);
    }

    /**
     * 踢出单个登录令牌
     * @param Request $request
     * @throws ExceptionBadRequest
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function kick(Request $request)
    {
        $session = (string) $request->get('session', '');
        if ($session === '') {
            throw new ExceptionBadRequest('登录令牌参数错误');
        }
        if (!AdminSession::kick($session)) {
            throw new ExceptionBadRequest('该登录令牌已失效');
        }
        return $this->success('已踢出该管理员登录');
    }

    /**
     * 踢出全部登录令牌
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function kickAll()
    {
        $count = AdminSession::kickAll();
        return $this->success("已踢出 {$count} 个登录令牌");
    }
}
