<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\LotteryUserConfig;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\User;
use App\Models\Web\Sys800;
use App\Models\Web\WebMemLogData;
use App\Models\Web\MoneyLog;
use App\Utils\Utils;

class AdminLotteryuserconfigController extends Controller
{

    public function getLotteryUserConfig(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $user_name = $request_data["user_name"] ?? "";

            $lottery_user_config = LotteryUserConfig::query();

            if ($user_name !== "") {
                $lottery_user_config = $lottery_user_config->where("username", "like", "%$user_name%");
            }

            $total_count = $lottery_user_config->count();

            $lottery_user_config = $lottery_user_config->offset(($page_no - 1) * $limit)
                ->take($limit)->get(["id", "userid", "username"]);

            foreach($lottery_user_config as $item) {
                $user = User::find($item["userid"]);
                $alias='';
                $money=0;
                $status='';
                if($item['userid']==0) {
                    $alias='默认配置';
                    $money=0;
                    $status='启用';
                }

                if (isset($user)) {

                    if($user['Status']=='0') {
                        $alias=$user["Alias"];
                        $money=$user["Money"];
                        $status="启用";
                    }
                    if($user['Status']=='1') {
                        $alias=$user["Alias"];
                        $money=$user["Money"];
                        $status="<span style='background-color: #FFFF00'>冻结</span>";
                    }
                    if($user['Status']=='2') {
                        $alias=$user["Alias"];
                        $money=$user["Money"];
                        $status="<span style='background-color: #FF0000'>禁用</span>";
                    }

                }

                $item["alias"] = $alias;
                $item["money"] = $money;
                $item["status"] = $status;
            }

            $response["data"] = $lottery_user_config;
            $response["total_count"] = $total_count;
            $response['message'] = "Lottery User Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getLotteryUserConfigItem(Request $request) {

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

            $item = LotteryUserConfig::find($id);

            $response["data"] = $item;
            $response['message'] = "Lottery User Item fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

    public function updateLotteryConfigItem(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [
                "data" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $item = $request_data["data"];

            $item = json_decode($item, true);

            $lottery_user_config = LotteryUserConfig::find($item["id"]);

            $lottery_user_config["cq_lower_bet"] = $item["cq_lower_bet"];
            $lottery_user_config["cq_bet"] = $item["cq_bet"];
            $lottery_user_config["cq_bet_reb"] = $item["cq_bet_reb"];
            $lottery_user_config["jx_lower_bet"] = $item["jx_lower_bet"];
            $lottery_user_config["jx_bet"] = $item["jx_bet"];
            $lottery_user_config["jx_bet_reb"] = $item["jx_bet_reb"];
            $lottery_user_config["tj_bet"] = $item["tj_bet"];
            $lottery_user_config["tj_bet_reb"] = $item["tj_bet_reb"];
            $lottery_user_config["gdsf_lower_bet"] = $item["gdsf_lower_bet"];
            $lottery_user_config["gdsf_bet"] = $item["gdsf_bet"];
            $lottery_user_config["gdsf_bet_reb"] = $item["gdsf_bet_reb"];
            $lottery_user_config["gxsf_lower_bet"] = $item["gxsf_lower_bet"];
            $lottery_user_config["gxsf_bet"] = $item["gxsf_bet"];
            $lottery_user_config["gxsf_bet_reb"] = $item["gxsf_bet_reb"];
            $lottery_user_config["tjsf_lower_bet"] = $item["tjsf_lower_bet"];
            $lottery_user_config["tjsf_bet"] = $item["tjsf_bet"];
            $lottery_user_config["tjsf_bet_reb"] = $item["tjsf_bet_reb"];
            $lottery_user_config["bjpk_lower_bet"] = $item["bjpk_lower_bet"];
            $lottery_user_config["bjpk_bet"] = $item["bjpk_bet"];
            $lottery_user_config["bjpk_bet_reb"] = $item["bjpk_bet_reb"];
            $lottery_user_config["xyft_lower_bet"] = $item["xyft_lower_bet"];
            $lottery_user_config["xyft_bet"] = $item["xyft_bet"];
            $lottery_user_config["xyft_bet_reb"] = $item["xyft_bet_reb"];
            $lottery_user_config["ffc5_lower_bet"] = $item["ffc5_lower_bet"];
            $lottery_user_config["ffc5_bet"] = $item["ffc5_bet"];
            $lottery_user_config["ffc5_bet_reb"] = $item["ffc5_bet_reb"];
            $lottery_user_config["txssc_lower_bet"] = $item["txssc_lower_bet"];
            $lottery_user_config["txssc_bet"] = $item["txssc_bet"];
            $lottery_user_config["txssc_bet_reb"] = $item["txssc_bet_reb"];
            $lottery_user_config["twssc_lower_bet"] = $item["twssc_lower_bet"];
            $lottery_user_config["twssc_bet"] = $item["twssc_bet"];
            $lottery_user_config["twssc_bet_reb"] = $item["twssc_bet_reb"];
            $lottery_user_config["azxy5_lower_bet"] = $item["azxy5_lower_bet"];
            $lottery_user_config["azxy5_bet"] = $item["azxy5_bet"];
            $lottery_user_config["azxy5_bet_reb"] = $item["azxy5_bet_reb"];
            $lottery_user_config["azxy10_lower_bet"] = $item["azxy10_lower_bet"];
            $lottery_user_config["azxy10_bet"] = $item["azxy10_bet"];
            $lottery_user_config["azxy10_bet_reb"] = $item["azxy10_bet_reb"];
            $lottery_user_config["bjkn_lower_bet"] = $item["bjkn_lower_bet"];
            $lottery_user_config["bjkn_bet"] = $item["bjkn_bet"];
            $lottery_user_config["bjkn_bet_reb"] = $item["bjkn_bet_reb"];
            $lottery_user_config["gd11_lower_bet"] = $item["gd11_lower_bet"];
            $lottery_user_config["gd11_bet"] = $item["gd11_bet"];
            $lottery_user_config["gd11_bet_reb"] = $item["gd11_bet_reb"];
            $lottery_user_config["t3_lower_bet"] = $item["t3_lower_bet"];
            $lottery_user_config["t3_bet"] = $item["t3_bet"];
            $lottery_user_config["t3_bet_reb"] = $item["t3_bet_reb"];
            $lottery_user_config["d3_lower_bet"] = $item["d3_lower_bet"];
            $lottery_user_config["d3_bet"] = $item["d3_bet"];
            $lottery_user_config["d3_bet_reb"] = $item["d3_bet_reb"];
            $lottery_user_config["p3_lower_bet"] = $item["p3_lower_bet"];
            $lottery_user_config["p3_bet"] = $item["p3_bet"];
            $lottery_user_config["p3_bet_reb"] = $item["p3_bet_reb"];
            $lottery_user_config["cqsf_lower_bet"] = $item["cqsf_lower_bet"];
            $lottery_user_config["cqsf_bet"] = $item["cqsf_bet"];
            $lottery_user_config["cqsf_bet_reb"] = $item["cqsf_bet_reb"];
            $lottery_user_config["cq_max_bet"] = $item["cq_max_bet"];
            $lottery_user_config["jx_max_bet"] = $item["jx_max_bet"];
            $lottery_user_config["tj_max_bet"] = $item["tj_max_bet"];
            $lottery_user_config["gdsf_max_bet"] = $item["gdsf_max_bet"];
            $lottery_user_config["gxsf_max_bet"] = $item["gxsf_max_bet"];
            $lottery_user_config["tjsf_max_bet"] = $item["tjsf_max_bet"];
            $lottery_user_config["bjpk_max_bet"] = $item["bjpk_max_bet"];
            $lottery_user_config["xyft_max_bet"] = $item["xyft_max_bet"];
            $lottery_user_config["ffc5_max_bet"] = $item["ffc5_max_bet"];
            $lottery_user_config["txssc_max_bet"] = $item["txssc_max_bet"];
            $lottery_user_config["twssc_max_bet"] = $item["twssc_max_bet"];
            $lottery_user_config["azxy5_max_bet"] = $item["azxy5_max_bet"];
            $lottery_user_config["azxy10_max_bet"] = $item["azxy10_max_bet"];
            $lottery_user_config["bjkn_max_bet"] = $item["bjkn_max_bet"];
            $lottery_user_config["gd11_max_bet"] = $item["gd11_max_bet"];
            $lottery_user_config["t3_max_bet"] = $item["t3_max_bet"];
            $lottery_user_config["d3_max_bet"] = $item["d3_max_bet"];
            $lottery_user_config["p3_max_bet"] = $item["p3_max_bet"];
            $lottery_user_config["cqsf_max_bet"] = $item["cqsf_max_bet"];

            // return $lottery_user_config;

            $lottery_user_config->save();

            $response['message'] = "Lottery User Config Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function startDiscount(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [
                "s_time" => "required|string",
                "e_time" => "required|string",
                "g_type" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user = $request->user();

            $s_time = $request_data["s_time"]." 00:00:00";
            $e_time = $request_data["e_time"]." 23:59:59";
            $g_type = $request_data["g_type"];
            $user_group = $request_data["user_group"] ?? "";

            $d3_rebate = $request_data["d3_rebate"] ?? "";
            $p3_rebate = $request_data["p3_rebate"] ?? "";
            $t3_rebate = $request_data["t3_rebate"] ?? "";
            $cq_rebate = $request_data["cq_rebate"] ?? "";
            $tj_rebate = $request_data["tj_rebate"] ?? "";
            $jx_rebate = $request_data["jx_rebate"] ?? "";
            $gdsf_rebate = $request_data["gdsf_rebate"] ?? "";
            $gxsf_rebate = $request_data["gxsf_rebate"] ?? "";
            $tjsf_rebate = $request_data["tjsf_rebate"] ?? "";
            $cqsf_rebate = $request_data["cqsf_rebate"] ?? "";
            $gd11_rebate = $request_data["gd11_rebate"] ?? "";
            $bjpk_rebate = $request_data["bjpk_rebate"] ?? "";
            $bjkn_rebate = $request_data["bjkn_rebate"] ?? "";
            $xyft_rebate = $request_data["xyft_rebate"] ?? "";
            $ffc5_rebate = $request_data["ffc5_rebate"] ?? "";
            $tx_rebate = $request_data["tx_rebate"] ?? "";
            $tw_rebate = $request_data["tw_rebate"] ?? "";
            $azxy5_rebate = $request_data["azxy5_rebate"] ?? "";
            $azxy10_rebate = $request_data["azxy10_rebate"] ?? "";

            if ($user_group != "") {
                $bad_names = str_replace(',', ',', $user_group);
                $bad_names = explode(",", $bad_names);
            }

            $order_lottery = OrderLottery::where("isFs", 0)
                ->where(function($query) {
                    $query->where("status", 1)
                        ->orWhere("status", 2);
                })
                ->whereIn("Gtype", $g_type);

            if ($user_group != "") {
                $order_lottery = $order_lottery->whereNotIn("username", $user_group);
            }
                
            $order_lottery = $order_lottery->whereBetween("bet_time", [$s_time, $e_time])
                ->select(DB::raw("distinct(username)"))
                ->get();

            $result_1 = array();

            foreach($order_lottery as $item) {

                $result = DB::table("order_lottery as o")
                    ->join("order_lottery_sub as o_sub", "o.order_num", "=", "o_sub.order_num")
                    ->whereIn("o.Gtype", $g_type)
                    ->where(function($query) {
                        $query->where("o_sub.status", 1)
                            ->orWhere("o_sub.status", 2);
                    })
                    ->where(function($query) {
                        $query->where("o_sub.is_win", 0)
                            ->orWhere("o_sub.is_win", 1);
                    })
                    ->where(function($query) {
                        $query->where("o_sub.fs", 0)
                            ->orWhere("o_sub.isFS", 0);
                    })
                    ->whereBetween("o.bet_time", [$s_time, $e_time])
                    ->where("o.username", $item["username"])
                    ->select(DB::raw("sum(o_sub.bet_money) as VGOLD, o.Gtype as g_type"))
                    ->first();

                $result = get_object_vars($result);

                $VGOLD = $result["VGOLD"];

                $user = User::where("UserName", $item["username"])->first();
                $UserName = $user["UserName"];
                $fanshui=$user['fanshui_cp'];
                $agents=$user['Agents'];
                $world=$user['World'];
                $corprator=$user['Corprator'];
                $super=$user['Super'];
                $admin=$user['Admin'];
                $Money=$user['Money'];

                switch($result["g_type"]) {
                    case "D3":
                        $fanshui = $d3_rebate == "" ? $fanshui : $d3_rebate;
                        break;
                    case "P3":
                        $fanshui = $p3_rebate == "" ? $fanshui : $p3_rebate;
                        break;
                    case "T3":
                        $fanshui = $t3_rebate == "" ? $fanshui : $t3_rebate;
                        break;
                    case "CQ":
                        $fanshui = $cq_rebate == "" ? $fanshui : $cq_rebate;
                        break;
                    case "TJ":
                        $fanshui = $tj_rebate == "" ? $fanshui : $tj_rebate;
                        break;
                    case "JX":
                        $fanshui = $jx_rebate == "" ? $fanshui : $jx_rebate;
                        break;
                    case "GDSF":
                        $fanshui = $gdsf_rebate == "" ? $fanshui : $gdsf_rebate;
                        break;
                    case "GXSF":
                        $fanshui = $gxsf_rebate == "" ? $fanshui : $gxsf_rebate;
                        break;
                    case "TJSF":
                        $fanshui = $tjsf_rebate == "" ? $fanshui : $tjsf_rebate;
                        break;
                    case "CQSF":
                        $fanshui = $cqsf_rebate == "" ? $fanshui : $cqsf_rebate;
                        break;
                    case "GD11":
                        $fanshui = $gd11_rebate == "" ? $fanshui : $gd11_rebate;
                        break;
                    case "BJPK":
                        $fanshui = $bjpk_rebate == "" ? $fanshui : $bjpk_rebate;
                        break;
                    case "BJKN":
                        $fanshui = $bjkn_rebate == "" ? $fanshui : $bjkn_rebate;
                        break;
                    case "XYFT":
                        $fanshui = $xyft_rebate == "" ? $fanshui : $xyft_rebate;
                        break;
                    case "FFC5":
                        $fanshui = $ffc5_rebate == "" ? $fanshui : $ffc5_rebate;
                        break;
                    case "TXSSC":
                        $fanshui = $tx_rebate == "" ? $fanshui : $tx_rebate;
                        break;
                    case "AZXY5":
                        $fanshui = $azxy5_rebate == "" ? $fanshui : $azxy5_rebate;
                        break;
                    case "AZXY10":
                        $fanshui = $azxy10_rebate == "" ? $fanshui : $azxy10_rebate;
                        break;
                }

                $money_ts=round($VGOLD*(int)$fanshui/100,2);

                DB::table("order_lottery as o")
                    ->join("order_lottery_sub as o_sub", "o.order_num", "=", "o_sub.order_num")
                    ->where("o.username", $item["username"])
                    ->whereIn("o.Gtype", $g_type)
                    ->whereBetween("o.bet_time", [$s_time, $e_time])
                    ->update([
                        "o.isFS" => 1,
                        "o_sub.isFS" => 1,
                    ]);

                if ($money_ts > 0) {

                    array_push($result_1, $UserName."有效投注额:$VGOLD,反水比率：$fanshui,反水金额:$money_ts");

                    $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                    $adddate=date("Y-m-d");
                    $date=date("Y-m-d H:i:s");
                    $previousAmount=$Money;
                    $currentAmount=$previousAmount+$money_ts;
                    $data = array(
                        "Checked" => 1,
                        "Payway" => "W",
                        "Gold" => $money_ts,
                        "previousAmount" => $previousAmount,
                        "currentAmount" => $currentAmount,
                        "AddDate" => $adddate,
                        "Type" => "S",
                        "UserName" => $item["username"],
                        "Agents" => $agents,
                        "World" => $world,
                        "Corprator" => $corprator,
                        "Super" => $super,
                        "Admin" => $admin,
                        "CurType" => "RMB",
                        "Date" => $date,
                        "Name" => $user["UserName"],
                        "User" => $user["UserName"],
                        "loginname" => $user["UserName"],
                        "Bank_Account" => "彩票返水",
                        "Order_Code" => $Order_Code,
                        "Music" => 1,
                    );

                    $sys_800 = new Sys800;

                    $sys_800->create($data);

                    $q1 = User::where('UserName', $item["username"])->increment('Money', $money_ts);

                    if($q1==1) {
                        $datetime = date("Y-m-d H:i:s");
                        $currentAmount = Utils::GetField($item["username"], 'Money');
                        $user_id = Utils::GetField($item["username"], 'id');
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $Order_Code;
                        $new_log->about =  $user["UserName"] . "彩票返水<br>有效金额:$VGOLD<br>返水金额:$money_ts";
                        $new_log->update_time = $datetime;
                        $new_log->type = $user["UserName"]."彩票返水";
                        $new_log->order_value = $money_ts;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        $new_log->save();
                    }
                }
            }

            $data = array(
                "UserName" => $user["UserName"],
                "LoginIP" => Utils::get_ip(),
                "LoginTime" => now(),
                "Context" => '执行彩票一键退水',
                "Url" => Utils::get_browser_ip(),       
            );

            $web_mem_log_data = new WebMemLogData;

            $web_mem_log_data->create($data);

            $response["data"] = $result_1;
            $response['message'] = "Discount Data updated successfully!";
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
