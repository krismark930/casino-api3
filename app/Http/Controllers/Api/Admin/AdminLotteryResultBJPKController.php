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
use App\Models\LotteryResultBJPK;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\Web\MoneyLog;
use Carbon\Carbon;
use App\Utils\Utils;

class AdminLotteryResultBJPKController extends Controller
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
            $lottery_type = "北京PK拾";
            $result = LotteryResultBJPK::whereDate("datetime", "=", $query_time);
            if ($qishu_query != "") {
                $result = $result->where("qishu", $qishu_query);
            }
            $result = $result->orderBy("qishu", "desc")->get();

            foreach ($result as $item) {
                $item["lottery_type"] = $lottery_type;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result BJPK Data fetched successfully!";
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
            $result = LotteryResultBJPK::find($id);

            $response["data"] = $result;
            $response['message'] = "Lottery Result BJPK Data fetched successfully!";
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


            if ($action == "add" && $id == 0) {

                $create_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                $result = LotteryResultBJPK::where("qishu", $qishu)->first();

                if (isset($result)) {
                    $response["message"] = "该期彩票结果已存在，请查询后编辑。";
                    return response()->json($response, $response['status']);
                }

                $item = new LotteryResultBJPK;

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

                $item->save();
            } else if ($action == "edit" && $id > 0) {

                $item = LotteryResultBJPK::find($id);

                $time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

                $prev_text = "修改时间：" . ($time) . "。\n修改前内容：" . $item["ball_1"] . "," . $item["ball_2"] . "," . $item["ball_3"] . "," . $item["ball_4"] . "," . $item["ball_5"] . "," . $item["ball_6"] . "," . $item["ball_7"] . "," . $item["ball_8"] . "," . $item["ball_9"] . "," . $item["ball_10"] . "。\n修改后内容：" . $ball_1 . "," . $ball_2 . "," . $ball_3 . "," . $ball_4 . "," . $ball_5 . "," . $ball_6 . "," . $ball_7 . "," . $ball_8 . "," . $ball_9 . "," . $ball_10 . "。\n\n" . $item["prev_text"];

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

                $item->save();
            }

            $response['message'] = "BJPK Lottery Result updated successfully!";
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

            $g_type = $request_data["g_type"] ?? "BJPK";
            $qishu = $request_data["qishu"];
            $js_type = $request_data["jsType"];

            $result = LotteryResultBJPK::where("qishu", $qishu)->first();
            $lottery_name = "北京PK拾";

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
                        $rsMoney = User::find($userid);
                        $assets = round($rsMoney['Money'], 2);
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
                LotteryResultBJPK::where("qishu", $qishu)
                    ->update(["state" => $stateType]);

                $response['message'] = "BJPK Lottery Result checkouted successfully!";
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;

                return response()->json($response, $response['status']);
            }

            $hms[] = Utils::Pk10_Auto_quick($hm, 1);
            $hms[] = Utils::Pk10_Auto_quick($hm, 2);
            $hms[] = Utils::Pk10_Auto_quick($hm, 3);
            $hms[] = Utils::Pk10_Auto_quick($hm, 4);
            $hms[] = Utils::Pk10_Auto_quick($hm, 5);
            $hms[] = Utils::Pk10_Auto_quick($hm, 6);
            $hms[] = Utils::Pk10_Auto_quick($hm, 7);
            $hms[] = Utils::Pk10_Auto_quick($hm, 8);
            $ds_1 = Utils::Ssc_Ds($result['ball_1']);
            $dx_1 = Utils::pk10_Dx($result['ball_1']);
            $lh_1 = Utils::Pk10_long_hu($result['ball_1'], $result['ball_10']);
            $ds_2 = Utils::Ssc_Ds($result['ball_2']);
            $dx_2 = Utils::pk10_Dx($result['ball_2']);
            $lh_2 = Utils::Pk10_long_hu($result['ball_2'], $result['ball_9']);
            $ds_3 = Utils::Ssc_Ds($result['ball_3']);
            $dx_3 = Utils::pk10_Dx($result['ball_3']);
            $lh_3 = Utils::Pk10_long_hu($result['ball_3'], $result['ball_8']);
            $ds_4 = Utils::Ssc_Ds($result['ball_4']);
            $dx_4 = Utils::pk10_Dx($result['ball_4']);
            $lh_4 = Utils::Pk10_long_hu($result['ball_4'], $result['ball_7']);
            $ds_5 = Utils::Ssc_Ds($result['ball_5']);
            $dx_5 = Utils::pk10_Dx($result['ball_5']);
            $lh_5 = Utils::Pk10_long_hu($result['ball_5'], $result['ball_6']);
            $ds_6 = Utils::Ssc_Ds($result['ball_6']);
            $dx_6 = Utils::pk10_Dx($result['ball_6']);
            $ds_7 = Utils::Ssc_Ds($result['ball_7']);
            $dx_7 = Utils::pk10_Dx($result['ball_7']);
            $ds_8 = Utils::Ssc_Ds($result['ball_8']);
            $dx_8 = Utils::pk10_Dx($result['ball_8']);
            $ds_9 = Utils::Ssc_Ds($result['ball_9']);
            $dx_9 = Utils::pk10_Dx($result['ball_9']);
            $ds_10 = Utils::Ssc_Ds($result['ball_10']);
            $dx_10 = Utils::pk10_Dx($result['ball_10']);
            foreach ($orders as $order) {
                $order = get_object_vars($order);
                $betInfo = explode(":", $order["number"]);
                $rTypeName = $order["rtype_str"];
                $quick_type = $order["quick_type"];

                // if($betInfo[1]=="LOCATE"){//每球定位
                //     $selectBall = $betInfo[2];
                //     $betContent = $betInfo[0];
                // }elseif($betInfo[2]=="DRAGON" || $betInfo[2]=="TIGER"){//龙虎
                //     $selectBall = $betInfo[0];
                //     $betContent = $betInfo[0].":".$betInfo[1].":".$betInfo[2];
                // }elseif($betInfo[0].":".$betInfo[1].":".$betInfo[2]=="SUM:FIRST:2"){//冠亚军和
                //     $selectBall = "冠亚军和";
                //     if(count($betInfo)==4){
                //         $betContent = $betInfo[3];
                //     }else{
                //         $zhArray        = array();
                //         if($betInfo[4]=="11"){
                //             $zhArray[] = $betInfo[4];
                //         }else{
                //             $zhArray[] = $betInfo[4];
                //             $zhArray[] = $betInfo[5];
                //             $zhArray[] = $betInfo[6];
                //             $zhArray[] = $betInfo[7];
                //         }
                //     }
                // }elseif($rTypeName=="快速-北京PK拾"){
                //     $selectBall = "quick";
                // }else{//每球 其他，如龙虎、大小、单双
                //     $selectBall = $betInfo[0];
                //     $betContent = $betInfo[1];
                // }


                $selectBall = "quick";
                $betContent = "";
                $isWinMulti = false;

                $userid = $order['user_id'];
                $datereg = $order['order_sub_num'];
                $rsMoney = User::find($userid);
                $assets =  round($rsMoney['money'], 2);

                if (in_array($selectBall, array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10"))) {
                    //各种玩法，算法
                    $ds = Utils::convertToEn(Utils::Pk10_Auto($hm, 10, $result['ball_' . $selectBall]));
                    $dx = Utils::convertToEn(Utils::Pk10_Auto($hm, 9, $result['ball_' . $selectBall]));
                    if (in_array($selectBall, array("1", "2", "3", "4", "5"))) {
                        $lh = Utils::convertToEnPK10(Utils::Pk10_Auto($hm, $selectBall + 3, 0), $selectBall);
                    }

                    if (in_array($betContent, array($result['ball_' . $selectBall], $ds, $dx, $lh))) {
                        $win_sign = "1";
                        $bet_money_total = $order['win'] + $order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    } else {
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                } elseif ($selectBall == "冠亚军和") {
                    //总和大小
                    $zhdx   = Utils::convertToEnPK10(Utils::Pk10_Auto($hm, 2, 0), null); //总和单双
                    $zhds   = Utils::convertToEnPK10(Utils::Pk10_Auto($hm, 3, 0), null);
                    $zh     = Utils::Pk10_Auto($hm, 1, 0);

                    if (in_array($betContent, array($zhdx, $zhds)) || in_array($zh, $betInfo)) {
                        $win_sign = "1";
                        $bet_money_total = $order['win'] + $order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    } elseif (in_array($betContent, array("OVER", "UNDER", "ODD", "EVEN")) && $zhdx == 'SUM:TIE') {
                        $win_sign = "2";
                        $bet_money_total = $order['bet_money'];
                        $bet_type = "彩票手工结算-彩票和局";
                    } else {
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                } elseif ($selectBall == "quick") { //快速彩票
                    $betInfo = $order["number"];
                    $is_win = "false";
                    if ($quick_type == "冠军") {
                        if ($betInfo == $result['ball_1'] || $betInfo == $ds_1 || $betInfo == $dx_1 || $betInfo == $lh_1) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "亚军") {
                        if ($betInfo == $result['ball_2'] || $betInfo == $ds_2 || $betInfo == $dx_2 || $betInfo == $lh_2) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "第三名") {
                        if ($betInfo == $result['ball_3'] || $betInfo == $ds_3 || $betInfo == $dx_3 || $betInfo == $lh_3) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "第四名") {
                        if ($betInfo == $result['ball_4'] || $betInfo == $ds_4 || $betInfo == $dx_4 || $betInfo == $lh_4) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "第五名") {
                        if ($betInfo == $result['ball_5'] || $betInfo == $ds_5 || $betInfo == $dx_5 || $betInfo == $lh_5) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "第六名") {
                        if ($betInfo == $result['ball_6'] || $betInfo == $ds_6 || $betInfo == $dx_6) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "第七名") {
                        if ($betInfo == $result['ball_7'] || $betInfo == $ds_7 || $betInfo == $dx_7) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "第八名") {
                        if ($betInfo == $result['ball_8'] || $betInfo == $ds_8 || $betInfo == $dx_8) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "第九名") {
                        if ($betInfo == $result['ball_9'] || $betInfo == $ds_9 || $betInfo == $dx_9) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "第十名") {
                        if ($betInfo == $result['ball_10'] || $betInfo == $ds_10 || $betInfo == $dx_10) {
                            $is_win = "true";
                        }
                    } elseif ($quick_type == "冠亚军和") {
                        if ($betInfo == $hms[0] || $betInfo == $hms[1] || $betInfo == $hms[2]) {
                            $is_win = "true";
                        }
                        if (11 == ($hm[0] + $hm[1]) && in_array($betInfo, array("大", "小", "双", "单"))) {
                            $is_win = "tie";
                        }
                    }
                    if ($is_win == "true") {
                        $win_sign = "1";
                        $bet_money_total = $order['win'] + $order['fs'];
                        $bet_type = "彩票手工结算-彩票中奖";
                    } elseif ($is_win == "tie") {
                        $win_sign = "2";
                        $bet_money_total = $order['bet_money'];
                        $bet_type = "彩票手工结算-彩票和局";
                    } else {
                        $win_sign = "0";
                        $bet_money_total = $order['fs'];
                        $bet_type = "彩票手工结算-彩票反水";
                    }
                } else { //选号
                    $selectBall = "选号";
                    $betContentArray = explode(",", $order["number"]);
                    $oddsArray = explode(",", $order["bet_rate"]);
                    $isWinMulti = "false";

                    if (count($betContentArray) == 2) {
                        if ($betContentArray[0] == $result['ball_1'] && $betContentArray[1] == $result['ball_2']) {
                            $h2nus = 2;
                        } elseif ($betContentArray[0] == $result['ball_1'] || $betContentArray[1] == $result['ball_2']) {
                            $h2nus = 1;
                        } else {
                            $h2nus = 0;
                        }
                        if ($h2nus == 2) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[0];
                        } elseif ($h2nus == 1) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[1];
                        }
                    } elseif (count($betContentArray) == 3) {
                        if ($betContentArray[0] == $result['ball_1'] && $betContentArray[1] == $result['ball_2'] && $betContentArray[2] == $result['ball_3']) {
                            $h2nus = 3;
                        } elseif (($betContentArray[0] == $result['ball_1'] && $betContentArray[1] == $result['ball_2'])
                            || ($betContentArray[1] == $result['ball_2'] && $betContentArray[2] == $result['ball_3'])
                            || ($betContentArray[0] == $result['ball_1'] && $betContentArray[2] == $result['ball_3'])
                        ) {
                            $h2nus = 2;
                        } else {
                            $h2nus = 0;
                        }

                        if ($h2nus == 3) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[0];
                        } elseif ($h2nus == 2) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[1];
                        }
                    } elseif (count($betContentArray) == 4) {
                        if ($betContentArray[0] == $result['ball_1'] && $betContentArray[1] == $result['ball_2'] && $betContentArray[2] == $result['ball_3'] && $betContentArray[3] == $result['ball_4']) {
                            $h2nus = 4;
                        } elseif (($betContentArray[0] == $result['ball_1'] && $betContentArray[1] == $result['ball_2'] && $betContentArray[2] == $result['ball_3'])
                            || ($betContentArray[0] == $result['ball_1'] && $betContentArray[1] == $result['ball_2'] && $betContentArray[3] == $result['ball_4'])
                            || ($betContentArray[0] == $result['ball_1'] && $betContentArray[2] == $result['ball_3'] && $betContentArray[3] == $result['ball_4'])
                            || ($betContentArray[1] == $result['ball_2'] && $betContentArray[2] == $result['ball_3'] && $betContentArray[3] == $result['ball_4'])
                        ) {
                            $h2nus = 3;
                        } elseif (($betContentArray[0] == $result['ball_1'] && $betContentArray[1] == $result['ball_2'])
                            || ($betContentArray[0] == $result['ball_1'] && $betContentArray[2] == $result['ball_3'])
                            || ($betContentArray[0] == $result['ball_1'] && $betContentArray[3] == $result['ball_4'])
                            || ($betContentArray[1] == $result['ball_2'] && $betContentArray[2] == $result['ball_3'])
                            || ($betContentArray[1] == $result['ball_2'] && $betContentArray[3] == $result['ball_4'])
                            || ($betContentArray[2] == $result['ball_3'] && $betContentArray[3] == $result['ball_4'])
                        ) {
                            $h2nus = 2;
                        } else {
                            $h2nus = 0;
                        }

                        if ($h2nus == 4) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[0];
                        } elseif ($h2nus == 3) {
                            $isWinMulti = "true";
                            $win_money = $order["bet_money"] * $oddsArray[1];
                        } elseif ($h2nus == 2) {
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
                        ->increment('Money', $win_money);

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

            LotteryResultBJPK::where("qishu", $qishu)
                ->update(["state" => $stateType]);

            $response['message'] = "BJPK Lottery Result checkouted successfully!";
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
