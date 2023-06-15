<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utils\Utils;
use App\Models\Sport;
use App\Models\WebSystemData;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class BKScoreController extends Controller
{
    public function saveBKScore(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        $request_data = $request->all();

        $web_system_data = WebSystemData::where("ID", 1)->first();

        $sid = $web_system_data['Uid_tw'];
        $site = $web_system_data['datasite'];
        $settime = $web_system_data['udp_bk_score'];
        $settime = 60;
        $time = $web_system_data['udp_bk_results'];
        $list_date = date('Y-m-d');
        // $list_date = "2023-04-14";

        $htmlcode = Utils::decrypt($request_data["cryptedData"]);

        $jsondata = json_decode($htmlcode, true);

        $Score_arr=array();

        if (array_key_exists('results_data', $jsondata)) {
            
            foreach($jsondata['results_data'] as $league => $data){
                for($i = 0; $i < count($data['gid']); $i++) {
                    $Score_arr[] = $data['gid'][$i];
                }
            }
            
        }

        // return $Score_arr;


        foreach($Score_arr as $key => $datainfo) {

            if ($datainfo["league_name"] !== "测试1") {

                $mid=(int)trim($datainfo['gid']);                            //mid
                $mb_name=trim($datainfo['team_h']);                     //主队名称
                $tg_name=trim($datainfo['team_c']);                     //客队名称
                $league_id=trim($datainfo['league_id']);                //联盟id
                $league_name=trim($datainfo['league_name']);            //联盟名称
                $mb_mid=trim($datainfo['num_h']);                       //主队编号
                $tg_mid=trim($datainfo['num_c']);                       //客队编号
                $mb_inball=$datainfo['GMH']['result_h'] ?? 0;          //主队全场进球数
                $tg_inball=$datainfo['GMH']['result_c'] ?? 0;          //客队全场进球数
                $mb_inball_hr=$datainfo['GMH1']['result_h'] ?? 0;      //主队上半进球数
                $tg_inball_hr=$datainfo['GMH1']['result_c'] ?? 0;      //客队上半进球数
                $mb_inball_xb=$datainfo['GMH2']['result_h'] ?? 0;      //主队下半进球数
                $tg_inball_xb=$datainfo['GMH2']['result_c'] ?? 0;      //客队下半进球数
                $mb_inball1=$datainfo['GMH3']['result_h'] ?? 0;        //主队第1节进球数
                $tg_inball1=$datainfo['GMH3']['result_c'] ?? 0;        //客队第1节进球数
                $mb_inball2=$datainfo['GMH4']['result_h'] ?? 0;        //主队第2节进球数
                $tg_inball2=$datainfo['GMH4']['result_c'] ?? 0;        //客队第2节进球数
                $mb_inball3=$datainfo['GMH5']['result_h'] ?? 0;        //主队第3节进球数
                $tg_inball3=$datainfo['GMH5']['result_c'] ?? 0;        //客队第3节进球数
                $mb_inball4=$datainfo['GMH6']['result_h'] ?? 0;        //主队第4节进球数
                $tg_inball4=$datainfo['GMH6']['result_c'] ?? 0;        //客队第4节进球数
                $mb_inball_js=$datainfo['HGMH']['result_h'] ?? 0;      //主队加时进球数
                $tg_inball_js=$datainfo['HGMH']['result_c'] ?? 0;      //客队加时进球数

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


                if($mb_inball=='' || $tg_inball=='' || $mb_inball=='-' || $tg_inball=='-') continue;

                if ($mb_inball == Score1 or $tg_inball == Score1){
                    $mb_inball='-1';
                    $tg_inball='-1';
                }

                if($mb_inball == Score2 or $tg_inball == Score2){
                    $mb_inball='-2';
                    $tg_inball='-2';
                }

                if ($mb_inball==Score3 or $tg_inball==Score3){
                    $mb_inball='-3';
                    $tg_inball='-3';
                }

                if ($mb_inball==Score4 or $tg_inball==Score4){
                    $mb_inball='-4';
                    $tg_inball='-4';
                }

                if ($mb_inball==Score5 or $tg_inball==Score5){
                    $mb_inball='-5';
                    $tg_inball='-5';
                }

                if ($mb_inball==Score6 or $tg_inball==Score6){
                    $mb_inball='-6';
                    $tg_inball='-6';
                }

                if ($mb_inball==Score7 or $tg_inball==Score7){
                    $mb_inball='-7';
                    $tg_inball='-7';
                }

                if ($mb_inball==Score8 or $tg_inball==Score8){
                    $mb_inball='-8';
                    $tg_inball='-8';
                }

                if ($mb_inball==Score9 or $tg_inball==Score9){
                    $mb_inball='-9';
                    $tg_inball='-9';
                }

                if ($mb_inball==Score10 or $tg_inball==Score10){
                    $mb_inball='-10';
                    $tg_inball='-10';
                }

                if ($mb_inball==Score11 or $tg_inball==Score11){
                    $mb_inball='-11';
                    $tg_inball='-11';
                }

                if ($mb_inball==Score12 or $tg_inball==Score12){
                    $mb_inball='-12';
                    $tg_inball='-12';
                }

                if ($mb_inball==Score13 or $tg_inball==Score13){
                    $mb_inball='-13';
                    $tg_inball='-13';
                }

                if ($mb_inball==Score14 or $tg_inball==Score14){
                    $mb_inball='-14';
                    $tg_inball='-14';
                }

                if ($mb_inball==Score15 or $tg_inball==Score15){
                    $mb_inball='-15';
                    $tg_inball='-15';
                }

                if ($mb_inball_hr==Score15 or $tg_inball_hr==Score15){
                    $mb_inball_hr='-15';
                    $tg_inball_hr='-15';
                }

                if ($mb_inball==Score19 or $tg_inball==Score19){
                    $mb_inball='-19';
                    $tg_inball='-19';
                }


                if (!is_numeric($mb_inball) or !is_numeric($tg_inball)) {
                    continue;
                }

                //赛事取消

                if($mb_inball<0){
                    $mb_inball_hr=$mb_inball_xb=$mb_inball1=$mb_inball2=$mb_inball3=$mb_inball4=$mb_inball;
                    $tg_inball_hr=$tg_inball_xb=$tg_inball1=$tg_inball2=$tg_inball3=$tg_inball4=$tg_inball;
                }


                $match_sports = Sport::where("Type", "BK")->where("MID", (int)$mid)->where("M_Date", $list_date)->first();


                // if (isset($match_sports)) {

                //     if ($match_sports['MB_Inball'] == "") {
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("MID", (int)$mid)
                //             ->update([
                //                 "MB_Inball_HR" => $mb_inball_hr,
                //                 "TG_Inball_HR" => $tg_inball_hr,
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%上半场%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball_hr,
                //                 "TG_Inball" => $tg_inball_hr,
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%下半场%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball_xb,
                //                 "TG_Inball" => $tg_inball_xb,
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%第一节%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball1,
                //                 "TG_Inball" => $tg_inball1,
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%第二节%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball2,
                //                 "TG_Inball" => $tg_inball2,
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%第三节%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball3,
                //                 "TG_Inball" => $tg_inball3,
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%第四节%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball4,
                //                 "TG_Inball" => $tg_inball4,
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%加时%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball_js,
                //                 "TG_Inball" => $tg_inball_js,
                //             ]);
                //     } elseif( $mb_inball < 0 || $tg_inball < 0 ) {
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("MID", (int)$mid)
                //             ->update([
                //                 "MB_Inball_HR" => $mb_inball_hr,
                //                 "TG_Inball_HR" => $tg_inball_hr,
                //                 "Cancel" => 1
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%上半场%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball_hr,
                //                 "TG_Inball" => $tg_inball_hr,
                //                 "Cancel" => 1
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%下半场%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball_xb,
                //                 "TG_Inball" => $tg_inball_xb,
                //                 "Cancel" => 1
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%第一节%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball1,
                //                 "TG_Inball" => $tg_inball1,
                //                 "Cancel" => 1
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%第二节%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball2,
                //                 "TG_Inball" => $tg_inball2,
                //                 "Cancel" => 1
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%第三节%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball3,
                //                 "TG_Inball" => $tg_inball3,
                //                 "Cancel" => 1
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%第四节%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball4,
                //                 "TG_Inball" => $tg_inball4,
                //                 "Cancel" => 1
                //             ]);
                //         Sport::where("Type", "BK")
                //             // ->where("GetScore", 1)
                //             ->where("M_Date", $list_date)
                //             ->where("ECID", (int)$match_sports["ECID"])
                //             ->where('MB_Team', 'like', "%加时%")
                //             ->update([
                //                 "MB_Inball" => $mb_inball_js,
                //                 "TG_Inball" => $tg_inball_js,
                //                 "Cancel" => 1
                //             ]);
                //     } else{

                //         $a= $match_sports['MB_Inball'].$match_sports['TG_Inball'];
                //         $b= trim($mb_inball).trim($tg_inball);

                //         if($a != $b) {
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("MID", (int)$mid)
                //                 ->update([
                //                     "MB_Inball_HR" => $mb_inball_hr,
                //                     "TG_Inball_HR" => $tg_inball_hr,
                //                     "Checked" => 1
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%上半场%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball_hr,
                //                     "TG_Inball" => $tg_inball_hr,
                //                     "Checked" => 1
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%下半场%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball_xb,
                //                     "TG_Inball" => $tg_inball_xb,
                //                     "Checked" => 1
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%第一节%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball1,
                //                     "TG_Inball" => $tg_inball1,
                //                     "Checked" => 1
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%第二节%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball2,
                //                     "TG_Inball" => $tg_inball2,
                //                     "Checked" => 1
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%第三节%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball3,
                //                     "TG_Inball" => $tg_inball3,
                //                     "Checked" => 1
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%第四节%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball4,
                //                     "TG_Inball" => $tg_inball4,
                //                     "Checked" => 1
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%加时%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball_js,
                //                     "TG_Inball" => $tg_inball_js,
                //                     "Checked" => 1
                //                 ]);
                //         } else {
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("MID", (int)$mid)
                //                 ->update([
                //                     "MB_Inball_HR" => $mb_inball_hr,
                //                     "TG_Inball_HR" => $tg_inball_hr,
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%上半场%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball_hr,
                //                     "TG_Inball" => $tg_inball_hr,
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%下半场%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball_xb,
                //                     "TG_Inball" => $tg_inball_xb,
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%第一节%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball1,
                //                     "TG_Inball" => $tg_inball1,
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%第二节%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball2,
                //                     "TG_Inball" => $tg_inball2,
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%第三节%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball3,
                //                     "TG_Inball" => $tg_inball3,
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%第四节%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball4,
                //                     "TG_Inball" => $tg_inball4,
                //                 ]);
                //             Sport::where("Type", "BK")
                //                 ->where("GetScore", 1)
                //                 ->where("M_Date", $list_date)
                //                 ->where("ECID", (int)$match_sports["ECID"])
                //                 ->where('MB_Team', 'like', "%加时%")
                //                 ->update([
                //                     "MB_Inball" => $mb_inball_js,
                //                     "TG_Inball" => $tg_inball_js,
                //                 ]);
                //         }
                //     }

                //     //第一节
                //     if ($mb_inball1 > 0 && $tg_inball1 > 0){

                //         $mb_mid1 = $mb_mid + 300000;
                //         $tg_mid1 = $tg_mid + 300000;

                //         $sport = Sport::where('M_Date', $list_date)
                //             ->where('Type', 'BK')
                //             ->where('MB_MID', $mb_mid1)
                //             ->where('TG_MID', $tg_mid1)
                //             ->first();

                //         if (isset($sport)) {

                //             $mid = $sport['MID'];

                //             if ($match_sports['MB_Inball'] == "") {
                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball1,
                //                         "TG_Inball" => $tg_inball1,
                //                         "MB_Inball_HR" => $mb_inball1,
                //                         "TG_Inball_HR" => $tg_inball1
                //                     ]);
                //             } elseif( $mb_inball1 < 0 || $tg_inball1 < 0 ) {
                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball1,
                //                         "TG_Inball" => $tg_inball1,
                //                         "MB_Inball_HR" => $mb_inball1,
                //                         "TG_Inball_HR" => $tg_inball1,
                //                         "Cancel" => 1
                //                     ]);
                //             } else{

                //                 $a = $sport['MB_Inball'].$sport['TG_Inball'];
                //                 $b = trim($mb_inball1).trim($tg_inball1);

                //                 if($a != $b) {
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball1,
                //                             "TG_Inball" => $tg_inball1,
                //                             "MB_Inball_HR" => $mb_inball1,
                //                             "TG_Inball_HR" => $tg_inball1,
                //                             "Checked" => 1
                //                         ]);
                //                 }else{
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball1,
                //                             "TG_Inball" => $tg_inball1,
                //                             "MB_Inball_HR" => $mb_inball1,
                //                             "TG_Inball_HR" => $tg_inball1
                //                         ]);
                //                 }
                //             }

                //         }
                //     }

                //     //第二节
                //     if ($mb_inball2 > 0 && $tg_inball2 > 0){

                //         $mb_mid2 = $mb_mid + 400000;
                //         $tg_mid2 = $tg_mid + 400000;

                //         $sport = Sport::where('M_Date', $list_date)
                //             ->where('Type', 'BK')
                //             ->where('MB_MID', $mb_mid2)
                //             ->where('TG_MID', $tg_mid2)
                //             ->first();

                //         if (isset($sport)) {

                //             $mid = $sport['MID'];

                //             if ($match_sports['MB_Inball'] == "") {

                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball2,
                //                         "TG_Inball" => $tg_inball2,
                //                         "MB_Inball_HR" => $mb_inball2,
                //                         "TG_Inball_HR" => $tg_inball2
                //                     ]);

                //             } elseif( $mb_inball2 < 0 || $tg_inball2 < 0 ) {

                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball2,
                //                         "TG_Inball" => $tg_inball2,
                //                         "MB_Inball_HR" => $mb_inball2,
                //                         "TG_Inball_HR" => $tg_inball2,
                //                         "Cancel" => 1
                //                     ]);

                //             } else{

                //                 $a = $sport['MB_Inball'].$sport['TG_Inball'];
                //                 $b = trim($mb_inball2).trim($tg_inball2);

                //                 if($a != $b) {
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball2,
                //                             "TG_Inball" => $tg_inball2,
                //                             "MB_Inball_HR" => $mb_inball2,
                //                             "TG_Inball_HR" => $tg_inball2,
                //                             "Checked" => 1
                //                         ]);
                //                 }else{
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball2,
                //                             "TG_Inball" => $tg_inball2,
                //                             "MB_Inball_HR" => $mb_inball2,
                //                             "TG_Inball_HR" => $tg_inball2
                //                         ]);
                //                 }
                //             }

                //         }
                //     }

                //     //第3节
                //     if ($mb_inball3 > 0 && $tg_inball3 > 0){

                //         $mb_mid3 = $mb_mid + 500000;
                //         $tg_mid3 = $tg_mid + 500000;

                //         $sport = Sport::where('M_Date', $list_date)
                //             ->where('Type', 'BK')
                //             ->where('MB_MID', $mb_mid3)
                //             ->where('TG_MID', $tg_mid3)
                //             ->first();

                //         if (isset($sport)) {

                //             $mid = $sport['MID'];

                //             if ($match_sports['MB_Inball'] == "") {

                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball3,
                //                         "TG_Inball" => $tg_inball3,
                //                         "MB_Inball_HR" => $mb_inball3,
                //                         "TG_Inball_HR" => $tg_inball3
                //                     ]);

                //             } elseif( $mb_inball3 < 0 || $tg_inball3 < 0 ) {

                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball3,
                //                         "TG_Inball" => $tg_inball3,
                //                         "MB_Inball_HR" => $mb_inball3,
                //                         "TG_Inball_HR" => $tg_inball3,
                //                         "Cancel" => 1
                //                     ]);

                //             } else{

                //                 $a = $sport['MB_Inball'].$sport['TG_Inball'];
                //                 $b = trim($mb_inball3).trim($tg_inball3);

                //                 if($a != $b) {
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball3,
                //                             "TG_Inball" => $tg_inball3,
                //                             "MB_Inball_HR" => $mb_inball3,
                //                             "TG_Inball_HR" => $tg_inball3,
                //                             "Checked" => 1
                //                         ]);
                //                 }else{
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball3,
                //                             "TG_Inball" => $tg_inball3,
                //                             "MB_Inball_HR" => $mb_inball3,
                //                             "TG_Inball_HR" => $tg_inball3
                //                         ]);
                //                 }
                //             }

                //         }
                //     }

                //     //第4节
                //     if ($mb_inball4 > 0 && $tg_inball4 > 0){

                //         $mb_mid4 = $mb_mid + 600000;
                //         $tg_mid4 = $tg_mid + 600000;

                //         $sport = Sport::where('M_Date', $list_date)
                //             ->where('Type', 'BK')
                //             ->where('MB_MID', $mb_mid4)
                //             ->where('TG_MID', $tg_mid4)
                //             ->first();

                //         if (isset($sport)) {

                //             $mid = $sport['MID'];

                //             if ($match_sports['MB_Inball'] == "") {

                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball4,
                //                         "TG_Inball" => $tg_inball4,
                //                         "MB_Inball_HR" => $mb_inball4,
                //                         "TG_Inball_HR" => $tg_inball4
                //                     ]);

                //             } elseif( $mb_inball4 < 0 || $tg_inball4 < 0 ) {

                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball4,
                //                         "TG_Inball" => $tg_inball4,
                //                         "MB_Inball_HR" => $mb_inball4,
                //                         "TG_Inball_HR" => $tg_inball4,
                //                         "Cancel" => 1
                //                     ]);

                //             } else{

                //                 $a = $sport['MB_Inball'].$sport['TG_Inball'];
                //                 $b = trim($mb_inball4).trim($tg_inball4);

                //                 if($a != $b) {
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball4,
                //                             "TG_Inball" => $tg_inball4,
                //                             "MB_Inball_HR" => $mb_inball4,
                //                             "TG_Inball_HR" => $tg_inball4,
                //                             "Checked" => 1
                //                         ]);
                //                 }else{
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball4,
                //                             "TG_Inball" => $tg_inball4,
                //                             "MB_Inball_HR" => $mb_inball4,
                //                             "TG_Inball_HR" => $tg_inball4
                //                         ]);
                //                 }
                //             }

                //         }
                //     }

                //     //上半场
                //     if ($mb_inball_hr > 0 && $tg_inball_hr > 0){

                //         $mb_mid5 = $mb_mid + 800000;
                //         $tg_mid5 = $tg_mid + 800000;

                //         $sport = Sport::where('M_Date', $list_date)
                //             ->where('Type', 'BK')
                //             ->where('MB_MID', $mb_mid5)
                //             ->where('TG_MID', $tg_mid5)
                //             ->first();

                //         if (isset($sport)) {

                //             $mid = $sport['MID'];

                //             if ($match_sports['MB_Inball'] == "") {

                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball_hr,
                //                         "TG_Inball" => $tg_inball_hr,
                //                         "MB_Inball_HR" => $mb_inball_hr,
                //                         "TG_Inball_HR" => $tg_inball_hr
                //                     ]);

                //             } elseif( $mb_inball_hr < 0 || $tg_inball_hr < 0 ) {

                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball_hr,
                //                         "TG_Inball" => $tg_inball_hr,
                //                         "MB_Inball_HR" => $mb_inball_hr,
                //                         "TG_Inball_HR" => $tg_inball_hr,
                //                         "Cancel" => 1
                //                     ]);

                //             } else{

                //                 $a = $sport['MB_Inball'].$sport['TG_Inball'];
                //                 $b = trim($mb_inball_hr).trim($tg_inball_hr);

                //                 if($a != $b) {
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball_hr,
                //                             "TG_Inball" => $tg_inball_hr,
                //                             "MB_Inball_HR" => $mb_inball_hr,
                //                             "TG_Inball_HR" => $tg_inball_hr,
                //                             "Checked" => 1
                //                         ]);
                //                 }else{
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball_hr,
                //                             "TG_Inball" => $tg_inball_hr,
                //                             "MB_Inball_HR" => $mb_inball_hr,
                //                             "TG_Inball_HR" => $tg_inball_hr
                //                         ]);
                //                 }
                //             }

                //         }
                //     }

                //     //下半场
                //     if ($mb_inball_xb > 0 && $tg_inball_xb > 0){

                //         $mb_mid6 = $mb_mid + 900000;
                //         $tg_mid6 = $tg_mid + 900000;

                //         $sport = Sport::where('M_Date', $list_date)
                //             ->where('Type', 'BK')
                //             ->where('MB_MID', $mb_mid6)
                //             ->where('TG_MID', $tg_mid6)
                //             ->first();

                //         if (isset($sport)) {

                //             $mid = $sport['MID'];

                //             if ($match_sports['MB_Inball'] == "") {

                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball_xb,
                //                         "TG_Inball" => $tg_inball_xb,
                //                         "MB_Inball_HR" => $mb_inball_xb,
                //                         "TG_Inball_HR" => $tg_inball_xb
                //                     ]);

                //             } elseif( $mb_inball_xb < 0 || $tg_inball_xb < 0 ) {

                //                 Sport::where("Type", "BK")
                //                     // ->where("GetScore", 1)
                //                     ->where("M_Date", $list_date)
                //                     ->where("MID", (int)$mid)
                //                     ->update([
                //                         "MB_Inball" => $mb_inball_xb,
                //                         "TG_Inball" => $tg_inball_xb,
                //                         "MB_Inball_HR" => $mb_inball_xb,
                //                         "TG_Inball_HR" => $tg_inball_xb,
                //                         "Cancel" => 1
                //                     ]);

                //             } else{

                //                 $a = $sport['MB_Inball'].$sport['TG_Inball'];
                //                 $b = trim($mb_inball_xb).trim($tg_inball_xb);

                //                 if($a != $b) {
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball_xb,
                //                             "TG_Inball" => $tg_inball_xb,
                //                             "MB_Inball_HR" => $mb_inball_xb,
                //                             "TG_Inball_HR" => $tg_inball_xb,
                //                             "Checked" => 1
                //                         ]);
                //                 }else{
                //                     Sport::where("Type", "BK")
                //                         ->where("GetScore", 1)
                //                         ->where("M_Date", $list_date)
                //                         ->where("MID", (int)$mid)
                //                         ->update([
                //                             "MB_Inball" => $mb_inball_xb,
                //                             "TG_Inball" => $tg_inball_xb,
                //                             "MB_Inball_HR" => $mb_inball_xb,
                //                             "TG_Inball_HR" => $tg_inball_xb
                //                         ]);
                //                 }
                //             }

                //         }
                //     }

                // }
            }
        }

        if (isset($jsondata)) {
            $response['data'] = $jsondata;
            $response['message'] = 'BK Score Data fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } else {
            $response['message'] = 'BK Score Data can not found!';
        }
        return response()->json($response, $response['status']); 
    }    
}
