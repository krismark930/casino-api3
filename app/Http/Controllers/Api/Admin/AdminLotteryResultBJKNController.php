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
use App\Models\LotteryResultBJKN;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\Web\MoneyLog;
use Carbon\Carbon;
use App\Utils\Utils;

class AdminLotteryResultBJKNController extends Controller
{
    public function getLotteryResult(Request $request)
    {

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
            $lottery_type = "北京快乐8";
            $result = LotteryResultBJKN::whereDate("datetime", "=", $query_time);
            if ($qishu_query != "") {
                $result = $result->where("qishu", $qishu_query);
            }
            $result = $result->orderBy("qishu", "desc")->get();

            foreach ($result as $item) {
                $hm         = array();
                $hm[]       = $item['ball_1'];
                $hm[]       = $item['ball_2'];
                $hm[]       = $item['ball_3'];
                $hm[]       = $item['ball_4'];
                $hm[]       = $item['ball_5'];
                $hm[]       = $item['ball_6'];
                $hm[]       = $item['ball_7'];
                $hm[]       = $item['ball_8'];
                $hm[]       = $item['ball_9'];
                $hm[]       = $item['ball_10'];
                $hm[]       = $item['ball_11'];
                $hm[]       = $item['ball_12'];
                $hm[]       = $item['ball_13'];
                $hm[]       = $item['ball_14'];
                $hm[]       = $item['ball_15'];
                $hm[]       = $item['ball_16'];
                $hm[]       = $item['ball_17'];
                $hm[]       = $item['ball_18'];
                $hm[]       = $item['ball_19'];
                $hm[]       = $item['ball_20'];
                $item["other_1"] = Utils::Kl8_convert(Utils::Kl8_Auto($hm, 2));
                $item["other_2"] = Utils::Kl8_convert(Utils::Kl8_Auto($hm, 3));
                $item["other_3"] = Utils::Kl8_convert(Utils::Kl8_Auto($hm, 5));
                $item["other_4"] = Utils::Kl8_convert(Utils::Kl8_Auto($hm, 4));
                $item["other_5"] = Utils::Kl8_Auto($hm, 1);
                $item["lottery_type"] = $lottery_type;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result BJKN Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getLotteryResultById(Request $request)
    {

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
            $result = LotteryResultBJKN::find($id);

            $response["data"] = $result;
            $response['message'] = "Lottery Result BJKN Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveLotteryResult(Request $request)
    {

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
            $ball_9 = $request_data["ball_9"];
            $ball_10 = $request_data["ball_10"];
            $ball_11 = $request_data["ball_11"];
            $ball_12 = $request_data["ball_12"];
            $ball_13 = $request_data["ball_13"];
            $ball_14 = $request_data["ball_14"];
            $ball_15 = $request_data["ball_15"];
            $ball_16 = $request_data["ball_16"];
            $ball_17 = $request_data["ball_17"];
            $ball_18 = $request_data["ball_18"];
            $ball_19 = $request_data["ball_19"];
            $ball_20 = $request_data["ball_20"];


            if ($action == "add" && $id == 0) {

                $create_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                $result = LotteryResultBJKN::where("qishu", $qishu)->first();
                if (isset($result)) {
                    $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                    return response()->json($response, $response['status']);
                }
                $item = new LotteryResultBJKN;

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
                $item["ball_9"] = $ball_9;
                $item["ball_10"] = $ball_10;
                $item["ball_11"] = $ball_11;
                $item["ball_12"] = $ball_12;
                $item["ball_13"] = $ball_13;
                $item["ball_14"] = $ball_14;
                $item["ball_15"] = $ball_15;
                $item["ball_16"] = $ball_16;
                $item["ball_17"] = $ball_17;
                $item["ball_18"] = $ball_18;
                $item["ball_19"] = $ball_19;
                $item["ball_20"] = $ball_20;

                $item->save();
            } else if ($action == "edit" && $id > 0) {

                $item = LotteryResultBJKN::find($id);

                $time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                $prev_text = "修改时间：" . ($time) . "。\n修改前内容：" . $item["ball_1"] . "," . $item["ball_2"] . "," . $item["ball_3"] . "," . $item["ball_4"] . "," . $item["ball_5"] . "," . $item["ball_6"] . "," . $item["ball_7"] . "," . $item["ball_8"] . "," . $item["ball_9"] . "," . $item["ball_10"] . "," . $item["ball_11"] . "," . $item["ball_12"] . "," . $item["ball_13"] . "," . $item["ball_14"] . "," . $item["ball_15"] . "," . $item["ball_16"] . "," . $item["ball_17"] . "," . $item["ball_18"] . "," . $item["ball_19"] . "," . $item["ball_20"] . "。\n修改后内容：" . $ball_1 . "," . $ball_2 . "," . $ball_3 . "," . $ball_4 . "," . $ball_5 . "," . $ball_6 . "," . $ball_7 . "," . $ball_8 . "," . $ball_9 . "," . $ball_10 . "," . $ball_11 . "," . $ball_12 . "," . $ball_13 . "," . $ball_14 . "," . $ball_15 . "," . $ball_16 . "," . $ball_17 . "," . $ball_18 . "," . $ball_19 . "," . $ball_20 . "。\n\n" . $item["prev_text"];

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
                $item["ball_9"] = $ball_9;
                $item["ball_10"] = $ball_10;
                $item["ball_11"] = $ball_11;
                $item["ball_12"] = $ball_12;
                $item["ball_13"] = $ball_13;
                $item["ball_14"] = $ball_14;
                $item["ball_15"] = $ball_15;
                $item["ball_16"] = $ball_16;
                $item["ball_17"] = $ball_17;
                $item["ball_18"] = $ball_18;
                $item["ball_19"] = $ball_19;
                $item["ball_20"] = $ball_20;

                $item->save();
            }

            $response['message'] = "BJKN Lottery Result updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function checkoutResult(Request $request)
    {

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

            $g_type = $request_data["g_type"] ?? "BJKN";
            $qishu = $request_data["qishu"];
            $type = $request_data["type"];
            $js_type = $request_data["jsType"];
            $result = LotteryResultBJKN::where("qishu", $qishu)->first();
            $lottery_name = "北京快乐8";

            $hm    = array();
            $hm[]  = $result['ball_1'];
            $hm[]  = $result['ball_2'];
            $hm[]  = $result['ball_3'];
            $hm[]  = $result['ball_4'];
            $hm[]  = $result['ball_5'];
            $hm[]  = $result['ball_6'];
            $hm[]  = $result['ball_7'];
            $hm[]  = $result['ball_8'];
            $hm[]  = $result['ball_9'];
            $hm[]  = $result['ball_10'];
            $hm[]  = $result['ball_11'];
            $hm[]  = $result['ball_12'];
            $hm[]  = $result['ball_13'];
            $hm[]  = $result['ball_14'];
            $hm[]  = $result['ball_15'];
            $hm[]  = $result['ball_16'];
            $hm[]  = $result['ball_17'];
            $hm[]  = $result['ball_18'];
            $hm[]  = $result['ball_19'];
            $hm[]  = $result['ball_20'];
            $stateType = "1";

            //状态为已结算，对所有的订单进行结算，需要从客户那边收回钱然后再进行结算

            if ($js_type == 1) {
                //获取已结算的订单
                $orders = Utils::getOrdersJs($g_type, $qishu);
                //订单不为空，进行退钱操作
                if (count($orders) > 0) {
                    foreach ($orders as $order) {
                        $order = get_object_vars($order);
                        $userid = $order['user_id'];
                        $datereg = $order['order_sub_num'];
                        $resultMoney = User::find($userid);
                        $assets = round($resultMoney['Money'], 2);
                        OrderLottery::where("id", $order["id"])->update(["status" => 0]);
                        OrderLotterySub::where("id", $order["sub_id"])
                            ->update(["status" => 0, "is_win" => null]);
                        if ($order['is_win'] == "1" || $order['is_win'] == "2" || ($order['is_win'] == "0" && $order['fs'] > 0)) {
                            //退钱
                            if ($order['is_win'] == "1") { //中奖金额+反水
                                $bet_money_total = $order['win'] + $order['fs'];
                            } elseif ($order['is_win'] == "2") { //平局的钱，返回的是下注的钱
                                $bet_money_total = $order['bet_money'];
                            } elseif ($order['is_win'] == "0" && $order['fs'] > 0) { //反水的钱
                                $bet_money_total = $order['fs'];
                            }

                            $q1 = User::where("id", $userid)
                                ->where("Pay_Type", 1)
                                ->decrement('Money', $bet_money_total);

                            //会员金额操作成功

                            if ($q1 == 1) {

                                $balance =   $assets - $bet_money_total;

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
            $orders = Utils::getOrdersByStatus($g_type, $qishu, "0");
            if (count($orders) == 0) {
                LotteryResultBJKN::where("qishu", $qishu)
                    ->update(["state" => $stateType]);

                $response['message'] = "BJKN Lottery Result checkouted successfully!";
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;

                return response()->json($response, $response['status']);
            }

            $hms[] = Utils::Kl8_Auto_zh($hm, 1);
            $hms[] = Utils::Kl8_Auto_zh($hm, 2);
            $hms[] = Utils::Kl8_Auto_zh($hm, 3);
            $hms[] = Utils::Kl8_Auto_zh($hm, 4);
            $hms[] = Utils::Kl8_Auto_zh($hm, 5);

            foreach ($orders as $order) {
                $order = get_object_vars($order);
                $betInfo = explode(":", $order["number"]);
                $rTypeName = $order["rtype_str"];
                $quick_type = $order["quick_type"];
                $betContentArray = explode(",", $order["number"]);
                $oddsArray = explode(",", $order["bet_rate"]);

                // if(in_array($betInfo[0],array("TOP","MIDDLE","BOTTOM","ODD","TIE","EVEN"))){//上中下、奇偶和盘
                //     $selectBall = "ONE";
                //     $betContent = $betInfo[0];
                // }elseif($betInfo[0]=="ALL"){//所有球总和、五行过关
                //     $selectBall = "ONE";
                //     $betContent = $betInfo[1].':'.$betInfo[2];
                //     if(in_array($betInfo[2].':'.$betInfo[3],array("UNDER:ODD","UNDER:EVEN","OVER:ODD","OVER:EVEN"))){
                //         $betContent = "SUM:".$betInfo[2].':SUM:'.$betInfo[3];
                //     }
                // }elseif(in_array($quick_type,array("选一","和值","上中下","奇和偶"))){
                //     $selectBall = "quick";
                // }else{//多选
                //     $selectBall = "multi";
                //     $betContentArray = explode(",",$order["number"]);
                //     $oddsArray = explode(",",$order["bet_rate"]);
                // }


                $selectBall = "quick";
                $betContent = "";
                $isWinMulti = false;

                $userid = $order['user_id'];
                $datereg = $order['order_sub_num'];
                $resultMoney = User::find($userid);
                $assets = round($resultMoney['money'], 2);

                if ($selectBall == "ONE") {
                    //各种玩法，算法
                    $szx = Utils::Kl8_Auto($hm, 4);
                    $qho = Utils::Kl8_Auto($hm, 5);
                    $zonghedx = Utils::Kl8_Auto($hm, 2);
                    $zongheds = Utils::Kl8_Auto($hm, 3);
                    $wx = Utils::Kl8_Auto($hm, 7);
                    $gg = $zonghedx . ":" . $zongheds;

                    if (in_array($betContent, array($szx, $qho, $zonghedx, $zongheds, $wx, $gg))) {
                        $win_sign = "1";
                        $bet_money_total = $order['win'] + $order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    } elseif ($zonghedx == "SUM:810" && in_array($betContent, array("SUM:OVER", "SUM:UNDER", "SUM:UNDER:SUM:ODD", "SUM:UNDER:SUM:EVEN", "SUM:OVER:SUM:ODD", "SUM:OVER:SUM:EVEN"))) {
                        $win_sign = "2";
                        $bet_money_total = $order['bet_money'];
                        $bet_type = "彩票手工结算-彩票和局";
                    } else {
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                } elseif ($selectBall == "quick") {
                    $betInfo = $order["number"];
                    $is_win = "false";
                    if ($quick_type == "选一") {
                        if (in_array($betInfo, $hm)) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "和值") {
                        if ($betInfo == $hms[1] || $betInfo == $hms[2]) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "上中下") {
                        if ($betInfo == $hms[3]) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "奇和偶") {
                        if ($betInfo == $hms[4]) {
                            $is_win = "true";
                        }
                    }
                    if ($is_win == "true") {
                        $win_sign = "1";
                        $bet_money_total = $order['win'] + $order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    } else {
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                } elseif ($selectBall == "multi") {
                    $isWinMulti = "false";
                    if (count($betContentArray) == 1) {
                        if (in_array($order["number"], array($result['ball_1'], $result['ball_2'], $result['ball_3'], $result['ball_4'], $result['ball_5'], $result['ball_6'], $result['ball_7'], $result['ball_8'], $result['ball_9'], $result['ball_10'], $result['ball_11'], $result['ball_12'], $result['ball_13'], $result['ball_14'], $result['ball_15'], $result['ball_16'], $result['ball_17'], $result['ball_18'], $result['ball_19'], $result['ball_20']))) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $order["bet_rate"];
                        }
                    } elseif (count($betContentArray) == 2) {
                        $x1rr = $betContentArray;
                        $h21 = 0;
                        $h22 = 0;
                        for ($i = 1; $i < 21; $i++) {
                            if ($x1rr[0] == $result['ball_' . $i . '']) {
                                $h21 = 1;
                            }
                            if ($x1rr[1] == $result['ball_' . $i . '']) {
                                $h22 = 1;
                            }
                        }
                        $h2nus = $h21 + $h22;
                        if ($h2nus == 2) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $order["bet_rate"];
                        }
                    } elseif (count($betContentArray) == 3) {
                        $x1rr = $betContentArray;
                        $h31 = 0;
                        $h32 = 0;
                        $h33 = 0;
                        for ($i = 1; $i < 21; $i++) {
                            if ($x1rr[0] == $result['ball_' . $i . '']) {
                                $h31 = 1;
                            }
                            if ($x1rr[1] == $result['ball_' . $i . '']) {
                                $h32 = 1;
                            }
                            if ($x1rr[2] == $result['ball_' . $i . '']) {
                                $h33 = 1;
                            }
                        }
                        $h2nus = $h31 + $h32 + $h33;
                        if ($h2nus == 3) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[0];
                        } else if ($h2nus == 2) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[1];
                        }
                    } elseif (count($betContentArray) == 4) {
                        $x1rr = $betContentArray;
                        $h41 = 0;
                        $h42 = 0;
                        $h43 = 0;
                        $h44 = 0;
                        for ($i = 1; $i < 21; $i++) {
                            if ($x1rr[0] == $result['ball_' . $i . '']) {
                                $h41 = 1;
                            }
                            if ($x1rr[1] == $result['ball_' . $i . '']) {
                                $h42 = 1;
                            }
                            if ($x1rr[2] == $result['ball_' . $i . '']) {
                                $h43 = 1;
                            }
                            if ($x1rr[3] == $result['ball_' . $i . '']) {
                                $h44 = 1;
                            }
                        }
                        $h2nus = $h41 + $h42 + $h43 + $h44;
                        if ($h2nus == 4) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[0];
                        } else if ($h2nus == 3) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[1];
                        } else if ($h2nus == 2) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[2];
                        }
                    } elseif (count($betContentArray) == 5) {
                        $x1rr = $betContentArray;
                        $h51 = 0;
                        $h52 = 0;
                        $h53 = 0;
                        $h54 = 0;
                        $h55 = 0;
                        for ($i = 1; $i < 21; $i++) {
                            if ($x1rr[0] == $result['ball_' . $i . '']) {
                                $h51 = 1;
                            }
                            if ($x1rr[1] == $result['ball_' . $i . '']) {
                                $h52 = 1;
                            }
                            if ($x1rr[2] == $result['ball_' . $i . '']) {
                                $h53 = 1;
                            }
                            if ($x1rr[3] == $result['ball_' . $i . '']) {
                                $h54 = 1;
                            }
                            if ($x1rr[4] == $result['ball_' . $i . '']) {
                                $h55 = 1;
                            }
                        }
                        $h2nus = $h51 + $h52 + $h53 + $h54 + $h55;

                        if ($h2nus == 5) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[0];
                        } else if ($h2nus == 4) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[1];
                        } else if ($h2nus == 3) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[2];
                        }
                    }

                    if ($isWinMulti == "true") {
                        $win_sign = "1";
                        $bet_money_total = $win_money + $order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    } else {
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                }

                //修改主单

                OrderLottery::where("id", $order["id"])->update(["status" => $stateType]);

                OrderLotterySub::where("id", $order["sub_id"])
                    ->update(["status" => $stateType, "is_win" => $win_sign]);

                if ($isWinMulti) {
                    OrderLotterySub::where("id", $order["sub_id"])
                        ->update(["win" => $win_money]);
                }

                if ($win_sign == "1" || $win_sign == "2" || ($win_sign == "0" && $order['fs'] > 0)) {

                    $q1 = User::where("id", $userid)
                        ->where("Pay_Type", 1)
                        ->increment('Money', $bet_money_total);

                    //会员金额操作成功

                    if ($q1 == 1) {

                        $balance =   $assets + $bet_money_total;

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

            LotteryResultBJKN::where("qishu", $qishu)
                ->update(["state" => $stateType]);

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
