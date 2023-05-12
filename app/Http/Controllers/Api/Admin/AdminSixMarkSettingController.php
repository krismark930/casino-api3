<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Config;
use App\Utils\Utils;
use App\Models\KasxNumber;
use App\Models\Kabl;
use App\Models\MacaoKabl;
use App\Models\Kadrop;
use App\Models\Kaguands;

class AdminSixMarkSettingController extends Controller
{
    public function getWebsiteSetting(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $config = Config::query()->first(["id","webname","weburl","tm","tmdx","tmps","zm","zmdx","ggpz","sanimal","affice","fenb","haffice2","a1","a2","a3","a10","opwww"]);

            $config["five_elements_1"] = Utils::Get_wx1_Color(25);
            $config["five_elements_2"] = Utils::Get_wx1_Color(26);
            $config["five_elements_3"] = Utils::Get_wx1_Color(27);
            $config["five_elements_4"] = Utils::Get_wx1_Color(28);
            $config["five_elements_5"] = Utils::Get_wx1_Color(29);

            $response["data"] = $config;
            $response['message'] = "Website Setting Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateWebsiteSetting(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $sanimal = $request_data["sanimal"];
            $a10 = $request_data["a10"];
            $five_elements_1 = $request_data["five_elements_1"];
            $five_elements_2 = $request_data["five_elements_2"];
            $five_elements_3 = $request_data["five_elements_3"];
            $five_elements_4 = $request_data["five_elements_4"];
            $five_elements_5 = $request_data["five_elements_5"];
            $opwww = $request_data["opwww"];
            $affice = $request_data["affice"];
            $haffice2 = $request_data["haffice2"];
            $webname = $request_data["webname"];
            $weburl = $request_data["weburl"];
            $tm = $request_data["tm"];
            $tmdx = $request_data["tmdx"];
            $tmps = $request_data["tmps"];
            $zm = $request_data["zm"];
            $zmdx = $request_data["zmdx"];
            $ggpz = $request_data["ggpz"];

            $sxnum1="01,13,25,37,49";
            $sxnum2="02,14,26,38";
            $sxnum3="03,15,27,39";
            $sxnum4="04,16,28,40";
            $sxnum5="05,17,29,41";
            $sxnum6="06,18,30,42";
            $sxnum7="07,19,31,43";
            $sxnum8="08,20,32,44";
            $sxnum9="09,21,33,45";
            $sxnum10="10,22,34,46";
            $sxnum11="11,23,35,47";
            $sxnum12="12,24,36,48";

            switch($sanimal) {
                case 1:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum2]);
                    break;
                case 2:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum3]);
                    break;
                case 3:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum4]);
                    break;
                case 4:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum5]);
                    break;
                case 5:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum6]);
                    break;
                case 6:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum7]);
                    break;
                case 7:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum8]);
                    break;
                case 8:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum9]);
                    break;
                case 9:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum10]);
                    break;
                case 10:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum11]);
                    break;
                case 11:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum12]);
                    break;
                case 12:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum1]);
                    break;

            }

            KasxNumber::where("id", 25)->update(["m_number" => $five_elements_1]);
            KasxNumber::where("id", 26)->update(["m_number" => $five_elements_2]);
            KasxNumber::where("id", 27)->update(["m_number" => $five_elements_3]);
            KasxNumber::where("id", 28)->update(["m_number" => $five_elements_4]);
            KasxNumber::where("id", 29)->update(["m_number" => $five_elements_5]);

            Config::query()->update([
                "a10" => $a10,
                "opwww" => $opwww,
                "affice" => $affice,
                "haffice2" => $haffice2,
                "webname" => $webname,
                "sanimal" => $sanimal,
                "weburl" => $weburl,
                "tm" => $tm,
                "tmdx" => $tmdx,
                "tmps" => $tmps,
                "zm" => $zm,
                "zmdx" => $zmdx,
                "ggpz" => $ggpz,
            ]);

            for ($i = 0; $i <= 49; $i++) {
                $ka_bl = Kabl::where("class2", "特A")->where("class3", $i)->first();
                $rate = $ka_bl["rate"] + $tm;
                Kabl::where("class2", "特B")->where("class3", $i)->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "正A")->where("class3", $i)->first();
                $rate = $ka_bl["rate"] + $zm;
                Kabl::where("class2", "正B")->where("class3", $i)->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "特A")->where("class3", "单")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                Kabl::where("class2", "特B")->where("class3", "单")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "特A")->where("class3", "双")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                Kabl::where("class2", "特B")->where("class3", "双")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "特A")->where("class3", "大")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                Kabl::where("class2", "特B")->where("class3", "大")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "特A")->where("class3", "小")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                Kabl::where("class2", "特B")->where("class3", "小")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "特A")->where("class3", "合单")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                Kabl::where("class2", "特B")->where("class3", "合单")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "特A")->where("class3", "合双")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                Kabl::where("class2", "特B")->where("class3", "合双")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "特A")->where("class3", "红波")->first();
                $rate = $ka_bl["rate"] + $tmps;
                Kabl::where("class2", "特B")->where("class3", "红波")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "特A")->where("class3", "蓝波")->first();
                $rate = $ka_bl["rate"] + $tmps;
                Kabl::where("class2", "特B")->where("class3", "蓝波")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "特A")->where("class3", "绿波")->first();
                $rate = $ka_bl["rate"] + $tmps;
                Kabl::where("class2", "特B")->where("class3", "绿波")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "正A")->where("class3", "总单")->first();
                $rate = $ka_bl["rate"] + $zmdx;
                Kabl::where("class2", "特B")->where("class3", "总单")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "正A")->where("class3", "总双")->first();
                $rate = $ka_bl["rate"] + $zmdx;
                Kabl::where("class2", "特B")->where("class3", "总双")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "正A")->where("class3", "总大")->first();
                $rate = $ka_bl["rate"] + $zmdx;
                Kabl::where("class2", "特B")->where("class3", "总大")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = Kabl::where("class2", "正A")->where("class3", "总小")->first();
                $rate = $ka_bl["rate"] + $zmdx;
                Kabl::where("class2", "特B")->where("class3", "总小")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
            }

            $response['message'] = "Website Setting Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }   

    public function getOddDiffSetting(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $config = Config::query()->first(["id","btm","ctm","dtm","btmdx","ctmdx","dtmdx","bzt","czt","dzt","bztdx","cztdx","dztdx","bzm","czm","dzm","bzmdx","czmdx","dzmdx","bzm6","czm6","dzm6","bbb","cbb","dbb","bsx","csx","dsx","bsx6","csx6","dsx6","bsxp","csxp","dsxp","bth","cth","dth","bzx","czx","dzx","blx","clx","dlx"]);

            $response["data"] = $config;
            $response['message'] = "OddDiff Setting Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateOddDiffSetting(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $btm = $request_data["btm"];
            $ctm = $request_data["ctm"];
            $dtm = $request_data["dtm"];
            $btmdx = $request_data["btmdx"];
            $ctmdx = $request_data["ctmdx"];
            $dtmdx = $request_data["dtmdx"];
            $bzt = $request_data["bzt"];
            $czt = $request_data["czt"];
            $dzt = $request_data["dzt"];
            $bztdx = $request_data["bztdx"];
            $cztdx = $request_data["cztdx"];
            $dztdx = $request_data["dztdx"];
            $bzm = $request_data["bzm"];
            $czm = $request_data["czm"];
            $dzm = $request_data["dzm"];
            $bzmdx = $request_data["bzmdx"];
            $czmdx = $request_data["czmdx"];
            $dzmdx = $request_data["dzmdx"];
            $bzm6 = $request_data["bzm6"];
            $czm6 = $request_data["czm6"];
            $dzm6 = $request_data["dzm6"];
            $bbb = $request_data["bbb"];
            $cbb = $request_data["cbb"];
            $dbb = $request_data["dbb"];
            $bsx = $request_data["bsx"];
            $csx = $request_data["csx"];
            $dsx = $request_data["dsx"];
            $bsx6 = $request_data["bsx6"];
            $csx6 = $request_data["csx6"];
            $dsx6 = $request_data["dsx6"];
            $bsxp = $request_data["bsxp"];
            $csxp = $request_data["csxp"];
            $dsxp = $request_data["dsxp"];
            $bth = $request_data["bth"];
            $cth = $request_data["cth"];
            $dth = $request_data["dth"];
            $bzx = $request_data["bzx"];
            $czx = $request_data["czx"];
            $dzx = $request_data["dzx"];
            $blx = $request_data["blx"];
            $clx = $request_data["clx"];
            $dlx = $request_data["dlx"];

            $config = Config::query()->update([
                "btm" => $btm,
                "ctm" => $ctm,
                "dtm" => $dtm,
                "btmdx" => $btmdx,
                "ctmdx" => $ctmdx,
                "dtmdx" => $dtmdx,
                "bzt" => $bzt,
                "czt" => $czt,
                "dzt" => $dzt,
                "bztdx" => $bztdx,
                "cztdx" => $cztdx,
                "dztdx" => $dztdx,
                "bzm" => $bzm,
                "czm" => $czm,
                "dzm" => $dzm,
                "bzmdx" => $bzmdx,
                "czmdx" => $czmdx,
                "dzmdx" => $dzmdx,
                "bzm6" => $bzm6,
                "czm6" => $czm6,
                "dzm6" => $dzm6,
                "bbb" => $bbb,
                "cbb" => $cbb,
                "dbb" => $dbb,
                "bsx" => $bsx,
                "csx" => $csx,
                "dsx" => $dsx,
                "bsx6" => $bsx6,
                "csx6" => $csx6,
                "dsx6" => $dsx6,
                "bsxp" => $bsxp,
                "csxp" => $csxp,
                "dsxp" => $dsxp,
                "bth" => $bth,
                "cth" => $cth,
                "dth" => $dth,
                "bzx" => $bzx,
                "czx" => $czx,
                "dzx" => $dzx,
                "blx" => $blx,
                "clx" => $clx,
                "dlx" => $dlx
            ]);
            
            $response['message'] = "OddDiff Setting Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getAutoPrecipitation(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ka_drop = Kadrop::orderBy("ID", "asc")->get(["ID", "drop_sort", "drop_value", "drop_unit", "low_drop"]);

            $response["data"] = $ka_drop;
            $response['message'] = "Auto Precipitation Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSingleQuota(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ka_bl = Kabl::where("class2", "特A")->get(["xr", "class3"]);

            $data = array();

            foreach($ka_bl as $item) {
                if ($item["class3"] < 50) {
                    array_push($data, array("class3" => $item["class3"], "xr" => $item["xr"]));
                }
            }

            $response["data"] = $data;
            $response['message'] = "Single Quota Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateSingleQuota(Request $request) {

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

            $data = $request_data["data"];
            $data = json_decode($data, true);

            foreach($data as $item) {
                Kabl::where("class2", "特A")
                    ->where("class3", $item["class3"])
                    ->update([
                        "xr" => $item["xr"],
                    ]);
                Kabl::where("class2", "特B")
                    ->where("class3", $item["class3"])
                    ->update([
                        "xr" => $item["xr"],
                    ]);
            }

            $response['message'] = "Single Quota Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getWaterSetting(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ka_guands = Kaguands::where("style", "六合彩")->where("lx", 0)->orderBy("id", "asc")->get();

            $response["data"] = $ka_guands;
            $response['message'] = "Water Setting Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateWatherSetting(Request $request) {

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

            $data = $request_data["data"];
            $data = json_decode($data, true);

            foreach($data as $item) {
                $ka_guands = Kaguands::find($item["id"]);
                $ka_guands->yg = $item["yg"];
                $ka_guands->ygb = $item["ygb"];
                $ka_guands->ygc = $item["ygc"];
                $ka_guands->ygd = $item["ygd"];
                $ka_guands->xx = $item["xx"];
                $ka_guands->xxx = $item["xxx"];
                $ka_guands->save();
            }

            $response['message'] = "Water Setting Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    
    public function getMacaoWebsiteSetting(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $config = Config::query()->first(["id","webname","weburl","tm","tmdx","tmps","zm","zmdx","ggpz","sanimal","affice","fenb","haffice2","a1","a2","a3","a10","opwww"]);

            $config["five_elements_1"] = Utils::Get_wx1_Color(25);
            $config["five_elements_2"] = Utils::Get_wx1_Color(26);
            $config["five_elements_3"] = Utils::Get_wx1_Color(27);
            $config["five_elements_4"] = Utils::Get_wx1_Color(28);
            $config["five_elements_5"] = Utils::Get_wx1_Color(29);

            $response["data"] = $config;
            $response['message'] = "Website Setting Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoWebsiteSetting(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $sanimal = $request_data["sanimal"];
            $a10 = $request_data["a10"];
            $five_elements_1 = $request_data["five_elements_1"];
            $five_elements_2 = $request_data["five_elements_2"];
            $five_elements_3 = $request_data["five_elements_3"];
            $five_elements_4 = $request_data["five_elements_4"];
            $five_elements_5 = $request_data["five_elements_5"];
            $opwww = $request_data["opwww"];
            $affice = $request_data["affice"];
            $haffice2 = $request_data["haffice2"];
            $webname = $request_data["webname"];
            $weburl = $request_data["weburl"];
            $tm = $request_data["tm"];
            $tmdx = $request_data["tmdx"];
            $tmps = $request_data["tmps"];
            $zm = $request_data["zm"];
            $zmdx = $request_data["zmdx"];
            $ggpz = $request_data["ggpz"];

            $sxnum1="01,13,25,37,49";
            $sxnum2="02,14,26,38";
            $sxnum3="03,15,27,39";
            $sxnum4="04,16,28,40";
            $sxnum5="05,17,29,41";
            $sxnum6="06,18,30,42";
            $sxnum7="07,19,31,43";
            $sxnum8="08,20,32,44";
            $sxnum9="09,21,33,45";
            $sxnum10="10,22,34,46";
            $sxnum11="11,23,35,47";
            $sxnum12="12,24,36,48";

            switch($sanimal) {
                case 1:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum2]);
                    break;
                case 2:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum3]);
                    break;
                case 3:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum4]);
                    break;
                case 4:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum5]);
                    break;
                case 5:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum6]);
                    break;
                case 6:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum7]);
                    break;
                case 7:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum8]);
                    break;
                case 8:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum9]);
                    break;
                case 9:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum10]);
                    break;
                case 10:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum11]);
                    break;
                case 11:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum1]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum12]);
                    break;
                case 12:
                    KasxNumber::where("id", 1)->update(["m_number" => $sxnum12]);
                    KasxNumber::where("id", 2)->update(["m_number" => $sxnum11]);
                    KasxNumber::where("id", 3)->update(["m_number" => $sxnum10]);
                    KasxNumber::where("id", 4)->update(["m_number" => $sxnum9]);
                    KasxNumber::where("id", 5)->update(["m_number" => $sxnum8]);
                    KasxNumber::where("id", 6)->update(["m_number" => $sxnum7]);
                    KasxNumber::where("id", 7)->update(["m_number" => $sxnum6]);
                    KasxNumber::where("id", 8)->update(["m_number" => $sxnum5]);
                    KasxNumber::where("id", 9)->update(["m_number" => $sxnum4]);
                    KasxNumber::where("id", 10)->update(["m_number" => $sxnum3]);
                    KasxNumber::where("id", 11)->update(["m_number" => $sxnum2]);
                    KasxNumber::where("id", 12)->update(["m_number" => $sxnum1]);
                    break;

            }

            KasxNumber::where("id", 25)->update(["m_number" => $five_elements_1]);
            KasxNumber::where("id", 26)->update(["m_number" => $five_elements_2]);
            KasxNumber::where("id", 27)->update(["m_number" => $five_elements_3]);
            KasxNumber::where("id", 28)->update(["m_number" => $five_elements_4]);
            KasxNumber::where("id", 29)->update(["m_number" => $five_elements_5]);

            Config::query()->update([
                "a10" => $a10,
                "opwww" => $opwww,
                "affice" => $affice,
                "haffice2" => $haffice2,
                "webname" => $webname,
                "sanimal" => $sanimal,
                "weburl" => $weburl,
                "tm" => $tm,
                "tmdx" => $tmdx,
                "tmps" => $tmps,
                "zm" => $zm,
                "zmdx" => $zmdx,
                "ggpz" => $ggpz,
            ]);

            for ($i = 0; $i <= 49; $i++) {
                $ka_bl = MacaoKabl::where("class2", "特A")->where("class3", $i)->first();
                $rate = $ka_bl["rate"] + $tm;
                MacaoKabl::where("class2", "特B")->where("class3", $i)->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "正A")->where("class3", $i)->first();
                $rate = $ka_bl["rate"] + $zm;
                MacaoKabl::where("class2", "正B")->where("class3", $i)->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "特A")->where("class3", "单")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                MacaoKabl::where("class2", "特B")->where("class3", "单")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "特A")->where("class3", "双")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                MacaoKabl::where("class2", "特B")->where("class3", "双")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "特A")->where("class3", "大")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                MacaoKabl::where("class2", "特B")->where("class3", "大")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "特A")->where("class3", "小")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                MacaoKabl::where("class2", "特B")->where("class3", "小")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "特A")->where("class3", "合单")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                MacaoKabl::where("class2", "特B")->where("class3", "合单")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "特A")->where("class3", "合双")->first();
                $rate = $ka_bl["rate"] + $tmdx;
                MacaoKabl::where("class2", "特B")->where("class3", "合双")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "特A")->where("class3", "红波")->first();
                $rate = $ka_bl["rate"] + $tmps;
                MacaoKabl::where("class2", "特B")->where("class3", "红波")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "特A")->where("class3", "蓝波")->first();
                $rate = $ka_bl["rate"] + $tmps;
                MacaoKabl::where("class2", "特B")->where("class3", "蓝波")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "特A")->where("class3", "绿波")->first();
                $rate = $ka_bl["rate"] + $tmps;
                MacaoKabl::where("class2", "特B")->where("class3", "绿波")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "正A")->where("class3", "总单")->first();
                $rate = $ka_bl["rate"] + $zmdx;
                MacaoKabl::where("class2", "特B")->where("class3", "总单")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "正A")->where("class3", "总双")->first();
                $rate = $ka_bl["rate"] + $zmdx;
                MacaoKabl::where("class2", "特B")->where("class3", "总双")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "正A")->where("class3", "总大")->first();
                $rate = $ka_bl["rate"] + $zmdx;
                MacaoKabl::where("class2", "特B")->where("class3", "总大")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
                $ka_bl = MacaoKabl::where("class2", "正A")->where("class3", "总小")->first();
                $rate = $ka_bl["rate"] + $zmdx;
                MacaoKabl::where("class2", "特B")->where("class3", "总小")->update([
                    "rate" => $rate,
                    "blrate" => $rate,
                ]);
            }

            $response['message'] = "Website Setting Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }   

    public function getMacaoOddDiffSetting(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $config = Config::query()->first(["id","btm","ctm","dtm","btmdx","ctmdx","dtmdx","bzt","czt","dzt","bztdx","cztdx","dztdx","bzm","czm","dzm","bzmdx","czmdx","dzmdx","bzm6","czm6","dzm6","bbb","cbb","dbb","bsx","csx","dsx","bsx6","csx6","dsx6","bsxp","csxp","dsxp","bth","cth","dth","bzx","czx","dzx","blx","clx","dlx"]);

            $response["data"] = $config;
            $response['message'] = "OddDiff Setting Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoOddDiffSetting(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $btm = $request_data["btm"];
            $ctm = $request_data["ctm"];
            $dtm = $request_data["dtm"];
            $btmdx = $request_data["btmdx"];
            $ctmdx = $request_data["ctmdx"];
            $dtmdx = $request_data["dtmdx"];
            $bzt = $request_data["bzt"];
            $czt = $request_data["czt"];
            $dzt = $request_data["dzt"];
            $bztdx = $request_data["bztdx"];
            $cztdx = $request_data["cztdx"];
            $dztdx = $request_data["dztdx"];
            $bzm = $request_data["bzm"];
            $czm = $request_data["czm"];
            $dzm = $request_data["dzm"];
            $bzmdx = $request_data["bzmdx"];
            $czmdx = $request_data["czmdx"];
            $dzmdx = $request_data["dzmdx"];
            $bzm6 = $request_data["bzm6"];
            $czm6 = $request_data["czm6"];
            $dzm6 = $request_data["dzm6"];
            $bbb = $request_data["bbb"];
            $cbb = $request_data["cbb"];
            $dbb = $request_data["dbb"];
            $bsx = $request_data["bsx"];
            $csx = $request_data["csx"];
            $dsx = $request_data["dsx"];
            $bsx6 = $request_data["bsx6"];
            $csx6 = $request_data["csx6"];
            $dsx6 = $request_data["dsx6"];
            $bsxp = $request_data["bsxp"];
            $csxp = $request_data["csxp"];
            $dsxp = $request_data["dsxp"];
            $bth = $request_data["bth"];
            $cth = $request_data["cth"];
            $dth = $request_data["dth"];
            $bzx = $request_data["bzx"];
            $czx = $request_data["czx"];
            $dzx = $request_data["dzx"];
            $blx = $request_data["blx"];
            $clx = $request_data["clx"];
            $dlx = $request_data["dlx"];

            $config = Config::query()->update([
                "btm" => $btm,
                "ctm" => $ctm,
                "dtm" => $dtm,
                "btmdx" => $btmdx,
                "ctmdx" => $ctmdx,
                "dtmdx" => $dtmdx,
                "bzt" => $bzt,
                "czt" => $czt,
                "dzt" => $dzt,
                "bztdx" => $bztdx,
                "cztdx" => $cztdx,
                "dztdx" => $dztdx,
                "bzm" => $bzm,
                "czm" => $czm,
                "dzm" => $dzm,
                "bzmdx" => $bzmdx,
                "czmdx" => $czmdx,
                "dzmdx" => $dzmdx,
                "bzm6" => $bzm6,
                "czm6" => $czm6,
                "dzm6" => $dzm6,
                "bbb" => $bbb,
                "cbb" => $cbb,
                "dbb" => $dbb,
                "bsx" => $bsx,
                "csx" => $csx,
                "dsx" => $dsx,
                "bsx6" => $bsx6,
                "csx6" => $csx6,
                "dsx6" => $dsx6,
                "bsxp" => $bsxp,
                "csxp" => $csxp,
                "dsxp" => $dsxp,
                "bth" => $bth,
                "cth" => $cth,
                "dth" => $dth,
                "bzx" => $bzx,
                "czx" => $czx,
                "dzx" => $dzx,
                "blx" => $blx,
                "clx" => $clx,
                "dlx" => $dlx
            ]);
            
            $response['message'] = "OddDiff Setting Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoAutoPrecipitation(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ka_drop = Kadrop::orderBy("ID", "asc")->get(["ID", "drop_sort", "drop_value", "drop_unit", "low_drop"]);

            $response["data"] = $ka_drop;
            $response['message'] = "Auto Precipitation Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoSingleQuota(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ka_bl = MacaoKabl::where("class2", "特A")->get(["xr", "class3"]);

            $data = array();

            foreach($ka_bl as $item) {
                if ($item["class3"] < 50) {
                    array_push($data, array("class3" => $item["class3"], "xr" => $item["xr"]));
                }
            }

            $response["data"] = $data;
            $response['message'] = "Single Quota Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoSingleQuota(Request $request) {

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

            $data = $request_data["data"];
            $data = json_decode($data, true);

            foreach($data as $item) {
                MacaoKabl::where("class2", "特A")
                    ->where("class3", $item["class3"])
                    ->update([
                        "xr" => $item["xr"],
                    ]);
                MacaoKabl::where("class2", "特B")
                    ->where("class3", $item["class3"])
                    ->update([
                        "xr" => $item["xr"],
                    ]);
            }

            $response['message'] = "Single Quota Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    } 

    public function getMacaoWaterSetting(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "class1" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ka_guands = Kaguands::where("style", "六合彩")->where("lx", 0)->orderBy("id", "asc")->get();

            $response["data"] = $ka_guands;
            $response['message'] = "Water Setting Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoWatherSetting(Request $request) {

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

            $data = $request_data["data"];
            $data = json_decode($data, true);

            foreach($data as $item) {
                $ka_guands = Kaguands::find($item["id"]);
                $ka_guands->yg = $item["yg"];
                $ka_guands->ygb = $item["ygb"];
                $ka_guands->ygc = $item["ygc"];
                $ka_guands->ygd = $item["ygd"];
                $ka_guands->xx = $item["xx"];
                $ka_guands->xxx = $item["xxx"];
                $ka_guands->save();
            }

            $response['message'] = "Water Setting Data updated successfully!";
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

