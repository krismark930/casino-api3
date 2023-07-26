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
use App\Models\LotteryResultCQ;
use App\Models\LotteryResultFFC5;
use App\Models\LotteryResultTXSSC;
use App\Models\LotteryResultTWSSC;
use App\Models\LotteryResultAZXY5;
use App\Models\LotteryResultJX;
use App\Models\LotteryResultTJ;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\Web\MoneyLog;
use Carbon\Carbon;
use App\Utils\Utils;

class AdminLotteryResultB5Controller extends Controller
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
                case "cq":
                    $lottery_type = "重庆时时彩";
                    $result = LotteryResultCQ::whereDate("datetime", "=", $query_time);
                    if ($qishu_query != "") {
                        $result = $result->where("qishu", $qishu_query);
                    }
                    $result = $result->orderBy("qishu", "desc")->get();
                    break;
                case "ffc5":
                    $lottery_type = "五分彩";
                    $result = LotteryResultFFC5::whereDate("datetime", "=", $query_time);
                    if ($qishu_query != "") {
                        $result = $result->where("qishu", $qishu_query);
                    }
                    $result = $result->orderBy("qishu", "desc")->get();
                    break;
                case "txssc":
                    $lottery_type = "腾讯时时彩";
                    $result = LotteryResultTXSSC::whereDate("datetime", "=", $query_time);
                    if ($qishu_query != "") {
                        $result = $result->where("qishu", $qishu_query);
                    }
                    $result = $result->orderBy("qishu", "desc")->get();
                    break;
                case "twssc":
                    $lottery_type = "台湾时时彩";
                    $result = LotteryResultTWSSC::whereDate("datetime", "=", $query_time);
                    if ($qishu_query != "") {
                        $result = $result->where("qishu", $qishu_query);
                    }
                    $result = $result->orderBy("qishu", "desc")->get();
                    break;
                case "azxy5":
                    $lottery_type = "澳洲幸运5";
                    $result = LotteryResultAZXY5::whereDate("datetime", "=", $query_time);
                    if ($qishu_query != "") {
                        $result = $result->where("qishu", $qishu_query);
                    }
                    $result = $result->orderBy("qishu", "desc")->get();
                    break;
                case "jx":
                    $lottery_type = "新疆时时彩";
                    $result = LotteryResultJX::whereDate("datetime", "=", $query_time);
                    if ($qishu_query != "") {
                        $result = $result->where("qishu", $qishu_query);
                    }
                    $result = $result->orderBy("qishu", "desc")->get();
                    break;
                case "tj":
                    $lottery_type = "天津时时彩";
                    $result = LotteryResultTJ::whereDate("datetime", "=", $query_time);
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
                $hm[] = Utils::BuLing($item["ball_4"]);
                $hm[] = Utils::BuLing($item["ball_5"]);
                $item["sum"] = Utils::Ssc_Auto($hm,1) . " / " . Utils::Ssc_Auto($hm,2) . " / " .Utils::Ssc_Auto($hm,3);
                $item["dragon_tiger"] = Utils::Ssc_Auto($hm,4);
                $item["top_middle_last"] = Utils::Ssc_Auto($hm,5) . " / " . Utils::Ssc_Auto($hm,6) . " / " . Utils::Ssc_Auto($hm,7);
                $item["lottery_type"] = $lottery_type;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result B5 Data fetched successfully!";
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
                case "cq":
                    $result = LotteryResultCQ::find($id);
                    break;
                case "ffc5":
                    $result = LotteryResultFFC5::find($id);
                    break;
                case "txssc":
                    $result = LotteryResultTXSSC::find($id);
                    break;
                case "twssc":
                    $result = LotteryResultTWSSC::find($id);
                    break;
                case "azxy5":
                    $result = LotteryResultAZXY5::find($id);
                    break;
                case "jx":
                    $result = LotteryResultJX::find($id);
                    break;
                case "tj":
                    $result = LotteryResultTJ::find($id);
                    break;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result B5 Data fetched successfully!";
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
            $ball_4 = $request_data["ball_4"];
            $ball_5 = $request_data["ball_5"];


            if ($action == "add" && $id==0) {

                $create_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                switch ($g_type) {
                    case "cq":
                        $result = LotteryResultCQ::where("qishu", $qishu)->first();
                        if (isset($result)) {
                            $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                            return response()->json($response, $response['status']);
                        }
                        $item = new LotteryResultCQ;
                        break;
                    case "ffc5":
                        $result = LotteryResultFFC5::where("qishu", $qishu)->first();
                        if (isset($result)) {
                            $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                            return response()->json($response, $response['status']);
                        }
                        $item = new LotteryResultFFC5;
                        break;
                    case "txssc":
                        $result = LotteryResultTXSSC::where("qishu", $qishu)->first();
                        if (isset($result)) {
                            $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                            return response()->json($response, $response['status']);
                        }
                        $item = new LotteryResultTXSSC;
                        break;
                    case "twssc":
                        $result = LotteryResultTWSSC::where("qishu", $qishu)->first();
                        if (isset($result)) {
                            $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                            return response()->json($response, $response['status']);
                        }
                        $item = new LotteryResultTWSSC;
                        break;
                    case "azxy5":
                        $result = LotteryResultAZXY5::where("qishu", $qishu)->first();
                        if (isset($result)) {
                            $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                            return response()->json($response, $response['status']);
                        }
                        $item = new LotteryResultAZXY5;
                        break;
                    case "jx":
                        $result = LotteryResultJX::where("qishu", $qishu)->first();
                        if (isset($result)) {
                            $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                            return response()->json($response, $response['status']);
                        }
                        $item = new LotteryResultJX;
                        break;
                    case "tj":
                        $result = LotteryResultTJ::where("qishu", $qishu)->first();
                        if (isset($result)) {
                            $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                            return response()->json($response, $response['status']);
                        }
                        $item = new LotteryResultTJ;
                        break;
                }

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
                
                switch ($g_type) {
                    case "cq":
                        $item = LotteryResultCQ::find($id);
                        break;
                    case "ffc5":
                        $item = LotteryResultFFC5::find($id);
                        break;
                    case "txssc":
                        $item = LotteryResultTXSSC::find($id);
                        break;
                    case "twssc":
                        $item = LotteryResultTWSSC::find($id);
                        break;
                    case "azxy5":
                        $item = LotteryResultAZXY5::find($id);
                        break;
                    case "jx":
                        $item = LotteryResultJX::find($id);
                        break;
                    case "tj":
                        $item = LotteryResultTJ::find($id);
                        break;
                }

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

            $response['message'] = "B5 Lottery Result updated successfully!";
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
                case "cq":
                    $result = LotteryResultCQ::where("qishu", $qishu)->first();
                    $lottery_name = "重庆时时彩";
                    break;
                case "ffc5":
                    $result = LotteryResultFFC5::where("qishu", $qishu)->first();
                    $lottery_name = "五分彩";
                    break;
                case "txssc":
                    $result = LotteryResultTXSSC::where("qishu", $qishu)->first();
                    $lottery_name = "腾讯时时彩";
                    break;
                case "twssc":
                    $result = LotteryResultTWSSC::where("qishu", $qishu)->first();
                    $lottery_name = "台湾时时彩";
                    break;
                case "azxy5":
                    $result = LotteryResultAZXY5::where("qishu", $qishu)->first();
                    $lottery_name = "澳洲幸运5";
                    break;
                case "jx":
                    $result = LotteryResultJX::where("qishu", $qishu)->first();
                    $lottery_name = "新疆时时彩";
                    break;
                case "tj":
                    $result = LotteryResultTJ::where("qishu", $qishu)->first();
                    $lottery_name = "天津时时彩";
                    break;
            }

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
                    case "cq":
                        LotteryResultCQ::where("qishu", $qishu)
                            ->update(["state" => $stateType]);
                        break;
                    case "ffc5":
                        LotteryResultFFC5::where("qishu", $qishu)
                            ->update(["state" => $stateType]);
                        break;
                    case "txssc":
                        LotteryResultTXSSC::where("qishu", $qishu)
                            ->update(["state" => $stateType]);
                        break;
                    case "twssc":
                        LotteryResultTWSSC::where("qishu", $qishu)
                            ->update(["state" => $stateType]);
                        break;
                    case "azxy5":
                        LotteryResultAZXY5::where("qishu", $qishu)
                            ->update(["state" => $stateType]);
                        break;
                    case "jx":
                        LotteryResultJX::where("qishu", $qishu)
                            ->update(["state" => $stateType]);
                        break;
                    case "tj":
                        LotteryResultTJ::where("qishu", $qishu)
                            ->update(["state" => $stateType]);
                        break;
                }

                $response['message'] = "B5 Lottery Result checkouted successfully!";
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;

                return response()->json($response, $response['status']);
            }

            //结算方法、结算数据
            $ballArray = array($result['ball_1'],$result['ball_2'],$result['ball_3'],$result['ball_4'],$result['ball_5']);
            $beforeThreeArray = array($result['ball_1'],$result['ball_2'],$result['ball_3']);
            $middleThreeArray = array($result['ball_2'],$result['ball_3'],$result['ball_4']);
            $afterThreeArray  = array($result['ball_3'],$result['ball_4'],$result['ball_5']);
            $ballWan = $result['ball_1'];
            $ballQian = $result['ball_2'];
            $ballHundred = $result['ball_3'];
            $ballTen = $result['ball_4'];
            $ballOne = $result['ball_5'];

            $hms[] = Utils::Ssc_Auto($ballArray,1);
            $hms[] = Utils::Ssc_Auto($ballArray,2);
            $hms[] = Utils::Ssc_Auto($ballArray,3);
            $hms[] = Utils::Ssc_Auto($ballArray,4);
            $hms[] = Utils::Ssc_Auto($ballArray,5);
            $hms[] = Utils::Ssc_Auto($ballArray,6);
            $hms[] = Utils::Ssc_Auto($ballArray,7);

            $niuniu = Utils::b5_niuniu($ballWan,$ballQian,$ballHundred,$ballTen,$ballOne);
            $niuds  = Utils::b5_niuds($niuniu);
            $niudx  = Utils::b5_niudx($niuniu);

            $ds_wan = Utils::Ssc_Ds($ballWan);
            $dx_wan = Utils::Ssc_Dx($ballWan);
            $ds_qian = Utils::Ssc_Ds($ballQian);
            $dx_qian = Utils::Ssc_Dx($ballQian);
            $ds_hundred = Utils::Ssc_Ds($ballHundred);
            $dx_hundred = Utils::Ssc_Dx($ballHundred);
            $ds_ten = Utils::Ssc_Ds($ballTen);
            $dx_ten = Utils::Ssc_Dx($ballTen);
            $ds_one = Utils::Ssc_Ds($ballOne);
            $dx_one = Utils::Ssc_Dx($ballOne);

            $beforeF = Utils::b5_array_f($beforeThreeArray);
            $middleF = Utils::b5_array_f($middleThreeArray);
            $afterF  = Utils::b5_array_f($afterThreeArray);
            $wqF     = Utils::b5_f($ballWan+$ballQian);
            $wbF     = Utils::b5_f($ballWan+$ballHundred);
            $wsF     = Utils::b5_f($ballWan+$ballTen);
            $wgF     = Utils::b5_f($ballWan+$ballOne);
            $qbF     = Utils::b5_f($ballQian+$ballHundred);
            $qsF     = Utils::b5_f($ballQian+$ballTen);
            $qgF     = Utils::b5_f($ballQian+$ballOne);
            $bsF     = Utils::b5_f($ballHundred+$ballTen);
            $bgF     = Utils::b5_f($ballHundred+$ballOne);
            $sgF     = Utils::b5_f($ballTen+$ballOne);

            $beforeS = $ballWan+$ballQian+$ballHundred;
            $middleS = $ballQian+$ballHundred+$ballTen;
            $afterS  = $ballHundred+$ballTen+$ballOne;
            $wqS     = $ballWan+$ballQian;
            $wbS     = $ballWan+$ballHundred;
            $wsS     = $ballWan+$ballTen;
            $wgS     = $ballWan+$ballOne;
            $qbS     = $ballQian+$ballHundred;
            $qsS     = $ballQian+$ballTen;
            $qgS     = $ballQian+$ballOne;
            $bsS     = $ballHundred+$ballTen;
            $bgS     = $ballHundred+$ballOne;
            $sgS     = $ballTen+$ballOne;

            $beforeKd = Utils::b5_kd($ballWan,$ballQian,$ballHundred);
            $middleKd = Utils::b5_kd($ballQian,$ballHundred,$ballTen);
            $afterKd  = Utils::b5_kd($ballHundred,$ballTen,$ballOne);

            $wanDs = Utils::b5_ds($ballWan,"535");
            $qianDs = Utils::b5_ds($ballQian,"536");
            $hundredDs = Utils::b5_ds($ballHundred,"537");
            $tenDs = Utils::b5_ds($ballTen,"538");
            $oneDs = Utils::b5_ds($ballOne,"539");
            $wqDs = Utils::b5_ds($wqS,"550");
            $wbDs = Utils::b5_ds($wbS,"551");
            $wsDs = Utils::b5_ds($wsS,"552");
            $wgDs = Utils::b5_ds($wgS,"553");
            $qbDs = Utils::b5_ds($qbS,"554");
            $qsDs = Utils::b5_ds($qsS,"555");
            $qgDs = Utils::b5_ds($qgS,"556");
            $bsDs = Utils::b5_ds($bsS,"557");
            $bgDs = Utils::b5_ds($bgS,"558");
            $sgDs = Utils::b5_ds($sgS,"559");
            $beforeDs = Utils::b5_ds($beforeS,"580");
            $middleDs = Utils::b5_ds($middleS,"581");
            $afterDs = Utils::b5_ds($afterS,"582");

            $wanDx = Utils::b5_dx($ballWan,"540");
            $qianDx = Utils::b5_dx($ballQian,"541");
            $hundredDx = Utils::b5_dx($ballHundred,"542");
            $tenDx = Utils::b5_dx($ballTen,"543");
            $oneDx = Utils::b5_dx($ballOne,"544");
            $wqDx = Utils::b5_dx($wqS,"560");
            $wbDx = Utils::b5_dx($wbS,"561");
            $wsDx = Utils::b5_dx($wsS,"562");
            $wgDx = Utils::b5_dx($wgS,"563");
            $qbDx = Utils::b5_dx($qbS,"564");
            $qsDx = Utils::b5_dx($qsS,"565");
            $qgDx = Utils::b5_dx($qgS,"566");
            $bsDx = Utils::b5_dx($bsS,"567");
            $bgDx = Utils::b5_dx($bgS,"568");
            $sgDx = Utils::b5_dx($sgS,"569");
            $beforeDx = Utils::b5_zh_dx($beforeS,"583");
            $middleDx = Utils::b5_zh_dx($middleS,"584");
            $afterDx = Utils::b5_zh_dx($afterS,"585");

            $wanZhihe = Utils::b5_zhihe($ballWan,"545");
            $qianZhihe = Utils::b5_zhihe($ballQian,"546");
            $hundredZhihe = Utils::b5_zhihe($ballHundred,"547");
            $tenZhihe = Utils::b5_zhihe($ballTen,"548");
            $oneZhihe = Utils::b5_zhihe($ballOne,"549");
            $wqZhihe = Utils::b5_zhihe($wqS,"570");
            $wbZhihe = Utils::b5_zhihe($wbS,"571");
            $wsZhihe = Utils::b5_zhihe($wsS,"572");
            $wgZhihe = Utils::b5_zhihe($wgS,"573");
            $qbZhihe = Utils::b5_zhihe($qbS,"574");
            $qsZhihe = Utils::b5_zhihe($qsS,"575");
            $qgZhihe = Utils::b5_zhihe($qgS,"576");
            $bsZhihe = Utils::b5_zhihe($bsS,"577");
            $bgZhihe = Utils::b5_zhihe($bgS,"578");
            $sgZhihe = Utils::b5_zhihe($sgS,"579");
            $beforeZhihe = Utils::b5_zhihe($beforeS,"586");
            $middleZhihe = Utils::b5_zhihe($middleS,"587");
            $afterZhihe = Utils::b5_zhihe($afterS,"588");

            $oeouArray = array($wanDs,$qianDs,$hundredDs,$tenDs,$oneDs,$wqDs,$wbDs,$wsDs,$wgDs,$qbDs,$qsDs,$qgDs,$bsDs,$bgDs,$sgDs,$beforeDs,$middleDs,$afterDs,$wanDx,$qianDx,$hundredDx,$tenDx,$oneDx,$wqDx,$wbDx,$wsDx,$wgDx,$qbDx,$qsDx,$qgDx,$bsDx,$bgDx,$sgDx,$beforeDx,$middleDx,$afterDx,$wanZhihe,$qianZhihe,$hundredZhihe,$tenZhihe,$oneZhihe,$wqZhihe,$wbZhihe,$wsZhihe,$wgZhihe,$qbZhihe,$qsZhihe,$qgZhihe,$bsZhihe,$bgZhihe,$sgZhihe,$beforeZhihe,$middleZhihe,$afterZhihe);


            foreach($orders as $order){
                $order = get_object_vars($order);
                $is_win = "false";
                $is_multi_win = 0;

                $rTypeName = $order["rtype_str"];
                $betInfo = $order["number"];
                $quick_type = $order["quick_type"];

                if($rTypeName=="全五-多重彩派"){
                    if($betInfo==$ballOne){
                        $is_win = "true";
                        $is_multi_win += 1;
                    }
                    if($betInfo==$ballTen){
                        $is_win = "true";
                        $is_multi_win += 1;
                    }
                    if($betInfo==$ballHundred){
                        $is_win = "true";
                        $is_multi_win += 1;
                    }
                    if($betInfo==$ballQian){
                        $is_win = "true";
                        $is_multi_win += 1;
                    }
                    if($betInfo==$ballWan){
                        $is_win = "true";
                        $is_multi_win += 1;
                    }
                }elseif($rTypeName=="(前三)一字组合"){
                    if(in_array($betInfo, $beforeThreeArray)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="(中三)一字组合"){
                    if(in_array($betInfo, $middleThreeArray)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="(后三)一字组合"){
                    if(in_array($betInfo, $afterThreeArray)){
                        $is_win = "true";
                    }
                }

                elseif($rTypeName=="(前三)和尾数"){
                    if($betInfo==$beforeF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="(中三)和尾数"){
                    if($betInfo==$middleF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="(后三)和尾数"){
                    if($betInfo==$afterF){
                        $is_win = "true";
                    }
                }
                elseif($rTypeName=="万仟和尾数"){
                    if($betInfo==$wqF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="万佰和尾数"){
                    if($betInfo==$wbF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="万拾和尾数"){
                    if($betInfo==$wsF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="万个和尾数"){
                    if($betInfo==$wgF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="仟佰和尾数"){
                    if($betInfo==$qbF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="仟拾和尾数"){
                    if($betInfo==$qsF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="仟个和尾数"){
                    if($betInfo==$qgF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="佰拾和尾数"){
                    if($betInfo==$bsF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="佰个和尾数"){
                    if($betInfo==$bgF){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="拾个和尾数"){
                    if($betInfo==$sgF){
                        $is_win = "true";
                    }
                }

                elseif($rTypeName=="(前三)和数"){
                    if($betInfo==$beforeS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="(中三)和数"){
                    if($betInfo==$middleS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="(后三)和数"){
                    if($betInfo==$afterS){
                        $is_win = "true";
                    }
                }
                elseif($rTypeName=="万仟和数"){
                    if($betInfo==$wqS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="万佰和数"){
                    if($betInfo==$wbS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="万拾和数"){
                    if($betInfo==$wsS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="万个和数"){
                    if($betInfo==$wgS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="仟佰和数"){
                    if($betInfo==$qbS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="仟拾和数"){
                    if($betInfo==$qsS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="仟个和数"){
                    if($betInfo==$qgS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="佰拾和数"){
                    if($betInfo==$bsS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="佰个和数"){
                    if($betInfo==$bgS){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="拾个和数"){
                    if($betInfo==$sgS){
                        $is_win = "true";
                    }
                }

                elseif($rTypeName == "万定位"){
                    if($betInfo==$ballWan){
                        $is_win = "true";
                    }
                }elseif($rTypeName == "仟定位"){
                    if($betInfo==$ballQian){
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

                elseif($rTypeName=="(前三)二字组合"){
                    if(in_array(intval($betInfo/10), $beforeThreeArray) && in_array($betInfo%10, $beforeThreeArray)){
                        if(intval($betInfo/10)==$betInfo%10){
                            if(($ballWan==intval($betInfo/10) && ($betInfo%10)==$ballQian) ||
                                ($ballWan==intval($betInfo/10) && ($betInfo%10)==$ballHundred) ||
                                ($ballQian==intval($betInfo/10) && ($betInfo%10)==$ballHundred)){
                                $is_win = "true";
                            }
                        }else{
                            $is_win = "true";
                        }
                    }
                }elseif($rTypeName=="(中三)二字组合"){
                    if(in_array(intval($betInfo/10), $middleThreeArray) && in_array($betInfo%10, $middleThreeArray)){
                        if(intval($betInfo/10)==$betInfo%10){
                            if(($ballQian==intval($betInfo/10) && ($betInfo%10)==$ballHundred) ||
                                ($ballQian==intval($betInfo/10) && ($betInfo%10)==$ballTen) ||
                                ($ballHundred==intval($betInfo/10) && ($betInfo%10)==$ballTen)){
                                $is_win = "true";
                            }
                        }else{
                            $is_win = "true";
                        }
                    }
                }elseif($rTypeName=="(后三)二字组合"){
                    if(in_array(intval($betInfo/10), $afterThreeArray) && in_array($betInfo%10, $afterThreeArray)){
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
                }
                elseif($rTypeName=="万仟定位"){
                    if($betInfo==strval($ballWan).strval($ballQian)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="万佰定位"){
                    if($betInfo==strval($ballWan).strval($ballHundred)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="万拾定位"){
                    if($betInfo==strval($ballWan).strval($ballTen)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="万个定位"){
                    if($betInfo==strval($ballWan).strval($ballOne)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="仟佰定位"){
                    if($betInfo==strval($ballQian).strval($ballHundred)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="仟拾定位"){
                    if($betInfo==strval($ballQian).strval($ballTen)){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="仟个定位"){
                    if($betInfo==strval($ballQian).strval($ballOne)){
                        $is_win = "true";
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

                elseif($rTypeName=="(前三)跨度"){
                    if($betInfo==$beforeKd){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="(中三)跨度"){
                    if($betInfo==$middleKd){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="(后三)跨度"){
                    if($betInfo==$afterKd){
                        $is_win = "true";
                    }
                }

                elseif($rTypeName=="(前三)组选三"){
                    $betInfo = explode("*",$order["number"]);
                    if($beforeKd==0){

                    }elseif($ballWan!=$ballQian && $ballWan!=$ballHundred && $ballQian!=$ballHundred){

                    }elseif((in_array($ballWan,$betInfo) && in_array($ballQian,$betInfo) && in_array($ballHundred,$betInfo)) &&
                        ($ballWan==$ballQian || $ballWan==$ballHundred || $ballQian==$ballHundred)
                    ){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="(中三)组选三"){
                    $betInfo = explode("*",$order["number"]);
                    if($middleKd==0){

                    }elseif($ballQian!=$ballHundred && $ballQian!=$ballTen && $ballHundred!=$ballTen){

                    }elseif((in_array($ballQian,$betInfo) && in_array($ballHundred,$betInfo) && in_array($ballTen,$betInfo)) &&
                        ($ballQian==$ballHundred || $ballQian==$ballTen || $ballHundred==$ballTen)
                    ){
                        $is_win = "true";
                    }
                }elseif($rTypeName=="(后三)组选三"){
                    $betInfo = explode("*",$order["number"]);
                    if($afterKd==0){

                    }elseif($ballHundred!=$ballTen && $ballHundred!=$ballOne && $ballTen!=$ballOne){

                    }elseif((in_array($ballHundred,$betInfo) && in_array($ballTen,$betInfo) && in_array($ballOne,$betInfo)) &&
                        ($ballHundred==$ballTen || $ballHundred==$ballOne || $ballTen==$ballOne)
                    ){
                        $is_win = "true";
                    }
                }

                elseif($rTypeName=="(前三)组选六"){
                    $betInfo = explode("*",$order["number"]);
                    if($ballWan!=$ballQian && $ballWan!=$ballHundred && $ballQian!=$ballHundred){
                        if(in_array($ballWan,$betInfo) && in_array($ballQian,$betInfo) && in_array($ballHundred,$betInfo)){
                            $is_win = "true";
                        }
                    }
                }elseif($rTypeName=="(中三)组选六"){
                    $betInfo = explode("*",$order["number"]);
                    if($ballQian!=$ballHundred && $ballQian!=$ballTen && $ballHundred!=$ballTen){
                        if(in_array($ballQian,$betInfo) && in_array($ballHundred,$betInfo) && in_array($ballTen,$betInfo)){
                            $is_win = "true";
                        }
                    }
                }elseif($rTypeName=="(后三)组选六"){
                    $betInfo = explode("*",$order["number"]);
                    if($ballHundred!=$ballTen && $ballHundred!=$ballOne && $ballTen!=$ballOne){
                        if(in_array($ballHundred,$betInfo) && in_array($ballTen,$betInfo) && in_array($ballOne,$betInfo)){
                            $is_win = "true";
                        }
                    }
                }

                elseif($rTypeName=="两面"){
                    if(in_array($betInfo, $oeouArray)){
                        $is_win = "true";
                    }
                }

                elseif(strpos($rTypeName,"快速-")!==false){
                    if($quick_type=="第一球"){
                        if($betInfo==$dx_wan || $betInfo==$ds_wan || $betInfo==$ballWan){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第二球"){
                        if($betInfo==$dx_qian || $betInfo==$ds_qian || $betInfo==$ballQian){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第三球"){
                        if($betInfo==$dx_hundred || $betInfo==$ds_hundred || $betInfo==$ballHundred){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第四球"){
                        if($betInfo==$dx_ten || $betInfo==$ds_ten || $betInfo==$ballTen){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="第五球"){
                        if($betInfo==$dx_one || $betInfo==$ds_one || $betInfo==$ballOne){
                            $is_win = "true";
                        }
                    }elseif($quick_type=="总和龙虎和"){
                        if(in_array($betInfo,array($hms[1],$hms[2],$hms[3]))){
                            $is_win = "true";
                        }
                        if($hms[3]=='和'){  //投注龙虎,退还本金
                            if($betInfo=='龙' or $betInfo=='虎'){
                                $is_win = "和";
                            }
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
                    }elseif($quick_type=="牛牛"){
                        if($betInfo==$niuniu || $betInfo==$niuds || $betInfo==$niudx){
                            $is_win = "true";
                        }
                    }
                }                

                $userid = $order['user_id'];
                $datereg = $order['order_sub_num'];
                $rsMoney    = User::find($userid);
                $assets =   round($rsMoney['Money'],2);

                if($is_win=="true"){
                    $win_sign = "1";
                    $bet_money_total = $order['win']+$order['fs'];
                    if($is_multi_win>0){
                        $bet_win_total = $order['bet_money']*($order['bet_rate']-1)*$is_multi_win + $order['bet_money'];
                        $bet_money_total = $bet_win_total + $order['fs'];
                    }
                    $bet_type = "彩票手工结算-彩票中奖";
                }elseif($is_win=="和"){
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

                if ($is_multi_win > 0) {
                    OrderLotterySub::where("id", $order["sub_id"])
                            ->update(["win" => $bet_win_total]);
                }

                if($win_sign == "1" ||$win_sign == "2" || ($win_sign == "0" && $order['fs']>0)){

                    $q1 = User::where("id", $userid)
                        ->where("Pay_Type", 1)
                        ->increment('Money', $bet_win_total);

                    //会员金额操作成功

                    if($q1 == 1) {

                        $balance=   $assets + $bet_win_total;

                        $datetime = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                        $new_log = new MoneyLog;
                        $new_log->user_id = $userid;
                        $new_log->order_num = $datereg;
                        $new_log->about = $lottery_name;
                        $new_log->update_time = $datetime;
                        $new_log->type = $bet_type;
                        $new_log->order_value = $bet_win_total;
                        $new_log->assets = $assets;
                        $new_log->balance = $balance;
                        $new_log->save();

                    }
                }
            }

            //最后更新彩票结果表，状态修改
            switch ($g_type) {
                case "cq":
                    $result = LotteryResultCQ::where("qishu", $qishu)
                        ->update(["state" => $stateType]);
                    break;
                case "ffc5":
                    $result = LotteryResultFFC5::where("qishu", $qishu)
                        ->update(["state" => $stateType]);
                    break;
                case "txssc":
                    $result = LotteryResultTXSSC::where("qishu", $qishu)
                        ->update(["state" => $stateType]);
                    break;
                case "twssc":
                    $result = LotteryResultTWSSC::where("qishu", $qishu)
                        ->update(["state" => $stateType]);
                    break;
                case "azxy5":
                    $result = LotteryResultAZXY5::where("qishu", $qishu)
                        ->update(["state" => $stateType]);
                    break;
                case "jx":
                    $result = LotteryResultJX::where("qishu", $qishu)
                        ->update(["state" => $stateType]);
                    break;
                case "tj":
                    $result = LotteryResultTJ::where("qishu", $qishu)
                        ->update(["state" => $stateType]);
                    break;
            }

            $response['message'] = "B5 Lottery Result checkouted successfully!";
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
