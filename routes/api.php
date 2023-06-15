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

use App\Http\Controllers\Admin\ThirdParty\SportScoreResultController;

// API User Controllers
use App\Http\Controllers\Api\User\BettingController;
use App\Http\Controllers\Api\User\BKBettingController;
use App\Http\Controllers\Api\User\UserMatchSportController;
use App\Http\Controllers\Api\User\ResultController;
use App\Http\Controllers\Api\User\KatanController;
use App\Http\Controllers\Api\User\KablController;
use App\Http\Controllers\Api\User\KakitheController;
use App\Http\Controllers\Api\User\LotteryScheduleController;
use App\Http\Controllers\Api\User\LotteryResultController;
use App\Http\Controllers\Api\User\LotteryOddsController;
use App\Http\Controllers\Api\User\LotterySaveController;
use App\Http\Controllers\Api\User\ChessController;
use App\Http\Controllers\Api\User\OGController;
use App\Http\Controllers\Api\User\AGController;
use App\Http\Controllers\Api\User\BBINController;
use App\Http\Controllers\Api\User\MGController;
use App\Http\Controllers\Api\User\PTController;
use App\Http\Controllers\Api\User\HomeController;

// API Admin Controllers
use App\Http\Controllers\Api\Admin\WebSystemDataController;
use App\Http\Controllers\Api\Admin\MatchCrownController;
use App\Http\Controllers\Api\Admin\MatchSportController;
use App\Http\Controllers\Api\Admin\MatchSportBKController;
use App\Http\Controllers\Api\Admin\FTScoreController;
use App\Http\Controllers\Api\Admin\BKScoreController;
use App\Http\Controllers\Api\Admin\UserInfoController;
use App\Http\Controllers\Api\Admin\KitheController;
use App\Http\Controllers\Api\Admin\YakitheController;
use App\Http\Controllers\Api\Admin\AdminServerController;
use App\Http\Controllers\Api\Admin\AdminKablController;
use App\Http\Controllers\Api\Admin\AdminKamemController;
use App\Http\Controllers\Api\Admin\AdminKaguanController;
use App\Http\Controllers\Api\Admin\AdminReportController;
use App\Http\Controllers\Api\Admin\AdminQueryController;
use App\Http\Controllers\Api\Admin\AdminRateSettingController;
use App\Http\Controllers\Api\Admin\AdminAlwaysColorController;
use App\Http\Controllers\Api\Admin\AdminSysconfigController;
use App\Http\Controllers\Api\Admin\AdminLotteryuserconfigController;
use App\Http\Controllers\Api\Admin\AdminLotteryResultB5Controller;
use App\Http\Controllers\Api\Admin\AdminLotteryResultAZXY10Controller;
use App\Http\Controllers\Api\Admin\AdminLotteryResultB3Controller;
use App\Http\Controllers\Api\Admin\AdminLotteryResultBJKNController;
use App\Http\Controllers\Api\Admin\AdminLotteryResultBJPKController;
use App\Http\Controllers\Api\Admin\AdminLotteryResultCQSFController;
use App\Http\Controllers\Api\Admin\AdminLotteryResultGD11Controller;
use App\Http\Controllers\Api\Admin\AdminLotteryResultGDSFController;
use App\Http\Controllers\Api\Admin\AdminLotteryResultGXSFController;
use App\Http\Controllers\Api\Admin\AdminLotteryResultTJSFController;
use App\Http\Controllers\Api\Admin\AdminLotteryResultXYFTController;
use App\Http\Controllers\Api\Admin\AdminSixMarkSettingController;
use App\Http\Controllers\Api\Admin\ThirdpartyLotteryResultController;
use App\Http\Controllers\Api\Admin\AdminOddsB5Controller;
use App\Http\Controllers\Api\Admin\AdminOddsAZXY10Controller;
use App\Http\Controllers\Api\Admin\AdminOddsB3Controller;
use App\Http\Controllers\Api\Admin\AdminOddsBJKNController;
use App\Http\Controllers\Api\Admin\AdminOddsBJPKController;
use App\Http\Controllers\Api\Admin\AdminOddsCQSFController;
use App\Http\Controllers\Api\Admin\AdminOddsGD11Controller;
use App\Http\Controllers\Api\Admin\AdminOddsGDSFController;
use App\Http\Controllers\Api\Admin\AdminOddsGXSFController;
use App\Http\Controllers\Api\Admin\AdminOddsTJSFController;
use App\Http\Controllers\Api\Admin\AdminOddsXYFTController;
use App\Http\Controllers\Api\Admin\HumanManagementController;
use App\Http\Controllers\Api\Admin\AdminPaymentController;
use App\Http\Controllers\Api\Admin\AdminBankController;
use App\Http\Controllers\Api\Admin\AdminSystemController;
use App\Http\Controllers\Api\Admin\AdminMessageController;
use App\Http\Controllers\Api\Admin\AdminAccessController;
use App\Http\Controllers\Api\Admin\AdminUserInfoController;
use App\Http\Controllers\Api\Admin\AdminOtherGameLogsController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\AdminStatisticsController;
use App\Http\Controllers\Api\Admin\SportReportController;

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
        // not calculated bet score api
        Route::post('/not-bet-score', [BettingController::class, 'getNotBetScore']);


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

    Route::group(['prefix' => 'result'], function ($router) {
        Route::post('/get_result_ft', [ResultController::class, 'getResultFT']);
        Route::post('/get_result_bk', [ResultController::class, 'getResultBK']);
    });

    // hong kong six mark

    Route::group(['prefix' => 'ka-tan', 'middleware' => 'auth:api'], function ($router){
        Route::post('/save', [KatanController::class, 'saveKatan']);
        Route::post('/parlay/save', [KatanController::class, 'saveKatanParlay']);
        Route::post('/even-code/save', [KatanController::class, 'saveKatanEven']);
        Route::post('/compatible/save', [KatanController::class, 'saveKatanCompatible']);
        Route::post('/zodaic-even/save', [KatanController::class, 'saveKatanZodiacEven']);
        Route::post('/mantissa-even/save', [KatanController::class, 'saveKatanMantissaEven']);
        Route::post('/miss-all/save', [KatanController::class, 'saveKatanMissAll']);
        Route::post('/bet-result/main', [KatanController::class, 'getMainBetResult']);
        Route::post('/bet-result/sub', [KatanController::class, 'getSubBetResult']);
    });

    Route::group(['prefix' => 'ka-bl'], function ($router){
        Route::post('/get', [KablController::class, 'getKablData']);
    });

    Route::group(['prefix' => 'ka-kithe'], function ($router){
        Route::post('/game-status', [KakitheController::class, 'getCurrentGameStatus']);
        Route::post('/game-version', [KakitheController::class, 'getGameVersion']);
        Route::post('/game-result', [KakitheController::class, 'getGameResult']);
        Route::post('/birth-history', [KakitheController::class, 'getBirthHistory']);
    });

    // macao six mark

    Route::group(['prefix' => 'macao-ka-tan', 'middleware' => 'auth:api'], function ($router){
        Route::post('/save', [KatanController::class, 'saveMacaoKatan']);
        Route::post('/parlay/save', [KatanController::class, 'saveMacaoKatanParlay']);
        Route::post('/even-code/save', [KatanController::class, 'saveMacaoKatanEven']);
        Route::post('/compatible/save', [KatanController::class, 'saveMacaoKatanCompatible']);
        Route::post('/zodaic-even/save', [KatanController::class, 'saveMacaoKatanZodiacEven']);
        Route::post('/mantissa-even/save', [KatanController::class, 'saveMacaoKatanMantissaEven']);
        Route::post('/miss-all/save', [KatanController::class, 'saveMacaoKatanMissAll']);
        Route::post('/bet-result/main', [KatanController::class, 'getMacaoMainBetResult']);
        Route::post('/bet-result/sub', [KatanController::class, 'getMacaoSubBetResult']);
    });

    Route::group(['prefix' => 'macao-ka-bl'], function ($router){
        Route::post('/get', [KablController::class, 'getMacaoKablData']);
    });

    Route::group(['prefix' => 'macao-ka-kithe'], function ($router){
        Route::post('/game-status', [KakitheController::class, 'getMacaoCurrentGameStatus']);
        Route::post('/game-version', [KakitheController::class, 'getMacaoGameVersion']);
        Route::post('/game-result', [KakitheController::class, 'getMacaoGameResult']);
        Route::post('/birth-history', [KakitheController::class, 'getMacaoBirthHistory']);
    });

    // always color

    Route::group(['prefix' => 'lottery-schedule'], function ($router) {
        Route::post('/b3', [LotteryScheduleController::class, 'getB3Schedule']);
        Route::post('/b5', [LotteryScheduleController::class, 'getB5Schedule']);
        Route::post('/gd11', [LotteryScheduleController::class, 'getGD11Schedule']);
        Route::post('/azxy10', [LotteryScheduleController::class, 'getAZXY10Schedule']);
        Route::post('/cqsf', [LotteryScheduleController::class, 'getCQSFSchedule']);
        Route::post('/gdsf', [LotteryScheduleController::class, 'getGDSFSchedule']);
        Route::post('/tjsf', [LotteryScheduleController::class, 'getTJSFSchedule']);
        Route::post('/gxsf', [LotteryScheduleController::class, 'getGXSFSchedule']);
        Route::post('/bjpk', [LotteryScheduleController::class, 'getBJPKSchedule']);
        Route::post('/xyft', [LotteryScheduleController::class, 'getXYFTSchedule']);
        Route::post('/status', [LotteryScheduleController::class, 'getLotteryStatus']);
    });

    Route::group(['prefix' => 'lottery-result'], function ($router) {
        Route::post('/b5', [LotteryResultController::class, 'getB5Result']);
        Route::post('/b3', [LotteryResultController::class, 'getB3Result']);
        Route::post('/other', [LotteryResultController::class, 'getOtherResult']);
        Route::post('/b5-birth-history', [LotteryResultController::class, 'getB5BirthHistory']);
        Route::post('/b3-birth-history', [LotteryResultController::class, 'getB3BirthHistory']);
        Route::post('/other-birth-history', [LotteryResultController::class, 'getOtherBirthHistory']);
        Route::post('/total-bet-result', [LotteryResultController::class, 'getTotalBetResult']);
    });

    Route::group(['prefix' => 'lottery-result', 'middleware' => 'auth:api'], function ($router) {
        Route::post('/total-bet', [LotteryResultController::class, 'getTotalBetResult']);
        Route::post('/sub-bet', [LotteryResultController::class, 'getSubBetResult']);
    });

    Route::group(['prefix' => 'lottery-odds'], function ($router) {
        Route::post('/b5', [LotteryOddsController::class, 'getB5Odds']);
        Route::post('/b3', [LotteryOddsController::class, 'getB3Odds']);
        Route::post('/other', [LotteryOddsController::class, 'getOtherOdds']);
    });

    Route::group(['prefix' => 'lottery-save', 'middleware' => 'auth:api'], function ($router) {
        Route::post('/b5', [LotterySaveController::class, 'saveB5']);
        Route::post('/b3', [LotterySaveController::class, 'saveB3']);
        Route::post('/other', [LotterySaveController::class, 'saveOther']);
    });

    Route::group(['prefix' => 'other-game'], function ($router) {
        Route::post('/chess-all', [ChessController::class, 'getChessGameAll']);
        Route::post('/ag-all', [AGController::class, 'getAGGameAll']);
        Route::post('/bbin-all', [BBINController::class, 'getBBINGameAll']);
        Route::post('/mg-all', [MGController::class, 'getMGGameAll']);
        Route::post('/pt-all', [PTController::class, 'getPTGameAll']);
        Route::group(['middleware' => 'auth:api'], function ($router) {
            Route::post('/ky-url', [ChessController::class, 'getKYUrl']);
            Route::post('/og-url', [OGController::class, 'getOGUrl']);
            Route::post('/ag-url', [AGController::class, 'getAGUrl']);
            Route::post('/bbin-url', [BBINController::class, 'getBBINUrl']);
            Route::post('/mg-url', [MGController::class, 'getMGUrl']);
            Route::post('/pt-url', [PTController::class, 'getPTUrl']);
        });
    });

    Route::group(['prefix' => 'home'], function ($router) {
        Route::post('/sys-config', [HomeController::class, 'getSysConfig']);
    });

});

// admin routes
Route::group(['prefix' => 'admin', 'middleware' => ['CORS', 'auth:admin']], function ($router){
    // web_system_data routes
    Route::group(['prefix' => 'web-system-data'], function ($router) {
        // get web_system_data api
        Route::get('/all', [WebSystemDataController::class, 'getWebSystemData']);
    });

    Route::post('/user-info', [UserInfoController::class, 'getUserInfo']);
    Route::post('/record', [UserInfoController::class, 'getRecord']);
    Route::post('/record-ip', [UserInfoController::class, 'getRecordIP']);

    // hongkong six mark

    Route::group(['prefix' => 'ka-kithe'], function ($router) {
        Route::post('/all', [KitheController::class, 'getKakitheAll']);
        Route::post('/lottery-status', [KitheController::class, 'getLotteryStatus']);
        Route::post('/game-result/save', [KitheController::class, 'saveGameResult']);
        Route::post('/handicap/update', [KitheController::class, 'updateHandicap']);
        Route::post('/best/update', [KitheController::class, 'updateBest']);
        Route::post('/update', [KitheController::class, 'updateKakithe']);
        Route::post('/status/update', [KitheController::class, 'updateKakitheStatus']);
        Route::post('/delete', [KitheController::class, 'deleteKakithe']);
        Route::post('/restore', [KitheController::class, 'restoreKakithe']);
        Route::post('/edit', [KitheController::class, 'editKakithe']);
        Route::post('/win', [KitheController::class, 'winKakithe']);
    });

    Route::group(['prefix' => 'ya-kithe'], function ($router) {
        Route::post('/all', [YakitheController::class, 'getYakitheAll']);
        Route::post('/item', [YakitheController::class, 'getYakitheItemById']);
        Route::post('/update', [YakitheController::class, 'updateYakithe']);
    });

    Route::group(['prefix' => 'ka-bl'], function ($router) {
        Route::post('/period', [AdminKablController::class, 'getPeriod']);
        Route::post('/special', [AdminKablController::class, 'getSpecialCodeData']);
        Route::post('/positive', [AdminKablController::class, 'getPositiveCodeData']);
        Route::post('/positive16', [AdminKablController::class, 'getPositiveCode16Data']);
        Route::post('/regular', [AdminKablController::class, 'getRegularCodeData']);
        Route::post('/pass', [AdminKablController::class, 'getPassData']);
        Route::post('/even-code', [AdminKablController::class, 'getEvenCodeData']);
        Route::post('/one-xiao', [AdminKablController::class, 'getOneXiaoCodeData']);
    });

    Route::group(['prefix' => 'ka-mem'], function ($router) {
        Route::post('/all', [AdminKamemController::class, 'getKamemAll']);
        Route::post('/superior', [AdminKamemController::class, 'getKamemSuperior']);
        Route::post('/status/update', [AdminKamemController::class, 'updateKamemStatus']);
        Route::post('/guan/all', [AdminKamemController::class, 'getKaguanMember']);
        Route::post('/add', [AdminKamemController::class, 'addKamem']);
    });

    Route::group(['prefix' => 'ka-dan'], function ($router) {
        Route::post('/superior', [AdminKamemController::class, 'getKadanSuperior']);
        Route::post('/add', [AdminKamemController::class, 'addKadan']);
    });

    Route::group(['prefix' => 'ka-zong'], function ($router) {
        Route::post('/superior', [AdminKamemController::class, 'getKazongSuperior']);
        Route::post('/add', [AdminKamemController::class, 'addKazong']);
    });

    Route::group(['prefix' => 'ka-guan'], function ($router) {
        Route::post('/superior', [AdminKamemController::class, 'getKaguanSuperior']);
        Route::post('/add', [AdminKamemController::class, 'addKaguan']);
        Route::post('/all', [AdminKaguanController::class, 'getKaguanAll']);
        Route::post('/status/update', [AdminKaguanController::class, 'updateKaguanStatus']);
    });

    Route::group(['prefix' => 'report'], function ($router) {
        Route::post('/all', [AdminReportController::class, 'getAllReport']);
        Route::post('/kaguan', [AdminReportController::class, 'getKaguanReport']);
        Route::post('/kazong', [AdminReportController::class, 'getKazongReport']);
        Route::post('/kadai', [AdminReportController::class, 'getKadaiReport']);
        Route::post('/kauser', [AdminReportController::class, 'getKauserReport']);
        Route::post('/total-bill', [AdminReportController::class, 'getTotalBill']);
        Route::post('/sub-bill', [AdminReportController::class, 'getSubBill']);
    });

    Route::group(['prefix' => 'query'], function ($router) {
        Route::post('/member', [AdminQueryController::class, 'getKaMember']);
        Route::post('/main', [AdminQueryController::class, 'getKatanMainData']);
        Route::post('/delete', [AdminQueryController::class, 'deleteKatan']);
        Route::post('/update', [AdminQueryController::class, 'updateKatan']);
    });

    Route::group(['prefix' => 'rate-setting'], function ($router) {
        Route::post('/special-code/get', [AdminRateSettingController::class, 'getSpecialCodeRate']);
        Route::post('/positive1-6/get', [AdminRateSettingController::class, 'getPositive16Rate']);
        Route::post('/consecutive-code/get', [AdminRateSettingController::class, 'getConsecutiveCodeRate']);
        Route::post('/half-wave/get', [AdminRateSettingController::class, 'getHalfWaveRate']);
        Route::post('/special/get', [AdminRateSettingController::class, 'getSpecialRate']);
        Route::post('/one-xiao/get', [AdminRateSettingController::class, 'getOneXiaoRate']);
        Route::post('/plus-update', [AdminRateSettingController::class, 'updateRatePlus']);
        Route::post('/other-update', [AdminRateSettingController::class, 'upateRateOther']);
        Route::post('/main-update', [AdminRateSettingController::class, 'upateRateMain']);
        Route::post('/restore-odds', [AdminRateSettingController::class, 'restoreOdds']);
    });

    Route::group(['prefix' => 'six-mark'], function ($router) {
        Route::post('/website-setting/get', [AdminSixMarkSettingController::class, 'getWebsiteSetting']);
        Route::post('/website-setting/update', [AdminSixMarkSettingController::class, 'updateWebsiteSetting']);
        Route::post('/odd-diff-setting/get', [AdminSixMarkSettingController::class, 'getOddDiffSetting']);
        Route::post('/odd-diff-setting/update', [AdminSixMarkSettingController::class, 'updateOddDiffSetting']);
        Route::post('/auto-precipitation/get', [AdminSixMarkSettingController::class, 'getAutoPrecipitation']);
        Route::post('/auto-precipitation/update', [AdminSixMarkSettingController::class, 'updateAutoPrecipitation']);
        Route::post('/single-quota/get', [AdminSixMarkSettingController::class, 'getSingleQuota']);
        Route::post('/single-quota/update', [AdminSixMarkSettingController::class, 'updateSingleQuota']);
        Route::post('/water-setting/get', [AdminSixMarkSettingController::class, 'getWaterSetting']);
        Route::post('/water-setting/update', [AdminSixMarkSettingController::class, 'updateWatherSetting']);
    });

    // macao six mark

    Route::group(['prefix' => 'macao-ka-kithe'], function ($router) {
        Route::post('/all', [KitheController::class, 'getMacaoKakitheAll']);
        Route::post('/lottery-status', [KitheController::class, 'getMacaoLotteryStatus']);
        Route::post('/game-result/save', [KitheController::class, 'saveMacaoGameResult']);
        Route::post('/handicap/update', [KitheController::class, 'updateMacaoHandicap']);
        Route::post('/best/update', [KitheController::class, 'updateMacaoBest']);
        Route::post('/update', [KitheController::class, 'updateMacaoKakithe']);
        Route::post('/status/update', [KitheController::class, 'updateMacaoKakitheStatus']);
        Route::post('/delete', [KitheController::class, 'deleteMacaoKakithe']);
        Route::post('/restore', [KitheController::class, 'restoreMacaoKakithe']);
        Route::post('/edit', [KitheController::class, 'editMacaoKakithe']);
        Route::post('/win', [KitheController::class, 'winMacaoKakithe']);
    });

    Route::group(['prefix' => 'macao-ya-kithe'], function ($router) {
        Route::post('/all', [YakitheController::class, 'getMacaoYakitheAll']);
        Route::post('/item', [YakitheController::class, 'getMacaoYakitheItemById']);
        Route::post('/update', [YakitheController::class, 'updateMacaoYakithe']);
    });

    Route::group(['prefix' => 'macao-ka-bl'], function ($router) {
        Route::post('/period', [AdminKablController::class, 'getMacaoPeriod']);
        Route::post('/special', [AdminKablController::class, 'getMacaoSpecialCodeData']);
        Route::post('/positive', [AdminKablController::class, 'getMacaoPositiveCodeData']);
        Route::post('/positive16', [AdminKablController::class, 'getMacaoPositiveCode16Data']);
        Route::post('/regular', [AdminKablController::class, 'getMacaoRegularCodeData']);
        Route::post('/pass', [AdminKablController::class, 'getMacaoPassData']);
        Route::post('/even-code', [AdminKablController::class, 'getMacaoEvenCodeData']);
        Route::post('/one-xiao', [AdminKablController::class, 'getMacaoOneXiaoCodeData']);
    });

    Route::group(['prefix' => 'macao-ka-mem'], function ($router) {
        Route::post('/all', [AdminKamemController::class, 'getMacaoKamemAll']);
        Route::post('/superior', [AdminKamemController::class, 'getMacaoKamemSuperior']);
        Route::post('/status/update', [AdminKamemController::class, 'updateMacaoKamemStatus']);
        Route::post('/guan/all', [AdminKamemController::class, 'getMacaoKaguanMember']);
        Route::post('/add', [AdminKamemController::class, 'addMacaoKamem']);
    });

    Route::group(['prefix' => 'macao-ka-dan'], function ($router) {
        Route::post('/superior', [AdminKamemController::class, 'getMacaoKadanSuperior']);
        Route::post('/add', [AdminKamemController::class, 'addMacaoKadan']);
    });

    Route::group(['prefix' => 'macao-ka-zong'], function ($router) {
        Route::post('/superior', [AdminKamemController::class, 'getMacaoKazongSuperior']);
        Route::post('/add', [AdminKamemController::class, 'addMacaoKazong']);
    });

    Route::group(['prefix' => 'macao-ka-guan'], function ($router) {
        Route::post('/superior', [AdminKamemController::class, 'getMacaoKaguanSuperior']);
        Route::post('/add', [AdminKamemController::class, 'addMacaoKaguan']);
        Route::post('/all', [AdminKaguanController::class, 'getMacaoKaguanAll']);
        Route::post('/status/update', [AdminKaguanController::class, 'updateMacaoKaguanStatus']);
    });

    Route::group(['prefix' => 'macao-report'], function ($router) {
        Route::post('/all', [AdminReportController::class, 'getMacaoAllReport']);
        Route::post('/kaguan', [AdminReportController::class, 'getMacaoKaguanReport']);
        Route::post('/kazong', [AdminReportController::class, 'getMacaoKazongReport']);
        Route::post('/kadai', [AdminReportController::class, 'getMacaoKadaiReport']);
        Route::post('/kauser', [AdminReportController::class, 'getMacaoKauserReport']);
        Route::post('/total-bill', [AdminReportController::class, 'getMacaoTotalBill']);
        Route::post('/sub-bill', [AdminReportController::class, 'getMacaoSubBill']);
    });

    Route::group(['prefix' => 'macao-query'], function ($router) {
        Route::post('/member', [AdminQueryController::class, 'getMacaoKaMember']);
        Route::post('/main', [AdminQueryController::class, 'getMacaoKatanMainData']);
        Route::post('/delete', [AdminQueryController::class, 'deleteMacaoKatan']);
        Route::post('/update', [AdminQueryController::class, 'updateMacaoKatan']);
    });

    Route::group(['prefix' => 'macao-rate-setting'], function ($router) {
        Route::post('/special-code/get', [AdminRateSettingController::class, 'getMacaoSpecialCodeRate']);
        Route::post('/positive1-6/get', [AdminRateSettingController::class, 'getMacaoPositive16Rate']);
        Route::post('/consecutive-code/get', [AdminRateSettingController::class, 'getMacaoConsecutiveCodeRate']);
        Route::post('/half-wave/get', [AdminRateSettingController::class, 'getMacaoHalfWaveRate']);
        Route::post('/special/get', [AdminRateSettingController::class, 'getMacaoSpecialRate']);
        Route::post('/one-xiao/get', [AdminRateSettingController::class, 'getMacaoOneXiaoRate']);
        Route::post('/plus-update', [AdminRateSettingController::class, 'updateMacaoRatePlus']);
        Route::post('/other-update', [AdminRateSettingController::class, 'upateMacaoRateOther']);
        Route::post('/main-update', [AdminRateSettingController::class, 'upateMacaoRateMain']);
        Route::post('/restore-odds', [AdminRateSettingController::class, 'restoreMacaoOdds']);
    });

    Route::group(['prefix' => 'macao-six-mark'], function ($router) {
        Route::post('/website-setting/get', [AdminSixMarkSettingController::class, 'getMacaoWebsiteSetting']);
        Route::post('/website-setting/update', [AdminSixMarkSettingController::class, 'updateMacaoWebsiteSetting']);
        Route::post('/odd-diff-setting/get', [AdminSixMarkSettingController::class, 'getMacaoOddDiffSetting']);
        Route::post('/odd-diff-setting/update', [AdminSixMarkSettingController::class, 'updateMacaoOddDiffSetting']);
        Route::post('/auto-precipitation/get', [AdminSixMarkSettingController::class, 'getMacaoAutoPrecipitation']);
        Route::post('/auto-precipitation/update', [AdminSixMarkSettingController::class, 'updateMacaoAutoPrecipitation']);
        Route::post('/single-quota/get', [AdminSixMarkSettingController::class, 'getMacaoSingleQuota']);
        Route::post('/single-quota/update', [AdminSixMarkSettingController::class, 'updateMacaoSingleQuota']);
        Route::post('/water-setting/get', [AdminSixMarkSettingController::class, 'getMacaoWaterSetting']);
        Route::post('/water-setting/update', [AdminSixMarkSettingController::class, 'updateMacaoWatherSetting']);
    });

    // always color lottery


    Route::group(['prefix' => 'always-color'], function ($router) {
        Route::post('/order-list/get', [AdminAlwaysColorController::class, 'getOrderList']);
        Route::post('/order-cancel/all', [AdminAlwaysColorController::class, 'getOrderCancelAll']);
        Route::post('/lottery-history-all', [AdminAlwaysColorController::class, 'getLotteryHistory']); 
        Route::post('/user-lottery', [AdminAlwaysColorController::class, 'getUserLottery']);
        Route::post('/detail-lottery', [AdminAlwaysColorController::class, 'getDetailLottery']);
    });

    Route::group(['prefix' => 'sys-config'], function ($router) {
        Route::post('/lottery', [AdminSysconfigController::class, 'getLotteryConfig']);
        Route::post('/lottery/update', [AdminSysconfigController::class, 'updateLotteryConfig']);
        Route::post('/usdt/update', [AdminSysconfigController::class, 'updateUSDTConfig']);
    });

    Route::group(['prefix' => 'user-config'], function ($router) {
        Route::post('/lottery/all', [AdminLotteryuserconfigController::class, 'getLotteryUserConfig']);
        Route::post('/lottery/item', [AdminLotteryuserconfigController::class, 'getLotteryUserConfigItem']);
        Route::post('/lottery/update', [AdminLotteryuserconfigController::class, 'updateLotteryConfigItem']);
        Route::post('/discount', [AdminLotteryuserconfigController::class, 'startDiscount']);
    });

    Route::group(['prefix' => 'lottery-result'], function ($router) {
        Route::group(['prefix' => 'b5'], function ($router) {
            Route::post('/all', [AdminLotteryResultB5Controller::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultB5Controller::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultB5Controller::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultB5Controller::class, 'checkoutResult']);
        });
        Route::group(['prefix' => 'azxy10'], function ($router) {
            Route::post('/all', [AdminLotteryResultAZXY10Controller::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultAZXY10Controller::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultAZXY10Controller::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultAZXY10Controller::class, 'checkoutResult']);
        });
        Route::group(['prefix' => 'b3'], function ($router) {
            Route::post('/all', [AdminLotteryResultB3Controller::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultB3Controller::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultB3Controller::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultB3Controller::class, 'checkoutResult']);
        });
        Route::group(['prefix' => 'bjkn'], function ($router) {
            Route::post('/all', [AdminLotteryResultBJKNController::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultBJKNController::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultBJKNController::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultBJKNController::class, 'checkoutResult']);
        });
        Route::group(['prefix' => 'bjpk'], function ($router) {
            Route::post('/all', [AdminLotteryResultBJPKController::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultBJPKController::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultBJPKController::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultBJPKController::class, 'checkoutResult']);
        });
        Route::group(['prefix' => 'cqsf'], function ($router) {
            Route::post('/all', [AdminLotteryResultCQSFController::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultCQSFController::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultCQSFController::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultCQSFController::class, 'checkoutResult']);
        });
        Route::group(['prefix' => 'gd11'], function ($router) {
            Route::post('/all', [AdminLotteryResultGD11Controller::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultGD11Controller::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultGD11Controller::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultGD11Controller::class, 'checkoutResult']);
        });
        Route::group(['prefix' => 'gdsf'], function ($router) {
            Route::post('/all', [AdminLotteryResultGDSFController::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultGDSFController::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultGDSFController::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultGDSFController::class, 'checkoutResult']);
        });
        Route::group(['prefix' => 'gxsf'], function ($router) {
            Route::post('/all', [AdminLotteryResultGXSFController::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultGXSFController::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultGXSFController::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultGXSFController::class, 'checkoutResult']);
        });
        Route::group(['prefix' => 'tjsf'], function ($router) {
            Route::post('/all', [AdminLotteryResultTJSFController::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultTJSFController::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultTJSFController::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultTJSFController::class, 'checkoutResult']);
        });
        Route::group(['prefix' => 'xyft'], function ($router) {
            Route::post('/all', [AdminLotteryResultXYFTController::class, 'getLotteryResult']);
            Route::post('/get', [AdminLotteryResultXYFTController::class, 'getLotteryResultById']);
            Route::post('/save', [AdminLotteryResultXYFTController::class, 'saveLotteryResult']);
            Route::post('/checkout', [AdminLotteryResultXYFTController::class, 'checkoutResult']);
        });
    });

    Route::group(['prefix' => 'odds-setting'], function ($router) {
        Route::group(['prefix' => 'b5'], function ($router) {
            Route::post('/get', [AdminOddsB5Controller::class, 'getOdds']);
            Route::post('/save', [AdminOddsB5Controller::class, 'saveOdds']);
        });
        Route::group(['prefix' => 'gdsf'], function ($router) {
            Route::post('/get', [AdminOddsGDSFController::class, 'getOdds']);
            Route::post('/save', [AdminOddsGDSFController::class, 'saveOdds']);
        });
    });

    // human management

    Route::group(['prefix' => 'human-management'], function ($router) {
        Route::post('/query', [HumanManagementController::class, 'getQuery']);
        Route::post('/query-ky', [HumanManagementController::class, 'getQueryKy']);
        Route::post('/query-htr', [HumanManagementController::class, 'getQueryHtr']);
        Route::post('/report', [HumanManagementController::class, 'getReport']);
        Route::post('/report-ky', [HumanManagementController::class, 'getReportKy']);
        Route::post('/report-htr', [HumanManagementController::class, 'getReportHtr']);
        Route::post('/discount-zr', [HumanManagementController::class, 'discountZr']);
        Route::post('/discount-dz', [HumanManagementController::class, 'discountDz']);
        Route::post('/discount-ky', [HumanManagementController::class, 'discountKy']);
        Route::post('/discount-htr', [HumanManagementController::class, 'discountHtr']);
        Route::post('/game-system', [HumanManagementController::class, 'getThirdpartyGameData']);
        Route::post('/game-open', [HumanManagementController::class, 'gameOpen']);
        Route::post('/game-delete', [HumanManagementController::class, 'deleteGame']);
        Route::post('/game-edit', [HumanManagementController::class, 'editGame']);
        Route::post('/game-update', [HumanManagementController::class, 'updateGame']);
        Route::post('/game-add', [HumanManagementController::class, 'addGame']);
    });

    // payment management

    Route::group(['prefix' => 'payment'], function ($router) {
        Route::post('/cash-system', [AdminPaymentController::class, 'getCashSystem']);
        Route::post('/cash-review', [AdminPaymentController::class, 'reviewCash']);
        Route::post('/cash-cancel', [AdminPaymentController::class, 'rejectCash']);
        Route::post('/cash-delete', [AdminPaymentController::class, 'deleteCash']);
        Route::post('/cash-save', [AdminPaymentController::class, 'saveCash']);
        Route::post('/cash-bulk-save', [AdminPaymentController::class, 'saveBulkCash']);
        Route::post('/payment-method', [AdminPaymentController::class, 'getPaymentMethod']);
        Route::post('/payment-method/add', [AdminPaymentController::class, 'addPaymentMethod']);
        Route::post('/payment-method/use', [AdminPaymentController::class, 'usePaymentMethod']);
        Route::post('/payment-method/delete', [AdminPaymentController::class, 'deletePaymentMethod']);
        Route::post('/web-bank-data', [AdminBankController::class, 'getWebBankData']);
        Route::post('/web-bank-data/add', [AdminBankController::class, 'addWebBankData']);
        Route::post('/web-bank-data/use', [AdminBankController::class, 'useWebBankData']);
        Route::post('/web-bank-data/delete', [AdminBankController::class, 'deleteWebBankData']);
    });

    // system management

    Route::group(['prefix' => 'system'], function ($router) {
        Route::post('/all', [AdminSystemController::class, 'getSystemAll']);
        Route::post('/update-url', [AdminSystemController::class, 'updateSystemUrl']);
        Route::post('/update-turn-service', [AdminSystemController::class, 'updateTurnService']);
        Route::post('/update-notification', [AdminSystemController::class, 'updateNotification']);
        Route::post('/notice', [AdminSystemController::class, 'getSystemNotice']);
        Route::post('/add-notice', [AdminSystemController::class, 'addSystemNotice']);
        Route::post('/update-notice', [AdminSystemController::class, 'updateSystemNotice']);
        Route::post('/delete-notice', [AdminSystemController::class, 'deleteSystemNotice']);
        Route::post('/message', [AdminMessageController::class, 'getWebMessageData']);
        Route::post('/add-message', [AdminMessageController::class, 'addWebMessageData']);
        Route::post('/delete-message', [AdminMessageController::class, 'deleteWebMessageData']);
        Route::post('/access', [AdminAccessController::class, 'getWebSys800Data']);
        Route::post('/delete-access', [AdminAccessController::class, 'deleteWebSys800Data']);
        Route::post('/cancel-access', [AdminAccessController::class, 'cancelWebSys800Data']);
        Route::post('/user-info', [AdminUserInfoController::class, 'getUserInfo']);
        Route::post('/update-user-info', [AdminUserInfoController::class, 'updateUserInfo']);
        Route::post('/delete-user-info', [AdminUserInfoController::class, 'deleteUserInfo']);
        Route::post('/site-news', [AdminUserInfoController::class, 'getContactInfo']);
        Route::post('/delete-site-news', [AdminUserInfoController::class, 'deleteContactInfo']);
        Route::post('/ag-logs', [AdminOtherGameLogsController::class, 'getAGLogs']);
        Route::post('/bbin-logs', [AdminOtherGameLogsController::class, 'getBBINLogs']);
        Route::post('/mg-logs', [AdminOtherGameLogsController::class, 'getMGLogs']);
        Route::post('/pt-logs', [AdminOtherGameLogsController::class, 'getPTLogs']);
        Route::post('/og-logs', [AdminOtherGameLogsController::class, 'getOGLogs']);
        Route::post('/ky-logs', [AdminOtherGameLogsController::class, 'getKYLogs']);
        Route::post('/admin-info', [AdminSystemController::class, 'getAdminInfo']);
        Route::post('/update-admin-info', [AdminSystemController::class, 'updateAdminInfo']);
    });

    // user management

    Route::group(['prefix' => 'user-management'], function ($router) {
        Route::post('/sub-user', [UserManagementController::class, 'getSubUser']);
        Route::post('/add-sub-user', [UserManagementController::class, 'addSubUser']);
        Route::post('/update-sub-user', [UserManagementController::class, 'updateSubUser']);
        Route::post('/suspend-sub-user', [UserManagementController::class, 'suspendSubUser']);
        Route::post('/delete-sub-user', [UserManagementController::class, 'deleteSubUser']);
        Route::post('/permission-sub-user', [UserManagementController::class, 'permissionSubUser']);
        Route::post('/company', [UserManagementController::class, 'getCompany']);
        Route::post('/company-info', [UserManagementController::class, 'getCompanyInfoForAdd']);
        Route::post('/add-company', [UserManagementController::class, 'addCompany']);
        Route::post('/update-company', [UserManagementController::class, 'updateCompany']);
        Route::post('/detail-company', [UserManagementController::class, 'detailCompany']);
        Route::post('/update-money-agency', [UserManagementController::class, 'updateMoneyAgency']);
        Route::post('/update-member', [UserManagementController::class, 'updateMember']);
    });

    // statistics management

    Route::group(['prefix' => 'statistics'], function ($router) {
        Route::post('/dividend-details', [AdminStatisticsController::class, 'getDividendDetails']);
        Route::post('/daily-accounts', [AdminStatisticsController::class, 'getDailyAccounts']);
        Route::post('/system-logs', [AdminStatisticsController::class, 'getSystemLogs']);
        Route::post('/get-online', [AdminStatisticsController::class, 'getOnlineData']);
    });

    // sport resport management

    Route::group(['prefix' => 'sport-report'], function ($router) {
        Route::get('/all', [SportReportController::class, 'getSportReport']);
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
    Route::group(['prefix' => 'server'], function ($router) {
        Route::get('/update', [AdminServerController::class, 'updateServer']);
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
        // third party score data
        Route::post('/total-result', [SportScoreResultController::class, 'getScoreResult']);

        Route::post('/auto_ft_check_score', [SportController::class, 'autoFTCheckScore']);
        Route::post('/auto_bk_check_score', [SportController::class, 'autoBKCheckScore']);
        Route::post('/auto_parlay_ft_check_score', [SportController::class, 'autoFTParlayCheckScore']);
        Route::post('/auto_parlay_bk_check_score', [SportController::class, 'autoBKParlayCheckScore']);
    });
    // lottery result routes
    Route::group(['prefix' => 'lottery-result'], function ($router) {
        // lottery result api
        Route::post('/all', [ThirdpartyLotteryResultController::class, 'getLotteryResult']);
        Route::post('/get-cqssc', [ThirdpartyLotteryResultController::class, 'getLotteryResultCQSSC']);
        Route::post('/get-ffc5', [ThirdpartyLotteryResultController::class, 'getLotteryResultHN300']);
        Route::post('/get-txssc', [ThirdpartyLotteryResultController::class, 'getLotteryResultTXFFC']);
        Route::post('/get-twssc', [ThirdpartyLotteryResultController::class, 'getLotteryResultTW300']);
        Route::post('/get-azxy5', [ThirdpartyLotteryResultController::class, 'getLotteryResultAZXY5']);
        Route::post('/get-xjssc', [ThirdpartyLotteryResultController::class, 'getLotteryResultXJSSC']);
        Route::post('/get-tjssc', [ThirdpartyLotteryResultController::class, 'getLotteryResultTJSSC']);
        Route::post('/get-gd11', [ThirdpartyLotteryResultController::class, 'getLotteryResultGD11X5']);
        Route::post('/get-azxy10', [ThirdpartyLotteryResultController::class, 'getLotteryResultAZXY10']);
        Route::post('/get-bjpk', [ThirdpartyLotteryResultController::class, 'getLotteryResultBJPK10']);
        Route::post('/get-xyft', [ThirdpartyLotteryResultController::class, 'getLotteryResultXYFT']);
        Route::post('/get-cqsf', [ThirdpartyLotteryResultController::class, 'getLotteryResultCQXYNC']);
        Route::post('/get-tjsf', [ThirdpartyLotteryResultController::class, 'getLotteryResultTJKL10']);
        Route::post('/get-gdsf', [ThirdpartyLotteryResultController::class, 'getLotteryResultGDKL10']);
        Route::post('/get-gxsf', [ThirdpartyLotteryResultController::class, 'getLotteryResultGXKL10']);
        Route::post('/get-shssl', [ThirdpartyLotteryResultController::class, 'getLotteryResultSHSSL']);
        Route::post('/get-tcpl3', [ThirdpartyLotteryResultController::class, 'getLotteryResultTCPL3']);
        Route::post('/get-fc3d', [ThirdpartyLotteryResultController::class, 'getLotteryResultFC3D']);

        // checkout result api
        Route::post('/azxy10', [ThirdpartyLotteryResultController::class, 'checkoutAZXY10']);
        Route::post('/azxy5', [ThirdpartyLotteryResultController::class, 'checkoutAZXY5']);
        Route::post('/bjkn', [ThirdpartyLotteryResultController::class, 'checkoutBJKN']);
        Route::post('/bjpk', [ThirdpartyLotteryResultController::class, 'checkoutBJPK10']);
        Route::post('/cqsf', [ThirdpartyLotteryResultController::class, 'checkoutCQSF']);
        Route::post('/cq', [ThirdpartyLotteryResultController::class, 'checkoutCQ']);
        Route::post('/d3', [ThirdpartyLotteryResultController::class, 'checkoutD3']);
        Route::post('/ffc5', [ThirdpartyLotteryResultController::class, 'checkoutFFC5']);
        Route::post('/gd11', [ThirdpartyLotteryResultController::class, 'checkoutGD11X5']);
        Route::post('/gdsf', [ThirdpartyLotteryResultController::class, 'checkoutGDSF']);
        Route::post('/gxsf', [ThirdpartyLotteryResultController::class, 'checkoutGXSF']);
        Route::post('/p3', [ThirdpartyLotteryResultController::class, 'checkoutP3']);
        Route::post('/t3', [ThirdpartyLotteryResultController::class, 'checkoutT3']);
        Route::post('/tjsf', [ThirdpartyLotteryResultController::class, 'checkoutTJSF']);
        Route::post('/tj', [ThirdpartyLotteryResultController::class, 'checkoutTJ']);
        Route::post('/twssc', [ThirdpartyLotteryResultController::class, 'checkoutTWSSC']);
        Route::post('/txssc', [ThirdpartyLotteryResultController::class, 'checkoutTXSSC']);
        Route::post('/jx', [ThirdpartyLotteryResultController::class, 'checkoutXJSSC']);
        Route::post('/xyft', [ThirdpartyLotteryResultController::class, 'checkoutXYFT']);
    });

    Route::post('/lottery-status', [KitheController::class, 'getLotteryStatus']);
    Route::post('/handicap/update', [KitheController::class, 'updateHandicap']);

    Route::post('/macao-lottery-status', [KitheController::class, 'getMacaoLotteryStatus']);
    Route::post('/macao-handicap/update', [KitheController::class, 'updateMacaoHandicap']);

    // other game routes
    Route::group(['prefix' => 'other-game'], function ($router) {
        Route::post('/og-token', [OGController::class, 'getOGToken']);
        Route::post('/og-transaction', [OGController::class, 'getOGTransaction']);
        Route::post('/ag-transaction', [AGController::class, 'getAGTransaction']);
        Route::post('/yoplay-transaction', [AGController::class, 'getYoplayTransaction']);
        Route::post('/bbin-transaction', [BBINController::class, 'getBBINTransaction']);
        Route::post('/mg-transaction', [MGController::class, 'getMGTransaction']);
        Route::post('/pt-transaction', [PTController::class, 'getPTTransaction']);
        Route::post('/ky-transaction', [ChessController::class, 'getKYTransaction']);
    });
});

Route::group(['prefix' => 'users', 'middleware' => 'CORS'], function ($router) {
    Route::post('/register', [UserController::class, 'register'])->name('register.user');
    Route::post('/login', [UserController::class, 'login'])->name('login.user');
    Route::post('/view-profile', [UserController::class, 'viewProfile'])->name('profile.user');
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

    Route::post('/update-get-score', [SportController::class, 'updateGetScore']);
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

Route::group(['prefix' => 'deposit', 'middleware' => ['CORS', 'auth:api']], function ($router) {
    Route::post('/getBank', [DepositController::class, 'getBank'])->name('web.deposit.getBank');
    Route::post('/get-crypto', [DepositController::class, 'getCrypto']);
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
    Route::post('/delete-event', [AdminChampionBettingController::class, 'handleDeleteEvent']);
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
    Route::get('/get_league_list', [AdminRealWaggerController::class, 'getLeagueList'])->name('admin.realwagger.getLeagueList');
    Route::get('/get_sdata', [AdminRealWaggerController::class, 'getSTableData'])->name('admin.realwagger.getSTableData');
    Route::get('/get_hdata', [AdminRealWaggerController::class, 'getHTableData'])->name('admin.realwagger.getHTableData');
    Route::get('/get_rbdata', [AdminRealWaggerController::class, 'getRBTableData'])->name('admin.realwagger.getRBTableData');
    Route::get('/get_pddata', [AdminRealWaggerController::class, 'getPDTableData'])->name('admin.realwagger.getPDTableData');
    Route::get('/get_hpddata', [AdminRealWaggerController::class, 'getHPDTableData'])->name('admin.realwagger.getHPDTableData');
    Route::get('/get_tdata', [AdminRealWaggerController::class, 'getTTableData'])->name('admin.realwagger.getTTableData');
    Route::get('/get_fdata', [AdminRealWaggerController::class, 'getFTableData'])->name('admin.realwagger.getFTableData');
    Route::get('/get_pdata', [AdminRealWaggerController::class, 'getPTableData'])->name('admin.realwagger.getPTableData');
    Route::get('/get_pldata', [AdminRealWaggerController::class, 'getPLTableData'])->name('admin.realwagger.getPLTableData');
    Route::get('/get_result_data', [AdminRealWaggerController::class, 'getResultTableData'])->name('admin.realwagger.getResultTableData');
});

