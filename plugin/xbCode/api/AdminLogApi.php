<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use Throwable;
use support\Log;
use Webman\Http\Request;
use Webman\Http\Response;
use Tinywan\Jwt\JwtToken;
use plugin\xbCode\enum\LogActionEnum;
use plugin\xbCode\enum\LogResultEnum;
use plugin\xbCode\utils\IpUtil;
use plugin\xbCode\app\model\AdminLog;
use plugin\xbCode\app\model\AdminRule;

/**
 * 管理员操作日志接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminLogApi
{
    /**
     * 需要过滤的噪声参数
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private const NOISE_KEYS = ['_act', '_method', 'xbValidate', '_token'];

    /**
     * 敏感参数关键词，命中后脱敏
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private const SENSITIVE_KEYS = ['password', 'passwd', 'pwd', 'secret', 'token', 'authorization', 'appkey', 'app_secret'];

    /**
     * 常用字段中文标注
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private const FIELD_LABELS = [
        'title' => '标题',
        'short_title' => '短名称',
        'name' => '名称',
        'username' => '账号',
        'nickname' => '昵称',
        'mobile' => '手机号',
        'email' => '邮箱',
        'avatar' => '头像',
        'path' => '路由地址',
        'method' => '请求方式',
        'icon' => '图标',
        'params' => '附带参数',
        'pid' => '父级',
        'role_id' => '角色',
        'state' => '状态',
        'is_show' => '是否显示',
        'is_system' => '是否系统',
        'is_default' => '是否默认',
        'sort' => '排序',
        'remark' => '备注',
        'content' => '内容',
        'type' => '类型',
        'value' => '值',
        'group' => '分组',
    ];

    /**
     * 菜单标题缓存
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static array $menuTitleCache = [];

    /**
     * 创建实例
     * @return AdminLogApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        return new static;
    }

    /**
     * 记录管理员操作日志
     * @param Request $request
     * @param Response $response
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function record(Request $request, Response $response)
    {
        try {
            // 仅记录增删改操作，GET不记录
            $method = strtoupper($request->method());
            if (!in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
                return;
            }
            // 解析操作结果
            [$result, $error] = $this->parseResult($response);
            // 操作类型（管理员登录单独识别）
            $action = $this->isLoginRequest($request) ? LogActionEnum::LOGIN['value'] : $this->parseAction($method);
            // 请求参数（脱敏）
            $params = $this->buildParams($request);
            // 组装日志数据
            $data = [
                'admin_id' => (int) ($request->uid ?? 0),
                'username' => (string) ($request->username ?? ''),
                'menu_title' => $this->getMenuTitle($request),
                'action' => $action,
                'method' => $method,
                'path' => $this->getPath($request),
                'content' => $this->buildContent($request, $method, $action, $params),
                'params' => json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'ip' => (string) $request->getRealIp(),
                'city' => '',
                'user_agent' => mb_substr((string) $request->header('user-agent', ''), 0, 500),
                'result' => $result,
                'error' => $error,
            ];
            // 补充操作人（未登录接口如登录，取提交账号）
            if (empty($data['username'])) {
                $data['username'] = (string) ($params['username'] ?? $this->getLoginUsername());
            }
            // 查询所在城市
            $data['city'] = IpUtil::getCity($data['ip']);
            // 保存日志
            (new AdminLog)->save($data);
        } catch (Throwable $th) {
            // 日志记录失败不影响业务
            Log::error("管理员操作日志记录失败：{$th->getMessage()}");
        }
    }

    /**
     * 解析操作结果
     * @param Response $response
     * @return array [result, error]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function parseResult(Response $response): array
    {
        if ($response->getStatusCode() >= 400) {
            return [LogResultEnum::FAIL['value'], 'HTTP状态码：' . $response->getStatusCode()];
        }
        $body = json_decode((string) $response->rawBody(), true);
        if (!is_array($body) || !array_key_exists('status', $body)) {
            return [LogResultEnum::FAIL['value'], '响应数据格式错误'];
        }
        if ((int) $body['status'] === 0) {
            return [LogResultEnum::SUCCESS['value'], ''];
        }
        return [LogResultEnum::FAIL['value'], mb_substr((string) ($body['msg'] ?? ''), 0, 500)];
    }

    /**
     * 是否管理员登录请求
     * @param Request $request
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function isLoginRequest(Request $request): bool
    {
        return $this->getPath($request) === 'admin/Publics/login';
    }

    /**
     * 解析操作类型
     * @param string $method
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function parseAction(string $method): string
    {
        return match ($method) {
            'POST' => LogActionEnum::ADD['value'],
            'PUT' => LogActionEnum::EDIT['value'],
            default => LogActionEnum::DELETE['value'],
        };
    }

    /**
     * 构建脱敏后的请求参数
     * @param Request $request
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildParams(Request $request): array
    {
        $data = $request->all();
        $params = [];
        foreach ($data as $key => $value) {
            if (in_array($key, self::NOISE_KEYS, true)) {
                continue;
            }
            if ($this->isSensitive($key)) {
                $params[$key] = '******';
                continue;
            }
            $params[$key] = $value;
        }
        return $params;
    }

    /**
     * 是否敏感字段
     * @param string $key
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function isSensitive(string $key): bool
    {
        $key = strtolower($key);
        foreach (self::SENSITIVE_KEYS as $word) {
            if (str_contains($key, $word)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 构建可读的操作内容
     * @param Request $request
     * @param string $method
     * @param string $action
     * @param array $params
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildContent(Request $request, string $method, string $action, array $params): string
    {
        $menuTitle = $this->getMenuTitle($request);
        // 管理员登录
        if ($action === LogActionEnum::LOGIN['value']) {
            $username = (string) ($params['username'] ?? ($request->username ?? ''));
            $content = "管理员「{$username}」登录系统";
            return mb_substr($content, 0, 500);
        }
        if ($method === 'DELETE') {
            $ids = $params['id'] ?? ($params['ids'] ?? '');
            $ids = is_array($ids) ? implode(',', $ids) : (string) $ids;
            $content = "删除「{$menuTitle}」" . ($ids !== '' ? "：ID={$ids}" : '');
            return mb_substr($content, 0, 500);
        }
        if ($method === 'PUT') {
            return $this->buildEditContent($request, $menuTitle, $params);
        }
        // POST 新增
        $fields = $this->buildFieldList($params);
        $content = "新增「{$menuTitle}」" . ($fields !== '' ? "：{$fields}" : '');
        return mb_substr($content, 0, 500);
    }

    /**
     * 构建修改内容（差量对比，小白也能看懂）
     * @param Request $request
     * @param string $menuTitle
     * @param array $params
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildEditContent(Request $request, string $menuTitle, array $params): string
    {
        $id = (int) ($params['id'] ?? $request->get('id', 0));
        $header = "修改「{$menuTitle}」" . ($id > 0 ? "(ID={$id})" : '');
        // 加载旧数据做差量对比
        $oldModel = $this->loadOldModel($request, $id);
        if (!$oldModel) {
            $fields = $this->buildFieldList($params);
            return mb_substr($header . ($fields !== '' ? "：{$fields}" : ''), 0, 500);
        }
        $old = $oldModel->toArray();
        $changes = [];
        foreach ($params as $key => $value) {
            if ($key === 'id') {
                continue;
            }
            if (!array_key_exists($key, $old)) {
                continue;
            }
            $oldValue = $this->formatValue($old[$key]);
            $newValue = $this->formatValue($value);
            if ($oldValue === $newValue) {
                continue;
            }
            $label = self::FIELD_LABELS[$key] ?? $key;
            $changes[] = "{$label}：{$oldValue} → {$newValue}";
        }
        $content = $header . ($changes ? '：' . implode('；', $changes) : '：未检测到数据变化');
        return mb_substr($content, 0, 500);
    }

    /**
     * 根据控制器推断并加载旧数据
     * @param Request $request
     * @param int $id
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function loadOldModel(Request $request, int $id)
    {
        if ($id <= 0) {
            return null;
        }
        $modelClass = $this->resolveModelClass($request->controller);
        if (!$modelClass || !class_exists($modelClass)) {
            return null;
        }
        try {
            return $modelClass::find($id);
        } catch (Throwable $th) {
            return null;
        }
    }

    /**
     * 解析控制器对应的模型类
     * @param string $controller
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function resolveModelClass(string $controller): string
    {
        if (empty($controller) || !class_exists($controller)) {
            return '';
        }
        // 控制器显式声明的日志模型优先
        $props = (new \ReflectionClass($controller))->getDefaultProperties();
        if (!empty($props['logModel']) && is_string($props['logModel'])) {
            return $props['logModel'];
        }
        // 由 plugin\{Plugin}\app\{module}\controller\{X}Controller 推断 plugin\{Plugin}\app\model\{X}
        if (!preg_match('#^(plugin\\\\[^\\\\]+)\\\\app\\\\[^\\\\]+\\\\controller\\\\(.+)$#', $controller, $matches)) {
            return '';
        }
        $pluginNamespace = $matches[1];
        $className = str_replace(config('app.controller_suffix', 'Controller'), '', $matches[2]);
        return "{$pluginNamespace}\\app\\model\\{$className}";
    }

    /**
     * 构建字段列表文本
     * @param array $params
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function buildFieldList(array $params): string
    {
        $list = [];
        foreach ($params as $key => $value) {
            if ($key === 'id') {
                continue;
            }
            $label = self::FIELD_LABELS[$key] ?? $key;
            $list[] = "{$label}={$this->formatValue($value)}";
        }
        return implode('，', $list);
    }

    /**
     * 格式化字段值
     * @param mixed $value
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formatValue(mixed $value): string
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        if (is_bool($value)) {
            $value = $value ? '是' : '否';
        }
        if ($value === null) {
            $value = '';
        }
        $value = (string) $value;
        return $value === '' ? '空' : $value;
    }

    /**
     * 获取操作菜单名称
     * @param Request $request
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getMenuTitle(Request $request): string
    {
        $path = $this->getPath($request);
        if ($path === '') {
            return '未知菜单';
        }
        if (array_key_exists($path, self::$menuTitleCache)) {
            return self::$menuTitleCache[$path];
        }
        $title = '';
        try {
            $title = (string) AdminRule::where('path', $path)->value('title', '');
            if ($title === '') {
                $title = (string) AdminRule::whereLike('path', "%{$path}%")->value('title', '');
            }
        } catch (Throwable $th) {
            $title = '';
        }
        $title = $title ?: $path;
        self::$menuTitleCache[$path] = $title;
        return $title;
    }

    /**
     * 获取请求路径（模块/控制器/方法）
     * @param Request $request
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getPath(Request $request): string
    {
        $controller = (string) ($request->controller ?? '');
        $action = (string) ($request->action ?? '');
        if ($controller === '' || $action === '') {
            return '';
        }
        // 控制器名称（去除后缀）
        $suffix = config('app.controller_suffix', 'Controller');
        $controllerName = basename(str_replace('\\', '/', $controller));
        $controllerName = str_replace($suffix, '', $controllerName);
        // 模块名称
        $module = (string) ($request->app ?? '');
        $path = "{$controllerName}/{$action}";
        return $module !== '' ? "{$module}/{$path}" : $path;
    }

    /**
     * 从令牌获取登录账号
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getLoginUsername(): string
    {
        try {
            $user = JwtToken::getExtend();
            return (string) ($user['username'] ?? '');
        } catch (Throwable $th) {
            return '';
        }
    }
}