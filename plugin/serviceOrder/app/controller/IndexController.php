<?php
namespace plugin\serviceOrder\app\controller;

use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        return view('index/index', ['name' => 'serviceOrder']);
    }
}