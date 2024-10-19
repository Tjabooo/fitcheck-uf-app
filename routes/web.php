<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthCheck;

Route::get('/', function () {
    return view('welcome');
})->middleware(AuthCheck::class);
