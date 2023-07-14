<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Kabl;
use App\Models\MacaoKabl;
use App\Models\KaTan;
use App\Models\MacaoKatan;
use App\Models\Kakithe;
use App\Models\MacaoKakithe;
use App\Utils\Utils;
use Illuminate\Support\Facades\DB;

class AdminKablController extends Controller
{
    public function getPeriod(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $data = array();

            $ka_kithe = Kakithe::orderBy("nn", "desc")->get(["nn"]);

            foreach($ka_kithe as $item) {
                array_push($data, array("label" => "第".$item["nn"]."期", "value" => $item["nn"]));
            }

            $response["data"] = $data;
            $response['message'] = "Peroid data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSpecialCodeData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $assistant_data = array();
            $assitant_amount= 0;

            $kabl = Kabl::where("class1", $class1)
                ->where("class2", "特A")
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = KaTan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                if ($item["class3"] < 50) {
                    $color = Utils::ka_Color_s($item["class3"]);
                    if ($color === "红波") {
                        $color = "red";
                    }
                    if ($color === "蓝波") {
                        $color = "green";
                    }
                    if ($color === "绿波") {
                        $color = "blue";
                    }
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $main_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($main_data, array(
                            "class3" => $class3,
                            "color" => $color,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );
                } else {
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $assitant_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($assistant_data, array(
                            "class3" => $class3,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );                    
                }
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response["assistant_data"] = $assistant_data;
            $response["assistant_amount"] = $assitant_amount;
            $response['message'] = "special code data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getPositiveCodeData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $assistant_data = array();
            $assitant_amount= 0;

            $kabl = Kabl::where("class1", $class1)
                ->where("class2", "正A")
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = KaTan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                if ($item["class3"] < 50) {
                    $color = Utils::ka_Color_s($item["class3"]);
                    if ($color === "红波") {
                        $color = "red";
                    }
                    if ($color === "蓝波") {
                        $color = "green";
                    }
                    if ($color === "绿波") {
                        $color = "blue";
                    }
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $main_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($main_data, array(
                            "class3" => $class3,
                            "color" => $color,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );
                } else {
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $assitant_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($assistant_data, array(
                            "class3" => $class3,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );                    
                }
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response["assistant_data"] = $assistant_data;
            $response["assistant_amount"] = $assitant_amount;
            $response['message'] = "Positive code data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getPositiveCode16Data(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $kabl = Kabl::where("class1", $class1)
                ->where("class2", $class2)
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = KaTan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                $sum_m = $ka_tan["sum_m"] ?? 0;
                $main_amount += $sum_m;
                $rate = $item["rate"];
                $class3 = $item["class3"];
                array_push($main_data, array(
                        "class3" => $class3,
                        "rate" => $rate,
                        "sum_m" => $sum_m,
                        "profit" => 0,
                    )
                );
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response['message'] = "Positive code 1-6 data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getRegularCodeData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $assistant_data = array();
            $assitant_amount= 0;

            $kabl = Kabl::where("class1", $class1)
                ->where("class2", "正1特")
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = KaTan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                if ($item["class3"] < 50) {
                    $color = Utils::ka_Color_s($item["class3"]);
                    if ($color === "红波") {
                        $color = "red";
                    }
                    if ($color === "蓝波") {
                        $color = "green";
                    }
                    if ($color === "绿波") {
                        $color = "blue";
                    }
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $main_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($main_data, array(
                            "class3" => $class3,
                            "color" => $color,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );
                } else {
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $assitant_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($assistant_data, array(
                            "class3" => $class3,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );                    
                }
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response["assistant_data"] = $assistant_data;
            $response["assistant_amount"] = $assitant_amount;
            $response['message'] = "Regular code data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getPassData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $kabl = Kabl::where("class1", $class1)
                ->where("class2", $class2)
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = KaTan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                if ($item["class3"] < 50) {
                    $color = Utils::ka_Color_s($item["class3"]);
                    if ($color === "红波") {
                        $color = "red";
                    }
                    if ($color === "蓝波") {
                        $color = "green";
                    }
                    if ($color === "绿波") {
                        $color = "blue";
                    }
                }
                $sum_m = $ka_tan["sum_m"] ?? 0;
                $main_amount += $sum_m;
                $rate = $item["rate"];
                $class3 = $item["class3"];
                array_push($main_data, array(
                        "class3" => $class3,
                        "rate" => $rate,
                        "sum_m" => $sum_m,
                        "color" => $color ?? "",
                        "profit" => 0,
                    )
                );
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response['message'] = "Pass data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getEvenCodeData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $kabl = Kabl::where("class1", $class1)
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = KaTan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                $sum_m = $ka_tan["sum_m"] ?? 0;
                $main_amount += $sum_m;
                $rate = $item["rate"];
                $class3 = $item["class3"];
                array_push($main_data, array(
                        "class3" => $class3,
                        "rate" => $rate,
                        "sum_m" => $sum_m,
                        "profit" => 0,
                    )
                );
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response['message'] = "Even Code data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }  

    public function getOneXiaoCodeData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $kabl = Kabl::where("class1", $class1)
                ->where("class2", $class2)
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = KaTan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                $sum_m = $ka_tan["sum_m"] ?? 0;
                $main_amount += $sum_m;
                $rate = $item["rate"];
                $class3 = $item["class3"];
                array_push($main_data, array(
                        "class3" => $class2 == "尾数" || $class1 == "尾数连" ? $class3."尾" : $class3,
                        "rate" => $rate,
                        "sum_m" => $sum_m,
                        "profit" => 0,
                    )
                );
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response['message'] = "One Xiao data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }


    public function getMacaoPeriod(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $data = array();

            $ka_kithe = MacaoKakithe::orderBy("nn", "desc")->get(["nn"]);

            foreach($ka_kithe as $item) {
                array_push($data, array("label" => "第".$item["nn"]."期", "value" => $item["nn"]));
            }

            $response["data"] = $data;
            $response['message'] = "Peroid data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoSpecialCodeData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $assistant_data = array();
            $assitant_amount= 0;

            $kabl = MacaoKabl::where("class1", $class1)
                ->where("class2", "特A")
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                if ($item["class3"] < 50) {
                    $color = Utils::ka_Color_s($item["class3"]);
                    if ($color === "红波") {
                        $color = "red";
                    }
                    if ($color === "蓝波") {
                        $color = "green";
                    }
                    if ($color === "绿波") {
                        $color = "blue";
                    }
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $main_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($main_data, array(
                            "class3" => $class3,
                            "color" => $color,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );
                } else {
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $assitant_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($assistant_data, array(
                            "class3" => $class3,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );                    
                }
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response["assistant_data"] = $assistant_data;
            $response["assistant_amount"] = $assitant_amount;
            $response['message'] = "special code data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoPositiveCodeData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $assistant_data = array();
            $assitant_amount= 0;

            $kabl = MacaoKabl::where("class1", $class1)
                ->where("class2", "正A")
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                if ($item["class3"] < 50) {
                    $color = Utils::ka_Color_s($item["class3"]);
                    if ($color === "红波") {
                        $color = "red";
                    }
                    if ($color === "蓝波") {
                        $color = "green";
                    }
                    if ($color === "绿波") {
                        $color = "blue";
                    }
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $main_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($main_data, array(
                            "class3" => $class3,
                            "color" => $color,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );
                } else {
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $assitant_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($assistant_data, array(
                            "class3" => $class3,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );                    
                }
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response["assistant_data"] = $assistant_data;
            $response["assistant_amount"] = $assitant_amount;
            $response['message'] = "Positive code data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoPositiveCode16Data(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $kabl = MacaoKabl::where("class1", $class1)
                ->where("class2", $class2)
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                $sum_m = $ka_tan["sum_m"] ?? 0;
                $main_amount += $sum_m;
                $rate = $item["rate"];
                $class3 = $item["class3"];
                array_push($main_data, array(
                        "class3" => $class3,
                        "rate" => $rate,
                        "sum_m" => $sum_m,
                        "profit" => 0,
                    )
                );
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response['message'] = "Positive code 1-6 data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoRegularCodeData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $assistant_data = array();
            $assitant_amount= 0;

            $kabl = MacaoKabl::where("class1", $class1)
                ->where("class2", "正1特")
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                if ($item["class3"] < 50) {
                    $color = Utils::ka_Color_s($item["class3"]);
                    if ($color === "红波") {
                        $color = "red";
                    }
                    if ($color === "蓝波") {
                        $color = "green";
                    }
                    if ($color === "绿波") {
                        $color = "blue";
                    }
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $main_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($main_data, array(
                            "class3" => $class3,
                            "color" => $color,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );
                } else {
                    $sum_m = $ka_tan["sum_m"] ?? 0;
                    $assitant_amount += $sum_m;
                    $rate = $item["rate"];
                    $class3 = $item["class3"];
                    array_push($assistant_data, array(
                            "class3" => $class3,
                            "rate" => $rate,
                            "sum_m" => $sum_m,
                            "profit" => 0,
                        )
                    );                    
                }
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response["assistant_data"] = $assistant_data;
            $response["assistant_amount"] = $assitant_amount;
            $response['message'] = "Regular code data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoPassData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $kabl = MacaoKabl::where("class1", $class1)
                ->where("class2", $class2)
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                if ($item["class3"] < 50) {
                    $color = Utils::ka_Color_s($item["class3"]);
                    if ($color === "红波") {
                        $color = "red";
                    }
                    if ($color === "蓝波") {
                        $color = "green";
                    }
                    if ($color === "绿波") {
                        $color = "blue";
                    }
                }
                $sum_m = $ka_tan["sum_m"] ?? 0;
                $main_amount += $sum_m;
                $rate = $item["rate"];
                $class3 = $item["class3"];
                array_push($main_data, array(
                        "class3" => $class3,
                        "rate" => $rate,
                        "sum_m" => $sum_m,
                        "color" => $color ?? "",
                        "profit" => 0,
                    )
                );
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response['message'] = "Pass data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getMacaoEvenCodeData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $kabl = MacaoKabl::where("class1", $class1)
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                $sum_m = $ka_tan["sum_m"] ?? 0;
                $main_amount += $sum_m;
                $rate = $item["rate"];
                $class3 = $item["class3"];
                array_push($main_data, array(
                        "class3" => $class3,
                        "rate" => $rate,
                        "sum_m" => $sum_m,
                        "profit" => 0,
                    )
                );
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response['message'] = "Even Code data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }  

    public function getMacaoOneXiaoCodeData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $period = $request_data["period"];

            $main_data = array();
            $main_amount = 0;

            $kabl = MacaoKabl::where("class1", $class1)
                ->where("class2", $class2)
                ->get(["class1", "class3", "rate"]);

            foreach($kabl as $item) {
                $ka_tan = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("Kithe", $period)
                    ->where("class1", $item["class1"])
                    ->where("class3", $item["class3"])
                    ->where("lx", 0)
                    ->first();
                $sum_m = $ka_tan["sum_m"] ?? 0;
                $main_amount += $sum_m;
                $rate = $item["rate"];
                $class3 = $item["class3"];
                array_push($main_data, array(
                        "class3" => $class2 == "尾数" || $class1 == "尾数连" ? $class3."尾" : $class3,
                        "rate" => $rate,
                        "sum_m" => $sum_m,
                        "profit" => 0,
                    )
                );
            }

            $response["main_data"] = $main_data;
            $response["main_amount"] = $main_amount;
            $response['message'] = "One Xiao data fetched successfully!";
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
