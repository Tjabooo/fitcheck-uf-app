<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GenerateController
{
    public function index()
    {
        return view('generate.index');
    }

    public function recent()
    {
        return view('generate.recent');
    }

    public function dressMe(Request $request)
    {
        //
    }
}
