<?php
use app\common\providers\SlotProvider;

// 加载框架插槽
SlotProvider::load(config('slot',[]));

// 加载插件插槽
foreach (config('plugin',[]) as $plugin) {
    foreach ($plugin as $name => $value) {
        if (!in_array($name,['slot'])) {
            continue;
        }
        SlotProvider::load($value);
    }
}