<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Kaguan;
use App\Models\Kamem;
use App\Models\KaTan;
use App\Models\MacaoKatan;

class AdminQueryController extends Controller
{
    public function getKaMember(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $period = $request_data["period"];
            $guan_name = $request_data["guan_name"] ?? "";
            $zong_name = $request_data["zong_name"] ?? "";
            $dai_name = $request_data["dai_name"] ?? "";
            $user_name = $request_data["user_name"] ?? "";

            if ($guan_name !== "") {
                $ka_tan = KaTan::select(DB::raw('distinct(zong)'))->where("guan", $guan_name);
            } else if ($zong_name !== "") {
                $ka_tan = KaTan::select(DB::raw('distinct(dai)'))->where("zong", $zong_name);
            } else if ($dai_name !== "") {
                $ka_tan = KaTan::select(DB::raw('distinct(username)'))->where("dai", $dai_name);
            } else {
                $ka_tan = KaTan::select(DB::raw('distinct(guan)'));
            }

            $ka_tan = $ka_tan->where("kithe", $period)->get();

            $data = array();

            foreach($ka_tan as $item) {

                $result = KaTan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m-sum_m*user_ds/100) as sum_st'));

                $result = $result->where("kithe", $period);

                if ($guan_name !== "") {
                    $result = $result->where("zong", $item["zong"]);
                } else if ($zong_name !== "") {
                    $result = $result->where("dai", $item["dai"]);
                } else if ($dai_name !== "") {
                    $result = $result->where("username", $item["username"]);
                } else {
                    $result = $result->where("guan", $item["guan"]);                    
                }

                $result = $result->first();

                $re = $result['re'];
                $sum_m = $result['sum_m'];
                $sum_st = $result['sum_st'];
                $dagu_zc = $result['dagu_zc'];
                $guan_zc = $result['guan_zc'];
                $zong_zc = $result['zong_zc'];
                $dai_zc = $result['dai_zc'];

                $xm = "";

                if ($guan_name !== "") {
                    $ka_guan = Kaguan::where("kauser", $item["zong"])->orderBy("id", "asc")->first();
                    if (isset($ka_guan)) {
                        $xm = $ka_guan["xm"];
                    }
                } else if ($zong_name !== "") {
                    $ka_guan = Kaguan::where("kauser", $item["dai"])->orderBy("id", "asc")->first();
                    if (isset($ka_guan)) {
                        $xm = $ka_guan["xm"];
                    }
                } else if ($dai_name !== "") {
                    $ka_mem = Kamem::where("kauser", $item["username"])->orderBy("id", "asc")->first();
                    if (isset($ka_mem)) {
                        $xm = $ka_mem["xm"];
                    }
                } else {
                    $ka_guan = Kaguan::where("kauser", $item["guan"])->orderBy("id", "asc")->first();
                    if (isset($ka_guan)) {
                        $xm = $ka_guan["xm"];
                    }
                }



                if ($guan_name !== "") {
                    $name = $item["zong"];
                    $grade = 2;
                } else if ($zong_name !== "") {
                    $name = $item["dai"];
                    $grade = 3;
                } else if ($dai_name !== "") {
                    $name = $item["username"];
                    $grade = 4;
                } else {
                    $name = $item["guan"];
                    $grade = 1;
                }

                $temp_data = array (
                    "grade" => $grade,
                    "name" => $name,
                    "re" => $re,
                    "xm" => $xm,
                    "sum_m" => round($sum_m, 2),
                    "sum_st" => round($sum_st, 2),
                    "dagu_zc" => round($dagu_zc, 2),
                    "guan_zc" => round($guan_zc, 2),
                    "zong_zc" => round($zong_zc, 2),
                    "dai_zc" => round($dai_zc, 2),
                );

                array_push($data, $temp_data);
            }

            $response["data"] = $data;
            $response['message'] = "Query Member Data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getKatanMainData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
                // "search_key" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $search_key = $request_data["search_key"] ?? "";
            $user_name = $request_data["user_name"] ?? "";
            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $class4 = $request_data["class4"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $sum_data = array();

            $z_re=0;
            $z_sum=0;

            $ka_tan = KaTan::query();

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
            }

            if ($class4 != "" && $search_key != "") {
                if ((int)$class4 == 1) {
                    $ka_tan = $ka_tan->where("username", $search_key);
                } else if((int)$class4 == 2) {
                    $ka_tan = $ka_tan->where("num", $search_key);
                } else if((int)$class4 == 3) {
                    $ka_tan = $ka_tan->where("abcd", $search_key);
                }
            }

            if ($user_name !== "") {
                $ka_tan = $ka_tan->where("username", $user_name);
            }

            $ka_tan = $ka_tan->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $ka_mem = Kamem::where("kauser", $item["username"])->orderBy("id", "asc")->first();

                if (isset($ka_mem)) {
                    $item["xm"] = $ka_mem["xm"];
                } else {
                    $item["xm"] = "";
                }

                $z_sum += $item["sum_m"];

                $no++;
                $z_re++;
            }

            $sum_data = array (
                "z_re" => $z_re,
                "z_sum" => $z_sum,
            );

            $response["data"] = $ka_tan;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Query Main data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteKatan(Request $request) {

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

            KaTan::destroy($id);

            $response['message'] = "Katan deleted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateKatan(Request $request) {

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
            $add_date = $request_data["adddate"];
            $sum_m = $request["sum_m"];
            $rate = $request["rate"];
            $user_ds = $request["user_ds"];
            $class1 = $request["class1"];
            $class2 = $request["class2"];
            $class3 = $request["class3"];

            $ka_tan = KaTan::find($id);

            $ka_tan->adddate = $add_date;
            $ka_tan->sum_m = $sum_m;
            $ka_tan->rate = $rate;
            $ka_tan->user_ds = $user_ds;
            $ka_tan->class1 = $class1;
            $ka_tan->class2 = $class2;
            $ka_tan->class3 = $class3;

            $ka_tan->save();

            $response['message'] = "Katan updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }  

    public function getMacaoKaMember(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "period" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $period = $request_data["period"];
            $guan_name = $request_data["guan_name"] ?? "";
            $zong_name = $request_data["zong_name"] ?? "";
            $dai_name = $request_data["dai_name"] ?? "";
            $user_name = $request_data["user_name"] ?? "";

            if ($guan_name !== "") {
                $ka_tan = MacaoKatan::select(DB::raw('distinct(zong)'))->where("guan", $guan_name);
            } else if ($zong_name !== "") {
                $ka_tan = MacaoKatan::select(DB::raw('distinct(dai)'))->where("zong", $zong_name);
            } else if ($dai_name !== "") {
                $ka_tan = MacaoKatan::select(DB::raw('distinct(username)'))->where("dai", $dai_name);
            } else {
                $ka_tan = MacaoKatan::select(DB::raw('distinct(guan)'));
            }

            $ka_tan = $ka_tan->where("kithe", $period)->get();

            $data = array();

            foreach($ka_tan as $item) {

                $result = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m-sum_m*user_ds/100) as sum_st'));

                $result = $result->where("kithe", $period);

                if ($guan_name !== "") {
                    $result = $result->where("zong", $item["zong"]);
                } else if ($zong_name !== "") {
                    $result = $result->where("dai", $item["dai"]);
                } else if ($dai_name !== "") {
                    $result = $result->where("username", $item["username"]);
                } else {
                    $result = $result->where("guan", $item["guan"]);                    
                }

                $result = $result->first();

                $re = $result['re'];
                $sum_m = $result['sum_m'];
                $sum_st = $result['sum_st'];
                $dagu_zc = $result['dagu_zc'];
                $guan_zc = $result['guan_zc'];
                $zong_zc = $result['zong_zc'];
                $dai_zc = $result['dai_zc'];

                $xm = "";

                if ($guan_name !== "") {
                    $ka_guan = Kaguan::where("kauser", $item["zong"])->orderBy("id", "asc")->first();
                    if (isset($ka_guan)) {
                        $xm = $ka_guan["xm"];
                    }
                } else if ($zong_name !== "") {
                    $ka_guan = Kaguan::where("kauser", $item["dai"])->orderBy("id", "asc")->first();
                    if (isset($ka_guan)) {
                        $xm = $ka_guan["xm"];
                    }
                } else if ($dai_name !== "") {
                    $ka_mem = Kamem::where("kauser", $item["username"])->orderBy("id", "asc")->first();
                    if (isset($ka_mem)) {
                        $xm = $ka_mem["xm"];
                    }
                } else {
                    $ka_guan = Kaguan::where("kauser", $item["guan"])->orderBy("id", "asc")->first();
                    if (isset($ka_guan)) {
                        $xm = $ka_guan["xm"];
                    }
                }



                if ($guan_name !== "") {
                    $name = $item["zong"];
                    $grade = 2;
                } else if ($zong_name !== "") {
                    $name = $item["dai"];
                    $grade = 3;
                } else if ($dai_name !== "") {
                    $name = $item["username"];
                    $grade = 4;
                } else {
                    $name = $item["guan"];
                    $grade = 1;
                }

                $temp_data = array (
                    "grade" => $grade,
                    "name" => $name,
                    "re" => $re,
                    "xm" => $xm,
                    "sum_m" => round($sum_m, 2),
                    "sum_st" => round($sum_st, 2),
                    "dagu_zc" => round($dagu_zc, 2),
                    "guan_zc" => round($guan_zc, 2),
                    "zong_zc" => round($zong_zc, 2),
                    "dai_zc" => round($dai_zc, 2),
                );

                array_push($data, $temp_data);
            }

            $response["data"] = $data;
            $response['message'] = "Query Member Data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoKatanMainData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
                // "search_key" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $search_key = $request_data["search_key"] ?? "";
            $user_name = $request_data["user_name"] ?? "";
            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $class4 = $request_data["class4"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $sum_data = array();

            $z_re=0;
            $z_sum=0;

            $ka_tan = MacaoKatan::query();

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
            }

            if ($class4 != "" && $search_key != "") {
                if ((int)$class4 == 1) {
                    $ka_tan = $ka_tan->where("username", $search_key);
                } else if((int)$class4 == 2) {
                    $ka_tan = $ka_tan->where("num", $search_key);
                } else if((int)$class4 == 3) {
                    $ka_tan = $ka_tan->where("abcd", $search_key);
                }
            }

            if ($user_name !== "") {
                $ka_tan = $ka_tan->where("username", $user_name);
            }

            $ka_tan = $ka_tan->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $ka_mem = Kamem::where("kauser", $item["username"])->orderBy("id", "asc")->first();

                if (isset($ka_mem)) {
                    $item["xm"] = $ka_mem["xm"];
                } else {
                    $item["xm"] = "";
                }

                $z_sum += $item["sum_m"];

                $no++;
                $z_re++;
            }

            $sum_data = array (
                "z_re" => $z_re,
                "z_sum" => $z_sum,
            );

            $response["data"] = $ka_tan;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Query Main data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteMacaoKatan(Request $request) {

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

            MacaoKatan::destroy($id);

            $response['message'] = "MacaoKatan deleted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoKatan(Request $request) {

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
            $add_date = $request_data["adddate"];
            $sum_m = $request["sum_m"];
            $rate = $request["rate"];
            $user_ds = $request["user_ds"];
            $class1 = $request["class1"];
            $class2 = $request["class2"];
            $class3 = $request["class3"];

            $ka_tan = MacaoKatan::find($id);

            $ka_tan->adddate = $add_date;
            $ka_tan->sum_m = $sum_m;
            $ka_tan->rate = $rate;
            $ka_tan->user_ds = $user_ds;
            $ka_tan->class1 = $class1;
            $ka_tan->class2 = $class2;
            $ka_tan->class3 = $class3;

            $ka_tan->save();

            $response['message'] = "MacaoKatan updated successfully!";
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
