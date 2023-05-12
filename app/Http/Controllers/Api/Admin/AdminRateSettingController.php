<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Kabl;
use App\Models\MacaoKabl;
use App\Utils\Utils;

class AdminRateSettingController extends Controller
{
    public function getSpecialCodeRate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $class2= $request_data["class2"];

            $main_data = array();

            $assistant_data = array();

            $kabl = Kabl::where("class1", $class1)
                ->where("class2", $class2)
                ->get();

            $array1 = array();
            $array2 = array();
            $array3 = array();
            $array4 = array();

            foreach($kabl as $item) {
                $item["checked"] = false;
                if ($item["class3"] < 50) {
                    $color = Utils::ka_Color_s($item["class3"]);
                    if ($color === "红波") {
                        $item["color"] = "red";
                    }
                    if ($color === "蓝波") {
                        $item["color"] = "green";
                    }
                    if ($color === "绿波") {
                        $item["color"] = "blue";
                    }
                    array_push($main_data, $item);
                } else {
                    if ($item["class3"] == "单" || $item["class3"] == "大" || $item["class3"] == "合单"|| $item["class3"] == "红波" || $item["class3"] == "蓝波" || $item["class3"] == "总单" || $item["class3"] == "总双" || $item["class3"] == "总大" || $item["class3"] == "总小") {
                        array_push($array1, $item);
                    } else if($item["class3"] == "双" || $item["class3"] == "小" || $item["class3"] == "合双" || $item["class3"] == "绿波") {
                        array_push($array2, $item);
                    } else if($item["class3"] == "家禽" || $item["class3"] == "野兽" || $item["class3"] == "尾大" || $item["class3"] == "尾小"|| $item["class3"] == "大单" || $item["class3"] == "合大" || $item["class3"] == "合小") {
                        array_push($array3, $item);
                    } else if($item["class3"] == "小单" || $item["class3"] == "大双" || $item["class3"] == "小双") {
                        array_push($array4, $item);
                    }
                }
            }

            array_push($array2, array("buttons" => array('提交', '重置')));

            array_push($assistant_data, $array1);
            array_push($assistant_data, $array2);
            array_push($assistant_data, $array3);
            array_push($assistant_data, $array4);

            $response["assistant_data"] = $assistant_data;
            $response["main_data"] = $main_data;
            $response['message'] = "special code Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getPositive16Rate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];

            $main_data = array();

            $kabl = Kabl::where("class1", $class1)
                ->get();

            $array1 = array();
            $array2 = array();
            $array3 = array();
            $array4 = array();
            $array5 = array();
            $array6 = array();

            foreach($kabl as $item) {
                $item["checked"] = false;
                if ($item["class2"] == "正码1" ) {
                    array_push($array1, $item);
                } else if ($item["class2"] == "正码2") {
                    array_push($array2, $item);
                } else if ($item["class2"] == "正码3") {
                    array_push($array3, $item);
                } else if ($item["class2"] == "正码4") {
                    array_push($array4, $item);
                } else if ($item["class2"] == "正码5") {
                    array_push($array5, $item);
                } else if ($item["class2"] == "正码6") {
                    array_push($array6, $item);
                }
            }

            array_push($main_data, $array1);
            array_push($main_data, $array2);
            array_push($main_data, $array3);
            array_push($main_data, $array4);
            array_push($main_data, $array5);
            array_push($main_data, $array6);

            $response["main_data"] = $main_data;
            $response['message'] = "Positive 1-6 Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getConsecutiveCodeRate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];

            $main_data = array();

            $kabl = Kabl::where("class1", $class1)
                ->get();

            $array1 = array();
            $array2 = array();
            $array3 = array();
            $array4 = array();
            $array5 = array();
            $array6 = array();

            foreach($kabl as $item) {
                $item["checked"] = false;
                if ($item["class2"] == "二全中" ) {
                    $array1 = $item;
                } else if ($item["class2"] == "二中特") {
                    array_push($array2, $item);
                } else if ($item["class2"] == "特串") {
                    $array3 = $item;
                } else if ($item["class2"] == "三全中") {
                    $array4 = $item;
                } else if ($item["class2"] == "三中二") {
                    array_push($array5, $item);
                } else if ($item["class2"] == "四中一") {
                    $array6 = $item;
                }
            }

            array_push($main_data, $array1);
            array_push($main_data, array("class2" => "二中特", "childs" => $array2, "gold" => $array2[0]["gold"]));
            array_push($main_data, $array3);
            array_push($main_data, $array4);
            array_push($main_data, array("class2" => "三中二", "childs" => $array5, "gold" => $array5[0]["gold"]));
            array_push($main_data, $array6);

            $response["main_data"] = $main_data;
            $response['message'] = "Consecutive Code Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getHalfWaveRate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];

            $kabl = Kabl::where("class1", $class1)
                ->get();

            foreach($kabl as $item) {
                $item["checked"] = false;
            }

            $response["main_data"] = $kabl;
            $response['message'] = "Half Wave Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSpecialRate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $class2= $request_data["class2"];

            $kabl = Kabl::where("class1", $class1)->where("class2", $class2)->get();

            foreach($kabl as $item) {
                $item["checked"] = false;
                $item["rate"] = round($item["rate"], 2);
            }

            $response["main_data"] = $kabl;
            $response['message'] = "Special Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

    public function getOneXiaoRate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $kabl = Kabl::where("class2", "一肖")->orWhere("class2", "正特尾数")->get();

            foreach($kabl as $item) {
                $item["checked"] = false;
            }

            $response["main_data"] = $kabl;
            $response['message'] = "One Xiao Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }        

    public function updateRatePlus(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
                "class3" => "required|string",
                "value" => "required|numeric"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $class2= $request_data["class2"];
            $class3= $request_data["class3"];
            $value = $request_data["value"];
            $class3_array = explode(",", $class3);

            foreach($class3_array as $class3_item) {
                $ka_bl = Kabl::where("class1", $class1)->where("class2", $class2)->where("class3", (int)$class3_item)->first();
                $rate = $ka_bl["rate"] + $value;
                $q1 = Kabl::where("class1", $class1)->where("class2", $class2)->where("class3", (int)$class3_item)->update(["rate" => $rate]);
            }

            $response['message'] = "Plus Rate data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function upateRateOther(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
                "class3" => "required|string",
                "rate" => "required|numeric"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $class2= $request_data["class2"];
            $class3= $request_data["class3"];
            $rate = $request_data["rate"];
            $class3_array = explode(",", $class3);

            foreach($class3_array as $class3_item) {
                $q1 = Kabl::where("class1", $class1)->where("class2", $class2)->where("class3", (int)$class3_item)->update(["rate" => $rate]);
            }

            $response['message'] = "Other Rate data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }  

    public function upateRateMain(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "data" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $data= $request_data["data"];

            $data = json_decode($data);

            foreach($data as $item) {
                $ka_bl = Kabl::find($item->id);
                $ka_bl->rate = $item->rate;
                $ka_bl->save();
            }

            $response['message'] = "Other Rate data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function restoreOdds(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            Kabl::query()->update(["rate" => DB::raw("mrate"), "blrate" => DB::raw("mrate")]);

            $response['message'] = "Rate restored successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }



    public function getMacaoSpecialCodeRate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $class2= $request_data["class2"];

            $main_data = array();

            $assistant_data = array();

            $kabl = MacaoKabl::where("class1", $class1)
                ->where("class2", $class2)
                ->get();

            $array1 = array();
            $array2 = array();
            $array3 = array();
            $array4 = array();

            foreach($kabl as $item) {
                $item["checked"] = false;
                if ($item["class3"] < 50) {
                    $color = Utils::ka_Color_s($item["class3"]);
                    if ($color === "红波") {
                        $item["color"] = "red";
                    }
                    if ($color === "蓝波") {
                        $item["color"] = "green";
                    }
                    if ($color === "绿波") {
                        $item["color"] = "blue";
                    }
                    array_push($main_data, $item);
                } else {
                    if ($item["class3"] == "单" || $item["class3"] == "大" || $item["class3"] == "合单"|| $item["class3"] == "红波" || $item["class3"] == "蓝波" || $item["class3"] == "总单" || $item["class3"] == "总双" || $item["class3"] == "总大" || $item["class3"] == "总小") {
                        array_push($array1, $item);
                    } else if($item["class3"] == "双" || $item["class3"] == "小" || $item["class3"] == "合双" || $item["class3"] == "绿波") {
                        array_push($array2, $item);
                    } else if($item["class3"] == "家禽" || $item["class3"] == "野兽" || $item["class3"] == "尾大" || $item["class3"] == "尾小"|| $item["class3"] == "大单" || $item["class3"] == "合大" || $item["class3"] == "合小") {
                        array_push($array3, $item);
                    } else if($item["class3"] == "小单" || $item["class3"] == "大双" || $item["class3"] == "小双") {
                        array_push($array4, $item);
                    }
                }
            }

            array_push($array2, array("buttons" => array('提交', '重置')));

            array_push($assistant_data, $array1);
            array_push($assistant_data, $array2);
            array_push($assistant_data, $array3);
            array_push($assistant_data, $array4);

            $response["assistant_data"] = $assistant_data;
            $response["main_data"] = $main_data;
            $response['message'] = "special code Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoPositive16Rate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];

            $main_data = array();

            $kabl = MacaoKabl::where("class1", $class1)
                ->get();

            $array1 = array();
            $array2 = array();
            $array3 = array();
            $array4 = array();
            $array5 = array();
            $array6 = array();

            foreach($kabl as $item) {
                $item["checked"] = false;
                if ($item["class2"] == "正码1" ) {
                    array_push($array1, $item);
                } else if ($item["class2"] == "正码2") {
                    array_push($array2, $item);
                } else if ($item["class2"] == "正码3") {
                    array_push($array3, $item);
                } else if ($item["class2"] == "正码4") {
                    array_push($array4, $item);
                } else if ($item["class2"] == "正码5") {
                    array_push($array5, $item);
                } else if ($item["class2"] == "正码6") {
                    array_push($array6, $item);
                }
            }

            array_push($main_data, $array1);
            array_push($main_data, $array2);
            array_push($main_data, $array3);
            array_push($main_data, $array4);
            array_push($main_data, $array5);
            array_push($main_data, $array6);

            $response["main_data"] = $main_data;
            $response['message'] = "Positive 1-6 Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getMacaoConsecutiveCodeRate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];

            $main_data = array();

            $kabl = MacaoKabl::where("class1", $class1)
                ->get();

            $array1 = array();
            $array2 = array();
            $array3 = array();
            $array4 = array();
            $array5 = array();
            $array6 = array();

            foreach($kabl as $item) {
                $item["checked"] = false;
                if ($item["class2"] == "二全中" ) {
                    $array1 = $item;
                } else if ($item["class2"] == "二中特") {
                    array_push($array2, $item);
                } else if ($item["class2"] == "特串") {
                    $array3 = $item;
                } else if ($item["class2"] == "三全中") {
                    $array4 = $item;
                } else if ($item["class2"] == "三中二") {
                    array_push($array5, $item);
                } else if ($item["class2"] == "四中一") {
                    $array6 = $item;
                }
            }

            array_push($main_data, $array1);
            array_push($main_data, array("class2" => "二中特", "childs" => $array2, "gold" => $array2[0]["gold"]));
            array_push($main_data, $array3);
            array_push($main_data, $array4);
            array_push($main_data, array("class2" => "三中二", "childs" => $array5, "gold" => $array5[0]["gold"]));
            array_push($main_data, $array6);

            $response["main_data"] = $main_data;
            $response['message'] = "Consecutive Code Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoHalfWaveRate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];

            $kabl = MacaoKabl::where("class1", $class1)
                ->get();

            foreach($kabl as $item) {
                $item["checked"] = false;
            }

            $response["main_data"] = $kabl;
            $response['message'] = "Half Wave Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoSpecialRate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $class2= $request_data["class2"];

            $kabl = MacaoKabl::where("class1", $class1)->where("class2", $class2)->get();

            foreach($kabl as $item) {
                $item["checked"] = false;
                $item["rate"] = round($item["rate"], 2);
            }

            $response["main_data"] = $kabl;
            $response['message'] = "Special Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

    public function getMacaoOneXiaoRate(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $kabl = MacaoKabl::where("class2", "一肖")->orWhere("class2", "正特尾数")->get();

            foreach($kabl as $item) {
                $item["checked"] = false;
            }

            $response["main_data"] = $kabl;
            $response['message'] = "One Xiao Rate data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }        

    public function updateMacaoRatePlus(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
                "class3" => "required|string",
                "value" => "required|numeric"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $class2= $request_data["class2"];
            $class3= $request_data["class3"];
            $value = $request_data["value"];
            $class3_array = explode(",", $class3);

            foreach($class3_array as $class3_item) {
                $ka_bl = MacaoKabl::where("class1", $class1)->where("class2", $class2)->where("class3", (int)$class3_item)->first();
                $rate = $ka_bl["rate"] + $value;
                $q1 = MacaoKabl::where("class1", $class1)->where("class2", $class2)->where("class3", (int)$class3_item)->update(["rate" => $rate]);
            }

            $response['message'] = "Plus Rate data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function upateMacaoRateOther(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "class1" => "required|string",
                "class2" => "required|string",
                "class3" => "required|string",
                "rate" => "required|numeric"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $class1= $request_data["class1"];
            $class2= $request_data["class2"];
            $class3= $request_data["class3"];
            $rate = $request_data["rate"];
            $class3_array = explode(",", $class3);

            foreach($class3_array as $class3_item) {
                $q1 = MacaoKabl::where("class1", $class1)->where("class2", $class2)->where("class3", (int)$class3_item)->update(["rate" => $rate]);
            }

            $response['message'] = "Other Rate data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }  

    public function upateMacaoRateMain(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "data" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $data= $request_data["data"];

            $data = json_decode($data);

            foreach($data as $item) {
                $ka_bl = MacaoKabl::find($item->id);
                $ka_bl->rate = $item->rate;
                $ka_bl->save();
            }

            $response['message'] = "Other Rate data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function restoreMacaoOdds(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            MacaoKabl::query()->update(["rate" => DB::raw("mrate"), "blrate" => DB::raw("mrate")]);

            $response['message'] = "Rate restored successfully!";
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
