<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Kakithe;
use App\Models\MacaoKakithe;
use App\Models\KasxNumber;
use App\Models\Yakithe;
use App\Models\MacaoYakithe;
use App\Utils\Utils;
use App\Models\Kabl;
use App\Models\MacaoKabl;

class YakitheController extends Controller
{
    public function getYakitheItemById(Request $request) {

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

            $ya_kithe = Yakithe::find($id);

            $response['data'] = $ya_kithe;
            $response['message'] = "Yakithe Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getYakitheAll(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $ya_kithe = Yakithe::orderBy("nn", "asc")->get();

            $response['data'] = $ya_kithe;
            $response['message'] = "Yakithe Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateYakithe(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $nn = $request_data["nn"];
            $nd = $request_data["nd"];
            $kitm = $request_data["kitm"];
            $kizt = $request_data["kizt"];
            $kizm6 = $request_data["kizm6"];
            $kigg = $request_data["kigg"];
            $kilm = $request_data["kilm"];
            $kisx = $request_data["kisx"];
            $kibb = $request_data["kibb"];
            $kiws = $request_data["kiws"];
            $zfbdate = $request_data["zfbdate"];
            $kitm1 = $request_data["kitm1"];
            $kizt1 = $request_data["kizt1"];
            $kizm1 = $request_data["kizm1"];
            $kizm61 = $request_data["kizm61"];
            $kigg1 = $request_data["kigg1"];
            $kilm1 = $request_data["kilm1"];
            $kisx1 = $request_data["kisx1"];
            $kibb1 = $request_data["kibb1"];
            $kiws1 = $request_data["kiws1"];
            $zfbdate1 = $request_data["zfbdate1"];
            $zfb = $request_data["zfb"];
            $best = $request_data["best"];

            $ya_kithe = Yakithe::find($id);

            $ya_kithe->nn = $nn;
            $ya_kithe->nd = $nd;
            $ya_kithe->kitm = $kitm;
            $ya_kithe->kizt = $kizt;
            $ya_kithe->kizm6 = $kizm6;
            $ya_kithe->kigg = $kigg;
            $ya_kithe->kilm = $kilm;
            $ya_kithe->kisx = $kisx;
            $ya_kithe->kibb = $kibb;
            $ya_kithe->kiws = $kiws;
            $ya_kithe->zfbdate = $zfbdate;
            $ya_kithe->kitm1 = $kitm1;
            $ya_kithe->kizt1 = $kizt1;
            $ya_kithe->kizm1 = $kizm1;
            $ya_kithe->kizm61 = $kizm61;
            $ya_kithe->kigg1 = $kigg1;
            $ya_kithe->kilm1 = $kilm1;
            $ya_kithe->kisx1 = $kisx1;
            $ya_kithe->kibb1 = $kibb1;
            $ya_kithe->kiws1 = $kiws1;
            $ya_kithe->zfb = $zfb;
            $ya_kithe->best = $best;
            $ya_kithe->zfbdate1 = $zfbdate1;

            if ($ya_kithe->save()) {
                $response["data"] = $ya_kithe;
                $response['message'] = "Yakithe data updated successfully!";
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            }
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoYakitheItemById(Request $request) {

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

            $ya_kithe = MacaoYakithe::find($id);

            $response['data'] = $ya_kithe;
            $response['message'] = "MacaoYakithe Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoYakitheAll(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $ya_kithe = MacaoYakithe::orderBy("nn", "asc")->get();

            $response['data'] = $ya_kithe;
            $response['message'] = "MacaoYakithe Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoYakithe(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $nn = $request_data["nn"];
            $nd = $request_data["nd"];
            $kitm = $request_data["kitm"];
            $kizt = $request_data["kizt"];
            $kizm6 = $request_data["kizm6"];
            $kigg = $request_data["kigg"];
            $kilm = $request_data["kilm"];
            $kisx = $request_data["kisx"];
            $kibb = $request_data["kibb"];
            $kiws = $request_data["kiws"];
            $zfbdate = $request_data["zfbdate"];
            $kitm1 = $request_data["kitm1"];
            $kizt1 = $request_data["kizt1"];
            $kizm1 = $request_data["kizm1"];
            $kizm61 = $request_data["kizm61"];
            $kigg1 = $request_data["kigg1"];
            $kilm1 = $request_data["kilm1"];
            $kisx1 = $request_data["kisx1"];
            $kibb1 = $request_data["kibb1"];
            $kiws1 = $request_data["kiws1"];
            $zfbdate1 = $request_data["zfbdate1"];
            $zfb = $request_data["zfb"];
            $best = $request_data["best"];

            $ya_kithe = MacaoYakithe::find($id);

            $ya_kithe->nn = $nn;
            $ya_kithe->nd = $nd;
            $ya_kithe->kitm = $kitm;
            $ya_kithe->kizt = $kizt;
            $ya_kithe->kizm6 = $kizm6;
            $ya_kithe->kigg = $kigg;
            $ya_kithe->kilm = $kilm;
            $ya_kithe->kisx = $kisx;
            $ya_kithe->kibb = $kibb;
            $ya_kithe->kiws = $kiws;
            $ya_kithe->zfbdate = $zfbdate;
            $ya_kithe->kitm1 = $kitm1;
            $ya_kithe->kizt1 = $kizt1;
            $ya_kithe->kizm1 = $kizm1;
            $ya_kithe->kizm61 = $kizm61;
            $ya_kithe->kigg1 = $kigg1;
            $ya_kithe->kilm1 = $kilm1;
            $ya_kithe->kisx1 = $kisx1;
            $ya_kithe->kibb1 = $kibb1;
            $ya_kithe->kiws1 = $kiws1;
            $ya_kithe->zfb = $zfb;
            $ya_kithe->best = $best;
            $ya_kithe->zfbdate1 = $zfbdate1;

            if ($ya_kithe->save()) {
                $response["data"] = $ya_kithe;
                $response['message'] = "MacaoYakithe data updated successfully!";
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            }
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }       
}
