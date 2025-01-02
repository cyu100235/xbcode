<?php
namespace xbcode;

use Exception;
use xbcode\trait\JsonTrait;

/**
 * 控制器基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbController
{
    // 引入JsonUtil
    use JsonTrait;

    /**
     * 构造方法
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * 初始化方法
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function init()
    {
    }

    /**
     * 渲染视图
     * @param string $file 模板文件
     * @param string $suffix 文件后缀
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function view(string $file, string $suffix = 'vue')
    {
        // 获取插件名称
        $plugin = request()->plugin;
        if ($plugin) {
            $file = "plugin/{$plugin}/{$file}";
        }
        // 模板文件
        $template = base_path() . "/{$file}";
        // 拼接文件名得到完整地址
        $path = "{$template}.{$suffix}";
        // 获取视图内容
        if (!file_exists($path)) {
            throw new Exception("视图文件不存在：{$file}");
        }
        $content = file_get_contents($path);
        if (empty($content)) {
            throw new Exception("视图文件内容为空：{$file}");
        }
        return response($content);
    }
}