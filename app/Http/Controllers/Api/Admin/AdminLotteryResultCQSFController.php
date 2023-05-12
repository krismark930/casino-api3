<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\LotteryUserConfig;
use App\Models\User;
use App\Models\LotteryResultCQSF;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\Web\MoneyLog;
use Carbon\Carbon;
use App\Utils\Utils;

class AdminLotteryResultCQSFController extends Controller
{
    public function getLotteryResult(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [
                "g_type" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $current_date = Carbon::now('Asia/Hong_Kong')->format('Y-m-d');

            $g_type = $request_data["g_type"];
            $qishu_query = $request_data["qishu_query"] ?? "";
            $query_time = $request_data["query_time"] ?? $current_date;
            $lottery_type = "重庆十分彩";
            $result = LotteryResultCQSF::whereDate("datetime", "=", $query_time);
            if ($qishu_query != "") {
                $result = $result->where("qishu", $qishu_query);
            }
            $result = $result->orderBy("qishu", "desc")->get();

            foreach($result as $item) {
                $item["lottery_type"] = $lottery_type;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result CQSF Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getLotteryResultById(Request $request) {

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
            $result = LotteryResultCQSF::find($id);

            $response["data"] = $result;
            $response['message'] = "Lottery Result CQSF Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

    public function saveLotteryResult(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [
                "action" => "required|string",
                "id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $action = $request_data["action"];
            $id = $request_data["id"];
            $qishu = $request_data["qishu"];
            $datetime = $request_data["datetime"];
            $ball_1 = $request_data["ball_1"];
            $ball_2 = $request_data["ball_2"];
            $ball_3 = $request_data["ball_3"];
            $ball_4 = $request_data["ball_4"];
            $ball_5 = $request_data["ball_5"];
            $ball_6 = $request_data["ball_6"];
            $ball_7 = $request_data["ball_7"];
            $ball_8 = $request_data["ball_8"];


            if ($action == "add" && $id==0) {

                $create_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                $result = LotteryResultCQSF::where("qishu", $qishu)->first();

                if (isset($result)) {
                    $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                    return response()->json($response, $response['status']);
                }

                $item = new LotteryResultCQSF;

                $item["qishu"] = $qishu;
                $item["create_time"] = $create_time;
                $item["datetime"] = $datetime;
                $item["ball_1"] = $ball_1;
                $item["ball_2"] = $ball_2;
                $item["ball_3"] = $ball_3;
                $item["ball_4"] = $ball_4;
                $item["ball_5"] = $ball_5;
                $item["ball_6"] = $ball_6;
                $item["ball_7"] = $ball_7;
                $item["ball_8"] = $ball_8;

                $item->save();

            } else if ($action == "edit" && $id > 0) {

                $item = LotteryResultCQSF::find($id);

                $time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                $prev_text = "修改时间：".($time)."。\n修改前内容：".$item["ball_1"].",".$item["ball_2"].",".$item["ball_3"].",".$item["ball_4"].",".$item["ball_5"].",".$item["ball_6"].",".$item["ball_7"].",".$item["ball_8"]."。\n修改后内容：".$ball_1.",".$ball_2.",".$ball_3.",".$ball_4.",".$ball_5.",".$ball_6.",".$ball_7.",".$ball_8."。\n\n".$item["prev_text"];

                $item["qishu"] = $qishu;
                $item["prev_text"] = $prev_text;
                $item["datetime"] = $datetime;
                $item["ball_1"] = $ball_1;
                $item["ball_2"] = $ball_2;
                $item["ball_3"] = $ball_3;
                $item["ball_4"] = $ball_4;
                $item["ball_5"] = $ball_5;
                $item["ball_6"] = $ball_6;
                $item["ball_7"] = $ball_7;
                $item["ball_8"] = $ball_8;

                $item->save();
            }            

            $response['message'] = "CQSF Lottery Result updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function checkoutResult(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [
                "qishu" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $g_type = $request_data["g_type"] ?? "CQSF";
            $qishu = $request_data["qishu"];
            $js_type = $request_data["jsType"];

            $result = LotteryResultCQSF::where("qishu", $qishu)->first();
            $lottery_name = "重庆十分彩";

            $hm    = array();
            $hm[]  = $result['ball_1'];
            $hm[]  = $result['ball_2'];
            $hm[]  = $result['ball_3'];
            $hm[]  = $result['ball_4'];
            $hm[]  = $result['ball_5'];
            $hm[]  = $result['ball_6'];
            $hm[]  = $result['ball_7'];
            $hm[]  = $result['ball_8'];
            $stateType = "1";

            //状态为已结算，对所有的订单进行结算，需要从客户那边收回钱然后再进行结算

            if ($js_type == 1) {
                //获取已结算的订单
                $orders = Utils::getOrdersJs($g_type,$qishu);
                //订单不为空，进行退钱操作
                if(count($orders) > 0) {
                    foreach($orders as $order){
                        $order = get_object_vars($order);
                        $userid = $order['user_id'];
                        $datereg = $order['order_sub_num'];
                        $resultMoney = User::find($userid);
                        $assets = round($resultMoney['Money'],2);
                        OrderLottery::where("id", $order["id"])->update(["status" => 0]);
                        OrderLotterySub::where("id", $order["sub_id"])
                                ->update(["status" => 0, "is_win" => null]);
                        if($order['is_win']=="1" || $order['is_win']=="2" || ($order['is_win']=="0" && $order['fs']>0)){
                            //退钱
                            if($order['is_win']=="1"){//中奖金额+反水
                                $bet_money_total = $order['win']+$order['fs'];
                            }elseif($order['is_win']=="2"){//平局的钱，返回的是下注的钱
                                $bet_money_total = $order['bet_money'];
                            }elseif($order['is_win']=="0" && $order['fs']>0){//反水的钱
                                $bet_money_total = $order['fs'];
                            }

                            $q1 = User::where("id", $userid)
                                ->where("Pay_Type", 1)
                                ->decrement('Money', $bet_money_total);

                            //会员金额操作成功

                            if($q1 == 1) {

                                $balance=   $assets - $bet_money_total;

                                $datetime = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                                $new_log = new MoneyLog;
                                $new_log->user_id = $userid;
                                $new_log->order_num = $datereg;
                                $new_log->about = $lottery_name;
                                $new_log->update_time = $datetime;
                                $new_log->type = "彩票重新结算-扣钱";
                                $new_log->order_value = $bet_money_total;
                                $new_log->assets = $assets;
                                $new_log->balance = $balance;
                                $new_log->save();

                            }
                        }
                    }
                }
                $stateType = "2";
            }  

            //获取未结算的订单
            $orders = Utils::getOrdersByStatus($g_type,$qishu,"0");

            if(count($orders) == 0) {
                LotteryResultCQSF::where("qishu", $qishu)
                    ->update(["state" => $stateType]);

                $response['message'] = "CQSF Lottery Result checkouted successfully!";
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;

                return response()->json($response, $response['status']);
            }

            $hms[]      = Utils::G10_Auto($hm,1);
            $hms[]      = Utils::G10_Auto($hm,2);
            $hms[]      = Utils::G10_Auto($hm,3);
            $hms[]      = Utils::G10_Auto($hm,4);
            $hms[]      = Utils::G10_Auto($hm,5);

            $ds_1       = Utils::G10_Ds($result['ball_1']);
            $ds_2       = Utils::G10_Ds($result['ball_2']);
            $ds_3       = Utils::G10_Ds($result['ball_3']);
            $ds_4       = Utils::G10_Ds($result['ball_4']);
            $ds_5       = Utils::G10_Ds($result['ball_5']);
            $ds_6       = Utils::G10_Ds($result['ball_6']);
            $ds_7       = Utils::G10_Ds($result['ball_7']);
            $ds_8       = Utils::G10_Ds($result['ball_8']);

            $dx_1       = Utils::G10_Dx($result['ball_1']);
            $dx_2       = Utils::G10_Dx($result['ball_2']);
            $dx_3       = Utils::G10_Dx($result['ball_3']);
            $dx_4       = Utils::G10_Dx($result['ball_4']);
            $dx_5       = Utils::G10_Dx($result['ball_5']);
            $dx_6       = Utils::G10_Dx($result['ball_6']);
            $dx_7       = Utils::G10_Dx($result['ball_7']);
            $dx_8       = Utils::G10_Dx($result['ball_8']);

            $wsdx_1 = Utils::G10_WsDx($result['ball_1']);
            $wsdx_2 = Utils::G10_WsDx($result['ball_2']);
            $wsdx_3 = Utils::G10_WsDx($result['ball_3']);
            $wsdx_4 = Utils::G10_WsDx($result['ball_4']);
            $wsdx_5 = Utils::G10_WsDx($result['ball_5']);
            $wsdx_6 = Utils::G10_WsDx($result['ball_6']);
            $wsdx_7 = Utils::G10_WsDx($result['ball_7']);
            $wsdx_8 = Utils::G10_WsDx($result['ball_8']);

            $hsds_1 = Utils::G10_HsDs($result['ball_1']);
            $hsds_2 = Utils::G10_HsDs($result['ball_2']);
            $hsds_3 = Utils::G10_HsDs($result['ball_3']);
            $hsds_4 = Utils::G10_HsDs($result['ball_4']);
            $hsds_5 = Utils::G10_HsDs($result['ball_5']);
            $hsds_6 = Utils::G10_HsDs($result['ball_6']);
            $hsds_7 = Utils::G10_HsDs($result['ball_7']);
            $hsds_8 = Utils::G10_HsDs($result['ball_8']);

            $fw_1       = Utils::G10_Fw($result['ball_1']);
            $fw_2       = Utils::G10_Fw($result['ball_2']);
            $fw_3       = Utils::G10_Fw($result['ball_3']);
            $fw_4       = Utils::G10_Fw($result['ball_4']);
            $fw_5       = Utils::G10_Fw($result['ball_5']);
            $fw_6       = Utils::G10_Fw($result['ball_6']);
            $fw_7       = Utils::G10_Fw($result['ball_7']);
            $fw_8       = Utils::G10_Fw($result['ball_8']);

            $zfb_1  = Utils::G10_Zfb($result['ball_1']);
            $zfb_2  = Utils::G10_Zfb($result['ball_2']);
            $zfb_3  = Utils::G10_Zfb($result['ball_3']);
            $zfb_4  = Utils::G10_Zfb($result['ball_4']);
            $zfb_5  = Utils::G10_Zfb($result['ball_5']);
            $zfb_6  = Utils::G10_Zfb($result['ball_6']);
            $zfb_7  = Utils::G10_Zfb($result['ball_7']);
            $zfb_8  = Utils::G10_Zfb($result['ball_8']);

            foreach($orders as $order){
                $order = get_object_vars($order);
                $betInfo = explode(":",$order["number"]);
                $rTypeName = $order["rtype_str"];
                $quick_type = $order["quick_type"];
                if($betInfo[1]=="LOCATE"){//每球定位
                    $selectBall = $betInfo[2];
                    $betContent = $betInfo[0];
                    if($betInfo[2]=="S"){
                        $selectBall = "8";
                    }
                }elseif($betInfo[1]=="MATCH"){//一中一
                    $selectBall = "一中一";
                    $betContent = $betInfo[0];
                }elseif($betInfo[0]!="ALL" && in_array($betInfo[1].":".$betInfo[2],array("LAST:OVER","LAST:UNDER","SUM:ODD","SUM:EVEN"))){//每球 尾数，总和
                    $selectBall = $betInfo[0];
                    $betContent = $betInfo[1].':'.$betInfo[2];
                    if($betInfo[0]=="S"){
                        $selectBall = "8";
                    }
                }elseif($betInfo[0]=="ALL" || in_array($betInfo[1].":".$betInfo[2],array("S:DRAGON","S:TIGER"))){//所有球总和
                    $selectBall = "总和";
                    $betContent = $betInfo[1].':'.$betInfo[2];
                    if($order["number"]=="ALL:SUM:LAST:OVER" || $order["number"]=="ALL:SUM:LAST:UNDER"){
                        $betContent = $betInfo[1].':'.$betInfo[2].':'.$betInfo[3];
                    }
                }elseif($rTypeName=="快速-重庆快乐十分"){
                    $selectBall = "quick";
                }else{//每球 其他，如中发白、四季五行、龙虎、大小、单双
                    $selectBall = $betInfo[0];
                    $betContent = $betInfo[1];
                    if($betInfo[0]=="S"){
                        $selectBall = "8";
                    }
                }

                $userid = $order['user_id'];
                $datereg = $order['order_sub_num'];
                $resultMoney    = User::find($userid);
                $assets =   round($resultMoney['money'],2);

                if(in_array($selectBall,array("1","2","3","4","5","6","7","8"))){
                    //各种玩法，算法
                    $ds     = Utils::convertToEn(Utils::G10_Ds($result['ball_'.$selectBall]));
                    $dx     = Utils::convertToEn(Utils::G10_Dx($result['ball_'.$selectBall]));
                    $wsdx   = Utils::convertToEn(Utils::G10_WsDx($result['ball_'.$selectBall]));
                    $hsds   = Utils::convertToEn(Utils::G10_HsDs($result['ball_'.$selectBall]));
                    $fw     = Utils::convertToEn(Utils::G10_Fw($result['ball_'.$selectBall]));
                    $zfb    = Utils::convertToEn(Utils::G10_Zfb($result['ball_'.$selectBall]));
                    $season = Utils::convertToEn(Utils::G10_season($result['ball_'.$selectBall]));
                    $wuxing = Utils::convertToEn(Utils::G10_wuxing($result['ball_'.$selectBall]));

                    if(in_array($betContent, array($result['ball_'.$selectBall],$ds, $dx, $wsdx, $hsds, $fw, $zfb, $season, $wuxing))){
                        $win_sign = "1";
                        $bet_money_total = $order['win']+$order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    }else{
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                }elseif($selectBall=="总和"){
                    $zhdx   = Utils::convertToEn(Utils::G10_Auto($hm,2));   //总和大小
                    $zhds   = Utils::convertToEn(Utils::G10_Auto($hm,3));   //总和单双
                    $zhwsdx = Utils::convertToEn(Utils::G10_Auto($hm,4));   //总和尾大小
                    $lh     = Utils::convertToEn(Utils::G10_Auto($hm,5));   //总和龙虎

                    if(in_array($betContent, array($zhdx,$zhds,$zhwsdx,$lh))){
                        $win_sign = "1";
                        $bet_money_total = $order['win']+$order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    }elseif(in_array($betContent, array("SUM:OVER","SUM:UNDER")) && $zhdx=='SUM:TIE'){
                        $win_sign = "2";
                        $bet_money_total = $order['bet_money'];
                        $bet_type = "彩票手工结算-彩票和局";
                    }else{
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                }elseif($selectBall=="一中一"){
                    if(in_array($betContent, array($result['ball_1'],$result['ball_2'],$result['ball_3'],$result['ball_4'],$result['ball_5'],
                        $result['ball_6'],$result['ball_7'],$result['ball_8']))){
                        $win_sign = "1";
                        $bet_money_total = $order['win']+$order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    }else{
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                }elseif($selectBall=="quick"){
                    $betInfo = $order["number"];
                    $is_win = "false";
                    if($quick_type=="第一球"){
                        if($betInfo==$result['ball_1'] || $betInfo==$ds_1 || $betInfo==$dx_1 || $betInfo==$wsdx_1|| $betInfo==$hsds_1|| $betInfo==$fw_1|| $betInfo==$zfb_1){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第二球"){
                        if($betInfo==$result['ball_2'] || $betInfo==$ds_2 || $betInfo==$dx_2 || $betInfo==$wsdx_2|| $betInfo==$hsds_2|| $betInfo==$fw_2|| $betInfo==$zfb_2){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第三球"){
                        if($betInfo==$result['ball_3'] || $betInfo==$ds_3 || $betInfo==$dx_3 || $betInfo==$wsdx_3|| $betInfo==$hsds_3|| $betInfo==$fw_3|| $betInfo==$zfb_3){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第四球"){
                        if($betInfo==$result['ball_4'] || $betInfo==$ds_4 || $betInfo==$dx_4 || $betInfo==$wsdx_4|| $betInfo==$hsds_4|| $betInfo==$fw_4|| $betInfo==$zfb_4){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第五球"){
                        if($betInfo==$result['ball_5'] || $betInfo==$ds_5 || $betInfo==$dx_5 || $betInfo==$wsdx_5|| $betInfo==$hsds_5|| $betInfo==$fw_5|| $betInfo==$zfb_5){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第六球"){
                        if($betInfo==$result['ball_6'] || $betInfo==$ds_6 || $betInfo==$dx_6|| $betInfo==$wsdx_6|| $betInfo==$hsds_6|| $betInfo==$fw_6|| $betInfo==$zfb_6){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第七球"){
                        if($betInfo==$result['ball_7'] || $betInfo==$ds_7 || $betInfo==$dx_7|| $betInfo==$wsdx_7|| $betInfo==$hsds_7|| $betInfo==$fw_7|| $betInfo==$zfb_7){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第八球"){
                        if($betInfo==$result['ball_8'] || $betInfo==$ds_8 || $betInfo==$dx_8|| $betInfo==$wsdx_8|| $betInfo==$hsds_8|| $betInfo==$fw_8|| $betInfo==$zfb_8){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="总和龙虎"){
                        if($betInfo==$hms[1] || $betInfo==$hms[2] || $betInfo==$hms[3] || $betInfo==$hms[4]){
                            $is_win = "true";
                        }
                        if($hms[1]=="和" && ($betInfo=="总和小" || $betInfo=="总和大")){
                            $is_win = "tie";
                        }
                        if($hms[4]=="和" && ($betInfo=="龙" || $betInfo=="虎")){
                            $is_win = "tie";
                        }
                    }
                    if($is_win == "true"){
                        $win_sign = "1";
                        $bet_money_total = $order['win']+$order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    }elseif($is_win == "tie"){
                        $win_sign = "2";
                        $bet_money_total = $order['bet_money'];
                        $bet_type = "彩票手工结算-彩票和局";
                    }else{
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                }

                //修改主单

                OrderLottery::where("id", $order["id"])->update(["status" => $stateType]);

                OrderLotterySub::where("id", $order["sub_id"])
                            ->update(["status" => $stateType, "is_win" => $win_sign]);

                if($win_sign == "1" ||$win_sign == "2" || ($win_sign == "0" && $order['fs']>0)){

                    $q1 = User::where("id", $userid)
                        ->where("Pay_Type", 1)
                        ->increment('Money', $win_money);

                    //会员金额操作成功

                    if($q1 == 1) {

                        $balance=   $assets + $bet_money_total;

                        $datetime = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                        $new_log = new MoneyLog;
                        $new_log->user_id = $userid;
                        $new_log->order_num = $datereg;
                        $new_log->about = $lottery_name;
                        $new_log->update_time = $datetime;
                        $new_log->type = $bet_type;
                        $new_log->order_value = $bet_money_total;
                        $new_log->assets = $assets;
                        $new_log->balance = $balance;
                        $new_log->save();

                    }
                }
            }

            LotteryResultCQSF::where("qishu", $qishu)
                ->update(["state" => $stateType]);

            $response['message'] = "CQSF Lottery Result checkouted successfully!";
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
