<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\WebReportZr;
use App\Models\WebReportKy;
use App\Models\WebReportHtr;
use App\Models\Dz2;
use Carbon\Carbon;
use App\Utils\Utils;
use App\Models\User;
use App\Models\Web\Sys800;
use App\Models\Web\WebMemLogData;
use App\Models\Web\MoneyLog;
use Illuminate\Support\Facades\DB;

class HumanManagementController extends Controller
{
    public function getQuery(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "date" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $date = $request_data["date"];
            $platform_type = $request_data["platformType"] ?? "";
            $type = $request_data["type"] ?? "";
            $user = $request_data["user"] ?? "";
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $s_time = $date . " 00:00:00";
            $e_time = $date . " 23:59:59";

            $result = WebReportZr::whereBetween("betTime", [$s_time, $e_time]);

            if ($user != "") {
                $result = $result->where("playerName", "like", "%$user%");
            }

            if ($platform_type != "") {
                $result = $result->where("platformType", $platform_type);
            }

            if ($type != "") {
                if ($type == '电子游艺') {
                    $result = $result->where(function ($query) {
                        $query->where("Type", "EBR")
                            ->orWhere("platformType", "MG")
                            ->orWhere("platformType", "PT");
                    });
                } else {
                    $result = $result->where("gameType", $type);
                }
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                ->take($limit)
                ->get();

            foreach ($result as $item) {
                $dz2 = Dz2::where("GameType", $item["gameType"])
                    ->orWhere("GameType_H5", $item["gameType"])
                    ->first(["GameName"]);
                if (!isset($dz2)) continue;
                $item["GameName"] = $dz2["GameName"];
                $item["playerName"] = substr($item['playerName'], 3);
                $item["tableCode"] = str_replace("null", "", $item['tableCode']);
            }

            $response["data"] = $result;
            $response["total_count"] = $total_count;
            $response['message'] = "Human Query Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getQueryKy(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "date" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $date = $request_data["date"];
            $type = $request_data["type"] ?? "";
            $user = $request_data["user"] ?? "";
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $s_time = $date . " 00:00:00";
            $e_time = $date . " 23:59:59";

            $ky_type = array();

            array_push($ky_type, array("label" => "全部", "value" => ""));

            $dz2 = Dz2::where("PlatformType", "KY")->get();

            foreach ($dz2 as $item) {
                array_push($ky_type, array("label" => $item["GameName"], "value" => $item["GameType"]));
            }

            $result = WebReportKy::whereBetween("GameStartTime", [$s_time, $e_time]);

            if ($user != "") {
                $result = $result->where("Accounts", "like", "%$user%");
            }

            if ($type != "") {
                $result = $result->where("KindID", $type);
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                ->take($limit)
                ->get();

            foreach ($result as $item) {
                $dz2 = Dz2::where("GameType", $item["KindID"])
                    ->first(["GameName"]);
                $item["GameName"] = $dz2["GameName"];
                $item["Accounts"] = substr($item['Accounts'], 3);
            }

            $response["data"] = $result;
            $response["total_count"] = $total_count;
            $response["ky_type"] = $ky_type;
            $response['message'] = "Human Query KY Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getQueryHtr(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "date" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $date = $request_data["date"];
            $user = $request_data["user"] ?? "";
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $s_time = $date . " 00:00:00";
            $e_time = $date . " 23:59:59";

            $result = WebReportHtr::whereBetween("SceneStartTime", [$s_time, $e_time]);

            if ($user != "") {
                $result = $result->where("playerName", "like", "%$user%");
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                ->take($limit)
                ->get();

            foreach ($result as $item) {
                $item["playerName"] = substr($item['playerName'], 3);
                if ($item["Type"] == 1) {
                    $item["Type"] = "場景捕魚";
                } else if ($item["Type"] == 2) {
                    $item["Type"] = "抽獎";
                } else if ($item["Type"] == 7) {
                    $item["Type"] = "捕魚王獎勵";
                }
            }

            $response["data"] = $result;
            $response["total_count"] = $total_count;
            $response['message'] = "Human Query KY Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getReport(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "s_time" => "required|string",
                "e_time" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $s_time = $request_data["s_time"];
            $e_time = $request_data["e_time"];
            $platform_type = $request_data["platformType"] ?? "";
            $type = $request_data["type"] ?? "";
            $user = $request_data["user"] ?? "";
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $data = array();

            $mem_result = WebReportZr::whereBetween("betTime", [$s_time, $e_time]);

            if ($platform_type != "") {
                $mem_result = $mem_result->where("platformType", $platform_type);
            }

            if ($user != "") {
                $result = $mem_result->where("playerName", "like", "%$user%");
            }

            if ($type != "") {
                if ($type == 'EBR') {
                    $mem_result = $mem_result->where(function ($query) {
                        $query->where("Type", "EBR")
                            ->orWhere("platformType", "MG")
                            ->orWhere("platformType", "PT");
                    });
                } else {
                    $mem_result = $mem_result->where("Type", $type);
                }
            }

            $mem_result = $mem_result->select(DB::raw("UserName as user_name, count(ID) as bs,sum(betAmount) as tz,sum(validBetAmount) as yxtz,sum(netAmount) as jg"))
                ->groupBy("UserName")->get();

            // return $mem_result;

            foreach ($mem_result as $item) {
                $user_name = $item->user_name;
                $bs = round($item->bs, 2);
                $tz = round($item->tz, 2);
                $yxtz = round($item->yxtz, 2);
                $jg = round($item->jg, 2);
                array_push($data, array("user_name" => $user_name, "bs" => $bs, "tz" => $tz, "yxtz" => $yxtz, "jg" => $jg));
            }

            $response["data"] = $data;
            $response['message'] = "Human Report Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getReportKy(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "s_time" => "required|string",
                "e_time" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $s_time = $request_data["s_time"];
            $e_time = $request_data["e_time"];
            $type = $request_data["type"] ?? "";
            $user = $request_data["user"] ?? "";
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $ky_type = array();

            array_push($ky_type, array("label" => "全部", "value" => ""));

            $dz2 = Dz2::where("PlatformType", "KY")->get();

            foreach ($dz2 as $item) {
                array_push($ky_type, array("label" => $item["GameName"], "value" => $item["GameType"]));
            }

            $data = array();

            $mem_result = WebReportKy::whereBetween("GameStartTime", [$s_time, $e_time]);

            if ($user != "") {
                $result = $mem_result->where("Accounts", "like", "%$user%");
            }

            if ($type != "") {
                $mem_result = $mem_result->where("KindID", $type);
            }

            $mem_result = $mem_result->select(DB::raw("Accounts as user_name, count(ID) as bs,sum(AllBet) as tz,sum(CellScore) as yxtz,sum(Profit) as jg"))
                ->groupBy("Accounts")->get();

            // return $mem_result;

            foreach ($mem_result as $item) {
                $user_name = substr($item->user_name, 3);
                $bs = round($item->bs, 2);
                $tz = round($item->tz, 2);
                $yxtz = round($item->yxtz, 2);
                $jg = round($item->jg, 2);
                array_push($data, array("user_name" => $user_name, "bs" => $bs, "tz" => $tz, "yxtz" => $yxtz, "jg" => $jg));
            }

            $response["data"] = $data;
            $response["ky_type"] = $ky_type;
            $response['message'] = "Human Report Ky Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getReportHtr(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "s_time" => "required|string",
                "e_time" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $s_time = $request_data["s_time"];
            $e_time = $request_data["e_time"];
            $type = $request_data["type"] ?? "";
            $user = $request_data["user"] ?? "";
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $data = array();

            $mem_result = WebReportHtr::whereBetween("SceneStartTime", [$s_time, $e_time]);

            if ($user != "") {
                $result = $mem_result->where("playerName", "like", "%$user%");
            }

            $mem_result = $mem_result->select(DB::raw("playerName as user_name, count(ID) as bs,sum(Cost) as tz,sum(transferAmount) as jg,sum(Jackpotcomm) as Jackpotcomm"))
                ->groupBy("playerName")->get();

            // return $mem_result;

            foreach ($mem_result as $item) {
                $user_name = substr($item->user_name, 3);
                $bs = round($item->bs, 2);
                $tz = round($item->tz, 2);
                $jg = round($item->jg, 2);
                $jackpotcomm = round($item->Jackpotcomm, 2);
                array_push($data, array("user_name" => $user_name, "bs" => $bs, "tz" => $tz, "jackpotcomm" => $jackpotcomm, "jg" => $jg));
            }

            $response["data"] = $data;
            $response['message'] = "Human Report Htr Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function discountZr(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "s_time" => "required|string",
                "e_time" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user = $request->user();

            $s_time = $request_data["s_time"] . " 00:00:00";
            $e_time = $request_data["e_time"] . " 23:59:59";

            $mem_result = WebReportZr::whereBetween("betTime", [$s_time, $e_time])
                ->where("isFS", 0)->where("Type", "BR")->where("platformType", "!=", "MG")->where("platformType", "!=", "PT");

            $mem_result = $mem_result->select(DB::raw("sum(validBetAmount) as validBetAmount, UserName as username"))->groupBy("UserName")->get();

            foreach ($mem_result as $item) {

                $VGOLD = $item->validBetAmount;

                $user = User::where("UserName", $item->username)->first();
                $fanshui = $user['fanshui_cp'];
                $agents = $user['Agents'];
                $world = $user['World'];
                $corprator = $user['Corprator'];
                $super = $user['Super'];
                $admin = $user['Admin'];
                $Money = $user['Money'];
                $money_ts = round($VGOLD * $fanshui / 100, 2);

                WebReportZr::whereBetween("betTime", [$s_time, $e_time])
                    ->where("isFS", 0)->where("Type", "BR")->where("platformType", "!=", "MG")->where("platformType", "!=", "PT")->where("UserName", $item->username)->update(["isFS" => 1]);

                if ($money_ts > 0) {
                    $Order_Code = 'CK' . date("YmdHis", time() + 12 * 3600) . mt_rand(1000, 9999);
                    $adddate = date("Y-m-d");
                    $date = date("Y-m-d H:i:s");
                    $previousAmount = $Money;
                    $currentAmount = $previousAmount + $money_ts;
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
                        "Bank_Account" => "真人返水",
                        "Order_Code" => $Order_Code,
                        "Music" => 1,
                    );

                    $sys_800 = new Sys800;

                    $sys_800->create($data);

                    $q1 = User::where('UserName', $item["username"])->increment('Money', $money_ts);

                    if ($q1 == 1) {
                        $datetime = date("Y-m-d H:i:s");
                        $currentAmount = Utils::GetField($item["username"], 'Money');
                        $user_id = Utils::GetField($item["username"], 'id');
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $Order_Code;
                        $new_log->about =  $user["UserName"] . "真人返水<br>有效金额:$VGOLD<br>返水金额:$money_ts";
                        $new_log->update_time = $datetime;
                        $new_log->type = $user["UserName"] . "真人返水";
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
                "Context" => '执行真人一键返水',
                "Url" => Utils::get_browser_ip(),
                "Level" => "管理员",
            );

            $web_mem_log_data = new WebMemLogData;

            $web_mem_log_data->create($data);

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

    public function discountDz(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "s_time" => "required|string",
                "e_time" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user = $request->user();

            $s_time = $request_data["s_time"] . " 00:00:00";
            $e_time = $request_data["e_time"] . " 23:59:59";

            $mem_result = WebReportZr::whereBetween("betTime", [$s_time, $e_time])
                ->where("isFS", 0)->where(function ($query) {
                    $query->where("Type", "EBR")
                        ->orWhere("platformType", "MG")
                        ->orWhere("platformType", "PT");
                });

            $mem_result = $mem_result->select(DB::raw("sum(validBetAmount) as validBetAmount, UserName as username"))->groupBy("UserName")->get();

            foreach ($mem_result as $item) {

                $VGOLD = $item->validBetAmount;

                $user = User::where("UserName", $item->username)->first();
                $fanshui = $user['fanshui_cp'];
                $agents = $user['Agents'];
                $world = $user['World'];
                $corprator = $user['Corprator'];
                $super = $user['Super'];
                $admin = $user['Admin'];
                $Money = $user['Money'];
                $money_ts = round($VGOLD * $fanshui / 100, 2);

                WebReportZr::whereBetween("betTime", [$s_time, $e_time])
                    ->where("isFS", 0)->where(function ($query) {
                        $query->where("Type", "EBR")
                            ->orWhere("platformType", "MG")
                            ->orWhere("platformType", "PT");
                    })->where("UserName", $item->username)->update(["isFS" => 1]);

                if ($money_ts > 0) {
                    $Order_Code = 'CK' . date("YmdHis", time() + 12 * 3600) . mt_rand(1000, 9999);
                    $adddate = date("Y-m-d");
                    $date = date("Y-m-d H:i:s");
                    $previousAmount = $Money;
                    $currentAmount = $previousAmount + $money_ts;
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
                        "Bank_Account" => "电子返水",
                        "Order_Code" => $Order_Code,
                        "Music" => 1,
                    );

                    $sys_800 = new Sys800;

                    $sys_800->create($data);

                    $q1 = User::where('UserName', $item["username"])->increment('Money', $money_ts);

                    if ($q1 == 1) {
                        $datetime = date("Y-m-d H:i:s");
                        $currentAmount = Utils::GetField($item["username"], 'Money');
                        $user_id = Utils::GetField($item["username"], 'id');
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $Order_Code;
                        $new_log->about =  $user["UserName"] . "电子返水<br>有效金额:$VGOLD<br>返水金额:$money_ts";
                        $new_log->update_time = $datetime;
                        $new_log->type = $user["UserName"] . "电子返水";
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
                "Context" => '执行电子一键返水',
                "Url" => Utils::get_browser_ip(),
                "Level" => "管理员",
            );

            $web_mem_log_data = new WebMemLogData;

            $web_mem_log_data->create($data);

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

    public function discountKy(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "s_time" => "required|string",
                "e_time" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user = $request->user();

            $s_time = $request_data["s_time"] . " 00:00:00";
            $e_time = $request_data["e_time"] . " 23:59:59";

            $mem_result = WebReportKy::whereBetween("GameStartTime", [$s_time, $e_time]);

            $mem_result = $mem_result->select(DB::raw("sum(CellScore) as CellScore, Accounts as username"))->groupBy("Accounts")->get();

            foreach ($mem_result as $item) {

                $VGOLD = $item->CellScore;

                $user = User::where("UserName", $item->username)->first();
                $fanshui = $user['fanshui_cp'];
                $agents = $user['Agents'];
                $world = $user['World'];
                $corprator = $user['Corprator'];
                $super = $user['Super'];
                $admin = $user['Admin'];
                $Money = $user['Money'];
                $money_ts = round($VGOLD * $fanshui / 100, 2);

                WebReportKy::whereBetween("GameStartTime", [$s_time, $e_time])
                    ->where("Accounts", $item->username)->update(["isFS" => 1]);

                if ($money_ts > 0) {
                    $Order_Code = 'CK' . date("YmdHis", time() + 12 * 3600) . mt_rand(1000, 9999);
                    $adddate = date("Y-m-d");
                    $date = date("Y-m-d H:i:s");
                    $previousAmount = $Money;
                    $currentAmount = $previousAmount + $money_ts;
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
                        "Bank_Account" => "棋牌返水",
                        "Order_Code" => $Order_Code,
                        "Music" => 1,
                    );

                    $sys_800 = new Sys800;

                    $sys_800->create($data);

                    $q1 = User::where('UserName', $item["username"])->increment('Money', $money_ts);

                    if ($q1 == 1) {
                        $datetime = date("Y-m-d H:i:s");
                        $currentAmount = Utils::GetField($item["username"], 'Money');
                        $user_id = Utils::GetField($item["username"], 'id');
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $Order_Code;
                        $new_log->about =  $user["UserName"] . "棋牌返水<br>有效金额:$VGOLD<br>返水金额:$money_ts";
                        $new_log->update_time = $datetime;
                        $new_log->type = $user["UserName"] . "棋牌返水";
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
                "Context" => '执行棋牌一键返水',
                "Url" => Utils::get_browser_ip(),
                "Level" => "管理员",
            );

            $web_mem_log_data = new WebMemLogData;

            $web_mem_log_data->create($data);

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

    public function discountHtr(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "s_time" => "required|string",
                "e_time" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user = $request->user();

            $s_time = $request_data["s_time"] . " 00:00:00";
            $e_time = $request_data["e_time"] . " 23:59:59";

            $mem_result = WebReportHtr::whereBetween("SceneStartTime", [$s_time, $e_time]);

            $mem_result = $mem_result->select(DB::raw("sum(Cost) as Cost, UserName as username"))->groupBy("UserName")->get();

            foreach ($mem_result as $item) {

                $VGOLD = $item->Cost;

                $user = User::where("UserName", $item->username)->first();
                $fanshui = $user['fanshui_cp'];
                $agents = $user['Agents'];
                $world = $user['World'];
                $corprator = $user['Corprator'];
                $super = $user['Super'];
                $admin = $user['Admin'];
                $Money = $user['Money'];
                $money_ts = round($VGOLD * $fanshui / 100, 2);

                WebReportHtr::whereBetween("SceneStartTime", [$s_time, $e_time])
                    ->where("UserName", $item->username)->update(["isFS" => 1]);

                if ($money_ts > 0) {
                    $Order_Code = 'CK' . date("YmdHis", time() + 12 * 3600) . mt_rand(1000, 9999);
                    $adddate = date("Y-m-d");
                    $date = date("Y-m-d H:i:s");
                    $previousAmount = $Money;
                    $currentAmount = $previousAmount + $money_ts;
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
                        "Bank_Account" => "捕鱼返水",
                        "Order_Code" => $Order_Code,
                        "Music" => 1,
                    );

                    $sys_800 = new Sys800;

                    $sys_800->create($data);

                    $q1 = User::where('UserName', $item["username"])->increment('Money', $money_ts);

                    if ($q1 == 1) {
                        $datetime = date("Y-m-d H:i:s");
                        $currentAmount = Utils::GetField($item["username"], 'Money');
                        $user_id = Utils::GetField($item["username"], 'id');
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $Order_Code;
                        $new_log->about =  $user["UserName"] . "捕鱼返水<br>有效金额:$VGOLD<br>返水金额:$money_ts";
                        $new_log->update_time = $datetime;
                        $new_log->type = $user["UserName"] . "捕鱼返水";
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
                "Context" => '执行捕鱼一键返水',
                "Url" => Utils::get_browser_ip(),
                "Level" => "管理员",
            );

            $web_mem_log_data = new WebMemLogData;

            $web_mem_log_data->create($data);

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

    public function getThirdpartyGameData(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $platform_type = $request_data["platformType"] ?? "";
            $game_name = $request_data["gameName"] ?? "";
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $dz2 = Dz2::where("Del", 0);

            if ($platform_type != "") {
                $dz2 = $dz2->where("PlatformType", $platform_type);
            }

            if ($game_name != "") {
                $dz2 = $dz2->where(function ($query) use ($game_name) {
                    $query->where("GameName", "like", "%$game_name%")
                        ->orWhere("GameName_EN", "like", "%$game_name%")
                        ->orWhere("GameType", "like", "%$game_name%")
                        ->orWhere("GameType_H5", "like", "%$game_name%");
                });
            }

            $total_count = $dz2->count();

            $dz2 = $dz2->offset(($page_no - 1) * $limit)
                ->take($limit)->orderBy("ID", "desc")->get();

            foreach ($dz2 as $item) {
                $item["Open"] = $item["Open"] == 1 ? true : false;
            }

            $response["data"] = $dz2;
            $response["total_count"] = $total_count;
            $response['message'] = "Game Data fethced successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function gameOpen(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric",
                "open" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];
            $open = $request_data["open"];

            Dz2::where("ID", $id)->update(["open" => $open]);

            $response['message'] = "Game Open Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteGame(Request $request)
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

            Dz2::where("ID", $id)->delete();

            $response['message'] = "Game deleted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function editGame(Request $request)
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

            $result = Dz2::where("ID", $id)->first();

            $response["data"] = $result;
            $response['message'] = "Game Item fethced successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateGame(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "ID" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ID = $request_data["ID"];

            Dz2::where("ID", $ID)
                ->update([
                    "GameName" => $request_data["GameName"],
                    "GameName_EN" => $request_data["GameName_EN"],
                    "PlatformType" => $request_data["PlatformType"],
                    "GameClass" => $request_data["GameClass"],
                    "GameType_H5" => $request_data["GameType_H5"],
                    "GameType" => $request_data["GameType"],
                    "ZH_Logo_File" => $request_data["ZH_Logo_File"],
                    "H5_Logo_File" => $request_data["H5_Logo_File"],
                    "Date" => $request_data["Date"],
                ]);

            $response['message'] = "Game Item updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addGame(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $new_data = array(
                "GameName" => $request_data["GameName"],
                "GameName_EN" => $request_data["GameName_EN"] ?? "",
                "PlatformType" => $request_data["PlatformType"],
                "GameClass" => $request_data["GameClass"] ?? "",
                "GameType_H5" => $request_data["GameType_H5"] ?? "",
                "GameType" => $request_data["GameType"],
                "ZH_Logo_File" => $request_data["ZH_Logo_File"],
                "H5_Logo_File" => $request_data["H5_Logo_File"] ?? "",
                "Date" => $request_data["Date"] ?? date("Y-m-d"),
            );

            $dz2 = new Dz2;

            $dz2->create($new_data);

            $response['message'] = "Game Item added successfully!";
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
