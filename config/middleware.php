<?php
use plugin\xbCode\utils\MiddlewareUtil;

if (!class_exists(MiddlewareUtil::class)) {
    return [];
}
return MiddlewareUtil::modules();