<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\WebSystemData;
use App\Models\WebMarqueeData;
use App\Models\SysConfig;
use App\Models\Config;
use App\Models\User;
use Carbon\Carbon;

class AdminSystemController extends Controller
{

    public function getSystemAll(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "lv" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $lv = $request_data["lv"];

            $role = "";

            switch ($lv){
                case 'M':
                    $role='Admin';
                    break;  
                case 'A':
                    $role='Super';
                    break;
                case 'B':
                    $role='Corprator';
                    break;
                case 'C':
                    $role='World';
                    break;
                case 'D':
                    $role='Agents';
                    break;
            }

            $name = "admin";

            $user_count = User::where($role, $name)->where("Pay_Type", 1)->count();

            if ($user_count == 0) {
                $response["message"] = "目前还没有会员，请添加后再操作!!";
                return response()->json($response, $response['status']);
            }

            $sysConfig = SysConfig::all()->first();

            $config = Config::all()->first();

            $web_system_data = WebSystemData::find(1);

            $response["web_system_data"] = $web_system_data;
            $response["sys_config"] = $sysConfig;
            $response["config"] = $config;
            $response['message'] = "System Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateSystemUrl(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $PCURL = $request_data["PCURL"] ?? "";
            $WAPURL = $request_data["WAPURL"] ?? "";
            $BadMember = $request_data["BadMember"] ?? "";
            $BadMember2 = $request_data["BadMember2"] ?? "";
            $BadMember_JQ = $request_data["BadMember_JQ"] ?? "";
            $kf1 = $request_data["kf1"] ?? "";
            $kf2 = $request_data["kf2"] ?? "";
            $kf3 = $request_data["kf3"] ?? "";
            $kf4 = $request_data["kf4"] ?? "";

            if ($PCURL != "") {
                $config = Config::where("id", 1)->update([
                    "PCURL" => $PCURL,
                ]);
            }

            if ($WAPURL != "") {
                $config = Config::where("id", 1)->update([
                    "WAPURL" => $WAPURL,
                ]);                
            }

            if ($BadMember != "") {
                $config = Config::where("id", 1)->update([
                    "BadMember" => $BadMember,
                ]);                
            }

            if ($BadMember2 != "") {
                $config = Config::where("id", 1)->update([
                    "BadMember2" => $BadMember2,
                ]);                
            }

            if ($BadMember_JQ != "") {
                $config = Config::where("id", 1)->update([
                    "BadMember_JQ" => $BadMember_JQ,
                ]);                
            }

            if ($kf1 != "") {
                $config = Config::where("id", 1)->update([
                    "kf1" => $kf1,
                ]);                
            }

            if ($kf2 != "") {
                $config = Config::where("id", 1)->update([
                    "kf2" => $kf2,
                ]);                
            }

            if ($kf3 != "") {
                $config = Config::where("id", 1)->update([
                    "kf3" => $kf3,
                ]);                
            }

            if ($kf4 != "") {
                $config = Config::where("id", 1)->update([
                    "kf4" => $kf4,
                ]);                
            }


            $response['message'] = "System Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateTurnService(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $isReg = $request_data["isReg"] ?? "";
            $AG_Repair = $request_data["AG_Repair"] ?? "";
            $BBIN_Repair = $request_data["BBIN_Repair"] ?? "";
            $MG_Repair = $request_data["MG_Repair"] ?? "";
            $PT_Repair = $request_data["PT_Repair"] ?? "";
            $OG_Repair = $request_data["OG_Repair"] ?? "";
            $KY_Repair = $request_data["KY_Repair"] ?? "";

            $web_name_wap = $request_data["web_name_wap"] ?? "";
            $web_title_wap = $request_data["web_title_wap"] ?? "";
            $web_gonggao_wap = $request_data["web_gonggao_wap"] ?? "";
            $web_popmsg_wap = $request_data["web_popmsg_wap"] ?? "";
            $web_description_wap = $request_data["web_description_wap"] ?? "";
            $web_keywords_wap = $request_data["web_keywords_wap"] ?? "";
            $web_author_wap = $request_data["web_author_wap"] ?? "";
            $web_banner_wap = $request_data["web_banner_wap"] ?? "";
            $web_slider_time_wap = $request_data["web_slider_time_wap"] ?? "";
            $web_refreshtime_wap = $request_data["web_refreshtime_wap"] ?? "";

            if ($isReg != "") {
                SysConfig::where("id", 1)->update([
                    "isReg" => $isReg,
                ]);
            }

            if ($AG_Repair != "") {
                SysConfig::where("id", 1)->update([
                    "AG_Repair" => $AG_Repair,
                ]);
            }

            if ($BBIN_Repair != "") {
                SysConfig::where("id", 1)->update([
                    "BBIN_Repair" => $BBIN_Repair,
                ]);
            }

            if ($MG_Repair != "") {
                SysConfig::where("id", 1)->update([
                    "MG_Repair" => $MG_Repair,
                ]);
            }

            if ($PT_Repair != "") {
                SysConfig::where("id", 1)->update([
                    "PT_Repair" => $PT_Repair,
                ]);
            }

            if ($OG_Repair != "") {
                SysConfig::where("id", 1)->update([
                    "OG_Repair" => $OG_Repair,
                ]);
            }

            if ($KY_Repair != "") {
                SysConfig::where("id", 1)->update([
                    "KY_Repair" => $KY_Repair,
                ]);
            }

            if ($web_name_wap != "") {
                SysConfig::where("id", 1)->update([
                    "web_name_wap" => $web_name_wap,
                ]);
            }

            if ($web_title_wap != "") {
                SysConfig::where("id", 1)->update([
                    "web_title_wap" => $web_title_wap,
                ]);
            }

            if ($web_gonggao_wap != "") {
                SysConfig::where("id", 1)->update([
                    "web_gonggao_wap" => $web_gonggao_wap,
                ]);
            }

            if ($web_popmsg_wap != "") {
                SysConfig::where("id", 1)->update([
                    "web_popmsg_wap" => $web_popmsg_wap,
                ]);
            }

            if ($web_description_wap != "") {
                SysConfig::where("id", 1)->update([
                    "web_description_wap" => $web_description_wap,
                ]);
            }

            if ($web_keywords_wap != "") {
                SysConfig::where("id", 1)->update([
                    "web_keywords_wap" => $web_keywords_wap,
                ]);
            }

            if ($web_author_wap != "") {
                SysConfig::where("id", 1)->update([
                    "web_author_wap" => $web_author_wap,
                ]);
            }

            if ($web_banner_wap != "") {
                SysConfig::where("id", 1)->update([
                    "web_banner_wap" => $web_banner_wap,
                ]);
            }

            if ($web_slider_time_wap != "") {
                SysConfig::where("id", 1)->update([
                    "web_slider_time_wap" => $web_slider_time_wap,
                ]);
            }

            if ($web_refreshtime_wap != "") {
                SysConfig::where("id", 1)->update([
                    "web_refreshtime_wap" => $web_refreshtime_wap,
                ]);
            }

            $response['message'] = "System Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateNotification(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();

            $request_data = $request->all();
            $GongGao = $request_data["GongGao"] ?? "";
            $systimee = $request_data["systimee"] ?? "";
            $systime = $request_data["systime"] ?? "";
            $website = $request_data["website"] ?? "";
            $BadArea = $request_data["BadArea"] ?? "";

            if ($GongGao != "") {
                WebSystemData::where("id", 1)->update([
                    "GongGao" => $GongGao,
                ]);
            }

            if ($systimee != "") {
                WebSystemData::where("id", 1)->update([
                    "systimee" => $systimee,
                ]);
            }

            if ($systime != "") {
                WebSystemData::where("id", 1)->update([
                    "systime" => $systime,
                ]);
            }

            if ($website != "") {
                WebSystemData::where("id", 1)->update([
                    "website" => $website,
                ]);
            }

            if ($BadArea != "") {
                WebSystemData::where("id", 1)->update([
                    "BadArea" => $BadArea,
                ]);
            }

            $response['message'] = "System Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSystemNotice(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "level" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();

            $level = $request_data["level"];
            $date_start = $request_data["date_start"] ?? Carbon::now("Asia/Hong_Kong")->format("Y-m-d");

            $web_marquee_data = WebMarqueeData::where("Level", $level)->where("Date", $date_start)->get();

            $response["system_notice"] = $user;
            $response["web_marquee_data"] = $web_marquee_data;
            $response['message'] = "System Notice Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addSystemNotice(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "Msg_System" => "required|string",
                "Msg_System_tw" => "required|string",
                "Msg_System_en" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $Msg_System=$request_data['Msg_System'];
            $Msg_System_tw=$request_data['Msg_System_tw'];
            $Msg_System_en=$request_data['Msg_System_en'];
            $date=Carbon::now("Asia/Hong_Kong")->format("Y-m-d");
            $time=$request_data['ntime'];   
            $m=$request_data['member'];
            $d=$request_data['agents'];
            $c=$request_data['world'];
            $b=$request_data['corprator'];
            $a=$request_data['super'];

            WebSystemData::query()->update([
                "Msg_System_tw" => $Msg_System_tw,
                "Msg_System_en" => $Msg_System_en,
                "Msg_System" => $Msg_System,
            ]);

            if ($m==1){

                $new_data = array(
                    "Level" => 'MEM',
                    "Message" => $Msg_System,
                    "Message_tw" => $Msg_System_tw,
                    "Message_en" => $Msg_System_en,
                    "Time" => $time,
                    "Date" => $date,
                    "Admin" => $user["UserName"],
                );

                $web_marquee_data = new WebMarqueeData;

                $web_marquee_data->create($new_data);
            }
            if ($d==1){

                $new_data = array(
                    "Level" => 'D',
                    "Message" => $Msg_System,
                    "Message_tw" => $Msg_System_tw,
                    "Message_en" => $Msg_System_en,
                    "Time" => $time,
                    "Date" => $date,
                    "Admin" => $user["UserName"],
                );

                $web_marquee_data = new WebMarqueeData;

                $web_marquee_data->create($new_data);
            }
            if ($c==1){

                $new_data = array(
                    "Level" => 'C',
                    "Message" => $Msg_System,
                    "Message_tw" => $Msg_System_tw,
                    "Message_en" => $Msg_System_en,
                    "Time" => $time,
                    "Date" => $date,
                    "Admin" => $user["UserName"],
                );

                $web_marquee_data = new WebMarqueeData;

                $web_marquee_data->create($new_data);
            }
            if ($b==1){

                $new_data = array(
                    "Level" => 'B',
                    "Message" => $Msg_System,
                    "Message_tw" => $Msg_System_tw,
                    "Message_en" => $Msg_System_en,
                    "Time" => $time,
                    "Date" => $date,
                    "Admin" => $user["UserName"],
                );

                $web_marquee_data = new WebMarqueeData;

                $web_marquee_data->create($new_data);
            }
            if ($a==1){

                $new_data = array(
                    "Level" => 'A',
                    "Message" => $Msg_System,
                    "Message_tw" => $Msg_System_tw,
                    "Message_en" => $Msg_System_en,
                    "Time" => $time,
                    "Date" => $date,
                    "Admin" => $user["UserName"],
                );

                $web_marquee_data = new WebMarqueeData;

                $web_marquee_data->create($new_data);
            }

            $response['message'] = "System Notice Data added successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateSystemNotice(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "ID" => "required|numeric",
                "Message" => "required|string",
                "Message_tw" => "required|string",
                "Message_en" => "required|string",
                "Date" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $ID = $request_data["ID"];
            $Message=$request_data['Message'];
            $Message_tw=$request_data['Message_tw'];
            $Message_en=$request_data['Message_en'];
            $Date=$request_data["Date"];

            WebMarqueeData::where("ID", $ID)->update([
                "Message" => $Message,
                "Message_tw" => $Message_tw,
                "Message_en" => $Message_en,
                "Date" => $Date,
            ]);

            $response['message'] = "System Notice Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteSystemNotice(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "ID" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $ID = $request_data["ID"];

            WebMarqueeData::where("ID", $ID)->delete();

            $response['message'] = "System Notice Data deleted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getAdminInfo(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $user = $request->user();

            $response["data"] = $user;
            $response['message'] = "Admin Info Data deleted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateAdminInfo(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "old_password" => "required|string",
                "new_password" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $old_password = $request_data["old_password"];
            $new_password = $request_data["new_password"];
            $confirm_password = $request_data["confirm_password"] ?? "";

            if (Hash::check($old_password, $user->password)) {
                $new_password = Hash::make($new_password);
                WebSystemData::where("id", $user["id"])->update([
                    "password" => $new_password
                ]);
            } else {
                $response["message"] = "Old Password Incorrect!";

                return response()->json($response, $response['status']);
            }

            $response['message'] = "Admin Info Data  updated successfully!";
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
