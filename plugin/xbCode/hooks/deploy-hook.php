<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
use plugin\xbCode\api\Deploy;

// 切换到项目根目录，保证 base_path() 能正确定位
$root = dirname(__DIR__, 3);
chdir($root);

require_once $root . '/vendor/autoload.php';
require_once $root . '/support/bootstrap.php';

try {
    Deploy::make()->run();
    echo '部署后置业务处理完成：Deploy::run()' . PHP_EOL;
    exit(0);
} catch (\Throwable $th) {
    echo '部署后置业务处理失败：' . $th->getMessage() . PHP_EOL;
    exit(1);
}
