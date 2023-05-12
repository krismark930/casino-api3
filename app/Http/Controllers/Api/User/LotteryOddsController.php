<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\Carbon;
use App\Models\OddsLotteryNormal;
use App\Models\OddsLottery;

class LotteryOddsController extends Controller
{
    public function getB5Odds(Request $request) {

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
            $lottery_type = "";
                
            switch ($g_type) {
                case "cq":
                    $lottery_type = "重庆时时彩";
                    break;
                case "ffc5":
                    $lottery_type = "五分彩";
                    break;
                case "txssc":
                    $lottery_type = "腾讯时时彩";
                    break;
                case "twssc":
                    $lottery_type = "台湾时时彩";
                    break;
                case "azxy5":
                    $lottery_type = "澳洲幸运5";
                    break;
                case "jx":
                    $lottery_type = "新疆时时彩";
                    break;
                case "tj":
                    $lottery_type = "天津时时彩";
                    break;
            }
            
            $result = OddsLotteryNormal::where("lottery_type", $lottery_type)->get();

            $response["data"] = $result;
            $response['message'] = "B5 Odds Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getB3Odds(Request $request) {

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
            $lottery_type = "";
                
            switch ($g_type) {
                case "d3":
                    $lottery_type = "3D彩";
                    break;
                case "p3":
                    $lottery_type = "排列三";
                    break;
                case "t3":
                    $lottery_type = "上海时时乐";
                    break;
            }
            
            $result = OddsLotteryNormal::where("lottery_type", $lottery_type)->get();

            $response["data"] = $result;
            $response['message'] = "B3 Odds Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getOtherOdds(Request $request) {

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
            $g_type = $request_data["g_type"];
            $lottery_type = "";
                
            switch ($g_type) {
                case "gd11":
                    $lottery_type = "广东十一选五";
                    break;
                case "azxy10":
                    $lottery_type = "澳洲幸运10";
                    break;
                case "cqsf":
                    $lottery_type = "重庆十分彩";
                    break;
                case "gdsf":
                    $lottery_type = "广东十分彩";
                    break;
                case "tjsf":
                    $lottery_type = "天津十分彩";
                    break;
                case "gxsf":
                    $lottery_type = "广西十分彩";
                    break;
                case "bjpk":
                    $lottery_type = "北京PK拾";
                    break;
                case "xyft":
                    $lottery_type = "幸运飞艇";
                    break;
            }

            $result = OddsLottery::where("lottery_type", $lottery_type)->get();

            $response['data'] = $result;
            $response['message'] = "Other Odds Data fetched successfully!";
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
