<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Kamem;
use App\Models\Kabl;
use App\Models\MacaoKabl;
use App\Models\Config;
use App\Utils\Utils;
use App\Models\KaTan;
use App\Models\MacaoKatan;
use App\Models\Kakithe;
use App\Models\MacaoKakithe;
use App\Models\Web\MoneyLog;
use App\Models\User;
use Carbon\Carbon;
use DB;

class KatanController extends Controller
{
    public function saveKatan(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
                'class2' => 'required|string',
                'class3' => 'required|string',
                'selectedAmount' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $class3 = $request_data["class3"];
            $selected_amount = $request_data["selectedAmount"];

            $class3_array = explode(",", $class3);
            $selected_amount_array = explode(",", $selected_amount);

            $user = $request->user();

            $configs = Config::all();

            $btm = $configs[0]['btm'];
            $ctm = $configs[0]['ctm'];
            $dtm = $configs[0]['dtm'];
            $btmdx = $configs[0]['btmdx'];
            $ctmdx = $configs[0]['ctmdx'];
            $dtmdx = $configs[0]['dtmdx'];
            $bzt = $configs[0]['bzt'];
            $czt = $configs[0]['czt'];
            $dzt = $configs[0]['dzt'];
            $bztdx = $configs[0]['bztdx'];
            $cztdx = $configs[0]['cztdx'];
            $dztdx = $configs[0]['dztdx'];
            $bzm = $configs[0]['bzm'];
            $czm = $configs[0]['czm'];
            $dzm = $configs[0]['dzm'];
            $bzmdx = $configs[0]['bzmdx'];
            $czmdx = $configs[0]['czmdx'];
            $dzmdx = $configs[0]['dzmdx'];
            $bth = $configs[0]['bth'];
            $cth = $configs[0]['cth'];
            $dth = $configs[0]['dth'];
            $bthdx = $configs[0]['bthdx'];
            $cthdx = $configs[0]['cthdx'];
            $dthdx = $configs[0]['dthdx'];
            $bzm6 = $configs[0]['bzm6'];
            $czm6 = $configs[0]['czm6'];
            $dzm6 = $configs[0]['dzm6'];
            $bsx = $configs[0]['bsx'];
            $csx = $configs[0]['csx'];
            $dsx = $configs[0]['dsx'];
            $bsx6 = $configs[0]['bsx6'];
            $csx6 = $configs[0]['csx6'];
            $dsx6 = $configs[0]['dsx6'];
            $bsxp = $configs[0]['bsxp'];
            $csxp = $configs[0]['csxp'];
            $dsxp = $configs[0]['dsxp'];
            $bbb = $configs[0]['bbb'];
            $cbb = $configs[0]['cbb'];
            $dbb = $configs[0]['dbb'];
            $bzx = $configs[0]['bzx'];
            $czx = $configs[0]['czx'];
            $dzx = $configs[0]['dzx'];
            $blx = $configs[0]['blx'];
            $clx = $configs[0]['clx'];
            $dlx = $configs[0]['dlx'];
            $jifei = $configs[0]['jifei'];
            $iszhudan = $configs[0]['iszhudan'];

            switch ($class2) {

                case "特A":

                    $XF = 11;

                    $mumu = 0;

                    $numm = 66;

                    break;

                case "特B":

                    $XF = 11;

                    $mumu = 58;

                    $numm = 66;

                    break;

                case "正A":

                    $XF = 15;

                    $mumu = 464;

                    $numm = 58;

                    break;

                case "正B":

                    $XF = 15;

                    $mumu = 517;

                    $numm = 58;

                    break;

                case "正1特":

                    $XF = 13;

                    $mumu = 116;

                    $numm = 60;

                    break;

                case "正2特":

                    $XF = 13;

                    $mumu = 174;

                    $numm = 60;

                    break;

                case "正3特":

                    $XF = 13;

                    $mumu = 232;

                    $numm = 60;

                    break;

                case "正4特":

                    $XF = 13;

                    $mumu = 290;

                    $numm = 60;

                    break;

                case "正5特":

                    $XF = 13;

                    $mumu = 348;

                    $numm = 60;

                    break;

                case "正6特":

                    $XF = 13;

                    $mumu = 406;

                    $numm = 60;

                    break;

                case "正1-6":

                    $XF = 13;

                    $mumu = 570;

                    $numm = 78;

                    break;

                case "五行":

                    $XF = 17;

                    $mumu = 712;

                    $numm = 5;

                    break;

                case "半波":

                    $XF = 25;

                    $mumu = 661;

                    $numm = 18;

                    break;

                case "半半波":

                    $XF = 25;

                    $mumu = 751;

                    $numm = 12;

                    break;

                case "正肖":

                    $XF = 25;

                    $mumu = 782;

                    $numm = 12;

                    break;

                case "七色波":

                    $XF = 25;

                    $mumu = 778;

                    $numm = 4;

                    break;

                case "尾数":

                    $XF = 27;

                    $mumu = 689;

                    $numm = 79;

                    break;

                case "特肖":

                    $XF = 23;

                    $mumu = 673;

                    $numm = 12;

                    break;

                case "一肖":

                    $XF = 23;

                    $mumu = 699;

                    $numm = 12;

                    break;

                case "正特尾数":

                    $XF = 23;

                    $mumu = 768;

                    $numm = 12;

                    break;

                case "过关":

                    $XF = 19;

                    break;

                case "连码":

                    $XF = 21;

                    break;

                default:

                    $mumu = 0;

                    $numm = 58;

                    $XF = 11;

                    break;
            }

            $ka_mem = Kamem::where("kauser", $user["UserName"])->first();

            $ts = $ka_mem["ts"];

            for ($r = 0; $r < $numm; $r++) {

                if (in_array((string)$r, $class3_array)) {
                    if ($r == 59 || $r == 60) {
                        if ($class2 == "特A") {
                            $rate_id = $r + 689;
                        } else {
                            switch ($class2) {
                                case "正1特": //1034
                                    $rate_id = $r + 975;
                                    break;
                                case "正2特": //1045
                                    $rate_id = $r + 1023;
                                    if ($r == 59) $rate_id = $r + 986;
                                    break;
                                case "正3特": //1044
                                    $rate_id = $r + 1024;
                                    if ($r == 59) $rate_id = $r + 985;
                                    break;
                                case "正4特": //1043
                                    $rate_id = $r + 1025;
                                    if ($r == 59) $rate_id = $r + 984;
                                    break;
                                case "正5特": //1042
                                    $rate_id = $r + 1026;
                                    if ($r == 59) $rate_id = $r + 983;
                                    break;
                                case "正6特": //1041
                                    $rate_id = $r + 1027;
                                    if ($r == 59) $rate_id = $r + 982;
                                    break;
                                default:
                                    $rate_id = $r + 671;
                            }
                        }
                    } else {
                        if ($class2 == "半波" && $r >= 13) {
                            $rate_id = $r + 705;
                        } else {
                            $rate_id = $r + $mumu;
                        }
                    }

                    if ($r == 61) {
                        if ($class2 == "特A") {
                            $rate_id = 795;
                        } else {
                            $rate_id = 801;
                        }
                    }

                    if ($r == 62) {
                        if ($class2 == "特A") {
                            $rate_id = 796;
                        } else {
                            $rate_id = 802;
                        }
                    }

                    if ($r == 63) {
                        if ($class2 == "特A") {
                            $rate_id = 797;
                        } else {
                            $rate_id = 803;
                        }
                    }

                    if ($r == 64) {
                        if ($class2 == "特A") {
                            $rate_id = 798;
                        } else {
                            $rate_id = 804;
                        }
                    }

                    if ($r == 65) {
                        if ($class2 == "特A") {
                            $rate_id = 799;
                        } else {
                            $rate_id = 805;
                        }
                    }

                    if ($r == 66) {
                        if ($class2 == "特A") {
                            $rate_id = 800;
                        } else {
                            $rate_id = 806;
                        }
                    }

                    if ($class2 == "正1-6") {
                        if ($r >= 1 && $r <= 7) {
                            $rate_id = $r + $mumu;
                        } elseif ($r >= 14 && $r <= 20) {
                            $rate_id = ($r - 6) + $mumu;
                        } elseif ($r >= 27 && $r <= 33) {
                            $rate_id = ($r - 12) + $mumu;
                        } elseif ($r >= 40 && $r <= 46) {
                            $rate_id = ($r - 18) + $mumu;
                        } elseif ($r >= 53 && $r <= 59) {
                            $rate_id = ($r - 24) + $mumu;
                        } elseif ($r >= 66 && $r <= 72) {
                            $rate_id = ($r - 30) + $mumu;
                        } elseif ($r >= 8 && $r <= 13) {
                            $rate_id = $r + 1039;
                        } elseif ($r >= 21 && $r <= 26) {
                            $rate_id = ($r - 7) + 1039;
                        } elseif ($r >= 34 && $r <= 39) {
                            $rate_id = ($r - 14) + 1039;
                        } elseif ($r >= 47 && $r <= 52) {
                            $rate_id = ($r - 21) + 1039;
                        } elseif ($r >= 60 && $r <= 65) {
                            $rate_id = ($r - 28) + 1039;
                        } elseif ($r >= 73 && $r <= 78) {
                            $rate_id = ($r - 35) + 1039;
                        }
                    }

                    $ka_bl = Kabl::where("id", $rate_id)->first();

                    switch ($ka_bl["class1"]) {
                        case "特码":
                            switch ($ka_bl["class3"]) {
                                case "单":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 2;
                                    $drop_sort = "单双";
                                    break;
                                case "双":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 2;
                                    $drop_sort = "单双";
                                    break;
                                case "家禽":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 24;
                                    $drop_sort = "家禽野兽";
                                    break;
                                case "野兽":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 24;
                                    $drop_sort = "家禽野兽";
                                    break;
                                case "尾大":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 32;
                                    $drop_sort = "尾大尾小";
                                    break;
                                case "尾小":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 32;
                                    $drop_sort = "尾大尾小";
                                    break;
                                case "大单":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 33;
                                    $drop_sort = "大单小单";
                                    break;
                                case "小单":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 33;
                                    $drop_sort = "大单小单";
                                    break;
                                case "大双":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 34;
                                    $drop_sort = "大双小双";
                                    break;
                                case "小双":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 34;
                                    $drop_sort = "大双小双";
                                    break;
                                case "大":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 3;
                                    $drop_sort = "大小";
                                    break;
                                case "小":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 3;
                                    $drop_sort = "大小";
                                    break;
                                case "合单":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 4;
                                    $drop_sort = "合数单双 ";
                                    break;
                                case "合双":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 4;
                                    $drop_sort = "合数单双 ";
                                    break;
                                case "红波":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 10;
                                    $drop_sort = "波色";
                                    break;
                                case "绿波":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 10;
                                    $drop_sort = "波色";
                                    break;
                                case "蓝波":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 10;
                                    $drop_sort = "波色";
                                    break;
                                default:
                                    $bmmm = $btm;
                                    $cmmm = $ctm;
                                    $dmmm = $dtm;
                                    if ($ka_bl["class2"] == "特A") {
                                        $R = 0;
                                    } else {
                                        $R = 1;
                                    }
                                    $drop_sort = "特码";
                                    break;
                            }
                            break;
                        case "正码":
                            switch ($ka_bl["class3"]) {
                                case "总单":
                                    $R = 8;
                                    $drop_sort = "总数单双";
                                    $bmmm = $bzmdx;
                                    $cmmm = $czmdx;
                                    $dmmm = $dzmdx;
                                    break;
                                case "总双":
                                    $R = 8;
                                    $drop_sort = "总数单双";
                                    $bmmm = $bzmdx;
                                    $cmmm = $czmdx;
                                    $dmmm = $dzmdx;
                                    break;
                                case "总大":
                                    $R = 9;
                                    $drop_sort = "总数大小";
                                    $bmmm = $bzmdx;
                                    $cmmm = $czmdx;
                                    $dmmm = $dzmdx;
                                    break;
                                case "总小":
                                    $R = 9;
                                    $drop_sort = "总数大小";
                                    $bmmm = $bzmdx;
                                    $cmmm = $czmdx;
                                    $dmmm = $dzmdx;
                                    break;
                                default:
                                    if ($ka_bl["class2"] == "正A") {
                                        $R = 6;
                                    } else {
                                        $R = 7;
                                    }
                                    $drop_sort = "正码";
                                    $bmmm = $bzm;
                                    $cmmm = $czm;
                                    $dmmm = $dzm;
                                    break;
                            }
                            break;
                        case "五行":
                            $R = 25;
                            $drop_sort = "五行";
                            $bmmm = $bzm6;
                            $cmmm = $czm6;
                            $dmmm = $dzm6;
                            break;
                        case "生肖":
                            switch ($ka_bl["class2"]) {
                                case "特肖":
                                    $bmmm = $bsx;
                                    $cmmm = $csx;
                                    $dmmm = $dsx;
                                    $R = 18;
                                    $drop_sort = "特肖";
                                    break;
                                case "四肖":
                                    $bmmm = 0;
                                    $cmmm = 0;
                                    $dmmm = 0;
                                    $R = 19;
                                    $drop_sort = "四肖";
                                    break;
                                case "五肖":
                                    $bmmm = 0;
                                    $cmmm = 0;
                                    $dmmm = 0;
                                    $R = 20;
                                    $drop_sort = "五肖";
                                    break;
                                case "六肖":
                                    $bmmm = $bsx6;
                                    $cmmm = $csx6;
                                    $dmmm = $dsx6;
                                    $R = 21;
                                    $drop_sort = "六肖";
                                    break;
                                case "一肖":
                                    $bmmm = $bsxp;
                                    $cmmm = $csxp;
                                    $dmmm = $dsxp;
                                    $R = 22;
                                    $drop_sort = "一肖";
                                    break;
                                case "正特尾数":
                                    $bmmm = $bsxp;
                                    $cmmm = $csxp;
                                    $dmmm = $dsxp;
                                    $R = 29;
                                    $drop_sort = "正特尾数";
                                    break;
                                default:
                                    $R = 18;
                                    $drop_sort = "特肖";
                                    $bmmm = $bsxp;
                                    $cmmm = $csxp;
                                    $dmmm = $dsxp;
                                    break;
                            }
                            break;
                        case "半波":
                            $bmmm = $bbb;
                            $cmmm = $cbb;
                            $dmmm = $dbb;
                            $R = 11;
                            $drop_sort = "半波";
                            break;
                        case "半半波":
                            $bmmm = $bbb;
                            $cmmm = $cbb;
                            $dmmm = $dbb;
                            $R = 11;
                            $drop_sort = "半半波";
                        case "正肖":
                            $bmmm = $bbb;
                            $cmmm = $cbb;
                            $dmmm = $dbb;
                            $R = 11;
                            $drop_sort = "正肖";
                        case "七色波":
                            $bmmm = $bbb;
                            $cmmm = $cbb;
                            $dmmm = $dbb;
                            $R = 11;
                            $drop_sort = "七色波";
                            break;
                        case "正特":
                            switch ($ka_bl["class3"]) {
                                case "单":
                                    $R = 2;
                                    $drop_sort = "单双";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "双":
                                    $R = 2;
                                    $drop_sort = "单双";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "大":
                                    $R = 3;
                                    $drop_sort = "大小";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "小":
                                    $R = 3;
                                    $drop_sort = "大小";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "合单":
                                    $R = 4;
                                    $drop_sort = "合数单双 ";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "合双":
                                    $R = 4;
                                    $drop_sort = "合数单双 ";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "红波":
                                    $R = 10;
                                    $drop_sort = "波色";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "绿波":
                                    $R = 10;
                                    $drop_sort = "波色";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "蓝波":
                                    $R = 10;
                                    $drop_sort = "波色";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                default:
                                    $R = 5;
                                    $drop_sort = "正特";
                                    $bmmm = $bzt;
                                    $cmmm = $czt;
                                    $dmmm = $dzt;
                                    break;
                            }
                            break;
                        case "尾数":
                            $R = 23;
                            $drop_sort = "尾数";
                            $bmmm = 0;
                            $cmmm = 0;
                            $dmmm = 0;
                            break;
                        case "正1-6":
                            $R = 38;
                            $drop_sort = "正1-6";
                            $bmmm = 0;
                            $cmmm = 0;
                            $dmmm = 0;
                            break;
                        default:
                            $R = 23;
                            $drop_sort = "尾数";
                            $bmmm = 0;
                            $cmmm = 0;
                            $dmmm = 0;
                            break;
                    }

                    //超过单期

                    $Current_Kithe_Num = Utils::getCurrentKitheNum();

                    $current_index = array_search((string)$r, $class3_array);

                    $current_bet_amount = $selected_amount_array[$current_index];

                    if ($ka_bl["class1"] == "特码" && $r <= 49) {

                        //超过单期

                        $sum_m55 = KaTan::where("Kithe", $Current_Kithe_Num)
                            ->where("class1", $ka_bl["class1"])
                            ->where("class2", $ka_bl["class2"])
                            ->where("class3", $ka_bl["class3"])
                            ->sum("sum_m");

                        if ($sum_m55 == "") {
                            $sum_m55 = 0;
                        }

                        if ($ka_bl["locked"] == 1) {

                            $response["message"] = "对不起，[" . $ka_bl["class3"] . "号]暂停下注.请反回重新选择!";

                            return response()->json($response, $response['status']);
                        }

                        if (($sum_m55 + $current_bet_amount) > $ka_bl["xr"]) {

                            $response["message"] = "对不起，[" . $ka_bl["class3"] . "]超过单项限额[" . $ka_bl["xr"] . "].请反回重新下注!";

                            return response()->json($response, $response['status']);
                        }
                    }

                    $sum_m = KaTan::where("Kithe", $Current_Kithe_Num)
                        ->where("class1", $ka_bl["class1"])
                        ->where("class2", $ka_bl["class2"])
                        ->where("class3", $ka_bl["class3"])
                        ->where("username", $user["UserName"])
                        ->sum("sum_m");

                    if (($sum_m + $current_bet_amount) > Utils::ka_memds($R, "xxx")) {

                        $response["message"] = "对不起，[" . $ka_bl["class3"] . "]超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                        return response()->json($response, $response['status']);
                    }

                    if ($ka_bl["locked"] == 1) {

                        $response["message"] = "对不起，[" . $ka_bl["class3"] . "号]暂停下注.请反回重新选择!'";

                        return response()->json($response, $response['status']);
                    }

                    switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                        case "A":
                            $rate5 = $ka_bl["rate"];
                            $Y = 1;
                            break;
                        case "B":
                            $rate5 = $ka_bl["rate"] - $bmmm;
                            $Y = 4;
                            break;
                        case "C":
                            $Y = 5;
                            $rate5 = $ka_bl["rate"] - $cmmm;
                            break;
                        case "D":
                            $rate5 = $ka_bl["rate"] - $dmmm;
                            $Y = 6;
                            break;
                        default:
                            $Y = 1;
                            $rate5 = $ka_bl["rate"];
                            break;
                    }

                    $num = Utils::randStr();
                    // $text = date("Y-m-d H:i:s");
                    $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                    $class11 = $ka_bl["class1"];
                    $class22 = $ka_bl["class2"];
                    $class33 = $ka_bl["class3"];
                    $sum_m = $current_bet_amount;
                    $user_ds = Utils::ka_memds($R, "yg");
                    // $dai_ds = Utils::ka_memdaids($R,$Y);
                    // $zong_ds = Utils::ka_memzongds($R,$Y);
                    // $guan_ds = Utils::ka_memguands($R,$Y);
                    $dai_ds = "";
                    $zong_ds = "";
                    $guan_ds = "";
                    $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
                    $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
                    $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
                    $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
                    $dai = Utils::ka_memuser("dan", $user["UserName"]);
                    $zong = Utils::ka_memuser("zong", $user["UserName"]);
                    $guan = Utils::ka_memuser("guan", $user["UserName"]);

                    $danid = Utils::ka_memuser("danid", $user["UserName"]);
                    $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
                    $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
                    $memid = Utils::ka_memuser("id", $user["UserName"]);

                    if ($sum_m < 1) {
                        $response["message"] = "You cann't betting with this amount!";
                        return $response->json($response, $response["status"]);
                    }

                    if (!is_numeric($sum_m)) {
                        User::where("UserName", $user["UserName"])->update(["status" => 2]);
                        $response["message"] = "Bet Amount have to be number!";
                        return $response->json($response, $response["status"]);
                    }

                    $ka_tan = new KaTan();

                    $ka_tan["num"] = $num;
                    $ka_tan["username"] = $user["UserName"];
                    $ka_tan["kithe"] = $Current_Kithe_Num;
                    $ka_tan["adddate"] = $text;
                    $ka_tan["class1"] = $class11;
                    $ka_tan["class2"] = $class22;
                    $ka_tan["class3"] = $class33;
                    $ka_tan["rate"] = $rate5;
                    $ka_tan["sum_m"] = $sum_m;
                    $ka_tan["user_ds"] = $user_ds;
                    $ka_tan["dai_ds"] = $dai_ds;
                    $ka_tan["zong_ds"] = $zong_ds;
                    $ka_tan["guan_ds"] = $guan_ds;
                    $ka_tan["dai_zc"] = $dai_zc;
                    $ka_tan["zong_zc"] = $zong_zc;
                    $ka_tan["guan_zc"] = $guan_zc;
                    $ka_tan["dagu_zc"] = $dagu_zc;
                    $ka_tan["bm"] = 0;
                    $ka_tan["dai"] = $dai;
                    $ka_tan["zong"] = $zong;
                    $ka_tan["guan"] = $guan;
                    $ka_tan["danid"] = $danid;
                    $ka_tan["zongid"] = $zongid;
                    $ka_tan["guanid"] = $guanid;
                    $ka_tan["abcd"] = "A";
                    $ka_tan["lx"] = 0;

                    $ka_tan->save();

                    $ouid = $ka_tan["id"];

                    $assets = $user['Money'];

                    $user_id = $user['id'];

                    $datetime = date("Y-m-d H:i:s");

                    $user["Money"] = $assets - $sum_m;

                    if ($user->save()) {

                        $money_log = new MoneyLog();

                        $money_log['user_id'] = $user_id;
                        $money_log['order_num'] = $num;
                        $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                        $money_log['update_time'] = $datetime;
                        $money_log['type'] = $class11 . "&nbsp;" . $class22 . "&nbsp;" . $class33;
                        $money_log['order_value'] = '-' . $sum_m;
                        $money_log['assets'] = $assets;
                        $money_log['balance'] = $user["Money"];

                        $money_log->save();
                    } else {
                        KaTan::where("id", $ouid)->delete();
                        $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                        return response()->json($response, $response['status']);
                    }
                }
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveKatanParlay(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
                'class2' => 'required|string',
                'class3' => 'required|string',
                'total_odds' => 'required|string',
                'gold' => 'required|numeric'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $class3 = $request_data["class3"];
            $total_odds = $request_data["total_odds"];
            $gold = $request_data["gold"];
            $XF = 19;
            $R = 12;

            $user = $request->user();

            switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                case "A":
                    $Y = 1;
                    break;
                case "B":
                    $Y = 4;
                    break;
                case "C":
                    $Y = 5;
                    break;
                case "D":
                    $Y = 6;
                    break;
                default:
                    $Y = 1;
                    break;
            }

            $Current_Kithe_Num = Utils::getCurrentKitheNum();

            $sum_m = KaTan::where("Kithe", $Current_Kithe_Num)
                ->where("class1", $class1)
                ->where("class2", $class2)
                ->where("class3", $class3)
                ->where("username", $user["UserName"])
                ->sum("sum_m");

            if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                return response()->json($response, $response['status']);
            }

            $num = Utils::randStr();
            // $text = date("Y-m-d H:i:s");
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $user_ds = Utils::ka_memds($R, "yg");
            $dai_ds = "";
            $zong_ds = "";
            $guan_ds = "";
            $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
            $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
            $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
            $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
            $dai = Utils::ka_memuser("dan", $user["UserName"]);
            $zong = Utils::ka_memuser("zong", $user["UserName"]);
            $guan = Utils::ka_memuser("guan", $user["UserName"]);

            $danid = Utils::ka_memuser("danid", $user["UserName"]);
            $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
            $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
            $memid = Utils::ka_memuser("id", $user["UserName"]);

            if ($gold < 1) {
                $response["message"] = "You cann't betting with this amount!";
                return $response->json($response, $response["status"]);
            }

            if (!is_numeric($gold)) {
                User::where("UserName", $user["UserName"])->update(["status" => 2]);
                $response["message"] = "Bet Amount have to be number!";
                return $response->json($response, $response["status"]);
            }

            $ka_tan = new KaTan();

            $ka_tan["num"] = $num;
            $ka_tan["username"] = $user["UserName"];
            $ka_tan["kithe"] = $Current_Kithe_Num;
            $ka_tan["adddate"] = $text;
            $ka_tan["class1"] = $class1;
            $ka_tan["class2"] = $class2;
            $ka_tan["class3"] = $class3;
            $ka_tan["rate"] = $total_odds;
            $ka_tan["sum_m"] = $gold;
            $ka_tan["user_ds"] = $user_ds;
            $ka_tan["dai_ds"] = $dai_ds;
            $ka_tan["zong_ds"] = $zong_ds;
            $ka_tan["guan_ds"] = $guan_ds;
            $ka_tan["dai_zc"] = $dai_zc;
            $ka_tan["zong_zc"] = $zong_zc;
            $ka_tan["guan_zc"] = $guan_zc;
            $ka_tan["dagu_zc"] = $dagu_zc;
            $ka_tan["bm"] = 0;
            $ka_tan["dai"] = $dai;
            $ka_tan["zong"] = $zong;
            $ka_tan["guan"] = $guan;
            $ka_tan["danid"] = $danid;
            $ka_tan["zongid"] = $zongid;
            $ka_tan["guanid"] = $guanid;
            $ka_tan["abcd"] = "A";
            $ka_tan["lx"] = 0;

            $ka_tan->save();

            $ouid = $ka_tan["id"];

            $assets = $user['Money'];

            $user_id = $user['id'];

            $datetime = date("Y-m-d H:i:s");

            $user["Money"] = $assets - $sum_m;

            if ($user->save()) {

                $money_log = new MoneyLog();

                $money_log['user_id'] = $user_id;
                $money_log['order_num'] = $num;
                $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                $money_log['update_time'] = $datetime;
                $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                $money_log['order_value'] = '-' . $sum_m;
                $money_log['assets'] = $assets;
                $money_log['balance'] = $user["Money"];

                $money_log->save();
            } else {
                KaTan::where("id", $ouid)->delete();
                $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                return response()->json($response, $response['status']);
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveKatanEven(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'data' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $data = json_decode($request_data["data"]);

            foreach ($data as $item) {

                $class1 = $item->class1;
                $class2 = $item->class2;
                $class3 = $item->num;
                $rate = $item->odds;
                $gold = $item->betAmount;

                switch ($class2) {
                    case "三全中":
                        $R = 14;
                        $ratess2 = 0;
                        break;
                    case "二全中":
                        $R = 13;
                        $ratess2 = 0;
                        break;
                    case "三中二":
                        $ratess2 = $rate;
                        $l_type = "中二";
                        $R = 15;
                        break;
                    case "二中特":
                        $ratess2 = $rate;
                        $l_type = "中特";
                        $R = 16;
                        break;
                    case "特串":
                        $R = 17;
                        $ratess2 = 0;
                        break;
                    case "四中一":
                        $R = 14;
                        $ratess2 = 0;
                        break;
                }

                $user = $request->user();

                switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                    case "A":
                        $Y = 1;
                        break;
                    case "B":
                        $Y = 4;
                        break;
                    case "C":
                        $Y = 5;
                        break;
                    case "D":
                        $Y = 6;
                        break;
                    default:
                        $Y = 1;
                        break;
                }

                $Current_Kithe_Num = Utils::getCurrentKitheNum();

                $sum_m = KaTan::where("Kithe", $Current_Kithe_Num)
                    ->where("class1", $class1)
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->where("username", $user["UserName"])
                    ->sum("sum_m");

                if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                    $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                    return response()->json($response, $response['status']);
                }

                $num = Utils::randStr();
                // $text = date("Y-m-d H:i:s");                
                $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                $user_ds = Utils::ka_memds($R, "yg");
                $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
                $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
                $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
                $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
                $dai = Utils::ka_memuser("dan", $user["UserName"]);
                $zong = Utils::ka_memuser("zong", $user["UserName"]);
                $guan = Utils::ka_memuser("guan", $user["UserName"]);

                // $dai_ds = Utils::ka_memguands($R, $Y, $dai);
                // $zong_ds = Utils::ka_memguands($R, $Y, $zong);
                // $guan_ds = Utils::ka_memguands($R, $Y, $guan);

                $dai_ds = 0;
                $zong_ds = 0;
                $guan_ds = 0;

                $danid = Utils::ka_memuser("danid", $user["UserName"]);
                $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
                $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
                $memid = Utils::ka_memuser("id", $user["UserName"]);

                if ($gold < 1) {
                    $response["message"] = "You cann't betting with this amount!";
                    return $response->json($response, $response["status"]);
                }

                if (!is_numeric($gold)) {
                    User::where("UserName", $user["UserName"])->update(["status" => 2]);
                    $response["message"] = "Bet Amount have to be number!";
                    return $response->json($response, $response["status"]);
                }

                $ka_tan = new KaTan();

                $ka_tan["num"] = $num;
                $ka_tan["username"] = $user["UserName"];
                $ka_tan["kithe"] = $Current_Kithe_Num;
                $ka_tan["adddate"] = $text;
                $ka_tan["class1"] = $class1;
                $ka_tan["class2"] = $class2;
                $ka_tan["class3"] = $class3;
                $ka_tan["rate"] = $rate;
                $ka_tan["sum_m"] = $gold;
                $ka_tan["user_ds"] = $user_ds;
                $ka_tan["dai_ds"] = $dai_ds;
                $ka_tan["zong_ds"] = $zong_ds;
                $ka_tan["guan_ds"] = $guan_ds;
                $ka_tan["dai_zc"] = $dai_zc;
                $ka_tan["zong_zc"] = $zong_zc;
                $ka_tan["guan_zc"] = $guan_zc;
                $ka_tan["dagu_zc"] = $dagu_zc;
                $ka_tan["bm"] = 0;
                $ka_tan["dai"] = $dai;
                $ka_tan["zong"] = $zong;
                $ka_tan["guan"] = $guan;
                $ka_tan["danid"] = $danid;
                $ka_tan["zongid"] = $zongid;
                $ka_tan["guanid"] = $guanid;
                $ka_tan["abcd"] = "A";
                $ka_tan["lx"] = 0;
                $ka_tan["rate2"] = $ratess2;

                $ka_tan->save();

                $ouid = $ka_tan["id"];

                $assets = $user['Money'];

                $user_id = $user['id'];

                $datetime = date("Y-m-d H:i:s");

                $user["Money"] = $assets - $sum_m;

                if ($user->save()) {

                    $money_log = new MoneyLog();

                    $money_log['user_id'] = $user_id;
                    $money_log['order_num'] = $num;
                    $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                    $money_log['update_time'] = $datetime;
                    $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                    $money_log['order_value'] = '-' . $sum_m;
                    $money_log['assets'] = $assets;
                    $money_log['balance'] = $user["Money"];

                    $money_log->save();
                } else {
                    KaTan::where("id", $ouid)->delete();
                    $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                    return response()->json($response, $response['status']);
                }
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveKatanCompatible(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
                'class2' => 'required|string',
                'class3' => 'required|string',
                'rate' => 'required|numeric',
                'gold' => 'required|numeric'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $class3 = $request_data["class3"];
            $rate = $request_data["rate"];
            $gold = $request_data["gold"];

            switch ($class2) {

                case "二肖":

                    $bmmm = 0;

                    $cmmm = 0;

                    $dmmm = 0;

                    $R = 19;

                    $XF = 23;

                    $rate_id = 901;

                    break;

                case "三肖":

                    $bmmm = 0;

                    $cmmm = 0;

                    $dmmm = 0;

                    $R = 20;

                    $XF = 23;

                    $rate_id = 913;

                    break;

                case "四肖":

                    $bmmm = 0;

                    $cmmm = 0;

                    $dmmm = 0;

                    $R = 21;

                    $XF = 23;

                    $rate_id = 925;

                    break;

                case "五肖":

                    $bmmm = 0;

                    $cmmm = 0;

                    $dmmm = 0;

                    $R = 23;

                    $XF = 23;

                    $rate_id = 937;

                    break;


                case "六肖":

                    $R = 26;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 949;

                    break;
                case "七肖":

                    $R = 27;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 961;

                    break;

                case "八肖":

                    $R = 28;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 973;


                    break;


                case "九肖":

                    $R = 29;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 985;

                    break;


                case "十肖":

                    $R = 31;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 997;

                    break;


                case "十一肖":

                    $R = 31;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 1009;

                    break;
            }

            $user = $request->user();

            switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                case "A":
                    $Y = 1;
                    break;
                case "B":
                    $Y = 4;
                    break;
                case "C":
                    $Y = 5;
                    break;
                case "D":
                    $Y = 6;
                    break;
                default:
                    $Y = 1;
                    break;
            }

            $Current_Kithe_Num = Utils::getCurrentKitheNum();

            $sum_m = KaTan::where("Kithe", $Current_Kithe_Num)
                ->where("class1", $class1)
                ->where("class2", $class2)
                ->where("class3", $class3)
                ->where("username", $user["UserName"])
                ->sum("sum_m");

            if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                return response()->json($response, $response['status']);
            }

            $num = Utils::randStr();
            // $text = date("Y-m-d H:i:s");
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $user_ds = Utils::ka_memds($R, "yg");
            $dai_ds = "";
            $zong_ds = "";
            $guan_ds = "";
            $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
            $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
            $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
            $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
            $dai = Utils::ka_memuser("dan", $user["UserName"]);
            $zong = Utils::ka_memuser("zong", $user["UserName"]);
            $guan = Utils::ka_memuser("guan", $user["UserName"]);

            $danid = Utils::ka_memuser("danid", $user["UserName"]);
            $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
            $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
            $memid = Utils::ka_memuser("id", $user["UserName"]);

            if ($gold < 1) {
                $response["message"] = "You cann't betting with this amount!";
                return $response->json($response, $response["status"]);
            }

            if (!is_numeric($gold)) {
                User::where("UserName", $user["UserName"])->update(["status" => 2]);
                $response["message"] = "Bet Amount have to be number!";
                return $response->json($response, $response["status"]);
            }

            $ka_tan = new KaTan();

            $ka_tan["num"] = $num;
            $ka_tan["username"] = $user["UserName"];
            $ka_tan["kithe"] = $Current_Kithe_Num;
            $ka_tan["adddate"] = $text;
            $ka_tan["class1"] = $class1;
            $ka_tan["class2"] = $class2;
            $ka_tan["class3"] = $class3;
            $ka_tan["rate"] = $rate;
            $ka_tan["sum_m"] = $gold;
            $ka_tan["user_ds"] = $user_ds;
            $ka_tan["dai_ds"] = $dai_ds;
            $ka_tan["zong_ds"] = $zong_ds;
            $ka_tan["guan_ds"] = $guan_ds;
            $ka_tan["dai_zc"] = $dai_zc;
            $ka_tan["zong_zc"] = $zong_zc;
            $ka_tan["guan_zc"] = $guan_zc;
            $ka_tan["dagu_zc"] = $dagu_zc;
            $ka_tan["bm"] = 0;
            $ka_tan["dai"] = $dai;
            $ka_tan["zong"] = $zong;
            $ka_tan["guan"] = $guan;
            $ka_tan["danid"] = $danid;
            $ka_tan["zongid"] = $zongid;
            $ka_tan["guanid"] = $guanid;
            $ka_tan["abcd"] = "A";
            $ka_tan["lx"] = 0;

            $ka_tan->save();

            $ouid = $ka_tan["id"];

            $assets = $user['Money'];

            $user_id = $user['id'];

            $datetime = date("Y-m-d H:i:s");

            $user["Money"] = $assets - $sum_m;

            if ($user->save()) {

                $money_log = new MoneyLog();

                $money_log['user_id'] = $user_id;
                $money_log['order_num'] = $num;
                $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                $money_log['update_time'] = $datetime;
                $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                $money_log['order_value'] = '-' . $sum_m;
                $money_log['assets'] = $assets;
                $money_log['balance'] = $user["Money"];

                $money_log->save();
            } else {
                KaTan::where("id", $ouid)->delete();
                $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                return response()->json($response, $response['status']);
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveKatanZodiacEven(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'data' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $data = json_decode($request_data["data"]);

            foreach ($data as $item) {

                $class1 = $item->class1;
                $class2 = $item->class2;
                $class3 = $item->num;
                $rate = $item->odds;
                $gold = $item->betAmount;

                switch ($class2) {

                    case "二肖连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 48;

                        $XF = 23;

                        break;


                    case "三肖连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 49;

                        $XF = 23;

                        break;

                    case "四肖连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 50;

                        $XF = 23;

                        break;

                    case "五肖连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 51;

                        $XF = 23;

                        break;


                    case "二肖连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 52;

                        $XF = 23;

                        break;

                    case "三肖连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 53;

                        $XF = 23;

                        break;

                    case "四肖连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 54;

                        $XF = 23;

                        break;
                }

                $user = $request->user();

                switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                    case "A":
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                    case "B":
                        $Y = "ygb";
                        $rate5 = $rate - $bmmm;
                        break;
                    case "C":
                        $Y = "ygc";
                        $rate5 = $rate - $cmmm;
                        break;
                    case "D":
                        $Y = "ygd";
                        $rate5 = $rate - $dmmm;
                        break;
                    default:
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                }

                $Current_Kithe_Num = Utils::getCurrentKitheNum();

                $sum_m = KaTan::where("Kithe", $Current_Kithe_Num)
                    ->where("class1", $class1)
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->where("username", $user["UserName"])
                    ->sum("sum_m");

                if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                    $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                    return response()->json($response, $response['status']);
                }

                $num = Utils::randStr();
                // $text = date("Y-m-d H:i:s");
                $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                $user_ds = Utils::ka_memds($R, "yg");
                $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
                $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
                $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
                $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
                $dai = Utils::ka_memuser("dan", $user["UserName"]);
                $zong = Utils::ka_memuser("zong", $user["UserName"]);
                $guan = Utils::ka_memuser("guan", $user["UserName"]);

                // $dai_ds = Utils::ka_memdaids($R,$Y, $dai);
                // $zong_ds = Utils::ka_memzongds($R,$Y, $zong);
                // $guan_ds = Utils::ka_memguands($R,$Y, $guan);

                $dai_ds = "";
                $zong_ds = "";
                $guan_ds = "";

                $danid = Utils::ka_memuser("danid", $user["UserName"]);
                $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
                $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
                $memid = Utils::ka_memuser("id", $user["UserName"]);

                if ($gold < 1) {
                    $response["message"] = "You cann't betting with this amount!";
                    return $response->json($response, $response["status"]);
                }

                if (!is_numeric($gold)) {
                    User::where("UserName", $user["UserName"])->update(["status" => 2]);
                    $response["message"] = "Bet Amount have to be number!";
                    return $response->json($response, $response["status"]);
                }

                $ka_tan = new KaTan();

                $ka_tan["num"] = $num;
                $ka_tan["username"] = $user["UserName"];
                $ka_tan["kithe"] = $Current_Kithe_Num;
                $ka_tan["adddate"] = $text;
                $ka_tan["class1"] = $class1;
                $ka_tan["class2"] = $class2;
                $ka_tan["class3"] = $class3;
                $ka_tan["rate"] = $rate5;
                $ka_tan["sum_m"] = $gold;
                $ka_tan["user_ds"] = $user_ds;
                $ka_tan["dai_ds"] = $dai_ds;
                $ka_tan["zong_ds"] = $zong_ds;
                $ka_tan["guan_ds"] = $guan_ds;
                $ka_tan["dai_zc"] = $dai_zc;
                $ka_tan["zong_zc"] = $zong_zc;
                $ka_tan["guan_zc"] = $guan_zc;
                $ka_tan["dagu_zc"] = $dagu_zc;
                $ka_tan["bm"] = 0;
                $ka_tan["dai"] = $dai;
                $ka_tan["zong"] = $zong;
                $ka_tan["guan"] = $guan;
                $ka_tan["danid"] = $danid;
                $ka_tan["zongid"] = $zongid;
                $ka_tan["guanid"] = $guanid;
                $ka_tan["abcd"] = "A";
                $ka_tan["lx"] = 0;

                $ka_tan->save();

                $ouid = $ka_tan["id"];

                $assets = $user['Money'];

                $user_id = $user['id'];

                $datetime = date("Y-m-d H:i:s");

                $user["Money"] = $assets - $sum_m;

                if ($user->save()) {

                    $money_log = new MoneyLog();

                    $money_log['user_id'] = $user_id;
                    $money_log['order_num'] = $num;
                    $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                    $money_log['update_time'] = $datetime;
                    $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                    $money_log['order_value'] = '-' . $sum_m;
                    $money_log['assets'] = $assets;
                    $money_log['balance'] = $user["Money"];

                    $money_log->save();
                } else {
                    KaTan::where("id", $ouid)->delete();
                    $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                    return response()->json($response, $response['status']);
                }
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveKatanMantissaEven(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'data' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $data = json_decode($request_data["data"]);

            foreach ($data as $item) {

                $class1 = $item->class1;
                $class2 = $item->class2;
                $class3 = $item->num;
                $rate = $item->odds;
                $gold = $item->betAmount;

                switch ($class2) {

                    case "二尾连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 56;

                        $XF = 23;

                        break;


                    case "三尾连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 57;

                        $XF = 23;

                        break;

                    case "四尾连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 58;

                        $XF = 23;

                        break;

                    case "二尾连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 59;

                        $XF = 23;

                        break;


                    case "三尾连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 60;

                        $XF = 23;

                        break;

                    case "四尾连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 61;

                        $XF = 23;

                        break;
                }

                $user = $request->user();

                switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                    case "A":
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                    case "B":
                        $Y = "ygb";
                        $rate5 = $rate - $bmmm;
                        break;
                    case "C":
                        $Y = "ygc";
                        $rate5 = $rate - $cmmm;
                        break;
                    case "D":
                        $Y = "ygd";
                        $rate5 = $rate - $dmmm;
                        break;
                    default:
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                }

                $Current_Kithe_Num = Utils::getCurrentKitheNum();

                $sum_m = KaTan::where("Kithe", $Current_Kithe_Num)
                    ->where("class1", $class1)
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->where("username", $user["UserName"])
                    ->sum("sum_m");

                if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                    $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                    return response()->json($response, $response['status']);
                }

                $num = Utils::randStr();
                // $text = date("Y-m-d H:i:s");
                $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                $user_ds = Utils::ka_memds($R, "yg");
                $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
                $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
                $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
                $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
                $dai = Utils::ka_memuser("dan", $user["UserName"]);
                $zong = Utils::ka_memuser("zong", $user["UserName"]);
                $guan = Utils::ka_memuser("guan", $user["UserName"]);

                // $dai_ds = Utils::ka_memdaids($R,$Y, $dai);
                // $zong_ds = Utils::ka_memzongds($R,$Y, $zong);
                // $guan_ds = Utils::ka_memguands($R,$Y, $guan);

                $dai_ds = "";
                $zong_ds = "";
                $guan_ds = "";

                $danid = Utils::ka_memuser("danid", $user["UserName"]);
                $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
                $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
                $memid = Utils::ka_memuser("id", $user["UserName"]);

                if ($gold < 1) {
                    $response["message"] = "You cann't betting with this amount!";
                    return $response->json($response, $response["status"]);
                }

                if (!is_numeric($gold)) {
                    User::where("UserName", $user["UserName"])->update(["status" => 2]);
                    $response["message"] = "Bet Amount have to be number!";
                    return $response->json($response, $response["status"]);
                }

                $ka_tan = new KaTan();

                $ka_tan["num"] = $num;
                $ka_tan["username"] = $user["UserName"];
                $ka_tan["kithe"] = $Current_Kithe_Num;
                $ka_tan["adddate"] = $text;
                $ka_tan["class1"] = $class1;
                $ka_tan["class2"] = $class2;
                $ka_tan["class3"] = $class3;
                $ka_tan["rate"] = $rate5;
                $ka_tan["sum_m"] = $gold;
                $ka_tan["user_ds"] = $user_ds;
                $ka_tan["dai_ds"] = $dai_ds;
                $ka_tan["zong_ds"] = $zong_ds;
                $ka_tan["guan_ds"] = $guan_ds;
                $ka_tan["dai_zc"] = $dai_zc;
                $ka_tan["zong_zc"] = $zong_zc;
                $ka_tan["guan_zc"] = $guan_zc;
                $ka_tan["dagu_zc"] = $dagu_zc;
                $ka_tan["bm"] = 0;
                $ka_tan["dai"] = $dai;
                $ka_tan["zong"] = $zong;
                $ka_tan["guan"] = $guan;
                $ka_tan["danid"] = $danid;
                $ka_tan["zongid"] = $zongid;
                $ka_tan["guanid"] = $guanid;
                $ka_tan["abcd"] = "A";
                $ka_tan["lx"] = 0;

                $ka_tan->save();

                $ouid = $ka_tan["id"];

                $assets = $user['Money'];

                $user_id = $user['id'];

                $datetime = date("Y-m-d H:i:s");

                $user["Money"] = $assets - $sum_m;

                if ($user->save()) {

                    $money_log = new MoneyLog();

                    $money_log['user_id'] = $user_id;
                    $money_log['order_num'] = $num;
                    $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                    $money_log['update_time'] = $datetime;
                    $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                    $money_log['order_value'] = '-' . $sum_m;
                    $money_log['assets'] = $assets;
                    $money_log['balance'] = $user["Money"];

                    $money_log->save();
                } else {
                    KaTan::where("id", $ouid)->delete();
                    $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                    return response()->json($response, $response['status']);
                }
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveKatanMissAll(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'data' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $data = json_decode($request_data["data"]);

            foreach ($data as $item) {

                $class1 = $item->class1;
                $class2 = $item->class2;
                $class3 = $item->num;
                $rate = $item->odds;
                $gold = $item->betAmount;

                switch ($class2) {
                    case "五不中":
                        $R = 37;
                        $ratess2 = 0;
                        break;
                    case "六不中":
                        $R = 38;
                        $ratess2 = 0;
                        break;
                    case "七不中":
                        $R = 39;
                        $ratess2 = 0;
                        break;
                    case "八不中":
                        $R = 40;
                        $ratess2 = 0;
                        break;
                    case "九不中":
                        $R = 41;
                        $ratess2 = 0;
                        break;
                    case "十不中":
                        $R = 42;
                        $ratess2 = 0;
                        break;
                    case "十一不中":
                        $R = 43;
                        $ratess2 = 0;
                        break;
                    case "十二不中":
                        $R = 44;
                        $ratess2 = 0;
                        break;
                }

                $user = $request->user();

                switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                    case "A":
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                    case "B":
                        $Y = "ygb";
                        $rate5 = $rate - $bmmm;
                        break;
                    case "C":
                        $Y = "ygc";
                        $rate5 = $rate - $cmmm;
                        break;
                    case "D":
                        $Y = "ygd";
                        $rate5 = $rate - $dmmm;
                        break;
                    default:
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                }

                $Current_Kithe_Num = Utils::getCurrentKitheNum();

                $sum_m = KaTan::where("Kithe", $Current_Kithe_Num)
                    ->where("class1", $class1)
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->where("username", $user["UserName"])
                    ->sum("sum_m");

                if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                    $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                    return response()->json($response, $response['status']);
                }

                $num = Utils::randStr();
                // $text = date("Y-m-d H:i:s");
                $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                $user_ds = Utils::ka_memds($R, "yg");
                $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
                $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
                $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
                $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
                $dai = Utils::ka_memuser("dan", $user["UserName"]);
                $zong = Utils::ka_memuser("zong", $user["UserName"]);
                $guan = Utils::ka_memuser("guan", $user["UserName"]);

                // $dai_ds = Utils::ka_memdaids($R,$Y, $dai);
                // $zong_ds = Utils::ka_memzongds($R,$Y, $zong);
                // $guan_ds = Utils::ka_memguands($R,$Y, $guan);

                $dai_ds = "";
                $zong_ds = "";
                $guan_ds = "";

                $danid = Utils::ka_memuser("danid", $user["UserName"]);
                $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
                $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
                $memid = Utils::ka_memuser("id", $user["UserName"]);

                if ($gold < 1) {
                    $response["message"] = "You cann't betting with this amount!";
                    return $response->json($response, $response["status"]);
                }

                if (!is_numeric($gold)) {
                    User::where("UserName", $user["UserName"])->update(["status" => 2]);
                    $response["message"] = "Bet Amount have to be number!";
                    return $response->json($response, $response["status"]);
                }

                $ka_tan = new KaTan();

                $ka_tan["num"] = $num;
                $ka_tan["username"] = $user["UserName"];
                $ka_tan["kithe"] = $Current_Kithe_Num;
                $ka_tan["adddate"] = $text;
                $ka_tan["class1"] = $class1;
                $ka_tan["class2"] = $class2;
                $ka_tan["class3"] = $class3;
                $ka_tan["rate"] = $rate5;
                $ka_tan["sum_m"] = $gold;
                $ka_tan["user_ds"] = $user_ds;
                $ka_tan["dai_ds"] = $dai_ds;
                $ka_tan["zong_ds"] = $zong_ds;
                $ka_tan["guan_ds"] = $guan_ds;
                $ka_tan["dai_zc"] = $dai_zc;
                $ka_tan["zong_zc"] = $zong_zc;
                $ka_tan["guan_zc"] = $guan_zc;
                $ka_tan["dagu_zc"] = $dagu_zc;
                $ka_tan["bm"] = 0;
                $ka_tan["dai"] = $dai;
                $ka_tan["zong"] = $zong;
                $ka_tan["guan"] = $guan;
                $ka_tan["danid"] = $danid;
                $ka_tan["zongid"] = $zongid;
                $ka_tan["guanid"] = $guanid;
                $ka_tan["abcd"] = "A";
                $ka_tan["lx"] = 0;

                $ka_tan->save();

                $ouid = $ka_tan["id"];

                $assets = $user['Money'];

                $user_id = $user['id'];

                $datetime = date("Y-m-d H:i:s");

                $user["Money"] = $assets - $sum_m;

                if ($user->save()) {

                    $money_log = new MoneyLog();

                    $money_log['user_id'] = $user_id;
                    $money_log['order_num'] = $num;
                    $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                    $money_log['update_time'] = $datetime;
                    $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                    $money_log['order_value'] = '-' . $sum_m;
                    $money_log['assets'] = $assets;
                    $money_log['balance'] = $user["Money"];

                    $money_log->save();
                } else {
                    KaTan::where("id", $ouid)->delete();
                    $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                    return response()->json($response, $response['status']);
                }
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMainBetResult(Request $request) {

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

            $user = $request->user();

            foreach($ka_kithe as $item) {

                $result = KaTan::where("kithe", $item["nn"])->where("username", $user["UserName"])->first();

                if (isset($result)) {

                    $result1 = KaTan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                    $result1 = $result1->where("kithe", $item["nn"])->where("username", $user["UserName"]);
                    
                    $result2 = KaTan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                    $result2 = $result2->where("kithe", $item["nn"])->where("username", $user["UserName"])->where("bm", 1);
                    
                    $result3 = KaTan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                    $result3 = $result3->where("kithe", $item["nn"])->where("username", $user["UserName"])->where("bm", 0);

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
                            "nn" => $item["nn"]."期",
                            "nd" => $nd,
                            "sum_m" => $result1["sum_m"],
                            "user_ds" => round(($result2['user_ds'] + $result3['user_ds']), 2),
                            "sum_m_1" => round(($result2['sum_m'] + $result3['sum_m']), 2)
                        );

                        array_push($data, $temp_data);

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
            $response['message'] = "Main Bet Result Data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSubBetResult(Request $request) {

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

            $user = $request->user();

            $ka_tan = KaTan::where("kithe", $period)->where("username", $user["UserName"])->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $z_sum += $item["sum_m"];

                if ($item["bm"] === 1) {
                    $item["z_user"] = round($item['sum_m']*$item['rate']-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100, 2);
                    $z_user+=$item['sum_m']*$item['rate']-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                } else if($item["bm"] === 0) {
                    $item["z_user"] = round(-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100, 2);
                    $z_user+=-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                } else {
                    $item["z_user"] = 0;
                }

                if ($item["bm"] !== 2) {
                    $item["z_userds"] = round($item['sum_m'] * abs($item['user_ds'])/100, 2);
                    $z_userds += $item['sum_m'] * abs($item['user_ds'])/100;
                } else {
                    $item["z_userds"] = 0;
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


    public function saveMacaoKatan(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
                'class2' => 'required|string',
                'class3' => 'required|string',
                'selectedAmount' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $class3 = $request_data["class3"];
            $selected_amount = $request_data["selectedAmount"];

            $class3_array = explode(",", $class3);
            $selected_amount_array = explode(",", $selected_amount);

            $user = $request->user();

            $configs = Config::all();

            $btm = $configs[0]['btm'];
            $ctm = $configs[0]['ctm'];
            $dtm = $configs[0]['dtm'];
            $btmdx = $configs[0]['btmdx'];
            $ctmdx = $configs[0]['ctmdx'];
            $dtmdx = $configs[0]['dtmdx'];
            $bzt = $configs[0]['bzt'];
            $czt = $configs[0]['czt'];
            $dzt = $configs[0]['dzt'];
            $bztdx = $configs[0]['bztdx'];
            $cztdx = $configs[0]['cztdx'];
            $dztdx = $configs[0]['dztdx'];
            $bzm = $configs[0]['bzm'];
            $czm = $configs[0]['czm'];
            $dzm = $configs[0]['dzm'];
            $bzmdx = $configs[0]['bzmdx'];
            $czmdx = $configs[0]['czmdx'];
            $dzmdx = $configs[0]['dzmdx'];
            $bth = $configs[0]['bth'];
            $cth = $configs[0]['cth'];
            $dth = $configs[0]['dth'];
            $bthdx = $configs[0]['bthdx'];
            $cthdx = $configs[0]['cthdx'];
            $dthdx = $configs[0]['dthdx'];
            $bzm6 = $configs[0]['bzm6'];
            $czm6 = $configs[0]['czm6'];
            $dzm6 = $configs[0]['dzm6'];
            $bsx = $configs[0]['bsx'];
            $csx = $configs[0]['csx'];
            $dsx = $configs[0]['dsx'];
            $bsx6 = $configs[0]['bsx6'];
            $csx6 = $configs[0]['csx6'];
            $dsx6 = $configs[0]['dsx6'];
            $bsxp = $configs[0]['bsxp'];
            $csxp = $configs[0]['csxp'];
            $dsxp = $configs[0]['dsxp'];
            $bbb = $configs[0]['bbb'];
            $cbb = $configs[0]['cbb'];
            $dbb = $configs[0]['dbb'];
            $bzx = $configs[0]['bzx'];
            $czx = $configs[0]['czx'];
            $dzx = $configs[0]['dzx'];
            $blx = $configs[0]['blx'];
            $clx = $configs[0]['clx'];
            $dlx = $configs[0]['dlx'];
            $jifei = $configs[0]['jifei'];
            $iszhudan = $configs[0]['iszhudan'];

            switch ($class2) {

                case "特A":

                    $XF = 11;

                    $mumu = 0;

                    $numm = 66;

                    break;

                case "特B":

                    $XF = 11;

                    $mumu = 58;

                    $numm = 66;

                    break;

                case "正A":

                    $XF = 15;

                    $mumu = 464;

                    $numm = 58;

                    break;

                case "正B":

                    $XF = 15;

                    $mumu = 517;

                    $numm = 58;

                    break;

                case "正1特":

                    $XF = 13;

                    $mumu = 116;

                    $numm = 60;

                    break;

                case "正2特":

                    $XF = 13;

                    $mumu = 174;

                    $numm = 60;

                    break;

                case "正3特":

                    $XF = 13;

                    $mumu = 232;

                    $numm = 60;

                    break;

                case "正4特":

                    $XF = 13;

                    $mumu = 290;

                    $numm = 60;

                    break;

                case "正5特":

                    $XF = 13;

                    $mumu = 348;

                    $numm = 60;

                    break;

                case "正6特":

                    $XF = 13;

                    $mumu = 406;

                    $numm = 60;

                    break;

                case "正1-6":

                    $XF = 13;

                    $mumu = 570;

                    $numm = 78;

                    break;

                case "五行":

                    $XF = 17;

                    $mumu = 712;

                    $numm = 5;

                    break;

                case "半波":

                    $XF = 25;

                    $mumu = 661;

                    $numm = 18;

                    break;

                case "半半波":

                    $XF = 25;

                    $mumu = 751;

                    $numm = 12;

                    break;

                case "正肖":

                    $XF = 25;

                    $mumu = 782;

                    $numm = 12;

                    break;

                case "七色波":

                    $XF = 25;

                    $mumu = 778;

                    $numm = 4;

                    break;

                case "尾数":

                    $XF = 27;

                    $mumu = 689;

                    $numm = 79;

                    break;

                case "特肖":

                    $XF = 23;

                    $mumu = 673;

                    $numm = 12;

                    break;

                case "一肖":

                    $XF = 23;

                    $mumu = 699;

                    $numm = 12;

                    break;

                case "正特尾数":

                    $XF = 23;

                    $mumu = 768;

                    $numm = 12;

                    break;

                case "过关":

                    $XF = 19;

                    break;

                case "连码":

                    $XF = 21;

                    break;

                default:

                    $mumu = 0;

                    $numm = 58;

                    $XF = 11;

                    break;
            }

            $ka_mem = Kamem::where("kauser", $user["UserName"])->first();

            $ts = $ka_mem["ts"];

            for ($r = 0; $r < $numm; $r++) {

                if (in_array((string)$r, $class3_array)) {
                    if ($r == 59 || $r == 60) {
                        if ($class2 == "特A") {
                            $rate_id = $r + 689;
                        } else {
                            switch ($class2) {
                                case "正1特": //1034
                                    $rate_id = $r + 975;
                                    break;
                                case "正2特": //1045
                                    $rate_id = $r + 1023;
                                    if ($r == 59) $rate_id = $r + 986;
                                    break;
                                case "正3特": //1044
                                    $rate_id = $r + 1024;
                                    if ($r == 59) $rate_id = $r + 985;
                                    break;
                                case "正4特": //1043
                                    $rate_id = $r + 1025;
                                    if ($r == 59) $rate_id = $r + 984;
                                    break;
                                case "正5特": //1042
                                    $rate_id = $r + 1026;
                                    if ($r == 59) $rate_id = $r + 983;
                                    break;
                                case "正6特": //1041
                                    $rate_id = $r + 1027;
                                    if ($r == 59) $rate_id = $r + 982;
                                    break;
                                default:
                                    $rate_id = $r + 671;
                            }
                        }
                    } else {
                        if ($class2 == "半波" && $r >= 13) {
                            $rate_id = $r + 705;
                        } else {
                            $rate_id = $r + $mumu;
                        }
                    }

                    if ($r == 61) {
                        if ($class2 == "特A") {
                            $rate_id = 795;
                        } else {
                            $rate_id = 801;
                        }
                    }

                    if ($r == 62) {
                        if ($class2 == "特A") {
                            $rate_id = 796;
                        } else {
                            $rate_id = 802;
                        }
                    }

                    if ($r == 63) {
                        if ($class2 == "特A") {
                            $rate_id = 797;
                        } else {
                            $rate_id = 803;
                        }
                    }

                    if ($r == 64) {
                        if ($class2 == "特A") {
                            $rate_id = 798;
                        } else {
                            $rate_id = 804;
                        }
                    }

                    if ($r == 65) {
                        if ($class2 == "特A") {
                            $rate_id = 799;
                        } else {
                            $rate_id = 805;
                        }
                    }

                    if ($r == 66) {
                        if ($class2 == "特A") {
                            $rate_id = 800;
                        } else {
                            $rate_id = 806;
                        }
                    }

                    if ($class2 == "正1-6") {
                        if ($r >= 1 && $r <= 7) {
                            $rate_id = $r + $mumu;
                        } elseif ($r >= 14 && $r <= 20) {
                            $rate_id = ($r - 6) + $mumu;
                        } elseif ($r >= 27 && $r <= 33) {
                            $rate_id = ($r - 12) + $mumu;
                        } elseif ($r >= 40 && $r <= 46) {
                            $rate_id = ($r - 18) + $mumu;
                        } elseif ($r >= 53 && $r <= 59) {
                            $rate_id = ($r - 24) + $mumu;
                        } elseif ($r >= 66 && $r <= 72) {
                            $rate_id = ($r - 30) + $mumu;
                        } elseif ($r >= 8 && $r <= 13) {
                            $rate_id = $r + 1039;
                        } elseif ($r >= 21 && $r <= 26) {
                            $rate_id = ($r - 7) + 1039;
                        } elseif ($r >= 34 && $r <= 39) {
                            $rate_id = ($r - 14) + 1039;
                        } elseif ($r >= 47 && $r <= 52) {
                            $rate_id = ($r - 21) + 1039;
                        } elseif ($r >= 60 && $r <= 65) {
                            $rate_id = ($r - 28) + 1039;
                        } elseif ($r >= 73 && $r <= 78) {
                            $rate_id = ($r - 35) + 1039;
                        }
                    }

                    $ka_bl = MacaoKabl::where("id", $rate_id)->first();

                    switch ($ka_bl["class1"]) {
                        case "特码":
                            switch ($ka_bl["class3"]) {
                                case "单":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 2;
                                    $drop_sort = "单双";
                                    break;
                                case "双":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 2;
                                    $drop_sort = "单双";
                                    break;
                                case "家禽":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 24;
                                    $drop_sort = "家禽野兽";
                                    break;
                                case "野兽":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 24;
                                    $drop_sort = "家禽野兽";
                                    break;
                                case "尾大":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 32;
                                    $drop_sort = "尾大尾小";
                                    break;
                                case "尾小":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 32;
                                    $drop_sort = "尾大尾小";
                                    break;
                                case "大单":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 33;
                                    $drop_sort = "大单小单";
                                    break;
                                case "小单":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 33;
                                    $drop_sort = "大单小单";
                                    break;
                                case "大双":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 34;
                                    $drop_sort = "大双小双";
                                    break;
                                case "小双":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 34;
                                    $drop_sort = "大双小双";
                                    break;
                                case "大":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 3;
                                    $drop_sort = "大小";
                                    break;
                                case "小":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 3;
                                    $drop_sort = "大小";
                                    break;
                                case "合单":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 4;
                                    $drop_sort = "合数单双 ";
                                    break;
                                case "合双":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 4;
                                    $drop_sort = "合数单双 ";
                                    break;
                                case "红波":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 10;
                                    $drop_sort = "波色";
                                    break;
                                case "绿波":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 10;
                                    $drop_sort = "波色";
                                    break;
                                case "蓝波":
                                    $bmmm = $btmdx;
                                    $cmmm = $ctmdx;
                                    $dmmm = $dtmdx;
                                    $R = 10;
                                    $drop_sort = "波色";
                                    break;
                                default:
                                    $bmmm = $btm;
                                    $cmmm = $ctm;
                                    $dmmm = $dtm;
                                    if ($ka_bl["class2"] == "特A") {
                                        $R = 0;
                                    } else {
                                        $R = 1;
                                    }
                                    $drop_sort = "特码";
                                    break;
                            }
                            break;
                        case "正码":
                            switch ($ka_bl["class3"]) {
                                case "总单":
                                    $R = 8;
                                    $drop_sort = "总数单双";
                                    $bmmm = $bzmdx;
                                    $cmmm = $czmdx;
                                    $dmmm = $dzmdx;
                                    break;
                                case "总双":
                                    $R = 8;
                                    $drop_sort = "总数单双";
                                    $bmmm = $bzmdx;
                                    $cmmm = $czmdx;
                                    $dmmm = $dzmdx;
                                    break;
                                case "总大":
                                    $R = 9;
                                    $drop_sort = "总数大小";
                                    $bmmm = $bzmdx;
                                    $cmmm = $czmdx;
                                    $dmmm = $dzmdx;
                                    break;
                                case "总小":
                                    $R = 9;
                                    $drop_sort = "总数大小";
                                    $bmmm = $bzmdx;
                                    $cmmm = $czmdx;
                                    $dmmm = $dzmdx;
                                    break;
                                default:
                                    if ($ka_bl["class2"] == "正A") {
                                        $R = 6;
                                    } else {
                                        $R = 7;
                                    }
                                    $drop_sort = "正码";
                                    $bmmm = $bzm;
                                    $cmmm = $czm;
                                    $dmmm = $dzm;
                                    break;
                            }
                            break;
                        case "五行":
                            $R = 25;
                            $drop_sort = "五行";
                            $bmmm = $bzm6;
                            $cmmm = $czm6;
                            $dmmm = $dzm6;
                            break;
                        case "生肖":
                            switch ($ka_bl["class2"]) {
                                case "特肖":
                                    $bmmm = $bsx;
                                    $cmmm = $csx;
                                    $dmmm = $dsx;
                                    $R = 18;
                                    $drop_sort = "特肖";
                                    break;
                                case "四肖":
                                    $bmmm = 0;
                                    $cmmm = 0;
                                    $dmmm = 0;
                                    $R = 19;
                                    $drop_sort = "四肖";
                                    break;
                                case "五肖":
                                    $bmmm = 0;
                                    $cmmm = 0;
                                    $dmmm = 0;
                                    $R = 20;
                                    $drop_sort = "五肖";
                                    break;
                                case "六肖":
                                    $bmmm = $bsx6;
                                    $cmmm = $csx6;
                                    $dmmm = $dsx6;
                                    $R = 21;
                                    $drop_sort = "六肖";
                                    break;
                                case "一肖":
                                    $bmmm = $bsxp;
                                    $cmmm = $csxp;
                                    $dmmm = $dsxp;
                                    $R = 22;
                                    $drop_sort = "一肖";
                                    break;
                                case "正特尾数":
                                    $bmmm = $bsxp;
                                    $cmmm = $csxp;
                                    $dmmm = $dsxp;
                                    $R = 29;
                                    $drop_sort = "正特尾数";
                                    break;
                                default:
                                    $R = 18;
                                    $drop_sort = "特肖";
                                    $bmmm = $bsxp;
                                    $cmmm = $csxp;
                                    $dmmm = $dsxp;
                                    break;
                            }
                            break;
                        case "半波":
                            $bmmm = $bbb;
                            $cmmm = $cbb;
                            $dmmm = $dbb;
                            $R = 11;
                            $drop_sort = "半波";
                            break;
                        case "半半波":
                            $bmmm = $bbb;
                            $cmmm = $cbb;
                            $dmmm = $dbb;
                            $R = 11;
                            $drop_sort = "半半波";
                        case "正肖":
                            $bmmm = $bbb;
                            $cmmm = $cbb;
                            $dmmm = $dbb;
                            $R = 11;
                            $drop_sort = "正肖";
                        case "七色波":
                            $bmmm = $bbb;
                            $cmmm = $cbb;
                            $dmmm = $dbb;
                            $R = 11;
                            $drop_sort = "七色波";
                            break;
                        case "正特":
                            switch ($ka_bl["class3"]) {
                                case "单":
                                    $R = 2;
                                    $drop_sort = "单双";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "双":
                                    $R = 2;
                                    $drop_sort = "单双";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "大":
                                    $R = 3;
                                    $drop_sort = "大小";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "小":
                                    $R = 3;
                                    $drop_sort = "大小";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "合单":
                                    $R = 4;
                                    $drop_sort = "合数单双 ";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "合双":
                                    $R = 4;
                                    $drop_sort = "合数单双 ";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "红波":
                                    $R = 10;
                                    $drop_sort = "波色";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "绿波":
                                    $R = 10;
                                    $drop_sort = "波色";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                case "蓝波":
                                    $R = 10;
                                    $drop_sort = "波色";
                                    $bmmm = $bztdx;
                                    $cmmm = $cztdx;
                                    $dmmm = $dztdx;
                                    break;
                                default:
                                    $R = 5;
                                    $drop_sort = "正特";
                                    $bmmm = $bzt;
                                    $cmmm = $czt;
                                    $dmmm = $dzt;
                                    break;
                            }
                            break;
                        case "尾数":
                            $R = 23;
                            $drop_sort = "尾数";
                            $bmmm = 0;
                            $cmmm = 0;
                            $dmmm = 0;
                            break;
                        case "正1-6":
                            $R = 38;
                            $drop_sort = "正1-6";
                            $bmmm = 0;
                            $cmmm = 0;
                            $dmmm = 0;
                            break;
                        default:
                            $R = 23;
                            $drop_sort = "尾数";
                            $bmmm = 0;
                            $cmmm = 0;
                            $dmmm = 0;
                            break;
                    }

                    //超过单期

                    $Current_Kithe_Num = Utils::getCurrentMacaoKitheNum();

                    $current_index = array_search((string)$r, $class3_array);

                    $current_bet_amount = $selected_amount_array[$current_index];

                    if ($ka_bl["class1"] == "特码" && $r <= 49) {

                        //超过单期

                        $sum_m55 = MacaoKatan::where("Kithe", $Current_Kithe_Num)
                            ->where("class1", $ka_bl["class1"])
                            ->where("class2", $ka_bl["class2"])
                            ->where("class3", $ka_bl["class3"])
                            ->sum("sum_m");

                        if ($sum_m55 == "") {
                            $sum_m55 = 0;
                        }

                        if ($ka_bl["locked"] == 1) {

                            $response["message"] = "对不起，[" . $ka_bl["class3"] . "号]暂停下注.请反回重新选择!";

                            return response()->json($response, $response['status']);
                        }

                        if (($sum_m55 + $current_bet_amount) > $ka_bl["xr"]) {

                            $response["message"] = "对不起，[" . $ka_bl["class3"] . "]超过单项限额[" . $ka_bl["xr"] . "].请反回重新下注!";

                            return response()->json($response, $response['status']);
                        }
                    }

                    $sum_m = MacaoKatan::where("Kithe", $Current_Kithe_Num)
                        ->where("class1", $ka_bl["class1"])
                        ->where("class2", $ka_bl["class2"])
                        ->where("class3", $ka_bl["class3"])
                        ->where("username", $user["UserName"])
                        ->sum("sum_m");

                    if (($sum_m + $current_bet_amount) > Utils::ka_memds($R, "xxx")) {

                        $response["message"] = "对不起，[" . $ka_bl["class3"] . "]超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                        return response()->json($response, $response['status']);
                    }

                    if ($ka_bl["locked"] == 1) {

                        $response["message"] = "对不起，[" . $ka_bl["class3"] . "号]暂停下注.请反回重新选择!'";

                        return response()->json($response, $response['status']);
                    }

                    switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                        case "A":
                            $rate5 = $ka_bl["rate"];
                            $Y = 1;
                            break;
                        case "B":
                            $rate5 = $ka_bl["rate"] - $bmmm;
                            $Y = 4;
                            break;
                        case "C":
                            $Y = 5;
                            $rate5 = $ka_bl["rate"] - $cmmm;
                            break;
                        case "D":
                            $rate5 = $ka_bl["rate"] - $dmmm;
                            $Y = 6;
                            break;
                        default:
                            $Y = 1;
                            $rate5 = $ka_bl["rate"];
                            break;
                    }

                    $num = Utils::randStr();
                    // $text = date("Y-m-d H:i:s");
                    $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                    $class11 = $ka_bl["class1"];
                    $class22 = $ka_bl["class2"];
                    $class33 = $ka_bl["class3"];
                    $sum_m = $current_bet_amount;
                    $user_ds = Utils::ka_memds($R, "yg");
                    // $dai_ds = Utils::ka_memdaids($R,$Y);
                    // $zong_ds = Utils::ka_memzongds($R,$Y);
                    // $guan_ds = Utils::ka_memguands($R,$Y);
                    $dai_ds = "";
                    $zong_ds = "";
                    $guan_ds = "";
                    $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
                    $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
                    $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
                    $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
                    $dai = Utils::ka_memuser("dan", $user["UserName"]);
                    $zong = Utils::ka_memuser("zong", $user["UserName"]);
                    $guan = Utils::ka_memuser("guan", $user["UserName"]);

                    $danid = Utils::ka_memuser("danid", $user["UserName"]);
                    $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
                    $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
                    $memid = Utils::ka_memuser("id", $user["UserName"]);

                    if ($sum_m < 1) {
                        $response["message"] = "You cann't betting with this amount!";
                        return $response->json($response, $response["status"]);
                    }

                    if (!is_numeric($sum_m)) {
                        User::where("UserName", $user["UserName"])->update(["status" => 2]);
                        $response["message"] = "Bet Amount have to be number!";
                        return $response->json($response, $response["status"]);
                    }

                    $ka_tan = new MacaoKatan();

                    $ka_tan["num"] = $num;
                    $ka_tan["username"] = $user["UserName"];
                    $ka_tan["kithe"] = $Current_Kithe_Num;
                    $ka_tan["adddate"] = $text;
                    $ka_tan["class1"] = $class11;
                    $ka_tan["class2"] = $class22;
                    $ka_tan["class3"] = $class33;
                    $ka_tan["rate"] = $rate5;
                    $ka_tan["sum_m"] = $sum_m;
                    $ka_tan["user_ds"] = $user_ds;
                    $ka_tan["dai_ds"] = $dai_ds;
                    $ka_tan["zong_ds"] = $zong_ds;
                    $ka_tan["guan_ds"] = $guan_ds;
                    $ka_tan["dai_zc"] = $dai_zc;
                    $ka_tan["zong_zc"] = $zong_zc;
                    $ka_tan["guan_zc"] = $guan_zc;
                    $ka_tan["dagu_zc"] = $dagu_zc;
                    $ka_tan["bm"] = 0;
                    $ka_tan["dai"] = $dai;
                    $ka_tan["zong"] = $zong;
                    $ka_tan["guan"] = $guan;
                    $ka_tan["danid"] = $danid;
                    $ka_tan["zongid"] = $zongid;
                    $ka_tan["guanid"] = $guanid;
                    $ka_tan["abcd"] = "A";
                    $ka_tan["lx"] = 0;

                    $ka_tan->save();

                    $ouid = $ka_tan["id"];

                    $assets = $user['Money'];

                    $user_id = $user['id'];

                    $datetime = date("Y-m-d H:i:s");

                    $user["Money"] = $assets - $sum_m;

                    if ($user->save()) {

                        $money_log = new MoneyLog();

                        $money_log['user_id'] = $user_id;
                        $money_log['order_num'] = $num;
                        $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                        $money_log['update_time'] = $datetime;
                        $money_log['type'] = $class11 . "&nbsp;" . $class22 . "&nbsp;" . $class33;
                        $money_log['order_value'] = '-' . $sum_m;
                        $money_log['assets'] = $assets;
                        $money_log['balance'] = $user["Money"];

                        $money_log->save();
                    } else {
                        MacaoKatan::where("id", $ouid)->delete();
                        $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                        return response()->json($response, $response['status']);
                    }
                }
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveMacaoKatanParlay(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
                'class2' => 'required|string',
                'class3' => 'required|string',
                'total_odds' => 'required|string',
                'gold' => 'required|numeric'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $class3 = $request_data["class3"];
            $total_odds = $request_data["total_odds"];
            $gold = $request_data["gold"];
            $XF = 19;
            $R = 12;

            $user = $request->user();

            switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                case "A":
                    $Y = 1;
                    break;
                case "B":
                    $Y = 4;
                    break;
                case "C":
                    $Y = 5;
                    break;
                case "D":
                    $Y = 6;
                    break;
                default:
                    $Y = 1;
                    break;
            }

            $Current_Kithe_Num = Utils::getCurrentMacaoKitheNum();

            $sum_m = MacaoKatan::where("Kithe", $Current_Kithe_Num)
                ->where("class1", $class1)
                ->where("class2", $class2)
                ->where("class3", $class3)
                ->where("username", $user["UserName"])
                ->sum("sum_m");

            if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                return response()->json($response, $response['status']);
            }

            $num = Utils::randStr();
            // $text = date("Y-m-d H:i:s");
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $user_ds = Utils::ka_memds($R, "yg");
            $dai_ds = "";
            $zong_ds = "";
            $guan_ds = "";
            $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
            $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
            $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
            $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
            $dai = Utils::ka_memuser("dan", $user["UserName"]);
            $zong = Utils::ka_memuser("zong", $user["UserName"]);
            $guan = Utils::ka_memuser("guan", $user["UserName"]);

            $danid = Utils::ka_memuser("danid", $user["UserName"]);
            $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
            $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
            $memid = Utils::ka_memuser("id", $user["UserName"]);

            if ($gold < 1) {
                $response["message"] = "You cann't betting with this amount!";
                return $response->json($response, $response["status"]);
            }

            if (!is_numeric($gold)) {
                User::where("UserName", $user["UserName"])->update(["status" => 2]);
                $response["message"] = "Bet Amount have to be number!";
                return $response->json($response, $response["status"]);
            }

            $ka_tan = new MacaoKatan();

            $ka_tan["num"] = $num;
            $ka_tan["username"] = $user["UserName"];
            $ka_tan["kithe"] = $Current_Kithe_Num;
            $ka_tan["adddate"] = $text;
            $ka_tan["class1"] = $class1;
            $ka_tan["class2"] = $class2;
            $ka_tan["class3"] = $class3;
            $ka_tan["rate"] = $total_odds;
            $ka_tan["sum_m"] = $gold;
            $ka_tan["user_ds"] = $user_ds;
            $ka_tan["dai_ds"] = $dai_ds;
            $ka_tan["zong_ds"] = $zong_ds;
            $ka_tan["guan_ds"] = $guan_ds;
            $ka_tan["dai_zc"] = $dai_zc;
            $ka_tan["zong_zc"] = $zong_zc;
            $ka_tan["guan_zc"] = $guan_zc;
            $ka_tan["dagu_zc"] = $dagu_zc;
            $ka_tan["bm"] = 0;
            $ka_tan["dai"] = $dai;
            $ka_tan["zong"] = $zong;
            $ka_tan["guan"] = $guan;
            $ka_tan["danid"] = $danid;
            $ka_tan["zongid"] = $zongid;
            $ka_tan["guanid"] = $guanid;
            $ka_tan["abcd"] = "A";
            $ka_tan["lx"] = 0;

            $ka_tan->save();

            $ouid = $ka_tan["id"];

            $assets = $user['Money'];

            $user_id = $user['id'];

            $datetime = date("Y-m-d H:i:s");

            $user["Money"] = $assets - $sum_m;

            if ($user->save()) {

                $money_log = new MoneyLog();

                $money_log['user_id'] = $user_id;
                $money_log['order_num'] = $num;
                $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                $money_log['update_time'] = $datetime;
                $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                $money_log['order_value'] = '-' . $sum_m;
                $money_log['assets'] = $assets;
                $money_log['balance'] = $user["Money"];

                $money_log->save();
            } else {
                MacaoKatan::where("id", $ouid)->delete();
                $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                return response()->json($response, $response['status']);
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveMacaoKatanEven(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'data' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $data = json_decode($request_data["data"]);

            foreach ($data as $item) {

                $class1 = $item->class1;
                $class2 = $item->class2;
                $class3 = $item->num;
                $rate = $item->odds;
                $gold = $item->betAmount;

                switch ($class2) {
                    case "三全中":
                        $R = 14;
                        $ratess2 = 0;
                        break;
                    case "二全中":
                        $R = 13;
                        $ratess2 = 0;
                        break;
                    case "三中二":
                        $ratess2 = $rate;
                        $l_type = "中二";
                        $R = 15;
                        break;
                    case "二中特":
                        $ratess2 = $rate;
                        $l_type = "中特";
                        $R = 16;
                        break;
                    case "特串":
                        $R = 17;
                        $ratess2 = 0;
                        break;
                    case "四中一":
                        $R = 14;
                        $ratess2 = 0;
                        break;
                }

                $user = $request->user();

                switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                    case "A":
                        $Y = 1;
                        break;
                    case "B":
                        $Y = 4;
                        break;
                    case "C":
                        $Y = 5;
                        break;
                    case "D":
                        $Y = 6;
                        break;
                    default:
                        $Y = 1;
                        break;
                }

                $Current_Kithe_Num = Utils::getCurrentMacaoKitheNum();

                $sum_m = MacaoKatan::where("Kithe", $Current_Kithe_Num)
                    ->where("class1", $class1)
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->where("username", $user["UserName"])
                    ->sum("sum_m");

                if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                    $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                    return response()->json($response, $response['status']);
                }

                $num = Utils::randStr();
                // $text = date("Y-m-d H:i:s");                
                $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                $user_ds = Utils::ka_memds($R, "yg");
                $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
                $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
                $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
                $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
                $dai = Utils::ka_memuser("dan", $user["UserName"]);
                $zong = Utils::ka_memuser("zong", $user["UserName"]);
                $guan = Utils::ka_memuser("guan", $user["UserName"]);

                // $dai_ds = Utils::ka_memguands($R, $Y, $dai);
                // $zong_ds = Utils::ka_memguands($R, $Y, $zong);
                // $guan_ds = Utils::ka_memguands($R, $Y, $guan);

                $dai_ds = 0;
                $zong_ds = 0;
                $guan_ds = 0;

                $danid = Utils::ka_memuser("danid", $user["UserName"]);
                $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
                $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
                $memid = Utils::ka_memuser("id", $user["UserName"]);

                if ($gold < 1) {
                    $response["message"] = "You cann't betting with this amount!";
                    return $response->json($response, $response["status"]);
                }

                if (!is_numeric($gold)) {
                    User::where("UserName", $user["UserName"])->update(["status" => 2]);
                    $response["message"] = "Bet Amount have to be number!";
                    return $response->json($response, $response["status"]);
                }

                $ka_tan = new MacaoKatan();

                $ka_tan["num"] = $num;
                $ka_tan["username"] = $user["UserName"];
                $ka_tan["kithe"] = $Current_Kithe_Num;
                $ka_tan["adddate"] = $text;
                $ka_tan["class1"] = $class1;
                $ka_tan["class2"] = $class2;
                $ka_tan["class3"] = $class3;
                $ka_tan["rate"] = $rate;
                $ka_tan["sum_m"] = $gold;
                $ka_tan["user_ds"] = $user_ds;
                $ka_tan["dai_ds"] = $dai_ds;
                $ka_tan["zong_ds"] = $zong_ds;
                $ka_tan["guan_ds"] = $guan_ds;
                $ka_tan["dai_zc"] = $dai_zc;
                $ka_tan["zong_zc"] = $zong_zc;
                $ka_tan["guan_zc"] = $guan_zc;
                $ka_tan["dagu_zc"] = $dagu_zc;
                $ka_tan["bm"] = 0;
                $ka_tan["dai"] = $dai;
                $ka_tan["zong"] = $zong;
                $ka_tan["guan"] = $guan;
                $ka_tan["danid"] = $danid;
                $ka_tan["zongid"] = $zongid;
                $ka_tan["guanid"] = $guanid;
                $ka_tan["abcd"] = "A";
                $ka_tan["lx"] = 0;
                $ka_tan["rate2"] = $ratess2;

                $ka_tan->save();

                $ouid = $ka_tan["id"];

                $assets = $user['Money'];

                $user_id = $user['id'];

                $datetime = date("Y-m-d H:i:s");

                $user["Money"] = $assets - $sum_m;

                if ($user->save()) {

                    $money_log = new MoneyLog();

                    $money_log['user_id'] = $user_id;
                    $money_log['order_num'] = $num;
                    $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                    $money_log['update_time'] = $datetime;
                    $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                    $money_log['order_value'] = '-' . $sum_m;
                    $money_log['assets'] = $assets;
                    $money_log['balance'] = $user["Money"];

                    $money_log->save();
                } else {
                    MacaoKatan::where("id", $ouid)->delete();
                    $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                    return response()->json($response, $response['status']);
                }
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveMacaoKatanCompatible(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'class1' => 'required|string',
                'class2' => 'required|string',
                'class3' => 'required|string',
                'rate' => 'required|numeric',
                'gold' => 'required|numeric'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $class1 = $request_data["class1"];
            $class2 = $request_data["class2"];
            $class3 = $request_data["class3"];
            $rate = $request_data["rate"];
            $gold = $request_data["gold"];

            switch ($class2) {

                case "二肖":

                    $bmmm = 0;

                    $cmmm = 0;

                    $dmmm = 0;

                    $R = 19;

                    $XF = 23;

                    $rate_id = 901;

                    break;

                case "三肖":

                    $bmmm = 0;

                    $cmmm = 0;

                    $dmmm = 0;

                    $R = 20;

                    $XF = 23;

                    $rate_id = 913;

                    break;

                case "四肖":

                    $bmmm = 0;

                    $cmmm = 0;

                    $dmmm = 0;

                    $R = 21;

                    $XF = 23;

                    $rate_id = 925;

                    break;

                case "五肖":

                    $bmmm = 0;

                    $cmmm = 0;

                    $dmmm = 0;

                    $R = 23;

                    $XF = 23;

                    $rate_id = 937;

                    break;


                case "六肖":

                    $R = 26;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 949;

                    break;
                case "七肖":

                    $R = 27;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 961;

                    break;

                case "八肖":

                    $R = 28;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 973;


                    break;


                case "九肖":

                    $R = 29;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 985;

                    break;


                case "十肖":

                    $R = 31;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 997;

                    break;


                case "十一肖":

                    $R = 31;

                    $bmmm = $bsx6;

                    $cmmm = $csx6;

                    $dmmm = $dsx6;

                    $XF = 23;

                    $rate_id = 1009;

                    break;
            }

            $user = $request->user();

            switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                case "A":
                    $Y = 1;
                    break;
                case "B":
                    $Y = 4;
                    break;
                case "C":
                    $Y = 5;
                    break;
                case "D":
                    $Y = 6;
                    break;
                default:
                    $Y = 1;
                    break;
            }

            $Current_Kithe_Num = Utils::getCurrentMacaoKitheNum();

            $sum_m = MacaoKatan::where("Kithe", $Current_Kithe_Num)
                ->where("class1", $class1)
                ->where("class2", $class2)
                ->where("class3", $class3)
                ->where("username", $user["UserName"])
                ->sum("sum_m");

            if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                return response()->json($response, $response['status']);
            }

            $num = Utils::randStr();
            // $text = date("Y-m-d H:i:s");
            $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
            $user_ds = Utils::ka_memds($R, "yg");
            $dai_ds = "";
            $zong_ds = "";
            $guan_ds = "";
            $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
            $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
            $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
            $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
            $dai = Utils::ka_memuser("dan", $user["UserName"]);
            $zong = Utils::ka_memuser("zong", $user["UserName"]);
            $guan = Utils::ka_memuser("guan", $user["UserName"]);

            $danid = Utils::ka_memuser("danid", $user["UserName"]);
            $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
            $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
            $memid = Utils::ka_memuser("id", $user["UserName"]);

            if ($gold < 1) {
                $response["message"] = "You cann't betting with this amount!";
                return $response->json($response, $response["status"]);
            }

            if (!is_numeric($gold)) {
                User::where("UserName", $user["UserName"])->update(["status" => 2]);
                $response["message"] = "Bet Amount have to be number!";
                return $response->json($response, $response["status"]);
            }

            $ka_tan = new MacaoKatan();

            $ka_tan["num"] = $num;
            $ka_tan["username"] = $user["UserName"];
            $ka_tan["kithe"] = $Current_Kithe_Num;
            $ka_tan["adddate"] = $text;
            $ka_tan["class1"] = $class1;
            $ka_tan["class2"] = $class2;
            $ka_tan["class3"] = $class3;
            $ka_tan["rate"] = $rate;
            $ka_tan["sum_m"] = $gold;
            $ka_tan["user_ds"] = $user_ds;
            $ka_tan["dai_ds"] = $dai_ds;
            $ka_tan["zong_ds"] = $zong_ds;
            $ka_tan["guan_ds"] = $guan_ds;
            $ka_tan["dai_zc"] = $dai_zc;
            $ka_tan["zong_zc"] = $zong_zc;
            $ka_tan["guan_zc"] = $guan_zc;
            $ka_tan["dagu_zc"] = $dagu_zc;
            $ka_tan["bm"] = 0;
            $ka_tan["dai"] = $dai;
            $ka_tan["zong"] = $zong;
            $ka_tan["guan"] = $guan;
            $ka_tan["danid"] = $danid;
            $ka_tan["zongid"] = $zongid;
            $ka_tan["guanid"] = $guanid;
            $ka_tan["abcd"] = "A";
            $ka_tan["lx"] = 0;

            $ka_tan->save();

            $ouid = $ka_tan["id"];

            $assets = $user['Money'];

            $user_id = $user['id'];

            $datetime = date("Y-m-d H:i:s");

            $user["Money"] = $assets - $sum_m;

            if ($user->save()) {

                $money_log = new MoneyLog();

                $money_log['user_id'] = $user_id;
                $money_log['order_num'] = $num;
                $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                $money_log['update_time'] = $datetime;
                $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                $money_log['order_value'] = '-' . $sum_m;
                $money_log['assets'] = $assets;
                $money_log['balance'] = $user["Money"];

                $money_log->save();
            } else {
                MacaoKatan::where("id", $ouid)->delete();
                $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                return response()->json($response, $response['status']);
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveMacaoKatanZodiacEven(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'data' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $data = json_decode($request_data["data"]);

            foreach ($data as $item) {

                $class1 = $item->class1;
                $class2 = $item->class2;
                $class3 = $item->num;
                $rate = $item->odds;
                $gold = $item->betAmount;

                switch ($class2) {

                    case "二肖连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 48;

                        $XF = 23;

                        break;


                    case "三肖连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 49;

                        $XF = 23;

                        break;

                    case "四肖连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 50;

                        $XF = 23;

                        break;

                    case "五肖连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 51;

                        $XF = 23;

                        break;


                    case "二肖连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 52;

                        $XF = 23;

                        break;

                    case "三肖连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 53;

                        $XF = 23;

                        break;

                    case "四肖连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 54;

                        $XF = 23;

                        break;
                }

                $user = $request->user();

                switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                    case "A":
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                    case "B":
                        $Y = "ygb";
                        $rate5 = $rate - $bmmm;
                        break;
                    case "C":
                        $Y = "ygc";
                        $rate5 = $rate - $cmmm;
                        break;
                    case "D":
                        $Y = "ygd";
                        $rate5 = $rate - $dmmm;
                        break;
                    default:
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                }

                $Current_Kithe_Num = Utils::getCurrentMacaoKitheNum();

                $sum_m = MacaoKatan::where("Kithe", $Current_Kithe_Num)
                    ->where("class1", $class1)
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->where("username", $user["UserName"])
                    ->sum("sum_m");

                if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                    $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                    return response()->json($response, $response['status']);
                }

                $num = Utils::randStr();
                // $text = date("Y-m-d H:i:s");
                $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                $user_ds = Utils::ka_memds($R, "yg");
                $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
                $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
                $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
                $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
                $dai = Utils::ka_memuser("dan", $user["UserName"]);
                $zong = Utils::ka_memuser("zong", $user["UserName"]);
                $guan = Utils::ka_memuser("guan", $user["UserName"]);

                // $dai_ds = Utils::ka_memdaids($R,$Y, $dai);
                // $zong_ds = Utils::ka_memzongds($R,$Y, $zong);
                // $guan_ds = Utils::ka_memguands($R,$Y, $guan);

                $dai_ds = "";
                $zong_ds = "";
                $guan_ds = "";

                $danid = Utils::ka_memuser("danid", $user["UserName"]);
                $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
                $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
                $memid = Utils::ka_memuser("id", $user["UserName"]);

                if ($gold < 1) {
                    $response["message"] = "You cann't betting with this amount!";
                    return $response->json($response, $response["status"]);
                }

                if (!is_numeric($gold)) {
                    User::where("UserName", $user["UserName"])->update(["status" => 2]);
                    $response["message"] = "Bet Amount have to be number!";
                    return $response->json($response, $response["status"]);
                }

                $ka_tan = new MacaoKatan();

                $ka_tan["num"] = $num;
                $ka_tan["username"] = $user["UserName"];
                $ka_tan["kithe"] = $Current_Kithe_Num;
                $ka_tan["adddate"] = $text;
                $ka_tan["class1"] = $class1;
                $ka_tan["class2"] = $class2;
                $ka_tan["class3"] = $class3;
                $ka_tan["rate"] = $rate5;
                $ka_tan["sum_m"] = $gold;
                $ka_tan["user_ds"] = $user_ds;
                $ka_tan["dai_ds"] = $dai_ds;
                $ka_tan["zong_ds"] = $zong_ds;
                $ka_tan["guan_ds"] = $guan_ds;
                $ka_tan["dai_zc"] = $dai_zc;
                $ka_tan["zong_zc"] = $zong_zc;
                $ka_tan["guan_zc"] = $guan_zc;
                $ka_tan["dagu_zc"] = $dagu_zc;
                $ka_tan["bm"] = 0;
                $ka_tan["dai"] = $dai;
                $ka_tan["zong"] = $zong;
                $ka_tan["guan"] = $guan;
                $ka_tan["danid"] = $danid;
                $ka_tan["zongid"] = $zongid;
                $ka_tan["guanid"] = $guanid;
                $ka_tan["abcd"] = "A";
                $ka_tan["lx"] = 0;

                $ka_tan->save();

                $ouid = $ka_tan["id"];

                $assets = $user['Money'];

                $user_id = $user['id'];

                $datetime = date("Y-m-d H:i:s");

                $user["Money"] = $assets - $sum_m;

                if ($user->save()) {

                    $money_log = new MoneyLog();

                    $money_log['user_id'] = $user_id;
                    $money_log['order_num'] = $num;
                    $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                    $money_log['update_time'] = $datetime;
                    $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                    $money_log['order_value'] = '-' . $sum_m;
                    $money_log['assets'] = $assets;
                    $money_log['balance'] = $user["Money"];

                    $money_log->save();
                } else {
                    MacaoKatan::where("id", $ouid)->delete();
                    $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                    return response()->json($response, $response['status']);
                }
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveMacaoKatanMantissaEven(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'data' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $data = json_decode($request_data["data"]);

            foreach ($data as $item) {

                $class1 = $item->class1;
                $class2 = $item->class2;
                $class3 = $item->num;
                $rate = $item->odds;
                $gold = $item->betAmount;

                switch ($class2) {

                    case "二尾连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 56;

                        $XF = 23;

                        break;


                    case "三尾连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 57;

                        $XF = 23;

                        break;

                    case "四尾连中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 58;

                        $XF = 23;

                        break;

                    case "二尾连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 59;

                        $XF = 23;

                        break;


                    case "三尾连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 60;

                        $XF = 23;

                        break;

                    case "四尾连不中":

                        $bmmm = 0;

                        $cmmm = 0;

                        $dmmm = 0;

                        $R = 61;

                        $XF = 23;

                        break;
                }

                $user = $request->user();

                switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                    case "A":
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                    case "B":
                        $Y = "ygb";
                        $rate5 = $rate - $bmmm;
                        break;
                    case "C":
                        $Y = "ygc";
                        $rate5 = $rate - $cmmm;
                        break;
                    case "D":
                        $Y = "ygd";
                        $rate5 = $rate - $dmmm;
                        break;
                    default:
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                }

                $Current_Kithe_Num = Utils::getCurrentMacaoKitheNum();

                $sum_m = MacaoKatan::where("Kithe", $Current_Kithe_Num)
                    ->where("class1", $class1)
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->where("username", $user["UserName"])
                    ->sum("sum_m");

                if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                    $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                    return response()->json($response, $response['status']);
                }

                $num = Utils::randStr();
                // $text = date("Y-m-d H:i:s");
                $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                $user_ds = Utils::ka_memds($R, "yg");
                $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
                $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
                $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
                $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
                $dai = Utils::ka_memuser("dan", $user["UserName"]);
                $zong = Utils::ka_memuser("zong", $user["UserName"]);
                $guan = Utils::ka_memuser("guan", $user["UserName"]);

                // $dai_ds = Utils::ka_memdaids($R,$Y, $dai);
                // $zong_ds = Utils::ka_memzongds($R,$Y, $zong);
                // $guan_ds = Utils::ka_memguands($R,$Y, $guan);

                $dai_ds = "";
                $zong_ds = "";
                $guan_ds = "";

                $danid = Utils::ka_memuser("danid", $user["UserName"]);
                $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
                $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
                $memid = Utils::ka_memuser("id", $user["UserName"]);

                if ($gold < 1) {
                    $response["message"] = "You cann't betting with this amount!";
                    return $response->json($response, $response["status"]);
                }

                if (!is_numeric($gold)) {
                    User::where("UserName", $user["UserName"])->update(["status" => 2]);
                    $response["message"] = "Bet Amount have to be number!";
                    return $response->json($response, $response["status"]);
                }

                $ka_tan = new MacaoKatan();

                $ka_tan["num"] = $num;
                $ka_tan["username"] = $user["UserName"];
                $ka_tan["kithe"] = $Current_Kithe_Num;
                $ka_tan["adddate"] = $text;
                $ka_tan["class1"] = $class1;
                $ka_tan["class2"] = $class2;
                $ka_tan["class3"] = $class3;
                $ka_tan["rate"] = $rate5;
                $ka_tan["sum_m"] = $gold;
                $ka_tan["user_ds"] = $user_ds;
                $ka_tan["dai_ds"] = $dai_ds;
                $ka_tan["zong_ds"] = $zong_ds;
                $ka_tan["guan_ds"] = $guan_ds;
                $ka_tan["dai_zc"] = $dai_zc;
                $ka_tan["zong_zc"] = $zong_zc;
                $ka_tan["guan_zc"] = $guan_zc;
                $ka_tan["dagu_zc"] = $dagu_zc;
                $ka_tan["bm"] = 0;
                $ka_tan["dai"] = $dai;
                $ka_tan["zong"] = $zong;
                $ka_tan["guan"] = $guan;
                $ka_tan["danid"] = $danid;
                $ka_tan["zongid"] = $zongid;
                $ka_tan["guanid"] = $guanid;
                $ka_tan["abcd"] = "A";
                $ka_tan["lx"] = 0;

                $ka_tan->save();

                $ouid = $ka_tan["id"];

                $assets = $user['Money'];

                $user_id = $user['id'];

                $datetime = date("Y-m-d H:i:s");

                $user["Money"] = $assets - $sum_m;

                if ($user->save()) {

                    $money_log = new MoneyLog();

                    $money_log['user_id'] = $user_id;
                    $money_log['order_num'] = $num;
                    $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                    $money_log['update_time'] = $datetime;
                    $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                    $money_log['order_value'] = '-' . $sum_m;
                    $money_log['assets'] = $assets;
                    $money_log['balance'] = $user["Money"];

                    $money_log->save();
                } else {
                    MacaoKatan::where("id", $ouid)->delete();
                    $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                    return response()->json($response, $response['status']);
                }
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveMacaoKatanMissAll(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'data' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $data = json_decode($request_data["data"]);

            foreach ($data as $item) {

                $class1 = $item->class1;
                $class2 = $item->class2;
                $class3 = $item->num;
                $rate = $item->odds;
                $gold = $item->betAmount;

                switch ($class2) {
                    case "五不中":
                        $R = 37;
                        $ratess2 = 0;
                        break;
                    case "六不中":
                        $R = 38;
                        $ratess2 = 0;
                        break;
                    case "七不中":
                        $R = 39;
                        $ratess2 = 0;
                        break;
                    case "八不中":
                        $R = 40;
                        $ratess2 = 0;
                        break;
                    case "九不中":
                        $R = 41;
                        $ratess2 = 0;
                        break;
                    case "十不中":
                        $R = 42;
                        $ratess2 = 0;
                        break;
                    case "十一不中":
                        $R = 43;
                        $ratess2 = 0;
                        break;
                    case "十二不中":
                        $R = 44;
                        $ratess2 = 0;
                        break;
                }

                $user = $request->user();

                switch (Utils::ka_memuser("abcd", $user["UserName"])) {
                    case "A":
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                    case "B":
                        $Y = "ygb";
                        $rate5 = $rate - $bmmm;
                        break;
                    case "C":
                        $Y = "ygc";
                        $rate5 = $rate - $cmmm;
                        break;
                    case "D":
                        $Y = "ygd";
                        $rate5 = $rate - $dmmm;
                        break;
                    default:
                        $Y = "yg";
                        $rate5 = $rate;
                        break;
                }

                $Current_Kithe_Num = Utils::getCurrentMacaoKitheNum();

                $sum_m = MacaoKatan::where("Kithe", $Current_Kithe_Num)
                    ->where("class1", $class1)
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->where("username", $user["UserName"])
                    ->sum("sum_m");

                if (($sum_m + $gold) > Utils::ka_memds($R, "xxx")) {

                    $response["message"] = "对不起，超过单项限额[".Utils::ka_memds($R, "xxx")."].请反回重新下注!";

                    return response()->json($response, $response['status']);
                }

                $num = Utils::randStr();
                // $text = date("Y-m-d H:i:s");
                $text = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                $user_ds = Utils::ka_memds($R, "yg");
                $dai_zc = Utils::ka_memuser("dan_zc", $user["UserName"]);
                $zong_zc = Utils::ka_memuser("zong_zc", $user["UserName"]);
                $guan_zc = Utils::ka_memuser("guan_zc", $user["UserName"]);
                $dagu_zc = Utils::ka_memuser("dagu_zc", $user["UserName"]);
                $dai = Utils::ka_memuser("dan", $user["UserName"]);
                $zong = Utils::ka_memuser("zong", $user["UserName"]);
                $guan = Utils::ka_memuser("guan", $user["UserName"]);

                // $dai_ds = Utils::ka_memdaids($R,$Y, $dai);
                // $zong_ds = Utils::ka_memzongds($R,$Y, $zong);
                // $guan_ds = Utils::ka_memguands($R,$Y, $guan);

                $dai_ds = "";
                $zong_ds = "";
                $guan_ds = "";

                $danid = Utils::ka_memuser("danid", $user["UserName"]);
                $zongid = Utils::ka_memuser("zongid", $user["UserName"]);
                $guanid = Utils::ka_memuser("guanid", $user["UserName"]);
                $memid = Utils::ka_memuser("id", $user["UserName"]);

                if ($gold < 1) {
                    $response["message"] = "You cann't betting with this amount!";
                    return $response->json($response, $response["status"]);
                }

                if (!is_numeric($gold)) {
                    User::where("UserName", $user["UserName"])->update(["status" => 2]);
                    $response["message"] = "Bet Amount have to be number!";
                    return $response->json($response, $response["status"]);
                }

                $ka_tan = new MacaoKatan();

                $ka_tan["num"] = $num;
                $ka_tan["username"] = $user["UserName"];
                $ka_tan["kithe"] = $Current_Kithe_Num;
                $ka_tan["adddate"] = $text;
                $ka_tan["class1"] = $class1;
                $ka_tan["class2"] = $class2;
                $ka_tan["class3"] = $class3;
                $ka_tan["rate"] = $rate5;
                $ka_tan["sum_m"] = $gold;
                $ka_tan["user_ds"] = $user_ds;
                $ka_tan["dai_ds"] = $dai_ds;
                $ka_tan["zong_ds"] = $zong_ds;
                $ka_tan["guan_ds"] = $guan_ds;
                $ka_tan["dai_zc"] = $dai_zc;
                $ka_tan["zong_zc"] = $zong_zc;
                $ka_tan["guan_zc"] = $guan_zc;
                $ka_tan["dagu_zc"] = $dagu_zc;
                $ka_tan["bm"] = 0;
                $ka_tan["dai"] = $dai;
                $ka_tan["zong"] = $zong;
                $ka_tan["guan"] = $guan;
                $ka_tan["danid"] = $danid;
                $ka_tan["zongid"] = $zongid;
                $ka_tan["guanid"] = $guanid;
                $ka_tan["abcd"] = "A";
                $ka_tan["lx"] = 0;

                $ka_tan->save();

                $ouid = $ka_tan["id"];

                $assets = $user['Money'];

                $user_id = $user['id'];

                $datetime = date("Y-m-d H:i:s");

                $user["Money"] = $assets - $sum_m;

                if ($user->save()) {

                    $money_log = new MoneyLog();

                    $money_log['user_id'] = $user_id;
                    $money_log['order_num'] = $num;
                    $money_log['about'] = $user["UserName"] . '投注六合彩<br>ID:' . $ouid;
                    $money_log['update_time'] = $datetime;
                    $money_log['type'] = $class1 . "&nbsp;" . $class2 . "&nbsp;" . $class3;
                    $money_log['order_value'] = '-' . $sum_m;
                    $money_log['assets'] = $assets;
                    $money_log['balance'] = $user["Money"];

                    $money_log->save();
                } else {
                    MacaoKatan::where("id", $ouid)->delete();
                    $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                    return response()->json($response, $response['status']);
                }
            }

            $response['message'] = 'Lottery Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoMainBetResult(Request $request) {

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

            $user = $request->user();

            foreach($ka_kithe as $item) {

                $result = MacaoKatan::where("kithe", $item["nn"])->where("username", $user["UserName"])->first();

                if (isset($result)) {

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m,count(*) as re,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc'));

                    $result1 = $result1->where("kithe", $item["nn"])->where("username", $user["UserName"]);
                    
                    $result2 = MacaoKatan::select(DB::raw('sum(sum_m*dai_zc/10-sum_m*rate*dai_zc/10+sum_m*(dai_ds-user_ds)/100*(10-dai_zc)/10-sum_m*user_ds/100*(dai_zc)/10) as daisf,sum(sum_m*zong_zc/10-sum_m*rate*zong_zc/10+sum_m*(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10-sum_m*dai_ds/100*(zong_zc)/10) as zongsf,sum(sum_m*guan_zc/10-sum_m*rate*guan_zc/10+sum_m*(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10-sum_m*zong_ds/100*(guan_zc)/10) as guansf,sum(sum_m*rate-sum_m+sum_m*Abs(user_ds)/100) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                    $result2 = $result2->where("kithe", $item["nn"])->where("username", $user["UserName"])->where("bm", 1);
                    
                    $result3 = MacaoKatan::select(DB::raw('sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10+sum_m*dai_zc/10-sum_m*(dai_zc)/10*user_ds/100) as daisf,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10+sum_m*zong_zc/10-sum_m*(zong_zc)/10*dai_ds/100) as zongsf,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10+sum_m*guan_zc/10-sum_m*guan_zc/10*zong_ds/100) as guansf,sum(sum_m*Abs(user_ds)/100-sum_m) as sum_m,sum(sum_m*dagu_zc/10) as dagu_zc,sum(sum_m*guan_zc/10) as guan_zc,sum(sum_m*zong_zc/10) as zong_zc,sum(sum_m*dai_zc/10) as dai_zc,sum(sum_m*Abs(user_ds)/100) as user_ds,sum(sum_m*Abs(guan_ds-zong_ds)/100*(10-guan_zc-zong_zc-dai_zc)/10) as guan_ds,sum(sum_m*Abs(zong_ds-dai_ds)/100*(10-zong_zc-dai_zc)/10) as zong_ds,sum(sum_m*Abs(dai_ds-user_ds)/100*(10-dai_zc)/10) as dai_ds'));

                    $result3 = $result3->where("kithe", $item["nn"])->where("username", $user["UserName"])->where("bm", 0);

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
                            "nn" => $item["nn"]."期",
                            "nd" => $nd,
                            "sum_m" => $result1["sum_m"],
                            "user_ds" => round(($result2['user_ds'] + $result3['user_ds']), 2),
                            "sum_m_1" => round(($result2['sum_m'] + $result3['sum_m']), 2)
                        );

                        array_push($data, $temp_data);

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
            $response['message'] = "Main Bet Result Data fatched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoSubBetResult(Request $request) {

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

            $user = $request->user();

            $ka_tan = MacaoKatan::where("kithe", $period)->where("username", $user["UserName"])->get();

            $no = 1;

            foreach($ka_tan as $item) {

                $z_sum += $item["sum_m"];

                if ($item["bm"] === 1) {
                    $item["z_user"] = round($item['sum_m']*$item['rate']-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100, 2);
                    $z_user+=$item['sum_m']*$item['rate']-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                } else if($item["bm"] === 0) {
                    $item["z_user"] = round(-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100, 2);
                    $z_user+=-$item['sum_m']+$item['sum_m']*abs($item['user_ds'])/100;
                } else {
                    $item["z_user"] = 0;
                }

                if ($item["bm"] !== 2) {
                    $item["z_userds"] = round($item['sum_m'] * abs($item['user_ds'])/100, 2);
                    $z_userds += $item['sum_m'] * abs($item['user_ds'])/100;
                } else {
                    $item["z_userds"] = 0;
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
