<?php
/**
 * 配置目录占位（框架要求）
 *
 * webman 的 Config::loadFromDir 只会扫描「同目录存在 app.php」的配置文件，
 * 此文件仅用于让 cache-isolation 目录被识别；
 * 载入时通过 exclude 列表跳过它，避免返回的 app 内容覆盖项目根级 app 配置。
 */
return [
    'enable' => true,
];
