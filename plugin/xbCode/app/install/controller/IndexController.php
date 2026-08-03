<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\install\controller;

use Exception;
use support\Request;
use plugin\xbCode\api\Packages;
use plugin\xbCode\XbController;
use plugin\xbCode\utils\FrameUtil;
use Webman\ThinkCache\driver\Redis;
use plugin\xbCode\app\install\utils\InstallUtil;
use plugin\xbCode\app\install\utils\EnvironmentUtil;
use plugin\xbCode\exception\business\ExceptionRedirect;
/**
 * 安装控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 构造方法
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function init()
    {
        // 请求地址
        $path = trim(request()->path(), '/');
        $ignore = [
            'install',
            'app/xbCode/install/Index/protocol',
            'app/xbCode/install/Index/complete',
        ];
        // 检测是否安装
        if (FrameUtil::checked() && !in_array($path, $ignore)) {
            $e = new ExceptionRedirect('已经安装，无需重复安装');
            $e->setRedirect('/complete');
            throw $e;
        }
    }

    /**
     * 安装视图
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        return $this->viewPage('public/install/index');
    }

    /**
     * 安装协议
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function protocol()
    {
        $path = dirname(__DIR__) . '/data/agreement.txt';
        if (!file_exists($path)) {
            return $this->successRes([
                'content' => '',
            ]);
        }
        $content = file_get_contents($path);
        $data = [
            'content' => $content,
        ];
        return $this->successRes($data);
    }

    /**
     * 环境检测
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function environment()
    {
        $data = EnvironmentUtil::get();
        return $this->successRes($data);
    }

    /**
     * 数据库配置检测
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function checked(Request $request)
    {
        $post = request()->post();
        if (empty($post)) {
            return $this->fail('参数错误');
        }
        // 执行插件验证
        $data = Packages::getPackages($request->plugin, 'plugins');
        foreach ($data as $name => $value) {
            $class = "\\plugin\\{$name}\\api\\Install";
            if (!class_exists($class)) {
                return $this->fail("{$name}插件安装类不存在");
            }
            if (!method_exists($class, 'install')) {
                return $this->fail("{$name}插件安装方法不存在");
            }
            if (!method_exists($class, 'installBefore')) {
                return $this->fail("{$name}插件安装前置方法不存在");
            }
            // 检测安装前检测
            $class::installBefore('');
        }
        try {
            $database = $post['database'];
            if (empty($database)) {
                throw new Exception('参数错误');
            }
            if (empty($database['host'])) {
                throw new Exception('请输入主机地址');
            }
            if (empty($database['database'])) {
                throw new Exception('请输入数据库名称');
            }
            if (empty($database['username'])) {
                throw new Exception('请输入数据库用户');
            }
            if (empty($database['password'])) {
                throw new Exception('请输入数据库密码');
            }
            if (empty($database['port'])) {
                throw new Exception('请输入数据库端口');
            }
            // 验证数据库
            try {
                $dsn = "mysql:host={$database['host']};dbname={$database['database']};port={$database['port']};";
                $params = [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4",
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 5,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ];
                new \PDO($dsn, $database['username'], $database['password'], $params);
            } catch (\Throwable $e) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        } catch (\Throwable $th) {
            // 检测是否数据库连接失败
            $errorCode = [
                1040,
                1041,
                1042,
                1043,
                1044,
                1045,
            ];
            if (in_array($th->getCode(), $errorCode)) {
                return $this->fail('数据库连接失败，请检查数据库配置');
            }
            // 返回错误信息
            return $this->fail($th->getMessage());
        }
        if (empty($post['redis'])) {
            return $this->fail('Redis配置参数错误');
        }
        $state = $post['redis']['enabled'] ?? '20';
        if ($state === '10') {
            return $this->successRes([], '数据验证成功，跳过Redis配置');
        }
        try {
            $redisData = $post['redis'];
            // 验证redis
            if (!isset($redisData['host']) || empty($redisData['host'])) {
                throw new Exception('请输入Redis主机地址');
            }
            if (!isset($redisData['port']) || empty($redisData['port'])) {
                throw new Exception('请输入Redis主机端口');
            }
            if (!isset($redisData['prefix']) || empty($redisData['prefix'])) {
                throw new Exception('请输入Redis前缀');
            }
            if (!class_exists('Redis')) {
                throw new Exception('Redis扩展未安装');
            }
            // 创建Redis实例
            new Redis([
                'host' => $redisData['host'] ?? '127.0.0.1',
                'port' => $redisData['port'] ?? 6379,
                'password' => $redisData['password'] ?? '',
                'select' => 0,
                'timeout' => 0,
                'expire' => 0,
                'persistent' => false,
                'prefix' => $redisData['prefix'] ?? 'xbCode:',
                'tag_prefix' => 'tag:',
                'serialize' => [],
            ]);
        } catch (\Throwable $th) {
            if (strpos($th->getMessage(), 'password')) {
                throw new Exception('Redis密码错误');
            }
            throw new Exception("Redis错误：{$th->getMessage()}");
        }
        // 返回成功
        return $this->successRes([], '数据验证成功');
    }

    /**
     * 站点设置检测
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function site()
    {
        $site = request()->post('site');
        $database = request()->post('database');
        if (empty($site)) {
            return $this->fail('站点数据错误');
        }
        if (empty($database)) {
            return $this->fail('数据库数据错误');
        }
        // 验证站点数据
        if (!isset($site['web_name']) || empty($site['web_name'])) {
            return $this->fail('请输入站点名称');
        }
        if (!isset($site['web_url']) || empty($site['web_url'])) {
            return $this->fail('请输入站点域名');
        }
        if (!filter_var($site['web_url'], FILTER_SANITIZE_URL)) {
            return $this->fail('请输入正确的域名地址');
        }
        if (!isset($site['username']) || empty($site['username'])) {
            return $this->fail('请输入站点管理员账号');
        }
        if (strlen($site['username']) < 5) {
            return $this->fail('管理员账号长度不能小于5位');
        }
        if (!isset($site['password']) || empty($site['password'])) {
            return $this->fail('请输入站点管理员密码');
        }
        if (strlen($site['password']) < 6 || strlen($site['password']) > 20) {
            return $this->fail('管理员密码不能小于6位或大于20位');
        }
        // 返回成功
        return $this->successRes([], '数据验证成功');
    }

    /**
     * 执行安装
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function install(Request $request)
    {
        // 获取安装步骤
        $step = $request->get('step', '');
        if (empty($step)) {
            return $this->fail('安装步骤参数错误');
        }
        $class = new InstallUtil;
        if (!method_exists($class, $step)) {
            return $this->fail('安装步骤方法不存在');
        }
        // 执行安装步骤
        return call_user_func([$class, $step], $request);
    }

    /**
     * 安装完成
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function complete()
    {
        // 获取后台地址
        $adminUrl = FrameUtil::getBackendUrl();
        if (!str_starts_with($adminUrl, '/')) {
            $adminUrl = "/{$adminUrl}";
        }
        if (!str_ends_with($adminUrl, '/')) {
            $adminUrl = "{$adminUrl}/";
        }
        // 提示消息词
        $message = '应用安装成功，请手动停止框架并重新启动';
        // 返回成功
        return $this->successRes([
            'message' => $message,
            'admin_url' => $adminUrl,
        ]);
    }
}
