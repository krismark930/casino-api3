<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Web\Sys800;
use Carbon\Carbon;
use App\Utils\Utils;
use App\Models\User;
use App\Models\Web\MoneyLog;
use App\Models\Web\WebMemLogData;

class AdminAccessController extends Controller
{
    public function getWebSys800Data(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "action" => "required|string"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();

            $action = $request_data["action"];
            $rtype = $request_data["rtype"] ?? "";
            $search = $request_data["search"] ?? "";
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $result = Sys800::where("Type", $action);

            if ($rtype == 'S' || $rtype == "T") {
                $result = $result->where("Type", $rtype)->where("Type2", 1);
            } else if ($rtype == "") {
                $result = $result->where("Type2", "!=", 3);
            } else if($rtype = "F") {
                $result = $result->where("Type2", 2);
            } else if($rtype == "Q") {
                $result = $result->where("Cancel", 1);
            } else if($rtype = "C") {
                $result = $result->where("Checked", 0);
            }

            if ($search != "") {
                $result = $result->where("UserName", "like", "%$search%")
                    ->orwhere("Date", "like", "%$search%")
                    ->orwhere("Bank_Account", "like", "%$search%")
                    ->orwhere("Phone", "like", "%$search%");
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                    ->take($limit)->orderBy("Date", "DESC")->get();

            $CK = 0;
            $TK = 0;

            foreach($result as $row) { 
                $id=$row['ID'];
                $Order_Code=$row['Order_Code'];
                $BankName = "";
                $Memo='';
                if($row['Notes']<>'' and $row['Notes']<>$row['Name']){
                    if($row['Cancel']==1){
                        $Memo='<font color=red>拒绝理由:'.$row['Notes'].'</font><br>';
                    }else{
                        $Memo='<font color=red>扣款原因:'.$row['Notes'].'</font><br>';
                    }
                }
                $Memo.=Utils::GetField($row['UserName'],"Notes");
                $row["Memo"] = $Memo;
                if(strpos('88'.$Order_Code,'CK')) $Order_Code='';
                if(strpos('88'.$Order_Code,'TK')) $Order_Code='';

                $row["Order_Code"] = $Order_Code;

                if($row['Cancel']==0){
                    if($row['Type']=='S') $CK=$CK+$row['Gold'];
                    if($row['Type']=='T' and $row['Cancel']==0) $TK=$TK+$row['Gold'];
                }
                $row["CK"] = $CK;
                $row["TK"] = $TK;
                $Phone=$row['Phone'];
                if(strlen($Phone)>=8){
                    $Phone=substr($Phone,0,4).'****'.substr($Phone,8,3);
                }
                $row["Phone"] = $Phone;
                if($row['Type']=='S'){
                    $BankName=$row['Bank'].'<br>'.$row['Bank_Address'];
                }else{
                    $BankName=$row['Bank_Address'];
                }
                $row["BankName"] = $BankName;
                if($row['Type']=='S'){
                    if ($row['Cancel']==1){
                        $row["other"] = '<font color=red>存入已拒绝</font><br>';
                    }else{
                        $row["other"] = '<font color=blue>已存入</font><br>';
                    }
                }else{
                    if ($row['Cancel']==1){
                        $row["other"] = "<font color='red'>已恢复</font><br>";
                    }else
                        $row["other"] = '<font color=blue>已提出</font><br>';
                }
            }

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response["CK"] = $CK;
            $response["TK"] = $TK;
            $response['message'] = "Web Sys800 Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteWebSys800Data(Request $request) {

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

            Sys800::where("ID", $ID)->delete();

            $login_info = '现金系统删除';

            $loginname = $request->user()->UserName;
    
            $ip_addr = Utils::get_ip();
    
            $web_mem_log_data = new WebMemLogData();
    
            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";
    
            $web_mem_log_data->save();

            $response['message'] = "Web Sys800 Data deleted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function cancelWebSys800Data(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "ID" => "required|numeric",
                "sResult" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $ID = $request_data["ID"];
            $sResult = $request_data["sResult"];

            $row = Sys800::where("ID", $ID)->first();

            Sys800::where("ID", $ID)->update([
                "User" => $user["UserName"],
                "Cancel" => 1,
                "Notes" => $sResult,
                "Checked" => 1
            ]);

            $gold=$row['Gold'];
            $memname=$row['UserName'];
            $user_id=Utils::GetField($memname,'id');
            $current_time = date("Y-m-d H:i:s",time()+12*3600);

            $previousAmount=Utils::GetField($memname,'Money');

            $currentAmount = $previousAmount + (int)$gold;

            $user = User::where("UserName", $memname)->first();

            $withdrawal_condition = $user["withdrawal_condition"];

            $new_withdrawal_condition = $withdrawal_condition + (int)$gold;

            $q1 = User::where("UserName", $memname)
                ->update([
                    'Money' => $currentAmount,
                    'Credit' => $currentAmount,
                    'withdrawal_condition' => $new_withdrawal_condition,
                ]);

            if($q1 == 1) {

                $new_log = new MoneyLog;
                $new_log->user_id = $user_id;
                $new_log->order_num = $row["Order_Code"];
                $new_log->about = $user["UserName"]."恢复提款";
                $new_log->update_time = $current_time;
                $new_log->type = $user["UserName"]."恢复提款";
                $new_log->order_value = $gold;
                $new_log->assets = $previousAmount;
                $new_log->balance = $currentAmount;
                $new_log->save();

            }

            $login_info = '恢复提款';

            $loginname = $request->user()->UserName;
    
            $ip_addr = Utils::get_ip();
    
            $web_mem_log_data = new WebMemLogData();
    
            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";
    
            $web_mem_log_data->save();

            $response['message'] = "Web Sys800 Data canceld successfully!";
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
