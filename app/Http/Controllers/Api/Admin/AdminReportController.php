<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Kaguan;
use App\Models\Kamem;
use App\Models\KaTan;
use App\Models\MacaoKatan;
use App\Models\Kakithe;
use App\Models\MacaoKakithe;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function getAllReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $data = array();

            $sum_data = array();

            $z_re = 0;
            $z_sum = 0;
            $z_dagu = 0;
            $z_guan = 0;
            $z_zong = 0;
            $z_dai = 0;
            $z_userds = 0;
            $z_guands = 0;
            $z_zongds = 0;
            $z_daids = 0;
            $z_usersf = 0;
            $z_guansf = 0;
            $z_zongsf = 0;
            $z_daisf = 0;
            $zz_sf = 0;

            $ka_tan = KaTan::select(DB::raw('distinct(guan)'));

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
            }

            $ka_tan = $ka_tan->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $result1 = KaTan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                $result1 = $result1->where("guan", $item["guan"]);
                
                $result2 = KaTan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result2 = $result2->where("guan", $item["guan"])->where("bm", 1);
                
                $result3 = KaTan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result3 = $result3->where("guan", $item["guan"])->where("bm", 0);

                if ($period !== "") {
                    $result1 = $result1->where("kithe", $period);
                    $result2 = $result2->where("kithe", $period);
                    $result3 = $result3->where("kithe", $period);
                } else {
                    $result1 = $result1->whereBetween("adddate", [$from_time, $end_time]);
                    $result2 = $result2->whereBetween("adddate", [$from_time, $end_time]);
                    $result3 = $result3->whereBetween("adddate", [$from_time, $end_time]);
                }

                if ($class2 != "") {
                    $result1 = $result1->where("class2", $class2);
                    $result2 = $result2->where("class2", $class2);
                    $result3 = $result3->where("class2", $class2);
                }

                $result1 = $result1->first();
                $result2 = $result2->first();
                $result3 = $result3->first();

                $re = $result1['re'];

                $sum_m = $result1['sum_m'];
                $dagu_zc = $result1['dagu_zc'];
                $guan_zc = $result1['guan_zc'];
                $zong_zc = $result1['zong_zc'];
                $dai_zc = $result1['dai_zc'];


                $z_usersf += $result2['sum_m'] + $result3['sum_m'];
                $z_guansf += $result2['guansf'] + $result3['guansf'];
                $z_zongsf += $result2['zongsf'] + $result3['zongsf'];
                $z_daisf += $result2['daisf'] + $result3['daisf'];
                $z_re += $result1['re'];
                $z_sum += $result1['sum_m'];
                $z_dagu += $result1['dagu_zc'];
                $z_guan += $result1['guan_zc'];
                $z_zong += $result1['zong_zc'];
                $z_dai += $result1['dai_zc'];
                $z_userds += $result2['user_ds'] + $result3['user_ds'];
                $z_guands += $result2['guan_ds'] + $result3['guan_ds'];
                $z_zongds += $result2['zong_ds'] + $result3['zong_ds'];
                $z_daids += $result2['dai_ds'] + $result3['dai_ds'];

                $usersf = $result2['sum_m'] + $result3['sum_m'];
                $guansf = $result2['guansf'] + $result3['guansf'];
                $zongsf = $result2['zongsf'] + $result3['zongsf'];
                $daisf = $result2['daisf'] + $result3['daisf'];

                $zz_sf += 0 - $usersf - $guansf - $zongsf - $daisf;

                $ka_guan = Kaguan::where("kauser", $item["guan"])->orderBy("id", "asc")->first();

                $xm = "";

                if (isset($ka_guan)) {
                    // $xm = "<font color=ff6600> (" . $ka_guan['xm'] . ")</font>";
                    $xm = $ka_guan["xm"];
                }

                $temp_data = array(
                    "no" => $no,
                    "guan" => $item["guan"],
                    "re" => $result1["re"],
                    "xm" => $xm,
                    "sum_m" => $result1["sum_m"],
                    "user_ds" => number_format(($result2['user_ds'] + $result3['user_ds']), 2),
                    "sum_m_1" => number_format(($result2['sum_m'] + $result3['sum_m']), 2),
                    "sum_m_1_color" => $result2['sum_m'] + $result3['sum_m'] >= 0 ? 'black' : 'red',
                    "guan_ds" => number_format(($result2['guan_ds'] + $result3['guan_ds']), 2),
                    "guan_ds_color" => $result2['guan_ds'] + $result3['guan_ds'] >= 0 ? 'black' : 'red',
                    "guan_sf_ds" => number_format(($result2['guansf'] + $result3['guansf'] - $result2['guan_ds'] - $result3['guan_ds']), 2),
                    "guan_sf_ds_color" => $result2['guansf'] + $result3['guansf'] - $result2['guan_ds'] - $result3['guan_ds'] >= 0 ? 'black' : 'red',
                    "guan_sf" => number_format(($result2['guansf'] + $result3['guansf']), 2),
                    "guan_sf_color" => $result2['guansf'] + $result3['guansf'] >= 0 ? 'black' : 'red',
                    "mix_sf" => number_format((0 - $usersf - $guansf - $zongsf - $daisf), 2),
                    "guan_mix_sf_color" => 0 - $usersf - $guansf - $zongsf - $daisf >= 0 ? 'black' : 'red',
                );

                array_push($data, $temp_data);

                $no++;

            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_userds" => number_format($z_userds, 2),
                "z_guands" => number_format($z_guands, 2),
                "z_usersf" => number_format($z_usersf, 2),
                "z_usersf_color" =>$z_usersf >= 0 ? "black" : "red",
                "z_guansf" => number_format($z_guansf, 2),
                "z_guansf_color" =>$z_guansf >= 0 ? "black" : "red",
                "zz_sf" => number_format($zz_sf, 2),
                "zz_sf_color" =>$zz_sf >= 0 ? "black" : "red",
                "z_guansf_ds" => number_format(($z_guansf - $z_guands), 2),
                "z_guansf_ds_color" => ($z_guansf - $z_guands) >= 0 ? "black" : "red",
            );

            $response["data"] = $data;
            $response["sum_data"] = $sum_data;
            $response['message'] = "All Report Data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

    public function getKaguanReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
                "guan_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $guan_name = $request_data["guan_name"];
            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $data = array();

            $sum_data = array();

            $z_re = 0;
            $z_sum = 0;
            $z_dagu = 0;
            $z_guan = 0;
            $z_zong = 0;
            $z_dai = 0;
            $z_userds = 0;
            $z_guands = 0;
            $z_zongds = 0;
            $z_daids = 0;
            $z_usersf = 0;
            $z_guansf = 0;
            $z_zongsf = 0;
            $z_daisf = 0;
            $zz_sf = 0;
            $zong_sf = 0;

            $ka_tan = KaTan::select(DB::raw('distinct(zong)'))->where("guan", $guan_name);

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
            }

            $ka_tan = $ka_tan->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $result1 = KaTan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                $result1 = $result1->where("zong", $item["zong"]);
                
                $result2 = KaTan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result2 = $result2->where("zong", $item["zong"])->where("bm", 1);
                
                $result3 = KaTan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result3 = $result3->where("zong", $item["zong"])->where("bm", 0);

                if ($period !== "") {
                    $result1 = $result1->where("kithe", $period);
                    $result2 = $result2->where("kithe", $period);
                    $result3 = $result3->where("kithe", $period);
                } else {
                    $result1 = $result1->whereBetween("adddate", [$from_time, $end_time]);
                    $result2 = $result2->whereBetween("adddate", [$from_time, $end_time]);
                    $result3 = $result3->whereBetween("adddate", [$from_time, $end_time]);
                }

                if ($class2 != "") {
                    $result1 = $result1->where("class2", $class2);
                    $result2 = $result2->where("class2", $class2);
                    $result3 = $result3->where("class2", $class2);
                }

                $result1 = $result1->first();
                $result2 = $result2->first();
                $result3 = $result3->first();

                $re = $result1['re'];

                $sum_m = $result1['sum_m'];
                $dagu_zc = $result1['dagu_zc'];
                $guan_zc = $result1['guan_zc'];
                $zong_zc = $result1['zong_zc'];
                $dai_zc = $result1['dai_zc'];


                $z_usersf += $result2['sum_m'] + $result3['sum_m'];
                $z_guansf += $result2['guansf'] + $result3['guansf'];
                $z_zongsf += $result2['zongsf'] + $result3['zongsf'];
                $z_daisf += $result2['daisf'] + $result3['daisf'];
                $z_re += $result1['re'];
                $z_sum += $result1['sum_m'];
                $z_dagu += $result1['dagu_zc'];
                $z_guan += $result1['guan_zc'];
                $z_zong += $result1['zong_zc'];
                $z_dai += $result1['dai_zc'];
                $z_userds += $result2['user_ds'] + $result3['user_ds'];
                $z_guands += $result2['guan_ds'] + $result3['guan_ds'];
                $z_zongds += $result2['zong_ds'] + $result3['zong_ds'];
                $z_daids += $result2['dai_ds'] + $result3['dai_ds'];

                $usersf = $result2['sum_m'] + $result3['sum_m'];
                $guansf = $result2['guansf'] + $result3['guansf'];
                $zongsf = $result2['zongsf'] + $result3['zongsf'];
                $daisf = $result2['daisf'] + $result3['daisf'];

                $zz_sf += 0 - $usersf - $guansf - $zongsf - $daisf;
                $zong_sf+=0-$usersf-$zongsf-$daisf;

                $ka_zong = Kaguan::where("kauser", $item["zong"])->orderBy("id", "asc")->first();

                $xm = "";

                if (isset($ka_zong)) {
                    $xm = $ka_zong["xm"];
                }

                $temp_data = array(
                    "no" => $no,
                    "zong" => $item["zong"],
                    "re" => $result1["re"],
                    "xm" => $xm,
                    "sum_m" => $result1["sum_m"],
                    "user_ds" => number_format(($result2['user_ds'] + $result3['user_ds']), 2),
                    "sum_m_1" => number_format(($result2['sum_m'] + $result3['sum_m']), 2),
                    "sum_m_1_color" => $result2['sum_m'] + $result3['sum_m'] >= 0 ? 'black' : 'red',
                    "guan_ds" => number_format(($result2['guan_ds'] + $result3['guan_ds']), 2),
                    "guan_ds_color" => $result2['guan_ds'] + $result3['guan_ds'] >= 0 ? 'black' : 'red',
                    "guan_sf_ds" => number_format(($result2['guansf'] + $result3['guansf'] - $result2['guan_ds'] - $result3['guan_ds']), 2),
                    "guan_sf_ds_color" => $result2['guansf'] + $result3['guansf'] - $result2['guan_ds'] - $result3['guan_ds'] >= 0 ? 'black' : 'red',
                    "guan_sf" => number_format(($result2['guansf'] + $result3['guansf']), 2),
                    "guan_sf_color" => $result2['guansf'] + $result3['guansf'] >= 0 ? 'black' : 'red',
                    "guan_mix_sf" => number_format((0 - $usersf - $guansf - $zongsf - $daisf), 2),
                    "guan_mix_sf_color" => 0 - $usersf - $guansf - $zongsf - $daisf >= 0 ? 'black' : 'red',
                    "zong_ds" => number_format(($result2['zong_ds'] + $result3['zong_ds']), 2),
                    "zong_ds_color" => $result2['zong_ds'] + $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf_ds" => number_format(($result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds']), 2),
                    "zong_sf_ds_color" => $result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf" => number_format(($result2['zongsf'] + $result3['zongsf']), 2),
                    "zong_sf_color" => $result2['zongsf'] + $result3['zongsf'] >= 0 ? 'black' : 'red',
                    "zong_mix_sf" => number_format((0 - $usersf - $zongsf - $daisf), 2),
                    "zong_mix_sf_color" => 0 - $usersf - $zongsf - $daisf >= 0 ? 'black' : 'red',
                );

                array_push($data, $temp_data);

                $no++;

            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_userds" => number_format($z_userds, 2),
                "z_guands" => number_format($z_guands, 2),
                "z_zongds" => number_format($z_zongds, 2),
                "z_usersf" => number_format($z_usersf, 2),
                "z_usersf_color" =>$z_usersf >= 0 ? "black" : "red",
                "z_guansf" => number_format($z_guansf, 2),
                "z_guansf_color" =>$z_guansf >= 0 ? "black" : "red",
                "z_zongsf" => number_format($z_zongsf, 2),
                "z_zongsf_color" =>$z_zongsf >= 0 ? "black" : "red",
                "zz_sf" => number_format($zz_sf, 2),
                "zz_sf_color" =>$zz_sf >= 0 ? "black" : "red",
                "zong_sf" => number_format($zong_sf, 2),
                "zong_sf_color" =>$zong_sf >= 0 ? "black" : "red",
                "z_zongsf_ds" => number_format(($z_zongsf - $z_zongds), 2),
                "z_zongsf_ds_color" => ($z_zongsf - $z_zongds) >= 0 ? "black" : "red",
                "z_guansf_ds" => number_format(($z_guansf - $z_guands), 2),
                "z_guansf_ds_color" => ($z_guansf - $z_guands) >= 0 ? "black" : "red",
            );

            $response["data"] = $data;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Guan Report data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getKazongReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
                "guan_name" => "required|string",
                "zong_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $guan_name = $request_data["guan_name"];
            $zong_name = $request_data["zong_name"];
            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $data = array();

            $sum_data = array();

            $z_re = 0;
            $z_sum = 0;
            $z_dagu = 0;
            $z_guan = 0;
            $z_zong = 0;
            $z_dai = 0;
            $z_userds = 0;
            $z_guands = 0;
            $z_zongds = 0;
            $z_daids = 0;
            $z_usersf = 0;
            $z_guansf = 0;
            $z_zongsf = 0;
            $z_daisf = 0;
            $zz_sf = 0;
            $zong_sf = 0;
            $dai_sf = 0;

            $ka_tan = KaTan::select(DB::raw('distinct(dai)'))->where("zong", $zong_name);

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
            }

            $ka_tan = $ka_tan->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $result1 = KaTan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                $result1 = $result1->where("dai", $item["dai"]);
                
                $result2 = KaTan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result2 = $result2->where("dai", $item["dai"])->where("bm", 1);
                
                $result3 = KaTan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result3 = $result3->where("dai", $item["dai"])->where("bm", 0);

                if ($period !== "") {
                    $result1 = $result1->where("kithe", $period);
                    $result2 = $result2->where("kithe", $period);
                    $result3 = $result3->where("kithe", $period);
                } else {
                    $result1 = $result1->whereBetween("adddate", [$from_time, $end_time]);
                    $result2 = $result2->whereBetween("adddate", [$from_time, $end_time]);
                    $result3 = $result3->whereBetween("adddate", [$from_time, $end_time]);
                }

                if ($class2 != "") {
                    $result1 = $result1->where("class2", $class2);
                    $result2 = $result2->where("class2", $class2);
                    $result3 = $result3->where("class2", $class2);
                }

                $result1 = $result1->first();
                $result2 = $result2->first();
                $result3 = $result3->first();

                $re = $result1['re'];

                $sum_m = $result1['sum_m'];
                $dagu_zc = $result1['dagu_zc'];
                $guan_zc = $result1['guan_zc'];
                $zong_zc = $result1['zong_zc'];
                $dai_zc = $result1['dai_zc'];


                $z_usersf += $result2['sum_m'] + $result3['sum_m'];
                $z_guansf += $result2['guansf'] + $result3['guansf'];
                $z_zongsf += $result2['zongsf'] + $result3['zongsf'];
                $z_daisf += $result2['daisf'] + $result3['daisf'];
                $z_re += $result1['re'];
                $z_sum += $result1['sum_m'];
                $z_dagu += $result1['dagu_zc'];
                $z_guan += $result1['guan_zc'];
                $z_zong += $result1['zong_zc'];
                $z_dai += $result1['dai_zc'];
                $z_userds += $result2['user_ds'] + $result3['user_ds'];
                $z_guands += $result2['guan_ds'] + $result3['guan_ds'];
                $z_zongds += $result2['zong_ds'] + $result3['zong_ds'];
                $z_daids += $result2['dai_ds'] + $result3['dai_ds'];

                $usersf = $result2['sum_m'] + $result3['sum_m'];
                $guansf = $result2['guansf'] + $result3['guansf'];
                $zongsf = $result2['zongsf'] + $result3['zongsf'];
                $daisf = $result2['daisf'] + $result3['daisf'];

                $zz_sf += 0 - $usersf - $zongsf - $daisf;
                $zong_sf+=0-$usersf-$zongsf-$daisf;
                $dai_sf+=0-$usersf-$daisf;

                $ka_dai = Kaguan::where("kauser", $item["dai"])->orderBy("id", "asc")->first();

                $xm = "";

                if (isset($ka_dai)) {
                    $xm = $ka_dai["xm"];
                }

                $temp_data = array(
                    "no" => $no,
                    "dai" => $item["dai"],
                    "re" => $result1["re"],
                    "xm" => $xm,
                    "sum_m" => $result1["sum_m"],
                    "user_ds" => number_format(($result2['user_ds'] + $result3['user_ds']), 2),
                    "sum_m_1" => number_format(($result2['sum_m'] + $result3['sum_m']), 2),
                    "sum_m_1_color" => $result2['sum_m'] + $result3['sum_m'] >= 0 ? 'black' : 'red',
                    "dai_ds" => number_format(($result2['dai_ds'] + $result3['dai_ds']), 2),
                    "dai_ds_color" => $result2['dai_ds'] + $result3['dai_ds'] >= 0 ? 'black' : 'red',
                    "dai_sf_ds" => number_format(($result2['daisf'] + $result3['daisf'] - $result2['dai_ds'] - $result3['dai_ds']), 2),
                    "dai_sf_ds_color" => $result2['daisf'] + $result3['daisf'] - $result2['dai_ds'] - $result3['dai_ds'] >= 0 ? 'black' : 'red',
                    "dai_sf" => number_format(($result2['daisf'] + $result3['daisf']), 2),
                    "dai_sf_color" => $result2['daisf'] + $result3['daisf'] >= 0 ? 'black' : 'red',
                    "dai_mix_sf" => number_format((0 - $usersf - $daisf), 2),
                    "dai_mix_sf_color" => 0 - $usersf - $daisf >= 0 ? 'black' : 'red',
                    "zong_ds" => number_format(($result2['zong_ds'] + $result3['zong_ds']), 2),
                    "zong_ds_color" => $result2['zong_ds'] + $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf_ds" => number_format(($result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds']), 2),
                    "zong_sf_ds_color" => $result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf" => number_format(($result2['zongsf'] + $result3['zongsf']), 2),
                    "zong_sf_color" => $result2['zongsf'] + $result3['zongsf'] >= 0 ? 'black' : 'red',
                    "zong_mix_sf" => number_format((0 - $usersf - $zongsf - $daisf), 2),
                    "zong_mix_sf_color" => 0 - $usersf - $zongsf - $daisf >= 0 ? 'black' : 'red',
                );

                array_push($data, $temp_data);

                $no++;

            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_userds" => number_format($z_userds, 2),
                "z_daids" => number_format($z_daids, 2),
                "z_zongds" => number_format($z_zongds, 2),
                "z_usersf" => number_format($z_usersf, 2),
                "z_usersf_color" =>$z_usersf >= 0 ? "black" : "red",
                "z_daisf" => number_format($z_daisf, 2),
                "z_daisf_color" =>$z_daisf >= 0 ? "black" : "red",
                "z_zongsf" => number_format($z_zongsf, 2),
                "z_zongsf_color" =>$z_zongsf >= 0 ? "black" : "red",
                "zz_sf" => number_format($zz_sf, 2),
                "zz_sf_color" =>$zz_sf >= 0 ? "black" : "red",
                "dai_sf" => number_format($dai_sf, 2),
                "zong_sf_color" =>$zong_sf >= 0 ? "black" : "red",
                "z_zongsf_ds" => number_format(($z_zongsf - $z_zongds), 2),
                "z_zongsf_ds_color" => ($z_zongsf - $z_zongds) >= 0 ? "black" : "red",
                "z_daisf_ds" => number_format(($z_daisf - $z_daids), 2),
                "z_daisf_ds_color" => ($z_daisf - $z_daids) >= 0 ? "black" : "red",
            );

            $response["data"] = $data;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Zong Report data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getKadaiReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
                "dai_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $dai_name = $request_data["dai_name"];
            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $data = array();

            $sum_data = array();

            $z_re = 0;
            $z_sum = 0;
            $z_dagu = 0;
            $z_guan = 0;
            $z_zong = 0;
            $z_dai = 0;
            $z_userds = 0;
            $z_guands = 0;
            $z_zongds = 0;
            $z_daids = 0;
            $z_usersf = 0;
            $z_guansf = 0;
            $z_zongsf = 0;
            $z_daisf = 0;
            $zz_sf = 0;
            $zong_sf = 0;
            $dai_sf = 0;

            $ka_tan = KaTan::select(DB::raw('distinct(username)'))->where("dai", $dai_name);

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
            }

            $ka_tan = $ka_tan->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $result1 = KaTan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                $result1 = $result1->where("username", $item["username"]);
                
                $result2 = KaTan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result2 = $result2->where("username", $item["username"])->where("bm", 1);
                
                $result3 = KaTan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result3 = $result3->where("username", $item["username"])->where("bm", 0);

                if ($period !== "") {
                    $result1 = $result1->where("kithe", $period);
                    $result2 = $result2->where("kithe", $period);
                    $result3 = $result3->where("kithe", $period);
                } else {
                    $result1 = $result1->whereBetween("adddate", [$from_time, $end_time]);
                    $result2 = $result2->whereBetween("adddate", [$from_time, $end_time]);
                    $result3 = $result3->whereBetween("adddate", [$from_time, $end_time]);
                }

                if ($class2 != "") {
                    $result1 = $result1->where("class2", $class2);
                    $result2 = $result2->where("class2", $class2);
                    $result3 = $result3->where("class2", $class2);
                }

                $result1 = $result1->first();
                $result2 = $result2->first();
                $result3 = $result3->first();

                $re = $result1['re'];

                $sum_m = $result1['sum_m'];
                $dagu_zc = $result1['dagu_zc'];
                $guan_zc = $result1['guan_zc'];
                $zong_zc = $result1['zong_zc'];
                $dai_zc = $result1['dai_zc'];


                $z_usersf += $result2['sum_m'] + $result3['sum_m'];
                $z_guansf += $result2['guansf'] + $result3['guansf'];
                $z_zongsf += $result2['zongsf'] + $result3['zongsf'];
                $z_daisf += $result2['daisf'] + $result3['daisf'];
                $z_re += $result1['re'];
                $z_sum += $result1['sum_m'];
                $z_dagu += $result1['dagu_zc'];
                $z_guan += $result1['guan_zc'];
                $z_zong += $result1['zong_zc'];
                $z_dai += $result1['dai_zc'];
                $z_userds += $result2['user_ds'] + $result3['user_ds'];
                $z_guands += $result2['guan_ds'] + $result3['guan_ds'];
                $z_zongds += $result2['zong_ds'] + $result3['zong_ds'];
                $z_daids += $result2['dai_ds'] + $result3['dai_ds'];

                $usersf = $result2['sum_m'] + $result3['sum_m'];
                $guansf = $result2['guansf'] + $result3['guansf'];
                $zongsf = $result2['zongsf'] + $result3['zongsf'];
                $daisf = $result2['daisf'] + $result3['daisf'];

                $zz_sf += 0 - $usersf - $daisf;
                $zong_sf+=0-$usersf-$zongsf-$daisf;
                $dai_sf+=0-$usersf-$daisf;

                $ka_mem = Kamem::where("kauser", $item["username"])->orderBy("id", "asc")->first();

                $xm = "";

                if (isset($ka_mem)) {
                    $xm = $ka_mem["xm"];
                }

                $temp_data = array(
                    "no" => $no,
                    "username" => $item["username"],
                    "re" => $result1["re"],
                    "xm" => $xm,
                    "sum_m" => $result1["sum_m"],
                    "user_ds" => number_format(($result2['user_ds'] + $result3['user_ds']), 2),
                    "sum_m_1" => number_format(($result2['sum_m'] + $result3['sum_m']), 2),
                    "sum_m_1_color" => $result2['sum_m'] + $result3['sum_m'] >= 0 ? 'black' : 'red',
                    "dai_ds" => number_format(($result2['dai_ds'] + $result3['dai_ds']), 2),
                    "dai_ds_color" => $result2['dai_ds'] + $result3['dai_ds'] >= 0 ? 'black' : 'red',
                    "dai_sf_ds" => number_format(($result2['daisf'] + $result3['daisf'] - $result2['dai_ds'] - $result3['dai_ds']), 2),
                    "dai_sf_ds_color" => $result2['daisf'] + $result3['daisf'] - $result2['dai_ds'] - $result3['dai_ds'] >= 0 ? 'black' : 'red',
                    "dai_sf" => number_format(($result2['daisf'] + $result3['daisf']), 2),
                    "dai_sf_color" => $result2['daisf'] + $result3['daisf'] >= 0 ? 'black' : 'red',
                    "dai_mix_sf" => number_format((0 - $usersf - $daisf), 2),
                    "dai_mix_sf_color" => 0 - $usersf - $daisf >= 0 ? 'black' : 'red',
                    "zong_ds" => number_format(($result2['zong_ds'] + $result3['zong_ds']), 2),
                    "zong_ds_color" => $result2['zong_ds'] + $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf_ds" => number_format(($result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds']), 2),
                    "zong_sf_ds_color" => $result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf" => number_format(($result2['zongsf'] + $result3['zongsf']), 2),
                    "zong_sf_color" => $result2['zongsf'] + $result3['zongsf'] >= 0 ? 'black' : 'red',
                );

                array_push($data, $temp_data);

                $no++;

            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_userds" => number_format($z_userds, 2),
                "z_daids" => number_format($z_daids, 2),
                "z_zongds" => number_format($z_zongds, 2),
                "z_usersf" => number_format($z_usersf, 2),
                "z_usersf_color" =>$z_usersf >= 0 ? "black" : "red",
                "z_daisf" => number_format($z_daisf, 2),
                "z_daisf_color" =>$z_daisf >= 0 ? "black" : "red",
                "z_zongsf" => number_format($z_zongsf, 2),
                "z_zongsf_color" =>$z_zongsf >= 0 ? "black" : "red",
                "dai_sf" => number_format($dai_sf, 2),
                "dai_sf_color" =>$dai_sf >= 0 ? "black" : "red",
                "dai_sf" => number_format($dai_sf, 2),
                "zong_sf_color" =>$zong_sf >= 0 ? "black" : "red",
                "z_zongsf_ds" => number_format(($z_zongsf - $z_zongds), 2),
                "z_zongsf_ds_color" => ($z_zongsf - $z_zongds) >= 0 ? "black" : "red",
                "z_daisf_ds" => number_format(($z_daisf - $z_daids), 2),
                "z_daisf_ds_color" => ($z_daisf - $z_daids) >= 0 ? "black" : "red",
            );

            $response["data"] = $data;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Dai Report data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }  

    public function getKauserReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
                "user_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user_name = $request_data["user_name"];
            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $sum_data = array();

            $z_re=0;
            $z_sum=0;
            $z_dagu=0;
            $z_guan=0;
            $z_zong=0;
            $z_dai=0;
            $re=0;
            $z_user=0;
            $z_userds=0;
            $z_daids=0;

            $ka_tan = KaTan::where("username", $user_name);

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
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

                $z_userds += $item['sum_m'] * abs($item['user_ds'])/100;

                if ($item["bm"] === 1) {
                    $z_user+=$item['sum_m']*$item['rate']-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                    $z_dai+=$item['sum_m']*$item['dai_zc']/10-$item['sum_m']*$item['rate']*$item['dai_zc']/10+$item['sum_m']*($item['dai_ds']-$item['user_ds'])/100*(10-$item['dai_zc'])/10-$item['sum_m']*$item['user_ds']/100*($item['dai_zc'])/10;
                } else if($item["bm"] === 0) {
                    $z_user+=-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                    $z_dai+=$item['sum_m']*Abs($item['dai_ds']-$item['user_ds'])/100+$item['sum_m']*$item['dai_zc']/10-$item['sum_m']*($item['dai_zc'])/10*$item['dai_ds']/100;
                }

                if ($item["bm"] !== 2) {
                    $z_daids+=$item['sum_m']*abs($item['dai_ds']-$item['user_ds'])/100*(10-$item['dai_zc'])/10;
                }

                $no++;
                $z_re++;
            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_user" => number_format($z_user, 2),
                "z_user_color" => $z_user >= 0 ? "black" : "red",
                "z_userds" => number_format($z_userds, 2),
                "z_dai" => number_format($z_daids, 2),
                "z_dai_color" => $z_dai >= 0 ? "black" : "red",
                "z_daids" => number_format($z_daids, 2),
            );

            $response["data"] = $ka_tan;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Dai Report data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getTotalBill(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $data = array();

            $sum_data = array();

            $z_re = 0;
            $z_sum = 0;
            $z_dagu = 0;
            $z_guan = 0;
            $z_zong = 0;
            $z_dai = 0;
            $z_userds = 0;
            $z_guands = 0;
            $z_zongds = 0;
            $z_daids = 0;
            $z_usersf = 0;
            $z_guansf = 0;
            $z_zongsf = 0;
            $z_daisf = 0;
            $zz_sf = 0;
            $zong_sf = 0;
            $dai_sf = 0;
            $d = array("日", "一", "二", "三", "四", "五", "六");

            $ka_kithe = Kakithe::orderBy('nn', 'desc')->get(['nn', 'nd']);

            $no = 1;

            foreach($ka_kithe as $item) {

                $result = KaTan::where("kithe", $item["nn"])->first();

                if (isset($result)) {

                    $result1 = KaTan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                    $result1 = $result1->where("kithe", $item["nn"]);
                    
                    $result2 = KaTan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                    $result2 = $result2->where("kithe", $item["nn"])->where("bm", 1);
                    
                    $result3 = KaTan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                    $result3 = $result3->where("kithe", $item["nn"])->where("bm", 0);

                    $result1 = $result1->first();
                    $result2 = $result2->first();
                    $result3 = $result3->first();

                    $re = $result1['re'];

                    $sum_m = $result1['sum_m'];
                    $dagu_zc = $result1['dagu_zc'];
                    $guan_zc = $result1['guan_zc'];
                    $zong_zc = $result1['zong_zc'];
                    $dai_zc = $result1['dai_zc'];


                    $z_usersf += $result2['sum_m'] + $result3['sum_m'];
                    $z_guansf += $result2['guansf'] + $result3['guansf'];
                    $z_zongsf += $result2['zongsf'] + $result3['zongsf'];
                    $z_daisf += $result2['daisf'] + $result3['daisf'];
                    $z_re += $result1['re'];
                    $z_sum += $result1['sum_m'];
                    $z_dagu += $result1['dagu_zc'];
                    $z_guan += $result1['guan_zc'];
                    $z_zong += $result1['zong_zc'];
                    $z_dai += $result1['dai_zc'];
                    $z_userds += $result2['user_ds'] + $result3['user_ds'];
                    $z_guands += $result2['guan_ds'] + $result3['guan_ds'];
                    $z_zongds += $result2['zong_ds'] + $result3['zong_ds'];
                    $z_daids += $result2['dai_ds'] + $result3['dai_ds'];

                    $usersf = $result2['sum_m'] + $result3['sum_m'];
                    $guansf = $result2['guansf'] + $result3['guansf'];
                    $zongsf = $result2['zongsf'] + $result3['zongsf'];
                    $daisf = $result2['daisf'] + $result3['daisf'];

                    $zz_sf += 0 - $usersf - $daisf;
                    $zong_sf += 0 - $usersf - $zongsf - $daisf;
                    $dai_sf += 0 - $usersf - $daisf;

                    $nd = substr($item['nd'], 0, 10)."星期".$d[date("w", strtotime($item['nd']))];;

                    if ($sum_m > 0) {

                        $temp_data = array(
                            "no" => $no,
                            "nn" => $item["nn"],
                            "nd" => $nd,
                            "sum_m" => $result1["sum_m"],
                            "user_ds" => number_format(($result2['user_ds'] + $result3['user_ds']), 2),
                            "sum_m_1" => number_format(($result2['sum_m'] + $result3['sum_m']), 2),
                            "sum_m_1_color" => $result2['sum_m'] + $result3['sum_m'] >= 0 ? 'black' : 'red',
                        );

                        array_push($data, $temp_data);

                        $no++;

                    }

                }

            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_userds" => number_format($z_userds, 2),
                "z_usersf" => number_format($z_usersf, 2),
                "z_usersf_color" =>$z_usersf >= 0 ? "black" : "red",
            );

            $response["data"] = $data;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Total Bill Data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSubBill(Request $request) {

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

            $sum_data = array();

            $z_re=0;
            $z_sum=0;
            $z_dagu=0;
            $z_guan=0;
            $z_zong=0;
            $z_dai=0;
            $re=0;
            $z_user=0;
            $z_userds=0;
            $z_daids=0;

            $ka_tan = KaTan::where("kithe", $period)->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $z_sum += $item["sum_m"];

                if ($item["bm"] === 1) {
                    $z_user+=$item['sum_m']*$item['rate']-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                } else if($item["bm"] === 0) {
                    $z_user+=-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                }

                if ($item["bm"] !== 2) {
                    $z_userds += $item['sum_m'] * abs($item['user_ds'])/100;
                }

                $class4 = "";

                if ($item["class1"] === "过关") {
                    $show1 = array_filter(explode(",", $item['class2']));
                    $show2 = array_filter(explode(",", $item['class3']));
                    $k = 0;
                    foreach($show1 as $show_item) {
                        $class4 = $class4."<span style='color: #ff0000'>".$show_item."&nbsp;".$show2[$k]."</span> @ &nbsp;<span style='color: #ff6600'><b>".$show2[$k + 1]."</b></span><br>";
                        $k = $k + 2;
                    }
                } else {
                    $class4 = $class4."<font color=ff0000>".$item['class2'].":</font>";
                    $class4 = $class4."<font color=ff6600>".$item['class3']."</font>";
                }

                $item["class4"] = $class4;

                $no++;
                $z_re++;
            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_user" => number_format($z_user, 2),
                "z_userds" => number_format($z_userds, 2),
            );

            $response["data"] = $ka_tan;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Sub Bill Data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }


    public function getMacaoAllReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $data = array();

            $sum_data = array();

            $z_re = 0;
            $z_sum = 0;
            $z_dagu = 0;
            $z_guan = 0;
            $z_zong = 0;
            $z_dai = 0;
            $z_userds = 0;
            $z_guands = 0;
            $z_zongds = 0;
            $z_daids = 0;
            $z_usersf = 0;
            $z_guansf = 0;
            $z_zongsf = 0;
            $z_daisf = 0;
            $zz_sf = 0;

            $ka_tan = MacaoKatan::select(DB::raw('distinct(guan)'));

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
            }

            $ka_tan = $ka_tan->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                $result1 = $result1->where("guan", $item["guan"]);
                
                $result2 = MacaoKatan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result2 = $result2->where("guan", $item["guan"])->where("bm", 1);
                
                $result3 = MacaoKatan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result3 = $result3->where("guan", $item["guan"])->where("bm", 0);

                if ($period !== "") {
                    $result1 = $result1->where("kithe", $period);
                    $result2 = $result2->where("kithe", $period);
                    $result3 = $result3->where("kithe", $period);
                } else {
                    $result1 = $result1->whereBetween("adddate", [$from_time, $end_time]);
                    $result2 = $result2->whereBetween("adddate", [$from_time, $end_time]);
                    $result3 = $result3->whereBetween("adddate", [$from_time, $end_time]);
                }

                if ($class2 != "") {
                    $result1 = $result1->where("class2", $class2);
                    $result2 = $result2->where("class2", $class2);
                    $result3 = $result3->where("class2", $class2);
                }

                $result1 = $result1->first();
                $result2 = $result2->first();
                $result3 = $result3->first();

                $re = $result1['re'];

                $sum_m = $result1['sum_m'];
                $dagu_zc = $result1['dagu_zc'];
                $guan_zc = $result1['guan_zc'];
                $zong_zc = $result1['zong_zc'];
                $dai_zc = $result1['dai_zc'];


                $z_usersf += $result2['sum_m'] + $result3['sum_m'];
                $z_guansf += $result2['guansf'] + $result3['guansf'];
                $z_zongsf += $result2['zongsf'] + $result3['zongsf'];
                $z_daisf += $result2['daisf'] + $result3['daisf'];
                $z_re += $result1['re'];
                $z_sum += $result1['sum_m'];
                $z_dagu += $result1['dagu_zc'];
                $z_guan += $result1['guan_zc'];
                $z_zong += $result1['zong_zc'];
                $z_dai += $result1['dai_zc'];
                $z_userds += $result2['user_ds'] + $result3['user_ds'];
                $z_guands += $result2['guan_ds'] + $result3['guan_ds'];
                $z_zongds += $result2['zong_ds'] + $result3['zong_ds'];
                $z_daids += $result2['dai_ds'] + $result3['dai_ds'];

                $usersf = $result2['sum_m'] + $result3['sum_m'];
                $guansf = $result2['guansf'] + $result3['guansf'];
                $zongsf = $result2['zongsf'] + $result3['zongsf'];
                $daisf = $result2['daisf'] + $result3['daisf'];

                $zz_sf += 0 - $usersf - $guansf - $zongsf - $daisf;

                $ka_guan = Kaguan::where("kauser", $item["guan"])->orderBy("id", "asc")->first();

                $xm = "";

                if (isset($ka_guan)) {
                    // $xm = "<font color=ff6600> (" . $ka_guan['xm'] . ")</font>";
                    $xm = $ka_guan["xm"];
                }

                $temp_data = array(
                    "no" => $no,
                    "guan" => $item["guan"],
                    "re" => $result1["re"],
                    "xm" => $xm,
                    "sum_m" => $result1["sum_m"],
                    "user_ds" => number_format(($result2['user_ds'] + $result3['user_ds']), 2),
                    "sum_m_1" => number_format(($result2['sum_m'] + $result3['sum_m']), 2),
                    "sum_m_1_color" => $result2['sum_m'] + $result3['sum_m'] >= 0 ? 'black' : 'red',
                    "guan_ds" => number_format(($result2['guan_ds'] + $result3['guan_ds']), 2),
                    "guan_ds_color" => $result2['guan_ds'] + $result3['guan_ds'] >= 0 ? 'black' : 'red',
                    "guan_sf_ds" => number_format(($result2['guansf'] + $result3['guansf'] - $result2['guan_ds'] - $result3['guan_ds']), 2),
                    "guan_sf_ds_color" => $result2['guansf'] + $result3['guansf'] - $result2['guan_ds'] - $result3['guan_ds'] >= 0 ? 'black' : 'red',
                    "guan_sf" => number_format(($result2['guansf'] + $result3['guansf']), 2),
                    "guan_sf_color" => $result2['guansf'] + $result3['guansf'] >= 0 ? 'black' : 'red',
                    "mix_sf" => number_format((0 - $usersf - $guansf - $zongsf - $daisf), 2),
                    "guan_mix_sf_color" => 0 - $usersf - $guansf - $zongsf - $daisf >= 0 ? 'black' : 'red',
                );

                array_push($data, $temp_data);

                $no++;

            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_userds" => number_format($z_userds, 2),
                "z_guands" => number_format($z_guands, 2),
                "z_usersf" => number_format($z_usersf, 2),
                "z_usersf_color" =>$z_usersf >= 0 ? "black" : "red",
                "z_guansf" => number_format($z_guansf, 2),
                "z_guansf_color" =>$z_guansf >= 0 ? "black" : "red",
                "zz_sf" => number_format($zz_sf, 2),
                "zz_sf_color" =>$zz_sf >= 0 ? "black" : "red",
                "z_guansf_ds" => number_format(($z_guansf - $z_guands), 2),
                "z_guansf_ds_color" => ($z_guansf - $z_guands) >= 0 ? "black" : "red",
            );

            $response["data"] = $data;
            $response["sum_data"] = $sum_data;
            $response['message'] = "All Report Data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoKaguanReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
                "guan_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $guan_name = $request_data["guan_name"];
            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $data = array();

            $sum_data = array();

            $z_re = 0;
            $z_sum = 0;
            $z_dagu = 0;
            $z_guan = 0;
            $z_zong = 0;
            $z_dai = 0;
            $z_userds = 0;
            $z_guands = 0;
            $z_zongds = 0;
            $z_daids = 0;
            $z_usersf = 0;
            $z_guansf = 0;
            $z_zongsf = 0;
            $z_daisf = 0;
            $zz_sf = 0;
            $zong_sf = 0;

            $ka_tan = MacaoKatan::select(DB::raw('distinct(zong)'))->where("guan", $guan_name);

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
            }

            $ka_tan = $ka_tan->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                $result1 = $result1->where("zong", $item["zong"]);
                
                $result2 = MacaoKatan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result2 = $result2->where("zong", $item["zong"])->where("bm", 1);
                
                $result3 = MacaoKatan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result3 = $result3->where("zong", $item["zong"])->where("bm", 0);

                if ($period !== "") {
                    $result1 = $result1->where("kithe", $period);
                    $result2 = $result2->where("kithe", $period);
                    $result3 = $result3->where("kithe", $period);
                } else {
                    $result1 = $result1->whereBetween("adddate", [$from_time, $end_time]);
                    $result2 = $result2->whereBetween("adddate", [$from_time, $end_time]);
                    $result3 = $result3->whereBetween("adddate", [$from_time, $end_time]);
                }

                if ($class2 != "") {
                    $result1 = $result1->where("class2", $class2);
                    $result2 = $result2->where("class2", $class2);
                    $result3 = $result3->where("class2", $class2);
                }

                $result1 = $result1->first();
                $result2 = $result2->first();
                $result3 = $result3->first();

                $re = $result1['re'];

                $sum_m = $result1['sum_m'];
                $dagu_zc = $result1['dagu_zc'];
                $guan_zc = $result1['guan_zc'];
                $zong_zc = $result1['zong_zc'];
                $dai_zc = $result1['dai_zc'];


                $z_usersf += $result2['sum_m'] + $result3['sum_m'];
                $z_guansf += $result2['guansf'] + $result3['guansf'];
                $z_zongsf += $result2['zongsf'] + $result3['zongsf'];
                $z_daisf += $result2['daisf'] + $result3['daisf'];
                $z_re += $result1['re'];
                $z_sum += $result1['sum_m'];
                $z_dagu += $result1['dagu_zc'];
                $z_guan += $result1['guan_zc'];
                $z_zong += $result1['zong_zc'];
                $z_dai += $result1['dai_zc'];
                $z_userds += $result2['user_ds'] + $result3['user_ds'];
                $z_guands += $result2['guan_ds'] + $result3['guan_ds'];
                $z_zongds += $result2['zong_ds'] + $result3['zong_ds'];
                $z_daids += $result2['dai_ds'] + $result3['dai_ds'];

                $usersf = $result2['sum_m'] + $result3['sum_m'];
                $guansf = $result2['guansf'] + $result3['guansf'];
                $zongsf = $result2['zongsf'] + $result3['zongsf'];
                $daisf = $result2['daisf'] + $result3['daisf'];

                $zz_sf += 0 - $usersf - $guansf - $zongsf - $daisf;
                $zong_sf+=0-$usersf-$zongsf-$daisf;

                $ka_zong = Kaguan::where("kauser", $item["zong"])->orderBy("id", "asc")->first();

                $xm = "";

                if (isset($ka_zong)) {
                    $xm = $ka_zong["xm"];
                }

                $temp_data = array(
                    "no" => $no,
                    "zong" => $item["zong"],
                    "re" => $result1["re"],
                    "xm" => $xm,
                    "sum_m" => $result1["sum_m"],
                    "user_ds" => number_format(($result2['user_ds'] + $result3['user_ds']), 2),
                    "sum_m_1" => number_format(($result2['sum_m'] + $result3['sum_m']), 2),
                    "sum_m_1_color" => $result2['sum_m'] + $result3['sum_m'] >= 0 ? 'black' : 'red',
                    "guan_ds" => number_format(($result2['guan_ds'] + $result3['guan_ds']), 2),
                    "guan_ds_color" => $result2['guan_ds'] + $result3['guan_ds'] >= 0 ? 'black' : 'red',
                    "guan_sf_ds" => number_format(($result2['guansf'] + $result3['guansf'] - $result2['guan_ds'] - $result3['guan_ds']), 2),
                    "guan_sf_ds_color" => $result2['guansf'] + $result3['guansf'] - $result2['guan_ds'] - $result3['guan_ds'] >= 0 ? 'black' : 'red',
                    "guan_sf" => number_format(($result2['guansf'] + $result3['guansf']), 2),
                    "guan_sf_color" => $result2['guansf'] + $result3['guansf'] >= 0 ? 'black' : 'red',
                    "guan_mix_sf" => number_format((0 - $usersf - $guansf - $zongsf - $daisf), 2),
                    "guan_mix_sf_color" => 0 - $usersf - $guansf - $zongsf - $daisf >= 0 ? 'black' : 'red',
                    "zong_ds" => number_format(($result2['zong_ds'] + $result3['zong_ds']), 2),
                    "zong_ds_color" => $result2['zong_ds'] + $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf_ds" => number_format(($result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds']), 2),
                    "zong_sf_ds_color" => $result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf" => number_format(($result2['zongsf'] + $result3['zongsf']), 2),
                    "zong_sf_color" => $result2['zongsf'] + $result3['zongsf'] >= 0 ? 'black' : 'red',
                    "zong_mix_sf" => number_format((0 - $usersf - $zongsf - $daisf), 2),
                    "zong_mix_sf_color" => 0 - $usersf - $zongsf - $daisf >= 0 ? 'black' : 'red',
                );

                array_push($data, $temp_data);

                $no++;

            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_userds" => number_format($z_userds, 2),
                "z_guands" => number_format($z_guands, 2),
                "z_zongds" => number_format($z_zongds, 2),
                "z_usersf" => number_format($z_usersf, 2),
                "z_usersf_color" =>$z_usersf >= 0 ? "black" : "red",
                "z_guansf" => number_format($z_guansf, 2),
                "z_guansf_color" =>$z_guansf >= 0 ? "black" : "red",
                "z_zongsf" => number_format($z_zongsf, 2),
                "z_zongsf_color" =>$z_zongsf >= 0 ? "black" : "red",
                "zz_sf" => number_format($zz_sf, 2),
                "zz_sf_color" =>$zz_sf >= 0 ? "black" : "red",
                "zong_sf" => number_format($zong_sf, 2),
                "zong_sf_color" =>$zong_sf >= 0 ? "black" : "red",
                "z_zongsf_ds" => number_format(($z_zongsf - $z_zongds), 2),
                "z_zongsf_ds_color" => ($z_zongsf - $z_zongds) >= 0 ? "black" : "red",
                "z_guansf_ds" => number_format(($z_guansf - $z_guands), 2),
                "z_guansf_ds_color" => ($z_guansf - $z_guands) >= 0 ? "black" : "red",
            );

            $response["data"] = $data;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Guan Report data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoKazongReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
                "guan_name" => "required|string",
                "zong_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $guan_name = $request_data["guan_name"];
            $zong_name = $request_data["zong_name"];
            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $data = array();

            $sum_data = array();

            $z_re = 0;
            $z_sum = 0;
            $z_dagu = 0;
            $z_guan = 0;
            $z_zong = 0;
            $z_dai = 0;
            $z_userds = 0;
            $z_guands = 0;
            $z_zongds = 0;
            $z_daids = 0;
            $z_usersf = 0;
            $z_guansf = 0;
            $z_zongsf = 0;
            $z_daisf = 0;
            $zz_sf = 0;
            $zong_sf = 0;
            $dai_sf = 0;

            $ka_tan = MacaoKatan::select(DB::raw('distinct(dai)'))->where("zong", $zong_name);

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
            }

            $ka_tan = $ka_tan->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                $result1 = $result1->where("dai", $item["dai"]);
                
                $result2 = MacaoKatan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result2 = $result2->where("dai", $item["dai"])->where("bm", 1);
                
                $result3 = MacaoKatan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result3 = $result3->where("dai", $item["dai"])->where("bm", 0);

                if ($period !== "") {
                    $result1 = $result1->where("kithe", $period);
                    $result2 = $result2->where("kithe", $period);
                    $result3 = $result3->where("kithe", $period);
                } else {
                    $result1 = $result1->whereBetween("adddate", [$from_time, $end_time]);
                    $result2 = $result2->whereBetween("adddate", [$from_time, $end_time]);
                    $result3 = $result3->whereBetween("adddate", [$from_time, $end_time]);
                }

                if ($class2 != "") {
                    $result1 = $result1->where("class2", $class2);
                    $result2 = $result2->where("class2", $class2);
                    $result3 = $result3->where("class2", $class2);
                }

                $result1 = $result1->first();
                $result2 = $result2->first();
                $result3 = $result3->first();

                $re = $result1['re'];

                $sum_m = $result1['sum_m'];
                $dagu_zc = $result1['dagu_zc'];
                $guan_zc = $result1['guan_zc'];
                $zong_zc = $result1['zong_zc'];
                $dai_zc = $result1['dai_zc'];


                $z_usersf += $result2['sum_m'] + $result3['sum_m'];
                $z_guansf += $result2['guansf'] + $result3['guansf'];
                $z_zongsf += $result2['zongsf'] + $result3['zongsf'];
                $z_daisf += $result2['daisf'] + $result3['daisf'];
                $z_re += $result1['re'];
                $z_sum += $result1['sum_m'];
                $z_dagu += $result1['dagu_zc'];
                $z_guan += $result1['guan_zc'];
                $z_zong += $result1['zong_zc'];
                $z_dai += $result1['dai_zc'];
                $z_userds += $result2['user_ds'] + $result3['user_ds'];
                $z_guands += $result2['guan_ds'] + $result3['guan_ds'];
                $z_zongds += $result2['zong_ds'] + $result3['zong_ds'];
                $z_daids += $result2['dai_ds'] + $result3['dai_ds'];

                $usersf = $result2['sum_m'] + $result3['sum_m'];
                $guansf = $result2['guansf'] + $result3['guansf'];
                $zongsf = $result2['zongsf'] + $result3['zongsf'];
                $daisf = $result2['daisf'] + $result3['daisf'];

                $zz_sf += 0 - $usersf - $zongsf - $daisf;
                $zong_sf+=0-$usersf-$zongsf-$daisf;
                $dai_sf+=0-$usersf-$daisf;

                $ka_dai = Kaguan::where("kauser", $item["dai"])->orderBy("id", "asc")->first();

                $xm = "";

                if (isset($ka_dai)) {
                    $xm = $ka_dai["xm"];
                }

                $temp_data = array(
                    "no" => $no,
                    "dai" => $item["dai"],
                    "re" => $result1["re"],
                    "xm" => $xm,
                    "sum_m" => $result1["sum_m"],
                    "user_ds" => number_format(($result2['user_ds'] + $result3['user_ds']), 2),
                    "sum_m_1" => number_format(($result2['sum_m'] + $result3['sum_m']), 2),
                    "sum_m_1_color" => $result2['sum_m'] + $result3['sum_m'] >= 0 ? 'black' : 'red',
                    "dai_ds" => number_format(($result2['dai_ds'] + $result3['dai_ds']), 2),
                    "dai_ds_color" => $result2['dai_ds'] + $result3['dai_ds'] >= 0 ? 'black' : 'red',
                    "dai_sf_ds" => number_format(($result2['daisf'] + $result3['daisf'] - $result2['dai_ds'] - $result3['dai_ds']), 2),
                    "dai_sf_ds_color" => $result2['daisf'] + $result3['daisf'] - $result2['dai_ds'] - $result3['dai_ds'] >= 0 ? 'black' : 'red',
                    "dai_sf" => number_format(($result2['daisf'] + $result3['daisf']), 2),
                    "dai_sf_color" => $result2['daisf'] + $result3['daisf'] >= 0 ? 'black' : 'red',
                    "dai_mix_sf" => number_format((0 - $usersf - $daisf), 2),
                    "dai_mix_sf_color" => 0 - $usersf - $daisf >= 0 ? 'black' : 'red',
                    "zong_ds" => number_format(($result2['zong_ds'] + $result3['zong_ds']), 2),
                    "zong_ds_color" => $result2['zong_ds'] + $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf_ds" => number_format(($result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds']), 2),
                    "zong_sf_ds_color" => $result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf" => number_format(($result2['zongsf'] + $result3['zongsf']), 2),
                    "zong_sf_color" => $result2['zongsf'] + $result3['zongsf'] >= 0 ? 'black' : 'red',
                    "zong_mix_sf" => number_format((0 - $usersf - $zongsf - $daisf), 2),
                    "zong_mix_sf_color" => 0 - $usersf - $zongsf - $daisf >= 0 ? 'black' : 'red',
                );

                array_push($data, $temp_data);

                $no++;

            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_userds" => number_format($z_userds, 2),
                "z_daids" => number_format($z_daids, 2),
                "z_zongds" => number_format($z_zongds, 2),
                "z_usersf" => number_format($z_usersf, 2),
                "z_usersf_color" =>$z_usersf >= 0 ? "black" : "red",
                "z_daisf" => number_format($z_daisf, 2),
                "z_daisf_color" =>$z_daisf >= 0 ? "black" : "red",
                "z_zongsf" => number_format($z_zongsf, 2),
                "z_zongsf_color" =>$z_zongsf >= 0 ? "black" : "red",
                "zz_sf" => number_format($zz_sf, 2),
                "zz_sf_color" =>$zz_sf >= 0 ? "black" : "red",
                "dai_sf" => number_format($dai_sf, 2),
                "zong_sf_color" =>$zong_sf >= 0 ? "black" : "red",
                "z_zongsf_ds" => number_format(($z_zongsf - $z_zongds), 2),
                "z_zongsf_ds_color" => ($z_zongsf - $z_zongds) >= 0 ? "black" : "red",
                "z_daisf_ds" => number_format(($z_daisf - $z_daids), 2),
                "z_daisf_ds_color" => ($z_daisf - $z_daids) >= 0 ? "black" : "red",
            );

            $response["data"] = $data;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Zong Report data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoKadaiReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
                "dai_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $dai_name = $request_data["dai_name"];
            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $data = array();

            $sum_data = array();

            $z_re = 0;
            $z_sum = 0;
            $z_dagu = 0;
            $z_guan = 0;
            $z_zong = 0;
            $z_dai = 0;
            $z_userds = 0;
            $z_guands = 0;
            $z_zongds = 0;
            $z_daids = 0;
            $z_usersf = 0;
            $z_guansf = 0;
            $z_zongsf = 0;
            $z_daisf = 0;
            $zz_sf = 0;
            $zong_sf = 0;
            $dai_sf = 0;

            $ka_tan = MacaoKatan::select(DB::raw('distinct(username)'))->where("dai", $dai_name);

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
            }

            $ka_tan = $ka_tan->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                $result1 = $result1->where("username", $item["username"]);
                
                $result2 = MacaoKatan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result2 = $result2->where("username", $item["username"])->where("bm", 1);
                
                $result3 = MacaoKatan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                $result3 = $result3->where("username", $item["username"])->where("bm", 0);

                if ($period !== "") {
                    $result1 = $result1->where("kithe", $period);
                    $result2 = $result2->where("kithe", $period);
                    $result3 = $result3->where("kithe", $period);
                } else {
                    $result1 = $result1->whereBetween("adddate", [$from_time, $end_time]);
                    $result2 = $result2->whereBetween("adddate", [$from_time, $end_time]);
                    $result3 = $result3->whereBetween("adddate", [$from_time, $end_time]);
                }

                if ($class2 != "") {
                    $result1 = $result1->where("class2", $class2);
                    $result2 = $result2->where("class2", $class2);
                    $result3 = $result3->where("class2", $class2);
                }

                $result1 = $result1->first();
                $result2 = $result2->first();
                $result3 = $result3->first();

                $re = $result1['re'];

                $sum_m = $result1['sum_m'];
                $dagu_zc = $result1['dagu_zc'];
                $guan_zc = $result1['guan_zc'];
                $zong_zc = $result1['zong_zc'];
                $dai_zc = $result1['dai_zc'];


                $z_usersf += $result2['sum_m'] + $result3['sum_m'];
                $z_guansf += $result2['guansf'] + $result3['guansf'];
                $z_zongsf += $result2['zongsf'] + $result3['zongsf'];
                $z_daisf += $result2['daisf'] + $result3['daisf'];
                $z_re += $result1['re'];
                $z_sum += $result1['sum_m'];
                $z_dagu += $result1['dagu_zc'];
                $z_guan += $result1['guan_zc'];
                $z_zong += $result1['zong_zc'];
                $z_dai += $result1['dai_zc'];
                $z_userds += $result2['user_ds'] + $result3['user_ds'];
                $z_guands += $result2['guan_ds'] + $result3['guan_ds'];
                $z_zongds += $result2['zong_ds'] + $result3['zong_ds'];
                $z_daids += $result2['dai_ds'] + $result3['dai_ds'];

                $usersf = $result2['sum_m'] + $result3['sum_m'];
                $guansf = $result2['guansf'] + $result3['guansf'];
                $zongsf = $result2['zongsf'] + $result3['zongsf'];
                $daisf = $result2['daisf'] + $result3['daisf'];

                $zz_sf += 0 - $usersf - $daisf;
                $zong_sf+=0-$usersf-$zongsf-$daisf;
                $dai_sf+=0-$usersf-$daisf;

                $ka_mem = Kamem::where("kauser", $item["username"])->orderBy("id", "asc")->first();

                $xm = "";

                if (isset($ka_mem)) {
                    $xm = $ka_mem["xm"];
                }

                $temp_data = array(
                    "no" => $no,
                    "username" => $item["username"],
                    "re" => $result1["re"],
                    "xm" => $xm,
                    "sum_m" => $result1["sum_m"],
                    "user_ds" => number_format(($result2['user_ds'] + $result3['user_ds']), 2),
                    "sum_m_1" => number_format(($result2['sum_m'] + $result3['sum_m']), 2),
                    "sum_m_1_color" => $result2['sum_m'] + $result3['sum_m'] >= 0 ? 'black' : 'red',
                    "dai_ds" => number_format(($result2['dai_ds'] + $result3['dai_ds']), 2),
                    "dai_ds_color" => $result2['dai_ds'] + $result3['dai_ds'] >= 0 ? 'black' : 'red',
                    "dai_sf_ds" => number_format(($result2['daisf'] + $result3['daisf'] - $result2['dai_ds'] - $result3['dai_ds']), 2),
                    "dai_sf_ds_color" => $result2['daisf'] + $result3['daisf'] - $result2['dai_ds'] - $result3['dai_ds'] >= 0 ? 'black' : 'red',
                    "dai_sf" => number_format(($result2['daisf'] + $result3['daisf']), 2),
                    "dai_sf_color" => $result2['daisf'] + $result3['daisf'] >= 0 ? 'black' : 'red',
                    "dai_mix_sf" => number_format((0 - $usersf - $daisf), 2),
                    "dai_mix_sf_color" => 0 - $usersf - $daisf >= 0 ? 'black' : 'red',
                    "zong_ds" => number_format(($result2['zong_ds'] + $result3['zong_ds']), 2),
                    "zong_ds_color" => $result2['zong_ds'] + $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf_ds" => number_format(($result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds']), 2),
                    "zong_sf_ds_color" => $result2['zongsf'] + $result3['zongsf'] - $result2['zong_ds'] - $result3['zong_ds'] >= 0 ? 'black' : 'red',
                    "zong_sf" => number_format(($result2['zongsf'] + $result3['zongsf']), 2),
                    "zong_sf_color" => $result2['zongsf'] + $result3['zongsf'] >= 0 ? 'black' : 'red',
                );

                array_push($data, $temp_data);

                $no++;

            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_userds" => number_format($z_userds, 2),
                "z_daids" => number_format($z_daids, 2),
                "z_zongds" => number_format($z_zongds, 2),
                "z_usersf" => number_format($z_usersf, 2),
                "z_usersf_color" =>$z_usersf >= 0 ? "black" : "red",
                "z_daisf" => number_format($z_daisf, 2),
                "z_daisf_color" =>$z_daisf >= 0 ? "black" : "red",
                "z_zongsf" => number_format($z_zongsf, 2),
                "z_zongsf_color" =>$z_zongsf >= 0 ? "black" : "red",
                "dai_sf" => number_format($dai_sf, 2),
                "dai_sf_color" =>$dai_sf >= 0 ? "black" : "red",
                "dai_sf" => number_format($dai_sf, 2),
                "zong_sf_color" =>$zong_sf >= 0 ? "black" : "red",
                "z_zongsf_ds" => number_format(($z_zongsf - $z_zongds), 2),
                "z_zongsf_ds_color" => ($z_zongsf - $z_zongds) >= 0 ? "black" : "red",
                "z_daisf_ds" => number_format(($z_daisf - $z_daids), 2),
                "z_daisf_ds_color" => ($z_daisf - $z_daids) >= 0 ? "black" : "red",
            );

            $response["data"] = $data;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Dai Report data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoKauserReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "period" => "required|numeric",
                // "class2" => "required|string",
                // "from_date" => "required|string",
                // "end_date" => "required|string",
                "user_name" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $user_name = $request_data["user_name"];
            $period = $request_data["period"] ?? "";
            $class2 = $request_data["class2"] ?? "";
            $from_date = $request_data["from_date"] ?? "";
            $end_date = $request_data["end_date"] ?? "";
            $from_time = $from_date." 00:00:00";
            $end_time = $end_date." 23:59:59";

            $sum_data = array();

            $z_re=0;
            $z_sum=0;
            $z_dagu=0;
            $z_guan=0;
            $z_zong=0;
            $z_dai=0;
            $re=0;
            $z_user=0;
            $z_userds=0;
            $z_daids=0;

            $ka_tan = MacaoKatan::where("username", $user_name);

            if ($period !== "") {
                $ka_tan = $ka_tan->where("kithe", $period);
            } else {
                $ka_tan = $ka_tan->whereBetween("adddate", [$from_time, $end_time]);
            }

            if ($class2 != "") {
                $ka_tan = $ka_tan->where("class2", $class2);
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

                $z_userds += $item['sum_m'] * abs($item['user_ds'])/100;

                if ($item["bm"] === 1) {
                    $z_user+=$item['sum_m']*$item['rate']-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                    $z_dai+=$item['sum_m']*$item['dai_zc']/10-$item['sum_m']*$item['rate']*$item['dai_zc']/10+$item['sum_m']*($item['dai_ds']-$item['user_ds'])/100*(10-$item['dai_zc'])/10-$item['sum_m']*$item['user_ds']/100*($item['dai_zc'])/10;
                } else if($item["bm"] === 0) {
                    $z_user+=-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                    $z_dai+=$item['sum_m']*Abs($item['dai_ds']-$item['user_ds'])/100+$item['sum_m']*$item['dai_zc']/10-$item['sum_m']*($item['dai_zc'])/10*$item['dai_ds']/100;
                }

                if ($item["bm"] !== 2) {
                    $z_daids+=$item['sum_m']*abs($item['dai_ds']-$item['user_ds'])/100*(10-$item['dai_zc'])/10;
                }

                $no++;
                $z_re++;
            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_user" => number_format($z_user, 2),
                "z_user_color" => $z_user >= 0 ? "black" : "red",
                "z_userds" => number_format($z_userds, 2),
                "z_dai" => number_format($z_daids, 2),
                "z_dai_color" => $z_dai >= 0 ? "black" : "red",
                "z_daids" => number_format($z_daids, 2),
            );

            $response["data"] = $ka_tan;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Dai Report data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoTotalBill(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $data = array();

            $sum_data = array();

            $z_re = 0;
            $z_sum = 0;
            $z_dagu = 0;
            $z_guan = 0;
            $z_zong = 0;
            $z_dai = 0;
            $z_userds = 0;
            $z_guands = 0;
            $z_zongds = 0;
            $z_daids = 0;
            $z_usersf = 0;
            $z_guansf = 0;
            $z_zongsf = 0;
            $z_daisf = 0;
            $zz_sf = 0;
            $zong_sf = 0;
            $dai_sf = 0;
            $d = array("日", "一", "二", "三", "四", "五", "六");

            $ka_kithe = MacaoKakithe::orderBy('nn', 'desc')->get(['nn', 'nd']);

            $no = 1;

            foreach($ka_kithe as $item) {

                $result = MacaoKatan::where("kithe", $item["nn"])->first();

                if (isset($result)) {

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                    $result1 = $result1->where("kithe", $item["nn"]);
                    
                    $result2 = MacaoKatan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                    $result2 = $result2->where("kithe", $item["nn"])->where("bm", 1);
                    
                    $result3 = MacaoKatan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                    $result3 = $result3->where("kithe", $item["nn"])->where("bm", 0);

                    $result1 = $result1->first();
                    $result2 = $result2->first();
                    $result3 = $result3->first();

                    $re = $result1['re'];

                    $sum_m = $result1['sum_m'];
                    $dagu_zc = $result1['dagu_zc'];
                    $guan_zc = $result1['guan_zc'];
                    $zong_zc = $result1['zong_zc'];
                    $dai_zc = $result1['dai_zc'];


                    $z_usersf += $result2['sum_m'] + $result3['sum_m'];
                    $z_guansf += $result2['guansf'] + $result3['guansf'];
                    $z_zongsf += $result2['zongsf'] + $result3['zongsf'];
                    $z_daisf += $result2['daisf'] + $result3['daisf'];
                    $z_re += $result1['re'];
                    $z_sum += $result1['sum_m'];
                    $z_dagu += $result1['dagu_zc'];
                    $z_guan += $result1['guan_zc'];
                    $z_zong += $result1['zong_zc'];
                    $z_dai += $result1['dai_zc'];
                    $z_userds += $result2['user_ds'] + $result3['user_ds'];
                    $z_guands += $result2['guan_ds'] + $result3['guan_ds'];
                    $z_zongds += $result2['zong_ds'] + $result3['zong_ds'];
                    $z_daids += $result2['dai_ds'] + $result3['dai_ds'];

                    $usersf = $result2['sum_m'] + $result3['sum_m'];
                    $guansf = $result2['guansf'] + $result3['guansf'];
                    $zongsf = $result2['zongsf'] + $result3['zongsf'];
                    $daisf = $result2['daisf'] + $result3['daisf'];

                    $zz_sf += 0 - $usersf - $daisf;
                    $zong_sf += 0 - $usersf - $zongsf - $daisf;
                    $dai_sf += 0 - $usersf - $daisf;

                    $nd = substr($item['nd'], 0, 10)."星期".$d[date("w", strtotime($item['nd']))];;

                    if ($sum_m > 0) {

                        $temp_data = array(
                            "no" => $no,
                            "nn" => $item["nn"],
                            "nd" => $nd,
                            "sum_m" => $result1["sum_m"],
                            "user_ds" => number_format(($result2['user_ds'] + $result3['user_ds']), 2),
                            "sum_m_1" => number_format(($result2['sum_m'] + $result3['sum_m']), 2),
                            "sum_m_1_color" => $result2['sum_m'] + $result3['sum_m'] >= 0 ? 'black' : 'red',
                        );

                        array_push($data, $temp_data);

                        $no++;

                    }

                }

            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_userds" => number_format($z_userds, 2),
                "z_usersf" => number_format($z_usersf, 2),
                "z_usersf_color" =>$z_usersf >= 0 ? "black" : "red",
            );

            $response["data"] = $data;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Total Bill Data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoSubBill(Request $request) {

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

            $sum_data = array();

            $z_re=0;
            $z_sum=0;
            $z_dagu=0;
            $z_guan=0;
            $z_zong=0;
            $z_dai=0;
            $re=0;
            $z_user=0;
            $z_userds=0;
            $z_daids=0;

            $ka_tan = MacaoKatan::where("kithe", $period)->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $z_sum += $item["sum_m"];

                if ($item["bm"] === 1) {
                    $z_user+=$item['sum_m']*$item['rate']-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                } else if($item["bm"] === 0) {
                    $z_user+=-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                }

                if ($item["bm"] !== 2) {
                    $z_userds += $item['sum_m'] * abs($item['user_ds'])/100;
                }

                $class4 = "";

                if ($item["class1"] === "过关") {
                    $show1 = array_filter(explode(",", $item['class2']));
                    $show2 = array_filter(explode(",", $item['class3']));
                    $k = 0;
                    foreach($show1 as $show_item) {
                        $class4 = $class4."<span style='color: #ff0000'>".$show_item."&nbsp;".$show2[$k]."</span> @ &nbsp;<span style='color: #ff6600'><b>".$show2[$k + 1]."</b></span><br>";
                        $k = $k + 2;
                    }
                } else {
                    $class4 = $class4."<font color=ff0000>".$item['class2'].":</font>";
                    $class4 = $class4."<font color=ff6600>".$item['class3']."</font>";
                }

                $item["class4"] = $class4;

                $no++;
                $z_re++;
            }

            $sum_data = array(
                "z_re" => $z_re,
                "z_sum" => $z_sum,
                "z_user" => number_format($z_user, 2),
                "z_userds" => number_format($z_userds, 2),
            );

            $response["data"] = $ka_tan;
            $response["sum_data"] = $sum_data;
            $response['message'] = "Sub Bill Data fatched successfully!";
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
