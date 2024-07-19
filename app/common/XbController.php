<?php
namespace app\common;

use app\common\utils\JsonUtil;

/**
 * 控制器基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbController
{
    // 引入JsonUtil
    use JsonUtil;

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
     * 渲染系统视图
     * @param string $file
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function adminView(string $file = '')
    {
        if (!$file) {
            $file = '/app/common/view/admin/index.html';
        }
        // 获取视图内容
        $path = base_path($file);
        if (!file_exists($path)) {
            throw new \Exception("未找到系统视图资源文件：{$path}\n");
        }
        $content = file_get_contents($path);
        if (!$content) {
            throw new \Exception('系统视图资源文件内容为空');
        }
        return (string) $content;
    }
}