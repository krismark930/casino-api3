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
use App\Models\LotteryResultGD11;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\Web\MoneyLog;
use Carbon\Carbon;
use App\Utils\Utils;

class AdminLotteryResultGD11Controller extends Controller
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
            $lottery_type = "广东十一选五";
            $result = LotteryResultGD11::whereDate("datetime", "=", $query_time);
            if ($qishu_query != "") {
                $result = $result->where("qishu", $qishu_query);
            }
            $result = $result->orderBy("qishu", "desc")->get();

            foreach($result as $item) {
                $item["lottery_type"] = $lottery_type;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result GD11 Data fetched successfully!";
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
            $result = LotteryResultGD11::find($id);

            $response["data"] = $result;
            $response['message'] = "Lottery Result GD11 Data fetched successfully!";
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

                $result = LotteryResultGD11::where("qishu", $qishu)->first();

                if (isset($result)) {
                    $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                    return response()->json($response, $response['status']);
                }

                $item = new LotteryResultGD11;

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

                $item = LotteryResultGD11::find($id);

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
                $item["ball_6"] = $ball_6;
                $item["ball_7"] = $ball_7;
                $item["ball_8"] = $ball_8;

                $item->save();
            }            

            $response['message'] = "GD11 Lottery Result updated successfully!";
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

            $g_type = $request_data["g_type"] ?? "GD11";
            $qishu = $request_data["qishu"];
            $js_type = $request_data["jsType"];

            $result = LotteryResultGD11::where("qishu", $qishu)->first();
            $lottery_name = "广东十一选五";

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
                LotteryResultGD11::where("qishu", $qishu)
                    ->update(["state" => $stateType]);

                $response['message'] = "GD11 Lottery Result checkouted successfully!";
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;

                return response()->json($response, $response['status']);
            }

            $ballArray = array($result['ball_1'],$result['ball_2'],$result['ball_3'],$result['ball_4'],$result['ball_5']);
            $beforeThreeArray = array($result['ball_1'],$result['ball_2'],$result['ball_3']);
            $middleThreeArray = array($result['ball_2'],$result['ball_3'],$result['ball_4']);
            $afterThreeArray  = array($result['ball_3'],$result['ball_4'],$result['ball_5']);
            $ball1 = $result['ball_1'];
            $ball2 = $result['ball_2'];
            $ball3 = $result['ball_3'];
            $ball4 = $result['ball_4'];
            $ball5 = $result['ball_5'];

            $hms[]      = Utils::gd11x5_Auto($ballArray,1);
            $hms[]      = Utils::gd11x5_Auto($ballArray,2);
            $hms[]      = Utils::gd11x5_Auto($ballArray,3);
            $hms[]      = Utils::gd11x5_Auto($ballArray,4);
            $hms[]      = Utils::gd11x5_Auto($ballArray,5);
            $hms[]      = Utils::gd11x5_Auto($ballArray,6);
            $hms[]      = Utils::gd11x5_Auto($ballArray,7);

            $ds_1 = Utils::Ssc_Ds($ball1);
            $dx_1 = Utils::gd11x5_Dx($ball1);
            $ds_2 = Utils::Ssc_Ds($ball2);
            $dx_2 = Utils::gd11x5_Dx($ball2);
            $ds_3 = Utils::Ssc_Ds($ball3);
            $dx_3 = Utils::gd11x5_Dx($ball3);
            $ds_4 = Utils::Ssc_Ds($ball4);
            $dx_4 = Utils::gd11x5_Dx($ball4);
            $ds_5 = Utils::Ssc_Ds($ball5);
            $dx_5 = Utils::gd11x5_Dx($ball5);

            $zh_dx = Utils::gd11x5_Auto($ballArray,2);
            $zh_ds = Utils::gd11x5_Auto($ballArray,3);
            $zh_tiger = Utils::gd11x5_Auto($ballArray,4);
            $zh_dx_en = Utils::getEnNameGd11($zh_dx);
            $zh_ds_en = Utils::getEnNameGd11($zh_ds);
            $zh_tiger_en = Utils::getEnNameGd11($zh_tiger);

            $before_shunzi = Utils::gd11x5_Auto($beforeThreeArray,5);
            $middle_shunzi = Utils::gd11x5_Auto($middleThreeArray,5);
            $after_shunzi = Utils::gd11x5_Auto($afterThreeArray,5);
            $before_shunzi_en = Utils::getEnNameGd11($before_shunzi);
            $middle_shunzi_en = Utils::getEnNameGd11($middle_shunzi);
            $after_shunzi_en = Utils::getEnNameGd11($after_shunzi);

            $ball1_Ds = Utils::lhc_Ds($ball1);
            $ball2_Ds = Utils::lhc_Ds($ball2);
            $ball3_Ds = Utils::lhc_Ds($ball3);
            $ball4_Ds = Utils::lhc_Ds($ball4);
            $ball5_Ds = Utils::lhc_Ds($ball5);
            $ball1_Ds_en = Utils::getEnNameGd11($ball1_Ds);
            $ball2_Ds_en = Utils::getEnNameGd11($ball2_Ds);
            $ball3_Ds_en = Utils::getEnNameGd11($ball3_Ds);
            $ball4_Ds_en = Utils::getEnNameGd11($ball4_Ds);
            $ball5_Ds_en = Utils::getEnNameGd11($ball5_Ds);

            $ball1_Dx = Utils::gd11x5_Dx($ball1);
            $ball2_Dx = Utils::gd11x5_Dx($ball2);
            $ball3_Dx = Utils::gd11x5_Dx($ball3);
            $ball4_Dx = Utils::gd11x5_Dx($ball4);
            $ball5_Dx = Utils::gd11x5_Dx($ball5);
            $ball1_Dx_en = Utils::getEnNameGd11($ball1_Dx);
            $ball2_Dx_en = Utils::getEnNameGd11($ball2_Dx);
            $ball3_Dx_en = Utils::getEnNameGd11($ball3_Dx);
            $ball4_Dx_en = Utils::getEnNameGd11($ball4_Dx);
            $ball5_Dx_en = Utils::getEnNameGd11($ball5_Dx);

            $ball1_HsDs = Utils::lhc_HsDs($ball1);
            $ball2_HsDs = Utils::lhc_HsDs($ball2);
            $ball3_HsDs = Utils::lhc_HsDs($ball3);
            $ball4_HsDs = Utils::lhc_HsDs($ball4);
            $ball5_HsDs = Utils::lhc_HsDs($ball5);
            $ball1_HsDs_en = Utils::getEnNameGd11($ball1_HsDs);
            $ball2_HsDs_en = Utils::getEnNameGd11($ball2_HsDs);
            $ball3_HsDs_en = Utils::getEnNameGd11($ball3_HsDs);
            $ball4_HsDs_en = Utils::getEnNameGd11($ball4_HsDs);
            $ball5_HsDs_en = Utils::getEnNameGd11($ball5_HsDs);

            $ball1_WsDx = Utils::lhc_WsDx($ball1);
            $ball2_WsDx = Utils::lhc_WsDx($ball2);
            $ball3_WsDx = Utils::lhc_WsDx($ball3);
            $ball4_WsDx = Utils::lhc_WsDx($ball4);
            $ball5_WsDx = Utils::lhc_WsDx($ball5);
            $ball1_WsDx_en = Utils::getEnNameGd11($ball1_WsDx);
            $ball2_WsDx_en = Utils::getEnNameGd11($ball2_WsDx);
            $ball3_WsDx_en = Utils::getEnNameGd11($ball3_WsDx);
            $ball4_WsDx_en = Utils::getEnNameGd11($ball4_WsDx);
            $ball5_WsDx_en = Utils::getEnNameGd11($ball5_WsDx);

            foreach($orders as $order){
                $order = get_object_vars($order);
                $is_win = "false";
                $betInfo = explode(":",$order["number"]);
                $quick_type = $order["quick_type"];
                $rTypeName = $order["rtype_str"];

                if($betInfo[1]=="LOCATE"){//每球定位
                    $selectBall = $betInfo[2];
                    if($selectBall == "1"){
                        if($betInfo[0]==$ball1){
                            $is_win = "true";
                        }
                    }elseif($selectBall == "2"){
                        if($betInfo[0]==$ball2){
                            $is_win = "true";
                        }
                    }elseif($selectBall == "3"){
                        if($betInfo[0]==$ball3){
                            $is_win = "true";
                        }
                    }elseif($selectBall == "4"){
                        if($betInfo[0]==$ball4){
                            $is_win = "true";
                        }
                    }elseif($selectBall == "5"){
                        if($betInfo[0]==$ball5){
                            $is_win = "true";
                        }
                    }
                }elseif($betInfo[1]=="MATCH"){
                    if(in_array($betInfo[0],$ballArray)){
                        $is_win = "true";
                    }
                }elseif($betInfo[0]=="TOTAL"){
                    if(in_array($betInfo[1],array($zh_dx_en,$zh_ds_en,$zh_tiger_en))){
                        $is_win = "true";
                    }elseif(($betInfo[1]=="OVER" || $betInfo[1]=="UNDER") && $zh_dx=="总和30"){
                        $is_win = "tie";
                    }
                }elseif($betInfo[0]=="BEFORE" || $betInfo[0]=="MIDDLE" || $betInfo[0]=="AFTER"){
                    if(($betInfo[0]=="BEFORE") && ($betInfo[1]==$before_shunzi_en)){
                        $is_win = "true";
                    }elseif(($betInfo[0]=="MIDDLE") && ($betInfo[1]==$middle_shunzi_en)){
                        $is_win = "true";
                    }elseif(($betInfo[0]=="AFTER") && ($betInfo[1]==$after_shunzi_en)){
                        $is_win = "true";
                    }
                }
                elseif($rTypeName=="快速-广东11选5"){
                    $betInfo = $order["number"];
                    if($quick_type=="第一球"){
                        if($betInfo==$dx_1 || $betInfo==$ds_1 || $betInfo==$ball1){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第二球"){
                        if($betInfo==$dx_2 || $betInfo==$ds_2 || $betInfo==$ball2){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第三球"){
                        if($betInfo==$dx_3 || $betInfo==$ds_3 || $betInfo==$ball3){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第四球"){
                        if($betInfo==$dx_4 || $betInfo==$ds_4 || $betInfo==$ball4){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第五球"){
                        if($betInfo==$dx_5 || $betInfo==$ds_5 || $betInfo==$ball5){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="总和龙虎和"){
                        if(in_array($betInfo,array($hms[1],$hms[2],$hms[3]))){
                            $is_win = "true";
                        }elseif(($betInfo=="总和大" || $betInfo=="总和小") &&$zh_dx=="总和30"){
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
                }
                else{
                    if($betInfo[0]=="1"){
                        if(in_array($betInfo[1],array($ball1_Ds_en,$ball1_Dx_en))){
                            $is_win = "true";
                        }
                        if($betInfo[2]){
                            if(in_array($betInfo[1].":".$betInfo[2],array($ball1_HsDs_en,$ball1_WsDx_en))){
                                $is_win = "true";
                            }
                        }
                    }elseif($betInfo[0] == "2"){
                        if(in_array($betInfo[1],array($ball2_Ds_en,$ball2_Dx_en))){
                            $is_win = "true";
                        }
                        if($betInfo[2]){
                            if(in_array($betInfo[1].":".$betInfo[2],array($ball2_HsDs_en,$ball2_WsDx_en))){
                                $is_win = "true";
                            }
                        }
                    }elseif($betInfo[0] == "3"){
                        if(in_array($betInfo[1],array($ball3_Ds_en,$ball3_Dx_en))){
                            $is_win = "true";
                        }
                        if($betInfo[2]){
                            if(in_array($betInfo[1].":".$betInfo[2],array($ball3_HsDs_en,$ball3_WsDx_en))){
                                $is_win = "true";
                            }
                        }
                    }elseif($betInfo[0] == "4"){
                        if(in_array($betInfo[1],array($ball4_Ds_en,$ball4_Dx_en))){
                            $is_win = "true";
                        }
                        if($betInfo[2]){
                            if(in_array($betInfo[1].":".$betInfo[2],array($ball4_HsDs_en,$ball4_WsDx_en))){
                                $is_win = "true";
                            }
                        }
                    }elseif($betInfo[0] == "5"){
                        if(in_array($betInfo[1],array($ball5_Ds_en,$ball5_Dx_en))){
                            $is_win = "true";
                        }
                        if($betInfo[2]){
                            if(in_array($betInfo[1].":".$betInfo[2],array($ball5_HsDs_en,$ball5_WsDx_en))){
                                $is_win = "true";
                            }
                        }
                    }
                }

                $userid = $order['user_id'];
                $datereg = $order['order_sub_num'];
                $resultMoney = User::find($userid);
                $assets = round($resultMoney['money'],2);

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

            LotteryResultGD11::where("qishu", $qishu)
                ->update(["state" => $stateType]);

            $response['message'] = "GD11 Lottery Result checkouted successfully!";
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
