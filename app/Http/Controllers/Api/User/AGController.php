<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Dz2;
use App\Utils\AG\agUtils;
use App\Models\Web\SysConfig;
use App\Models\User;
use App\Models\WebReportZr;
use Carbon\Carbon;

function GetUrl($url, $ip = null, $timeout = 20)
{
    $ch = curl_init();

    //需要获取的URL地址，也可以在PHP的curl_init()函数中设置
    curl_setopt($ch, CURLOPT_URL, $url);

    //启用时会设置HTTP的method为GET，因为GET是默认是，所以只在被修改的情况下使用s
    curl_setopt($ch, CURLOPT_HTTPGET, true);

    //在启用CURLOPT_RETURNTRANSFER时候将获取数据返回
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //bind to specific ip address if it is sent trough arguments
    if ($ip) {
        //在外部网络接口中使用的名称，可以是一个接口名，IP或者主机名
        curl_setopt($ch, CURLOPT_INTERFACE, $ip);
    }

    //设置curl允许执行的最长秒数  $timeout
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    //执行一个curl会话
    $result = curl_exec($ch);

    curl_close($ch);

    if (curl_errno($ch)) {
        return false;
    } else {
        return $result;
    }
}

class AGController extends Controller
{

    public function getAGGameAll(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "g_type" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $result = Dz2::where("Open", 1)
                ->where(function ($query) {
                    $query->where("PlatformType", "AG")
                        ->orWhere("PlatformType", "YOPLAY")
                        ->orWhere("PlatformType", "XIN");
                })
                ->get();

            foreach ($result as $item) {
                $item["ZH_Logo_File"] = "http://pic.pj6678.com/" . $item["ZH_Logo_File"];
            }

            $response["data"] = $result;
            $response['message'] = "AG Game Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getAGUrl(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "game_type" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $game_type = $request_data["game_type"];

            $user = $request->user();

            $ag_username = $user["AG_User"];
            $ag_password = $user["AG_Pass"];
            $username = $user['UserName'];
            $tp = $user['AG_Type'];

            $login_url = "";

            $sysConfig = SysConfig::all()->first();

            if ($username == "guest") {
                $AGUtils = new AGUtils($sysConfig);
                $ag_username = strtoupper('TEST' . $AGUtils->getpassword(10));
                $ag_password = strtoupper($AGUtils->getpassword(10));
                $result = $AGUtils->Addmember($ag_username, $ag_password, 0);
                $results = $AGUtils->Deposit($ag_username, $ag_password, 2000, 'IN');
                $login_url = $AGUtils->getGameUrl($ag_username, $ag_password, "A", $_SERVER['HTTP_HOST'], 0);
            } else {
                $AGUtils = new AGUtils($sysConfig);
                if ($ag_username == null || $ag_username == "") {
                    $WebCode = ltrim(trim($sysConfig['AG_User']));
                    if (!preg_match("/^[A-Za-z0-9]{4,12}$/", $user['UserName'])) {
                        $ag_username = $WebCode . '_' . $AGUtils->getpassword(10);
                    } else {
                        $ag_username = $WebCode . '_' . trim($user['UserName']) . $AGUtils->getpassword(1);
                    }
                    $ag_username = strtoupper($ag_username);
                    $ag_password = strtoupper($AGUtils->getpassword(10));
                    $result = $AGUtils->Addmember($ag_username, $ag_password, 1);

                    if ($result['info'] == '0') {
                        User::where("UserName", $username)->update([
                            "AG_User" => $ag_username,
                            "AG_Pass" => $ag_password,
                        ]);
                    } else {
                        $response["message"] = '网络异常，请与在线客服联系！';
                        return response()->json($response, $response['status']);
                    }
                }

                $login_url = $AGUtils->getGameUrl($ag_username, $ag_password, $tp, $_SERVER['HTTP_HOST'], 1, $game_type);
            }

            $response["data"] = $login_url;
            $response['message'] = "AG Game URL fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getAGTransaction(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $sysConfig = SysConfig::all()->first();

            $AGUtils = new AGUtils($sysConfig);

            $plan_code = "82492F975FCCF4FD695DECF898E76322";

            $end_date = Carbon::now()->setTimezone('GMT-4')->format("Y-m-d H:i:s");

            $start_date = Carbon::now()->setTimezone('GMT-4')->subMinutes(10)->format("Y-m-d H:i:s");

            $real_game_type = array(
                "BAC",
                "CBAC",
                "LINK",
                "DT",
                "SHB",
                "ROU",
                "LBAC",
                "SBAC",
                "NN",
                "BJ",
                "ZJH",
                "BF",
                "SG",
            );


            $url = env("TRANSACTION_URL") . "/ag.php";

            $result = get_object_vars(json_decode(GetUrl($url)))["row"] ?? array();

            return $result;

            foreach ($result as $row) {

                $row = get_object_vars(get_object_vars($row)["@attributes"]);

                $billNo = $row["billNo"];
                $playerName = $row["playName"];
                $Type = "";
                $GameType = $row["gameType"];
                $gameCode = $row["gameCode"];
                $netAmount = $row["netAmount"];
                $betTime = $row["betTime"];
                $betAmount = $row["betAmount"];
                $validBetAmount = $row["validBetAmount"];
                $playType = $row["playType"];
                $tableCode = $row["tableCode"];
                $loginIP = $row["betIP"];
                $recalcuTime = $row["recalcuTime"];
                $platformType = $row["platformType"];
                $round = $row["round"];
                $VendorId = 0;
                $result = $row["result"];
                $gameType = addslashes($GameType);
                $gameCode = addslashes($gameCode);

                // return $gameType;

                $web_report_zr = WebReportZr::where("billNo", $billNo)
                    ->where("platformType", $platformType)->first();

                $user = User::where("AG_User", $playerName)->first();

                if (!isset($user)) continue;

                $UserName = $user["UserName"];

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
                    "round" => $round,
                    "platformType" => $platformType,
                    "VendorId" => $VendorId,
                    "Checked" => 1,
                );

                if (!isset($web_report_zr)) {
                    $web_report_zr = new WebReportZr;
                    $web_report_zr->create($new_data);
                } else {
                    WebReportZr::where("billNo", $billNo)
                        ->where("platformType", $platformType)
                        ->update($new_data);
                }

                $AGUtils = new AGUtils($sysConfig);

                $user = User::where("UserName", $playerName)->first();

                // $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                // User::where("UserName", $UserName)->update([
                //     "AG_Money" => $balance,
                // ]);
            }

            $response['message'] = "AG Game Transaction saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getEGameTransaction(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $sysConfig = SysConfig::all()->first();

            $AGUtils = new AGUtils($sysConfig);

            $plan_code = "82492F975FCCF4FD695DECF898E76322";

            $end_date = Carbon::now()->setTimezone('GMT-4')->format("Y-m-d H:i:s");

            $start_date = Carbon::now()->setTimezone('GMT-4')->subMinutes(10)->format("Y-m-d H:i:s");

            $real_game_type = array(
                "BAC",
                "CBAC",
                "LINK",
                "DT",
                "SHB",
                "ROU",
                "LBAC",
                "SBAC",
                "NN",
                "BJ",
                "ZJH",
                "BF",
                "SG",
            );


            $url = env("TRANSACTION_URL") . "/ag.php";

            $result = get_object_vars(json_decode(GetUrl($url)))["row"] ?? array();

            return $result;

            foreach ($result as $row) {

                $row = get_object_vars(get_object_vars($row)["@attributes"]);

                $billNo = $row["billNo"];
                $playerName = $row["playName"];
                $Type = "";
                $GameType = $row["gameType"];
                $gameCode = $row["gameCode"];
                $netAmount = $row["netAmount"];
                $betTime = $row["betTime"];
                $betAmount = $row["betAmount"];
                $validBetAmount = $row["validBetAmount"];
                $playType = $row["playType"];
                $tableCode = $row["tableCode"];
                $loginIP = $row["betIP"];
                $recalcuTime = $row["recalcuTime"];
                $platformType = $row["platformType"];
                $round = $row["round"];
                $VendorId = 0;
                $result = $row["result"];
                $gameType = addslashes($GameType);
                $gameCode = addslashes($gameCode);

                // return $gameType;

                $web_report_zr = WebReportZr::where("billNo", $billNo)
                    ->where("platformType", $platformType)->first();

                $user = User::where("AG_User", $playerName)->first();

                if (!isset($user)) continue;

                $UserName = $user["UserName"];

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
                    "round" => $round,
                    "platformType" => $platformType,
                    "VendorId" => $VendorId,
                    "Checked" => 1,
                );

                if (!isset($web_report_zr)) {
                    $web_report_zr = new WebReportZr;
                    $web_report_zr->create($new_data);
                } else {
                    WebReportZr::where("billNo", $billNo)
                        ->where("platformType", $platformType)
                        ->update($new_data);
                }

                $AGUtils = new AGUtils($sysConfig);

                $user = User::where("UserName", $playerName)->first();

                // $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                // User::where("UserName", $UserName)->update([
                //     "AG_Money" => $balance,
                // ]);
            }

            $response['message'] = "AG Game Transaction saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getYoplayTransaction(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $sysConfig = SysConfig::all()->first();

            $AGUtils = new AGUtils($sysConfig);

            $plan_code = "82492F975FCCF4FD695DECF898E76322";

            $end_date = Carbon::now()->setTimezone('GMT+4')->format("Y-m-d H:i:s");

            $start_date = Carbon::now()->setTimezone('GMT+4')->subMinutes(10)->format("Y-m-d H:i:s");

            $yo_play_game_type = array(
                "YFP",
                "YDZ",
                "YBIR",
                "YMFD",
                "YFD",
                "YBEN",
                "YHR",
                "YMFR",
                "YGS",
                "YFR",
                "YMBN",
                "YGFS",
                "YJFS",
                "YMBI",
                "YMBA",
                "YMBZ",
                "YMAC",
                "YMJW",
                "YMJH",
                "YMBF",
                "YMSG",
                "YMJJ",
                "YJTW",
                "YMD2",
                "YJBZ",
                "YMSL",
                "YMDD",
                "YMKM",
                "YMDL",
                "YMPL",
                "YMBJ",
                "YMLD",
                "YMGG",
                "YMFW",
                "YMBS",
                "YMEF",
                "YMLS",
                "YMPP",
                "YJBI",
                "YMRA",
                "YJFD",
                "YMFP",
                "YMPR",
                "YM2K",
            );

            $users = User::query()->get(["AG_User"]);


            $url = env("TRANSACTION_URL") . "/yoplay.php";

            $result = get_object_vars(json_decode(GetUrl($url)))["row"] ?? array();

            return get_object_vars(json_decode(GetUrl($url)));

            foreach ($yo_play_game_type as $game_type) {

                foreach ($orders as $order) {

                    foreach ($users as $user) {

                        $AG_User = $user["AG_User"];

                        $agent = "";

                        $billno = "";

                        $orders = $AGUtils->getYoplayOrder($plan_code, $agent, $AG_User, $start_date, $end_date, $game_type, $billno);

                        return $orders;
                    }

                    $billNo = $order[0]["result"][1]["row"]["billNo"];
                    $playerName = $order[0]["result"][1]["row"]["playName"];
                    $Type = "";
                    $GameType = $order[0]["result"][1]["row"]["gameType"];
                    $gameCode = $order[0]["result"][1]["row"]["gameCode"];
                    $netAmount = $order[0]["result"][1]["row"]["netAmount"];
                    $betTime = $order[0]["result"][1]["row"]["betTime"];
                    $betAmount = $order[0]["result"][1]["row"]["betAmount"];
                    $validBetAmount = $order[0]["result"][1]["row"]["validBetAmount"];
                    $playType = $order[0]["result"][1]["row"]["playType"];
                    $tableCode = $order[0]["result"][1]["row"]["tableCode"];
                    $loginIP = $order[0]["result"][1]["row"]["betIP"];
                    $recalcuTime = $order[0]["result"][1]["row"]["recalcuTime"];
                    $platformType = $order[0]["result"][1]["row"]["platformType"];
                    $round = $order[0]["result"][1]["row"]["round"];
                    $VendorId = 0;
                    $result = $order[0]["result"][1]["row"]["result"];
                    $gameType = addslashes($GameType);
                    $gameCode = addslashes($gameCode);

                    $web_report_zr = WebReportZr::where("billNo", $billNo)
                        ->where("platformType", $platformType)->first();

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
                        "round" => $round,
                        "platformType" => $platformType,
                        "VendorId" => $VendorId,
                        "Checked" => 1,
                    );

                    if (isset($web_report_zr)) {
                        $web_report_zr = new WebReportZr;
                        $web_report_zr->create($new_data);
                    } else {
                        WebReportZr::where("billNo", $billNo)
                            ->where("platformType", $platformType)
                            ->update($new_data);
                    }

                    $AGUtils = new AGUtils($sysConfig);

                    $user = User::where("UserName", $UserName)->first();

                    $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                    User::where("UserName", $UserName)->update([
                        "AG_Money" => $balance,
                    ]);
                }
            }

            $response['message'] = "YOPLAY Game Transaction saved successfully!";
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
