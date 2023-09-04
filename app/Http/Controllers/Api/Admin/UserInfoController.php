<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\GUser;
use App\Models\Gzhudan;
use App\Models\KaTan;
use App\Models\MacaoKatan;
use App\Models\OrderLottery;
use App\Models\OrderLotterySub;
use App\Models\User;
use App\Models\WebMemberLogs;
use App\Models\WebReportData;
use App\Models\WebReportZr;
use App\Models\Web\MoneyLog;
use App\Models\Web\Report;
use App\Models\Web\Sys800;
use App\Models\WebReportKy;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserInfoController extends Controller
{
    public function getUserInfo(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'user_name' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $username = $request_data["user_name"];

            $user = User::where("UserName", $username)->first();

            $user_id = $user['id'];
            $money = $user['Money'];
            $alias = $user['Alias'];
            $wucha = $user['wucha'];
            $AddDate = $user['AddDate'];
            $Agents = $user['Agents'];
            $LoginIP = $user['LoginIP'];
            $Url = $user['Url'];
            $OnlineTime = $user['OnlineTime'];
            $LoginTime = $user['LoginTime'];
            $AG_User = $user['AG_User'];
            $BBIN_User = $user['BBIN_User'];
            $OG_User = $user['OG_User'];
            $MG_User = $user['MG_User'];
            $PT_User = $user['PT_User'];
            $KY_User = $user['KY_User'];
            $AG_Money = round(WebReportZr::where("UserName", $username)->where(function ($query) {
                $query->where("platformType", "AGIN")
                    ->orWhere("platformType", "XIN")
                    ->orWhere("platformType", "YOPLAY");
            })->select(DB::raw("SUM(validBetAmount) as amount"))->get()[0]["amount"], 2) ?? 0;

            $BBIN_Money = round(WebReportZr::where("UserName", $username)->where("platformType", "BBIN")->select(DB::raw("SUM(validBetAmount) as amount"))->get()[0]["amount"], 2) ?? 0;
            $OG_Money = round(WebReportZr::where("UserName", $username)->where("platformType", "OG")->select(DB::raw("SUM(validBetAmount) as amount"))->get()[0]["amount"], 2) ?? 0;
            $MG_Money = round(WebReportZr::where("UserName", $username)->where("platformType", "MG")->select(DB::raw("SUM(validBetAmount) as amount"))->get()[0]["amount"],2) ?? 0;
            $PT_Money = round(WebReportZr::where("UserName", $username)->where("platformType", "PT")->select(DB::raw("SUM(validBetAmount) as amount"))->get()[0]["amount"], 2) ?? 0;
            $KY_Money = round(WebReportKy::where("Accounts", $KY_User)->select(DB::raw("SUM(AllBet) as amount"))->get()[0]["amount"], 2) ?? 0;
            $yxzs2 = 0;
            $yxzs_ssc = 0;
            $yxzs_lottery = 0;

            $sport_bet_money = Report::where("M_Name", $username)->select(DB::raw("SUM(BetScore) as amount"))->get()[0]["amount"] ?? 0;

            $other_lottery_bet_money = OrderLotterySub::where("username", $username)->select(DB::raw("SUM(bet_money) as amount"))->get()[0]["amount"] ?? 0;

            $hongKong_six_mark_bet_money = KaTan::where("username", $username)->select(DB::raw("SUM(sum_m) as amount"))->get()[0]["amount"] ?? 0;

            $macao_six_mark_bet_money = MacaoKatan::where("username", $username)->select(DB::raw("SUM(sum_m) as amount"))->get()[0]["amount"] ?? 0;

            $web_sys800_data = Sys800::where('username', $username)
                ->where("Type", "S")
                ->where('Bank_Account', 'like', '%反水%')
                ->where("Type2", 3)
                ->orderBy("id", "desc")
                ->first();

            if (isset($web_sys800_data)) {
                $CK_Date = $web_sys800_data["AddDate"];
                $datetime = $web_sys800_data["Date"];
                $v_gold = WebReportData::where("M_Name", $username)
                    ->where("BetTime", $datetime)
                    ->where("Cancel", 0)
                    ->sum("VGOLD");
                $yxzs2 = $v_gold;
                $newtime = date("Y-m-d H:i:s");
                $g_jiner = Gzhudan::where("g_date", ">", $newtime)
                    ->where("g_nid", $username)
                    ->whereNotNull("g_win")
                    ->where("g_win", "!=", 0)
                    ->sum("g_jiner");
                $yxzs_ssc = $g_jiner;
                $bet_money = OrderLottery::leftjoin("order_lottery_sub as o_sub", "order_lottery.order_num", "=", "o_sub.order_num")
                    ->where("o_sub.username", $username)
                    ->where("order_lottery.bet_time", ">", $newtime)
                    ->where(function ($query) {
                        $query->where('o_sub.status', 1)
                            ->orWhere('o_sub.status', 2);
                    })
                    ->sum("o_sub.bet_money");
                $yxzs_lottery = $bet_money;
            }

            $sscye = 0;
            $sscye = GUser::where("g_name", $username)->sum("g_money_yes");

            $ckzs2 = 0;
            $ckzs2 = Sys800::where("Type", "S")
                ->where("Type2", 3)
                ->where("Checked", 1)
                ->where("Cancel", 0)
                ->where("Username", $username)
                ->sum("gold");

            $ckzs = Sys800::where("Type", "S")
                ->where("Checked", 1)
                ->where("Cancel", 0)
                ->where("Username", $username)
                ->sum("gold");

            $cjzs = Sys800::where("Type", "S")
                ->where("Bank_Account", 'like', "%彩金%")
                ->where("Checked", 1)
                ->where("Cancel", 0)
                ->where("Username", $username)
                ->sum("gold");

            $hszs = Sys800::where("Type", "S")
                ->where(function ($query) {
                    $query->where("Bank_Account", 'like', "%返水%")
                        ->orwhere("Bank_Account", 'like', "%反水%");
                })
                ->where("Checked", 1)
                ->where("Cancel", 0)
                ->where("Username", $username)
                ->sum("gold");

            $qkzs = Sys800::where("Type", "T")
                ->where("Cancel", 0)
                ->where("Username", $username)
                ->sum("gold");

            $qkzs_nocheck = Sys800::where("Type", "T")
                ->where("Checked", 0)
                ->where("Cancel", 0)
                ->where("Username", $username)
                ->sum("gold");

            $qkzs2 = Sys800::where("Type", "T")
                ->where("Type2", 3)
                ->where("Checked", 1)
                ->where("Cancel", 0)
                ->where("Username", $username)
                ->sum("gold");

            $zzzs = WebReportData::where("M_Name", $username)
                ->where("Cancel", 0)
                ->sum("M_Result");

            $wjszs = WebReportData::where("M_Name", $username)
                ->where("Cancel", 0)
                ->where("M_Result", "")
                ->sum("BetScore");

            $yxzs = WebReportData::where("M_Name", $username)
                ->where("Cancel", 0)
                ->sum("VGOLD");

            $lottery_zs = OrderLotterySub::where("username", $username)
                ->where("status", "!=", 3)
                ->sum("bet_money");

            $lottery_fs = OrderLotterySub::where("username", $username)
                ->where(function ($query) {
                    $query->where("status", 1)->orWhere("status", 2);
                })
                ->where("is_win", "!=", 2)
                ->sum("fs");

            $lottery_wjs = OrderLotterySub::where("username", $username)
                ->where("status", 0)
                ->sum("bet_money");

            $lottery_zs2 = OrderLotterySub::where("username", $username)
                ->where("is_win", 2)
                ->sum("bet_money");

            $lottery_zs3 = OrderLotterySub::where("username", $username)
                ->where("is_win", 1)
                ->sum("win");

            $lottery_win = $lottery_zs3 + $lottery_zs2 + $lottery_fs + $lottery_wjs - $lottery_zs;

            $ssc1 = Gzhudan::where("g_nid", $username)->sum("g_win");

            $ssc2 = Gzhudan::where("g_nid", $username)->where("g_win", null)->sum("g_jiner");

            $lhc_wjs = KaTan::where("username", $username)->where("Checked", 0)->sum("sum_m");

            $lhc_fs = KaTan::select(DB::raw("SUM(user_ds * sum_m/100) as userds"))
                ->where("username", $username)->where("Checked", 1)->first("userds");

            $lhc_fs = $lhc_fs["userds"] ?? 0;

            $lhc_zs = KaTan::where("username", $username)->where("Checked", 1)->sum("sum_m");

            $lhc_zs2 = KaTan::select(DB::raw("SUM(rate * sum_m) as win"))
                ->where("username", $username)
                ->where("Checked", 1)
                ->where("bm", 1)
                ->first("win");

            $lhc_zs2 = $lhc_zs2["win"] ?? 0;

            $lhc_zs3 = KaTan::where("username", $username)
                ->where("Checked", 1)
                ->where("bm", 2)
                ->sum("sum_m");

            $lhgkzs = intval($lhc_zs2 + $lhc_zs3 + $lhc_fs - $lhc_zs);

            $edwc = intval($ckzs + $zzzs + $lhgkzs - $lhc_wjs - $money - $qkzs - $wjszs + $ssc1 - $ssc2 + $lottery_win - $lottery_wjs - $wucha);

            $data = array(
                "username" => $username,
                "user_id" => $user_id,
                "money" => $money ?? 0,
                "alias" => $alias ?? 0,
                "wucha" => $wucha ?? 0,
                "AddDate" => $AddDate,
                "Agents" => $Agents,
                "LoginIP" => $LoginIP,
                "Url" => $Url,
                "OnlineTime" => $OnlineTime,
                "LoginTime" => $LoginTime,
                "AG_User" => $AG_User,
                "BBIN_User" => $BBIN_User,
                "OG_User" => $OG_User,
                "MG_User" => $MG_User,
                "PT_User" => $PT_User,
                "KY_User" => $KY_User,
                "AG_Money" => $AG_Money,
                "BBIN_Money" => $BBIN_Money,
                "OG_Money" => $OG_Money,
                "MG_Money" => $MG_Money,
                "PT_Money" => $PT_Money,
                "KY_Money" => $KY_Money,
                "yxzs2" => $yxzs2 ?? 0,
                "yxzs_ssc" => $yxzs_ssc ?? 0,
                "yxzs_lottery" => $yxzs_lottery ?? 0,
                "v_gold" => $v_gold ?? 0,
                "g_jiner" => $g_jiner ?? 0,
                "bet_money" => $bet_money ?? 0,
                "sscye" => $sscye ?? 0,
                "ckzs2" => $ckzs2 ?? 0,
                "ckzs" => $ckzs ?? 0,
                "cjzs" => $cjzs ?? 0,
                "hszs" => $hszs ?? 0,
                "qkzs" => $qkzs ?? 0,
                "qkzs_nocheck" => $qkzs_nocheck ?? 0,
                "qkzs2" => $qkzs2 ?? 0,
                "zzzs" => $zzzs ?? 0,
                "wjszs" => $wjszs ?? 0,
                "yxzs" => $yxzs ?? 0,
                "lottery_zs" => $lottery_zs ?? 0,
                "lottery_fs" => $lottery_fs ?? 0,
                "lottery_wjs" => $lottery_wjs ?? 0,
                "lottery_zs2" => $lottery_zs2 ?? 0,
                "lottery_zs3" => $lottery_zs3 ?? 0,
                "lottery_win" => $lottery_win ?? 0,
                "ssc1" => $ssc1 ?? 0,
                "ssc2" => $ssc2 ?? 0,
                "lhc_wjs" => $lhc_wjs ?? 0,
                "lhc_fs" => $lhc_fs ?? 0,
                "lhc_zs" => $lhc_zs ?? 0,
                "lhc_zs2" => $lhc_zs2 ?? 0,
                "lhc_zs3" => $lhc_zs3 ?? 0,
                "lhgkzs" => $lhgkzs ?? 0,
                "edwc" => $edwc ?? 0,
                "sport_bet_money" => $sport_bet_money,
                "other_lottery_bet_money" => $other_lottery_bet_money,
                "hongKong_six_mark_bet_money" => $hongKong_six_mark_bet_money,
                "macao_six_mark_bet_money" => $macao_six_mark_bet_money,
            );

            $response["data"] = $data;
            $response['message'] = 'UserInfo fetched successfully';
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getRecordIP(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'user_name' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $username = $request_data["user_name"];

            $page_no = $request_data["page_no"] ?? 1;

            $limit = $request_data["limit"] ?? 100;

            $user = User::where("UserName", $username)->first("LoginIP");

            $LoginIP = $user["LoginIP"];

            $users = User::where("LoginIP", $LoginIP)->get("UserName");

            $total_count = WebMemberLogs::where("UserName", $username)->count();

            $web_member_logs = WebMemberLogs::where("UserName", $username)
                ->orderBy("ID", "desc")
                ->offset(($page_no - 1) * $limit)
                ->limit($limit)
                ->get();

            $response["data"] = $web_member_logs;
            $response["LoginIP"] = $LoginIP;
            $response["users"] = $users;
            $response["total_count"] = $total_count;
            $response['message'] = 'Record IP Data fetched successfully';
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getRecord(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'user_id' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user_id = $request_data["user_id"];

            $page_no = $request_data["page_no"] ?? 1;

            $limit = $request_data["limit"] ?? 100;

            $total_count = MoneyLog::where("user_id", $user_id)->count();

            $money_logs = MoneyLog::where("user_id", $user_id)
                ->orderBy("id", "desc")
                ->offset(($page_no - 1) * $limit)
                ->limit($limit)
                ->get();

            $response["data"] = $money_logs;
            $response["total_count"] = $total_count;
            $response['message'] = 'Money Log Data fetched successfully';
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
