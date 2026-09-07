<?php

use plugin\xbCode\api\PluginJson;

return [
    'debug' => true,
    'version' => PluginJson::get('version', '1.0.0', dirname(__DIR__) . '/plugin.json'),
    'controller_suffix' => 'Controller',
    'controller_reuse' => false
];
