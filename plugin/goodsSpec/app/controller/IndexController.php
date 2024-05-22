<?php
namespace plugin\goodsSpec\app\controller;

use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        return view('index/index', ['name' => 'goodsSpec']);
    }
}