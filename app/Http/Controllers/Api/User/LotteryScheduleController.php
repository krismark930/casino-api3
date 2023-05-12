<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\Carbon;
use App\Models\SysConfig;
use App\Models\LotterySchedule;

class LotteryScheduleController extends Controller
{
    public function getB5Schedule(Request $request) {

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

            $current_time = Carbon::now('Asia/Hong_Kong')->format('H:i:s');

            $g_type = $request_data["g_type"];

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $lottery_config = json_decode($sys_config["Lottery_Config"], true);

            $lottery_type = "";
                
            switch ($g_type) {
                case "cq":
                    $lottery_type = "重庆时时彩";
                    $result = LotterySchedule::where("lottery_type", $lottery_type)
                            ->where("kaipan_time", "<=", $current_time)
                            ->where("kaijiang_time", ">", $current_time)
                            ->first();
                    if (isset($result)) {
                        $result["is_open"] = true;
                        $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                        $result["qishu"] = $current_date.$result["qishu"];
                        $e_time = Carbon::parse($result["kaijiang_time"]);
                        $s_time = Carbon::parse($current_time);
                        $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
                    } else {
                        $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                        $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                        $result = $first_lottery;
                        $isLateNight = false;
                        if ($current_time >= $last_lottery["kaijiang_time"]) {
                            $isLateNight = true;
                        }
                        $current_date = $isLateNight ? Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Ymd') : Carbon::now('Asia/Hong_Kong')->format('Ymd');       
                        $result["qishu"] = $current_date.$result["qishu"];
                        $result["is_open"] = false;
                        $result["diff_time"] = 0;
                    }
                    break;
                case "ffc5":
                    $lottery_type = "五分彩";
                    $result = LotterySchedule::where("lottery_type", $lottery_type)
                            ->where("kaipan_time", "<=", $current_time)
                            ->where("kaijiang_time", ">", $current_time)
                            ->first();
                    $result["is_open"] = true;
                    $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                    $result["qishu"] = $current_date.$result["qishu"];
                    $e_time = Carbon::parse($result["kaijiang_time"]);
                    $s_time = Carbon::parse($current_time);
                    $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
                    break;
                case "txssc":
                    $lottery_type = "腾讯时时彩";
                    $result = LotterySchedule::where("lottery_type", $lottery_type)
                            ->where("kaipan_time", "<=", $current_time)
                            ->where("kaijiang_time", ">", $current_time)
                            ->first();
                    $result["is_open"] = true;
                    $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                    $result["qishu"] = $current_date.$result["qishu"];
                    $e_time = Carbon::parse($result["kaijiang_time"]);
                    $s_time = Carbon::parse($current_time);
                    $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
                    break;
                case "twssc":
                    $lottery_type = "台湾时时彩";
                    $result = LotterySchedule::where("lottery_type", $lottery_type)
                            ->where("kaipan_time", "<=", $current_time)
                            ->where("kaijiang_time", ">", $current_time)
                            ->first();
                    $result["is_open"] = true;
                    $e_time = Carbon::parse($result["kaijiang_time"]);
                    $s_time = Carbon::parse($current_time);
                    $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
                    $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');
                    $last_time = $lottery_config["twssc"]["ktime"]." ".$times;
                    $last_time = Carbon::parse($last_time);
                    $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                    $diff = $last_time->diffInDays($current_time);
                    $result["qishu"] = $diff * 203 + $lottery_config["twssc"]["knum"] + $result["qishu"] - 1;
                    break;
                case "azxy5":
                    $lottery_type = "澳洲幸运5";
                    $result = LotterySchedule::where("lottery_type", $lottery_type)
                            ->where("kaipan_time", "<=", $current_time)
                            ->where("kaijiang_time", ">", $current_time)
                            ->first();
                    $result["is_open"] = true;
                    $e_time = Carbon::parse($result["kaijiang_time"]);
                    $s_time = Carbon::parse($current_time);
                    $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
                    $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');
                    $last_time = $lottery_config["azxy5"]["ktime"]." ".$times;
                    $last_time = Carbon::parse($last_time);
                    $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                    $diff = $last_time->diffInDays($current_time);
                    $result["qishu"] = $diff * 288 + $lottery_config["azxy5"]["knum"] + $result["qishu"] - 1;
                    break;
                case "jx":
                    $lottery_type = "新疆时时彩";
                    $result = LotterySchedule::where("lottery_type", $lottery_type)
                            ->where("kaipan_time", "<=", $current_time)
                            ->where("kaijiang_time", ">", $current_time)
                            ->first();
                    if (isset($result)) {
                        $result["is_open"] = true;
                        $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                        if(intval($result['qishu'])>=43) {
                            $current_date = Carbon::now('Asia/Hong_Kong')->subDays(1)->format('Ymd');
                        }
                        $result["qishu"] = $current_date.$result["qishu"];
                        $e_time = Carbon::parse($result["kaijiang_time"]);
                        $s_time = Carbon::parse($current_time);
                        $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
                    } else {
                        $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                        $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                        $result = $first_lottery;
                        $isLateNight = false;
                        if ($current_time >= $last_lottery["kaijiang_time"]) {
                            $isLateNight = true;
                        }
                        $current_date = $isLateNight ? Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Ymd') : Carbon::now('Asia/Hong_Kong')->format('Ymd');
                        if(intval($result['qishu'])>=43) {
                            $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                        }
                        $result["qishu"] = $current_date.$result["qishu"];
                        $result["is_open"] = false;
                        $result["diff_time"] = 0;
                    }               
                    break;
                case "tj":
                    $lottery_type = "天津时时彩";
                    $result = LotterySchedule::where("lottery_type", $lottery_type)
                            ->where("kaipan_time", "<=", $current_time)
                            ->where("kaijiang_time", ">", $current_time)
                            ->first();
                    if (isset($result)) {
                        $result["is_open"] = true;
                        $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                        $result["qishu"] = $current_date.$result["qishu"];
                        $e_time = Carbon::parse($result["kaijiang_time"]);
                        $s_time = Carbon::parse($current_time);
                        $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
                    } else {
                        $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                        $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                        $result = $first_lottery;
                        $isLateNight = false;
                        if ($current_time >= $last_lottery["kaijiang_time"]) {
                            $isLateNight = true;
                        }
                        $current_date = $isLateNight ? Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Ymd') : Carbon::now('Asia/Hong_Kong')->format('Ymd');       
                        $result["qishu"] = $current_date.$result["qishu"];
                        $result["is_open"] = false;
                        $result["diff_time"] = 0;
                    }
                    break;
            }

            $response["data"] = $result;
            $response['message'] = "B5 Schedule Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getB3Schedule(Request $request) {

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

            $current_time = Carbon::now('Asia/Hong_Kong')->format('H:i:s');

            $g_type = $request_data["g_type"];

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $lottery_config = json_decode($sys_config["Lottery_Config"], true);

            $lottery_type = "";
                
            switch ($g_type) {
                case "d3":
                    $lottery_type = "3D彩";
                    $result = array();
                    $result["is_open"] = true;
                    $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');
                    $last_time = $lottery_config["d3"]["ktime"]." ".$times;
                    $last_time = Carbon::parse($last_time);
                    $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                    $lost_days = $last_time->diffInDays($current_time);
                    $result["qishu"] = $lost_days + $lottery_config["d3"]["knum"];

                    if("23:59:59" >= $times && $times >= "21:00:00"){
                        $result["qishu"] = $result["qishu"] + 1;
                        $result["kaijiang_time"] = Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Y-m-d').' 20:28:00';
                        $result["is_open"] = false;
                        $result["diff_time"] = 0;
                    }else{
                        $result["kaijiang_time"] = Carbon::now('Asia/Hong_Kong')->format('Y-m-d').' 20:28:00';
                        $e_time = Carbon::parse($result["kaijiang_time"]);
                        $s_time = Carbon::parse($current_time);
                        $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
                    }
                    break;
                case "p3":
                    $lottery_type = "排列三";
                    $result = array();
                    $result["is_open"] = true;
                    $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');
                    $last_time = $lottery_config["p3"]["ktime"]." ".$times;
                    $last_time = Carbon::parse($last_time);
                    $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                    $lost_days = $last_time->diffInDays($current_time);
                    $result["qishu"] = $lost_days + $lottery_config["p3"]["knum"];

                    if("23:59:59" >= $times && $times >= "21:00:00"){
                        $result["qishu"] = $result["qishu"] + 1;
                        $result["kaijiang_time"] = Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Y-m-d').' 20:28:00';
                        $result["is_open"] = false;
                        $result["diff_time"] = 0;
                    }else{
                        $result["kaijiang_time"] = Carbon::now('Asia/Hong_Kong')->format('Y-m-d').' 20:28:00';
                        $e_time = Carbon::parse($result["kaijiang_time"]);
                        $s_time = Carbon::parse($current_time);
                        $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
                    }
                    break;
                case "t3":
                    $lottery_type = "上海时时乐";
                    $result = LotterySchedule::where("lottery_type", $lottery_type)
                            ->where("kaipan_time", "<=", $current_time)
                            ->where("kaijiang_time", ">", $current_time)
                            ->first();
                    if (isset($result)) {
                        $result["is_open"] = true;
                        $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                        $result["qishu"] = $current_date.$result["qishu"];
                        $e_time = Carbon::parse($result["kaijiang_time"]);
                        $s_time = Carbon::parse($current_time);
                        $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
                    } else {
                        $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                        $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                        $result = $first_lottery;
                        $isLateNight = false;
                        if ($current_time >= $last_lottery["kaijiang_time"]) {
                            $isLateNight = true;
                        }
                        $current_date = $isLateNight ? Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Ymd') : Carbon::now('Asia/Hong_Kong')->format('Ymd');       
                        $result["qishu"] = $current_date.$result["qishu"];
                        $result["is_open"] = false;
                        $result["diff_time"] = 0;
                    }
                    break;
            }

            $response["data"] = $result;
            $response['message'] = "B3 Schedule Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getGD11Schedule(Request $request) {

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

            $current_time = Carbon::now('Asia/Hong_Kong')->format('H:i:s');

            $g_type = $request_data["g_type"];

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $lottery_config = json_decode($sys_config["Lottery_Config"], true);

            $lottery_type = "广东十一选五";

            $result = LotterySchedule::where("lottery_type", $lottery_type)
                    ->where("kaipan_time", "<=", $current_time)
                    ->where("kaijiang_time", ">", $current_time)
                    ->first();
            if (isset($result)) {
                $result["is_open"] = true;
                $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                $result["qishu"] = $current_date.$result["qishu"];
                $e_time = Carbon::parse($result["kaijiang_time"]);
                $s_time = Carbon::parse($current_time);
                $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
            } else {
                $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                $result = $first_lottery;
                $isLateNight = false;
                if ($current_time >= $last_lottery["kaijiang_time"]) {
                    $isLateNight = true;
                }
                $current_date = $isLateNight ? Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Ymd') : Carbon::now('Asia/Hong_Kong')->format('Ymd');       
                $result["qishu"] = $current_date.$result["qishu"];
                $result["is_open"] = false;
                        $result["diff_time"] = 0;
            }

            $response["data"] = $result;
            $response['message'] = "GD11 Schedule Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getAZXY10Schedule(Request $request) {

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

            $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');

            $g_type = $request_data["g_type"];

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $lottery_config = json_decode($sys_config["Lottery_Config"], true);

            $lottery_type = "澳洲幸运10";

            $result = LotterySchedule::where("lottery_type", $lottery_type)
                    ->where("kaipan_time", "<=", $times)
                    ->where("kaijiang_time", ">", $times)
                    ->first();

            $result["is_open"] = true;

            $last_time = $lottery_config["azxy10"]["ktime"]." ".$times;
            $last_time = Carbon::parse($last_time);
            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $lost_days = $last_time->diffInDays($current_time);
            $result["qishu"] = $lost_days * 288 + $lottery_config["azxy10"]["knum"] + $result["qishu"] - 1;
            $e_time = Carbon::parse($result["kaijiang_time"]);
            $s_time = Carbon::parse($times);
            $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;

            $response["data"] = $result;
            $response['message'] = "AZXY10 Schedule Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getCQSFSchedule(Request $request) {

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

            $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');

            $g_type = $request_data["g_type"];

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $lottery_config = json_decode($sys_config["Lottery_Config"], true);

            $lottery_type = "重庆十分彩";

            $result = LotterySchedule::where("lottery_type", $lottery_type)
                    ->where("kaipan_time", "<=", $times)
                    ->where("kaijiang_time", ">", $times)
                    ->first();

            if (isset($result)) {
                $result["is_open"] = true;
                $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                $result["qishu"] = $current_date.$result["qishu"];
                $e_time = Carbon::parse($result["kaijiang_time"]);
                $s_time = Carbon::parse($times);
                $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
            } else {
                $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                $result = $first_lottery;
                $isLateNight = false;
                if ($times >= $last_lottery["kaijiang_time"]) {
                    $isLateNight = true;
                }
                $current_date = $isLateNight ? Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Ymd') : Carbon::now('Asia/Hong_Kong')->format('Ymd');       
                $result["qishu"] = $current_date.$result["qishu"];
                $result["is_open"] = false;
                $result["diff_time"] = 0;
            }

            $response["data"] = $result;
            $response['message'] = "CQSF Schedule Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getGDSFSchedule(Request $request) {

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

            $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');

            $g_type = $request_data["g_type"];

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $lottery_config = json_decode($sys_config["Lottery_Config"], true);

            $lottery_type = "广东十分彩";

            $result = LotterySchedule::where("lottery_type", $lottery_type)
                    ->where("kaipan_time", "<=", $times)
                    ->where("kaijiang_time", ">", $times)
                    ->first();

            if (isset($result)) {
                $result["is_open"] = true;
                $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                $result["qishu"] = $current_date.$result["qishu"];
                $e_time = Carbon::parse($result["kaijiang_time"]);
                $s_time = Carbon::parse($times);
                $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
            } else {
                $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                $result = $first_lottery;
                $isLateNight = false;
                if ($times >= $last_lottery["kaijiang_time"]) {
                    $isLateNight = true;
                }
                $current_date = $isLateNight ? Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Ymd') : Carbon::now('Asia/Hong_Kong')->format('Ymd');       
                $result["qishu"] = $current_date.$result["qishu"];
                $result["is_open"] = false;
                $result["diff_time"] = 0;
            }

            $response["data"] = $result;
            $response['message'] = "GDSF Schedule Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getTJSFSchedule(Request $request) {

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

            $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');

            $g_type = $request_data["g_type"];

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $lottery_config = json_decode($sys_config["Lottery_Config"], true);

            $lottery_type = "天津十分彩";

            $result = LotterySchedule::where("lottery_type", $lottery_type)
                    ->where("kaipan_time", "<=", $times)
                    ->where("kaijiang_time", ">", $times)
                    ->first();

            if (isset($result)) {
                $result["is_open"] = true;
                $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                $result["qishu"] = $current_date.$result["qishu"];
                $e_time = Carbon::parse($result["kaijiang_time"]);
                $s_time = Carbon::parse($times);
                $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
            } else {
                $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                $result = $first_lottery;
                $isLateNight = false;
                if ($times >= $last_lottery["kaijiang_time"]) {
                    $isLateNight = true;
                }
                $current_date = $isLateNight ? Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Ymd') : Carbon::now('Asia/Hong_Kong')->format('Ymd');       
                $result["qishu"] = $current_date.$result["qishu"];
                $result["is_open"] = false;
                $result["diff_time"] = 0;
            }

            $response["data"] = $result;
            $response['message'] = "TJSF Schedule Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getGXSFSchedule(Request $request) {

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

            $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');

            $g_type = $request_data["g_type"];

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $lottery_config = json_decode($sys_config["Lottery_Config"], true);

            $lottery_type = "广西十分彩";

            $result = LotterySchedule::where("lottery_type", $lottery_type)
                    ->where("kaipan_time", "<=", $times)
                    ->where("kaijiang_time", ">", $times)
                    ->first();

            $isLateNight = false;

            if (isset($result)) {
                $result["is_open"] = true;
                $e_time = Carbon::parse($result["kaijiang_time"]);
                $s_time = Carbon::parse($times);
                $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
            } else {
                $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                $result = $first_lottery;
                if ($times >= $last_lottery["kaijiang_time"]) {
                    $isLateNight = true;
                }
                $result["is_open"] = false;
                $result["diff_time"] = 0;
            }

            $last_time = Carbon::now('Asia/Hong_Kong')->format('Y').'-01-01 00:00:01';
            $last_time = Carbon::parse($last_time);
            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $lost_days = $last_time->diffInDays($current_time) + 1;
            if ($isLateNight) {
                $lost_days++;
            }
            $result["qishu"] = Carbon::now('Asia/Hong_Kong')->format('Y').$lost_days.$result["qishu"];

            $response["data"] = $result;
            $response['message'] = "GXSF Schedule Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getBJPKSchedule(Request $request) {

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

            $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');

            $g_type = $request_data["g_type"];

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $lottery_config = json_decode($sys_config["Lottery_Config"], true);

            $lottery_type = "北京PK拾";

            $result = LotterySchedule::where("lottery_type", $lottery_type)
                    ->where("kaipan_time", "<=", $times)
                    ->where("kaijiang_time", ">", $times)
                    ->first();
                    
            $isLateNight = false;

            if (isset($result)) {
                $result["is_open"] = true;
                $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                $e_time = Carbon::parse($result["kaijiang_time"]);
                $s_time = Carbon::parse($times);
                $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
            } else {
                $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                $result = $first_lottery;
                if ($times >= $last_lottery["kaijiang_time"]) {
                    $isLateNight = true;
                }
                $result["is_open"] = false;
                $result["diff_time"] = 0;
            }

            $last_time = $lottery_config["pk10"]["ktime"]." ".$times;
            $last_time = Carbon::parse($last_time);
            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $lost_days = $last_time->diffInDays($current_time);

            if ($isLateNight) {
                $lost_days++;
            }
            $result["qishu"] = $lost_days * 44 + $lottery_config["pk10"]["knum"] + $result["qishu"] - 1;

            $response["data"] = $result;
            $response['message'] = "BJPK Schedule Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getXYFTSchedule(Request $request) {

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

            $times = Carbon::now('Asia/Hong_Kong')->format('H:i:s');

            $g_type = $request_data["g_type"];

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $lottery_config = json_decode($sys_config["Lottery_Config"], true);

            $lottery_type = "幸运飞艇";

            $result = LotterySchedule::where("lottery_type", $lottery_type)
                    ->where("kaipan_time", "<=", $times)
                    ->where("kaijiang_time", ">", $times)
                    ->first();
                    
            if (isset($result)) {
                $result["is_open"] = true;
                $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                if(intval($result['qishu']) > 131) {
                    $current_date = Carbon::now('Asia/Hong_Kong')->subDays(1)->format('Ymd');
                }
                $result["qishu"] = $current_date.$result["qishu"];
                $e_time = Carbon::parse($result["kaijiang_time"]);
                $s_time = Carbon::parse($times);
                $result["diff_time"] = $e_time->diffInSeconds($s_time) * 1000 + 60000;
            } else {
                $first_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "asc")->first();
                $last_lottery = LotterySchedule::where("lottery_type", $lottery_type)->orderBy("id", "desc")->first();
                $result = $first_lottery;
                $isLateNight = false;
                if ($times >= $last_lottery["kaijiang_time"]) {
                    $isLateNight = true;
                }
                $current_date = $isLateNight ? Carbon::now('Asia/Hong_Kong')->addDays(1)->format('Ymd') : Carbon::now('Asia/Hong_Kong')->format('Ymd');
                if(intval($result['qishu']) > 131) {
                    $current_date = Carbon::now('Asia/Hong_Kong')->format('Ymd');
                }       
                $result["qishu"] = $current_date.$result["qishu"];
                $result["is_open"] = false;
                $result["diff_time"] = 0;
            }

            $response["data"] = $result;
            $response['message'] = "XYFT Schedule Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getLotteryStatus(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $sys_config = SysConfig::query()->first(["Lottery_Config"]);

            $response["data"] = json_decode($sys_config["Lottery_Config"], true);
            $response['message'] = "Lottery Status Data fetched successfully!";
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
