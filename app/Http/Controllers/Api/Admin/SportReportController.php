<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Sport;
use App\Models\WebReportData;
use App\Models\User;
use App\Utils\Utils;

class SportReportController extends Controller
{

    public function getSportReport(Request $request) {

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

            $Rep_readme0='赛事尚有{}场未输入完毕';
            $Rep_readme1='赛事结果已输入完毕';
            $Rep_readme2='没有赛事';

            $mdate_t=date('Y-m-d');
            $mdate_y=date('Y-m-d',time()-24*60*60);

            $cou = Sport::where("Type", "FT")->where("M_Date", $mdate_t)->where("MB_Inball", "!=", "")->count();
            $cou1 = Sport::where("Type", "FT")->where("M_Date", $mdate_t)->count();
            if ($cou1==0){
                $ft_caption=$Rep_readme2;//今日没有比赛
            }else if ($cou1-$cou==0){           
                $ft_caption=$Rep_readme1;//今日输入完毕
            }else{          
                $ft_caption=str_replace('{}',$cou1-$cou,$Rep_readme0);//今日尚有多少场未输入完毕
            }
            $cou2 = Sport::where("Type", "FT")->where("M_Date", $mdate_y)->where("MB_Inball", "!=", "")->count();
            $cou3 = Sport::where("Type", "FT")->where("M_Date", $mdate_y)->count();
            if ($cou3==0){      
                $ft_caption1=$Rep_readme2;//昨日没有比赛
            }else if ($cou3-$cou2==0){      
                $ft_caption1=$Rep_readme1;//昨日输入完毕
            }else{  
                $ft_caption1=str_replace('{}',$cou3-$cou2,$Rep_readme0);//昨日尚有多少场未输入完毕
            }
            
            $cou = Sport::where("Type", "BK")->where("M_Date", $mdate_t)->where("MB_Inball", "!=", "")->count();
            $cou1 = Sport::where("Type", "BK")->where("M_Date", $mdate_t)->count();
            if ($cou1==0){
                $bk_caption=$Rep_readme2;//今日没有比赛
            }else if ($cou1-$cou==0){           
                $bk_caption=$Rep_readme1;//今日输入完毕
            }else{          
                $bk_caption=str_replace('{}',$cou1-$cou,$Rep_readme0);//今日尚有多少场未输入完毕
            }
            $cou2 = Sport::where("Type", "BK")->where("M_Date", $mdate_y)->where("MB_Inball", "!=", "")->count();
            $cou3 = Sport::where("Type", "BK")->where("M_Date", $mdate_y)->count();
            if ($cou3==0){      
                $bk_caption1=$Rep_readme2;//昨日没有比赛
            }else if ($cou3-$cou2==0){
                $bk_caption1=$Rep_readme1;//昨日输入完毕
            }else{  
                $bk_caption1=str_replace('{}',$cou3-$cou2,$Rep_readme0);//昨日尚有多少场未输入完毕
            }

            $data = array("ft_caption" => $ft_caption, "ft_caption1" => $ft_caption1, "bk_caption" => $bk_caption, "bk_caption1" => $bk_caption1);

            $response["data"] = $data;
            $response['message'] = 'Sport Report Data fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSportReportTop(Request $request) {

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

            $_REQUEST = $request->all();
            $logined_user = $request->user();

            $lv=$_REQUEST["level"] ?? "";
            $report_kind=$_REQUEST['report_kind'] ?? "";
            $pay_type=$_REQUEST['pay_type'] ?? "";
            $type=$_REQUEST['type'] ?? "";
            $date_start=$_REQUEST['date_start'] ?? "";
            $date_end=$_REQUEST['date_end'] ?? "";
            $gtype=$_REQUEST['gtype'] ?? "";
            $cancel=$_REQUEST['cancel'] ?? "";
            $result_type=$_REQUEST['result_type'] ?? "";
            $next_name=$_REQUEST['next_name'] ?? "";
            $corp_ck=$_REQUEST['corp_ck'] ?? "";
            $act=$_REQUEST['act'] ?? "";
            $m_name=$_REQUEST['m_name'] ?? "";

            $wtype = "";

            $username=$logined_user['UserName'];

            switch ($lv){
                case 'A':
                    $Title="管理";
                    $user='Super';
                    $name="";
                    break;
                case 'B':
                    $Title="公司";
                    $user='Corprator';
                    $name="Super='$next_name' and";
                    break;
                case 'C':
                    $Title="股东";
                    $user='World';
                    $name="Corprator='$next_name' and";
                    break;
                case 'D':
                    $Title="总代理";
                    $user='Agents';
                    $name="World='$next_name' and";
                    break;
                case 'MEM':
                    $Title="代理商";
                    $user='M_Name';
                    $name="Agents='$next_name' and";
                    break;
            }

            if ($lv == "member") {

                $loginfo='查询会员:'.$m_name.'&nbsp;&nbsp;'.$date_start.'至'.$date_end.'报表投注明细';

            } else {

                if ($logined_user['SubUser']==1){
                    $loginfo='子帐号:'.$username.'查询'.$Title.':'.$next_name.'&nbsp;&nbsp;'.$date_start.'至'.$date_end.'报表投注明细';
                }else{
                    $loginfo='查询'.$Title.':'.$next_name.'&nbsp;&nbsp;'.$date_start.'至'.$date_end.'报表投注明细';
                }

            }

            switch ($pay_type){
                case "0":
                    $credit="block";
                    $mgold="block";
                    $pay_type="Pay_Type=0 and ";
                    $rep_pay="信用额度";
                    break;
                case "1":
                    $credit="block";
                    $mgold="block";
                    $pay_type="Pay_Type=1 and ";
                    $rep_pay="现金";
                    break;
                case "":
                    $credit="block";
                    $mgold="block";
                    $pay_type="";
                    $rep_pay="全部";
                    break;
            }

            switch ($gtype){
                case "":
                    $Active="";
                    break;
                case "FT":
                    $Active=" (Active=1 or Active=11) and ";
                    break;
                case "BK":
                    $Active=" (Active=2 or Active=22) and ";
                    break;
                case "BS":
                    $Active=" (Active=3 or Active=33) and ";
                    break;  
                case "TN":
                    $Active=" (Active=4 or Active=44) and ";
                    break;
                case "VB":
                    $Active=" (Active=5 or Active=55) and ";
                    break;
                case "OP":
                    $Active=" (Active=6 or Active=66) and ";
                    break;
                case "FU":
                    $Active=" (Active=7 or Active=77) and ";
                    break;  
                case "FS":
                    $Active=" Active=8 and ";
                    break;
                case "SIX":
                    $Active=" Active=9 and ";
                    break;      
            }   

            switch ($type){
                case "M":
                    $wtype=" Type='M' and ";
                    $Content='全场獨贏';
                    break;
                case "R":
                    $wtype=" Type='R' and ";
                    $Content='全场讓球';
                    break;
                case "OU":
                    $wtype=" Type='OU' and ";
                    $Content='全场大小球';
                    break;
                case "EO":
                    $wtype=" Type='EO' and ";
                    $Content='全场單雙';
                    break;  
                case "VR":
                    $wtype=" Type='VR' and ";
                    $Content='上半場獨贏';
                    break;
                case "VOU":
                    $wtype=" Type='VOU' and ";
                    $Content='上半場讓球';
                    break;
                case "VM":
                    $wtype=" Type='VM' and ";
                    $Content='上半場大小';
                    break;
                case "VEO":
                    $wtype=" Type='VEO' and ";
                    $Content='上半場單雙';
                    break;  
                case "UR":
                    $wtype=" Type='UR' and ";
                    $Content='下半場讓球';
                    break;
                case "UOU":
                    $wtype=" Type='UOU' and ";
                    $Content='下半場大小';
                    break;
                case "UEO":
                    $wtype=" Type='UEO' and ";
                    $Content='下半場單雙';
                    break;  
                case "QR":
                    $wtype=" Type='QR' and ";
                    $Content='单节讓球';
                    break;
                case "QOU":
                    $wtype=" Type='QOU' and ";
                    $Content='单节大小';
                    break;
                case "QEO":
                    $wtype=" Type='QEO' and ";
                    $Content='单节單雙';
                    break;
                case "RM":
                    $wtype=" Type='RM' and";
                    $Content='滾球獨贏';
                    break;          
                case "RB":
                    $wtype=" Type='RB' and";
                    $Content='滾球讓球';
                    break;
                case "ROU":
                    $wtype=" Type='ROU' and";
                    $Content='滾球大小';
                    break;
                case "VRM":
                    $wtype=" Type='VRM' and";
                    $Content='滾球上半場獨贏';
                    break;
                case "VRB":
                    $wtype=" Type='VRB' and";
                    $Content='滾球上半場讓球';
                    break;
                case "VROU":
                    $wtype=" Type='VROU' and";
                    $Content='滾球上半場大小';
                    break;
                case "URB":
                    $wtype=" Type='URB' and";
                    $Content='滾球下半場讓球';
                    break;
                case "UROU":
                    $wtype=" Type='UROU' and";
                    $Content='滾球下半場大小球';
                    break;  
                case "PD":
                    $wtype=" Type='PD' and ";
                    $Content='波胆';
                    break;
                case "VPD":
                    $wtype=" Type='VPD' and ";
                    $Content='半场波胆';
                    break;
                case "T":
                    $wtype=" Type='T' and ";
                    $Content='入球数';
                    break;  
                case "F":
                    $wtype=" Type='F' and ";
                    $Content='半全场';
                    break;
                case "PC":
                    $wtype=" Type='PC' and ";
                    $Content='混合过关';
                    break;
                case "CS":
                    $wtype=" Type='CS' and ";
                    $Content='冠军赛';
                    break;
                case "":
                    $wtype="";
                    $Content='全部';
                    break;
            }

            switch ($result_type){
                case "":
                    $m_result="";
                    break;
                case "Y":
                    $m_result=" M_Result!='' and ";
                    break;
                case "N":
                    $m_result=" M_Result='' and ";
                    break;
            }

            if ($report_kind=='A'){
                $kind="总帐";
                $cancel='';
            }else if ($report_kind=='C'){
                $kind="分类帐";
                $cancel='';
            }else if ($report_kind=='D'){
                $kind="已注销注单";
                $cancel=' Cancel=1 and';
            }else if ($report_kind=='E'){
                $kind="未接受注单";
                $cancel=' Confirmed=-17 and';
            }
                
            if ($wtype==''){
                $awtype='';
            }else{
                $awtype='& wtype='.$wtype;
            }

            if ($lv != "member") {

                $v_sql="select sum(VGOLD) as Valid from web_report_data where  ".$m_result.$wtype.$Active.$pay_type.$name.$cancel." M_Date>='$date_start' and M_Date<='$date_end'";
                $v_result = DB::select($v_sql);
                $v_row=get_object_vars($v_result[0]);

                if ($v_row['Valid']==0){
                    $all_vgold=1;
                }else{
                    $all_vgold=$v_row['Valid'];
                }

            }

            if ($lv == "member") {

                $sql="select ID,MID,LineType,Mtype,BetIP,Active,Cancel,BetTime,OpenType,OddsType,ShowType,D_Result,M_Result,VGOLD,T_Result,TurnRate,M_Name,A_Point,B_Point,C_Point,D_Point,BetType,Middle,BetScore,M_Date,M_Rate,Agents,MB_ball,TG_ball,Confirmed,Danger from web_report_data where ".$m_result.$wtype.$Active." M_Name='$m_name' and M_Date>='$date_start' and M_Date<='$date_end' $cancel order by orderby,BetTime desc";

                $result = DB::select($sql);

                $ncount=0;
                $score=0;
                $win=0;
                $vgolds=0;
                $twin = 0;
                $agents = "";

                $data = array();

                foreach ($result as $row) {

                    $row = get_object_vars($row);

                    $agents=$row['Agents'];
                    $ncount+=1;
                    $score+=(int)$row['BetScore'];
                    $twin+=(int)$row['T_Result'];
                    $win+=(int)$row['M_Result'];
                    $middle=$row['Middle'];

                    $Title = "";
                            
                    switch($row['Active']){
                        case 1:
                            $active='1';
                            $Title="足球";
                            break;
                        case 11:
                            $active='11';
                            $Title="足球";
                            break;
                        case 2:
                            $active='2';
                            $Title="篮球";
                            break;
                        case 22:
                            $active='22';
                            $Title="篮球";
                            break;
                    }

                    $row["Title"] = $Title;

                    $time=strtotime($row['BetTime']);
                    $times=date("m-d",$time).'<br>'.date("H:i:s",$time);

                    $vgolds+=(int)$row['VGOLD'];

                    if($row['Danger']==1 or $row['Cancel']==1) {
                        $row['BetTime']='<font color="#FFFFFF"><span style="background-color: #FF0000">'.$times.'</span></font>';
                    }else{
                        $row['BetTime']=$times;
                    }
                    if ($row["Cancel"] != 0) {
                        switch($row['Confirmed']){
                            case 0:
                            $row["M_Result"]=Score20;
                            break;
                            case -1:
                            $row["M_Result"]=Score21;
                            break;
                            case -2:
                            $row["M_Result"]=Score22;
                            break;
                            case -3:
                            $row["M_Result"]=Score23;
                            break;
                            case -4:
                            $row["M_Result"]=Score24;
                            break;
                            case -5:
                            $row["M_Result"]=Score25;
                            break;
                            case -6:
                            $row["M_Result"]=Score26;
                            break;
                            case -7:
                            $row["M_Result"]=Score27;
                            break;
                            case -8:
                            $row["M_Result"]=Score28;
                            break;
                            case -9:
                            $row["M_Result"]=Score29;
                            break;
                            case -10:
                            $row["M_Result"]=Score30;
                            break;
                            case -11:
                            $row["M_Result"]=Score31;
                            break;
                            case -12:
                            $row["M_Result"]=Score32;
                            break;
                            case -13:
                            $row["M_Result"]=Score33;
                            break;
                            case -14:
                            $row["M_Result"]=Score34;
                            break;
                            case -15:
                            $row["M_Result"]=Score35;
                            break;
                            case -16:
                            $row["M_Result"]=Score36;
                            break;
                            case -17:
                            $row["M_Result"]=Score37;
                            break;
                            case -18:
                            $row["M_Result"]=Score38;
                            break;
                            case -19:
                            $row["M_Result"]=Score39;
                            break;
                            case -20:
                            $row["M_Result"]=Score40;
                            break;
                            case -21:
                            $row["M_Result"]=Score41;
                            break;
                        }

                    }
                    $row["Level"] = $logined_user["Level"];
                    array_push($data, $row);
                }


                $total_data = array(
                    "ncount" => $ncount,
                    "score" => $score,
                    "win" => $win,
                    "vgolds" => $vgolds,
                    "twin" => $twin,
                    "agents" => $agents,
                    "Level" => $logined_user["Level"],
                );

                $result = $data;

            } else {

                $sql="select CurType,A_Point,B_Point,C_Point,D_Point,$user as name,sum(vgold) as vgold,count(*) as coun,sum(BetScore) as BetScore,sum(M_Result) as M_Result,sum(A_Result) as A_Result,sum(B_Result) as B_Result,sum(C_Result) as C_Result,sum(D_Result) as D_Result,sum(T_Result) as T_Result,sum(VGOLD) as VGOLD from web_report_data where  ".$m_result.$wtype.$Active.$pay_type.$name.$cancel." M_Date>='$date_start' and M_Date<='$date_end'";

                $mysql=$sql." and Pay_Type=1 group by $user order by ID asc";
                $result = DB::select($mysql);

                $c_betscore = 0;
                $c_num = 0;
                $c_m_result = 0;
                $c_t_result = 0;      
                $c_a_result = 0;
                $c_b_result = 0;
                $c_c_result = 0;
                $c_d_result = 0;
                $c_vscore = 0;
                $vgold = 0;
                $c_vgold = 0;
                $gold = 0;
                $c_gold = 0;
                $sgold = 0;
                $c_sgold = 0;

                foreach ($result as $row) {
                    $row = get_object_vars($row);
                    $c_betscore+=$row['BetScore'];
                    $c_num+=$row['coun'];
                    $c_m_result+=$row['M_Result'];
                    $c_t_result+=$row['T_Result'];      
                    $c_a_result+=$row['A_Result'];
                    $c_b_result+=$row['B_Result'];
                    $c_c_result+=$row['C_Result'];
                    $c_d_result+=$row['D_Result'];
                    $c_vscore+=$row['VGOLD'];
                    $vgold=$row['VGOLD'];
                    $c_vgold+=$vgold;
                    $gold=$row['VGOLD'];
                    $c_gold+=$gold;
                    $sgold=$row['VGOLD'];
                    $c_sgold+=$sgold;
                }


                $total_data = array(
                    "c_betscore" => $c_betscore,
                    "c_num" => $c_num,
                    "c_m_result" => $c_m_result,
                    "c_t_result" => $c_t_result,
                    "c_a_result" => $c_a_result,
                    "c_b_result" => $c_b_result,
                    "c_c_result" => $c_c_result,
                    "c_d_result" => $c_d_result,
                    "c_vscore" => $c_vscore,
                    "vgold" => $vgold,
                    "c_vgold" => $c_vgold,
                    "gold" => $gold,
                    "c_gold" => $c_gold,
                    "sgold" => $sgold,
                    "c_sgold" => $c_sgold,
                    "all_vgold" => $all_vgold,
                );

            }

            $response["data"] = $result;
            $response["total_data"] = $total_data;
            $response['message'] = 'Sport Report Top Data fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function startSportRebate(Request $request) {

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

            $_REQUEST = $request->all();
            $logined_user = $request->user();
            $username=$logined_user['UserName'];
            $loginname = $logined_user["UserName"];

            $StartDate=$_REQUEST["start_date"] ?? date("Y-m-d");
            $OverDate=$_REQUEST["end_date"] ?? date("Y-m-d");
            $BadName = $_REQUEST["bad_name"] ?? "";
            if($StartDate==$OverDate){
                $Date_Memo=$StartDate;
            }else{
                $Date_Memo=$StartDate."至".$OverDate;
            }
            $BadNames = array();
            if ($BadName != "") {
                $BadName=str_replace('，',',',$BadName);
                $BadNames=explode(",",$BadName);                
            }

            // return $BadNames;

            if (count($BadNames) == 0) {

                $result = WebReportData::where("isFs", 0)->where("TurnRate", 0)->where("Cancel", 0)->where("Checked", 1)
                    ->whereBetween("M_Date", [$StartDate, $OverDate])->select(DB::raw("distinct(M_Name)"))->get();

            } else {

                $result = WebReportData::where("isFs", 0)->where("TurnRate", 0)->where("Cancel", 0)->where("Checked", 1)
                    ->whereBetween("M_Date", [$StartDate, $OverDate])->whereNotIn("M_Name", $BadNames)->select(DB::raw("distinct(M_Name)"))->get();

            }

            // return $result;

            foreach($result as $row) {
                $UserName = $row["M_Name"];
                $result1 = WebReportData::where("M_Name", $row["M_Name"])->where("isFs", 0)->where("TurnRate", 0)->where("Cancel", 0)->where("Checked", 1)
                    ->select(DB::raw("sum(VGOLD) as VGOLD"))->first();
                $VGOLD = $result1->VGOLD;
                $user = User::where("UserName", $row["M_Name"])->first();
                $fanshui=$user['fanshui'];
                $agents=$user['Agents'];
                $world=$user['World'];
                $corprator=$user['Corprator'];
                $super=$user['Super'];
                $admin=$user['Admin'];
                $Money=$user['Money'];
                $money_ts=round($VGOLD*$fanshui/100,2);

                WebReportData::where("M_Name", $row["M_Name"])->where("isFs", 0)->where("TurnRate", 0)->where("Cancel", 0)->where("Checked", 1)
                    ->whereBetween("M_Date", [$StartDate, $OverDate])->update([
                        "isFs" => 1,
                    ]);

                if ($money_ts > 0) {

                    $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                    $adddate=date("Y-m-d");
                    $date=date("Y-m-d H:i:s");
                    $previousAmount=$Money;
                    $currentAmount=$previousAmount+$money_ts;

                    $sql = "insert into web_sys800_data set Checked=1,Payway='W',Gold='$money_ts',previousAmount='$previousAmount',currentAmount='$currentAmount',AddDate='$adddate',Type='S',UserName='$UserName',Agents='$agents',World='$world',Corprator='$corprator',Super='$super',Admin='$admin',CurType='RMB',Date='$date',Name='$username',User='$username',Bank_Account='体育返水',Order_Code='$Order_Code',Music=1;";

                    DB::select($sql);

                    $q1 = User::where("UserName", $row["M_Name"])->increment('Money', (int)$money_ts);

                    if($q1==1){
                        $user_id=Utils::GetField($UserName,'ID');
                        $balance=Utils::GetField($UserName,'Money');
                        $datetime=date("Y-m-d H:i:s",time()+12*3600);
                        $money_log_sql="insert into money_log set user_id='$user_id',order_num='$Order_Code',about='".$loginname."体育返水<br>有效金额:$VGOLD<br>返水金额:$money_ts',update_time='$datetime',type='".$Date_Memo."体育返水',order_value='$money_ts',assets=$previousAmount,balance=$balance";
                        DB::select($money_log_sql);
                    }

                }

            }

            $loginfo='执行体育一键退水';
            $ip_addr = Utils::get_ip();
            $browser_ip = Utils::get_browser_ip();
            $mysql="insert into web_mem_log_data(UserName,Loginip,LoginTime,ConText,Url) values('$loginname','$ip_addr',now(),'$loginfo','".$browser_ip."')";
            DB::select($mysql);

            $response['message'] = 'Sport Rebate finished successfully';
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