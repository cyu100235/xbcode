<?php

namespace plugin\workOrder\app\controller;

use support\Request;

class IndexController
{

    public function index()
    {
        return view('index/index', ['name' => 'foo']);
    }

}
