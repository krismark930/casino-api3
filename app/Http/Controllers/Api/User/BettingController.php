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
use App\Models\WebSystemData;
use App\Utils\Utils;
use App\Models\Web\MoneyLog;
use Auth;

class BettingController extends Controller
{
    public function saveFTBettingOrderData(Request $request)
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
            $gold = $request_data["gold"];
            $active = $request_data["active"];
            $line_type = $request_data["line_type"];
            $gid = $request_data["m_id"];
            $type = $request_data["type"];
            $order_rate = $request_data["order_rate"];

            $configs = Config::all();

            $hg_confirm = $configs[0]['HG_Confirm'];
            $bad_name = explode(",", $configs[0]['BadMember']);
            $bad_name2 = explode(",", $configs[0]['BadMember2']);
            $bad_name3 = explode(",", $configs[0]['BadMember3']);
            $bad_name_jq = explode(",", $configs[0]['BadMember_JQ']);

            // $user_id = $request_data["id"];
            $user_id = Auth::guard("api")->user();

            $user = User::where('id', $user_id)->where('Status', 0)->first();

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

            $gold = (float)$request_data['gold'];
            $gid = $request_data['m_id'];
            $line = $request_data['line_type'];

            if ($h_money < $gold) {
                $response['message'] = 'Your available balance is insufficient, please deposit first!';
                return response()->json($response, $response['status']);
            }

            $mem_xe = getXianEr('FT', $request_data["line_type"], $user);
            $mem_xe['BET_SC'] = $mem_xe['BET_SC'] === 0 ? 500000 : $mem_xe['BET_SC'];
            $web_system_data = WebSystemData::where("ID", 1)->first();
            $bt_set = array($web_system_data['P3'], $web_system_data["MAX"]);
            $XianEr = $bt_set[0];

            $bet_score = WebReportData::where('M_Name', $user_name)->where('Cancel', 0)->where('MID', $gid)->sum('BetScore');

            if ($bet_score + $gold > $mem_xe['BET_SC']) {
                $response['message'] = 'The betting amount you entered is greater than the limit amount for a single game!';
                return response()->json($response, $response['status']);
            }

            $newDate = now()->subMinutes(6 * 60 + 90);

            $match_sports = Sport::where('MID', $gid)->whereRaw("Cancel != 1 and Open = 1 and MB_Team != '' and MB_Team_tw != '' and MB_Team_en != '' and M_Start > '" . $newDate . "'")->first();

            if (!isset($match_sports)) {
                $response['message'] = 'Schedule is closed!';
                return response()->json($response, $response['status']);
            }

            // Fetch away team names in four languages written to the database
            $w_tg_team = $match_sports['TG_Team'];
            $w_tg_team_tw = $match_sports['TG_Team_tw'];
            $w_tg_team_en = $match_sports['TG_Team_en'];

            // Take out the home team names in four languages, and remove the words "main" and "center" in them
            $w_mb_team = filiter_team(trim($match_sports['MB_Team']));
            $w_mb_team_tw = filiter_team(trim($match_sports['MB_Team_tw']));
            $w_mb_team_en = filiter_team(trim($match_sports['MB_Team_en']));

            $w_mb_mid = $match_sports['MB_MID'];
            $w_tg_mid = $match_sports['TG_MID'];

            if (strpos($w_tg_team, '角球') or strpos($w_mb_team, '角球') or strpos($w_tg_team, '点球') or strpos($w_mb_team, '点球')) {  // Block corner and penalty betting
                if (in_array($user_name, $badname_jq)) {
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
            $show_type = $match_sports["ShowTypeR"];

            if ($line === '12' or $line === '13' or $line === '14') {  // get half time
                $show_type = $match_sports["ShowTypeHR"];
            }

            $bet_time = date('Y-m-d H:i:s');

            $m_start = strtotime($match_sports['M_Start']);
            // $date_time = time();
            $date_time = now()->subMinutes(5 * 60 + 90);

            // if ($m_start - $date_time < 120){
            //     $response['message'] = 'Schedule is closed!';
            //     return response()->json($response, $response['status']);
            // }

            // Process according to the type of bet: build into a new data format, ready to write to the database

            switch ($line) {
                case 1:
                    $bet_type = '独赢';
                    $bet_type_tw = '獨贏';
                    $bet_type_en = "1x2";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_M";
                    $turn = "FT_Turn_M";
                    switch ($type) {
                        case "H":
                            $w_m_place = $w_mb_team;
                            $w_m_place_tw = $w_mb_team_tw;
                            $w_m_place_en = $w_mb_team_en;
                            $s_m_place = $s_mb_team;
                            $w_m_rate = change_rate($open, $match_sports["MB_Win_Rate"]);
                            $mtype = 'MH';
                            break;
                        case "C":
                            $w_m_place = $w_tg_team;
                            $w_m_place_tw = $w_tg_team_tw;
                            $w_m_place_en = $w_tg_team_en;
                            $s_m_place = $s_tg_team;
                            $w_m_rate = change_rate($open, $match_sports["TG_Win_Rate"]);
                            $mtype = 'MC';
                            break;
                        case "N":
                            $w_m_place = "和局";
                            $w_m_place_tw = "和局";
                            $w_m_place_en = "Flat";
                            $s_m_place = $Draw;
                            $w_m_rate = change_rate($open, $match_sports["M_Flat_Rate"]);
                            $mtype = 'MN';
                            break;
                    }
                    $Sign = "VS.";
                    $grape = "";
                    $gwin = ($w_m_rate - 1) * $gold;
                    $ptype = 'M';
                    break;
                case 2:
                    $bet_type = '让球';
                    $bet_type_tw = "讓球";
                    $bet_type_en = "Handicap";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_R_";
                    $rate = get_other_ioratio($odd_f_type, $match_sports["MB_LetB_Rate"], $match_sports["TG_LetB_Rate"], 100);

                    switch ($type) {

                        case "H":
                            $w_m_place = $w_mb_team;
                            $w_m_place_tw = $w_mb_team_tw;
                            $w_m_place_en = $w_mb_team_en;
                            $s_m_place = $s_mb_team;
                            $w_m_rate = change_rate($open, $rate[0]);
                            $turn_url = "";
                            $mtype = 'RH';
                            break;
                        case "C":
                            $w_m_place = $w_tg_team;
                            $w_m_place_tw = $w_tg_team_tw;
                            $w_m_place_en = $w_tg_team_en;
                            $s_m_place = $s_tg_team;
                            $w_m_rate = change_rate($open, $rate[1]);
                            $turn_url = "";
                            $mtype = 'RC';
                            break;
                    }

                    $Sign = $match_sports['M_LetB'];
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
                case 3:
                    $bet_type = '大小';
                    $bet_type_tw = "大小";
                    $bet_type_en = "Over/Under";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_OU_";
                    $rate = get_other_ioratio($odd_f_type, $match_sports["MB_Dime_Rate"], $match_sports["TG_Dime_Rate"], 100);
                    switch ($type) {
                        case "C":
                            $w_m_place = $match_sports["MB_Dime"];
                            $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);
                            $w_m_place_tw = $match_sports["MB_Dime"];
                            $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);
                            $w_m_place_en = $match_sports["MB_Dime"];
                            $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["MB_Dime"];

                            $s_m_place = $match_sports["MB_Dime"];
                            $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            $w_m_rate = change_rate($open, $rate[0]);
                            $turn_url = "";
                            $mtype = 'OUH';
                            break;
                        case "H":
                            $w_m_place = $match_sports["TG_Dime"];
                            $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);
                            $w_m_place_tw = $match_sports["TG_Dime"];
                            $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);
                            $w_m_place_en = $match_sports["TG_Dime"];
                            $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["TG_Dime"];

                            $s_m_place = $match_sports["TG_Dime"];
                            $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);

                            $w_m_rate = change_rate($open, $rate[1]);
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
                case 4:
                    $bet_type = '波胆';
                    $bet_type_tw = "波膽";
                    $bet_type_en = "Correct Score";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_PD";
                    if ($rtype != 'OVH') {
                        $rtype = str_replace('C', 'TG', str_replace('H', 'MB', $rtype));
                        $w_m_rate = $match_sports[$rtype];
                    } else {
                        $w_m_rate = $match_sports['UP5'];
                    }
                    if ($rtype == "OVH") {
                        $s_m_place = Order_Other_Score;
                        $w_m_place = '其它比分';
                        $w_m_place_tw = '其它比分';
                        $w_m_place_en = 'Other Score';
                        $Sign = "VS.";
                    } else {
                        $M_Place = "";
                        $M_Sign = $rtype;
                        $M_Sign = str_replace("MB", "", $M_Sign);
                        $M_Sign = str_replace("TG", ":", $M_Sign);
                        $Sign = $M_Sign . "";
                    }
                    $grape = "";
                    $turn = "FT_Turn_PD";
                    $gwin = ($w_m_rate - 1) * $gold;
                    $ptype = 'PD';
                    $mtype = $rtype;
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
                            $s_m_place = '(' . $Order_Odd . ')';
                            $w_m_rate = change_rate($open, $match_sports["S_Single_Rate"]);
                            break;
                        case "EVEN":
                            $w_m_place = '双';
                            $w_m_place_tw = '雙';
                            $w_m_place_en = 'even';
                            $s_m_place = '(' . $Order_Even . ')';
                            $w_m_rate = change_rate($open, $match_sports["S_Double_Rate"]);
                            break;
                    }
                    $Sign = "VS.";
                    $turn = "FT_Turn_EO";
                    $gwin = ($w_m_rate - 1) * $gold;
                    $ptype = 'EO';
                    $mtype = $rtype;
                    break;
                case 6:
                    $bet_type = '总入球';
                    $bet_type_tw = "總入球";
                    $bet_type_en = "Total";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_T";
                    switch ($rtype) {
                        case "0~1":
                            $w_m_place = '0~1';
                            $w_m_place_tw = '0~1';
                            $w_m_place_en = '0~1';
                            $s_m_place = '(0~1)';
                            $w_m_rate = $match_sports["S_0_1"];
                            break;
                        case "2~3":
                            $w_m_place = '2~3';
                            $w_m_place_tw = '2~3';
                            $w_m_place_en = '2~3';
                            $s_m_place = '(2~3)';
                            $w_m_rate = $match_sports["S_2_3"];
                            break;
                        case "4~6":
                            $w_m_place = '4~6';
                            $w_m_place_tw = '4~6';
                            $w_m_place_en = '4~6';
                            $s_m_place = '(4~6)';
                            $w_m_rate = $match_sports["S_4_6"];
                            break;
                        case "OVER":
                            $w_m_place = '7up';
                            $w_m_place_tw = '7up';
                            $w_m_place_en = '7up';
                            $s_m_place = '(7up)';
                            $w_m_rate = $match_sports["S_7UP"];
                            break;
                    }
                    $turn = "FT_Turn_T";
                    $Sign = "VS.";
                    $gwin = ($w_m_rate - 1) * $gold;
                    $ptype = 'T';
                    $mtype = $rtype;
                    break;
                case 7:
                    $bet_type = '半全场';
                    $bet_type_tw = "半全場";
                    $bet_type_en = "Half/Full Time";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_F";
                    switch ($rtype) {
                        case "FHH":
                            $w_m_place = $w_mb_team . '&nbsp;/&nbsp;' . $w_mb_team;
                            $w_m_place_tw = $w_mb_team_tw . '&nbsp;/&nbsp;' . $w_mb_team_tw;
                            $w_m_place_en = $w_mb_team_en . '&nbsp;/&nbsp;' . $w_mb_team_en;
                            $s_m_place = $match_sports[$mb_team] . '&nbsp;/&nbsp;' . $match_sports[$mb_team];
                            $w_m_rate = $match_sports["MBMB"];
                            break;
                        case "FHN":
                            $w_m_place = $w_mb_team . '&nbsp;/&nbsp;和局';
                            $w_m_place_tw = $w_mb_team_tw . '&nbsp;/&nbsp;和局';
                            $w_m_place_en = $w_mb_team_en . '&nbsp;/&nbsp;Flat';
                            $s_m_place = $match_sports[$mb_team] . '&nbsp;/&nbsp;' . $Draw;
                            $w_m_rate = $match_sports["MBFT"];
                            break;
                        case "FHC":
                            $w_m_place = $w_mb_team . '&nbsp;/&nbsp;' . $w_tg_team;
                            $w_m_place_tw = $w_mb_team_tw . '&nbsp;/&nbsp;' . $w_tg_team_tw;
                            $w_m_place_en = $w_mb_team_en . '&nbsp;/&nbsp;' . $w_tg_team_en;
                            $s_m_place = $match_sports[$mb_team] . '&nbsp;/&nbsp;' . $match_sports[$tg_team];
                            $w_m_rate = $match_sports["MBTG"];
                            break;
                        case "FNH":
                            $w_m_place = '和局&nbsp;/&nbsp;' . $w_mb_team;
                            $w_m_place_tw = '和局&nbsp;/&nbsp;' . $w_mb_team_tw;
                            $w_m_place_en = 'Flat&nbsp;/&nbsp;' . $w_mb_team_en;
                            $s_m_place = $Draw . '&nbsp;/&nbsp;' . $match_sports[$mb_team];
                            $w_m_rate = $match_sports["FTMB"];
                            break;
                        case "FNN":
                            $w_m_place = '和局&nbsp;/&nbsp;和局';
                            $w_m_place_tw = '和局&nbsp;/&nbsp;和局';
                            $w_m_place_en = 'Flat&nbsp;/&nbsp;Flat';
                            $s_m_place = $Draw . '&nbsp;/&nbsp;' . $Draw;
                            $w_m_rate = $match_sports["FTFT"];
                            break;
                        case "FNC":
                            $w_m_place = '和局&nbsp;/&nbsp;' . $w_tg_team;
                            $w_m_place_tw = '和局&nbsp;/&nbsp;' . $w_tg_team_tw;
                            $w_m_place_en = 'Flat&nbsp;/&nbsp;' . $w_tg_team_en;
                            $s_m_place = $Draw . '&nbsp;/&nbsp;' . $match_sports[$tg_team];
                            $w_m_rate = $match_sports["FTTG"];
                            break;
                        case "FCH":
                            $w_m_place = $w_tg_team . '&nbsp;/&nbsp;' . $w_mb_team;
                            $w_m_place_tw = $w_tg_team_tw . '&nbsp;/&nbsp;' . $w_mb_team_tw;
                            $w_m_place_en = $w_tg_team_en . '&nbsp;/&nbsp;' . $w_mb_team_en;
                            $s_m_place = $match_sports[$tg_team] . '&nbsp;/&nbsp;' . $match_sports[$mb_team];
                            $w_m_rate = $match_sports["TGMB"];
                            break;
                        case "FCN":
                            $w_m_place = $w_tg_team . '&nbsp;/&nbsp;和局';
                            $w_m_place_tw = $w_tg_team_tw . '&nbsp;/&nbsp;和局';
                            $w_m_place_en = $w_tg_team_en . '&nbsp;/&nbsp;Flat';
                            $s_m_place = $match_sports[$tg_team] . '&nbsp;/&nbsp;' . $Draw;
                            $w_m_rate = $match_sports["TGFT"];
                            break;
                        case "FCC":
                            $w_m_place = $w_tg_team . '&nbsp;/&nbsp;' . $w_tg_team;
                            $w_m_place_tw = $w_tg_team_tw . '&nbsp;/&nbsp;' . $w_tg_team_tw;
                            $w_m_place_en = $w_tg_team_en . '&nbsp;/&nbsp;' . $w_tg_team_en;
                            $s_m_place = $match_sports[$tg_team] . '&nbsp;/&nbsp;' . $match_sports[$tg_team];
                            $w_m_rate = $match_sports["TGTG"];
                            break;
                    }
                    $Sign = "VS.";
                    $turn = "FT_Turn_F";
                    $gwin = ($w_m_rate - 1) * $gold;
                    $ptype = 'F';
                    $mtype = $rtype;
                    break;
                case 11:
                    $bet_type = '半场独赢';
                    $bet_type_tw = "半場獨贏";
                    $bet_type_en = "1st Half 1x2";
                    $btype = "-&nbsp;<font color=red><b>[$Order_1st_Half]</b></font>";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_M";
                    $turn = "FT_Turn_M";
                    switch ($type) {
                        case "H":
                            $w_m_place = $w_mb_team;
                            $w_m_place_tw = $w_mb_team_tw;
                            $w_m_place_en = $w_mb_team_en;
                            $s_m_place = $match_sports[$mb_team];
                            $w_m_rate = change_rate($open, $match_sports["MB_Win_Rate_H"]);
                            $mtype = 'VMH';
                            break;
                        case "C":
                            $w_m_place = $w_tg_team;
                            $w_m_place_tw = $w_tg_team_tw;
                            $w_m_place_en = $w_tg_team_en;
                            $s_m_place = $match_sports[$tg_team];
                            $w_m_rate = change_rate($open, $match_sports["TG_Win_Rate_H"]);
                            $mtype = 'VMC';
                            break;
                        case "N":
                            $w_m_place = "和局";
                            $w_m_place_tw = "和局";
                            $w_m_place_en = "Flat";
                            $s_m_place = $Draw;
                            $w_m_rate = change_rate($open, $match_sports["M_Flat_Rate_H"]);
                            $mtype = 'VMN';
                            break;
                    }
                    $Sign = "VS.";
                    $grape = "";
                    $gwin = ($w_m_rate - 1) * $gold;
                    $ptype = 'VM';
                    break;
                case 12:
                    $bet_type = '半场让球';
                    $bet_type_tw = "半場讓球";
                    $bet_type_en = "1st Half Handicap";
                    $btype = "-&nbsp;<font color=red><b>[$Order_1st_Half]</b></font>";
                    $caption = "足球";
                    $turn_rate = "FT_Turn_R_";
                    $rate = get_other_ioratio($odd_f_type, $match_sports["MB_LetB_Rate_H"], $match_sports["TG_LetB_Rate_H"], 100);
                    switch ($type) {
                        case "H":
                            $w_m_place = $w_mb_team;
                            $w_m_place_tw = $w_mb_team_tw;
                            $w_m_place_en = $w_mb_team_en;
                            $s_m_place = $match_sports[$mb_team];
                            $w_m_rate = change_rate($open, $rate[0]);
                            $turn_url = "";
                            $mtype = 'VRH';
                            break;
                        case "C":
                            $w_m_place = $w_tg_team;
                            $w_m_place_tw = $w_tg_team_tw;
                            $w_m_place_en = $w_tg_team_en;
                            $s_m_place = $match_sports[$tg_team];
                            $w_m_rate = change_rate($open, $rate[1]);
                            $turn_url = "";
                            $mtype = 'VRC';
                            break;
                    }
                    $Sign = $match_sports['M_LetB_H'];
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
                    $ptype = 'VR';
                    break;
                case 13:
                    $bet_type = '半场大小';
                    $bet_type_tw = "半場大小";
                    $bet_type_en = "1st Half Over/Under";
                    $caption = "足球";
                    $btype = "-&nbsp;<font color=red><b>[$Order_1st_Half]</b></font>";
                    $turn_rate = "FT_Turn_OU_";
                    $rate = get_other_ioratio($odd_f_type, $match_sports["MB_Dime_Rate_H"], $match_sports["TG_Dime_Rate_H"], 100);
                    switch ($type) {
                        case "C":
                            $w_m_place = $match_sports["MB_Dime_H"];
                            $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);
                            $w_m_place_tw = $match_sports["MB_Dime_H"];
                            $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);
                            $w_m_place_en = $match_sports["MB_Dime_H"];
                            $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["MB_Dime_H"];

                            $s_m_place = $match_sports["MB_Dime_H"];
                            $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            $w_m_rate = change_rate($open, $rate[0]);
                            $turn_url = "";
                            $mtype = 'VOUH';
                            break;
                        case "H":
                            $w_m_place = $match_sports["TG_Dime_H"];
                            $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);
                            $w_m_place_tw = $match_sports["TG_Dime_H"];
                            $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);
                            $w_m_place_en = $match_sports["TG_Dime_H"];
                            $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["TG_Dime_H"];

                            $s_m_place = $match_sports["TG_Dime_H"];
                            $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            $w_m_rate = change_rate($open, $rate[1]);
                            $turn_url = "";
                            $mtype = 'VOUC';
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
                    $ptype = 'VOU';
                    break;
                case 14:
                    $bet_type = '半场波胆';
                    $bet_type_tw = "半場波膽";
                    $bet_type_en = "1st Half Correct Score";
                    $caption = "足球";
                    $btype = "-&nbsp;<font color=red><b>[$Order_1st_Half]</b></font>";
                    $turn_rate = "FT_Turn_PD";
                    if ($rtype != 'OVH') {
                        $rtype = str_replace('C', 'TG', str_replace('H', 'MB', $rtype));
                        $w_m_rate = $match_sports[$rtype . H];
                    } else {
                        $w_m_rate = $match_sports['UP5H'];
                    }
                    if ($rtype == "OVH") {
                        $s_m_place = $Order_Other_Score;
                        $w_m_place = '其它比分';
                        $w_m_place_tw = '其它比分';
                        $w_m_place_en = 'Other Score';
                        $Sign = "VS.";
                    } else {
                        $M_Place = "";
                        $M_Sign = $rtype;
                        $M_Sign = str_replace("MB", "", $M_Sign);
                        $M_Sign = str_replace("TG", ":", $M_Sign);
                        $Sign = $M_Sign . "";
                    }
                    $grape = "";
                    $turn = "FT_Turn_PD";
                    $gwin = ($w_m_rate - 1) * $gold;
                    $ptype = 'VPD';
                    $mtype = $rtype;
                    break;
            }

            if ($line == 11 or $line == 12 or $line == 13 or $line == 14) {
                $bottom1_cn = "-&nbsp;<font color=#666666>[上半]</font>&nbsp;";
                $bottom1_tw = "-&nbsp;<font color=#666666>[上半]</font>&nbsp;";
                $bottom1_en = "-&nbsp;<font color=#666666>[1st Half]</font>&nbsp;";
            }
            if ($line == 2 or $line == 3 or $line == 12 or $line == 13) {
                if ($w_m_rate != $order_rate) {
                    $response["message"] = "error";
                    return response()->json($response, $response['status']);
                }
                $oddstype = $odd_f_type;
            } else {
                $oddstype = '';
            }
            $s_m_place = filiter_team(trim($s_m_place));

            $w_mid = "<br>[" . $match_sports['MB_MID'] . "]vs[" . $match_sports['TG_MID'] . "]<br>";

            $lines=$match_sports['M_League']."<br>[".$match_sports['MB_MID'].']vs['.$match_sports['TG_MID']."]<br>".$w_mb_team."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";

            $lines=$lines."<FONT color=#cc0000>$w_m_place</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";  



            $lines_tw=$match_sports['M_League_tw']."<br>[".$match_sports['MB_MID'].']vs['.$match_sports['TG_MID']."]<br>".$w_mb_team_tw."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team_tw."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";

            $lines_tw=$lines_tw."<FONT color=#cc0000>$w_m_place_tw</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";



            $lines_en=$match_sports['M_League_en']."<br>[".$match_sports['MB_MID'].']vs['.$match_sports['TG_MID']."]<br>".$w_mb_team_en."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team_en."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";

            $lines_en=$lines_en."<FONT color=#cc0000>$w_m_place_en</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";             

            if ($w_m_rate == '' or $gwin <= 0 or $gwin == '') {
                $response['message'] = 'The schedule has been closed!';
                return response()->json($response, $response['status']);
            }

            $ip_addr = Utils::get_ip();

            $user = User::where("UserName", $user_name)->first();
            if (isset($user)) {
                $m_turn = $user['M_turn'] + 0;
            } else {
                $m_turn = "";
            }

            $super_web_agent = WebAgent::where("UserName", $super)->first();
            if (isset($super_web_agent)) {
                $a_rate = $super_web_agent['A_turn'] + 0;
            } else {
                $a_rate = "super";
            }

            $corprator_web_agent = WebAgent::where("UserName", $corprator)->first();
            if (isset($corprator_web_agent)) {
                $b_rate = $corprator_web_agent['B_turn'] + 0;
            } else {
                $b_rate = "corprator";
            }

            $world_web_agent = WebAgent::where("UserName", $world)->first();
            if (isset($world_web_agent)) {
                $c_rate = $world_web_agent['C_turn'] + 0;
            } else {
                $c_rate = "world";
            }

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

            if ($oddstype === '') $oddstype = 'H';

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
            $new_web_report_data->A_Rate = $a_rate;
            $new_web_report_data->B_Rate = $b_rate;
            $new_web_report_data->C_Rate = $c_rate;
            $new_web_report_data->D_Rate = $d_rate;
            $new_web_report_data->A_Point = $a_point;
            $new_web_report_data->B_Point = $b_point;
            $new_web_report_data->C_Point = $c_point;
            $new_web_report_data->D_Point = $d_point;
            $new_web_report_data->BetIP = $ip_addr;
            $new_web_report_data->Ptype = $ptype;
            $new_web_report_data->Gtype = 'FT';
            $new_web_report_data->CurType = $w_current;
            $new_web_report_data->Ratio = $w_ratio;
            $new_web_report_data->MB_MID = $w_mb_mid;
            $new_web_report_data->TG_MID = $w_tg_mid;
            $new_web_report_data->Pay_Type = $pay_type;

            $new_web_report_data->save();

            $ouid = $new_web_report_data['ID'];

            $assets = $user['Money'];
            $user_id = $user['id'];

            $datetime = date("Y-m-d H:i:s", time() + 12 * 3600);

            $user["Money"] = $assets - $gold;

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

    public function saveFTBettingInPlay(Request $request)
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
            $gold = $request_data["gold"];
            $active = $request_data["active"];
            $line_type = $request_data["line_type"];
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

            $gold = (float)$request_data['gold'];
            $gid = $request_data['m_id'];
            $line = $request_data['line_type'];

            if ($h_money < $gold) {
                $response['message'] = 'Your available balance is insufficient, please deposit first!';
                return response()->json($response, $response['status']);
            }

            $mem_xe = getXianEr('FT', $request_data["line_type"], $user);
            $mem_xe['BET_SC'] = $mem_xe['BET_SC'] === 0 ? 500000 : $mem_xe['BET_SC'];
            $web_system_data = WebSystemData::where("ID", 1)->first();
            $bt_set = array($web_system_data['P3'], $web_system_data["MAX"]);
            $XianEr = $bt_set[0];

            $bet_score = WebReportData::where('M_Name', $user_name)->where('Cancel', 0)->where('MID', $gid)->sum('BetScore');

            if ($bet_score + $gold > $mem_xe['BET_SC']) {
                $response['message'] = 'The betting amount you entered is greater than the limit amount for a single game!';
                return response()->json($response, $response['status']);
            }

            $newDate = now()->subMinutes(6 * 60 + 90);

            $match_sports = Sport::where('MID', $gid)->whereRaw("Open = 1 and MB_Team != ''")->first();



            if (!isset($match_sports)) {
                $response['message'] = 'Schedule is closed!';
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
                if (in_array($user_name, $badname_jq)) {
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
            $show_type = $match_sports["ShowTypeRB"];
            // return $show_type;

            $bet_time = date('Y-m-d H:i:s');

            $m_start = strtotime($match_sports['M_Start']);

            // $date_time = time();
            $date_time = now()->subMinutes(5 * 60 + 90);

            $inball = $match_sports['MB_Ball'] . ":" . $match_sports['TG_Ball'];

            $mb_ball = $match_sports['MB_Ball'];

            $tg_ball = $match_sports['TG_Ball'];

            switch ($line) {
                case 4:
                    $bet_type='波胆';
                    $bet_type_tw="波膽";
                    $bet_type_en="Correct Score";
                    $caption="足球";
                    $turn_rate="FT_Turn_PD";
                    if($rtype!='OVH'){
                        $rtype=str_replace('C','TG',str_replace('H','MB',$rtype));
                        $w_m_rate=$match_sports[$rtype];
                    }else{
                        $w_m_rate=$match_sports['UP5'];
                    }
                    if ($rtype=="OVH"){     
                        $s_m_place="Order_Other_Score";
                        $w_m_place='其它比分';
                        $w_m_place_tw='其它比分';
                        $w_m_place_en='Other Score';
                        $Sign="VS.";
                    }else{                        
                        $w_m_place='';
                        $w_m_place_tw='';
                        $w_m_place_en='';
                        $M_Place="";
                        $M_Sign=$rtype;
                        $M_Sign=str_replace("MB","",$M_Sign);
                        $M_Sign=str_replace("TG",":",$M_Sign);
                        $Sign=$M_Sign."";
                    }
                    $grape="";
                    $turn="FT_Turn_PD";
                    $gwin=($w_m_rate-1)*$gold;      
                    $ptype='PD';        
                    $mtype=$rtype;
                    $w_gtype = '';
                    break;                

                case 21:

                    $bet_type = '滚球独赢';

                    $bet_type_tw = '滾球獨贏';

                    $bet_type_en = "Running 1x2";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_M";

                    $turn = "FT_Turn_M";

                    switch ($type) {

                        case "H":

                            $w_m_place = $w_mb_team;

                            $w_m_place_tw = $w_mb_team_tw;

                            $w_m_place_en = $w_mb_team_en;

                            $s_m_place = $s_mb_team;

                            $w_m_rate = $match_sports["MB_Win_Rate_RB"];

                            $turn_url = "";

                            $w_gtype = 'RMH';

                            break;

                        case "C":

                            $w_m_place = $w_tg_team;

                            $w_m_place_tw = $w_tg_team_tw;

                            $w_m_place_en = $w_tg_team_en;

                            $s_m_place = $s_tg_team;

                            $w_m_rate = $match_sports["TG_Win_Rate_RB"];

                            $turn_url = "";

                            $w_gtype = 'RMC';

                            break;

                        case "N":

                            $w_m_place = "和局";

                            $w_m_place_tw = "和局";

                            $w_m_place_en = "Flat";

                            $s_m_place = "和局";

                            $w_m_rate = $match_sports["M_Flat_Rate_RB"];

                            $turn_url = "";

                            $w_gtype = 'RMN';

                            break;
                    }

                    $Sign = "VS.";

                    $grape = $type;

                    $gwin = ($w_m_rate - 1) * $gold;

                    $ptype = 'RM';

                    break;

                case 9:

                    $bet_type = '滚球让球';

                    $bet_type_tw = "滾球讓球";

                    $bet_type_en = "Running Ball";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_RE_";

                    $MB_LetB_Rate_RB = $match_sports["MB_LetB_Rate_RB"];

                    $TG_LetB_Rate_RB = $match_sports["TG_LetB_Rate_RB"];

                    $rate = get_other_ioratio($odd_f_type, $MB_LetB_Rate_RB, $TG_LetB_Rate_RB, 100);

                    switch ($type) {

                        case "H":

                            $w_m_place = $w_mb_team;

                            $w_m_place_tw = $w_mb_team_tw;

                            $w_m_place_en = $w_mb_team_en;

                            $s_m_place = $s_mb_team;

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $w_gtype = 'RRH';

                            break;

                        case "C":

                            $w_m_place = $w_tg_team;

                            $w_m_place_tw = $w_tg_team_tw;

                            $w_m_place_en = $w_tg_team_en;

                            $s_m_place = $s_tg_team;

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $w_gtype = 'RRC';

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

                    $turn = "FT_Turn_RE";

                    $gwin = ($w_m_rate) * $gold;

                    $ptype = 'RE';

                    break;
                case 22:

                    $bet_type = '滚球让球';

                    $bet_type_tw = "滾球讓球";

                    $bet_type_en = "Running Ball";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_RE_";

                    $MB_LetB_Rate_RB = $match_sports["IOR_REH_HDP_0"];

                    $TG_LetB_Rate_RB = $match_sports["IOR_REC_HDP_0"];

                    $rate = get_other_ioratio($odd_f_type, $MB_LetB_Rate_RB, $TG_LetB_Rate_RB, 100);

                    switch ($type) {

                        case "H":

                            $w_m_place = $w_mb_team;

                            $w_m_place_tw = $w_mb_team_tw;

                            $w_m_place_en = $w_mb_team_en;

                            $s_m_place = $s_mb_team;

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $w_gtype = 'RRH';

                            break;

                        case "C":

                            $w_m_place = $w_tg_team;

                            $w_m_place_tw = $w_tg_team_tw;

                            $w_m_place_en = $w_tg_team_en;

                            $s_m_place = $s_tg_team;

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $w_gtype = 'RRC';

                            break;
                    }

                    $Sign = $match_sports['RATIO_RE_HDP_0'];

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

                    $turn = "FT_Turn_RE";

                    $gwin = ($w_m_rate) * $gold;

                    $ptype = 'RE';

                    break;
                case 23:

                    $bet_type = '滚球让球';

                    $bet_type_tw = "滾球讓球";

                    $bet_type_en = "Running Ball";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_RE_";

                    $MB_LetB_Rate_RB = $match_sports["IOR_REH_HDP_1"];

                    $TG_LetB_Rate_RB = $match_sports["IOR_REC_HDP_1"];

                    $rate = get_other_ioratio($odd_f_type, $MB_LetB_Rate_RB, $TG_LetB_Rate_RB, 100);

                    switch ($type) {

                        case "H":

                            $w_m_place = $w_mb_team;

                            $w_m_place_tw = $w_mb_team_tw;

                            $w_m_place_en = $w_mb_team_en;

                            $s_m_place = $s_mb_team;

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $w_gtype = 'RRH';

                            break;

                        case "C":

                            $w_m_place = $w_tg_team;

                            $w_m_place_tw = $w_tg_team_tw;

                            $w_m_place_en = $w_tg_team_en;

                            $s_m_place = $s_tg_team;

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $w_gtype = 'RRC';

                            break;
                    }

                    $Sign = $match_sports['RATIO_RE_HDP_1'];

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

                    $turn = "FT_Turn_RE";

                    $gwin = ($w_m_rate) * $gold;

                    $ptype = 'RE';

                    break;
                case 24:

                    $bet_type = '滚球让球';

                    $bet_type_tw = "滾球讓球";

                    $bet_type_en = "Running Ball";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_RE_";

                    $MB_LetB_Rate_RB = $match_sports["IOR_REH_HDP_2"];

                    $TG_LetB_Rate_RB = $match_sports["IOR_REC_HDP_2"];

                    $rate = get_other_ioratio($odd_f_type, $MB_LetB_Rate_RB, $TG_LetB_Rate_RB, 100);

                    switch ($type) {

                        case "H":

                            $w_m_place = $w_mb_team;

                            $w_m_place_tw = $w_mb_team_tw;

                            $w_m_place_en = $w_mb_team_en;

                            $s_m_place = $s_mb_team;

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $w_gtype = 'RRH';

                            break;

                        case "C":

                            $w_m_place = $w_tg_team;

                            $w_m_place_tw = $w_tg_team_tw;

                            $w_m_place_en = $w_tg_team_en;

                            $s_m_place = $s_tg_team;

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $w_gtype = 'RRC';

                            break;
                    }

                    $Sign = $match_sports['IOR_REH_HDP_2'];

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

                    $turn = "FT_Turn_RE";

                    $gwin = ($w_m_rate) * $gold;

                    $ptype = 'RE';

                    break;
                case 25:

                    $bet_type = '滚球让球';

                    $bet_type_tw = "滾球讓球";

                    $bet_type_en = "Running Ball";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_RE_";

                    $MB_LetB_Rate_RB = $match_sports["IOR_REH_HDP_3"];

                    $TG_LetB_Rate_RB = $match_sports["IOR_REC_HDP_3"];

                    $rate = get_other_ioratio($odd_f_type, $MB_LetB_Rate_RB, $TG_LetB_Rate_RB, 100);

                    switch ($type) {

                        case "H":

                            $w_m_place = $w_mb_team;

                            $w_m_place_tw = $w_mb_team_tw;

                            $w_m_place_en = $w_mb_team_en;

                            $s_m_place = $s_mb_team;

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $w_gtype = 'RRH';

                            break;

                        case "C":

                            $w_m_place = $w_tg_team;

                            $w_m_place_tw = $w_tg_team_tw;

                            $w_m_place_en = $w_tg_team_en;

                            $s_m_place = $s_tg_team;

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $w_gtype = 'RRC';

                            break;
                    }

                    $Sign = $match_sports['IOR_REH_HDP_3'];

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

                    $turn = "FT_Turn_RE";

                    $gwin = ($w_m_rate) * $gold;

                    $ptype = 'RE';

                    break;                                


                case 10:

                    $bet_type = '滚球大小';

                    $bet_type_tw = "滾球大小";

                    $bet_type_en = "Running Over/Under";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_OU_";

                    $MB_Dime_Rate_RB = $match_sports["MB_Dime_Rate_RB"];

                    $TG_Dime_Rate_RB = $match_sports["TG_Dime_Rate_RB"];

                    $rate = get_other_ioratio($odd_f_type, $MB_Dime_Rate_RB, $TG_Dime_Rate_RB, 100);

                    switch ($type) {

                        case "C":

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

                            $w_gtype = 'ROUH';

                            break;

                        case "H":

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

                            $w_gtype = 'ROUC';

                            break;
                    }

                    $Sign = "VS.";

                    $grape = $m_place;

                    $turn = "FT_Turn_OU";

                    $gwin = ($w_m_rate) * $gold;

                    $ptype = 'ROU';

                    break;

                case 26:

                    $bet_type = '滚球大小';

                    $bet_type_tw = "滾球大小";

                    $bet_type_en = "Running Over/Under";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_OU_";

                    $MB_Dime_Rate_RB = $match_sports["IOR_ROUC_HDP_0"];

                    $TG_Dime_Rate_RB = $match_sports["IOR_ROUH_HDP_0"];

                    $rate = get_other_ioratio($odd_f_type, $MB_Dime_Rate_RB, $TG_Dime_Rate_RB, 100);

                    switch ($type) {

                        case "C":

                            $w_m_place = $match_sports["RATIO_ROUO_HDP_0"];

                            $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["RATIO_ROUO_HDP_0"];

                            $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["RATIO_ROUO_HDP_0"];

                            $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["RATIO_ROUO_HDP_0"];

                            $s_m_place = $match_sports["RATIO_ROUO_HDP_0"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('O', 'over&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $w_gtype = 'ROUH';

                            break;

                        case "H":

                            $w_m_place = $match_sports["RATIO_ROUO_HDP_0"];

                            $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["RATIO_ROUO_HDP_0"];

                            $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["RATIO_ROUO_HDP_0"];

                            $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["RATIO_ROUO_HDP_0"];

                            $s_m_place = $match_sports["RATIO_ROUO_HDP_0"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('U', 'under&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $w_gtype = 'ROUC';

                            break;
                    }

                    $Sign = "VS.";

                    $grape = $m_place;

                    $turn = "FT_Turn_OU";

                    $gwin = ($w_m_rate) * $gold;

                    $ptype = 'ROU';

                    break;
                    
                case 27:

                    $bet_type = '滚球大小';

                    $bet_type_tw = "滾球大小";

                    $bet_type_en = "Running Over/Under";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_OU_";

                    $MB_Dime_Rate_RB = $match_sports["IOR_ROUC_HDP_1"];

                    $TG_Dime_Rate_RB = $match_sports["IOR_ROUH_HDP_1"];

                    $rate = get_other_ioratio($odd_f_type, $MB_Dime_Rate_RB, $TG_Dime_Rate_RB, 100);

                    switch ($type) {

                        case "C":

                            $w_m_place = $match_sports["RATIO_ROUO_HDP_1"];

                            $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["RATIO_ROUO_HDP_1"];

                            $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["RATIO_ROUO_HDP_1"];

                            $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["RATIO_ROUO_HDP_1"];

                            $s_m_place = $match_sports["RATIO_ROUO_HDP_1"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('O', 'over&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $w_gtype = 'ROUH';

                            break;

                        case "H":

                            $w_m_place = $match_sports["RATIO_ROUO_HDP_1"];

                            $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["RATIO_ROUO_HDP_1"];

                            $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["RATIO_ROUO_HDP_1"];

                            $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["RATIO_ROUO_HDP_1"];

                            $s_m_place = $match_sports["RATIO_ROUO_HDP_1"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('U', 'under&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $w_gtype = 'ROUC';

                            break;
                    }

                    $Sign = "VS.";

                    $grape = $m_place;

                    $turn = "FT_Turn_OU";

                    $gwin = ($w_m_rate) * $gold;

                    $ptype = 'ROU';

                    break;
                    
                case 28:

                    $bet_type = '滚球大小';

                    $bet_type_tw = "滾球大小";

                    $bet_type_en = "Running Over/Under";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_OU_";

                    $MB_Dime_Rate_RB = $match_sports["IOR_ROUC_HDP_2"];

                    $TG_Dime_Rate_RB = $match_sports["IOR_ROUH_HDP_2"];

                    $rate = get_other_ioratio($odd_f_type, $MB_Dime_Rate_RB, $TG_Dime_Rate_RB, 100);

                    switch ($type) {

                        case "C":

                            $w_m_place = $match_sports["RATIO_ROUO_HDP_2"];

                            $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["RATIO_ROUO_HDP_2"];

                            $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["RATIO_ROUO_HDP_2"];

                            $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["RATIO_ROUO_HDP_2"];

                            $s_m_place = $match_sports["RATIO_ROUO_HDP_2"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('O', 'over&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $w_gtype = 'ROUH';

                            break;

                        case "H":

                            $w_m_place = $match_sports["RATIO_ROUO_HDP_2"];

                            $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["RATIO_ROUO_HDP_2"];

                            $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["RATIO_ROUO_HDP_2"];

                            $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["RATIO_ROUO_HDP_2"];

                            $s_m_place = $match_sports["RATIO_ROUO_HDP_2"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('U', 'under&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $w_gtype = 'ROUC';

                            break;
                    }

                    $Sign = "VS.";

                    $grape = $m_place;

                    $turn = "FT_Turn_OU";

                    $gwin = ($w_m_rate) * $gold;

                    $ptype = 'ROU';

                    break;
                    

                case 29:

                    $bet_type = '滚球大小';

                    $bet_type_tw = "滾球大小";

                    $bet_type_en = "Running Over/Under";

                    $caption = "足球";

                    $turn_rate = "FT_Turn_OU_";

                    $MB_Dime_Rate_RB = $match_sports["IOR_ROUC_HDP_3"];

                    $TG_Dime_Rate_RB = $match_sports["IOR_ROUH_HDP_3"];

                    $rate = get_other_ioratio($odd_f_type, $MB_Dime_Rate_RB, $TG_Dime_Rate_RB, 100);

                    switch ($type) {

                        case "C":

                            $w_m_place = $match_sports["RATIO_ROUO_HDP_3"];

                            $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["RATIO_ROUO_HDP_3"];

                            $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["RATIO_ROUO_HDP_3"];

                            $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["RATIO_ROUO_HDP_3"];

                            $s_m_place = $match_sports["RATIO_ROUO_HDP_3"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('O', 'over&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[0], 3);

                            $turn_url = "";

                            $w_gtype = 'ROUH';

                            break;

                        case "H":

                            $w_m_place = $match_sports["RATIO_ROUO_HDP_3"];

                            $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);

                            $w_m_place_tw = $match_sports["RATIO_ROUO_HDP_3"];

                            $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);

                            $w_m_place_en = $match_sports["RATIO_ROUO_HDP_3"];

                            $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);

                            $m_place = $match_sports["RATIO_ROUO_HDP_3"];

                            $s_m_place = $match_sports["RATIO_ROUO_HDP_3"];

                            if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "zh-cn") {

                                $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                            } else if ($langx == "en-us" or $langx == 'th-tis') {

                                $s_m_place = str_replace('U', 'under&nbsp;', $s_m_place);
                            }

                            $w_m_rate = number_format($rate[1], 3);

                            $turn_url = "";

                            $w_gtype = 'ROUC';

                            break;
                    }

                    $Sign = "VS.";

                    $grape = $m_place;

                    $turn = "FT_Turn_OU";

                    $gwin = ($w_m_rate) * $gold;

                    $ptype = 'ROU';

                    break;                
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

            $a_point = "";

            $b_point = "";

            $c_point = "";

            $d_point = "";

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
            $new_web_report_data->Mtype = $w_gtype;
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
            // $new_web_report_data->BetIP = $ip_addr;
            $new_web_report_data->Ptype = $ptype;
            $new_web_report_data->Gtype = 'FT';
            $new_web_report_data->CurType = $w_current;
            $new_web_report_data->Ratio = $w_ratio;
            $new_web_report_data->MB_MID = $w_mb_mid;
            $new_web_report_data->TG_MID = $w_tg_mid;
            $new_web_report_data->Pay_Type = $pay_type;

            $new_web_report_data->save();

            $ouid = $new_web_report_data['ID'];

            $assets = $user['Money'];
            $user_id = $user['id'];

            $datetime = date("Y-m-d H:i:s", time() + 12 * 3600);

            $user["Money"] = $assets - $gold;

            if ($user->save()) {
                $money_log = new MoneyLog();

                $money_log['user_id'] = $user_id;
                $money_log['order_num'] = $order_id;
                $money_log['about'] = '投注足球<br>gid:' . $gid . '<br>RID:' . $ouid;
                $money_log['update_time'] = $datetime;
                // $money_log['type'] = $lines;
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
