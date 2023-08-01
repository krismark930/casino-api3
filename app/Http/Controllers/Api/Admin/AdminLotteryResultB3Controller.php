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
use App\Models\LotteryResultD3;
use App\Models\LotteryResultP3;
use App\Models\LotteryResultT3;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\Web\MoneyLog;
use Carbon\Carbon;
use App\Utils\Utils;

class AdminLotteryResultB3Controller extends Controller
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
            $lottery_type = "";
                
            switch ($g_type) {
                case "d3":
                    $lottery_type = "3D彩";
                    $result = LotteryResultD3::whereDate("datetime", ">=", $query_time);
                    if ($qishu_query != "") {
                        $result = $result->where("qishu", $qishu_query);
                    }
                    $result = $result->orderBy("qishu", "desc")->get();
                    break;
                case "p3":
                    $lottery_type = "排列三";
                    $result = LotteryResultP3::whereDate("datetime", ">=", $query_time);
                    if ($qishu_query != "") {
                        $result = $result->where("qishu", $qishu_query);
                    }
                    $result = $result->orderBy("qishu", "desc")->get();
                    break;
                case "t3":
                    $lottery_type = "上海时时乐";
                    $result = LotteryResultT3::whereDate("datetime", $query_time);
                    if ($qishu_query != "") {
                        $result = $result->where("qishu", $qishu_query);
                    }
                    $result = $result->orderBy("qishu", "desc")->get();
                    break;
            }

            foreach($result as $item) {
                $hm = array();
                $hm[] = Utils::BuLing($item["ball_1"]);
                $hm[] = Utils::BuLing($item["ball_2"]);
                $hm[] = Utils::BuLing($item["ball_3"]);
                $item["sum"] = Utils::f3D_Auto($hm,1) . " / " . Utils::f3D_Auto($hm,2) . " / " . Utils::f3D_Auto($hm,3);
                $item["dragon_tiger"] = Utils::f3D_Auto($hm,4);
                $item["top_middle_last"] = Utils::f3D_Auto($hm,5);
                $item["last"] = Utils::f3D_Auto($hm,6);
                $item["lottery_type"] = $lottery_type;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result B3 Data fetched successfully!";
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
                "g_type" => "required|string",
                "id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $g_type = $request_data["g_type"];
            $id = $request_data["id"];
                
            switch ($g_type) {
                case "d3":
                    $result = LotteryResultD3::find($id);
                    break;
                case "p3":
                    $result = LotteryResultP3::find($id);
                    break;
                case "t3":
                    $result = LotteryResultT3::find($id);
                    break;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result B3 Data fetched successfully!";
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
                "g_type" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $action = $request_data["action"];
            $id = $request_data["id"];
            $g_type = $request_data["g_type"];
            $qishu = $request_data["qishu"];
            $datetime = $request_data["datetime"];
            $ball_1 = $request_data["ball_1"];
            $ball_2 = $request_data["ball_2"];
            $ball_3 = $request_data["ball_3"];


            if ($action == "add" && $id==0) {

                $create_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                switch ($g_type) {
                    case "d3":
                        $result = LotteryResultD3::where("qishu", $qishu)->first();
                        if (isset($result)) {
                            $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                            return response()->json($response, $response['status']);
                        }
                        $item = new LotteryResultD3;
                        break;
                    case "p3":
                        $result = LotteryResultP3::where("qishu", $qishu)->first();
                        if (isset($result)) {
                            $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                            return response()->json($response, $response['status']);
                        }
                        $item = new LotteryResultP3;
                        break;
                    case "t3":
                        $result = LotteryResultT3::where("qishu", $qishu)->first();
                        if (isset($result)) {
                            $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                            return response()->json($response, $response['status']);
                        }
                        $item = new LotteryResultT3;
                        break;
                }

                $item["qishu"] = $qishu;
                $item["create_time"] = $create_time;
                $item["datetime"] = $datetime;
                $item["ball_1"] = $ball_1;
                $item["ball_2"] = $ball_2;
                $item["ball_3"] = $ball_3;

                $item->save();

            } else if ($action == "edit" && $id > 0) {
                
                switch ($g_type) {
                    case "d3":
                        $item = LotteryResultD3::find($id);
                        break;
                    case "p3":
                        $item = LotteryResultP3::find($id);
                        break;
                    case "t3":
                        $item = LotteryResultT3::find($id);
                        break;
                }

                $time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                $prev_text = "修改时间：".($time)."。\n修改前内容：".$item["ball_1"].",".$item["ball_2"].",".$item["ball_3"]."。\n修改后内容：".$ball_1.",".$ball_2.",".$ball_3."。\n\n".$item["prev_text"];

                $item["qishu"] = $qishu;
                $item["prev_text"] = $prev_text;
                $item["datetime"] = $datetime;
                $item["ball_1"] = $ball_1;
                $item["ball_2"] = $ball_2;
                $item["ball_3"] = $ball_3;

                $item->save();
            }            

            $response['message'] = "B3 Lottery Result updated successfully!";
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
                "g_type" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $g_type = $request_data["g_type"];
            $qishu = $request_data["qishu"];
            $type = $request_data["type"];
            $js_type = $request_data["jsType"];

            switch ($g_type) {
                case "d3":
                    $result = LotteryResultD3::where("qishu", $qishu)->first();
                    $lottery_name = "3D彩";
                    break;
                case "p3":
                    $result = LotteryResultP3::where("qishu", $qishu)->first();
                    $lottery_name = "排列三";
                    break;
                case "t3":
                    $result = LotteryResultT3::where("qishu", $qishu)->first();
                    $lottery_name = "上海时时乐";
                    break;
            }

            $hm    = array();
            $hm[]  = $result['ball_1'];
            $hm[]  = $result['ball_2'];
            $hm[]  = $result['ball_3'];
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
                        $rsMoney = User::find($userid);
                        $assets = round($rsMoney['Money'],2);
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
                switch ($g_type) {
                    case "d3":
                        LotteryResultD3::where("qishu", $qishu)
                            ->update(["state" => $stateType]);
                        break;
                    case "p3":
                        LotteryResultP3::where("qishu", $qishu)
                            ->update(["state" => $stateType]);
                        break;
                    case "t3":
                        LotteryResultT3::where("qishu", $qishu)
                            ->update(["state" => $stateType]);
                        break;
                }

                $response['message'] = "B3 Lottery Result checkouted successfully!";
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;

                return response()->json($response, $response['status']);
            }

            //结算方法、结算数据
            $ballArray = array($result['ball_1'],$result['ball_2'],$result['ball_3']);
            $ballHundred = $result['ball_1'];
            $ballTen = $result['ball_2'];
            $ballOne = $result['ball_3'];

            $hms[] = Utils::f3D_Auto($ballArray,1);
            $hms[] = Utils::f3D_Auto($ballArray,2);
            $hms[] = Utils::f3D_Auto($ballArray,3);
            $hms[] = Utils::f3D_Auto($ballArray,4);
            $hms[] = Utils::f3D_Auto($ballArray,5);
            $hms[] = Utils::f3D_Auto($ballArray,6);

            $ds_1 = Utils::Ssc_Ds($ballHundred);
            $dx_1 = Utils::Ssc_Dx($ballHundred);
            $ds_2 = Utils::Ssc_Ds($ballTen);
            $dx_2 = Utils::Ssc_Dx($ballTen);
            $ds_3 = Utils::Ssc_Ds($ballOne);
            $dx_3 = Utils::Ssc_Dx($ballOne);

            $hundredDs = Utils::b3_ds($ballHundred,"M");
            $tenDs = Utils::b3_ds($ballTen,"C");
            $oneDs = Utils::b3_ds($ballOne,"U");
            $mcDs = Utils::b3_ds($ballHundred+$ballTen,"MC");
            $muDs = Utils::b3_ds($ballHundred+$ballOne,"MU");
            $cuDs = Utils::b3_ds($ballTen+$ballOne,"CU");
            $mcuDs = Utils::b3_ds($ballHundred+$ballTen+$ballOne,"MCU");

            $hundredDx = Utils::b3_dx($ballHundred,"M");
            $tenDx = Utils::b3_dx($ballTen,"C");
            $oneDx = Utils::b3_dx($ballOne,"U");
            $mcDx = Utils::b3_dx($ballHundred+$ballTen,"MC");
            $muDx = Utils::b3_dx($ballHundred+$ballOne,"MU");
            $cuDx = Utils::b3_dx($ballTen+$ballOne,"CU");
            $mcuDx = ($ballHundred+$ballTen+$ballOne) > 13 ? "MCU_OVER" : "MCU_UNDER";

            $hundredZhihe = Utils::b3_zhihe($ballHundred,"M");
            $tenZhihe = Utils::b3_zhihe($ballTen,"C");
            $oneZhihe = Utils::b3_zhihe($ballOne,"U");
            $mcZhihe = Utils::b3_zhihe($ballHundred+$ballTen,"MC");
            $muZhihe = Utils::b3_zhihe($ballHundred+$ballOne,"MU");
            $cuZhihe = Utils::b3_zhihe($ballTen+$ballOne,"CU");
            $mcuZhihe = Utils::b3_zhihe($ballHundred+$ballTen+$ballOne,"MCU");

            $oeouArray = array($hundredDs,$tenDs,$oneDs,$mcDs,$muDs,$cuDs,$mcuDs,
                $hundredDx,$tenDx,$oneDx,$mcDx,$muDx,$cuDx,$mcuDx,
                $hundredZhihe,$tenZhihe,$oneZhihe,$mcZhihe,$muZhihe,$cuZhihe,$mcuZhihe);

            $wpArray = array($hundredDs,$tenDs,$oneDs,$hundredDx,$tenDx,$oneDx,$hundredZhihe,$tenZhihe,$oneZhihe);

            $mcF = Utils::b3_f($ballHundred+$ballTen);
            $muF = Utils::b3_f($ballHundred+$ballOne);
            $cuF = Utils::b3_f($ballTen+$ballOne);
            $mcuF = Utils::b3_f($ballHundred+$ballTen+$ballOne);

            $mcS = $ballHundred+$ballTen;
            $muS = $ballHundred+$ballOne;
            $cuS = $ballTen+$ballOne;
            $mcuS = $ballHundred+$ballTen+$ballOne;

            $kd = Utils::b3_kd($ballHundred,$ballTen,$ballOne);

            foreach($orders as $order){
                $order = get_object_vars($order);
                $is_win = "false";
                $rTypeName = $order["rtype_str"];
                $betInfo = $order["number"];
                $quick_type = $order["quick_type"];

                if($rTypeName=="一字"){
                    if(in_array($betInfo, $ballArray)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="佰定位"){
                    if($betInfo==$ballHundred){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="拾定位"){
                    if($betInfo==$ballTen){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="个定位"){
                    if($betInfo==$ballOne){
                        $is_win = "true";
                    }
                }

                elseif($rTypeName=="佰拾个和尾数"){
                    if($betInfo==$mcuF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="佰拾和尾数"){
                    if($betInfo==$mcF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="佰个和尾数"){
                    if($betInfo==$muF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="拾个和尾数"){
                    if($betInfo==$cuF){
                        $is_win = "true";
                    }
                }

                elseif($rTypeName=="和数"){
                    if($betInfo==$mcuS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="佰拾和数"){
                    if($betInfo==$mcS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="佰个和数"){
                    if($betInfo==$muS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="拾个和数"){
                    if($betInfo==$cuS){
                        $is_win = "true";
                    }
                }

                elseif($rTypeName=="二字"){
                    if(in_array(intval($betInfo/10), $ballArray) && in_array($betInfo%10, $ballArray)){
                        if(intval($betInfo/10)==$betInfo%10){
                            if(($ballHundred==intval($betInfo/10) && ($betInfo%10)==$ballTen) ||
                                ($ballHundred==intval($betInfo/10) && ($betInfo%10)==$ballOne) ||
                                ($ballTen==intval($betInfo/10) && ($betInfo%10)==$ballOne)){
                                $is_win = "true";
                            }
                        }else{
                            $is_win = "true";
                        }
                    }
                }elseif($rTypeName=="佰拾定位"){
                    if($betInfo==strval($ballHundred).strval($ballTen)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="佰个定位"){
                    if($betInfo==strval($ballHundred).strval($ballOne)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="拾个定位"){
                    if($betInfo==strval($ballTen).strval($ballOne)){
                        $is_win = "true";
                    }
                }

                elseif($rTypeName=="两面"){
                    if(in_array($betInfo, $oeouArray)){
                        $is_win = "true";
                    }
                }
                elseif($rTypeName=="跨度"){
                    if($betInfo==$kd){
                        $is_win = "true";
                    }
                }
                elseif($rTypeName=="组选三"){
                    $betInfo = explode("*",$order["number"]);
                    if($kd==0){

                    }elseif($ballHundred!=$ballTen && $ballHundred!=$ballOne && $ballTen!=$ballOne){

                    }elseif((in_array($ballHundred,$betInfo) && in_array($ballTen,$betInfo) && in_array($ballOne,$betInfo)) &&
                        ($ballHundred==$ballTen || $ballHundred==$ballOne || $ballTen==$ballOne)
                    ){
                        $is_win = "true";
                    }
                }
                elseif($rTypeName=="组选六"){
                    $betInfo = explode("*",$order["number"]);
                    if($ballHundred!=$ballTen && $ballHundred!=$ballOne && $ballTen!=$ballOne){
                        if(in_array($ballHundred,$betInfo) && in_array($ballTen,$betInfo) && in_array($ballOne,$betInfo)){
                            $is_win = "true";
                        }
                    }
                }
                elseif($rTypeName=="一字过关"){
                    $betInfo = explode("*",$order["number"]);
                    if(in_array($betInfo[0],$wpArray) && in_array($betInfo[1],$wpArray)){
                        if(count($betInfo)==3){
                            if(in_array($betInfo[2],$wpArray)){
                                $is_win = "true";
                            }
                        }else{
                            $is_win = "true";
                        }
                    }
                }
                elseif(strpos($rTypeName,"快速-")!==false){
                    if($quick_type=="第一球"){
                        if($betInfo==$dx_1 || $betInfo==$ds_1 || $betInfo==$ballHundred){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第二球"){
                        if($betInfo==$dx_2 || $betInfo==$ds_2 || $betInfo==$ballTen){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第三球"){
                        if($betInfo==$dx_3 || $betInfo==$ds_3 || $betInfo==$ballOne){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="总和龙虎和"){
                        //print_r($hms);exit;
                        if(in_array($betInfo,array($hms[1],$hms[2],$hms[3]))){
                            $is_win = "true";
                        }
                        if($hms[3]=='和' and ($betInfo=='龙' or $betInfo=='虎')){
                            $is_win = "和";
                        }
                    }elseif($quick_type=="三连"){
                        if($betInfo==$hms[4]){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="跨度"){
                        if($betInfo==$hms[5]){
                            $is_win = "true";
                        }
                    }
                }

                $userid = $order['user_id'];
                $datereg = $order['order_sub_num'];
                $rsMoney    = User::find($userid);
                $assets =   round($rsMoney['money'],2);

                if($is_win=="true"){
                    $win_sign = "1";
                    $bet_money_total = $order['win']+$order['fs'] +$order['bet_money'];
                    $bet_type = "彩票手工结算-彩票中奖";
                }elseif($is_win=='和'){
                    $win_sign = "2";
                    $bet_money_total = $order['bet_money'];
                    $bet_type = "彩票手工结算-和局-退还本金";
                }else{
                    $win_sign = "0";
                    $bet_money_total = $order['fs'];
                    $bet_type = "彩票手工结算-彩票反水";
                }

                OrderLottery::where("id", $order["id"])->update(["status" => $stateType]);

                OrderLotterySub::where("id", $order["sub_id"])
                            ->update(["status" => $stateType, "is_win" => $win_sign]);

                if($win_sign == "1" ||$win_sign == "2" || ($win_sign == "0" && $order['fs']>0)){

                    $q1 = User::where("id", $userid)
                        ->where("Pay_Type", 1)
                        ->increment('Money', $bet_money_total);

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

            //最后更新彩票结果表，状态修改
            switch ($g_type) {
                case "d3":
                    $result = LotteryResultD3::where("qishu", $qishu)
                        ->update(["state" => $stateType]);
                    break;
                case "p3":
                    $result = LotteryResultP3::where("qishu", $qishu)
                        ->update(["state" => $stateType]);
                    break;
                case "t3":
                    $result = LotteryResultT3::where("qishu", $qishu)
                        ->update(["state" => $stateType]);
                    break;
            }

            $response['message'] = "B3 Lottery Result checkouted successfully!";
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
