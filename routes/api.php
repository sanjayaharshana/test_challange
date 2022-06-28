<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LogsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register',[AuthController::class,'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class,'get_method_invalid'])->name('get.register');
Route::get('/login', [AuthController::class,'get_method_invalid'])->name('get.login');




Route::group(['middleware' => 'auth:sanctum'], function()
{
    //All the routes that belongs to the group goes here
    Route::get('products', [ProductController::class,'index'])->name('get.products');
    Route::get('logs', [LogsController::class,'index'])->name('get.logs');

});


//Route::middleware('')->get('/user', function (Request $request) {
//    return $request->user();
//});
