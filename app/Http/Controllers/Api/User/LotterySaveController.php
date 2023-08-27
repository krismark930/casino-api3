<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use App\Models\LotteryUserConfig;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\User;
use App\Models\Web\MoneyLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LotterySaveController extends Controller
{
    public function saveB5(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "g_type" => "required|string",
                "qishu" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $current_date = Carbon::now('Asia/Hong_Kong')->format('YmdHis');

            $g_type = strtoupper($request_data["g_type"]);
            $qishu = $request_data["qishu"];
            $total_bet_amount = $request_data["selectedBetAmount"];
            $total_win_amount = $request_data["winAmount"];
            $selected_list = $request_data["selectedItemList"];
            // $selected_list = json_decode($selected_list, true);
            $user = $request->user();
            $assets = $user["Money"];
            $balance = (int) $user["Money"] - $total_bet_amount;

            $order_lottery = new OrderLottery;
            $order_lottery->user_id = $user["id"];
            $order_lottery->username = $user["UserName"];
            $order_lottery->Gtype = $g_type;

            $lottery_type = "";

            switch ($g_type) {
                case "CQ":
                    $lottery_type = "重庆时时彩";
                    $order_lottery->rtype = "701";
                    break;
                case "FFC5":
                    $lottery_type = "五分彩";
                    $order_lottery->rtype = "701";
                    break;
                case "TXSSC":
                    $lottery_type = "腾讯时时彩";
                    $order_lottery->rtype = "701";
                    break;
                case "TWSSC":
                    $lottery_type = "台湾时时彩";
                    $order_lottery->rtype = "701";
                    break;
                case "AZXY5":
                    $lottery_type = "澳洲幸运5";
                    $order_lottery->rtype = "701";
                    break;
                case "JX":
                    $lottery_type = "新疆时时彩";
                    $order_lottery->rtype = "711";
                    break;
                case "TJ":
                    $lottery_type = "天津时时彩";
                    $order_lottery->rtype = "721";
                    break;
            }

            $order_lottery->rtype_str = "快速-" . $lottery_type;
            $order_lottery->bet_info = "bet_info";
            $order_lottery->bet_money = $total_bet_amount;
            $order_lottery->win = $total_win_amount;
            $order_lottery->lottery_number = $qishu;
            $order_lottery->bet_time = $current_time;

            $order_lottery->save();

            $order_lottery->order_num = $current_date . $order_lottery["id"];

            if ($order_lottery->save()) {

                $q1 = User::where("id", $user["id"])
                    ->decrement('Money', $total_bet_amount);
                $q1 = User::where("id", $user["id"])
                    ->decrement('withdrawal_condition', $total_bet_amount);

                //会员金额操作成功

                if ($q1 == 1) {

                    $new_log = new MoneyLog;
                    $new_log->user_id = $user["id"];
                    $new_log->order_num = $order_lottery->order_num;
                    $new_log->about = $lottery_type;
                    $new_log->update_time = $current_time;
                    $new_log->type = "彩票下注";
                    $new_log->order_value = $total_bet_amount;
                    $new_log->assets = $assets;
                    $new_log->balance = $balance;
                    $new_log->save();

                }

            }

            foreach ($selected_list as $item) {
                $lottery_user_config = LotteryUserConfig::where("userid", $user["id"])->first();
                $order_lottery_sub = new OrderLotterySub;
                $order_lottery_sub->username = $user["UserName"];
                $order_lottery_sub->order_num = $order_lottery->order_num;
                $order_lottery_sub->quick_type = $item["quick_type"];
                $order_lottery_sub->number = $item["number"];
                $order_lottery_sub->bet_rate = $item["odds"];
                $order_lottery_sub->bet_money = $item["betAmount"];
                $order_lottery_sub->win = $item["winableAmount"];
                switch ($g_type) {
                    case "CQ":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["cq_bet_reb"];
                        break;
                    case "FFC5":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["ffc5_bet_reb"];
                        break;
                    case "TXSSC":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["txssc_bet_reb"];
                        break;
                    case "TWSSC":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["twssc_bet_reb"];
                        break;
                    case "AZXY5":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["azxy5_bet_reb"];
                        break;
                    case "JX":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["jx_bet_reb"];
                        break;
                    case "TJ":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["tj_bet_reb"];
                        break;
                }
                $order_lottery_sub->balance = $balance;
                $order_lottery_sub->save();
                $order_lottery_sub->order_sub_num = $current_date . $order_lottery_sub->id;
                $order_lottery_sub->save();
            }

            $response['message'] = "B5 Order Data saved successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveB3(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "g_type" => "required|string",
                "qishu" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $current_date = Carbon::now('Asia/Hong_Kong')->format('YmdHis');

            $g_type = strtoupper($request_data["g_type"]);
            $qishu = $request_data["qishu"];
            $total_bet_amount = $request_data["selectedBetAmount"];
            $total_win_amount = $request_data["winAmount"];
            $selected_list = $request_data["selectedItemList"];
            // $selected_list = json_decode($selected_list, true);
            $user = $request->user();
            $assets = $user["Money"];
            $balance = (int) $user["Money"] - $total_bet_amount;

            $order_lottery = new OrderLottery;
            $order_lottery->user_id = $user["id"];
            $order_lottery->username = $user["UserName"];
            $order_lottery->Gtype = $g_type;

            $lottery_type = "";

            switch ($g_type) {
                case "D3":
                    $lottery_type = "3D彩";
                    $order_lottery->rtype = "745";
                    break;
                case "P3":
                    $lottery_type = "排列三";
                    $order_lottery->rtype = "748";
                    break;
                case "T3":
                    $lottery_type = "上海时时乐";
                    $order_lottery->rtype = "741";
                    break;
            }

            $order_lottery->rtype_str = "快速-" . $lottery_type;
            $order_lottery->bet_info = "bet_info";
            $order_lottery->bet_money = $total_bet_amount;
            $order_lottery->win = $total_win_amount;
            $order_lottery->lottery_number = $qishu;
            $order_lottery->bet_time = $current_time;

            $order_lottery->save();

            $order_lottery->order_num = $current_date . $order_lottery["id"];

            if ($order_lottery->save()) {

                $q1 = User::where("id", $user["id"])
                    ->decrement('Money', $total_bet_amount);

                $q1 = User::where("id", $user["id"])
                    ->decrement('withdrawal_condition', $total_bet_amount);

                //会员金额操作成功

                if ($q1 == 1) {

                    $new_log = new MoneyLog;
                    $new_log->user_id = $user["id"];
                    $new_log->order_num = $order_lottery->order_num;
                    $new_log->about = $lottery_type;
                    $new_log->update_time = $current_time;
                    $new_log->type = "彩票下注";
                    $new_log->order_value = $total_bet_amount;
                    $new_log->assets = $assets;
                    $new_log->balance = $balance;
                    $new_log->save();

                }

            }

            foreach ($selected_list as $item) {
                $lottery_user_config = LotteryUserConfig::where("userid", $user["id"])->first();
                $order_lottery_sub = new OrderLotterySub;
                $order_lottery_sub->username = $user["UserName"];
                $order_lottery_sub->order_num = $order_lottery->order_num;
                $order_lottery_sub->quick_type = $item["quick_type"];
                $order_lottery_sub->number = $item["number"];
                $order_lottery_sub->bet_rate = $item["odds"];
                $order_lottery_sub->bet_money = $item["betAmount"];
                $order_lottery_sub->win = $item["winableAmount"];
                switch ($g_type) {
                    case "D3":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["d3_bet_reb"];
                        break;
                    case "P3":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["p3_bet_reb"];
                        break;
                    case "T3":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["t3_bet_reb"];
                        break;
                }
                $order_lottery_sub->balance = $balance;
                $order_lottery_sub->save();
                $order_lottery_sub->order_sub_num = $current_date . $order_lottery_sub->id;
                $order_lottery_sub->save();
            }

            $response['message'] = "B3 Order Data saved successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveOther(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "g_type" => "required|string",
                "qishu" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $current_date = Carbon::now('Asia/Hong_Kong')->format('YmdHis');

            $g_type = strtoupper($request_data["g_type"]);
            $qishu = $request_data["qishu"];
            $total_bet_amount = $request_data["selectedBetAmount"];
            $total_win_amount = $request_data["winAmount"];
            $selected_list = $request_data["selectedItemList"];
            // $selected_list = json_decode($selected_list, true);
            $user = $request->user();
            $assets = $user["Money"];
            $balance = (int) $user["Money"] - $total_bet_amount;

            $order_lottery = new OrderLottery;
            $order_lottery->user_id = $user["id"];
            $order_lottery->username = $user["UserName"];
            $order_lottery->Gtype = $g_type;

            $lottery_type = "";

            switch ($g_type) {
                case "GD11":
                    $lottery_type = "广东11选5";
                    $order_lottery->rtype = "731";
                    break;
                case "AZXY10":
                    $lottery_type = "澳洲幸运10";
                    $order_lottery->rtype = "761";
                    break;
                case "CQSF":
                    $lottery_type = "重庆快乐十分";
                    $order_lottery->rtype = "791";
                    break;
                case "GDSF":
                    $lottery_type = "广东快乐十分";
                    $order_lottery->rtype = "771";
                    break;
                case "GXSF":
                    $lottery_type = "广西十分彩";
                    $order_lottery->rtype = "777";
                    break;
                case "TJSF":
                    $lottery_type = "天津快乐十分";
                    $order_lottery->rtype = "781";
                    break;
                case "BJPK":
                    $lottery_type = "北京PK拾";
                    $order_lottery->rtype = "761";
                    break;
                case "XYFT":
                    $lottery_type = "幸运飞艇";
                    $order_lottery->rtype = "761";
                    break;
            }

            $order_lottery->rtype_str = "快速-" . $lottery_type;
            $order_lottery->bet_info = "bet_info";
            $order_lottery->bet_money = $total_bet_amount;
            $order_lottery->win = $total_win_amount;
            $order_lottery->lottery_number = $qishu;
            $order_lottery->bet_time = $current_time;

            $order_lottery->save();

            $order_lottery->order_num = $current_date . $order_lottery["id"];

            if ($order_lottery->save()) {

                $q1 = User::where("id", $user["id"])
                    ->decrement('Money', $total_bet_amount);

                $q1 = User::where("id", $user["id"])
                    ->decrement('withdrawal_condition', $total_bet_amount);

                //会员金额操作成功

                if ($q1 == 1) {

                    
                    $new_log = new MoneyLog;
                    $new_log->user_id = $user["id"];
                    $new_log->order_num = $order_lottery->order_num;
                    $new_log->about = $lottery_type;
                    $new_log->update_time = $current_time;
                    $new_log->type = "彩票下注";
                    $new_log->order_value = $total_bet_amount;
                    $new_log->assets = $assets;
                    $new_log->balance = $balance;
                    $new_log->save();

                }

            }

            foreach ($selected_list as $item) {
                $lottery_user_config = LotteryUserConfig::where("userid", $user["id"])->first();
                $order_lottery_sub = new OrderLotterySub;
                $order_lottery_sub->username = $user["UserName"];
                $order_lottery_sub->order_num = $order_lottery->order_num;
                $order_lottery_sub->quick_type = $item["quick_type"];
                $order_lottery_sub->number = $item["number"];
                $order_lottery_sub->bet_rate = $item["odds"];
                $order_lottery_sub->bet_money = $item["betAmount"];
                $order_lottery_sub->win = $item["winableAmount"];
                switch ($g_type) {
                    case "GD11":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["gd11_bet_reb"];
                        break;
                    case "AZXY10":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["azxy10_bet_reb"];
                        break;
                    case "CQSF":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["cqsf_bet_reb"];
                        break;
                    case "GDSF":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["gdsf_bet_reb"];
                        break;
                    case "GXSF":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["gxsf_bet_reb"];
                        break;
                    case "TJSF":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["tjsf_bet_reb"];
                        break;
                    case "BJPK":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["bjpk_bet_reb"];
                        break;
                    case "XYFT":
                        $order_lottery_sub->fs = (int) $item["betAmount"] * $lottery_user_config["xyft_bet_reb"];
                        break;
                }
                $order_lottery_sub->balance = $balance;
                $order_lottery_sub->save();
                $order_lottery_sub->order_sub_num = $current_date . $order_lottery_sub->id;
                $order_lottery_sub->save();
            }

            $response['message'] = "Other Order Data saved successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
}
