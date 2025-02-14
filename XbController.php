<?php
namespace plugin\xbCode;

use Exception;
use plugin\xbCode\utils\trait\JsonTrait;

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
        // 初始化
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
     * @param bool $isPlugin 是否插件视图
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function view(string $file, string $suffix = 'vue', string $plugin = '')
    {
        // 获取插件名称
        if (empty($plugin)) {
            $plugin = request()->plugin;
        }
        // 拼接文件地址
        $file = "plugin/{$plugin}/{$file}";
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