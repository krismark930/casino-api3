<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\Web\DepositController;
use App\Http\Controllers\Web\TransferController;
use App\Http\Controllers\Web\WithdrawController;
use App\Http\Controllers\Web\AccountController;
/* Admin controllers. */
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SystemSetting\SystemParametersController;
use App\Http\Controllers\Admin\AdminLiveBettingController;
use App\Http\Controllers\Admin\AdminSearchBettingController;
use App\Http\Controllers\Admin\AdminChampionBettingController;
use App\Http\Controllers\Admin\AdminAllianceRestrictionController;
use App\Http\Controllers\Admin\DataManipulation\AdminDataManipulationController;
use App\Http\Controllers\Admin\DataManipulation\AdminBetCheckController;
use App\Http\Controllers\Admin\DataManipulation\AdminParlayController;
use App\Http\Controllers\Admin\DataManipulation\AdminDataAddController;
use App\Http\Controllers\Admin\AdminDataController;
use App\Http\Controllers\Admin\AdminRealWaggerController;

// API User Controllers
use App\Http\Controllers\Api\User\BettingController;
use App\Http\Controllers\Api\User\BKBettingController;
use App\Http\Controllers\Api\User\UserMatchSportController;
use App\Http\Controllers\Api\User\ResultController;

// API Admin Controllers
use App\Http\Controllers\Api\Admin\WebSystemDataController;
use App\Http\Controllers\Api\Admin\MatchCrownController;
use App\Http\Controllers\Api\Admin\MatchSportController;
use App\Http\Controllers\Api\Admin\MatchSportBKController;
use App\Http\Controllers\Api\Admin\FTScoreController;
use App\Http\Controllers\Api\Admin\BKScoreController;

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
        // ft betting order today api
        Route::post('/single-ft-today', [BettingController::class, 'saveFTBettingToday']);
        // ft betting inplay api
        Route::post('/single-ft-play', [BettingController::class, 'saveFTBettingInPlay']);
        // ft betting champion api
        Route::post('/single-ft-champion', [BettingController::class, 'saveFTBettingChampion']);        
        // ft betting parlay api
        Route::post('/single-ft-parlay', [BettingController::class, 'saveFTBettingParlay']);
        // ft betting parlay api
        Route::post('/multi-parlay', [BettingController::class, 'saveMultiBettingParlay']);
        // ft betting history api
        Route::post('/ft-bet-history', [BettingController::class, 'getFTBetHistory']);
        // ft betting slip api
        Route::post('/ft-bet-slip', [BettingController::class, 'getFTBetSlip']);


        // bk betting order today api
        Route::post('/single-bk-today', [BKBettingController::class, 'saveBKBettingToday']);
        // bk betting inplay api
        Route::post('/single-bk-play', [BKBettingController::class, 'saveBKBettingInPlay']);
        // bk betting champion api
        Route::post('/single-bk-champion', [BKBettingController::class, 'saveBKBettingChampion']);        
        // bk betting parlay api
        Route::post('/single-bk-parlay', [BKBettingController::class, 'saveBKBettingParlay']);
    });
    // matched sports route
    Route::group(['prefix' => 'match-sport'], function ($router) {
        // today of ft data api
        Route::post('/ft-data', [UserMatchSportController::class, 'getFTData']);
        // get count by ft and bt in match-sport
        Route::get('/get-count', [UserMatchSportController::class, 'getCountSport']);
    });

    Route::group(['prefix' => 'result', 'middleware' => 'CORS'], function ($router){
        Route::post('/get_result_ft', [ResultController::class, 'getResultFT']);
        Route::post('/get_result_bk', [ResultController::class, 'getResultBK']);
    });
});

// admin routes
Route::group(['prefix' => 'admin', 'middleware' => ['CORS', 'auth:admin']], function ($router){
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
        // save match sport data by showtype "today"
        Route::post('/save-ft-today', [MatchSportController::class, 'saveFTDefaultToday']);
        // save match sport data by showtype "live"
        Route::post('/save-ft-inplay', [MatchSportController::class, 'saveFTDefaultInplay']);
        // save match sport data by showtype "parlay"
        Route::post('/save-ft-parlay', [MatchSportController::class, 'saveFTDefaultParlay']);
        // save match sport data by HDP in OBT
        Route::post('/ft-hdp-obt', [MatchSportController::class, 'saveFT_HDP_OBT']);
        // save match sport data by CORNER in OBT
        Route::post('/ft-corner-obt', [MatchSportController::class, 'saveFT_CORNER_INPLAY']);
        // save match sport correct score data by showtype "live"
        Route::post('/ft-correct-score', [MatchSportController::class, 'saveFT_CORRECT_SCORE']);
        // get ft data 
        Route::get('/ft-data', [MatchSportController::class, 'getFTData']);
        // get In play Data
        Route::get('/ft-in-play-data', [MatchSportController::class, 'getFTInPlayData']);        
        Route::get('/ft-correct-score-inplay', [MatchSportController::class, 'getFTCorrectScoreInPlayData']);
        Route::post('/ft-corner-today', [MatchSportController::class, 'saveFT_CORNER_TODAY']);


        // save bk data by showtype "live"
        Route::post('/save-bk-inplay', [MatchSportBKController::class, 'saveBKDefaultInplay']);
        // save bk data by showtype "today"
        Route::post('/save-bk-today', [MatchSportBKController::class, 'saveBKDefaultToday']);
        // save bk data by showtype "parlay"
        Route::post('/save-bk-parlay', [MatchSportBKController::class, 'saveBKDefaultParlay']);
    });
    // score routes
    Route::group(['prefix' => 'score'], function ($router) {
        // save ft score api
        Route::post('/ft-result', [FTScoreController::class, 'saveFTScore']);
        // save bt score api
        Route::post('/bk-result', [BKScoreController::class, 'saveBKScore']);

        Route::post('/auto_ft_check_score', [SportController::class, 'autoFTCheckScore']);
        Route::post('/auto_bk_check_score', [SportController::class, 'autoBKCheckScore']);
        Route::post('/auto_paylay_ft_check_score', [SportController::class, 'autoFTParlayCheckScore']);
        Route::post('/auto_paylay_bk_check_score', [SportController::class, 'autoBKParlayCheckScore']);
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
Route::group(['prefix' => 'sport', 'middleware' => ['CORS', 'auth:admin']], function ($router){

    Route::post('/save_score', [SportController::class, 'saveScore']);

    Route::post('/ft_check_score', [SportController::class, 'checkFTScore']);
    Route::post('/bk_check_score', [SportController::class, 'checkBKScore']);

    Route::post('/bet_slip', [SportController::class, 'getBetSlipList']);

    Route::post('/get_item', [SportController::class, 'getItem']);
    Route::post('/sport_by_order', [SportController::class, 'getSportByOrder']);
    Route::post('/update-sport-open', [SportController::class, 'updateSportOpen']);
    Route::post('/get-league-by-date', [SportController::class, 'getLeagueByDate']);

    // 恢复赛事和注单  
    Route::post('/bet-resumption', [SportController::class, 'betResumption']);

    Route::post('/bet-event', [SportController::class, 'betEvent']);

    // Route::post('/add_temp', [SportController::class, 'addTemp']);
    // Route::get('/delete_temps', [SportController::class, 'deleteTemps']);
    // Route::get('/get_temps', [SportController::class, 'getTemps']);
    // Route::post('/edit_temp', [SportController::class, 'editTemp']);
    // Route::post('/get_betting_records', [SportController::class, 'get_betting_records']);
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


Route::group(['prefix' => 'livebetting', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    Route::get('/get_items', [AdminLiveBettingController::class, 'getItems'])->name('admin.livebetting.getItems');
    Route::get('/get_function_items', [AdminLiveBettingController::class, 'getFunctionItems'])->name('admin.livebetting.getFunctionItems');
    Route::get('/cancel_event', [AdminLiveBettingController::class, 'handleCancelEvent'])->name('admin.livebetting.handleCancelEvent');
    Route::get('/resume_event', [AdminLiveBettingController::class, 'handleResumeEvent'])->name('admin.livebetting.handleResumeEvent');
});


Route::group(['prefix' => 'searchbetting', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    Route::get('/get_items', [AdminSearchBettingController::class, 'getItems'])->name('admin.searchbetting.getItems');//->middleware('auth:admin');
    Route::get('/get_function_items', [AdminSearchBettingController::class, 'getFunctionItems'])->name('admin.searchbetting.getFunctionItems');
    Route::get('/cancel_event', [AdminSearchBettingController::class, 'handleCancelEvent'])->name('admin.searchbetting.handleCancelEvent');
    Route::get('/resume_event', [AdminSearchBettingController::class, 'handleResumeEvent'])->name('admin.searchbetting.handleResumeEvent');
    Route::get('/balance_event', [AdminSearchBettingController::class, 'handleBalanceEvent'])->name('admin.searchbetting.handleBalanceEvent');
});

Route::group(['prefix' => 'championbetting', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    Route::get('/get_items', [AdminChampionBettingController::class, 'getItems'])->name('admin.championbetting.getItems');//->middleware('auth:admin');
    Route::get('/get_function_items', [AdminChampionBettingController::class, 'getFunctionItems'])->name('admin.championbetting.getFunctionItems');
    Route::get('/cancel_event', [AdminChampionBettingController::class, 'handleCancelEvent'])->name('admin.championbetting.handleCancelEvent');
    Route::get('/resume_event', [AdminChampionBettingController::class, 'handleResumeEvent'])->name('admin.championbetting.handleResumeEvent');
    Route::get('/balance_event', [AdminChampionBettingController::class, 'handleBalanceEvent'])->name('admin.championbetting.handleBalanceEvent');
});

Route::group(['prefix' => 'alliancerestriction', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    Route::get('/get_items', [AdminAllianceRestrictionController::class, 'getItems'])->name('admin.alliance_restriction.getItems');
    Route::get('/get_item', [AdminAllianceRestrictionController::class, 'getItem'])->name('admin.alliance_restriction.getItem');
    Route::post('/set_item', [AdminAllianceRestrictionController::class, 'setItem'])->name('admin.alliance_restriction.setItem');
    Route::get('/delete_event', [AdminAllianceRestrictionController::class, 'handleDeleteEvent'])->name('admin.alliance_restriction.handleDeleteEvent');
});

Route::group(['prefix' => 'datamanipulation', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    Route::get('/scheduledata/get_alliance_items', [AdminDataManipulationController::class, 'scheduledata_getAllianceTypes'])->name('admin.datamanipulation.scheduledata.getAllianceTypes');
    Route::get('/scheduledata/get_items', [AdminDataManipulationController::class, 'scheduledata_getItems'])->name('admin.datamanipulation.scheduledata.getItems');
    Route::get('/scheduledata/get_item', [AdminDataManipulationController::class, 'scheduledata_getItem'])->name('admin.datamanipulation.scheduledata.getItem');
    Route::post('/scheduledata/set_item', [AdminDataManipulationController::class, 'scheduledata_setItem'])->name('admin.datamanipulation.scheduledata.setItem');
    Route::post('/scheduledata/close_bet', [AdminDataManipulationController::class, 'scheduledata_closeBet'])->name('admin.datamanipulation.scheduledata.closeBet');
    Route::post('/scheduledata/delete_event', [AdminDataManipulationController::class, 'scheduledata_deleteEvent'])->name('admin.datamanipulation.scheduledata.deleteEvent');
});

Route::group(['prefix' => 'betcheck', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    Route::get('/get_items', [AdminBetCheckController::class, 'getItems'])->name('admin.betcheck.getItems');
    Route::get('/get_functions', [AdminBetCheckController::class, 'getFunctions'])->name('admin.betcheck.getFunctions');
    Route::get('/cancel_event', [AdminBetCheckController::class, 'cancelEvent'])->name('admin.betcheck.cancelEvent');
    Route::get('/resume_event', [AdminBetCheckController::class, 'resumeEvent'])->name('admin.betcheck.resumeEvent');
});

Route::group(['prefix' => 'check-list', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    Route::get('/get_items', [AdminParlayController::class, 'getItems'])->name('admin.parlay.getItems');
    Route::get('/get_functions', [AdminParlayController::class, 'getFunctions'])->name('admin.betcheck.getFunctions');
    Route::get('/cancel_event', [AdminParlayController::class, 'cancelEvent'])->name('admin.betcheck.cancelEvent');
    Route::get('/resume_event', [AdminParlayController::class, 'resumeEvent'])->name('admin.betcheck.resumeEvent');
    Route::get('/modify_event', [AdminParlayController::class, 'modifyEvent'])->name('admin.betcheck.modifyEvent');
});

Route::group(['prefix' => 'check-list', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    Route::post('/add_item', [AdminDataAddController::class, 'addItem'])->name('admin.parlay.addItem');
});

Route::group(['prefix' => 'data-refresh', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    Route::get('/get_data', [AdminDataController::class, 'getData'])->name('admin.dataRefresh.getData');
    Route::post('/set_data', [AdminDataController::class, 'setData'])->name('admin.dataRefresh.setData');
});

Route::group(['prefix' => 'real_wagger', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    Route::get('/get_sdata', [AdminRealWaggerController::class, 'getSTableData'])->name('admin.realwagger.getSTableData');
    Route::get('/get_hdata', [AdminRealWaggerController::class, 'getHTableData'])->name('admin.realwagger.getHTableData');
    Route::get('/get_league_list', [AdminRealWaggerController::class, 'getLeagueList'])->name('admin.realwagger.getLeagueList');
});
