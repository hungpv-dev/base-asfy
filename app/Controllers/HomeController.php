<?php

namespace App\Controllers;

use App\Commons\Controller;
use App\Models\User;
use App\Utils\Request;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        $url = 'http://localhost:8000';
        $title = 'Trang chủ';
        return view('home',compact('url','title'));
    }

    public function test($id,$slug){
        echo $id,$slug;
    }
}
