<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Dz2;
use App\Utils\BBIN\bbinUtils;
use App\Utils\FTP\ftpUtils;
use App\Models\Web\SysConfig;
use App\Models\User;
use App\Models\WebReportZr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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

class BBINController extends Controller
{

    public function getBBINGameAll(Request $request)
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

            $PlatformType = 'BBIN';

            $result = Dz2::where("Open", 1)
                ->where("PlatformType", $PlatformType)
                ->get();

            foreach ($result as $item) {
                if (!is_file(storage_path("app/public/upload/zr_images/").$item["ZH_Logo_File"])) {
                    $item["ZH_Logo_File"] = "http://pic.pj6678.com/".$item["ZH_Logo_File"];
                } else {
                    $item["ZH_Logo_File"] = env('APP_URL').Storage::url("upload/zr_images/").$item["ZH_Logo_File"];
                }
            }

            $response["data"] = $result;
            $response['message'] = "BBIN Game Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getBBINUrl(Request $request)
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

            $BBIN_username = $user["BBIN_User"];
            $BBIN_password = $user["BBIN_Pass"];
            $username = $user['UserName'];
            $tp = $user['BBIN_Type'];

            $sysConfig = SysConfig::all()->first();

            $login_url = "";

            $BBINUtils = new BBINUtils($sysConfig);

            if ($BBIN_username == null || $BBIN_username == "") {
                $WebCode = ltrim(trim(env('AG_USER')));
                if (!preg_match("/^[A-Za-z0-9]{4,12}$/", $user['UserName'])) {
                    $BBIN_username = $BBINUtils->getpassword_bbin(10);
                } else {
                    $BBIN_username = trim($user['UserName']) . $BBINUtils->getpassword_bbin(1);
                }
                $BBIN_username = 'h07' . $WebCode . $BBIN_username;
                $BBIN_username = strtolower($BBIN_username);
                $BBIN_password = strtolower($BBINUtils->getpassword_bbin(10));
                $result = $BBINUtils->Addmember_BBIN($BBIN_username, $BBIN_password, 1);
                // return $result;
                if ($result['info'] == '0') {
                    User::where("UserName", $username)->update([
                        "BBIN_User" => $BBIN_username,
                        "BBIN_Pass" => $BBIN_password,
                    ]);
                } else {
                    $response["message"] = '网络异常，请与在线客服联系！';
                    return response()->json($response, $response['status']);
                }
            }

            $login_url = $BBINUtils->getGameUrl_BBIN($BBIN_username, $BBIN_password, $tp, $_SERVER['HTTP_HOST'], 1, $game_type);

            $response["data"] = $login_url;
            $response['message'] = "BBIN Game URL fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getFTPBBINTransaction()
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

                $file_path = "BBIN/" . $date . "/" . $file_name . ".xml";

                // return $file_path;

                $fileExists = Storage::disk('ftp')->exists($file_path);

                // $fileExists = Storage::disk('ftp')->exists("BBIN/20230728/202307280412.xml");

                if ($fileExists) {

                    $xmlContents = Storage::disk('ftp')->get($file_path);

                    // $xmlContents = Storage::disk('ftp')->get("BBIN/20230728/202307280412.xml");
    
                    $xml_array = explode("\r\n", $xmlContents);
    
                    $xml_array = array_filter($xml_array);

                    // return $xml_array;
    
                    foreach($xml_array as $xml) {
        
                        $result = simplexml_load_string($xml);
            
                        $row = get_object_vars($result);
    
                        $row = $row["@attributes"];

                        $data_type = $row["dataType"];

                        if ($data_type != "BR") continue;

                        $billNo = $row["billNo"];
                        $playerName = $row["playerName"];
                        $GameType = $row["gameType"];
                        $gameCode = $row["gameCode"] == "null" ? "" : $row["gameCode"];
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
    
                        $user = User::where("BBIN_User", $playerName)->first();
    
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

                            User::where("UserName", $UserName)->decrement("withdrawal_condition", (int)$betAmount);
                        } else {
                            WebReportZr::where("billNo", $billNo)
                                ->where("platformType", $platformType)
                                ->update($new_data);
                        }

                        // $sysConfig = SysConfig::all()->first();
            
            
                        // $BBINUtils = new BBINUtils($sysConfig);
            
                        // $user = User::where("UserName", $UserName)->first();
            
                        // $balance = $BBINUtils->getMoney_BBIN($user["BBIN_User"], $user["BBIN_Pass"]);
            
                        // User::where("UserName", $UserName)->update([
                        //     "BBIN_Money" => $balance,
                        // ]);
    
                    }
                }
            }
            $response['message'] = "BBIN Game Transaction saved successfully!";
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
