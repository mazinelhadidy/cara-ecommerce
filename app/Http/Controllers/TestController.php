<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function test(){
    return view('admin.layout');
    }

    public function display()
    {
        return view('Front-End.index');
    }

   
    
}