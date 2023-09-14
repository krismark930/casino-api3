<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kabl;
use App\Models\Kakithe;
use App\Models\KaTan;
use App\Models\MacaoKabl;
use App\Models\MacaoKakithe;
use App\Models\MacaoKatan;
use App\Models\MacaoYakithe;
use App\Models\NewMacaoKakithe;
use App\Models\NewMacaoKatan;
use App\Models\NewMacaoYakithe;
use App\Models\User;
use App\Models\Web\MoneyLog;
use App\Models\Yakithe;
use App\Utils\Utils;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KitheController extends Controller
{

    public function winKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $ka_kithe = Kakithe::find($id);

            $kithe = $ka_kithe['nn'];
            $na = $ka_kithe['na'];
            $n1 = $ka_kithe['n1'];
            $n2 = $ka_kithe['n2'];
            $n3 = $ka_kithe['n3'];
            $n4 = $ka_kithe['n4'];
            $n5 = $ka_kithe['n5'];
            $n6 = $ka_kithe['n6'];
            $sxsx = $ka_kithe['sx'];

            //结算特码

            KaTan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("class3", $na)
                ->update(["bm" => 1]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("bm", "!=", 0)
                ->where("class3", "!=", $na)
                ->where("class3", "!=", '单')
                ->where("class3", "!=", '双')
                ->where("class3", "!=", '大')
                ->where("class3", "!=", '小')
                ->where("class3", "!=", '合单')
                ->where("class3", "!=", '合双')
                ->where("class3", "!=", '红波')
                ->where("class3", "!=", '蓝波')
                ->where("class3", "!=", '绿波')
                ->update(["bm" => 0]);

            $Rs5 = KaTan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)->where("class1", "特码")->where("class3", $na)
                ->first();

            if (isset($Rs5)) {
                $zwin1 = $Rs5["re"];
            } else {
                $zwin1 = 0;
            }

            // 特码单双

            if ($na % 2 == 1) {
                $class3 = "单";
                $class31 = "双";
            } else {
                $class31 = "单";
                $class3 = "双";
            }

            if ($na == 49) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '单')
                            ->where('class3', '双');
                    })
                    ->update(["bm" => 2]);

                $result1dd = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '单')
                            ->where('class3', '双');
                    })
                    ->first();

                if (isset($result1dd)) {
                    $zwin2 = $result1dd["re"];
                } else {
                    $zwin2 = 0;
                }

            } else {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                $result1ee = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1ee)) {
                    $zwin2 = $result1ee["re"];
                } else {
                    $zwin2 = 0;
                }
            }

            //特码大小

            if ($na >= 25) {
                $class3 = "大";
                $class31 = "小";
            } else {
                $class31 = "大";
                $class3 = "小";
            }

            if ($na == 49) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '大')
                            ->where('class3', '小');
                    })
                    ->update(["bm" => 1]);

                $result1ff = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '大')
                            ->where('class3', '小');
                    })
                    ->first();

                if (isset($result1ff)) {
                    $zwin3 = $result1ff["re"];
                } else {
                    $zwin3 = 0;
                }

            } else {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                $result1gg = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1gg)) {
                    $zwin3 = $result1gg["re"];
                } else {
                    $zwin3 = 0;
                }
            }

            //合单合双

            if ((($na % 10) + intval($na / 10)) % 2 == 0) {
                $class3 = "合双";
                $class31 = "合单";
            } else {
                $class31 = "合双";
                $class3 = "合单";
            }

            if ($na == 49) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '合单')
                            ->where('class3', '合双');
                    })
                    ->update(["bm" => 2]);

                $result1vv = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '合单')
                            ->where('class3', '合双');
                    })
                    ->first();

                if (isset($result1vv)) {
                    $zwin4 = $result1vv["re"];
                } else {
                    $zwin4 = 0;
                }
            } else {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin4 = $result1["re"];
                } else {
                    $zwin4 = 0;
                }
            }

            // 结算特码波色

            $class3 = Utils::ka_Color_s($na);

            KaTan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("bm", "!=", 0)
                ->where(function ($query) {
                    $query->where('class3', '红波')
                        ->orWhere('class3', '蓝波')
                        ->orWhere('class3', '绿波');
                })
                ->update(["bm" => 0]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("class3", $class3)
                ->update(["bm" => 1]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin5 = $result1["re"];
            } else {
                $zwin5 = 0;
            }

            //结算家禽/野兽

            if ($sxsx == "狗"
                || $sxsx == "猪"
                || $sxsx == "鸡"
                || $sxsx == "羊"
                || $sxsx == "马"
                || $sxsx == "牛") {

                $psx = "家禽";
                $psx1 = "野兽";

            } else {

                $psx = "野兽";
                $psx1 = "家禽";
            }

            if ($na == 49) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) use ($psx, $psx1) {
                        $query->where('class3', $psx)
                            ->where('class3', $psx1);
                    })
                    ->update(["bm" => 2]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) use ($psx, $psx1) {
                        $query->where('class3', $psx)
                            ->where('class3', $psx1);
                    })
                    ->first();

                if (isset($result1)) {
                    $zwin6 = $result1["re"];
                } else {
                    $zwin6 = 0;
                }
            } else {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $psx1)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $psx)
                    ->update(["bm" => 1]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $psx)
                    ->first();

                if (isset($result1)) {
                    $zwin6 = $result1["re"];
                } else {
                    $zwin6 = 0;
                }
            }

            //结算尾大/尾小

            $wdwx0 = $na % 10;

            if ($wdwx0 > 4) {
                $class3 = "尾大";
                $class31 = "尾小";
            } else {
                $class31 = "尾大";
                $class3 = "尾小";
            }

            if ($na == 49) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '尾大')
                            ->where('class3', '尾小');
                    })
                    ->update(["bm" => 2]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '尾大')
                            ->where('class3', '尾小');
                    })
                    ->first();

                if (isset($result1)) {
                    $zwin7 = $result1["re"];
                } else {
                    $zwin7 = 0;
                }
            } else {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin7 = $result1["re"];
                } else {
                    $zwin7 = 0;
                }
            }

            // 大单小单/大双小双

            if ($na <= 25) {
                if ($na % 2 == 1) {
                    $ddxd = "小单";
                    $ddxd1 = "小双";
                } else {
                    $ddxd1 = "小单";
                    $ddxd = "小双";
                }
            } else {
                if ($na % 2 == 1) {
                    $ddxd = "大单";
                    $ddxd1 = "大双";
                } else {
                    $ddxd1 = "大单";
                    $ddxd = "大双";
                }
            }

            if ($na < 50) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $ddxd1)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $ddxd)
                    ->update(["bm" => 1]);

                $resultddxd = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $ddxd)
                    ->first();

                if (isset($resultddxd)) {
                    $zwin8 = $resultddxd["re"];
                } else {
                    $zwin8 = 0;
                }
            }

            //结算正特

            for ($i = 1; $i <= 6; $i++) {

                if ($i == 1) {
                    $class2 = "正1特";
                    $tmtm = $n1;
                }

                if ($i == 2) {
                    $class2 = "正2特";
                    $tmtm = $n2;
                }

                if ($i == 3) {
                    $class2 = "正3特";
                    $tmtm = $n3;
                }

                if ($i == 4) {
                    $class2 = "正4特";
                    $tmtm = $n4;
                }

                if ($i == 5) {
                    $class2 = "正5特";
                    $tmtm = $n5;
                }

                if ($i == 6) {
                    $class2 = "正6特";
                    $tmtm = $n6;
                }

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $tmtm)
                    ->update(["bm" => 1]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("bm", "!=", 0)
                    ->where("class3", "!=", $tmtm)
                    ->where("class3", "!=", '单')
                    ->where("class3", "!=", '双')
                    ->where("class3", "!=", '大')
                    ->where("class3", "!=", '小')
                    ->where("class3", "!=", '合单')
                    ->where("class3", "!=", '合双')
                    ->where("class3", "!=", '红波')
                    ->where("class3", "!=", '蓝波')
                    ->where("class3", "!=", '绿波')
                    ->update(["bm" => 0]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $tmtm)
                    ->first();

                if (isset($result1)) {
                    $zwin9 = array();
                    array_push($zwin9, array("class2" => $class2, "count" => $result1["re"]));
                } else {
                    $zwin9 = array();
                    array_push($zwin9, array("class2" => $class2, "count" => 0));
                }

                //正特单双

                if ($tmtm % 2 == 1) {
                    $class3 = "单";
                    $class31 = "双";
                } else {
                    $class31 = "单";
                    $class3 = "双";
                }

                if ($tmtm == 49) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => 0));
                    }
                }

                //正特大小
                if ($tmtm >= 25) {
                    $class3 = "大";
                    $class31 = "小";
                } else {
                    $class31 = "大";
                    $class3 = "小";
                }

                if ($tmtm == 49) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin11 = array();
                        array_push($zwin11, $result1["re"]);
                    } else {
                        $zwin11 = array();
                        array_push($zwin11, 0);
                    }
                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin11 = array();
                        array_push($zwin11, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin11 = array();
                        array_push($zwin11, array("class2" => $class2, "count" => 0));
                    }
                }

                //正特合单合双
                if ((($tmtm % 10) + intval($tmtm / 10)) % 2 == 0) {
                    $class3 = "合双";
                    $class31 = "合单";
                } else {
                    $class31 = "合双";
                    $class3 = "合单";
                }

                if ($tmtm == 49) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => 0));
                    }
                }

                //正特合大合小
                if ((($tmtm % 10) + intval($tmtm / 10)) > 6) {
                    $class3 = "合大";
                    $class31 = "合小";
                } else {
                    $class31 = "合大";
                    $class3 = "合小";
                }

                if ($tmtm == 49) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => 0));
                    }
                }

                //结算正特波色

                $class3 = Utils::ka_Color_s($tmtm);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("bm", "!=", 0)
                    ->where(function ($query) {
                        $query->where('class3', '红波')
                            ->orWhere('class3', '蓝波')
                            ->orWhere('class3', '绿波');
                    })
                    ->update(["bm" => 0]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin14 = array();
                    array_push($zwin14, array("class2" => $class2, "count" => $result1["re"]));
                } else {
                    $zwin14 = array();
                    array_push($zwin14, array("class2" => $class2, "count" => 0));
                }
            }

            //结算正特结束

            //结算正码1-6

            for ($i = 1; $i <= 6; $i++) {

                if ($i == 1) {$class2 = "正码1";
                    $tmtm = $n1;}
                if ($i == 2) {$class2 = "正码2";
                    $tmtm = $n2;}
                if ($i == 3) {$class2 = "正码3";
                    $tmtm = $n3;}
                if ($i == 4) {$class2 = "正码4";
                    $tmtm = $n4;}
                if ($i == 5) {$class2 = "正码5";
                    $tmtm = $n5;}
                if ($i == 6) {$class2 = "正码6";
                    $tmtm = $n6;}

                //单双
                if ($tmtm % 2 == 1) {
                    $class3 = "单";
                    $class31 = "双";
                } else {
                    $class31 = "单";
                    $class3 = "双";
                }

                if ($tmtm == 49) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => 0));
                    }
                }

                //大小
                if ($tmtm >= 25) {
                    $class3 = "大";
                    $class31 = "小";
                } else {
                    $class31 = "大";
                    $class3 = "小";
                }

                if ($tmtm == 49) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => 0));
                    }
                }

                //合大合小
                if ((($tmtm % 10) + intval($tmtm / 10)) > 6) {
                    $class3 = "合大";
                    $class31 = "合小";
                } else {
                    $class31 = "合大";
                    $class3 = "合小";
                }

                if ($tmtm == 49) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => 0));
                    }
                }

                //合单合双
                if ((($tmtm % 10) + intval($tmtm / 10)) % 2 == 1) {
                    $class3 = "合单";
                    $class31 = "合双";
                } else {
                    $class31 = "合单";
                    $class3 = "合双";
                }

                if ($tmtm == 49) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => 0));
                    }
                }

                //尾大尾小
                if ($tmtm % 10 > 4) {
                    $class3 = "尾大";
                    $class31 = "尾小";
                } else {
                    $class31 = "尾大";
                    $class3 = "尾小";
                }

                if ($tmtm == 49) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '尾大')
                                ->orWhere('class3', '尾小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '尾大')
                                ->orWhere('class3', '尾小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin19 = array();
                        array_push($zwin19, $result1["re"]);
                    } else {
                        $zwin19 = array();
                        array_push($zwin19, 0);
                    }
                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin19 = array();
                        array_push($zwin19, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin19 = array();
                        array_push($zwin19, array("class2" => $class2, "count" => 0));
                    }
                }

                //波色
                $class3 = Utils::ka_Color_s($tmtm);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正1-6")
                    ->where("class2", $class2)
                    ->where("bm", "!=", 0)
                    ->where(function ($query) {
                        $query->where('class3', '红波')
                            ->orWhere('class3', '蓝波')
                            ->orWhere('class3', '绿波');
                    })
                    ->update(["bm" => 0]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正1-6")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "正1-6")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin20 = array();
                    array_push($zwin20, array("class2" => $class2, "count" => $result1["re"]));
                } else {
                    $zwin20 = array();
                    array_push($zwin20, array("class2" => $class2, "count" => 0));
                }
            }

            //结算正码1-6 END

            //正码
            $class2 = "正码";

            KaTan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("bm", "!=", 0)
                ->where('class3', "!=", $n1)
                ->where('class3', "!=", $n2)
                ->where('class3', "!=", $n3)
                ->where('class3', "!=", $n4)
                ->where('class3', "!=", $n5)
                ->where('class3', "!=", $n6)
                ->update(["bm" => 0]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where(function ($query) use ($n1, $n2, $n3, $n4, $n5, $n6) {
                    $query->where('class3', $n1)
                        ->orWhere('class3', $n2)
                        ->orWhere('class3', $n3)
                        ->orWhere('class3', $n4)
                        ->orWhere('class3', $n5)
                        ->orWhere('class3', $n6);
                })
                ->update(["bm" => 1]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class2", $class2)
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin21 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin21 = array("class2" => $class2, "count" => 0);
            }

            $sum_number = $n1 + $n2 + $n3 + $n4 + $n5 + $n6 + $na;

            $class2 = "正码";

            if ($sum_number % 2 == 1) {
                $class3 = "总单";
                $class31 = "总双";
            } else {
                $class31 = "总单";
                $class3 = "总双";
            }

            KaTan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->update(["bm" => 1]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("bm", "!=", 0)
                ->where("class3", $class31)
                ->update(["bm" => 0]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin22 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin22 = array("class2" => $class2, "count" => 0);
            }

            $class2 = "正码";

            if ($sum_number <= 174) {
                $class3 = "总小";
                $class31 = "总大";
            } else {
                $class31 = "总小";
                $class3 = "总大";
            }

            KaTan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->update(["bm" => 1]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("bm", "!=", 0)
                ->where("class3", $class31)
                ->update(["bm" => 0]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin23 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin23 = array("class2" => $class2, "count" => 0);
            }

            // 连码
            $class2 = "三全中";
            $zwin24 = array();
            $zwin = 0;

            $result = Katan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", "三全中")
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);

                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                }
                if ($number5 > 2) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin24 = array("class2" => $class2, "count" => $zwin);

            $class2 = "三中二";
            $zwin25 = array();
            $zwin = 0;

            $result = Katan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", "三中二")
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                }
                if ($number5 > 2) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else if ($number5 == 2) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1, "rate" => "rate2"]);

                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin25 = array("class2" => $class2, "count" => $zwin);

            $class2 = "二全中";
            $zwin26 = array();
            $zwin = 0;

            $result = Katan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                }
                if ($number5 > 1) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin26 = array("class2" => $class2, "count" => $zwin);

            $class2 = "二中特";
            $zwin27 = array();
            $zwin = 0;

            $result = Katan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $number4 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                    if ($numberxz[$i] == $na) {$number4++;}
                }
                if ($number5 > 1) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else if ($number4 == 1 and $number5 == 1) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1, "rate" => "rate2"]);

                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin27 = array("class2" => $class2, "count" => $zwin);

            $class2 = "特串";
            $zwin28 = 0;
            $zwin = 0;

            $result = Katan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $number4 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                    if ($numberxz[$i] == $na) {$number4++;}
                }

                if ($number4 == 1 and $number5 == 1) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin28 = array("class2" => $class2, "count" => $zwin);

            $class2 = "四中一";
            $zwin29 = array();
            $zwin = 0;

            $result = Katan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                    if ($numberxz[$i] == $na) {$number4++;}
                }

                if ($number5 > 0) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin29 = array("class2" => $class2, "count" => $zwin);

            //过关
            $class2 = "过关";
            $zwin30 = array();
            $zwin = 0;

            $result = Katan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "过关")
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $number4 = 0;
                $class3 = $item['class3'];
                $class2 = $item['class2'];
                $class33 = explode(",", $class3);
                $class22 = explode(",", $class2);
                $ss1 = count($class33);
                $ss2 = count($class22);
                $result = 0;
                $result2 = 1;
                for ($i = 0; $i < $ss2; $i++) {
                    if ($class22[$i] == "正码1") {$tmtm = $n1;}
                    if ($class22[$i] == "正码2") {$tmtm = $n2;}
                    if ($class22[$i] == "正码3") {$tmtm = $n3;}
                    if ($class22[$i] == "正码4") {$tmtm = $n4;}
                    if ($class22[$i] == "正码5") {$tmtm = $n5;}
                    if ($class22[$i] == "正码6") {$tmtm = $n6;}
                    $result = 0;
                    switch ($class33[$i]) {
                        case "大":
                            if ($tmtm >= 25) {$result = 1;}
                            break;
                        case "小":
                            if ($tmtm < 25) {$result = 1;}
                            break;
                        case "单":
                            if ($tmtm % 2 == 1) {$result = 1;}
                            break;
                        case "双":
                            if ($tmtm % 2 == 0) {$result = 1;}
                            break;
                        case "红波":
                            if (Utils::ka_Color_s($tmtm) == "红波") {$result = 1;}
                            break;
                        case "蓝波":
                            if (Utils::ka_Color_s($tmtm) == "蓝波") {$result = 1;}
                            break;
                        case "绿波":
                            if (Utils::ka_Color_s($tmtm) == "绿波") {$result = 1;}
                            break;
                        default:
                            $result = 0;
                            break;
                    }
                    if ($result == 0) {$result2 = 0;}
                }

                if ($result2 == 1) {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "过关")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    KaTan::where("kithe", $kithe)
                        ->where("class1", "过关")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "过关")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin30 = array("class2" => $class2, "count" => $zwin);

            //结算半波
            $class2 = "半波";
            $class3 = Utils::ka_Color_s($na);
            if ($class3 == "红波") {
                if ($na >= 25) {$class31 = "红大";} else { $class31 = "红小";}
                if ($na % 2 == 1) {$class32 = "红单";} else { $class32 = "红双";}
                if (($na % 10 + intval($na / 10)) % 2 == 1) {$class33 = "红合单";} else { $class33 = "红合双";}
            }

            if ($class3 == "绿波") {
                if ($na >= 25) {$class31 = "绿大";} else { $class31 = "绿小";}
                if ($na % 2 == 1) {$class32 = "绿单";} else { $class32 = "绿双";}
                if (($na % 10 + intval($na / 10)) % 2 == 1) {$class33 = "绿合单";} else { $class33 = "绿合双";}
            }
            if ($class3 == "蓝波") {
                if ($na >= 25) {$class31 = "蓝大";} else { $class31 = "蓝小";}
                if ($na % 2 == 1) {$class32 = "蓝单";} else { $class32 = "蓝双";}
                if (($na % 10 + intval($na / 10)) % 2 == 1) {$class33 = "蓝合单";} else { $class33 = "蓝合双";}
            }

            KaTan::where("kithe", $kithe)
                ->where("class1", "半波")
                ->where("bm", "!=", 0)
                ->where("class2", $class2)
                ->update(["bm" => 0]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31, $class32, $class33) {
                    $query->where('class3', $class31)
                        ->orWhere('class3', $class32)
                        ->orWhere('class3', $class33);
                })
                ->update(["bm" => 1]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31, $class32, $class33) {
                    $query->where('class3', $class31)
                        ->orWhere('class3', $class32)
                        ->orWhere('class3', $class33);
                })
                ->first();

            if (isset($result1)) {
                $zwin31 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin31 = array("class2" => $class2, "count" => 0);
            }

            //结算半半波
            $class2 = "半半波";
            $class3 = Utils::ka_Color_s($na);
            if ($class3 == "红波") {
                if ($na >= 25) {
                    if ($na % 2 == 1) {
                        $class31 = "红大单";
                    } else {
                        $class31 = "红大双";
                    }

                } else {
                    if ($na % 2 == 1) {
                        $class31 = "红小单";
                    } else {
                        $class31 = "红小双";
                    }

                }
            }
            if ($class3 == "绿波") {
                if ($na >= 25) {
                    if ($na % 2 == 1) {
                        $class31 = "绿大单";
                    } else {
                        $class31 = "绿大双";
                    }

                } else {
                    if ($na % 2 == 1) {
                        $class31 = "绿小单";
                    } else {
                        $class31 = "绿小双";
                    }

                }
            }
            if ($class3 == "蓝波") {
                if ($na >= 25) {
                    if ($na % 2 == 1) {
                        $class31 = "蓝大单";
                    } else {
                        $class31 = "蓝大双";
                    }

                } else {
                    if ($na % 2 == 1) {
                        $class31 = "蓝小单";
                    } else {
                        $class31 = "蓝小双";
                    }

                }
            }

            KaTan::where("kithe", $kithe)
                ->where("class1", "半半波")
                ->where("bm", "!=", 0)
                ->where("class2", $class2)
                ->update(["bm" => 0]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "半半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31) {
                    $query->where('class3', $class31);
                })
                ->update(["bm" => 1]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "半半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31) {
                    $query->where('class3', $class31);
                })
                ->first();

            if (isset($result1)) {
                $zwin32 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin32 = array("class2" => $class2, "count" => 0);
            }

            //结算生肖
            if ($na < 10) {$naa = "0" . $na;} else { $naa = $na;}
            $sxsx = Utils::Get_sx_Color($naa);

            KaTan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where("class2", "特肖")
                ->where('class3', "!=", $sxsx)
                ->where("bm", "!=", 0)
                ->update(["bm" => 0]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where("class2", "特肖")
                ->where('class3', $sxsx)
                ->update(["bm" => 1]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where("class2", "特肖")
                ->where('class3', $sxsx)
                ->first();

            if (isset($result1)) {
                $zwin33 = $result1["re"];
            } else {
                $zwin33 = 0;
            }

            //结算合肖
            if ($na < 10) {$naa = "0" . $na;} else { $naa = $na;}
            $sxsx = Utils::Get_sx_Color($naa);

            if ($tmtm == 49) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->update(["bm" => 2]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->first();

                if (isset($result1)) {
                    $zwin34 = $result1["re"];
                } else {
                    $zwin34 = 0;
                }
            } else {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->where("class3", "like", "%$sxsx%")
                    ->update(["bm" => 1]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->where("class3", "like", "%$sxsx%")
                    ->first();

                if (isset($result1)) {
                    $zwin34 = $result1["re"];
                } else {
                    $zwin34 = 0;
                }
            }

            //结算平肖

            if ($na < 10) {
                $naa = "0" . $na;
                $sxsx0 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $na;
                $sxsx0 = Utils::Get_sx_Color($naa);
            }

            if ($n1 < 10) {
                $naa = "0" . $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            }
            if ($n2 < 10) {
                $naa = "0" . $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            }
            if ($n3 < 10) {
                $naa = "0" . $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            }
            if ($n4 < 10) {
                $naa = "0" . $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            }
            if ($n5 < 10) {
                $naa = "0" . $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            }
            if ($n6 < 10) {
                $naa = "0" . $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            }

            KaTan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where('class2', '一肖')
                ->where("bm", "!=", 0)
                ->update(["bm" => 0]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where('class2', '一肖')
                ->where(function ($query) use ($sxsx0, $sxsx1, $sxsx2, $sxsx3, $sxsx4, $sxsx5, $sxsx6) {
                    $query->where('class3', $sxsx0)
                        ->orWhere('class3', $sxsx1)
                        ->orWhere('class3', $sxsx2)
                        ->orWhere('class3', $sxsx3)
                        ->orWhere('class3', $sxsx4)
                        ->orWhere('class3', $sxsx5)
                        ->orWhere('class3', $sxsx6);
                })
                ->update(["bm" => 1]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where('class2', '一肖')
                ->where(function ($query) use ($sxsx0, $sxsx1, $sxsx2, $sxsx3, $sxsx4, $sxsx5, $sxsx6) {
                    $query->where('class3', $sxsx0)
                        ->orWhere('class3', $sxsx1)
                        ->orWhere('class3', $sxsx2)
                        ->orWhere('class3', $sxsx3)
                        ->orWhere('class3', $sxsx4)
                        ->orWhere('class3', $sxsx5)
                        ->orWhere('class3', $sxsx6);
                })
                ->first();

            if (isset($result1)) {
                $zwin35 = $result1["re"];
            } else {
                $zwin35 = 0;
            }

            //结算正肖

            if ($n1 < 10) {
                $naa = "0" . $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            } else { $naa = $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            }
            if ($n2 < 10) {
                $naa = "0" . $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            }
            if ($n3 < 10) {
                $naa = "0" . $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            }
            if ($n4 < 10) {
                $naa = "0" . $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            }
            if ($n5 < 10) {
                $naa = "0" . $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            }
            if ($n6 < 10) {
                $naa = "0" . $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            }

            $sss = array();

            $sss["鼠"] = 0;
            $sss["虎"] = 0;
            $sss["龙"] = 0;
            $sss["马"] = 0;
            $sss["猴"] = 0;
            $sss["狗"] = 0;
            $sss["牛"] = 0;
            $sss["兔"] = 0;
            $sss["蛇"] = 0;
            $sss["羊"] = 0;
            $sss["鸡"] = 0;
            $sss["猪"] = 0;

            if ($sxsx1 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx1 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx1 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx1 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx1 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx1 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx1 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx1 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx1 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx1 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx1 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx1 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx2 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx2 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx2 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx2 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx2 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx2 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx2 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx2 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx2 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx2 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx2 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx2 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx3 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx3 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx3 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx3 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx3 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx3 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx3 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx3 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx3 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx3 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx3 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx3 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx4 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx4 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx4 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx4 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx4 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx4 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx4 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx4 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx4 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx4 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx4 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx4 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx5 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx5 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx5 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx5 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx5 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx5 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx5 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx5 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx5 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx5 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx5 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx5 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx6 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx6 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx6 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx6 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx6 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx6 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx6 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx6 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx6 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx6 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx6 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx6 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            KaTan::where("kithe", $kithe)
                ->where("class1", "正肖")
                ->where('class2', '正肖')
                ->where("bm", 0)
                ->update(["rate2" => "rate"]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "正肖")
                ->where('class2', '正肖')
                ->where("bm", "!=", 0)
                ->update(["bm" => 0]);

            if ($sss["鼠"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '鼠')
                    ->update(["bm" => 1]);

            }

            if ($sss["虎"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '虎')
                    ->update(["bm" => 1]);

            }

            if ($sss["龙"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '龙')
                    ->update(["bm" => 1]);

            }

            if ($sss["马"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '马')
                    ->update(["bm" => 1]);

            }

            if ($sss["猴"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '猴')
                    ->update(["bm" => 1]);

            }
            if ($sss["狗"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '狗')
                    ->update(["bm" => 1]);

            }
            if ($sss["牛"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '牛')
                    ->update(["bm" => 1]);

            }
            if ($sss["兔"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '兔')
                    ->update(["bm" => 1]);

            }
            if ($sss["蛇"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '兔')
                    ->update(["bm" => 1]);

            }
            if ($sss["羊"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '羊')
                    ->update(["bm" => 1]);

            }
            if ($sss["鸡"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '鸡')
                    ->update(["bm" => 1]);

            }
            if ($sss["猪"] > 0) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '猪')
                    ->update(["bm" => 1]);

            }

            if ($sss["牛"] == 1 && ($n1 == "49" || $n2 == "49" || $n3 == "49" || $n4 == "49" || $n5 == "49" || $n6 == "49")) {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '牛')
                    ->update(["bm" => 1]);
            }

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where('class2', '一肖')
                ->where(function ($query) use ($sss) {
                    if ($sss["鼠"] > 0) {
                        $query->orWhere("class3", "鼠");
                    }
                    if ($sss["虎"] > 0) {
                        $query->orWhere("class3", "虎");
                    }
                    if ($sss["龙"] > 0) {
                        $query->orWhere("class3", "龙");
                    }
                    if ($sss["马"] > 0) {
                        $query->orWhere("class3", "马");
                    }
                    if ($sss["猴"] > 0) {
                        $query->orWhere("class3", "猴");
                    }
                    if ($sss["狗"] > 0) {
                        $query->orWhere("class3", "狗");
                    }
                    if ($sss["牛"] > 0) {
                        $query->orWhere("class3", "牛");
                    }
                    if ($sss["兔"] > 0) {
                        $query->orWhere("class3", "兔");
                    }
                    if ($sss["蛇"] > 0) {
                        $query->orWhere("class3", "蛇");
                    }
                    if ($sss["羊"] > 0) {
                        $query->orWhere("class3", "羊");
                    }
                    if ($sss["鸡"] > 0) {
                        $query->orWhere("class3", "鸡");
                    }
                    if ($sss["猪"] > 0) {
                        $query->orWhere("class3", "猪");
                    }
                })
                ->first();

            if (isset($result1)) {
                $zwin36 = $result1["re"];
            } else {
                $zwin36 = 0;
            }

            //结算头数
            $wsws0 = floor($na / 10);

            KaTan::where("kithe", $kithe)
                ->where("class1", "头数")
                ->where('class2', '头数')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "头数")
                ->where('class2', '头数')
                ->where('class3', $wsws0)
                ->update(["bm" => 1]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "头数")
                ->where('class2', '头数')
                ->where('class3', $wsws0)
                ->first();

            if (isset($result1)) {
                $zwin37 = $result1["re"];
            } else {
                $zwin37 = 0;
            }

            //结算尾数
            $wsws0 = $na % 10;

            KaTan::where("kithe", $kithe)
                ->where("class1", "尾数")
                ->where('class2', '尾数')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "尾数")
                ->where('class2', '尾数')
                ->where('class3', $wsws0)
                ->update(["bm" => 1]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "尾数")
                ->where('class2', '尾数')
                ->where('class3', $wsws0)
                ->first();

            if (isset($result1)) {
                $zwin38 = $result1["re"];
            } else {
                $zwin38 = 0;
            }

            //结算正特尾数
            $wsws0 = $na % 10;
            $wsws1 = $n1 % 10;
            $wsws2 = $n2 % 10;
            $wsws3 = $n3 % 10;
            $wsws4 = $n4 % 10;
            $wsws5 = $n5 % 10;
            $wsws6 = $n6 % 10;

            KaTan::where("kithe", $kithe)
                ->where("class1", "正特尾数")
                ->where('class2', '正特尾数')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "正特尾数")
                ->where('class2', '正特尾数')
                ->where(function ($query) use ($wsws0, $wsws1, $wsws2, $wsws3, $wsws4, $wsws5, $wsws6) {
                    $query->where('class3', $wsws0)
                        ->orWhere('class3', $wsws1)
                        ->orWhere('class3', $wsws2)
                        ->orWhere('class3', $wsws3)
                        ->orWhere('class3', $wsws4)
                        ->orWhere('class3', $wsws5)
                        ->orWhere('class3', $wsws6);
                })
                ->update(["bm" => 1]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正特尾数")
                ->where('class2', '正特尾数')
                ->where(function ($query) use ($wsws0, $wsws1, $wsws2, $wsws3, $wsws4, $wsws5, $wsws6) {
                    $query->where('class3', $wsws0)
                        ->orWhere('class3', $wsws1)
                        ->orWhere('class3', $wsws2)
                        ->orWhere('class3', $wsws3)
                        ->orWhere('class3', $wsws4)
                        ->orWhere('class3', $wsws5)
                        ->orWhere('class3', $wsws6);
                })
                ->first();

            if (isset($result1)) {
                $zwin39 = $result1["re"];
            } else {
                $zwin39 = 0;
            }

            //结算七色波
            $wsws0 = Utils::ka_Color_s($na);
            $wsws1 = Utils::ka_Color_s($n1);
            $wsws2 = Utils::ka_Color_s($n2);
            $wsws3 = Utils::ka_Color_s($n3);
            $wsws4 = Utils::ka_Color_s($n4);
            $wsws5 = Utils::ka_Color_s($n5);
            $wsws6 = Utils::ka_Color_s($n6);

            $hongbo_na = 0;
            $lvbo_na = 0;
            $lanbo_na = 0;
            $hongbo = 0;
            $lvbo = 0;
            $lanbo = 0;
            $hongbo_z = 0;
            $lvbo_z = 0;
            $lanbo_z = 0;
            if ($wsws0 == "红波") {
                $hongbo_na = $hongbo_na + 1.5;
            }

            if ($wsws0 == "绿波") {
                $lvbo_na = $lvbo_na + 1.5;
            }

            if ($wsws0 == "蓝波") {
                $lanbo_na = $lanbo_na + 1.5;
            }

            if ($wsws1 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws1 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws1 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws2 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws2 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws2 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws3 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws3 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws3 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws4 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws4 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws4 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws5 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws5 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws5 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws6 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws6 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws6 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            $hongbo_z = $hongbo_na + $hongbo;
            $lvbo_z = $lvbo_na + $lvbo;
            $lanbo_z = $lanbo_na + $lanbo;

            if ($hongbo_z > $lvbo_z && $hongbo_z > $lanbo_z) {
                $qsbgo = "红波";
            }

            if ($lvbo_z > $hongbo_z && $lvbo_z > $lanbo_z) {
                $qsbgo = "绿波";
            }

            if ($lanbo_z > $hongbo_z && $lanbo_z > $lvbo_z) {
                $qsbgo = "蓝波";
            }

            if ($hongbo_z == 3 && $lvbo_z == 3 && $wsws0 == "蓝波") {
                $qsbgo = "合局";
            }

            if ($lvbo_z == 3 && $lanbo_z == 3 && $wsws0 == "红波") {
                $qsbgo = "合局";
            }

            if ($hongbo_z == 3 && $lanbo_z == 3 && $wsws0 == "绿波") {
                $qsbgo = "合局";
            }

            if ($qsbgo == "合局") {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('bm', "!=", 0)
                    ->update(["bm" => 0]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', "!=", "合局")
                    ->update(["bm" => 2]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', "合局")
                    ->update(["bm" => 1]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', "合局")
                    ->first();

                if (isset($result1)) {
                    $zwin40 = $result1["re"];
                } else {
                    $zwin40 = 0;
                }

            } else {

                KaTan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('bm', "!=", 0)
                    ->update(["bm" => 0]);

                KaTan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', $qsbgo)
                    ->update(["bm" => 1]);

                $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', $qsbgo)
                    ->first();

                if (isset($result1)) {
                    $zwin40 = $result1["re"];
                } else {
                    $zwin40 = 0;
                }
            }

            //结算五行
            if ($na < 10) {
                $wxwx = "0" . $na;
            } else {
                $wxwx = $na;
            }
            $wxwxwx = Utils::Get_wxwx_Color($wxwx);

            KaTan::where("kithe", $kithe)
                ->where("class1", "五行")
                ->where('class2', '五行')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

            KaTan::where("kithe", $kithe)
                ->where("class1", "五行")
                ->where('class2', '五行')
                ->where('class3', $wxwxwx)
                ->update(["bm" => 1]);

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "五行")
                ->where('class2', '五行')
                ->where('class3', $wxwxwx)
                ->first();

            if (isset($result1)) {
                $zwin41 = $result1["re"];
            } else {
                $zwin41 = 0;
            }

            //全不中

            KaTan::where("kithe", $kithe)
                ->where("class1", "全不中")
                ->update(["bm" => 1]);

            $result1kk = KaTan::where("kithe", $kithe)
                ->where("class1", "全不中")
                ->get(["class3"]);

            foreach ($result1kk as $item) {
                $class3 = $item["class3"];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $na || $numberxz[$i] == $n1 || $numberxz[$i] == $n2 || $numberxz[$i] == $n3 || $numberxz[$i] == $n4 || $numberxz[$i] == $n5 || $numberxz[$i] == $n6) {

                        KaTan::where("kithe", $kithe)
                            ->where("class1", "全不中")
                            ->where("class3", "like", "%$class3%")
                            ->update(["bm" => 0]);
                    }
                }

            }

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "全不中")
                ->where('bm', 1)
                ->first();

            if (isset($result1)) {
                $zwin42 = $result1["re"];
            } else {
                $zwin42 = 0;
            }

            //生肖连
            if (intval($n1) < 10) {
                $n1 = "0" . $n1;
            }

            if (intval($n2) < 10) {
                $n2 = "0" . $n2;
            }

            if (intval($n3) < 10) {
                $n3 = "0" . $n3;
            }

            if (intval($n4) < 10) {
                $n4 = "0" . $n4;
            }

            if (intval($n5) < 10) {
                $n5 = "0" . $n5;
            }

            if (intval($n6) < 10) {
                $n6 = "0" . $n6;
            }

            if (intval($na) < 10) {
                $na = "0" . $na;
            }

            $lx_sx1 = Utils::Get_sx_Color($n1);
            $lx_sx2 = Utils::Get_sx_Color($n2);
            $lx_sx3 = Utils::Get_sx_Color($n3);
            $lx_sx4 = Utils::Get_sx_Color($n4);
            $lx_sx5 = Utils::Get_sx_Color($n5);
            $lx_sx6 = Utils::Get_sx_Color($n6);
            $lx_sx7 = Utils::Get_sx_Color($na);

            KaTan::where("kithe", $kithe)
                ->where("class1", "生肖连")
                ->update(["bm" => 0]);

            $result = KaTan::where("kithe", $kithe)
                ->where("class1", "生肖连")
                ->get(["id", "class2", "class3"]);

            foreach ($result as $item) {
                $Rs_id = $item['id'];
                $class2 = $item['class2'];
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $cont = 0;
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($lx_sx1 == $numberxz[$i] || $lx_sx2 == $numberxz[$i] || $lx_sx3 == $numberxz[$i] || $lx_sx4 == $numberxz[$i] || $lx_sx5 == $numberxz[$i] || $lx_sx6 == $numberxz[$i] || $lx_sx7 == $numberxz[$i]) {
                        $cont += 1;
                        continue;
                    }
                }
                if ($cont == $ss1 && ($class2 == "二肖连中" || $class2 == "三肖连中" || $class2 == "四肖连中" || $class2 == "五肖连中")) {

                    KaTan::where("id", $Rs_id)
                        ->update(["bm" => 1]);

                }

                if ($cont == 0 && ($class2 == "二肖连不中" || $class2 == "三肖连不中" || $class2 == "四肖连不中")) {

                    KaTan::where("id", $Rs_id)
                        ->update(["bm" => 1]);
                }

            }

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "生肖连")
                ->where('bm', 1)
                ->first();

            if (isset($result1)) {
                $zwin43 = $result1["re"];
            } else {
                $zwin43 = 0;
            }

            KaTan::where("kithe", $kithe)
                ->where("class1", "尾数连")
                ->update(["bm" => 0]);

            $result = KaTan::where("kithe", $kithe)
                ->where("class1", "尾数连")
                ->get(["id", "class2", "class3"]);

            foreach ($result as $item) {
                $Rs_id = $item['id'];
                $class2 = $item['class2'];
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $cont = 0;
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if (substr($n1, -1) == $numberxz[$i] || substr($n2, -1) == $numberxz[$i] || substr($n3, -1) == $numberxz[$i] || substr($n4, -1) == $numberxz[$i] || substr($n5, -1) == $numberxz[$i] || substr($n6, -1) == $numberxz[$i] || substr($na, -1) == $numberxz[$i]) {
                        $cont += 1;
                        continue;
                    }
                }
                if ($cont == $ss1 && ($class2 == "二尾连中" || $class2 == "三尾连中" || $class2 == "四尾连中")) {

                    KaTan::where("id", $Rs_id)
                        ->update(["bm" => 1]);
                }
                if ($cont == 0 && ($class2 == "二尾连不中" || $class2 == "三尾连不中" || $class2 == "四尾连不中")) {

                    KaTan::where("id", $Rs_id)
                        ->update(["bm" => 1]);
                }

            }

            $result1 = Katan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "尾数连")
                ->where('bm', 1)
                ->first();

            if (isset($result1)) {
                $zwin44 = $result1["re"];
            } else {
                $zwin44 = 0;
            }

            $result = Katan::where("kithe", $kithe)->where("checked", 0)->get();

            $loginname = $request->user()->UserName;

            foreach ($result as $item) {
                $id = $item['id'];
                $username = $item['username'];
                if ($item['bm'] == 1) { //会员中奖
                    $gold = $item['sum_m'] * $item['rate'] + $item['sum_m'] * abs($item['user_ds']) / 100;
                    $status = '中奖';
                } elseif ($item['bm'] == 2) { //和局 不退水
                    $gold = $item['sum_m'];
                    $status = '和局';
                } else { //未中奖退水
                    $gold = $item['sum_m'] * abs($item['user_ds']) / 100;
                    $status = '未中奖，反水';
                }

                KaTan::where("id", $id)->update(["checked" => 1]);

                if ($gold !== 0) {
                    $previousAmount = Utils::GetField($username, 'Money');

                    $q1 = User::where("UserName", $username)->increment('Money', $gold);

                    if ($q1 == 1) {

                        $currentAmount = Utils::GetField($username, 'Money');

                        $user_id = Utils::GetField($username, 'id');

                        $datetime = date("Y-m-d H:i:s");

                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $item["num"];
                        $new_log->about = $loginname . "结复六合注单";
                        $new_log->update_time = $datetime;
                        $new_log->type = '投注金额:' . $item['sum_m'] . '&nbsp;赔率:' . $item['rate'] . '&nbsp;' . $item['class1'] . '&nbsp;' . $item['class2'] . '&nbsp;' . $item['class3'];
                        $new_log->order_value = $gold;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        $new_log->save();

                    } else {

                        $ka_tan = KaTan::find($id);

                        $ka_tan->checked = 0;
                        $ka_tan->bm = 0;

                        $ka_tan->save();

                    }

                }

            }

            $data = array(
                "zwin1" => $zwin1,
                "zwin2" => $zwin2,
                "zwin3" => $zwin3,
                "zwin4" => $zwin4,
                "zwin5" => $zwin5,
                "zwin6" => $zwin6,
                "zwin7" => $zwin7,
                "zwin8" => $zwin8,
                "zwin9" => $zwin9,
                "zwin10" => $zwin10,
                "zwin11" => $zwin11,
                "zwin12" => $zwin12,
                "zwin13" => $zwin13,
                "zwin14" => $zwin14,
                "zwin15" => $zwin15,
                "zwin16" => $zwin16,
                "zwin17" => $zwin17,
                "zwin18" => $zwin18,
                "zwin19" => $zwin19,
                "zwin20" => $zwin20,
                "zwin21" => $zwin21,
                "zwin22" => $zwin22,
                "zwin23" => $zwin23,
                "zwin24" => $zwin24,
                "zwin25" => $zwin25,
                "zwin26" => $zwin26,
                "zwin27" => $zwin27,
                "zwin28" => $zwin28,
                "zwin29" => $zwin29,
                "zwin30" => $zwin30,
                "zwin31" => $zwin31,
                "zwin32" => $zwin32,
                "zwin33" => $zwin33,
                "zwin34" => $zwin34,
                "zwin35" => $zwin35,
                "zwin36" => $zwin36,
                "zwin37" => $zwin37,
                "zwin38" => $zwin38,
                "zwin39" => $zwin39,
                "zwin40" => $zwin40,
                "zwin41" => $zwin41,
                "zwin42" => $zwin42,
                "zwin43" => $zwin43,
                "zwin44" => $zwin44,
            );

            Kakithe::where("nn", $kithe)->update(["score" => 1]);

            $response["data"] = $data;
            $response['message'] = "Kakithe Bet calculated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function editKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
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
            $nn = $request_data["nn"];
            $nd = $request_data["nd"];
            $na = $request_data["na"];
            $sx = $request_data["sx"];
            $n1 = $request_data["n1"];
            $n2 = $request_data["n2"];
            $n3 = $request_data["n3"];
            $n4 = $request_data["n4"];
            $n5 = $request_data["n5"];
            $n6 = $request_data["n6"];

            $ka_kithe = Kakithe::find($id);

            $ka_kithe->nn = $nn;
            $ka_kithe->nd = $nd;
            $ka_kithe->na = $na;
            $ka_kithe->sx = $sx;
            $ka_kithe->n1 = $n1;
            $ka_kithe->n2 = $n2;
            $ka_kithe->n3 = $n3;
            $ka_kithe->n4 = $n4;
            $ka_kithe->n5 = $n5;
            $ka_kithe->n6 = $n6;

            $ka_kithe->save();

            $response['message'] = "Kakithe edited successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function restoreKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];

            $loginname = $request->user()->UserName;

            $ka_kithe = Kakithe::find($id);

            $nn = $ka_kithe["nn"];

            $ka_tans = Katan::where("kithe", $nn)->orderBy("id", "asc")->get();

            foreach ($ka_tans as $item) {
                $id = $item["id"];
                $username = $item["username"];
                $bm = $item["bm"];
                $sum_m = $item["sum_m"];
                $rate = $item["rate"];
                $user_ds = $item["user_ds"];

                if ($item["checked"] == 1) {
                    switch ($bm) {
                        case 1:
                            $gold = $sum_m + $user_ds;
                            break;
                        case 2:
                            $gold = $sum_m;
                            break;
                        case 0:
                            $gold = $user_ds;
                            break;
                    }

                    $ka_tan = KaTan::find($id);

                    $ka_tan->checked = 0;
                    $ka_tan->bm = 0;

                    $ka_tan->save();

                    if ($gold !== 0) {
                        $previousAmount = Utils::GetField($username, 'Money');

                        $q1 = User::where("UserName", $username)->decrement('Money', $gold);

                        $q1 = User::where("UserName", $username)->decrement('withdrawal_condition', $gold);

                        if ($q1 == 1) {

                            $currentAmount = Utils::GetField($username, 'Money');

                            $user_id = Utils::GetField($username, 'id');

                            $datetime = date("Y-m-d H:i:s");

                            $new_log = new MoneyLog;
                            $new_log->user_id = $user_id;
                            $new_log->order_num = $item["num"];
                            $new_log->about = $loginname . "恢复六合注单";
                            $new_log->update_time = $datetime;
                            $new_log->type = '投注金额:' . $item['sum_m'] . '&nbsp;赔率:' . $item['rate'] . '&nbsp;' . $item['class1'] . '&nbsp;' . $item['class2'] . '&nbsp;' . $item['class3'];
                            $new_log->order_value = $gold;
                            $new_log->assets = $previousAmount;
                            $new_log->balance = $currentAmount;
                            $new_log->save();

                        } else {

                            $ka_tan = KaTan::find($id);

                            $ka_tan->checked = 1;
                            $ka_tan->bm = $bm;

                            $ka_tan->save();

                        }

                    }
                }
            }

            $ka_kithe->score = 0;

            $ka_kithe->save();

            $response['message'] = "Kakithe recovered successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];

            $ids = explode(",", $id);

            foreach ($ids as $item_id) {

                Kakithe::destroy($item_id);

            }

            $response['message'] = "Kakithe Item deleted successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateKakitheStatus(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $ka_kithe = Kakithe::find($id);

            $ka_kithe->lx = $status;

            $ka_kithe->save();

            $response['message'] = "Kakithe Status updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getKakitheAll(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "page" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $page = $request_data["page"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $period = $request_data["period"] ?? "";
            $status = $request_data["status"] ?? "";

            $ka_kithe = Kakithe::where("na", "!=", 0);

            if ($status !== "") {
                $ka_kithe = $ka_kithe->where("lx", $status);
            }

            if ($period !== "") {
                $ka_kithe = $ka_kithe->where("nn", "like", "%$period%");
            }

            $total_count = $ka_kithe->count();

            $ka_kithe = $ka_kithe->orderBy("id", "desc")
                ->offset(($page - 1) * $limit)
                ->take($limit)
                ->get();

            foreach ($ka_kithe as $item) {
                $item["checked"] = false;
                $item["lx"] = $item["lx"] == 1 ? true : false;
            }

            $response["total_count"] = $total_count;
            $response['data'] = $ka_kithe;
            $response['message'] = "Kakithe Data fetched successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getLotteryStatus(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $ka_kithe = Kakithe::where("na", 0)->first();

            $ka_kithe["best"] = $ka_kithe["best"] == 0 ? true : false;

            $response['data'] = $ka_kithe;
            $response['message'] = "Kakithe Data fetched successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveGameResult(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "n1" => "required",
                "n2" => "required",
                "n3" => "required",
                "n4" => "required",
                "n5" => "required",
                "n6" => "required",
                "na" => "required",
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $n1 = $request_data["n1"];
            $n2 = $request_data["n2"];
            $n3 = $request_data["n3"];
            $n4 = $request_data["n4"];
            $n5 = $request_data["n5"];
            $n6 = $request_data["n6"];
            $na = $request_data["na"];

            if ((int) $na !== 0) {
                $fb = (int) $na;

                if ($fb < 10) {
                    $vv = "0" . $fb;
                } else {
                    $vv = $fb;
                }

                $sx = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n1;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x1 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n2;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x2 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n3;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x3 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n4;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x4 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n5;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x5 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n6;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x6 = Utils::Get_sx_Color($vv);

                $ka_kithe = Kakithe::find($id);

                $ka_kithe->n1 = $n1;
                $ka_kithe->n2 = $n2;
                $ka_kithe->n3 = $n3;
                $ka_kithe->n4 = $n4;
                $ka_kithe->n5 = $n5;
                $ka_kithe->n6 = $n6;
                $ka_kithe->na = $na;
                $ka_kithe->x1 = $x1;
                $ka_kithe->x2 = $x2;
                $ka_kithe->x3 = $x3;
                $ka_kithe->x4 = $x4;
                $ka_kithe->x5 = $x5;
                $ka_kithe->x6 = $x6;
                $ka_kithe->sx = $sx;

                $ka_kithe->save();

                Kabl::query()->update(["gold" => 0]);

                $ya_kithe = Yakithe::orderBy("nn", "asc")->first();

                //添加新
                $ykaid = $ya_kithe['id'];
                $nn = $ya_kithe['nn'] + 3;

                $m_count = Kakithe::where("nn", $nn)->count();

                if ($m_count <= 0) {

                    $ka_kithe = new Kakithe();

                    $ka_kithe->best = $ya_kithe["best"];
                    $ka_kithe->nn = $ya_kithe["nn"];
                    $ka_kithe->nd = $ya_kithe["nd"];
                    $ka_kithe->kitm = $ya_kithe["kitm"];
                    $ka_kithe->kizt = $ya_kithe["kizt"];
                    $ka_kithe->kizm = $ya_kithe["kizm"];
                    $ka_kithe->kizm6 = $ya_kithe["kizm6"];
                    $ka_kithe->kigg = $ya_kithe["kigg"];
                    $ka_kithe->kilm = $ya_kithe["kilm"];
                    $ka_kithe->kisx = $ya_kithe["kisx"];
                    $ka_kithe->kibb = $ya_kithe["kibb"];
                    $ka_kithe->kiws = $ya_kithe["kiws"];
                    $ka_kithe->zfbdate = $ya_kithe["zfbdate"];
                    $ka_kithe->kitm1 = $ya_kithe["kitm1"];
                    $ka_kithe->kizt1 = $ya_kithe["kizt1"];
                    $ka_kithe->kizm1 = $ya_kithe["kizm1"];
                    $ka_kithe->kizm61 = $ya_kithe["kizm61"];
                    $ka_kithe->kigg1 = $ya_kithe["kigg1"];
                    $ka_kithe->kilm1 = $ya_kithe["kilm1"];
                    $ka_kithe->kisx1 = $ya_kithe["kisx1"];
                    $ka_kithe->kibb1 = $ya_kithe["kibb1"];
                    $ka_kithe->kiws1 = $ya_kithe["kiws1"];
                    $ka_kithe->zfbdate1 = $ya_kithe["zfbdate1"];
                    $ka_kithe->zfb = $ya_kithe["zfb"];
                    $ka_kithe->n1 = $ya_kithe["n1"];
                    $ka_kithe->n2 = $ya_kithe["n2"];
                    $ka_kithe->n3 = $ya_kithe["n3"];
                    $ka_kithe->n4 = $ya_kithe["n4"];
                    $ka_kithe->n5 = $ya_kithe["n5"];
                    $ka_kithe->n6 = $ya_kithe["n6"];
                    $ka_kithe->na = $ya_kithe["na"];
                    $ka_kithe->lx = $ya_kithe["lx"];

                    $ka_kithe->save();
                }

                Yakithe::where("id", $ykaid)->update(["nn" => $nn]);

            } else {

                $ka_kithe = Kakithe::find($id);

                $ka_kithe->n1 = $n1;
                $ka_kithe->n2 = $n2;
                $ka_kithe->n3 = $n3;
                $ka_kithe->n4 = $n4;
                $ka_kithe->n5 = $n5;
                $ka_kithe->n6 = $n6;
                $ka_kithe->na = $na;

                $ka_kithe->save();

            }

            $response['message'] = "Game Result updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateHandicap(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "zfb" => "required",
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $zfb = $request_data["zfb"];
            $best = $request_data["best"] ?? "";

            if ((int) $zfb == 1) {

                $ka_kithe = Kakithe::find($id);

                $ka_kithe->kitm = 1;
                $ka_kithe->kizt = 1;
                $ka_kithe->kizm = 1;
                $ka_kithe->kizm6 = 1;
                $ka_kithe->kigg = 1;
                $ka_kithe->kilm = 1;
                $ka_kithe->kisx = 1;
                $ka_kithe->kibb = 1;
                $ka_kithe->kiws = 1;
                $ka_kithe->zfb = 1;
                if ($best != "") {
                    $ka_kithe->best = $best;
                }

                $ka_kithe->save();

            } else {

                $ka_kithe = Kakithe::find($id);

                $ka_kithe->kitm = 0;
                $ka_kithe->kizt = 0;
                $ka_kithe->kizm = 0;
                $ka_kithe->kizm6 = 0;
                $ka_kithe->kigg = 0;
                $ka_kithe->kilm = 0;
                $ka_kithe->kisx = 0;
                $ka_kithe->kibb = 0;
                $ka_kithe->kiws = 0;
                $ka_kithe->zfb = 0;

                $ka_kithe->save();

            }

            $response['message'] = "Kithe Handicap updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateBest(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "best" => "required",
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $best = $request_data["best"];

            $ka_kithe = Kakithe::find($id);

            $ka_kithe->best = $best;

            if ($ka_kithe->save()) {
                $response["data"] = $ka_kithe;
                $response['message'] = "Kithe Best updated successfully!";
                $response['success'] = true;
                $response['status'] = STATUS_OK;
            }
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required",
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
            $kizm = $request_data["kizm"];
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

            $ya_kithe = Yakithe::orderBy("nn", "asc")->get();

            $version = 1;

            foreach ($ya_kithe as $item) {
                $nnd1 = $nn + $version;
                $data = Yakithe::find($item["id"]);
                $data->nn = $nnd1;
                $data->save();
                $version++;
            }

            $ka_kithe = Kakithe::find($id);

            $ka_kithe->nn = $nn;
            $ka_kithe->nd = $nd;
            $ka_kithe->kitm = $kitm;
            $ka_kithe->kizt = $kizt;
            $ka_kithe->kizm = $kizm;
            $ka_kithe->kizm6 = $kizm6;
            $ka_kithe->kigg = $kigg;
            $ka_kithe->kilm = $kilm;
            $ka_kithe->kisx = $kisx;
            $ka_kithe->kibb = $kibb;
            $ka_kithe->kiws = $kiws;
            $ka_kithe->zfbdate = $zfbdate;
            $ka_kithe->kitm1 = $kitm1;
            $ka_kithe->kizt1 = $kizt1;
            $ka_kithe->kizm1 = $kizm1;
            $ka_kithe->kizm61 = $kizm61;
            $ka_kithe->kigg1 = $kigg1;
            $ka_kithe->kilm1 = $kilm1;
            $ka_kithe->kisx1 = $kisx1;
            $ka_kithe->kibb1 = $kibb1;
            $ka_kithe->kiws1 = $kiws1;
            $ka_kithe->zfbdate1 = $zfbdate1;

            if ($ka_kithe->save()) {
                $response["data"] = $ka_kithe;
                $response['message'] = "Kithe Data updated successfully!";
                $response['success'] = true;
                $response['status'] = STATUS_OK;
            }
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function winMacaoKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $ka_kithe = MacaoKakithe::find($id);

            $kithe = $ka_kithe['nn'];
            $na = $ka_kithe['na'];
            $n1 = $ka_kithe['n1'];
            $n2 = $ka_kithe['n2'];
            $n3 = $ka_kithe['n3'];
            $n4 = $ka_kithe['n4'];
            $n5 = $ka_kithe['n5'];
            $n6 = $ka_kithe['n6'];
            $sxsx = $ka_kithe['sx'];

            //结算特码

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("class3", $na)
                ->update(["bm" => 1]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("bm", "!=", 0)
                ->where("class3", "!=", $na)
                ->where("class3", "!=", '单')
                ->where("class3", "!=", '双')
                ->where("class3", "!=", '大')
                ->where("class3", "!=", '小')
                ->where("class3", "!=", '合单')
                ->where("class3", "!=", '合双')
                ->where("class3", "!=", '红波')
                ->where("class3", "!=", '蓝波')
                ->where("class3", "!=", '绿波')
                ->update(["bm" => 0]);

            $Rs5 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)->where("class1", "特码")->where("class3", $na)
                ->first();

            if (isset($Rs5)) {
                $zwin1 = $Rs5["re"];
            } else {
                $zwin1 = 0;
            }

            // 特码单双

            if ($na % 2 == 1) {
                $class3 = "单";
                $class31 = "双";
            } else {
                $class31 = "单";
                $class3 = "双";
            }

            if ($na == 49) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '单')
                            ->where('class3', '双');
                    })
                    ->update(["bm" => 2]);

                $result1dd = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '单')
                            ->where('class3', '双');
                    })
                    ->first();

                if (isset($result1dd)) {
                    $zwin2 = $result1dd["re"];
                } else {
                    $zwin2 = 0;
                }

            } else {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                $result1ee = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1ee)) {
                    $zwin2 = $result1ee["re"];
                } else {
                    $zwin2 = 0;
                }
            }

            //特码大小

            if ($na >= 25) {
                $class3 = "大";
                $class31 = "小";
            } else {
                $class31 = "大";
                $class3 = "小";
            }

            if ($na == 49) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '大')
                            ->where('class3', '小');
                    })
                    ->update(["bm" => 1]);

                $result1ff = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '大')
                            ->where('class3', '小');
                    })
                    ->first();

                if (isset($result1ff)) {
                    $zwin3 = $result1ff["re"];
                } else {
                    $zwin3 = 0;
                }

            } else {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                $result1gg = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1gg)) {
                    $zwin3 = $result1gg["re"];
                } else {
                    $zwin3 = 0;
                }
            }

            //合单合双

            if ((($na % 10) + intval($na / 10)) % 2 == 0) {
                $class3 = "合双";
                $class31 = "合单";
            } else {
                $class31 = "合双";
                $class3 = "合单";
            }

            if ($na == 49) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '合单')
                            ->where('class3', '合双');
                    })
                    ->update(["bm" => 2]);

                $result1vv = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '合单')
                            ->where('class3', '合双');
                    })
                    ->first();

                if (isset($result1vv)) {
                    $zwin4 = $result1vv["re"];
                } else {
                    $zwin4 = 0;
                }
            } else {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin4 = $result1["re"];
                } else {
                    $zwin4 = 0;
                }
            }

            // 结算特码波色

            $class3 = Utils::ka_Color_s($na);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("bm", "!=", 0)
                ->where(function ($query) {
                    $query->where('class3', '红波')
                        ->orWhere('class3', '蓝波')
                        ->orWhere('class3', '绿波');
                })
                ->update(["bm" => 0]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("class3", $class3)
                ->update(["bm" => 1]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin5 = $result1["re"];
            } else {
                $zwin5 = 0;
            }

            //结算家禽/野兽

            if ($sxsx == "狗"
                || $sxsx == "猪"
                || $sxsx == "鸡"
                || $sxsx == "羊"
                || $sxsx == "马"
                || $sxsx == "牛") {

                $psx = "家禽";
                $psx1 = "野兽";

            } else {

                $psx = "野兽";
                $psx1 = "家禽";
            }

            if ($na == 49) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) use ($psx, $psx1) {
                        $query->where('class3', $psx)
                            ->where('class3', $psx1);
                    })
                    ->update(["bm" => 2]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) use ($psx, $psx1) {
                        $query->where('class3', $psx)
                            ->where('class3', $psx1);
                    })
                    ->first();

                if (isset($result1)) {
                    $zwin6 = $result1["re"];
                } else {
                    $zwin6 = 0;
                }
            } else {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $psx1)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $psx)
                    ->update(["bm" => 1]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $psx)
                    ->first();

                if (isset($result1)) {
                    $zwin6 = $result1["re"];
                } else {
                    $zwin6 = 0;
                }
            }

            //结算尾大/尾小

            $wdwx0 = $na % 10;

            if ($wdwx0 > 4) {
                $class3 = "尾大";
                $class31 = "尾小";
            } else {
                $class31 = "尾大";
                $class3 = "尾小";
            }

            if ($na == 49) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '尾大')
                            ->where('class3', '尾小');
                    })
                    ->update(["bm" => 2]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '尾大')
                            ->where('class3', '尾小');
                    })
                    ->first();

                if (isset($result1)) {
                    $zwin7 = $result1["re"];
                } else {
                    $zwin7 = 0;
                }
            } else {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin7 = $result1["re"];
                } else {
                    $zwin7 = 0;
                }
            }

            // 大单小单/大双小双

            if ($na <= 25) {
                if ($na % 2 == 1) {
                    $ddxd = "小单";
                    $ddxd1 = "小双";
                } else {
                    $ddxd1 = "小单";
                    $ddxd = "小双";
                }
            } else {
                if ($na % 2 == 1) {
                    $ddxd = "大单";
                    $ddxd1 = "大双";
                } else {
                    $ddxd1 = "大单";
                    $ddxd = "大双";
                }
            }

            if ($na < 50) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $ddxd1)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $ddxd)
                    ->update(["bm" => 1]);

                $resultddxd = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $ddxd)
                    ->first();

                if (isset($resultddxd)) {
                    $zwin8 = $resultddxd["re"];
                } else {
                    $zwin8 = 0;
                }
            }

            //结算正特

            for ($i = 1; $i <= 6; $i++) {

                if ($i == 1) {
                    $class2 = "正1特";
                    $tmtm = $n1;
                }

                if ($i == 2) {
                    $class2 = "正2特";
                    $tmtm = $n2;
                }

                if ($i == 3) {
                    $class2 = "正3特";
                    $tmtm = $n3;
                }

                if ($i == 4) {
                    $class2 = "正4特";
                    $tmtm = $n4;
                }

                if ($i == 5) {
                    $class2 = "正5特";
                    $tmtm = $n5;
                }

                if ($i == 6) {
                    $class2 = "正6特";
                    $tmtm = $n6;
                }

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $tmtm)
                    ->update(["bm" => 1]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("bm", "!=", 0)
                    ->where("class3", "!=", $tmtm)
                    ->where("class3", "!=", '单')
                    ->where("class3", "!=", '双')
                    ->where("class3", "!=", '大')
                    ->where("class3", "!=", '小')
                    ->where("class3", "!=", '合单')
                    ->where("class3", "!=", '合双')
                    ->where("class3", "!=", '红波')
                    ->where("class3", "!=", '蓝波')
                    ->where("class3", "!=", '绿波')
                    ->update(["bm" => 0]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $tmtm)
                    ->first();

                if (isset($result1)) {
                    $zwin9 = array();
                    array_push($zwin9, array("class2" => $class2, "count" => $result1["re"]));
                } else {
                    $zwin9 = array();
                    array_push($zwin9, array("class2" => $class2, "count" => 0));
                }

                //正特单双

                if ($tmtm % 2 == 1) {
                    $class3 = "单";
                    $class31 = "双";
                } else {
                    $class31 = "单";
                    $class3 = "双";
                }

                if ($tmtm == 49) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => 0));
                    }
                }

                //正特大小
                if ($tmtm >= 25) {
                    $class3 = "大";
                    $class31 = "小";
                } else {
                    $class31 = "大";
                    $class3 = "小";
                }

                if ($tmtm == 49) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin11 = array();
                        array_push($zwin11, $result1["re"]);
                    } else {
                        $zwin11 = array();
                        array_push($zwin11, 0);
                    }
                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin11 = array();
                        array_push($zwin11, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin11 = array();
                        array_push($zwin11, array("class2" => $class2, "count" => 0));
                    }
                }

                //正特合单合双
                if ((($tmtm % 10) + intval($tmtm / 10)) % 2 == 0) {
                    $class3 = "合双";
                    $class31 = "合单";
                } else {
                    $class31 = "合双";
                    $class3 = "合单";
                }

                if ($tmtm == 49) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => 0));
                    }
                }

                //正特合大合小
                if ((($tmtm % 10) + intval($tmtm / 10)) > 6) {
                    $class3 = "合大";
                    $class31 = "合小";
                } else {
                    $class31 = "合大";
                    $class3 = "合小";
                }

                if ($tmtm == 49) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => 0));
                    }
                }

                //结算正特波色

                $class3 = Utils::ka_Color_s($tmtm);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("bm", "!=", 0)
                    ->where(function ($query) {
                        $query->where('class3', '红波')
                            ->orWhere('class3', '蓝波')
                            ->orWhere('class3', '绿波');
                    })
                    ->update(["bm" => 0]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin14 = array();
                    array_push($zwin14, array("class2" => $class2, "count" => $result1["re"]));
                } else {
                    $zwin14 = array();
                    array_push($zwin14, array("class2" => $class2, "count" => 0));
                }
            }

            //结算正特结束

            //结算正码1-6

            for ($i = 1; $i <= 6; $i++) {

                if ($i == 1) {$class2 = "正码1";
                    $tmtm = $n1;}
                if ($i == 2) {$class2 = "正码2";
                    $tmtm = $n2;}
                if ($i == 3) {$class2 = "正码3";
                    $tmtm = $n3;}
                if ($i == 4) {$class2 = "正码4";
                    $tmtm = $n4;}
                if ($i == 5) {$class2 = "正码5";
                    $tmtm = $n5;}
                if ($i == 6) {$class2 = "正码6";
                    $tmtm = $n6;}

                //单双
                if ($tmtm % 2 == 1) {
                    $class3 = "单";
                    $class31 = "双";
                } else {
                    $class31 = "单";
                    $class3 = "双";
                }

                if ($tmtm == 49) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => 0));
                    }
                }

                //大小
                if ($tmtm >= 25) {
                    $class3 = "大";
                    $class31 = "小";
                } else {
                    $class31 = "大";
                    $class3 = "小";
                }

                if ($tmtm == 49) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => 0));
                    }
                }

                //合大合小
                if ((($tmtm % 10) + intval($tmtm / 10)) > 6) {
                    $class3 = "合大";
                    $class31 = "合小";
                } else {
                    $class31 = "合大";
                    $class3 = "合小";
                }

                if ($tmtm == 49) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => 0));
                    }
                }

                //合单合双
                if ((($tmtm % 10) + intval($tmtm / 10)) % 2 == 1) {
                    $class3 = "合单";
                    $class31 = "合双";
                } else {
                    $class31 = "合单";
                    $class3 = "合双";
                }

                if ($tmtm == 49) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => 0));
                    }
                }

                //尾大尾小
                if ($tmtm % 10 > 4) {
                    $class3 = "尾大";
                    $class31 = "尾小";
                } else {
                    $class31 = "尾大";
                    $class3 = "尾小";
                }

                if ($tmtm == 49) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '尾大')
                                ->orWhere('class3', '尾小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '尾大')
                                ->orWhere('class3', '尾小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin19 = array();
                        array_push($zwin19, $result1["re"]);
                    } else {
                        $zwin19 = array();
                        array_push($zwin19, 0);
                    }
                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin19 = array();
                        array_push($zwin19, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin19 = array();
                        array_push($zwin19, array("class2" => $class2, "count" => 0));
                    }
                }

                //波色
                $class3 = Utils::ka_Color_s($tmtm);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正1-6")
                    ->where("class2", $class2)
                    ->where("bm", "!=", 0)
                    ->where(function ($query) {
                        $query->where('class3', '红波')
                            ->orWhere('class3', '蓝波')
                            ->orWhere('class3', '绿波');
                    })
                    ->update(["bm" => 0]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正1-6")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "正1-6")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin20 = array();
                    array_push($zwin20, array("class2" => $class2, "count" => $result1["re"]));
                } else {
                    $zwin20 = array();
                    array_push($zwin20, array("class2" => $class2, "count" => 0));
                }
            }

            //结算正码1-6 END

            //正码
            $class2 = "正码";

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("bm", "!=", 0)
                ->where('class3', "!=", $n1)
                ->where('class3', "!=", $n2)
                ->where('class3', "!=", $n3)
                ->where('class3', "!=", $n4)
                ->where('class3', "!=", $n5)
                ->where('class3', "!=", $n6)
                ->update(["bm" => 0]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where(function ($query) use ($n1, $n2, $n3, $n4, $n5, $n6) {
                    $query->where('class3', $n1)
                        ->orWhere('class3', $n2)
                        ->orWhere('class3', $n3)
                        ->orWhere('class3', $n4)
                        ->orWhere('class3', $n5)
                        ->orWhere('class3', $n6);
                })
                ->update(["bm" => 1]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class2", $class2)
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin21 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin21 = array("class2" => $class2, "count" => 0);
            }

            $sum_number = $n1 + $n2 + $n3 + $n4 + $n5 + $n6 + $na;

            $class2 = "正码";

            if ($sum_number % 2 == 1) {
                $class3 = "总单";
                $class31 = "总双";
            } else {
                $class31 = "总单";
                $class3 = "总双";
            }

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->update(["bm" => 1]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("bm", "!=", 0)
                ->where("class3", $class31)
                ->update(["bm" => 0]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin22 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin22 = array("class2" => $class2, "count" => 0);
            }

            $class2 = "正码";

            if ($sum_number <= 174) {
                $class3 = "总小";
                $class31 = "总大";
            } else {
                $class31 = "总小";
                $class3 = "总大";
            }

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->update(["bm" => 1]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("bm", "!=", 0)
                ->where("class3", $class31)
                ->update(["bm" => 0]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin23 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin23 = array("class2" => $class2, "count" => 0);
            }

            // 连码
            $class2 = "三全中";
            $zwin24 = array();
            $zwin = 0;

            $result = MacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", "三全中")
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);

                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                }
                if ($number5 > 2) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin24 = array("class2" => $class2, "count" => $zwin);

            $class2 = "三中二";
            $zwin25 = array();
            $zwin = 0;

            $result = MacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", "三中二")
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                }
                if ($number5 > 2) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else if ($number5 == 2) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1, "rate" => "rate2"]);

                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin25 = array("class2" => $class2, "count" => $zwin);

            $class2 = "二全中";
            $zwin26 = array();
            $zwin = 0;

            $result = MacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                }
                if ($number5 > 1) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin26 = array("class2" => $class2, "count" => $zwin);

            $class2 = "二中特";
            $zwin27 = array();
            $zwin = 0;

            $result = MacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $number4 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                    if ($numberxz[$i] == $na) {$number4++;}
                }
                if ($number5 > 1) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else if ($number4 == 1 and $number5 == 1) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1, "rate" => "rate2"]);

                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin27 = array("class2" => $class2, "count" => $zwin);

            $class2 = "特串";
            $zwin28 = 0;
            $zwin = 0;

            $result = MacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $number4 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                    if ($numberxz[$i] == $na) {$number4++;}
                }

                if ($number4 == 1 and $number5 == 1) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin28 = array("class2" => $class2, "count" => $zwin);

            $class2 = "四中一";
            $zwin29 = array();
            $zwin = 0;

            $result = MacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                    if ($numberxz[$i] == $na) {$number4++;}
                }

                if ($number5 > 0) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin29 = array("class2" => $class2, "count" => $zwin);

            //过关
            $class2 = "过关";
            $zwin30 = array();
            $zwin = 0;

            $result = MacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "过关")
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $number4 = 0;
                $class3 = $item['class3'];
                $class2 = $item['class2'];
                $class33 = explode(",", $class3);
                $class22 = explode(",", $class2);
                $ss1 = count($class33);
                $ss2 = count($class22);
                $result = 0;
                $result2 = 1;
                for ($i = 0; $i < $ss2; $i++) {
                    if ($class22[$i] == "正码1") {$tmtm = $n1;}
                    if ($class22[$i] == "正码2") {$tmtm = $n2;}
                    if ($class22[$i] == "正码3") {$tmtm = $n3;}
                    if ($class22[$i] == "正码4") {$tmtm = $n4;}
                    if ($class22[$i] == "正码5") {$tmtm = $n5;}
                    if ($class22[$i] == "正码6") {$tmtm = $n6;}
                    $result = 0;
                    switch ($class33[$i]) {
                        case "大":
                            if ($tmtm >= 25) {$result = 1;}
                            break;
                        case "小":
                            if ($tmtm < 25) {$result = 1;}
                            break;
                        case "单":
                            if ($tmtm % 2 == 1) {$result = 1;}
                            break;
                        case "双":
                            if ($tmtm % 2 == 0) {$result = 1;}
                            break;
                        case "红波":
                            if (Utils::ka_Color_s($tmtm) == "红波") {$result = 1;}
                            break;
                        case "蓝波":
                            if (Utils::ka_Color_s($tmtm) == "蓝波") {$result = 1;}
                            break;
                        case "绿波":
                            if (Utils::ka_Color_s($tmtm) == "绿波") {$result = 1;}
                            break;
                        default:
                            $result = 0;
                            break;
                    }
                    if ($result == 0) {$result2 = 0;}
                }

                if ($result2 == 1) {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "过关")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    MacaoKatan::where("kithe", $kithe)
                        ->where("class1", "过关")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "过关")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin30 = array("class2" => $class2, "count" => $zwin);

            //结算半波
            $class2 = "半波";
            $class3 = Utils::ka_Color_s($na);
            if ($class3 == "红波") {
                if ($na >= 25) {$class31 = "红大";} else { $class31 = "红小";}
                if ($na % 2 == 1) {$class32 = "红单";} else { $class32 = "红双";}
                if (($na % 10 + intval($na / 10)) % 2 == 1) {$class33 = "红合单";} else { $class33 = "红合双";}
            }

            if ($class3 == "绿波") {
                if ($na >= 25) {$class31 = "绿大";} else { $class31 = "绿小";}
                if ($na % 2 == 1) {$class32 = "绿单";} else { $class32 = "绿双";}
                if (($na % 10 + intval($na / 10)) % 2 == 1) {$class33 = "绿合单";} else { $class33 = "绿合双";}
            }
            if ($class3 == "蓝波") {
                if ($na >= 25) {$class31 = "蓝大";} else { $class31 = "蓝小";}
                if ($na % 2 == 1) {$class32 = "蓝单";} else { $class32 = "蓝双";}
                if (($na % 10 + intval($na / 10)) % 2 == 1) {$class33 = "蓝合单";} else { $class33 = "蓝合双";}
            }

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "半波")
                ->where("bm", "!=", 0)
                ->where("class2", $class2)
                ->update(["bm" => 0]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31, $class32, $class33) {
                    $query->where('class3', $class31)
                        ->orWhere('class3', $class32)
                        ->orWhere('class3', $class33);
                })
                ->update(["bm" => 1]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31, $class32, $class33) {
                    $query->where('class3', $class31)
                        ->orWhere('class3', $class32)
                        ->orWhere('class3', $class33);
                })
                ->first();

            if (isset($result1)) {
                $zwin31 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin31 = array("class2" => $class2, "count" => 0);
            }

            //结算半半波
            $class2 = "半半波";
            $class3 = Utils::ka_Color_s($na);
            if ($class3 == "红波") {
                if ($na >= 25) {
                    if ($na % 2 == 1) {
                        $class31 = "红大单";
                    } else {
                        $class31 = "红大双";
                    }

                } else {
                    if ($na % 2 == 1) {
                        $class31 = "红小单";
                    } else {
                        $class31 = "红小双";
                    }

                }
            }
            if ($class3 == "绿波") {
                if ($na >= 25) {
                    if ($na % 2 == 1) {
                        $class31 = "绿大单";
                    } else {
                        $class31 = "绿大双";
                    }

                } else {
                    if ($na % 2 == 1) {
                        $class31 = "绿小单";
                    } else {
                        $class31 = "绿小双";
                    }

                }
            }
            if ($class3 == "蓝波") {
                if ($na >= 25) {
                    if ($na % 2 == 1) {
                        $class31 = "蓝大单";
                    } else {
                        $class31 = "蓝大双";
                    }

                } else {
                    if ($na % 2 == 1) {
                        $class31 = "蓝小单";
                    } else {
                        $class31 = "蓝小双";
                    }

                }
            }

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "半半波")
                ->where("bm", "!=", 0)
                ->where("class2", $class2)
                ->update(["bm" => 0]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "半半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31) {
                    $query->where('class3', $class31);
                })
                ->update(["bm" => 1]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "半半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31) {
                    $query->where('class3', $class31);
                })
                ->first();

            if (isset($result1)) {
                $zwin32 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin32 = array("class2" => $class2, "count" => 0);
            }

            //结算生肖
            if ($na < 10) {$naa = "0" . $na;} else { $naa = $na;}
            $sxsx = Utils::Get_sx_Color($naa);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where("class2", "特肖")
                ->where('class3', "!=", $sxsx)
                ->where("bm", "!=", 0)
                ->update(["bm" => 0]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where("class2", "特肖")
                ->where('class3', $sxsx)
                ->update(["bm" => 1]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where("class2", "特肖")
                ->where('class3', $sxsx)
                ->first();

            if (isset($result1)) {
                $zwin33 = $result1["re"];
            } else {
                $zwin33 = 0;
            }

            //结算合肖
            if ($na < 10) {$naa = "0" . $na;} else { $naa = $na;}
            $sxsx = Utils::Get_sx_Color($naa);

            if ($tmtm == 49) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->update(["bm" => 2]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->first();

                if (isset($result1)) {
                    $zwin34 = $result1["re"];
                } else {
                    $zwin34 = 0;
                }
            } else {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->where("class3", "like", "%$sxsx%")
                    ->update(["bm" => 1]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->where("class3", "like", "%$sxsx%")
                    ->first();

                if (isset($result1)) {
                    $zwin34 = $result1["re"];
                } else {
                    $zwin34 = 0;
                }
            }

            //结算平肖

            if ($na < 10) {
                $naa = "0" . $na;
                $sxsx0 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $na;
                $sxsx0 = Utils::Get_sx_Color($naa);
            }

            if ($n1 < 10) {
                $naa = "0" . $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            }
            if ($n2 < 10) {
                $naa = "0" . $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            }
            if ($n3 < 10) {
                $naa = "0" . $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            }
            if ($n4 < 10) {
                $naa = "0" . $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            }
            if ($n5 < 10) {
                $naa = "0" . $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            }
            if ($n6 < 10) {
                $naa = "0" . $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            }

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where('class2', '一肖')
                ->where("bm", "!=", 0)
                ->update(["bm" => 0]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where('class2', '一肖')
                ->where(function ($query) use ($sxsx0, $sxsx1, $sxsx2, $sxsx3, $sxsx4, $sxsx5, $sxsx6) {
                    $query->where('class3', $sxsx0)
                        ->orWhere('class3', $sxsx1)
                        ->orWhere('class3', $sxsx2)
                        ->orWhere('class3', $sxsx3)
                        ->orWhere('class3', $sxsx4)
                        ->orWhere('class3', $sxsx5)
                        ->orWhere('class3', $sxsx6);
                })
                ->update(["bm" => 1]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where('class2', '一肖')
                ->where(function ($query) use ($sxsx0, $sxsx1, $sxsx2, $sxsx3, $sxsx4, $sxsx5, $sxsx6) {
                    $query->where('class3', $sxsx0)
                        ->orWhere('class3', $sxsx1)
                        ->orWhere('class3', $sxsx2)
                        ->orWhere('class3', $sxsx3)
                        ->orWhere('class3', $sxsx4)
                        ->orWhere('class3', $sxsx5)
                        ->orWhere('class3', $sxsx6);
                })
                ->first();

            if (isset($result1)) {
                $zwin35 = $result1["re"];
            } else {
                $zwin35 = 0;
            }

            //结算正肖

            if ($n1 < 10) {
                $naa = "0" . $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            } else { $naa = $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            }
            if ($n2 < 10) {
                $naa = "0" . $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            }
            if ($n3 < 10) {
                $naa = "0" . $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            }
            if ($n4 < 10) {
                $naa = "0" . $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            }
            if ($n5 < 10) {
                $naa = "0" . $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            }
            if ($n6 < 10) {
                $naa = "0" . $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            }

            $sss = array();

            $sss["鼠"] = 0;
            $sss["虎"] = 0;
            $sss["龙"] = 0;
            $sss["马"] = 0;
            $sss["猴"] = 0;
            $sss["狗"] = 0;
            $sss["牛"] = 0;
            $sss["兔"] = 0;
            $sss["蛇"] = 0;
            $sss["羊"] = 0;
            $sss["鸡"] = 0;
            $sss["猪"] = 0;

            if ($sxsx1 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx1 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx1 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx1 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx1 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx1 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx1 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx1 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx1 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx1 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx1 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx1 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx2 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx2 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx2 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx2 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx2 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx2 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx2 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx2 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx2 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx2 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx2 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx2 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx3 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx3 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx3 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx3 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx3 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx3 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx3 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx3 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx3 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx3 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx3 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx3 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx4 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx4 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx4 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx4 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx4 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx4 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx4 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx4 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx4 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx4 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx4 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx4 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx5 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx5 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx5 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx5 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx5 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx5 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx5 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx5 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx5 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx5 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx5 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx5 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx6 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx6 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx6 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx6 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx6 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx6 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx6 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx6 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx6 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx6 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx6 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx6 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "正肖")
                ->where('class2', '正肖')
                ->where("bm", 0)
                ->update(["rate2" => "rate"]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "正肖")
                ->where('class2', '正肖')
                ->where("bm", "!=", 0)
                ->update(["bm" => 0]);

            if ($sss["鼠"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '鼠')
                    ->update(["bm" => 1]);

            }

            if ($sss["虎"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '虎')
                    ->update(["bm" => 1]);

            }

            if ($sss["龙"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '龙')
                    ->update(["bm" => 1]);

            }

            if ($sss["马"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '马')
                    ->update(["bm" => 1]);

            }

            if ($sss["猴"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '猴')
                    ->update(["bm" => 1]);

            }
            if ($sss["狗"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '狗')
                    ->update(["bm" => 1]);

            }
            if ($sss["牛"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '牛')
                    ->update(["bm" => 1]);

            }
            if ($sss["兔"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '兔')
                    ->update(["bm" => 1]);

            }
            if ($sss["蛇"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '兔')
                    ->update(["bm" => 1]);

            }
            if ($sss["羊"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '羊')
                    ->update(["bm" => 1]);

            }
            if ($sss["鸡"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '鸡')
                    ->update(["bm" => 1]);

            }
            if ($sss["猪"] > 0) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '猪')
                    ->update(["bm" => 1]);

            }

            if ($sss["牛"] == 1 && ($n1 == "49" || $n2 == "49" || $n3 == "49" || $n4 == "49" || $n5 == "49" || $n6 == "49")) {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '牛')
                    ->update(["bm" => 1]);
            }

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where('class2', '一肖')
                ->where(function ($query) use ($sss) {
                    if ($sss["鼠"] > 0) {
                        $query->orWhere("class3", "鼠");
                    }
                    if ($sss["虎"] > 0) {
                        $query->orWhere("class3", "虎");
                    }
                    if ($sss["龙"] > 0) {
                        $query->orWhere("class3", "龙");
                    }
                    if ($sss["马"] > 0) {
                        $query->orWhere("class3", "马");
                    }
                    if ($sss["猴"] > 0) {
                        $query->orWhere("class3", "猴");
                    }
                    if ($sss["狗"] > 0) {
                        $query->orWhere("class3", "狗");
                    }
                    if ($sss["牛"] > 0) {
                        $query->orWhere("class3", "牛");
                    }
                    if ($sss["兔"] > 0) {
                        $query->orWhere("class3", "兔");
                    }
                    if ($sss["蛇"] > 0) {
                        $query->orWhere("class3", "蛇");
                    }
                    if ($sss["羊"] > 0) {
                        $query->orWhere("class3", "羊");
                    }
                    if ($sss["鸡"] > 0) {
                        $query->orWhere("class3", "鸡");
                    }
                    if ($sss["猪"] > 0) {
                        $query->orWhere("class3", "猪");
                    }
                })
                ->first();

            if (isset($result1)) {
                $zwin36 = $result1["re"];
            } else {
                $zwin36 = 0;
            }

            //结算头数
            $wsws0 = floor($na / 10);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "头数")
                ->where('class2', '头数')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "头数")
                ->where('class2', '头数')
                ->where('class3', $wsws0)
                ->update(["bm" => 1]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "头数")
                ->where('class2', '头数')
                ->where('class3', $wsws0)
                ->first();

            if (isset($result1)) {
                $zwin37 = $result1["re"];
            } else {
                $zwin37 = 0;
            }

            //结算尾数
            $wsws0 = $na % 10;

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "尾数")
                ->where('class2', '尾数')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "尾数")
                ->where('class2', '尾数')
                ->where('class3', $wsws0)
                ->update(["bm" => 1]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "尾数")
                ->where('class2', '尾数')
                ->where('class3', $wsws0)
                ->first();

            if (isset($result1)) {
                $zwin38 = $result1["re"];
            } else {
                $zwin38 = 0;
            }

            //结算正特尾数
            $wsws0 = $na % 10;
            $wsws1 = $n1 % 10;
            $wsws2 = $n2 % 10;
            $wsws3 = $n3 % 10;
            $wsws4 = $n4 % 10;
            $wsws5 = $n5 % 10;
            $wsws6 = $n6 % 10;

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "正特尾数")
                ->where('class2', '正特尾数')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "正特尾数")
                ->where('class2', '正特尾数')
                ->where(function ($query) use ($wsws0, $wsws1, $wsws2, $wsws3, $wsws4, $wsws5, $wsws6) {
                    $query->where('class3', $wsws0)
                        ->orWhere('class3', $wsws1)
                        ->orWhere('class3', $wsws2)
                        ->orWhere('class3', $wsws3)
                        ->orWhere('class3', $wsws4)
                        ->orWhere('class3', $wsws5)
                        ->orWhere('class3', $wsws6);
                })
                ->update(["bm" => 1]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正特尾数")
                ->where('class2', '正特尾数')
                ->where(function ($query) use ($wsws0, $wsws1, $wsws2, $wsws3, $wsws4, $wsws5, $wsws6) {
                    $query->where('class3', $wsws0)
                        ->orWhere('class3', $wsws1)
                        ->orWhere('class3', $wsws2)
                        ->orWhere('class3', $wsws3)
                        ->orWhere('class3', $wsws4)
                        ->orWhere('class3', $wsws5)
                        ->orWhere('class3', $wsws6);
                })
                ->first();

            if (isset($result1)) {
                $zwin39 = $result1["re"];
            } else {
                $zwin39 = 0;
            }

            //结算七色波
            $wsws0 = Utils::ka_Color_s($na);
            $wsws1 = Utils::ka_Color_s($n1);
            $wsws2 = Utils::ka_Color_s($n2);
            $wsws3 = Utils::ka_Color_s($n3);
            $wsws4 = Utils::ka_Color_s($n4);
            $wsws5 = Utils::ka_Color_s($n5);
            $wsws6 = Utils::ka_Color_s($n6);

            $hongbo_na = 0;
            $lvbo_na = 0;
            $lanbo_na = 0;
            $hongbo = 0;
            $lvbo = 0;
            $lanbo = 0;
            $hongbo_z = 0;
            $lvbo_z = 0;
            $lanbo_z = 0;
            if ($wsws0 == "红波") {
                $hongbo_na = $hongbo_na + 1.5;
            }

            if ($wsws0 == "绿波") {
                $lvbo_na = $lvbo_na + 1.5;
            }

            if ($wsws0 == "蓝波") {
                $lanbo_na = $lanbo_na + 1.5;
            }

            if ($wsws1 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws1 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws1 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws2 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws2 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws2 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws3 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws3 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws3 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws4 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws4 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws4 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws5 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws5 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws5 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws6 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws6 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws6 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            $hongbo_z = $hongbo_na + $hongbo;
            $lvbo_z = $lvbo_na + $lvbo;
            $lanbo_z = $lanbo_na + $lanbo;

            if ($hongbo_z > $lvbo_z && $hongbo_z > $lanbo_z) {
                $qsbgo = "红波";
            }

            if ($lvbo_z > $hongbo_z && $lvbo_z > $lanbo_z) {
                $qsbgo = "绿波";
            }

            if ($lanbo_z > $hongbo_z && $lanbo_z > $lvbo_z) {
                $qsbgo = "蓝波";
            }

            if ($hongbo_z == 3 && $lvbo_z == 3 && $wsws0 == "蓝波") {
                $qsbgo = "合局";
            }

            if ($lvbo_z == 3 && $lanbo_z == 3 && $wsws0 == "红波") {
                $qsbgo = "合局";
            }

            if ($hongbo_z == 3 && $lanbo_z == 3 && $wsws0 == "绿波") {
                $qsbgo = "合局";
            }

            if ($qsbgo == "合局") {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('bm', "!=", 0)
                    ->update(["bm" => 0]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', "!=", "合局")
                    ->update(["bm" => 2]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', "合局")
                    ->update(["bm" => 1]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', "合局")
                    ->first();

                if (isset($result1)) {
                    $zwin40 = $result1["re"];
                } else {
                    $zwin40 = 0;
                }

            } else {

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('bm', "!=", 0)
                    ->update(["bm" => 0]);

                MacaoKatan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', $qsbgo)
                    ->update(["bm" => 1]);

                $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', $qsbgo)
                    ->first();

                if (isset($result1)) {
                    $zwin40 = $result1["re"];
                } else {
                    $zwin40 = 0;
                }
            }

            //结算五行
            if ($na < 10) {
                $wxwx = "0" . $na;
            } else {
                $wxwx = $na;
            }
            $wxwxwx = Utils::Get_wxwx_Color($wxwx);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "五行")
                ->where('class2', '五行')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "五行")
                ->where('class2', '五行')
                ->where('class3', $wxwxwx)
                ->update(["bm" => 1]);

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "五行")
                ->where('class2', '五行')
                ->where('class3', $wxwxwx)
                ->first();

            if (isset($result1)) {
                $zwin41 = $result1["re"];
            } else {
                $zwin41 = 0;
            }

            //全不中

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "全不中")
                ->update(["bm" => 1]);

            $result1kk = MacaoKatan::where("kithe", $kithe)
                ->where("class1", "全不中")
                ->get(["class3"]);

            foreach ($result1kk as $item) {
                $class3 = $item["class3"];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $na || $numberxz[$i] == $n1 || $numberxz[$i] == $n2 || $numberxz[$i] == $n3 || $numberxz[$i] == $n4 || $numberxz[$i] == $n5 || $numberxz[$i] == $n6) {

                        MacaoKatan::where("kithe", $kithe)
                            ->where("class1", "全不中")
                            ->where("class3", "like", "%$class3%")
                            ->update(["bm" => 0]);
                    }
                }

            }

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "全不中")
                ->where('bm', 1)
                ->first();

            if (isset($result1)) {
                $zwin42 = $result1["re"];
            } else {
                $zwin42 = 0;
            }

            //生肖连
            if (intval($n1) < 10) {
                $n1 = "0" . $n1;
            }

            if (intval($n2) < 10) {
                $n2 = "0" . $n2;
            }

            if (intval($n3) < 10) {
                $n3 = "0" . $n3;
            }

            if (intval($n4) < 10) {
                $n4 = "0" . $n4;
            }

            if (intval($n5) < 10) {
                $n5 = "0" . $n5;
            }

            if (intval($n6) < 10) {
                $n6 = "0" . $n6;
            }

            if (intval($na) < 10) {
                $na = "0" . $na;
            }

            $lx_sx1 = Utils::Get_sx_Color($n1);
            $lx_sx2 = Utils::Get_sx_Color($n2);
            $lx_sx3 = Utils::Get_sx_Color($n3);
            $lx_sx4 = Utils::Get_sx_Color($n4);
            $lx_sx5 = Utils::Get_sx_Color($n5);
            $lx_sx6 = Utils::Get_sx_Color($n6);
            $lx_sx7 = Utils::Get_sx_Color($na);

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖连")
                ->update(["bm" => 0]);

            $result = MacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖连")
                ->get(["id", "class2", "class3"]);

            foreach ($result as $item) {
                $Rs_id = $item['id'];
                $class2 = $item['class2'];
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $cont = 0;
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($lx_sx1 == $numberxz[$i] || $lx_sx2 == $numberxz[$i] || $lx_sx3 == $numberxz[$i] || $lx_sx4 == $numberxz[$i] || $lx_sx5 == $numberxz[$i] || $lx_sx6 == $numberxz[$i] || $lx_sx7 == $numberxz[$i]) {
                        $cont += 1;
                        continue;
                    }
                }
                if ($cont == $ss1 && ($class2 == "二肖连中" || $class2 == "三肖连中" || $class2 == "四肖连中" || $class2 == "五肖连中")) {

                    MacaoKatan::where("id", $Rs_id)
                        ->update(["bm" => 1]);

                }

                if ($cont == 0 && ($class2 == "二肖连不中" || $class2 == "三肖连不中" || $class2 == "四肖连不中")) {

                    MacaoKatan::where("id", $Rs_id)
                        ->update(["bm" => 1]);
                }

            }

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "生肖连")
                ->where('bm', 1)
                ->first();

            if (isset($result1)) {
                $zwin43 = $result1["re"];
            } else {
                $zwin43 = 0;
            }

            MacaoKatan::where("kithe", $kithe)
                ->where("class1", "尾数连")
                ->update(["bm" => 0]);

            $result = MacaoKatan::where("kithe", $kithe)
                ->where("class1", "尾数连")
                ->get(["id", "class2", "class3"]);

            foreach ($result as $item) {
                $Rs_id = $item['id'];
                $class2 = $item['class2'];
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $cont = 0;
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if (substr($n1, -1) == $numberxz[$i] || substr($n2, -1) == $numberxz[$i] || substr($n3, -1) == $numberxz[$i] || substr($n4, -1) == $numberxz[$i] || substr($n5, -1) == $numberxz[$i] || substr($n6, -1) == $numberxz[$i] || substr($na, -1) == $numberxz[$i]) {
                        $cont += 1;
                        continue;
                    }
                }
                if ($cont == $ss1 && ($class2 == "二尾连中" || $class2 == "三尾连中" || $class2 == "四尾连中")) {

                    MacaoKatan::where("id", $Rs_id)
                        ->update(["bm" => 1]);
                }
                if ($cont == 0 && ($class2 == "二尾连不中" || $class2 == "三尾连不中" || $class2 == "四尾连不中")) {

                    MacaoKatan::where("id", $Rs_id)
                        ->update(["bm" => 1]);
                }

            }

            $result1 = MacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "尾数连")
                ->where('bm', 1)
                ->first();

            if (isset($result1)) {
                $zwin44 = $result1["re"];
            } else {
                $zwin44 = 0;
            }

            $result = MacaoKatan::where("kithe", $kithe)->where("checked", 0)->get();

            $loginname = $request->user()->UserName;

            foreach ($result as $item) {
                $id = $item['id'];
                $username = $item['username'];
                if ($item['bm'] == 1) { //会员中奖
                    $gold = $item['sum_m'] * $item['rate'] + $item['sum_m'] * abs($item['user_ds']) / 100;
                    $status = '中奖';
                } elseif ($item['bm'] == 2) { //和局 不退水
                    $gold = $item['sum_m'];
                    $status = '和局';
                } else { //未中奖退水
                    $gold = $item['sum_m'] * abs($item['user_ds']) / 100;
                    $status = '未中奖，反水';
                }

                MacaoKatan::where("id", $id)->update(["checked" => 1]);

                if ($gold !== 0) {
                    $previousAmount = Utils::GetField($username, 'Money');

                    $q1 = User::where("UserName", $username)->increment('Money', $gold);

                    if ($q1 == 1) {

                        $currentAmount = Utils::GetField($username, 'Money');

                        $user_id = Utils::GetField($username, 'id');

                        $datetime = date("Y-m-d H:i:s");

                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $item["num"];
                        $new_log->about = $loginname . "结复六合注单";
                        $new_log->update_time = $datetime;
                        $new_log->type = '投注金额:' . $item['sum_m'] . '&nbsp;赔率:' . $item['rate'] . '&nbsp;' . $item['class1'] . '&nbsp;' . $item['class2'] . '&nbsp;' . $item['class3'];
                        $new_log->order_value = $gold;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        $new_log->save();

                    } else {

                        $ka_tan = MacaoKatan::find($id);

                        $ka_tan->checked = 0;
                        $ka_tan->bm = 0;

                        $ka_tan->save();

                    }

                }

            }

            $data = array(
                "zwin1" => $zwin1,
                "zwin2" => $zwin2,
                "zwin3" => $zwin3,
                "zwin4" => $zwin4,
                "zwin5" => $zwin5,
                "zwin6" => $zwin6,
                "zwin7" => $zwin7,
                "zwin8" => $zwin8,
                "zwin9" => $zwin9,
                "zwin10" => $zwin10,
                "zwin11" => $zwin11,
                "zwin12" => $zwin12,
                "zwin13" => $zwin13,
                "zwin14" => $zwin14,
                "zwin15" => $zwin15,
                "zwin16" => $zwin16,
                "zwin17" => $zwin17,
                "zwin18" => $zwin18,
                "zwin19" => $zwin19,
                "zwin20" => $zwin20,
                "zwin21" => $zwin21,
                "zwin22" => $zwin22,
                "zwin23" => $zwin23,
                "zwin24" => $zwin24,
                "zwin25" => $zwin25,
                "zwin26" => $zwin26,
                "zwin27" => $zwin27,
                "zwin28" => $zwin28,
                "zwin29" => $zwin29,
                "zwin30" => $zwin30,
                "zwin31" => $zwin31,
                "zwin32" => $zwin32,
                "zwin33" => $zwin33,
                "zwin34" => $zwin34,
                "zwin35" => $zwin35,
                "zwin36" => $zwin36,
                "zwin37" => $zwin37,
                "zwin38" => $zwin38,
                "zwin39" => $zwin39,
                "zwin40" => $zwin40,
                "zwin41" => $zwin41,
                "zwin42" => $zwin42,
                "zwin43" => $zwin43,
                "zwin44" => $zwin44,
            );

            MacaoKakithe::where("nn", $kithe)->update(["score" => 1]);

            $response["data"] = $data;
            $response['message'] = "MacaoKakithe Bet calculated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function winNewMacaoKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $ka_kithe = NewMacaoKakithe::find($id);

            $kithe = $ka_kithe['nn'];
            $na = $ka_kithe['na'];
            $n1 = $ka_kithe['n1'];
            $n2 = $ka_kithe['n2'];
            $n3 = $ka_kithe['n3'];
            $n4 = $ka_kithe['n4'];
            $n5 = $ka_kithe['n5'];
            $n6 = $ka_kithe['n6'];
            $sxsx = $ka_kithe['sx'];

            //结算特码

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("class3", $na)
                ->update(["bm" => 1]);

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("bm", "!=", 0)
                ->where("class3", "!=", $na)
                ->where("class3", "!=", '单')
                ->where("class3", "!=", '双')
                ->where("class3", "!=", '大')
                ->where("class3", "!=", '小')
                ->where("class3", "!=", '合单')
                ->where("class3", "!=", '合双')
                ->where("class3", "!=", '红波')
                ->where("class3", "!=", '蓝波')
                ->where("class3", "!=", '绿波')
                ->update(["bm" => 0]);

            $Rs5 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)->where("class1", "特码")->where("class3", $na)
                ->first();

            if (isset($Rs5)) {
                $zwin1 = $Rs5["re"];
            } else {
                $zwin1 = 0;
            }

            // 特码单双

            if ($na % 2 == 1) {
                $class3 = "单";
                $class31 = "双";
            } else {
                $class31 = "单";
                $class3 = "双";
            }

            if ($na == 49) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '单')
                            ->where('class3', '双');
                    })
                    ->update(["bm" => 2]);

                $result1dd = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '单')
                            ->where('class3', '双');
                    })
                    ->first();

                if (isset($result1dd)) {
                    $zwin2 = $result1dd["re"];
                } else {
                    $zwin2 = 0;
                }

            } else {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                $result1ee = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1ee)) {
                    $zwin2 = $result1ee["re"];
                } else {
                    $zwin2 = 0;
                }
            }

            //特码大小

            if ($na >= 25) {
                $class3 = "大";
                $class31 = "小";
            } else {
                $class31 = "大";
                $class3 = "小";
            }

            if ($na == 49) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '大')
                            ->where('class3', '小');
                    })
                    ->update(["bm" => 1]);

                $result1ff = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '大')
                            ->where('class3', '小');
                    })
                    ->first();

                if (isset($result1ff)) {
                    $zwin3 = $result1ff["re"];
                } else {
                    $zwin3 = 0;
                }

            } else {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                $result1gg = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1gg)) {
                    $zwin3 = $result1gg["re"];
                } else {
                    $zwin3 = 0;
                }
            }

            //合单合双

            if ((($na % 10) + intval($na / 10)) % 2 == 0) {
                $class3 = "合双";
                $class31 = "合单";
            } else {
                $class31 = "合双";
                $class3 = "合单";
            }

            if ($na == 49) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '合单')
                            ->where('class3', '合双');
                    })
                    ->update(["bm" => 2]);

                $result1vv = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '合单')
                            ->where('class3', '合双');
                    })
                    ->first();

                if (isset($result1vv)) {
                    $zwin4 = $result1vv["re"];
                } else {
                    $zwin4 = 0;
                }
            } else {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin4 = $result1["re"];
                } else {
                    $zwin4 = 0;
                }
            }

            // 结算特码波色

            $class3 = Utils::ka_Color_s($na);

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("bm", "!=", 0)
                ->where(function ($query) {
                    $query->where('class3', '红波')
                        ->orWhere('class3', '蓝波')
                        ->orWhere('class3', '绿波');
                })
                ->update(["bm" => 0]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("class3", $class3)
                ->update(["bm" => 1]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "特码")
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin5 = $result1["re"];
            } else {
                $zwin5 = 0;
            }

            //结算家禽/野兽

            if ($sxsx == "狗"
                || $sxsx == "猪"
                || $sxsx == "鸡"
                || $sxsx == "羊"
                || $sxsx == "马"
                || $sxsx == "牛") {

                $psx = "家禽";
                $psx1 = "野兽";

            } else {

                $psx = "野兽";
                $psx1 = "家禽";
            }

            if ($na == 49) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) use ($psx, $psx1) {
                        $query->where('class3', $psx)
                            ->where('class3', $psx1);
                    })
                    ->update(["bm" => 2]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) use ($psx, $psx1) {
                        $query->where('class3', $psx)
                            ->where('class3', $psx1);
                    })
                    ->first();

                if (isset($result1)) {
                    $zwin6 = $result1["re"];
                } else {
                    $zwin6 = 0;
                }
            } else {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $psx1)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $psx)
                    ->update(["bm" => 1]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $psx)
                    ->first();

                if (isset($result1)) {
                    $zwin6 = $result1["re"];
                } else {
                    $zwin6 = 0;
                }
            }

            //结算尾大/尾小

            $wdwx0 = $na % 10;

            if ($wdwx0 > 4) {
                $class3 = "尾大";
                $class31 = "尾小";
            } else {
                $class31 = "尾大";
                $class3 = "尾小";
            }

            if ($na == 49) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '尾大')
                            ->where('class3', '尾小');
                    })
                    ->update(["bm" => 2]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where(function ($query) {
                        $query->where('class3', '尾大')
                            ->where('class3', '尾小');
                    })
                    ->first();

                if (isset($result1)) {
                    $zwin7 = $result1["re"];
                } else {
                    $zwin7 = 0;
                }
            } else {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class31)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin7 = $result1["re"];
                } else {
                    $zwin7 = 0;
                }
            }

            // 大单小单/大双小双

            if ($na <= 25) {
                if ($na % 2 == 1) {
                    $ddxd = "小单";
                    $ddxd1 = "小双";
                } else {
                    $ddxd1 = "小单";
                    $ddxd = "小双";
                }
            } else {
                if ($na % 2 == 1) {
                    $ddxd = "大单";
                    $ddxd1 = "大双";
                } else {
                    $ddxd1 = "大单";
                    $ddxd = "大双";
                }
            }

            if ($na < 50) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $ddxd1)
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $ddxd)
                    ->update(["bm" => 1]);

                $resultddxd = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "特码")
                    ->where("class3", $ddxd)
                    ->first();

                if (isset($resultddxd)) {
                    $zwin8 = $resultddxd["re"];
                } else {
                    $zwin8 = 0;
                }
            }

            //结算正特

            for ($i = 1; $i <= 6; $i++) {

                if ($i == 1) {
                    $class2 = "正1特";
                    $tmtm = $n1;
                }

                if ($i == 2) {
                    $class2 = "正2特";
                    $tmtm = $n2;
                }

                if ($i == 3) {
                    $class2 = "正3特";
                    $tmtm = $n3;
                }

                if ($i == 4) {
                    $class2 = "正4特";
                    $tmtm = $n4;
                }

                if ($i == 5) {
                    $class2 = "正5特";
                    $tmtm = $n5;
                }

                if ($i == 6) {
                    $class2 = "正6特";
                    $tmtm = $n6;
                }

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $tmtm)
                    ->update(["bm" => 1]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("bm", "!=", 0)
                    ->where("class3", "!=", $tmtm)
                    ->where("class3", "!=", '单')
                    ->where("class3", "!=", '双')
                    ->where("class3", "!=", '大')
                    ->where("class3", "!=", '小')
                    ->where("class3", "!=", '合单')
                    ->where("class3", "!=", '合双')
                    ->where("class3", "!=", '红波')
                    ->where("class3", "!=", '蓝波')
                    ->where("class3", "!=", '绿波')
                    ->update(["bm" => 0]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $tmtm)
                    ->first();

                if (isset($result1)) {
                    $zwin9 = array();
                    array_push($zwin9, array("class2" => $class2, "count" => $result1["re"]));
                } else {
                    $zwin9 = array();
                    array_push($zwin9, array("class2" => $class2, "count" => 0));
                }

                //正特单双

                if ($tmtm % 2 == 1) {
                    $class3 = "单";
                    $class31 = "双";
                } else {
                    $class31 = "单";
                    $class3 = "双";
                }

                if ($tmtm == 49) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                        NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin10 = array();
                        array_push($zwin10, array("class2" => $class2, "count" => 0));
                    }
                }

                //正特大小
                if ($tmtm >= 25) {
                    $class3 = "大";
                    $class31 = "小";
                } else {
                    $class31 = "大";
                    $class3 = "小";
                }

                if ($tmtm == 49) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin11 = array();
                        array_push($zwin11, $result1["re"]);
                    } else {
                        $zwin11 = array();
                        array_push($zwin11, 0);
                    }
                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                        NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin11 = array();
                        array_push($zwin11, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin11 = array();
                        array_push($zwin11, array("class2" => $class2, "count" => 0));
                    }
                }

                //正特合单合双
                if ((($tmtm % 10) + intval($tmtm / 10)) % 2 == 0) {
                    $class3 = "合双";
                    $class31 = "合单";
                } else {
                    $class31 = "合双";
                    $class3 = "合单";
                }

                if ($tmtm == 49) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                        NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin12 = array();
                        array_push($zwin12, array("class2" => $class2, "count" => 0));
                    }
                }

                //正特合大合小
                if ((($tmtm % 10) + intval($tmtm / 10)) > 6) {
                    $class3 = "合大";
                    $class31 = "合小";
                } else {
                    $class31 = "合大";
                    $class3 = "合小";
                }

                if ($tmtm == 49) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                        NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正特")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin13 = array();
                        array_push($zwin13, array("class2" => $class2, "count" => 0));
                    }
                }

                //结算正特波色

                $class3 = Utils::ka_Color_s($tmtm);

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("bm", "!=", 0)
                    ->where(function ($query) {
                        $query->where('class3', '红波')
                            ->orWhere('class3', '蓝波')
                            ->orWhere('class3', '绿波');
                    })
                    ->update(["bm" => 0]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "正特")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin14 = array();
                    array_push($zwin14, array("class2" => $class2, "count" => $result1["re"]));
                } else {
                    $zwin14 = array();
                    array_push($zwin14, array("class2" => $class2, "count" => 0));
                }
            }

            //结算正特结束

            //结算正码1-6

            for ($i = 1; $i <= 6; $i++) {

                if ($i == 1) {$class2 = "正码1";
                    $tmtm = $n1;}
                if ($i == 2) {$class2 = "正码2";
                    $tmtm = $n2;}
                if ($i == 3) {$class2 = "正码3";
                    $tmtm = $n3;}
                if ($i == 4) {$class2 = "正码4";
                    $tmtm = $n4;}
                if ($i == 5) {$class2 = "正码5";
                    $tmtm = $n5;}
                if ($i == 6) {$class2 = "正码6";
                    $tmtm = $n6;}

                //单双
                if ($tmtm % 2 == 1) {
                    $class3 = "单";
                    $class31 = "双";
                } else {
                    $class31 = "单";
                    $class3 = "双";
                }

                if ($tmtm == 49) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '单')
                                ->orWhere('class3', '双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                        NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin15 = array();
                        array_push($zwin15, array("class2" => $class2, "count" => 0));
                    }
                }

                //大小
                if ($tmtm >= 25) {
                    $class3 = "大";
                    $class31 = "小";
                } else {
                    $class31 = "大";
                    $class3 = "小";
                }

                if ($tmtm == 49) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '大')
                                ->orWhere('class3', '小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                        NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin16 = array();
                        array_push($zwin16, array("class2" => $class2, "count" => 0));
                    }
                }

                //合大合小
                if ((($tmtm % 10) + intval($tmtm / 10)) > 6) {
                    $class3 = "合大";
                    $class31 = "合小";
                } else {
                    $class31 = "合大";
                    $class3 = "合小";
                }

                if ($tmtm == 49) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合大')
                                ->orWhere('class3', '合小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                        NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin17 = array();
                        array_push($zwin17, array("class2" => $class2, "count" => 0));
                    }
                }

                //合单合双
                if ((($tmtm % 10) + intval($tmtm / 10)) % 2 == 1) {
                    $class3 = "合单";
                    $class31 = "合双";
                } else {
                    $class31 = "合单";
                    $class3 = "合双";
                }

                if ($tmtm == 49) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->update(["bm" => 2]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '合单')
                                ->orWhere('class3', '合双');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => 0));
                    }
                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                        NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin18 = array();
                        array_push($zwin18, array("class2" => $class2, "count" => 0));
                    }
                }

                //尾大尾小
                if ($tmtm % 10 > 4) {
                    $class3 = "尾大";
                    $class31 = "尾小";
                } else {
                    $class31 = "尾大";
                    $class3 = "尾小";
                }

                if ($tmtm == 49) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '尾大')
                                ->orWhere('class3', '尾小');
                        })
                        ->update(["bm" => 2]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where(function ($query) {
                            $query->where('class3', '尾大')
                                ->orWhere('class3', '尾小');
                        })
                        ->first();

                    if (isset($result1)) {
                        $zwin19 = array();
                        array_push($zwin19, $result1["re"]);
                    } else {
                        $zwin19 = array();
                        array_push($zwin19, 0);
                    }
                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class31)
                        ->where("bm", "!=", 0)
                        ->update(["bm" => 0]);

                        NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                    $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                        ->where("kithe", $kithe)
                        ->where("class1", "正1-6")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->first();

                    if (isset($result1)) {
                        $zwin19 = array();
                        array_push($zwin19, array("class2" => $class2, "count" => $result1["re"]));
                    } else {
                        $zwin19 = array();
                        array_push($zwin19, array("class2" => $class2, "count" => 0));
                    }
                }

                //波色
                $class3 = Utils::ka_Color_s($tmtm);

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正1-6")
                    ->where("class2", $class2)
                    ->where("bm", "!=", 0)
                    ->where(function ($query) {
                        $query->where('class3', '红波')
                            ->orWhere('class3', '蓝波')
                            ->orWhere('class3', '绿波');
                    })
                    ->update(["bm" => 0]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正1-6")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->update(["bm" => 1]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "正1-6")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin20 = array();
                    array_push($zwin20, array("class2" => $class2, "count" => $result1["re"]));
                } else {
                    $zwin20 = array();
                    array_push($zwin20, array("class2" => $class2, "count" => 0));
                }
            }

            //结算正码1-6 END

            //正码
            $class2 = "正码";

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("bm", "!=", 0)
                ->where('class3', "!=", $n1)
                ->where('class3', "!=", $n2)
                ->where('class3', "!=", $n3)
                ->where('class3', "!=", $n4)
                ->where('class3', "!=", $n5)
                ->where('class3', "!=", $n6)
                ->update(["bm" => 0]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where(function ($query) use ($n1, $n2, $n3, $n4, $n5, $n6) {
                    $query->where('class3', $n1)
                        ->orWhere('class3', $n2)
                        ->orWhere('class3', $n3)
                        ->orWhere('class3', $n4)
                        ->orWhere('class3', $n5)
                        ->orWhere('class3', $n6);
                })
                ->update(["bm" => 1]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class2", $class2)
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin21 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin21 = array("class2" => $class2, "count" => 0);
            }

            $sum_number = $n1 + $n2 + $n3 + $n4 + $n5 + $n6 + $na;

            $class2 = "正码";

            if ($sum_number % 2 == 1) {
                $class3 = "总单";
                $class31 = "总双";
            } else {
                $class31 = "总单";
                $class3 = "总双";
            }

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->update(["bm" => 1]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("bm", "!=", 0)
                ->where("class3", $class31)
                ->update(["bm" => 0]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin22 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin22 = array("class2" => $class2, "count" => 0);
            }

            $class2 = "正码";

            if ($sum_number <= 174) {
                $class3 = "总小";
                $class31 = "总大";
            } else {
                $class31 = "总小";
                $class3 = "总大";
            }

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->update(["bm" => 1]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("bm", "!=", 0)
                ->where("class3", $class31)
                ->update(["bm" => 0]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正码")
                ->where("class3", $class3)
                ->first();

            if (isset($result1)) {
                $zwin23 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin23 = array("class2" => $class2, "count" => 0);
            }

            // 连码
            $class2 = "三全中";
            $zwin24 = array();
            $zwin = 0;

            $result = NewMacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", "三全中")
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);

                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                }
                if ($number5 > 2) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin24 = array("class2" => $class2, "count" => $zwin);

            $class2 = "三中二";
            $zwin25 = array();
            $zwin = 0;

            $result = NewMacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", "三中二")
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                }
                if ($number5 > 2) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else if ($number5 == 2) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1, "rate" => "rate2"]);

                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin25 = array("class2" => $class2, "count" => $zwin);

            $class2 = "二全中";
            $zwin26 = array();
            $zwin = 0;

            $result = NewMacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                }
                if ($number5 > 1) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin26 = array("class2" => $class2, "count" => $zwin);

            $class2 = "二中特";
            $zwin27 = array();
            $zwin = 0;

            $result = NewMacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $number4 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                    if ($numberxz[$i] == $na) {$number4++;}
                }
                if ($number5 > 1) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else if ($number4 == 1 and $number5 == 1) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1, "rate" => "rate2"]);

                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin27 = array("class2" => $class2, "count" => $zwin);

            $class2 = "特串";
            $zwin28 = 0;
            $zwin = 0;

            $result = NewMacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $number4 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                    if ($numberxz[$i] == $na) {$number4++;}
                }

                if ($number4 == 1 and $number5 == 1) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin28 = array("class2" => $class2, "count" => $zwin);

            $class2 = "四中一";
            $zwin29 = array();
            $zwin = 0;

            $result = NewMacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "连码")
                ->where("class2", $class2)
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $n1) {$number5++;}
                    if ($numberxz[$i] == $n2) {$number5++;}
                    if ($numberxz[$i] == $n3) {$number5++;}
                    if ($numberxz[$i] == $n4) {$number5++;}
                    if ($numberxz[$i] == $n5) {$number5++;}
                    if ($numberxz[$i] == $n6) {$number5++;}
                    if ($numberxz[$i] == $na) {$number4++;}
                }

                if ($number5 > 0) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "连码")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "连码")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin29 = array("class2" => $class2, "count" => $zwin);

            //过关
            $class2 = "过关";
            $zwin30 = array();
            $zwin = 0;

            $result = NewMacaoKatan::select(DB::raw('distinct(class3),class1,class2'))
                ->where("kithe", $kithe)
                ->where("class1", "过关")
                ->get();

            foreach ($result as $item) {
                $number5 = 0;
                $number4 = 0;
                $class3 = $item['class3'];
                $class2 = $item['class2'];
                $class33 = explode(",", $class3);
                $class22 = explode(",", $class2);
                $ss1 = count($class33);
                $ss2 = count($class22);
                $result = 0;
                $result2 = 1;
                for ($i = 0; $i < $ss2; $i++) {
                    if ($class22[$i] == "正码1") {$tmtm = $n1;}
                    if ($class22[$i] == "正码2") {$tmtm = $n2;}
                    if ($class22[$i] == "正码3") {$tmtm = $n3;}
                    if ($class22[$i] == "正码4") {$tmtm = $n4;}
                    if ($class22[$i] == "正码5") {$tmtm = $n5;}
                    if ($class22[$i] == "正码6") {$tmtm = $n6;}
                    $result = 0;
                    switch ($class33[$i]) {
                        case "大":
                            if ($tmtm >= 25) {$result = 1;}
                            break;
                        case "小":
                            if ($tmtm < 25) {$result = 1;}
                            break;
                        case "单":
                            if ($tmtm % 2 == 1) {$result = 1;}
                            break;
                        case "双":
                            if ($tmtm % 2 == 0) {$result = 1;}
                            break;
                        case "红波":
                            if (Utils::ka_Color_s($tmtm) == "红波") {$result = 1;}
                            break;
                        case "蓝波":
                            if (Utils::ka_Color_s($tmtm) == "蓝波") {$result = 1;}
                            break;
                        case "绿波":
                            if (Utils::ka_Color_s($tmtm) == "绿波") {$result = 1;}
                            break;
                        default:
                            $result = 0;
                            break;
                    }
                    if ($result == 0) {$result2 = 0;}
                }

                if ($result2 == 1) {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "过关")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 1]);

                } else {

                    NewMacaoKatan::where("kithe", $kithe)
                        ->where("class1", "过关")
                        ->where("class2", $class2)
                        ->where("class3", $class3)
                        ->update(["bm" => 0]);

                }

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "过关")
                    ->where("class2", $class2)
                    ->where("class3", $class3)
                    ->first();

                if (isset($result1)) {
                    $zwin += $result1["re"];
                }
            }

            $zwin30 = array("class2" => $class2, "count" => $zwin);

            //结算半波
            $class2 = "半波";
            $class3 = Utils::ka_Color_s($na);
            if ($class3 == "红波") {
                if ($na >= 25) {$class31 = "红大";} else { $class31 = "红小";}
                if ($na % 2 == 1) {$class32 = "红单";} else { $class32 = "红双";}
                if (($na % 10 + intval($na / 10)) % 2 == 1) {$class33 = "红合单";} else { $class33 = "红合双";}
            }

            if ($class3 == "绿波") {
                if ($na >= 25) {$class31 = "绿大";} else { $class31 = "绿小";}
                if ($na % 2 == 1) {$class32 = "绿单";} else { $class32 = "绿双";}
                if (($na % 10 + intval($na / 10)) % 2 == 1) {$class33 = "绿合单";} else { $class33 = "绿合双";}
            }
            if ($class3 == "蓝波") {
                if ($na >= 25) {$class31 = "蓝大";} else { $class31 = "蓝小";}
                if ($na % 2 == 1) {$class32 = "蓝单";} else { $class32 = "蓝双";}
                if (($na % 10 + intval($na / 10)) % 2 == 1) {$class33 = "蓝合单";} else { $class33 = "蓝合双";}
            }

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "半波")
                ->where("bm", "!=", 0)
                ->where("class2", $class2)
                ->update(["bm" => 0]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31, $class32, $class33) {
                    $query->where('class3', $class31)
                        ->orWhere('class3', $class32)
                        ->orWhere('class3', $class33);
                })
                ->update(["bm" => 1]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31, $class32, $class33) {
                    $query->where('class3', $class31)
                        ->orWhere('class3', $class32)
                        ->orWhere('class3', $class33);
                })
                ->first();

            if (isset($result1)) {
                $zwin31 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin31 = array("class2" => $class2, "count" => 0);
            }

            //结算半半波
            $class2 = "半半波";
            $class3 = Utils::ka_Color_s($na);
            if ($class3 == "红波") {
                if ($na >= 25) {
                    if ($na % 2 == 1) {
                        $class31 = "红大单";
                    } else {
                        $class31 = "红大双";
                    }

                } else {
                    if ($na % 2 == 1) {
                        $class31 = "红小单";
                    } else {
                        $class31 = "红小双";
                    }

                }
            }
            if ($class3 == "绿波") {
                if ($na >= 25) {
                    if ($na % 2 == 1) {
                        $class31 = "绿大单";
                    } else {
                        $class31 = "绿大双";
                    }

                } else {
                    if ($na % 2 == 1) {
                        $class31 = "绿小单";
                    } else {
                        $class31 = "绿小双";
                    }

                }
            }
            if ($class3 == "蓝波") {
                if ($na >= 25) {
                    if ($na % 2 == 1) {
                        $class31 = "蓝大单";
                    } else {
                        $class31 = "蓝大双";
                    }

                } else {
                    if ($na % 2 == 1) {
                        $class31 = "蓝小单";
                    } else {
                        $class31 = "蓝小双";
                    }

                }
            }

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "半半波")
                ->where("bm", "!=", 0)
                ->where("class2", $class2)
                ->update(["bm" => 0]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "半半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31) {
                    $query->where('class3', $class31);
                })
                ->update(["bm" => 1]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "半半波")
                ->where("class2", $class2)
                ->where(function ($query) use ($class31) {
                    $query->where('class3', $class31);
                })
                ->first();

            if (isset($result1)) {
                $zwin32 = array("class2" => $class2, "count" => $result1["re"]);
            } else {
                $zwin32 = array("class2" => $class2, "count" => 0);
            }

            //结算生肖
            if ($na < 10) {$naa = "0" . $na;} else { $naa = $na;}
            $sxsx = Utils::Get_sx_Color($naa);

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where("class2", "特肖")
                ->where('class3', "!=", $sxsx)
                ->where("bm", "!=", 0)
                ->update(["bm" => 0]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where("class2", "特肖")
                ->where('class3', $sxsx)
                ->update(["bm" => 1]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where("class2", "特肖")
                ->where('class3', $sxsx)
                ->first();

            if (isset($result1)) {
                $zwin33 = $result1["re"];
            } else {
                $zwin33 = 0;
            }

            //结算合肖
            if ($na < 10) {$naa = "0" . $na;} else { $naa = $na;}
            $sxsx = Utils::Get_sx_Color($naa);

            if ($tmtm == 49) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->update(["bm" => 2]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->first();

                if (isset($result1)) {
                    $zwin34 = $result1["re"];
                } else {
                    $zwin34 = 0;
                }
            } else {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->where("bm", "!=", 0)
                    ->update(["bm" => 0]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->where("class3", "like", "%$sxsx%")
                    ->update(["bm" => 1]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "生肖")
                    ->where(function ($query) {
                        $query->where('class2', '二肖')
                            ->orWhere('class2', '三肖')
                            ->orWhere('class2', '四肖')
                            ->orWhere('class2', '五肖')
                            ->orWhere('class2', '六肖')
                            ->orWhere('class2', '七肖')
                            ->orWhere('class2', '八肖')
                            ->orWhere('class2', '九肖')
                            ->orWhere('class2', '十肖')
                            ->orWhere('class2', '十一肖');
                    })
                    ->where("class3", "like", "%$sxsx%")
                    ->first();

                if (isset($result1)) {
                    $zwin34 = $result1["re"];
                } else {
                    $zwin34 = 0;
                }
            }

            //结算平肖

            if ($na < 10) {
                $naa = "0" . $na;
                $sxsx0 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $na;
                $sxsx0 = Utils::Get_sx_Color($naa);
            }

            if ($n1 < 10) {
                $naa = "0" . $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            }
            if ($n2 < 10) {
                $naa = "0" . $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            }
            if ($n3 < 10) {
                $naa = "0" . $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            }
            if ($n4 < 10) {
                $naa = "0" . $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            }
            if ($n5 < 10) {
                $naa = "0" . $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            }
            if ($n6 < 10) {
                $naa = "0" . $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            }

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where('class2', '一肖')
                ->where("bm", "!=", 0)
                ->update(["bm" => 0]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖")
                ->where('class2', '一肖')
                ->where(function ($query) use ($sxsx0, $sxsx1, $sxsx2, $sxsx3, $sxsx4, $sxsx5, $sxsx6) {
                    $query->where('class3', $sxsx0)
                        ->orWhere('class3', $sxsx1)
                        ->orWhere('class3', $sxsx2)
                        ->orWhere('class3', $sxsx3)
                        ->orWhere('class3', $sxsx4)
                        ->orWhere('class3', $sxsx5)
                        ->orWhere('class3', $sxsx6);
                })
                ->update(["bm" => 1]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where('class2', '一肖')
                ->where(function ($query) use ($sxsx0, $sxsx1, $sxsx2, $sxsx3, $sxsx4, $sxsx5, $sxsx6) {
                    $query->where('class3', $sxsx0)
                        ->orWhere('class3', $sxsx1)
                        ->orWhere('class3', $sxsx2)
                        ->orWhere('class3', $sxsx3)
                        ->orWhere('class3', $sxsx4)
                        ->orWhere('class3', $sxsx5)
                        ->orWhere('class3', $sxsx6);
                })
                ->first();

            if (isset($result1)) {
                $zwin35 = $result1["re"];
            } else {
                $zwin35 = 0;
            }

            //结算正肖

            if ($n1 < 10) {
                $naa = "0" . $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            } else { $naa = $n1;
                $sxsx1 = Utils::Get_sx_Color($naa);
            }
            if ($n2 < 10) {
                $naa = "0" . $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n2;
                $sxsx2 = Utils::Get_sx_Color($naa);
            }
            if ($n3 < 10) {
                $naa = "0" . $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n3;
                $sxsx3 = Utils::Get_sx_Color($naa);
            }
            if ($n4 < 10) {
                $naa = "0" . $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n4;
                $sxsx4 = Utils::Get_sx_Color($naa);
            }
            if ($n5 < 10) {
                $naa = "0" . $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n5;
                $sxsx5 = Utils::Get_sx_Color($naa);
            }
            if ($n6 < 10) {
                $naa = "0" . $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            } else {
                $naa = $n6;
                $sxsx6 = Utils::Get_sx_Color($naa);
            }

            $sss = array();

            $sss["鼠"] = 0;
            $sss["虎"] = 0;
            $sss["龙"] = 0;
            $sss["马"] = 0;
            $sss["猴"] = 0;
            $sss["狗"] = 0;
            $sss["牛"] = 0;
            $sss["兔"] = 0;
            $sss["蛇"] = 0;
            $sss["羊"] = 0;
            $sss["鸡"] = 0;
            $sss["猪"] = 0;

            if ($sxsx1 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx1 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx1 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx1 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx1 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx1 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx1 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx1 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx1 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx1 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx1 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx1 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx2 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx2 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx2 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx2 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx2 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx2 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx2 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx2 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx2 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx2 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx2 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx2 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx3 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx3 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx3 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx3 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx3 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx3 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx3 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx3 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx3 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx3 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx3 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx3 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx4 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx4 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx4 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx4 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx4 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx4 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx4 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx4 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx4 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx4 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx4 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx4 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx5 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx5 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx5 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx5 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx5 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx5 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx5 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx5 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx5 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx5 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx5 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx5 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            if ($sxsx6 == "鼠") {$sss["鼠"] = $sss["鼠"] + 1;}
            if ($sxsx6 == "虎") {$sss["虎"] = $sss["虎"] + 1;}
            if ($sxsx6 == "龙") {$sss["龙"] = $sss["龙"] + 1;}
            if ($sxsx6 == "马") {$sss["马"] = $sss["马"] + 1;}
            if ($sxsx6 == "猴") {$sss["猴"] = $sss["猴"] + 1;}
            if ($sxsx6 == "狗") {$sss["狗"] = $sss["狗"] + 1;}
            if ($sxsx6 == "牛") {$sss["牛"] = $sss["牛"] + 1;}
            if ($sxsx6 == "兔") {$sss["兔"] = $sss["兔"] + 1;}
            if ($sxsx6 == "蛇") {$sss["蛇"] = $sss["蛇"] + 1;}
            if ($sxsx6 == "羊") {$sss["羊"] = $sss["羊"] + 1;}
            if ($sxsx6 == "鸡") {$sss["鸡"] = $sss["鸡"] + 1;}
            if ($sxsx6 == "猪") {$sss["猪"] = $sss["猪"] + 1;}

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "正肖")
                ->where('class2', '正肖')
                ->where("bm", 0)
                ->update(["rate2" => "rate"]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "正肖")
                ->where('class2', '正肖')
                ->where("bm", "!=", 0)
                ->update(["bm" => 0]);

            if ($sss["鼠"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '鼠')
                    ->update(["bm" => 1]);

            }

            if ($sss["虎"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '虎')
                    ->update(["bm" => 1]);

            }

            if ($sss["龙"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '龙')
                    ->update(["bm" => 1]);

            }

            if ($sss["马"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '马')
                    ->update(["bm" => 1]);

            }

            if ($sss["猴"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '猴')
                    ->update(["bm" => 1]);

            }
            if ($sss["狗"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '狗')
                    ->update(["bm" => 1]);

            }
            if ($sss["牛"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '牛')
                    ->update(["bm" => 1]);

            }
            if ($sss["兔"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '兔')
                    ->update(["bm" => 1]);

            }
            if ($sss["蛇"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '兔')
                    ->update(["bm" => 1]);

            }
            if ($sss["羊"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '羊')
                    ->update(["bm" => 1]);

            }
            if ($sss["鸡"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '鸡')
                    ->update(["bm" => 1]);

            }
            if ($sss["猪"] > 0) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '猪')
                    ->update(["bm" => 1]);

            }

            if ($sss["牛"] == 1 && ($n1 == "49" || $n2 == "49" || $n3 == "49" || $n4 == "49" || $n5 == "49" || $n6 == "49")) {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "正肖")
                    ->where('class2', '正肖')
                    ->where('class3', '牛')
                    ->update(["bm" => 1]);
            }

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where('class2', '一肖')
                ->where(function ($query) use ($sss) {
                    if ($sss["鼠"] > 0) {
                        $query->orWhere("class3", "鼠");
                    }
                    if ($sss["虎"] > 0) {
                        $query->orWhere("class3", "虎");
                    }
                    if ($sss["龙"] > 0) {
                        $query->orWhere("class3", "龙");
                    }
                    if ($sss["马"] > 0) {
                        $query->orWhere("class3", "马");
                    }
                    if ($sss["猴"] > 0) {
                        $query->orWhere("class3", "猴");
                    }
                    if ($sss["狗"] > 0) {
                        $query->orWhere("class3", "狗");
                    }
                    if ($sss["牛"] > 0) {
                        $query->orWhere("class3", "牛");
                    }
                    if ($sss["兔"] > 0) {
                        $query->orWhere("class3", "兔");
                    }
                    if ($sss["蛇"] > 0) {
                        $query->orWhere("class3", "蛇");
                    }
                    if ($sss["羊"] > 0) {
                        $query->orWhere("class3", "羊");
                    }
                    if ($sss["鸡"] > 0) {
                        $query->orWhere("class3", "鸡");
                    }
                    if ($sss["猪"] > 0) {
                        $query->orWhere("class3", "猪");
                    }
                })
                ->first();

            if (isset($result1)) {
                $zwin36 = $result1["re"];
            } else {
                $zwin36 = 0;
            }

            //结算头数
            $wsws0 = floor($na / 10);

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "头数")
                ->where('class2', '头数')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "头数")
                ->where('class2', '头数')
                ->where('class3', $wsws0)
                ->update(["bm" => 1]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "头数")
                ->where('class2', '头数')
                ->where('class3', $wsws0)
                ->first();

            if (isset($result1)) {
                $zwin37 = $result1["re"];
            } else {
                $zwin37 = 0;
            }

            //结算尾数
            $wsws0 = $na % 10;

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "尾数")
                ->where('class2', '尾数')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "尾数")
                ->where('class2', '尾数')
                ->where('class3', $wsws0)
                ->update(["bm" => 1]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "尾数")
                ->where('class2', '尾数')
                ->where('class3', $wsws0)
                ->first();

            if (isset($result1)) {
                $zwin38 = $result1["re"];
            } else {
                $zwin38 = 0;
            }

            //结算正特尾数
            $wsws0 = $na % 10;
            $wsws1 = $n1 % 10;
            $wsws2 = $n2 % 10;
            $wsws3 = $n3 % 10;
            $wsws4 = $n4 % 10;
            $wsws5 = $n5 % 10;
            $wsws6 = $n6 % 10;

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "正特尾数")
                ->where('class2', '正特尾数')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "正特尾数")
                ->where('class2', '正特尾数')
                ->where(function ($query) use ($wsws0, $wsws1, $wsws2, $wsws3, $wsws4, $wsws5, $wsws6) {
                    $query->where('class3', $wsws0)
                        ->orWhere('class3', $wsws1)
                        ->orWhere('class3', $wsws2)
                        ->orWhere('class3', $wsws3)
                        ->orWhere('class3', $wsws4)
                        ->orWhere('class3', $wsws5)
                        ->orWhere('class3', $wsws6);
                })
                ->update(["bm" => 1]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "正特尾数")
                ->where('class2', '正特尾数')
                ->where(function ($query) use ($wsws0, $wsws1, $wsws2, $wsws3, $wsws4, $wsws5, $wsws6) {
                    $query->where('class3', $wsws0)
                        ->orWhere('class3', $wsws1)
                        ->orWhere('class3', $wsws2)
                        ->orWhere('class3', $wsws3)
                        ->orWhere('class3', $wsws4)
                        ->orWhere('class3', $wsws5)
                        ->orWhere('class3', $wsws6);
                })
                ->first();

            if (isset($result1)) {
                $zwin39 = $result1["re"];
            } else {
                $zwin39 = 0;
            }

            //结算七色波
            $wsws0 = Utils::ka_Color_s($na);
            $wsws1 = Utils::ka_Color_s($n1);
            $wsws2 = Utils::ka_Color_s($n2);
            $wsws3 = Utils::ka_Color_s($n3);
            $wsws4 = Utils::ka_Color_s($n4);
            $wsws5 = Utils::ka_Color_s($n5);
            $wsws6 = Utils::ka_Color_s($n6);

            $hongbo_na = 0;
            $lvbo_na = 0;
            $lanbo_na = 0;
            $hongbo = 0;
            $lvbo = 0;
            $lanbo = 0;
            $hongbo_z = 0;
            $lvbo_z = 0;
            $lanbo_z = 0;
            if ($wsws0 == "红波") {
                $hongbo_na = $hongbo_na + 1.5;
            }

            if ($wsws0 == "绿波") {
                $lvbo_na = $lvbo_na + 1.5;
            }

            if ($wsws0 == "蓝波") {
                $lanbo_na = $lanbo_na + 1.5;
            }

            if ($wsws1 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws1 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws1 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws2 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws2 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws2 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws3 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws3 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws3 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws4 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws4 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws4 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws5 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws5 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws5 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            if ($wsws6 == "红波") {
                $hongbo = $hongbo + 1;
            }

            if ($wsws6 == "绿波") {
                $lvbo = $lvbo + 1;
            }

            if ($wsws6 == "蓝波") {
                $lanbo = $lanbo + 1;
            }

            $hongbo_z = $hongbo_na + $hongbo;
            $lvbo_z = $lvbo_na + $lvbo;
            $lanbo_z = $lanbo_na + $lanbo;

            if ($hongbo_z > $lvbo_z && $hongbo_z > $lanbo_z) {
                $qsbgo = "红波";
            }

            if ($lvbo_z > $hongbo_z && $lvbo_z > $lanbo_z) {
                $qsbgo = "绿波";
            }

            if ($lanbo_z > $hongbo_z && $lanbo_z > $lvbo_z) {
                $qsbgo = "蓝波";
            }

            if ($hongbo_z == 3 && $lvbo_z == 3 && $wsws0 == "蓝波") {
                $qsbgo = "合局";
            }

            if ($lvbo_z == 3 && $lanbo_z == 3 && $wsws0 == "红波") {
                $qsbgo = "合局";
            }

            if ($hongbo_z == 3 && $lanbo_z == 3 && $wsws0 == "绿波") {
                $qsbgo = "合局";
            }

            if ($qsbgo == "合局") {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('bm', "!=", 0)
                    ->update(["bm" => 0]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', "!=", "合局")
                    ->update(["bm" => 2]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', "合局")
                    ->update(["bm" => 1]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', "合局")
                    ->first();

                if (isset($result1)) {
                    $zwin40 = $result1["re"];
                } else {
                    $zwin40 = 0;
                }

            } else {

                NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('bm', "!=", 0)
                    ->update(["bm" => 0]);

                    NewMacaoKatan::where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', $qsbgo)
                    ->update(["bm" => 1]);

                $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                    ->where("kithe", $kithe)
                    ->where("class1", "七色波")
                    ->where('class2', '七色波')
                    ->where('class3', $qsbgo)
                    ->first();

                if (isset($result1)) {
                    $zwin40 = $result1["re"];
                } else {
                    $zwin40 = 0;
                }
            }

            //结算五行
            if ($na < 10) {
                $wxwx = "0" . $na;
            } else {
                $wxwx = $na;
            }
            $wxwxwx = Utils::Get_wxwx_Color($wxwx);

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "五行")
                ->where('class2', '五行')
                ->where('bm', "!=", 0)
                ->update(["bm" => 0]);

                NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "五行")
                ->where('class2', '五行')
                ->where('class3', $wxwxwx)
                ->update(["bm" => 1]);

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "五行")
                ->where('class2', '五行')
                ->where('class3', $wxwxwx)
                ->first();

            if (isset($result1)) {
                $zwin41 = $result1["re"];
            } else {
                $zwin41 = 0;
            }

            //全不中

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "全不中")
                ->update(["bm" => 1]);

            $result1kk = NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "全不中")
                ->get(["class3"]);

            foreach ($result1kk as $item) {
                $class3 = $item["class3"];
                $numberxz = explode(",", $class3);
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($numberxz[$i] == $na || $numberxz[$i] == $n1 || $numberxz[$i] == $n2 || $numberxz[$i] == $n3 || $numberxz[$i] == $n4 || $numberxz[$i] == $n5 || $numberxz[$i] == $n6) {

                        NewMacaoKatan::where("kithe", $kithe)
                            ->where("class1", "全不中")
                            ->where("class3", "like", "%$class3%")
                            ->update(["bm" => 0]);
                    }
                }

            }

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "全不中")
                ->where('bm', 1)
                ->first();

            if (isset($result1)) {
                $zwin42 = $result1["re"];
            } else {
                $zwin42 = 0;
            }

            //生肖连
            if (intval($n1) < 10) {
                $n1 = "0" . $n1;
            }

            if (intval($n2) < 10) {
                $n2 = "0" . $n2;
            }

            if (intval($n3) < 10) {
                $n3 = "0" . $n3;
            }

            if (intval($n4) < 10) {
                $n4 = "0" . $n4;
            }

            if (intval($n5) < 10) {
                $n5 = "0" . $n5;
            }

            if (intval($n6) < 10) {
                $n6 = "0" . $n6;
            }

            if (intval($na) < 10) {
                $na = "0" . $na;
            }

            $lx_sx1 = Utils::Get_sx_Color($n1);
            $lx_sx2 = Utils::Get_sx_Color($n2);
            $lx_sx3 = Utils::Get_sx_Color($n3);
            $lx_sx4 = Utils::Get_sx_Color($n4);
            $lx_sx5 = Utils::Get_sx_Color($n5);
            $lx_sx6 = Utils::Get_sx_Color($n6);
            $lx_sx7 = Utils::Get_sx_Color($na);

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖连")
                ->update(["bm" => 0]);

            $result = NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "生肖连")
                ->get(["id", "class2", "class3"]);

            foreach ($result as $item) {
                $Rs_id = $item['id'];
                $class2 = $item['class2'];
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $cont = 0;
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if ($lx_sx1 == $numberxz[$i] || $lx_sx2 == $numberxz[$i] || $lx_sx3 == $numberxz[$i] || $lx_sx4 == $numberxz[$i] || $lx_sx5 == $numberxz[$i] || $lx_sx6 == $numberxz[$i] || $lx_sx7 == $numberxz[$i]) {
                        $cont += 1;
                        continue;
                    }
                }
                if ($cont == $ss1 && ($class2 == "二肖连中" || $class2 == "三肖连中" || $class2 == "四肖连中" || $class2 == "五肖连中")) {

                    NewMacaoKatan::where("id", $Rs_id)
                        ->update(["bm" => 1]);

                }

                if ($cont == 0 && ($class2 == "二肖连不中" || $class2 == "三肖连不中" || $class2 == "四肖连不中")) {

                    NewMacaoKatan::where("id", $Rs_id)
                        ->update(["bm" => 1]);
                }

            }

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "生肖连")
                ->where('bm', 1)
                ->first();

            if (isset($result1)) {
                $zwin43 = $result1["re"];
            } else {
                $zwin43 = 0;
            }

            NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "尾数连")
                ->update(["bm" => 0]);

            $result = NewMacaoKatan::where("kithe", $kithe)
                ->where("class1", "尾数连")
                ->get(["id", "class2", "class3"]);

            foreach ($result as $item) {
                $Rs_id = $item['id'];
                $class2 = $item['class2'];
                $class3 = $item['class3'];
                $numberxz = explode(",", $class3);
                $cont = 0;
                $ss1 = count($numberxz);
                for ($i = 0; $i < $ss1; $i++) {
                    if (substr($n1, -1) == $numberxz[$i] || substr($n2, -1) == $numberxz[$i] || substr($n3, -1) == $numberxz[$i] || substr($n4, -1) == $numberxz[$i] || substr($n5, -1) == $numberxz[$i] || substr($n6, -1) == $numberxz[$i] || substr($na, -1) == $numberxz[$i]) {
                        $cont += 1;
                        continue;
                    }
                }
                if ($cont == $ss1 && ($class2 == "二尾连中" || $class2 == "三尾连中" || $class2 == "四尾连中")) {

                    NewMacaoKatan::where("id", $Rs_id)
                        ->update(["bm" => 1]);
                }
                if ($cont == 0 && ($class2 == "二尾连不中" || $class2 == "三尾连不中" || $class2 == "四尾连不中")) {

                    NewMacaoKatan::where("id", $Rs_id)
                        ->update(["bm" => 1]);
                }

            }

            $result1 = NewMacaoKatan::select(DB::raw('sum(sum_m) as sum_m, count(*) as re'))
                ->where("kithe", $kithe)
                ->where("class1", "尾数连")
                ->where('bm', 1)
                ->first();

            if (isset($result1)) {
                $zwin44 = $result1["re"];
            } else {
                $zwin44 = 0;
            }

            $result = NewMacaoKatan::where("kithe", $kithe)->where("checked", 0)->get();

            $loginname = $request->user()->UserName;

            foreach ($result as $item) {
                $id = $item['id'];
                $username = $item['username'];
                if ($item['bm'] == 1) { //会员中奖
                    $gold = $item['sum_m'] * $item['rate'] + $item['sum_m'] * abs($item['user_ds']) / 100;
                    $status = '中奖';
                } elseif ($item['bm'] == 2) { //和局 不退水
                    $gold = $item['sum_m'];
                    $status = '和局';
                } else { //未中奖退水
                    $gold = $item['sum_m'] * abs($item['user_ds']) / 100;
                    $status = '未中奖，反水';
                }

                NewMacaoKatan::where("id", $id)->update(["checked" => 1]);

                if ($gold !== 0) {
                    $previousAmount = Utils::GetField($username, 'Money');

                    $q1 = User::where("UserName", $username)->increment('Money', $gold);

                    if ($q1 == 1) {

                        $currentAmount = Utils::GetField($username, 'Money');

                        $user_id = Utils::GetField($username, 'id');

                        $datetime = date("Y-m-d H:i:s");

                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $item["num"];
                        $new_log->about = $loginname . "结复六合注单";
                        $new_log->update_time = $datetime;
                        $new_log->type = '投注金额:' . $item['sum_m'] . '&nbsp;赔率:' . $item['rate'] . '&nbsp;' . $item['class1'] . '&nbsp;' . $item['class2'] . '&nbsp;' . $item['class3'];
                        $new_log->order_value = $gold;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        $new_log->save();

                    } else {

                        $ka_tan = NewMacaoKatan::find($id);

                        $ka_tan->checked = 0;
                        $ka_tan->bm = 0;

                        $ka_tan->save();

                    }

                }

            }

            $data = array(
                "zwin1" => $zwin1,
                "zwin2" => $zwin2,
                "zwin3" => $zwin3,
                "zwin4" => $zwin4,
                "zwin5" => $zwin5,
                "zwin6" => $zwin6,
                "zwin7" => $zwin7,
                "zwin8" => $zwin8,
                "zwin9" => $zwin9,
                "zwin10" => $zwin10,
                "zwin11" => $zwin11,
                "zwin12" => $zwin12,
                "zwin13" => $zwin13,
                "zwin14" => $zwin14,
                "zwin15" => $zwin15,
                "zwin16" => $zwin16,
                "zwin17" => $zwin17,
                "zwin18" => $zwin18,
                "zwin19" => $zwin19,
                "zwin20" => $zwin20,
                "zwin21" => $zwin21,
                "zwin22" => $zwin22,
                "zwin23" => $zwin23,
                "zwin24" => $zwin24,
                "zwin25" => $zwin25,
                "zwin26" => $zwin26,
                "zwin27" => $zwin27,
                "zwin28" => $zwin28,
                "zwin29" => $zwin29,
                "zwin30" => $zwin30,
                "zwin31" => $zwin31,
                "zwin32" => $zwin32,
                "zwin33" => $zwin33,
                "zwin34" => $zwin34,
                "zwin35" => $zwin35,
                "zwin36" => $zwin36,
                "zwin37" => $zwin37,
                "zwin38" => $zwin38,
                "zwin39" => $zwin39,
                "zwin40" => $zwin40,
                "zwin41" => $zwin41,
                "zwin42" => $zwin42,
                "zwin43" => $zwin43,
                "zwin44" => $zwin44,
            );

            NewMacaoKakithe::where("nn", $kithe)->update(["score" => 1]);

            $response["data"] = $data;
            $response['message'] = "New MacaoKakithe Bet calculated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function editMacaoKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
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
            $nn = $request_data["nn"];
            $nd = $request_data["nd"];
            $na = $request_data["na"];
            $sx = $request_data["sx"];
            $n1 = $request_data["n1"];
            $n2 = $request_data["n2"];
            $n3 = $request_data["n3"];
            $n4 = $request_data["n4"];
            $n5 = $request_data["n5"];
            $n6 = $request_data["n6"];

            $ka_kithe = MacaoKakithe::find($id);

            $ka_kithe->nn = $nn;
            $ka_kithe->nd = $nd;
            $ka_kithe->na = $na;
            $ka_kithe->sx = $sx;
            $ka_kithe->n1 = $n1;
            $ka_kithe->n2 = $n2;
            $ka_kithe->n3 = $n3;
            $ka_kithe->n4 = $n4;
            $ka_kithe->n5 = $n5;
            $ka_kithe->n6 = $n6;

            $ka_kithe->save();

            $response['message'] = "Macao Kakithe edited successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function editNewMacaoKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
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
            $nn = $request_data["nn"];
            $nd = $request_data["nd"];
            $na = $request_data["na"];
            $sx = $request_data["sx"];
            $n1 = $request_data["n1"];
            $n2 = $request_data["n2"];
            $n3 = $request_data["n3"];
            $n4 = $request_data["n4"];
            $n5 = $request_data["n5"];
            $n6 = $request_data["n6"];

            $ka_kithe = NewMacaoKakithe::find($id);

            $ka_kithe->nn = $nn;
            $ka_kithe->nd = $nd;
            $ka_kithe->na = $na;
            $ka_kithe->sx = $sx;
            $ka_kithe->n1 = $n1;
            $ka_kithe->n2 = $n2;
            $ka_kithe->n3 = $n3;
            $ka_kithe->n4 = $n4;
            $ka_kithe->n5 = $n5;
            $ka_kithe->n6 = $n6;

            $ka_kithe->save();

            $response['message'] = "New Macao Kakithe edited successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function restoreMacaoKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];

            $loginname = $request->user()->UserName;

            $ka_kithe = MacaoKakithe::find($id);

            $nn = $ka_kithe["nn"];

            $ka_tans = MacaoKatan::where("kithe", $nn)->orderBy("id", "asc")->get();

            foreach ($ka_tans as $item) {
                $id = $item["id"];
                $username = $item["username"];
                $bm = $item["bm"];
                $sum_m = $item["sum_m"];
                $rate = $item["rate"];
                $user_ds = $item["user_ds"];

                if ($item["checked"] == 1) {
                    switch ($bm) {
                        case 1:
                            $gold = $sum_m + $user_ds;
                            break;
                        case 2:
                            $gold = $sum_m;
                            break;
                        case 0:
                            $gold = $user_ds;
                            break;
                    }

                    $ka_tan = MacaoKatan::find($id);

                    $ka_tan->checked = 0;
                    $ka_tan->bm = 0;

                    $ka_tan->save();

                    if ($gold !== 0) {
                        $previousAmount = Utils::GetField($username, 'Money');

                        $q1 = User::where("UserName", $username)->decrement('Money', $gold);

                        $q1 = User::where("UserName", $username)->decrement('withdrawal_condition', $gold);

                        if ($q1 == 1) {

                            $currentAmount = Utils::GetField($username, 'Money');

                            $user_id = Utils::GetField($username, 'id');

                            $datetime = date("Y-m-d H:i:s");

                            $new_log = new MoneyLog;
                            $new_log->user_id = $user_id;
                            $new_log->order_num = $item["num"];
                            $new_log->about = $loginname . "恢复六合注单";
                            $new_log->update_time = $datetime;
                            $new_log->type = '投注金额:' . $item['sum_m'] . '&nbsp;赔率:' . $item['rate'] . '&nbsp;' . $item['class1'] . '&nbsp;' . $item['class2'] . '&nbsp;' . $item['class3'];
                            $new_log->order_value = $gold;
                            $new_log->assets = $previousAmount;
                            $new_log->balance = $currentAmount;
                            $new_log->save();

                        } else {

                            $ka_tan = MacaoKatan::find($id);

                            $ka_tan->checked = 1;
                            $ka_tan->bm = $bm;

                            $ka_tan->save();

                        }

                    }
                }
            }

            $ka_kithe->score = 0;

            $ka_kithe->save();

            $response['message'] = "MacaoKakithe recovered successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function restoreNewMacaoKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];

            $loginname = $request->user()->UserName;

            $ka_kithe = NewMacaoKakithe::find($id);

            $nn = $ka_kithe["nn"];

            $ka_tans = NewMacaoKatan::where("kithe", $nn)->orderBy("id", "asc")->get();

            foreach ($ka_tans as $item) {
                $id = $item["id"];
                $username = $item["username"];
                $bm = $item["bm"];
                $sum_m = $item["sum_m"];
                $rate = $item["rate"];
                $user_ds = $item["user_ds"];

                if ($item["checked"] == 1) {
                    switch ($bm) {
                        case 1:
                            $gold = $sum_m + $user_ds;
                            break;
                        case 2:
                            $gold = $sum_m;
                            break;
                        case 0:
                            $gold = $user_ds;
                            break;
                    }

                    $ka_tan = NewMacaoKatan::find($id);

                    $ka_tan->checked = 0;
                    $ka_tan->bm = 0;

                    $ka_tan->save();

                    if ($gold !== 0) {
                        $previousAmount = Utils::GetField($username, 'Money');

                        $q1 = User::where("UserName", $username)->decrement('Money', $gold);

                        $q1 = User::where("UserName", $username)->decrement('withdrawal_condition', $gold);

                        if ($q1 == 1) {

                            $currentAmount = Utils::GetField($username, 'Money');

                            $user_id = Utils::GetField($username, 'id');

                            $datetime = date("Y-m-d H:i:s");

                            $new_log = new MoneyLog;
                            $new_log->user_id = $user_id;
                            $new_log->order_num = $item["num"];
                            $new_log->about = $loginname . "恢复六合注单";
                            $new_log->update_time = $datetime;
                            $new_log->type = '投注金额:' . $item['sum_m'] . '&nbsp;赔率:' . $item['rate'] . '&nbsp;' . $item['class1'] . '&nbsp;' . $item['class2'] . '&nbsp;' . $item['class3'];
                            $new_log->order_value = $gold;
                            $new_log->assets = $previousAmount;
                            $new_log->balance = $currentAmount;
                            $new_log->save();

                        } else {

                            $ka_tan = NewMacaoKatan::find($id);

                            $ka_tan->checked = 1;
                            $ka_tan->bm = $bm;

                            $ka_tan->save();

                        }

                    }
                }
            }

            $ka_kithe->score = 0;

            $ka_kithe->save();

            $response['message'] = "New MacaoKakithe recovered successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteMacaoKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];

            $ids = explode(",", $id);

            foreach ($ids as $item_id) {

                MacaoKakithe::destroy($item_id);

            }

            $response['message'] = "MacaoKakithe Item deleted successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteNewMacaoKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];

            $ids = explode(",", $id);

            foreach ($ids as $item_id) {
                NewMacaoKakithe::destroy($item_id);
            }

            $response['message'] = "New MacaoKakithe Item deleted successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoKakitheStatus(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $ka_kithe = MacaoKakithe::find($id);

            $ka_kithe->lx = $status;

            $ka_kithe->save();

            $response['message'] = "MacaoKakithe Status updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateNewMacaoKakitheStatus(Request $request)
    {

        $response = [];
        $response['success'] = false;
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

            $ka_kithe = NewMacaoKakithe::find($id);

            $ka_kithe->lx = $status;

            $ka_kithe->save();

            $response['message'] = "New MacaoKakithe Status updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoKakitheAll(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "page" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $page = $request_data["page"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $period = $request_data["period"] ?? "";
            $status = $request_data["status"] ?? "";

            $ka_kithe = MacaoKakithe::where("na", "!=", 0);

            if ($status !== "") {
                $ka_kithe = $ka_kithe->where("lx", $status);
            }

            if ($period !== "") {
                $ka_kithe = $ka_kithe->where("nn", "like", "%$period%");
            }

            $total_count = $ka_kithe->count();

            $ka_kithe = $ka_kithe->orderBy("id", "desc")
                ->offset(($page - 1) * $limit)
                ->take($limit)
                ->get();

            foreach ($ka_kithe as $item) {
                $item["checked"] = false;
                $item["lx"] = $item["lx"] == 1 ? true : false;
            }

            $response["total_count"] = $total_count;
            $response['data'] = $ka_kithe;
            $response['message'] = "MacaoKakithe Data fetched successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getNewMacaoKakitheAll(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "page" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $page = $request_data["page"] ?? 1;
            $limit = $request_data["limit"] ?? 20;
            $period = $request_data["period"] ?? "";
            $status = $request_data["status"] ?? "";

            $ka_kithe = NewMacaoKakithe::where("na", "!=", 0);

            if ($status !== "") {
                $ka_kithe = $ka_kithe->where("lx", $status);
            }

            if ($period !== "") {
                $ka_kithe = $ka_kithe->where("nn", "like", "%$period%");
            }

            $total_count = $ka_kithe->count();

            $ka_kithe = $ka_kithe->orderBy("id", "desc")
                ->offset(($page - 1) * $limit)
                ->take($limit)
                ->get();

            foreach ($ka_kithe as $item) {
                $item["checked"] = false;
                $item["lx"] = $item["lx"] == 1 ? true : false;
            }

            $response["total_count"] = $total_count;
            $response['data'] = $ka_kithe;
            $response['message'] = "NewMacaoKakithe Data fetched successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMacaoLotteryStatus(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $ka_kithe = MacaoKakithe::where("na", 0)->first();

            $ka_kithe["best"] = $ka_kithe["best"] == 0 ? true : false;

            $response['data'] = $ka_kithe;
            $response['message'] = "MacaoKakithe Data fetched successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getNewMacaoLotteryStatus(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $ka_kithe = NewMacaoKakithe::where("na", 0)->first();

            $ka_kithe["best"] = $ka_kithe["best"] == 0 ? true : false;

            $response['data'] = $ka_kithe;
            $response['message'] = "NewMacaoKakithe Data fetched successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveMacaoGameResult(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "n1" => "required",
                "n2" => "required",
                "n3" => "required",
                "n4" => "required",
                "n5" => "required",
                "n6" => "required",
                "na" => "required",
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $n1 = $request_data["n1"];
            $n2 = $request_data["n2"];
            $n3 = $request_data["n3"];
            $n4 = $request_data["n4"];
            $n5 = $request_data["n5"];
            $n6 = $request_data["n6"];
            $na = $request_data["na"];

            if ((int) $na !== 0) {
                $fb = (int) $na;

                if ($fb < 10) {
                    $vv = "0" . $fb;
                } else {
                    $vv = $fb;
                }

                $sx = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n1;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x1 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n2;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x2 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n3;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x3 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n4;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x4 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n5;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x5 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n6;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x6 = Utils::Get_sx_Color($vv);

                $ka_kithe = MacaoKakithe::find($id);

                $ka_kithe->n1 = $n1;
                $ka_kithe->n2 = $n2;
                $ka_kithe->n3 = $n3;
                $ka_kithe->n4 = $n4;
                $ka_kithe->n5 = $n5;
                $ka_kithe->n6 = $n6;
                $ka_kithe->na = $na;
                $ka_kithe->x1 = $x1;
                $ka_kithe->x2 = $x2;
                $ka_kithe->x3 = $x3;
                $ka_kithe->x4 = $x4;
                $ka_kithe->x5 = $x5;
                $ka_kithe->x6 = $x6;
                $ka_kithe->sx = $sx;

                $ka_kithe->save();

                MacaoKabl::query()->update(["gold" => 0]);

                $ya_kithe = MacaoYakithe::orderBy("nn", "asc")->first();

                //添加新
                $ykaid = $ya_kithe['id'];
                $nn = $ya_kithe['nn'] + 3;

                $m_count = MacaoKakithe::where("nn", $nn)->count();

                if ($m_count <= 0) {

                    $ka_kithe = new MacaoKakithe();

                    $ka_kithe->best = $ya_kithe["best"];
                    $ka_kithe->nn = $ya_kithe["nn"];
                    $ka_kithe->nd = $ya_kithe["nd"];
                    $ka_kithe->kitm = $ya_kithe["kitm"];
                    $ka_kithe->kizt = $ya_kithe["kizt"];
                    $ka_kithe->kizm = $ya_kithe["kizm"];
                    $ka_kithe->kizm6 = $ya_kithe["kizm6"];
                    $ka_kithe->kigg = $ya_kithe["kigg"];
                    $ka_kithe->kilm = $ya_kithe["kilm"];
                    $ka_kithe->kisx = $ya_kithe["kisx"];
                    $ka_kithe->kibb = $ya_kithe["kibb"];
                    $ka_kithe->kiws = $ya_kithe["kiws"];
                    $ka_kithe->zfbdate = $ya_kithe["zfbdate"];
                    $ka_kithe->kitm1 = $ya_kithe["kitm1"];
                    $ka_kithe->kizt1 = $ya_kithe["kizt1"];
                    $ka_kithe->kizm1 = $ya_kithe["kizm1"];
                    $ka_kithe->kizm61 = $ya_kithe["kizm61"];
                    $ka_kithe->kigg1 = $ya_kithe["kigg1"];
                    $ka_kithe->kilm1 = $ya_kithe["kilm1"];
                    $ka_kithe->kisx1 = $ya_kithe["kisx1"];
                    $ka_kithe->kibb1 = $ya_kithe["kibb1"];
                    $ka_kithe->kiws1 = $ya_kithe["kiws1"];
                    $ka_kithe->zfbdate1 = $ya_kithe["zfbdate1"];
                    $ka_kithe->zfb = $ya_kithe["zfb"];
                    $ka_kithe->n1 = $ya_kithe["n1"];
                    $ka_kithe->n2 = $ya_kithe["n2"];
                    $ka_kithe->n3 = $ya_kithe["n3"];
                    $ka_kithe->n4 = $ya_kithe["n4"];
                    $ka_kithe->n5 = $ya_kithe["n5"];
                    $ka_kithe->n6 = $ya_kithe["n6"];
                    $ka_kithe->na = $ya_kithe["na"];
                    $ka_kithe->lx = $ya_kithe["lx"];

                    $ka_kithe->save();
                }

                MacaoYakithe::where("id", $ykaid)->update(["nn" => $nn]);

            } else {

                $ka_kithe = MacaoKakithe::find($id);

                $ka_kithe->n1 = $n1;
                $ka_kithe->n2 = $n2;
                $ka_kithe->n3 = $n3;
                $ka_kithe->n4 = $n4;
                $ka_kithe->n5 = $n5;
                $ka_kithe->n6 = $n6;
                $ka_kithe->na = $na;

                $ka_kithe->save();

            }

            $response['message'] = "Game Result updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveNewMacaoGameResult(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "n1" => "required",
                "n2" => "required",
                "n3" => "required",
                "n4" => "required",
                "n5" => "required",
                "n6" => "required",
                "na" => "required",
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $n1 = $request_data["n1"];
            $n2 = $request_data["n2"];
            $n3 = $request_data["n3"];
            $n4 = $request_data["n4"];
            $n5 = $request_data["n5"];
            $n6 = $request_data["n6"];
            $na = $request_data["na"];

            if ((int) $na !== 0) {
                $fb = (int) $na;

                if ($fb < 10) {
                    $vv = "0" . $fb;
                } else {
                    $vv = $fb;
                }

                $sx = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n1;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x1 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n2;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x2 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n3;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x3 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n4;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x4 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n5;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x5 = Utils::Get_sx_Color($vv);

                $xb1 = (int) $n6;

                if ($xb1 < 10) {
                    $vv = "0" . $xb1;
                } else {
                    $vv = $xb1;
                }
                $x6 = Utils::Get_sx_Color($vv);

                $ka_kithe = NewMacaoKakithe::find($id);

                $ka_kithe->n1 = $n1;
                $ka_kithe->n2 = $n2;
                $ka_kithe->n3 = $n3;
                $ka_kithe->n4 = $n4;
                $ka_kithe->n5 = $n5;
                $ka_kithe->n6 = $n6;
                $ka_kithe->na = $na;
                $ka_kithe->x1 = $x1;
                $ka_kithe->x2 = $x2;
                $ka_kithe->x3 = $x3;
                $ka_kithe->x4 = $x4;
                $ka_kithe->x5 = $x5;
                $ka_kithe->x6 = $x6;
                $ka_kithe->sx = $sx;

                $ka_kithe->save();

                MacaoKabl::query()->update(["gold" => 0]);

                $ya_kithe = NewMacaoYakithe::orderBy("nn", "asc")->first();

                //添加新
                $ykaid = $ya_kithe['id'];
                $nn = $ya_kithe['nn'] + 3;

                $m_count = NewMacaoKakithe::where("nn", $nn)->count();

                if ($m_count <= 0) {

                    $ka_kithe = new NewMacaoKakithe();

                    $ka_kithe->best = $ya_kithe["best"];
                    $ka_kithe->nn = $ya_kithe["nn"];
                    $ka_kithe->nd = $ya_kithe["nd"];
                    $ka_kithe->kitm = $ya_kithe["kitm"];
                    $ka_kithe->kizt = $ya_kithe["kizt"];
                    $ka_kithe->kizm = $ya_kithe["kizm"];
                    $ka_kithe->kizm6 = $ya_kithe["kizm6"];
                    $ka_kithe->kigg = $ya_kithe["kigg"];
                    $ka_kithe->kilm = $ya_kithe["kilm"];
                    $ka_kithe->kisx = $ya_kithe["kisx"];
                    $ka_kithe->kibb = $ya_kithe["kibb"];
                    $ka_kithe->kiws = $ya_kithe["kiws"];
                    $ka_kithe->zfbdate = $ya_kithe["zfbdate"];
                    $ka_kithe->kitm1 = $ya_kithe["kitm1"];
                    $ka_kithe->kizt1 = $ya_kithe["kizt1"];
                    $ka_kithe->kizm1 = $ya_kithe["kizm1"];
                    $ka_kithe->kizm61 = $ya_kithe["kizm61"];
                    $ka_kithe->kigg1 = $ya_kithe["kigg1"];
                    $ka_kithe->kilm1 = $ya_kithe["kilm1"];
                    $ka_kithe->kisx1 = $ya_kithe["kisx1"];
                    $ka_kithe->kibb1 = $ya_kithe["kibb1"];
                    $ka_kithe->kiws1 = $ya_kithe["kiws1"];
                    $ka_kithe->zfbdate1 = $ya_kithe["zfbdate1"];
                    $ka_kithe->zfb = $ya_kithe["zfb"];
                    $ka_kithe->n1 = $ya_kithe["n1"];
                    $ka_kithe->n2 = $ya_kithe["n2"];
                    $ka_kithe->n3 = $ya_kithe["n3"];
                    $ka_kithe->n4 = $ya_kithe["n4"];
                    $ka_kithe->n5 = $ya_kithe["n5"];
                    $ka_kithe->n6 = $ya_kithe["n6"];
                    $ka_kithe->na = $ya_kithe["na"];
                    $ka_kithe->lx = $ya_kithe["lx"];

                    $ka_kithe->save();
                }

                NewMacaoYakithe::where("id", $ykaid)->update(["nn" => $nn]);

            } else {

                $ka_kithe = NewMacaoKakithe::find($id);

                $ka_kithe->n1 = $n1;
                $ka_kithe->n2 = $n2;
                $ka_kithe->n3 = $n3;
                $ka_kithe->n4 = $n4;
                $ka_kithe->n5 = $n5;
                $ka_kithe->n6 = $n6;
                $ka_kithe->na = $na;

                $ka_kithe->save();

            }

            $response['message'] = "Game Result updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoHandicap(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "zfb" => "required",
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $zfb = $request_data["zfb"];
            $best = $request_data["best"] ?? "";

            if ((int) $zfb == 1) {

                $ka_kithe = MacaoKakithe::find($id);

                $ka_kithe->kitm = 1;
                $ka_kithe->kizt = 1;
                $ka_kithe->kizm = 1;
                $ka_kithe->kizm6 = 1;
                $ka_kithe->kigg = 1;
                $ka_kithe->kilm = 1;
                $ka_kithe->kisx = 1;
                $ka_kithe->kibb = 1;
                $ka_kithe->kiws = 1;
                $ka_kithe->zfb = 1;
                if ($best != "") {
                    $ka_kithe->best = $best;
                }

                $ka_kithe->save();

            } else {

                $ka_kithe = MacaoKakithe::find($id);

                $ka_kithe->kitm = 0;
                $ka_kithe->kizt = 0;
                $ka_kithe->kizm = 0;
                $ka_kithe->kizm6 = 0;
                $ka_kithe->kigg = 0;
                $ka_kithe->kilm = 0;
                $ka_kithe->kisx = 0;
                $ka_kithe->kibb = 0;
                $ka_kithe->kiws = 0;
                $ka_kithe->zfb = 0;

                $ka_kithe->save();

            }

            $response['message'] = "MacaoKakithe Handicap updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateNewMacaoHandicap(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "zfb" => "required",
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $zfb = $request_data["zfb"];
            $best = $request_data["best"] ?? "";

            if ((int) $zfb == 1) {

                $ka_kithe = NewMacaoKakithe::find($id);

                $ka_kithe->kitm = 1;
                $ka_kithe->kizt = 1;
                $ka_kithe->kizm = 1;
                $ka_kithe->kizm6 = 1;
                $ka_kithe->kigg = 1;
                $ka_kithe->kilm = 1;
                $ka_kithe->kisx = 1;
                $ka_kithe->kibb = 1;
                $ka_kithe->kiws = 1;
                $ka_kithe->zfb = 1;
                if ($best != "") {
                    $ka_kithe->best = $best;
                }

                $ka_kithe->save();

            } else {

                $ka_kithe = NewMacaoKakithe::find($id);

                $ka_kithe->kitm = 0;
                $ka_kithe->kizt = 0;
                $ka_kithe->kizm = 0;
                $ka_kithe->kizm6 = 0;
                $ka_kithe->kigg = 0;
                $ka_kithe->kilm = 0;
                $ka_kithe->kisx = 0;
                $ka_kithe->kibb = 0;
                $ka_kithe->kiws = 0;
                $ka_kithe->zfb = 0;

                $ka_kithe->save();

            }

            $response['message'] = "NewMacaoKakithe Handicap updated successfully!";
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoBest(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "best" => "required",
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $best = $request_data["best"];

            $ka_kithe = MacaoKakithe::find($id);

            $ka_kithe->best = $best;

            if ($ka_kithe->save()) {
                $response["data"] = $ka_kithe;
                $response['message'] = "MacaoKakithe Best updated successfully!";
                $response['success'] = true;
                $response['status'] = STATUS_OK;
            }
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateNewMacaoBest(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "best" => "required",
                "id" => "required",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $best = $request_data["best"];

            $ka_kithe = NewMacaoKakithe::find($id);

            $ka_kithe->best = $best;

            if ($ka_kithe->save()) {
                $response["data"] = $ka_kithe;
                $response['message'] = "NewMacaoKakithe Best updated successfully!";
                $response['success'] = true;
                $response['status'] = STATUS_OK;
            }
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateMacaoKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required",
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
            $kizm = $request_data["kizm"];
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

            $ya_kithe = MacaoYakithe::orderBy("nn", "asc")->get();

            $version = 1;

            foreach ($ya_kithe as $item) {
                $nnd1 = $nn + $version;
                $data = MacaoYakithe::find($item["id"]);
                $data->nn = $nnd1;
                $data->save();
                $version++;
            }

            $ka_kithe = MacaoKakithe::find($id);

            $ka_kithe->nn = $nn;
            $ka_kithe->nd = $nd;
            $ka_kithe->kitm = $kitm;
            $ka_kithe->kizt = $kizt;
            $ka_kithe->kizm = $kizm;
            $ka_kithe->kizm6 = $kizm6;
            $ka_kithe->kigg = $kigg;
            $ka_kithe->kilm = $kilm;
            $ka_kithe->kisx = $kisx;
            $ka_kithe->kibb = $kibb;
            $ka_kithe->kiws = $kiws;
            $ka_kithe->zfbdate = $zfbdate;
            $ka_kithe->kitm1 = $kitm1;
            $ka_kithe->kizt1 = $kizt1;
            $ka_kithe->kizm1 = $kizm1;
            $ka_kithe->kizm61 = $kizm61;
            $ka_kithe->kigg1 = $kigg1;
            $ka_kithe->kilm1 = $kilm1;
            $ka_kithe->kisx1 = $kisx1;
            $ka_kithe->kibb1 = $kibb1;
            $ka_kithe->kiws1 = $kiws1;
            $ka_kithe->zfbdate1 = $zfbdate1;

            if ($ka_kithe->save()) {
                $response["data"] = $ka_kithe;
                $response['message'] = "Macao Kakithe Data updated successfully!";
                $response['success'] = true;
                $response['status'] = STATUS_OK;
            }
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateNewMacaoKakithe(Request $request)
    {

        $response = [];
        $response['success'] = false;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required",
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
            $kizm = $request_data["kizm"];
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

            $ya_kithe = NewMacaoYakithe::orderBy("nn", "asc")->get();

            $version = 1;

            foreach ($ya_kithe as $item) {
                $nnd1 = $nn + $version;
                $data = NewMacaoYakithe::find($item["id"]);
                $data->nn = $nnd1;
                $data->save();
                $version++;
            }

            $ka_kithe = NewMacaoKakithe::find($id);

            $ka_kithe->nn = $nn;
            $ka_kithe->nd = $nd;
            $ka_kithe->kitm = $kitm;
            $ka_kithe->kizt = $kizt;
            $ka_kithe->kizm = $kizm;
            $ka_kithe->kizm6 = $kizm6;
            $ka_kithe->kigg = $kigg;
            $ka_kithe->kilm = $kilm;
            $ka_kithe->kisx = $kisx;
            $ka_kithe->kibb = $kibb;
            $ka_kithe->kiws = $kiws;
            $ka_kithe->zfbdate = $zfbdate;
            $ka_kithe->kitm1 = $kitm1;
            $ka_kithe->kizt1 = $kizt1;
            $ka_kithe->kizm1 = $kizm1;
            $ka_kithe->kizm61 = $kizm61;
            $ka_kithe->kigg1 = $kigg1;
            $ka_kithe->kilm1 = $kilm1;
            $ka_kithe->kisx1 = $kisx1;
            $ka_kithe->kibb1 = $kibb1;
            $ka_kithe->kiws1 = $kiws1;
            $ka_kithe->zfbdate1 = $zfbdate1;

            if ($ka_kithe->save()) {
                $response["data"] = $ka_kithe;
                $response['message'] = "New Macao Kakithe Data updated successfully!";
                $response['success'] = true;
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
