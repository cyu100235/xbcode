<?php
use plugin\xbCode\utils\MiddlewareUtil;

try {
    return MiddlewareUtil::modules();
} catch (\Throwable $th) {
    return [];
}