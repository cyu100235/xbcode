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
     * @param string $file 文件地址
     * @param string $suffix 文件后缀
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function adminView(string $file = '', string $suffix = 'html')
    {
        if (!$file) {
            $file = "runtime/admin-view/index";
        }
        // 拼接文件名
        $file = "{$file}.{$suffix}";
        // 获取视图内容
        $path = base_path($file);
        return response()->withFile($path);
    }
}