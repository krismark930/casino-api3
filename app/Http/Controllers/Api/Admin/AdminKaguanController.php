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

class AdminKaguanController extends Controller
{
    public function getKaguanAll(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "page_no" => "required|numeric",
                "grade" => "required|numeric"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $account = $request_data["account"] ?? "";
            $status = $request_data["status"] ?? "";
            $guanid = $request_data["guan_id"] ?? "";
            $zongid = $request_data["zong_id"] ?? "";
            $grade = $request_data["grade"];

            $ka_guan = Kaguan::where("lx", $grade);

            if ($account !== "") {
                $ka_guan = $ka_guan->where("kauser", "like", "%$account%");
            }

            if ($status != "") {
                $ka_guan = $ka_guan->where('stat', $status);
            }

            if ($guanid != "") {
                $ka_guan = $ka_guan->where("guanid", $guanid);
            }

            if ($zongid != "") {
                $ka_guan = $ka_guan->where("zongid", $zongid);
            }

            $total_count = $ka_guan->count();

            $ka_guan = $ka_guan->offset(($page_no - 1) * $limit)->take($limit)->get();

            foreach($ka_guan as $item) {

                if ($grade == 3) {

                    $ka_mem = Kamem::select(DB::raw("count(id) As memnum2"))
                            ->where("danid", $item["id"])
                            ->orderBy("id", "desc")
                            ->first();

                    $result = Kaguan::where("id", $item["zongid"])
                            ->where("lx", 2)
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum2"] = $ka_mem["memnum1"];

                    $item["mjmj"] = $result["sj"];

                }

                if ($grade == 2) {

                    $result = Kaguan::select(DB::raw("count(id) As memnum1"))
                            ->where("zongid", $item["id"])
                            ->where("lx", 3)
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum1"] = $result["memnum1"];

                    $result1 = Kaguan::select(DB::raw("count(id) As memnum2"))
                            ->where("zongid", $item["id"])
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum2"] = $result1["memnum2"];
                }

                if ($grade == 1) {

                    $result = Kaguan::select(DB::raw("count(id) As memnum"))
                            ->where("guanid", $item["id"])
                            ->where("lx", 2)
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum"] = $result["memnum"];

                    $result1 = Kaguan::select(DB::raw("count(id) As memnum1"))
                            ->where("guanid", $item["id"])
                            ->where("lx", 3)
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum1"] = $result1["memnum1"];

                    $result2 = Kamem::select(DB::raw("count(id) As memnum2"))
                            ->where("guanid", $item["id"])
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum2"] = $result2["memnum2"];

                }

                $item["stat"] = $item["stat"] == 0 ? true : false;  
            }

            $response["data"] = $ka_guan;
            $response["total_count"] = $total_count;
            $response['message'] = "Kaguan Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateKaguanStatus(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric",
                "status" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];
            $status = $request_data["status"];

            $ka_guan = Kaguan::find($id);

            $ka_guan->stat = $status;
            $ka_guan->save();

            $response['message'] = "Kaguan Status updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getMacaoKaguanAll(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "page_no" => "required|numeric",
                "grade" => "required|numeric"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $account = $request_data["account"] ?? "";
            $status = $request_data["status"] ?? "";
            $guanid = $request_data["guan_id"] ?? "";
            $zongid = $request_data["zong_id"] ?? "";
            $grade = $request_data["grade"];

            $ka_guan = Kaguan::where("lx", $grade);

            if ($account !== "") {
                $ka_guan = $ka_guan->where("kauser", "like", "%$account%");
            }

            if ($status != "") {
                $ka_guan = $ka_guan->where('stat', $status);
            }

            if ($guanid != "") {
                $ka_guan = $ka_guan->where("guanid", $guanid);
            }

            if ($zongid != "") {
                $ka_guan = $ka_guan->where("zongid", $zongid);
            }

            $total_count = $ka_guan->count();

            $ka_guan = $ka_guan->offset(($page_no - 1) * $limit)->take($limit)->get();

            foreach($ka_guan as $item) {

                if ($grade == 3) {

                    $ka_mem = Kamem::select(DB::raw("count(id) As memnum2"))
                            ->where("danid", $item["id"])
                            ->orderBy("id", "desc")
                            ->first();

                    $result = Kaguan::where("id", $item["zongid"])
                            ->where("lx", 2)
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum2"] = $ka_mem["memnum1"];

                    $item["mjmj"] = $result["sj"];

                }

                if ($grade == 2) {

                    $result = Kaguan::select(DB::raw("count(id) As memnum1"))
                            ->where("zongid", $item["id"])
                            ->where("lx", 3)
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum1"] = $result["memnum1"];

                    $result1 = Kaguan::select(DB::raw("count(id) As memnum2"))
                            ->where("zongid", $item["id"])
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum2"] = $result1["memnum2"];
                }

                if ($grade == 1) {

                    $result = Kaguan::select(DB::raw("count(id) As memnum"))
                            ->where("guanid", $item["id"])
                            ->where("lx", 2)
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum"] = $result["memnum"];

                    $result1 = Kaguan::select(DB::raw("count(id) As memnum1"))
                            ->where("guanid", $item["id"])
                            ->where("lx", 3)
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum1"] = $result1["memnum1"];

                    $result2 = Kamem::select(DB::raw("count(id) As memnum2"))
                            ->where("guanid", $item["id"])
                            ->orderBy("id", "desc")
                            ->first();

                    $item["memnum2"] = $result2["memnum2"];

                }

                $item["stat"] = $item["stat"] == 0 ? true : false;  
            }

            $response["data"] = $ka_guan;
            $response["total_count"] = $total_count;
            $response['message'] = "Kaguan Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoKaguanStatus(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric",
                "status" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];
            $status = $request_data["status"];

            $ka_guan = Kaguan::find($id);

            $ka_guan->stat = $status;
            $ka_guan->save();

            $response['message'] = "Kaguan Status updated successfully!";
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
