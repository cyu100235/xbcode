<?php

namespace hg\apidoc\export\utils;

use hg\apidoc\exception\ErrorException;

class ConfigProvider
{


    protected static $config = [];


    public static function get(){

        if (!empty(static::$config)) {
            $config = static::$config;
        }else{
            throw new ErrorException('ConfigProvider get error');
        }
        return $config;
    }



}