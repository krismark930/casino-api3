<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebAgent;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\SysConfig;
use App\Models\User;

class AdminStatisticsController extends Controller
{
    public function getDividendDetails(Request $request) {

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

            $id = $request_data["id"] ?? "";
            $start_time = $request_data["start_time"]." 00:00:00";
            $end_time = $request_data["end_time"]." 23:59:59";

            $web_agent = WebAgent::where("level", "D");

            if ($id != "") {
                $web_agent = $web_agent->where("ID", $id);
            }

            $agents_result = WebAgent::where("level", "D")->get();

            $agents = array();

            foreach($agents_result as $item) {
                array_push($agents, array("label" => $item["UserName"], "value" => $item["ID"]));
            }

            $web_agent = $web_agent->first();

            $AG_UserName=$web_agent['UserName'];  //代理商账号
            $zc=intval($web_agent['D_Point']);   //占成比率

            //获取充值会员列表
            $UserName=$this->getUsers("select distinct(UserName) FROM web_sys800_data where AddDate>='$start_time' and AddDate<='$end_time'",$AG_UserName);
            if ($UserName == "") {
                $caijin=0;
            } else {
                $sql="select sum(gold) from web_sys800_data where UserName in ($UserName) and  FIND_IN_SET(`Bank_Account`,'彩金,体验金,返水,返利,银行返利,银行卡返利') and AddDate>='".$start_time."' and AddDate<='".$end_time."' and Type='S' and Cancel=0";

                $result = DB::select($sql);
                $row = get_object_vars($result[0]);
                $caijin=empty($row["gold"]) ? 0 : $row["gold"];                
            }

            //获取玩体育会员列表
            $UserName=$this->getUsers("select distinct(M_Name) FROM web_report_data where M_Date>='$start_time' and M_Date<='$end_time'",$AG_UserName);
            if($UserName == "") {
                $ty_ztze=0;
                $ty_xztze=0;
                $ty_hyjg=0;
            } else {
                $sql="select sum(BetScore) as ztze, sum(VGOLD) as xztze, sum(M_Result) as hyjg  from web_report_data  where M_Name in ($UserName) and M_Date>='".$start_time."' and M_Date<='".$end_time."' and Cancel='0'";
                $result = DB::select($sql);
                $row=get_object_vars($result[0]);
                $ty_ztze=$row['ztze'];
                $ty_xztze=$row['xztze'];
                $ty_hyjg=-$row['hyjg'];                
            }

            //获取玩时时彩会员列表
            $UserName=$this->getUsers("select distinct(g_nid) FROM g_zhudan where g_date>='$start_time' and g_date<='$end_time'",$AG_UserName);
            if ($UserName == "") {
                $ssc_ztze=0;
                $ssc_xztze=0;
                $ssc_hyjg=-0;
            } else {
                $sql="select sum(g_jiner) as ztze, sum(g_jiner) as xztze, sum(g_win) as hyjg  from g_zhudan  where g_nid in ($UserName) and g_date>='$start_time' and g_date<='$end_time'";
                $result = DB::select($sql);
                $row=get_object_vars($result[0]);
                $ssc_ztze=$row['ztze'];
                $ssc_xztze=$row['xztze'];
                $ssc_hyjg=-$row['hyjg'];                
            }

            //获取玩时时彩会员列表
            $UserName=$this->getUsers("select distinct(`username`) FROM order_lottery o where o.bet_time>='$start_time' and o.bet_time<='$end_time'",$AG_UserName);
            if($UserName == "") {
                $ssc_ztze2=0;
                $ssc_xztze2=0;
                $ssc_hyjg2=0;
                $ssc_hyjg2=0;
            } else {
                $sql="select sum(o_sub.bet_money) as sum_m,sum(IF(o_sub.is_win=1,o_sub.win,0))+sum(if(o_sub.is_win=2,o_sub.bet_money,0)) as gwin,sum(o_sub.fs) as user_ds from order_lottery o,order_lottery_sub o_sub where o.bet_time>='$start_time' and o.bet_time<='$end_time' and o.username in($UserName) and o_sub.status=1 and  o.order_num=o_sub.order_num";
                $result = DB::select($sql);
                $row=get_object_vars($result[0]);
                $ssc_ztze2=$row['sum_m'];
                $ssc_xztze2=$row['sum_m'];
                $ssc_hyjg2=$row['gwin']-$row['sum_m'];
                $ssc_hyjg2=-$ssc_hyjg2;                
            }

            //获取玩真人会员列表
            $UserName=$this->getUsers("select distinct(UserName) FROM web_report_zr o where o.betTime>='$start_time' and o.betTime<='$end_time'",$AG_UserName);
            if ($UserName == "") {
                $zr_ztze=0;
                $zr_xztze=0;
                $zr_hyjg=0;

            } else {
                $sql="select sum(betAmount) as betAmount, sum(validBetAmount) as validBetAmount, sum(netAmount) as netAmount  from web_report_zr  where UserName in ($UserName) and betTime>='$start_time' and betTime<='$end_time'";
                //echo $sql;
                $result = DB::select($sql);
                $row=get_object_vars($result[0]);
                $zr_ztze=$row['betAmount'];
                $zr_xztze=$row['validBetAmount'];
                $zr_hyjg=-$row['netAmount'];                
            }


            //获取玩捕鱼王会员列表
            $UserName=$this->getUsers("select distinct(UserName) FROM web_report_htr o where o.SceneStartTime>='$start_time' and o.SceneEndTime<='$end_time'",$AG_UserName);
            if ($UserName == "") {
                $htr_ztze=0;
                $htr_xztze=0;
                $htr_hyjg=0;   
            } else {
                $sql="select sum(Cost) as Cost, sum(transferAmount) as transferAmount from web_report_htr  where UserName in ($UserName) and SceneStartTime>='$start_time' and SceneEndTime<='$end_time'";
                $result = DB::select($sql);
                $row=get_object_vars($result[0]);
                $htr_ztze=$row['Cost'];
                $htr_xztze=$row['Cost'];
                $htr_hyjg=-$row['transferAmount'];                
            }

            //获取玩六合用户会员列表
            $UserName=$this->getUsers("select distinct(username) FROM `ka_tan` where adddate>='$start_time' and adddate<='$end_time'",$AG_UserName);
            if ($UserName == "") {
                $lhc_ztze=0;
                $lhc_xztze=0;
                $user_ds=0;
                $zjje=0;
                $lhc_hyjg=0; 
            } else {
                $sql="select sum(sum_m) as ztze   from `ka_tan`   where username in ($UserName) and adddate>='$start_time' and adddate<='$end_time'";
                $result = DB::select($sql);
                $row=get_object_vars($result[0]);
                $lhc_ztze=$row['ztze'];

                $sql="select sum(sum_m) as xztze, sum(sum_m*user_ds/100) as user_ds from `ka_tan`   where username in ($UserName) and adddate>='$start_time' and adddate<='$end_time' and bm<>2";
                $result = DB::select($sql);
                $row=get_object_vars($result[0]);
                $lhc_xztze=$row['xztze'];
                $user_ds=$row['user_ds'];

                $sql="select sum(sum_m*rate) as zjje from `ka_tan` where username in ($UserName) and adddate>='$start_time' and adddate<='$end_time' and bm=1";
                $result = DB::select($sql);
                $row=get_object_vars($result[0]);
                $zjje=$row['zjje'];
                $lhc_hyjg=$lhc_xztze-$user_ds-$zjje;                
            }

            $okpay=$ty_hyjg+$ssc_hyjg+$ssc_hyjg2+$zr_hyjg+$htr_hyjg+$lhc_hyjg-$caijin;
            if($okpay<0) $okpay=0;
            $okpay2=$okpay*$zc/100;

            $data = array();

            array_push($data, array("title" => "体育博弈", "value_1" => round($ty_ztze, 2), "value_2" => round($ty_xztze, 2), "value_3" => round($ty_hyjg, 2)));
            array_push($data, array("title" => "时时彩", "value_1" => round($ssc_ztze+$ssc_ztze2, 2), "value_2" => round($ssc_xztze+$ssc_xztze2, 2), "value_3" => round($ssc_hyjg+$ssc_hyjg2, 2)));
            array_push($data, array("title" => "六合彩", "value_1" => round($lhc_ztze), "value_2" => round($lhc_xztze, 2), "value_3" => round($lhc_ztze)));
            array_push($data, array("title" => "真人", "value_1" => round($zr_ztze), "value_2" => round($zr_xztze), "value_3" => round($zr_hyjg)));
            array_push($data, array("title" => "AG捕鱼王", "value_1" => round($htr_ztze), "value_2" => round($htr_xztze), "value_3" => round($htr_hyjg)));

            $total_value = array("value_1" => $okpay, "value_2" => $caijin, "value_3" => $okpay2);

            $response["total_value"] = $total_value;
            $response["data"] = $data;
            $response["agents"] = $agents;
            $response['message'] = "Devidend Details Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getDailyAccounts(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $startdate=$request_data['startdate'] ?? "";
            $overdate=$request_data['overdate'] ?? "";
            $username=$request_data['username'] ?? "";
            $Agent=$request_data['Agent'] ?? "";
            $Type=$request_data['Type'] ?? "";
            if($Type=='') $tj='';
            if($Type=='S') $tj=" and Type='S'";
            if($Type=='T') $tj=" and Type='T'";
            if($Type=='C') $tj=" and Type2=2";
            if($Type=='F') $tj=" and (Locate('返水',Bank_Account)>0 or Locate('反水',Bank_Account)>0)";
            $TJ_Agent='';
            if($Agent<>''){
                $TJ_Agent=" and Agents='$Agent'";
            }
            if($startdate=='') $startdate=date("Y-m-d");
            if($overdate=='')  $overdate=date("Y-m-d");

            if($username<>'') $tj=$tj." and UserName like '%$username%'";
            $sql = "select distinct Agents from web_sys800_data where AddDate>='$startdate' and AddDate<='$overdate'  $tj and Type2<>3 order by ID desc";  //获取代理商
            $result = DB::select($sql);
            $Agent_arr=array();
            foreach($result as $item) {
                $item = get_object_vars($item);
                array_push($Agent_arr, array("label" => $item["Agents"], "value" => $item["Agents"]));
            }

            $sql = "select * from web_sys800_data where AddDate>='$startdate' and AddDate<='$overdate'  $tj $TJ_Agent and Type2<>3 order by ID desc";
            $data=DB::select($sql);
            $CK=0;$TK=0;

            foreach($data as $row) {
                $row = get_object_vars($row);
                if($row['Type']=='S' and $row['Cancel']==0) $CK=$CK+(int)$row['Gold'];
                if($row['Type']=='T' and $row['Cancel']==0) $TK=$TK+(int)$row['Gold'];
                $Phone=$row['Phone'];
                if(strlen($Phone)>=8){
                    $Phone=substr($Phone,0,4).'****'.substr($Phone,8,3);
                }
                $row["Phone"] = $Phone;
                if($row['Type']=='S'){
                    if ($row['Cancel']==1){
                        $row["Cancel"] = '<font color=red>存入已拒绝</font><br>';
                    }else{
                        $row["Cancel"] = '<font color=blue>已存入</font><br>';
                    }
                }else{
                    if ($row['Cancel']==1){
                        $row["Cancel"] = "<font color='red'>已恢复</font><br>";
                    }else {
                        $row["Cancel"] = '<font color=blue>已提出</font><br>';
                    }
                }
                $row = $row;
            }

            $total_value = array("CK" => $CK, "TK" => $TK);

            $response["total_value"] = $total_value;
            $response["data"] = $data;
            $response["agents"] = $Agent_arr;
            $response['message'] = "Daily Accounts Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSystemLogs(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $date_start=$request_data['date_start'] ?? "";
            $parents_id=$request_data['parents_id'] ?? "";
            $active=$request_data['active'] ?? "";
            $page=$request_data["page"] ?? 1;
            $search=$request_data["search"] ?? "";
            $name=$request_data['name'];

            $datetime=date("Y-m-d H:i:s",time()-10*86400);
            $sql = "delete from web_mem_log_data where LoginTime <'$datetime'";
            DB::select($sql);

            if ($active==1){
                $sql = "update web_agents_data set Oid='logout',Online=0,LogoutTime=now() where UserName='$name'";
                DB::select($sql);
                $sql = "update web_system_data set Oid='logout',Online=0,LogoutTime=now() where UserName='$name'";
                DB::select($sql);
                $sql = "delete from web_mem_log_data where UserName='$name'";
                DB::select($sql);
            }
            if ($page==''){
                $page=1;
            }
            if ($search!=''){
                $search="and (UserName like '%$search%' or LoginTime like '%$search%' or Level like '%$search%')";
            }

            if ($parents_id==''){
                $sql = "select * from web_mem_log_data where LoginTime like '%$date_start%' $search group by UserName order by ID desc";
            }else{
                $sql = "select * from web_mem_log_data where LoginTime like '%$date_start%' and UserName='$parents_id' order by ID desc";
            }
            $result = DB::select($sql);
            $cou=count($result);
            $page_size=20;
            $page_count=ceil($cou/$page_size);
            $offset=($page-1)*$page_size;
            $mysql=$sql."  limit $offset,$page_size;";
            $result = DB::select($mysql);
            $cou=count($result);
            if ($cou==0){
                $page_count=1;
            }

            $mysql = "select UserName,Level from web_mem_log_data where Level!='' and LoginTime like '%$date_start%' and UserName!='dan555' and UserName!='dan222' group by UserName order by ID desc";

            $agents_result = DB::select($mysql);

            $agents = array();

            array_push($agents, array("label" => "全部", "value" => ""));

            foreach($agents_result as $row) {
                $row = get_object_vars($row);
                array_push($agents, array("label" => $row['UserName']."===".$row['Level'], "value" => $row["UserName"]));
            }

            $response["total_count"] = $page_count;
            $response["data"] = $result;
            $response["agents"] = $agents;
            $response['message'] = "System Logs Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getOnlineData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $username=$request_data['username'] ?? "";
            $active=$request_data['active'] ?? "";
            $level=$request_data['level'] ?? "";
            $lv=$request_data['lv'] ?? "";
            $name=$request_data['name'] ?? "";

            $date=date('Y-m-d');

            if ($active==1){
                $sql = "update web_member_data set Oid='logout',Online='0',LogoutTime=now() where UserName='$username'";
                DB::select($sql);
                $sql = "update web_agents_data set Oid='logout',Online='0',LogoutTime=now() where UserName='$username'";
                DB::select($sql);
            }
            if($level==''){
               $level='member';
            }
            if($level=='member' or $level==''){
               $data='web_member_data';
            }else if($level=='agents'){
               $data='web_agents_data';
            }
            if ($name!=''){
                $n_sql="and UserName like '%$name%'";
            }else{
                $n_sql='';
            }

            $n_sql = $n_sql."and Online = 1";

            // $sql="select * from $data where Online=1 and Oid!='logout' and UserName<>'guest' $n_sql order by id desc";
            $sql="select * from $data where UserName<>'guest' $n_sql order by id desc";
            $result=DB::select($sql);

            $response["data"] = $result;
            $response['message'] = "Oline Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateSysConfig(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $AG = $request_data["AG"];
            $BBIN = $request_data["BBIN"];
            $OG = $request_data["OG"];
            $MG = $request_data["MG"];
            $PT = $request_data["PT"];
            $KY = $request_data["KY"];

            SysConfig::where("id", 1)->update([
                "AG" => $AG,
                "BBIN" => $BBIN,
                "OG" => $OG,
                "MG" => $MG,
                "PT" => $PT,
                "KY" => $KY,
            ]);

            $response['message'] = "SysConfig Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateRealPerson(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];
            $AG_TR = $request_data["AG_TR"];
            $BBIN_TR = $request_data["BBIN_TR"];
            $OG_TR = $request_data["OG_TR"];
            $MG_TR = $request_data["MG_TR"];
            $PT_TR = $request_data["PT_TR"];
            $KY_TR = $request_data["KY_TR"];
            $AG_User = $request_data["AG_User"];
            $AG_Pass = $request_data["AG_Pass"];
            $AG_Type = $request_data["AG_Type"];
            $BBIN_User = $request_data["BBIN_User"];
            $BBIN_Pass = $request_data["BBIN_Pass"];
            $MG_User = $request_data["MG_User"];
            $MG_Pass = $request_data["MG_Pass"];
            $PT_User = $request_data["PT_User"];
            $PT_Pass = $request_data["PT_Pass"];
            $OG_User = $request_data["OG_User"];
            $OG_Limit1 = $request_data["OG_Limit1"];
            $OG_Limit2 = $request_data["OG_Limit2"];
            $KY_User = $request_data["KY_User"];

            User::where("id", $id)->update([
                "AG_TR" => $AG_TR,
                "BBIN_TR" => $BBIN_TR,
                "OG_TR" => $OG_TR,
                "MG_TR" => $MG_TR,
                "PT_TR" => $PT_TR,
                "KY_TR" => $KY_TR,
                "AG_User" => $AG_User,
                "AG_Pass" => $AG_Pass,
                "AG_Type" => $AG_Type,
                "BBIN_User" => $BBIN_User,
                "BBIN_Pass" => $BBIN_Pass,
                "MG_User" => $MG_User,
                "MG_Pass" => $MG_Pass,
                "PT_User" => $PT_User,
                "PT_Pass" => $PT_Pass,
                "OG_User" => $OG_User,
                "OG_Limit1" => $OG_Limit1,
                "OG_Limit2" => $OG_Limit2,
                "KY_User" => $KY_User,
            ]);

            $response['message'] = "Member Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    private function getUsers($sql, $Agents='ddm999') {
        $result = DB::select($sql);
        $Users="";
        foreach($result as $item) {
            foreach($item as $key => $value) {
                $Users=$Users."'".$value."',";
            }
        }
        $Users = trim($Users,",");
        if ($Users == "") {
            return $Users;
        }
        unset($result);
        $sql="SELECT UserName  FROM `web_member_data` where Agents='$Agents' and UserName in (".$Users.") order by UserName";
        $result = DB::select($sql);
        $Users='';
        foreach($result as $item) {
            $item = get_object_vars($item);
            $Users=$Users."'".$item['UserName']."',";
        }
        $Users = trim($Users,",");
        return $Users;
    }
}