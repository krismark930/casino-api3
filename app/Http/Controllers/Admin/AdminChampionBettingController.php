<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Web\Report;
use App\Models\Web\System;
use App\Utils\Utils;
use App\Models\Sport;
use App\Models\MatchCrown;
use App\Models\Config;
use App\Models\User;
use App\Models\Web\MoneyLog;
use App\Models\Web\WebMemLogData;
use Auth;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminChampionBettingController extends Controller
{
    //
    public function getItems(Request $request)
    {
        // Web Report Data
        $user = Auth::guard("admin")->user();
        $page = $request['page'];
        $active = $request['match'];
        // $ptype = $request['wtype'];
        $m_name = $request['username'];
        $checkout = $request['checkout'];
        $type = $request['type'];
        $result_type = $request['result_type'];
        // $mids = Report::select('MID')->where('M_Date', $m_date)->get();
        
        try {
            $mids = Report::where('LineType', 16);
            if($active) {
                $mids = $mids->where('Active', $active);
            }
            if($m_name) {
            $mids = $mids->where('M_Name', 'like', '%'.trim($m_name).'%');
            }
            if($user['Level']) {
                switch($user['Level']) {
                    case 'A':
                        $mids = $mids->where('Super', $user['UserName']);
                        break;
                    case 'B':
                        $mids = $mids->where('Corprator', $user['UserName']);
                        break;
                    case 'C':
                        $mids = $mids->where('World', $user['UserName']);
                        break;
                    case 'D':
                        $mids = $mids->where('Agents', $user['UserName']);
                        break;
                    default:
                        break;
                }
            }
            if($result_type != "all") {
                switch($result_type) {
                    // case "all":
                    //     break;
                    case "Y":
                        $mids = $mids->where('M_Result', '!=', '');
                        break;
                    case "N":
                        $mids = $mids->where('M_Result', '');
                        break;
                    case "W":
                        $mids = $mids->where('M_Result', '>', '0');
                        break;
                    case "W>=500":
                        $mids = $mids->where('M_Result', '>', '0')->where('BetScore', '>=', '500');
                        break;
                    case ">=100":
                        $mids = $mids->where('BetScore', '>=', '100');
                        break;
                    case ">=500":
                        $mids = $mids->where('BetScore', '>=', '500');
                        break;
                    case ">=1000":
                        $mids = $mids->where('BetScore', '>=', '1000');
                        break;
                    case ">=5000":
                        $mids = $mids->where('BetScore', '>=', '5000');
                        break;
                    case ">=10000":
                        $mids = $mids->where('BetScore', '>=', '10000');
                        break;
                }
            }

            if($checkout == '0') {
                $mids = $mids->where('M_Result', '');
            }

            // $totalCount = $mids->count();
            // $mids = $mids->offset($page * 20 - 20)->limit(20)->orderBy('ID', "desc")->get();

            $mids = $mids->orderBy('ID', "desc")->get();
            
            $data = array();
            
            foreach($mids as $row){
                
                // Web Match Crown Data
                $item = MatchCrown::where('MID', $row['MID'])->first();
                // $retime = $item['Retime'];
                // if($retime == 0)    $retime = "";
                // if($retime == -1)   $retime = "中场";
                $retime = '';
                if($item) {
                    $time = $row['BetTime'];
                    $m_start = $item['M_Start'];
                    if($m_start != "") {
                        $aa = floor((strtotime($time) - strtotime($m_start)) / 60);
                        if($aa > 240 || $aa < 0)    $aa = "";
                    }
                } else {
                    continue;
                }

                $time = strtotime($row['BetTime']);
                $times = date("Y-m-d", $time).'<br>'.date("H:i:s", $time);

                if($row['Danger']==1 or $row['Cancel']==1) {
                    $bettimes='<font color="#FFFFFF"><span style="background-color: #FF0000">'.$times.'</span></font>';
                }else{
                    $bettimes=$times;
                }
                if($row['Cancel']==1){
                    $betscore='<S><font color=#cc0000>'.number_format($row['BetScore']).'</font></S>';
                }else{
                    $betscore=number_format($row['BetScore']);
                }

                if($row['Cancel'] == 1) {
                    $operate = '<font color=red><b>已注销</b></font></a>';
                } else {
                    $operate = '<font color=blue><b>正常</b></font>';
                }

                if ($row["Checked"] == 0) {

                    $state="<font color=red>未结算</font>";

                } else {

                    $state = '<font>已结算</font>';
                }

                if ($row["Cancel"] == 1) {

                    switch($row['Confirmed']) {
                        case 0:
                            $M_Result = Score20;
                            break;
                        case -1:
                            $M_Result= Score21;
                            break;
                        case -2:
                            $M_Result= Score22;
                            break;
                        case -3:
                            $M_Result= Score23;
                            break;
                        case -4:
                            $M_Result= Score24;
                            break;
                        case -5:
                            $M_Result= Score25;
                            break;
                        case -6:
                            $M_Result= Score26;
                            break;
                        case -7:
                            $M_Result= Score27;
                            break;
                        case -8:
                            $M_Result= Score28;
                            break;
                        case -9:
                            $M_Result= Score29;
                            break;
                        case -10:
                            $M_Result= Score30;
                            break;
                        case -11:
                            $M_Result= Score31;
                            break;
                        case -12:
                            $M_Result= Score32;
                            break;
                        case -13:
                            $M_Result= Score33;
                            break;
                        case -14:
                            $M_Result= Score34;
                            break;
                        case -15:
                            $M_Result= Score35;
                            break;
                        case -16:
                            $M_Result= Score36;
                            break;
                        case -17:
                            $M_Result= Score37;
                            break;
                        case -18:
                            $M_Result= Score38;
                            break;
                        case -19:
                            $M_Result= Score39;
                            break;
                        case -20:
                            $M_Result= Score40;
                            break;
                        case -21:
                            $M_Result= Score41;
                            break;
                    }

                    $M_Result = "<font color=red>".$M_Result."</font>";

                } else {

                    $M_Result = "<font>".$row["M_Result"]."</font>";
                }

                $temp = array(
                    'id' => $row['ID'],
                    'gid' => $row['MID'],
                    'OpenType' => $row['OpenType'],                    
                    'OrderID' => $row['OrderID'],
                    'TurnRate' => $row['TurnRate'],
                    'userName' => $row['M_Name'],
                    'minutes' => '<div><font color="red"><b>'.$retime.'<br/><font color="blue">'.$aa.'</font></b></font>'.'</div>',
                    'bettingTime' => $bettimes,
                    'startingTime' => $m_start,
                    'gameType' => $row['BetType'],
                    'content' => $row['Middle'],
                    'state' => $state,
                    'betAmount' => $betscore,
                    'winableAmount' => $row['Gwin'],
                    'memberResult' => $M_Result,
                    'betSlip' => $operate,
                    'function' => 'function',
                );
                array_push($data, $temp);
            }
            return array(
                'data' => $data,
                // 'totalCount' => $totalCount,
            );
        } catch(Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getFunctionItems() {
        $scors = Utils::Scores;
        $arr = array('判为全赢', '判为全输', '判为和局');
        $scors = array_splice($scors, 20, 23);
        $scors = array_merge($arr, $scors);
        $data = array();

        foreach($scors as $row) {
            $temp = array(
                'label' => $row,
                'value' => $row,
            );
            array_push($data, $temp);
        }
        return $data;
    }
    
    public function getUrl() {
        //-----------------------------
        $a=array(
            "'",
            '"',
            ";",
            "and",
            "update",
            "where",
            "set",
            "user",
            "pass",
            "insert"
        );
        $b=array("","","","","","","","","","");
        $host=str_replace($a,$b,strtolower(request()->server('HTTP_HOST')));
        $host=substr($host,0,30);
        if(request()->server("HTTPS")=='on' or request()->server('SERVER_PORT')==443){
            $https='https://';
        }else{
            if(request()->server('HTTP_X_FORWARDED_PROTO')<>''){
                $https=request()->server('HTTP_X_FORWARDED_PROTO').'://';
            }else{
                $https='http://';
            }
        }
        //--------------------
        return $https.$host;
    }

    public function handleResumeEvent(Request $request) {
        $config = Config::query()->limit(1)->get();
        $config = $config[0];
        $adminList = explode(',', $config['MemberModList']);
        $user = Auth::guard("admin")->user();
        $loginname = $user['LoginName'];
        $id = $request['id'];
        $gid = $request['gid'];
      
      try {
        $res = Report::where('Cancel', '1')->where('MID', $gid)->where('ID', $id)->get();
        if(count($res)) {
            $res = $res[0];
            $username=$res['M_Name'];
            $betscore=$res['BetScore'];
            $m_result=$res['M_Result'];
            $orderId=$res['OrderID'];

            $selectedUser = User::where('UserName', $username)->get()[0];
            $assets = $selectedUser['Money'];

            $affectRows = User::where('UserName', $username)->where('Pay_Type', 1)->decrement('Money', $betscore);

            if($affectRows == 1) {
                $balance = $selectedUser['Money'];
                $user_id = $selectedUser['id'];
                $datetime = date("Y-m-d H:i:s");
                $insertedMoney = MoneyLog::create([
                    'user_id' => $user_id,
                    'order_num' => $orderId,
                    'about' => $loginname."恢复注单(后台操作)<br>MID:$gid<br>RID:$id",
                    'update_time' => $datetime,
                    'type' => $res['Middle'],
                    'order_value' => $betscore,
                    'assets' => $assets,
                    'balance' => $balance,
                ]);

                $affectRows = Report::where('id', $id)->update([
                    'VGOLD' => '',
                    'M_Result' => '0',
                    'A_Result' => '',
                    'B_Result' => '',
                    'C_Result' => '',
                    'D_Result' => '',
                    'T_Result' => '',
                    'Cancel' => '0',
                    'Confirmed' => '0',
                    'Danger' => '0',
                    'Checked' => '0',
                ]);

                if($affectRows != 1) {
                    MoneyLog::where('id', $insertedMoney['id'])->where('user_id', $user_id)->delete();
                    User::where('UserName', $username)->where('Pay_Type', 1)->increment('Money', $betscore);
                }
            }
            $ip_addr = request()->ip();
            $loginfo = '恢复注单';
            WebMemLogData::create([
                'UserName' => $loginname,
                'Logintime' => now(),
                'ConText' => $loginfo,
                'Loginip' => $ip_addr,
                'Url' => $this->getUrl(),
            ]);
        }
        return;
      } catch(Exception $e) {
        return '操作失败!';
      }
    }

    public function handleDeleteEvent(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'id' => 'required|numeric'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];

            Report::where("ID", $id)->delete();

            $response['message'] = 'Selected Report deleted successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function handleCancelEvent(Request $request) {
        $config = Config::query()->limit(1)->get();
        $adminList = explode(',', $config[0]['MemberModList']);
        $user = Auth::guard("admin")->user();
        $loginname = $user['LoginName'];
      $id = $request['id'];
      $gid = $request['gid'];
      $confirmed = $request['confirmed'];
      
      try {
        $res = Report::where('ID', $id)->where('MID', $gid)->get();
        if(count($res)) {
            $res = $res[0];
            $username=$res['M_Name'];
            $betscore=$res['BetScore'];
            $m_result=$res['M_Result'];
            $orderId=$res['OrderID'];
            $balance = $m_result == '' ? $betscore : -$m_result;

            
            $affectRows = Report::where('id', $id)->update([
                'VGOLD' => '0',
                'M_Result' => '0',
                'A_Result' => '0',
                'B_Result' => '0',
                'C_Result' => '0',
                'D_Result' => '0',
                'T_Result' => '0',
                'Cancel' => 1,
                'Confirmed' => $confirmed,
                'Danger' => '0',
                'Checked' => '1',
            ]);

            $selectedUser = User::where('UserName', $username)->get()[0];
            $assets = $selectedUser['Money'];

            $affectRows = User::where('UserName', $username)->where('Pay_Type', 1)->limit(1)->increment('Money', (int)$balance);

            if($affectRows == 1) {
                $balance2 = $selectedUser['Money'];
                $user_id = $selectedUser['id'];
                $datetime = date("Y-m-d H:i:s");
                $insertedMoney = MoneyLog::create([
                    'user_id' => $user_id,
                    'order_num' => $orderId,
                    'about' => $loginname."取消注单(后台操作)<br>MID:$gid<br>RID:$id",
                    'update_time' => $datetime,
                    'type' => $res['Middle'],
                    'order_value' => $balance,
                    'assets' => $assets,
                    'balance' => $balance2,
                ]);
            }
            //  else {
            //     Report::where('id', $id)->update([
            //         'VGOLD' => '',
            //         'M_Result' => '',
            //         'A_Result' => '',
            //         'B_Result' => '',
            //         'C_Result' => '',
            //         'D_Result' => '',
            //         'T_Result' => '',
            //         'Cancel' => '0',
            //         'Confirmed' => '0',
            //         'Danger' => '0',
            //         'Checked' => '0',
            //     ]);
            // }
            $ip_addr = request()->ip();
            $loginfo = '取消注单';
            WebMemLogData::create([
                'UserName' => $loginname,
                'Logintime' => now(),
                'ConText' => $loginfo,
                'Loginip' => $ip_addr,
                'Url' => $this->getUrl(),
            ]);
        }
        return;
      } catch(Exception $e) {
        return response()->json('操作失败!', 500);
      }
    }

    public function handleBalanceEvent(Request $request) {
        $config = Config::query()->limit(1)->get()[0];
        $adminList = explode(',', $config['MemberModList']);
        $user = Auth::guard("admin")->user();
        $loginname = $user['LoginName'];
        $id = $request['id'];
        $gid = $request['gid'];
        $confirmed = $request['confirmed'];
      
      try {
        $res = Report::where('ID', $id)->where('MID', $gid)->where('Pay_Type', 1)->get();
        if(count($res) <= 0) {
            return response()->json('Report not found!', 400);
        }
        $res = $res[0];
        if($res['LineType'] == 8) {
            return response()->json('Temporarily does not support cross-border settlement!', 400);
        }
        // if($res['M_Result'] != '' || $res['Checked'] == 1 || $res['Cancel'] == 1) {
        //     return response()->json('The bet status is incorrect!', 400);
        // }
        $arr_m=array('MH','MC','MN','VMH','VMC','VMN','RMH','RMC','RMN','VRMH','VRMC','VRMN');
        $arr_line=array(1,11,21,31,16,4,5,6,7);
        $OrderID=$res['OrderID'];
        $username=$res['M_Name'];
        $BetScore=$res['BetScore'];
        $M_Rate=$res['M_Rate'];
        $Mtype=$res['Mtype'];
        $LineType=$res['LineType'];
        $TurnRate=$res['TurnRate']*$BetScore/100;
        if(in_array($LineType,$arr_line)) $M_Rate--;
        $Gwin=$res['Gwin'];
        if($confirmed==11){  //全赢
            $balance=$BetScore+$BetScore*$M_Rate+$TurnRate;
            $VGold=$BetScore;
            $memo=$loginname.'判为全赢';
        }else if($confirmed==12){  //全输
            $balance=$TurnRate;
            $VGold=$BetScore;
            $memo=$loginname.'判为全输';
        }elseif($confirmed==13){  //赢一半
            $balance=$BetScore+$BetScore*$M_Rate/2+$TurnRate;
            $VGold=$BetScore/2;
            $memo=$loginname.'判为赢一半';
        }else if($confirmed==14){  //输一半
            $balance=$BetScore/2+$TurnRate;
            $VGold=$BetScore/2;
            $memo=$loginname.'判为输一半';
        }else if($confirmed==15){  //和局
            $balance=$BetScore * 2;
            $VGold=0;
            $memo=$loginname.'判为和局';
        }else{
            return response()->json('invalid parameter!', 400);
        }
        $M_Result=$balance-$BetScore;
        Report::where('id', $id)->update([
            'VGOLD' => $VGold,
            'M_Result' => $M_Result,
            'A_Result' => '0',
            'B_Result' => '0',
            'C_Result' => '0',
            'D_Result' => '0',
            'T_Result' => '0',
            'Cancel' => '0',
            'Danger' => '0',
            'Checked' => '1',
        ]);
        $selectedUser = User::where('UserName', $username)->get()[0];
        $assets = $selectedUser['Money'];

        try {
            $affectRows = User::where('UserName', $username)->where('Pay_Type', 1)->limit(1)->increment('Money', $balance);
        } catch (Exception $e) {
            return response()->json('User Money Setting failed!', 400);
        }
        if($affectRows == 1 || floatval($balance) == 0) {
            $balance2 = $selectedUser['Money'];
            $user_id = $selectedUser['id'];
            $datetime = date("Y-m-d H:i:s");
            try {
                $insertedMoney = MoneyLog::create([
                    'user_id' => $user_id,
                    'order_num' => $OrderID,
                    'about' => $memo."(通过查询注单操作)<br>MID:$gid<br>RID:$id",
                    'update_time' => $datetime,
                    'type' => $res['Middle'],
                    'order_value' => $balance,
                    'assets' => $assets,
                    'balance' => $balance2,
                ]);
            } catch(Exception $e) {
                return response()->json('Failed to add accounting records (settlement betting slip)！<br>RID:'.$id, 400);
            }
        } else {
            Report::where('id', $id)->update([
                'VGOLD' => '',
                'M_Result' => '',
                'A_Result' => '',
                'B_Result' => '',
                'C_Result' => '',
                'D_Result' => '',
                'T_Result' => '',
                'Cancel' => '0',
                'Confirmed' => '0',
                'Danger' => '0',
                'Checked' => '0',
            ]);
        }

        $ip_addr = request()->ip();

        $loginfo = '通过查询注单结算注单';

        WebMemLogData::create([
            'UserName' => $loginname,
            'Logintime' => now(),
            'ConText' => $loginfo,
            'Loginip' => $ip_addr,
            'Url' => $this->getUrl(),
        ]);

        return response()->json('恭喜您,注单已经结算!', 200);

      } catch (Exception $e) {
        return 'operation failed!';
      }
    }
}