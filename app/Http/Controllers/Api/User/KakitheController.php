<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Kakithe;
use App\Models\MacaoKakithe;
use App\Models\KasxNumber;
use App\Models\NewMacaoKakithe;
use App\Utils\Utils;
use Carbon\Carbon;

class KakitheController extends Controller
{
    public function getCurrentGameStatus(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];

            $ka_kithe = Kakithe::where("na", 0)->first();

            // if ($ka_kithe["kitm"] == 0 || $ka_kithe["zfb"] == 0) {
            //     $response['message'] = '目前没有开盘!';
            //     $response['data'] = 0;
            //     $response['result_time'] = $ka_kithe["kitm1"];
            // } else {
            //     $response['data'] = 1;
            //     $response['message'] = '目前有开盘!';
            //     $response['result_time'] = $ka_kithe["kitm1"];
            // }

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $e_time = Carbon::parse($ka_kithe["nd"]);
            $s_time = Carbon::parse($current_time);
            $ka_kithe["diff_time"] = $e_time->diffInSeconds($s_time) * 1000;

            $response['data'] = $ka_kithe;
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getGameVersion(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $Current_Kithe_Num = Utils::getCurrentKitheNum();

            $response['data'] = $Current_Kithe_Num;
            $response['message'] = "Before Game Version fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getGameResult(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $ka_kithe = Kakithe::where("score", 1)->orderBy("nn", "desc")->first();

            $data = array($ka_kithe["n1"], $ka_kithe["n2"], $ka_kithe["n3"], $ka_kithe["n4"], $ka_kithe["n5"], $ka_kithe["n6"], $ka_kithe["na"]);

            $response['order_number'] = $ka_kithe["nn"];
            $response['data'] = $data;
            $response['message'] = "Game Result fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getBirthHistory(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $offset = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $ka_sxnumber = KasxNumber::orderBy("ID", "asc")->take(12)->get();

            $ka_kithe = Kakithe::where("score", 1)
                ->orderBy("nn", "desc")
                ->skip(($offset-1) * $limit)
                ->take($limit)
                ->get();

            $data = array();

            foreach($ka_kithe as $item) {
                $temp = array();
                $temp["version"] = $item["nn"];
                $temp["result"] = array($item["n1"], $item["n2"], $item["n3"], $item["n4"], $item["n5"], $item["n6"], $item["na"]);
                $temp["animal"] = array();

                foreach($temp["result"] as $number) {
                    foreach($ka_sxnumber as $sx_item) {
                        $m_number_array = explode(",", $sx_item["m_number"]);
                        if (in_array($number, $m_number_array)) {
                            array_push($temp["animal"], $sx_item["sx"]);
                        }
                    }
                }

                array_push($data, $temp);
            }

            $response['data'] = $data;
            $response['message'] = "Game Result fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }


    public function getMacaoCurrentGameStatus(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];

            $ka_kithe = MacaoKakithe::where("na", 0)->first();

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $e_time = Carbon::parse($ka_kithe["nd"]);
            $s_time = Carbon::parse($current_time);
            $ka_kithe["diff_time"] = $e_time->diffInSeconds($s_time) * 1000;

            $response['data'] = $ka_kithe;
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }


    public function getNewMacaoCurrentGameStatus(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];

            $ka_kithe = NewMacaoKakithe::where("na", 0)->first();

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $e_time = Carbon::parse($ka_kithe["nd"]);
            $s_time = Carbon::parse($current_time);
            $ka_kithe["diff_time"] = $e_time->diffInSeconds($s_time) * 1000;

            $response['data'] = $ka_kithe;
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoGameVersion(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $Current_Kithe_Num = Utils::getCurrentMacaoKitheNum();

            $response['data'] = $Current_Kithe_Num;
            $response['message'] = "Before Game Version fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getNewMacaoGameVersion(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $Current_Kithe_Num = Utils::getNewCurrentMacaoKitheNum();

            $response['data'] = $Current_Kithe_Num;
            $response['message'] = "Before Game Version fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoGameResult(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $ka_kithe = MacaoKakithe::where("score", 1)->orderBy("nn", "desc")->first();

            $data = array($ka_kithe["n1"], $ka_kithe["n2"], $ka_kithe["n3"], $ka_kithe["n4"], $ka_kithe["n5"], $ka_kithe["n6"], $ka_kithe["na"]);

            $response['order_number'] = $ka_kithe["nn"];
            $response['data'] = $data;
            $response['message'] = "Game Result fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getNewMacaoGameResult(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $ka_kithe = NewMacaoKakithe::where("score", 1)->orderBy("nn", "desc")->first();

            $data = array($ka_kithe["n1"], $ka_kithe["n2"], $ka_kithe["n3"], $ka_kithe["n4"], $ka_kithe["n5"], $ka_kithe["n6"], $ka_kithe["na"]);

            $response['order_number'] = $ka_kithe["nn"];
            $response['data'] = $data;
            $response['message'] = "Game Result fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoBirthHistory(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $offset = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $ka_sxnumber = KasxNumber::orderBy("ID", "asc")->take(12)->get();

            $ka_kithe = MacaoKakithe::where("score", 1)
                ->orderBy("nn", "desc")
                ->skip(($offset-1) * $limit)
                ->take($limit)
                ->get();

            $data = array();

            foreach($ka_kithe as $item) {
                $temp = array();
                $temp["version"] = $item["nn"];
                $temp["result"] = array($item["n1"], $item["n2"], $item["n3"], $item["n4"], $item["n5"], $item["n6"], $item["na"]);
                $temp["animal"] = array();

                foreach($temp["result"] as $number) {
                    foreach($ka_sxnumber as $sx_item) {
                        $m_number_array = explode(",", $sx_item["m_number"]);
                        if (in_array($number, $m_number_array)) {
                            array_push($temp["animal"], $sx_item["sx"]);
                        }
                    }
                }

                array_push($data, $temp);
            }

            $response['data'] = $data;
            $response['message'] = "Game Result fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getNewMacaoBirthHistory(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $offset = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $ka_sxnumber = KasxNumber::orderBy("ID", "asc")->take(12)->get();

            $ka_kithe = NewMacaoKakithe::where("score", 1)
                ->orderBy("nn", "desc")
                ->skip(($offset-1) * $limit)
                ->take($limit)
                ->get();

            $data = array();

            foreach($ka_kithe as $item) {
                $temp = array();
                $temp["version"] = $item["nn"];
                $temp["result"] = array($item["n1"], $item["n2"], $item["n3"], $item["n4"], $item["n5"], $item["n6"], $item["na"]);
                $temp["animal"] = array();

                foreach($temp["result"] as $number) {
                    foreach($ka_sxnumber as $sx_item) {
                        $m_number_array = explode(",", $sx_item["m_number"]);
                        if (in_array($number, $m_number_array)) {
                            array_push($temp["animal"], $sx_item["sx"]);
                        }
                    }
                }

                array_push($data, $temp);
            }

            $response['data'] = $data;
            $response['message'] = "Game Result fetched successfully!";
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
