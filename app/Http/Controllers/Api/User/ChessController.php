<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Dz2;
use App\Utils\KY\kyUtils;
use App\Models\Web\Sys800;
use App\Models\Web\SysConfig;
use App\Models\WebReportKy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ChessController extends Controller
{

    public function getChessGameAll(Request $request)
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

            $result = Dz2::where("PlatformType", "KY")->get();

            foreach ($result as $item) {
                if (!is_file(storage_path("app/public/upload/zr_images/") . $item["ZH_Logo_File"])) {
                    $item["ZH_Logo_File"] = "http://pic.pj6678.com/" . $item["ZH_Logo_File"];
                } else {
                    $item["ZH_Logo_File"] = env('APP_URL') . Storage::url("upload/zr_images/") . $item["ZH_Logo_File"];
                }
            }

            $response["data"] = $result;
            $response['message'] = "Chess Game Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getKYUrl(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "KindID" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $KindID = $request_data["KindID"];

            $user = $request->user();

            if ($KindID == "") {
                $KindID = 0;
            }

            $KY_User = $user["KY_User"];
            $username = $user['UserName'];

            $login_url = "";

            $sysConfig = SysConfig::all()->first();
            $KYUtils = new KYUtils($sysConfig);

            if ($KY_User == null || $KY_User == "") {
                $AG_User = ltrim(trim($sysConfig['AG_User']));
                if (!preg_match("/^[A-Za-z0-9]{4,12}$/", $username)) {
                    $KY_User = $AG_User . '_' . $KYUtils->getpassword_KY(10);
                } else {
                    $KY_User = $AG_User . '_' . trim($user['UserName']) . $KYUtils->getpassword_KY(1);
                }
                $KY_User = strtoupper($KY_User);
                $result = $KYUtils->Add_KY_member($KY_User);
                if ($result == 1) {
                    User::where("UserName", $username)->update(["KY_User" => $KY_User]);
                } else {
                    $response["message"] = '网络异常，请与在线客服联系！';
                    return response()->json($response, $response['status']);
                }
            }

            $login_url = $KYUtils->KY_GameUrl($KY_User, $KindID);

            $response["data"] = $login_url;
            $response['message'] = "Chess Game URL fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getKYTransaction()
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $sysConfig = SysConfig::all()->first();

            $KY_CJ_Time = strtotime($sysConfig["KY_CJ_Time"]);

            $agent = $sysConfig['KY_Agent'];

            $sc = 30 * 60; //采集时长 30分钟内

            $KYUtils = new KYUtils($sysConfig);

            $GameData = $KYUtils->getKYGameRecords($sc);
            $GameData2 = array();

            $error = $GameData['code'];
            $allcount = 0;

            if ($GameData['code'] == 0) {
                $allcount = $GameData['count'];
                for ($i = 0; $i < $allcount; $i++) {
                    unset($GameData2);
                    $GameData2['GameID'] = $GameData['list']['GameID'][$i];
                    $GameData2['Accounts'] = str_replace($agent . '_', '', $GameData['list']['Accounts'][$i]);
                    $GameData2['ServerID'] = $GameData['list']['ServerID'][$i];
                    $GameData2['KindID'] = $GameData['list']['KindID'][$i];
                    $GameData2['TableID'] = $GameData['list']['TableID'][$i];
                    $GameData2['ChairID'] = $GameData['list']['ChairID'][$i];
                    $GameData2['UserCount'] = $GameData['list']['UserCount'][$i];
                    $GameData2['CellScore'] = $GameData['list']['CellScore'][$i];
                    $GameData2['AllBet'] = $GameData['list']['AllBet'][$i];
                    $GameData2['Profit'] = $GameData['list']['Profit'][$i];
                    $GameData2['Revenue'] = $GameData['list']['Revenue'][$i];
                    $GameData2['GameStartTime'] = $GameData['list']['GameStartTime'][$i];
                    $GameData2['GameEndTime'] = $GameData['list']['GameEndTime'][$i];
                    $GameData2['CardValue'] = $GameData['list']['CardValue'][$i];
                    $GameData2['ChannelID'] = $GameData['list']['ChannelID'][$i];
                    $GameData2['LineCode'] = str_replace($agent . '_', '', $GameData['list']['LineCode'][$i]);
                    $game = WebReportKy::where("GameID", $GameData2["GameID"])->first();

                    $user = User::where("KY_User", $GameData2['Accounts'])->first();
                    $UserName = $user["UserName"];
                    if (isset($game)) {
                        WebReportKy::where("GameID", $GameData2["GameID"])->update($GameData2);
                    } else {
                        $web_report_ky = new WebReportKy;
                        $web_report_ky->create($GameData2);

                        User::where("UserName", $UserName)->decrement("withdrawal_condition", $GameData2['AllBet']);
                    }

                    // $balance = $KYUtils->KY_Money2($GameData['list']['Accounts'][$i]);

                    // User::where("UserName", $UserName)->update([
                    //     "KY_Money" => $balance,
                    // ]);
                }
            }

            $current_timestamp = Carbon::now("Asia/Hong_Kong")->timestamp;

            if (($error == 0 and $allcount > 0) or $error == 16) {  //未出错更新采集时间
                $newDateTime = date("Y-m-d H:i:s", $KY_CJ_Time + $sc);
                $newDateTime2 = date("Y-m-d H:i:s", $current_timestamp - $sc);
                if ($newDateTime > $newDateTime2) $newDateTime = $newDateTime2;
                SysConfig::query()->update([
                    "KY_CJ_Time" => $newDateTime
                ]);
            } else {
                $response["message"] = "刷新太快";
                $response['success'] = FALSE;
                $response['status'] = STATUS_OK;
                return response()->json($response, $response['status']);
            }

            $response['message'] = "Chess Game Transaction fetched successfully!";
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
