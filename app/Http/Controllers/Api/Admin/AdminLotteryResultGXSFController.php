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
use App\Models\LotteryResultGXSF;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\Web\MoneyLog;
use Carbon\Carbon;
use App\Utils\Utils;

class AdminLotteryResultGXSFController extends Controller
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
            $lottery_type = "广西十分彩";
            $result = LotteryResultGXSF::whereDate("datetime", "=", $query_time);
            if ($qishu_query != "") {
                $result = $result->where("qishu", $qishu_query);
            }
            $result = $result->orderBy("qishu", "desc")->get();

            foreach($result as $item) {
                $item["lottery_type"] = $lottery_type;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result GXSF Data fetched successfully!";
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
            $result = LotteryResultGXSF::find($id);

            $response["data"] = $result;
            $response['message'] = "Lottery Result GXSF Data fetched successfully!";
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

            if ($action == "add" && $id==0) {

                $create_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                $result = LotteryResultGXSF::where("qishu", $qishu)->first();

                if (isset($result)) {
                    $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                    return response()->json($response, $response['status']);
                }

                $item = new LotteryResultGXSF;

                $item["qishu"] = $qishu;
                $item["create_time"] = $create_time;
                $item["datetime"] = $datetime;
                $item["ball_1"] = $ball_1;
                $item["ball_2"] = $ball_2;
                $item["ball_3"] = $ball_3;
                $item["ball_4"] = $ball_4;
                $item["ball_5"] = $ball_5;

                $item->save();

            } else if ($action == "edit" && $id > 0) {

                $item = LotteryResultGXSF::find($id);

                $time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                $prev_text = "修改时间：".($time)."。\n修改前内容：".$item["ball_1"].",".$item["ball_2"].",".$item["ball_3"].",".$item["ball_4"].",".$item["ball_5"]."。\n修改后内容：".$ball_1.",".$ball_2.",".$ball_3.",".$ball_4.",".$ball_5."。\n\n".$item["prev_text"];

                $item["qishu"] = $qishu;
                $item["prev_text"] = $prev_text;
                $item["datetime"] = $datetime;
                $item["ball_1"] = $ball_1;
                $item["ball_2"] = $ball_2;
                $item["ball_3"] = $ball_3;
                $item["ball_4"] = $ball_4;
                $item["ball_5"] = $ball_5;

                $item->save();
            }            

            $response['message'] = "GXSF Lottery Result updated successfully!";
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

            $g_type = $request_data["g_type"] ?? "GXSF";
            $qishu = $request_data["qishu"];
            $js_type = $request_data["jsType"];

            $result = LotteryResultGXSF::where("qishu", $qishu)->first();
            $lottery_name = "广西十分彩";

            $hm    = array();
            $hm[]  = $result['ball_1'];
            $hm[]  = $result['ball_2'];
            $hm[]  = $result['ball_3'];
            $hm[]  = $result['ball_4'];
            $hm[]  = $result['ball_5'];
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
                LotteryResultGXSF::where("qishu", $qishu)
                    ->update(["state" => $stateType]);

                $response['message'] = "GXSF Lottery Result checkouted successfully!";
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;

                return response()->json($response, $response['status']);
            }

            $hms[] = Utils::gxsf_Auto($hm,1);
            $hms[] = Utils::gxsf_Auto($hm,2);
            $hms[] = Utils::gxsf_Auto($hm,3);
            $hms[] = Utils::gxsf_Auto($hm,4);
            $hms[] = Utils::gxsf_Auto($hm,5);
            $hms[] = Utils::gxsf_Auto($hm,6);
            $hms[] = Utils::gxsf_Auto($hm,7);
            $ds_1 = Utils::gxsf_Ds($result['ball_1']);
            $dx_1 = Utils::gxsf_Dx($result['ball_1']);
            $ds_2 = Utils::gxsf_Ds($result['ball_2']);
            $dx_2 = Utils::gxsf_Dx($result['ball_2']);
            $ds_3 = Utils::gxsf_Ds($result['ball_3']);
            $dx_3 = Utils::gxsf_Dx($result['ball_3']);
            $ds_4 = Utils::gxsf_Ds($result['ball_4']);
            $dx_4 = Utils::gxsf_Dx($result['ball_4']);
            $ds_5 = Utils::gxsf_Ds($result['ball_5']);
            $dx_5 = Utils::gxsf_Dx($result['ball_5']);

            foreach($orders as $order){
                $order = get_object_vars($order);
                $betInfo = explode(":",$order["number"]);
                $rTypeName = $order["rtype_str"];
                $quick_type = $order["quick_type"];

                // if($betInfo[1]=="LOCATE"){//每球定位
                //     $selectBall = $betInfo[2];
                //     $betContent = $betInfo[0];
                //     if($betInfo[2]=="S"){
                //         $selectBall = "5";
                //     }
                // }elseif($betInfo[1]=="MATCH"){//一中一
                //     $selectBall = "一中一";
                //     $betContent = $betInfo[0];
                // }elseif($betInfo[0]!="ALL" && in_array($betInfo[1].":".$betInfo[2],array("LAST:OVER","LAST:UNDER","SUM:ODD","SUM:EVEN"))){//每球 尾数，总和
                //     $selectBall = $betInfo[0];
                //     $betContent = $betInfo[1].':'.$betInfo[2];
                //     if($betInfo[0]=="S"){
                //         $selectBall = "5";
                //     }
                // }elseif(in_array($betInfo[1].":".$betInfo[2].":".$betInfo[3],array("OVER:S:ODD","OVER:S:EVEN","UNDER:S:ODD","UNDER:S:EVEN"))){//大单、大双、小单、小双
                //     $selectBall = $betInfo[0];
                //     $betContent = $betInfo[1].":".$betInfo[2].":".$betInfo[3];
                //     if($betInfo[0]=="S"){
                //         $selectBall = "5";
                //     }
                // }elseif($rTypeName=="快速-广西十分彩"){
                //     $selectBall = "quick";
                // }else{//每球 其他，如四季五行、大小、单双、红蓝绿波
                //     $selectBall = $betInfo[0];
                //     $betContent = $betInfo[1];
                //     if($betInfo[0]=="S"){
                //         $selectBall = "5";
                //     }
                // }


                $selectBall = "quick";
                $betContent = "";

                $userid = $order['user_id'];
                $datereg = $order['order_sub_num'];
                $resultMoney    = User::find($userid);
                $assets =   round($resultMoney['money'],2);

                if(in_array($selectBall,array("1","2","3","4","5")) && $result['ball_'.$selectBall]!=21){//开奖结果不等于21的玩法
                    //各种玩法，算法
                    $ds     = Utils::convertToEn(Utils::gxsf_Ds($result['ball_'.$selectBall]));
                    $dx     = Utils::convertToEn(Utils::gxsf_Dx($result['ball_'.$selectBall]));
                    $wsdx   = Utils::convertToEn(Utils::gxsf_WsDx($result['ball_'.$selectBall]));
                    $hsds   = Utils::convertToEn(Utils::gxsf_HsDs($result['ball_'.$selectBall]));
                    $bante  = $dx.":S:".$ds;
                    $season = Utils::convertToEn(Utils::gxsf_season($result['ball_'.$selectBall]));
                    $wuxing = Utils::convertToEn(Utils::gxsf_wuxing($result['ball_'.$selectBall]));
                    $color  = Utils::gxsf_color($result['ball_'.$selectBall]);

                    if(in_array($betContent, array($result['ball_'.$selectBall],$ds, $dx, $wsdx, $hsds, $bante, $season, $wuxing,$color))){
                        $win_sign = "1";
                        $bet_money_total = $order['win']+$order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    }else{
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                }elseif(in_array($selectBall,array("1","2","3","4","5")) && $result['ball_'.$selectBall]==21){//开奖结果等于21的玩法
                    if($betContent== 21 || $betContent=="GREEN" || $betContent=="WOOD"){
                        $win_sign = "1";
                        $bet_money_total = $order['win']+$order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    }elseif($betInfo[1]=="LOCATE" && $betContent!= 21 || $betContent=="RED" || $betContent=="BLUE"
                        ||$betContent=="METAL" || $betContent=="WATER" || $betContent=="FIRE" || $betContent=="EARTH"){
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }else{
                        $win_sign = "2";
                        $bet_money_total = $order['bet_money'];
                        $bet_type = "彩票手工结算-彩票和局";
                    }
                }elseif($selectBall=="一中一"){
                    if(in_array($betContent, array($result['ball_1'],$result['ball_2'],$result['ball_3'],$result['ball_4'],$result['ball_5']))){
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
                        if($betInfo==$dx_1 || $betInfo==$ds_1 || $betInfo==$result['ball_1']){
                            $is_win = "true";
                        }
                        if($dx_1=="和" && in_array($betInfo,array("大","小","单","双"))){
                            $is_win = "tie";
                        }
                    }elseif($quick_type=="第二球"){
                        if($betInfo==$dx_2 || $betInfo==$ds_2 || $betInfo==$result['ball_2']){
                            $is_win = "true";
                        }
                        if($dx_2=="和" && in_array($betInfo,array("大","小","单","双"))){
                            $is_win = "tie";
                        }
                    }elseif($quick_type=="第三球"){
                        if($betInfo==$dx_3 || $betInfo==$ds_3 || $betInfo==$result['ball_3']){
                            $is_win = "true";
                        }
                        if($dx_3=="和" && in_array($betInfo,array("大","小","单","双"))){
                            $is_win = "tie";
                        }
                    }elseif($quick_type=="第四球"){
                        if($betInfo==$dx_4 || $betInfo==$ds_4 || $betInfo==$result['ball_4']){
                            $is_win = "true";
                        }
                        if($dx_4=="和" && in_array($betInfo,array("大","小","单","双"))){
                            $is_win = "tie";
                        }
                    }elseif($quick_type=="第五球"){
                        if($betInfo==$dx_5 || $betInfo==$ds_5 || $betInfo==$result['ball_5']){
                            $is_win = "true";
                        }
                        if($dx_5=="和" && in_array($betInfo,array("大","小","单","双"))){
                            $is_win = "tie";
                        }
                    }elseif($quick_type=="总和龙虎和"){
                        //print_r($hms);exit;
                        if(in_array($betInfo,array($hms[1],$hms[2],$hms[3]))){
                            $is_win = "true";
                        }
                        if($hms[3]=='和' and ($betInfo=='龙' or $betInfo=='虎')){
                             $is_win = "tie";
                        }
                    }elseif($quick_type=="前三"){
                        if($betInfo==$hms[4]){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="中三"){
                        if($betInfo==$hms[5]){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="后三"){
                        if($betInfo==$hms[6]){
                            $is_win = "true";
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

            LotteryResultGXSF::where("qishu", $qishu)
                ->update(["state" => $stateType]);

            $response['message'] = "GXSF Lottery Result checkouted successfully!";
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
