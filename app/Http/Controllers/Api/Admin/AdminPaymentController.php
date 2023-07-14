<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\WebSystemData;
use App\Models\User;
use App\Models\Web\Sys800;
use App\Utils\Utils;
use App\Models\Web\MoneyLog;
use App\Models\Web\SysConfig;
use App\Models\WebPaymentData;
use App\Models\Web\WebMemLogData;

class AdminPaymentController extends Controller
{
    public function getCashSystem(Request $request) {

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

            $current_date = Carbon::now('Asia/Hong_Kong')->format('Y-m-d');

            $memname = $request_data["name"] ?? "";
            $s_date = $request_data["s_date"] ?? $current_date;
            $e_date = $request_data["e_date"] ?? $current_date;
            $rtype = $request_data["type"] ?? "";
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $lv = $request_data["lv"];

            $web_system_data = WebSystemData::where("ID", 1)->first();

            $admin_url = explode(";", $web_system_data["Admin_Url"]);

            // if (in_array($_SERVER['HTTP_HOST'], $admin_url){
            //    $web='web_system_data';
            // } else {
            //    $web='web_agents_data';
            // }

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

            $result = Sys800::join("web_member_data", "web_sys800_data.UserName", "=", "web_member_data.UserName")->where("web_sys800_data.Admin", "admin")->whereBetween("web_sys800_data.AddDate", [$s_date, $e_date]);

            if ($memname != "") {
                $result = $result->where("web_sys800_data.UserName", $memname);
            }

            if ($rtype == 'S' || $rtype == "T") {
                $result = $result->where("web_sys800_data.Type", $rtype)->where("web_sys800_data.Type2", 1);
            } else if ($rtype == "") {
                $result = $result->where("web_sys800_data.Type2", "!=", 3);
            } else if($rtype = "F") {
                $result = $result->where("web_sys800_data.Type2", 2);
            } else if($rtype == "Q") {
                $result = $result->where("web_sys800_data.Cancel", 1);
            } else if($rtype = "C") {
                $result = $result->where("web_sys800_data.Checked", 0);
            }

            $total_count = $result->count();

            $result = $result->select(DB::raw("web_sys800_data.*, web_member_data.Money"))
                ->offset(($page_no - 1) * $limit)
                ->take($limit)->orderBy("ID", "desc")->get();

            foreach($result as $item) {
                if ($item->Type == "S") {
                    $item->status = "存入";
                    $item->type = "存入";
                } else if ($item->Type == "T") {
                    $item->status = "提出";
                    $item->type = "提出";
                }

                if ($item->Cancel == 1) {
                    $item->status = "<font color='red'>".$item->status."已拒绝</font>";
                }

                if ($item->Type2 == 1) {
                    $item->Type2 = "正常";
                } else if ($item->Type2 == 2) {
                    $item->Type2 = "赠送";
                } else if ($item->Type2 == 3) {
                    $item->Type2 = "转换";
                }

                $item->Alias = Utils::GetField($item->UserName, "Alias");

                if (strtoupper($item->Bank_Address) == 'USDT' && $item->Type == 'T') {
                    $sys_config = SysConfig::query()->first(["USDT"]);
                    if (!isset($sys_config)) {
                        $sys_config["USDT"] = 1.0000;
                    }
                    $usdt_number = abs($item->Gold) / $sys_config["USDT"];
                    $item->BankInfo = "USDT汇率:".$sys_config["USDT"]."<br>".$item->Bank_Account."<br>".number_format($usdt_number,2);
                } else {
                    if(strtoupper($item->Bank)=='USDT' && $item->Type == 'T') {
                        $item->BankInfo = $item->Bank_Address."<br>".$item->Bank_Account;
                    }else{
                        $item->BankInfo = $item->Bank."<br>".$item->Bank_Address."<br>".$item->Bank_Account;
                    }                    
                }
            }

            $response["data"] = $result;
            $response["total_count"] = $total_count;
            $response['message'] = "Cash Data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function reviewCash(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user = $request->user();

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $id = $request_data["id"];

            $sys_800 = Sys800::where("ID", $id)->first();

            $gold = $sys_800["Gold"];

            if ($sys_800["Checked"] != 0) {
                $response["message"] = "此定单已经审核";
                return response()->json($response, $response['status']);
            }

            if ($sys_800["Type"] == "S") {
                $username = $sys_800["UserName"];
                $previousAmount = Utils::GetField($username,'Money');
                $user_id = Utils::GetField($username, "id");
                Utils::ProcessUpdate($username);

                $currentAmount = $previousAmount + (int)$gold;

                $q1 = User::where("UserName", $username)
                    ->update([
                        'Money' => $currentAmount,
                        'Credit' => $currentAmount,
                    ]);

                if($q1 == 1) {

                    $currentAmount = Utils::GetField($username,'Money');

                    Sys800::where("ID", $id)->update([
                        "Payway" => "B",
                        "Checked" => 1,
                        "Music" => 1,
                        "User" => $user["UserName"],
                        "Date" => $current_time,
                        "previousAmount" => $previousAmount,
                        "currentAmount" => $currentAmount,                        
                    ]);

                    $new_log = new MoneyLog;
                    $new_log->user_id = $user_id;
                    $new_log->order_num = $sys_800["Order_Code"];
                    $new_log->about = $user["UserName"]."审核存款";
                    $new_log->update_time = $current_time;
                    $new_log->type = "存款";
                    $new_log->order_value = $gold;
                    $new_log->assets = $previousAmount;
                    $new_log->balance = $currentAmount;
                    $new_log->save();

                } else {

                    $currentAmount = $previousAmount - (int)$gold;
                
                    User::where("UserName", $username)
                        ->update([
                            'Money' => (int)$currentAmount,
                            'Credit' => (int)$currentAmount,
                        ]);
                }
            } else {
                $username = $sys_800["UserName"];
                $previousAmount = Utils::GetField($username,'Money');
                $user_id = Utils::GetField($username, "id");
                Utils::ProcessUpdate($username);

                $currentAmount = $previousAmount - (int)$gold;

                $q1 = User::where("UserName", $username)
                    ->update([
                        'Money' => (int)$currentAmount,
                        'Credit' => (int)$currentAmount,
                    ]);

                if($q1 == 1) {

                    $currentAmount = Utils::GetField($username,'Money');

                    Sys800::where("ID", $id)->update([
                        "Checked" => 1,
                        "Music" => 1,
                        "User" => $user["UserName"],
                        "Date" => $current_time,
                        "previousAmount" => $previousAmount,
                        "currentAmount" => $currentAmount, 
                    ]);

                    $new_log = new MoneyLog;
                    $new_log->user_id = $user_id;
                    $new_log->order_num = $sys_800["Order_Code"];
                    $new_log->about = $user["UserName"]."审核提款";
                    $new_log->update_time = $current_time;
                    $new_log->type = "提款";
                    $new_log->order_value = -$gold;
                    $new_log->assets = $previousAmount;
                    $new_log->balance = $currentAmount;
                    $new_log->save();

                } else {

                    $currentAmount = $previousAmount + (int)$gold;
                
                    User::where("UserName", $username)
                        ->incrementEach([
                            'Money' => (int)$currentAmount,
                            'Credit' => (int)$currentAmount,
                        ]);
                }

            }

            $user = User::where("UserName", $username)->first(["Money"]);

            $currentAmount = $user["Money"];

            $response["data"] = $currentAmount;
            $response['message'] = "Cash Data reviewed successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function rejectCash(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user = $request->user();

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $id = $request_data["id"];
            $cancel_result = $request_data["cancelMsg"] ?? "";

            $sys_800 = Sys800::where("ID", $id)->first();

            $gold = $sys_800["Gold"];

            if ($sys_800["Cancel"] == 1) {
                $response["message"] = "这笔金额已经拒绝！";
                return response()->json($response, $response['status']);
            }

            if ($sys_800["Type"] == "T" && $sys_800["Checked"] == 0) {
                $username = $sys_800["UserName"];
                $previousAmount = Utils::GetField($username,'Money');
                $user_id = Utils::GetField($username, "id");
                Utils::ProcessUpdate($username);

                $currentAmount = $previousAmount + (int)$gold;

                $q1 = User::where("UserName", $username)
                    ->update([
                        'Money' => $currentAmount,
                        'Credit' => $currentAmount,
                    ]);

                if($q1 == 1) {

                    $currentAmount = Utils::GetField($username,'Money');

                    Sys800::where("ID", $id)->update([
                        "Payway" => "B",
                        "Checked" => 1,
                        "Cancel" => 1,
                        "Music" => 1,
                        "User" => $user["UserName"],
                        "Date" => $current_time,
                        "Notes" => $cancel_result                       
                    ]);

                    $new_log = new MoneyLog;
                    $new_log->user_id = $user_id;
                    $new_log->order_num = $sys_800["Order_Code"];
                    $new_log->about = $user["UserName"]."审核提款<br>ID:".$id;
                    $new_log->update_time = $current_time;
                    $new_log->type = $user["UserName"]."审核提款";
                    $new_log->order_value = $gold;
                    $new_log->assets = $previousAmount;
                    $new_log->balance = $currentAmount;
                    $new_log->save();

                } else {

                    $currentAmount = $previousAmount - (int)$gold;
                
                    User::where("UserName", $username)
                        ->update([
                            'Money' => (int)$currentAmount,
                            'Credit' => (int)$currentAmount,
                        ]);
                }
            } else {
                $username = $sys_800["UserName"];
                $previousAmount = Utils::GetField($username,'Money');
                $currentAmount = $previousAmount;

                Sys800::where("ID", $id)->update([
                    "Checked" => 1,
                    "Cancel" => 1,
                    "Music" => 1,
                    "User" => $user["UserName"],
                    "Date" => $current_time,
                    "previousAmount" => $previousAmount,
                    "currentAmount" => $currentAmount,
                    "Notes" => $cancel_result
                ]);


            }

            $user = User::where("UserName", $username)->first(["Money"]);

            $currentAmount = $user["Money"];

            $response["data"] = $currentAmount;
            $response['message'] = "Cash Data rejected successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteCash(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];

            Sys800::where("ID", $id)->delete();

            $response['message'] = "Cash Data deleted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveCash(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "name" => "required|string",
                "type" => "required|string",
                "gold" => "required|numeric",
                "memo" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $name = $request_data["name"];
            $type = $request_data["type"];
            $gold = $request_data["gold"];
            $memo = $request_data["memo"];

            $login_user = $request->user();

            $loginname = $login_user["UserName"];

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $user = User::where("UserName", $name)->first();

            if (!isset($user)) {
                $response["message"] = "查无此会员(".$name.")!";
                return response()->json($response, $response['status']);
            }

            $agents=$user['Agents'];
            $world=$user['World'];
            $corprator=$user['Corprator'];
            $super=$user['Super'];
            $admin=$user['Admin'];
            $alias=$user['Alias'];
            $username = $name;

            if ($type == "T") {
                $assets = Utils::GetField($name, "Money");
                $balance = $assets - $gold;
                $user_id = Utils::GetField($name, "id");
                $Order_Code = 'TK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                $sys_800 = new Sys800;
                $data = array(
                    "Payway" => "W",
                    "Gold" => $gold,
                    "previousAmount" => $assets,
                    "currentAmount" => $balance,
                    "AddDate" => $current_time,
                    "Type" => $type,
                    "UserName" => $name,
                    "Agents" => $agents,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => "RMB",
                    "Name" => $alias,
                    "User" => $login_user["UserName"],
                    // "Checked" => 1,
                    // "Date" => $current_time,
                    "Order_Code" => $Order_Code,
                    "Notes" => $memo,
                );

                $sys_800->create($data);
            
                // $q1 = User::where("UserName", $username)
                //     ->update([
                //         'Money' => (int)$balance,
                //         'Credit' => (int)$balance,
                //     ]);

                // if ($q1 == 1) {

                //     $new_log = new MoneyLog;
                //     $new_log->user_id = $user_id;
                //     $new_log->order_num = $Order_Code;
                //     $new_log->about = $user["UserName"]."后台扣款";
                //     $new_log->update_time = $current_time;
                //     $new_log->type = "扣款";
                //     $new_log->order_value = $gold;
                //     $new_log->assets = $assets;
                //     $new_log->balance = $balance;
                //     $new_log->save();

                // }

            } else {

                $user_id = Utils::GetField($name, "id");
                $Order_Code = 'TK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);

                $Type2 = 0;
                if($memo=='彩金' or $memo=='返水' or $type=='ZS'){
                    $Type2=2;
                    $type='S';
                }else{
                    $Type2=1;
                }
                $sys_800 = new Sys800;

                $data = array(
                    "Payway" => "W",
                    "Gold" => $gold,
                    "AddDate" => $current_time,
                    "Type" => $type,
                    "Type2" => $Type2,
                    "UserName" => $name,
                    "Agents" => $agents,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => "RMB",
                    "Name" => $alias,
                    "User" => $login_user["UserName"],
                    // "Date" => $current_time,
                    "Order_Code" => $Order_Code,
                    "Notes" => $memo,
                );

                $sys_800->create($data);

            }

            $level = $login_user["Level"];

            $lv = "M";

            switch ($level){
                case 'M':
                    $lv='管理员';
                    break;
                case 'A':
                    $lv='公司';
                    break;
                case 'B':
                    $lv='股东';
                    break;
                case 'C':
                    $lv='总代理';
                    break;
                case 'D':
                    $lv='代理商';
                    break;
            }

            $ip_addr = Utils::get_ip();
            $browser_ip = Utils::get_browser_ip();
            $loginfo='执行批量充值';

            $web_mem_log_data = new WebMemLogData;
            $web_mem_log_data->create([
                "UserName" => $loginname,
                "LoginIP" => $ip_addr,
                "LoginTime" => now(),
                "ConText" => $loginfo,
                "Url" => $browser_ip,
                "Level" => $lv,
            ]);

            $user = User::where("UserName", $username)->first(["Money"]);

            $currentAmount = $user["Money"];

            $response["data"] = $currentAmount;
            $response['message'] = "Cash Data saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveBulkCash(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "names" => "required|string",
                "gold" => "required|numeric",
                "memo" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $names = $request_data["names"];
            $gold = $request_data["gold"];
            $memo = $request_data["memo"];

            $names=str_replace("\r","|",$names);
            $names=str_replace("\n","|",$names);
            $names=str_replace("||","|",$names);
            $names=str_replace("||","|",$names);

            $name_array = explode("|", $names);

            $login_user = $request->user();

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            foreach($name_array as $name) {

                $user = User::where("UserName", $name)->first();

                if (!isset($user)) {
                    $response["message"] = "查无此会员(".$name.")!";
                    return response()->json($response, $response['status']);
                }

                $agents=$user['Agents'];
                $world=$user['World'];
                $corprator=$user['Corprator'];
                $super=$user['Super'];
                $admin=$user['Admin'];
                $alias=$user['Alias'];
                $username = $name;
                $assets = Utils::GetField($name, "Money");
                $balance = $assets + $gold;
                $user_id = Utils::GetField($name, "id");
                $Order_Code = 'CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                $sys_800 = new Sys800;
                $data = array(
                    "Payway" => "W",
                    "Gold" => $gold,
                    "previousAmount" => $assets,
                    "currentAmount" => $balance,
                    "AddDate" => $current_time,
                    "Type" => "S",
                    "UserName" => $name,
                    "Agents" => $agents,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => "RMB",
                    "Name" => $alias,
                    "User" => $login_user["UserName"],
                    // "Checked" => 1,
                    // "Date" => $current_time,
                    "Order_Code" => $Order_Code,
                    "Notes" => $memo,
                );

                $sys_800->create($data);
            
                $q1 = User::where("UserName", $username)
                    ->update([
                        'Money' => (int)$balance,
                        'Credit' => (int)$balance,
                    ]);

                if ($q1 == 1) {

                    $new_log = new MoneyLog;
                    $new_log->user_id = $user_id;
                    $new_log->order_num = $Order_Code;
                    $new_log->about = $user["UserName"]."后台批量存款";
                    $new_log->update_time = $current_time;
                    $new_log->type = $memo;
                    $new_log->order_value = $gold;
                    $new_log->assets = $assets;
                    $new_log->balance = $balance;
                    $new_log->save();

                }

            }
            $response['message'] = "Cash Bulk Data saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }


    public function getPaymentMethod(Request $request) {

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

            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $lv = $request_data["lv"];
            $pay_type = $request_data["pay_type"] ?? "";

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

            $result = WebPaymentData::query();

            if ($pay_type == "") {
                $result = $result->where("Type", "<=", 10)->get();
            } else {
                $result = $result->where("Type", $pay_type)->get();
            }

            $response["data"] = $result;
            $response['message'] = "Payment Method Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addPaymentMethod(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "Address" => "required|string",
                "Business" => "required|string",
                "Keys" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ID = $request_data["ID"] ?? "";
            $Address = $request_data["Address"];
            $Business = $request_data["Business"];
            $Keys = $request_data["Keys"];
            $TerminalID = $request_data["TerminalID"] ?? "";
            $FixedGold = $request_data["FixedGold"] ?? "";
            $VIP = $request_data["VIP"];
            $WAP = $request_data["WAP"];
            $Limit1 = $request_data["Limit1"];
            $Limit2 = $request_data["Limit2"];
            $Switch = $request_data["Switch"];
            $Music = $request_data["Music"];
            $Sort = $request_data["Sort"];

            $new_data = array(
                "Address" => $Address,
                "Business" => $Business,
                "Keys" => $Keys,
                "TerminalID" => $TerminalID,
                "FixedGold" => $FixedGold,
                "VIP" => $VIP,
                "WAP" => $WAP,
                "Limit1" => $Limit1,
                "Limit2" => $Limit2,
                "Switch" => $Switch,
                "Music" => $Music,
                "Sort" => $Sort,
            );

            if ($ID == "") {
                $payment_method = new WebPaymentData;
                $payment_method->create($new_data);
            } else {
                WebPaymentData::where("ID", $ID)
                    ->update($new_data);
            }
            
            $response['message'] = "Payment Method Data saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function usePaymentMethod(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "ID" => "required|numeric",
                "Switch" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ID = $request_data["ID"];
            $Switch = $request_data["Switch"];

            WebPaymentData::where("ID", $ID)->update(["Switch" => $Switch]);
            
            $response['message'] = "Payment Method Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deletePaymentMethod(Request $request) {

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

            $request_data = $request->all();

            $ID = $request_data["ID"];

            WebPaymentData::where("ID", $ID)->delete();
            
            $response['message'] = "Payment Method Data deleted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getUser(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $name = $request_data["name"];

            $result = User::where("UserName", $name)->first(["Alias", "Money"]);
            
            $response["data"] = $result;
            $response['message'] = "User Data fetched successfully!";
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

