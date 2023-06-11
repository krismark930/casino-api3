<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\Web\MoneyLog;
use App\Utils\Utils;
use Carbon\Carbon;

class AdminAlwaysColorController extends Controller
{
    public function getOrderList(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "type" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $type = $request_data["type"] ?? "";
            $s_time = $request_data["s_time"] ?? "";
            $e_time = $request_data["e_time"] ?? "";
            $qishu = $request_data["qishu"] ?? "";
            $zf = $request_data["zf"] ?? "";
            $id = $request_data["id"] ?? "";
            $js = $request_data["js"] ?? "";
            $tf_id = $request_data["tf_id"] ?? "";
            $uid = $request_data["uid"] ?? "";
            $user_name = $request_data["user_name"] ?? "";
            $cancel_reson = $request_data["cancel_reason"] ?? "";
            $page_no = $request_data["page"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $isnulldate = false;
            $js_array = explode(",", $js);
            $t_allmoney=0;
            $t_sy=0;
            $total = array();

            if ($s_time == "" && $qishu == "") {
                $_stime = date('Y-m-d',strtotime('-6 day'));
                $isnulldate = true;
            }

            $type == '' ? $se1 = '#FF0' : $se1 = '#FFF';
            $type == '六合彩' ? $se2 = '#FF0' : $se2 = '#FFF';
            $type == '广东快乐十分' ? $se3 = '#FF0' : $se3 = '#FFF';
            $type == '重庆时时彩' ? $se4 = '#FF0' : $se4 = '#FFF';
            $type == '北京PK10' ? $se5 = '#FF0' : $se5 = '#FFF';
            $type == '幸运飞艇' ? $se5 = '#FF0' : $se5 = '#FFF';
            $type == '澳洲幸运10' ? $se5 = '#FF0' : $se5 = '#FFF';
            $type == '重庆幸运农场' ? $se6 = '#FF0' : $se6 = '#FFF';
            $type == '北京快乐8' ? $se7 = '#FF0' : $se7 = '#FFF';

            if ($zf == 1) {
                if ($id > 0) {

                    $result =DB::table('order_lottery as o')
                        ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
                        ->join('web_member_data as u', 'u.id', '=', 'o.user_id')
                        ->where("o_sub.id", $id)
                        ->select('o.user_id', 'o.order_num', 'o.Gtype','o_sub.order_sub_num','o_sub.bet_money','o_sub.fs','o_sub.win','o_sub.status','o_sub.is_win','u.Money')
                        ->first();

                    $user_id = $result->user_id;
                    $bet_money = $result->bet_money;
                    $money=$result->Money;
                    $datereg = $result->order_sub_num;
                    $lottery_name = Utils::getZhPageTitle($result->Gtype);
                    $bet_money_total = $result->bet_money;
                    $status=$result->status;
                    $win=$result->win;
                    $is_win=$result->is_win;
                    $fanshui=$result->fs;

                    if($status=='0'){
                        $new_money=$money+$bet_money;
                    }elseif($status=='1' or $status=='2'){
                        if($is_win=='0'){  //输
                            $new_money=$money+$bet_money-$fanshui;
                        }elseif($is_win=='1'){  //赢
                            $new_money=$money-$win-$fanshui;
                        }elseif($is_win=='2'){ //和
                            $new_money=$money-$fanshui;
                        }elseif($is_win=='3'){ //赢一半
                            $new_money=$money-($win/2)-$fanshui;
                        }
                    }

                    if($status !== '3' && isset($new_money)) {

                        //返还金额
                        $user = User::find($user_id);
                        $user->Money = $new_money;
                        $user->save();

                        DB::table('order_lottery as o')
                        ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
                        ->join('web_member_data as u', 'u.id', '=', 'o.user_id')
                        ->where("o_sub.id", $id)                        
                        ->update(['o_sub.status' => 3]);

                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $datereg;
                        $new_log->about = $lottery_name;
                        $new_log->update_time = now();
                        $new_log->type = "作废订单加钱,理由:".$cancel_reason;
                        $new_log->order_value = $bet_money_total;
                        $new_log->assets = $money;
                        $new_log->balance = $new_money;
                        $new_log->save();

                    }
                }
            }

            // $customer = Customer::leftJoin('orders', function($join) {
            //       $join->on('customers.id', '=', 'orders.customer_id');
            //     })
            //     ->whereNull('orders.customer_id')
            //     ->first([
            //         'customers.id',
            //         'customers.first_name',
            //         'customers.last_name',
            //         'customers.email',
            //         'customers.phone',
            //         'customers.address1',
            //         'customers.address2',
            //         'customers.city',
            //         'customers.state',
            //         'customers.county',
            //         'customers.district',
            //         'customers.postal_code',
            //         'customers.country'
            //     ]);


            if ($user_name !== "") {
                $user = User::where("UserName", $user_name)->first();
                $uid = $user["id"];
            }

            $result1 = DB::table("order_lottery as o")
                ->join("order_lottery_sub as o_sub", "o.order_num", "=", "o_sub.order_num")
                // ->join('web_member_data as u', 'u.id', '=', 'o.user_id')
                ->where("o_sub.bet_money", ">", 0);

            if ($type != "ALL_LOTTERY" && $type !== "") {
                $result1 = $result1->where("o.Gtype", $type);
            }

            if ($uid !== "") {
                $result1 = $result1->where("o.user_id", $uid);
            }

            if ($s_time !== "" && $e_time !== "") {
                $result1 = $result1->where("o.bet_time", ">=", $s_time." 00:00:00");
            }

            if($s_time !== "" && $e_time == "" && !$isnulldate) {
                $result1 = $result1->where(DB::raw("left(o.bet_time,10)"), $s_time);
            }

            if($e_time !== "") {
                $result1 = $result1->where("o.bet_time", "<=", $e_time." 23:59:59");
            }

            if ($qishu !== "") {
                $result1 = $result1->where("o.lottery_number", $qishu);
            }

            if ($js !== "") {
                $result1 = $result1->whereIn("o_sub.status", $js_array);
            }

            if ($tf_id !== "") {
                $result1 = $result1->where("o_sub.order_sub_num", $tf_id);
            }

            $result1 = $result1->select("o.username","o.Gtype","o.lottery_number AS qishu","o.rtype_str","o.bet_time","o.order_num","o_sub.quick_type","o_sub.number","o_sub.bet_money AS bet_money_one","o_sub.fs","o.user_id","o_sub.bet_rate AS bet_rate_one","o_sub.is_win","o_sub.status","o_sub.id AS id","o_sub.win AS win_sub","o_sub.balance","o_sub.order_sub_num");

            $count = $result1->count();

            $result1 = $result1->orderBy("o_sub.id", "desc")
                ->offset(($page_no - 1) * $limit)
                ->take($limit)
                ->get();

            foreach($result1 as $item) {

                $user = User::find($item->user_id);

                $color = "#FFFFFF";
                $over  = "#EBEBEB";
                $out   = "#ffffff";
                $t_allmoney += $item->bet_money_one;

                $money_result = 0;
                if($item->is_win=="1"){
                    $t_sy= $t_sy + $item->win_sub + $item->fs;
                    $money_result = $item->win_sub + $item->fs;
                }elseif($item->is_win=="2"){
                    $t_sy+=$item->bet_money_one;
                    $money_result = $item->bet_money_one;
                }elseif($item->is_win=="0"){
                    $t_sy+=$item->fs-$item->bet_money_one;
                    $money_result = $item->fs-$item->bet_money_one;
                }

                if($item->is_win==1 || $item->is_win=="2"){
                    $color = "#FFE1E1";
                    $over  = "#FFE1E1";
                    $out   = "#FFE1E1";
                }

                $contentName = Utils::getName($item->number,$item->Gtype,$item->rtype_str,$item->quick_type);

                $item->content_name = $contentName;

                $item->money_result = round($money_result, 2);

                $item->lottery_name = Utils::getZhPageTitle($item->Gtype);

                $item->checked = false;

                $bet_rate = $item->bet_rate_one;

                if(strpos($bet_rate, ",")){
                    $bet_rate_array = explode(",", $bet_rate);
                    $item->bet_rate_one = $bet_rate_array[0];
                }
            }

            $total = array(
                "count" => $count,
                "t_allmoney" => $t_allmoney,
                "t_sy" => round($t_sy, 2)
            );

            $response["data"] = $result1;
            $response["total_item"] = $total;
            $response['message'] = "Order List Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getOrderCancelAll(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "ids" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ids = $request_data["ids"];
            $cancel_reason = $request_data["cancel_reason"];
            $id_array = explode(",", $ids);

            foreach ($id_array as $id) {

                $result = OrderLottery::leftJoin('order_lottery_sub', function($join) {
                      $join->on('order_lottery.order_num', '=', 'order_lottery_sub.order_num');
                    })
                    ->leftJoin('web_member_data', function($join) {
                      $join->on('web_member_data.id', '=', 'order_lottery.user_id');
                    })
                    ->where("order_lottery_sub.id", $id)
                    ->first([
                        'order_lottery.user_id',
                        'order_lottery.order_num',
                        'order_lottery.Gtype',
                        'order_lottery_sub.order_sub_num',
                        'order_lottery_sub.bet_money',
                        'order_lottery_sub.fs',
                        'order_lottery_sub.win',
                        'order_lottery_sub.status',
                        'order_lottery_sub.is_win',
                        'web_member_data.Money'
                    ]);

                $user_id = $result->user_id;
                $bet_money = $result->bet_money;
                $money=$result->Money;
                $datereg = $result->order_sub_num;
                $lottery_name = Utils::getZhPageTitle($result->Gtype);
                $bet_money_total = $result->bet_money;
                $status=$result->status;
                $win=$result->win;
                $is_win=$result->is_win;
                $fanshui=$result->fs;

                if($status=='0'){
                    $new_money=$money+$bet_money;
                }elseif($status=='1' or $status=='2'){
                    if($is_win=='0'){  //输
                        $new_money=$money+$bet_money-$fanshui;
                    }elseif($is_win=='1'){  //赢
                        $new_money=$money-$win-$fanshui;
                    }elseif($is_win=='2'){ //和
                        $new_money=$money-$fanshui;
                    }elseif($is_win=='3'){ //赢一半
                        $new_money=$money-($win/2)-$fanshui;
                    }
                }

                if($status !== '3' && isset($new_money)) {

                    //返还金额
                    $user = User::find($user_id);
                    $user->Money = $new_money;
                    $user->save();

                    DB::table('order_lottery as o')
                    ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
                    ->join('web_member_data as u', 'u.id', '=', 'o.user_id')
                    ->where("o_sub.id", $id)                        
                    ->update(['o_sub.status' => 3]);

                    $new_log = new MoneyLog;
                    $new_log->user_id = $user_id;
                    $new_log->order_num = $datereg;
                    $new_log->about = $lottery_name;
                    $new_log->update_time = now();
                    $new_log->type = "作废订单加钱,理由:".$cancel_reason;
                    $new_log->order_value = $bet_money_total;
                    $new_log->assets = $money;
                    $new_log->balance = $new_money;
                    $new_log->save();

                }
            }


            $response['message'] = "Order List canceled successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }       

    public function getLotteryHistory(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "s_time" => "required|string",
                "e_time" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $s_time = $request_data["s_time"];
            $e_time = $request_data["e_time"];
            $user_group = $request_data["user_group"] ?? "";
            $user_ignore_group = $request_data["user_ignore_group"] ?? "";
            $t_allmoney = 0;
            $t_sy = 0;
            $uid = '';
            $inUserString = "";

            if ($user_group != "" || $user_ignore_group != "") {
                $userArray = array();
                $userIgnoreArray = array();
                $sql_sub = "";

                if (strpos($user_group,",")) {
                    $userArray = explode(",",trim($user_group));
                    foreach($userArray as $item) {
                        $item = trim($item);
                    }
                } else if(strpos($user_group,"，")){
                    $userArray = explode("，",trim($user_group));
                    foreach($userArray as $item) {
                        $item = trim($item);
                    }
                } else if($user_group !== "") {
                    $userArray = explode(",",trim($user_group));
                }

                if (strpos($user_ignore_group,",")) {
                    $userIgnoreArray = explode(",",trim($user_ignore_group));
                    foreach($userIgnoreArray as $item) {
                        $item = trim($item);
                    }
                } else if (strpos($user_ignore_group,"，")) {
                    $userIgnoreArray = explode("，",trim($user_ignore_group));
                    foreach($userIgnoreArray as $item) {
                        $item = trim($item);
                    }
                } else if ($user_ignore_group !== "") {
                    $userIgnoreArray = explode("",trim($user_ignore_group));
                }

                $user = User::query();

                if (count($userArray) > 0) {
                    $user = $user->whereIn("UserName", $userArray);
                }

                if (count($userIgnoreArray) > 0) {
                    $user = $user->whereNotIn("UserName", $userIgnoreArray);
                }

                $user = $user->get(["ID"]);

                if (count($user) > 0) {
                    foreach($user as $item) {
                        $inUserString .= $item["ID"].",";
                    }
                }
            }

            $d3_result = Utils::getBetMoneyAndCount($s_time,$e_time,"D3",$inUserString);
            $p3_result = Utils::getBetMoneyAndCount($s_time,$e_time,"P3",$inUserString);
            $t3_result = Utils::getBetMoneyAndCount($s_time,$e_time,"T3",$inUserString);
            $cq_result = Utils::getBetMoneyAndCount($s_time,$e_time,"CQ",$inUserString);
            $tj_result = Utils::getBetMoneyAndCount($s_time,$e_time,"TJ",$inUserString);
            $jx_result = Utils::getBetMoneyAndCount($s_time,$e_time,"JX",$inUserString);
            $gxsf_result = Utils::getBetMoneyAndCount($s_time,$e_time,"GXSF",$inUserString);
            $gdsf_result = Utils::getBetMoneyAndCount($s_time,$e_time,"GDSF",$inUserString);
            $tjsf_result = Utils::getBetMoneyAndCount($s_time,$e_time,"TJSF",$inUserString);
            $cqsf_result = Utils::getBetMoneyAndCount($s_time,$e_time,"CQSF",$inUserString);
            $gd11_result = Utils::getBetMoneyAndCount($s_time,$e_time,"GD11",$inUserString);
            $bjpk_result = Utils::getBetMoneyAndCount($s_time,$e_time,"BJPK",$inUserString);
            $xyft_result = Utils::getBetMoneyAndCount($s_time,$e_time,"XYFT",$inUserString);
            $ffc5_result = Utils::getBetMoneyAndCount($s_time,$e_time,"FFC5",$inUserString);
            $bjkn_result = Utils::getBetMoneyAndCount($s_time,$e_time,"BJKN",$inUserString);
            $txssc_result = Utils::getBetMoneyAndCount($s_time,$e_time,"txssc",$inUserString);
            $twssc_result = Utils::getBetMoneyAndCount($s_time,$e_time,"twssc",$inUserString);
            $azxy5_result = Utils::getBetMoneyAndCount($s_time,$e_time,"azxy5",$inUserString);
            $azxy10_result = Utils::getBetMoneyAndCount($s_time,$e_time,"azxy10",$inUserString);

            $d3_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"D3",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $p3_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"P3",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $t3_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"T3",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $cq_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"CQ",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $tj_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"TJ",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $jx_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"JX",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $gxsf_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"GXSF",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $gdsf_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"GDSF",$inUserString,"o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $tjsf_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"TJSF",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $cqsf_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"CQSF",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $gd11_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"GD11",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $bjpk_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"BJPK",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $xyft_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"XYFT",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $ffc5_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"FFC5",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $bjkn_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"BJKN",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $txssc_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"TXSSC",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $twssc_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"TWSSC",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $azxy5_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"AZXY5",$inUserString," o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");
            $azxy10_result_valid = Utils::getBetMoneyAndCount($s_time,$e_time,"AZXY10",$inUserString,"o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 ");

            $d3_win = Utils::getTotalWin2($s_time,$e_time,"D3",$inUserString);
            $p3_win = Utils::getTotalWin2($s_time,$e_time,"P3",$inUserString);
            $t3_win = Utils::getTotalWin2($s_time,$e_time,"T3",$inUserString);
            $cq_win = Utils::getTotalWin2($s_time,$e_time,"CQ",$inUserString);
            $tj_win = Utils::getTotalWin2($s_time,$e_time,"TJ",$inUserString);
            $jx_win = Utils::getTotalWin2($s_time,$e_time,"JX",$inUserString);
            $gxsf_win = Utils::getTotalWin2($s_time,$e_time,"GXSF",$inUserString);
            $gdsf_win = Utils::getTotalWin2($s_time,$e_time,"GDSF",$inUserString);
            $tjsf_win = Utils::getTotalWin2($s_time,$e_time,"TJSF",$inUserString);
            $cqsf_win = Utils::getTotalWin2($s_time,$e_time,"CQSF",$inUserString);
            $gd11_win = Utils::getTotalWin2($s_time,$e_time,"GD11",$inUserString);
            $bjpk_win = Utils::getTotalWin2($s_time,$e_time,"BJPK",$inUserString);
            $xyft_win = Utils::getTotalWin2($s_time,$e_time,"XYFT",$inUserString);
            $ffc5_win = Utils::getTotalWin2($s_time,$e_time,"FFC5",$inUserString);
            $bjkn_win = Utils::getTotalWin2($s_time,$e_time,"BJKN",$inUserString);
            $txssc_win = Utils::getTotalWin2($s_time,$e_time,"TXSSC",$inUserString);
            $twssc_win = Utils::getTotalWin2($s_time,$e_time,"TWSSC",$inUserString);
            $azxy5_win = Utils::getTotalWin2($s_time,$e_time,"AZXY5",$inUserString);
            $azxy10_win = Utils::getTotalWin2($s_time,$e_time,"AZXY10",$inUserString);

            $d3_array = array(
                "g_type" => "3D彩",
                "gtype" => "D3",
                "bet_count" => $d3_result->bet_count,
                "bet_money" => round($d3_result->bet_money, 2),
                "valid_bet_money" => round($d3_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($d3_win - $d3_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($d3_result_valid->bet_money - $d3_win, 2),
            );

            $p3_array = array(
                "g_type" => "排列三",
                "gtype" => "P3",
                "bet_count" => $p3_result->bet_count,
                "bet_money" => round($p3_result->bet_money, 2),
                "valid_bet_money" => round($p3_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($p3_win - $p3_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($p3_result_valid->bet_money - $p3_win, 2),
            );

            $t3_array = array(
                "g_type" => "上海时时乐",
                "gtype" => "T3",
                "bet_count" => $t3_result->bet_count,
                "bet_money" => round($t3_result->bet_money, 2),
                "valid_bet_money" => round($t3_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($t3_win - $t3_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($t3_result_valid->bet_money - $t3_win, 2),
            );

            $cq_array = array(
                "g_type" => "重庆时时彩",
                "gtype" => "CQ",
                "bet_count" => $cq_result->bet_count,
                "bet_money" => round($cq_result->bet_money, 2),
                "valid_bet_money" => round($cq_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($cq_win - $cq_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($cq_result_valid->bet_money - $cq_win, 2),
            );

            $jx_array = array(
                "g_type" => "新疆时时彩",
                "gtype" => "JX",
                "bet_count" => $jx_result->bet_count,
                "bet_money" => round($jx_result->bet_money, 2),
                "valid_bet_money" => round($jx_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($jx_win - $jx_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($jx_result_valid->bet_money - $jx_win, 2),
            );

            $tj_array = array(
                "g_type" => "天津时时彩",
                "gtype" => "TJ",
                "bet_count" => $tj_result->bet_count,
                "bet_money" => round($tj_result->bet_money, 2),
                "valid_bet_money" => round($tj_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($tj_win - $tj_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($tj_result_valid->bet_money - $tj_win, 2),
            );

            $gxsf_array = array(
                "g_type" => "广西十分彩",
                "gtype" => "GXSF",
                "bet_count" => $gxsf_result->bet_count,
                "bet_money" => round($gxsf_result->bet_money, 2),
                "valid_bet_money" => round($gxsf_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($gxsf_win - $gxsf_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($gxsf_result_valid->bet_money - $gxsf_win, 2),
            );

            $gdsf_array = array(
                "g_type" => "广东十分彩",
                "gtype" => "GDSF",
                "bet_count" => $gdsf_result->bet_count,
                "bet_money" => round($gdsf_result->bet_money, 2),
                "valid_bet_money" => round($gdsf_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($gdsf_win - $gdsf_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($gdsf_result_valid->bet_money - $gdsf_win, 2),
            );

            $tjsf_array = array(
                "g_type" => "天津十分彩",
                "gtype" => "TJSF",
                "bet_count" => $tjsf_result->bet_count,
                "bet_money" => round($tjsf_result->bet_money, 2),
                "valid_bet_money" => round($tjsf_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($tjsf_win - $tjsf_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($tjsf_result_valid->bet_money - $tjsf_win, 2),
            );

            $cqsf_array = array(
                "g_type" => "重庆十分彩",
                "gtype" => "CQSF",
                "bet_count" => $cqsf_result->bet_count,
                "bet_money" => round($cqsf_result->bet_money, 2),
                "valid_bet_money" => round($cqsf_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($cqsf_win - $cqsf_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($cqsf_result_valid->bet_money - $cqsf_win, 2),
            );

            $bjkn_array = array(
                "g_type" => "北京快乐8",
                "gtype" => "BJKN",
                "bet_count" => $bjkn_result->bet_count,
                "bet_money" => round($bjkn_result->bet_money, 2),
                "valid_bet_money" => round($bjkn_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($bjkn_win - $bjkn_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($bjkn_result_valid->bet_money - $bjkn_win, 2),
            );

            $gd11_array = array(
                "g_type" => "广东十一选五",
                "gtype" => "GD11",
                "bet_count" => $gd11_result->bet_count,
                "bet_money" => round($gd11_result->bet_money, 2),
                "valid_bet_money" => round($gd11_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($gd11_win - $gd11_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($gd11_result_valid->bet_money - $gd11_win, 2),
            );

            $bjpk_array = array(
                "g_type" => "北京PK拾",
                "gtype" => "BJPK",
                "bet_count" => $bjpk_result->bet_count,
                "bet_money" => round($bjpk_result->bet_money, 2),
                "valid_bet_money" => round($bjpk_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($bjpk_win - $bjpk_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($bjpk_result_valid->bet_money - $bjpk_win, 2),
            );

            $xyft_array = array(
                "g_type" => "幸运飞艇",
                "gtype" => "XYFT",
                "bet_count" => $xyft_result->bet_count,
                "bet_money" => round($xyft_result->bet_money, 2),
                "valid_bet_money" => round($xyft_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($xyft_win - $xyft_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($xyft_result_valid->bet_money - $xyft_win, 2),
            );

            $ffc5_array = array(
                "g_type" => "五分彩",
                "gtype" => "FFC5",
                "bet_count" => $ffc5_result->bet_count,
                "bet_money" => round($ffc5_result->bet_money, 2),
                "valid_bet_money" => round($ffc5_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($ffc5_win - $ffc5_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($ffc5_result_valid->bet_money - $ffc5_win, 2),
            );

            $txssc_array = array(
                "g_type" => "腾讯时时彩",
                "gtype" => "TXSSC",
                "bet_count" => $txssc_result->bet_count,
                "bet_money" => round($txssc_result->bet_money, 2),
                "valid_bet_money" => round($txssc_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($txssc_win - $txssc_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($txssc_result_valid->bet_money - $txssc_win, 2),
            );

            $twssc_array = array(
                "g_type" => "台湾时时彩",
                "gtype" => "TWSSC",
                "bet_count" => $twssc_result->bet_count,
                "bet_money" => round($twssc_result->bet_money, 2),
                "valid_bet_money" => round($twssc_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($twssc_win - $twssc_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($twssc_result_valid->bet_money - $twssc_win, 2),
            );

            $azxy5_array = array(
                "g_type" => "澳洲幸运5",
                "gtype" => "AZXY5",
                "bet_count" => $azxy5_result->bet_count,
                "bet_money" => round($azxy5_result->bet_money, 2),
                "valid_bet_money" => round($azxy5_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($azxy5_win - $azxy5_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($azxy5_result_valid->bet_money - $azxy5_win, 2),
            );

            $azxy10_array = array(
                "g_type" => "澳洲幸运10",
                "gtype" => "AZXY10",
                "bet_count" => $azxy10_result->bet_count,
                "bet_money" => round($azxy10_result->bet_money, 2),
                "valid_bet_money" => round($azxy10_result_valid->bet_money, 2),
                "bet_money_diff_1" => round($azxy10_win - $azxy10_result_valid->bet_money, 2),
                "bet_money_diff_2" => round($azxy10_result_valid->bet_money - $azxy10_win, 2),
            );

            $data = array();
            array_push($data, $d3_array);
            array_push($data, $p3_array);
            array_push($data, $t3_array);
            array_push($data, $cq_array);
            array_push($data, $jx_array);
            array_push($data, $tj_array);
            array_push($data, $gxsf_array);
            array_push($data, $gdsf_array);
            array_push($data, $tjsf_array);
            array_push($data, $cqsf_array);
            array_push($data, $bjkn_array);
            array_push($data, $gd11_array);
            array_push($data, $bjpk_array);
            array_push($data, $xyft_array);
            array_push($data, $ffc5_array);
            array_push($data, $txssc_array);
            array_push($data, $twssc_array);
            array_push($data, $azxy5_array);
            array_push($data, $azxy10_array);

            $response["data"] = $data;
            $response['message'] = "Total Order History Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getUserLottery(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "gtype" => "required|string",
                "s_time" => "required|string",
                "e_time" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $type = $request_data["gtype"] ?? "";
            $s_time = $request_data["s_time"];
            $e_time = $request_data["e_time"];
            $user_group = $request_data["user_group"] ?? "";
            $user_ignore_group = $request_data["user_ignore_group"] ?? "";
            $page_no = $request_data["page"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $t_allmoney = 0;
            $t_sy = 0;
            $in_user_array = array();
            $total = array();

            if ($user_group != "" || $user_ignore_group != "") {
                $userArray = array();
                $userIgnoreArray = array();
                $sql_sub = "";

                if (strpos($user_group,",")) {
                    $userArray = explode(",",trim($user_group));
                    foreach($userArray as $item) {
                        $item = trim($item);
                    }
                } else if(strpos($user_group,"，")){
                    $userArray = explode("，",trim($user_group));
                    foreach($userArray as $item) {
                        $item = trim($item);
                    }
                } else if($user_group !== "") {
                    $userArray = explode(",",trim($user_group));
                }

                if (strpos($user_ignore_group,",")) {
                    $userIgnoreArray = explode(",",trim($user_ignore_group));
                    foreach($userIgnoreArray as $item) {
                        $item = trim($item);
                    }
                } else if (strpos($user_ignore_group,"，")) {
                    $userIgnoreArray = explode("，",trim($user_ignore_group));
                    foreach($userIgnoreArray as $item) {
                        $item = trim($item);
                    }
                } else if ($user_ignore_group !== "") {
                    $userIgnoreArray = explode("",trim($user_ignore_group));
                }

                $user = User::query();

                if (count($userArray) > 0) {
                    $user = $user->whereIn("UserName", $userArray);
                }

                if (count($userIgnoreArray) > 0) {
                    $user = $user->whereNotIn("UserName", $userIgnoreArray);
                }

                $user = $user->get(["ID"]);

                if (count($user) > 0) {
                    foreach($user as $item) {
                        array_push($in_user_array, $item["ID"]);
                    }
                }
            }

            $result1 = DB::table("order_lottery as o")
                ->join("order_lottery_sub as o_sub", "o.order_num", "=", "o_sub.order_num")
                ->join("web_member_data as u", "o.user_id", "=", "u.id");

            if ($type != "ALL_LOTTERY" && $type !== "") {
                $result1 = $result1->where("o.Gtype", $type);
            }

            if ($s_time !== "" && $e_time !== "") {
                $result1 = $result1->whereBetween("o.bet_time", [$s_time." 00:00:00", $e_time." 23:59:59"]);
            }

            if (count($in_user_array) > 0) {
                $result1 = $result1->whereIn("o.user_id", $in_user_array);
            }

            $result1 = $result1->select(DB::raw("o.Gtype,u.UserName,u.Alias,u.id,count(o_sub.id) bet_count,sum(o_sub.bet_money) bet_money_total,SUM(CASE WHEN o.status!=0 AND o.status!=3 AND o_sub.is_win!=2 THEN o_sub.bet_money ELSE 0 END) bet_money_total_valid,SUM(IF(o_sub.is_win=1,o_sub.win+o_sub.fs,IF(o_sub.is_win=0,o_sub.fs,0))) win_total"));

            $result1 = $result1->groupBy("o.user_id");

            $count = count($result1->groupBy("o.user_id")->get());

            $result1 = $result1->orderBy("o_sub.id", "desc")
                ->offset(($page_no - 1) * $limit)
                ->take($limit)
                ->get();

            foreach($result1 as $item) {
                $color = "#FFFFFF";
                $over  = "#EBEBEB";
                $out   = "#ffffff";
                $t_allmoney+=$item->bet_money_total;
                $t_sy += $item->win_total - $item->bet_money_total;
                $item->lottery_name = Utils::getZhPageTitle($item->Gtype);
            }

            $total = array(
                "count" => $count,
                "t_allmoney" => $t_allmoney,
                "t_sy" => round($t_sy, 2)
            );

            $response["data"] = $result1;
            $response["total_item"] = $total;
            $response['message'] = "User Lottery Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }   


    public function getDetailLottery(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "gtype" => "required|string",
                "s_time" => "required|string",
                "e_time" => "required|string",
                "user_group" => "required|string"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $type = $request_data["gtype"] ?? "";
            $s_time = $request_data["s_time"];
            $e_time = $request_data["e_time"];
            $user_group = $request_data["user_group"];
            $user_ignore_group = $request_data["user_ignore_group"] ?? "";
            $page_no = $request_data["page"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $t_allmoney = 0;
            $t_sy = 0;
            $in_user_array = array();
            $total = array();

            if ($user_group != "" || $user_ignore_group != "") {
                $userArray = array();
                $userIgnoreArray = array();
                $sql_sub = "";

                if (strpos($user_group,",")) {
                    $userArray = explode(",",trim($user_group));
                    foreach($userArray as $item) {
                        $item = trim($item);
                    }
                } else if(strpos($user_group,"，")){
                    $userArray = explode("，",trim($user_group));
                    foreach($userArray as $item) {
                        $item = trim($item);
                    }
                } else if($user_group !== "") {
                    $userArray = explode(",",trim($user_group));
                }

                if (strpos($user_ignore_group,",")) {
                    $userIgnoreArray = explode(",",trim($user_ignore_group));
                    foreach($userIgnoreArray as $item) {
                        $item = trim($item);
                    }
                } else if (strpos($user_ignore_group,"，")) {
                    $userIgnoreArray = explode("，",trim($user_ignore_group));
                    foreach($userIgnoreArray as $item) {
                        $item = trim($item);
                    }
                } else if ($user_ignore_group !== "") {
                    $userIgnoreArray = explode("",trim($user_ignore_group));
                }

                $user = User::query();

                if (count($userArray) > 0) {
                    $user = $user->whereIn("UserName", $userArray);
                }

                if (count($userIgnoreArray) > 0) {
                    $user = $user->whereNotIn("UserName", $userIgnoreArray);
                }

                $user = $user->get(["ID"]);

                if (count($user) > 0) {
                    foreach($user as $item) {
                        array_push($in_user_array, $item["ID"]);
                    }
                }
            }

            $result1 = DB::table("order_lottery as o")
                ->join("order_lottery_sub as o_sub", "o.order_num", "=", "o_sub.order_num");

            if ($type != "ALL_LOTTERY" && $type !== "") {
                $result1 = $result1->where("o.Gtype", $type);
            }

            if ($s_time !== "" && $e_time !== "") {
                $result1 = $result1->whereBetween("o.bet_time", [$s_time." 00:00:00", $e_time." 23:59:59"]);
            }

            if (count($in_user_array) > 0) {
                $result1 = $result1->whereIn("o.user_id", $in_user_array);
            }

            $result1 = $result1->select(DB::raw("o.username,o.Gtype,o.lottery_number AS qishu,o.rtype_str,o.bet_time,o.order_num,o_sub.quick_type,o_sub.number,o_sub.bet_money AS bet_money_one,o_sub.fs, o.user_id, o_sub.bet_rate AS bet_rate_one,o_sub.is_win,o_sub.status, o_sub.id AS id,o_sub.win AS win_sub,o_sub.balance,o_sub.order_sub_num"));

            $count = $result1->count();

            $result1 = $result1->orderBy("o_sub.id", "desc")
                ->offset(($page_no - 1) * $limit)
                ->take($limit)
                ->get();

            foreach($result1 as $item) {

                $user = User::find($item->user_id);

                $color = "#FFFFFF";
                $over  = "#EBEBEB";
                $out   = "#ffffff";
                $t_allmoney += $item->bet_money_one;

                $money_result = 0;
                if($item->is_win=="1"){
                    $t_sy= $t_sy + $item->win_sub + $item->fs - $item->bet_money_one;
                    $money_result = $item->win_sub + $item->fs-$item->bet_money_one;
                }elseif($item->is_win=="2"){
                    $t_sy+=$item->bet_money_one;
                    $money_result = $item->fs;
                }elseif($item->is_win=="0"){
                    $t_sy+=$item->fs-$item->bet_money_one;
                    $money_result = $item->fs-$item->bet_money_one;
                }

                if($item->is_win==1 || $item->is_win=="2"){
                    $color = "#FFE1E1";
                    $over  = "#FFE1E1";
                    $out   = "#FFE1E1";
                }

                $contentName = Utils::getName($item->number,$item->Gtype,$item->rtype_str,$item->quick_type);

                $item->content_name = $contentName;

                $item->money_result = round($money_result, 2);

                $item->lottery_name = Utils::getZhPageTitle($item->Gtype);

                $item->checked = false;

                $bet_rate = $item->bet_rate_one;

                if(strpos($bet_rate, ",")){
                    $bet_rate_array = explode(",", $bet_rate);
                    $item->bet_rate_one = $bet_rate_array[0];
                }
            }

            $total = array(
                "count" => $count,
                "t_allmoney" => $t_allmoney,
                "t_sy" => round($t_sy, 2)
            );

            $response["data"] = $result1;
            $response["total_item"] = $total;
            $response['message'] = "Detail Lottery Data fetched successfully!";
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
