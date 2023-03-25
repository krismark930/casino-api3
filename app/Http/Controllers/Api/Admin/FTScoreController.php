<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utils\Utils;
use App\Models\Sport;
use App\Models\WebSystemData;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class FTScoreController extends Controller
{
    public function saveFTScore(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        $request_data = $request->all();

        $web_system_data = WebSystemData::where("ID", 1)->first();

        $sid = $web_system_data['Uid_tw'];
        $site = $web_system_data['datasite'];
        $settime = $web_system_data['udp_ft_score'];
        $settime = 60;
        $time = $web_system_data['udp_ft_results'];
        $list_date = date('Y-m-d',time()-$time*60*60);

        $htmlcode = Utils::decrypt($request_data["cryptedData"]);

        $jsondata = json_decode($htmlcode, true);

        $Score_arr=array();

        foreach($jsondata['results_data'] as $league => $data){
            for($i = 0; $i < count($data['gid']); $i++) {
                $Score_arr[] = $data['gid'][$i];
            }
        }

        // return $Score_arr;


        foreach($Score_arr as $key => $datainfo) {

            if ($datainfo["league_name"] !== "测试1") {

                $mid = $datainfo['gid'];                //mid
                $mb_name = trim($datainfo['team_h']);             //主队名称
                $tg_name = trim($datainfo['team_c']);             //客队名称
                $league_id = trim($datainfo['league_id']);        //联盟id
                $league_name = trim($datainfo['league_name']);    //联盟名称
                $mb_mid = trim($datainfo['num_h']);               //主队编号
                $tg_mid = trim($datainfo['num_c']);               //客队编号
                $mb_inball = $datainfo['GMH'] ?? "";        //主队全场进球数
                $mb_inball_hr = $datainfo['HGMH'] ?? "";    //客队全场进球数
                $tg_inball = $datainfo['GMC'] ??  "";       //主队上半场进球数
                $tg_inball_hr = $datainfo['HGMC'] ?? "";    //客队上半场进球数

                $date = date('Y')."-".trim($datainfo['date']);

                if($date < '2021-06-01'){
                    $date=(date('Y')+1)."-".trim($datainfo['date']);
                }

                $time = trim($datainfo['time']);
                list($hh,$mm) = explode(":",$time);
                $ap = substr($time,-1);

                if($ap == 'p' and $hh <> 12){
                    $hh += 12;
                }
                $timestamp = $date." ".$hh.":".$mm.":00";  

                // if($mb_inball == '' or $tg_inball == '' or $mb_inball_hr == '' or $tg_inball_hr == '') continue;  //比分不全

                if ($mb_inball == '赛事无PK/ 加时' or $tg_inball == '赛事无PK/ 加时'){
                    $mb_inball = '-7';
                    $tg_inball = '-7';
                }

                if ($mb_inball_hr == '赛事无PK/ 加时' or $tg_inball_hr == '赛事无PK/ 加时'){
                    $mb_inball_hr = '-7';
                    $tg_inball_hr = '-7';
                }

                if ($mb_inball == Score1 or $tg_inball == Score1){
                    $mb_inball='-1';
                    $tg_inball='-1';
                }

                if ($mb_inball_hr==Score1 or $tg_inball_hr==Score1){
                    $mb_inball_hr='-1';
                    $tg_inball_hr='-1';
                }

                if(strpos('aaa'.$mb_inball,Score2) or strpos('aaa'.$tg_inball,Score2)){
                    $mb_inball='-2';
                    $tg_inball='-2';
                }

                if(strpos('aaa'.$mb_inball_hr,Score2) or strpos('aaa'.$tg_inball_hr,Score2)){
                    $mb_inball_hr='-2';
                    $tg_inball_hr='-2';
                }

                if ($mb_inball==Score3 or $tg_inball==Score3){
                    $mb_inball='-3';
                    $tg_inball='-3';
                }

                if ($mb_inball_hr==Score3 or $tg_inball_hr==Score3){
                    $mb_inball_hr='-3';
                    $tg_inball_hr='-3';
                }

                if ($mb_inball==Score4 or $tg_inball==Score4){
                    $mb_inball='-4';
                    $tg_inball='-4';
                }

                if ($mb_inball_hr==Score4 or $tg_inball_hr==Score4){
                    $mb_inball_hr='-4';
                    $tg_inball_hr='-4';
                }

                if ($mb_inball==Score5 or $tg_inball==Score5){
                    $mb_inball='-5';
                    $tg_inball='-5';
                }

                if ($mb_inball_hr==Score5 or $tg_inball_hr==Score5){
                    $mb_inball_hr='-5';
                    $tg_inball_hr='-5';
                }

                if ($mb_inball==Score6 or $tg_inball==Score6){
                    $mb_inball='-6';
                    $tg_inball='-6';
                }

                if ($mb_inball_hr==Score6 or $tg_inball_hr==Score6){
                    $mb_inball_hr='-6';
                    $tg_inball_hr='-6';
                }

                if ($mb_inball==Score7 or $tg_inball==Score7){
                    $mb_inball='-7';
                    $tg_inball='-7';
                }

                if ($mb_inball_hr==Score7 or $tg_inball_hr==Score7){
                    $mb_inball_hr='-7';
                    $tg_inball_hr='-7';
                }

                if ($mb_inball==Score8 or $tg_inball==Score8){
                    $mb_inball='-8';
                    $tg_inball='-8';
                }

                if ($mb_inball_hr==Score8 or $tg_inball_hr==Score8){
                    $mb_inball_hr='-8';
                    $tg_inball_hr='-8';
                }

                if ($mb_inball==Score9 or $tg_inball==Score9){
                    $mb_inball='-9';
                    $tg_inball='-9';
                }

                if ($mb_inball_hr==Score9 or $tg_inball_hr==Score9){
                    $mb_inball_hr='-9';
                    $tg_inball_hr='-9';
                }

                if ($mb_inball==Score10 or $tg_inball==Score10){
                    $mb_inball='-10';
                    $tg_inball='-10';
                }

                if ($mb_inball_hr==Score10 or $tg_inball_hr==Score10){
                    $mb_inball_hr='-10';
                    $tg_inball_hr='-10';
                }

                if ($mb_inball==Score11 or $tg_inball==Score11){
                    $mb_inball='-11';
                    $tg_inball='-11';
                }

                if ($mb_inball_hr==Score11 or $tg_inball_hr==Score11){
                    $mb_inball_hr='-11';
                    $tg_inball_hr='-11';
                }

                if ($mb_inball==Score12 or $tg_inball==Score12){
                    $mb_inball='-12';
                    $tg_inball='-12';
                }

                if ($mb_inball_hr==Score12 or $tg_inball_hr==Score12){
                    $mb_inball_hr='-12';
                    $tg_inball_hr='-12';
                }

                if ($mb_inball==Score13 or $tg_inball==Score13){
                    $mb_inball='-13';
                    $tg_inball='-13';
                }

                if ($mb_inball_hr==Score13 or $tg_inball_hr==Score13){
                    $mb_inball_hr='-13';
                    $tg_inball_hr='-13';
                }

                // if ($mb_inball==Score14 or $tg_inball==Score14){
                //     $mb_inball='-14';
                //     $tg_inball='-14';
                // }

                // if ($mb_inball_hr==Score14 or $tg_inball_hr==Score14){
                //     $mb_inball_hr='-14';
                //     $tg_inball_hr='-14';
                // }

                if ($mb_inball==Score19 or $tg_inball==Score19){
                    $mb_inball='-19';
                    $tg_inball='-19';
                }

                if ($mb_inball_hr==Score19 or $tg_inball_hr==Score19){
                    $mb_inball_hr='-19';
                    $tg_inball_hr='-19';
                }

                $match_sports = Sport::where("Type", "FT")->where("MID", $mid)->where("M_Date", $list_date)->first();


                if (isset($match_sports)) {

                    if ($match_sports['MB_Inball'] == "") {
                        Sport::where("Type", "FT")
                            // ->where("GetScore", 1)
                            ->where("M_Date", $list_date)
                            ->where("MID", (int)$mid)
                            ->update([
                                "MB_Inball" => $mb_inball,
                                "TG_Inball" => $tg_inball,
                                "MB_Inball_HR" => $mb_inball_hr,
                                "TG_Inball_HR" => $tg_inball_hr
                            ]);
                    } elseif( $mb_inball < 0 || $tg_inball < 0 ) {
                        Sport::where("Type", "FT")
                            // ->where("GetScore", 1)
                            ->where("M_Date", $list_date)
                            ->where("MID", (int)$mid)
                            ->update([
                                "MB_Inball" => $mb_inball,
                                "TG_Inball" => $tg_inball,
                                "MB_Inball_HR" => $mb_inball_hr,
                                "TG_Inball_HR" => $tg_inball_hr,
                                "Cancel" => 1
                            ]);
                    } else{
                        Sport::where("Type", "FT")
                            ->where("M_Date", $list_date)
                            ->where("MID", (int)$mid)
                            ->update([
                                "MB_Inball" => $mb_inball,
                                "TG_Inball" => $tg_inball,
                                "MB_Inball_HR" => $mb_inball_hr,
                                "TG_Inball_HR" => $tg_inball_hr,
                                "Cancel" => 1
                            ]);

                        $match_sports = Sport::where("MID", $mid)->where("M_Date", $list_date)->first();

                        $a= $match_sports['MB_Inball'].$match_sports['TG_Inball'].$match_sports['MB_Inball_HR'].$match_sports['TG_Inball_HR'];
                        $b= trim($mb_inball).trim($tg_inball).trim($mb_inball_hr).trim($tg_inball_hr);
                        if($a != $b) {
                            $t=date("Y-m-d H:i:s");
                            $MID = $match_sports['MID'];
                            $M_League = $match_sports['M_League'];
                            $MB_Team = $match_sports['MB_Team'];
                            $TG_Team = $match_sports['TG_Team'];
                            $aa = $match_sports['MB_Inball_HR'].'-'.$match_sports['TG_Inball_HR'].'  '.$match_sports['MB_Inball'].'-'.$match_sports['TG_Inball'];
                            $bb = $mb_inball_hr.'-'.$tg_inball_hr.'  '.$mb_inball.'-'.$tg_inball;
                            Sport::where("Type", "FT")
                                ->where("GetScore", 1)
                                ->where("M_Date", $list_date)
                                ->where("MID", (int)$mid)
                                ->update([
                                    "MB_Inball" => $mb_inball,
                                    "TG_Inball" => $tg_inball,
                                    "MB_Inball_HR" => $mb_inball_hr,
                                    "TG_Inball_HR" => $tg_inball_hr,
                                    "Checked" => 1
                                ]);
                        }else{
                            Sport::where("Type", "FT")
                                ->where("GetScore", 1)
                                ->where("M_Date", $list_date)
                                ->where("MID", (int)$mid)
                                ->update([
                                    "MB_Inball" => $mb_inball,
                                    "TG_Inball" => $tg_inball,
                                    "MB_Inball_HR" => $mb_inball_hr,
                                    "TG_Inball_HR" => $tg_inball_hr
                                ]);
                        }
                    }

                }
            }
        }

        if (isset($jsondata)) {
            $response['data'] = $jsondata;
            $response['message'] = 'FT Score Data fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } else {
            $response['message'] = 'FT Score Data can not found!';
        }
        return response()->json($response, $response['status']); 
    }    
}
