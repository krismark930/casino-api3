<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\OddsLottery;

class AdminOddsGDSFController extends Controller
{

    public function getOdds(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {   

            $rules = [
                "lottery_type" => "required|string",
                "sub_type" => "required|string"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $lottery_type = $request_data["lottery_type"];
            $sub_type = $request_data["sub_type"];
            $ball_type = $request_data["ball_type"] ?? "";

            $result = OddsLottery::where("lottery_type", $lottery_type)
                ->where("sub_type", $sub_type);

            if ($ball_type !== "") {
                $result = $result->where("ball_type", $ball_type);
            }

            $result = $result->first();

            $response['data'] = $result;
            $response['message'] = "GDSF Odds Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }   

    public function saveOdds(Request $request) {

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

            $result = OddsLottery::find($id);

            $result->h1 = $request_data["h1"];
            $result->h2 = $request_data["h2"];
            $result->h3 = $request_data["h3"];
            $result->h4 = $request_data["h4"];
            $result->h5 = $request_data["h5"];
            $result->h6 = $request_data["h6"];
            $result->h7 = $request_data["h7"];
            $result->h8 = $request_data["h8"];
            $result->h9 = $request_data["h9"];
            $result->h10 = $request_data["h10"];
            $result->h11 = $request_data["h11"];
            $result->h12 = $request_data["h12"];
            $result->h13 = $request_data["h13"];
            $result->h14 = $request_data["h14"];
            $result->h15 = $request_data["h15"];
            $result->h16 = $request_data["h16"];
            $result->h17 = $request_data["h17"];
            $result->h18 = $request_data["h18"];
            $result->h19 = $request_data["h19"];
            $result->h20 = $request_data["h20"];
            $result->h21 = $request_data["h21"];
            $result->h22 = $request_data["h22"];
            $result->h23 = $request_data["h23"];
            $result->h24 = $request_data["h24"];
            $result->h25 = $request_data["h25"];
            $result->h26 = $request_data["h26"];
            $result->h27 = $request_data["h27"];
            $result->h28 = $request_data["h28"];
            $result->h29 = $request_data["h29"];
            $result->h30 = $request_data["h30"];
            $result->h31 = $request_data["h31"];
            $result->h32 = $request_data["h32"];
            $result->h33 = $request_data["h33"];
            $result->h34 = $request_data["h34"];
            $result->h35 = $request_data["h35"];
            $result->h36 = $request_data["h36"];

            $result->save();

            $response['message'] = "GDSF Odds Data updated successfully!";
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
