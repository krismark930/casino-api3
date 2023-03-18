<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\Web\DepositController;
use App\Http\Controllers\Web\TransferController;
use App\Http\Controllers\Web\WithdrawController;
use App\Http\Controllers\Web\AccountController;
/* Admin controllers. */
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SystemSetting\SystemParametersController;
use App\Http\Controllers\Admin\AdminLiveBettingController;
use App\Http\Controllers\Admin\AdminSearchBettingController;

// API User Controllers
use App\Http\Controllers\Api\User\BettingController;
use App\Http\Controllers\Api\User\UserMatchSportController;

// API Admin Controllers
use App\Http\Controllers\Api\Admin\WebSystemDataController;
use App\Http\Controllers\Api\Admin\MatchCrownController;
use App\Http\Controllers\Api\Admin\MatchSportController;


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

// user routes
Route::group(['prefix' => 'user', 'middleware' => ['CORS']], function ($router){
    // betting routes
    Route::group(['prefix' => 'betting'], function ($router) {
        // ft betting order api
        Route::post('/single-ft', [BettingController::class, 'saveFTBettingOrderData']);
        // ft betting inplay api
        Route::post('/single-ft-re', [BettingController::class, 'saveFTBettingInPlay']);
    });
    // matched sports route
    Route::group(['prefix' => 'match-sport'], function ($router) {
        // today of ft data api
        Route::post('/ft-data', [UserMatchSportController::class, 'getFTData']);
        // get count by ft and bt in match-sport
        Route::get('/get-count', [UserMatchSportController::class, 'getCountSport']);
    });
});

// admin routes
Route::group(['prefix' => 'admin', 'middleware' => ['CORS', 'auth:api']], function ($router){
    // web_system_data routes
    Route::group(['prefix' => 'web-system-data'], function ($router) {
        // get web_system_data api
        Route::get('/all', [WebSystemDataController::class, 'getWebSystemData']);
    });
});

// routes for third party
Route::group(['prefix' => 'third-party'], function ($router){
    // web_system_data routes
    Route::group(['prefix' => 'web-system-data'], function ($router) {
        // get web_system_data api
        Route::get('/all', [WebSystemDataController::class, 'getWebSystemData']);
        Route::put('/{id}', [WebSystemDataController::class, 'updateWebSystemData']);
    });
    // match_crown routes
    Route::group(['prefix' => 'match-crown'], function ($router) {
        // get match_crown data by MID and Gid
        Route::get('/{MID}/{Gid}', [MatchCrownController::class, 'getMatchCrownDataByMID']);
        // update match_crown data by MID and Gid
        Route::put('/{MID}/{Gid}', [MatchCrownController::class, 'updateMatchCrownDataByMID']);
        // add match_crown data
        Route::post('/add', [MatchCrownController::class, 'addMatchCrownData']);
    });
    // match_sport routes
    Route::group(['prefix' => 'match-sport'], function ($router) {
        // save match sport data by showtype "early"
        Route::post('/save-ft-fu-r', [MatchSportController::class, 'saveFT_FU_R']);
        // save match sport data by showtype "live"
        Route::post('/save-ft-inplay', [MatchSportController::class, 'saveFT_FU_R_INPLAY']);
        // save match sport data by HDP in OBT
        Route::post('/ft-hdp-obt', [MatchSportController::class, 'saveFT_HDP_OBT']);
        // save match sport data by CORNER in OBT
        Route::post('/ft-corner-obt', [MatchSportController::class, 'saveFT_CORNER_INPLAY']);
        // save match sport data by showtype "today"
        Route::post('/save-ft-pd', [MatchSportController::class, 'saveFT_PD']);
        // save match sport correct score data by showtype "live"
        Route::post('/ft-correct-score', [MatchSportController::class, 'saveFT_CORRECT_SCORE']);
        // get ft data
        Route::get('/ft-data', [MatchSportController::class, 'getFTData']);
        // get In play Data
        Route::get('/ft-in-play-data', [MatchSportController::class, 'getFTInPlayData']);
        Route::get('/ft-correct-score-inplay-data', [MatchSportController::class, 'getFTCorrectScoreInPlayData']);
        Route::post('/ft-corner-today', [MatchSportController::class, 'saveFT_CORNER_TODAY']);
    });
});

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

/* Sports routes */
Route::group(['prefix' => 'sport', 'middleware' => 'CORS'], function ($router){
    Route::resource('/get_data', SportController::class);
    Route::post('/save_score', [SportController::class, 'saveScore']);
    Route::post('/check_score', [SportController::class, 'checkScore']);
    Route::post('/bet_slip', [SportController::class, 'showData']);
    Route::post('/get_item', [SportController::class, 'getItem']);
    Route::post('/get_items', [SportController::class, 'getItems']);
    Route::post('/get_item_date', [SportController::class, 'get_item_date']);
    Route::post('/bet_ft', [SportController::class, 'singleBetFt'])->name('sport.bet_ft');
    Route::post('/multi_bet_ft', [SportController::class, 'multiBetFt'])->name('sport.multi_bet_ft');
    Route::post('/add_temp', [SportController::class, 'addTemp'])->name('sport.add_temp');
    Route::get('/delete_temps', [SportController::class, 'deleteTemps'])->name('sport.delete_temps');
    Route::get('/get_temps', [SportController::class, 'getTemps'])->name('sport.get_temps');
    Route::post('/edit_temp', [SportController::class, 'editTemp'])->name('sport.edit_temp');
    Route::post('/get_betting_records', [SportController::class, 'get_betting_records'])->name('sport.get_betting_records');
});

Route::group(['prefix' => 'result', 'middleware' => 'CORS'], function ($router){
    Route::post('/get_result_ft', [ResultController::class, 'getResultFt'])->name('result.getResultFt');
});
/* Admin routes. */
Route::group(['prefix'=>'admin', 'middleware'=>'CORS'], function ($router) {
    /* Authentication */
    Route::post('/login', [AuthController::class, 'login'])->name('admin.auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('admin.auth.register');

    Route::group(['prefix'=>'system-setting'], function ($router) {
        /* System Setting */
        // System Parameters
        // Website URL
        Route::post('/system-parameters/get-urls', [SystemParametersController::class, 'get_urls'])->name('admin.system-setting.system-parameters.get-urls');
        Route::post('/system-parameters/set-urls', [SystemParametersController::class, 'set_urls'])->name('admin.system-setting.system-parameters.set-urls');

        //Turn on/off services
        Route::post('/system-parameters/get-turnservices', [SystemParametersController::class, 'get_turnservices'])->name('admin.system-setting.system-parameters.get-turnservices');
        Route::post('/system-parameters/set-turnservices', [SystemParametersController::class, 'set_turnservices'])->name('admin.system-setting.system-parameters.set-turnservices');

        //HomePage Notification
        Route::post('/system-parameters/get-homenotifications', [SystemParametersController::class, 'get_homenotifications'])->name('admin.system-setting.system-parameters.get-homenotifications');
        Route::post('/system-parameters/set-homenotifications', [SystemParametersController::class, 'set_homenotifications'])->name('admin.system-setting.system-parameters.set-homenotifications');

    });
});

Route::group(['prefix' => 'deposit', 'middleware' => 'CORS'], function ($router) {
    Route::get('/getBank', [DepositController::class, 'getBank'])->name('web.deposit.getBank');
    Route::post('/addMoney', [DepositController::class, 'addMoney'])->name('web.deposit.addMoney');
});
Route::group(['prefix' => 'transfer', 'middleware' => 'CORS'], function ($router) {
    Route::get('/getSysConfig', [TransferController::class, 'getSysConfig'])->name('web.transfer.getSysConfig');
    Route::post('/transferMoney', [TransferController::class, 'transferMoney'])->name('web.transfer.transferMoney');
});
Route::group(['prefix' => 'withdraw', 'middleware' => 'CORS'], function ($router) {
    Route::get('/get-transaction-history', [WithdrawController::class, 'getTransactionHistory'])->name('web.withdraw.getTransactionHistory');
    Route::post('/quick-withdraw', [WithdrawController::class, 'quickWithdraw'])->name('web.withdraw.quickWithdraw');
});
Route::group(['prefix' => 'account', 'middleware' => 'CORS'], function ($router) {
    Route::get('/get-bank-list', [AccountController::class, 'getUserBankAccounts'])->name('web.account.getUserBankAccounts');
    Route::get('/get-crypto-list', [AccountController::class, 'getUserCryptoAccounts'])->name('web.account.getUserCryptoAccounts');
    Route::post('/add-bank-account', [AccountController::class, 'addBankAccount'])->name('web.account.addBankAccount');
    Route::post('/add-crypto-account', [AccountController::class, 'addCryptoAccount'])->name('web.account.addCryptoAccount');
    Route::post('/edit-bank-account', [AccountController::class, 'editBankAccount'])->name('web.account.editBankAccount');
    Route::post('/edit-crypto-account', [AccountController::class, 'editCryptoAccount'])->name('web.account.editCryptoAccount');
    Route::delete('/delete-bank-account', [AccountController::class, 'deleteBankAccount'])->name('web.account.deleteBankAccount');
    Route::delete('/delete-crypto-account', [AccountController::class, 'deleteCryptoAccount'])->name('web.account.deleteCryptoAccount');
});


Route::group(['prefix' => 'livebetting', 'middleware' => 'CORS'], function ($router){
    Route::get('/get_items', [AdminLiveBettingController::class, 'getItems'])->name('admin.livebetting.getItems');
    Route::get('/get_function_items', [AdminLiveBettingController::class, 'getFunctionItems'])->name('admin.livebetting.getFunctionItems');
});


Route::group(['prefix' => 'searchbetting', 'middleware' => 'CORS'], function ($router){
    Route::get('/get_items', [AdminSearchBettingController::class, 'getItems'])->name('admin.searchbetting.getItems');
    Route::get('/get_function_items', [AdminSearchBettingController::class, 'getFunctionItems'])->name('admin.searchbetting.getFunctionItems');
});

