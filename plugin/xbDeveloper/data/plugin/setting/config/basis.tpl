<?php

use plugin\xbCode\builder\Components\Form\InputText;

return [
    InputText::make()
        ->name('plugin_name')
        ->label('网站名称')
        ->description('应用名称，显示在浏览器标签页')
        ->get(),
];