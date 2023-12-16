<?php
declare(strict_types=1);

namespace app\common\middleware;

use Closure;
use think\App;

/**
 * 应用服务中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AppServiceMiddleware
{
    /** @var App */
    protected $app;

    /**
     * 中间件构造函数
     * @param \think\App $app
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 应用插件
     * @param mixed $request
     * @param \Closure $next
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function handle($request, Closure $next)
    {
        # 获取应用名
        $appName = request()->xBaseName;
        # 调度转发
        return $this->app->middleware
            ->pipeline('plugin')
            ->send($request)
            ->then(function ($request) use ($next) {
                return $next($request);
            });
    }
}