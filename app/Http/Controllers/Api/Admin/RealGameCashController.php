<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Utils\AG\agUtils;
use App\Utils\BBIN\bbinUtils;
use App\Utils\MG\mgUtils;
use App\Utils\PT\ptUtils;
use App\Utils\OG\ogUtils;
use App\Utils\KY\kyUtils;
use App\Models\Web\SysConfig;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Web\WebMemLogData;
use App\Utils\Utils;

class RealGameCashController extends Controller
{

    public function getAGMoney(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "user_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $login_user = $request->user();
            $loginname = $login_user["UserName"];

            $sysConfig = SysConfig::all()->first();

            $AGUtils = new AGUtils($sysConfig);

            $user_name = $request_data["user_name"];

            $user = User::where("UserName", $user_name)->first(["id", "AG_User", "AG_Pass"]);

            if (isset($user)) {

                $balance = $AGUtils->getMoney($user["AG_User"], $user["AG_Pass"]);

                User::where("UserName", $user_name)->update([
                    "AG_Money" => $balance,
                ]);
            }

            $login_info = 'AG资金更新';

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";

            $web_mem_log_data->save();

            $response['message'] = "AG Money updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getBBINMoney(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "user_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $login_user = $request->user();
            $loginname = $login_user["UserName"];

            $sysConfig = SysConfig::all()->first();

            $BBINUtils = new BBINUtils($sysConfig);

            $user_name = $request_data["user_name"];

            $user = User::where("UserName", $user_name)->first(["id", "BBIN_User", "BBIN_Pass"]);

            if (isset($user)) {

                $balance = $BBINUtils->getMoney_BBIN($user["BBIN_User"], $user["BBIN_Pass"]);

                User::where("UserName", $user_name)->update([
                    "BBIN_Money" => $balance,
                ]);
            }

            $login_info = 'BBIN资金更新';

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";

            $web_mem_log_data->save();

            $response['message'] = "BBIN Money updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getOGMoney(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "user_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $login_user = $request->user();
            $loginname = $login_user["UserName"];

            $sysConfig = SysConfig::all()->first();

            $OGUtils = new OGUtils($sysConfig);

            $user_name = $request_data["user_name"];

            $user = User::where("UserName", $user_name)->first(["id", "OG_User"]);

            if (isset($user)) {

                $balance = $OGUtils->OG_Money($user["OG_User"]);

                User::where("UserName", $user_name)->update([
                    "OG_Money" => $balance,
                ]);
            }

            $login_info = 'OG资金更新';

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";

            $web_mem_log_data->save();

            $response['message'] = "OG Money updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMGMoney(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "user_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $login_user = $request->user();
            $loginname = $login_user["UserName"];

            $sysConfig = SysConfig::all()->first();

            $MGUtils = new MGUtils($sysConfig);

            $user_name = $request_data["user_name"];

            $user = User::where("UserName", $user_name)->first(["id", "MG_User", "MG_Pass"]);

            if (isset($user)) {

                $balance = $MGUtils->getMoney_MG($user["MG_User"], $user["MG_Pass"]);

                User::where("UserName", $user_name)->update([
                    "MG_Money" => $balance,
                ]);
            }

            $login_info = 'MG资金更新';

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";

            $web_mem_log_data->save();

            $response['message'] = "MG Money updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getPTMoney(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "user_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $login_user = $request->user();
            $loginname = $login_user["UserName"];

            $sysConfig = SysConfig::all()->first();

            $PTUtils = new PTUtils($sysConfig);

            $user_name = $request_data["user_name"];

            $user = User::where("UserName", $user_name)->first(["id", "PT_User", "PT_Pass"]);

            if (isset($user)) {

                $balance = $PTUtils->getMoney_PT($user["PT_User"], $user["PT_Pass"]);

                User::where("UserName", $user_name)->update([
                    "PT_Money" => $balance,
                ]);
            }

            $login_info = 'PT资金更新';

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";

            $web_mem_log_data->save();

            $response['message'] = "PT Money updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getKYMoney(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "user_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $login_user = $request->user();
            $loginname = $login_user["UserName"];

            $sysConfig = SysConfig::all()->first();

            $KYUtils = new KYUtils($sysConfig);

            $user_name = $request_data["user_name"];

            $user = User::where("UserName", $user_name)->first(["id", "KY_User"]);

            if (isset($user)) {

                $balance = $KYUtils->KY_Money2($user["KY_User"]);

                User::where("UserName", $user_name)->update([
                    "KY_Money" => $balance,
                ]);
            }

            $login_info = 'KY资金更新';

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";

            $web_mem_log_data->save();

            $response['message'] = "KY Money updated successfully!";
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
