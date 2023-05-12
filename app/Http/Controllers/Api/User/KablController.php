<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Kabl;
use App\Models\MacaoKabl;

class KablController extends Controller
{
    public function getKablData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
                'class2' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];

            if ($class1 == "过关") {

                $ka_bl = Kabl::where("class1", $class1)->get();

            } else {

                $ka_bl = Kabl::where("class1", $class1)->where("class2", $class2)->get();

            }

            $response['data'] = $ka_bl;
            $response['message'] = 'Lottery Rate Data fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoKablData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
                'class2' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];

            if ($class1 == "过关") {

                $ka_bl = MacaoKabl::where("class1", $class1)->get();

            } else {

                $ka_bl = MacaoKabl::where("class1", $class1)
                        ->where("class2", $class2)->get();

            }

            $response['data'] = $ka_bl;
            $response['message'] = 'Lottery Rate Data fetched successfully!';
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
