<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\Kamem;
use App\Models\KaTan;
use App\Models\MacaoKatan;
use App\Models\Kaguan;
use App\Models\Kazi;
use App\Models\Kaquota;
use App\Models\Kaguands;
use Carbon\Carbon;
use App\Utils\Utils;

class AdminKamemController extends Controller
{
    public function getKaguanMember(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $data1 = array();
            $data2 = array();
            $data3 = array();

            array_push($data1, array("label" => "全部", "value" => ""));
            array_push($data2, array("label" => "全部", "value" => ""));
            array_push($data3, array("label" => "全部", "value" => ""));

            $ka_guan = Kaguan::all();

            foreach($ka_guan as $item) {
                switch ($item["lx"]) {
                    case 1:
                        array_push($data1, array("label" => $item["kauser"], "value" => $item["id"]));
                        break;
                    case 2: 
                        array_push($data2, array("label" => $item["kauser"], "value" => $item["id"]));
                        break;
                    case 3:
                        array_push($data3, array("label" => $item["kauser"], "value" => $item["id"]));
                        break;
                }
            }

            $response["data1"] = $data1;
            $response["data2"] = $data2;
            $response["data3"] = $data3;
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

    public function getKamemSuperior(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $kamem_superior = Kaguan::where("lx", 3)->get(["id", "kauser", "sf", "cs"]);

            $data = array();

            foreach($kamem_superior as $item) {
                $result1 = Kamem::select(DB::raw("SUM(cs) As sum_m"))->where("danid", $item["id"])->first();
                $mumul = 0;
                if (isset($result1)) {
                    $mumul = $result1["sum_m"];
                }
                $result2 = KaTan::select(DB::raw("SUM(sum_m) As sum_m"))
                    ->where("username", $item["kauser"])
                    ->first();
                $mkmkl = 0;
                if (isset($result2)) {
                    $mkmkl = $result2["sum_m"];
                }
                $cscs = $item["cs"] - $mumul - $mkmkl;
                array_push($data, array("label" => $item["kauser"] . "--" . $cscs, "value" => $item["id"]));
            }
            $response["data"] = $data;
            $response['message'] = "Kamem Superior Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

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
            $danid = $request_data["dan_id"] ?? "";
            $grade = $request_data["grade"];

            $ka_mem = Kamem::where("lx", $grade);

            if ($account !== "") {
                $ka_mem = $ka_mem->where("kauser", "like", "%$account%");
            }

            if ($status != "") {
                $ka_mem = $ka_mem->where('stat', $status);
            }

            if ($guanid != "") {
                $ka_mem = $ka_mem->where("guanid", $guanid);
            }

            if ($zongid != "") {
                $ka_mem = $ka_mem->where("zongid", $zongid);
            }

            if ($danid != "") {
                $ka_mem = $ka_mem->where("danid", $danid);
            }

            $total_count = $ka_mem->count();

            $ka_mem = $ka_mem->offset(($page_no - 1) * $limit)->take($limit)->get();

            foreach($ka_mem as $item) {
                $item["stat"] = $item["stat"] == 0 ? true : false;
            }

            $response["data"] = $ka_mem;
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

    public function getKamemAll(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "page_no" => "required|numeric",
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
            $danid = $request_data["dan_id"] ?? "";

            $ka_mem = Kamem::query();

            if ($account !== "") {
                $ka_mem = $ka_mem->where("kauser", "like", "%$account%");
            }

            if ($status != "") {
                $ka_mem = $ka_mem->where('stat', $status);
            }

            if ($guanid != "") {
                $ka_mem = $ka_mem->where("guanid", $guanid);
            }

            if ($zongid != "") {
                $ka_mem = $ka_mem->where("zongid", $zongid);
            }

            if ($danid != "") {
                $ka_mem = $ka_mem->where("danid", $danid);
            }

            $total_count = $ka_mem->count();

            $ka_mem = $ka_mem->offset(($page_no - 1) * $limit)->take($limit)->get();

            foreach($ka_mem as $item) {
                $item["stat"] = $item["stat"] == 0 ? true : false;
            }

            $response["data"] = $ka_mem;
            $response["total_count"] = $total_count;
            $response['message'] = "Kamem Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateKamemStatus(Request $request) {

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

            $ka_mem = Kamem::find($id);

            $ka_mem->stat = $status;
            $ka_mem->save();

            $response['message'] = "Kamem Status updated successfully!";
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

    public function addKamem(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "dan_id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $dan_id = $request_data["dan_id"];
            $kauser = $request_data["kauser"];
            $stat = $request_data["stat"];
            $kapassword = $request_data["kapassword"];
            $xm = $request_data["xm"];
            $kyx = $request_data["kyx"];
            $cs = $request_data["cs"];
            $xy = $request_data["xy"];
            $abcd = $request_data["abcd"];
            $tmb = $request_data["tmb"];

            $ka_guan = Kaguan::where("kauser", $kauser)->first();

            if (isset($ka_guan)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_mem = Kamem::where("kauser", $kauser)->first();

            if (isset($ka_mem)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_zi = Kazi::where("kauser", $kauser)->first();

            if (isset($ka_zi)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $pass = Hash::make($kapassword);
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $ka_guan = Kaguan::find($dan_id);
            $guan = $ka_guan["guan"];
            $guanid = $ka_guan["guanid"];
            $zongid = $ka_guan["zongid"];
            $zong = $ka_guan["zong"];
            $danid = $ka_guan['id'];
            $dan = $ka_guan['kauser'];
            $dan_zc = $ka_guan['sf'] / 10;
            $zong_zc = $ka_guan['sj'] / 10;
            $ka_zong = Kaguan::find($zongid);
            $guan_zc = $ka_zong['sj'] / 10;
            $dagu_zc = 10 - $guan_zc - $dan_zc - $zong_zc;

            $ka_mem = new Kamem;
            $ka_mem->kauser = $kauser;
            $ka_mem->kapassword = $pass;
            $ka_mem->xm = $xm;
            $ka_mem->cs = $cs;
            $ka_mem->ts = $cs;
            $ka_mem->guan = $guan;
            $ka_mem->zong = $zong;
            $ka_mem->dan = $dan;
            $ka_mem->stat = $stat;
            $ka_mem->xy = $xy;
            $ka_mem->guanid = $guanid;
            $ka_mem->zongid = $zongid;
            $ka_mem->danid = $danid;
            $ka_mem->look = 0;
            $ka_mem->adddate = $text;
            $ka_mem->slogin = $text;
            $ka_mem->zlogin = $text;
            $ka_mem->sip = Utils::get_ip();
            $ka_mem->zip = Utils::get_ip();
            $ka_mem->abcd = $abcd;
            $ka_mem->dan_zc = $dan_zc;
            $ka_mem->guan_zc = $guan_zc;
            $ka_mem->zong_zc = $zong_zc;
            $ka_mem->dagu_zc = $dagu_zc;

            $ka_mem->save();

            $ka_mem = Kamem::where("kauser", $kauser)->first();
            $SoftID = $ka_mem["id"];

            $result = Kaquota::where("lx", 0)
                ->where("userid", $danid)
                ->where("flag", 0)
                ->first();

            switch($abcd) {
                case "A":
                    $yg = $result['yg'];
                    break;
                case "B":
                    $yg = $result['ygb'];
                    break;
                case "C":
                    $yg = $result['ygc'];
                    break;
                case "D":
                    $yg = $result['ygd'];
                    break;
            }

            $ka_quota = new Kaquota;
            $ka_quota->yg = $yg;
            $ka_quota->ygb = 0;
            $ka_quota->ygc = 0;
            $ka_quota->ygd = 0;
            $ka_quota->xx = $result['xx'];
            $ka_quota->xxx = $result['xxx'];
            $ka_quota->ds = $result['ds'];
            $ka_quota->style = $result['style'];
            $ka_quota->username = $kauser;
            $ka_quota->userid = $SoftID;
            $ka_quota->lx = 0;
            $ka_quota->flag = 1;
            $ka_quota->guanid = $guanid;
            $ka_quota->zongid = $zongid;
            $ka_quota->danid = $danid;
            $ka_quota->memid = $SoftID;
            $ka_quota->abcd = $abcd;

            $ka_quota->save();

            $response['message'] = "Kamem added successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getKadanSuperior(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();
            $zongid = $request_data["zong_id"] ?? "";

            $kadan_superior = Kaguan::where("lx", 2)->get(["id", "kauser", "sf", "cs"]);

            $data = array();

            foreach($kadan_superior as $item) {
                $result1 = Kaguan::select(DB::raw("SUM(cs) As sum_m"))
                    ->where("lx", 3)
                    ->where("zongid", $item["id"])
                    ->first();
                $mumul = 0;
                if (isset($result1)) {
                    $mumul = $result1["sum_m"];
                }
                $result2 = KaTan::select(DB::raw("SUM(sum_m) As sum_m"))
                    ->where("username", $item["kauser"])
                    ->first();
                $mkmkl = 0;
                if (isset($result2)) {
                    $mkmkl = $result2["sum_m"];
                }
                $cscs = $item["cs"] - $mumul - $mkmkl;
                array_push($data, array("label" => $item["kauser"] . "--" . $cscs, "value" => $item["id"]));
            }

            if ($zongid != "") {
                $result = Kaquota::where("userid", $zongid)
                    ->where("lx", 0)
                    ->where("flag", 0)
                    ->get();
            } else {
                $result = Kaguands::where("lx", 0)->get();
            }
            $response["data"] = $data;
            $response["other_data"] = $result;
            $response['message'] = "Kadan Superior Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addKadan(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "zong_id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $zong_id = $request_data["zong_id"];
            $kauser = $request_data["kauser"];
            $stat = $request_data["stat"];
            $kapassword = $request_data["kapassword"];
            $xm = $request_data["xm"];
            $kyx = $request_data["kyx"];
            $cs = $request_data["cs"];
            $xy = $request_data["xy"];
            $tmb = $request_data["tmb"];
            $rs = $request_data["rs"];
            $sj = $request_data["sj"];
            $sf = $request_data["sf"];
            $pz = $request_data["pz"];
            $other_data = $request_data["other_data"];
            $other_data = json_decode($other_data, true);

            $ka_guan = Kaguan::where("kauser", $kauser)->first();

            if (isset($ka_guan)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_mem = Kamem::where("kauser", $kauser)->first();

            if (isset($ka_mem)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_zi = Kazi::where("kauser", $kauser)->first();

            if (isset($ka_zi)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $pass = Hash::make($kapassword);
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $ka_zong = Kaguan::find($zong_id);
            $guan = $ka_zong["guan"];
            $guanid = $ka_zong["guanid"];
            $zongid = $ka_zong["id"];
            $zong = $ka_zong["kauser"];

            $ka_guan = new Kaguan;
            $ka_guan->kauser = $kauser;
            $ka_guan->kapassword = $pass;
            $ka_guan->xm = $xm;
            $ka_guan->cs = $cs;
            $ka_guan->rs = $rs;
            $ka_guan->ts = $cs;
            $ka_guan->tmb = $tmb;
            $ka_guan->sj = $sj;
            $ka_guan->sf = $sf;
            $ka_guan->guan = $guan;
            $ka_guan->zong = $zong;
            $ka_guan->stat = $stat;
            $ka_guan->guanid = $guanid;
            $ka_guan->zongid = $zongid;
            $ka_guan->look = 0;
            $ka_guan->lx = 3;
            $ka_guan->pz = $pz;
            $ka_guan->ztws = 0;
            $ka_guan->tm = 500000;
            $ka_guan->zm = 500000;
            $ka_guan->zt = 500000;
            $ka_guan->zm6 = 500000;
            $ka_guan->lm = 500000;
            $ka_guan->gg = 500000;
            $ka_guan->xx = 500000;
            $ka_guan->sx = 500000;
            $ka_guan->bb = 500000;
            $ka_guan->ws = 500000;
            $ka_guan->adddate = $text;
            $ka_guan->slogin = $text;
            $ka_guan->zlogin = $text;
            $ka_guan->sip = Utils::get_ip();
            $ka_guan->zip = Utils::get_ip();

            $ka_guan->save();

            $ka_guan = Kaguan::where("kauser", $kauser)->first();
            $SoftID = $ka_guan["id"];

            foreach($other_data as $item) {

                $ka_quota = new Kaquota;
                $ka_quota->yg = $item["yg"];
                $ka_quota->ygb = $item["ygb"];
                $ka_quota->ygc = $item["ygc"];
                $ka_quota->ygd = $item["ygd"];
                $ka_quota->xx = $item['xx'];
                $ka_quota->xxx = $item['xxx'];
                $ka_quota->ds = $item['ds'];
                $ka_quota->style = $item['style'];
                $ka_quota->username = $kauser;
                $ka_quota->userid = $SoftID;
                $ka_quota->lx = 0;
                $ka_quota->flag = 0;
                $ka_quota->guanid = $guanid;
                $ka_quota->zongid = $zongid;
                $ka_quota->danid = 0;
                $ka_quota->memid = 0;

                $ka_quota->save();

            }

            $response['message'] = "Kadan data added successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }  

    public function getKazongSuperior(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();
            $guanid = $request_data["guan_id"] ?? "";

            $kadan_superior = Kaguan::where("lx", 1)->get(["id", "kauser", "sf", "cs"]);

            $data = array();

            foreach($kadan_superior as $item) {
                $result1 = Kaguan::select(DB::raw("SUM(cs) As sum_m"))
                    ->where("lx", 2)
                    ->where("guanid", $item["id"])
                    ->first();
                $mumul = 0;
                if (isset($result1)) {
                    $mumul = $result1["sum_m"];
                }
                $result2 = KaTan::select(DB::raw("SUM(sum_m) As sum_m"))
                    ->where("username", $item["kauser"])
                    ->first();
                $mkmkl = 0;
                if (isset($result2)) {
                    $mkmkl = $result2["sum_m"];
                }
                $cscs = $item["cs"] - $mumul - $mkmkl;
                array_push($data, array("label" => $item["kauser"] . "--" . $cscs, "value" => $item["id"]));
            }

            if ($guanid != "") {
                $result = Kaquota::where("userid", $guanid)
                    ->where("lx", 0)
                    ->where("flag", 0)
                    ->get();
            } else {
                $result = Kaguands::where("lx", 0)->get();
            }
            $response["data"] = $data;
            $response["other_data"] = $result;
            $response['message'] = "Kazong Superior Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addKazong(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "guan_id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $guan_id = $request_data["guan_id"];
            $kauser = $request_data["kauser"];
            $stat = $request_data["stat"];
            $kapassword = $request_data["kapassword"];
            $xm = $request_data["xm"];
            $kyx = $request_data["kyx"];
            $cs = $request_data["cs"];
            $xy = $request_data["xy"];
            $tmb = $request_data["tmb"];
            $rs = $request_data["rs"];
            $sj = $request_data["sj"];
            $sf = $request_data["sf"];
            $pz = $request_data["pz"];
            $other_data = $request_data["other_data"];
            $other_data = json_decode($other_data, true);

            $ka_guan = Kaguan::where("kauser", $kauser)->first();

            if (isset($ka_guan)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_mem = Kamem::where("kauser", $kauser)->first();

            if (isset($ka_mem)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_zi = Kazi::where("kauser", $kauser)->first();

            if (isset($ka_zi)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $pass = Hash::make($kapassword);
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $ka_guan = Kaguan::find($guan_id);
            $guan = $ka_guan["kauser"];
            $guanid = $guan_id;

            $ka_guan = new Kaguan;
            $ka_guan->kauser = $kauser;
            $ka_guan->kapassword = $pass;
            $ka_guan->xm = $xm;
            $ka_guan->cs = $cs;
            $ka_guan->rs = $rs;
            $ka_guan->ts = $cs;
            $ka_guan->tmb = $tmb;
            $ka_guan->sj = $sj;
            $ka_guan->sf = $sf;
            $ka_guan->guan = $guan;
            $ka_guan->zong = $kauser;
            $ka_guan->guanid = $guanid;
            $ka_guan->zongid = 0;
            $ka_guan->stat = $stat;
            $ka_guan->look = 0;
            $ka_guan->lx = 2;
            $ka_guan->pz = $pz;
            $ka_guan->ztws = 0;
            $ka_guan->tm = 500000;
            $ka_guan->zm = 500000;
            $ka_guan->zt = 500000;
            $ka_guan->zm6 = 500000;
            $ka_guan->lm = 500000;
            $ka_guan->gg = 500000;
            $ka_guan->xx = 500000;
            $ka_guan->sx = 500000;
            $ka_guan->bb = 500000;
            $ka_guan->ws = 500000;
            $ka_guan->adddate = $text;
            $ka_guan->slogin = $text;
            $ka_guan->zlogin = $text;
            $ka_guan->sip = Utils::get_ip();
            $ka_guan->zip = Utils::get_ip();

            $ka_guan->save();

            $ka_guan = Kaguan::where("kauser", $kauser)->first();
            $SoftID = $ka_guan["id"];

            foreach($other_data as $item) {

                $ka_quota = new Kaquota;
                $ka_quota->yg = $item["yg"];
                $ka_quota->ygb = $item["ygb"];
                $ka_quota->ygc = $item["ygc"];
                $ka_quota->ygd = $item["ygd"];
                $ka_quota->xx = $item['xx'];
                $ka_quota->xxx = $item['xxx'];
                $ka_quota->ds = $item['ds'];
                $ka_quota->style = $item['style'];
                $ka_quota->username = $kauser;
                $ka_quota->userid = $SoftID;
                $ka_quota->lx = 0;
                $ka_quota->flag = 0;
                $ka_quota->guanid = $guanid;
                $ka_quota->zongid = 0;
                $ka_quota->danid = 0;
                $ka_quota->memid = 0;

                $ka_quota->save();

            }

            $response['message'] = "Kazong data added successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }   

    public function getKaguanSuperior(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $result = Kaguands::where("lx", 0)->get();
            $response["other_data"] = $result;
            $response['message'] = "Kaguan Superior Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addKaguan(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $kauser = $request_data["kauser"];
            $stat = $request_data["stat"];
            $kapassword = $request_data["kapassword"];
            $xm = $request_data["xm"];
            $kyx = $request_data["kyx"];
            $cs = $request_data["cs"];
            $xy = $request_data["xy"];
            $tmb = $request_data["tmb"];
            $rs = $request_data["rs"];
            $sj = $request_data["sj"];
            $sf = $request_data["sf"];
            $pz = $request_data["pz"];
            $other_data = $request_data["other_data"];
            $other_data = json_decode($other_data, true);

            $ka_guan = Kaguan::where("kauser", $kauser)->first();

            if (isset($ka_guan)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_mem = Kamem::where("kauser", $kauser)->first();

            if (isset($ka_mem)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_zi = Kazi::where("kauser", $kauser)->first();

            if (isset($ka_zi)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $pass = Hash::make($kapassword);
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $ka_guan = new Kaguan;
            $ka_guan->kauser = $kauser;
            $ka_guan->kapassword = $pass;
            $ka_guan->xm = $xm;
            $ka_guan->cs = $cs;
            $ka_guan->rs = $rs;
            $ka_guan->ts = $cs;
            $ka_guan->tmb = $tmb;
            $ka_guan->sj = $sj;
            $ka_guan->sf = $sf;
            $ka_guan->guan = $kauser;
            $ka_guan->zong = $kauser;
            $ka_guan->guanid = 0;
            $ka_guan->zongid = 0;
            $ka_guan->stat = $stat;
            $ka_guan->look = 0;
            $ka_guan->lx = 1;
            $ka_guan->pz = $pz;
            $ka_guan->ztws = 0;
            $ka_guan->tm = 500000;
            $ka_guan->zm = 500000;
            $ka_guan->zt = 500000;
            $ka_guan->zm6 = 500000;
            $ka_guan->lm = 500000;
            $ka_guan->gg = 500000;
            $ka_guan->xx = 500000;
            $ka_guan->sx = 500000;
            $ka_guan->bb = 500000;
            $ka_guan->ws = 500000;
            $ka_guan->adddate = $text;
            $ka_guan->slogin = $text;
            $ka_guan->zlogin = $text;
            $ka_guan->sip = Utils::get_ip();
            $ka_guan->zip = Utils::get_ip();

            $ka_guan->save();

            $ka_guan = Kaguan::where("kauser", $kauser)->first();
            $SoftID = $ka_guan["id"];

            foreach($other_data as $item) {

                $ka_quota = new Kaquota;
                $ka_quota->yg = $item["yg"];
                $ka_quota->ygb = $item["ygb"];
                $ka_quota->ygc = $item["ygc"];
                $ka_quota->ygd = $item["ygd"];
                $ka_quota->xx = $item['xx'];
                $ka_quota->xxx = $item['xxx'];
                $ka_quota->ds = $item['ds'];
                $ka_quota->style = $item['style'];
                $ka_quota->username = $kauser;
                $ka_quota->userid = $SoftID;
                $ka_quota->lx = 0;
                $ka_quota->flag = 0;
                $ka_quota->guanid = 0;
                $ka_quota->zongid = 0;
                $ka_quota->danid = 0;
                $ka_quota->memid = 0;

                $ka_quota->save();
            }

            $response['message'] = "Kaguan data added successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }


    public function getMacaoKaguanMember(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $data1 = array();
            $data2 = array();
            $data3 = array();

            array_push($data1, array("label" => "全部", "value" => ""));
            array_push($data2, array("label" => "全部", "value" => ""));
            array_push($data3, array("label" => "全部", "value" => ""));

            $ka_guan = Kaguan::all();

            foreach($ka_guan as $item) {
                switch ($item["lx"]) {
                    case 1:
                        array_push($data1, array("label" => $item["kauser"], "value" => $item["id"]));
                        break;
                    case 2: 
                        array_push($data2, array("label" => $item["kauser"], "value" => $item["id"]));
                        break;
                    case 3:
                        array_push($data3, array("label" => $item["kauser"], "value" => $item["id"]));
                        break;
                }
            }

            $response["data1"] = $data1;
            $response["data2"] = $data2;
            $response["data3"] = $data3;
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

    public function getMacaoKamemSuperior(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $kamem_superior = Kaguan::where("lx", 3)->get(["id", "kauser", "sf", "cs"]);

            $data = array();

            foreach($kamem_superior as $item) {
                $result1 = Kamem::select(DB::raw("SUM(cs) As sum_m"))->where("danid", $item["id"])->first();
                $mumul = 0;
                if (isset($result1)) {
                    $mumul = $result1["sum_m"];
                }
                $result2 = MacaoKatan::select(DB::raw("SUM(sum_m) As sum_m"))
                    ->where("username", $item["kauser"])
                    ->first();
                $mkmkl = 0;
                if (isset($result2)) {
                    $mkmkl = $result2["sum_m"];
                }
                $cscs = $item["cs"] - $mumul - $mkmkl;
                array_push($data, array("label" => $item["kauser"] . "--" . $cscs, "value" => $item["id"]));
            }
            $response["data"] = $data;
            $response['message'] = "Kamem Superior Data fetched successfully!";
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
            $danid = $request_data["dan_id"] ?? "";
            $grade = $request_data["grade"];

            $ka_mem = Kamem::where("lx", $grade);

            if ($account !== "") {
                $ka_mem = $ka_mem->where("kauser", "like", "%$account%");
            }

            if ($status != "") {
                $ka_mem = $ka_mem->where('stat', $status);
            }

            if ($guanid != "") {
                $ka_mem = $ka_mem->where("guanid", $guanid);
            }

            if ($zongid != "") {
                $ka_mem = $ka_mem->where("zongid", $zongid);
            }

            if ($danid != "") {
                $ka_mem = $ka_mem->where("danid", $danid);
            }

            $total_count = $ka_mem->count();

            $ka_mem = $ka_mem->offset(($page_no - 1) * $limit)->take($limit)->get();

            foreach($ka_mem as $item) {
                $item["stat"] = $item["stat"] == 0 ? true : false;
            }

            $response["data"] = $ka_mem;
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

    public function getMacaoKamemAll(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "page_no" => "required|numeric",
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
            $danid = $request_data["dan_id"] ?? "";

            $ka_mem = Kamem::query();

            if ($account !== "") {
                $ka_mem = $ka_mem->where("kauser", "like", "%$account%");
            }

            if ($status != "") {
                $ka_mem = $ka_mem->where('stat', $status);
            }

            if ($guanid != "") {
                $ka_mem = $ka_mem->where("guanid", $guanid);
            }

            if ($zongid != "") {
                $ka_mem = $ka_mem->where("zongid", $zongid);
            }

            if ($danid != "") {
                $ka_mem = $ka_mem->where("danid", $danid);
            }

            $total_count = $ka_mem->count();

            $ka_mem = $ka_mem->offset(($page_no - 1) * $limit)->take($limit)->get();

            foreach($ka_mem as $item) {
                $item["stat"] = $item["stat"] == 0 ? true : false;
            }

            $response["data"] = $ka_mem;
            $response["total_count"] = $total_count;
            $response['message'] = "Kamem Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoKamemStatus(Request $request) {

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

            $ka_mem = Kamem::find($id);

            $ka_mem->stat = $status;
            $ka_mem->save();

            $response['message'] = "Kamem Status updated successfully!";
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

    public function addMacaoKamem(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "dan_id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $dan_id = $request_data["dan_id"];
            $kauser = $request_data["kauser"];
            $stat = $request_data["stat"];
            $kapassword = $request_data["kapassword"];
            $xm = $request_data["xm"];
            $kyx = $request_data["kyx"];
            $cs = $request_data["cs"];
            $xy = $request_data["xy"];
            $abcd = $request_data["abcd"];
            $tmb = $request_data["tmb"];

            $ka_guan = Kaguan::where("kauser", $kauser)->first();

            if (isset($ka_guan)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_mem = Kamem::where("kauser", $kauser)->first();

            if (isset($ka_mem)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_zi = Kazi::where("kauser", $kauser)->first();

            if (isset($ka_zi)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $pass = Hash::make($kapassword);
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $ka_guan = Kaguan::find($dan_id);
            $guan = $ka_guan["guan"];
            $guanid = $ka_guan["guanid"];
            $zongid = $ka_guan["zongid"];
            $zong = $ka_guan["zong"];
            $danid = $ka_guan['id'];
            $dan = $ka_guan['kauser'];
            $dan_zc = $ka_guan['sf'] / 10;
            $zong_zc = $ka_guan['sj'] / 10;
            $ka_zong = Kaguan::find($zongid);
            $guan_zc = $ka_zong['sj'] / 10;
            $dagu_zc = 10 - $guan_zc - $dan_zc - $zong_zc;

            $ka_mem = new Kamem;
            $ka_mem->kauser = $kauser;
            $ka_mem->kapassword = $pass;
            $ka_mem->xm = $xm;
            $ka_mem->cs = $cs;
            $ka_mem->ts = $cs;
            $ka_mem->guan = $guan;
            $ka_mem->zong = $zong;
            $ka_mem->dan = $dan;
            $ka_mem->stat = $stat;
            $ka_mem->xy = $xy;
            $ka_mem->guanid = $guanid;
            $ka_mem->zongid = $zongid;
            $ka_mem->danid = $danid;
            $ka_mem->look = 0;
            $ka_mem->adddate = $text;
            $ka_mem->slogin = $text;
            $ka_mem->zlogin = $text;
            $ka_mem->sip = Utils::get_ip();
            $ka_mem->zip = Utils::get_ip();
            $ka_mem->abcd = $abcd;
            $ka_mem->dan_zc = $dan_zc;
            $ka_mem->guan_zc = $guan_zc;
            $ka_mem->zong_zc = $zong_zc;
            $ka_mem->dagu_zc = $dagu_zc;

            $ka_mem->save();

            $ka_mem = Kamem::where("kauser", $kauser)->first();
            $SoftID = $ka_mem["id"];

            $result = Kaquota::where("lx", 0)
                ->where("userid", $danid)
                ->where("flag", 0)
                ->first();

            switch($abcd) {
                case "A":
                    $yg = $result['yg'];
                    break;
                case "B":
                    $yg = $result['ygb'];
                    break;
                case "C":
                    $yg = $result['ygc'];
                    break;
                case "D":
                    $yg = $result['ygd'];
                    break;
            }

            $ka_quota = new Kaquota;
            $ka_quota->yg = $yg;
            $ka_quota->ygb = 0;
            $ka_quota->ygc = 0;
            $ka_quota->ygd = 0;
            $ka_quota->xx = $result['xx'];
            $ka_quota->xxx = $result['xxx'];
            $ka_quota->ds = $result['ds'];
            $ka_quota->style = $result['style'];
            $ka_quota->username = $kauser;
            $ka_quota->userid = $SoftID;
            $ka_quota->lx = 0;
            $ka_quota->flag = 1;
            $ka_quota->guanid = $guanid;
            $ka_quota->zongid = $zongid;
            $ka_quota->danid = $danid;
            $ka_quota->memid = $SoftID;
            $ka_quota->abcd = $abcd;

            $ka_quota->save();

            $response['message'] = "Kamem added successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getMacaoKadanSuperior(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();
            $zongid = $request_data["zong_id"] ?? "";

            $kadan_superior = Kaguan::where("lx", 2)->get(["id", "kauser", "sf", "cs"]);

            $data = array();

            foreach($kadan_superior as $item) {
                $result1 = Kaguan::select(DB::raw("SUM(cs) As sum_m"))
                    ->where("lx", 3)
                    ->where("zongid", $item["id"])
                    ->first();
                $mumul = 0;
                if (isset($result1)) {
                    $mumul = $result1["sum_m"];
                }
                $result2 = MacaoKatan::select(DB::raw("SUM(sum_m) As sum_m"))
                    ->where("username", $item["kauser"])
                    ->first();
                $mkmkl = 0;
                if (isset($result2)) {
                    $mkmkl = $result2["sum_m"];
                }
                $cscs = $item["cs"] - $mumul - $mkmkl;
                array_push($data, array("label" => $item["kauser"] . "--" . $cscs, "value" => $item["id"]));
            }

            if ($zongid != "") {
                $result = Kaquota::where("userid", $zongid)
                    ->where("lx", 0)
                    ->where("flag", 0)
                    ->get();
            } else {
                $result = Kaguands::where("lx", 0)->get();
            }
            $response["data"] = $data;
            $response["other_data"] = $result;
            $response['message'] = "Kadan Superior Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addMacaoKadan(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "zong_id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $zong_id = $request_data["zong_id"];
            $kauser = $request_data["kauser"];
            $stat = $request_data["stat"];
            $kapassword = $request_data["kapassword"];
            $xm = $request_data["xm"];
            $kyx = $request_data["kyx"];
            $cs = $request_data["cs"];
            $xy = $request_data["xy"];
            $tmb = $request_data["tmb"];
            $rs = $request_data["rs"];
            $sj = $request_data["sj"];
            $sf = $request_data["sf"];
            $pz = $request_data["pz"];
            $other_data = $request_data["other_data"];
            $other_data = json_decode($other_data, true);

            $ka_guan = Kaguan::where("kauser", $kauser)->first();

            if (isset($ka_guan)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_mem = Kamem::where("kauser", $kauser)->first();

            if (isset($ka_mem)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_zi = Kazi::where("kauser", $kauser)->first();

            if (isset($ka_zi)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $pass = Hash::make($kapassword);
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $ka_zong = Kaguan::find($zong_id);
            $guan = $ka_zong["guan"];
            $guanid = $ka_zong["guanid"];
            $zongid = $ka_zong["id"];
            $zong = $ka_zong["kauser"];

            $ka_guan = new Kaguan;
            $ka_guan->kauser = $kauser;
            $ka_guan->kapassword = $pass;
            $ka_guan->xm = $xm;
            $ka_guan->cs = $cs;
            $ka_guan->rs = $rs;
            $ka_guan->ts = $cs;
            $ka_guan->tmb = $tmb;
            $ka_guan->sj = $sj;
            $ka_guan->sf = $sf;
            $ka_guan->guan = $guan;
            $ka_guan->zong = $zong;
            $ka_guan->stat = $stat;
            $ka_guan->guanid = $guanid;
            $ka_guan->zongid = $zongid;
            $ka_guan->look = 0;
            $ka_guan->lx = 3;
            $ka_guan->pz = $pz;
            $ka_guan->ztws = 0;
            $ka_guan->tm = 500000;
            $ka_guan->zm = 500000;
            $ka_guan->zt = 500000;
            $ka_guan->zm6 = 500000;
            $ka_guan->lm = 500000;
            $ka_guan->gg = 500000;
            $ka_guan->xx = 500000;
            $ka_guan->sx = 500000;
            $ka_guan->bb = 500000;
            $ka_guan->ws = 500000;
            $ka_guan->adddate = $text;
            $ka_guan->slogin = $text;
            $ka_guan->zlogin = $text;
            $ka_guan->sip = Utils::get_ip();
            $ka_guan->zip = Utils::get_ip();

            $ka_guan->save();

            $ka_guan = Kaguan::where("kauser", $kauser)->first();
            $SoftID = $ka_guan["id"];

            foreach($other_data as $item) {

                $ka_quota = new Kaquota;
                $ka_quota->yg = $item["yg"];
                $ka_quota->ygb = $item["ygb"];
                $ka_quota->ygc = $item["ygc"];
                $ka_quota->ygd = $item["ygd"];
                $ka_quota->xx = $item['xx'];
                $ka_quota->xxx = $item['xxx'];
                $ka_quota->ds = $item['ds'];
                $ka_quota->style = $item['style'];
                $ka_quota->username = $kauser;
                $ka_quota->userid = $SoftID;
                $ka_quota->lx = 0;
                $ka_quota->flag = 0;
                $ka_quota->guanid = $guanid;
                $ka_quota->zongid = $zongid;
                $ka_quota->danid = 0;
                $ka_quota->memid = 0;

                $ka_quota->save();

            }

            $response['message'] = "Kadan data added successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }  

    public function getMacaoKazongSuperior(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();
            $guanid = $request_data["guan_id"] ?? "";

            $kadan_superior = Kaguan::where("lx", 1)->get(["id", "kauser", "sf", "cs"]);

            $data = array();

            foreach($kadan_superior as $item) {
                $result1 = Kaguan::select(DB::raw("SUM(cs) As sum_m"))
                    ->where("lx", 2)
                    ->where("guanid", $item["id"])
                    ->first();
                $mumul = 0;
                if (isset($result1)) {
                    $mumul = $result1["sum_m"];
                }
                $result2 = MacaoKatan::select(DB::raw("SUM(sum_m) As sum_m"))
                    ->where("username", $item["kauser"])
                    ->first();
                $mkmkl = 0;
                if (isset($result2)) {
                    $mkmkl = $result2["sum_m"];
                }
                $cscs = $item["cs"] - $mumul - $mkmkl;
                array_push($data, array("label" => $item["kauser"] . "--" . $cscs, "value" => $item["id"]));
            }

            if ($guanid != "") {
                $result = Kaquota::where("userid", $guanid)
                    ->where("lx", 0)
                    ->where("flag", 0)
                    ->get();
            } else {
                $result = Kaguands::where("lx", 0)->get();
            }
            $response["data"] = $data;
            $response["other_data"] = $result;
            $response['message'] = "Kazong Superior Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addMacaoKazong(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "guan_id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $guan_id = $request_data["guan_id"];
            $kauser = $request_data["kauser"];
            $stat = $request_data["stat"];
            $kapassword = $request_data["kapassword"];
            $xm = $request_data["xm"];
            $kyx = $request_data["kyx"];
            $cs = $request_data["cs"];
            $xy = $request_data["xy"];
            $tmb = $request_data["tmb"];
            $rs = $request_data["rs"];
            $sj = $request_data["sj"];
            $sf = $request_data["sf"];
            $pz = $request_data["pz"];
            $other_data = $request_data["other_data"];
            $other_data = json_decode($other_data, true);

            $ka_guan = Kaguan::where("kauser", $kauser)->first();

            if (isset($ka_guan)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_mem = Kamem::where("kauser", $kauser)->first();

            if (isset($ka_mem)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_zi = Kazi::where("kauser", $kauser)->first();

            if (isset($ka_zi)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $pass = Hash::make($kapassword);
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $ka_guan = Kaguan::find($guan_id);
            $guan = $ka_guan["kauser"];
            $guanid = $guan_id;

            $ka_guan = new Kaguan;
            $ka_guan->kauser = $kauser;
            $ka_guan->kapassword = $pass;
            $ka_guan->xm = $xm;
            $ka_guan->cs = $cs;
            $ka_guan->rs = $rs;
            $ka_guan->ts = $cs;
            $ka_guan->tmb = $tmb;
            $ka_guan->sj = $sj;
            $ka_guan->sf = $sf;
            $ka_guan->guan = $guan;
            $ka_guan->zong = $kauser;
            $ka_guan->guanid = $guanid;
            $ka_guan->zongid = 0;
            $ka_guan->stat = $stat;
            $ka_guan->look = 0;
            $ka_guan->lx = 2;
            $ka_guan->pz = $pz;
            $ka_guan->ztws = 0;
            $ka_guan->tm = 500000;
            $ka_guan->zm = 500000;
            $ka_guan->zt = 500000;
            $ka_guan->zm6 = 500000;
            $ka_guan->lm = 500000;
            $ka_guan->gg = 500000;
            $ka_guan->xx = 500000;
            $ka_guan->sx = 500000;
            $ka_guan->bb = 500000;
            $ka_guan->ws = 500000;
            $ka_guan->adddate = $text;
            $ka_guan->slogin = $text;
            $ka_guan->zlogin = $text;
            $ka_guan->sip = Utils::get_ip();
            $ka_guan->zip = Utils::get_ip();

            $ka_guan->save();

            $ka_guan = Kaguan::where("kauser", $kauser)->first();
            $SoftID = $ka_guan["id"];

            foreach($other_data as $item) {

                $ka_quota = new Kaquota;
                $ka_quota->yg = $item["yg"];
                $ka_quota->ygb = $item["ygb"];
                $ka_quota->ygc = $item["ygc"];
                $ka_quota->ygd = $item["ygd"];
                $ka_quota->xx = $item['xx'];
                $ka_quota->xxx = $item['xxx'];
                $ka_quota->ds = $item['ds'];
                $ka_quota->style = $item['style'];
                $ka_quota->username = $kauser;
                $ka_quota->userid = $SoftID;
                $ka_quota->lx = 0;
                $ka_quota->flag = 0;
                $ka_quota->guanid = $guanid;
                $ka_quota->zongid = 0;
                $ka_quota->danid = 0;
                $ka_quota->memid = 0;

                $ka_quota->save();

            }

            $response['message'] = "Kazong data added successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }   

    public function getMacaoKaguanSuperior(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $result = Kaguands::where("lx", 0)->get();
            $response["other_data"] = $result;
            $response['message'] = "Kaguan Superior Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addMacaoKaguan(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $kauser = $request_data["kauser"];
            $stat = $request_data["stat"];
            $kapassword = $request_data["kapassword"];
            $xm = $request_data["xm"];
            $kyx = $request_data["kyx"];
            $cs = $request_data["cs"];
            $xy = $request_data["xy"];
            $tmb = $request_data["tmb"];
            $rs = $request_data["rs"];
            $sj = $request_data["sj"];
            $sf = $request_data["sf"];
            $pz = $request_data["pz"];
            $other_data = $request_data["other_data"];
            $other_data = json_decode($other_data, true);

            $ka_guan = Kaguan::where("kauser", $kauser)->first();

            if (isset($ka_guan)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_mem = Kamem::where("kauser", $kauser)->first();

            if (isset($ka_mem)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $ka_zi = Kazi::where("kauser", $kauser)->first();

            if (isset($ka_zi)) {
                $response["message"] = "这一用户名称已被占用，请得新输入！!";
                return response()->json($response, $response['status']);
            }

            $pass = Hash::make($kapassword);
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            $ka_guan = new Kaguan;
            $ka_guan->kauser = $kauser;
            $ka_guan->kapassword = $pass;
            $ka_guan->xm = $xm;
            $ka_guan->cs = $cs;
            $ka_guan->rs = $rs;
            $ka_guan->ts = $cs;
            $ka_guan->tmb = $tmb;
            $ka_guan->sj = $sj;
            $ka_guan->sf = $sf;
            $ka_guan->guan = $kauser;
            $ka_guan->zong = $kauser;
            $ka_guan->guanid = 0;
            $ka_guan->zongid = 0;
            $ka_guan->stat = $stat;
            $ka_guan->look = 0;
            $ka_guan->lx = 1;
            $ka_guan->pz = $pz;
            $ka_guan->ztws = 0;
            $ka_guan->tm = 500000;
            $ka_guan->zm = 500000;
            $ka_guan->zt = 500000;
            $ka_guan->zm6 = 500000;
            $ka_guan->lm = 500000;
            $ka_guan->gg = 500000;
            $ka_guan->xx = 500000;
            $ka_guan->sx = 500000;
            $ka_guan->bb = 500000;
            $ka_guan->ws = 500000;
            $ka_guan->adddate = $text;
            $ka_guan->slogin = $text;
            $ka_guan->zlogin = $text;
            $ka_guan->sip = Utils::get_ip();
            $ka_guan->zip = Utils::get_ip();

            $ka_guan->save();

            $ka_guan = Kaguan::where("kauser", $kauser)->first();
            $SoftID = $ka_guan["id"];

            foreach($other_data as $item) {

                $ka_quota = new Kaquota;
                $ka_quota->yg = $item["yg"];
                $ka_quota->ygb = $item["ygb"];
                $ka_quota->ygc = $item["ygc"];
                $ka_quota->ygd = $item["ygd"];
                $ka_quota->xx = $item['xx'];
                $ka_quota->xxx = $item['xxx'];
                $ka_quota->ds = $item['ds'];
                $ka_quota->style = $item['style'];
                $ka_quota->username = $kauser;
                $ka_quota->userid = $SoftID;
                $ka_quota->lx = 0;
                $ka_quota->flag = 0;
                $ka_quota->guanid = 0;
                $ka_quota->zongid = 0;
                $ka_quota->danid = 0;
                $ka_quota->memid = 0;

                $ka_quota->save();
            }

            $response['message'] = "Kaguan data added successfully!";
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
