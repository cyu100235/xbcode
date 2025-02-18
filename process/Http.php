<?php
namespace plugin\xbCode\process;

use Webman\App;

/**
 * Http服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Http extends App
{
    /**
     * onWorkerStart
     * @param mixed $worker
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function onWorkerStart($worker)
    {
        parent::onWorkerStart($worker);
        // 处理NGINX文件
        $this->checkedNginxFile();
    }

    /**
     * 处理NGINX文件
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function checkedNginxFile()
    {
        $baseNginxPath = base_path() . '/nginx.conf';
        if (file_exists($baseNginxPath)) {
            return;
        }
        $nginxPath = dirname(__DIR__) . '/nginx.conf';
        copy($nginxPath, $baseNginxPath);
    }
}