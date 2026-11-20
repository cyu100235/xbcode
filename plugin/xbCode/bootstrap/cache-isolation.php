<?php
/**
 * 缓存环境隔离注入（框架级）
 *
 * 本文件经 plugin/xbCode/config/autoload.php 注册，在 support/bootstrap.php 中
 * 于全部 bootstrap 类执行之前载入；此时项目配置已就绪、Redis 与缓存连接尚未创建，
 * 因此这里注入的命名空间对后续所有连接一律生效。
 */
\plugin\xbCode\bootstrap\CacheIsolation::apply();
