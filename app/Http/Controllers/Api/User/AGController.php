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
use App\Models\WebReportHtr;
use App\Models\WebReportZr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Ftp;

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
                        ->orWhere("PlatformType", "YOPLAY");
                    // ->orWhere("PlatformType", "XIN");
                })
                ->get();

            foreach ($result as $item) {
                if (!is_file(storage_path("app/public/upload/zr_images/").$item["ZH_Logo_File"])) {
                    $item["ZH_Logo_File"] = "http://pic.pj6678.com/".$item["ZH_Logo_File"];
                } else {
                    $item["ZH_Logo_File"] = env('APP_URL').Storage::url("upload/zr_images/").$item["ZH_Logo_File"];
                }
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

    // transaction controllers by ftp call

    public function getFTPAGINTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $filename_array = array();

            array_push($filename_array, Carbon::now()->setTimezone('GMT-4')->format("YmdHi"));

            for ($i = 1; $i <= 40; $i++) {

                array_push($filename_array, Carbon::now()->setTimezone('GMT-4')->subMinutes($i)->format("YmdHi"));
            }

            $date = Carbon::now()->setTimezone('GMT-4')->format("Ymd");

            foreach ($filename_array as $file_name) {

                $file_path = "AGIN/" . $date . "/" . $file_name . ".xml";

                // return $file_path;

                $fileExists = Storage::disk('ftp')->exists($file_path);

                if ($fileExists) {

                    $xmlContents = Storage::disk('ftp')->get($file_path);
    
                    $xml_array = explode("\r\n", $xmlContents);
    
                    $xml_array = array_filter($xml_array);
    
                    foreach($xml_array as $xml) {
        
                        $result = simplexml_load_string($xml);
            
                        $row = get_object_vars($result);
    
                        $row = $row["@attributes"];
    
                        $billNo = $row["billNo"];
                        $playerName = $row["playerName"];
                        $GameType = $row["gameType"];
                        $gameCode = $row["gameCode"];
                        $netAmount = $row["netAmount"];
                        $betTime = $row["betTime"];
                        $betAmount = $row["betAmount"];
                        $validBetAmount = $row["validBetAmount"];
                        $playType = $row["playType"];
                        $tableCode = $row["tableCode"];
                        $loginIP = $row["loginIP"];
                        $recalcuTime = $row["recalcuTime"];
                        $platformType = $row["platformType"];
                        $round = $row["round"];
                        $VendorId = $row["agentCode"];
                        $result = $row["result"];
                        $gameType = addslashes($GameType);
                        $gameCode = addslashes($gameCode);
    
                        $web_report_zr = WebReportZr::where("billNo", $billNo)
                            ->where("platformType", $platformType)->first();
    
                        $user = User::where("AG_User", $playerName)->first();
    
                        if (!isset($user)) continue;
    
                        $UserName = $user["UserName"];
    
                        $new_data = array(
                            "billNo" => $billNo,
                            "UserName" => $UserName,
                            "playerName" => $playerName,
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
    
                    }
                }
            }

            $response['message'] = "AGIN Game Transaction saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
    public function getFTPYoplayTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $filename_array = array();

            array_push($filename_array, Carbon::now()->setTimezone('GMT-4')->format("YmdHi"));

            for ($i = 1; $i <= 40; $i++) {

                array_push($filename_array, Carbon::now()->setTimezone('GMT-4')->subMinutes($i)->format("YmdHi"));
            }

            $date = Carbon::now()->setTimezone('GMT-4')->format("Ymd");

            foreach ($filename_array as $file_name) {

                $file_path = "YOPLAY/" . $date . "/" . $file_name . ".xml";

                // return $file_path;

                $fileExists = Storage::disk('ftp')->exists($file_path);

                if ($fileExists) {

                    $xmlContents = Storage::disk('ftp')->get($file_path);
    
                    $xml_array = explode("\r\n", $xmlContents);
    
                    $xml_array = array_filter($xml_array);
    
                    foreach($xml_array as $xml) {
        
                        $result = simplexml_load_string($xml);
            
                        $row = get_object_vars($result);
    
                        $row = $row["@attributes"];
    
                        $billNo = $row["billNo"];
                        $playerName = $row["playerName"];
                        $GameType = $row["gameType"];
                        $gameCode = $row["gameCode"];
                        $netAmount = $row["netAmount"];
                        $betTime = $row["betTime"];
                        $betAmount = $row["betAmount"];
                        $validBetAmount = $row["validBetAmount"];
                        $playType = $row["playType"] == "null" ? "" : $row["playType"];
                        $tableCode = $row["tableCode"] == "null" ? "" : $row["tableCode"];
                        $loginIP = $row["loginIP"];
                        $recalcuTime = $row["recalcuTime"];
                        $platformType = $row["platformType"];
                        $round = $row["round"];
                        $VendorId = $row["agentCode"];
                        $result = $row["result"];
                        $gameType = addslashes($GameType);
                        $gameCode = addslashes($gameCode);
    
                        $web_report_zr = WebReportZr::where("billNo", $billNo)
                            ->where("platformType", $platformType)->first();
    
                        $user = User::where("AG_User", $playerName)->first();
    
                        if (!isset($user)) continue;
    
                        $UserName = $user["UserName"];
    
                        $new_data = array(
                            "billNo" => $billNo,
                            "UserName" => $UserName,
                            "playerName" => $playerName,
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
    
                    }
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
    public function getFTPXinTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $filename_array = array();

            array_push($filename_array, Carbon::now()->setTimezone('GMT-4')->format("YmdHi"));

            for ($i = 1; $i <= 40; $i++) {

                array_push($filename_array, Carbon::now()->setTimezone('GMT-4')->subMinutes($i)->format("YmdHi"));
            }

            $date = Carbon::now()->setTimezone('GMT-4')->format("Ymd");

            foreach ($filename_array as $file_name) {

                $file_path = "XIN/" . $date . "/" . $file_name . ".xml";

                // return $file_path;

                $fileExists = Storage::disk('ftp')->exists($file_path);

                // $fileExists = Storage::disk('ftp')->exists("XIN/20230728/202307280058.xml");

                if ($fileExists) {

                    $xmlContents = Storage::disk('ftp')->get($file_path);

                    // $xmlContents = Storage::disk('ftp')->get("XIN/20230728/202307280058.xml");
    
                    $xml_array = explode("\r\n", $xmlContents);
    
                    $xml_array = array_filter($xml_array);
    
                    foreach($xml_array as $xml) {
        
                        $result = simplexml_load_string($xml);
            
                        $row = get_object_vars($result);
    
                        $row = $row["@attributes"];

                        // return $row;
                        
                        $data_type = $row["dataType"];

                        if ($data_type != "EBR") continue;
    
                        $billNo = $row["billNo"];
                        $playerName = $row["playerName"];
                        $GameType = $row["gameType"];
                        $gameCode = $row["gameCode"];
                        $netAmount = $row["netAmount"];
                        $betTime = $row["betTime"];
                        $betAmount = $row["betAmount"];
                        $validBetAmount = $row["validBetAmount"];
                        $playType = $row["playType"] == "null" ? "" : $row["playType"];
                        $tableCode = $row["tableCode"] == "null" ? "" : $row["tableCode"];
                        $loginIP = $row["loginIP"];
                        $recalcuTime = $row["recalcuTime"];
                        $platformType = $row["platformType"];
                        $round = $row["round"];
                        $VendorId = $row["agentCode"];
                        $result = $row["result"];
                        $gameType = addslashes($GameType);
                        $gameCode = addslashes($gameCode);
    
                        $web_report_zr = WebReportZr::where("billNo", $billNo)
                            ->where("platformType", $platformType)->first();
    
                        $user = User::where("AG_User", $playerName)->first();
    
                        if (!isset($user)) continue;
    
                        $UserName = $user["UserName"];
    
                        $new_data = array(
                            "billNo" => $billNo,
                            "UserName" => $UserName,
                            "playerName" => $playerName,
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
    
                    }
                }
            }

            $response['message'] = "XIN Game Transaction saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
    public function getFTPHunterTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $filename_array = array();

            array_push($filename_array, Carbon::now()->setTimezone('GMT-4')->format("YmdHi"));

            for ($i = 1; $i <= 10; $i++) {

                array_push($filename_array, Carbon::now()->setTimezone('GMT-4')->subMinutes($i)->format("YmdHi"));
            }

            $date = Carbon::now()->setTimezone('GMT-4')->format("Ymd");

            foreach ($filename_array as $file_name) {

                $file_path = "HUNTER/" . $date . "/" . $file_name . ".xml";

                // return $file_path;

                // $fileExists = Storage::disk('ftp')->exists($file_path);

                $fileExists = Storage::disk('ftp')->exists("HUNTER/20230726/202307260744.xml");

                if ($fileExists) {

                    // $xmlContents = Storage::disk('ftp')->get($file_path);

                    $xmlContents = Storage::disk('ftp')->get("HUNTER/20230726/202307260744.xml");
    
                    $xml_array = explode("\r\n", $xmlContents);
    
                    $xml_array = array_filter($xml_array);
    
                    foreach($xml_array as $xml) {
        
                        $result = simplexml_load_string($xml);
            
                        $row = get_object_vars($result);
    
                        $row = $row["@attributes"];

                        return $row;

                        $tradeNo = $row["tradeNo"];
                        $playerName = $row["playerName"];
                        $type = $row["transferId"];
                        $netAmount = $row["dst_amount"];
                        $srcAmount = $row["src_amount"];
                        $betTime = date("Y-m-d H:i:s", $row["creationTime"]);
                        $recalcuTime = date("Y-m-d H:i:s", $row["creationTime"]);
                        $betAmount = $row["account"];
                        $validBetAmount = $row["cus_account"];
                        $cost = $row["fishcost"];
                        $scene_id = $row["sceneid"];
                        $room_id = $row["roomid"];
                        $room_bet = $row["betx"];
                        $loginIP = $row["betIp"];
                        $platformType = "HUNTER";
                        $jackpotcontribute = "jackpotcontribute";
                        $VendorId = 0;
    
                        $web_report_htr = WebReportHtr::where("tradeNo", $tradeNo)
                            ->where("platformType", $platformType)->first();
    
                        $user = User::where("AG_User", $playerName)->first();
    
                        if (!isset($user)) continue;
    
                        $UserName = $user["UserName"];
    
                        $new_data = array(
                            "tradeNo" => $tradeNo,
                            "UserName" => $UserName,
                            "playerName" => $playerName,
                            "Type" => $type,
                            "platformType" => $platformType,
                            "sceneId" => $scene_id,
                            "SceneStartTime" => $betTime,
                            "SceneEndTime" => $recalcuTime,
                            "Roomid" => $room_id,
                            "Roombet" => $room_bet,
                            "Cost" => $betAmount,
                            "Earn" => $validBetAmount,
                            "Jackpotcomm" => $jackpotcontribute,
                            "transferAmount" => "",
                            "previousAmount" => $srcAmount,
                            "currentAmount" => $netAmount,
                            "IP" => $loginIP,
                            "VendorId" => $VendorId,
                            "Checked" => 1,
                        );
    
                        if (!isset($web_report_htr)) {
                            $web_report_htr = new WebReportHtr();
                            $web_report_htr->create($new_data);
                        } else {
                            WebReportHtr::where("tradeNo", $tradeNo)
                                ->where("platformType", $platformType)
                                ->update($new_data);
                        }
    
                    }
                }
            }

            $response['message'] = "XIN Game Transaction saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    // transaction contollers by api call
    public function getAGTransaction()
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

            $url = env("TRANSACTION_URL") . "/third-party/ag-transaction/real";

            $result = get_object_vars(json_decode(GetUrl($url)));

            $result = get_object_vars($result["data"])["row"] ?? array();

            // return is_array($result);

            if (is_array($result)) {

                foreach ($result as $row) {

                    $row = get_object_vars($row)["@attributes"];
                    $row = get_object_vars($row);

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

                    // $AGUtils = new AGUtils($sysConfig);

                    // $user = User::where("UserName", $playerName)->first();

                    // $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                    // User::where("UserName", $UserName)->update([
                    //     "AG_Money" => $balance,
                    // ]);
                }
            } else {

                $row = get_object_vars($result)["@attributes"];
                $row = get_object_vars($row);

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

                if (isset($user)) {

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
                }
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

    public function getEGameTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $sysConfig = SysConfig::all()->first();

            $url = env("TRANSACTION_URL") . "/third-party/ag-transaction/egame";

            $result = get_object_vars(json_decode(GetUrl($url)));

            $result = get_object_vars($result["data"])["row"] ?? array();

            // return $result;

            if (is_array($result)) {

                foreach ($result as $item) {

                    $item = get_object_vars($item)["@attributes"];
                    $item = get_object_vars($item);

                    // return $item;

                    $billNo = $item["billno"];
                    $playerName = $item["username"];
                    $Type = "";
                    $GameType = $item["gametype"];
                    $gameCode = $item["gmcode"];
                    $netAmount = $item["dst_amount"];
                    $betTime = $item["billtime"];
                    $betAmount = $item["account"];
                    $validBetAmount = $item["valid_account"];
                    $playType = $item["slottype"];
                    $tableCode = $item["slottype"];
                    $loginIP = $item["betIP"];
                    $recalcuTime = $item["reckontime"];
                    $platformType = $item["platformtype"];
                    $round = $item["round"] ?? "";
                    $VendorId = 0;
                    $result = $item["flag"];
                    $gameType = addslashes($GameType);
                    $gameCode = addslashes($gameCode);

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

                    // $AGUtils = new AGUtils($sysConfig);

                    // $user = User::where("UserName", $playerName)->first();

                    // $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                    // User::where("UserName", $UserName)->update([
                    //     "AG_Money" => $balance,
                    // ]);
                }
            } else {

                $item = get_object_vars($result)["@attributes"];
                $item = get_object_vars($item);

                // return $item;

                $billNo = $item["billno"];
                $playerName = $item["username"];
                $Type = "";
                $GameType = $item["gametype"];
                $gameCode = $item["gmcode"];
                $netAmount = $item["dst_amount"];
                $betTime = $item["billtime"];
                $betAmount = $item["account"];
                $validBetAmount = $item["valid_account"];
                $playType = $item["slottype"];
                $tableCode = $item["slottype"];
                $loginIP = $item["betIP"];
                $recalcuTime = $item["reckontime"];
                $platformType = $item["platformtype"];
                $round = $item["round"] ?? "";
                $VendorId = 0;
                $result = $item["flag"];
                $gameType = addslashes($GameType);
                $gameCode = addslashes($gameCode);

                $web_report_zr = WebReportZr::where("billNo", $billNo)
                    ->where("platformType", $platformType)->first();

                $user = User::where("AG_User", $playerName)->first();

                if (isset($user)) {

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
                }
            }

            $response['message'] = "EG Game Transaction saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSlotGameTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {


            $url = env("TRANSACTION_URL") . "/third-party/ag-transaction/slot-game";

            $result = get_object_vars(json_decode(GetUrl($url)));

            $result = get_object_vars($result["data"])["row"] ?? array();

            // return $result;

            if (is_array($result)) {
            } else {
            }

            foreach ($result as $item) {

                $item = get_object_vars($item)["@attributes"];
                $item = get_object_vars($item);

                // return $item;

                $billNo = $item["billno"];
                $playerName = $item["username"];
                $Type = "";
                $GameType = $item["gametype"];
                $gameCode = $item["gmcode"];
                $netAmount = $item["dst_amount"];
                $betTime = $item["billtime"];
                $betAmount = $item["account"];
                $validBetAmount = $item["valid_account"];
                $playType = $item["slottype"];
                $tableCode = $item["slottype"];
                $loginIP = $item["betIP"];
                $recalcuTime = $item["reckontime"];
                $platformType = $item["platformtype"];
                $round = $item["round"] ?? "";
                $VendorId = 0;
                $result = $item["flag"];
                $gameType = addslashes($GameType);
                $gameCode = addslashes($gameCode);

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

                // $AGUtils = new AGUtils($sysConfig);

                // $user = User::where("UserName", $playerName)->first();

                // $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                // User::where("UserName", $UserName)->update([
                //     "AG_Money" => $balance,
                // ]);
            }

            $response['message'] = "EG Game Transaction saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getYoplayTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $url = env("TRANSACTION_URL") . "/third-party/ag-transaction/yoplay";

            $result = get_object_vars(json_decode(GetUrl($url)));

            $result = get_object_vars($result["data"])["row"] ?? array();

            // return $result;

            if (is_array($result)) {

                foreach ($result as $item) {

                    $item = get_object_vars($item)["@attributes"];
                    $item = get_object_vars($item);

                    // return $item;

                    $billNo = $item["billno"];
                    $playerName = $item["username"];
                    $Type = "";
                    $GameType = $item["gametype"];
                    $gameCode = $item["gmcode"];
                    $netAmount = $item["dst_amount"];
                    $betTime = $item["billtime"];
                    $betAmount = $item["account"];
                    $validBetAmount = $item["valid_account"];
                    $playType = $item["slottype"];
                    $tableCode = $item["slottype"];
                    $loginIP = $item["betIP"];
                    $recalcuTime = $item["reckontime"];
                    // $platformType = $item["platformtype"];
                    $platformType = "YOPLAY";
                    $round = $item["round"] ?? "";
                    $VendorId = 0;
                    $result = $item["flag"];
                    $gameType = addslashes($GameType);
                    $gameCode = addslashes($gameCode);

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

                    // $AGUtils = new AGUtils($sysConfig);

                    // $user = User::where("UserName", $UserName)->first();

                    // $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                    // User::where("UserName", $UserName)->update([
                    //     "AG_Money" => $balance,
                    // ]);
                }
            } else {

                $item = get_object_vars($result)["@attributes"];
                $item = get_object_vars($item);

                // return $item;

                $billNo = $item["billno"];
                $playerName = $item["username"];
                $Type = "";
                $GameType = $item["gametype"];
                $gameCode = $item["gmcode"];
                $netAmount = $item["dst_amount"];
                $betTime = $item["billtime"];
                $betAmount = $item["account"];
                $validBetAmount = $item["valid_account"];
                $playType = $item["slottype"];
                $tableCode = $item["slottype"];
                $loginIP = $item["betIP"];
                $recalcuTime = $item["reckontime"];
                $platformType = $item["platformtype"];
                $round = $item["round"] ?? "";
                $VendorId = 0;
                $result = $item["flag"];
                $gameType = addslashes($GameType);
                $gameCode = addslashes($gameCode);

                $web_report_zr = WebReportZr::where("billNo", $billNo)
                    ->where("platformType", $platformType)->first();

                $user = User::where("AG_User", $playerName)->first();

                if (isset($user)) {

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

    public function getXinSlotTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $url = env("TRANSACTION_URL") . "/third-party/ag-transaction/xin-slot";

            $result = get_object_vars(json_decode(GetUrl($url)));

            $result = get_object_vars($result["data"])["row"] ?? array();

            return $result;

            if (is_array($result)) {

                foreach ($result as $item) {

                    $item = get_object_vars($item)["@attributes"];
                    $item = get_object_vars($item);

                    // return $item;

                    $billNo = $item["billno"];
                    $playerName = $item["username"];
                    $Type = "";
                    $GameType = $item["gametype"];
                    $gameCode = $item["gmcode"];
                    $netAmount = $item["dst_amount"];
                    $betTime = $item["billtime"];
                    $betAmount = $item["account"];
                    $validBetAmount = $item["valid_account"];
                    $playType = $item["slottype"];
                    $tableCode = $item["slottype"];
                    $loginIP = $item["betIP"];
                    $recalcuTime = $item["reckontime"];
                    // $platformType = $item["platformtype"];
                    $platformType = "XIN";
                    $round = $item["round"] ?? "";
                    $VendorId = 0;
                    $result = $item["flag"];
                    $gameType = addslashes($GameType);
                    $gameCode = addslashes($gameCode);

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

                    // $AGUtils = new AGUtils($sysConfig);

                    // $user = User::where("UserName", $UserName)->first();

                    // $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                    // User::where("UserName", $UserName)->update([
                    //     "AG_Money" => $balance,
                    // ]);
                }
            } else {

                $item = get_object_vars($result)["@attributes"];
                $item = get_object_vars($item);

                // return $item;

                $billNo = $item["billno"];
                $playerName = $item["username"];
                $Type = "";
                $GameType = $item["gametype"];
                $gameCode = $item["gmcode"];
                $netAmount = $item["dst_amount"];
                $betTime = $item["billtime"];
                $betAmount = $item["account"];
                $validBetAmount = $item["valid_account"];
                $playType = $item["slottype"];
                $tableCode = $item["slottype"];
                $loginIP = $item["betIP"];
                $recalcuTime = $item["reckontime"];
                $platformType = $item["platformtype"];
                $round = $item["round"] ?? "";
                $VendorId = 0;
                $result = $item["flag"];
                $gameType = addslashes($GameType);
                $gameCode = addslashes($gameCode);

                $web_report_zr = WebReportZr::where("billNo", $billNo)
                    ->where("platformType", $platformType)->first();

                $user = User::where("AG_User", $playerName)->first();

                if (isset($user)) {

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
                }
            }

            $response['message'] = "XIN Slot Game Transaction saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getXinTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $url = env("TRANSACTION_URL") . "/third-party/ag-transaction/xin";

            $result = get_object_vars(json_decode(GetUrl($url)));

            $result = get_object_vars($result["data"])["row"] ?? array();

            return $result;

            if (is_array($result)) {

                foreach ($result as $item) {

                    $item = get_object_vars($item)["@attributes"];
                    $item = get_object_vars($item);

                    // return $item;

                    $billNo = $item["billno"];
                    $playerName = $item["username"];
                    $Type = "";
                    $GameType = $item["gametype"];
                    $gameCode = $item["gmcode"];
                    $netAmount = $item["dst_amount"];
                    $betTime = $item["billtime"];
                    $betAmount = $item["account"];
                    $validBetAmount = $item["valid_account"];
                    $playType = $item["slottype"];
                    $tableCode = $item["slottype"];
                    $loginIP = $item["betIP"];
                    $recalcuTime = $item["reckontime"];
                    $platformType = "XIN";
                    $round = $item["round"] ?? "";
                    $VendorId = 0;
                    $result = $item["flag"];
                    $gameType = addslashes($GameType);
                    $gameCode = addslashes($gameCode);

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

                    // $AGUtils = new AGUtils($sysConfig);

                    // $user = User::where("UserName", $UserName)->first();

                    // $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                    // User::where("UserName", $UserName)->update([
                    //     "AG_Money" => $balance,
                    // ]);
                }
            } else {

                $item = get_object_vars($result)["@attributes"];
                $item = get_object_vars($item);

                // return $item;

                $billNo = $item["billno"];
                $playerName = $item["username"];
                $Type = "";
                $GameType = $item["gametype"];
                $gameCode = $item["gmcode"];
                $netAmount = $item["dst_amount"];
                $betTime = $item["billtime"];
                $betAmount = $item["account"];
                $validBetAmount = $item["valid_account"];
                $playType = $item["slottype"];
                $tableCode = $item["slottype"];
                $loginIP = $item["betIP"];
                $recalcuTime = $item["reckontime"];
                $platformType = $item["platformtype"];
                $round = $item["round"] ?? "";
                $VendorId = 0;
                $result = $item["flag"];
                $gameType = addslashes($GameType);
                $gameCode = addslashes($gameCode);

                $web_report_zr = WebReportZr::where("billNo", $billNo)
                    ->where("platformType", $platformType)->first();

                $user = User::where("AG_User", $playerName)->first();

                if (isset($user)) {

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
                }
            }

            $response['message'] = "XIN Game Transaction saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getHunterTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $url = env("TRANSACTION_URL") . "/third-party/ag-transaction/hunter";

            $result = get_object_vars(json_decode(GetUrl($url)));

            $result = get_object_vars($result["data"])["row"] ?? array();

            // return $result;

            if (is_array($result)) {

                foreach ($result as $item) {

                    $item = get_object_vars($item)["@attributes"];
                    $item = get_object_vars($item);

                    // return $item;

                    $tradeNo = $item["billno"];
                    $playerName = $item["username"];
                    $type = $item["fishid"];
                    $GameType = $item["gametype"];
                    $netAmount = $item["dst_amount"];
                    $srcAmount = $item["src_amount"];
                    $betTime = date("Y-m-d H:i:s", $item["billtime"] + 3600);
                    $recalcuTime = date("Y-m-d H:i:s", $item["reckontime"] + 3600);
                    $betAmount = $item["account"];
                    $validBetAmount = $item["cus_account"];
                    $cost = $item["fishcost"];
                    $scene_id = $item["sceneid"];
                    $room_id = $item["roomid"];
                    $room_bet = $item["betx"];
                    $loginIP = $item["betIp"];
                    $platformType = "HUNTER";
                    $jackpotcontribute = "jackpotcontribute";
                    $VendorId = 0;
                    $gameType = addslashes($GameType);

                    $web_report_htr = WebReportHtr::where("tradeNo", $tradeNo)
                        ->where("platformType", $platformType)->first();

                    $user = User::where("AG_User", $playerName)->first();

                    if (!isset($user)) continue;

                    $UserName = $user["UserName"];

                    $new_data = array(
                        "tradeNo" => $tradeNo,
                        "UserName" => $UserName,
                        "playerName" => $playerName,
                        "Type" => $type,
                        "platformType" => $platformType,
                        "sceneId" => $scene_id,
                        "SceneStartTime" => $betTime,
                        "SceneEndTime" => $recalcuTime,
                        "Roomid" => $room_id,
                        "Roombet" => $room_bet,
                        "Cost" => $betAmount,
                        "Earn" => $validBetAmount,
                        "Jackpotcomm" => $jackpotcontribute,
                        "transferAmount" => "",
                        "previousAmount" => $srcAmount,
                        "currentAmount" => $netAmount,
                        "IP" => $loginIP,
                        "VendorId" => $VendorId,
                        "Checked" => 1,
                    );

                    if (!isset($web_report_htr)) {
                        $web_report_htr = new WebReportHtr();
                        $web_report_htr->create($new_data);
                    } else {
                        WebReportHtr::where("tradeNo", $tradeNo)
                            ->where("platformType", $platformType)
                            ->update($new_data);
                    }

                    // $AGUtils = new AGUtils($sysConfig);

                    // $user = User::where("UserName", $UserName)->first();

                    // $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                    // User::where("UserName", $UserName)->update([
                    //     "AG_Money" => $balance,
                    // ]);
                }
            } else {

                $item = get_object_vars($result)["@attributes"];
                $item = get_object_vars($item);

                // return $item;

                $tradeNo = $item["billno"];
                $playerName = $item["username"];
                $type = $item["fishid"];
                $GameType = $item["gametype"];
                $netAmount = $item["dst_amount"];
                $srcAmount = $item["src_amount"];
                $betTime = date("Y-m-d H:i:s", $item["billtime"]);
                $recalcuTime = date("Y-m-d H:i:s", $item["reckontime"]);
                $betAmount = $item["account"];
                $validBetAmount = $item["cus_account"];
                $cost = $item["fishcost"];
                $scene_id = $item["sceneid"];
                $room_id = $item["roomid"];
                $room_bet = $item["betx"];
                $loginIP = $item["betIp"];
                $platformType = "HUNTER";
                $jackpotcontribute = "jackpotcontribute";
                $VendorId = 0;
                $gameType = addslashes($GameType);

                $web_report_htr = WebReportHtr::where("tradeNo", $tradeNo)
                    ->where("platformType", $platformType)->first();

                $user = User::where("AG_User", $playerName)->first();

                if (isset($user)) {

                    $UserName = $user["UserName"];

                    $new_data = array(
                        "tradeNo" => $tradeNo,
                        "UserName" => $UserName,
                        "playerName" => $playerName,
                        "Type" => $type,
                        "platformType" => $platformType,
                        "sceneId" => $scene_id,
                        "SceneStartTime" => $betTime,
                        "SceneEndTime" => $recalcuTime,
                        "Roomid" => $room_id,
                        "Roombet" => $room_bet,
                        "Cost" => $betAmount,
                        "Earn" => $validBetAmount,
                        "Jackpotcomm" => $jackpotcontribute,
                        "transferAmount" => "",
                        "previousAmount" => $srcAmount,
                        "currentAmount" => $netAmount,
                        "IP" => $loginIP,
                        "VendorId" => $VendorId,
                        "Checked" => 1,
                    );

                    if (!isset($web_report_htr)) {
                        $web_report_htr = new WebReportHtr();
                        $web_report_htr->create($new_data);
                    } else {
                        WebReportHtr::where("tradeNo", $tradeNo)
                            ->where("platformType", $platformType)
                            ->update($new_data);
                    }
                }
            }

            $response['message'] = "Hunter Game Transaction saved successfully!";
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
