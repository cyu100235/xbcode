<?php

use plugin\xbCode\builder\Components\Form\FieldSet;
use plugin\xbCode\builder\Components\Form\InputText;

return [
    FieldSet::make()
        ->title('插件配置')
        ->body([
            InputText::make()
                ->name('title')
                ->label('插件名称')
                ->description('示例：小白积木云')
                ->get(),
        ])
        ->get(),
];