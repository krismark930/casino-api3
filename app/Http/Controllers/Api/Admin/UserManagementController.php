<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WebAgent;
use App\Models\WebSystemData;
use App\Models\Web\MoneyLog;
use App\Models\Web\Sys800;
use App\Models\Web\WebMemLogData;
use App\Utils\Utils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    public function getSubUser(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $sort = $request_data["sort"] ?? "";
            $orderby = $request_data["orderby"] ?? "";

            $loginname = $user["UserName"];

            $web_system_data = WebSystemData::find(1);

            $admin_url = array_filter(explode(";", $web_system_data['Admin_Url']));

            // $data = "";

            // if (in_array($_SERVER['HTTP_HOST'], $admin_url){
            //     $data='web_system_data';
            // }else{
            //     $data='web_agents_data';
            // }

            $result = WebSystemData::where("SubName", $web_system_data["UserName"]);

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                ->take($limit)->orderby($sort, $orderby)->get();

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response['message'] = "Sub User Data fetched successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        $login_info = '查看子帐号';

        $ip_addr = Utils::get_ip();

        $web_mem_log_data = new WebMemLogData();

        $web_mem_log_data->UserName = $loginname;
        $web_mem_log_data->LoginTime = now();
        $web_mem_log_data->Context = $login_info;
        $web_mem_log_data->LoginIP = $ip_addr;
        $web_mem_log_data->Url = Utils::get_browser_ip();
        $web_mem_log_data->Level = "管理员";

        $web_mem_log_data->save();

        return response()->json($response, $response['status']);
    }

    public function addSubUser(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "UserName" => "required|string",
                "Passwd" => "required|string",
                "Alias" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $UserName = $request_data["UserName"];
            $Passwd = $request_data["Passwd"];
            $Alias = $request_data["Alias"];
            $AddDate = Carbon::now("Asia/Hong_Kong")->format("Y-m-d H:i:s");
            $competence = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,';

            $loginname = $user["UserName"];

            $row = WebSystemData::find(1);

            $web_system_data = WebSystemData::where("UserName", $UserName)->first();

            if (isset($web_system_data)) {

                $response["message"] = "您添加的子帐号已经存在，请重新输入！！";

                return response()->json($response, $response['status']);
            } else {
                $new_data = array(
                    "Level" => "M",
                    "UserName" => $UserName,
                    "LoginName" => $UserName,
                    "password" => Hash::make($Passwd),
                    "Passwd" => $Passwd,
                    "Alias" => $Alias,
                    "Status" => 0,
                    "Competence" => $competence,
                    "SubUser" => 1,
                    "SubName" => $row["UserName"],
                    "AddDate" => $AddDate,
                );

                // return $new_data;

                $result = new WebSystemData;
                $result->create($new_data);
            }

            $login_info = '添加子账户权限';

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";

            $web_mem_log_data->save();

            $response['message'] = "Sub User Data added successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateSubUser(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "UserName" => "required|string",
                "Passwd" => "required|string",
                "Alias" => "required|string",
                "id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $UserName = $request_data["UserName"];
            $Passwd = $request_data["Passwd"];
            $Alias = $request_data["Alias"];
            $id = $request_data["id"];

            $web_system_data = WebSystemData::where("id", $id)->update([
                "UserName" => $UserName,
                "Alias" => $Alias,
                "password" => Hash::make($Passwd),
                "Passwd" => $Passwd,
            ]);

            $response['message'] = "Sub User Data updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function suspendSubUser(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "Status" => "required|numeric",
                "id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $Status = $request_data["Status"];
            $id = $request_data["id"];

            $web_system_data = WebSystemData::where("id", $id)->update([
                "Status" => $Status,
            ]);

            $response['message'] = "Sub User Data suspended successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteSubUser(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $user = $request->user();
            $request_data = $request->all();
            $id = $request_data["id"];

            $web_system_data = WebSystemData::where("id", $id)->delete();

            $response['message'] = "Sub User Data deleted successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function permissionSubUser(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric",
                "Competence" => "required|string",
                "Style" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $Competence = $request_data["Competence"];
            $Style = $request_data["Style"];
            $id = $request_data["id"];

            $loginname = $user["UserName"];

            $web_system_data = WebSystemData::where("id", $id)->update([
                "Competence" => $Competence,
                "Style" => $Style,
            ]);

            $login_info = '查看子帐号权限';

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";

            $web_mem_log_data->save();

            $response['message'] = "Sub User Data permissioned successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getCompany(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $logined_user = $request->user();
            $request_data = $request->all();
            $lv = $request_data["lv"];
            $page = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $sort = $request_data["sort"] ?? "ADDDATE";
            $orderby = $request_data["orderby"] ?? "DESC";
            $parents_id = $request_data["parents_id"] ?? "";
            $enable = $request_data["enable"] ?? "";
            $disable = $request_data["disable"] ?? "";
            $suspend = $request_data["suspend"] ?? "";
            $logout = $request_data["logout"] ?? "";
            $sort = $request_data["sort"] ?? "";
            $active_id = $request_data["active_id"] ?? "";
            $active = $request_data['active'] ?? "";
            $username = $request_data["name"] ?? "";
            $search = $request_data["search"] ?? "";
            $dlg_option = $request_data['dlg_option'] ?? "";

            $row = WebSystemData::find(1);

            $admin_url = array_filter(explode(";", $row['Admin_Url']));

            $web = "";

            if (in_array($_SERVER['HTTP_HOST'], $admin_url)) {
                $web = 'web_system_data';
            } else {
                $web = 'web_agents_data';
            }

            $ManageMember = $row['ManageMember']; //代理商会员管理权限
            $ManageCredit = $row['ManageCredit']; //代理商额度管理权限
            $name = $row['UserName'];
            $passw = $row['Level'];
            $subUser = $row['SubUser'];
            if ($subUser == 0) {
                $name = $row['UserName'];
            } else {
                $name = $row['SubName'];
            }
            if ($name == "admin8888") {
                $name = "admin";
            }

            // return $name;

            switch ($lv) {
                case 'A':
                    $Title = "公司";
                    $Caption = "管理";
                    $level = 'M';
                    $lower = 'B';
                    $class = '#ED4E41';
                    $bgcolor = '#D72415';
                    $user = 'Admin';
                    $check = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and";
                    $agents = "Admin='$name' and Level='A' and subuser=0 ";
                    $data = 'web_agents_data';
                    break;
                case 'B':
                    $Title = "股东";
                    $Caption = "公司";
                    $level = 'A';
                    $lower = 'C';
                    $class = '#429CCD';
                    $bgcolor = '0E75B0';
                    $user = 'Super';
                    $check = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and";
                    $agents = "(Admin='$name' or Super='$name') and Level='B' and subuser=0 ";
                    $data = 'web_agents_data';
                    break;
                case 'C':
                    $Title = "总代理";
                    $Caption = "股东";
                    $level = 'B';
                    $lower = 'D';
                    $class = '#CD9A99';
                    $bgcolor = '976061';
                    $user = 'Corprator';
                    $check = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and";
                    $agents = "(Admin='$name' or Super='$name' or Corprator='$name') and Level='C' and subuser=0 ";
                    $data = 'web_agents_data';
                    break;
                case 'D':
                    $Title = "代理商";
                    $Caption = "总代理";
                    $level = 'C';
                    $lower = 'MEM';
                    $class = '#86C0A6';
                    $bgcolor = '4B8E6F';
                    $user = 'World';
                    $check = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and";
                    $agents = "(Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and Level='D' and subuser=0 ";
                    $data = 'web_agents_data';
                    break;
                case 'MEM':
                    $Title = "会员";
                    $Caption = "代理商";
                    $level = 'D';
                    $class = '#FEF5B5';
                    $bgcolor = 'E3D46E';
                    $user = 'Agents';
                    $check = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and";
                    $agents = "(Admin='$name' or Super='$name' or Corprator='$name' or World='$name' or Agents='$name')";
                    $data = 'web_member_data';
                    break;
            }

            $parents = DB::select("select UserName,Alias from web_agents_data where $check subuser=0 and Status<=1 and Level='$level' order by UserName ASC");

            $parents_array = array();
            array_push($parents_array, array("label" => "全部", "value" => ""));

            foreach ($parents as $item) {
                $item = get_object_vars($item);
                array_push($parents_array, array("label" => $item['UserName'] . "==" . $item['Alias'], "value" => $item["UserName"]));
            }

            if ($enable == "") {
                $enable = 'ALL';
            }

            if ($sort == "") {
                $sort = 'ADDDATE';
            }

            if ($orderby == "") {
                $orderby = 'DESC';
            }

            if ($search != '') {
                if ($data == 'web_agents_data') { //代理商没有机器码
                    $search = "and (UserName like '%$search%' or LoginName like '%$search%' or Alias like '%$search%' or AddDate like '%$search%')";
                } else {
                    $search = "and (UserName like '%$search%' or LoginName like '%$search%' or Alias like '%$search%' or MachineCode='$search' or AddDate like '%$search%')";
                }
            } else {
                $search = "";
            }
            $status = "";
            if ($enable == "Y") {
                $status = "and Status='0'";
            } else if ($enable == "S") {
                $status = "and Status='1'";
            } else if ($enable == "N") {
                $status = "and Status='2'";
            } else if ($enable == "T") {
                $status = "and Money<>Credit";
            } else if ($enable == 'C') {
                $AddDate = date("Y-m-d");
                $sql800 = "select distinct UserName from web_sys800_data where AddDate='$AddDate' and Type2=1";
                $row800 = DB::select($sql800);
                $UserNames = "";
                foreach ($row800 as $row) {
                    $UserNames .= "'" . $row['UserName'] . "',";
                }
                $UserNames = trim($UserNames, ',');
                $status = "and UserName in($UserNames)";
            }
            $agdata = "(Super='$username' or Corprator='$username' or World='$username')";
            $memdata = "(super='$username' or Corprator='$username' or World='$username' or Agents='$username')";

            switch ($active) {
                case "Y":
                    if ($web == 'web_system_data') {
                        $mysql = "update web_agents_data set EditType='1' where ID=$active_id";
                        DB::select($mysql);
                    } else {
                        echo '您无此权限!';
                    }
                    break;
                case "N":
                    if ($web == 'web_system_data') {
                        $mysql = "update web_agents_data set EditType='0' where ID=$active_id";
                        DB::select($mysql);
                    } else {
                        echo '您无此权限!';
                    }
                    break;
                case "enable":
                    if ($web == 'web_system_data' or $ManageMember == 1) {
                        $mysql = "update web_agents_data set Oid='logout',Status=0,LogoutTime=now() where ID=$active_id";
                        DB::select($mysql);
                        $mysql = "update web_agents_data set Oid='logout',Status=0,LogoutTime=now() where $agdata";
                        DB::select($mysql);
                        $mysql = "update web_member_data set Oid='logout',Status=0,LogoutTime=now() where ID=$active_id";
                        DB::select($mysql);
                        $mysql = "update web_member_data set Oid='logout',Status=0,LogoutTime=now() where $memdata";
                        DB::select($mysql);
                    } else {
                        echo '您无此权限!';
                    }
                    break;
                case "suspend":
                    if ($web == 'web_system_data' or $ManageMember == 1) {
                        $mysql = "update web_agents_data set Oid='logout',Status=1,LogoutTime=now() where ID=$active_id";
                        DB::select($mysql);
                        $mysql = "update web_agents_data set Oid='logout',Status=1,LogoutTime=now() where $agdata";
                        DB::select($mysql);
                        $mysql = "update web_member_data set Oid='logout',Status=1,LogoutTime=now() where ID=$active_id";
                        DB::select($mysql);
                        $mysql = "update web_member_data set Oid='logout',Status=1,LogoutTime=now() where $memdata";
                        DB::select($mysql);
                    } else {
                        echo '您无此权限!';
                    }
                    break;
                case "disable":
                    if ($web == 'web_system_data' or $ManageMember == 1) {
                        $mysql = "update web_agents_data set Oid='logout',Status=2,LogoutTime=now() where ID=$active_id";
                        DB::select($mysql);
                        $mysql = "update web_agents_data set Oid='logout',Status=2,LogoutTime=now() where $agdata";
                        DB::select($mysql);
                        $mysql = "update web_member_data set Oid='logout',Status=2,LogoutTime=now() where ID=$active_id";
                        DB::select($mysql);
                        $mysql = "update web_member_data set Oid='logout',Status=2,LogoutTime=now() where $memdata";
                        DB::select($mysql);
                    } else {
                        echo '您无此权限!';
                    }
                    break;
                case "logout":
                    if ($web == 'web_system_data' or $ManageMember == 1) {
                        $mysql = "update web_member_data set Oid='logout',Online=0,LogoutTime=now() where ID=$active_id";
                        DB::select($mysql);
                    } else {
                        echo '您无此权限!';
                    }
                    break;
            }

            if ($parents_id == '') {
                $sql = "select * from $data where $agents $status $search order by " . $sort . " " . $orderby;
            } else {
                $sql = "select * from $data where $agents $status and $user='$parents_id'  order by " . $sort . " " . $orderby;
            }

            $total_count = count(DB::select($sql));

            $offset = ($page - 1) * $limit;
            $mysql = $sql . " limit $offset,$limit;";

            $result = DB::select($mysql);

            $result_1 = array();

            foreach ($result as $row) {
                $row = get_object_vars($row);
                $sys_800 = Sys800::where("Type", "T")->where("UserName", $row["UserName"])->where("Cancel", 0)->select(DB::raw("SUM(Gold) as withdraw_money"))->get();
                // return $result[0]["withdraw_money"];
                $withdraw_money = $sys_800[0]["withdraw_money"];
                $row["Credit"] = $row["Money"] - $withdraw_money;
                array_push($result_1, $row);
            }

            $response["total_count"] = $total_count;
            $response["data"] = $result_1;
            $response["parents"] = $parents_array;
            $response["web"] = $web;
            $response['message'] = "Company Data fetched successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getCompanyInfoForAdd(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $logined_user = $request->user();
            $request_data = $request->all();
            $lv = $request_data["lv"];
            $parents_id = $request_data["parents_id"] ?? "";

            $row = WebSystemData::find(1);

            $admin_url = array_filter(explode(";", $row['Admin_Url']));

            $web = "";

            if (in_array($_SERVER['HTTP_HOST'], $admin_url)) {
                $web = 'web_system_data';
            } else {
                $web = 'web_agents_data';
            }

            $id = $row['id'];
            $passw = $row['Level'];
            $curtype = $row['CurType'];
            $name = $row['UserName'];
            $admin = $row['Admin'];

            switch ($lv) {
                case 'A':
                    $Title = "公司";
                    $Caption = "管理";
                    $level = 'M';
                    $data = 'web_system_data';
                    $agents = "UserName='$name'";
                    $user = "Level='A' and Admin='$parents_id'";
                    break;
                case 'B':
                    $Title = "股东";
                    $Caption = "公司";
                    $level = 'A';
                    $data = 'web_agents_data';
                    $agents = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and subuser=0";
                    $user = "Level='B' and Super='$parents_id'";
                    break;
                case 'C':
                    $Title = "总代理";
                    $Caption = "股东";
                    $level = 'B';
                    $data = 'web_agents_data';
                    $agents = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and subuser=0";
                    $user = "Level='C' and Corprator='$parents_id'";
                    break;
                case 'D':
                    $Title = "代理商";
                    $Caption = "总代理";
                    $level = 'C';
                    $data = 'web_agents_data';
                    $agents = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and subuser=0";
                    $user = "Level='D' and World='$parents_id'";
                    break;
            }

            $parents = array();
            $arow = DB::select("select UserName,Alias from $data where $agents and Status=0 and Level='$level' order by ID desc");
            foreach ($arow as $item) {
                $item = get_object_vars($item);
                array_push($parents, array("label" => $item['UserName'] . "==" . $item['Alias'], "value" => $item["UserName"]));
            }
            $srow = DB::select("select sum(credit) as credit,sum(Points) as Points from web_agents_data where Status=0 and $user");
            $erow = DB::select("select sum(credit) as credit,sum(Points) as Points from web_agents_data where Status>0 and $user");
            $crow = DB::select("select sum(Credit) as credit from web_agents_data where $user");

            $response["crow"] = $crow[0]->credit ?? 0;
            $response["srow"] = $srow[0]->credit ?? 0;
            $response["erow"] = $erow[0]->credit ?? 0;
            $response["parents"] = $parents;
            $response['message'] = "Company Info fetched successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addCompany(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $logined_user = $request->user();

            $_REQUEST = $request->all();
            $lv = $_REQUEST["lv"];
            $parents_id = $_REQUEST['parents_id'];

            $AddDate = date('Y-m-d H:i:s'); //新增日期
            $username = $_REQUEST['UserName']; //帐号
            $loginname = $_REQUEST['LoginName']; //帐号
            if ($lv == "MEM") {
                $password = Hash::make($_REQUEST['password']); //密码
            } else {
                $password = Hash::make($_REQUEST['PassWord']); //密码
            }
            $maxcredit = $_REQUEST['maxcredit']; //总信用额度
            $wager = $_REQUEST['wager']; // 即时注单
            $CurType = $_REQUEST['CurType'] ?? ""; //币别
            $alias = $_REQUEST['Alias'] ?? ""; //名称
            $usedate = $_REQUEST['usedate'] ?? "";
            $address = $_REQUEST['address'] ?? "";

            $row = WebSystemData::find(1);

            $admin_url = array_filter(explode(";", $row['Admin_Url']));

            $web = "";

            if (in_array($_SERVER['HTTP_HOST'], $admin_url)) {
                $web = 'web_system_data';
            } else {
                $web = 'web_agents_data';
            }

            $id = $row['id'];
            $passw = $row['Level'];
            $curtype = $row['CurType'];
            $name = $row['UserName'];
            $admin = $row['Admin'];

            switch ($lv) {
                case 'A':
                    $Title = "公司";
                    $Caption = "管理";
                    $level = 'M';
                    $data = 'web_system_data';
                    $agents = "UserName='$name'";
                    break;
                case 'B':
                    $Title = "股东";
                    $Caption = "公司";
                    $level = 'A';
                    $data = 'web_agents_data';
                    $agents = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and subuser=0";
                    break;
                case 'C':
                    $Title = "总代理";
                    $Caption = "股东";
                    $level = 'B';
                    $data = 'web_agents_data';
                    $agents = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and subuser=0";
                    break;
                case 'D':
                    $Title = "代理商";
                    $Caption = "总代理";
                    $level = 'C';
                    $data = 'web_agents_data';
                    $agents = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and subuser=0";
                    break;
                case 'MEM':
                    $Title = "会员";
                    $Caption = "代理商";
                    $level = 'C';
                    $data = 'web_member_data';
                    $agents = "(UserName='$name' or Admin='$name' or Super='$name' or Corprator='$name' or World='$name') and subuser=0";
                    break;
            }

            if ($lv == "MEM") {

                $agent = 'ddm999';

                // if($web=='web_agents_data') {
                //     $row_1 = $logined_user;
                // }else{
                $row_1 = WebAgent::where("UserName", $agent)->first();
                // }

                // return $row_1['Admin'];

            } else {

                $row_1 = DB::select("select * from $data where UserName='$parents_id'");

                $row_1 = get_object_vars($row_1[0]);
            }

            $Winloss_A = $row_1['A_Point'] ?? "";
            $Winloss_B = $row_1['B_Point'] ?? "";
            $Winloss_C = $row_1['C_Point'] ?? "";
            $Winloss_D = $row_1['D_Point'] ?? "";
            $sports = $row_1['Sports'] ?? "";
            $lottery = $row_1['Lottery'] ?? "";
            $world = $row_1['World'] ?? "";
            $corprator = $row_1['Corprator'] ?? "";
            $super = $row_1['Super'] ?? "";
            $admin = $row_1['Admin'] ?? "";
            $linetype = $row_1['LineType'] ?? "";

            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~足球
            $FT_Turn_R_A = $row_1['FT_Turn_R_A'];
            $FT_Turn_R_B = $row_1['FT_Turn_R_B'];
            $FT_Turn_R_C = $row_1['FT_Turn_R_C'];
            $FT_Turn_R_D = $row_1['FT_Turn_R_D'];
            $FT_R_Bet = $row_1['FT_R_Bet'];
            $FT_R_Scene = $row_1['FT_R_Scene'];
            $FT_Turn_OU_A = $row_1['FT_Turn_OU_A'];
            $FT_Turn_OU_B = $row_1['FT_Turn_OU_B'];
            $FT_Turn_OU_C = $row_1['FT_Turn_OU_C'];
            $FT_Turn_OU_D = $row_1['FT_Turn_OU_D'];
            $FT_OU_Bet = $row_1['FT_OU_Bet'];
            $FT_OU_Scene = $row_1['FT_OU_Scene'];
            $FT_Turn_RE_A = $row_1['FT_Turn_RE_A'];
            $FT_Turn_RE_B = $row_1['FT_Turn_RE_B'];
            $FT_Turn_RE_C = $row_1['FT_Turn_RE_C'];
            $FT_Turn_RE_D = $row_1['FT_Turn_RE_D'];
            $FT_RE_Bet = $row_1['FT_RE_Bet'];
            $FT_RE_Scene = $row_1['FT_RE_Scene'];
            $FT_Turn_ROU_A = $row_1['FT_Turn_ROU_A'];
            $FT_Turn_ROU_B = $row_1['FT_Turn_ROU_B'];
            $FT_Turn_ROU_C = $row_1['FT_Turn_ROU_C'];
            $FT_Turn_ROU_D = $row_1['FT_Turn_ROU_D'];
            $FT_ROU_Bet = $row_1['FT_ROU_Bet'];
            $FT_ROU_Scene = $row_1['FT_ROU_Scene'];
            $FT_Turn_EO_A = $row_1['FT_Turn_EO_A'];
            $FT_Turn_EO_B = $row_1['FT_Turn_EO_B'];
            $FT_Turn_EO_C = $row_1['FT_Turn_EO_C'];
            $FT_Turn_EO_D = $row_1['FT_Turn_EO_D'];
            $FT_EO_Bet = $row_1['FT_EO_Bet'];
            $FT_EO_Scene = $row_1['FT_EO_Scene'];
            $FT_Turn_RM = $row_1['FT_Turn_RM'];
            $FT_RM_Bet = $row_1['FT_RM_Bet'];
            $FT_RM_Scene = $row_1['FT_RM_Scene'];
            $FT_Turn_M = $row_1['FT_Turn_M'];
            $FT_M_Bet = $row_1['FT_M_Bet'];
            $FT_M_Scene = $row_1['FT_M_Scene'];
            $FT_Turn_PD = $row_1['FT_Turn_PD'];
            $FT_PD_Bet = $row_1['FT_PD_Bet'];
            $FT_PD_Scene = $row_1['FT_PD_Scene'];
            $FT_Turn_T = $row_1['FT_Turn_T'];
            $FT_T_Bet = $row_1['FT_T_Bet'];
            $FT_T_Scene = $row_1['FT_T_Scene'];
            $FT_Turn_F = $row_1['FT_Turn_F'];
            $FT_F_Bet = $row_1['FT_F_Bet'];
            $FT_F_Scene = $row_1['FT_F_Scene'];
            $FT_Turn_P = $row_1['FT_Turn_P'];
            $FT_P_Bet = $row_1['FT_P_Bet'];
            $FT_P_Scene = $row_1['FT_P_Scene'];
            $FT_Turn_PR = $row_1['FT_Turn_PR'];
            $FT_PR_Bet = $row_1['FT_PR_Bet'];
            $FT_PR_Scene = $row_1['FT_PR_Scene'];
            $FT_Turn_P3 = $row_1['FT_Turn_P3'];
            $FT_P3_Bet = $row_1['FT_P3_Bet'];
            $FT_P3_Scene = $row_1['FT_P3_Scene'];
            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~篮球
            $BK_Turn_R_A = $row_1['BK_Turn_R_A'];
            $BK_Turn_R_B = $row_1['BK_Turn_R_B'];
            $BK_Turn_R_C = $row_1['BK_Turn_R_C'];
            $BK_Turn_R_D = $row_1['BK_Turn_R_D'];
            $BK_R_Bet = $row_1['BK_R_Bet'];
            $BK_R_Scene = $row_1['BK_R_Scene'];
            $BK_Turn_OU_A = $row_1['BK_Turn_OU_A'];
            $BK_Turn_OU_B = $row_1['BK_Turn_OU_B'];
            $BK_Turn_OU_C = $row_1['BK_Turn_OU_C'];
            $BK_Turn_OU_D = $row_1['BK_Turn_OU_D'];
            $BK_OU_Bet = $row_1['BK_OU_Bet'];
            $BK_OU_Scene = $row_1['BK_OU_Scene'];
            $BK_Turn_RE_A = $row_1['BK_Turn_RE_A'];
            $BK_Turn_RE_B = $row_1['BK_Turn_RE_B'];
            $BK_Turn_RE_C = $row_1['BK_Turn_RE_C'];
            $BK_Turn_RE_D = $row_1['BK_Turn_RE_D'];
            $BK_RE_Bet = $row_1['BK_RE_Bet'];
            $BK_RE_Scene = $row_1['BK_RE_Scene'];
            $BK_Turn_ROU_A = $row_1['BK_Turn_ROU_A'];
            $BK_Turn_ROU_B = $row_1['BK_Turn_ROU_B'];
            $BK_Turn_ROU_C = $row_1['BK_Turn_ROU_C'];
            $BK_Turn_ROU_D = $row_1['BK_Turn_ROU_D'];
            $BK_ROU_Bet = $row_1['BK_ROU_Bet'];
            $BK_ROU_Scene = $row_1['BK_ROU_Scene'];
            $BK_Turn_EO_A = $row_1['BK_Turn_EO_A'];
            $BK_Turn_EO_B = $row_1['BK_Turn_EO_B'];
            $BK_Turn_EO_C = $row_1['BK_Turn_EO_C'];
            $BK_Turn_EO_D = $row_1['BK_Turn_EO_D'];
            $BK_EO_Bet = $row_1['BK_EO_Bet'];
            $BK_EO_Scene = $row_1['BK_EO_Scene'];
            $BK_Turn_PR = $row_1['BK_Turn_PR'];
            $BK_PR_Bet = $row_1['BK_PR_Bet'];
            $BK_PR_Scene = $row_1['BK_PR_Scene'];
            $BK_Turn_P3 = $row_1['BK_Turn_P3'];
            $BK_P3_Bet = $row_1['BK_P3_Bet'];
            $BK_P3_Scene = $row_1['BK_P3_Scene'];
            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~冠军
            $FS_Turn_FS = $row_1['FS_Turn_FS'];
            $FS_FS_Scene = $row_1['FS_FS_Scene'];
            $FS_FS_Bet = $row_1['FS_FS_Bet'];
            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~棒球
            $BS_Turn_R_A = $row_1['BS_Turn_R_A'];
            $BS_Turn_R_B = $row_1['BS_Turn_R_B'];
            $BS_Turn_R_C = $row_1['BS_Turn_R_C'];
            $BS_Turn_R_D = $row_1['BS_Turn_R_D'];
            $BS_R_Bet = $row_1['BS_R_Bet'];
            $BS_R_Scene = $row_1['BS_R_Scene'];
            $BS_Turn_OU_A = $row_1['BS_Turn_OU_A'];
            $BS_Turn_OU_B = $row_1['BS_Turn_OU_B'];
            $BS_Turn_OU_C = $row_1['BS_Turn_OU_C'];
            $BS_Turn_OU_D = $row_1['BS_Turn_OU_D'];
            $BS_OU_Bet = $row_1['BS_OU_Bet'];
            $BS_OU_Scene = $row_1['BS_OU_Scene'];
            $BS_Turn_RE_A = $row_1['BS_Turn_RE_A'];
            $BS_Turn_RE_B = $row_1['BS_Turn_RE_B'];
            $BS_Turn_RE_C = $row_1['BS_Turn_RE_C'];
            $BS_Turn_RE_D = $row_1['BS_Turn_RE_D'];
            $BS_RE_Bet = $row_1['BS_RE_Bet'];
            $BS_RE_Scene = $row_1['BS_RE_Scene'];
            $BS_Turn_ROU_A = $row_1['BS_Turn_ROU_A'];
            $BS_Turn_ROU_B = $row_1['BS_Turn_ROU_B'];
            $BS_Turn_ROU_C = $row_1['BS_Turn_ROU_C'];
            $BS_Turn_ROU_D = $row_1['BS_Turn_ROU_D'];
            $BS_ROU_Bet = $row_1['BS_ROU_Bet'];
            $BS_ROU_Scene = $row_1['BS_ROU_Scene'];
            $BS_Turn_EO_A = $row_1['BS_Turn_EO_A'];
            $BS_Turn_EO_B = $row_1['BS_Turn_EO_B'];
            $BS_Turn_EO_C = $row_1['BS_Turn_EO_C'];
            $BS_Turn_EO_D = $row_1['BS_Turn_EO_D'];
            $BS_EO_Bet = $row_1['BS_EO_Bet'];
            $BS_EO_Scene = $row_1['BS_EO_Scene'];
            $BS_Turn_1X2_A = $row_1['BS_Turn_1X2_A'];
            $BS_Turn_1X2_B = $row_1['BS_Turn_1X2_B'];
            $BS_Turn_1X2_C = $row_1['BS_Turn_1X2_C'];
            $BS_Turn_1X2_D = $row_1['BS_Turn_1X2_D'];
            $BS_1X2_Bet = $row_1['BS_1X2_Bet'];
            $BS_1X2_Scene = $row_1['BS_1X2_Scene'];
            $BS_Turn_PD = $row_1['BS_Turn_PD'];
            $BS_PD_Scene = $row_1['BS_PD_Scene'];
            $BS_PD_Bet = $row_1['BS_PD_Bet'];
            $BS_Turn_T = $row_1['BS_Turn_T'];
            $BS_T_Scene = $row_1['BS_T_Scene'];
            $BS_T_Bet = $row_1['BS_T_Bet'];
            $BS_Turn_M = $row_1['BS_Turn_M'];
            $BS_M_Scene = $row_1['BS_M_Scene'];
            $BS_M_Bet = $row_1['BS_M_Bet'];
            $BS_Turn_P = $row_1['BS_Turn_P'];
            $BS_P_Scene = $row_1['BS_P_Scene'];
            $BS_P_Bet = $row_1['BS_P_Bet'];
            $BS_Turn_PR = $row_1['BS_Turn_PR'];
            $BS_PR_Scene = $row_1['BS_PR_Scene'];
            $BS_PR_Bet = $row_1['BS_PR_Bet'];
            $BS_Turn_P3 = $row_1['BS_Turn_P3'];
            $BS_P3_Scene = $row_1['BS_P3_Scene'];
            $BS_P3_Bet = $row_1['BS_P3_Bet'];
            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~网球
            $TN_Turn_R_A = $row_1['TN_Turn_R_A'];
            $TN_Turn_R_B = $row_1['TN_Turn_R_B'];
            $TN_Turn_R_C = $row_1['TN_Turn_R_C'];
            $TN_Turn_R_D = $row_1['TN_Turn_R_D'];
            $TN_R_Bet = $row_1['TN_R_Bet'];
            $TN_R_Scene = $row_1['TN_R_Scene'];
            $TN_Turn_OU_A = $row_1['TN_Turn_OU_A'];
            $TN_Turn_OU_B = $row_1['TN_Turn_OU_B'];
            $TN_Turn_OU_C = $row_1['TN_Turn_OU_C'];
            $TN_Turn_OU_D = $row_1['TN_Turn_OU_D'];
            $TN_OU_Bet = $row_1['TN_OU_Bet'];
            $TN_OU_Scene = $row_1['TN_OU_Scene'];
            $TN_EO_Scene = $row_1['TN_EO_Scene'];
            $TN_M_Scene = $row_1['TN_M_Scene'];
            $TN_PR_Scene = $row_1['TN_PR_Scene'];
            $TN_P_Scene = $row_1['TN_P_Scene'];
            $TN_PD_Scene = $row_1['TN_PD_Scene'];
            // $TN_T_Scene=$row_1['TN_T_Scene'];
            // $TN_F_Scene=$row_1['TN_F_Scene'];
            $TN_T_Scene = "";
            $TN_F_Scene = "";
            $TN_Turn_RE_A = $row_1['TN_Turn_RE_A'];
            $TN_Turn_RE_B = $row_1['TN_Turn_RE_B'];
            $TN_Turn_RE_C = $row_1['TN_Turn_RE_C'];
            $TN_Turn_RE_D = $row_1['TN_Turn_RE_D'];
            $TN_RE_Bet = $row_1['TN_RE_Bet'];
            $TN_RE_Scene = $row_1['TN_RE_Scene'];
            $TN_Turn_ROU_A = $row_1['TN_Turn_ROU_A'];
            $TN_Turn_ROU_B = $row_1['TN_Turn_ROU_B'];
            $TN_Turn_ROU_C = $row_1['TN_Turn_ROU_C'];
            $TN_Turn_ROU_D = $row_1['TN_Turn_ROU_D'];
            $TN_ROU_Bet = $row_1['TN_ROU_Bet'];
            $TN_ROU_Scene = $row_1['TN_ROU_Scene'];
            $TN_Turn_EO_A = $row_1['TN_Turn_EO_A'];
            $TN_Turn_EO_B = $row_1['TN_Turn_EO_B'];
            $TN_Turn_EO_C = $row_1['TN_Turn_EO_C'];
            $TN_Turn_EO_D = $row_1['TN_Turn_EO_D'];
            $TN_EO_Bet = $row_1['TN_EO_Bet'];
            $TN_EO_Scene = $row_1['TN_EO_Scene'];
            $TN_Turn_M = $row_1['TN_Turn_M'];
            $TN_M_Bet = $row_1['TN_M_Bet'];
            $TN_M_Scene = $row_1['TN_M_Scene'];
            $TN_Turn_PD = $row_1['TN_Turn_PD'];
            $TN_PD_Bet = $row_1['TN_PD_Bet'];
            $TN_PD_Scene = $row_1['TN_PD_Scene'];
            $TN_Turn_P = $row_1['TN_Turn_P'];
            $TN_P_Bet = $row_1['TN_P_Bet'];
            $TN_P_Scene = $row_1['TN_P_Scene'];
            $TN_Turn_PR = $row_1['TN_Turn_PR'];
            $TN_PR_Bet = $row_1['TN_PR_Bet'];
            $TN_PR_Scene = $row_1['TN_PR_Scene'];
            $TN_Turn_P3 = $row_1['TN_Turn_P3'];
            $TN_P3_Bet = $row_1['TN_P3_Bet'];
            $TN_P3_Scene = $row_1['TN_P3_Scene'];
            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~排球
            $VB_Turn_R_A = $row_1['VB_Turn_R_A'];
            $VB_Turn_R_B = $row_1['VB_Turn_R_B'];
            $VB_Turn_R_C = $row_1['VB_Turn_R_C'];
            $VB_Turn_R_D = $row_1['VB_Turn_R_D'];
            $VB_R_Bet = $row_1['VB_R_Bet'];
            $VB_R_Scene = $row_1['VB_R_Scene'];
            $VB_Turn_OU_A = $row_1['VB_Turn_OU_A'];
            $VB_Turn_OU_B = $row_1['VB_Turn_OU_B'];
            $VB_Turn_OU_C = $row_1['VB_Turn_OU_C'];
            $VB_Turn_OU_D = $row_1['VB_Turn_OU_D'];
            $VB_OU_Bet = $row_1['VB_OU_Bet'];
            $VB_OU_Scene = $row_1['VB_OU_Scene'];
            $VB_Turn_RE_A = $row_1['VB_Turn_RE_A'];
            $VB_Turn_RE_B = $row_1['VB_Turn_RE_B'];
            $VB_Turn_RE_C = $row_1['VB_Turn_RE_C'];
            $VB_Turn_RE_D = $row_1['VB_Turn_RE_D'];
            $VB_RE_Bet = $row_1['VB_RE_Bet'];
            $VB_RE_Scene = $row_1['VB_RE_Scene'];
            $VB_Turn_ROU_A = $row_1['VB_Turn_ROU_A'];
            $VB_Turn_ROU_B = $row_1['VB_Turn_ROU_B'];
            $VB_Turn_ROU_C = $row_1['VB_Turn_ROU_C'];
            $VB_Turn_ROU_D = $row_1['VB_Turn_ROU_D'];
            $VB_ROU_Bet = $row_1['VB_ROU_Bet'];
            $VB_ROU_Scene = $row_1['VB_ROU_Scene'];
            $VB_Turn_EO_A = $row_1['VB_Turn_EO_A'];
            $VB_Turn_EO_B = $row_1['VB_Turn_EO_B'];
            $VB_Turn_EO_C = $row_1['VB_Turn_EO_C'];
            $VB_Turn_EO_D = $row_1['VB_Turn_EO_D'];
            $VB_EO_Bet = $row_1['VB_EO_Bet'];
            $VB_EO_Scene = $row_1['VB_EO_Scene'];
            $VB_Turn_M = $row_1['VB_Turn_M'];
            $VB_M_Bet = $row_1['VB_M_Bet'];
            $VB_M_Scene = $row_1['VB_M_Scene'];
            $VB_Turn_PD = $row_1['VB_Turn_PD'];
            $VB_PD_Bet = $row_1['VB_PD_Bet'];
            $VB_PD_Scene = $row_1['VB_PD_Scene'];
            $VB_Turn_P = $row_1['VB_Turn_P'];
            $VB_P_Bet = $row_1['VB_P_Bet'];
            $VB_P_Scene = $row_1['VB_P_Scene'];
            $VB_Turn_PR = $row_1['VB_Turn_PR'];
            $VB_PR_Bet = $row_1['VB_PR_Bet'];
            $VB_PR_Scene = $row_1['VB_PR_Scene'];
            $VB_Turn_P3 = $row_1['VB_Turn_P3'];
            $VB_P3_Bet = $row_1['VB_P3_Bet'];
            $VB_P3_Scene = $row_1['VB_P3_Scene'];
            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~其它
            $OP_Turn_R_A = $row_1['OP_Turn_R_A'];
            $OP_Turn_R_B = $row_1['OP_Turn_R_B'];
            $OP_Turn_R_C = $row_1['OP_Turn_R_C'];
            $OP_Turn_R_D = $row_1['OP_Turn_R_D'];
            $OP_R_Bet = $row_1['OP_R_Bet'];
            $OP_R_Scene = $row_1['OP_R_Scene'];
            $OP_Turn_OU_A = $row_1['OP_Turn_OU_A'];
            $OP_Turn_OU_B = $row_1['OP_Turn_OU_B'];
            $OP_Turn_OU_C = $row_1['OP_Turn_OU_C'];
            $OP_Turn_OU_D = $row_1['OP_Turn_OU_D'];
            $OP_OU_Bet = $row_1['OP_OU_Bet'];
            $OP_OU_Scene = $row_1['OP_OU_Scene'];
            $OP_Turn_RE_A = $row_1['OP_Turn_RE_A'];
            $OP_Turn_RE_B = $row_1['OP_Turn_RE_B'];
            $OP_Turn_RE_C = $row_1['OP_Turn_RE_C'];
            $OP_Turn_RE_D = $row_1['OP_Turn_RE_D'];
            $OP_RE_Bet = $row_1['OP_RE_Bet'];
            $OP_RE_Scene = $row_1['OP_RE_Scene'];
            $OP_Turn_ROU_A = $row_1['OP_Turn_ROU_A'];
            $OP_Turn_ROU_B = $row_1['OP_Turn_ROU_B'];
            $OP_Turn_ROU_C = $row_1['OP_Turn_ROU_C'];
            $OP_Turn_ROU_D = $row_1['OP_Turn_ROU_D'];
            $OP_ROU_Bet = $row_1['OP_ROU_Bet'];
            $OP_ROU_Scene = $row_1['OP_ROU_Scene'];
            $OP_Turn_EO_A = $row_1['OP_Turn_EO_A'];
            $OP_Turn_EO_B = $row_1['OP_Turn_EO_B'];
            $OP_Turn_EO_C = $row_1['OP_Turn_EO_C'];
            $OP_Turn_EO_D = $row_1['OP_Turn_EO_D'];
            $OP_EO_Bet = $row_1['OP_EO_Bet'];
            $OP_EO_Scene = $row_1['OP_EO_Scene'];
            $OP_Turn_M = $row_1['OP_Turn_M'];
            $OP_M_Bet = $row_1['OP_M_Bet'];
            $OP_M_Scene = $row_1['OP_M_Scene'];
            $OP_Turn_PD = $row_1['OP_Turn_PD'];
            $OP_PD_Bet = $row_1['OP_PD_Bet'];
            $OP_PD_Scene = $row_1['OP_PD_Scene'];
            $OP_Turn_T = $row_1['OP_Turn_T'];
            $OP_T_Bet = $row_1['OP_T_Bet'];
            $OP_T_Scene = $row_1['OP_T_Scene'];
            $OP_Turn_F = $row_1['OP_Turn_F'];
            $OP_F_Bet = $row_1['OP_F_Bet'];
            $OP_F_Scene = $row_1['OP_F_Scene'];
            $OP_Turn_P = $row_1['OP_Turn_P'];
            $OP_P_Bet = $row_1['OP_P_Bet'];
            $OP_P_Scene = $row_1['OP_P_Scene'];
            $OP_Turn_PR = $row_1['OP_Turn_PR'];
            $OP_PR_Bet = $row_1['OP_PR_Bet'];
            $OP_PR_Scene = $row_1['OP_PR_Scene'];
            $OP_Turn_P3 = $row_1['OP_Turn_P3'];
            $OP_P3_Bet = $row_1['OP_P3_Bet'];
            $OP_P3_Scene = $row_1['OP_P3_Scene'];
            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~六合彩
            $SIX_Turn_SCA_A = $row_1['SIX_Turn_SCA_A'];
            $SIX_Turn_SCA_B = $row_1['SIX_Turn_SCA_B'];
            $SIX_Turn_SCA_C = $row_1['SIX_Turn_SCA_C'];
            $SIX_Turn_SCA_D = $row_1['SIX_Turn_SCA_D'];
            $SIX_SCA_Bet = $row_1['SIX_SCA_Bet'];
            $SIX_SCA_Scene = $row_1['SIX_SCA_Scene'];
            $SIX_Turn_SCB_A = $row_1['SIX_Turn_SCB_A'];
            $SIX_Turn_SCB_B = $row_1['SIX_Turn_SCB_B'];
            $SIX_Turn_SCB_C = $row_1['SIX_Turn_SCB_C'];
            $SIX_Turn_SCB_D = $row_1['SIX_Turn_SCB_D'];
            $SIX_SCB_Bet = $row_1['SIX_SCB_Bet'];
            $SIX_SCB_Scene = $row_1['SIX_SCB_Scene'];
            $SIX_Turn_SCA_AOUEO_A = $row_1['SIX_Turn_SCA_AOUEO_A'];
            $SIX_Turn_SCA_AOUEO_B = $row_1['SIX_Turn_SCA_AOUEO_B'];
            $SIX_Turn_SCA_AOUEO_C = $row_1['SIX_Turn_SCA_AOUEO_C'];
            $SIX_Turn_SCA_AOUEO_D = $row_1['SIX_Turn_SCA_AOUEO_D'];
            $SIX_SCA_AOUEO_Bet = $row_1['SIX_SCA_AOUEO_Bet'];
            $SIX_SCA_AOUEO_Scene = $row_1['SIX_SCA_AOUEO_Scene'];
            $SIX_Turn_SCA_BOUEO_A = $row_1['SIX_Turn_SCA_BOUEO_A'];
            $SIX_Turn_SCA_BOUEO_B = $row_1['SIX_Turn_SCA_BOUEO_B'];
            $SIX_Turn_SCA_BOUEO_C = $row_1['SIX_Turn_SCA_BOUEO_C'];
            $SIX_Turn_SCA_BOUEO_D = $row_1['SIX_Turn_SCA_BOUEO_D'];
            $SIX_SCA_BOUEO_Bet = $row_1['SIX_SCA_BOUEO_Bet'];
            $SIX_SCA_BOUEO_Scene = $row_1['SIX_SCA_BOUEO_Scene'];
            $SIX_Turn_SCA_RBG_A = $row_1['SIX_Turn_SCA_RBG_A'];
            $SIX_Turn_SCA_RBG_B = $row_1['SIX_Turn_SCA_RBG_B'];
            $SIX_Turn_SCA_RBG_C = $row_1['SIX_Turn_SCA_RBG_C'];
            $SIX_Turn_SCA_RBG_D = $row_1['SIX_Turn_SCA_RBG_D'];
            $SIX_SCA_RBG_Bet = $row_1['SIX_SCA_RBG_Bet'];
            $SIX_SCA_RBG_Scene = $row_1['SIX_SCA_RBG_Scene'];
            $SIX_Turn_AC_A = $row_1['SIX_Turn_AC_A'];
            $SIX_Turn_AC_B = $row_1['SIX_Turn_AC_B'];
            $SIX_Turn_AC_C = $row_1['SIX_Turn_AC_C'];
            $SIX_Turn_AC_D = $row_1['SIX_Turn_AC_D'];
            $SIX_AC_Bet = $row_1['SIX_AC_Bet'];
            $SIX_AC_Scene = $row_1['SIX_AC_Scene'];
            $SIX_Turn_AC_TOUEO_A = $row_1['SIX_Turn_AC_TOUEO_A'];
            $SIX_Turn_AC_TOUEO_B = $row_1['SIX_Turn_AC_TOUEO_B'];
            $SIX_Turn_AC_TOUEO_C = $row_1['SIX_Turn_AC_TOUEO_C'];
            $SIX_Turn_AC_TOUEO_D = $row_1['SIX_Turn_AC_TOUEO_D'];
            $SIX_AC_TOUEO_Bet = $row_1['SIX_AC_TOUEO_Bet'];
            $SIX_AC_TOUEO_Scene = $row_1['SIX_AC_TOUEO_Scene'];
            $SIX_Turn_AC6_AOUEO_A = $row_1['SIX_Turn_AC6_AOUEO_A'];
            $SIX_Turn_AC6_AOUEO_B = $row_1['SIX_Turn_AC6_AOUEO_B'];
            $SIX_Turn_AC6_AOUEO_C = $row_1['SIX_Turn_AC6_AOUEO_C'];
            $SIX_Turn_AC6_AOUEO_D = $row_1['SIX_Turn_AC6_AOUEO_D'];
            $SIX_AC6_AOUEO_Bet = $row_1['SIX_AC6_AOUEO_Bet'];
            $SIX_AC6_AOUEO_Scene = $row_1['SIX_AC6_AOUEO_Scene'];
            $SIX_Turn_AC6_BOUEO_A = $row_1['SIX_Turn_AC6_BOUEO_A'];
            $SIX_Turn_AC6_BOUEO_B = $row_1['SIX_Turn_AC6_BOUEO_B'];
            $SIX_Turn_AC6_BOUEO_C = $row_1['SIX_Turn_AC6_BOUEO_C'];
            $SIX_Turn_AC6_BOUEO_D = $row_1['SIX_Turn_AC6_BOUEO_D'];
            $SIX_AC6_BOUEO_Bet = $row_1['SIX_AC6_BOUEO_Bet'];
            $SIX_AC6_BOUEO_Scene = $row_1['SIX_AC6_BOUEO_Scene'];
            $SIX_Turn_AC6_RBG_A = $row_1['SIX_Turn_AC6_RBG_A'];
            $SIX_Turn_AC6_RBG_B = $row_1['SIX_Turn_AC6_RBG_B'];
            $SIX_Turn_AC6_RBG_C = $row_1['SIX_Turn_AC6_RBG_C'];
            $SIX_Turn_AC6_RBG_D = $row_1['SIX_Turn_AC6_RBG_D'];
            $SIX_AC6_RBG_Bet = $row_1['SIX_AC6_RBG_Bet'];
            $SIX_AC6_RBG_Scene = $row_1['SIX_AC6_RBG_Scene'];
            $SIX_Turn_SX_A = $row_1['SIX_Turn_SX_A'];
            $SIX_Turn_SX_B = $row_1['SIX_Turn_SX_B'];
            $SIX_Turn_SX_C = $row_1['SIX_Turn_SX_C'];
            $SIX_Turn_SX_D = $row_1['SIX_Turn_SX_D'];
            $SIX_SX_Bet = $row_1['SIX_SX_Bet'];
            $SIX_SX_Scene = $row_1['SIX_SX_Scene'];
            $SIX_Turn_HW_A = $row_1['SIX_Turn_HW_A'];
            $SIX_Turn_HW_B = $row_1['SIX_Turn_HW_B'];
            $SIX_Turn_HW_C = $row_1['SIX_Turn_HW_C'];
            $SIX_Turn_HW_D = $row_1['SIX_Turn_HW_D'];
            $SIX_HW_Bet = $row_1['SIX_HW_Bet'];
            $SIX_HW_Scene = $row_1['SIX_HW_Scene'];
            $SIX_Turn_MT_A = $row_1['SIX_Turn_MT_A'];
            $SIX_Turn_MT_B = $row_1['SIX_Turn_MT_B'];
            $SIX_Turn_MT_C = $row_1['SIX_Turn_MT_C'];
            $SIX_Turn_MT_D = $row_1['SIX_Turn_MT_D'];
            $SIX_MT_Bet = $row_1['SIX_MT_Bet'];
            $SIX_MT_Scene = $row_1['SIX_MT_Scene'];
            $SIX_Turn_M_A = $row_1['SIX_Turn_M_A'];
            $SIX_Turn_M_B = $row_1['SIX_Turn_M_B'];
            $SIX_Turn_M_C = $row_1['SIX_Turn_M_C'];
            $SIX_Turn_M_D = $row_1['SIX_Turn_M_D'];
            $SIX_M_Bet = $row_1['SIX_M_Bet'];
            $SIX_M_Scene = $row_1['SIX_M_Scene'];
            $SIX_Turn_EC_A = $row_1['SIX_Turn_EC_A'];
            $SIX_Turn_EC_B = $row_1['SIX_Turn_EC_B'];
            $SIX_Turn_EC_C = $row_1['SIX_Turn_EC_C'];
            $SIX_Turn_EC_D = $row_1['SIX_Turn_EC_D'];
            $SIX_EC_Bet = $row_1['SIX_EC_Bet'];
            $SIX_EC_Scene = $row_1['SIX_EC_Scene'];
            $FT_VR_Bet = $row_1['FT_VR_Bet'];
            $FT_VR_Scene = $row_1['FT_VR_Scene'];

            if ($lv == "MEM") {

                $FT_Turn_R_A = $row_1['FT_Turn_R_A'];
                $FT_Turn_R_B = $row_1['FT_Turn_R_B'];
                $FT_Turn_R_C = $row_1['FT_Turn_R_C'];
                $FT_Turn_R_D = $row_1['FT_Turn_R_D'];
                $FT_R_Bet = $row_1['FT_R_Bet'];
                $FT_R_Scene = $row_1['FT_R_Scene'];
                $FT_Turn_OU_A = $row_1['FT_Turn_OU_A'];
                $FT_Turn_OU_B = $row_1['FT_Turn_OU_B'];
                $FT_Turn_OU_C = $row_1['FT_Turn_OU_C'];
                $FT_Turn_OU_D = $row_1['FT_Turn_OU_D'];
                $FT_OU_Bet = $row_1['FT_OU_Bet'];
                $FT_OU_Scene = $row_1['FT_OU_Scene'];
                $FT_Turn_VR_A = $row_1['FT_Turn_VR_A'];
                $FT_Turn_VR_B = $row_1['FT_Turn_VR_B'];
                $FT_Turn_VR_C = $row_1['FT_Turn_VR_C'];
                $FT_Turn_VR_D = $row_1['FT_Turn_VR_D'];
                $FT_VR_Bet = $row_1['FT_VR_Bet'];
                $FT_VR_Scene = $row_1['FT_VR_Scene'];
                $FT_Turn_VOU_A = $row_1['FT_Turn_VOU_A'];
                $FT_Turn_VOU_B = $row_1['FT_Turn_VOU_B'];
                $FT_Turn_VOU_C = $row_1['FT_Turn_VOU_C'];
                $FT_Turn_VOU_D = $row_1['FT_Turn_VOU_D'];
                $FT_VOU_Bet = $row_1['FT_VOU_Bet'];
                $FT_VOU_Scene = $row_1['FT_VOU_Scene'];
                $FT_Turn_RE_A = $row_1['FT_Turn_RE_A'];
                $FT_Turn_RE_B = $row_1['FT_Turn_RE_B'];
                $FT_Turn_RE_C = $row_1['FT_Turn_RE_C'];
                $FT_Turn_RE_D = $row_1['FT_Turn_RE_D'];
                $FT_RE_Bet = $row_1['FT_RE_Bet'];
                $FT_RE_Scene = $row_1['FT_RE_Scene'];
                $FT_Turn_ROU_A = $row_1['FT_Turn_ROU_A'];
                $FT_Turn_ROU_B = $row_1['FT_Turn_ROU_B'];
                $FT_Turn_ROU_C = $row_1['FT_Turn_ROU_C'];
                $FT_Turn_ROU_D = $row_1['FT_Turn_ROU_D'];
                $FT_ROU_Bet = $row_1['FT_ROU_Bet'];
                $FT_ROU_Scene = $row_1['FT_ROU_Scene'];
                $FT_Turn_VRE_A = $row_1['FT_Turn_VRE_A'];
                $FT_Turn_VRE_B = $row_1['FT_Turn_VRE_B'];
                $FT_Turn_VRE_C = $row_1['FT_Turn_VRE_C'];
                $FT_Turn_VRE_D = $row_1['FT_Turn_VRE_D'];
                $FT_VRE_Bet = $row_1['FT_VRE_Bet'];
                $FT_VRE_Scene = $row_1['FT_VRE_Scene'];
                $FT_Turn_VROU_A = $row_1['FT_Turn_VROU_A'];
                $FT_Turn_VROU_B = $row_1['FT_Turn_VROU_B'];
                $FT_Turn_VROU_C = $row_1['FT_Turn_VROU_C'];
                $FT_Turn_VROU_D = $row_1['FT_Turn_VROU_D'];
                $FT_VROU_Bet = $row_1['FT_VROU_Bet'];
                $FT_VROU_Scene = $row_1['FT_VROU_Scene'];
                $FT_Turn_EO_A = $row_1['FT_Turn_EO_A'];
                $FT_Turn_EO_B = $row_1['FT_Turn_EO_B'];
                $FT_Turn_EO_C = $row_1['FT_Turn_EO_C'];
                $FT_Turn_EO_D = $row_1['FT_Turn_EO_D'];
                $FT_EO_Bet = $row_1['FT_EO_Bet'];
                $FT_EO_Scene = $row_1['FT_EO_Scene'];
                $FT_Turn_RM = $row_1['FT_Turn_RM'];
                $FT_RM_Bet = $row_1['FT_RM_Bet'];
                $FT_RM_Scene = $row_1['FT_RM_Scene'];
                $FT_Turn_M = $row_1['FT_Turn_M'];
                $FT_M_Bet = $row_1['FT_M_Bet'];
                $FT_M_Scene = $row_1['FT_M_Scene'];
                $FT_Turn_PD = $row_1['FT_Turn_PD'];
                $FT_PD_Bet = $row_1['FT_PD_Bet'];
                $FT_PD_Scene = $row_1['FT_PD_Scene'];
                $FT_Turn_T = $row_1['FT_Turn_T'];
                $FT_T_Bet = $row_1['FT_T_Bet'];
                $FT_T_Scene = $row_1['FT_T_Scene'];
                $FT_Turn_F = $row_1['FT_Turn_F'];
                $FT_F_Bet = $row_1['FT_F_Bet'];
                $FT_F_Scene = $row_1['FT_F_Scene'];
                $FT_Turn_PR = $row_1['FT_Turn_PR'];
                $FT_PR_Bet = $row_1['FT_PR_Bet'];
                $FT_PR_Scene = $row_1['FT_PR_Scene'];

                $BK_Turn_R_A = $row_1['BK_Turn_R_A'];
                $BK_Turn_R_B = $row_1['BK_Turn_R_B'];
                $BK_Turn_R_C = $row_1['BK_Turn_R_C'];
                $BK_Turn_R_D = $row_1['BK_Turn_R_D'];
                $BK_R_Bet = $row_1['BK_R_Bet'];
                $BK_R_Scene = $row_1['BK_R_Scene'];
                $BK_Turn_OU_A = $row_1['BK_Turn_OU_A'];
                $BK_Turn_OU_B = $row_1['BK_Turn_OU_B'];
                $BK_Turn_OU_C = $row_1['BK_Turn_OU_C'];
                $BK_Turn_OU_D = $row_1['BK_Turn_OU_D'];
                $BK_OU_Bet = $row_1['BK_OU_Bet'];
                $BK_OU_Scene = $row_1['BK_OU_Scene'];
                $BK_Turn_VR_A = $row_1['BK_Turn_VR_A'];
                $BK_Turn_VR_B = $row_1['BK_Turn_VR_B'];
                $BK_Turn_VR_C = $row_1['BK_Turn_VR_C'];
                $BK_Turn_VR_D = $row_1['BK_Turn_VR_D'];
                $BK_VR_Bet = $row_1['BK_VR_Bet'];
                $BK_VR_Scene = $row_1['BK_VR_Scene'];
                $BK_Turn_VOU_A = $row_1['BK_Turn_VOU_A'];
                $BK_Turn_VOU_B = $row_1['BK_Turn_VOU_B'];
                $BK_Turn_VOU_C = $row_1['BK_Turn_VOU_C'];
                $BK_Turn_VOU_D = $row_1['BK_Turn_VOU_D'];
                $BK_VOU_Bet = $row_1['BK_VOU_Bet'];
                $BK_VOU_Scene = $row_1['BK_VOU_Scene'];

                $BK_Turn_RE_A = $row_1['BK_Turn_RE_A'];
                $BK_Turn_RE_B = $row_1['BK_Turn_RE_B'];
                $BK_Turn_RE_C = $row_1['BK_Turn_RE_C'];
                $BK_Turn_RE_D = $row_1['BK_Turn_RE_D'];
                $BK_RE_Bet = $row_1['BK_RE_Bet'];
                $BK_RE_Scene = $row_1['BK_RE_Scene'];
                $BK_Turn_ROU_A = $row_1['BK_Turn_ROU_A'];
                $BK_Turn_ROU_B = $row_1['BK_Turn_ROU_B'];
                $BK_Turn_ROU_C = $row_1['BK_Turn_ROU_C'];
                $BK_Turn_ROU_D = $row_1['BK_Turn_ROU_D'];
                $BK_ROU_Bet = $row_1['BK_ROU_Bet'];
                $BK_ROU_Scene = $row_1['BK_ROU_Scene'];
                $BK_Turn_VRE_A = $row_1['BK_Turn_VRE_A'];
                $BK_Turn_VRE_B = $row_1['BK_Turn_VRE_B'];
                $BK_Turn_VRE_C = $row_1['BK_Turn_VRE_C'];
                $BK_Turn_VRE_D = $row_1['BK_Turn_VRE_D'];
                $BK_VRE_Bet = $row_1['BK_VRE_Bet'];
                $BK_VRE_Scene = $row_1['BK_VRE_Scene'];
                $BK_Turn_VROU_A = $row_1['BK_Turn_VROU_A'];
                $BK_Turn_VROU_B = $row_1['BK_Turn_VROU_B'];
                $BK_Turn_VROU_C = $row_1['BK_Turn_VROU_C'];
                $BK_Turn_VROU_D = $row_1['BK_Turn_VROU_D'];
                $BK_VROU_Bet = $row_1['BK_VROU_Bet'];
                $BK_VROU_Scene = $row_1['BK_VROU_Scene'];
                $BK_Turn_EO_A = $row_1['BK_Turn_EO_A'];
                $BK_Turn_EO_B = $row_1['BK_Turn_EO_B'];
                $BK_Turn_EO_C = $row_1['BK_Turn_EO_C'];
                $BK_Turn_EO_D = $row_1['BK_Turn_EO_D'];
                $BK_EO_Bet = $row_1['BK_EO_Bet'];
                $BK_EO_Scene = $row_1['BK_EO_Scene'];
                $BK_Turn_PR = $row_1['BK_Turn_PR'];
                $BK_PR_Bet = $row_1['BK_PR_Bet'];
                $BK_PR_Scene = $row_1['BK_PR_Scene'];

                $BS_Turn_R_A = $row_1['BS_Turn_R_A'];
                $BS_Turn_R_B = $row_1['BS_Turn_R_B'];
                $BS_Turn_R_C = $row_1['BS_Turn_R_C'];
                $BS_Turn_R_D = $row_1['BS_Turn_R_D'];
                $BS_R_Bet = $row_1['BS_R_Bet'];
                $BS_R_Scene = $row_1['BS_R_Scene'];
                $BS_Turn_OU_A = $row_1['BS_Turn_OU_A'];
                $BS_Turn_OU_B = $row_1['BS_Turn_OU_B'];
                $BS_Turn_OU_C = $row_1['BS_Turn_OU_C'];
                $BS_Turn_OU_D = $row_1['BS_Turn_OU_D'];
                $BS_OU_Bet = $row_1['BS_OU_Bet'];
                $BS_OU_Scene = $row_1['BS_OU_Scene'];
                $BS_Turn_VR_A = $row_1['BS_Turn_VR_A'];
                $BS_Turn_VR_B = $row_1['BS_Turn_VR_B'];
                $BS_Turn_VR_C = $row_1['BS_Turn_VR_C'];
                $BS_Turn_VR_D = $row_1['BS_Turn_VR_D'];
                $BS_VR_Bet = $row_1['BS_VR_Bet'];
                $BS_VR_Scene = $row_1['BS_VR_Scene'];
                $BS_Turn_VOU_A = $row_1['BS_Turn_VOU_A'];
                $BS_Turn_VOU_B = $row_1['BS_Turn_VOU_B'];
                $BS_Turn_VOU_C = $row_1['BS_Turn_VOU_C'];
                $BS_Turn_VOU_D = $row_1['BS_Turn_VOU_D'];
                $BS_VOU_Bet = $row_1['BS_VOU_Bet'];
                $BS_VOU_Scene = $row_1['BS_VOU_Scene'];
                $BS_Turn_RE_A = $row_1['BS_Turn_RE_A'];
                $BS_Turn_RE_B = $row_1['BS_Turn_RE_B'];
                $BS_Turn_RE_C = $row_1['BS_Turn_RE_C'];
                $BS_Turn_RE_D = $row_1['BS_Turn_RE_D'];
                $BS_RE_Bet = $row_1['BS_RE_Bet'];
                $BS_RE_Scene = $row_1['BS_RE_Scene'];
                $BS_Turn_ROU_A = $row_1['BS_Turn_ROU_A'];
                $BS_Turn_ROU_B = $row_1['BS_Turn_ROU_B'];
                $BS_Turn_ROU_C = $row_1['BS_Turn_ROU_C'];
                $BS_Turn_ROU_D = $row_1['BS_Turn_ROU_D'];
                $BS_ROU_Bet = $row_1['BS_ROU_Bet'];
                $BS_ROU_Scene = $row_1['BS_ROU_Scene'];
                $BS_Turn_VRE_A = $row_1['BS_Turn_VRE_A'];
                $BS_Turn_VRE_B = $row_1['BS_Turn_VRE_B'];
                $BS_Turn_VRE_C = $row_1['BS_Turn_VRE_C'];
                $BS_Turn_VRE_D = $row_1['BS_Turn_VRE_D'];
                $BS_VRE_Bet = $row_1['BS_VRE_Bet'];
                $BS_VRE_Scene = $row_1['BS_VRE_Scene'];
                $BS_Turn_VROU_A = $row_1['BS_Turn_VROU_A'];
                $BS_Turn_VROU_B = $row_1['BS_Turn_VROU_B'];
                $BS_Turn_VROU_C = $row_1['BS_Turn_VROU_C'];
                $BS_Turn_VROU_D = $row_1['BS_Turn_VROU_D'];
                $BS_VROU_Bet = $row_1['BS_VROU_Bet'];
                $BS_VROU_Scene = $row_1['BS_VROU_Scene'];
                $BS_Turn_EO_A = $row_1['BS_Turn_EO_A'];
                $BS_Turn_EO_B = $row_1['BS_Turn_EO_B'];
                $BS_Turn_EO_C = $row_1['BS_Turn_EO_C'];
                $BS_Turn_EO_D = $row_1['BS_Turn_EO_D'];
                $BS_EO_Bet = $row_1['BS_EO_Bet'];
                $BS_EO_Scene = $row_1['BS_EO_Scene'];
                $BS_Turn_1X2_A = $row_1['BS_Turn_1X2_A'];
                $BS_Turn_1X2_B = $row_1['BS_Turn_1X2_B'];
                $BS_Turn_1X2_C = $row_1['BS_Turn_1X2_C'];
                $BS_Turn_1X2_D = $row_1['BS_Turn_1X2_D'];
                $BS_1X2_Bet = $row_1['BS_1X2_Bet'];
                $BS_1X2_Scene = $row_1['BS_1X2_Scene'];
                $BS_Turn_M = $row_1['BS_Turn_M'];
                $BS_M_Bet = $row_1['BS_M_Bet'];
                $BS_M_Scene = $row_1['BS_M_Scene'];
                $BS_Turn_PD = $row_1['BS_Turn_PD'];
                $BS_PD_Bet = $row_1['BS_PD_Bet'];
                $BS_PD_Scene = $row_1['BS_PD_Scene'];
                $BS_Turn_T = $row_1['BS_Turn_T'];
                $BS_T_Bet = $row_1['BS_T_Bet'];
                $BS_T_Scene = $row_1['BS_T_Scene'];
                $BS_Turn_P = $row_1['BS_Turn_P'];
                $BS_P_Bet = $row_1['BS_P_Bet'];
                $BS_P_Scene = $row_1['BS_P_Scene'];
                $BS_Turn_PR = $row_1['BS_Turn_PR'];
                $BS_PR_Bet = $row_1['BS_PR_Bet'];
                $BS_PR_Scene = $row_1['BS_PR_Scene'];

                $TN_Turn_R_A = $row_1['TN_Turn_R_A'];
                $TN_Turn_R_B = $row_1['TN_Turn_R_B'];
                $TN_Turn_R_C = $row_1['TN_Turn_R_C'];
                $TN_Turn_R_D = $row_1['TN_Turn_R_D'];
                $TN_R_Bet = $row_1['TN_R_Bet'];
                $TN_R_Scene = $row_1['TN_R_Scene'];
                $TN_Turn_OU_A = $row_1['TN_Turn_OU_A'];
                $TN_Turn_OU_B = $row_1['TN_Turn_OU_B'];
                $TN_Turn_OU_C = $row_1['TN_Turn_OU_C'];
                $TN_Turn_OU_D = $row_1['TN_Turn_OU_D'];
                $TN_OU_Bet = $row_1['TN_OU_Bet'];
                $TN_OU_Scene = $row_1['TN_OU_Scene'];
                $TN_Turn_RE_A = $row_1['TN_Turn_RE_A'];
                $TN_Turn_RE_B = $row_1['TN_Turn_RE_B'];
                $TN_Turn_RE_C = $row_1['TN_Turn_RE_C'];
                $TN_Turn_RE_D = $row_1['TN_Turn_RE_D'];
                $TN_RE_Bet = $row_1['TN_RE_Bet'];
                $TN_RE_Scene = $row_1['TN_RE_Scene'];
                $TN_Turn_ROU_A = $row_1['TN_Turn_ROU_A'];
                $TN_Turn_ROU_B = $row_1['TN_Turn_ROU_B'];
                $TN_Turn_ROU_C = $row_1['TN_Turn_ROU_C'];
                $TN_Turn_ROU_D = $row_1['TN_Turn_ROU_D'];
                $TN_ROU_Bet = $row_1['TN_ROU_Bet'];
                $TN_ROU_Scene = $row_1['TN_ROU_Scene'];
                $TN_Turn_EO_A = $row_1['TN_Turn_EO_A'];
                $TN_Turn_EO_B = $row_1['TN_Turn_EO_B'];
                $TN_Turn_EO_C = $row_1['TN_Turn_EO_C'];
                $TN_Turn_EO_D = $row_1['TN_Turn_EO_D'];
                $TN_EO_Bet = $row_1['TN_EO_Bet'];
                $TN_EO_Scene = $row_1['TN_EO_Scene'];
                $TN_Turn_M = $row_1['TN_Turn_M'];
                $TN_M_Bet = $row_1['TN_M_Bet'];
                $TN_M_Scene = $row_1['TN_M_Scene'];
                $TN_Turn_PD = $row_1['TN_Turn_PD'];
                $TN_PD_Scene = $row_1['TN_PD_Scene'];
                $TN_PD_Bet = $row_1['TN_PD_Bet'];
                $TN_Turn_P = $row_1['TN_Turn_P'];
                $TN_P_Scene = $row_1['TN_P_Scene'];
                $TN_P_Bet = $row_1['TN_P_Bet'];
                $TN_Turn_PR = $row_1['TN_Turn_PR'];
                $TN_PR_Bet = $row_1['TN_PR_Bet'];
                $TN_PR_Scene = $row_1['TN_PR_Scene'];

                $VB_Turn_R_A = $row_1['VB_Turn_R_A'];
                $VB_Turn_R_B = $row_1['VB_Turn_R_B'];
                $VB_Turn_R_C = $row_1['VB_Turn_R_C'];
                $VB_Turn_R_D = $row_1['VB_Turn_R_D'];
                $VB_R_Bet = $row_1['VB_R_Bet'];
                $VB_R_Scene = $row_1['VB_R_Scene'];
                $VB_Turn_OU_A = $row_1['VB_Turn_OU_A'];
                $VB_Turn_OU_B = $row_1['VB_Turn_OU_B'];
                $VB_Turn_OU_C = $row_1['VB_Turn_OU_C'];
                $VB_Turn_OU_D = $row_1['VB_Turn_OU_D'];
                $VB_OU_Bet = $row_1['VB_OU_Bet'];
                $VB_OU_Scene = $row_1['VB_OU_Scene'];
                $VB_Turn_RE_A = $row_1['VB_Turn_RE_A'];
                $VB_Turn_RE_B = $row_1['VB_Turn_RE_B'];
                $VB_Turn_RE_C = $row_1['VB_Turn_RE_C'];
                $VB_Turn_RE_D = $row_1['VB_Turn_RE_D'];
                $VB_RE_Bet = $row_1['VB_RE_Bet'];
                $VB_RE_Scene = $row_1['VB_RE_Scene'];
                $VB_Turn_ROU_A = $row_1['VB_Turn_ROU_A'];
                $VB_Turn_ROU_B = $row_1['VB_Turn_ROU_B'];
                $VB_Turn_ROU_C = $row_1['VB_Turn_ROU_C'];
                $VB_Turn_ROU_D = $row_1['VB_Turn_ROU_D'];
                $VB_ROU_Bet = $row_1['VB_ROU_Bet'];
                $VB_ROU_Scene = $row_1['VB_ROU_Scene'];
                $VB_Turn_EO_A = $row_1['VB_Turn_EO_A'];
                $VB_Turn_EO_B = $row_1['VB_Turn_EO_B'];
                $VB_Turn_EO_C = $row_1['VB_Turn_EO_C'];
                $VB_Turn_EO_D = $row_1['VB_Turn_EO_D'];
                $VB_EO_Bet = $row_1['VB_EO_Bet'];
                $VB_EO_Scene = $row_1['VB_EO_Scene'];
                $VB_Turn_M = $row_1['VB_Turn_M'];
                $VB_M_Bet = $row_1['VB_M_Bet'];
                $VB_M_Scene = $row_1['VB_M_Scene'];
                $VB_Turn_PD = $row_1['VB_Turn_PD'];
                $VB_PD_Bet = $row_1['VB_PD_Bet'];
                $VB_PD_Scene = $row_1['VB_PD_Scene'];
                $VB_Turn_P = $row_1['VB_Turn_P'];
                $VB_P_Bet = $row_1['VB_P_Bet'];
                $VB_P_Scene = $row_1['VB_P_Scene'];
                $VB_Turn_PR = $row_1['VB_Turn_PR'];
                $VB_PR_Bet = $row_1['VB_PR_Bet'];
                $VB_PR_Scene = $row_1['VB_PR_Scene'];

                $OP_Turn_R_A = $row_1['OP_Turn_R_A'];
                $OP_Turn_R_B = $row_1['OP_Turn_R_B'];
                $OP_Turn_R_C = $row_1['OP_Turn_R_C'];
                $OP_Turn_R_D = $row_1['OP_Turn_R_D'];
                $OP_R_Bet = $row_1['OP_R_Bet'];
                $OP_R_Scene = $row_1['OP_R_Scene'];
                $OP_Turn_OU_A = $row_1['OP_Turn_OU_A'];
                $OP_Turn_OU_B = $row_1['OP_Turn_OU_B'];
                $OP_Turn_OU_C = $row_1['OP_Turn_OU_C'];
                $OP_Turn_OU_D = $row_1['OP_Turn_OU_D'];
                $OP_OU_Bet = $row_1['OP_OU_Bet'];
                $OP_OU_Scene = $row_1['OP_OU_Scene'];
                $OP_Turn_VR_A = $row_1['OP_Turn_VR_A'];
                $OP_Turn_VR_B = $row_1['OP_Turn_VR_B'];
                $OP_Turn_VR_C = $row_1['OP_Turn_VR_C'];
                $OP_Turn_VR_D = $row_1['OP_Turn_VR_D'];
                $OP_VR_Bet = $row_1['OP_VR_Bet'];
                $OP_VR_Scene = $row_1['OP_VR_Scene'];
                $OP_Turn_VOU_A = $row_1['OP_Turn_VOU_A'];
                $OP_Turn_VOU_B = $row_1['OP_Turn_VOU_B'];
                $OP_Turn_VOU_C = $row_1['OP_Turn_VOU_C'];
                $OP_Turn_VOU_D = $row_1['OP_Turn_VOU_D'];
                $OP_VOU_Bet = $row_1['OP_VOU_Bet'];
                $OP_VOU_Scene = $row_1['OP_VOU_Scene'];
                $OP_Turn_RE_A = $row_1['OP_Turn_RE_A'];
                $OP_Turn_RE_B = $row_1['OP_Turn_RE_B'];
                $OP_Turn_RE_C = $row_1['OP_Turn_RE_C'];
                $OP_Turn_RE_D = $row_1['OP_Turn_RE_D'];
                $OP_RE_Bet = $row_1['OP_RE_Bet'];
                $OP_RE_Scene = $row_1['OP_RE_Scene'];
                $OP_Turn_ROU_A = $row_1['OP_Turn_ROU_A'];
                $OP_Turn_ROU_B = $row_1['OP_Turn_ROU_B'];
                $OP_Turn_ROU_C = $row_1['OP_Turn_ROU_C'];
                $OP_Turn_ROU_D = $row_1['OP_Turn_ROU_D'];
                $OP_ROU_Bet = $row_1['OP_ROU_Bet'];
                $OP_ROU_Scene = $row_1['OP_ROU_Scene'];
                $OP_Turn_VRE_A = $row_1['OP_Turn_VRE_A'];
                $OP_Turn_VRE_B = $row_1['OP_Turn_VRE_B'];
                $OP_Turn_VRE_C = $row_1['OP_Turn_VRE_C'];
                $OP_Turn_VRE_D = $row_1['OP_Turn_VRE_D'];
                $OP_VRE_Bet = $row_1['OP_VRE_Bet'];
                $OP_VRE_Scene = $row_1['OP_VRE_Scene'];
                $OP_Turn_VROU_A = $row_1['OP_Turn_VROU_A'];
                $OP_Turn_VROU_B = $row_1['OP_Turn_VROU_B'];
                $OP_Turn_VROU_C = $row_1['OP_Turn_VROU_C'];
                $OP_Turn_VROU_D = $row_1['OP_Turn_VROU_D'];
                $OP_VROU_Bet = $row_1['OP_VROU_Bet'];
                $OP_VROU_Scene = $row_1['OP_VROU_Scene'];
                $OP_Turn_EO_A = $row_1['OP_Turn_EO_A'];
                $OP_Turn_EO_B = $row_1['OP_Turn_EO_B'];
                $OP_Turn_EO_C = $row_1['OP_Turn_EO_C'];
                $OP_Turn_EO_D = $row_1['OP_Turn_EO_D'];
                $OP_EO_Bet = $row_1['OP_EO_Bet'];
                $OP_EO_Scene = $row_1['OP_EO_Scene'];
                $OP_Turn_M = $row_1['OP_Turn_M'];
                $OP_M_Bet = $row_1['OP_M_Bet'];
                $OP_M_Scene = $row_1['OP_M_Scene'];
                $OP_Turn_PD = $row_1['OP_Turn_PD'];
                $OP_PD_Bet = $row_1['OP_PD_Bet'];
                $OP_PD_Scene = $row_1['OP_PD_Scene'];
                $OP_Turn_T = $row_1['OP_Turn_T'];
                $OP_T_Bet = $row_1['OP_T_Bet'];
                $OP_T_Scene = $row_1['OP_T_Scene'];
                $OP_Turn_F = $row_1['OP_Turn_F'];
                $OP_F_Bet = $row_1['OP_F_Bet'];
                $OP_F_Scene = $row_1['OP_F_Scene'];
                $OP_Turn_P = $row_1['OP_Turn_P'];
                $OP_P_Bet = $row_1['OP_P_Bet'];
                $OP_P_Scene = $row_1['OP_P_Scene'];
                $OP_Turn_PR = $row_1['OP_Turn_PR'];
                $OP_PR_Bet = $row_1['OP_PR_Bet'];
                $OP_PR_Scene = $row_1['OP_PR_Scene'];

                $FU_Turn_OU_A = $row_1['FU_Turn_OU_A'];
                $FU_Turn_OU_B = $row_1['FU_Turn_OU_B'];
                $FU_Turn_OU_C = $row_1['FU_Turn_OU_C'];
                $FU_Turn_OU_D = $row_1['FU_Turn_OU_D'];
                $FU_OU_Bet = $row_1['FU_OU_Bet'];
                $FU_OU_Scene = $row_1['FU_OU_Scene'];
                $FU_Turn_EO_A = $row_1['FU_Turn_EO_A'];
                $FU_Turn_EO_B = $row_1['FU_Turn_EO_B'];
                $FU_Turn_EO_C = $row_1['FU_Turn_EO_C'];
                $FU_Turn_EO_D = $row_1['FU_Turn_EO_D'];
                $FU_EO_Bet = $row_1['FU_EO_Bet'];
                $FU_EO_Scene = $row_1['FU_EO_Scene'];
                $FU_Turn_PD = $row_1['FU_Turn_PD'];
                $FU_PD_Bet = $row_1['FU_PD_Bet'];
                $FU_PD_Scene = $row_1['FU_PD_Scene'];

                $FS_Turn_FS = $row_1['FS_Turn_FS'];
                $FS_FS_Bet = $row_1['FS_FS_Bet'];
                $FS_FS_Scene = $row_1['FS_FS_Scene'];

                $type = 'C';
                if ($AddDate == "") {
                    $AddDate = date('Y-m-d H:i:s');
                }
                //新增日期
                $ip_addr = "127.0.0.1";
                $notes = ""; //来源

                $username = trim($username);
                $password = trim($password);

                $msql = "select * from web_member_data where UserName='$username'";
                $mresult = DB::select($msql);
                $mcou = count($mresult);

                if ($mcou > 0) {
                    $response["message"] = "您输入的帐号 $username 已经有人使用了，请回上一页重新输入";
                    return response()->json($response, $response['status']);
                }
                $phone = "";
                $a = 1;
                $status = 0;
                $bankname = "";
                $bankaddress = "";
                $bankno = "";
                $e_mail = "";

                $sql = "insert into web_member_data set ";
                $sql .= "UserName='" . $username . "',";
                $sql .= "LoginName='" . $loginname . "',";
                $sql .= "PassWord='" . $password . "',";
                $sql .= "Credit=0,";
                $sql .= "Money=0,";
                $sql .= "Alias='" . $alias . "',";
                $sql .= "Sports='" . $sports . "',";
                $sql .= "Lottery='" . $lottery . "',";
                $sql .= "AddDate='" . $AddDate . "',";
                $sql .= "Status='$status',";
                $sql .= "CurType='RMB',";
                $sql .= "Pay_Type='1',";
                $sql .= "Opentype='C',";
                $sql .= "Agents='" . $agent . "',";
                $sql .= "World='" . $world . "',";
                $sql .= "Corprator='" . $corprator . "',";
                $sql .= "Super='" . $super . "',";
                $sql .= "Admin='" . $admin . "',";
                $sql .= "Phone='" . $phone . "',";
                $sql .= "Notes='" . $notes . "',";
                $sql .= "Address='" . $address . "',";
                $sql .= "LoginIP='" . $ip_addr . "',";
                $sql .= "Reg='1',";
                $sql .= "FT_Turn_R='" . $a . "',";
                $sql .= "FT_R_Bet='" . $FT_R_Bet . "',";
                $sql .= "FT_R_Scene='" . $FT_R_Scene . "',";
                $sql .= "FT_Turn_OU='" . $a . "',";
                $sql .= "FT_OU_Bet='" . $FT_OU_Bet . "',";
                $sql .= "FT_OU_Scene='" . $FT_OU_Scene . "',";
                $sql .= "FT_Turn_VR='" . $a . "',";
                $sql .= "FT_VR_Bet='" . $FT_VR_Bet . "',";
                $sql .= "FT_VR_Scene='" . $FT_VR_Scene . "',";
                $sql .= "FT_Turn_VOU='" . $a . "',";
                $sql .= "FT_VOU_Bet='" . $FT_VOU_Bet . "',";
                $sql .= "FT_VOU_Scene='" . $FT_VOU_Scene . "',";
                $sql .= "FT_Turn_RE='" . $a . "',";
                $sql .= "FT_RE_Bet='" . $FT_RE_Bet . "',";
                $sql .= "FT_RE_Scene='" . $FT_RE_Scene . "',";
                $sql .= "FT_Turn_ROU='" . $a . "',";
                $sql .= "FT_ROU_Bet='" . $FT_ROU_Bet . "',";
                $sql .= "FT_ROU_Scene='" . $FT_ROU_Scene . "',";
                $sql .= "FT_Turn_VRE='" . $a . "',";
                $sql .= "FT_VRE_Bet='" . $FT_VRE_Bet . "',";
                $sql .= "FT_VRE_Scene='" . $FT_VRE_Scene . "',";
                $sql .= "FT_Turn_VROU='" . $a . "',";
                $sql .= "FT_VROU_Bet='" . $FT_VROU_Bet . "',";
                $sql .= "FT_VROU_Scene='" . $FT_VROU_Scene . "',";
                $sql .= "FT_Turn_EO='" . $a . "',";
                $sql .= "FT_EO_Bet='" . $FT_EO_Bet . "',";
                $sql .= "FT_EO_Scene='" . $FT_EO_Scene . "',";
                $sql .= "FT_Turn_RM='" . $FT_Turn_RM . "',";
                $sql .= "FT_RM_Bet='" . $FT_RM_Bet . "',";
                $sql .= "FT_RM_Scene='" . $FT_RM_Scene . "',";
                $sql .= "FT_Turn_M='" . $FT_Turn_M . "',";
                $sql .= "FT_M_Bet='" . $FT_M_Bet . "',";
                $sql .= "FT_M_Scene='" . $FT_M_Scene . "',";
                $sql .= "FT_Turn_PD='" . $FT_Turn_PD . "',";
                $sql .= "FT_PD_Bet='" . $FT_PD_Bet . "',";
                $sql .= "FT_PD_Scene='" . $FT_PD_Scene . "',";
                $sql .= "FT_Turn_T='" . $FT_Turn_T . "',";
                $sql .= "FT_T_Bet='" . $FT_T_Bet . "',";
                $sql .= "FT_T_Scene='" . $FT_T_Scene . "',";
                $sql .= "FT_Turn_F='" . $FT_Turn_F . "',";
                $sql .= "FT_F_Bet='" . $FT_F_Bet . "',";
                $sql .= "FT_F_Scene='" . $FT_F_Scene . "',";
                $sql .= "FT_Turn_PR='" . $FT_Turn_PR . "',";
                $sql .= "FT_PR_Bet='" . $FT_PR_Bet . "',";
                $sql .= "FT_PR_Scene='" . $FT_PR_Scene . "',";

                $sql .= "BK_Turn_R='" . $a . "',";
                $sql .= "BK_R_Bet='" . $BK_R_Bet . "',";
                $sql .= "BK_R_Scene='" . $BK_R_Scene . "',";
                $sql .= "BK_Turn_OU='" . $a . "',";
                $sql .= "BK_OU_Bet='" . $BK_OU_Bet . "',";
                $sql .= "BK_OU_Scene='" . $BK_OU_Scene . "',";
                $sql .= "BK_Turn_VR='" . $a . "',";
                $sql .= "BK_VR_Bet='" . $BK_VR_Bet . "',";
                $sql .= "BK_VR_Scene='" . $BK_VR_Scene . "',";
                $sql .= "BK_Turn_VOU='" . $a . "',";
                $sql .= "BK_VOU_Bet='" . $BK_VOU_Bet . "',";
                $sql .= "BK_VOU_Scene='" . $BK_VOU_Scene . "',";
                $sql .= "BK_Turn_RE='" . $a . "',";
                $sql .= "BK_RE_Bet='" . $BK_RE_Bet . "',";
                $sql .= "BK_RE_Scene='" . $BK_RE_Scene . "',";
                $sql .= "BK_Turn_ROU='" . $a . "',";
                $sql .= "BK_ROU_Bet='" . $BK_ROU_Bet . "',";
                $sql .= "BK_ROU_Scene='" . $BK_ROU_Scene . "',";
                $sql .= "BK_Turn_VRE='" . $a . "',";
                $sql .= "BK_VRE_Bet='" . $BK_VRE_Bet . "',";
                $sql .= "BK_VRE_Scene='" . $BK_VRE_Scene . "',";
                $sql .= "BK_Turn_VROU='" . $a . "',";
                $sql .= "BK_VROU_Bet='" . $BK_VROU_Bet . "',";
                $sql .= "BK_VROU_Scene='" . $BK_VROU_Scene . "',";
                $sql .= "BK_Turn_EO='" . $a . "',";
                $sql .= "BK_EO_Bet='" . $BK_EO_Bet . "',";
                $sql .= "BK_EO_Scene='" . $BK_EO_Scene . "',";
                $sql .= "BK_Turn_PR='" . $BK_Turn_PR . "',";
                $sql .= "BK_PR_Bet='" . $BK_PR_Bet . "',";
                $sql .= "BK_PR_Scene='" . $BK_PR_Scene . "',";

                $a = "BS_Turn_R_$type";
                $sql .= "BS_Turn_R='" . $a . "',";
                $sql .= "BS_R_Bet='" . $BS_R_Bet . "',";
                $sql .= "BS_R_Scene='" . $BS_R_Scene . "',";
                $a = "BS_Turn_OU_$type";
                $sql .= "BS_Turn_OU='" . $a . "',";
                $sql .= "BS_OU_Scene='" . $BS_OU_Scene . "',";
                $sql .= "BS_OU_Bet='" . $BS_OU_Bet . "',";
                $a = "BS_Turn_VR_$type";
                $sql .= "BS_Turn_VR='" . $a . "',";
                $sql .= "BS_VR_Bet='" . $BS_VR_Bet . "',";
                $sql .= "BS_VR_Scene='" . $BS_VR_Scene . "',";
                $a = "BS_Turn_VOU_$type";
                $sql .= "BS_Turn_VOU='" . $a . "',";
                $sql .= "BS_VOU_Scene='" . $BS_VOU_Scene . "',";
                $sql .= "BS_VOU_Bet='" . $BS_VOU_Bet . "',";
                $a = "BS_Turn_RE_$type";
                $sql .= "BS_Turn_RE='" . $a . "',";
                $sql .= "BS_RE_Bet='" . $BS_RE_Bet . "',";
                $sql .= "BS_RE_Scene='" . $BS_RE_Scene . "',";
                $a = "BS_Turn_ROU_$type";
                $sql .= "BS_Turn_ROU='" . $a . "',";
                $sql .= "BS_ROU_Bet='" . $BS_ROU_Bet . "',";
                $sql .= "BS_ROU_Scene='" . $BS_ROU_Scene . "',";
                $a = "BS_Turn_VRE_$type";
                $sql .= "BS_Turn_VRE='" . $a . "',";
                $sql .= "BS_VRE_Bet='" . $BS_VRE_Bet . "',";
                $sql .= "BS_VRE_Scene='" . $BS_VRE_Scene . "',";
                $a = "BS_Turn_VROU_$type";
                $sql .= "BS_Turn_VROU='" . $a . "',";
                $sql .= "BS_VROU_Bet='" . $BS_VROU_Bet . "',";
                $sql .= "BS_VROU_Scene='" . $BS_VROU_Scene . "',";
                $a = "BS_Turn_EO_$type";
                $sql .= "BS_Turn_EO='" . $a . "',";
                $sql .= "BS_EO_Bet='" . $BS_EO_Bet . "',";
                $sql .= "BS_EO_Scene='" . $BS_EO_Scene . "',";
                $a = "BS_Turn_1X2_$type";
                $sql .= "BS_Turn_1X2='" . $a . "',";
                $sql .= "BS_1X2_Bet='" . $BS_1X2_Bet . "',";
                $sql .= "BS_1X2_Scene='" . $BS_1X2_Scene . "',";
                $sql .= "BS_Turn_M='" . $BS_Turn_M . "',";
                $sql .= "BS_M_Bet='" . $BS_M_Bet . "',";
                $sql .= "BS_M_Scene='" . $BS_M_Scene . "',";
                $sql .= "BS_Turn_PD='" . $BS_Turn_PD . "',";
                $sql .= "BS_PD_Bet='" . $BS_PD_Bet . "',";
                $sql .= "BS_PD_Scene='" . $BS_PD_Scene . "',";
                $sql .= "BS_Turn_T='" . $BS_Turn_T . "',";
                $sql .= "BS_T_Bet='" . $BS_T_Bet . "',";
                $sql .= "BS_T_Scene='" . $BS_T_Scene . "',";
                $sql .= "BS_Turn_P='" . $BS_Turn_P . "',";
                $sql .= "BS_P_Bet='" . $BS_P_Bet . "',";
                $sql .= "BS_P_Scene='" . $BS_P_Scene . "',";
                $sql .= "BS_Turn_PR='" . $BS_Turn_PR . "',";
                $sql .= "BS_PR_Bet='" . $BS_PR_Bet . "',";
                $sql .= "BS_PR_Scene='" . $BS_PR_Scene . "',";

                $a = "TN_Turn_R_$type";
                $sql .= "TN_Turn_R='" . $a . "',";
                $sql .= "TN_R_Bet='" . $TN_R_Bet . "',";
                $sql .= "TN_R_Scene='" . $TN_R_Scene . "',";
                $a = "TN_Turn_OU_$type";
                $sql .= "TN_Turn_OU='" . $a . "',";
                $sql .= "TN_OU_Bet='" . $TN_OU_Bet . "',";
                $sql .= "TN_OU_Scene='" . $TN_OU_Scene . "',";
                $a = "TN_Turn_RE_$type";
                $sql .= "TN_Turn_RE='" . $a . "',";
                $sql .= "TN_RE_Bet='" . $TN_RE_Bet . "',";
                $sql .= "TN_RE_Scene='" . $TN_RE_Scene . "',";
                $a = "TN_Turn_ROU_$type";
                $sql .= "TN_Turn_ROU='" . $a . "',";
                $sql .= "TN_ROU_Bet='" . $TN_ROU_Bet . "',";
                $sql .= "TN_ROU_Scene='" . $TN_ROU_Scene . "',";
                $a = "TN_Turn_EO_$type";
                $sql .= "TN_Turn_EO='" . $a . "',";
                $sql .= "TN_EO_Bet='" . $TN_EO_Bet . "',";
                $sql .= "TN_EO_Scene='" . $TN_EO_Scene . "',";
                $sql .= "TN_Turn_M='" . $TN_Turn_M . "',";
                $sql .= "TN_M_Bet='" . $TN_M_Bet . "',";
                $sql .= "TN_M_Scene='" . $TN_M_Scene . "',";
                $sql .= "TN_Turn_PD='" . $TN_Turn_PD . "',";
                $sql .= "TN_PD_Bet='" . $TN_PD_Bet . "',";
                $sql .= "TN_PD_Scene='" . $TN_PD_Scene . "',";
                $sql .= "TN_Turn_P='" . $TN_Turn_P . "',";
                $sql .= "TN_P_Bet='" . $TN_P_Bet . "',";
                $sql .= "TN_P_Scene='" . $TN_P_Scene . "',";
                $sql .= "TN_Turn_PR='" . $TN_Turn_PR . "',";
                $sql .= "TN_PR_Bet='" . $TN_PR_Bet . "',";
                $sql .= "TN_PR_Scene='" . $TN_PR_Scene . "',";

                $a = "VB_Turn_R_$type";
                $sql .= "VB_Turn_R='" . $a . "',";
                $sql .= "VB_R_Bet='" . $VB_R_Bet . "',";
                $sql .= "VB_R_Scene='" . $VB_R_Scene . "',";
                $a = "VB_Turn_OU_$type";
                $sql .= "VB_Turn_OU='" . $a . "',";
                $sql .= "VB_OU_Bet='" . $VB_OU_Bet . "',";
                $sql .= "VB_OU_Scene='" . $VB_OU_Scene . "',";
                $a = "VB_Turn_RE_$type";
                $sql .= "VB_Turn_RE='" . $a . "',";
                $sql .= "VB_RE_Bet='" . $VB_RE_Bet . "',";
                $sql .= "VB_RE_Scene='" . $VB_RE_Scene . "',";
                $a = "VB_Turn_ROU_$type";
                $sql .= "VB_Turn_ROU='" . $a . "',";
                $sql .= "VB_ROU_Bet='" . $VB_ROU_Bet . "',";
                $sql .= "VB_ROU_Scene='" . $VB_ROU_Scene . "',";
                $a = "VB_Turn_EO_$type";
                $sql .= "VB_Turn_EO='" . $a . "',";
                $sql .= "VB_EO_Bet='" . $VB_EO_Bet . "',";
                $sql .= "VB_EO_Scene='" . $VB_EO_Scene . "',";
                $sql .= "VB_Turn_M='" . $VB_Turn_M . "',";
                $sql .= "VB_M_Bet='" . $VB_M_Bet . "',";
                $sql .= "VB_M_Scene='" . $VB_M_Scene . "',";
                $sql .= "VB_Turn_PD='" . $VB_Turn_PD . "',";
                $sql .= "VB_PD_Bet='" . $VB_PD_Bet . "',";
                $sql .= "VB_PD_Scene='" . $VB_PD_Scene . "',";
                $sql .= "VB_Turn_P='" . $VB_Turn_P . "',";
                $sql .= "VB_P_Bet='" . $VB_P_Bet . "',";
                $sql .= "VB_P_Scene='" . $VB_P_Scene . "',";
                $sql .= "VB_Turn_PR='" . $VB_Turn_PR . "',";
                $sql .= "VB_PR_Bet='" . $VB_PR_Bet . "',";
                $sql .= "VB_PR_Scene='" . $VB_PR_Scene . "',";

                $a = "OP_Turn_R_$type";
                $sql .= "OP_Turn_R='" . $a . "',";
                $sql .= "OP_R_Bet='" . $OP_R_Bet . "',";
                $sql .= "OP_R_Scene='" . $OP_R_Scene . "',";
                $a = "OP_Turn_OU_$type";
                $sql .= "OP_Turn_OU='" . $a . "',";
                $sql .= "OP_OU_Bet='" . $OP_OU_Bet . "',";
                $sql .= "OP_OU_Scene='" . $OP_OU_Scene . "',";
                $a = "OP_Turn_VR_$type";
                $sql .= "OP_Turn_VR='" . $a . "',";
                $sql .= "OP_VR_Bet='" . $OP_VR_Bet . "',";
                $sql .= "OP_VR_Scene='" . $OP_VR_Scene . "',";
                $a = "OP_Turn_VOU_$type";
                $sql .= "OP_Turn_VOU='" . $a . "',";
                $sql .= "OP_VOU_Bet='" . $OP_VOU_Bet . "',";
                $sql .= "OP_VOU_Scene='" . $OP_VOU_Scene . "',";
                $a = "OP_Turn_RE_$type";
                $sql .= "OP_Turn_RE='" . $a . "',";
                $sql .= "OP_RE_Bet='" . $OP_RE_Bet . "',";
                $sql .= "OP_RE_Scene='" . $OP_RE_Scene . "',";
                $a = "OP_Turn_ROU_$type";
                $sql .= "OP_Turn_ROU='" . $a . "',";
                $sql .= "OP_ROU_Bet='" . $OP_ROU_Bet . "',";
                $sql .= "OP_ROU_Scene='" . $OP_ROU_Scene . "',";
                $a = "OP_Turn_VRE_$type";
                $sql .= "OP_Turn_VRE='" . $a . "',";
                $sql .= "OP_VRE_Bet='" . $OP_VRE_Bet . "',";
                $sql .= "OP_VRE_Scene='" . $OP_VRE_Scene . "',";
                $a = "OP_Turn_VROU_$type";
                $sql .= "OP_Turn_VROU='" . $a . "',";
                $sql .= "OP_VROU_Bet='" . $OP_VROU_Bet . "',";
                $sql .= "OP_VROU_Scene='" . $OP_VROU_Scene . "',";
                $a = "OP_Turn_EO_$type";
                $sql .= "OP_Turn_EO='" . $a . "',";
                $sql .= "OP_EO_Bet='" . $OP_EO_Bet . "',";
                $sql .= "OP_EO_Scene='" . $OP_EO_Scene . "',";
                $sql .= "OP_Turn_M='" . $OP_Turn_M . "',";
                $sql .= "OP_M_Bet='" . $OP_M_Bet . "',";
                $sql .= "OP_M_Scene='" . $OP_M_Scene . "',";
                $sql .= "OP_Turn_PD='" . $OP_Turn_PD . "',";
                $sql .= "OP_PD_Bet='" . $OP_PD_Bet . "',";
                $sql .= "OP_PD_Scene='" . $OP_PD_Scene . "',";
                $sql .= "OP_Turn_T='" . $OP_Turn_T . "',";
                $sql .= "OP_T_Bet='" . $OP_T_Bet . "',";
                $sql .= "OP_T_Scene='" . $OP_T_Scene . "',";
                $sql .= "OP_Turn_F='" . $OP_Turn_F . "',";
                $sql .= "OP_F_Bet='" . $OP_F_Bet . "',";
                $sql .= "OP_F_Scene='" . $OP_F_Scene . "',";
                $sql .= "OP_Turn_P='" . $OP_Turn_P . "',";
                $sql .= "OP_P_Bet='" . $OP_P_Bet . "',";
                $sql .= "OP_P_Scene='" . $OP_P_Scene . "',";
                $sql .= "OP_Turn_PR='" . $OP_Turn_PR . "',";
                $sql .= "OP_PR_Bet='" . $OP_PR_Bet . "',";
                $sql .= "OP_PR_Scene='" . $OP_PR_Scene . "',";

                $a = "FU_Turn_OU_$type";
                $sql .= "FU_Turn_OU='" . $a . "',";
                $sql .= "FU_OU_Bet='" . $FU_OU_Bet . "',";
                $sql .= "FU_OU_Scene='" . $FU_OU_Scene . "',";
                $a = "FU_Turn_EO_$type";
                $sql .= "FU_Turn_EO='" . $a . "',";
                $sql .= "FU_EO_Bet='" . $FU_EO_Bet . "',";
                $sql .= "FU_EO_Scene='" . $FU_EO_Scene . "',";
                $sql .= "FU_Turn_PD='" . $FU_Turn_PD . "',";
                $sql .= "FU_PD_Bet='" . $FU_PD_Bet . "',";
                $sql .= "FU_PD_Scene='" . $FU_PD_Scene . "',";

                $sql .= "FS_Turn_FS='" . $FS_Turn_FS . "',";
                $sql .= "FS_FS_Bet='" . $FS_FS_Bet . "',";
                $sql .= "FS_FS_Scene='" . $FS_FS_Scene . "',";

                //$sql.="OnlineTime='2013-11-01 12:12:12',";
                $sql .= "Bank_Address='" . $bankname . "',";
                $sql .= "Bank_Account='" . $bankno . "',";
                $sql .= "E_Mail='" . $e_mail . "'";

                DB::select($sql);
            } else {

                if ($lv == 'B') {
                    $winloss_b = $_REQUEST['winloss_b'] ?? "";
                } else if ($lv == 'D') {
                    $winloss_d = $_REQUEST['winloss_d'] ?? 0;
                    $winloss_c = $_REQUEST['winloss_c'] ?? 0;
                    $winloss_b = $Winloss_B - $winloss_d - $winloss_c;
                    $winloss_a = 100 - $winloss_d - $winloss_c - $winloss_b;
                }

                switch ($lv) {
                    case 'A':
                        $add = 'a';
                        $user = "Level='A' and Admin='$parents_id'";
                        $competence = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,0,1,1,1,1,1,1,1,';
                        $agent = "World='$world',Corprator='$corprator',Super='$super',Admin='$parents_id'";
                        break;
                    case 'B':
                        $add = 'b';
                        $competence = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,0,0,1,1,1,1,1,1,';
                        $user = "Level='B' and Super='$parents_id'";
                        $agent = "World='$world',Corprator='$corprator',Super='$parents_id',Admin='$admin'";
                        break;
                    case 'C':
                        $add = 'c';
                        $competence = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,0,0,0,1,1,1,0,1,';
                        $user = "Level='C' and Corprator='$parents_id'";
                        $agent = "World='$world',Corprator='$parents_id',Super='$super',Admin='$admin'";
                        break;
                    case 'D':
                        $add = 'd';
                        $competence = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,0,0,0,0,1,1,0,1,';
                        $user = "Level='D' and World='$parents_id'";
                        $agent = "World='$parents_id',Corprator='$corprator',Super='$super',Admin='$admin'";
                        break;
                }

                if ($lv == 'B') {
                    $abcd = 100;
                } else if ($lv == 'D') {
                    $abcd = 80;
                }

                $row_2 = get_object_vars(DB::select("select Credit from $data where UserName='$parents_id'")[0]);

                $credit = $row_2['Credit'] ?? 0;
                $points = $row_2['Points'] ?? 0;

                $row_3 = get_object_vars(DB::select("select sum(Credit) as credit from web_agents_data where $user")[0]);

                if ($row_3['credit'] + $maxcredit > $credit) {
                    $response["message"] = "";
                }

                $row_4 = DB::select("select * from web_agents_data where UserName='$username'");

                if (count($row_4) > 0) {
                    $response["message"] = "您输入的帐号 $username 已经有人使用了，请回上一页重新输入";
                    return response()->json($response, $response['status']);
                } else {

                    $sql = "insert into web_agents_data set ";
                    $sql .= "Level='" . $lv . "',";
                    $sql .= "UserName='" . $username . "',";
                    $sql .= "PassWord='" . $password . "',";
                    $sql .= "Credit='" . $maxcredit . "',";
                    $sql .= "Alias='" . $alias . "',";
                    $sql .= "AddDate='" . $AddDate . "',";
                    $sql .= "Status='0',";
                    //$sql.="CurType='".$curtype."',";
                    $sql .= "LineType='" . $linetype . "',";
                    $sql .= "wager='" . $wager . "',";
                    $sql .= "Competence='" . $competence . "',";
                    $sql .= "UseDate='" . $usedate . "',";
                    // if($lv=='C'){
                    $sql .= "A_Point='" . $Winloss_A . "',";
                    $sql .= "B_Point='" . $Winloss_B . "',";
                    $sql .= "C_Point='" . $Winloss_C . "',";
                    $sql .= "D_Point='" . $Winloss_D . "',";
                    // }else{
                    //    $sql.="A_Point='".$winloss_a."',";
                    //    $sql.="B_Point='".$winloss_b."',";
                    //    $sql.="C_Point='".$winloss_c."',";
                    //    $sql.="D_Point='".$winloss_d."',";
                    // }
                    $sql .= "$agent,";
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~足球
                    $sql .= "FT_Turn_R_A='" . $FT_Turn_R_A . "',";
                    $sql .= "FT_Turn_R_B='" . $FT_Turn_R_B . "',";
                    $sql .= "FT_Turn_R_C='" . $FT_Turn_R_C . "',";
                    $sql .= "FT_Turn_R_D='" . $FT_Turn_R_D . "',";
                    $sql .= "FT_R_Bet='" . $FT_R_Bet . "',";
                    $sql .= "FT_R_Scene='" . $FT_R_Scene . "',";
                    $sql .= "FT_Turn_OU_A='" . $FT_Turn_OU_A . "',";
                    $sql .= "FT_Turn_OU_B='" . $FT_Turn_OU_B . "',";
                    $sql .= "FT_Turn_OU_C='" . $FT_Turn_OU_C . "',";
                    $sql .= "FT_Turn_OU_D='" . $FT_Turn_OU_D . "',";
                    $sql .= "FT_OU_Bet='" . $FT_OU_Bet . "',";
                    $sql .= "FT_OU_Scene='" . $FT_OU_Scene . "',";
                    $sql .= "FT_Turn_RE_A='" . $FT_Turn_RE_A . "',";
                    $sql .= "FT_Turn_RE_B='" . $FT_Turn_RE_B . "',";
                    $sql .= "FT_Turn_RE_C='" . $FT_Turn_RE_C . "',";
                    $sql .= "FT_Turn_RE_D='" . $FT_Turn_RE_D . "',";
                    $sql .= "FT_RE_Bet='" . $FT_RE_Bet . "',";
                    $sql .= "FT_RE_Scene='" . $FT_RE_Scene . "',";
                    $sql .= "FT_Turn_ROU_A='" . $FT_Turn_ROU_A . "',";
                    $sql .= "FT_Turn_ROU_B='" . $FT_Turn_ROU_B . "',";
                    $sql .= "FT_Turn_ROU_C='" . $FT_Turn_ROU_C . "',";
                    $sql .= "FT_Turn_ROU_D='" . $FT_Turn_ROU_D . "',";
                    $sql .= "FT_ROU_Bet='" . $FT_ROU_Bet . "',";
                    $sql .= "FT_ROU_Scene='" . $FT_ROU_Scene . "',";
                    $sql .= "FT_Turn_EO_A='" . $FT_Turn_EO_A . "',";
                    $sql .= "FT_Turn_EO_B='" . $FT_Turn_EO_B . "',";
                    $sql .= "FT_Turn_EO_C='" . $FT_Turn_EO_C . "',";
                    $sql .= "FT_Turn_EO_D='" . $FT_Turn_EO_D . "',";
                    $sql .= "FT_EO_Bet='" . $FT_EO_Bet . "',";
                    $sql .= "FT_EO_Scene='" . $FT_EO_Scene . "',";
                    $sql .= "FT_Turn_RM='" . $FT_Turn_RM . "',";
                    $sql .= "FT_RM_Bet='" . $FT_RM_Bet . "',";
                    $sql .= "FT_RM_Scene='" . $FT_RM_Scene . "',";
                    $sql .= "FT_Turn_M='" . $FT_Turn_M . "',";
                    $sql .= "FT_M_Bet='" . $FT_M_Bet . "',";
                    $sql .= "FT_M_Scene='" . $FT_M_Scene . "',";
                    $sql .= "FT_Turn_PD='" . $FT_Turn_PD . "',";
                    $sql .= "FT_PD_Bet='" . $FT_PD_Bet . "',";
                    $sql .= "FT_PD_Scene='" . $FT_PD_Scene . "',";
                    $sql .= "FT_Turn_T='" . $FT_Turn_T . "',";
                    $sql .= "FT_T_Bet='" . $FT_T_Bet . "',";
                    $sql .= "FT_T_Scene='" . $FT_T_Scene . "',";
                    $sql .= "FT_Turn_F='" . $FT_Turn_F . "',";
                    $sql .= "FT_F_Bet='" . $FT_F_Bet . "',";
                    $sql .= "FT_F_Scene='" . $FT_F_Scene . "',";
                    $sql .= "FT_P_Bet='" . $FT_P_Bet . "',";
                    $sql .= "FT_Turn_P='" . $FT_Turn_P . "',";
                    $sql .= "FT_P_Scene='" . $FT_P_Scene . "',";
                    $sql .= "FT_Turn_PR='" . $FT_Turn_PR . "',";
                    $sql .= "FT_PR_Bet='" . $FT_PR_Bet . "',";
                    $sql .= "FT_PR_Scene='" . $FT_PR_Scene . "',";
                    $sql .= "FT_Turn_P3='" . $FT_Turn_P3 . "',";
                    $sql .= "FT_P3_Bet='" . $FT_P3_Bet . "',";
                    $sql .= "FT_P3_Scene='" . $FT_P3_Scene . "',";
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~篮球
                    $sql .= "BK_Turn_R_A='" . $BK_Turn_R_A . "',";
                    $sql .= "BK_Turn_R_B='" . $BK_Turn_R_B . "',";
                    $sql .= "BK_Turn_R_C='" . $BK_Turn_R_C . "',";
                    $sql .= "BK_Turn_R_D='" . $BK_Turn_R_D . "',";
                    $sql .= "BK_R_Bet='" . $BK_R_Bet . "',";
                    $sql .= "BK_R_Scene='" . $BK_R_Scene . "',";
                    $sql .= "BK_Turn_OU_A='" . $BK_Turn_OU_A . "',";
                    $sql .= "BK_Turn_OU_B='" . $BK_Turn_OU_B . "',";
                    $sql .= "BK_Turn_OU_C='" . $BK_Turn_OU_C . "',";
                    $sql .= "BK_Turn_OU_D='" . $BK_Turn_OU_D . "',";
                    $sql .= "BK_OU_Bet='" . $BK_OU_Bet . "',";
                    $sql .= "BK_OU_Scene='" . $BK_OU_Scene . "',";
                    $sql .= "BK_Turn_RE_A='" . $BK_Turn_RE_A . "',";
                    $sql .= "BK_Turn_RE_B='" . $BK_Turn_RE_B . "',";
                    $sql .= "BK_Turn_RE_C='" . $BK_Turn_RE_C . "',";
                    $sql .= "BK_Turn_RE_D='" . $BK_Turn_RE_D . "',";
                    $sql .= "BK_RE_Bet='" . $BK_RE_Bet . "',";
                    $sql .= "BK_RE_Scene='" . $BK_RE_Scene . "',";
                    $sql .= "BK_Turn_ROU_A='" . $BK_Turn_ROU_A . "',";
                    $sql .= "BK_Turn_ROU_B='" . $BK_Turn_ROU_B . "',";
                    $sql .= "BK_Turn_ROU_C='" . $BK_Turn_ROU_C . "',";
                    $sql .= "BK_Turn_ROU_D='" . $BK_Turn_ROU_D . "',";
                    $sql .= "BK_ROU_Bet='" . $BK_ROU_Bet . "',";
                    $sql .= "BK_ROU_Scene='" . $BK_ROU_Scene . "',";
                    $sql .= "BK_Turn_EO_A='" . $BK_Turn_EO_A . "',";
                    $sql .= "BK_Turn_EO_B='" . $BK_Turn_EO_B . "',";
                    $sql .= "BK_Turn_EO_C='" . $BK_Turn_EO_C . "',";
                    $sql .= "BK_Turn_EO_D='" . $BK_Turn_EO_D . "',";
                    $sql .= "BK_EO_Bet='" . $BK_EO_Bet . "',";
                    $sql .= "BK_EO_Scene='" . $BK_EO_Scene . "',";
                    $sql .= "BK_Turn_PR='" . $BK_Turn_PR . "',";
                    $sql .= "BK_PR_Bet='" . $BK_PR_Bet . "',";
                    $sql .= "BK_PR_Scene='" . $BK_PR_Scene . "',";
                    $sql .= "BK_Turn_P3='" . $BK_Turn_P3 . "',";
                    $sql .= "BK_P3_Bet='" . $BK_P3_Bet . "',";
                    $sql .= "BK_P3_Scene='" . $BK_P3_Scene . "',";
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~冠军
                    $sql .= "FS_Turn_FS='" . $FS_Turn_FS . "',";
                    $sql .= "FS_FS_Scene='" . $FS_FS_Scene . "',";
                    $sql .= "FS_FS_Bet='" . $FS_FS_Bet . "',";
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~棒球
                    $sql .= "BS_Turn_R_A='" . $BS_Turn_R_A . "',";
                    $sql .= "BS_Turn_R_B='" . $BS_Turn_R_B . "',";
                    $sql .= "BS_Turn_R_C='" . $BS_Turn_R_C . "',";
                    $sql .= "BS_Turn_R_D='" . $BS_Turn_R_D . "',";
                    $sql .= "BS_R_Bet='" . $BS_R_Bet . "',";
                    $sql .= "BS_R_Scene='" . $BS_R_Scene . "',";
                    $sql .= "BS_Turn_OU_A='" . $BS_Turn_OU_A . "',";
                    $sql .= "BS_Turn_OU_B='" . $BS_Turn_OU_B . "',";
                    $sql .= "BS_Turn_OU_C='" . $BS_Turn_OU_C . "',";
                    $sql .= "BS_Turn_OU_D='" . $BS_Turn_OU_D . "',";
                    $sql .= "BS_OU_Bet='" . $BS_OU_Bet . "',";
                    $sql .= "BS_OU_Scene='" . $BS_OU_Scene . "',";
                    $sql .= "BS_Turn_RE_A='" . $BS_Turn_RE_A . "',";
                    $sql .= "BS_Turn_RE_B='" . $BS_Turn_RE_B . "',";
                    $sql .= "BS_Turn_RE_C='" . $BS_Turn_RE_C . "',";
                    $sql .= "BS_Turn_RE_D='" . $BS_Turn_RE_D . "',";
                    $sql .= "BS_RE_Bet='" . $BS_RE_Bet . "',";
                    $sql .= "BS_RE_Scene='" . $BS_RE_Scene . "',";
                    $sql .= "BS_Turn_ROU_A='" . $BS_Turn_ROU_A . "',";
                    $sql .= "BS_Turn_ROU_B='" . $BS_Turn_ROU_B . "',";
                    $sql .= "BS_Turn_ROU_C='" . $BS_Turn_ROU_C . "',";
                    $sql .= "BS_Turn_ROU_D='" . $BS_Turn_ROU_D . "',";
                    $sql .= "BS_ROU_Bet='" . $BS_ROU_Bet . "',";
                    $sql .= "BS_ROU_Scene='" . $BS_ROU_Scene . "',";
                    $sql .= "BS_Turn_EO_A='" . $BS_Turn_EO_A . "',";
                    $sql .= "BS_Turn_EO_B='" . $BS_Turn_EO_B . "',";
                    $sql .= "BS_Turn_EO_C='" . $BS_Turn_EO_C . "',";
                    $sql .= "BS_Turn_EO_D='" . $BS_Turn_EO_D . "',";
                    $sql .= "BS_EO_Bet='" . $BS_EO_Bet . "',";
                    $sql .= "BS_EO_Scene='" . $BS_EO_Scene . "',";
                    $sql .= "BS_Turn_M='" . $BS_Turn_M . "',";
                    $sql .= "BS_M_Bet='" . $BS_M_Bet . "',";
                    $sql .= "BS_M_Scene='" . $BS_M_Scene . "',";
                    $sql .= "BS_Turn_PD='" . $BS_Turn_PD . "',";
                    $sql .= "BS_PD_Bet='" . $BS_PD_Bet . "',";
                    $sql .= "BS_PD_Scene='" . $BS_PD_Scene . "',";
                    $sql .= "BS_Turn_T='" . $BS_Turn_T . "',";
                    $sql .= "BS_T_Bet ='" . $BS_T_Bet . "',";
                    $sql .= "BS_T_Scene='" . $BS_T_Scene . "',";
                    $sql .= "BS_Turn_P='" . $BS_Turn_P . "',";
                    $sql .= "BS_P_Bet='" . $BS_P_Bet . "',";
                    $sql .= "BS_P_Scene='" . $BS_P_Scene . "',";
                    $sql .= "BS_Turn_PR='" . $BS_Turn_PR . "',";
                    $sql .= "BS_PR_Bet='" . $BS_PR_Bet . "',";
                    $sql .= "BS_PR_Scene='" . $BS_PR_Scene . "',";
                    $sql .= "BS_Turn_P3='" . $BS_Turn_P3 . "',";
                    $sql .= "BS_P3_Bet='" . $BS_P3_Bet . "',";
                    $sql .= "BS_P3_Scene='" . $BS_P3_Scene . "',";
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~网球
                    $sql .= "TN_Turn_R_A='" . $TN_Turn_R_A . "',";
                    $sql .= "TN_Turn_R_B='" . $TN_Turn_R_B . "',";
                    $sql .= "TN_Turn_R_C='" . $TN_Turn_R_C . "',";
                    $sql .= "TN_Turn_R_D='" . $TN_Turn_R_D . "',";
                    $sql .= "TN_R_Bet='" . $TN_R_Bet . "',";
                    $sql .= "TN_R_Scene='" . $TN_R_Scene . "',";
                    $sql .= "TN_Turn_OU_A='" . $TN_Turn_OU_A . "',";
                    $sql .= "TN_Turn_OU_B='" . $TN_Turn_OU_B . "',";
                    $sql .= "TN_Turn_OU_C='" . $TN_Turn_OU_C . "',";
                    $sql .= "TN_Turn_OU_D='" . $TN_Turn_OU_D . "',";
                    $sql .= "TN_OU_Bet='" . $TN_OU_Bet . "',";
                    $sql .= "TN_OU_Scene='" . $TN_OU_Scene . "',";
                    $sql .= "TN_Turn_RE_A='" . $TN_Turn_RE_A . "',";
                    $sql .= "TN_Turn_RE_B='" . $TN_Turn_RE_B . "',";
                    $sql .= "TN_Turn_RE_C='" . $TN_Turn_RE_C . "',";
                    $sql .= "TN_Turn_RE_D='" . $TN_Turn_RE_D . "',";
                    $sql .= "TN_RE_Bet='" . $TN_RE_Bet . "',";
                    $sql .= "TN_RE_Scene='" . $TN_RE_Scene . "',";
                    $sql .= "TN_Turn_ROU_A='" . $TN_Turn_ROU_A . "',";
                    $sql .= "TN_Turn_ROU_B='" . $TN_Turn_ROU_B . "',";
                    $sql .= "TN_Turn_ROU_C='" . $TN_Turn_ROU_C . "',";
                    $sql .= "TN_Turn_ROU_D='" . $TN_Turn_ROU_D . "',";
                    $sql .= "TN_ROU_Bet='" . $TN_ROU_Bet . "',";
                    $sql .= "TN_ROU_Scene='" . $TN_ROU_Scene . "',";
                    $sql .= "TN_Turn_EO_A='" . $TN_Turn_EO_A . "',";
                    $sql .= "TN_Turn_EO_B='" . $TN_Turn_EO_B . "',";
                    $sql .= "TN_Turn_EO_C='" . $TN_Turn_EO_C . "',";
                    $sql .= "TN_Turn_EO_D='" . $TN_Turn_EO_D . "',";
                    $sql .= "TN_EO_Bet='" . $TN_EO_Bet . "',";
                    $sql .= "TN_EO_Scene='" . $TN_EO_Scene . "',";
                    $sql .= "TN_Turn_M='" . $TN_Turn_M . "',";
                    $sql .= "TN_M_Bet='" . $TN_M_Bet . "',";
                    $sql .= "TN_M_Scene='" . $TN_M_Scene . "',";
                    $sql .= "TN_Turn_PD='" . $TN_Turn_PD . "',";
                    $sql .= "TN_PD_Bet='" . $TN_PD_Bet . "',";
                    $sql .= "TN_PD_Scene='" . $TN_PD_Scene . "',";
                    $sql .= "TN_Turn_P='" . $TN_Turn_P . "',";
                    $sql .= "TN_P_Bet='" . $TN_P_Bet . "',";
                    $sql .= "TN_P_Scene='" . $TN_P_Scene . "',";
                    $sql .= "TN_Turn_PR='" . $TN_Turn_PR . "',";
                    $sql .= "TN_PR_Bet='" . $TN_PR_Bet . "',";
                    $sql .= "TN_PR_Scene='" . $TN_PR_Scene . "',";
                    $sql .= "TN_Turn_P3='" . $TN_Turn_P3 . "',";
                    $sql .= "TN_P3_Bet='" . $TN_P3_Bet . "',";
                    $sql .= "TN_P3_Scene='" . $TN_P3_Scene . "',";
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~排球
                    $sql .= "VB_Turn_R_A='" . $VB_Turn_R_A . "',";
                    $sql .= "VB_Turn_R_B='" . $VB_Turn_R_B . "',";
                    $sql .= "VB_Turn_R_C='" . $VB_Turn_R_C . "',";
                    $sql .= "VB_Turn_R_D='" . $VB_Turn_R_D . "',";
                    $sql .= "VB_R_Bet='" . $VB_R_Bet . "',";
                    $sql .= "VB_R_Scene='" . $VB_R_Scene . "',";
                    $sql .= "VB_Turn_OU_A='" . $VB_Turn_OU_A . "',";
                    $sql .= "VB_Turn_OU_B='" . $VB_Turn_OU_B . "',";
                    $sql .= "VB_Turn_OU_C='" . $VB_Turn_OU_C . "',";
                    $sql .= "VB_Turn_OU_D='" . $VB_Turn_OU_D . "',";
                    $sql .= "VB_OU_Bet='" . $VB_OU_Bet . "',";
                    $sql .= "VB_OU_Scene='" . $VB_OU_Scene . "',";
                    $sql .= "VB_Turn_RE_A='" . $VB_Turn_RE_A . "',";
                    $sql .= "VB_Turn_RE_B='" . $VB_Turn_RE_B . "',";
                    $sql .= "VB_Turn_RE_C='" . $VB_Turn_RE_C . "',";
                    $sql .= "VB_Turn_RE_D='" . $VB_Turn_RE_D . "',";
                    $sql .= "VB_RE_Bet='" . $VB_RE_Bet . "',";
                    $sql .= "VB_RE_Scene='" . $VB_RE_Scene . "',";
                    $sql .= "VB_Turn_ROU_A='" . $VB_Turn_ROU_A . "',";
                    $sql .= "VB_Turn_ROU_B='" . $VB_Turn_ROU_B . "',";
                    $sql .= "VB_Turn_ROU_C='" . $VB_Turn_ROU_C . "',";
                    $sql .= "VB_Turn_ROU_D='" . $VB_Turn_ROU_D . "',";
                    $sql .= "VB_ROU_Bet='" . $VB_ROU_Bet . "',";
                    $sql .= "VB_ROU_Scene='" . $VB_ROU_Scene . "',";
                    $sql .= "VB_Turn_EO_A='" . $VB_Turn_EO_A . "',";
                    $sql .= "VB_Turn_EO_B='" . $VB_Turn_EO_B . "',";
                    $sql .= "VB_Turn_EO_C='" . $VB_Turn_EO_C . "',";
                    $sql .= "VB_Turn_EO_D='" . $VB_Turn_EO_D . "',";
                    $sql .= "VB_EO_Bet='" . $VB_EO_Bet . "',";
                    $sql .= "VB_EO_Scene='" . $VB_EO_Scene . "',";
                    $sql .= "VB_Turn_M='" . $VB_Turn_M . "',";
                    $sql .= "VB_M_Bet='" . $VB_M_Bet . "',";
                    $sql .= "VB_M_Scene='" . $VB_M_Scene . "',";
                    $sql .= "VB_Turn_PD='" . $VB_Turn_PD . "',";
                    $sql .= "VB_PD_Bet='" . $VB_PD_Bet . "',";
                    $sql .= "VB_PD_Scene='" . $VB_PD_Scene . "',";
                    $sql .= "VB_Turn_P='" . $VB_Turn_P . "',";
                    $sql .= "VB_P_Bet='" . $VB_P_Bet . "',";
                    $sql .= "VB_P_Scene='" . $VB_P_Scene . "',";
                    $sql .= "VB_Turn_PR='" . $VB_Turn_PR . "',";
                    $sql .= "VB_PR_Bet='" . $VB_PR_Bet . "',";
                    $sql .= "VB_PR_Scene='" . $VB_PR_Scene . "',";
                    $sql .= "VB_Turn_P3='" . $VB_Turn_P3 . "',";
                    $sql .= "VB_P3_Bet='" . $VB_P3_Bet . "',";
                    $sql .= "VB_P3_Scene='" . $VB_P3_Scene . "',";
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~其它
                    $sql .= "OP_Turn_R_A='" . $OP_Turn_R_A . "',";
                    $sql .= "OP_Turn_R_B='" . $OP_Turn_R_B . "',";
                    $sql .= "OP_Turn_R_C='" . $OP_Turn_R_C . "',";
                    $sql .= "OP_Turn_R_D='" . $OP_Turn_R_D . "',";
                    $sql .= "OP_R_Bet='" . $OP_R_Bet . "',";
                    $sql .= "OP_R_Scene='" . $OP_R_Scene . "',";
                    $sql .= "OP_Turn_OU_A='" . $OP_Turn_OU_A . "',";
                    $sql .= "OP_Turn_OU_B='" . $OP_Turn_OU_B . "',";
                    $sql .= "OP_Turn_OU_C='" . $OP_Turn_OU_C . "',";
                    $sql .= "OP_Turn_OU_D='" . $OP_Turn_OU_D . "',";
                    $sql .= "OP_OU_Bet='" . $OP_OU_Bet . "',";
                    $sql .= "OP_OU_Scene='" . $OP_OU_Scene . "',";
                    $sql .= "OP_Turn_RE_A='" . $OP_Turn_RE_A . "',";
                    $sql .= "OP_Turn_RE_B='" . $OP_Turn_RE_B . "',";
                    $sql .= "OP_Turn_RE_C='" . $OP_Turn_RE_C . "',";
                    $sql .= "OP_Turn_RE_D='" . $OP_Turn_RE_D . "',";
                    $sql .= "OP_RE_Bet='" . $OP_RE_Bet . "',";
                    $sql .= "OP_RE_Scene='" . $OP_RE_Scene . "',";
                    $sql .= "OP_Turn_ROU_A='" . $OP_Turn_ROU_A . "',";
                    $sql .= "OP_Turn_ROU_B='" . $OP_Turn_ROU_B . "',";
                    $sql .= "OP_Turn_ROU_C='" . $OP_Turn_ROU_C . "',";
                    $sql .= "OP_Turn_ROU_D='" . $OP_Turn_ROU_D . "',";
                    $sql .= "OP_ROU_Bet='" . $OP_ROU_Bet . "',";
                    $sql .= "OP_ROU_Scene='" . $OP_ROU_Scene . "',";
                    $sql .= "OP_Turn_EO_A='" . $OP_Turn_EO_A . "',";
                    $sql .= "OP_Turn_EO_B='" . $OP_Turn_EO_B . "',";
                    $sql .= "OP_Turn_EO_C='" . $OP_Turn_EO_C . "',";
                    $sql .= "OP_Turn_EO_D='" . $OP_Turn_EO_D . "',";
                    $sql .= "OP_EO_Bet='" . $OP_EO_Bet . "',";
                    $sql .= "OP_EO_Scene='" . $OP_EO_Scene . "',";
                    $sql .= "OP_Turn_M='" . $OP_Turn_M . "',";
                    $sql .= "OP_M_Bet='" . $OP_M_Bet . "',";
                    $sql .= "OP_M_Scene='" . $OP_M_Scene . "',";
                    $sql .= "OP_Turn_PD='" . $OP_Turn_PD . "',";
                    $sql .= "OP_PD_Bet='" . $OP_PD_Bet . "',";
                    $sql .= "OP_PD_Scene='" . $OP_PD_Scene . "',";
                    $sql .= "OP_Turn_T='" . $OP_Turn_T . "',";
                    $sql .= "OP_T_Bet='" . $OP_T_Bet . "',";
                    $sql .= "OP_T_Scene='" . $OP_T_Scene . "',";
                    $sql .= "OP_Turn_F='" . $OP_Turn_F . "',";
                    $sql .= "OP_F_Bet='" . $OP_F_Bet . "',";
                    $sql .= "OP_F_Scene='" . $OP_F_Scene . "',";
                    $sql .= "OP_P_Bet='" . $OP_P_Bet . "',";
                    $sql .= "OP_Turn_P='" . $OP_Turn_P . "',";
                    $sql .= "OP_P_Scene='" . $OP_P_Scene . "',";
                    $sql .= "OP_Turn_PR='" . $OP_Turn_PR . "',";
                    $sql .= "OP_PR_Bet='" . $OP_PR_Bet . "',";
                    $sql .= "OP_PR_Scene='" . $OP_PR_Scene . "',";
                    $sql .= "OP_Turn_P3='" . $OP_Turn_P3 . "',";
                    $sql .= "OP_P3_Bet='" . $OP_P3_Bet . "',";
                    $sql .= "OP_P3_Scene='" . $OP_P3_Scene . "',";
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~六合彩
                    $sql .= "SIX_Turn_SCA_A='" . $SIX_Turn_SCA_A . "',";
                    $sql .= "SIX_Turn_SCA_B='" . $SIX_Turn_SCA_B . "',";
                    $sql .= "SIX_Turn_SCA_C='" . $SIX_Turn_SCA_C . "',";
                    $sql .= "SIX_Turn_SCA_D='" . $SIX_Turn_SCA_D . "',";
                    $sql .= "SIX_SCA_Bet='" . $SIX_SCA_Bet . "',";
                    $sql .= "SIX_SCA_Scene='" . $SIX_SCA_Scene . "',";
                    $sql .= "SIX_Turn_SCB_A='" . $SIX_Turn_SCB_A . "',";
                    $sql .= "SIX_Turn_SCB_B='" . $SIX_Turn_SCB_B . "',";
                    $sql .= "SIX_Turn_SCB_C='" . $SIX_Turn_SCB_C . "',";
                    $sql .= "SIX_Turn_SCB_D='" . $SIX_Turn_SCB_D . "',";
                    $sql .= "SIX_SCB_Bet='" . $SIX_SCB_Bet . "',";
                    $sql .= "SIX_SCB_Scene='" . $SIX_SCB_Scene . "',";
                    $sql .= "SIX_Turn_SCA_AOUEO_A='" . $SIX_Turn_SCA_AOUEO_A . "',";
                    $sql .= "SIX_Turn_SCA_AOUEO_B='" . $SIX_Turn_SCA_AOUEO_B . "',";
                    $sql .= "SIX_Turn_SCA_AOUEO_C='" . $SIX_Turn_SCA_AOUEO_C . "',";
                    $sql .= "SIX_Turn_SCA_AOUEO_D='" . $SIX_Turn_SCA_AOUEO_D . "',";
                    $sql .= "SIX_SCA_AOUEO_Bet='" . $SIX_SCA_AOUEO_Bet . "',";
                    $sql .= "SIX_SCA_AOUEO_Scene='" . $SIX_SCA_AOUEO_Scene . "',";
                    $sql .= "SIX_Turn_SCA_BOUEO_A='" . $SIX_Turn_SCA_BOUEO_A . "',";
                    $sql .= "SIX_Turn_SCA_BOUEO_B='" . $SIX_Turn_SCA_BOUEO_B . "',";
                    $sql .= "SIX_Turn_SCA_BOUEO_C='" . $SIX_Turn_SCA_BOUEO_C . "',";
                    $sql .= "SIX_Turn_SCA_BOUEO_D='" . $SIX_Turn_SCA_BOUEO_D . "',";
                    $sql .= "SIX_SCA_BOUEO_Bet='" . $SIX_SCA_BOUEO_Bet . "',";
                    $sql .= "SIX_SCA_BOUEO_Scene='" . $SIX_SCA_BOUEO_Scene . "',";
                    $sql .= "SIX_Turn_SCA_RBG_A='" . $SIX_Turn_SCA_RBG_A . "',";
                    $sql .= "SIX_Turn_SCA_RBG_B='" . $SIX_Turn_SCA_RBG_B . "',";
                    $sql .= "SIX_Turn_SCA_RBG_C='" . $SIX_Turn_SCA_RBG_C . "',";
                    $sql .= "SIX_Turn_SCA_RBG_D='" . $SIX_Turn_SCA_RBG_D . "',";
                    $sql .= "SIX_SCA_RBG_Bet='" . $SIX_SCA_RBG_Bet . "',";
                    $sql .= "SIX_SCA_RBG_Scene='" . $SIX_SCA_RBG_Scene . "',";
                    $sql .= "SIX_Turn_AC_A='" . $SIX_Turn_AC_A . "',";
                    $sql .= "SIX_Turn_AC_B='" . $SIX_Turn_AC_B . "',";
                    $sql .= "SIX_Turn_AC_C='" . $SIX_Turn_AC_C . "',";
                    $sql .= "SIX_Turn_AC_D='" . $SIX_Turn_AC_D . "',";
                    $sql .= "SIX_AC_Bet='" . $SIX_AC_Bet . "',";
                    $sql .= "SIX_AC_Scene='" . $SIX_AC_Scene . "',";
                    $sql .= "SIX_Turn_AC_TOUEO_A='" . $SIX_Turn_AC_TOUEO_A . "',";
                    $sql .= "SIX_Turn_AC_TOUEO_B='" . $SIX_Turn_AC_TOUEO_B . "',";
                    $sql .= "SIX_Turn_AC_TOUEO_C='" . $SIX_Turn_AC_TOUEO_C . "',";
                    $sql .= "SIX_Turn_AC_TOUEO_D='" . $SIX_Turn_AC_TOUEO_D . "',";
                    $sql .= "SIX_AC_TOUEO_Bet='" . $SIX_AC_TOUEO_Bet . "',";
                    $sql .= "SIX_AC_TOUEO_Scene='" . $SIX_AC_TOUEO_Scene . "',";

                    $sql .= "SIX_Turn_AC6_AOUEO_A='" . $SIX_Turn_AC6_AOUEO_A . "',";
                    $sql .= "SIX_Turn_AC6_AOUEO_B='" . $SIX_Turn_AC6_AOUEO_B . "',";
                    $sql .= "SIX_Turn_AC6_AOUEO_C='" . $SIX_Turn_AC6_AOUEO_C . "',";
                    $sql .= "SIX_Turn_AC6_AOUEO_D='" . $SIX_Turn_AC6_AOUEO_D . "',";
                    $sql .= "SIX_AC6_AOUEO_Bet='" . $SIX_AC6_AOUEO_Bet . "',";
                    $sql .= "SIX_AC6_AOUEO_Scene='" . $SIX_AC6_AOUEO_Scene . "',";

                    $sql .= "SIX_Turn_AC6_BOUEO_A='" . $SIX_Turn_AC6_BOUEO_A . "',";
                    $sql .= "SIX_Turn_AC6_BOUEO_B='" . $SIX_Turn_AC6_BOUEO_B . "',";
                    $sql .= "SIX_Turn_AC6_BOUEO_C='" . $SIX_Turn_AC6_BOUEO_C . "',";
                    $sql .= "SIX_Turn_AC6_BOUEO_D='" . $SIX_Turn_AC6_BOUEO_D . "',";
                    $sql .= "SIX_AC6_BOUEO_Bet='" . $SIX_AC6_BOUEO_Bet . "',";
                    $sql .= "SIX_AC6_BOUEO_Scene='" . $SIX_AC6_BOUEO_Scene . "',";

                    $sql .= "SIX_Turn_AC6_RBG_A='" . $SIX_Turn_AC6_RBG_A . "',";
                    $sql .= "SIX_Turn_AC6_RBG_B='" . $SIX_Turn_AC6_RBG_B . "',";
                    $sql .= "SIX_Turn_AC6_RBG_C='" . $SIX_Turn_AC6_RBG_C . "',";
                    $sql .= "SIX_Turn_AC6_RBG_D='" . $SIX_Turn_AC6_RBG_D . "',";
                    $sql .= "SIX_AC6_RBG_Bet='" . $SIX_AC6_RBG_Bet . "',";
                    $sql .= "SIX_AC6_RBG_Scene='" . $SIX_AC6_RBG_Scene . "',";
                    $sql .= "SIX_Turn_SX_A='" . $SIX_Turn_SX_A . "',";
                    $sql .= "SIX_Turn_SX_B='" . $SIX_Turn_SX_B . "',";
                    $sql .= "SIX_Turn_SX_C='" . $SIX_Turn_SX_C . "',";
                    $sql .= "SIX_Turn_SX_D='" . $SIX_Turn_SX_D . "',";
                    $sql .= "SIX_SX_Bet='" . $SIX_SX_Bet . "',";
                    $sql .= "SIX_SX_Scene='" . $SIX_SX_Scene . "',";
                    $sql .= "SIX_Turn_HW_A='" . $SIX_Turn_HW_A . "',";
                    $sql .= "SIX_Turn_HW_B='" . $SIX_Turn_HW_B . "',";
                    $sql .= "SIX_Turn_HW_C='" . $SIX_Turn_HW_C . "',";
                    $sql .= "SIX_Turn_HW_D='" . $SIX_Turn_HW_D . "',";
                    $sql .= "SIX_HW_Bet='" . $SIX_HW_Bet . "',";
                    $sql .= "SIX_HW_Scene='" . $SIX_HW_Scene . "',";
                    $sql .= "SIX_Turn_MT_A='" . $SIX_Turn_MT_A . "',";
                    $sql .= "SIX_Turn_MT_B='" . $SIX_Turn_MT_B . "',";
                    $sql .= "SIX_Turn_MT_C='" . $SIX_Turn_MT_C . "',";
                    $sql .= "SIX_Turn_MT_D='" . $SIX_Turn_MT_D . "',";
                    $sql .= "SIX_MT_Bet='" . $SIX_MT_Bet . "',";
                    $sql .= "SIX_MT_Scene='" . $SIX_MT_Scene . "',";
                    $sql .= "SIX_Turn_M_A='" . $SIX_Turn_M_A . "',";
                    $sql .= "SIX_Turn_M_B='" . $SIX_Turn_M_B . "',";
                    $sql .= "SIX_Turn_M_C='" . $SIX_Turn_M_C . "',";
                    $sql .= "SIX_Turn_M_D='" . $SIX_Turn_M_D . "',";
                    $sql .= "SIX_M_Bet='" . $SIX_M_Bet . "',";
                    $sql .= "SIX_M_Scene='" . $SIX_M_Scene . "',";
                    $sql .= "SIX_Turn_EC_A='" . $SIX_Turn_EC_A . "',";
                    $sql .= "SIX_Turn_EC_B='" . $SIX_Turn_EC_B . "',";
                    $sql .= "SIX_Turn_EC_C='" . $SIX_Turn_EC_C . "',";
                    $sql .= "SIX_Turn_EC_D='" . $SIX_Turn_EC_D . "',";
                    $sql .= "SIX_EC_Bet='" . $SIX_EC_Bet . "',";
                    $sql .= "SIX_EC_Scene='" . $SIX_EC_Scene . "'";

                    DB::select($sql);

                    DB::select("update web_agents_data set Count=Count+1 where UserName='$parents_id'");
                }
            }

            $response['message'] = "Company Data added successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateCompany(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $logined_user = $request->user();

            $request_data = $request->all();

            $lv = $request_data["lv"];
            $admin = $request_data["admin"];
            $parents_id = $request_data["parents_id"] ?? "";
            $parents_name = $request_data["parents_name"] ?? "";
            $name = $request_data["name"] ?? "";
            $keys = $request_data['keys'] ?? "";
            $id = $request_data["id"];
            $gold = $request_data["maxcredit"] ?? "";
            $pasd = Hash::make($request_data["password"]);
            $alias = $request_data["alias"] ?? "";
            $ManageMember2 = $request_data['ManageMember'] ?? ""; //代理商会员管理权限
            $ManageCredit2 = $request_data['ManageCredit'] ?? ""; //代理商额度管理权限
            $D_Point = $request_data['D_Point'] ?? "";
            $super = $request_data['Super'] ?? "";
            $corprator = $request_data['Corprator'] ?? "";
            $world = $request_data['World'] ?? "";

            $username = $logined_user['UserName'];
            $passw = $logined_user['Level'];

            switch ($lv) {
                case 'A':
                    $Title = "公司";
                    $level = 'M';
                    $webdata = 'web_system_data';
                    $data = 'web_agents_data';
                    $user = "Level='B' and Super='$name'";
                    $agents = "(UserName='$username' or Admin='$admin' ro Super='$username' or Corprator='$username' or World='$username') and";
                    $ag = "UserName='$admin'";
                    $wo = "Admin='$admin' and UserName!='$name'";
                    break;
                case 'B':
                    $Title = "股东";
                    $level = 'A';
                    $webdata = 'web_agents_data';
                    $data = 'web_agents_data';
                    $user = "Level='C' and Corprator='$name'";
                    $agents = "(UserName='$username' or Super='$username' or Corprator='$username' or World='$username') and";
                    $ag = "UserName='$super'";
                    $wo = "Super='$super' and UserName!='$name'";
                    break;
                case 'C':
                    $Title = "总代理";
                    $level = 'B';
                    $webdata = 'web_agents_data';
                    $data = 'web_agents_data';
                    $user = "Level='D' and World='$name'";
                    $agents = "(UserName='$username' or Super='$username' or Corprator='$username' or World='$username') and";
                    $ag = "UserName='$corprator'";
                    $wo = "Corprator='$corprator' and UserName!='$name'";
                    break;
                case 'D':
                    $Title = "代理商";
                    $level = 'C';
                    $webdata = 'web_agents_data';
                    $data = 'web_member_data';
                    $user = "Agents='$name'";
                    $agents = "(UserName='$username' or Super='$username' or Corprator='$username' or World='$username') and";
                    $ag = "UserName='$world'";
                    $wo = "World='$world' and UserName!='$name'";
                    break;
                case 'MEM':
                    $Title = "会员";
                    $level = 'D';
                    $webdata = 'web_agents_data';
                    $data = 'web_member_data';
                    $user = "UserName='$name' and Level='D'";
                    $ag = "UserName='$name'";
                    $wo = "UserName='$name' and UserName!='$name'";
                    break;
            }

            if ($lv == 'D') {
                $mysql = "update web_agents_data set PassWord='$pasd',Alias='$alias',ManageMember='$ManageMember2',ManageCredit='$ManageCredit2',D_Point='$D_Point' where ID='$id'";
                DB::select($mysql);
            } else {
                $mysql = "update web_agents_data set PassWord='$pasd',Alias='$alias' where ID='$id'";
                DB::select($mysql);
            }

            $response['message'] = "Company Data updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateAgency(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "lv" => "required|string",
                "UserName" => "required|string",
                "agent" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $logined_user = $request->user();

            $request_data = $request->all();

            $lv = $request_data["lv"];
            $username = $request_data["UserName"];
            $agent = $request_data["agent"];

            $row = WebAgent::where("UserName", $agent)->first();

            if (!isset($row)) {

                $response["message"] = "请输入正确的代理商账号!!";

                return response()->json($response, $response['status']);
            }

            $world = $row['World'];
            $corprator = $row['Corprator'];
            $super = $row['Super'];
            $admin = $row['Admin'];

            $mysql = "update web_member_data set agents='$agent',World='$world',Corprator='$corprator',Super='$super',Admin='$admin' where UserName='$username'";
            DB::select($mysql);
            $rsql = "update web_report_data set agents='$agent',World='$world',Corprator='$corprator',Super='$super',Admin='$admin' where M_Name='$username'";
            DB::select($mysql);

            $response['message'] = "Agent Data moved successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function detailCompany(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $logined_user = $request->user();

            $_REQUEST = $request->all();

            $lv = $_REQUEST["lv"];
            $admin = $_REQUEST["admin"];
            $parents_id = $_REQUEST["parents_id"] ?? "";
            $parents_name = $_REQUEST["parents_name"] ?? "";
            $name = $_REQUEST["name"] ?? "";
            $gtype = $_REQUEST["gtype"] ?? "";
            $id = $_REQUEST["id"];

            if ($lv == "MEM") {

                $sql = "select * from web_member_data where id=$parents_id";
                $row = get_object_vars(DB::select($sql)[0]);
            } else {

                $sql = "select * from web_agents_data where ID=$parents_id";
                $row = get_object_vars(DB::select($sql)[0]);
            }

            $username = $row["UserName"];
            $admin = $row['Admin'];
            $super = $row['Super'];
            $corprator = $row['Corprator'];
            $world = $row["World"];
            $alias = $row["Alias"];

            switch ($lv) {
                case 'A':
                    $Title = "公司";
                    $user = "$admin";
                    $ag = "Super='$name' and Level='B'";
                    $data = "web_agents_data";
                    $webdata = 'web_system_data';
                    $agname = "Super='$username'";
                    break;
                case 'B':
                    $Title = "股东";
                    $user = "$super";
                    $ag = "Corprator='$name' and Level='C'";
                    $data = "web_agents_data";
                    $webdata = 'web_agents_data';
                    $agname = "Corprator='$username'";
                    break;
                case 'C':
                    $Title = "总代理";
                    $user = "$corprator";
                    $ag = "World='$name' and Level='D'";
                    $data = "web_agents_data";
                    $webdata = 'web_agents_data';
                    $agname = "World='$username'";
                    break;
                case 'D':
                    $Title = "代理商";
                    $user = "$world";
                    $ag = "Agents='$name'";
                    $data = "web_member_data";
                    $webdata = 'web_agents_data';
                    $agname = "Agents='$username'";
                    break;
                case 'MEM':
                    $Title = "会员";
                    $Caption = "代理商";
                    $level = 'D';
                    $class = '#FEF5B5';
                    $bgcolor = 'E3D46E';
                    $user = $name;
                    $webdata = 'web_member_data';
                    break;
            }

            $asql = "select * from $webdata where UserName='$user'";
            $arow = get_object_vars(DB::select($asql)[0]);

            if ($lv == "MEM") {

                switch ($gtype) {
                    case "FT":
                        $mysql = "update web_member_data set FT_Turn_R=" . $_REQUEST['FT_Turn_R'] . ",FT_R_Bet=" . $_REQUEST['FT_R_SO'] . ",FT_R_Scene=" . $_REQUEST['FT_R_SC'] . ",FT_Turn_OU=" . $_REQUEST['FT_Turn_OU'] . ",FT_OU_Bet=" . $_REQUEST['FT_OU_SO'] . ",FT_OU_Scene=" . $_REQUEST['FT_OU_SC'] . ",FT_Turn_RE=" . $_REQUEST['FT_Turn_RE'] . ",FT_RE_Bet=" . $_REQUEST['FT_RE_SO'] . ",FT_RE_Scene=" . $_REQUEST['FT_RE_SC'] . ",FT_Turn_ROU=" . $_REQUEST['FT_Turn_ROU'] . ",FT_ROU_Bet=" . $_REQUEST['FT_ROU_SO'] . ",FT_ROU_Scene=" . $_REQUEST['FT_ROU_SC'] . ",FT_Turn_EO=" . $_REQUEST['FT_Turn_EO'] . ",FT_EO_Bet=" . $_REQUEST['FT_EO_SO'] . ",FT_EO_Scene=" . $_REQUEST['FT_EO_SC'] . ",FT_Turn_M=" . $_REQUEST['FT_Turn_M'] . ",FT_M_Bet=" . $_REQUEST['FT_M_SO'] . ",FT_M_Scene=" . $_REQUEST['FT_M_SC'] . ",FT_Turn_PD=" . $_REQUEST['FT_Turn_PD'] . ",FT_PD_Bet=" . $_REQUEST['FT_PD_SO'] . ",FT_PD_Scene=" . $_REQUEST['FT_PD_SC'] . ",FT_Turn_T=" . $_REQUEST['FT_Turn_T'] . ",FT_T_Bet=" . $_REQUEST['FT_T_SO'] . ",FT_T_Scene=" . $_REQUEST['FT_T_SC'] . ",FT_Turn_F=" . $_REQUEST['FT_Turn_F'] . ",FT_F_Bet=" . $_REQUEST['FT_F_SO'] . ",FT_F_Scene=" . $_REQUEST['FT_F_SC'] . ",FT_Turn_RM=" . $_REQUEST['FT_Turn_RM'] . ",FT_RM_Bet=" . $_REQUEST['FT_RM_SO'] . ",FT_RM_Scene=" . $_REQUEST['FT_RM_SC'] . ",FT_Turn_P=" . $_REQUEST['FT_Turn_P'] . ",FT_P_Bet=" . $_REQUEST['FT_P_SO'] . ",FT_P_Scene=" . $_REQUEST['FT_P_SC'] . ",FT_Turn_PR=" . $_REQUEST['FT_Turn_PR'] . ",FT_PR_Bet=" . $_REQUEST['FT_PR_SO'] . ",FT_PR_Scene=" . $_REQUEST['FT_PR_SC'] . ",FT_Turn_P3=" . intval($_REQUEST['FT_Turn_P3']) . ",FT_P3_Bet=" . $_REQUEST['FT_P3_SO'] . ",FT_P3_Scene=" . $_REQUEST['FT_P3_SC'] . " where id='$parents_id'";
                        DB::select($mysql);
                        break;
                    case "BK":
                        $mysql = "update web_member_data set BK_Turn_R=" . $_REQUEST['BK_Turn_R'] . ",BK_R_Bet=" . $_REQUEST['BK_R_SO'] . ",BK_R_Scene=" . $_REQUEST['BK_R_SC'] . ",BK_Turn_OU=" . $_REQUEST['BK_Turn_OU'] . ",BK_OU_Bet=" . $_REQUEST['BK_OU_SO'] . ",BK_OU_Scene=" . $_REQUEST['BK_OU_SC'] . ",BK_Turn_RE=" . $_REQUEST['BK_Turn_RE'] . ",BK_RE_Bet=" . $_REQUEST['BK_RE_SO'] . ",BK_RE_Scene=" . $_REQUEST['BK_RE_SC'] . ",BK_Turn_ROU=" . $_REQUEST['BK_Turn_ROU'] . ",BK_ROU_Bet=" . $_REQUEST['BK_ROU_SO'] . ",BK_ROU_Scene=" . $_REQUEST['BK_ROU_SC'] . ",BK_Turn_EO=" . $_REQUEST['BK_Turn_EO'] . ",BK_EO_Bet=" . $_REQUEST['BK_EO_SO'] . ",BK_EO_Scene=" . $_REQUEST['BK_EO_SC'] . ",BK_Turn_PR=" . $_REQUEST['BK_Turn_PR'] . ",BK_PR_Bet=" . $_REQUEST['BK_PR_SO'] . ",BK_PR_Scene=" . $_REQUEST['BK_PR_SC'] . ",BK_Turn_P3=" . intval($_REQUEST['BK_Turn_P3']) . ",BK_P3_Bet=" . $_REQUEST['BK_P3_SO'] . ",BK_P3_Scene=" . $_REQUEST['BK_P3_SC'] . ",FS_Turn_FS=" . intval($_REQUEST['FS_Turn_FS']) . ",FS_FS_Bet=" . $_REQUEST['FS_FS_SO'] . ",FS_FS_Scene=" . $_REQUEST['FS_FS_SC'] . " where id='$parents_id'";
                        DB::select($mysql);
                        break;
                }
            } else {

                switch ($gtype) {
                    case "FT":
                        $mysql = "update web_agents_data set FT_Turn_R_A=" . $_REQUEST['FT_Turn_R_A'] . ",FT_Turn_R_B=" . $_REQUEST['FT_Turn_R_B'] . ",FT_Turn_R_C=" . $_REQUEST['FT_Turn_R_C'] . ",FT_Turn_R_D=" . $_REQUEST['FT_Turn_R_D'] . ",FT_R_Bet=" . $_REQUEST['FT_R_SO'] . ",FT_R_Scene=" . $_REQUEST['FT_R_SC'] . ",FT_Turn_OU_A=" . $_REQUEST['FT_Turn_OU_A'] . ",FT_Turn_OU_B=" . $_REQUEST['FT_Turn_OU_B'] . ",FT_Turn_OU_C=" . $_REQUEST['FT_Turn_OU_C'] . ",FT_Turn_OU_D=" . $_REQUEST['FT_Turn_OU_D'] . ",FT_OU_Bet=" . $_REQUEST['FT_OU_SO'] . ",FT_OU_Scene=" . $_REQUEST['FT_OU_SC'] . ",FT_Turn_RE_A=" . $_REQUEST['FT_Turn_RE_A'] . ",FT_Turn_RE_B=" . $_REQUEST['FT_Turn_RE_B'] . ",FT_Turn_RE_C=" . $_REQUEST['FT_Turn_RE_C'] . ",FT_Turn_RE_D=" . $_REQUEST['FT_Turn_RE_D'] . ",FT_RE_Bet=" . $_REQUEST['FT_RE_SO'] . ",FT_RE_Scene=" . $_REQUEST['FT_RE_SC'] . ",FT_Turn_ROU_A=" . $_REQUEST['FT_Turn_ROU_A'] . ",FT_Turn_ROU_B=" . $_REQUEST['FT_Turn_ROU_B'] . ",FT_Turn_ROU_C=" . $_REQUEST['FT_Turn_ROU_C'] . ",FT_Turn_ROU_D=" . $_REQUEST['FT_Turn_ROU_D'] . ",FT_ROU_Bet=" . $_REQUEST['FT_ROU_SO'] . ",FT_ROU_Scene=" . $_REQUEST['FT_ROU_SC'] . ",FT_Turn_EO_A=" . $_REQUEST['FT_Turn_EO_A'] . ",FT_Turn_EO_B=" . $_REQUEST['FT_Turn_EO_B'] . ",FT_Turn_EO_C=" . $_REQUEST['FT_Turn_EO_C'] . ",FT_Turn_EO_D=" . $_REQUEST['FT_Turn_EO_D'] . ",FT_EO_Bet=" . $_REQUEST['FT_EO_SO'] . ",FT_EO_Scene=" . $_REQUEST['FT_EO_SC'] . ",FT_Turn_RM=" . $_REQUEST['FT_Turn_RM'] . ",FT_RM_Bet=" . $_REQUEST['FT_RM_SO'] . ",FT_RM_Scene=" . $_REQUEST['FT_RM_SC'] . ",FT_Turn_M=" . $_REQUEST['FT_Turn_M'] . ",FT_M_Bet=" . $_REQUEST['FT_M_SO'] . ",FT_M_Scene=" . $_REQUEST['FT_M_SC'] . ",FT_Turn_PD=" . $_REQUEST['FT_Turn_PD'] . ",FT_PD_Bet=" . $_REQUEST['FT_PD_SO'] . ",FT_PD_Scene=" . $_REQUEST['FT_PD_SC'] . ",FT_Turn_T=" . $_REQUEST['FT_Turn_T'] . ",FT_T_Bet=" . $_REQUEST['FT_T_SO'] . ",FT_T_Scene=" . $_REQUEST['FT_T_SC'] . ",FT_Turn_F=" . $_REQUEST['FT_Turn_F'] . ",FT_F_Bet=" . $_REQUEST['FT_F_SO'] . ",FT_F_Scene=" . $_REQUEST['FT_F_SC'] . ",FT_Turn_P=" . $_REQUEST['FT_Turn_P'] . ",FT_P_Bet=" . $_REQUEST['FT_P_SO'] . ",FT_P_Scene=" . $_REQUEST['FT_P_SC'] . ",FT_Turn_PR=" . $_REQUEST['FT_Turn_PR'] . ",FT_PR_Bet=" . $_REQUEST['FT_PR_SO'] . ",FT_PR_Scene=" . $_REQUEST['FT_PR_SC'] . ",FT_Turn_P3=" . $_REQUEST['FT_Turn_P3'] . ",FT_P3_Bet=" . $_REQUEST['FT_P3_SO'] . ",FT_P3_Scene=" . $_REQUEST['FT_P3_SC'] . " where ID='$parents_id'";
                        DB::select($mysql);
                        break;
                    case "BK":
                        $mysql = "update web_agents_data set BK_Turn_R_A=" . $_REQUEST['BK_Turn_R_A'] . ",BK_Turn_R_B=" . $_REQUEST['BK_Turn_R_B'] . ",BK_Turn_R_C=" . $_REQUEST['BK_Turn_R_C'] . ",BK_Turn_R_D=" . $_REQUEST['BK_Turn_R_D'] . ",BK_R_Bet=" . $_REQUEST['BK_R_SO'] . ",BK_R_Scene=" . $_REQUEST['BK_R_SC'] . ",BK_Turn_OU_A=" . $_REQUEST['BK_Turn_OU_A'] . ",BK_Turn_OU_B=" . $_REQUEST['BK_Turn_OU_B'] . ",BK_Turn_OU_C=" . $_REQUEST['BK_Turn_OU_C'] . ",BK_Turn_OU_D=" . $_REQUEST['BK_Turn_OU_D'] . ",BK_OU_Bet=" . $_REQUEST['BK_OU_SO'] . ",BK_OU_Scene=" . $_REQUEST['BK_OU_SC'] . ",BK_Turn_RE_A=" . $_REQUEST['BK_Turn_RE_A'] . ",BK_Turn_RE_B=" . $_REQUEST['BK_Turn_RE_B'] . ",BK_Turn_RE_C=" . $_REQUEST['BK_Turn_RE_C'] . ",BK_Turn_RE_D=" . $_REQUEST['BK_Turn_RE_D'] . ",BK_RE_Bet=" . $_REQUEST['BK_RE_SO'] . ",BK_RE_Scene=" . $_REQUEST['BK_RE_SC'] . ",BK_Turn_ROU_A=" . $_REQUEST['BK_Turn_ROU_A'] . ",BK_Turn_ROU_B=" . $_REQUEST['BK_Turn_ROU_B'] . ",BK_Turn_ROU_C=" . $_REQUEST['BK_Turn_ROU_C'] . ",BK_Turn_ROU_D=" . $_REQUEST['BK_Turn_ROU_D'] . ",BK_ROU_Bet=" . $_REQUEST['BK_ROU_SO'] . ",BK_ROU_Scene=" . $_REQUEST['BK_ROU_SC'] . ",BK_Turn_EO_A=" . $_REQUEST['BK_Turn_EO_A'] . ",BK_Turn_EO_B=" . $_REQUEST['BK_Turn_EO_B'] . ",BK_Turn_EO_C=" . $_REQUEST['BK_Turn_EO_C'] . ",BK_Turn_EO_D=" . $_REQUEST['BK_Turn_EO_D'] . ",BK_EO_Bet=" . $_REQUEST['BK_EO_SO'] . ",BK_EO_Scene=" . $_REQUEST['BK_EO_SC'] . ",BK_Turn_PR=" . $_REQUEST['BK_Turn_PR'] . ",BK_PR_Bet=" . $_REQUEST['BK_PR_SO'] . ",BK_PR_Scene=" . $_REQUEST['BK_PR_SC'] . ",BK_Turn_P3=" . $_REQUEST['BK_Turn_P3'] . ",BK_P3_Bet=" . $_REQUEST['BK_P3_SO'] . ",BK_P3_Scene=" . $_REQUEST['BK_P3_SC'] . ",FS_Turn_FS=" . $_REQUEST['FS_Turn_FS'] . ",FS_FS_Bet=" . $_REQUEST['FS_FS_SO'] . ",FS_FS_Scene=" . $_REQUEST['FS_FS_SC'] . " where ID='$parents_id'";
                        DB::select($mysql);
                        break;
                }
            }

            $response['message'] = "Company Data updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMoneyAgency(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "agent" => "required|string",
                "Money" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $logined_user = $request->user();

            $_REQUEST = $request->all();

            $agent = $_REQUEST["agent"];
            $admin = $_REQUEST["admin"];
            $newmoney = $_REQUEST["Money"];

            $web_agent = WebAgent::where("UserName", $agent)->first();
            $ag_money = $web_agent["Money"];
            $date = date("Y-m-d");
            $datetime = date("Y-m-d H:i:s");

            if ($newmoney != $ag_money) {
                $kk = $newmoney - $ag_money;
                DB::select("update web_agents_data set Money=Money+$kk,Credit=Credit+$kk where UserName='$agent'");
                if ($kk > 0) {
                    $sql = "insert into web_sys800_data set Checked=1,Payway='AG',Gold='$kk',AddDate='$date',Type='S',UserName='代理$agent',Agents='$agent',Admin='$admin',CurType='RMB',Date='$datetime',Bank_Address='新余额:$newmoney',Bank_Account='旧余额:$ag_money',Order_Code='代理商充值'";
                    DB::select($sql);
                } else {
                    $sql = "insert into web_sys800_data set Checked=1,Payway='AG',Gold='$kk',AddDate='$date',Type='T',UserName='代理$agent',Agents='$agent',Admin='$admin',CurType='RMB',Date='$datetime',Bank_Address='新余额:$newmoney',Bank_Account='旧余额:$ag_money',Order_Code='代理商提款'";
                    DB::select($sql);
                }
            }

            $response['message'] = "Agent Money updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMember(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $_REQUEST = $request->all();
            $id = $_REQUEST["id"];
            $username = $_REQUEST["UserName"];
            //$gold=$_REQUEST["maxcredit"];//总信用额度
            $pasd = $_REQUEST["password"]; //密码
            $Address = $_REQUEST["Address"] ?? ""; //取款密码
            $Bank_Account = $_REQUEST["Bank_Account"] ?? ""; //银行账号
            $Bank_Address = $_REQUEST["Bank_Address"] ?? ""; //开户地址

            $alias = $_REQUEST["Alias"] ?? ""; //名称
            $type = $_REQUEST['Type'] ?? ""; //开放盘口
            $fanshui = $_REQUEST['fanshui'] ?? ""; //一键反水
            $fanshui_cp = $_REQUEST['fanshui_cp'] ?? ""; //彩票反水
            $fanshui_zr = $_REQUEST['fanshui_zr'] ?? ""; //真人反水
            $fanshui_dz = $_REQUEST['fanshui_dz'] ?? ""; //电子反水
            $fanshui_ky = $_REQUEST['fanshui_ky'] ?? ""; //电子反水
            $question = $_REQUEST['question'] ?? ""; //提示问题
            $answer = $_REQUEST['answer'] ?? ""; //答案

            $VIP = $_REQUEST['VIP'] ?? ""; //VIP
            $Phone = $_REQUEST['Phone'] ?? ""; //手机号码
            $QQ = $_REQUEST['QQ'] ?? ""; //QQ
            $Notes = $_REQUEST['Notes'] ?? ""; //备注
            // $newmoney = $_REQUEST["Money"] ?? "";
            $credit = $_REQUEST["Credit"] ?? "";

            $operation_type = $_REQUEST["operation_type"] ?? "";
            $more_money = $_REQUEST["more_money"] ?? "";

            $condition_multiplier = $_REQUEST["condition_multiplier"];
            $withdrawal_condition = $_REQUEST["withdrawal_condition"];

            $withdraw_condition_type = $_REQUEST["withdraw_condition_type"];
            $withdraw_more_money = $_REQUEST["withdraw_more_money"];

            $user = User::find($id);

            $money = $user["Money"];
            $bonus_money = $user["bonus_amount"] ?? 0;
            $agent = $user["Agents"];
            $world = $user["World"];
            $corprator = $user["Corprator"];
            $super = $user["Super"];
            $admin = $user["Admin"];

            // $kk = (int)$newmoney - $money;
            $date = date("Y-m-d");
            $datetime = date("Y-m-d H:i:s");

            $mysql = "update web_member_data set OpenType='$type',fanshui='$fanshui',fanshui_cp='$fanshui_cp',fanshui_zr='$fanshui_zr',fanshui_dz='$fanshui_dz',fanshui_ky='$fanshui_ky',VIP='$VIP',Bank_Address='$Bank_Address',Bank_Account='$Bank_Account',Notes='$Notes',question='$question',answer='$answer',Credit='$credit',condition_multiplier='$condition_multiplier' where id='$id'";

            DB::select($mysql);

            if ($username != "") {

                User::where("id", $id)->update([
                    "UserName" => $username,
                ]);
            }

            if ($pasd != "") {

                User::where("id", $id)->update([
                    "password" => Hash::make($pasd),
                ]);
            }

            $login_user = $request->user();

            $loginname = $login_user["UserName"];

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $Order_Code = 'TK' . date("YmdHis", time() + 12 * 3600) . mt_rand(1000, 9999);
            $sys_800 = new Sys800;

            if ($more_money != "" && $operation_type == "1") {

                $newmoney = (int) $money + (int) $more_money;

                $data = array(
                    "Payway" => "W",
                    "Gold" => $more_money,
                    "previousAmount" => $money,
                    "currentAmount" => $newmoney,
                    "AddDate" => $current_time,
                    "Type" => "S",
                    "Type2" => "1",
                    "UserName" => $username,
                    "Agents" => $agent,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => "RMB",
                    "Name" => $alias,
                    "User" => $loginname,
                    "Checked" => 1,
                    "Date" => $current_time,
                    "Order_Code" => $Order_Code,
                    "created_at" => $current_time,
                    "Notes" => "",
                );

                $sys_800->create($data);

                $user["Money"] = $newmoney;
                $user->save();

                $new_log = new MoneyLog();
                $new_log->user_id = $user["id"];
                $new_log->order_num = $Order_Code;
                $new_log->about = $user["UserName"] . "人工加款";
                $new_log->update_time = $current_time;
                $new_log->type = $user["UserName"] . "人工加款";
                $new_log->order_value = $more_money;
                $new_log->assets = $money;
                $new_log->balance = $newmoney;
                $new_log->save();
            }

            if ($more_money != "" && $operation_type == "3") {

                $newmoney = (int) $bonus_money + (int) $more_money;

                $data = array(
                    "Payway" => "W",
                    "Gold" => $more_money,
                    "previousAmount" => $bonus_money,
                    "currentAmount" => $newmoney,
                    "AddDate" => $current_time,
                    "Type" => "S",
                    "Type2" => "2",
                    "UserName" => $username,
                    "Agents" => $agent,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => "RMB",
                    "Name" => $alias,
                    "User" => $loginname,
                    "Checked" => 1,
                    "Date" => $current_time,
                    "Order_Code" => $Order_Code,
                    "created_at" => $current_time,
                    "Notes" => "",
                );

                $sys_800->create($data);

                $user["bonus_amount"] = $newmoney;
                $user->save();

                $new_log = new MoneyLog();
                $new_log->user_id = $user["id"];
                $new_log->order_num = $Order_Code;
                $new_log->about = $user["UserName"] . "彩金加款";
                $new_log->update_time = $current_time;
                $new_log->type = $user["UserName"] . "彩金加款";
                $new_log->order_value = $more_money;
                $new_log->assets = $money;
                $new_log->balance = $newmoney;
                $new_log->save();
            }

            $Order_Code = 'CK' . date("YmdHis", time() + 12 * 3600) . mt_rand(1000, 9999);

            if ($more_money != "" && $operation_type == "2") {
                // $sql = "insert into web_sys800_data set Checked=1,Payway='AG',Gold='$kk',AddDate='$date',Type='T',UserName='會員$user',Agents='$agent',Admin='$admin',CurType='RMB',Date='$datetime',Bank_Address='新余额:$newmoney',Bank_Account='旧余额:$money',Order_Code='會員提款'";
                // DB::select($sql);

                $newmoney = (int) $money - (int) $more_money;

                $data = array(
                    "Payway" => "W",
                    "Gold" => $more_money,
                    "previousAmount" => $money,
                    "currentAmount" => $newmoney,
                    "AddDate" => $current_time,
                    "Type" => "T",
                    "UserName" => $username,
                    "Agents" => $agent,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => "RMB",
                    "Name" => $alias,
                    "User" => $login_user["UserName"],
                    "Date" => $current_time,
                    "Order_Code" => $Order_Code,
                    "created_at" => $current_time,
                    "Checked" => 1,
                    "Notes" => "",
                );

                $sys_800->create($data);

                $new_log = new MoneyLog();
                $new_log->user_id = $user["id"];
                $new_log->order_num = $Order_Code;
                $new_log->about = $user["UserName"] . "人工扣款";
                $new_log->update_time = $current_time;
                $new_log->type = $user["UserName"] . "人工扣款";
                $new_log->order_value = $more_money;
                $new_log->assets = $money;
                $new_log->balance = $newmoney;
                $new_log->save();

                $user["Money"] = $newmoney;
                $user->save();
            }

            if ($more_money != "" && $operation_type == "4") {
                // $sql = "insert into web_sys800_data set Checked=1,Payway='AG',Gold='$kk',AddDate='$date',Type='T',UserName='會員$user',Agents='$agent',Admin='$admin',CurType='RMB',Date='$datetime',Bank_Address='新余额:$newmoney',Bank_Account='旧余额:$money',Order_Code='會員提款'";
                // DB::select($sql);

                $newmoney = (int) $bonus_money - (int) $more_money;

                $data = array(
                    "Payway" => "W",
                    "Gold" => $more_money,
                    "previousAmount" => $bonus_money,
                    "currentAmount" => $newmoney,
                    "AddDate" => $current_time,
                    "Type" => "T",
                    "UserName" => $username,
                    "Agents" => $agent,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => "RMB",
                    "Name" => $alias,
                    "User" => $login_user["UserName"],
                    "Date" => $current_time,
                    "Order_Code" => $Order_Code,
                    "created_at" => $current_time,
                    "Checked" => 1,
                    "Notes" => "",
                );

                $sys_800->create($data);

                $new_log = new MoneyLog();
                $new_log->user_id = $user["id"];
                $new_log->order_num = $Order_Code;
                $new_log->about = $user["UserName"] . "彩金扣款";
                $new_log->update_time = $current_time;
                $new_log->type = $user["UserName"] . "彩金扣款";
                $new_log->order_value = $more_money;
                $new_log->assets = $money;
                $new_log->balance = $newmoney;
                $new_log->save();

                $user["bonus_amount"] = $newmoney;
                $user->save();
            }

            $Order_Code = 'TK' . date("YmdHis", time() + 12 * 3600) . mt_rand(1000, 9999);
            $sys_800 = new Sys800;

            // Add money to the wash amount

            if ($withdraw_more_money != "" && $withdraw_condition_type == "1") {

                $newmoney = (int) $withdrawal_condition + (int) $withdraw_more_money;

                $data = array(
                    "Payway" => "W",
                    "Gold" => $withdraw_more_money,
                    "previousAmount" => $withdrawal_condition,
                    "currentAmount" => $newmoney,
                    "AddDate" => $current_time,
                    "Type" => "S",
                    "Type2" => "5", // wash amount type
                    "UserName" => $username,
                    "Agents" => $agent,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => "RMB",
                    "Name" => $alias,
                    "User" => $loginname,
                    "Checked" => 1,
                    "Date" => $current_time,
                    "Order_Code" => $Order_Code,
                    "created_at" => $current_time,
                    "Notes" => "洗码金额加款",
                );

                $sys_800->create($data);

                $user["withdrawal_condition"] = $newmoney;
                $user->save();

                $new_log = new MoneyLog();
                $new_log->user_id = $user["id"];
                $new_log->order_num = $Order_Code;
                $new_log->about = $user["UserName"] . "洗码金额加款";
                $new_log->update_time = $current_time;
                $new_log->type = $user["UserName"] . "洗码金额加款";
                $new_log->order_value = $withdraw_more_money;
                $new_log->assets = $withdrawal_condition;
                $new_log->balance = $newmoney;
                $new_log->save();
            }

            $Order_Code = 'CK' . date("YmdHis", time() + 12 * 3600) . mt_rand(1000, 9999);

            if ($withdraw_more_money != "" && $withdraw_condition_type == "2") {

                $newmoney = (int) $withdrawal_condition - (int) $withdraw_more_money;

                $data = array(
                    "Payway" => "W",
                    "Gold" => $withdraw_more_money,
                    "previousAmount" => $withdrawal_condition,
                    "currentAmount" => $newmoney,
                    "AddDate" => $current_time,
                    "Type" => "T",
                    "UserName" => $username,
                    "Agents" => $agent,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => "RMB",
                    "Name" => $alias,
                    "User" => $login_user["UserName"],
                    "Date" => $current_time,
                    "Order_Code" => $Order_Code,
                    "created_at" => $current_time,
                    "Checked" => 1,
                    "Notes" => "洗码金额扣款",
                );

                $sys_800->create($data);

                $new_log = new MoneyLog();
                $new_log->user_id = $user["id"];
                $new_log->order_num = $Order_Code;
                $new_log->about = $user["UserName"] . "洗码金额扣款";
                $new_log->update_time = $current_time;
                $new_log->type = $user["UserName"] . "洗码金额扣款";
                $new_log->order_value = $withdraw_more_money;
                $new_log->assets = $withdrawal_condition;
                $new_log->balance = $newmoney;
                $new_log->save();

                $user["withdrawal_condition"] = $newmoney;
                $user->save();
            }

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = "会员更新";
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->level = 2;

            $web_mem_log_data->save();

            $response['message'] = "Member data updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
}
