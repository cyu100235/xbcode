<?php

namespace hg\apidoc\export\providers;

use Illuminate\Support\ServiceProvider;

class LaravelService extends ServiceProvider
{


    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config.php' => config_path('apidoc-export.php'),
        ]);
    }
 
}