<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Dz2;
use App\Models\Test;
use App\Utils\OG\ogUtils;
use App\Models\Web\SysConfig;
use App\Models\User;
use App\Models\WebReportZr;
use Carbon\Carbon;

class OGController extends Controller
{
    protected $arrGameType = array();
    protected $arrPlayType = array();

    public function __construct()
    {

        $this->arrGameType['SPEED BACCARAT'] = '极速百家乐';
        $this->arrGameType['BACCARAT'] = '百家乐';
        $this->arrGameType['BIDDING BACCARAT'] = '竞咪百家乐';
        $this->arrGameType['NEW DT'] = '新式龙虎';
        $this->arrGameType['CLASSIC DT'] = '經典龙虎';
        $this->arrGameType['DRAGON'] = '幸运转盘龙';
        $this->arrGameType['ROULETTE'] = '轮盘';

        //百家乐
        $this->arrPlayType['player'] = '闲';
        $this->arrPlayType['banker'] = '庄';
        $this->arrPlayType['tie'] = '和';
        $this->arrPlayType['player_pair'] = '闲对';
        $this->arrPlayType['banker_pair'] = '庄对';
        $this->arrPlayType['super_six'] = 'Super6';
        //龙虎
        $this->arrPlayType['dragon'] = '龙';
        $this->arrPlayType['tiger'] = '虎';
        $this->arrPlayType['dt-tie'] = '和';

        //轮盘
        for ($i = 1; $i <= 36; $i++) {
            $this->arrPlayType['s' . $i] = $i;
        }
        for ($i = 1; $i <= 33; $i++) {
            $this->arrPlayType['split' . $i] = $i . ',' . ($i + 3);
        }
        $this->arrPlayType['near1'] = '1,2';
        $this->arrPlayType['near2'] = '2,3';
        $this->arrPlayType['near3'] = '4,5';
        $this->arrPlayType['near4'] = '5,6';
        $this->arrPlayType['near5'] = '7,8';
        $this->arrPlayType['near6'] = '8,9';
        $this->arrPlayType['near7'] = '10,11';
        $this->arrPlayType['near8'] = '11,12';
        $this->arrPlayType['near9'] = '13,14';
        $this->arrPlayType['near10'] = '14,15';
        $this->arrPlayType['near11'] = '16,17';
        $this->arrPlayType['near12'] = '17,18';
        $this->arrPlayType['near13'] = '19,20';
        $this->arrPlayType['near14'] = '20,21';
        $this->arrPlayType['near15'] = '22,23';
        $this->arrPlayType['near16'] = '23,24';
        $this->arrPlayType['near17'] = '25,26';
        $this->arrPlayType['near18'] = '26,27';
        $this->arrPlayType['near19'] = '28,29';
        $this->arrPlayType['near20'] = '29,30';
        $this->arrPlayType['near21'] = '31,32';
        $this->arrPlayType['near22'] = '32,33';
        $this->arrPlayType['near23'] = '34,35';
        $this->arrPlayType['near24'] = '35,36';
        $this->arrPlayType['zero1'] = '0,1';
        $this->arrPlayType['zero2'] = '0,2';
        $this->arrPlayType['zero3'] = '0,3';
        $this->arrPlayType['tri1'] = '0,1,2';
        $this->arrPlayType['tri2'] = '0,2,3';
        for ($i = 1; $i <= 12; $i++) {
            $a = ($i - 1) * 3 + 1;
            $b = ($i - 1) * 3 + 2;
            $c = ($i - 1) * 3 + 3;
            $this->arrPlayType['street' . $i] = $a . ',' . $b . ',' . $c;
        }
        $this->arrPlayType['square1'] = '1,2,4,5';
        $this->arrPlayType['square2'] = '2,3,5,6';
        $this->arrPlayType['square3'] = '4,5,7,8';
        $this->arrPlayType['square4'] = '5,6,8,9';
        $this->arrPlayType['square5'] = '7,8,10,11';
        $this->arrPlayType['square6'] = '8,9,11,12';
        $this->arrPlayType['square7'] = '10,11,13,14';
        $this->arrPlayType['square8'] = '11,12,14,15';
        $this->arrPlayType['square9'] = '13,14,16,17';
        $this->arrPlayType['square10'] = '14,15,17,18';
        $this->arrPlayType['square11'] = '16,17,19,20';
        $this->arrPlayType['square12'] = '17,18,20,21';
        $this->arrPlayType['square13'] = '19,20,22,23';
        $this->arrPlayType['square14'] = '20,21,23,24';
        $this->arrPlayType['square15'] = '22,23,25,26';
        $this->arrPlayType['square16'] = '23,24,26,27';
        $this->arrPlayType['square17'] = '25,26,28,29';
        $this->arrPlayType['square18'] = '26,27,29,30';
        $this->arrPlayType['square19'] = '28,29,31,32';
        $this->arrPlayType['square20'] = '29,30,32,33';
        $this->arrPlayType['square21'] = '31,32,34,35';
        $this->arrPlayType['square22'] = '32,33,35,36';
        $this->arrPlayType['line0'] = '0,1,2,3';
        $this->arrPlayType['line1'] = '1-6';
        $this->arrPlayType['line2'] = '4-9';
        $this->arrPlayType['line3'] = '7-12';
        $this->arrPlayType['line4'] = '10-15';
        $this->arrPlayType['line5'] = '13-18';
        $this->arrPlayType['line6'] = '16-21';
        $this->arrPlayType['line7'] = '19-24';
        $this->arrPlayType['line8'] = '22-27';
        $this->arrPlayType['line9'] = '25-30';
        $this->arrPlayType['line10'] = '28-33';
        $this->arrPlayType['line11'] = '31-36';

        $this->arrPlayType['dozen1'] = '1-12';
        $this->arrPlayType['dozen2'] = '13-24';
        $this->arrPlayType['dozen3'] = '25-36';
        $this->arrPlayType['row1'] = '3,6,9,12,15,18,21,24,27,30,33,36';
        $this->arrPlayType['row2'] = '2,5.8,11,14,17,20,23,26,29,32,35';
        $this->arrPlayType['row3'] = '1,4,7,10,13,16,19,22,25,28.31,34';
        $this->arrPlayType['red'] = '紅';
        $this->arrPlayType['black'] = '黑';
        $this->arrPlayType['odd'] = '單';
        $this->arrPlayType['even'] = '雙';
        $this->arrPlayType['small'] = '小';
        $this->arrPlayType['big'] = '大';
        $this->arrPlayType['1'] = '1';
        $this->arrPlayType['2'] = '2';
        $this->arrPlayType['5'] = '5';
        $this->arrPlayType['10'] = '10';
        $this->arrPlayType['20'] = '20';
        $this->arrPlayType['og'] = '财';
    }

    public function getOGUrl(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "game_type" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user = $request->user();

            $og_username = $user["OG_User"];
            $username = $user['UserName'];
            $OG_Limit1 = $user['OG_Limit1'];
            $OG_Limit2 = $user['OG_Limit2'];

            $login_url = "";

            $sysConfig = SysConfig::all()->first();
            $OGUtils = new OGUtils($sysConfig);

            if ($og_username == null || $og_username == "") {
                $og_username = $username . '_' . $OGUtils->getpassword_OG(3);
                $og_username = strtoupper($og_username);
                $result = $OGUtils->Add_OG_Member($og_username);
                sleep(1);
                $OGUtils->OG_Limit($og_username, $OG_Limit1, $OG_Limit2);
                if ($result == 1) {
                    User::where("UserName", $username)->update(["OG_User" => $og_username]);
                } else {
                    $response["message"] = '网络异常，请与在线客服联系！';
                    return response()->json($response, $response['status']);
                }
            }

            $login_url = $OGUtils->OG_GameUrl($og_username);

            $response["data"] = $login_url;
            $response['message'] = "OG Game URL fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getOGToken()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $login_url = "";

            $sysConfig = SysConfig::all()->first();
            $OGUtils = new OGUtils($sysConfig);
            return $OGUtils->GetToken();

            $response['message'] = "OG Game Token saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getOGTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $sysConfig = SysConfig::all()->first();

            $end_date = Carbon::now()->setTimezone('GMT+8')->format("Y-m-d H:i:s");
            $start_date = Carbon::now()->setTimezone('GMT+8')->subMinutes(10)->format("Y-m-d H:i:s");

            // $end_date = "2023-07-30 12:28:00";
            // $start_date = "2023-07-30 12:18:00";

            $OGUtils = new OGUtils($sysConfig);

            $game_array = $OGUtils->GetGameData($start_date, $end_date);

            // return $game_array;

            foreach ($game_array as $item) {

                $billNo = $item['bettingcode'];
                if ($billNo == '') {
                    continue;
                }
                $playerName = substr($item['membername'], 5);
                $UserName = substr($playerName, 0, -4);
                $gameCode = $item['roundno'];  //游戏局号  
                $netAmount = $item['winloseamount'];  //输赢
                $betTime = $item['bettingdate'];  //投注时间
                $gameType = $this->arrGameType[$item['gamename']] ?? $item['gamename'];  //游戏名称
                $betAmount = $item['bettingamount'];  //投注金额
                $validBetAmount = $item['validbet'];  //有效金额
                if ($item['winloseresult'] == 'tie') $validBetAmount = 0;
                $playType = $this->arrPlayType[$item['bet']] ?? $item['bet'];  //投注内容
                $tableCode = $item['gameid'];  //桌台号
                $loginIP = $item["status"];
                $recalcuTime = $item['bettingdate'];  //派彩时间
                $platformType = 'OG';
                $Type = "BR";
                $round = $item['bettingcode'];
                $VendorId = $item['vendor_id'];

                $user = User::where("OG_User", strtoupper($playerName))->first();

                // return $user;

                if (!isset($user)) continue;

                $game = WebReportZr::where("billNo", $billNo)->where("platformType", $platformType)->first();

                $new_data = array(
                    "billNo" => $billNo,
                    "UserName" => $UserName,
                    "playerName" => $playerName,
                    "Type" => $Type,
                    "gameType" => $gameType,
                    "gameCode" => $gameCode,
                    "netAmount" => $netAmount,
                    "betTime" => $betTime,
                    "betAmount" => $betAmount,
                    "validBetAmount" => $validBetAmount,
                    "playType" => $playType,
                    "tableCode" => $tableCode,
                    "loginIP" => $loginIP,
                    "recalcuTime" => $recalcuTime,
                    "round" => "",
                    "platformType" => $platformType,
                    "VendorId" => $VendorId,
                    "Checked" => 1,
                );

                if (!isset($game)) {
                    $web_report_zr = new WebReportZr;
                    $web_report_zr->create($new_data);

                    User::where("UserName", $UserName)->decrement("withdrawal_condition", (int)$validBetAmount);
                } else {
                    WebReportZr::where("billNo", $billNo)
                        ->where("platformType", $platformType)
                        ->update($new_data);
                }

                // $balance= $OGUtils->OG_Money(strtoupper($playerName));

                // User::where("UserName", $UserName)->update([
                //     "OG_Money" => $balance,
                // ]);
            }

            $response['message'] = "OG Game Transaction saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
}
