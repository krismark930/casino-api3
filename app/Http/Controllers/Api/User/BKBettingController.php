<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Config;
use App\Models\User;
use App\Models\WebReportData;
use App\Models\Sport;
use App\Models\WebAgent;
use App\Models\Web\Report;
use App\Models\WebSystemData;
use App\Utils\Utils;
use App\Models\Web\MoneyLog;
use App\Models\MatchCrown;
use Auth;
use Illuminate\Support\Facades\DB;

class BKBettingController extends Controller
{

    public function saveBKBettingInPlay(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'gold' => 'required|numeric|min:10|max:500000',
                'active' => 'required',
                'line_type' => 'required|numeric',
                'm_id' => 'required|numeric',
                'type' => 'required',
                'order_rate' => 'required',
                'odd_f_type' => 'required',
                'id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $odd_f_type = $request_data["odd_f_type"];
            $gold = (float)$request_data["gold"];
            $active = $request_data["active"];
            $line = $request_data["line_type"];
            $gid = $request_data["m_id"];
            $type = $request_data["type"];
            $order_rate = $request_data["order_rate"];
            $rtype = $request_data["r_type"] ?? "";
            $langx = "zh-cn";

            $configs = Config::all();

            $hg_confirm = $configs[0]['HG_Confirm'];
            $bad_name = explode(",", $configs[0]['BadMember']);
            $bad_name2 = explode(",", $configs[0]['BadMember2']);

            $user_id = $request_data["id"];
            // $user_id = Auth::guard("api")->user()->id;

            $user = User::where('id', $user_id)->where('Status', 0)->first();

            if (!isset($user)) {
                $response['message'] = 'Please login again!';
                return response()->json($response, $response['status']);                
            }

            // return $user;

            $open = $user['OpenType'];
            $pay_type = $user['Pay_Type'];
            $user_name = $user['UserName'];
            $agents = $user['Agents'];
            $world = $user['World'];
            $credit = $user['Money'];
            $corprator = $user['Corprator'];
            $super = $user['Super'];
            $admin = $user['Admin'];
            $w_ratio = $user['ratio'];
            $h_money = $user['Money'];
            $w_current = $user['CurType'];

            if ($h_money < $gold) {
                $response['message'] = 'Your available balance is insufficient, please deposit first!';
                return response()->json($response, $response['status']);
            }

            $mem_xe = getXianEr('BK', $request_data["line_type"], $user);
            $mem_xe['BET_SC'] = $mem_xe['BET_SC'] === 0 ? 500000 : $mem_xe['BET_SC'];
            $web_system_data = WebSystemData::where("ID", 1)->first();
            $bt_set = array($web_system_data['P3'], $web_system_data["MAX"]);
            $XianEr = $bt_set[0];

            $bet_score = WebReportData::where('M_Name', $user_name)->where('Cancel', 0)->where('MID', $gid)->sum('BetScore');

            if ($bet_score + $gold > $mem_xe['BET_SC']) {
                $response['message'] = 'The betting amount you entered is greater than the limit amount for a single game!';
                return response()->json($response, $response['status']);
            }

            // $newDate = now()->subMinutes(6 * 60 + 90);

            $match_sports = Sport::where('MID', $gid)->whereRaw("Open = 1 and MB_Team != ''")->first();

            if (!isset($match_sports)) {
                $response['message'] = 'Sport Not Found!';
                return response()->json($response, $response['status']);
            }


            if ($odd_f_type == 'E'){
                $r_num = 1;
            } else {
                $r_num = 0;
            }

            $w_tg_team = $match_sports['TG_Team'];

            $w_tg_team_tw = $match_sports['TG_Team_tw'];

            $w_tg_team_en = $match_sports['TG_Team_en'];



            $w_mb_team = $match_sports['MB_Team'];

            $w_mb_team_tw = $match_sports['MB_Team_tw'];

            $w_mb_team_en = $match_sports['MB_Team_en'];


            $w_mb_mid = $match_sports['MB_MID'];
            $w_tg_mid = $match_sports['TG_MID'];

            // Get the host and guest team name of the current font

            $s_mb_team = filiter_team($match_sports['MB_Team']);
            $s_tg_team = filiter_team($match_sports['TG_Team']);

            // Alliance processing: There is a difference between the alliance style written to the database and the displayed style

            $s_sleague = $match_sports['M_League'];

            // betting time

            $m_date = $match_sports["M_Date"];
            $show_type = $match_sports["ShowTypeRB"];

            $bet_time = date('Y-m-d H:i:s', strtotime(' + 1 hours'));

            $m_start = strtotime($match_sports['M_Start']);

            // $date_time = time();
            $date_time = now()->subMinutes(5 * 60 + 90);

            // if ($datetime - $m_start < 120) {
            //     $response['message'] = 'This Match is closed!';
            //     return response()->json($response, $response['status']);
            // }

            $inball = $match_sports['MB_Ball'] . ":" . $match_sports['TG_Ball'];

            $mb_ball = $match_sports['MB_Ball'];

            $tg_ball = $match_sports['TG_Ball'];

            switch ($line) {

                case 9:

                    $bet_type = '滚球让球';

                    $bet_type_tw = "滾球讓球";

                    $bet_type_en = "Running Ball";

                    $caption = Order_Basketball.Order_Running_Ball_betting_order;

                    $turn_rate = "BK_Turn_RE_A";

                    $MB_LetB_Rate_RB = $match_sports["MB_LetB_Rate_RB"];

                    $TG_LetB_Rate_RB = $match_sports["TG_LetB_Rate_RB"];

                    $rate = get_other_ioratio($odd_f_type, $MB_LetB_Rate_RB, $TG_LetB_Rate_RB, 100);

                    if ($rate[0] - $r_num > 1.5 || $rate[1] - $r_num > 1.5){
                        $response['message'] = 'Schedule is closed!';
                        return response()->json($response, $response['status']);
                    }

                    switch ($type) {

                        case "H":

                            $w_m_place = $w_mb_team;

                            $w_m_place_tw = $w_mb_team_tw;

                            $w_m_place_en = $w_mb_team_en;

                            $s_m_place = $s_mb_team;

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $mtype = 'RRH';

                            break;

                        case "C":

                            $w_m_place = $w_tg_team;

                            $w_m_place_tw = $w_tg_team_tw;

                            $w_m_place_en = $w_tg_team_en;

                            $s_m_place = $s_tg_team;

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $mtype = 'RRC';

                            break;
                    }

                    $Sign = $match_sports['M_LetB_RB'];

                    $grape = $Sign;

                    if (strtoupper($show_type) == "H") {

                        $l_team = $s_mb_team;

                        $r_team = $s_tg_team;

                        $w_l_team = $w_mb_team;

                        $w_l_team_tw = $w_mb_team_tw;

                        $w_l_team_en = $w_mb_team_en;

                        $w_r_team = $w_tg_team;

                        $w_r_team_tw = $w_tg_team_tw;

                        $w_r_team_en = $w_tg_team_en;

                        $inball = $match_sports['MB_Ball'] . ":" . $match_sports['TG_Ball'];
                    } else {

                        $r_team = $s_mb_team;

                        $l_team = $s_tg_team;

                        $w_r_team = $w_mb_team;

                        $w_r_team_tw = $w_mb_team_tw;

                        $w_r_team_en = $w_mb_team_en;

                        $w_l_team = $w_tg_team;

                        $w_l_team_tw = $w_tg_team_tw;

                        $w_l_team_en = $w_tg_team_en;

                        $inball = $match_sports['TG_Ball'] . ":" . $match_sports['MB_Ball'];
                    }

                    $s_mb_team = $l_team;

                    $s_tg_team = $r_team;

                    $w_mb_team = $w_l_team;

                    $w_mb_team_tw = $w_l_team_tw;

                    $w_mb_team_en = $w_l_team_en;

                    $w_tg_team = $w_r_team;

                    $w_tg_team_tw = $w_r_team_tw;

                    $w_tg_team_en = $w_r_team_en;

                    $turn = "BK_Turn_RE";

                    if ($odd_f_type == 'H') {

                        $gwin = ($w_m_rate) * $gold;

                    } else if ($odd_f_type == 'M' || $odd_f_type == 'I') {

                        if ($w_m_rate < 0) {
                            $gwin = $gold;
                        } else {
                            $gwin = ($w_m_rate) * $gold;
                        }
                    } else if ($odd_f_type == 'E') {

                        $gwin = ($w_m_rate-1) * $gold;
                    }

                    $ptype = 'RE';

                    $w_wtype='R';

                    break;                                        


                case 10:

                    $bet_type = '滚球大小';

                    $bet_type_tw = "滾球大小";

                    $bet_type_en = "Running Over/Under";

                    $caption=Order_Basketball.Order_Running_Ball_Over_Under_betting_order;

                    $turn_rate = "BK_Turn_OU_A";

                    $MB_Dime_Rate_RB = $match_sports["MB_Dime_Rate_RB"];

                    $TG_Dime_Rate_RB = $match_sports["TG_Dime_Rate_RB"];

                    $rate = get_other_ioratio($odd_f_type, $MB_Dime_Rate_RB, $TG_Dime_Rate_RB, 100);

                    switch ($type) {

                        case "H":

                            $w_m_place = $match_sports["MB_Dime_RB"];

                            $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["MB_Dime_RB"];

                            $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["MB_Dime_RB"];

                            $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["MB_Dime_RB"];

                            $s_m_place = $match_sports["MB_Dime_RB"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('O', 'over&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $mtype = 'ROUH';

                            break;

                        case "C":

                            $w_m_place = $match_sports["TG_Dime_RB"];

                            $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["TG_Dime_RB"];

                            $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["TG_Dime_RB"];

                            $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["TG_Dime_RB"];

                            $s_m_place = $match_sports["TG_Dime_RB"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('U', 'under&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $mtype = 'ROUC';

                            break;
                    }

                    $Sign = "VS.";

                    $grape = $m_place;

                    $turn = "BK_Turn_OU";

                    $ptype = 'ROU';

                    $w_wtype='R';

                    if ($odd_f_type == 'H') {

                        $gwin = ($w_m_rate) * $gold;

                    } else if ($odd_f_type == 'M' || $odd_f_type == 'I') {

                        if ($w_m_rate < 0) {
                            $gwin = $gold;
                        } else {
                            $gwin = ($w_m_rate) * $gold;
                        }
                    } else if ($odd_f_type == 'E') {
                        
                        $gwin = ($w_m_rate-1) * $gold;
                    }

                    break;
                case 5:
                    $bet_type = '单双';
                    $bet_type_tw = "單雙";
                    $bet_type_en = "Odd/Even";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_EO_";
                    switch ($rtype) {
                        case "ODD":
                            $w_m_place = '单';
                            $w_m_place_tw = '單';
                            $w_m_place_en = 'odd';
                            $s_m_place = '(' . Order_Odd . ')';
                            $w_m_rate = $match_sports["S_Single_Rate"];
                            break;
                        case "EVEN":
                            $w_m_place = '双';
                            $w_m_place_tw = '雙';
                            $w_m_place_en = 'even';
                            $s_m_place = '(' . Order_Even . ')';
                            $w_m_rate = $match_sports["S_Double_Rate"];
                            break;
                    }
                    $Sign = "VS.";
                    $turn = "FT_Turn_EO";
                    $gwin = ($w_m_rate - 1) * $gold;
                    $ptype = 'EO';
                    $mtype = $rtype;                    
                    $grape="";
                    break;
                
                // case 11:

                //     $bet_type = '滚球第1队得分';

                //     $bet_type_tw = "滚球第1队得分";

                //     $bet_type_en = "Running Team 1 Points";

                //     $caption = Order_Basketball;

                //     $turn_rate = "BK_Turn_OU_A";

                //     $MB_Points_Rate_RB_1 = $match_sports["MB_Points_Rate_RB_1"];

                //     $TG_Points_Rate_RB_1 = $match_sports["TG_Points_Rate_RB_1"];

                //     $rate = get_other_ioratio($odd_f_type, $MB_Points_Rate_RB_1, $TG_Points_Rate_RB_1, 100);

                //     switch ($type) {

                //         case "C":

                //             $w_m_place = $match_sports["MB_Points_RB_1"];

                //             $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);

                //             $w_m_place_tw = $match_sports["MB_Points_RB_1"];

                //             $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);

                //             $w_m_place_en = $match_sports["MB_Points_RB_1"];

                //             $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                //             $m_place = $match_sports["MB_Points_RB_1"];

                //             $s_m_place = $match_sports["MB_Points_RB_1"];

                //             if ($langx == "zh-cn") {

                //                 $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);

                //             } else if ($langx == "zh-cn") {

                //                 $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);

                //             } else if ($langx == "en-us" or $langx == 'th-tis') {

                //                 $s_m_place = str_replace('O', 'over&nbsp;', $s_m_place);
                //             }

                //             $w_m_rate = number_format($rate[0], 3);

                //             $turn_url = "";

                //             $mtype = 'ROUHU';

                //             break;

                //         case "H":

                //             $w_m_place = $match_sports["TG_Points_RB_1"];

                //             $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);

                //             $w_m_place_tw = $match_sports["TG_Points_RB_1"];

                //             $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);

                //             $w_m_place_en = $match_sports["TG_Points_RB_1"];

                //             $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                //             $m_place = $match_sports["TG_Points_RB_1"];

                //             $s_m_place = $match_sports["TG_Points_RB_1"];

                //             if ($langx == "zh-cn") {

                //                 $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);

                //             } else if ($langx == "zh-cn") {

                //                 $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);

                //             } else if ($langx == "en-us" or $langx == 'th-tis') {

                //                 $s_m_place = str_replace('U', 'under&nbsp;', $s_m_place);

                //             }

                //             $w_m_rate = number_format($rate[1], 3);

                //             $turn_url = "";

                //             $mtype = 'ROUHO';

                //             break;
                //     }

                //     $Sign = "VS.";

                //     $grape = $m_place;

                //     $turn = "BK_Turn_P1";

                //     $ptype = 'ROUH';

                //     $w_wtype='R';

                //     if ($odd_f_type == 'H') {

                //         $gwin = ($w_m_rate) * $gold;

                //     } else if ($odd_f_type == 'M' || $odd_f_type == 'I') {

                //         if ($w_m_rate < 0) {
                //             $gwin = $gold;
                //         } else {
                //             $gwin = ($w_m_rate) * $gold;
                //         }
                //     } else if ($odd_f_type == 'E') {
                        
                //         $gwin = ($w_m_rate-1) * $gold;
                //     }

                //     break;

                // case 12:

                //     $bet_type = '滚球第2队得分';

                //     $bet_type_tw = "滚球第2队得分";

                //     $bet_type_en = "Running Team 2 Points";

                //     $caption = Order_Basketball;

                //     $turn_rate = "BK_Turn_OU_A";

                //     $MB_Points_Rate_RB_2 = $match_sports["MB_Points_Rate_RB_2"];

                //     $TG_Points_Rate_RB_2 = $match_sports["TG_Points_Rate_RB_2"];

                //     $rate = get_other_ioratio($odd_f_type, $MB_Points_Rate_RB_2, $TG_Points_Rate_RB_2, 100);

                //     switch ($type) {

                //         case "C":

                //             $w_m_place = $match_sports["MB_Points_RB_2"];

                //             $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);

                //             $w_m_place_tw = $match_sports["MB_Points_RB_2"];

                //             $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);

                //             $w_m_place_en = $match_sports["MB_Points_RB_2"];

                //             $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                //             $m_place = $match_sports["MB_Points_RB_2"];

                //             $s_m_place = $match_sports["MB_Points_RB_2"];

                //             if ($langx == "zh-cn") {

                //                 $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);

                //             } else if ($langx == "zh-cn") {

                //                 $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);

                //             } else if ($langx == "en-us" or $langx == 'th-tis') {

                //                 $s_m_place = str_replace('O', 'over&nbsp;', $s_m_place);
                //             }

                //             $w_m_rate = number_format($rate[0], 3);

                //             $turn_url = "";

                //             $mtype = 'ROUCO';

                //             break;

                //         case "H":

                //             $w_m_place = $match_sports["TG_Points_RB_2"];

                //             $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);

                //             $w_m_place_tw = $match_sports["TG_Points_RB_2"];

                //             $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);

                //             $w_m_place_en = $match_sports["TG_Points_RB_2"];

                //             $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                //             $m_place = $match_sports["TG_Points_RB_2"];

                //             $s_m_place = $match_sports["TG_Points_RB_2"];

                //             if ($langx == "zh-cn") {

                //                 $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);

                //             } else if ($langx == "zh-cn") {

                //                 $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);

                //             } else if ($langx == "en-us" or $langx == 'th-tis') {

                //                 $s_m_place = str_replace('U', 'under&nbsp;', $s_m_place);

                //             }

                //             $w_m_rate = number_format($rate[1], 3);

                //             $turn_url = "";

                //             $mtype = 'ROUCU';

                //             break;
                //     }

                //     $Sign = "VS.";

                //     $grape = $m_place;

                //     $turn = "BK_Turn_P1";

                //     $ptype = 'ROUH';

                //     $w_wtype='R';

                //     if ($odd_f_type == 'H') {

                //         $gwin = ($w_m_rate) * $gold;

                //     } else if ($odd_f_type == 'M' || $odd_f_type == 'I') {

                //         if ($w_m_rate < 0) {
                //             $gwin = $gold;
                //         } else {
                //             $gwin = ($w_m_rate) * $gold;
                //         }
                //     } else if ($odd_f_type == 'E') {
                        
                //         $gwin = ($w_m_rate-1) * $gold;
                //     }

                //     break;
            }


            if ($line == 9 or $line == 10) {

                $oddstype = $odd_f_type;

            } else {

                $oddstype = '';
            }

            $w_mb_mid = $match_sports['MB_MID'];

            $w_tg_mid = $match_sports['TG_MID'];

            $lines=$match_sports['M_League']."<br>[".$match_sports['MB_MID'].']vs['.$match_sports['TG_MID']."]<br>".$w_mb_team."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";

            $lines=$lines."<FONT color=#cc0000>$w_m_place</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";  



            $lines_tw=$match_sports['M_League_tw']."<br>[".$match_sports['MB_MID'].']vs['.$match_sports['TG_MID']."]<br>".$w_mb_team_tw."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team_tw."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";

            $lines_tw=$lines_tw."<FONT color=#cc0000>$w_m_place_tw</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";



            $lines_en=$match_sports['M_League_en']."<br>[".$match_sports['MB_MID'].']vs['.$match_sports['TG_MID']."]<br>".$w_mb_team_en."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team_en."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";

            $lines_en=$lines_en."<FONT color=#cc0000>$w_m_place_en</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";

            $m_turn = $user['M_turn'] + 0;

            $agent = WebAgent::where("UserName", $agents)->first();
            if (isset($agent)) {
                $d_rate = $agent['D_turn'] + 0;
                $a_point = $agent['A_Point'] + 0;
                $b_point = $agent['B_Point'] + 0;
                $c_point = $agent['C_Point'] + 0;
                $d_point = $agent['D_Point'] + 0;
            } else {
                $d_rate = 0;
                $a_point = 0;
                $b_point = 0;
                $c_point = 0;
                $d_point = 0;
            }

            if ($w_m_rate == '' or $gwin <= 0 or $gwin == '') {
                $response['message'] = 'The schedule has been closed!';
                return response()->json($response, $response['status']);
            }

            $ip_addr = Utils::get_ip();

            $max_id = WebReportData::where('BetTime', '<', $bet_time)->max('ID');
            $num = rand(10, 50);
            $id = $max_id + $num;

            $web_system_data = WebSystemData::all();

            $order_id = show_voucher($line, $id, $web_system_data[0]);  //定单号

            if ($oddstype == '') $oddstype = 'H';

            $new_web_report_data = new WebReportData();

            $new_web_report_data->ID = $id;
            $new_web_report_data->OrderID = $order_id;
            $new_web_report_data->MID = $gid;
            $new_web_report_data->Active = $active;
            $new_web_report_data->LineType = $line;
            $new_web_report_data->Mtype = $mtype;
            $new_web_report_data->M_Date = $m_date;
            $new_web_report_data->BetTime = $bet_time;
            $new_web_report_data->BetScore = $gold;
            $new_web_report_data->Middle = $lines;
            $new_web_report_data->BetType = $bet_type;
            $new_web_report_data->M_Place = $grape;
            $new_web_report_data->M_Rate = $w_m_rate;
            $new_web_report_data->M_Name = $user_name;
            $new_web_report_data->Gwin = $gwin;
            $new_web_report_data->TurnRate = $m_turn;
            $new_web_report_data->OpenType = $open;
            $new_web_report_data->OddsType = $oddstype;
            $new_web_report_data->ShowType = $show_type;
            $new_web_report_data->Agents = $agents;
            $new_web_report_data->World = $world;
            $new_web_report_data->Corprator = $corprator;
            $new_web_report_data->Super = $super;
            $new_web_report_data->Admin = $admin;
            // $new_web_report_data->A_Rate = $a_rate;
            // $new_web_report_data->B_Rate = $b_rate;
            // $new_web_report_data->C_Rate = $c_rate;
            // $new_web_report_data->D_Rate = $d_rate;
            $new_web_report_data->A_Point = $a_point;
            $new_web_report_data->B_Point = $b_point;
            $new_web_report_data->C_Point = $c_point;
            $new_web_report_data->D_Point = $d_point;
            $new_web_report_data->BetIP = $ip_addr;
            $new_web_report_data->Ptype = $ptype;
            $new_web_report_data->Gtype = 'BK';
            $new_web_report_data->CurType = $w_current;
            $new_web_report_data->Ratio = $w_ratio;
            $new_web_report_data->MB_MID = $w_mb_mid;
            $new_web_report_data->TG_MID = $w_tg_mid;
            $new_web_report_data->Pay_Type = $pay_type;

            $new_web_report_data->save();

            $ouid = $new_web_report_data['ID'];

            $assets = $user['Money'];
            $user_id = $user['id'];

            $datetime = date("Y-m-d H:i:s");

            $user["Money"] = $assets - $gold;

            $user["withdrawal_condition"] = $user["withdrawal_condition"] - $gold <= 0 ? 0 : $user["withdrawal_condition"] - $gold;

            if ($user->save()) {
                $money_log = new MoneyLog();

                $money_log['user_id'] = $user_id;
                $money_log['order_num'] = $order_id;
                $money_log['about'] = '投注足球<br>gid:' . $gid . '<br>RID:' . $ouid;
                $money_log['update_time'] = $datetime;
                $money_log['type'] = $lines;
                $money_log['order_value'] = '-' . $gold;
                $money_log['assets'] = $assets;
                $money_log['balance'] = $user["Money"];

                $money_log->save();
            } else {
                WebReportData::where("id", $ouid)->delete();
                $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                return response()->json($response, $response['status']);
            }

            $response['data'] = $new_web_report_data;
            $response['message'] = 'Betting Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveBKBettingToday(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'gold' => 'required|numeric|min:10|max:500000',
                'active' => 'required',
                'line_type' => 'required|numeric',
                'm_id' => 'required|numeric',
                'type' => 'required',
                'order_rate' => 'required',
                'odd_f_type' => 'required',
                'id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $odd_f_type = $request_data["odd_f_type"];
            $gold = (float)$request_data["gold"];
            $active = $request_data["active"];
            $line = $request_data["line_type"];
            $gid = $request_data["m_id"];
            $type = $request_data["type"];
            $order_rate = $request_data["order_rate"];
            $rtype = $request_data["r_type"] ?? "";
            $langx = "zh-cn";

            $configs = Config::all();

            $hg_confirm = $configs[0]['HG_Confirm'];
            $bad_name = explode(",", $configs[0]['BadMember']);
            $bad_name2 = explode(",", $configs[0]['BadMember2']);
            $bad_name3 = explode(",", $configs[0]['BadMember3']);
            $bad_name_jq = explode(",", $configs[0]['BadMember_JQ']);

            $user_id = $request_data["id"];
            // $user_id = Auth::guard("api")->user()->id;

            $user = User::where('id', $user_id)->where('Status', 0)->first();

            if (!isset($user)) {
                $response['message'] = 'Please login again!';
                return response()->json($response, $response['status']);                
            }

            // return $user;

            $open = $user['OpenType'];
            $pay_type = $user['Pay_Type'];
            $user_name = $user['UserName'];
            $agents = $user['Agents'];
            $world = $user['World'];
            $credit = $user['Money'];
            $corprator = $user['Corprator'];
            $super = $user['Super'];
            $admin = $user['Admin'];
            $w_ratio = $user['ratio'];
            $h_money = $user['Money'];
            $w_current = $user['CurType'];

            // if ($h_money < $gold) {
            //     $response['message'] = 'Your available balance is insufficient, please deposit first!';
            //     return response()->json($response, $response['status']);
            // }

            $mem_xe = getXianEr('BK', $request_data["line_type"], $user);
            $mem_xe['BET_SC'] = $mem_xe['BET_SC'] === 0 ? 500000 : $mem_xe['BET_SC'];
            $web_system_data = WebSystemData::where("ID", 1)->first();
            $bt_set = array($web_system_data['P3'], $web_system_data["MAX"]);
            $XianEr = $bt_set[0];

            $bet_score = WebReportData::where('M_Name', $user_name)->where('Cancel', 0)->where('MID', $gid)->sum('BetScore');

            if ($bet_score + $gold > $mem_xe['BET_SC']) {
                $response['message'] = 'The betting amount you entered is greater than the limit amount for a single game!';
                return response()->json($response, $response['status']);
            }

            // $newDate = now()->subMinutes(6 * 60 + 90);

            $match_sports = Sport::where('MID', $gid)->whereRaw("Open = 1 and MB_Team != ''")->first();

            if (!isset($match_sports)) {
                $response['message'] = 'Sport Not Found!';
                return response()->json($response, $response['status']);
            }


            if ($odd_f_type == 'E'){
                $r_num = 1;
            } else {
                $r_num = 0;
            }

            $w_tg_team = $match_sports['TG_Team'];

            $w_tg_team_tw = $match_sports['TG_Team_tw'];

            $w_tg_team_en = $match_sports['TG_Team_en'];



            $w_mb_team = $match_sports['MB_Team'];

            $w_mb_team_tw = $match_sports['MB_Team_tw'];

            $w_mb_team_en = $match_sports['MB_Team_en'];


            $w_mb_mid = $match_sports['MB_MID'];
            $w_tg_mid = $match_sports['TG_MID'];

            // Get the host and guest team name of the current font

            $s_mb_team = filiter_team($match_sports['MB_Team']);
            $s_tg_team = filiter_team($match_sports['TG_Team']);

            // Alliance processing: There is a difference between the alliance style written to the database and the displayed style

            $s_sleague = $match_sports['M_League'];

            // betting time

            $m_date = $match_sports["M_Date"];

            $show_type = $match_sports["ShowTypeR"];

            $bet_time = date('Y-m-d H:i:s', strtotime(' + 1 hours'));

            $m_start = strtotime($match_sports['M_Start']);

            // $date_time = time();
            $date_time = now()->subMinutes(90);

            // if ($datetime - $m_start < 120) {
            //     $response['message'] = 'This Match is closed!';
            //     return response()->json($response, $response['status']);
            // }

            $inball = $match_sports['MB_Ball'] . ":" . $match_sports['TG_Ball'];

            $mb_ball = $match_sports['MB_Ball'];

            $tg_ball = $match_sports['TG_Ball'];

            switch ($line) {

                case 2:

                    $bet_type = '让球';

                    $bet_type_tw = "讓球";

                    $bet_type_en = "Handicap";

                    $caption = Order_Basketball;

                    $turn_rate = "BK_Turn_RE_A";

                    $MB_LetB_Rate = $match_sports["MB_LetB_Rate"];

                    $TG_LetB_Rate = $match_sports["TG_LetB_Rate"];

                    $rate = get_other_ioratio($odd_f_type, $MB_LetB_Rate, $TG_LetB_Rate, 100);

                    // $rate = [$MB_LetB_Rate, $TG_LetB_Rate];

                    // return $rate;

                    if ($rate[0] - $r_num > 1.5 || $rate[1] - $r_num > 1.5){
                        $response['message'] = 'Schedule is closed!';
                        return response()->json($response, $response['status']);
                    }

                    switch ($type) {

                        case "H":

                            $w_m_place = $w_mb_team;

                            $w_m_place_tw = $w_mb_team_tw;

                            $w_m_place_en = $w_mb_team_en;

                            $s_m_place = $s_mb_team;

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $mtype = 'RH';

                            break;

                        case "C":

                            $w_m_place = $w_tg_team;

                            $w_m_place_tw = $w_tg_team_tw;

                            $w_m_place_en = $w_tg_team_en;

                            $s_m_place = $s_tg_team;

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $mtype = 'RC';

                            break;
                    }

                    $Sign = $match_sports['M_LetB'];

                    $grape = $Sign;

                    if (strtoupper($show_type) == "H") {

                        $l_team = $s_mb_team;

                        $r_team = $s_tg_team;

                        $w_l_team = $w_mb_team;

                        $w_l_team_tw = $w_mb_team_tw;

                        $w_l_team_en = $w_mb_team_en;

                        $w_r_team = $w_tg_team;

                        $w_r_team_tw = $w_tg_team_tw;

                        $w_r_team_en = $w_tg_team_en;

                        $inball = $match_sports['MB_Ball'] . ":" . $match_sports['TG_Ball'];
                    } else {

                        $r_team = $s_mb_team;

                        $l_team = $s_tg_team;

                        $w_r_team = $w_mb_team;

                        $w_r_team_tw = $w_mb_team_tw;

                        $w_r_team_en = $w_mb_team_en;

                        $w_l_team = $w_tg_team;

                        $w_l_team_tw = $w_tg_team_tw;

                        $w_l_team_en = $w_tg_team_en;

                        $inball = $match_sports['TG_Ball'] . ":" . $match_sports['MB_Ball'];
                    }

                    $s_mb_team = $l_team;

                    $s_tg_team = $r_team;

                    $w_mb_team = $w_l_team;

                    $w_mb_team_tw = $w_l_team_tw;

                    $w_mb_team_en = $w_l_team_en;

                    $w_tg_team = $w_r_team;

                    $w_tg_team_tw = $w_r_team_tw;

                    $w_tg_team_en = $w_r_team_en;

                    $turn = "BK_Turn_R";

                    if ($odd_f_type == 'H') {

                        $gwin = ($w_m_rate) * $gold;

                    } else if ($odd_f_type == 'M' || $odd_f_type == 'I') {

                        if ($w_m_rate < 0) {
                            $gwin = $gold;
                        } else {
                            $gwin = ($w_m_rate) * $gold;
                        }
                    } else if ($odd_f_type == 'E') {

                        $gwin = ($w_m_rate-1) * $gold;
                    }

                    $ptype = 'R';

                    $w_wtype='R';

                    break;                                        


                case 3:

                    $bet_type = '大小';

                    $bet_type_tw = "大小";

                    $bet_type_en = "Over/Under";

                    $caption=Order_Basketball;

                    $turn_rate = "BK_Turn_OU_A";

                    $MB_Dime_Rate = $match_sports["MB_Dime_Rate"];

                    $TG_Dime_Rate = $match_sports["TG_Dime_Rate"];

                    $rate = get_other_ioratio($odd_f_type, $MB_Dime_Rate, $TG_Dime_Rate, 100);

                    switch ($type) {

                        case "H":

                            $w_m_place = $match_sports["MB_Dime"];

                            $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["MB_Dime"];

                            $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["MB_Dime"];

                            $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["MB_Dime"];

                            $s_m_place = $match_sports["MB_Dime"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('O', 'over&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $mtype = 'OUH';

                            break;

                        case "C":

                            $w_m_place = $match_sports["TG_Dime"];

                            $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["TG_Dime"];

                            $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["TG_Dime"];

                            $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["TG_Dime"];

                            $s_m_place = $match_sports["TG_Dime"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('U', 'under&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $mtype = 'OUC';

                            break;
                    }

                    $Sign = "VS.";

                    $grape = $m_place;

                    $turn = "BK_Turn_OU";

                    $ptype = 'OU';

                    $w_wtype='R';

                    if ($odd_f_type == 'H') {

                        $gwin = ($w_m_rate) * $gold;

                    } else if ($odd_f_type == 'M' || $odd_f_type == 'I') {

                        if ($w_m_rate < 0) {
                            $gwin = $gold;
                        } else {
                            $gwin = ($w_m_rate) * $gold;
                        }
                    } else if ($odd_f_type == 'E') {
                        
                        $gwin = ($w_m_rate-1) * $gold;
                    }

                    break;
                case 105:
                    $bet_type = '单双';
                    $bet_type_tw = "單雙";
                    $bet_type_en = "Odd/Even";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_EO_";
                    switch ($rtype) {
                        case "ODD":
                            $w_m_place = '单';
                            $w_m_place_tw = '單';
                            $w_m_place_en = 'odd';
                            $s_m_place = '(' . Order_Odd . ')';
                            $w_m_rate = $match_sports["S_Single_Rate"];
                            break;
                        case "EVEN":
                            $w_m_place = '双';
                            $w_m_place_tw = '雙';
                            $w_m_place_en = 'even';
                            $s_m_place = '(' . Order_Even . ')';
                            $w_m_rate = $match_sports["S_Double_Rate"];
                            break;
                    }
                    $Sign = "VS.";
                    $turn = "FT_Turn_EO";
                    $gwin = ($w_m_rate - 1) * $gold;
                    $ptype = 'EO';
                    $mtype = $rtype;                    
                    $grape="";
                    break;
            }


            if ($line == 1 or $line == 2) {

                $oddstype = $odd_f_type;

            } else {

                $oddstype = '';
            }

            $w_mb_mid = $match_sports['MB_MID'];

            $w_tg_mid = $match_sports['TG_MID'];

            $lines=$match_sports['M_League']."<br>[".$match_sports['MB_MID'].']vs['.$match_sports['TG_MID']."]<br>".$w_mb_team."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";

            $lines=$lines."<FONT color=#cc0000>$w_m_place</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";  



            $lines_tw=$match_sports['M_League_tw']."<br>[".$match_sports['MB_MID'].']vs['.$match_sports['TG_MID']."]<br>".$w_mb_team_tw."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team_tw."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";

            $lines_tw=$lines_tw."<FONT color=#cc0000>$w_m_place_tw</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";



            $lines_en=$match_sports['M_League_en']."<br>[".$match_sports['MB_MID'].']vs['.$match_sports['TG_MID']."]<br>".$w_mb_team_en."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team_en."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";

            $lines_en=$lines_en."<FONT color=#cc0000>$w_m_place_en</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";

            $m_turn = $user['M_turn'] + 0;

            // return $w_m_rate;

            if ($w_m_rate == '' or $gwin <= 0 or $gwin == '') {
                $response['message'] = 'The schedule has been closed!';
                return response()->json($response, $response['status']);
            }

            $ip_addr = Utils::get_ip();

            $agent = WebAgent::where("UserName", $agents)->first();
            if (isset($agent)) {
                $d_rate = $agent['D_turn'] + 0;
                $a_point = $agent['A_Point'] + 0;
                $b_point = $agent['B_Point'] + 0;
                $c_point = $agent['C_Point'] + 0;
                $d_point = $agent['D_Point'] + 0;
            } else {
                $d_rate = 0;
                $a_point = 0;
                $b_point = 0;
                $c_point = 0;
                $d_point = 0;
            }

            $max_id = WebReportData::where('BetTime', '<', $bet_time)->max('ID');
            $num = rand(10, 50);
            $id = $max_id + $num;

            $web_system_data = WebSystemData::all();

            $order_id = show_voucher($line, $id, $web_system_data[0]);  //定单号

            if ($oddstype == '') $oddstype = 'H';

            $new_web_report_data = new WebReportData();

            $new_web_report_data->ID = $id;
            $new_web_report_data->OrderID = $order_id;
            $new_web_report_data->MID = $gid;
            $new_web_report_data->Active = $active;
            $new_web_report_data->LineType = $line;
            $new_web_report_data->Mtype = $mtype;
            $new_web_report_data->M_Date = $m_date;
            $new_web_report_data->BetTime = $bet_time;
            $new_web_report_data->BetScore = $gold;
            $new_web_report_data->Middle = $lines;
            $new_web_report_data->BetType = $bet_type;
            $new_web_report_data->M_Place = $grape;
            $new_web_report_data->M_Rate = $w_m_rate;
            $new_web_report_data->M_Name = $user_name;
            $new_web_report_data->Gwin = $gwin;
            $new_web_report_data->TurnRate = $m_turn;
            $new_web_report_data->OpenType = $open;
            $new_web_report_data->OddsType = $oddstype;
            $new_web_report_data->ShowType = $show_type;
            $new_web_report_data->Agents = $agents;
            $new_web_report_data->World = $world;
            $new_web_report_data->Corprator = $corprator;
            $new_web_report_data->Super = $super;
            $new_web_report_data->Admin = $admin;
            // $new_web_report_data->A_Rate = $a_rate;
            // $new_web_report_data->B_Rate = $b_rate;
            // $new_web_report_data->C_Rate = $c_rate;
            // $new_web_report_data->D_Rate = $d_rate;
            $new_web_report_data->A_Point = $a_point;
            $new_web_report_data->B_Point = $b_point;
            $new_web_report_data->C_Point = $c_point;
            $new_web_report_data->D_Point = $d_point;
            $new_web_report_data->BetIP = $ip_addr;
            $new_web_report_data->Ptype = $ptype;
            $new_web_report_data->Gtype = 'BK';
            $new_web_report_data->CurType = $w_current;
            $new_web_report_data->Ratio = $w_ratio;
            $new_web_report_data->MB_MID = $w_mb_mid;
            $new_web_report_data->TG_MID = $w_tg_mid;
            $new_web_report_data->Pay_Type = $pay_type;

            $new_web_report_data->save();

            $ouid = $new_web_report_data['ID'];

            $assets = $user['Money'];
            $user_id = $user['id'];

            $datetime = date("Y-m-d H:i:s");

            $user["Money"] = $assets - $gold;

            $user["withdrawal_condition"] = $user["withdrawal_condition"] - $gold <= 0 ? 0 : $user["withdrawal_condition"] - $gold;

            if ($user->save()) {
                $money_log = new MoneyLog();

                $money_log['user_id'] = $user_id;
                $money_log['order_num'] = $order_id;
                $money_log['about'] = '投注足球<br>gid:' . $gid . '<br>RID:' . $ouid;
                $money_log['update_time'] = $datetime;
                $money_log['type'] = $lines;
                $money_log['order_value'] = '-' . $gold;
                $money_log['assets'] = $assets;
                $money_log['balance'] = $user["Money"];

                $money_log->save();
            } else {
                WebReportData::where("id", $ouid)->delete();
                $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                return response()->json($response, $response['status']);
            }

            $response['data'] = $new_web_report_data;
            $response['message'] = 'Betting Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

    public function saveBKBettingChampion(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'gold' => 'required|numeric|min:10|max:500000',
                'active' => 'required',
                'line_type' => 'required|numeric',
                'm_id' => 'required|numeric',
                'order_rate' => 'required',
                'id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $odd_f_type = $request_data["odd_f_type"];
            $gold = $request_data["gold"];
            $active = $request_data["active"];
            $line = $request_data["line_type"];
            $gid = $request_data["m_id"];
            $order_rate = $request_data["order_rate"];
            $rtype = $request_data["r_type"] ?? "";
            $wtype = "FS";
            $gametype = "BK";
            $langx = "zh-cn";

            $configs = Config::all();

            $hg_confirm = $configs[0]['HG_Confirm'];
            $bad_name = explode(",", $configs[0]['BadMember']);
            $bad_name2 = explode(",", $configs[0]['BadMember2']);
            $bad_name3 = explode(",", $configs[0]['BadMember3']);
            $bad_name_jq = explode(",", $configs[0]['BadMember_JQ']);

            $user_id = $request_data["id"];
            // $user_id = Auth::guard("api")->user()->id;

            $user = User::where('id', $user_id)->where('Status', 0)->first();

            if (!isset($user)) {
                $response['message'] = 'Please login again!';
                return response()->json($response, $response['status']);                
            }

            // return $user;

            $open = $user['OpenType'];
            $pay_type = $user['Pay_Type'];
            $user_name = $user['UserName'];
            $agents = $user['Agents'];
            $world = $user['World'];
            $credit = $user['Money'];
            $corprator = $user['Corprator'];
            $super = $user['Super'];
            $admin = $user['Admin'];
            $w_ratio = $user['ratio'];
            $h_money = $user['Money'];
            $w_current = $user['CurType'];

            if ($h_money < $gold) {
                $response['message'] = 'Your available balance is insufficient, please deposit first!';
                return response()->json($response, $response['status']);
            }

            $mem_xe = getXianEr('BK', 'FS', $user);

            // if($gold > $mem_xe['BET_SO']){
            //     $response['message'] = 'Exceeded the total amount of single bets!';
            //     return response()->json($response, $response['status']);
            // }

            $bet_score = WebReportData::where('M_Name', $user_name)->where('Cancel', 0)->where('MID', $gid)->sum('BetScore');

            // if ($bet_score + $gold > $mem_xe['BET_SC']) {
            //     $response['message'] = 'The betting amount you entered is greater than the limit amount for a single game!';
            //     return response()->json($response, $response['status']);
            // }

            // $newDate = now()->subMinutes(6 * 60 + 90);

            $match_crown = MatchCrown::where('MID', $gid)->where('Gid', $rtype)->first();

            if (!isset($match_crown)) {
                $response['message'] = 'Schedule is closed!';
                return response()->json($response, $response['status']);
            }

            //下注时间Date('Y').'-'.   $match_crown["ShowType"]
            $m_date = date("Y-m-d",strtotime($match_crown["M_Start"]));
            $show_type = $rtype;
            $bet_time = date('Y-m-d H:i:s', strtotime(' + 1 hours'));
    
            //联盟处理:生成写入数据库的联盟样式和显示的样式,二者有区别
            $w_sleague = $match_crown['M_League'];
            $w_sleague_tw = $match_crown['M_League_tw'];
            $w_sleague_en = $match_crown['M_League_en'];
            $s_sleague = $match_crown['M_League'];
    
            $w_sitem = $match_crown['M_Item'];
            $w_sitem_tw = $match_crown['M_Item_tw'];
            $w_sitem_en = $match_crown['M_Item_en'];
            $s_sitem = $match_crown['M_Item'];
    
            //根据下注的类型进行处理：构建成新的数据格式,准备写入数据库

            $bet_type = '冠军';
            $bet_type_tw = "冠軍";
            $bet_type_en = "Outright";
            $turn_rate = "FS_Turn_FS";
            $turn = "FS_Turn_FS";
            
            $num = $match_crown['Num'];
            $BKype = $match_crown['mshow'];
            $w_mb_team = $match_crown['MB_Team'];
            $w_mb_team_tw = $match_crown['MB_Team_tw'];
            $w_mb_team_en = $match_crown['MB_Team_en'];
            $s_mb_team = "";
            // $s_m_rate = num_rate($open, $match_crown['M_Rate']);
            $s_m_rate =$match_crown['M_Rate'];
            $s_m_rate = str_replace(",", "", $s_m_rate);
            $gwin = ($s_m_rate - 1) * $gold;
            $wtype = $gametype;
            
            $lines=$match_crown['M_League']."&nbsp;-&nbsp;".$match_crown['M_Item']."<br>".$w_mb_team."&nbsp;&nbsp;@&nbsp;<FONT color=#CC0000><b>".$s_m_rate."</b></FONT>";  
            
            $lines_tw=$match_crown['M_League_tw']."&nbsp;-&nbsp;".$match_crown['M_Item_tw']."<br>".$w_mb_team_tw."&nbsp;&nbsp;@&nbsp;<FONT color=#CC0000><b>".$s_m_rate."</b></FONT>";
            
            $lines_en=$match_crown['M_League_en']."&nbsp;-&nbsp;".$match_crown['M_Item_en']."<br>".$w_mb_team_en."&nbsp;&nbsp;@&nbsp;<FONT color=#CC0000><b>".$s_m_rate."</b></FONT>";

            if ($s_m_rate == '' or $gwin <= 0 or $gwin == '') {
                $response['message'] = 'The schedule has been closed!';
                return response()->json($response, $response['status']);
            }

            $ip_addr = Utils::get_ip();

            $m_turn = $user['M_turn'] + 0;

            $agent = WebAgent::where("UserName", $agents)->first();
            if (isset($agent)) {
                $d_rate = $agent['D_turn'] + 0;
                $a_point = $agent['A_Point'] + 0;
                $b_point = $agent['B_Point'] + 0;
                $c_point = $agent['C_Point'] + 0;
                $d_point = $agent['D_Point'] + 0;
            } else {
                $d_rate = 0;
                $a_point = 0;
                $b_point = 0;
                $c_point = 0;
                $d_point = 0;
            }
            $max_id = WebReportData::where('BetTime', '<', $bet_time)->max('ID');
            $num = rand(10, 50);
            $id = $max_id + $num;

            $web_system_data = WebSystemData::all();

            $order_id = show_voucher($line, $id, $web_system_data[0]);  //定单号

            $oddstype = $odd_f_type;

            if ($oddstype == '') $oddstype = 'H';

            $new_web_report_data = new WebReportData();

            $new_web_report_data->ID = $id;
            $new_web_report_data->OrderID = $order_id;
            $new_web_report_data->danger = 0;
            $new_web_report_data->MID = $gid;
            $new_web_report_data->Active = $active;
            $new_web_report_data->LineType = $line;
            // $new_web_report_data->Mtype = $mtype;
            $new_web_report_data->M_Date = $m_date;
            $new_web_report_data->BetTime = $bet_time;
            $new_web_report_data->BetScore = $gold;
            $new_web_report_data->Middle = $lines;
            $new_web_report_data->BetType = $bet_type;
            // $new_web_report_data->M_Place = $grape;
            $new_web_report_data->M_Rate = $s_m_rate;
            $new_web_report_data->M_Name = $user_name;
            $new_web_report_data->Gwin = $gwin;
            $new_web_report_data->TurnRate = $m_turn;
            $new_web_report_data->OpenType = $open;
            $new_web_report_data->OddsType = $oddstype;
            $new_web_report_data->ShowType = $show_type;
            $new_web_report_data->Agents = $agents;
            $new_web_report_data->World = $world;
            $new_web_report_data->Corprator = $corprator;
            $new_web_report_data->Super = $super;
            $new_web_report_data->Admin = $admin;
            $new_web_report_data->A_Point = $a_point;
            $new_web_report_data->B_Point = $b_point;
            $new_web_report_data->C_Point = $c_point;
            $new_web_report_data->D_Point = $d_point;
            $new_web_report_data->BetIP = $ip_addr;
            $new_web_report_data->Ptype = $wtype;
            $new_web_report_data->Gtype = 'FS';
            $new_web_report_data->CurType = $w_current;
            $new_web_report_data->Ratio = $w_ratio;
            $new_web_report_data->Pay_Type = $pay_type;

            $new_web_report_data->save();

            $ouid = $new_web_report_data['ID'];

            $assets = $user['Money'];
            $user_id = $user['id'];

            $datetime = date("Y-m-d H:i:s");

            $user["Money"] = $assets - $gold;

            $user["withdrawal_condition"] = $user["withdrawal_condition"] - $gold <= 0 ? 0 : $user["withdrawal_condition"] - $gold;

            if ($user->save()) {
                $money_log = new MoneyLog();

                $money_log['user_id'] = $user_id;
                $money_log['order_num'] = $order_id;
                $money_log['about'] = '投注足球<br>gid:' . $gid . '<br>RID:' . $ouid;
                $money_log['update_time'] = $datetime;
                $money_log['type'] = $lines;
                $money_log['order_value'] = '-' . $gold;
                $money_log['assets'] = $assets;
                $money_log['balance'] = $user["Money"];

                $money_log->save();
            } else {
                WebReportData::where("id", $ouid)->delete();
                $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                return response()->json($response, $response['status']);
            }

            $response['data'] = $new_web_report_data;
            $response['message'] = 'Betting Order added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveBKBettingParlay(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'gold' => 'required|numeric|min:10|max:500000',
                'active' => 'required',
                'line_type' => 'required|numeric',
                'm_id' => 'required|numeric',
                'type' => 'required',
                'order_rate' => 'required',
                'odd_f_type' => 'required',
                'id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $odd_f_type = $request_data["odd_f_type"];
            $gold = (float)$request_data["gold"];
            $active = $request_data["active"];
            $line = $request_data["line_type"];
            $gid = $request_data["m_id"];
            $type = $request_data["type"];
            $order_rate = $request_data["order_rate"];
            $rtype = $request_data["r_type"] ?? "";
            $langx = "zh-cn";

            $configs = Config::all();

            $hg_confirm = $configs[0]['HG_Confirm'];
            $bad_name = explode(",", $configs[0]['BadMember']);
            $bad_name2 = explode(",", $configs[0]['BadMember2']);
            $bad_name3 = explode(",", $configs[0]['BadMember3']);
            $bad_name_jq = explode(",", $configs[0]['BadMember_JQ']);

            $user_id = $request_data["id"];
            // $user_id = Auth::guard("api")->user()->id;

            $user = User::where('id', $user_id)->where('Status', 0)->first();

            if (!isset($user)) {
                $response['message'] = 'Please login again!';
                return response()->json($response, $response['status']);                
            }

            // return $user;

            $open = $user['OpenType'];
            $pay_type = $user['Pay_Type'];
            $user_name = $user['UserName'];
            $agents = $user['Agents'];
            $world = $user['World'];
            $credit = $user['Money'];
            $corprator = $user['Corprator'];
            $super = $user['Super'];
            $admin = $user['Admin'];
            $w_ratio = $user['ratio'];
            $h_money = $user['Money'];
            $w_current = $user['CurType'];

            if ($h_money < $gold) {
                $response['message'] = 'Your available balance is insufficient, please deposit first!';
                return response()->json($response, $response['status']);
            }

            $mem_xe = getXianEr('BK', $request_data["line_type"], $user);
            $mem_xe['BET_SC'] = $mem_xe['BET_SC'] === 0 ? 500000 : $mem_xe['BET_SC'];
            $web_system_data = WebSystemData::where("ID", 1)->first();
            $bt_set = array($web_system_data['P3'], $web_system_data["MAX"]);
            $XianEr = $bt_set[0];

            $bet_score = WebReportData::where('M_Name', $user_name)->where('Cancel', 0)->where('MID', $gid)->sum('BetScore');

            if ($bet_score + $gold > $mem_xe['BET_SC']) {
                $response['message'] = 'The betting amount you entered is greater than the limit amount for a single game!';
                return response()->json($response, $response['status']);
            }

            // $newDate = now()->subMinutes(6 * 60 + 90);

            $match_sports = Sport::where('MID', $gid)->whereRaw("Open = 1 and MB_Team != ''")->first();



            if (!isset($match_sports)) {
                $response['message'] = 'Sport Not Found!';
                return response()->json($response, $response['status']);
            }

            $w_tg_team = $match_sports['TG_Team'];

            $w_tg_team_tw = $match_sports['TG_Team_tw'];

            $w_tg_team_en = $match_sports['TG_Team_en'];



            $w_mb_team = $match_sports['MB_Team'];

            $w_mb_team_tw = $match_sports['MB_Team_tw'];

            $w_mb_team_en = $match_sports['MB_Team_en'];


            $w_mb_mid = $match_sports['MB_MID'];
            $w_tg_mid = $match_sports['TG_MID'];

            if (strpos($w_tg_team, '角球') or strpos($w_mb_team, '角球') or strpos($w_tg_team, '点球') or strpos($w_mb_team, '点球')) {  // Block corner and penalty betting
                if (in_array($user_name, $bad_name_jq)) {
                    $response['message'] = attention("This match is closed. Please try again!", "", "zh-cn");
                    return response()->json($response, $response['status']);
                }
            }

            // Get the host and guest team name of the current font

            $s_mb_team = filiter_team($match_sports['MB_Team']);
            $s_tg_team = filiter_team($match_sports['TG_Team']);

            // Alliance processing: There is a difference between the alliance style written to the database and the displayed style

            $s_sleague = $match_sports['M_League'];

            // betting time

            $m_date = $match_sports["M_Date"];
            $show_type = $match_sports["ShowTypeP"];

            $bet_time = date('Y-m-d H:i:s', strtotime(' + 1 hours'));

            $m_start = strtotime($match_sports['M_Start']);

            // $date_time = time();
            $date_time = now()->subMinutes(5 * 60 + 90);

            $inball = $match_sports['MB_Ball'] . ":" . $match_sports['TG_Ball'];

            $mb_ball = $match_sports['MB_Ball'];

            $tg_ball = $match_sports['TG_Ball'];

            switch ($line) {
                case 102:
                case 109:
                    $bet_type = '让球';
                    $bet_type_tw = "讓球";
                    $bet_type_en = "Handicap";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_R_";
                    $rate = get_other_ioratio($odd_f_type, $match_sports["MB_P_LetB_Rate"], $match_sports["TG_P_LetB_Rate"], 100);

                    switch ($type) {

                        case "H":
                            $w_m_place = $w_mb_team;
                            $w_m_place_tw = $w_mb_team_tw;
                            $w_m_place_en = $w_mb_team_en;
                            $s_m_place = $s_mb_team;
                            $w_m_rate = $rate[0] - 1;
                            $turn_url = "";
                            $mtype = 'RH';
                            break;
                        case "C":
                            $w_m_place = $w_tg_team;
                            $w_m_place_tw = $w_tg_team_tw;
                            $w_m_place_en = $w_tg_team_en;
                            $s_m_place = $s_tg_team;
                            $w_m_rate = $rate[1] - 1;
                            $turn_url = "";
                            $mtype = 'RC';
                            break;
                    }

                    $Sign = $match_sports['M_P_LetB'];
                    $grape = $Sign;

                    if ($show_type == "H") {
                        $l_team = $s_mb_team;
                        $r_team = $s_tg_team;
                        $w_l_team = $w_mb_team;
                        $w_l_team_tw = $w_mb_team_tw;
                        $w_l_team_en = $w_mb_team_en;
                        $w_r_team = $w_tg_team;
                        $w_r_team_tw = $w_tg_team_tw;
                        $w_r_team_en = $w_tg_team_en;
                    } else {
                        $r_team = $s_mb_team;
                        $l_team = $s_tg_team;
                        $w_r_team = $w_mb_team;
                        $w_r_team_tw = $w_mb_team_tw;
                        $w_r_team_en = $w_mb_team_en;
                        $w_l_team = $w_tg_team;
                        $w_l_team_tw = $w_tg_team_tw;
                        $w_l_team_en = $w_tg_team_en;
                    }

                    $s_mb_team = $l_team;
                    $s_tg_team = $r_team;
                    $w_mb_team = $w_l_team;
                    $w_mb_team_tw = $w_l_team_tw;
                    $w_mb_team_en = $w_l_team_en;
                    $w_tg_team = $w_r_team;
                    $w_tg_team_tw = $w_r_team_tw;
                    $w_tg_team_en = $w_r_team_en;

                    $turn = "FT_Turn_R";

                    if ($odd_f_type === 'H') {
                        $gwin = ($w_m_rate) * $gold;
                    } else if ($odd_f_type === 'M' or $odd_f_type === 'I') {
                        if ($w_m_rate < 0) {
                            $gwin = $gold;
                        } else {
                            $gwin = ($w_m_rate) * $gold;
                        }
                    } else if ($odd_f_type === 'E') {
                        $gwin = ($w_m_rate - 1) * $gold;
                    }
                    $ptype = 'R';
                    break;
                    $bet_type = '让球';
                    $bet_type_tw = "讓球";
                    $bet_type_en = "Handicap";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_R_";
                    $rate = get_other_ioratio($odd_f_type, $match_sports["MB_P_LetB_Rate"], $match_sports["TG_P_LetB_Rate"], 100);

                    switch ($type) {

                        case "H":
                            $w_m_place = $w_mb_team;
                            $w_m_place_tw = $w_mb_team_tw;
                            $w_m_place_en = $w_mb_team_en;
                            $s_m_place = $s_mb_team;
                            $w_m_rate = $rate[0] - 1;
                            $turn_url = "";
                            $mtype = 'RH';
                            break;
                        case "C":
                            $w_m_place = $w_tg_team;
                            $w_m_place_tw = $w_tg_team_tw;
                            $w_m_place_en = $w_tg_team_en;
                            $s_m_place = $s_tg_team;
                            $w_m_rate = $rate[1] - 1;
                            $turn_url = "";
                            $mtype = 'RC';
                            break;
                    }

                    $Sign = $match_sports['M_P_LetB'];
                    $grape = $Sign;

                    if ($show_type == "H") {
                        $l_team = $s_mb_team;
                        $r_team = $s_tg_team;
                        $w_l_team = $w_mb_team;
                        $w_l_team_tw = $w_mb_team_tw;
                        $w_l_team_en = $w_mb_team_en;
                        $w_r_team = $w_tg_team;
                        $w_r_team_tw = $w_tg_team_tw;
                        $w_r_team_en = $w_tg_team_en;
                    } else {
                        $r_team = $s_mb_team;
                        $l_team = $s_tg_team;
                        $w_r_team = $w_mb_team;
                        $w_r_team_tw = $w_mb_team_tw;
                        $w_r_team_en = $w_mb_team_en;
                        $w_l_team = $w_tg_team;
                        $w_l_team_tw = $w_tg_team_tw;
                        $w_l_team_en = $w_tg_team_en;
                    }

                    $s_mb_team = $l_team;
                    $s_tg_team = $r_team;
                    $w_mb_team = $w_l_team;
                    $w_mb_team_tw = $w_l_team_tw;
                    $w_mb_team_en = $w_l_team_en;
                    $w_tg_team = $w_r_team;
                    $w_tg_team_tw = $w_r_team_tw;
                    $w_tg_team_en = $w_r_team_en;

                    $turn = "FT_Turn_R";

                    if ($odd_f_type === 'H') {
                        $gwin = ($w_m_rate) * $gold;
                    } else if ($odd_f_type === 'M' or $odd_f_type === 'I') {
                        if ($w_m_rate < 0) {
                            $gwin = $gold;
                        } else {
                            $gwin = ($w_m_rate) * $gold;
                        }
                    } else if ($odd_f_type === 'E') {
                        $gwin = ($w_m_rate - 1) * $gold;
                    }
                    $ptype = 'R';
                    break;
                case 103:
                case 110:
                    $bet_type = '大小';
                    $bet_type_tw = "大小";
                    $bet_type_en = "Over/Under";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_OU_";
                    $rate = get_other_ioratio($odd_f_type, $match_sports["MB_P_Dime_Rate"], $match_sports["TG_P_Dime_Rate"], 100);
                    switch ($type) {
                        case "H":
                            $w_m_place = $match_sports["MB_P_Dime"];
                            $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);
                            $w_m_place_tw = $match_sports["MB_P_Dime"];
                            $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);
                            $w_m_place_en = $match_sports["MB_P_Dime"];
                            $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["MB_P_Dime"];

                            $s_m_place = $match_sports["MB_P_Dime"];
                            $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            $w_m_rate = $rate[0] - 1;
                            $turn_url = "";
                            $mtype = 'OUH';
                            break;
                        case "C":
                            $w_m_place = $match_sports["TG_P_Dime"];
                            $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);
                            $w_m_place_tw = $match_sports["TG_P_Dime"];
                            $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);
                            $w_m_place_en = $match_sports["TG_P_Dime"];
                            $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["TG_P_Dime"];

                            $s_m_place = $match_sports["TG_P_Dime"];
                            $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);

                            $w_m_rate = $rate[1] - 1;
                            $turn_url = "";
                            $mtype = 'OUC';
                            break;
                    }
                    $Sign = "VS.";
                    $grape = $m_place;
                    $turn = "FT_Turn_OU";
                    if ($odd_f_type == 'H') {
                        $gwin = ($w_m_rate) * $gold;
                    } else if ($odd_f_type == 'M' or $odd_f_type == 'I') {
                        if ($w_m_rate < 0) {
                            $gwin = $gold;
                        } else {
                            $gwin = ($w_m_rate) * $gold;
                        }
                    } else if ($odd_f_type == 'E') {
                        $gwin = ($w_m_rate - 1) * $gold;
                    }
                    $ptype = 'OU';
                    break;
                case 5:
                case 105:
                    $bet_type = '单双';
                    $bet_type_tw = "單雙";
                    $bet_type_en = "Odd/Even";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_EO_";
                    switch ($rtype) {
                        case "ODD":
                            $w_m_place = '单';
                            $w_m_place_tw = '單';
                            $w_m_place_en = 'odd';
                            $s_m_place = '(' . Order_Odd . ')';
                            $w_m_rate = $match_sports["S_P_Single_Rate"];
                            break;
                        case "EVEN":
                            $w_m_place = '双';
                            $w_m_place_tw = '雙';
                            $w_m_place_en = 'even';
                            $s_m_place = '(' . Order_Even . ')';
                            $w_m_rate = $match_sports["S_P_Double_Rate"];
                            break;
                    }
                    $Sign = "VS.";
                    $turn = "FT_Turn_EO";
                    $gwin = ($w_m_rate - 1) * $gold;
                    $ptype = 'EO';
                    $mtype = $rtype;
                    
                    $grape="";
                    break;
            }

            if ($odd_f_type == 'H') {

                $gwin = ($w_m_rate) * $gold;

            } else if ($odd_f_type == 'M' || $odd_f_type == 'I') {

                if ($w_m_rate < 0) {

                    $gwin = $gold;

                } else {
                    $gwin = ($w_m_rate) * $gold;
                }
            } else if ($odd_f_type == 'E') {
                
                $gwin = ($w_m_rate-1) * $gold;
            }


            if ($line == 51 or $line == 52) {
                $oddstype = $odd_f_type;
            } else {
                $oddstype = '';
            }

            $w_mb_mid = $match_sports['MB_MID'];

            $w_tg_mid = $match_sports['TG_MID'];

            $lines=$match_sports['M_League']."<br>[".$match_sports['MB_MID'].']vs['.$match_sports['TG_MID']."]<br>".$w_mb_team."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";

            $lines=$lines."<FONT color=#cc0000>$w_m_place</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";

            $m_turn = $user['M_turn'] + 0;

            return "ok";

            if ($w_m_rate == '' or $gwin <= 0 or $gwin == '') {
                $response['message'] = 'The schedule has been closed!';
                return response()->json($response, $response['status']);
            }

            $ip_addr = Utils::get_ip();

            $agent = WebAgent::where("UserName", $agents)->first();
            if (isset($agent)) {
                $d_rate = $agent['D_turn'] + 0;
                $a_point = $agent['A_Point'] + 0;
                $b_point = $agent['B_Point'] + 0;
                $c_point = $agent['C_Point'] + 0;
                $d_point = $agent['D_Point'] + 0;
            } else {
                $d_rate = 0;
                $a_point = 0;
                $b_point = 0;
                $c_point = 0;
                $d_point = 0;
            }

            $max_id = WebReportData::where('BetTime', '<', $bet_time)->max('ID');
            $num = rand(10, 50);
            $id = $max_id + $num;

            $web_system_data = WebSystemData::all();

            $order_id = show_voucher($line, $id, $web_system_data[0]);  //定单号

            if ($oddstype == '') $oddstype = 'H';

            $new_web_report_data = new WebReportData();

            $new_web_report_data->ID = $id;
            $new_web_report_data->OrderID = $order_id;
            $new_web_report_data->MID = $gid;
            $new_web_report_data->Active = $active;
            $new_web_report_data->LineType = $line;
            $new_web_report_data->Mtype = $mtype;
            $new_web_report_data->M_Date = $m_date;
            $new_web_report_data->BetTime = $bet_time;
            $new_web_report_data->BetScore = $gold;
            $new_web_report_data->Middle = $lines;
            $new_web_report_data->BetType = $bet_type;
            $new_web_report_data->M_Place = $grape;
            $new_web_report_data->M_Rate = $w_m_rate;
            $new_web_report_data->M_Name = $user_name;
            $new_web_report_data->Gwin = $gwin;
            $new_web_report_data->TurnRate = $m_turn;
            $new_web_report_data->OpenType = $open;
            $new_web_report_data->OddsType = $oddstype;
            $new_web_report_data->ShowType = $show_type;
            $new_web_report_data->Agents = $agents;
            $new_web_report_data->World = $world;
            $new_web_report_data->Corprator = $corprator;
            $new_web_report_data->Super = $super;
            $new_web_report_data->Admin = $admin;
            // $new_web_report_data->A_Rate = $a_rate;
            // $new_web_report_data->B_Rate = $b_rate;
            // $new_web_report_data->C_Rate = $c_rate;
            // $new_web_report_data->D_Rate = $d_rate;
            $new_web_report_data->A_Point = $a_point;
            $new_web_report_data->B_Point = $b_point;
            $new_web_report_data->C_Point = $c_point;
            $new_web_report_data->D_Point = $d_point;
            $new_web_report_data->BetIP = $ip_addr;
            $new_web_report_data->Ptype = $ptype;
            $new_web_report_data->Gtype = 'BK';
            $new_web_report_data->CurType = $w_current;
            $new_web_report_data->Ratio = $w_ratio;
            $new_web_report_data->MB_MID = $w_mb_mid;
            $new_web_report_data->TG_MID = $w_tg_mid;
            $new_web_report_data->Pay_Type = $pay_type;

            $new_web_report_data->save();

            $ouid = $new_web_report_data['ID'];

            $assets = $user['Money'];
            $user_id = $user['id'];

            $datetime = date("Y-m-d H:i:s");

            $user["Money"] = $assets - $gold;

            $user["withdrawal_condition"] = $user["withdrawal_condition"] - $gold <= 0 ? 0 : $user["withdrawal_condition"] - $gold;

            if ($user->save()) {
                $money_log = new MoneyLog();

                $money_log['user_id'] = $user_id;
                $money_log['order_num'] = $order_id;
                $money_log['about'] = '投注足球<br>gid:' . $gid . '<br>RID:' . $ouid;
                $money_log['update_time'] = $datetime;
                $money_log['type'] = $lines;
                $money_log['order_value'] = '-' . $gold;
                $money_log['assets'] = $assets;
                $money_log['balance'] = $user["Money"];

                $money_log->save();
            } else {
                WebReportData::where("id", $ouid)->delete();
                $response['message'] = 'If the bet is unsuccessful, please contact the customer service!';
                return response()->json($response, $response['status']);
            }

            $response['data'] = $new_web_report_data;
            $response['message'] = 'Betting Order added successfully!';
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
