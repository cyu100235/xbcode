<?php

use plugin\xbCode\api\Env;
use plugin\xbCode\api\PluginJson;

return [
    'enable' => true,
    'debug' => Env::get('APP_DEBUG'),
    'version' => PluginJson::get('version', '1.0.0', dirname(__DIR__) . '/plugin.json'),
    'controller_suffix' => 'Controller',
    'controller_reuse' => false,
];