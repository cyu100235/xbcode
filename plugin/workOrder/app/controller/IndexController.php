<?php

namespace plugin\user\app\controller;

use support\Request;

class IndexController
{

    public function index()
    {
        return view('index/index', ['name' => 'foo']);
    }

}
