<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\LotteryUserConfig;

class LotteryConfigController extends Controller
{
    public function getLotteryUserConfig(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [
                // "user_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            // $user_name = $request_data["user_name"];

            $user = $request->user();

            $result = LotteryUserConfig::where("username", $user["UserName"])->first();

            if (!isset($result)) {
                $result = LotteryUserConfig::query()->first();
            }

            $response["data"] = $result;
            $response['message'] = "Lottery User Config Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
    public function addLotteryUserConfig(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [
                // "user_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            // $user_name = $request_data["user_name"];

            $user = $request->user();

            $result = LotteryUserConfig::where("username", $user["UserName"])->first();

            if (!isset($result)) {
                $result = LotteryUserConfig::first();
                $new_data = [
                    "username" => $user["UserName"],
                    "userid" => $user["id"],
                    "cq_lower_bet" => $result["cq_lower_bet"],
                    "cq_max_bet" => $result["cq_max_bet"],
                    "cq_bet" => $result["cq_bet"],
                    "cq_bet_reb" => $result["cq_bet_reb"],
                    "jx_lower_bet" => $result["jx_lower_bet"],
                    "jx_bet" => $result["jx_bet"],
                    "jx_bet_reb" => $result["jx_bet_reb"],
                    "tj_bet" => $result["tj_bet"],
                    "tj_bet_reb" => $result["tj_bet_reb"],
                    "gdsf_lower_bet" => $result["gdsf_lower_bet"],
                    "gdsf_bet" => $result["gdsf_bet"],
                    "gdsf_bet_reb" => $result["gdsf_bet_reb"],
                    "gxsf_lower_bet" => $result["gxsf_lower_bet"],
                    "gxsf_bet" => $result["gxsf_bet"],
                    "gxsf_bet_reb" => $result["gxsf_bet_reb"],
                    "tjsf_lower_bet" => $result["tjsf_lower_bet"],
                    "tjsf_bet" => $result["tjsf_bet"],
                    "tjsf_bet_reb" => $result["tjsf_bet_reb"],
                    "bjpk_lower_bet" => $result["bjpk_lower_bet"],
                    "bjpk_bet" => $result["bjpk_bet"],
                    "bjpk_bet_reb" => $result["bjpk_bet_reb"],
                    "xyft_lower_bet" => $result["xyft_lower_bet"],
                    "xyft_bet" => $result["xyft_bet"],
                    "xyft_bet_reb" => $result["xyft_bet_reb"],
                    "ffc5_lower_bet" => $result["ffc5_lower_bet"],
                    "ffc5_bet" => $result["ffc5_bet"],
                    "ffc5_bet_reb" => $result["ffc5_bet_reb"],
                    "txssc_lower_bet" => $result["txssc_lower_bet"],
                    "txssc_bet" => $result["txssc_bet"],
                    "txssc_bet_reb" => $result["txssc_bet_reb"],
                    "twssc_lower_bet" => $result["twssc_lower_bet"],
                    "twssc_bet" => $result["twssc_bet"],
                    "twssc_bet_reb" => $result["twssc_bet_reb"],
                    "azxy5_lower_bet" => $result["azxy5_lower_bet"],
                    "azxy5_bet" => $result["azxy5_bet"],
                    "azxy5_bet_reb" => $result["azxy5_bet_reb"],
                    "azxy10_lower_bet" => $result["azxy10_lower_bet"],
                    "azxy10_bet" => $result["azxy10_bet"],
                    "azxy10_bet_reb" => $result["azxy10_bet_reb"],
                    "bjkn_lower_bet" => $result["bjkn_lower_bet"],
                    "bjkn_bet" => $result["bjkn_bet"],
                    "bjkn_bet_reb" => $result["bjkn_bet_reb"],
                    "gd11_lower_bet" => $result["gd11_lower_bet"],
                    "gd11_bet" => $result["gd11_bet"],
                    "gd11_bet_reb" => $result["gd11_bet_reb"],
                    "t3_lower_bet" => $result["t3_lower_bet"],
                    "t3_bet" => $result["t3_bet"],
                    "t3_bet_reb" => $result["t3_bet_reb"],
                    "d3_lower_bet" => $result["d3_lower_bet"],
                    "d3_bet" => $result["d3_bet"],
                    "d3_bet_reb" => $result["d3_bet_reb"],
                    "p3_lower_bet" => $result["p3_lower_bet"],
                    "p3_bet" => $result["p3_bet"],
                    "p3_bet_reb" => $result["p3_bet_reb"],
                    "cqsf_lower_bet" => $result["cqsf_lower_bet"],
                    "cqsf_bet" => $result["cqsf_bet"],
                    "cqsf_bet_reb" => $result["cqsf_bet_reb"],
                    "jx_max_bet" => $result["jx_max_bet"],
                    "tj_max_bet" => $result["tj_max_bet"],
                    "gdsf_max_bet" => $result["gdsf_max_bet"],
                    "gxsf_max_bet" => $result["gxsf_max_bet"],
                    "tjsf_max_bet" => $result["tjsf_max_bet"],
                    "bjpk_max_bet" => $result["bjpk_max_bet"],
                    "xyft_max_bet" => $result["xyft_max_bet"],
                    "ffc5_max_bet" => $result["ffc5_max_bet"],
                    "txssc_max_bet" => $result["txssc_max_bet"],
                    "twssc_max_bet" => $result["twssc_max_bet"],
                    "azxy5_max_bet" => $result["azxy5_max_bet"],
                    "azxy10_max_bet" => $result["azxy10_max_bet"],
                    "bjkn_max_bet" => $result["bjkn_max_bet"],
                    "gd11_max_bet" => $result["gd11_max_bet"],
                    "t3_max_bet" => $result["t3_max_bet"],
                    "d3_max_bet" => $result["d3_max_bet"],
                    "p3_max_bet" => $result["p3_max_bet"],
                    "cqsf_max_bet" => $result["cqsf_max_bet"],
                ];
                LotteryUserConfig::create($new_data);
            }

            $response['message'] = "Lottery User Config Data saved successfully!";
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
