<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Web\DepositController;
use App\Http\Controllers\Web\TransferController;
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
Route::middleware('auth:api')->get('/deposit', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'users', 'middleware' => 'CORS'], function ($router) {
    Route::post('/register', [UserController::class, 'register'])->name('register.user');
    Route::post('/login', [UserController::class, 'login'])->name('login.user');
    Route::get('/view-profile', [UserController::class, 'viewProfile'])->name('profile.user');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout.user');
});

/* Admin routes. */
Route::group(['prefix'=>'admin', 'middleware'=>'CORS'], function ($router) {
    Route::post('/login', [AuthController::class, 'login'])->name('admin.auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('admin.auth.register');
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.auth.logout')->middleware('auth:admin');
    Route::get('/user', [AuthController::class, 'user'])->name('admin.auth.user')->middleware('auth:admin');
});

Route::group(['prefix' => 'deposit', 'middleware' => 'CORS'], function ($router) {
    Route::get('/getBank', [DepositController::class, 'getBank'])->name('web.deposit.getBank');
    Route::post('/addMoney', [DepositController::class, 'addMoney'])->name('web.deposit.addMoney');
});
Route::group(['prefix' => 'transfer', 'middleware' => 'CORS'], function ($router) {
    Route::get('/getSysConfig', [TransferController::class, 'getSysConfig'])->name('web.transfer.getSysConfig');
    Route::post('/transferMoney', [TransferController::class, 'transferMoney'])->name('web.transfer.transferMoney');
});


