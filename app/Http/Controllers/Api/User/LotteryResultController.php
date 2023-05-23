<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\LotteryResultCQ;
use App\Models\LotteryResultFFC5;
use App\Models\LotteryResultTXSSC;
use App\Models\LotteryResultTWSSC;
use App\Models\LotteryResultAZXY5;
use App\Models\LotteryResultJX;
use App\Models\LotteryResultTJ;
use App\Models\LotteryResultD3;
use App\Models\LotteryResultP3;
use App\Models\LotteryResultT3;
use App\Models\LotteryResultGD11;
use App\Models\LotteryResultCQSF;
use App\Models\LotteryResultGDSF;
use App\Models\LotteryResultGXSF;
use App\Models\LotteryResultTJSF;
use App\Models\LotteryResultAZXY10;
use App\Models\LotteryResultBJPK;
use App\Models\LotteryResultXYFT;
use App\Utils\Utils;
use Carbon\Carbon;

class LotteryResultController extends Controller
{
    public function getB5Result(Request $request) {

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
                
            switch ($g_type) {
                case "cq":
                    $result = LotteryResultCQ::orderBy("datetime", "desc")->first();
                    break;
                case "ffc5":
                    $result = LotteryResultFFC5::orderBy("datetime", "desc")->first();
                    break;
                case "txssc":
                    $result = LotteryResultTXSSC::orderBy("datetime", "desc")->first();
                    break;
                case "twssc":
                    $result = LotteryResultTWSSC::orderBy("datetime", "desc")->first();
                    break;
                case "azxy5":
                    $result = LotteryResultAZXY5::orderBy("datetime", "desc")->first();
                    break;
                case "jx":
                    $result = LotteryResultJX::orderBy("datetime", "desc")->first();
                    break;
                case "tj":
                    $result = LotteryResultTJ::orderBy("datetime", "desc")->first();
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

    public function getB3Result(Request $request) {

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
                
            switch ($g_type) {
                case "d3":
                    $result = LotteryResultD3::orderBy("datetime", "desc")->first();
                    break;
                case "p3":
                    $result = LotteryResultP3::orderBy("datetime", "desc")->first();
                    break;
                case "t3":
                    $result = LotteryResultT3::orderBy("datetime", "desc")->first();
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

    public function getOtherResult(Request $request) {

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
                
            switch ($g_type) {
                case "gd11":
                    $result = LotteryResultGD11::orderBy("datetime", "desc")->first();
                    break;
                case "azxy10":
                    $result = LotteryResultAZXY10::orderBy("datetime", "desc")->first();
                    break;
                case "cqsf":
                    $result = LotteryResultCQSF::orderBy("datetime", "desc")->first();
                    break;
                case "gdsf":
                    $result = LotteryResultGDSF::orderBy("datetime", "desc")->first();
                    break;
                case "gxsf":
                    $result = LotteryResultGXSF::orderBy("datetime", "desc")->first();
                    break;
                case "tjsf":
                    $result = LotteryResultTJSF::orderBy("datetime", "desc")->first();
                    break;
                case "bjpk":
                    $result = LotteryResultBJPK::orderBy("datetime", "desc")->first();
                    break;
                case "xyft":
                    $result = LotteryResultXYFT::orderBy("datetime", "desc")->first();
                    break;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result Other Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getB5BirthHistory(Request $request) {

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
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 100;
                
            switch ($g_type) {
                case "cq":
                    $result = LotteryResultCQ::offset(($page_no - 1) * $limit)
                            ->take($limit)->orderBy("qishu", "desc")->get();
                    break;
                case "ffc5":
                    $result = LotteryResultFFC5::offset(($page_no - 1) * $limit)
                            ->take($limit)->orderBy("qishu", "desc")->get();
                    break;
                case "txssc":
                    $result = LotteryResultTXSSC::offset(($page_no - 1) * $limit)
                            ->take($limit)->orderBy("qishu", "desc")->get();
                    break;
                case "twssc":
                    $result = LotteryResultTWSSC::offset(($page_no - 1) * $limit)
                            ->take($limit)->orderBy("qishu", "desc")->get();
                    break;
                case "azxy5":
                    $result = LotteryResultAZXY5::offset(($page_no - 1) * $limit)
                            ->take($limit)->orderBy("qishu", "desc")->get();
                    break;
                case "jx":
                    $result = LotteryResultJX::offset(($page_no - 1) * $limit)
                            ->take($limit)->orderBy("qishu", "desc")->get();
                    break;
                case "tj":
                    $result = LotteryResultTJ::offset(($page_no - 1) * $limit)
                            ->take($limit)->orderBy("qishu", "desc")->get();
                    break;
            }

            $response["data"] = $result;
            $response['message'] = "B5 History Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }  

    public function getB3BirthHistory(Request $request) {

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
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 100;
                
            switch ($g_type) {
                case "d3":
                    $result = LotteryResultD3::offset(($page_no - 1) * $limit)
                            ->take($limit)->orderBy("qishu", "desc")->get();
                    break;
                case "p3":
                    $result = LotteryResultP3::offset(($page_no - 1) * $limit)
                            ->take($limit)->orderBy("qishu", "desc")->get();
                    break;
                case "t3":
                    $result = LotteryResultT3::offset(($page_no - 1) * $limit)
                            ->take($limit)->orderBy("qishu", "desc")->get();
                    break;
            }

            $response["data"] = $result;
            $response['message'] = "B3 History Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getOtherBirthHistory(Request $request) {

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
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 100;
                
            switch ($g_type) {
                case "gd11":
                    $result = LotteryResultGD11::offset(($page_no - 1) * $limit)
                        ->orderBy("qishu", "desc")
                        ->take($limit)->get();
                    break;
                case "azxy10":
                    $result = LotteryResultAZXY10::offset(($page_no - 1) * $limit)
                        ->orderBy("qishu", "desc")
                        ->take($limit)->get();
                    break;
                case "cqsf":
                    $result = LotteryResultCQSF::offset(($page_no - 1) * $limit)
                        ->orderBy("qishu", "desc")
                        ->take($limit)->get();
                    break;
                case "gdsf":
                    $result = LotteryResultGDSF::offset(($page_no - 1) * $limit)
                        ->orderBy("qishu", "desc")
                        ->take($limit)->get();
                    break;
                case "gxsf":
                    $result = LotteryResultGXSF::offset(($page_no - 1) * $limit)
                        ->orderBy("qishu", "desc")
                        ->take($limit)->get();
                    break;
                case "tjsf":
                    $result = LotteryResultTJSF::offset(($page_no - 1) * $limit)
                        ->orderBy("qishu", "desc")
                        ->take($limit)->get();
                    break;
                case "bjpk":
                    $result = LotteryResultBJPK::offset(($page_no - 1) * $limit)
                        ->orderBy("qishu", "desc")
                        ->take($limit)->get();
                    break;
                case "xyft":
                    $result = LotteryResultXYFT::offset(($page_no - 1) * $limit)
                        ->orderBy("qishu", "desc")
                        ->take($limit)->get();
                    break;
            }

            $response["data"] = $result;
            $response['message'] = "Lottery Result Other Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 
    public function getTotalBetResult(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $current_date = Carbon::now('Asia/Hong_Kong')->format('Y-m-d');

            $request_data = $request->all();

            $s_time = $request_data["s_time"] ?? $current_date;

            $user = $request->user();

            $userid = $user["id"];

            $d3_today_result = Utils::getOneDayOrder($userid,$s_time,"D3");
            $p3_today_result = Utils::getOneDayOrder($userid,$s_time,"P3");
            $t3_today_result = Utils::getOneDayOrder($userid,$s_time,"T3");
            $cq_today_result = Utils::getOneDayOrder($userid,$s_time,"CQ");
            $tj_today_result = Utils::getOneDayOrder($userid,$s_time,"TJ");
            $jx_today_result = Utils::getOneDayOrder($userid,$s_time,"JX");
            $gxsf_today_result = Utils::getOneDayOrder($userid,$s_time,"GXSF");
            $gdsf_today_result = Utils::getOneDayOrder($userid,$s_time,"GDSF");
            $tjsf_today_result = Utils::getOneDayOrder($userid,$s_time,"TJSF");
            $gd11_today_result = Utils::getOneDayOrder($userid,$s_time,"GD11");
            $bjpk_today_result = Utils::getOneDayOrder($userid,$s_time,"BJPK");
            $bjkn_today_result = Utils::getOneDayOrder($userid,$s_time,"BJKN");
            $cqsf_today_result = Utils::getOneDayOrder($userid,$s_time,"CQSF");
            $xyft_today_result = Utils::getOneDayOrder($userid,$s_time,"XYFT");
            $ffc5_today_result = Utils::getOneDayOrder($userid,$s_time,"FFC5");
            $txssc_today_result = Utils::getOneDayOrder($userid,$s_time,"TXSSC");
            $twssc_today_result = Utils::getOneDayOrder($userid,$s_time,"TWSSC");
            $azxy5_today_result = Utils::getOneDayOrder($userid,$s_time,"AZXY5");
            $azxy10_today_result = Utils::getOneDayOrder($userid,$s_time,"AZXY10");

            $d3_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"D3","0");
            $p3_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"P3","0");
            $t3_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"T3","0");
            $cq_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"CQ","0");
            $tj_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"TJ","0");
            $jx_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"JX","0");
            $gxsf_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"GXSF","0");
            $gdsf_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"GDSF","0");
            $tjsf_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"TJSF","0");
            $gd11_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"GD11","0");
            $bjpk_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"BJPK","0");
            $bjkn_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"BJKN","0");
            $cqsf_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"CQSF","0");
            $xyft_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"XYFT","0");
            $ffc5_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"FFC5","0");
            $txssc_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"TXSSC","0");
            $twssc_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"TWSSC","0");
            $azxy5_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"AZXY5","0");
            $azxy10_today_result_status0 = Utils::getOneDayOrder($userid,$s_time,"AZXY10","0");


            $d3_win = Utils::getOneDayTotalWinByType($userid,$s_time,"D3");
            $p3_win = Utils::getOneDayTotalWinByType($userid,$s_time,"P3");
            $t3_win = Utils::getOneDayTotalWinByType($userid,$s_time,"T3");
            $cq_win = Utils::getOneDayTotalWinByType($userid,$s_time,"CQ");
            $tj_win = Utils::getOneDayTotalWinByType($userid,$s_time,"TJ");
            $jx_win = Utils::getOneDayTotalWinByType($userid,$s_time,"JX");
            $gxsf_win = Utils::getOneDayTotalWinByType($userid,$s_time,"GXSF");
            $gdsf_win = Utils::getOneDayTotalWinByType($userid,$s_time,"GDSF");
            $tjsf_win = Utils::getOneDayTotalWinByType($userid,$s_time,"TJSF");
            $gd11_win = Utils::getOneDayTotalWinByType($userid,$s_time,"GD11");
            $bjpk_win = Utils::getOneDayTotalWinByType($userid,$s_time,"BJPK");
            $bjkn_win = Utils::getOneDayTotalWinByType($userid,$s_time,"BJKN");
            $cqsf_win = Utils::getOneDayTotalWinByType($userid,$s_time,"CQSF");
            $xyft_win = Utils::getOneDayTotalWinByType($userid,$s_time,"XYFT");
            $ffc5_win = Utils::getOneDayTotalWinByType($userid,$s_time,"FFC5");
            $txssc_win = Utils::getOneDayTotalWinByType($userid,$s_time,"TXSSC");
            $twssc_win = Utils::getOneDayTotalWinByType($userid,$s_time,"TWSSC");
            $azxy5_win = Utils::getOneDayTotalWinByType($userid,$s_time,"AZXY5");
            $azxy10_win = Utils::getOneDayTotalWinByType($userid,$s_time,"AZXY10");

            $d3_array = array(
                "lottery_type" => "3D彩",
                "g_type" => "D3",
                "bet_money" => round($d3_today_result->bet_money, 2),
                "bet_money_0" => round($d3_today_result_status0->bet_money, 2),
                "win_money" => round($d3_win, 2),
            );

            $p3_array = array(
                "lottery_type" => "排列三",
                "g_type" => "P3",
                "bet_money" => round($p3_today_result->bet_money, 2),
                "bet_money_0" => round($p3_today_result_status0->bet_money, 2),
                "win_money" => round($p3_win, 2),
            );

            $t3_array = array(
                "lottery_type" => "上海时时乐",
                "g_type" => "T3",
                "bet_money" => round($t3_today_result->bet_money, 2),
                "bet_money_0" => round($t3_today_result_status0->bet_money, 2),
                "win_money" => round($t3_win, 2),
            );

            $cq_array = array(
                "lottery_type" => "重庆时时彩",
                "g_type" => "CQ",
                "bet_money" => round($cq_today_result->bet_money, 2),
                "bet_money_0" => round($cq_today_result_status0->bet_money, 2),
                "win_money" => round($cq_win, 2),
            );

            $jx_array = array(
                "lottery_type" => "新疆时时彩",
                "g_type" => "JX",
                "bet_money" => round($jx_today_result->bet_money, 2),
                "bet_money_0" => round($jx_today_result_status0->bet_money, 2),
                "win_money" => round($jx_win, 2),
            );

            $tj_array = array(
                "lottery_type" => "天津时时彩",
                "g_type" => "TJ",
                "bet_money" => round($tj_today_result->bet_money, 2),
                "bet_money_0" => round($tj_today_result_status0->bet_money, 2),
                "win_money" => round($tj_win, 2),
            );

            $gxsf_array = array(
                "lottery_type" => "广西十分彩",
                "g_type" => "GXSF",
                "bet_money" => round($gxsf_today_result->bet_money, 2),
                "bet_money_0" => round($gxsf_today_result_status0->bet_money, 2),
                "win_money" => round($gxsf_win, 2),
            );

            $gdsf_array = array(
                "lottery_type" => "广东十分彩",
                "g_type" => "GDSF",
                "bet_money" => round($gdsf_today_result->bet_money, 2),
                "bet_money_0" => round($gdsf_today_result_status0->bet_money, 2),
                "win_money" => round($gdsf_win, 2),
            );

            $tjsf_array = array(
                "lottery_type" => "天津十分彩",
                "g_type" => "TJSF",
                "bet_money" => round($tjsf_today_result->bet_money, 2),
                "bet_money_0" => round($tjsf_today_result_status0->bet_money, 2),
                "win_money" => round($tjsf_win, 2),
            );

            $cqsf_array = array(
                "lottery_type" => "重庆十分彩",
                "g_type" => "CQSF",
                "bet_money" => round($cqsf_today_result->bet_money, 2),
                "bet_money_0" => round($cqsf_today_result_status0->bet_money, 2),
                "win_money" => round($cqsf_win, 2),
            );

            $gd11_array = array(
                "lottery_type" => "广东十一选五",
                "g_type" => "GD11",
                "bet_money" => round($gd11_today_result->bet_money, 2),
                "bet_money_0" => round($gd11_today_result_status0->bet_money, 2),
                "win_money" => round($gd11_win, 2),
            );

            $bjpk_array = array(
                "lottery_type" => "北京PK拾",
                "g_type" => "BJPK",
                "bet_money" => round($bjpk_today_result->bet_money, 2),
                "bet_money_0" => round($bjpk_today_result_status0->bet_money, 2),
                "win_money" => round($bjpk_win, 2),
            );

            $xyft_array = array(
                "lottery_type" => "幸运飞艇",
                "g_type" => "XYFT",
                "bet_money" => round($xyft_today_result->bet_money, 2),
                "bet_money_0" => round($xyft_today_result_status0->bet_money, 2),
                "win_money" => round($xyft_win, 2),
            );

            $ffc5_array = array(
                "lottery_type" => "五分彩",
                "g_type" => "FFC5",
                "bet_money" => round($ffc5_today_result->bet_money, 2),
                "bet_money_0" => round($ffc5_today_result_status0->bet_money, 2),
                "win_money" => round($ffc5_win, 2),
            );

            $azxy5_array = array(
                "lottery_type" => "澳洲幸运5",
                "g_type" => "AZXY5",
                "bet_money" => round($azxy5_today_result->bet_money, 2),
                "bet_money_0" => round($azxy5_today_result_status0->bet_money, 2),
                "win_money" => round($azxy5_win, 2),
            );

            $azxy10_array = array(
                "lottery_type" => "澳洲幸运10",
                "g_type" => "AZXY10",
                "bet_money" => round($azxy10_today_result->bet_money, 2),
                "bet_money_0" => round($azxy10_today_result_status0->bet_money, 2),
                "win_money" => round($azxy10_win, 2),
            );

            $data = array();
            array_push($data, $d3_array);
            array_push($data, $p3_array);
            array_push($data, $t3_array);
            array_push($data, $cq_array);
            array_push($data, $jx_array);
            array_push($data, $tj_array);
            array_push($data, $gxsf_array);
            array_push($data, $gdsf_array);
            array_push($data, $tjsf_array);
            array_push($data, $cqsf_array);
            array_push($data, $gd11_array);
            array_push($data, $bjpk_array);
            array_push($data, $xyft_array);
            array_push($data, $ffc5_array);
            array_push($data, $azxy5_array);
            array_push($data, $azxy10_array);

            $response["data"] = $data;
            $response['message'] = "Total Bet Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 
    public function getSubBetResult(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [
                "g_type" => "required|string",
                "status" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $current_date = Carbon::now('Asia/Hong_Kong')->format('Y-m-d');

            $request_data = $request->all();

            $s_time = $request_data["s_time"] ?? $current_date;
            $g_type = $request_data["g_type"];
            $status = $request_data["status"];
            $page_no = $request_data["page"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $t_allmoney = 0;
            $t_sy = 0;
            $total = array();

            $user = $request->user();

            $userid = $user["id"];

            $result = DB::table("order_lottery as o")
                ->join("order_lottery_sub as o_sub", "o.order_num", "=", "o_sub.order_num");

            if ($status == "all") {
                $result = $result->where("o.Gtype", $g_type);
                $result = $result->whereBetween("o.bet_time", [$s_time." 00:00:00", $s_time." 23:59:59"]);
            } else {
                $result = $result->where("o.status", 0);
            }

            $result = $result->where("o.user_id", $userid);

            $result = $result->select(DB::raw("o.username,o.Gtype,o.lottery_number AS qishu,o.rtype_str,o.bet_time,o.order_num,o_sub.quick_type,o_sub.number,o_sub.bet_money AS bet_money_one,o_sub.fs, o.user_id, o_sub.bet_rate AS bet_rate_one,o_sub.is_win,o_sub.status, o_sub.id AS id,o_sub.win AS win_sub,o_sub.balance,o_sub.order_sub_num"));

            $count = $result->count();

            $result = $result->orderBy("o_sub.id", "desc")
                ->offset(($page_no - 1) * $limit)
                ->take($limit)
                ->get();

            foreach($result as $item) {

                $color = "#FFFFFF";
                $over  = "#EBEBEB";
                $out   = "#ffffff";
                $t_allmoney += $item->bet_money_one;

                $money_result = 0;
                if($item->is_win=="1"){
                    $t_sy= $t_sy + $item->win_sub + $item->fs - $item->bet_money_one;
                    $money_result = $item->win_sub + $item->fs-$item->bet_money_one;
                }elseif($item->is_win=="2"){
                    $t_sy+=$item->bet_money_one;
                    $money_result = $item->fs;
                }elseif($item->is_win=="0"){
                    $t_sy+=$item->fs-$item->bet_money_one;
                    $money_result = $item->fs-$item->bet_money_one;
                }

                if($item->is_win==1 || $item->is_win=="2"){
                    $color = "#FFE1E1";
                    $over  = "#FFE1E1";
                    $out   = "#FFE1E1";
                }

                $contentName = Utils::getName($item->number,$item->Gtype,$item->rtype_str,$item->quick_type);

                $item->content_name = $contentName;

                $item->money_result = round($money_result, 2);

                $item->lottery_name = Utils::getZhPageTitle($item->Gtype);

                $item->checked = false;

                $bet_rate = $item->bet_rate_one;

                if(strpos($bet_rate, ",")){
                    $bet_rate_array = explode(",", $bet_rate);
                    $item->bet_rate_one = $bet_rate_array[0];
                }
            }

            $total = array(
                "count" => $count,
                "t_allmoney" => $t_allmoney,
                "t_sy" => round($t_sy, 2)
            );

            $response["data"] = $result;
            $response["total_item"] = $total;
            $response['message'] = "Sub Bet Data fetched successfully!";
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
