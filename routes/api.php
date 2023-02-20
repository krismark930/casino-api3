<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SportController;
use GuzzleHttp\Middleware;

/* Admin controllers. */
use App\Http\Controllers\Admin\AuthController;


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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/test',[UserController::class,'test']);

Route::group(['prefix' => 'users', 'middleware' => 'CORS'], function ($router) {
    Route::post('/register', [UserController::class, 'register'])->name('register.user');
    Route::post('/login', [UserController::class, 'login'])->name('login.user');
    Route::get('/view-profile', [UserController::class, 'viewProfile'])->name('profile.user');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout.user');
});
Route::group(['prefix' => 'sport', 'middleware' => 'CORS'], function ($router){
    Route::resource('/get_data', SportController::class);
});

Route::post('/get_item_date', [SportController::class, 'get_item_date']);


/* Admin routes. */
Route::group(['prefix'=>'admin', 'middleware'=>'CORS'], function ($router) {
    Route::post('/login', [AuthController::class, 'login'])->name('admin.auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('admin.auth.register');
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.auth.logout')->middleware('auth:admin');
    Route::get('/user', [AuthController::class, 'user'])->name('admin.auth.user')->middleware('auth:admin');
});
