<?php

use App\Http\Controllers\api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/',function (){
    return view('welcome');
});
