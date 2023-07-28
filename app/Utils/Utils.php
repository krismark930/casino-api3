<?php

namespace App\Utils;

use App\Models\Web\UpdateLog;
use App\Models\User;
use App\Models\Kakithe;
use App\Models\MacaoKakithe;
use App\Models\Kaquota;
use App\Models\Kamem;
use App\Models\KasxNumber;
use App\Models\Kacolor;
use App\Models\Config;
use App\Models\OrderLotterySub;
use App\Models\OrderLottery;
use App\Models\Web\MoneyLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Utils
{
    const Scores = [
        '',
        '取消',
        '赛事腰斩',
        '赛事改期',
        '赛事延期',
        '赛事延赛',
        '赛事取消',
        '赛事无PK加时',
        '球员弃权',
        '队名错误',
        '主客场错误',
        '先发投手更换',
        '选手更换',
        '联赛名称错',
        '', '', '', '', '',
        '提前开赛',
        '[注单确认]',
        '[取消]',
        '[赛事腰斩]',
        '[赛事改期]',
        '[赛事延期]',
        '[赛事延赛]',
        '[赛事取消]',
        '[赛事无PK加时]',
        '[球员弃权]',
        '[队名错误]',
        '[主客场错误]',
        '[先发投手更换]',
        '[选手更换]',
        '[联赛名称错误]',
        '[盘口错误]',
        '[提前开赛]',
        '[赛果错误]',
        '[未接受注单]',
        '[进球取消]',
        '[红卡取消]',
        '[非正常投注]',
        '[赔率错误]'
    ];

    const Rep_HK = '香港盤';
    const Rep_Malay = '馬來盤';
    const Rep_Indo = '印尼盤';
    const Rep_Euro = '歐洲盤';

    const Mnu_Soccer = '足球';
    const Mnu_Bask = '篮球';
    const Mnu_Base = '棒球';
    const Mnu_Tennis = '网球';
    const Mnu_Voll = '排球';
    const Mnu_Other = '其它';
    const Mnu_BasketBall = '籃球';
    const Mnu_Stock = '指数';
    const Mnu_Outright = '冠军';
    const Mnu_MarkSix = '六合彩';
    const Mnu_EarlyMarket = '早餐';
    const Mnu_Guan = 'Mnu_Guan';

    static function BalanceToAccount($rows, $is_win = 0, $lottery_type = '重庆时时彩', $rate = 0)
    {
        //$rows 当前行 $id注单号 $sub_id子注单号 is_win输赢
        $user_id = $rows['user_id'];
        $bet_rate = $rows['bet_rate'];
        $order_num = $rows['order_num'];  //定单号
        $order_sub_num = $rows['order_sub_num'];  //子定单号
        if ($rate != 0) {  //多种赔率重算可赢金额
            $rows['win'] = $rows['bet_money'] * $rate;
            $win = $rows['bet_money'] * $rate;
            //更新可赢金额
            OrderLotterySub::where("order_num", $order_num)
                ->where("order_sub_num", $order_sub_num)
                ->where("bet_rate", $bet_rate)
                ->update([
                    "win" => $win
                ]);
        }
        if ($is_win == 0) {  //输
            $bet_money_total =  $rows['fs'];  //加上可赢金额、返水
            if ($bet_money_total > 0) {  //未中奖,返还返水
                $assets = round(User::find($user_id)->Money, 2);
                $q1 = User::where("id", $user_id)->increment('Money', $bet_money_total);
                //会员金额操作成功
                if ($q1 == 1) {
                    $balance =   $assets + $bet_money_total;
                    $datetime = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                    $new_log = new MoneyLog;
                    $new_log->user_id = $user_id;
                    $new_log->order_num = $order_sub_num;
                    $new_log->about = $lottery_type;
                    $new_log->update_time = $datetime;
                    $new_log->type = '彩票自动结算-返还返水';
                    $new_log->order_value = $bet_money_total;
                    $new_log->assets = $assets;
                    $new_log->balance = $balance;
                    $new_log->save();
                }
            }
            OrderLottery::where("id", $rows["id"])->update(["status" => 1]);
            OrderLotterySub::where("id", $rows["sub_id"])
                ->update(["status" => 1, "is_win" => 0]);
        } elseif ($is_win == 1) {  //赢
            OrderLottery::where("id", $rows["id"])->update(["status" => 1]);
            OrderLotterySub::where("id", $rows["sub_id"])
                ->update(["status" => 1, "is_win" => 1]);
            $bet_money_total =  $rows['win'] + $rows["bet_money"] + $rows['fs'];  //加上可赢金额、返水
            if ($bet_money_total > 0) {  //未中奖,返还返水
                $assets = round(User::find($user_id)->Money, 2);
                $q1 = User::where("id", $user_id)->increment('Money', $bet_money_total);
                //会员金额操作成功
                if ($q1 == 1) {
                    $balance =   $assets + $bet_money_total;
                    $datetime = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                    $new_log = new MoneyLog;
                    $new_log->user_id = $user_id;
                    $new_log->order_num = $order_sub_num;
                    $new_log->about = $lottery_type;
                    $new_log->update_time = $datetime;
                    $new_log->type = '彩票自动结算-彩票中奖';
                    $new_log->order_value = $bet_money_total;
                    $new_log->assets = $assets;
                    $new_log->balance = $balance;
                    $new_log->save();
                }
            }
        } elseif ($is_win == 2) {  //和
            OrderLottery::where("id", $rows["id"])->update(["status" => 1]);
            OrderLotterySub::where("id", $rows["sub_id"])
                ->update(["status" => 1, "is_win" => 2]);
            //注单中奖，给会员账户增加奖金
            $bet_money_total =  $rows['bet_money'] + $rows['fs'];  //返还本金
            if ($bet_money_total > 0) {
                $assets = round(User::find($user_id)->Money, 2);
                $q1 = User::where("id", $user_id)->increment('Money', $bet_money_total);
                //会员金额操作成功
                if ($q1 == 1) {
                    $balance =   $assets + $bet_money_total;
                    $datetime = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                    $new_log = new MoneyLog;
                    $new_log->user_id = $user_id;
                    $new_log->order_num = $order_sub_num;
                    $new_log->about = $lottery_type;
                    $new_log->update_time = $datetime;
                    $new_log->type = '彩票自动结算-和局-返还本金';
                    $new_log->order_value = $bet_money_total;
                    $new_log->assets = $assets;
                    $new_log->balance = $balance;
                    $new_log->save();
                }
            }
        } elseif ($is_win == 3) {  //赢一半
            OrderLottery::where("id", $rows["id"])->update(["status" => 1]);
            OrderLotterySub::where("id", $rows["sub_id"])
                ->update(["status" => 1, "is_win" => 2]);
            //注单中奖，给会员账户增加奖金
            $bet_money_total = $rows['win'] / 2 + $rows['bet_money'] + $rows['fs'];  //返还本金
            if ($bet_money_total > 0) {
                $assets = round(User::find($user_id)->Money, 2);
                $q1 = User::where("id", $user_id)->increment('Money', $bet_money_total);
                //会员金额操作成功
                if ($q1 == 1) {
                    $balance =   $assets + $bet_money_total;
                    $datetime = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');
                    $new_log = new MoneyLog;
                    $new_log->user_id = $user_id;
                    $new_log->order_num = $order_sub_num;
                    $new_log->about = $lottery_type;
                    $new_log->update_time = $datetime;
                    $new_log->type = '彩票自动结算-彩票中奖-赢-半';
                    $new_log->order_value = $bet_money_total;
                    $new_log->assets = $assets;
                    $new_log->balance = $balance;
                    $new_log->save();
                }
            }
        }
    }

    static function Get_wx1_Color($rrr)
    {
        $ka_sxnumber = KasxNumber::where("ID", $rrr)->first();
        return $ka_sxnumber['m_number'];
    }

    static function ka_config($i)
    {
        $config = Config::query()->first(["id", "webname", "weburl", "tm", "tmdx", "tmps", "zm", "zmdx", "ggpz", "sanimal", "affice", "fenb", "haffice2", "a1", "a2", "a3", "a10", "opwww"]);
        return $config[$i];
    }

    static function convertToEn($nameZh)
    {
        $enName = "";
        if ($nameZh == "单") {
            $enName = "ODD";
        } elseif ($nameZh == "双") {
            $enName = "EVEN";
        } elseif ($nameZh == "大") {
            $enName = "OVER";
        } elseif ($nameZh == "小") {
            $enName = "UNDER";
        } elseif ($nameZh == "尾大") {
            $enName = "LAST:OVER";
        } elseif ($nameZh == "尾小") {
            $enName = "LAST:UNDER";
        } elseif ($nameZh == "合数双") {
            $enName = "SUM:EVEN";
        } elseif ($nameZh == "合数单") {
            $enName = "SUM:ODD";
        } elseif ($nameZh == "东") {
            $enName = "EAST";
        } elseif ($nameZh == "南") {
            $enName = "SOUTH";
        } elseif ($nameZh == "西") {
            $enName = "WEST";
        } elseif ($nameZh == "北") {
            $enName = "NORTH";
        } elseif ($nameZh == "中") {
            $enName = "ZHONG";
        } elseif ($nameZh == "发") {
            $enName = "FA";
        } elseif ($nameZh == "白") {
            $enName = "BAI";
        } elseif ($nameZh == "春") {
            $enName = "SPRING";
        } elseif ($nameZh == "夏") {
            $enName = "SUMMER";
        } elseif ($nameZh == "秋") {
            $enName = "FALL";
        } elseif ($nameZh == "冬") {
            $enName = "WINTER";
        } elseif ($nameZh == "金") {
            $enName = "METAL";
        } elseif ($nameZh == "木") {
            $enName = "WOOD";
        } elseif ($nameZh == "水") {
            $enName = "WATER";
        } elseif ($nameZh == "火") {
            $enName = "FIRE";
        } elseif ($nameZh == "土") {
            $enName = "EARTH";
        } elseif ($nameZh == "总和大") {
            $enName = "SUM:OVER";
        } elseif ($nameZh == "总和小") {
            $enName = "SUM:UNDER";
        } elseif ($nameZh == "和") {
            $enName = "SUM:TIE";
        } elseif ($nameZh == "总和单") {
            $enName = "SUM:ODD";
        } elseif ($nameZh == "总和双") {
            $enName = "SUM:EVEN";
        } elseif ($nameZh == "总和尾大") {
            $enName = "SUM:LAST:OVER";
        } elseif ($nameZh == "总和尾小") {
            $enName = "SUM:LAST:UNDER";
        } elseif ($nameZh == "龙") {
            $enName = "S:DRAGON";
        } elseif ($nameZh == "虎") {
            $enName = "S:TIGER";
        }
        return $enName;
    }
    static function convertToEnPK10($nameZh, $type)
    {
        $enName = "";
        if ($nameZh == "龙") {
            $enName = $type . ":" . (11 - $type) . ":DRAGON";
        } else if ($nameZh == "虎") {
            $enName = $type . ":" . (11 - $type) . ":TIGER";
        } else if ($nameZh == "冠亚大") {
            $enName = "OVER";
        } elseif ($nameZh == "冠亚小") {
            $enName = "UNDER";
        } elseif ($nameZh == "冠亚双") {
            $enName = "EVEN";
        } elseif ($nameZh == "冠亚单") {
            $enName = "ODD";
        } elseif ($nameZh == "冠亚和") {
            $enName = "SUM:TIE";
        }
        return $enName;
    }

    //广东快乐十分开奖函数
    static function G10_Auto($num, $type)
    {
        $zh = $num[0] + $num[1] + $num[2] + $num[3] + $num[4] + $num[5] + $num[6] + $num[7];
        if ($type == 1) {
            return $zh;
        }
        if ($type == 2) {
            if ($zh >= 85 && $zh <= 132) {
                return '总和大';
            }
            if ($zh >= 36 && $zh <= 83) {
                return '总和小';
            }
            if ($zh == 84) {
                return '和';
            }
        }
        if ($type == 3) {
            if ($zh % 2 == 0) {
                return '总和双';
            } else {
                return '总和单';
            }
        }
        if ($type == 4) {
            $zhws = substr($zh, strlen($zh) - 1);
            if ($zhws >= 5) {
                return '总和尾大';
            } else {
                return '总和尾小';
            }
        }
        if ($type == 5) {
            if ($num[0] > $num[7]) {
                return '龙';
            } elseif ($num[0] == $num[7]) {
                return '和';
            } else {
                return '虎';
            }
        }
    }
    //广东快乐十分单双
    static function G10_Ds($ball)
    {
        if ($ball % 2 == 0) {
            return '双';
        } else {
            return '单';
        }
    }
    //广东快乐十分大小
    static function G10_Dx($ball)
    {
        if ($ball > 10) {
            return '大';
        } else {
            return '小';
        }
    }
    //广东快乐十分尾数大小
    static function G10_WsDx($ball)
    {
        $wsdx = substr($ball, -1);
        if ($wsdx > 4) {
            return '尾大';
        } else {
            return '尾小';
        }
    }
    //广东快乐十分合数单双
    static function G10_HsDs($ball)
    {
        $ball = BuLing($ball);
        $a = substr($ball, 0, 1);
        $b = substr($ball, -1);
        $c = $a + $b;
        if ($c % 2 == 0) {
            return '合数双';
        } else {
            return '合数单';
        }
    }
    //广东快乐十分号码方位
    static function G10_Fw($ball)
    {
        if (BuLing($ball) == '01' || BuLing($ball) == '05' || BuLing($ball) == '09' || BuLing($ball) == '13' || BuLing($ball) == '17') {
            $fw = '东';
        } else if (BuLing($ball) == '02' || BuLing($ball) == '06' || BuLing($ball) == '10' || BuLing($ball) == '14' || BuLing($ball) == '18') {
            $fw = '南';
        } else if (BuLing($ball) == '03' || BuLing($ball) == '07' || BuLing($ball) == '11' || BuLing($ball) == '15' || BuLing($ball) == '19') {
            $fw = '西';
        } else {
            $fw = '北';
        }
        return $fw;
    }
    //广东快乐十分号码中发白
    static function G10_Zfb($ball)
    {
        if (BuLing($ball) == '01' || BuLing($ball) == '02' || BuLing($ball) == '03' || BuLing($ball) == '04' || BuLing($ball) == '05' || BuLing($ball) == '06' || BuLing($ball) == '07') {
            $zfb = '中';
        } else if (BuLing($ball) == '08' || BuLing($ball) == '09' || BuLing($ball) == '10' || BuLing($ball) == '11' || BuLing($ball) == '12' || BuLing($ball) == '13' || BuLing($ball) == '14') {
            $zfb = '发';
        } else {
            $zfb = '白';
        }
        return $zfb;
    }
    //广东快乐十分号码春夏秋冬
    static function G10_season($ball)
    {
        if (BuLing($ball) == '01' || BuLing($ball) == '02' || BuLing($ball) == '03' || BuLing($ball) == '04' || BuLing($ball) == '05') {
            $season = '春';
        } else if (BuLing($ball) == '06' || BuLing($ball) == '07' || BuLing($ball) == '08' || BuLing($ball) == '09' || BuLing($ball) == '10') {
            $season = '夏';
        } else if (BuLing($ball) == '11' || BuLing($ball) == '12' || BuLing($ball) == '13' || BuLing($ball) == '14' || BuLing($ball) == '15') {
            $season = '秋';
        } else {
            $season = '冬';
        }
        return $season;
    }
    //广东快乐十分号码春夏秋冬
    static function G10_wuxing($ball)
    {
        if (BuLing($ball) == '05' || BuLing($ball) == '10' || BuLing($ball) == '15' || BuLing($ball) == '20') {
            $wuxing = '金';
        } else if (BuLing($ball) == '01' || BuLing($ball) == '06' || BuLing($ball) == '11' || BuLing($ball) == '16') {
            $wuxing = '木';
        } else if (BuLing($ball) == '02' || BuLing($ball) == '07' || BuLing($ball) == '12' || BuLing($ball) == '17') {
            $wuxing = '水';
        } else if (BuLing($ball) == '03' || BuLing($ball) == '08' || BuLing($ball) == '13' || BuLing($ball) == '18') {
            $wuxing = '火';
        } else {
            $wuxing = '土';
        }
        return $wuxing;
    }


    //广西十分彩 结算函数
    static function gxsf_Ds($ball)
    {
        if ($ball == 21) {
            return '和';
        }
        if ($ball % 2 == 0) {
            return '双';
        } else {
            return '单';
        }
    }
    static function gxsf_Dx($ball)
    {
        if ($ball == 21) {
            return '和';
        }
        if ($ball > 10) {
            return '大';
        } else {
            return '小';
        }
    }
    static function gxsf_WsDx($ball)
    {
        $wsdx = substr($ball, -1);
        if ($ball == 21) {
            return '和';
        }
        if ($wsdx > 4) {
            return '尾大';
        } else {
            return '尾小';
        }
    }
    static function gxsf_HsDs($ball)
    {
        $ball = BuLing($ball);
        $a = substr($ball, 0, 1);
        $b = substr($ball, -1);
        $c = $a + $b;
        if ($c == 21) {
            return '和';
        }
        if ($c % 2 == 0) {
            return '合数双';
        } else {
            return '合数单';
        }
    }
    static function gxsf_season($ball)
    {
        if ($ball == 21) {
            return '和';
        }
        if (BuLing($ball) == '01' || BuLing($ball) == '02' || BuLing($ball) == '03' || BuLing($ball) == '04' || BuLing($ball) == '05') {
            $season = '春';
        } else if (BuLing($ball) == '06' || BuLing($ball) == '07' || BuLing($ball) == '08' || BuLing($ball) == '09' || BuLing($ball) == '10') {
            $season = '夏';
        } else if (BuLing($ball) == '11' || BuLing($ball) == '12' || BuLing($ball) == '13' || BuLing($ball) == '14' || BuLing($ball) == '15') {
            $season = '秋';
        } else {
            $season = '冬';
        }
        return $season;
    }
    static function gxsf_wuxing($ball)
    {
        if (BuLing($ball) == '05' || BuLing($ball) == '10' || BuLing($ball) == '15' || BuLing($ball) == '20') {
            $wuxing = '金';
        } else if (BuLing($ball) == '01' || BuLing($ball) == '06' || BuLing($ball) == '11' || BuLing($ball) == '16' || BuLing($ball) == '21') {
            $wuxing = '木';
        } else if (BuLing($ball) == '02' || BuLing($ball) == '07' || BuLing($ball) == '12' || BuLing($ball) == '17') {
            $wuxing = '水';
        } else if (BuLing($ball) == '03' || BuLing($ball) == '08' || BuLing($ball) == '13' || BuLing($ball) == '18') {
            $wuxing = '火';
        } else {
            $wuxing = '土';
        }
        return $wuxing;
    }
    static function gxsf_color($ball)
    {
        if (BuLing($ball) == '01' || BuLing($ball) == '04' || BuLing($ball) == '07' || BuLing($ball) == '10' || BuLing($ball) == '13' || BuLing($ball) == '16' || BuLing($ball) == '19') {
            $wuxing = 'RED';
        } else if (BuLing($ball) == '02' || BuLing($ball) == '05' || BuLing($ball) == '08' || BuLing($ball) == '11' || BuLing($ball) == '14' || BuLing($ball) == '17' || BuLing($ball) == '20') {
            $wuxing = 'BLUE';
        } else {
            $wuxing = 'GREEN';
        }
        return $wuxing;
    }

    //广西十分彩开奖函数
    static function gxsf_Auto($num, $type)
    {
        $zh = $num[0] + $num[1] + $num[2] + $num[3] + $num[4];
        if ($type == 1) {
            return $zh;
        }
        if ($type == 2) {
            if ($zh >= 55) {
                return '总和大';
            }
            if ($zh < 55) {
                return '总和小';
            }
        }
        if ($type == 3) {
            if ($zh % 2 == 0) {
                return '总和双';
            } else {
                return '总和单';
            }
        }
        if ($type == 4) {
            if ($num[0] > $num[4]) {
                return '龙';
            }
            if ($num[0] < $num[4]) {
                return '虎';
            }
            if ($num[0] == $num[4]) {
                return '和';
            }
        }
        if ($type == 5) {

            $n1 = $num[0];
            $n2 = $num[1];
            $n3 = $num[2];
            if (($n1 == 1 || $n2 == 1 || $n3 == 1) && ($n1 == 21 || $n2 == 21 || $n3 == 21)) {
                if ($n1 == 1) {
                    $n1 = 22;
                }
                if ($n2 == 1) {
                    $n2 = 22;
                }
                if ($n3 == 1) {
                    $n3 = 22;
                }
            }

            if ($n1 == $n2 && $n2 == $n3) {
                return "豹子";
            } elseif (($n1 == $n2) || ($n1 == $n3) || ($n2 == $n3)) {
                return "对子";
            } elseif (($n1 == 22 || $n2 == 22 || $n3 == 22) && ($n1 == 21 || $n2 == 21 || $n3 == 21) && ($n1 == 2 || $n2 == 2 || $n3 == 2)) {
                return "顺子";
            } elseif (((abs($n1 - $n2) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 2) && (abs($n1 - $n3) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 1) && (abs($n1 - $n3) == 1))) {
                return "顺子";
            } elseif ((abs($n1 - $n2) == 1) || (abs($n1 - $n3) == 1) || (abs($n2 - $n3) == 1)) {
                return "半顺";
            } else {
                return "杂六";
            }
        }
        if ($type == 6) {
            $n1 = $num[1];
            $n2 = $num[2];
            $n3 = $num[3];
            if (($n1 == 1 || $n2 == 1 || $n3 == 1) && ($n1 == 21 || $n2 == 21 || $n3 == 21)) {
                if ($n1 == 1) {
                    $n1 = 22;
                }
                if ($n2 == 1) {
                    $n2 = 22;
                }
                if ($n3 == 1) {
                    $n3 = 22;
                }
            }

            if ($n1 == $n2 && $n2 == $n3) {
                return "豹子";
            } elseif (($n1 == $n2) || ($n1 == $n3) || ($n2 == $n3)) {
                return "对子";
            } elseif (($n1 == 22 || $n2 == 22 || $n3 == 22) && ($n1 == 21 || $n2 == 21 || $n3 == 21) && ($n1 == 2 || $n2 == 2 || $n3 == 2)) {
                return "顺子";
            } elseif (((abs($n1 - $n2) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 2) && (abs($n1 - $n3) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 1) && (abs($n1 - $n3) == 1))) {
                return "顺子";
            } elseif ((abs($n1 - $n2) == 1) || (abs($n1 - $n3) == 1) || (abs($n2 - $n3) == 1)) {
                return "半顺";
            } else {
                return "杂六";
            }
        }
        if ($type == 7) {
            $n1 = $num[2];
            $n2 = $num[3];
            $n3 = $num[4];
            if (($n1 == 1 || $n2 == 1 || $n3 == 1) && ($n1 == 21 || $n2 == 21 || $n3 == 21)) {
                if ($n1 == 1) {
                    $n1 = 22;
                }
                if ($n2 == 1) {
                    $n2 = 22;
                }
                if ($n3 == 1) {
                    $n3 = 22;
                }
            }

            if ($n1 == $n2 && $n2 == $n3) {
                return "豹子";
            } elseif (($n1 == $n2) || ($n1 == $n3) || ($n2 == $n3)) {
                return "对子";
            } elseif (($n1 == 22 || $n2 == 22 || $n3 == 22) && ($n1 == 21 || $n2 == 21 || $n3 == 21) && ($n1 == 2 || $n2 == 2 || $n3 == 2)) {
                return "顺子";
            } elseif (((abs($n1 - $n2) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 2) && (abs($n1 - $n3) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 1) && (abs($n1 - $n3) == 1))) {
                return "顺子";
            } elseif ((abs($n1 - $n2) == 1) || (abs($n1 - $n3) == 1) || (abs($n2 - $n3) == 1)) {
                return "半顺";
            } else {
                return "杂六";
            }
        }
    }


    //重庆时时彩开奖函数
    static function Ssc_Auto($num, $type)
    {
        $zh = $num[0] + $num[1] + $num[2] + $num[3] + $num[4];
        if ($type == 1) {
            return $zh;
        }
        if ($type == 2) {
            if ($zh >= 23) {
                return '总和大';
            }
            if ($zh <= 22) {
                return '总和小';
            }
        }
        if ($type == 3) {
            if ($zh % 2 == 0) {
                return '总和双';
            } else {
                return '总和单';
            }
        }
        if ($type == 4) {
            if ($num[0] > $num[4]) {
                return '龙';
            }
            if ($num[0] < $num[4]) {
                return '虎';
            }
            if ($num[0] == $num[4]) {
                return '和';
            }
        }
        if ($type == 5) {

            $n1 = $num[0];
            $n2 = $num[1];
            $n3 = $num[2];
            if (($n1 == 0 || $n2 == 0 || $n3 == 0) && ($n1 == 9 || $n2 == 9 || $n3 == 9)) {
                if ($n1 == 0) {
                    $n1 = 10;
                }
                if ($n2 == 0) {
                    $n2 = 10;
                }
                if ($n3 == 0) {
                    $n3 = 10;
                }
            }

            if ($n1 == $n2 && $n2 == $n3) {
                return "豹子";
            } elseif (($n1 == $n2) || ($n1 == $n3) || ($n2 == $n3)) {
                return "对子";
            } elseif (($n1 == 10 || $n2 == 10 || $n3 == 10) && ($n1 == 9 || $n2 == 9 || $n3 == 9) && ($n1 == 1 || $n2 == 1 || $n3 == 1)) {
                return "顺子";
            } elseif (((abs($n1 - $n2) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 2) && (abs($n1 - $n3) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 1) && (abs($n1 - $n3) == 1))) {
                return "顺子";
            } elseif ((abs($n1 - $n2) == 1) || (abs($n1 - $n3) == 1) || (abs($n2 - $n3) == 1)) {
                return "半顺";
            } else {
                return "杂六";
            }
        }
        if ($type == 6) {
            $n1 = $num[1];
            $n2 = $num[2];
            $n3 = $num[3];
            if (($n1 == 0 || $n2 == 0 || $n3 == 0) && ($n1 == 9 || $n2 == 9 || $n3 == 9)) {
                if ($n1 == 0) {
                    $n1 = 10;
                }
                if ($n2 == 0) {
                    $n2 = 10;
                }
                if ($n3 == 0) {
                    $n3 = 10;
                }
            }

            if ($n1 == $n2 && $n2 == $n3) {
                return "豹子";
            } elseif (($n1 == $n2) || ($n1 == $n3) || ($n2 == $n3)) {
                return "对子";
            } elseif (($n1 == 10 || $n2 == 10 || $n3 == 10) && ($n1 == 9 || $n2 == 9 || $n3 == 9) && ($n1 == 1 || $n2 == 1 || $n3 == 1)) {
                return "顺子";
            } elseif (((abs($n1 - $n2) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 2) && (abs($n1 - $n3) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 1) && (abs($n1 - $n3) == 1))) {
                return "顺子";
            } elseif ((abs($n1 - $n2) == 1) || (abs($n1 - $n3) == 1) || (abs($n2 - $n3) == 1)) {
                return "半顺";
            } else {
                return "杂六";
            }
        }
        if ($type == 7) {
            $n1 = $num[2];
            $n2 = $num[3];
            $n3 = $num[4];
            if (($n1 == 0 || $n2 == 0 || $n3 == 0) && ($n1 == 9 || $n2 == 9 || $n3 == 9)) {
                if ($n1 == 0) {
                    $n1 = 10;
                }
                if ($n2 == 0) {
                    $n2 = 10;
                }
                if ($n3 == 0) {
                    $n3 = 10;
                }
            }

            if ($n1 == $n2 && $n2 == $n3) {
                return "豹子";
            } elseif (($n1 == $n2) || ($n1 == $n3) || ($n2 == $n3)) {
                return "对子";
            } elseif (($n1 == 10 || $n2 == 10 || $n3 == 10) && ($n1 == 9 || $n2 == 9 || $n3 == 9) && ($n1 == 1 || $n2 == 1 || $n3 == 1)) {
                return "顺子";
            } elseif (((abs($n1 - $n2) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 2) && (abs($n1 - $n3) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 1) && (abs($n1 - $n3) == 1))) {
                return "顺子";
            } elseif ((abs($n1 - $n2) == 1) || (abs($n1 - $n3) == 1) || (abs($n2 - $n3) == 1)) {
                return "半顺";
            } else {
                return "杂六";
            }
        }
    }
    //重庆时时彩单双
    static function Ssc_Ds($ball)
    {
        if ($ball % 2 == 0) {
            return '双';
        } else {
            return '单';
        }
    }
    //重庆时时彩大小
    static function Ssc_Dx($ball)
    {
        if ($ball > 4) {
            return '大';
        } else {
            return '小';
        }
    }

    //重庆时时彩大小
    static function pk10_Dx($ball)
    {
        if ($ball > 5) {
            return '大';
        } else {
            return '小';
        }
    }

    //北京PK拾开奖函数
    static function Pk10_Auto_quick($num, $type)
    {
        $zh = $num[0] + $num[1];
        if ($type == 1) {
            return $zh;
        }
        if ($type == 2) {
            if ($zh > 11) {
                return '大';
            } else {
                return '小';
            }
        }
        if ($type == 3) {
            if ($zh % 2 == 0) {
                return '双';
            } else {
                return '单';
            }
        }
        if ($type == 4) {
            if ($num[0] > $num[9]) {
                return '龙';
            } else {
                return '虎';
            }
        }
        if ($type == 5) {
            if ($num[1] > $num[8]) {
                return '龙';
            } else {
                return '虎';
            }
        }
        if ($type == 6) {
            if ($num[2] > $num[7]) {
                return '龙';
            } else {
                return '虎';
            }
        }
        if ($type == 7) {
            if ($num[3] > $num[6]) {
                return '龙';
            } else {
                return '虎';
            }
        }
        if ($type == 8) {
            if ($num[4] > $num[5]) {
                return '龙';
            } else {
                return '虎';
            }
        }
    }
    static function Pk10_long_hu($num1, $num2)
    {
        if ($num1 > $num2) {
            return '龙';
        } else {
            return '虎';
        }
    }

    //北京PK拾开奖函数
    static function Pk10_Auto($num, $type, $ballnum)
    {
        $zh = $num[0] + $num[1];
        if ($type == 1) {
            return $zh;
        }
        if ($type == 2) {
            if ($zh == 11) {
                return '冠亚和';
            }
            if ($zh > 11) {
                return '冠亚大';
            } else {
                return '冠亚小';
            }
        }
        if ($type == 3) {
            if ($zh == 11) {
                return '冠亚和';
            }
            if ($zh % 2 == 0) {
                return '冠亚双';
            } else {
                return '冠亚单';
            }
        }
        if ($type == 4) {
            if ($num[0] > $num[9]) {
                return '龙';
            } else {
                return '虎';
            }
        }
        if ($type == 5) {
            if ($num[1] > $num[8]) {
                return '龙';
            } else {
                return '虎';
            }
        }
        if ($type == 6) {
            if ($num[2] > $num[7]) {
                return '龙';
            } else {
                return '虎';
            }
        }
        if ($type == 7) {
            if ($num[3] > $num[6]) {
                return '龙';
            } else {
                return '虎';
            }
        }
        if ($type == 8) {
            if ($num[4] > $num[5]) {
                return '龙';
            } else {
                return '虎';
            }
        }
        if ($type == 9) {
            if ($ballnum > 5) {
                return '大';
            } else {
                return '小';
            }
        }
        if ($type == 10) {
            if ($ballnum % 2 == 0) {
                return '双';
            } else {
                return '单';
            }
        }
    }

    //排列三，上海时时乐，3D开奖函数
    static function b3_ds($num, $info)
    {
        if ($num % 2 == 0) {
            return $info . '_EVEN';
        } else {
            return $info . '_ODD';
        }
    }
    static function b3_dx($num, $info)
    {
        if (($num % 10) > 4) {
            return $info . '_OVER';
        } else {
            return $info . '_UNDER';
        }
    }
    static function b3_zhihe($num, $info)
    {
        if (in_array($num % 10, array(1, 2, 3, 5, 7))) {
            return $info . '_PRIME';
        } else {
            return $info . '_COMPO';
        }
    }
    static function b3_f($num)
    {
        return $num % 10;
    }
    static function b3_kd($num0, $num1, $num2)
    {
        return static::get_max($num0, $num1, $num2) - static::get_min($num0, $num1, $num2);
    }

    static function f3D_Auto($num, $type)
    {
        $zh = $num[0] + $num[1] + $num[2];
        if ($type == 1) {
            return $zh;
        }
        if ($type == 2) {
            if ($zh >= 14) {
                return '总和大';
            }
            if ($zh <= 13) {
                return '总和小';
            }
        }
        if ($type == 3) {
            if ($zh % 2 == 0) {
                return '总和双';
            } else {
                return '总和单';
            }
        }
        if ($type == 4) {
            if ($num[0] > $num[2]) {
                return '龙';
            }
            if ($num[0] < $num[2]) {
                return '虎';
            }
            if ($num[0] == $num[2]) {
                return '和';
            }
        }
        if ($type == 5) {

            $n1 = $num[0];
            $n2 = $num[1];
            $n3 = $num[2];
            if (($n1 == 0 || $n2 == 0 || $n3 == 0) && ($n1 == 9 || $n2 == 9 || $n3 == 9)) {
                if ($n1 == 0) {
                    $n1 = 10;
                }
                if ($n2 == 0) {
                    $n2 = 10;
                }
                if ($n3 == 0) {
                    $n3 = 10;
                }
            }

            if ($n1 == $n2 && $n2 == $n3) {
                return "豹子";
            } elseif (($n1 == $n2) || ($n1 == $n3) || ($n2 == $n3)) {
                return "对子";
            } elseif (($n1 == 10 || $n2 == 10 || $n3 == 10) && ($n1 == 9 || $n2 == 9 || $n3 == 9) && ($n1 == 1 || $n2 == 1 || $n3 == 1)) {
                return "顺子";
            } elseif (((abs($n1 - $n2) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 2) && (abs($n1 - $n3) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 1) && (abs($n1 - $n3) == 1))) {
                return "顺子";
            } elseif ((abs($n1 - $n2) == 1) || (abs($n1 - $n3) == 1) || (abs($n2 - $n3) == 1)) {
                return "半顺";
            } else {
                return "杂六";
            }
        }
        if ($type == 6) {
            return static::get_max($num[0], $num[1], $num[2]) - static::get_min($num[0], $num[1], $num[2]);
        }
    }



    //重庆时时彩，天津时时彩，新疆时时彩 B5彩票
    static function b5_ds($num, $info)
    {
        if ($num % 2 == 0) {
            return $info . '-EVEN';
        } else {
            return $info . '-ODD';
        }
    }
    static function b5_dx($num, $info)
    {
        if (($num % 10) > 4) {
            return $info . '-OVER';
        } else {
            return $info . '-UNDER';
        }
    }
    static function b5_zh_dx($num, $info)
    {
        if ($num > 13) {
            return $info . '-OVER';
        } else {
            return $info . '-UNDER';
        }
    }
    static function b5_zhihe($num, $info)
    {
        if (in_array($num % 10, array(1, 2, 3, 5, 7))) {
            return $info . '-PRIME';
        } else {
            return $info . '-COMPO';
        }
    }
    static function b5_array_f($numArray)
    {
        $zh = $numArray[0] + $numArray[1] + $numArray[2];
        return $zh % 10;
    }
    static function b5_f($num)
    {
        return $num % 10;
    }
    static function b5_kd($num0, $num1, $num2)
    {
        return static::get_max($num0, $num1, $num2) - static::get_min($num0, $num1, $num2);
    }
    static function b5_niuniu($ball1, $ball2, $ball3, $ball4, $ball5)
    {
        $is_niu = "false";
        $niu_ji = "";
        if (($ball1 + $ball2 + $ball3) % 10 == 0) {
            $is_niu = "true";
            $niu_ji = ($ball4 + $ball5) % 10;
        } elseif (($ball1 + $ball2 + $ball4) % 10 == 0) {
            $is_niu = "true";
            $niu_ji = ($ball3 + $ball5) % 10;
        } elseif (($ball1 + $ball2 + $ball5) % 10 == 0) {
            $is_niu = "true";
            $niu_ji = ($ball3 + $ball4) % 10;
        } elseif (($ball1 + $ball3 + $ball4) % 10 == 0) {
            $is_niu = "true";
            $niu_ji = ($ball2 + $ball5) % 10;
        } elseif (($ball1 + $ball3 + $ball5) % 10 == 0) {
            $is_niu = "true";
            $niu_ji = ($ball2 + $ball4) % 10;
        } elseif (($ball1 + $ball4 + $ball5) % 10 == 0) {
            $is_niu = "true";
            $niu_ji = ($ball2 + $ball3) % 10;
        } elseif (($ball2 + $ball3 + $ball4) % 10 == 0) {
            $is_niu = "true";
            $niu_ji = ($ball1 + $ball5) % 10;
        } elseif (($ball2 + $ball3 + $ball5) % 10 == 0) {
            $is_niu = "true";
            $niu_ji = ($ball1 + $ball4) % 10;
        } elseif (($ball2 + $ball4 + $ball5) % 10 == 0) {
            $is_niu = "true";
            $niu_ji = ($ball1 + $ball3) % 10;
        } elseif (($ball3 + $ball4 + $ball5) % 10 == 0) {
            $is_niu = "true";
            $niu_ji = ($ball1 + $ball2) % 10;
        }
        if ($is_niu == "true") {
            if ($niu_ji == "0") {
                $niu_ji = "牛";
            }
            return "牛" . $niu_ji;
        } else {
            return "无牛";
        }
    }
    static function b5_niuds($value)
    {
        if ($value == "牛1" || $value == "牛3" || $value == "牛5" || $value == "牛7" || $value == "牛9") {
            return "牛单";
        } elseif ($value == "牛2" || $value == "牛4" || $value == "牛6" || $value == "牛8" || $value == "牛牛") {
            return "牛双";
        } else {
            return "无牛";
        }
    }
    static function b5_niudx($value)
    {
        if ($value == "牛1" || $value == "牛2" || $value == "牛3" || $value == "牛4" || $value == "牛5") {
            return "牛小";
        } elseif ($value == "牛6" || $value == "牛7" || $value == "牛8" || $value == "牛9" || $value == "牛牛") {
            return "牛大";
        } else {
            return "无牛";
        }
    }


    //北京快乐8开奖函数
    static function Kl8_convert($name)
    {
        if ($name == "SUM:OVER") {
            return '总和大';
        } elseif ($name == "SUM:UNDER") {
            return '总和小';
        } elseif ($name == "SUM:810") {
            return '总和810';
        } elseif ($name == "SUM:EVEN") {
            return '总和双';
        } elseif ($name == "SUM:ODD") {
            return '总和单';
        } elseif ($name == "TOP") {
            return '上';
        } elseif ($name == "MIDDLE") {
            return '中';
        } elseif ($name == "BOTTOM") {
            return '下';
        } elseif ($name == "EVEN") {
            return '偶';
        } elseif ($name == "ODD") {
            return '奇';
        } elseif ($name == "TIE") {
            return '和';
        }
    }
    static function Kl8_Auto($num, $type)
    {
        $zh = $num[0] + $num[1] + $num[2] + $num[3] + $num[4] + $num[5] + $num[6] + $num[7] + $num[8] + $num[9] + $num[10] + $num[11] + $num[12] + $num[13] + $num[14] + $num[15] + $num[16] + $num[17] + $num[18] + $num[19];
        if ($type == 1) {
            return $zh;
        }
        if ($type == 2) {
            if ($zh > 810) {
                return 'SUM:OVER'; //'总和大';
            } else if ($zh < 810) {
                return 'SUM:UNDER'; //'总和小';
            } else if ($zh == 810) {
                return 'SUM:810'; //'总和810';
            }
        }
        if ($type == 3) {
            if ($zh % 2 == 0) {
                return 'SUM:EVEN'; //'总和双';
            } else {
                return 'SUM:ODD'; //'总和单';
            }
        }
        if ($type == 4) {
            $shang = 0;
            $xia = 0;
            for ($i = 0; $i < 20; $i++) {
                if ($num[$i] < 41) {
                    $shang = $shang + 1;
                } else {
                    $xia = $xia + 1;
                }
            }

            if ($shang > $xia) {
                return 'TOP';
            } else if ($shang < $xia) {
                return 'BOTTOM';
            } else if ($shang == $xia) {
                return "MIDDLE";
            }
        }
        if ($type == 5) {
            $ji = 0;
            $ou = 0;
            for ($i = 0; $i < 20; $i++) {
                if ($num[$i] % 2 == 0) {
                    $ou = $ou + 1;
                } else {
                    $ji = $ji + 1;
                }
            }
            if ($ou > $ji) {
                return 'EVEN';
            } else if ($ou < $ji) {
                return 'ODD';
            } else if ($ou == $ji) {
                return "TIE";
            }
        }

        if ($type == 7) { //五行
            if (695 >= $zh && $zh >= 210) {
                return 'SUM:METAL';
            } else if (763 >= $zh && $zh >= 696) {
                return 'SUM:WOOD';
            } else if (855 >= $zh && $zh >= 764) {
                return 'SUM:WATER';
            } else if (923 >= $zh && $zh >= 856) {
                return 'SUM:FIRE';
            } else if (1410 >= $zh && $zh >= 924) {
                return 'SUM:EARTH';
            }
        }
    }

    //北京快乐8开奖函数
    static function Kl8_Auto_zh($num, $type)
    {
        $zh = $num[0] + $num[1] + $num[2] + $num[3] + $num[4] + $num[5] + $num[6] + $num[7] + $num[8] + $num[9] + $num[10] + $num[11] + $num[12] + $num[13] + $num[14] + $num[15] + $num[16] + $num[17] + $num[18] + $num[19];
        if ($type == 1) {
            return $zh;
        }
        if ($type == 2) {
            if ($zh > 810) {
                return '总和大';
            } else if ($zh < 810) {
                return '总和小';
            } else if ($zh == 810) {
                return '总和810';
            }
        }
        if ($type == 3) {
            if ($zh % 2 == 0) {
                return '总和双';
            } else {
                return '总和单';
            }
        }
        if ($type == 4) {
            $shang = 0;
            $xia = 0;
            for ($i = 0; $i < 20; $i++) {
                if ($num[$i] < 41) {
                    $shang = $shang + 1;
                } else {
                    $xia = $xia + 1;
                }
            }

            if ($shang > $xia) {
                return '上';
            } else if ($shang < $xia) {
                return '下';
            } else if ($shang == $xia) {
                return "中";
            }
        }
        if ($type == 5) {
            $ji = 0;
            $ou = 0;
            for ($i = 0; $i < 20; $i++) {
                if ($num[$i] % 2 == 0) {
                    $ou = $ou + 1;
                } else {
                    $ji = $ji + 1;
                }
            }
            if ($ou > $ji) {
                return '偶';
            } else if ($ou < $ji) {
                return '奇';
            } else if ($ou == $ji) {
                return "和";
            }
        }
    }


    //广东11选5
    static function gd11x5_Auto($num, $type)
    {
        $zh = $num[0] + $num[1] + $num[2] + $num[3] + $num[4];
        if ($type == 1) {
            return $zh;
        }
        if ($type == 2) {
            if ($zh >= 31) {
                return '总和大';
            }
            if ($zh <= 29) {
                return '总和小';
            }
            if ($zh == 30) {
                return '总和30';
            }
        }
        if ($type == 3) {
            if ($zh % 2 == 0) {
                return '总和双';
            } else {
                return '总和单';
            }
        }
        if ($type == 4) {
            if ($num[0] > $num[4]) {
                return '龙';
            }
            if ($num[0] < $num[4]) {
                return '虎';
            }
            if ($num[0] == $num[4]) {
                return '和';
            }
        }
        if ($type == 5) {

            $n1 = $num[0];
            $n2 = $num[1];
            $n3 = $num[2];
            if (($n1 == 1 || $n2 == 1 || $n3 == 1) && ($n1 == 11 || $n2 == 11 || $n3 == 11)) {
                if ($n1 == 1) {
                    $n1 = 12;
                }
                if ($n2 == 1) {
                    $n2 = 12;
                }
                if ($n3 == 1) {
                    $n3 = 12;
                }
            }

            if (($n1 == 12 || $n2 == 12 || $n3 == 12) && ($n1 == 11 || $n2 == 11 || $n3 == 11) && ($n1 == 2 || $n2 == 2 || $n3 == 2)) {
                return "顺子";
            } elseif (((abs($n1 - $n2) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 2) && (abs($n1 - $n3) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 1) && (abs($n1 - $n3) == 1))) {
                return "顺子";
            } elseif ((abs($n1 - $n2) == 1) || (abs($n1 - $n3) == 1) || (abs($n2 - $n3) == 1)) {
                return "半顺";
            } else {
                return "杂六";
            }
        }
        if ($type == 6) {
            $n1 = $num[1];
            $n2 = $num[2];
            $n3 = $num[3];
            if (($n1 == 1 || $n2 == 1 || $n3 == 1) && ($n1 == 11 || $n2 == 11 || $n3 == 11)) {
                if ($n1 == 1) {
                    $n1 = 12;
                }
                if ($n2 == 1) {
                    $n2 = 12;
                }
                if ($n3 == 1) {
                    $n3 = 12;
                }
            }

            if (($n1 == 12 || $n2 == 12 || $n3 == 12) && ($n1 == 11 || $n2 == 11 || $n3 == 11) && ($n1 == 2 || $n2 == 2 || $n3 == 2)) {
                return "顺子";
            } elseif (((abs($n1 - $n2) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 2) && (abs($n1 - $n3) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 1) && (abs($n1 - $n3) == 1))) {
                return "顺子";
            } elseif ((abs($n1 - $n2) == 1) || (abs($n1 - $n3) == 1) || (abs($n2 - $n3) == 1)) {
                return "半顺";
            } else {
                return "杂六";
            }
        }
        if ($type == 7) {
            $n1 = $num[2];
            $n2 = $num[3];
            $n3 = $num[4];
            if (($n1 == 1 || $n2 == 1 || $n3 == 1) && ($n1 == 11 || $n2 == 11 || $n3 == 11)) {
                if ($n1 == 1) {
                    $n1 = 12;
                }
                if ($n2 == 1) {
                    $n2 = 12;
                }
                if ($n3 == 1) {
                    $n3 = 12;
                }
            }

            if (($n1 == 12 || $n2 == 12 || $n3 == 12) && ($n1 == 11 || $n2 == 11 || $n3 == 11) && ($n1 == 2 || $n2 == 2 || $n3 == 2)) {
                return "顺子";
            } elseif (((abs($n1 - $n2) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 2) && (abs($n1 - $n3) == 1) && (abs($n2 - $n3) == 1)) || ((abs($n1 - $n2) == 1) && (abs($n1 - $n3) == 1))) {
                return "顺子";
            } elseif ((abs($n1 - $n2) == 1) || (abs($n1 - $n3) == 1) || (abs($n2 - $n3) == 1)) {
                return "半顺";
            } else {
                return "杂六";
            }
        }
    }
    //广东11选5大小
    static function gd11x5_Dx($ball)
    {
        if ($ball > 5) {
            return '大';
        } else {
            return '小';
        }
    }
    static function getEnNameGd11($name)
    {
        $enName = "";
        if ($name == "总和大") {
            $enName = "OVER";
        } elseif ($name == "总和小") {
            $enName = "UNDER";
        } elseif ($name == "总和单") {
            $enName = "ODD";
        } elseif ($name == "总和双") {
            $enName = "EVEN";
        } elseif ($name == "龙") {
            $enName = "DRAGON";
        } elseif ($name == "虎") {
            $enName = "TIGER";
        } elseif ($name == "和") {
            $enName = "TIE";
        } elseif ($name == "顺子") {
            $enName = "SHUNZI";
        } elseif ($name == "半顺") {
            $enName = "BANSHUN";
        } elseif ($name == "杂六") {
            $enName = "ZALIU";
        } elseif ($name == "大") {
            $enName = "OVER";
        } elseif ($name == "小") {
            $enName = "UNDER";
        } elseif ($name == "单") {
            $enName = "ODD";
        } elseif ($name == "双") {
            $enName = "EVEN";
        } elseif ($name == "和单") {
            $enName = "SUM:ODD";
        } elseif ($name == "和双") {
            $enName = "SUM:EVEN";
        } elseif ($name == "尾大") {
            $enName = "LAST:OVER";
        } elseif ($name == "尾小") {
            $enName = "LAST:UNDER";
        }
        return $enName;
    }



    //六合彩单双
    static function lhc_Ds($ball)
    {
        if ($ball % 2 == 0) {
            return '双';
        } else {
            return '单';
        }
    }
    //六合彩大小
    static function lhc_Dx($ball)
    {
        if ($ball >= 25) {
            return '大';
        } else {
            return '小';
        }
    }
    //六合彩尾数大小
    static function lhc_WsDx($ball)
    {
        $wsdx = substr($ball, -1);
        if ($wsdx > 4) {
            return '尾大';
        } else {
            return '尾小';
        }
    }
    //六合彩合数单双
    static function lhc_HsDs($ball)
    {
        $ball = BuLing($ball);
        $a = substr($ball, 0, 1);
        $b = substr($ball, -1);
        $c = $a + $b;
        if ($c % 2 == 0) {
            return '和双';
        } else {
            return '和单';
        }
    }
    //六合彩合数大小
    static function lhc_HsDx($ball)
    {
        $ball = BuLing($ball);
        $a = substr($ball, 0, 1);
        $b = substr($ball, -1);
        $c = $a + $b;
        if ($c >= 7) {
            return '和大';
        } else {
            return '和小';
        }
    }
    //六合彩合数红绿蓝
    static function lhc_rgb($ball)
    {
        if (BuLing($ball) == '01' || BuLing($ball) == '02' || BuLing($ball) == '12' || BuLing($ball) == '13' || BuLing($ball) == '23' || BuLing($ball) == '24' || BuLing($ball) == '34' || BuLing($ball) == '35' || BuLing($ball) == '45' || BuLing($ball) == '46' || BuLing($ball) == '07' || BuLing($ball) == '08' || BuLing($ball) == '18' || BuLing($ball) == '19' || BuLing($ball) == '29' || BuLing($ball) == '30' || BuLing($ball) == '40') {
            $color = '红波';
        } else if (BuLing($ball) == '11' || BuLing($ball) == '21' || BuLing($ball) == '22' || BuLing($ball) == '32' || BuLing($ball) == '33'     || BuLing($ball) == '43' || BuLing($ball) == '44' || BuLing($ball) == '05' || BuLing($ball) == '06' || BuLing($ball) == '16' || BuLing($ball) == '17' || BuLing($ball) == '27' || BuLing($ball) == '28' || BuLing($ball) == '38' || BuLing($ball) == '39' || BuLing($ball) == '49') {
            $color = '绿波';
        } else {
            $color = '蓝波';
        }
        return $color;
    }
    //六合彩总和大小
    static function lhc_sum_dx($num)
    {
        $zh = $num[0] + $num[1] + $num[2] + $num[3] + $num[4] + $num[5] + $num[6];
        if ($zh >= 175) {
            return '总和 大';
        } else {
            return '总和 小';
        }
    }
    //六合彩总和单双
    static function lhc_sum_ds($num)
    {
        $zh = $num[0] + $num[1] + $num[2] + $num[3] + $num[4] + $num[5] + $num[6];
        if ($zh % 2 == 0) {
            return '总和 双';
        } else {
            return '总和 单';
        }
    }
    //六合彩 生肖
    static function lhc_sum_sx($ball, $dateTime = "2015-02-19 00:00:01")
    {
        $animal = "";
        if (strtotime("2015-02-19 00:00:01") - strtotime($dateTime) > 0) { //马年
            if (in_array(BuLing($ball), array("08", "20", "32", "44"))) {
                $animal =  '猪';
            } elseif (in_array(BuLing($ball), array("07", "19", "31", "43"))) {
                $animal =  '鼠';
            } elseif (in_array(BuLing($ball), array("06", "18", "30", "42"))) {
                $animal =  '牛';
            } elseif (in_array(BuLing($ball), array("05", "17", "29", "41"))) {
                $animal =  '虎';
            } elseif (in_array(BuLing($ball), array("04", "16", "28", "40"))) {
                $animal =  '兔';
            } elseif (in_array(BuLing($ball), array("03", "15", "27", "39"))) {
                $animal =  '龙';
            } elseif (in_array(BuLing($ball), array("02", "14", "26", "38"))) {
                $animal =  '蛇';
            } elseif (in_array(BuLing($ball), array("01", "13", "25", "37", "49"))) {
                $animal =  '马';
            } elseif (in_array(BuLing($ball), array("12", "24", "36", "48"))) {
                $animal =  '羊';
            } elseif (in_array(BuLing($ball), array("11", "23", "35", "47"))) {
                $animal =  '猴';
            } elseif (in_array(BuLing($ball), array("10", "22", "34", "46"))) {
                $animal =  '鸡';
            } elseif (in_array(BuLing($ball), array("09", "21", "33", "45"))) {
                $animal =  '狗';
            }
        } else {
            if (in_array(BuLing($ball), array("07", "19", "31", "43"))) {
                $animal =  '牛';
            } elseif (in_array(BuLing($ball), array("06", "18", "30", "42"))) {
                $animal =  '虎';
            } elseif (in_array(BuLing($ball), array("05", "17", "29", "41"))) {
                $animal =  '兔';
            } elseif (in_array(BuLing($ball), array("04", "16", "28", "40"))) {
                $animal =  '龙';
            } elseif (in_array(BuLing($ball), array("03", "15", "27", "39"))) {
                $animal =  '蛇';
            } elseif (in_array(BuLing($ball), array("02", "14", "26", "38"))) {
                $animal =  '马';
            } elseif (in_array(BuLing($ball), array("01", "13", "25", "37", "49"))) {
                $animal =  '羊';
            } elseif (in_array(BuLing($ball), array("12", "24", "36", "48"))) {
                $animal =  '猴';
            } elseif (in_array(BuLing($ball), array("11", "23", "35", "47"))) {
                $animal =  '鸡';
            } elseif (in_array(BuLing($ball), array("10", "22", "34", "46"))) {
                $animal =  '狗';
            } elseif (in_array(BuLing($ball), array("09", "21", "33", "45"))) {
                $animal =  '猪';
            } elseif (in_array(BuLing($ball), array("08", "20", "32", "44"))) {
                $animal =  '鼠';
            }
        }
        return $animal;
    }
    static function lhc_sx_number($ball1, $ball2, $ball3, $ball4, $ball5, $ball6, $ball7, $type)
    {
        $animal_number = 0;
        $ballArray = array($ball1, $ball2, $ball3, $ball4, $ball5, $ball6, $ball7);
        if (in_array('鼠', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('牛', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('虎', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('兔', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('龙', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('蛇', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('马', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('羊', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('猴', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('鸡', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('狗', $ballArray)) {
            $animal_number += 1;
        }
        if (in_array('猪', $ballArray)) {
            $animal_number += 1;
        }
        $sx_string = "";
        if ($type == "1") {
            if ($animal_number % 2 == 0) {
                $sx_string = "总肖双";
            } else {
                $sx_string = "总肖单";
            }
        } elseif ($type == "2") {
            if ($animal_number == 2 || $animal_number == 3 || $animal_number == 4) {
                $sx_string = "234肖";
            } elseif ($animal_number == 5) {
                $sx_string = "5肖";
            } elseif ($animal_number == 6) {
                $sx_string = "6肖";
            } elseif ($animal_number == 7) {
                $sx_string = "7肖";
            }
        }

        return $sx_string;
    }
    static function lhc_head($ball)
    {
        $a = substr($ball, 0, 1);
        $head = "";
        if ($a == "0" || $ball < 10) {
            $head = "头0";
        } elseif ($a == "1") {
            $head = "头1";
        } elseif ($a == "2") {
            $head = "头2";
        } elseif ($a == "3") {
            $head = "头3";
        } elseif ($a == "4") {
            $head = "头4";
        }
        return $head;
    }
    static function lhc_tail($ball)
    {
        $b = substr($ball, -1);
        $tail = "";
        if ($b == "0") {
            $tail = "尾0";
        } elseif ($b == "1") {
            $tail = "尾1";
        } elseif ($b == "2") {
            $tail = "尾2";
        } elseif ($b == "3") {
            $tail = "尾3";
        } elseif ($b == "4") {
            $tail = "尾4";
        } elseif ($b == "5") {
            $tail = "尾5";
        } elseif ($b == "6") {
            $tail = "尾6";
        } elseif ($b == "7") {
            $tail = "尾7";
        } elseif ($b == "8") {
            $tail = "尾8";
        } elseif ($b == "9") {
            $tail = "尾9";
        }
        return $tail;
    }

    static function lhc_c7($ball1, $ball2, $ball3, $ball4, $ball5, $ball6, $ball7)
    {
        $c7 = "";
        $r = 0;
        $g = 0;
        $b = 0;
        if ($ball1 == "红波") {
            $r += 1;
        } elseif ($ball1 == "绿波") {
            $g += 1;
        } elseif ($ball1 == "蓝波") {
            $b += 1;
        }
        if ($ball2 == "红波") {
            $r += 1;
        } elseif ($ball2 == "绿波") {
            $g += 1;
        } elseif ($ball2 == "蓝波") {
            $b += 1;
        }
        if ($ball3 == "红波") {
            $r += 1;
        } elseif ($ball3 == "绿波") {
            $g += 1;
        } elseif ($ball3 == "蓝波") {
            $b += 1;
        }
        if ($ball4 == "红波") {
            $r += 1;
        } elseif ($ball4 == "绿波") {
            $g += 1;
        } elseif ($ball4 == "蓝波") {
            $b += 1;
        }
        if ($ball5 == "红波") {
            $r += 1;
        } elseif ($ball5 == "绿波") {
            $g += 1;
        } elseif ($ball5 == "蓝波") {
            $b += 1;
        }
        if ($ball6 == "红波") {
            $r += 1;
        } elseif ($ball6 == "绿波") {
            $g += 1;
        } elseif ($ball6 == "蓝波") {
            $b += 1;
        }
        if ($ball7 == "红波") {
            $r += 1.5;
        } elseif ($ball7 == "绿波") {
            $g += 1.5;
        } elseif ($ball7 == "蓝波") {
            $b += 1.5;
        }
        if (($r == 3 && $g == 3 && $b == 1.5) || ($r == 3 && $g == 1.5 && $b == 3) || ($r == 1.5 && $g == 3 && $b == 3)) {
            $c7 = "正肖 和局";
        } elseif ($r > $b && $r > $g) {
            $c7 = "正肖 红波";
        } elseif ($b > $r && $b > $g) {
            $c7 = "正肖 蓝波";
        } elseif ($g > $b && $g > $r) {
            $c7 = "正肖 绿波";
        }
        return $c7;
    }
    //六合彩 半半波
    static function lhc_bbb($ball)
    {
        $bbbString = "";
        if (in_array(BuLing($ball), array("29", "35", "45"))) {
            $bbbString =  '红大单';
        } elseif (in_array(BuLing($ball), array("30", "34", "40", "46"))) {
            $bbbString =  '红大双';
        } elseif (in_array(BuLing($ball), array("01", "07", "13", "19", "23"))) {
            $bbbString =  '红小单';
        } elseif (in_array(BuLing($ball), array("02", "08", "12", "18", "24"))) {
            $bbbString =  '红小双';
        } elseif (in_array(BuLing($ball), array("27", "33", "39", "43"))) {
            $bbbString =  '绿大单';
        } elseif (in_array(BuLing($ball), array("28", "32", "38", "44"))) {
            $bbbString =  '绿大双';
        } elseif (in_array(BuLing($ball), array("05", "11", "17", "21"))) {
            $bbbString =  '绿小单';
        } elseif (in_array(BuLing($ball), array("06", "16", "22"))) {
            $bbbString =  '绿小双';
        } elseif (in_array(BuLing($ball), array("25", "31", "37", "41", "47"))) {
            $bbbString =  '蓝大单';
        } elseif (in_array(BuLing($ball), array("26", "36", "42", "48"))) {
            $bbbString =  '蓝大双';
        } elseif (in_array(BuLing($ball), array("03", "09", "15"))) {
            $bbbString =  '蓝小单';
        } elseif (in_array(BuLing($ball), array("04", "10", "14", "20"))) {
            $bbbString =  '蓝小双';
        }
        return $bbbString;
    }
    static function lhc_bb_ds($bbbString)
    {
        $bbString = "";
        if ($bbbString == "红大单" || $bbbString ==  "红小单") {
            $bbString = "红单";
        } elseif ($bbbString == "红大双" || $bbbString ==  "红小双") {
            $bbString = "红双";
        } elseif ($bbbString == "绿大单" || $bbbString ==  "绿小单") {
            $bbString = "绿单";
        } elseif ($bbbString == "绿大双" || $bbbString ==  "绿小双") {
            $bbString = "绿双";
        } elseif ($bbbString == "蓝大单" || $bbbString ==  "蓝小单") {
            $bbString = "蓝单";
        } elseif ($bbbString == "蓝大双" || $bbbString ==  "蓝小双") {
            $bbString = "蓝双";
        }
        return $bbString;
    }
    static function lhc_bb_dx($bbbString)
    {
        $bbString = "";
        if ($bbbString == "红大双" || $bbbString ==  "红大单") {
            $bbString = "红大";
        } elseif ($bbbString == "红小双" || $bbbString ==  "红小单") {
            $bbString = "红小";
        } elseif ($bbbString == "绿大双" || $bbbString ==  "绿大单") {
            $bbString = "绿大";
        } elseif ($bbbString == "绿小双" || $bbbString ==  "绿小单") {
            $bbString = "绿小";
        } elseif ($bbbString == "蓝大双" || $bbbString ==  "蓝大单") {
            $bbString = "蓝大";
        } elseif ($bbbString == "蓝小双" || $bbbString ==  "蓝小单") {
            $bbString = "蓝小";
        }
        return $bbString;
    }


    //求3个数中最大数
    static function get_max($a, $b, $c)
    {
        return $a > $b ? ($a > $c ? $a : $c) : ($b > $c ? $b : $c);
    }

    //求3个数中最小数
    static function get_min($a, $b, $c)
    {
        return $a < $b ? ($a < $c ? $a : $c) : ($b < $c ? $b : $c);
    }


    /*
    数字补0函数，当数字小于10的时候在前面自动补0
    */
    static function BuLing($num)
    {
        if ($num < 10) {
            $num = '0' . $num;
        }
        return $num;
    }

    static function getOrders($lotteryType, $qishu)
    {
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->where("o.Gtype", $lotteryType)->where("o.lottery_number", $qishu)
            ->select("o.order_num", "o.user_id", "o.lottery_number AS qishu", "o.bet_info", "o.bet_money AS bet_monel_total", "o.win AS win_total", "o.status AS order_status", "o_sub.number", "o_sub.bet_rate", "o_sub.bet_money", "o_sub.win", "o_sub.fs", "o_sub.status AS sub_status")
            ->get();
        return $result;
    }
    static function getOrdersByStatus($lotteryType, $qishu, $status)
    {
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->where("o.Gtype", $lotteryType)->where("o.lottery_number", $qishu)
            ->where("o.status", $status)->where("o_sub.status", $status)
            ->select("o.id", "o.order_num", "o.user_id", "o.lottery_number AS qishu", "o.bet_info", "o.rtype_str", "o.bet_money AS bet_monel_total", "o.win AS win_total", "o.status AS order_status", "o_sub.number", "o_sub.bet_rate", "o_sub.bet_money", "o_sub.win", "o_sub.fs", "o_sub.order_sub_num", "o_sub.quick_type", "o_sub.id AS sub_id", "o_sub.status AS sub_status", "o_sub.is_win")
            ->get();
        return $result;
    }

    static function getOrdersJs($lotteryType, $qishu)
    {
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->where("o.Gtype", $lotteryType)->where("o.lottery_number", $qishu)
            ->whereIn("o.status", ["1", "2"])
            ->select("o.id", "o.order_num", "o.user_id", "o.lottery_number AS qishu", "o.bet_info", "o.rtype_str", "o.bet_money AS bet_monel_total", "o.win AS win_total", "o.status AS order_status", "o_sub.number", "o_sub.bet_rate", "o_sub.bet_money", "o_sub.win", "o_sub.fs", "o_sub.order_sub_num", "o_sub.quick_type", "o_sub.id AS sub_id", "o_sub.status AS sub_status", "o_sub.is_win")
            ->get();
        return $result;
    }


    static function getOneDayOrder($user_id, $day, $gType, $statusString = "")
    {
        $oneDayStart = $day . ' 00:00:00';
        $oneDayEnd = $day . ' 23:59:59';
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("COUNT(o_sub.id) AS bet_count,IFNULL(SUM(IFNULL(o_sub.bet_money,0)),0) AS bet_money"))
            ->where("o.Gtype", $gType)->where("o.user_id", $user_id)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd]);
        if ($statusString != "") {
            $result = $result->where("o_sub.status", $statusString);
        }
        $result = $result->first();
        return $result;
    }

    static function getOneDayTotalCount($user_id, $day, $statusString = "")
    {
        $oneDayStart = $day . ' 00:00:00';
        $oneDayEnd = $day . ' 23:59:59';
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("COUNT(o_sub.id) AS bet_count,IFNULL(SUM(IFNULL(o_sub.bet_money,0)),0) AS bet_money"))
            ->where("o.user_id", $user_id)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd]);
        if ($statusString != "") {
            $result = $result->where("o_sub.status", $statusString);
        }
        $result = $result->first();
        return $result;
    }

    static function getOneDayTotalCountByType($user_id, $day, $gType, $statusString = "")
    {
        $oneDayStart = $day . ' 00:00:00';
        $oneDayEnd = $day . ' 23:59:59';
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("COUNT(o_sub.id) AS bet_count,IFNULL(SUM(IFNULL(o_sub.bet_money,0)),0) AS bet_money"))
            ->where("o.Gtype", $gType)->where("o.user_id", $user_id)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd]);
        if ($statusString != "") {
            $result = $result->where("o_sub.status", $statusString);
        }
        $result = $result->first();
        return $result;
    }

    static function getOneDayTotalWin($user_id, $day)
    {
        $oneDayStart = $day . ' 00:00:00';
        $oneDayEnd = $day . ' 23:59:59';
        $winTotal = 0;
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.win,0)+IFNULL(o_sub.fs,0)),0) AS win_money"))
            ->where("o_sub.is_win", 1)->where("o.user_id", $user_id)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->first();

        $winTotal = $result->win_money;

        $result1 = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.fs,0)),0) AS win_fs"))
            ->where("o_sub.is_win", 0)->where("o.user_id", $user_id)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->first();

        $winTotal += $result1->win_fs;

        $result2 = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.bet_money,0)),0) AS win_back"))
            ->where("o.user_id", $user_id)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where(function ($query) {
                $query->where('o_sub.is_win', 2)
                    ->orWhere('o_sub.status', 3);
            })
            ->first();

        $winTotal += $result2->win_back;

        return $winTotal;
    }

    static function getOneDayTotalWinByType($user_id, $day, $gType)
    {
        $oneDayStart = $day . ' 00:00:00';
        $oneDayEnd = $day . ' 23:59:59';
        $winTotal = 0;
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.win,0)+IFNULL(o_sub.fs,0)),0) AS win_money"))
            ->where("o_sub.is_win", 1)->where("o.user_id", $user_id)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where("o.Gtype", $gType)
            ->first();

        $winTotal = $result->win_money;

        $result1 = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.fs,0)),0) AS win_fs"))
            ->where("o_sub.is_win", 0)->where("o.user_id", $user_id)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where("o.Gtype", $gType)
            ->first();

        $winTotal += $result1->win_fs;

        $result2 = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.bet_money,0)),0) AS win_back"))
            ->where("o.user_id", $user_id)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where("o.Gtype", $gType)
            ->where(function ($query) {
                $query->where('o_sub.is_win', 2)
                    ->orWhere('o_sub.status', 3);
            })
            ->first();

        $winTotal += $result2->win_back;

        return $winTotal;
    }

    static function getBetMoneyAndCount($dayStart, $dayEnd, $gType, $user_group = "", $statusString = "")
    {
        $oneDayStart = $dayStart . ' 00:00:00';
        $oneDayEnd = $dayEnd . ' 23:59:59';
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("COUNT(o_sub.id) AS bet_count,IFNULL(SUM(IFNULL(o_sub.bet_money,0)),0) AS bet_money"))
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where("o.Gtype", $gType);

        if ($user_group != "") {
            $user_group = explode(",", $user_group);
            $result = $result->whereIn("o.user_id", $user_group);
        }
        if ($statusString != "") {
            $result = $result->whereRaw($statusString);
        }
        return $result->first();
    }

    static function getTotalWin2($dayStart, $dayEnd, $gType, $user_group = "", $statusString = "")
    {
        $oneDayStart = $dayStart . ' 00:00:00';
        $oneDayEnd = $dayEnd . ' 23:59:59';
        $winTotal = 0;
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.win,0)+IFNULL(o_sub.fs,0)),0) AS win_money"))
            ->where("o_sub.is_win", 1)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where("o.Gtype", $gType);

        if ($user_group != "") {
            $user_group = explode(",", $user_group);
            $result = $result->whereIn("o.user_id", $user_group);
        }
        if ($statusString != "") {
            $result = $result->where("o_sub.status", $statusString);
        }

        $result = $result->first();

        $winTotal = $result->win_money;

        $result2 = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.fs,0)),0) AS fanshui"))
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where("o.Gtype", $gType)
            ->where(function ($query) {
                $query->where('o_sub.status', 1)
                    ->orWhere('o_sub.status', 2);
            });

        if ($user_group != "") {
            $user_group = explode(",", $user_group);
            $result2 = $result2->whereIn("o.user_id", $user_group);
        }
        if ($statusString != "") {
            $result2 = $result2->where("o_sub.status", $statusString);
        }

        $result2 = $result2->first();

        $winTotal += $result2->fanshui;

        return $winTotal;
    }

    static function getTotalWin($dayStart, $dayEnd, $gType, $user_group = "", $statusString = "")
    {
        $oneDayStart = $dayStart . ' 00:00:00';
        $oneDayEnd = $dayEnd . ' 23:59:59';
        $winTotal = 0;
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.win,0)+IFNULL(o_sub.fs,0)),0) AS win_money"))
            ->where("o_sub.is_win", 1)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where("o.Gtype", $gType);

        if ($user_group != "") {
            $user_group = explode(",", $user_group);
            $result = $result->whereIn("o.user_id", $user_group);
        }
        if ($statusString != "") {
            $result = $result->where("o_sub.status", $statusString);
        }

        $result = $result->first();

        $winTotal = $result->win_money;

        $result1 = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.fs,0)),0) AS win_fs"))
            ->where("o_sub.is_win", 0)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where("o.Gtype", $gType);

        if ($user_group != "") {
            $user_group = explode(",", $user_group);
            $result1 = $result1->whereIn("o.user_id", $user_group);
        }
        if ($statusString != "") {
            $result1 = $result1->where("o_sub.status", $statusString);
        }

        $result1 = $result1->first();

        $winTotal = $result1->win_fs;

        $result2 = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.bet_money,0)),0) AS win_back"))
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where("o.Gtype", $gType)
            ->where(function ($query) {
                $query->where('o_sub.is_win', 2)
                    ->orWhere('o_sub.status', 3);
            });

        if ($user_group != "") {
            $user_group = explode(",", $user_group);
            $result2 = $result2->whereIn("o.user_id", $user_group);
        }
        if ($statusString != "") {
            $result2 = $result2->where("o_sub.status", $statusString);
        }

        $result2 = $result2->first();

        $winTotal += $result2->win_back;

        return $winTotal;
    }


    static function getAllBetMoneyAndCount($dayStart, $dayEnd, $user_group = "", $statusString = "")
    {
        $oneDayStart = $dayStart . ' 00:00:00';
        $oneDayEnd = $dayEnd . ' 23:59:59';
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("COUNT(o_sub.id) AS bet_count, IFNULL(SUM(IFNULL(o_sub.bet_money,0)),0) AS bet_money"))
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd]);

        if ($user_group != "") {
            $user_group = explode(",", $user_group);
            $result = $result->whereIn("o.user_id", $user_group);
        }
        if ($statusString != "") {
            $result = $result->where("o_sub.status", $statusString);
        }
        return $result->first();
    }

    static function getAllTotalWin($dayStart, $dayEnd, $user_group = "", $statusString = "")
    {

        $oneDayStart = $dayStart . ' 00:00:00';
        $oneDayEnd = $dayEnd . ' 23:59:59';
        $winTotal = 0;
        $result = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.win,0)+IFNULL(o_sub.fs,0)),0) AS win_money"))
            ->where("o_sub.is_win", 1)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd]);

        if ($user_group != "") {
            $user_group = explode(",", $user_group);
            $result = $result->whereIn("o.user_id", $user_group);
        }
        if ($statusString != "") {
            $result = $result->where("o_sub.status", $statusString);
        }

        $result = $result->first();

        $winTotal = $result->win_money;

        $result1 = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.fs,0)),0) AS win_fs"))
            ->where("o_sub.is_win", 0)
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd]);

        if ($user_group != "") {
            $user_group = explode(",", $user_group);
            $result1 = $result1->whereIn("o.user_id", $user_group);
        }
        if ($statusString != "") {
            $result1 = $result1->where("o_sub.status", $statusString);
        }

        $result1 = $result1->first();

        $winTotal = $result1->win_fs;

        $result2 = DB::table('order_lottery as o')
            ->join('order_lottery_sub as o_sub', 'o.order_num', '=', 'o_sub.order_num')
            ->select(DB::raw("IFNULL(SUM(IFNULL(o_sub.bet_money,0)),0) AS win_back"))
            ->whereBetween("o.bet_time", [$oneDayStart, $oneDayEnd])
            ->where(function ($query) {
                $query->where('o_sub.is_win', 2)
                    ->orWhere('o_sub.status', 3);
            });

        if ($user_group != "") {
            $user_group = explode(",", $user_group);
            $result2 = $result2->whereIn("o.user_id", $user_group);
        }
        if ($statusString != "") {
            $result2 = $result2->where("o_sub.status", $statusString);
        }

        $result2 = $result2->first();

        $winTotal += $result2->win_back;

        return $winTotal;
    }

    static function getName($contentName, $gType, $rTypeName = "", $quickType = "")
    {
        $name = $contentName;
        if (strpos($rTypeName, "快速-") !== false) {
            $name = $quickType . "-" . $contentName;
            return $name;
        }
        if ($gType == "GD11") {
            $name = getNameGd11($contentName);
        } elseif ($gType == "BJPK") {
            $name = getNameBJPK($contentName);
        } elseif ($gType == "BJKN") {
            $name = getNameBJKN($contentName);
        } elseif ($gType == "GXSF") {
            $name = getNameGXSF($contentName);
        } elseif ($gType == "GDSF") {
            $name = getNameGDSF($contentName);
        } elseif ($gType == "TJSF") {
            $name = getNameTJSF($contentName);
        } elseif ($gType == "CQ" || $gType == "TJ" || $gType == "JX") {
            $name = getNameb5($contentName);
        } elseif ($gType == "D3" || $gType == "P3" || $gType == "T3") {
            $name = getNameB3($contentName);
        }
        return $name;
    }

    static function getNameGd11($contentName)
    {
        $number = $contentName;
        $betInfo = explode(":", $contentName);
        $name_gd11 = $contentName;

        if ($betInfo[1] == "LOCATE") { //每球定位
            $selectBall = $betInfo[2];
            if ($selectBall == "1") {
                $name_gd11 = "正码一 " . $betInfo[0];
            } elseif ($selectBall == "2") {
                $name_gd11 = "正码二 " . $betInfo[0];
            } elseif ($selectBall == "3") {
                $name_gd11 = "正码三 " . $betInfo[0];
            } elseif ($selectBall == "4") {
                $name_gd11 = "正码四 " . $betInfo[0];
            } elseif ($selectBall == "5") {
                $name_gd11 = "正码五 " . $betInfo[0];
            }
        } elseif ($betInfo[1] == "MATCH") {
            $name_gd11 = $betInfo[0];
        } elseif ($betInfo[0] == "TOTAL") {
            if ($betInfo[1] == "OVER") {
                $name_gd11 = "总和大";
            } elseif ($betInfo[1] == "UNDER") {
                $name_gd11 = "总和小";
            } elseif ($betInfo[1] == "ODD") {
                $name_gd11 = "总和单";
            } elseif ($betInfo[1] == "EVEN") {
                $name_gd11 = "总和双";
            } elseif ($betInfo[1] == "DRAGON") {
                $name_gd11 = "龙";
            } elseif ($betInfo[1] == "TIGER") {
                $name_gd11 = "虎";
            } elseif ($betInfo[1] == "TIE") {
                $name_gd11 = "和";
            }
        } elseif ($betInfo[0] == "BEFORE" || $betInfo[0] == "MIDDLE" || $betInfo[0] == "AFTER") {
            if ($number == "BEFORE:SHUNZI") {
                $name_gd11 = "前三 顺子";
            } elseif ($number == "BEFORE:BANSHUN") {
                $name_gd11 = "前三 半顺";
            } elseif ($number == "BEFORE:ZALIU") {
                $name_gd11 = "前三 杂六";
            } elseif ($number == "MIDDLE:SHUNZI") {
                $name_gd11 = "中三 顺子";
            } elseif ($number == "MIDDLE:BANSHUN") {
                $name_gd11 = "中三 半顺";
            } elseif ($number == "MIDDLE:ZALIU") {
                $name_gd11 = "中三 杂六";
            } elseif ($number == "AFTER:SHUNZI") {
                $name_gd11 = "后三 顺子";
            } elseif ($number == "AFTER:BANSHUN") {
                $name_gd11 = "后三 半顺";
            } elseif ($number == "AFTER:ZALIU") {
                $name_gd11 = "后三 杂六";
            }
        } else {
            if ($betInfo[0] == "1") {
                $name_gd11_pre = "正码一 ";
            } elseif ($betInfo[0] == "2") {
                $name_gd11_pre = "正码二 ";
            } elseif ($betInfo[0] == "3") {
                $name_gd11_pre = "正码三 ";
            } elseif ($betInfo[0] == "4") {
                $name_gd11_pre = "正码四 ";
            } elseif ($betInfo[0] == "5") {
                $name_gd11_pre = "正码五 ";
            }
            if ($betInfo[1] == "OVER") {
                $name_gd11 = $name_gd11_pre . "大";
            } elseif ($betInfo[1] == "UNDER") {
                $name_gd11 = $name_gd11_pre . "小";
            } elseif ($betInfo[1] == "ODD") {
                $name_gd11 = $name_gd11_pre . "单";
            } elseif ($betInfo[1] == "EVEN") {
                $name_gd11 = $name_gd11_pre . "双";
            } elseif ($betInfo[1] . ":" . $betInfo[2] == "SUM:ODD") {
                $name_gd11 = $name_gd11_pre . "和单";
            } elseif ($betInfo[1] . ":" . $betInfo[2] == "SUM:EVEN") {
                $name_gd11 = $name_gd11_pre . "和双";
            } elseif ($betInfo[1] . ":" . $betInfo[2] == "LAST:OVER") {
                $name_gd11 = $name_gd11_pre . "尾大";
            } elseif ($betInfo[1] . ":" . $betInfo[2] == "LAST:UNDER") {
                $name_gd11 = $name_gd11_pre . "尾小";
            }
        }

        return $name_gd11;
    }

    static function getNameBJPK($contentName)
    {
        $betInfo = explode(":", $contentName);
        $name_bjpk = $contentName;

        if ($betInfo[1] == "LOCATE") { //每球定位
            $selectBall = $betInfo[2];
            if ($selectBall == "1") {
                $name_bjpk = "冠军 " . $betInfo[0];
            } elseif ($selectBall == "2") {
                $name_bjpk = "亚军 " . $betInfo[0];
            } elseif ($selectBall == "3") {
                $name_bjpk = "季军 " . $betInfo[0];
            } elseif ($selectBall == "4") {
                $name_bjpk = "第四名 " . $betInfo[0];
            } elseif ($selectBall == "5") {
                $name_bjpk = "第五名 " . $betInfo[0];
            } elseif ($selectBall == "6") {
                $name_bjpk = "第六名 " . $betInfo[0];
            } elseif ($selectBall == "7") {
                $name_bjpk = "第七名 " . $betInfo[0];
            } elseif ($selectBall == "8") {
                $name_bjpk = "第八名 " . $betInfo[0];
            } elseif ($selectBall == "9") {
                $name_bjpk = "第九名 " . $betInfo[0];
            } elseif ($selectBall == "10") {
                $name_bjpk = "第十名 " . $betInfo[0];
            }
        } elseif ($betInfo[0] > 0) {
            $selectBall = $betInfo[0];
            if ($selectBall == "1") {
                if ($betInfo[2]) {
                    $name_bjpk = "冠军 " . getCommonName($betInfo[2]);
                } else {
                    $name_bjpk = "冠军 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "2") {
                if ($betInfo[2]) {
                    $name_bjpk = "亚军 " . getCommonName($betInfo[2]);
                } else {
                    $name_bjpk = "亚军 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "3") {
                if ($betInfo[2]) {
                    $name_bjpk = "季军 " . getCommonName($betInfo[2]);
                } else {
                    $name_bjpk = "季军 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "4") {
                if ($betInfo[2]) {
                    $name_bjpk = "第四名 " . getCommonName($betInfo[2]);
                } else {
                    $name_bjpk = "第四名 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "5") {
                if ($betInfo[2]) {
                    $name_bjpk = "第五名 " . getCommonName($betInfo[2]);
                } else {
                    $name_bjpk = "第五名 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "6") {
                if ($betInfo[2]) {
                    $name_bjpk = "第六名 " . getCommonName($betInfo[2]);
                } else {
                    $name_bjpk = "第六名 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "7") {
                if ($betInfo[2]) {
                    $name_bjpk = "第七名 " . getCommonName($betInfo[2]);
                } else {
                    $name_bjpk = "第七名 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "8") {
                if ($betInfo[2]) {
                    $name_bjpk = "第八名 " . getCommonName($betInfo[2]);
                } else {
                    $name_bjpk = "第八名 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "9") {
                if ($betInfo[2]) {
                    $name_bjpk = "第九名 " . getCommonName($betInfo[2]);
                } else {
                    $name_bjpk = "第九名 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "10") {
                if ($betInfo[2]) {
                    $name_bjpk = "第十名 " . getCommonName($betInfo[2]);
                } else {
                    $name_bjpk = "第十名 " . getCommonName($betInfo[1]);
                }
            }
        } elseif ("SUM:FIRST:2" == $betInfo[0] . ":" . $betInfo[1] . ":" . $betInfo[2]) {
            if ($betInfo[3] == "OVER") {
                $name_bjpk = "和大";
            } elseif ($betInfo[3] == "UNDER") {
                $name_bjpk = "和小";
            } elseif ($betInfo[3] == "ODD") {
                $name_bjpk = "和单";
            } elseif ($betInfo[3] == "EVEN") {
                $name_bjpk = "和双";
            } else {
                $name_bjpk = substr($contentName, 15);
            }
        }

        return $name_bjpk;
    }

    static function getNameBJKN($contentName)
    {
        $name_bjkn = $contentName;

        if ($contentName == "ALL:SUM:ODD") {
            $name_bjkn = "和单";
        } elseif ($contentName == "ALL:SUM:EVEN") {
            $name_bjkn = "和双";
        } elseif ($contentName == "ALL:SUM:OVER") {
            $name_bjkn = "和大";
        } elseif ($contentName == "ALL:SUM:UNDER") {
            $name_bjkn = "和小";
        } elseif ($contentName == "ALL:SUM:810") {
            $name_bjkn = "和 810";
        } elseif ($contentName == "TOP") {
            $name_bjkn = "上盘";
        } elseif ($contentName == "MIDDLE") {
            $name_bjkn = "中盘";
        } elseif ($contentName == "BOTTOM") {
            $name_bjkn = "下盘";
        } elseif ($contentName == "ODD") {
            $name_bjkn = "奇盘";
        } elseif ($contentName == "TIE") {
            $name_bjkn = "和盘";
        } elseif ($contentName == "EVEN") {
            $name_bjkn = "偶盘";
        } elseif ($contentName == "ALL:SUM:METAL") {
            $name_bjkn = "金";
        } elseif ($contentName == "ALL:SUM:WOOD") {
            $name_bjkn = "木";
        } elseif ($contentName == "ALL:SUM:WATER") {
            $name_bjkn = "水";
        } elseif ($contentName == "ALL:SUM:FIRE") {
            $name_bjkn = "火";
        } elseif ($contentName == "ALL:SUM:EARTH") {
            $name_bjkn = "土";
        } elseif ($contentName == "ALL:SUM:UNDER:ODD") {
            $name_bjkn = "小单";
        } elseif ($contentName == "ALL:SUM:UNDER:EVEN") {
            $name_bjkn = "小双";
        } elseif ($contentName == "ALL:SUM:OVER:ODD") {
            $name_bjkn = "大单";
        } elseif ($contentName == "ALL:SUM:OVER:EVEN") {
            $name_bjkn = "大双";
        }

        return $name_bjkn;
    }

    static function getNameGXSF($contentName)
    {
        $betInfo = explode(":", $contentName);
        $name_gxsf = $contentName;

        if ($betInfo[1] == "LOCATE") { //每球定位
            $selectBall = $betInfo[2];
            if ($selectBall == "1") {
                $name_gxsf = "正码一 " . $betInfo[0];
            } elseif ($selectBall == "2") {
                $name_gxsf = "正码二 " . $betInfo[0];
            } elseif ($selectBall == "3") {
                $name_gxsf = "正码三 " . $betInfo[0];
            } elseif ($selectBall == "4") {
                $name_gxsf = "正码四 " . $betInfo[0];
            } elseif ($selectBall == "S") {
                $name_gxsf = "特别号 " . $betInfo[0];
            }
        } elseif ($betInfo[1] == "MATCH") {
            $name_gxsf = $betInfo[0];
        } elseif ($betInfo[0] > 0 || $betInfo[0] == "S") {
            $selectBall = $betInfo[0];
            if ($selectBall == "1") {
                if (count($betInfo) == 4) {
                    $name_gxsf = "正码一 " . getCommonName($betInfo[1] . ":" . $betInfo[3]);
                } elseif ($betInfo[2]) {
                    $name_gxsf = "正码一 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gxsf = "正码一 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "2") {
                if (count($betInfo) == 4) {
                    $name_gxsf = "正码二 " . getCommonName($betInfo[1] . ":" . $betInfo[3]);
                } elseif ($betInfo[2]) {
                    $name_gxsf = "正码二 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gxsf = "正码二 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "3") {
                if (count($betInfo) == 4) {
                    $name_gxsf = "正码三 " . getCommonName($betInfo[1] . ":" . $betInfo[3]);
                } elseif ($betInfo[2]) {
                    $name_gxsf = "正码三 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gxsf = "正码三 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "4") {
                if (count($betInfo) == 4) {
                    $name_gxsf = "正码四 " . getCommonName($betInfo[1] . ":" . $betInfo[3]);
                } elseif ($betInfo[2]) {
                    $name_gxsf = "正码四 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gxsf = "正码四 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "S") {
                if (count($betInfo) == 4) {
                    $name_gxsf = "特别号 " . getCommonName($betInfo[1] . ":" . $betInfo[3]);
                } elseif ($betInfo[2]) {
                    $name_gxsf = "特别号 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gxsf = "特别号 " . getCommonName($betInfo[1]);
                }
            }
        }

        return $name_gxsf;
    }

    static function getNameGDSF($contentName)
    {
        $betInfo = explode(":", $contentName);
        $name_gdsf = $contentName;

        if ($betInfo[1] == "LOCATE") { //每球定位
            $selectBall = $betInfo[2];
            if ($selectBall == "1") {
                $name_gdsf = "第一球 " . $betInfo[0];
            } elseif ($selectBall == "2") {
                $name_gdsf = "第二球 " . $betInfo[0];
            } elseif ($selectBall == "3") {
                $name_gdsf = "第三球 " . $betInfo[0];
            } elseif ($selectBall == "4") {
                $name_gdsf = "第四球 " . $betInfo[0];
            } elseif ($selectBall == "5") {
                $name_gdsf = "第五球 " . $betInfo[0];
            } elseif ($selectBall == "6") {
                $name_gdsf = "第六球 " . $betInfo[0];
            } elseif ($selectBall == "7") {
                $name_gdsf = "第七球 " . $betInfo[0];
            } elseif ($selectBall == "S") {
                $name_gdsf = "第八球 " . $betInfo[0];
            }
        } elseif ($betInfo[1] == "MATCH") {
            $name_gdsf = $betInfo[0];
        } elseif ($betInfo[0] > 0 || $betInfo[0] == "S") {
            $selectBall = $betInfo[0];
            if ($selectBall == "1") {
                if ($betInfo[2]) {
                    $name_gdsf = "第一球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gdsf = "第一球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "2") {
                if ($betInfo[2]) {
                    $name_gdsf = "第二球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gdsf = "第二球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "3") {
                if ($betInfo[2]) {
                    $name_gdsf = "第三球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gdsf = "第三球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "4") {
                if ($betInfo[2]) {
                    $name_gdsf = "第四球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gdsf = "第四球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "5") {
                if ($betInfo[2]) {
                    $name_gdsf = "第五球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gdsf = "第五球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "6") {
                if ($betInfo[2]) {
                    $name_gdsf = "第六球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gdsf = "第六球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "7") {
                if ($betInfo[2]) {
                    $name_gdsf = "第七球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gdsf = "第七球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "S") {
                if ($betInfo[2]) {
                    $name_gdsf = "第八球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_gdsf = "第八球 " . getCommonName($betInfo[1]);
                }
            }
            if ($contentName == "1:S:DRAGON") {
                $name_gdsf = "龙";
            } elseif ($contentName == "1:S:TIGER") {
                $name_gdsf = "虎";
            }
        } else {
            if ($contentName == "ALL:SUM:OVER") {
                $name_gdsf = "总和大";
            } elseif ($contentName == "ALL:SUM:UNDER") {
                $name_gdsf = "总和小";
            } elseif ($contentName == "ALL:SUM:ODD") {
                $name_gdsf = "总和单";
            } elseif ($contentName == "ALL:SUM:EVEN") {
                $name_gdsf = "总和双";
            } elseif ($contentName == "ALL:SUM:LAST:OVER") {
                $name_gdsf = "总和尾数大";
            } elseif ($contentName == "ALL:SUM:LAST:UNDER") {
                $name_gdsf = "总和尾数小";
            }
        }

        return $name_gdsf;
    }

    static function getNameTJSF($contentName)
    {
        $betInfo = explode(":", $contentName);
        $name_tjsf = $contentName;

        if ($betInfo[1] == "LOCATE") { //每球定位
            $selectBall = $betInfo[2];
            if ($selectBall == "1") {
                $name_tjsf = "第一球 " . $betInfo[0];
            } elseif ($selectBall == "2") {
                $name_tjsf = "第二球 " . $betInfo[0];
            } elseif ($selectBall == "3") {
                $name_tjsf = "第三球 " . $betInfo[0];
            } elseif ($selectBall == "4") {
                $name_tjsf = "第四球 " . $betInfo[0];
            } elseif ($selectBall == "5") {
                $name_tjsf = "第五球 " . $betInfo[0];
            } elseif ($selectBall == "6") {
                $name_tjsf = "第六球 " . $betInfo[0];
            } elseif ($selectBall == "7") {
                $name_tjsf = "第七球 " . $betInfo[0];
            } elseif ($selectBall == "S") {
                $name_tjsf = "特别号 " . $betInfo[0];
            }
        } elseif ($betInfo[1] == "MATCH") {
            $name_tjsf = $betInfo[0];
        } elseif ($betInfo[0] > 0 || $betInfo[0] == "S") {
            $selectBall = $betInfo[0];
            if ($selectBall == "1") {
                if ($betInfo[2]) {
                    $name_tjsf = "第一球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_tjsf = "第一球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "2") {
                if ($betInfo[2]) {
                    $name_tjsf = "第二球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_tjsf = "第二球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "3") {
                if ($betInfo[2]) {
                    $name_tjsf = "第三球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_tjsf = "第三球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "4") {
                if ($betInfo[2]) {
                    $name_tjsf = "第四球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_tjsf = "第四球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "5") {
                if ($betInfo[2]) {
                    $name_tjsf = "第五球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_tjsf = "第五球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "6") {
                if ($betInfo[2]) {
                    $name_tjsf = "第六球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_tjsf = "第六球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "7") {
                if ($betInfo[2]) {
                    $name_tjsf = "第七球 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_tjsf = "第七球 " . getCommonName($betInfo[1]);
                }
            } elseif ($selectBall == "S") {
                if ($betInfo[2]) {
                    $name_tjsf = "特别号 " . getCommonName($betInfo[1] . ":" . $betInfo[2]);
                } else {
                    $name_tjsf = "特别号 " . getCommonName($betInfo[1]);
                }
            }
            if ($contentName == "1:S:DRAGON") {
                $name_tjsf = "龙";
            } elseif ($contentName == "1:S:TIGER") {
                $name_tjsf = "虎";
            }
        } else {
            if ($contentName == "ALL:SUM:OVER") {
                $name_tjsf = "总和大";
            } elseif ($contentName == "ALL:SUM:UNDER") {
                $name_tjsf = "总和小";
            } elseif ($contentName == "ALL:SUM:ODD") {
                $name_tjsf = "总和单";
            } elseif ($contentName == "ALL:SUM:EVEN") {
                $name_tjsf = "总和双";
            } elseif ($contentName == "ALL:SUM:LAST:OVER") {
                $name_tjsf = "总和尾数大";
            } elseif ($contentName == "ALL:SUM:LAST:UNDER") {
                $name_tjsf = "总和尾数小";
            }
        }

        return $name_tjsf;
    }

    static function getNameB5($contentName)
    {
        $name_b5 = get535NameByCode($contentName);
        return $name_b5;
    }

    static function getNameB3($contentName)
    {
        $name_b3 = getOeouNameByCode($contentName);
        if ($name_b3 == $contentName) {
            if (strpos($contentName, "*") !== false) {
                $betInfo = explode("*", $contentName);
                if (in_array($betInfo[0], array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9"))) {
                    return $name_b3;
                }
                if ($betInfo[2]) {
                    $name_b3 = getOeouNameByCode($betInfo[0]) . "*" . getOeouNameByCode($betInfo[1]) . "*" . getOeouNameByCode($betInfo[2]);
                } else {
                    $name_b3 = getOeouNameByCode($betInfo[0]) . "*" . getOeouNameByCode($betInfo[1]);
                }
            }
        }
        return $name_b3;
    }

    static function get535NameByCode($aConcede)
    {
        $name = $aConcede;
        if ($aConcede == "535-ODD") {
            $name = "万 单";
        } elseif ($aConcede == "535-EVEN") {
            $name = "万 双";
        } elseif ($aConcede == "540-OVER") {
            $name = "万 大";
        } elseif ($aConcede == "540-UNDER") {
            $name = "万 小";
        } elseif ($aConcede == "545-PRIME") {
            $name = "万 质";
        } elseif ($aConcede == "545-COMPO") {
            $name = "万 合";
        } elseif ($aConcede == "536-ODD") {
            $name = "仟 单";
        } elseif ($aConcede == "536-EVEN") {
            $name = "仟 双";
        } elseif ($aConcede == "541-OVER") {
            $name = "仟 大";
        } elseif ($aConcede == "541-UNDER") {
            $name = "仟 小";
        } elseif ($aConcede == "546-PRIME") {
            $name = "仟 质";
        } elseif ($aConcede == "546-COMPO") {
            $name = "仟 合";
        } elseif ($aConcede == "537-ODD") {
            $name = "佰 单";
        } elseif ($aConcede == "537-EVEN") {
            $name = "佰 双";
        } elseif ($aConcede == "542-OVER") {
            $name = "佰 大";
        } elseif ($aConcede == "542-UNDER") {
            $name = "佰 小";
        } elseif ($aConcede == "547-PRIME") {
            $name = "佰 质";
        } elseif ($aConcede == "547-COMPO") {
            $name = "佰 合";
        } elseif ($aConcede == "538-ODD") {
            $name = "拾 单";
        } elseif ($aConcede == "538-EVEN") {
            $name = "拾 双";
        } elseif ($aConcede == "543-OVER") {
            $name = "拾 大";
        } elseif ($aConcede == "543-UNDER") {
            $name = "拾 小";
        } elseif ($aConcede == "548-PRIME") {
            $name = "拾 质";
        } elseif ($aConcede == "548-COMPO") {
            $name = "拾 合";
        } elseif ($aConcede == "539-ODD") {
            $name = "个 单";
        } elseif ($aConcede == "539-EVEN") {
            $name = "个 双";
        } elseif ($aConcede == "544-OVER") {
            $name = "个 大";
        } elseif ($aConcede == "544-UNDER") {
            $name = "个 小";
        } elseif ($aConcede == "549-PRIME") {
            $name = "个 质";
        } elseif ($aConcede == "549-COMPO") {
            $name = "个 合";
        } elseif ($aConcede == "550-ODD") {
            $name = "万仟 单";
        } elseif ($aConcede == "550-EVEN") {
            $name = "万仟 双";
        } elseif ($aConcede == "560-OVER") {
            $name = "万仟 大";
        } elseif ($aConcede == "560-UNDER") {
            $name = "万仟 小";
        } elseif ($aConcede == "570-PRIME") {
            $name = "万仟 质";
        } elseif ($aConcede == "570-COMPO") {
            $name = "万仟 合";
        } elseif ($aConcede == "551-ODD") {
            $name = "万佰 单";
        } elseif ($aConcede == "551-EVEN") {
            $name = "万佰 双";
        } elseif ($aConcede == "561-OVER") {
            $name = "万佰 大";
        } elseif ($aConcede == "561-UNDER") {
            $name = "万佰 小";
        } elseif ($aConcede == "571-PRIME") {
            $name = "万佰 质";
        } elseif ($aConcede == "571-COMPO") {
            $name = "万佰 合";
        } elseif ($aConcede == "552-ODD") {
            $name = "万拾 单";
        } elseif ($aConcede == "552-EVEN") {
            $name = "万拾 双";
        } elseif ($aConcede == "562-OVER") {
            $name = "万拾 大";
        } elseif ($aConcede == "562-UNDER") {
            $name = "万拾 小";
        } elseif ($aConcede == "572-PRIME") {
            $name = "万拾 质";
        } elseif ($aConcede == "572-COMPO") {
            $name = "万拾 合";
        } elseif ($aConcede == "553-ODD") {
            $name = "万个 单";
        } elseif ($aConcede == "553-EVEN") {
            $name = "万个 双";
        } elseif ($aConcede == "563-OVER") {
            $name = "万个 大";
        } elseif ($aConcede == "563-UNDER") {
            $name = "万个 小";
        } elseif ($aConcede == "573-PRIME") {
            $name = "万个 质";
        } elseif ($aConcede == "573-COMPO") {
            $name = "万个 合";
        } elseif ($aConcede == "554-ODD") {
            $name = "仟佰 单";
        } elseif ($aConcede == "554-EVEN") {
            $name = "仟佰 双";
        } elseif ($aConcede == "564-OVER") {
            $name = "仟佰 大";
        } elseif ($aConcede == "564-UNDER") {
            $name = "仟佰 小";
        } elseif ($aConcede == "574-PRIME") {
            $name = "仟佰 质";
        } elseif ($aConcede == "574-COMPO") {
            $name = "仟佰 合";
        } elseif ($aConcede == "555-ODD") {
            $name = "仟拾 单";
        } elseif ($aConcede == "555-EVEN") {
            $name = "仟拾 双";
        } elseif ($aConcede == "565-OVER") {
            $name = "仟拾 大";
        } elseif ($aConcede == "565-UNDER") {
            $name = "仟拾 小";
        } elseif ($aConcede == "575-PRIME") {
            $name = "仟拾 质";
        } elseif ($aConcede == "575-COMPO") {
            $name = "仟拾 合";
        } elseif ($aConcede == "556-ODD") {
            $name = "仟个 单";
        } elseif ($aConcede == "556-EVEN") {
            $name = "仟个 双";
        } elseif ($aConcede == "566-OVER") {
            $name = "仟个 大";
        } elseif ($aConcede == "566-UNDER") {
            $name = "仟个 小";
        } elseif ($aConcede == "576-PRIME") {
            $name = "仟个 质";
        } elseif ($aConcede == "576-COMPO") {
            $name = "仟个 合";
        } elseif ($aConcede == "557-ODD") {
            $name = "佰拾 单";
        } elseif ($aConcede == "557-EVEN") {
            $name = "佰拾 双";
        } elseif ($aConcede == "567-OVER") {
            $name = "佰拾 大";
        } elseif ($aConcede == "567-UNDER") {
            $name = "佰拾 小";
        } elseif ($aConcede == "577-PRIME") {
            $name = "佰拾 质";
        } elseif ($aConcede == "577-COMPO") {
            $name = "佰拾 合";
        } elseif ($aConcede == "558-ODD") {
            $name = "佰个 单";
        } elseif ($aConcede == "558-EVEN") {
            $name = "佰个 双";
        } elseif ($aConcede == "568-OVER") {
            $name = "佰个 大";
        } elseif ($aConcede == "568-UNDER") {
            $name = "佰个 小";
        } elseif ($aConcede == "578-PRIME") {
            $name = "佰个 质";
        } elseif ($aConcede == "578-COMPO") {
            $name = "佰个 合";
        } elseif ($aConcede == "559-ODD") {
            $name = "拾个 单";
        } elseif ($aConcede == "559-EVEN") {
            $name = "拾个 双";
        } elseif ($aConcede == "569-OVER") {
            $name = "拾个 大";
        } elseif ($aConcede == "569-UNDER") {
            $name = "拾个 小";
        } elseif ($aConcede == "579-PRIME") {
            $name = "拾个 质";
        } elseif ($aConcede == "579-COMPO") {
            $name = "拾个 合";
        } elseif ($aConcede == "580-ODD") {
            $name = "前三 单";
        } elseif ($aConcede == "580-EVEN") {
            $name = "前三 双";
        } elseif ($aConcede == "583-OVER") {
            $name = "前三 大";
        } elseif ($aConcede == "583-UNDER") {
            $name = "前三 小";
        } elseif ($aConcede == "586-PRIME") {
            $name = "前三 质";
        } elseif ($aConcede == "586-COMPO") {
            $name = "前三 合";
        } elseif ($aConcede == "581-ODD") {
            $name = "中三 单";
        } elseif ($aConcede == "581-EVEN") {
            $name = "中三 双";
        } elseif ($aConcede == "584-OVER") {
            $name = "中三 大";
        } elseif ($aConcede == "584-UNDER") {
            $name = "中三 小";
        } elseif ($aConcede == "587-PRIME") {
            $name = "中三 质";
        } elseif ($aConcede == "587-COMPO") {
            $name = "中三 合";
        } elseif ($aConcede == "582-ODD") {
            $name = "后三 单";
        } elseif ($aConcede == "582-EVEN") {
            $name = "后三 双";
        } elseif ($aConcede == "585-OVER") {
            $name = "后三 大";
        } elseif ($aConcede == "585-UNDER") {
            $name = "后三 小";
        } elseif ($aConcede == "588-PRIME") {
            $name = "后三 质";
        } elseif ($aConcede == "588-COMPO") {
            $name = "后三 合";
        }
        return $name;
    }

    static function getOeouNameByCode($aConcede)
    {
        $name = $aConcede;
        if ($aConcede == "M_ODD") {
            $name = "佰 单";
        } elseif ($aConcede == "M_EVEN") {
            $name = "佰 双";
        } elseif ($aConcede == "M_OVER") {
            $name = "佰 大";
        } elseif ($aConcede == "M_UNDER") {
            $name = "佰 小";
        } elseif ($aConcede == "M_PRIME") {
            $name = "佰 质";
        } elseif ($aConcede == "M_COMPO") {
            $name = "佰 合";
        } elseif ($aConcede == "C_ODD") {
            $name = "拾 单";
        } elseif ($aConcede == "C_EVEN") {
            $name = "拾 双";
        } elseif ($aConcede == "C_OVER") {
            $name = "拾 大";
        } elseif ($aConcede == "C_UNDER") {
            $name = "拾 小";
        } elseif ($aConcede == "C_PRIME") {
            $name = "拾 质";
        } elseif ($aConcede == "C_COMPO") {
            $name = "拾 合";
        } elseif ($aConcede == "U_ODD") {
            $name = "个 单";
        } elseif ($aConcede == "U_EVEN") {
            $name = "个 双";
        } elseif ($aConcede == "U_OVER") {
            $name = "个 大";
        } elseif ($aConcede == "U_UNDER") {
            $name = "个 小";
        } elseif ($aConcede == "U_PRIME") {
            $name = "个 质";
        } elseif ($aConcede == "U_COMPO") {
            $name = "个 合";
        } elseif ($aConcede == "MC_ODD") {
            $name = "佰拾 单";
        } elseif ($aConcede == "MC_EVEN") {
            $name = "佰拾 双";
        } elseif ($aConcede == "MC_OVER") {
            $name = "佰拾 大";
        } elseif ($aConcede == "MC_UNDER") {
            $name = "佰拾 小";
        } elseif ($aConcede == "MC_PRIME") {
            $name = "佰拾 质";
        } elseif ($aConcede == "MC_COMPO") {
            $name = "佰拾 合";
        } elseif ($aConcede == "MU_ODD") {
            $name = "佰个 单";
        } elseif ($aConcede == "MU_EVEN") {
            $name = "佰个 双";
        } elseif ($aConcede == "MU_OVER") {
            $name = "佰个 大";
        } elseif ($aConcede == "MU_UNDER") {
            $name = "佰个 小";
        } elseif ($aConcede == "MU_PRIME") {
            $name = "佰个 质";
        } elseif ($aConcede == "MU_COMPO") {
            $name = "佰个 合";
        } elseif ($aConcede == "CU_ODD") {
            $name = "拾个 单";
        } elseif ($aConcede == "CU_EVEN") {
            $name = "拾个 双";
        } elseif ($aConcede == "CU_OVER") {
            $name = "拾个 大";
        } elseif ($aConcede == "CU_UNDER") {
            $name = "拾个 小";
        } elseif ($aConcede == "CU_PRIME") {
            $name = "拾个 质";
        } elseif ($aConcede == "CU_COMPO") {
            $name = "拾个 合";
        } elseif ($aConcede == "MCU_ODD") {
            $name = "佰拾个 单";
        } elseif ($aConcede == "MCU_EVEN") {
            $name = "佰拾个 双";
        } elseif ($aConcede == "MCU_OVER") {
            $name = "佰拾个 大";
        } elseif ($aConcede == "MCU_UNDER") {
            $name = "佰拾个 小";
        } elseif ($aConcede == "MCU_PRIME") {
            $name = "佰拾个 质";
        } elseif ($aConcede == "MCU_COMPO") {
            $name = "佰拾个 合";
        }
        return $name;
    }

    static function getCommonName($content)
    {
        $name = "";
        if ($content == "OVER") {
            $name = "大";
        } elseif ($content == "UNDER") {
            $name = "小";
        } elseif ($content == "ODD") {
            $name = "单";
        } elseif ($content == "EVEN") {
            $name = "双";
        } elseif ($content == "DRAGON") {
            $name = "龙";
        } elseif ($content == "TIGER") {
            $name = "虎";
        } elseif ($content == "SUM:ODD") {
            $name = "和单";
        } elseif ($content == "SUM:EVEN") {
            $name = "和双";
        } elseif ($content == "LAST:OVER") {
            $name = "尾大";
        } elseif ($content == "LAST:UNDER") {
            $name = "尾小";
        } elseif ($content == "RED") {
            $name = "红波";
        } elseif ($content == "BLUE") {
            $name = "蓝波";
        } elseif ($content == "GREEN") {
            $name = "绿波";
        } elseif ($content == "OVER:ODD") {
            $name = "大单";
        } elseif ($content == "OVER:EVEN") {
            $name = "大双";
        } elseif ($content == "UNDER:ODD") {
            $name = "小单";
        } elseif ($content == "UNDER:EVEN") {
            $name = "小双";
        } elseif ($content == "SPRING") {
            $name = "春";
        } elseif ($content == "SUMMER") {
            $name = "夏";
        } elseif ($content == "FALL") {
            $name = "秋";
        } elseif ($content == "WINTER") {
            $name = "冬";
        } elseif ($content == "METAL") {
            $name = "金";
        } elseif ($content == "WOOD") {
            $name = "木";
        } elseif ($content == "WATER") {
            $name = "水";
        } elseif ($content == "FIRE") {
            $name = "火";
        } elseif ($content == "EARTH") {
            $name = "土";
        } elseif ($content == "EAST") {
            $name = "东";
        } elseif ($content == "SOUTH") {
            $name = "南";
        } elseif ($content == "WEST") {
            $name = "西";
        } elseif ($content == "NORTH") {
            $name = "北";
        } elseif ($content == "ZHONG") {
            $name = "中";
        } elseif ($content == "FA") {
            $name = "发";
        } elseif ($content == "BAI") {
            $name = "白";
        }
        return $name;
    }

    static function getZhPageTitle($gType)
    {
        if ($gType == "T3") {
            return "上海时时乐";
        } elseif ($gType == "P3") {
            return "排列三";
        } elseif ($gType == "CQ") {
            return "重庆时时彩";
        } elseif ($gType == "TJ") {
            return "天津时时彩";
        } elseif ($gType == "JX") {
            return "新疆时时彩";
        } elseif ($gType == "BJKN") {
            return "北京快乐8";
        } elseif ($gType == "GXSF") {
            return "广西十分彩";
        } elseif ($gType == "GDSF") {
            return "广东十分彩";
        } elseif ($gType == "TJSF") {
            return "天津十分彩";
        } elseif ($gType == "BJPK") {
            return "北京PK拾";
        } elseif ($gType == "XYFT") {
            return "幸运飞艇";
        } elseif ($gType == "GD11") {
            return "广东十一选五";
        } elseif ($gType == "CQSF") {
            return "重庆十分彩";
        } elseif ($gType == "FFC5") {
            return "五分彩";
        } elseif ($gType == "AZXY5") {
            return "澳洲幸运5";
        } elseif ($gType == "AZXY10") {
            return "澳洲幸运10";
        } elseif ($gType == "TXSSC") {
            return "腾讯时时彩";
        } elseif ($gType == "TWSSC") {
            return "台湾时时彩";
        } elseif ($gType == "LT") {
            return "六合彩";
        } else {
            return "3D彩";
        }
    }

    static function ka_Color_s($i)
    {

        $ka_color = Kacolor::find($i);

        if ($ka_color['color'] == "r") {
            $bscolor = "红波";
        }

        if ($ka_color['color'] == "b") {
            $bscolor = "蓝波";
        }

        if ($ka_color['color'] == "g") {
            $bscolor = "绿波";
        }

        return $bscolor;
    }

    static function Get_wxwx_Color($rrr)
    {

        $ka_sxnumber = KasxNumber::where("id", "<=", 29)
            ->where("id", ">=", 25)
            ->where("m_number", "like", "%$rrr%")
            ->first();

        return $ka_sxnumber['sx'];
    }

    static function Get_sx_Color($rrr)
    {

        $ka_sxnumber = KasxNumber::where("id", "<=", 12)
            ->where("m_number", "like", "%$rrr%")
            ->first();

        return $ka_sxnumber['sx'];
    }

    static function ka_memds($i, $b)
    {
        $ka_quota = Kaquota::where("username", "gd")
            ->orderBy("id", "asc")
            ->get(["ds", "yg", "xx", "xxx", "ygb", "ygc", "ygd"]);
        return $ka_quota[$i][$b];
    }

    //生成订单
    static function randStr($len = 12)
    {
        $chars = '0123456789'; // 字符，以建立密码   
        mt_srand((float)microtime() * 1000000 * getmypid()); // 随机数发生器 (必须做)   
        $password = '';
        while (strlen($password) < $len) {
            $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
        }
        return $password;
    }

    static function ka_memuser($i, $user_name)
    {
        $ka_mem = Kamem::where("kauser", $user_name)->first();
        return $ka_mem[$i];
    }

    static function ka_memdaids($i, $b, $dai)
    {

        $ka_quota = Kaquota::where("username", $dai)
            ->orderBy("id", "asc")
            ->get(["ds", "yg", "xx", "xxx", "ygb", "ygc", "ygd"]);

        return $ka_quota[$i][$b];
    }

    static function ka_memzongds($i, $b, $zong)
    {

        $ka_quota = Kaquota::where("username", $zong)
            ->orderBy("id", "asc")
            ->get(["ds", "yg", "xx", "xxx", "ygb", "ygc", "ygd"]);

        return $ka_quota[$i][$b];
    }

    static function ka_memguands($i, $b, $guan)
    {

        $ka_quota = Kaquota::where("username", $guan)
            ->orderBy("id", "asc")
            ->get(["ds", "yg", "xx", "xxx", "ygb", "ygc", "ygd"]);

        return $ka_quota[$i][$b];
    }

    static function getCurrentKitheNum()
    {
        $Current_Kithe_Num = 1;
        $Current_KitheTable = Kakithe::where("na", 0)->first(["id", "nn", "nd", "na", "n1", "n2", "n3", "n4", "n5", "n6", "lx", "kitm", "kitm1", "kizt", "kizt1", "kizm", "kizm1", "kizm6", "kizm61", "kigg", "kigg1", "kilm", "kilm1", "kisx", "kisx1", "kibb", "kibb1", "kiws", "kiws1", "zfb", "zfbdate", "zfbdate1", "best"]);
        if ($Current_KitheTable["na"] == 0 || $Current_KitheTable["n1"] == 0 || $Current_KitheTable["n2"] == 0 || $Current_KitheTable["n3"] == 0 || $Current_KitheTable["n4"] == 0 || $Current_KitheTable["n5"] == 0 || $Current_KitheTable["n6"] == 0) {
            $Current_Kithe_Num = $Current_KitheTable["nn"];
        } else {
            $Current_Kithe_Num = $Current_KitheTable["nn"] + 1;
        }

        return $Current_Kithe_Num;
    }

    static function getCurrentMacaoKitheNum()
    {
        $Current_Kithe_Num = 1;
        $Current_KitheTable = MacaoKakithe::where("na", 0)->first(["id", "nn", "nd", "na", "n1", "n2", "n3", "n4", "n5", "n6", "lx", "kitm", "kitm1", "kizt", "kizt1", "kizm", "kizm1", "kizm6", "kizm61", "kigg", "kigg1", "kilm", "kilm1", "kisx", "kisx1", "kibb", "kibb1", "kiws", "kiws1", "zfb", "zfbdate", "zfbdate1", "best"]);
        if ($Current_KitheTable["na"] == 0 || $Current_KitheTable["n1"] == 0 || $Current_KitheTable["n2"] == 0 || $Current_KitheTable["n3"] == 0 || $Current_KitheTable["n4"] == 0 || $Current_KitheTable["n5"] == 0 || $Current_KitheTable["n6"] == 0) {
            $Current_Kithe_Num = $Current_KitheTable["nn"];
        } else {
            $Current_Kithe_Num = $Current_KitheTable["nn"] + 1;
        }

        return $Current_Kithe_Num;
    }

    static function ProcessUpdate($username)
    {
        global $dbname;
        do {
            $flag = 1;
            $MD5String = md5($username . time());
            $DateTime = time();
            $data = [
                "MD5String" => $MD5String,
                "DateTime" => $DateTime,
            ];
            $updateLog = new UpdateLog;
            if ($updateLog->create($data)) {
            } else {
                $flag = 0;
            }
            if ($flag == 0) sleep(1);  //暂停1秒
        } while ($flag == 0);
    }

    static function GetField($username, $Field)
    {
        $user = User::where('UserName', $username)->first();
        if (!$user) {
            return "";
        }
        return $user[$Field];
    }

    static function Mobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
    static function SafeString($inString)
    {
        $inString = str_replace('"', '', $inString);
        $inString = str_replace("'", "", $inString);
        $inString = str_replace('<', '', $inString);
        $inString = str_replace('>', '', $inString);
        $inString = str_replace('=', '', $inString);
        //$inString=str_replace(' ','',$inString);
        if (strlen($inString) > 50) return '';
        if (strpos(strtolower($inString), 'script')) return '';
        if (strpos(strtolower($inString), 'frame')) return '';
        if (strpos(strtolower($inString), 'update')) return '';
        if (strpos(strtolower($inString), '%20')) return '';
        if (strpos(strtolower($inString), '*')) return '';
        if (strpos(strtolower($inString), "'")) return '';
        if (strpos(strtolower($inString), "select")) return '';
        if (strpos(strtolower($inString), "convert")) return '';
        if (strpos(strtolower($inString), "concat")) return '';
        if (strpos(strtolower($inString), "0x")) return '';
        return $inString;
    }

    static function filiter_team($repteam)
    {
        //$repteam=trim(str_replace(" ","",$repteam));
        $repteam = trim(str_replace("[H]", "", $repteam));
        $repteam = trim(str_replace("[主]", "", $repteam));
        $repteam = trim(str_replace("[中]", "", $repteam));
        $repteam = trim(str_replace("[主]", "", $repteam));
        $repteam = trim(str_replace("[中]", "", $repteam));
        $repteam = trim(str_replace("[Home]", "", $repteam));
        $repteam = trim(str_replace("[Mid]", "", $repteam));
        $repteam = trim(str_replace("<font color=#990000> - [上半场]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=#990000> - [下半场]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=#990000> - [上半場]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=#990000> - [下半場]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=#990000> - [1st]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=#990000> - [2nd]</font>", "", $repteam));

        $repteam = trim(str_replace("<font color=gray> - [上半]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [下半]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [第1节]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [第2节]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [第3节]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [第4节]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [上半]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [下半]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [第1節]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [第2節]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [第3節]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [第4節]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [1st Half]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [2nd Half]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [Q1]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [Q2]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [Q3]</font>", "", $repteam));
        $repteam = trim(str_replace("<font color=gray> - [Q4]</font>", "", $repteam));

        $filiter_team = $repteam;
        return $filiter_team;
    }

    static function num_rate($c_type, $c_rate)
    {
        $t_rate = '0';
        switch ($c_type) {
            case 'A':
                $t_rate = '0';
                break;
            case 'B':
                $t_rate = '0';
                break;
            case 'C':
                $t_rate = '0';
                break;
            case 'D':
                $t_rate = '0';
                break;
        }
        if ($c_rate != '') {
            $num_rate = number_format($c_rate - $t_rate, 2);
            if ($num_rate <= 0) {
                $num_rate = '';
            }
        } else {
            $num_rate = '';
        }
        return $num_rate;
    }

    static function change_rate($c_type, $c_rate)
    {
        $t_rate = 0;
        switch ($c_type) {
            case 'A':
                $t_rate = '0.03';
                break;
            case 'B':
                $t_rate = '0.01';
                break;
            case 'C':
                $t_rate = '0';
                break;
            case 'D':
                $t_rate = '-0.01';
                break;
        }
        if ($c_rate != '' and $c_rate != '0') {
            $change_rate = number_format($c_rate - $t_rate, 3);
            if ($change_rate <= 0 and $change_rate >= -0.03) {
                $change_rate = '';
            }
        } else {
            $change_rate = '';
        }
        return $change_rate;
    }
    static function fileter0($rate)
    {
        $fileter0 = "";
        for ($i = 1; $i < strlen($rate); $i++) {
            if (substr($rate, -$i, 1) <> '0') {
                if (substr($rate, -$i, 1) == '.') {
                    $fileter0 = substr($rate, 0, strlen($rate) - $i);
                } else {
                    $fileter0 = substr($rate, 0, strlen($rate) - $i + 1);
                }
                break;
            }
        }
        return $fileter0;
    }

    static function singleset($ptype)
    {
        $sql = "select $ptype as P3,R,MAX from web_system_data where ID=1";
        $row = DB::select($sql)[0];
        $p = $row->P3;
        $pmax = $row->MAX;
        return array($p, $pmax);
    }

    static function show_voucher($line, $id)
    {
        $show_voucher = "";
        $sql = "select OUID,DTID,PMID from web_system_data";
        $row = DB::select($sql)['0'];
        $ouid = $row->OUID;
        $dtid = $row->DTID;
        $pmid = $row->PMID;
        switch ($line) {
            case 1:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 2:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 3:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 4:
                $show_voucher = 'DT' . ($id + $dtid);
                break;
            case 5:
                $show_voucher = 'DT' . ($id + $dtid);
                break;
            case 6:
                $show_voucher = 'DT' . ($id + $dtid);
                break;
            case 7:
                $show_voucher = 'DT' . ($id + $dtid);
                break;
            case 8:
                $show_voucher = 'PM' . ($id + $pmid);
                break;
            case 9:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 10:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 11:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 12:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 13:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 14:
                $show_voucher = 'DT' . ($id + $dtid);
                break;
            case 15:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 16:
                $show_voucher = 'DT' . ($id + $dtid);
                break;
            case 19:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 20:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 21:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 31:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
        }
        return $show_voucher;
    }
    static function get_ip()
    {

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $c_agentip = 1;
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $onlineip = $_SERVER['HTTP_CLIENT_IP'];
            $c_agentip = 1;
        } else {
            $onlineip = $_SERVER['REMOTE_ADDR'];
            $c_agentip = 0;
        }

        $a = array("'", '"', ";", "and", "or", "update", "select", "insert", "table");

        $b = array("", "", "", "", "", "", "", "", "");

        $onlineip = str_replace($a, $b, $onlineip);

        $onlineip = substr($onlineip, 0, 20);

        return $onlineip;
    }

    static function get_browser_ip()
    {
        $a = array(
            "'",
            '"',
            ";",
            "and",
            "update",
            "where",
            "set",
            "user",
            "pass",
            "insert"
        );
        $b = array("", "", "", "", "", "", "", "", "", "");
        $host = str_replace($a, $b, strtolower($_SERVER['HTTP_HOST']));
        $host = substr($host, 0, 30);

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $https = 'https://';
        } else {
            if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                $https = $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://';
            } else {
                $https = 'http://';
            }
        }
        $browser_ip = $https . $host;
        return $browser_ip;
    }
    static function chg_ior($odd_f, $iorH, $iorC, $showior)
    {
        $ior = array();
        if ($iorH < 3) $iorH *= 1000;
        if ($iorC < 3) $iorC *= 1000;
        $iorH = $iorH;
        $iorC = $iorC;
        switch ($odd_f) {
            case "H":    //香港變盤(輸水盤)
                $ior = Utils::get_HK_ior($iorH, $iorC);
                break;
            case "M":    //馬來盤
                $ior = Utils::get_MA_ior($iorH, $iorC);
                break;
            case "I":    //印尼盤
                $ior = Utils::get_IND_ior($iorH, $iorC);
                break;
            case "E":    //歐洲盤
                $ior = Utils::get_EU_ior($iorH, $iorC);
                break;
            default:    //香港盤
                $ior[0] = $iorH;
                $ior[1] = $iorC;
        }
        $ior[0] /= 1000;
        $ior[1] /= 1000;
        $ior[0] = Utils::Decimal_point($ior[0], $showior);
        $ior[1] = Utils::Decimal_point($ior[1], $showior);
        //$ior[0]=number(Decimal_point($ior[0],$showior),3);
        //$ior[1]=number(Decimal_point($ior[1],$showior),3);
        return $ior;
    }
    /*
    * 選擇多盤口時 轉換成該選擇賠率
    * @param odd_type 	選擇盤口
    * @param iorH		主賠率
    * @param iorC		客賠率
    * @param show		顯示位數
    * @return		回傳陣列 0-->H  ,1-->C
    */
    static function get_other_ioratio($odd_type, $iorH, $iorC, $showior)
    {
        $out = array();
        if ($iorH != "" || $iorC != "") {
            $out = Utils::chg_ior($odd_type, $iorH, $iorC, $showior);
        } else {
            $out[0] = $iorH;
            $out[1] = $iorC;
        }
        return $out;
    }
    /*
    去正負號做小數第幾位捨去
    進來的值是小數值
    */
    static function Decimal_point($tmpior, $show)
    {
        $sign = "";
        $sign = (($tmpior < 0) ? "Y" : "N");
        $tmpior = (floor(abs($tmpior) * $show + 1 / $show)) / $show;
        return ($tmpior * (($sign == "Y") ? -1 : 1));
    }
    /**
     * 換算成歐洲盤賠率
     * @param H_ratio
     * @param C_ratio
     * @return
     */
    static function get_EU_ior($H_ratio, $C_ratio)
    {
        $out_ior = array();
        $out_ior = Utils::get_HK_ior($H_ratio, $C_ratio);
        $H_ratio = $out_ior[0];
        $C_ratio = $out_ior[1];
        $out_ior[0] = $H_ratio + 1000;
        $out_ior[1] = $C_ratio + 1000;
        return $out_ior;
    }
    static function get_HK_ior($H_ratio, $C_ratio)
    {
        $out_ior = array();
        $line = "";
        $lowRatio = "";
        $nowRatio = "";
        $highRatio = "";
        $nowType = "";
        if ($H_ratio <= 1000 && $C_ratio <= 1000) {
            $out_ior[0] = $H_ratio;
            $out_ior[1] = $C_ratio;
            return $out_ior;
        }
        $line = 2000 - ($H_ratio + $C_ratio);
        if ($H_ratio > $C_ratio) {
            $lowRatio = $C_ratio;
            $nowType = "C";
        } else {
            $lowRatio = $H_ratio;
            $nowType = "H";
        }
        if (((2000 - $line) - $lowRatio) > 1000) {
            //對盤馬來盤
            $nowRatio = ($lowRatio + $line) * (-1);
        } else {
            //對盤香港盤
            $nowRatio = (2000 - $line) - $lowRatio;
        }
        if ($nowRatio < 0) {
            $highRatio = (abs(1000 / $nowRatio) * 1000);
        } else {
            $highRatio = (2000 - $line - $nowRatio);
        }
        if ($nowType == "H") {
            $out_ior[0] = $lowRatio;
            $out_ior[1] = $highRatio;
        } else {
            $out_ior[0] = $highRatio;
            $out_ior[1] = $lowRatio;
        }
        return $out_ior;
    }
    /**
     * 換算成馬來盤賠率
     * @param H_ratio
     * @param C_ratio
     * @return
     */
    static function get_MA_ior($H_ratio, $C_ratio)
    {
        $out_ior = array();
        $line = "";
        $lowRatio = "";
        $highRatio = "";
        $nowType = "";
        if (($H_ratio <= 1000 && $C_ratio <= 1000)) {
            $out_ior[0] = $H_ratio;
            $out_ior[1] = $C_ratio;
            return $out_ior;
        }
        $line = 2000 - ($H_ratio + $C_ratio);
        if ($H_ratio > $C_ratio) {
            $lowRatio = $C_ratio;
            $nowType = "C";
        } else {
            $lowRatio = $H_ratio;
            $nowType = "H";
        }
        $highRatio = ($lowRatio + $line) * (-1);
        if ($nowType == "H") {
            $out_ior[0] = $lowRatio;
            $out_ior[1] = $highRatio;
        } else {
            $out_ior[0] = $highRatio;
            $out_ior[1] = $lowRatio;
        }
        return $out_ior;
    }
    /**
     * 換算成印尼盤賠率
     * @param H_ratio
     * @param C_ratio
     * @return
     */
    static function get_IND_ior($H_ratio, $C_ratio)
    {
        $out_ior = array();
        $out_ior = get_HK_ior($H_ratio, $C_ratio);
        $H_ratio = $out_ior[0];
        $C_ratio = $out_ior[1];
        $H_ratio /= 1000;
        $C_ratio /= 1000;
        if ($H_ratio < 1) {
            $H_ratio = (-1) / $H_ratio;
        }
        if ($C_ratio < 1) {
            $C_ratio = (-1) / $C_ratio;
        }
        $out_ior[0] = $H_ratio * 1000;
        $out_ior[1] = $C_ratio * 1000;
        return $out_ior;
    }

    /****************************************************************************************** */

    static function MoneyToSsc($t_user)
    {
        $result = User::select('UserName', 'Money', 'Credit')->where('UserName', $t_user)->get();
        $rowuser = $result[0];
        if (!empty($rowuser['UserName'])) {
            DB::table('g_user')->where('g_name', $rowuser['UserName'])
                ->update(['g_money' => $rowuser['Credit'], 'g_money_yes' => $rowuser['Money']]);
        }
    }
    //大小球计算：
    static function odds_dime($mbin1, $tgin1, $dime, $mtype)
    {
        $grade = 0;
        if ($mbin1 < 0 or $tgin1 < 0) return 0;
        $dime = str_replace('大', '', $dime);
        $dime = str_replace('小', '', $dime);
        $dime = str_replace('O', '', $dime);
        $dime = str_replace('U', '', $dime);
        $total_inball = $mbin1 + $tgin1;
        $dime_odds = explode("/", $dime);
        switch (sizeof($dime_odds)) {
            case 1:
                $odds_inball = $total_inball - $dime_odds[0];
                switch ($mtype) { //下大
                    case 'OUH':
                        if ($odds_inball > 0) {
                            $grade = 1;
                        } else if ($odds_inball < 0) {
                            $grade = -1;
                        } else {
                            $grade = 0;
                        }
                        break;
                    case 'OUC': //下小
                        if ($odds_inball > 0) {
                            $grade = -1;
                        } else if ($odds_inball < 0) {
                            $grade = 1;
                        } else {
                            $grade = 0;
                        }
                        break;
                }
                break;
            case 2:
                if (ceil($dime_odds[0]) == $dime_odds[0]) {
                    $odds_inball = $total_inball - $dime_odds[0];
                    switch ($mtype) {
                        case "OUH":
                            if ($odds_inball > 0) {
                                $grade = 1;
                            } else if ($odds_inball < 0) {
                                $grade = -1;
                            } else if ($odds_inball == 0) {
                                $grade = -0.5;
                            }
                            break;
                        case "OUC":
                            if ($odds_inball > 0) {
                                $grade = -1;
                            } else if ($odds_inball < 0) {
                                $grade = 1;
                            } else if ($odds_inball == 0) {
                                $grade = 0.5;
                            }
                            break;
                    }
                } else {
                    $odds_inball = $total_inball - $dime_odds[1];
                    switch ($mtype) {
                        case "OUH":
                            if ($odds_inball > 0) {
                                $grade = 1;
                            } else if ($odds_inball < 0) {
                                $grade = -1;
                            } else if ($odds_inball == 0) {
                                $grade = 0.5;
                            }
                            break;
                        case "OUC":
                            if ($odds_inball > 0) {
                                $grade = -1;
                            } else if ($odds_inball < 0) {
                                $grade = 1;
                            } else if ($odds_inball == 0) {
                                $grade = -0.5;
                            }
                            break;
                    }
                }
                break;
        }
        $odds_dime = $grade;
        return $odds_dime;
    }
    //让球计算:
    static function odds_letb($mbin, $tgin, $showtype, $dime, $mtype)
    {
        $grade = 0;
        if ($mbin < 0 or $tgin < 0) return 0;
        if (mb_eregi("[+]", $dime)) {
            $letb_odds = explode("+", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){//半球在后1/1.5
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else if (mb_eregi("[-]", $dime)) {
            $letb_odds = explode("-", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else { //这里指的是另外的。注意这里
            $letb_odds = explode("/", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        }
        $odds_letb = $grade;
        return $odds_letb;
    }
    //上半大小球计算：
    static function odds_dime_v($mbin1, $tgin1, $dime, $mtype)
    {
        $grade = 0;
        if ($mbin1 < 0 or $tgin1 < 0) return 0;
        $dime = str_replace('大', '', $dime);
        $dime = str_replace('小', '', $dime);
        $dime = str_replace('O', '', $dime);
        $dime = str_replace('U', '', $dime);
        $total_inball = $mbin1 + $tgin1;
        $dime_odds = explode("/", $dime);
        switch (sizeof($dime_odds)) {
            case 1:
                $odds_inball = $total_inball - $dime_odds[0];
                switch ($mtype) { //下大
                    case 'VOUH':
                        if ($odds_inball > 0) {
                            $grade = 1;
                        } else if ($odds_inball < 0) {
                            $grade = -1;
                        } else {
                            $grade = 0;
                        }
                        break;
                    case 'VOUC': //下小
                        if ($odds_inball > 0) {
                            $grade = -1;
                        } else if ($odds_inball < 0) {
                            $grade = 1;
                        } else {
                            $grade = 0;
                        }
                        break;
                }
                break;
            case 2:
                if (ceil($dime_odds[0]) == $dime_odds[0]) {
                    $odds_inball = $total_inball - $dime_odds[0];
                    switch ($mtype) {
                        case "VOUH":
                            if ($odds_inball > 0) {
                                $grade = 1;
                            } else if ($odds_inball < 0) {
                                $grade = -1;
                            } else if ($odds_inball == 0) {
                                $grade = -0.5;
                            }
                            break;
                        case "VOUC":
                            if ($odds_inball > 0) {
                                $grade = -1;
                            } else if ($odds_inball < 0) {
                                $grade = 1;
                            } else if ($odds_inball == 0) {
                                $grade = 0.5;
                            }
                            break;
                    }
                } else {
                    $odds_inball = $total_inball - $dime_odds[1];
                    switch ($mtype) {
                        case "VOUH":
                            if ($odds_inball > 0) {
                                $grade = 1;
                            } else if ($odds_inball < 0) {
                                $grade = -1;
                            } else if ($odds_inball == 0) {
                                $grade = 0.5;
                            }
                            break;
                        case "VOUC":
                            if ($odds_inball > 0) {
                                $grade = -1;
                            } else if ($odds_inball < 0) {
                                $grade = 1;
                            } else if ($odds_inball == 0) {
                                $grade = -0.5;
                            }
                            break;
                    }
                }
                break;
        }
        $odds_dime = $grade;
        return $odds_dime;
    }
    //上半让球计算:
    static function odds_letb_v($mbin, $tgin, $showtype, $dime, $mtype)
    {
        $grade = 0;
        if ($mbin < 0 or $tgin < 0) return 0;
        if (mb_eregi("[+]", $dime)) {
            $letb_odds = explode("+", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){//半球在后1/1.5
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else if (mb_eregi("[-]", $dime)) {
            $letb_odds = explode("-", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else { //这里指的是另外的。注意这里
            $letb_odds = explode("/", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        }
        $odds_letb = $grade;
        return $odds_letb;
    }
    //滚球大小球计算：
    static function odds_dime_rb($mbin1, $tgin1, $dime, $mtype)
    {
        $grade = 0;
        if ($mbin1 < 0 or $tgin1 < 0) return 0;
        $dime = str_replace('大', '', $dime);
        $dime = str_replace('小', '', $dime);
        $dime = str_replace('O', '', $dime);
        $dime = str_replace('U', '', $dime);
        $total_inball = $mbin1 + $tgin1;
        $dime_odds = explode("/", $dime);
        switch (sizeof($dime_odds)) {
            case 1:
                $odds_inball = $total_inball - $dime_odds[0];
                switch ($mtype) { //下大
                    case 'ROUH':
                        if ($odds_inball > 0) {
                            $grade = 1;
                        } else if ($odds_inball < 0) {
                            $grade = -1;
                        } else {
                            $grade = 0;
                        }
                        break;
                    case 'ROUC': //下小
                        if ($odds_inball > 0) {
                            $grade = -1;
                        } else if ($odds_inball < 0) {
                            $grade = 1;
                        } else {
                            $grade = 0;
                        }
                        break;
                }
                break;
            case 2:
                if (ceil($dime_odds[0]) == $dime_odds[0]) {
                    $odds_inball = $total_inball - $dime_odds[0];
                    switch ($mtype) {
                        case "ROUH":
                            if ($odds_inball > 0) {
                                $grade = 1;
                            } else if ($odds_inball < 0) {
                                $grade = -1;
                            } else if ($odds_inball == 0) {
                                $grade = -0.5;
                            }
                            break;
                        case "ROUC":
                            if ($odds_inball > 0) {
                                $grade = -1;
                            } else if ($odds_inball < 0) {
                                $grade = 1;
                            } else if ($odds_inball == 0) {
                                $grade = 0.5;
                            }
                            break;
                    }
                } else {
                    $odds_inball = $total_inball - $dime_odds[1];
                    switch ($mtype) {
                        case "ROUH":
                            if ($odds_inball > 0) {
                                $grade = 1;
                            } else if ($odds_inball < 0) {
                                $grade = -1;
                            } else if ($odds_inball == 0) {
                                $grade = 0.5;
                            }
                            break;
                        case "ROUC":
                            if ($odds_inball > 0) {
                                $grade = -1;
                            } else if ($odds_inball < 0) {
                                $grade = 1;
                            } else if ($odds_inball == 0) {
                                $grade = -0.5;
                            }
                            break;
                    }
                }
                break;
        }
        $odds_dime = $grade;
        return $odds_dime;
    }
    //滚球让球计算:
    static function odds_letb_rb($mbin, $tgin, $showtype, $dime, $mtype)
    {
        $grade = 0;
        if ($mbin < 0 or $tgin < 0) return 0;
        if (mb_eregi("[+]", $dime)) {
            $letb_odds = explode("+", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //		if (strlen($letb_odds[1])>2){//半球在后1/1.5
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else if (mb_eregi("[-]", $dime)) {
            $letb_odds = explode("-", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else { //这里指的是另外的。注意这里
            $letb_odds = explode("/", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        }
        $odds_letb = $grade;
        return $odds_letb;
    }
    //滚球上半大小球计算：
    static function odds_dime_vrb($mbin1, $tgin1, $dime, $mtype)
    {
        $grade = 0;
        if ($mbin1 < 0 or $tgin1 < 0) return 0;
        $dime = str_replace('大', '', $dime);
        $dime = str_replace('小', '', $dime);
        $dime = str_replace('O', '', $dime);
        $dime = str_replace('U', '', $dime);
        $total_inball = $mbin1 + $tgin1;
        $dime_odds = explode("/", $dime);
        switch (sizeof($dime_odds)) {
            case 1:
                $odds_inball = $total_inball - $dime_odds[0];
                switch ($mtype) { //下大
                    case 'VROUH':
                        if ($odds_inball > 0) {
                            $grade = 1;
                        } else if ($odds_inball < 0) {
                            $grade = -1;
                        } else {
                            $grade = 0;
                        }
                        break;
                    case 'VROUC': //下小
                        if ($odds_inball > 0) {
                            $grade = -1;
                        } else if ($odds_inball < 0) {
                            $grade = 1;
                        } else {
                            $grade = 0;
                        }
                        break;
                }
                break;
            case 2:
                if (ceil($dime_odds[0]) == $dime_odds[0]) {
                    $odds_inball = $total_inball - $dime_odds[0];
                    switch ($mtype) {
                        case "VROUH":
                            if ($odds_inball > 0) {
                                $grade = 1;
                            } else if ($odds_inball < 0) {
                                $grade = -1;
                            } else if ($odds_inball == 0) {
                                $grade = -0.5;
                            }
                            break;
                        case "VROUC":
                            if ($odds_inball > 0) {
                                $grade = -1;
                            } else if ($odds_inball < 0) {
                                $grade = 1;
                            } else if ($odds_inball == 0) {
                                $grade = 0.5;
                            }
                            break;
                    }
                } else {
                    $odds_inball = $total_inball - $dime_odds[1];
                    switch ($mtype) {
                        case "VROUH":
                            if ($odds_inball > 0) {
                                $grade = 1;
                            } else if ($odds_inball < 0) {
                                $grade = -1;
                            } else if ($odds_inball == 0) {
                                $grade = 0.5;
                            }
                            break;
                        case "VROUC":
                            if ($odds_inball > 0) {
                                $grade = -1;
                            } else if ($odds_inball < 0) {
                                $grade = 1;
                            } else if ($odds_inball == 0) {
                                $grade = -0.5;
                            }
                            break;
                    }
                }
                break;
        }
        $odds_dime = $grade;
        return $odds_dime;
    }
    //滚球上半让球计算:
    static function odds_letb_vrb($mbin, $tgin, $showtype, $dime, $mtype)
    {
        $grade = 0;
        if ($mbin < 0 or $tgin < 0) return 0;
        if (mb_eregi("[+]", $dime)) {
            $letb_odds = explode("+", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){//半球在后1/1.5
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else if (mb_eregi("[-]", $dime)) {
            $letb_odds = explode("-", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else { //这里指的是另外的。注意这里
            $letb_odds = explode("/", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        }
        $odds_letb = $grade;
        return $odds_letb;
    }
    //波胆计算：
    static function odds_pd($mb_in_score, $tg_in_score, $m_place)
    {
        $grade = 0;
        if ($mb_in_score < 0 or $tg_in_score < 0) return 0;
        $betplace = 'MB' . $mb_in_score . 'TG' . $tg_in_score;
        if (($m_place == 'OVMB' or $m_place == 'OVH') and $mb_in_score > 4) {
            $grade = 1;
        } elseif (($m_place == 'OVMB' or $m_place == 'OVH') and $tg_in_score > 4) {
            $grade = 1;
        } elseif ($m_place == $betplace) {
            $grade = 1;
        } else {
            $grade = -1;
        }

        $odds_pd = $grade;
        return $odds_pd;
    }
    //上半波胆计算：
    static function odds_pd_v($mb_in_score_v, $tg_in_score_v, $m_place)
    {
        $grade = 0;
        if ($mb_in_score_v < 0 or $tg_in_score_v < 0) return 0;
        $betplace = 'MB' . $mb_in_score_v . 'TG' . $tg_in_score_v;
        if (($m_place == 'OVMB' or $m_place == 'OVH') and $mb_in_score_v > 4) {
            $grade = 1;
        } elseif (($m_place == 'OVMB' or $m_place == 'OVH') and $tg_in_score_v > 4) {
            $grade = 1;
        } elseif ($m_place == $betplace) {
            $grade = 1;
        } else {
            $grade = -1;
        }

        $odds_pd_v = $grade;
        return $odds_pd_v;
    }
    //单双计算:
    static function odds_eo($mb_in_score, $tg_in_score, $m_place)
    {
        $grade = 0;
        if ($mb_in_score < 0 or $tg_in_score < 0) return 0;
        $inball = ($mb_in_score + $tg_in_score);
        switch ($inball % 2) {
            case 1:
                if ($m_place == 'ODD') {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 0:
                if ($m_place == 'EVEN') {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
        }
        $odds_eo = $grade;
        return $odds_eo;
    }
    //入球数计算:
    static function odds_t($mb_in_score, $tg_in_score, $m_place)
    {
        $grade = 0;
        if ($mb_in_score < 0 or $tg_in_score < 0) return 0;
        $inball = $mb_in_score + $tg_in_score;
        if ($inball >= 0 and $inball <= 1) {
            $goin_place = "0~1";
        } else if ($inball >= 2 and $inball <= 3) {
            $goin_place = "2~3";
        } else if ($inball >= 4 and $inball <= 6) {
            $goin_place = "4~6";
        } else if ($inball >= 7) {
            $goin_place = "OVER";
        }
        if ($m_place == $goin_place) {
            $grade = 1;
        } else {
            $grade = -1;
        }
        $odds_t = $grade;
        return $odds_t;
    }
    //入球数计算:
    static function odds_bst($mb_in_score, $tg_in_score, $m_place)
    {
        $grade = 0;
        if ($mb_in_score < 0 or $tg_in_score < 0) return 0;
        $inball = $mb_in_score + $tg_in_score;
        if ($inball >= 1 and $inball <= 2) {
            $goin_place = "1~2";
        } else if ($inball >= 3 and $inball <= 4) {
            $goin_place = "3~4";
        } else if ($inball >= 5 and $inball <= 6) {
            $goin_place = "5~6";
        } else if ($inball >= 5 and $inball <= 6) {
            $goin_place = "5~6";
        } else if ($inball >= 7 and $inball <= 8) {
            $goin_place = "7~8";
        } else if ($inball >= 9 and $inball <= 10) {
            $goin_place = "9~10";
        } else if ($inball >= 11 and $inball <= 12) {
            $goin_place = "11~12";
        } else if ($inball >= 13 and $inball <= 14) {
            $goin_place = "13~14";
        } else if ($inball >= 15 and $inball <= 16) {
            $goin_place = "15~16";
        } else if ($inball >= 17 and $inball <= 18) {
            $goin_place = "17~18";
        } else if ($inball >= 19) {
            $goin_place = "19UP";
        }
        if ($m_place == $goin_place) {
            $grade = 1;
        } else {
            $grade = -1;
        }
        $odds_bst = $grade;
        return $odds_bst;
    }
    //半全计算：
    static function odds_half($mb_in_score_v, $tg_in_score_v, $mb_in_score, $tg_in_score, $m_place)
    {
        $grade = 0;
        if ($mb_in_score < 0 or $tg_in_score < 0) return 0;
        $grade = 0;
        if ($mb_in_score_v > $tg_in_score_v) {
            $m_w1 = "H";
        } elseif ($mb_in_score_v == $tg_in_score_v) {
            $m_w1 = "N";
        } else {
            $m_w1 = "C";
        }

        if ($mb_in_score > $tg_in_score) {
            $m_w2 = "H";
        } elseif ($mb_in_score == $tg_in_score) {
            $m_w2 = "N";
        } else {
            $m_w2 = "C";
        }
        $m_w = "F$m_w1$m_w2";
        if ($m_place == $m_w) {
            $grade = 1;
        } else {
            $grade = -1;
        }
        $odds_half = $grade;
        return $odds_half;
    }
    //独赢计算：
    static function win_chk($mbin, $tgin, $m_type)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        $grade = 0;
        switch ($m_type) {
            case 'MH':
                if ($mbin > $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'MC':
                if ($mbin < $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'MN':
                if ($mbin == $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
        }
        $win_chk = $grade;
        return $win_chk;
    }
    //上半独赢计算：
    static function win_chk_v($mbin, $tgin, $m_type)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        $grade = 0;
        switch ($m_type) {
            case 'VMH':
                if ($mbin > $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'VMC':
                if ($mbin < $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'VMN':
                if ($mbin == $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
        }
        $win_chk = $grade;
        return $win_chk;
    }
    //独赢计算：
    static function win_chk_rb($mbin, $tgin, $m_type)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        $grade = 0;
        switch ($m_type) {
            case 'RMH':
                if ($mbin > $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'RMC':
                if ($mbin < $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'RMN':
                if ($mbin == $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
        }
        $win_chk = $grade;
        return $win_chk;
    }
    //上半独赢计算：
    static function win_chk_vrb($mbin, $tgin, $m_type)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        $grade = 0;
        switch ($m_type) {
            case 'VRMH':
                if ($mbin > $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'VRMC':
                if ($mbin < $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'VRMN':
                if ($mbin == $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
        }
        $win_chk = $grade;
        return $win_chk;
    }

    static function decrypt($encrypted)
    {
        $key = 'Facai168Facai168'; // Replace with your own key
        $iv = ""; // Set the IV to all zeroes for ECB mode

        $encrypted = base64_decode($encrypted);
        if (!$encrypted) {
            error_log("Invalid base64 string: $encrypted");
            return false;
        }

        $decrypted = openssl_decrypt($encrypted, 'des-ecb', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) {
            $error = openssl_error_string();
            error_log("Decryption error: $error");
            return false;
        }

        // $decrypted = static::pkcs5_unpad($decrypted);
        // if ($decrypted === false) {
        //     error_log("Padding error");
        //     return false;
        // }

        return $decrypted;
    }

    static function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    static function pkcs5_unpad($text)
    {

        $pad_length = ord($text[strlen($text) - 1]);
        if ($pad_length > strlen($text)) {
            return false;
        }

        return substr($text, 0, -1 * $pad_length);
    }
}
