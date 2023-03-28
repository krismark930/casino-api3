<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\User;
use App\Models\WebSystemData;
use App\Models\Web\Report;
use App\Models\Config;
use App\Models\Web\MoneyLog;
use App\Models\Web\WebMemLogData;
use App\Models\WebReportTemp;
use App\Utils\Utils;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

include("include.php");

class SportController extends Controller
{
    public function betResumption(Request $request) {        

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'mid' => 'required|numeric',
                'g_type' => 'required|string'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $mid = $request_data["mid"];
            $g_type = $request_data["g_type"];

            $loginname = $request->user()->UserName;

            $web_report_data = Report::where("MID", $mid)
                ->get(["ID","OrderID","M_Name","Middle","Pay_Type","BetScore","M_Result","Checked"]);

            foreach($web_report_data as $item) {

                $ID = $item['ID'];
                $username = $item['M_Name'];
                $betscore = $item['BetScore'];
                $m_result = $item['M_Result'];
                $OrderID = $item['OrderID'];
                $Middle = $item['Middle'];

                Report::where('ID', $ID)
                    ->where('Gtype', $g_type)
                    ->update([
                        'VGOLD' => '',
                        'M_Result' => '',
                        'D_Result' => '',
                        'C_Result' => '',
                        'B_Result' => '',
                        'A_Result' => '',
                        'T_Result' => '',
                        'Cancel' => 0,
                        'Checked' => 0,
                        'Confirmed' => 0,
                        'Danger' => 0,
                    ]);

                //有结果

                if ($item['Checked'] == 1) {

                    $cash = $betscore + $m_result;
                    $assets = Utils::GetField($username, 'Money');

                    $ql = User::where("UserName", $username)
                        ->where("Pay_Type", 1)
                        ->decrement('Money', $cash)

                    //会员金额操作成功

                    if($q1 == 1 || $cash == 0) {

                        $balance = Utils::GetField($username,'Money');

                        $user_id = Utils::GetField($username,'ID');

                        $datetime=date("Y-m-d H:i:s", time() + 12 * 3600);

                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $OrderID;
                        $new_log->about = $loginname."审核比分恢复赛事<br>MID:".$mid."<br>RID:".$ID;
                        $new_log->update_time = $datetime;
                        $new_log->type = $Middle;
                        $new_log->order_value = $cash;
                        $new_log->assets = $assets;
                        $new_log->balance = $balance;
                        $new_log->save();

                    }
                }

            }

            Sport::where('MID', $mid)
                ->where('Type', $g_type)
                ->update([
                    'MB_Inball' => '',
                    'TG_Inball' => '',
                    'TG_Inball_HR' => '',
                    'MB_Inball_HR' => '',
                    'Cancel' => 0,
                    'Score' => 0,
                ]);

            $login_info = '恢复赛事'.$mid;

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->level = 2;

            $web_mem_log_data->save();

            $response['message'] = 'Bet Resumption Data updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function betEvent(Request $request) {        

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'mid' => 'required|numeric',
                'confirmed' => 'required|numeric',
                'g_type' => 'required|string'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $mid = $request_data["mid"];
            $confirmed = $request_data["confirmed"];
            $g_type = $request_data["g_type"];

            $loginname = $request->user()->UserName;

            $sport = Sport::where("MID", $mid)->first("Score");

            if($sport['Score'] == 1){
                if(!($confirmed == -2 or $confirmed == -99)){
                    $response["Message"] = "本场赛事已经结算，请先恢复结算再对本场赛事进行处理";
                    return response()->json($response, $response['status']);
                }
            }

            $Score_arr=array();

            $Score_arr[1]='取消';
            $Score_arr[2]='赛事腰斩';
            $Score_arr[3]='赛事改期';
            $Score_arr[4]='赛事延期';
            $Score_arr[5]='赛事延赛';
            $Score_arr[6]='赛事取消';
            $Score_arr[7]='赛事无PK加时';
            $Score_arr[8]='球员弃权';
            $Score_arr[9]='队名错误';
            $Score_arr[10]='主客场错误';
            $Score_arr[11]='先发投手更换';
            $Score_arr[12]='选手更换';
            $Score_arr[13]='联赛名称错误';
            $Score_arr[19]='提前开赛';
            //赛事腰斩 上半场赛事
            $Score_arr[99]='赛事腰斩(下半场)';

            $Memo=$Score_arr[abs($confirmed)];

            //下半腰斩
            if ( $confirmed == -99 ) {

                $confirmed=-2;

                $reports = Report::where("MID", $mid)
                    ->whereNotIn('Mtype', ['VRC','VRH','VMN','VMC','VMH','VOUH','VOUC','VRMH','VRMC','VRMN','VROUH','VROUC','VRRH','VRRC'])
                    ->where('Cancel', 0)
                    ->get(['ID','OrderID','M_Name','Pay_Type','BetScore','M_Result','Middle']);
            } else {

                $reports = Report::where("MID", $mid)
                    ->where('Cancel', 0)
                    ->get(['ID','OrderID','M_Name','Pay_Type','BetScore','M_Result','Middle']);
            }                       


            foreach($reports as $item) {

                $ID = $item['ID'];
                $username = $item['M_Name'];
                $betscore = $item['BetScore'];
                $m_result = $item['M_Result'];
                $OrderID = $item['OrderID'];
                $Middle = $item['Middle'];

                if ( $m_result == '' ) {
                    $Gold = $betscore;
                } else {
                    $Gold = -$m_result;
                }                

                Report::where('ID', $ID)
                    ->where('Gtype', $g_type)
                    ->update([
                        'VGOLD' => 0,
                        'M_Result' => 0,
                        'D_Result' => 0,
                        'C_Result' => 0,
                        'B_Result' => 0,
                        'A_Result' => 0,
                        'T_Result' => 0,
                        'Cancel' => 1,
                        'Checked' => 1,
                        'Confirmed' => $confirmed,
                        'Danger' => 0,
                    ]);

                // 获取之前资金

                $assets = Utils::GetField($username, 'Money');

                $ql = User::where("UserName", $username)
                    ->where("Pay_Type", 1)
                    ->increment('Money', (int)$betscore);

                //会员金额操作成功

                if($q1 == 1 || $Gold == 0) {

                    $balance = Utils::GetField($username,'Money');

                    $user_id = Utils::GetField($username,'ID');

                    $datetime=date("Y-m-d H:i:s", time() + 12 * 3600);

                    $new_log = new MoneyLog;
                    $new_log->user_id = $user_id;
                    $new_log->order_num = $OrderID;
                    $new_log->about = $loginname."设置为【".$Memo."】取消赛事<br>MID:".$mid."<br>RID:".$ID;
                    $new_log->update_time = $datetime;
                    $new_log->type = $Middle;
                    $new_log->order_value = $Gold;
                    $new_log->assets = $assets;
                    $new_log->balance = $balance;
                    $new_log->save();

                }

            }

            if ($confirmed == -99) {

                Sport::where('MID', $mid)
                    ->where('Type', $g_type)
                    ->update([
                        'MB_Inball' => $confirmed,
                        'TG_Inball' => $confirmed,
                        'Cancel' => 1,
                        'Score' => 1,
                    ]);

            } else {

                Sport::where('MID', $mid)
                    ->where('Type', $g_type)
                    ->update([
                        'MB_Inball' => $confirmed,
                        'TG_Inball' => $confirmed,
                        'TG_Inball_HR' => $confirmed,
                        'MB_Inball_HR' => $confirmed,
                        'Cancel' => 1,
                        'Score' => 1,
                    ]);

            }

            $login_info = '取消赛事'.$mid;

            $ip_addr = Utils::get_ip();

            $web_mem_log_data = new WebMemLogData();

            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->level = 2;

            $web_mem_log_data->save();

            $response['message'] = 'Bet Event Data updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

    public function updateSportOpen(Request $request) {        

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'm_date' => 'required|string',
                'g_type' => 'required|string',
                'open' => 'required|numeric'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $m_date = $request_data["m_date"];
            $g_type = $request_data["g_type"];
            $open = $request_data["open"];
            $mid = $request_data["mid"] ?? "";

            if ($mid === "") {
                Sport::where("Type", $g_type)->where("M_Date", $m_date)
                    ->update(['open' => $open]);
            } else {
                Sport::where("Type", $g_type)
                    ->where("M_Date", $m_date)
                    ->where("MID", $mid)
                    ->update(['open' => $open]);
            }

            $response['message'] = 'Sport Open Data updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getLeagueByDate(Request $request) {        

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'm_date' => 'required|string',
                'g_type' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $m_date = $request_data["m_date"];
            $g_type = $request_data["g_type"];

            $leagues = Sport::where("Type", $g_type)
                ->where("M_Date", $m_date)
                ->distinct()
                ->get(['M_League']);

            $response['data'] = $leagues;
            $response['message'] = 'League Data fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

    public function getItem(Request $request)
    {
        $id = $request->post('id');
        $data = Sport::select('MID', 'Type', 'M_Date', 'M_Time', 'MB_Team', 'TG_Team', 'MB_Inball', 'TG_Inball', 'MB_Inball_HR', 'TG_Inball_HR', 'M_League')
            ->where('MID', $id)->get();
        return $data;
    }

    public function getSportByOrder(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'm_date' => 'required|string',
                'g_type' => 'required|string',
                'display_type' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $m_date = $request_data["m_date"] ?? date('Y-m-d');
            $g_type = $request_data["g_type"] ?? 'FT';
            $display_type = $request_data["display_type"] ?? 2;
            $search = $request_data["search"] ?? "";
            $league = $request_data["league"] ?? "";

            $offset = $request_data['offset'] ?? 0;
            $limit = $request_data['limit'] ?? 20;

            $MIDS = "";

            $reports = Report::select('MID')->where('M_Date', $m_date)->get();

            foreach ($reports as $report) {
                $MIDS = $MIDS . $report["MID"] . ",";
            }

            $MIDS = str_replace(',,', ',', $MIDS);
            $MIDS = trim($MIDS, ',');
            $MIDS = explode(",", $MIDS);

            if ($display_type === 1) {

                //显示全部

                $items = Sport::select('MID', 'M_Date', 'M_Time', 'MB_Team', 'TG_Team', 'MB_Inball', 'TG_Inball', 'MB_Inball_HR', 'TG_Inball_HR', 'Cancel', 'Checked', 'Open', 'M_League', 'GetScore')
                    ->where('M_Date', $m_date)
                    ->where('Type', $g_type)
                    ->where('Score', 0);

                if ($league !== "") {
                    $items = $items->where('M_League', $league);
                }

                if ($search !== "") {
                    $items = $items->where(function ($query) use ($search) {
                        $query->orWhere('M_League', 'LIKE', '%'.$search.'%')
                              ->orWhere('TG_Team', 'LIKE', '%'.$search.'%')
                              ->orWhere('MB_Team', 'LIKE', '%'.$search.'%');
                    });
                }

                $items = $items->orderBy("M_Start", "asc")
                    ->orderBy("M_League", "asc")
                    ->orderBy("MB_Team", "asc")
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            } else if ($display_type === 2) {

                //显示只有投注的

                $items = Sport::select('MID', 'M_Date', 'M_Time', 'MB_Team', 'TG_Team', 'MB_Inball', 'TG_Inball', 'MB_Inball_HR', 'TG_Inball_HR', 'Cancel', 'Checked', 'Open', 'M_League','GetScore')
                    ->where('M_Date', $m_date)
                    ->where('Type', $g_type)
                    ->where('Score', 0);

                if ($league !== "")
                    $items = $items->where('M_League', $league);

                if ($search !== "") {
                    $items = $items->where(function ($query) use ($search) {
                        $query->orWhere('M_League', 'LIKE', '%'.$search.'%')
                              ->orWhere('TG_Team', 'LIKE', '%'.$search.'%')
                              ->orWhere('MB_Team', 'LIKE', '%'.$search.'%');
                    });
                }

                $items = $items->whereIn('MID', $MIDS)
                    ->orderBy("M_Start", "asc")
                    ->orderBy("M_League", "asc")
                    ->orderBy("MB_Team", "asc")
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            } else if ($display_type === 3) {

                //显示二次比分

                $items = Sport::select('MID', 'M_Date', 'M_Time', 'MB_Team', 'TG_Team', 'MB_Inball', 'TG_Inball', 'MB_Inball_HR', 'TG_Inball_HR', 'Cancel', 'Checked', 'Open', 'M_League', 'GetScore')
                    ->where('M_Date', $m_date)
                    ->where('Type', $g_type)
                    ->where('Score', 1)
                    ->where('Checked', 1);

                if ($league !== "")
                    $items = $items->where('M_League', $league);

                if ($search !== "") {
                    $items = $items->where(function ($query) use ($search) {
                        $query->orWhere('M_League', 'LIKE', '%'.$search.'%')
                              ->orWhere('TG_Team', 'LIKE', '%'.$search.'%')
                              ->orWhere('MB_Team', 'LIKE', '%'.$search.'%');
                    });
                }

                $items = $items->orderBy("M_Start", "asc")
                    ->orderBy("M_League", "asc")
                    ->orderBy("MB_Team", "asc")
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            }

            $response['data'] = $items;
            $response['message'] = 'CheckScore2 Data fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveScore(Request $request)
    {
        $item = $request->post('item');
        $mb_inball = $item['MB_Inball'];
        $tg_inball = $item['TG_Inball'];
        $mb_inball_hr = $item['MB_Inball_HR'];
        $tg_inball_hr = $item['TG_Inball_HR'];
        $gtype = $item['Type'];
        $gid = $request->post('id');
        Sport::where('Type', $gtype)->where('MID', $gid)
            ->update([
                'MB_Inball' => $mb_inball,
                'MB_Inball_HR' => $mb_inball_hr,
                'TG_Inball' => $tg_inball,
                'TG_Inball_HR' => $tg_inball_hr
            ]);
    }

    public function checkFTScore(Request $request)
    {
        $gid = $request['id'];
        $new_item = $request['item'];
        $type = $new_item['type'];
        $item = Sport::where('MID', $gid)->first();
        $mb_in_score = $new_item['MB_Inball'];
        $tg_in_score = $new_item['TG_Inball'];
        $mb_in_score_v = $new_item['MB_Inball_HR'];
        $tg_in_score_v = $new_item['TG_Inball_HR'];
        $data = array(); // return data
        if ($type == 'FT') {
            if (trim($mb_in_score) == "-" || trim($tg_in_score) == "-" || trim($mb_in_score) == "" || trim($tg_in_score) == "" || trim($mb_in_score) == "－" || trim($tg_in_score) == "－") {
                Sport::where('MID', $gid)->update(['MB_Inball' => $mb_in_score, 'TG_Inball' => $tg_in_score]);
            }
            if ($mb_in_score < 0 or $tg_in_score < 0 or $mb_in_score_v < 0 or $tg_in_score_v < 0) {
                Sport::where('MID', $gid)->update([
                    'MB_Inball' => $mb_in_score,
                    'TG_Inball' => $tg_in_score,
                    'MB_Inball_HR' => $mb_in_score_v,
                    'TG_Inball_HR' => $tg_in_score_v
                ]);
            }

            Utils::ProcessUpdate($gid, 3);

            $result = Sport::where('MID', $gid)->where('MB_Inball', '')->where('TG_Inball', '')->count();
            if ($result == 0) {
                return [
                    'code' => 'settled',
                    'message' => '本场赛事已经结算!'
                ];
            }

            //需直接传递过来比分：上半和全场，可根据实际情况分别分批传递
            $bc_arr = array('VRC', 'VRH', 'VMN', 'VMC', 'VMH', 'VOUH', 'VOUC', 'VRMH', 'VRMC', 'VRMN', 'VROUH', 'VROUC', 'VRRH', 'VRRC');
            $Score_arr = array();
            $Score_arr[1] = '取消';
            $Score_arr[2] = '赛事腰斩';
            $Score_arr[3] = '赛事改期';
            $Score_arr[4] = '赛事延期';
            $Score_arr[5] = '赛事延赛';
            $Score_arr[6] = '赛事取消';
            $Score_arr[7] = '赛事无PK加时';
            $Score_arr[8] = '球员弃权';
            $Score_arr[9] = '队名错误';
            $Score_arr[10] = '主客场错误';
            $Score_arr[11] = '先发投手更换';
            $Score_arr[12] = '选手更换';
            $Score_arr[13] = '联赛名称错误';
            $Score_arr[19] = '提前开赛';

            $result = Report::select('ID', 'MID', 'OrderID', 'Active', 'M_Name', 'LineType', 'OpenType', 'ShowType', 'Mtype', 'Gwin', 'VGOLD', 'TurnRate', 'BetType', 'M_Place', 'M_Rate', 'Middle', 'BetScore', 'A_Rate', 'B_Rate', 'C_Rate', 'D_Rate', 'A_Point', 'B_Point', 'C_Point', 'D_Point', 'Pay_Type', 'Checked')
                ->whereRaw('FIND_IN_SET(?, MID) > 0', [$gid])
                ->whereIn('Active', [1, 11])
                ->where('LineType', '!=', 8)
                ->where('Cancel', '!=', 1)
                ->where('Checked', 0)
                ->orderBy('LineType', 'asc')
                ->get();

            foreach ($result as $row_index => $row) {
                $mtype = $row['Mtype'];
                $id = $row['ID'];
                $user = $row['M_Name'];
                switch ($row['LineType']) {
                    case 1:
                        $graded = Utils::win_chk($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 50:
                        $graded = Utils::win_chk($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 2:
                        $graded = Utils::odds_letb($mb_in_score, $tg_in_score, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 51:
                        $graded = Utils::odds_letb($mb_in_score, $tg_in_score, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 3:
                        $graded = Utils::odds_dime($mb_in_score, $tg_in_score, $row['M_Place'], $row['Mtype']);
                        break;
                    case 52:
                        $graded = Utils::odds_dime($mb_in_score, $tg_in_score, $row['M_Place'], $row['Mtype']);
                        break;
                    case 4:
                        $graded = Utils::odds_pd($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 5:
                        $graded = Utils::odds_eo($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 6:
                        $graded = Utils::odds_t($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 7:
                        $graded = Utils::odds_half($mb_in_score_v, $tg_in_score_v, $mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 9:
                        $score = explode('<FONT color=red><b>', $row['Middle']);
                        $msg = explode("</b></FONT><br>", $score[1]);
                        $bcd = explode(":", $msg[0]);
                        $m_in = $bcd[0];
                        $t_in = $bcd[1];
                        if ($row['ShowType'] == 'H') {
                            $mbinscore1 = $mb_in_score - $m_in;
                            $tginscore1 = $tg_in_score - $t_in;
                        } else {
                            $mbinscore1 = $mb_in_score - $t_in;
                            $tginscore1 = $tg_in_score - $m_in;
                        }
                        $graded = Utils::odds_letb_rb($mbinscore1, $tginscore1, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 19:
                        $score = explode('<FONT color=red><b>', $row['Middle']);
                        $msg = explode("</b></FONT><br>", $score[1]);
                        $bcd = explode(":", $msg[0]);
                        $m_in = $bcd[0];
                        $t_in = $bcd[1];
                        if ($row['ShowType'] == 'H') {
                            $mbinscore1 = $mb_in_score_v - $m_in;
                            $tginscore1 = $tg_in_score_v - $t_in;
                        } else {
                            $mbinscore1 = $mb_in_score_v - $t_in;
                            $tginscore1 = $tg_in_score_v - $m_in;
                        }
                        $graded = Utils::odds_letb_vrb($mbinscore1, $tginscore1, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 10:
                        $graded = Utils::odds_dime_rb($mb_in_score, $tg_in_score, $row['M_Place'], $row['Mtype']);
                        break;
                    case 20:
                        $graded = Utils::odds_dime_vrb($mb_in_score_v, $tg_in_score_v, $row['M_Place'], $row['Mtype']);
                        break;
                    case 21:
                        $graded = Utils::win_chk_rb($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 31:
                        $graded = Utils::win_chk_vrb($mb_in_score_v, $tg_in_score_v, $row['Mtype']);
                        break;
                    case 11:
                        $graded = Utils::win_chk_v($mb_in_score_v, $tg_in_score_v, $row['Mtype']);
                        break;
                    case 12:
                        $graded = Utils::odds_letb_v($mb_in_score_v, $tg_in_score_v, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 13:
                        $graded = Utils::odds_dime_v($mb_in_score_v, $tg_in_score_v, $row['M_Place'], $row['Mtype']);
                        break;
                    case 14:
                        $graded = Utils::odds_pd_v($mb_in_score_v, $tg_in_score_v, $row['Mtype']);
                        break;
                }
                //echo $graded."-----------<br>";
                $num = 0;
                if (floatval($row['M_Rate']) < 0) {
                    $num = str_replace("-", "", $row['M_Rate']);
                } else if (floatval($row['M_Rate']) > 0) {
                    $num = 1;
                }
                switch ($graded) {
                    case 1:
                        $g_res = $row['Gwin'];
                        break;
                    case 0.5:
                        $g_res = $row['Gwin'] * 0.5;
                        break;
                    case -0.5:
                        $g_res = -$row['BetScore'] * 0.5 * $num;
                        break;
                    case -1:
                        $g_res = -$row['BetScore'] * $num;
                        break;
                    case 0:
                        $g_res = 0;
                        break;
                }

                /*$vgold=abs($graded)*$row['BetScore'];
                $betscore=$row['BetScore'];
                $turn=abs($graded)*$row['BetScore']*$row['TurnRate']/100;*/

                $betscore = $row['BetScore'];  //投注金额
                $vgold = $row['VGOLD']; //有效金额
                if (empty($vgold) or $vgold <> 0) {
                    $vgold = abs($graded) * $row['BetScore'];
                } else {
                    $vgold = 0;
                }
                $turn = abs($graded) * $vgold * intval($row['TurnRate']) / 100;  //返水

                $d_point = intval($row['D_Point']) / 100;
                $c_point = intval($row['C_Point']) / 100;
                $b_point = intval($row['B_Point']) / 100;
                $a_point = intval($row['A_Point']) / 100;

                $members = $g_res + $turn; //和会员结帐的金额

                $agents = $g_res * (1 - $d_point) + (1 - $d_point) * intval($row['D_Rate']) / 100 * intval($row['BetScore']) * abs($graded); //上缴总代理结帐的金额
                $world = $g_res * (1 - $c_point - $d_point) + (1 - $c_point - $d_point) * intval($row['C_Rate']) / 100 * $row['BetScore'] * abs($graded); //上缴股东结帐
                if (1 - $b_point - $c_point - $d_point != 0) {
                    $corprator = $g_res * (1 - $b_point - $c_point - $d_point) + (1 - $b_point - $c_point - $d_point) * intval($row['B_Rate']) / 100 * $row['BetScore'] * abs($graded); //上缴公司结帐
                } else {
                    $corprator = $g_res * ($b_point + $a_point) + ($b_point + $a_point) * intval($row['B_Rate']) / 100 * $row['BetScore'] * abs($graded); //和公司结帐
                }
                $super = $g_res * $a_point + $a_point * intval($row['A_Rate']) / 100 * $row['BetScore'] * abs($graded); //和公司结帐
                $agent = $g_res * 1 + 1 * intval($row['D_Rate']) / 100 * $row['BetScore'] * abs($graded); //公司退水帐目


                $previousAmount = Utils::GetField($user, 'Money');
                $user_id = Utils::GetField($user, 'id');
                $datetime = date("Y-m-d H:i:s", time() + 12 * 3600);
                $q1 = 0;
                if (in_array($mtype, $bc_arr)) {
                    $isQC = 0;
                } else {
                    $isQC = 1;
                }  //是否全场赛事注单
                if ($mb_in_score_v < 0 and $mb_in_score < 0) {
                    $BiFen = "半场:" . $Score_arr[abs($mb_in_score_v)] . " 全场:" . $Score_arr[abs($mb_in_score)];
                } elseif ($mb_in_score < 0) {
                    $BiFen = "半场:$mb_in_score_v-$tg_in_score_v 全场:" . $Score_arr[abs($mb_in_score)];
                } else {
                    $BiFen = "半场:$mb_in_score_v-$tg_in_score_v 全场:$mb_in_score-$tg_in_score";
                }
                if (($mb_in_score < 0 and $isQC == 1) or $mb_in_score_v < 0) {

                    //取消注单  全场比分为“取消”只取消全场  半场比分取消：全部取消

                    if ($row['Checked'] == 0) {
                        if ($row['Pay_Type'] == 1) {
                            $cash = $row['BetScore'];
                            Utils::ProcessUpdate($user);  //防止并发
                            $q1 = User::where('UserName', $user)->increment('Money', $cash);
                        }
                    }

                    if ($q1 == 1) {
                        $currentAmount = Utils::GetField($user, 'Money');
                        $Order_Code = $row['OrderID'];
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = $Order_Code;
                        $new_log->about =  "loginname" . "系统取消赛事($BiFen)<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        $new_log->update_time = $datetime;
                        $new_log->type = $row['Middle'];
                        $new_log->order_value = $cash;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        $new_log->save();

                        Report::where('ID', $id)->whereIn('active', [1, 11])->where('LineType', '!=', 8)
                            ->update([
                                'VGOLD' => 0,
                                'M_Result' => 0,
                                'D_Result' => 0,
                                'C_Result' => 0,
                                'B_Result' => 0,
                                'A_Result' => 0,
                                'T_Result' => 0,
                                'Cancel' => 1,
                                'Checked' => 1,
                                'Confirmed' => $mb_in_score
                            ]);
                    }
                } else {  //结算注单
                    $cash = 0;
                    if ($row['Checked'] == 0) {
                        if ($row['Pay_Type'] == 1) {
                            $cash = $row['BetScore'] + $members;
                            Utils::ProcessUpdate($user);  //防止并发
                            $q1 = User::where('UserName', $user)->increment('Money', $cash);
                        }
                    }
                    if ($q1 == 1 or $cash == 0) {
                        $currentAmount = Utils::GetField($user, 'Money');
                        $Order_Code = $row['OrderID'];
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = "$Order_Code";
                        $new_log->update_time = $datetime;
                        $new_log->type = $row['Middle'];
                        $new_log->order_value = $cash;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        if ($cash < $row['BetScore']) {
                            $new_log->about =  "系统取消赛事($BiFen)<br>输<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        } else {
                            $new_log->about =  "系统取消赛事($BiFen)<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        }
                        $new_log->save();

                        Report::where('ID', $id)
                            ->update([
                                'VGOLD' => $vgold,
                                'M_Result' => $members,
                                'D_Result' => $agents,
                                'C_Result' => $world,
                                'B_Result' => $corprator,
                                'A_Result' => $super,
                                'T_Result' => $agent,
                                'Checked' => 1
                            ]);
                    }
                }
            }
            Sport::where('Type', 'FT')->where('MID', $gid)->update(['Score' => 1]);

            switch ($row['OddsType']) {
                case 'H':
                    $Odds = '<BR><font color =green>' . Utils::Rep_HK . '</font>';
                    break;
                case 'M':
                    $Odds = '<BR><font color =green>' . Utils::Rep_Malay . '</font>';
                    break;
                case 'I':
                    $Odds = '<BR><font color =green>' . Utils::Rep_Indo . '</font>';
                    break;
                case 'E':
                    $Odds = '<BR><font color =green>' . Utils::Rep_Euro . '</font>';
                    break;
                case '':
                    $Odds = '';
                    break;
            }

            $time = $row['BetTime'];
            $times = date("Y-m-d", $time) . '<br>' . date("H:i:s", $time);

            $temp = array(
                'field_count' => $row_index,
                'times' => $times,
                'M_Name' => $row['M_Name'],
                'OpenType' => $row['OpenType'],
                'TurnRate' => $row['TurnRate'],
                'Mnu_Soccer' => Utils::Mnu_Soccer,
                'Odds' => $Odds,
                'LineType' => $row['LineType'],
                'BetType' => $row['BetType'],
                'voucher' => Utils::show_voucher($row['LineType'], $row['ID']),
                'Middle' => $row['Middle'],
                'BetScore' => $row['BetScore'],
                'd_point' => $d_point,
                'c_point' => $c_point,
                'b_point' => $b_point,
                'a_point' => $a_point,
                'turn' => $turn,
                'g_res' => $g_res,
                'actual_amount' => $members,
                'agents' => $agents,
                'world' => $world,
                'corprator' => $corprator,
                'pay_type' => $row['Pay_Type'],
                'memname' => $row['M_Name'],
                'BetScore' => $row['BetScore'],
                'id' => $row['ID'],
                'mb_inball' => $mb_in_score,
                'tg_inball' => $tg_in_score,
                'mb_inball_v' => $mb_in_score_v,
                'tg_inball_v' => $tg_in_score_v,
                'gtype' => $item['Type'],
                'gid' => $gid,
            );
            array_push($data, $temp);
        }
        return json_encode($data);
    }

    public function checkBKScore(Request $request)
    {

        $gid = $request['id'];
        $new_item = $request['item'];
        $type = $new_item['type'];
        $item = Sport::where('MID', $gid)->first();
        $mb_in_score = $new_item['MB_Inball'];
        $tg_in_score = $new_item['TG_Inball'];
        $mb_in_score_v = $new_item['MB_Inball_HR'];
        $tg_in_score_v = $new_item['TG_Inball_HR'];

        $data = array();

        if ($type == 'BK') {
            if (trim($mb_in_score) == "-" || trim($tg_in_score) == "-" || trim($mb_in_score) == "" || trim($tg_in_score) == "" || trim($mb_in_score) == "－" || trim($tg_in_score) == "－") {
                Sport::where('MID', $gid)->update(['MB_Inball' => $mb_in_score, 'TG_Inball' => $tg_in_score]);
            }
            if ($mb_in_score < 0 or $tg_in_score < 0 or $mb_in_score_v < 0 or $tg_in_score_v < 0) {
                Sport::where('MID', $gid)->update([
                    'MB_Inball' => $mb_in_score,
                    'TG_Inball' => $tg_in_score,
                    'MB_Inball_HR' => $mb_in_score_v,
                    'TG_Inball_HR' => $tg_in_score_v
                ]);
            }

            Utils::ProcessUpdate($gid, 3);

            $result = Sport::where('MID', $gid)->where('MB_Inball', '')->where('TG_Inball', '')->count();
            if ($result == 0) {
                return [
                    'code' => 'settled',
                    'message' => '本场赛事已经结算!'
                ];
            }

            //需直接传递过来比分：上半和全场，可根据实际情况分别分批传递
            $bc_arr = array('VRC', 'VRH', 'VMN', 'VMC', 'VMH', 'VOUH', 'VOUC', 'VRMH', 'VRMC', 'VRMN', 'VROUH', 'VROUC', 'VRRH', 'VRRC');
            $Score_arr = array();
            $Score_arr[1] = '取消';
            $Score_arr[2] = '赛事腰斩';
            $Score_arr[3] = '赛事改期';
            $Score_arr[4] = '赛事延期';
            $Score_arr[5] = '赛事延赛';
            $Score_arr[6] = '赛事取消';
            $Score_arr[7] = '赛事无PK加时';
            $Score_arr[8] = '球员弃权';
            $Score_arr[9] = '队名错误';
            $Score_arr[10] = '主客场错误';
            $Score_arr[11] = '先发投手更换';
            $Score_arr[12] = '选手更换';
            $Score_arr[13] = '联赛名称错误';
            $Score_arr[19] = '提前开赛';

            $result = Report::select('ID', 'MID', 'OrderID', 'Active', 'M_Name', 'LineType', 'OpenType', 'ShowType', 'Mtype', 'Gwin', 'VGOLD', 'TurnRate', 'BetType', 'M_Place', 'M_Rate', 'Middle', 'BetScore', 'A_Rate', 'B_Rate', 'C_Rate', 'D_Rate', 'A_Point', 'B_Point', 'C_Point', 'D_Point', 'Pay_Type', 'Checked')
                ->whereRaw('FIND_IN_SET(?, MID) > 0', [$gid])
                ->whereIn('Active', [2, 22])
                ->where('LineType', '!=', 8)
                ->where('Cancel', '!=', 1)
                ->where('Checked', 0)
                ->orderBy('LineType', 'asc')
                ->get();

            foreach ($result as $row_index => $row) {
                $mtype = $row['Mtype'];
                $id = $row['ID'];
                $user = $row['M_Name'];
                switch ($row['LineType']) {
                    case 2:
                        $graded = Utils::odds_letb($mb_in_score, $tg_in_score, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 3:
                        $graded = Utils::odds_dime($mb_in_score, $tg_in_score, $row['M_Place'], $row['Mtype']);
                        break;
                    case 5:
                        $graded = Utils::odds_eo($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 9:
                        $score = explode('<FONT color=red><b>', $row['Middle']);
                        $msg = explode("</b></FONT><br>", $score[1]);
                        $bcd = explode(":", $msg[0]);
                        $m_in = $bcd[0];
                        $t_in = $bcd[1];
                        if ($row['ShowType'] == 'H') {
                            $mbinscore1 = $mb_in_score - $m_in;
                            $tginscore1 = $tg_in_score - $t_in;
                        } else {
                            $mbinscore1 = $mb_in_score - $t_in;
                            $tginscore1 = $tg_in_score - $m_in;
                        }
                        $graded = Utils::odds_letb_rb($mbinscore1, $tginscore1, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 10:
                        $graded = Utils::odds_dime_rb($mb_in_score, $tg_in_score, $row['M_Place'], $row['Mtype']);
                        break;
                }
                //echo $graded."-----------<br>";
                $num = 0;
                if (floatval($row['M_Rate']) < 0) {
                    $num = str_replace("-", "", $row['M_Rate']);
                } else if (floatval($row['M_Rate']) > 0) {
                    $num = 1;
                }
                switch ($graded) {
                    case 1:
                        $g_res = $row['Gwin'];
                        break;
                    case 0.5:
                        $g_res = $row['Gwin'] * 0.5;
                        break;
                    case -0.5:
                        $g_res = -$row['BetScore'] * 0.5 * $num;
                        break;
                    case -1:
                        $g_res = -$row['BetScore'] * $num;
                        break;
                    case 0:
                        $g_res = 0;
                        break;
                }

                /*$vgold=abs($graded)*$row['BetScore'];
                $betscore=$row['BetScore'];
                $turn=abs($graded)*$row['BetScore']*$row['TurnRate']/100;*/

                $betscore = $row['BetScore'];  //投注金额
                $vgold = $row['VGOLD']; //有效金额
                if (empty($vgold) or $vgold <> 0) {
                    $vgold = abs($graded) * $row['BetScore'];
                } else {
                    $vgold = 0;
                }
                $turn = abs($graded) * $vgold * intval($row['TurnRate']) / 100;  //返水

                $d_point = intval($row['D_Point']) / 100;
                $c_point = intval($row['C_Point']) / 100;
                $b_point = intval($row['B_Point']) / 100;
                $a_point = intval($row['A_Point']) / 100;

                $members = $g_res + $turn; //和会员结帐的金额

                $agents = $g_res * (1 - $d_point) + (1 - $d_point) * intval($row['D_Rate']) / 100 * intval($row['BetScore']) * abs($graded); //上缴总代理结帐的金额
                $world = $g_res * (1 - $c_point - $d_point) + (1 - $c_point - $d_point) * intval($row['C_Rate']) / 100 * $row['BetScore'] * abs($graded); //上缴股东结帐
                if (1 - $b_point - $c_point - $d_point != 0) {
                    $corprator = $g_res * (1 - $b_point - $c_point - $d_point) + (1 - $b_point - $c_point - $d_point) * intval($row['B_Rate']) / 100 * $row['BetScore'] * abs($graded); //上缴公司结帐
                } else {
                    $corprator = $g_res * ($b_point + $a_point) + ($b_point + $a_point) * intval($row['B_Rate']) / 100 * $row['BetScore'] * abs($graded); //和公司结帐
                }
                $super = $g_res * $a_point + $a_point * intval($row['A_Rate']) / 100 * $row['BetScore'] * abs($graded); //和公司结帐
                $agent = $g_res * 1 + 1 * intval($row['D_Rate']) / 100 * $row['BetScore'] * abs($graded); //公司退水帐目


                $previousAmount = Utils::GetField($user, 'Money');
                $user_id = Utils::GetField($user, 'id');
                $datetime = date("Y-m-d H:i:s", time() + 12 * 3600);
                $q1 = 0;
                if (in_array($mtype, $bc_arr)) {
                    $isQC = 0;
                } else {
                    $isQC = 1;
                }  //是否全场赛事注单
                if ($mb_in_score_v < 0 and $mb_in_score < 0) {
                    $BiFen = "半场:" . $Score_arr[abs($mb_in_score_v)] . " 全场:" . $Score_arr[abs($mb_in_score)];
                } elseif ($mb_in_score < 0) {
                    $BiFen = "半场:$mb_in_score_v-$tg_in_score_v 全场:" . $Score_arr[abs($mb_in_score)];
                } else {
                    $BiFen = "半场:$mb_in_score_v-$tg_in_score_v 全场:$mb_in_score-$tg_in_score";
                }
                //取消注单  全场比分为“取消”只取消全场  半场比分取消：全部取消
                if (($mb_in_score < 0 and $isQC == 1) or $mb_in_score_v < 0) {
                    if ($row['Checked'] == 0) {
                        if ($row['Pay_Type'] == 1) {
                            $cash = $row['BetScore'];
                            Utils::ProcessUpdate($user);  //防止并发
                            $q1 = User::where('UserName', $user)->increment('Money', $cash);
                        }
                    }

                    if ($q1 == 1) {
                        $currentAmount = Utils::GetField($user, 'Money');
                        $Order_Code = $row['OrderID'];
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = "$Order_Code";
                        $new_log->about =  "loginname" . "系统取消赛事($BiFen)<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        $new_log->update_time = $datetime;
                        $new_log->type = $row['Middle'];
                        $new_log->order_value = $cash;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        $new_log->save();

                        Report::where('ID', $id)->whereIn('active', [1, 11])->where('LineType', '!=', 8)
                            ->update([
                                'VGOLD' => 0,
                                'M_Result' => 0,
                                'D_Result' => 0,
                                'C_Result' => 0,
                                'B_Result' => 0,
                                'A_Result' => 0,
                                'T_Result' => 0,
                                'Cancel' => 1,
                                'Checked' => 1,
                                'Confirmed' => $mb_in_score
                            ]);
                    }
                } else {  //结算注单
                    $cash = 0;
                    if ($row['Checked'] == 0) {
                        if ($row['Pay_Type'] == 1) {
                            $cash = $row['BetScore'] + $members;
                            Utils::ProcessUpdate($user);  //防止并发
                            $q1 = User::where('UserName', $user)->increment('Money', $cash);
                        }
                    }
                    if ($q1 == 1 or $cash == 0) {
                        $currentAmount = Utils::GetField($user, 'Money');
                        $Order_Code = $row['OrderID'];
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = "$Order_Code";
                        $new_log->update_time = $datetime;
                        $new_log->type = $row['Middle'];
                        $new_log->order_value = $cash;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        if ($cash < $row['BetScore']) {
                            $new_log->about =  "系统取消赛事($BiFen)<br>输<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        } else {
                            $new_log->about =  "系统取消赛事($BiFen)<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        }
                        $new_log->save();

                        Report::where('ID', $id)
                            ->update([
                                'VGOLD' => $vgold,
                                'M_Result' => $members,
                                'D_Result' => $agents,
                                'C_Result' => $world,
                                'B_Result' => $corprator,
                                'A_Result' => $super,
                                'T_Result' => $agent,
                                'Checked' => 1
                            ]);
                    }
                }
            }
            Sport::where('Type', 'BK')->where('MID', $gid)->update(['Score' => 1]);

            switch ($row['OddsType']) {
                case 'H':
                    $Odds = '<BR><font color =green>' . Utils::Rep_HK . '</font>';
                    break;
                case 'M':
                    $Odds = '<BR><font color =green>' . Utils::Rep_Malay . '</font>';
                    break;
                case 'I':
                    $Odds = '<BR><font color =green>' . Utils::Rep_Indo . '</font>';
                    break;
                case 'E':
                    $Odds = '<BR><font color =green>' . Utils::Rep_Euro . '</font>';
                    break;
                case '':
                    $Odds = '';
                    break;
            }

            $time = $row['BetTime'];
            $times = date("Y-m-d", $time) . '<br>' . date("H:i:s", $time);

            $temp = array(
                'field_count' => $row_index,
                'times' => $times,
                'M_Name' => $row['M_Name'],
                'OpenType' => $row['OpenType'],
                'TurnRate' => $row['TurnRate'],
                'Mnu_Soccer' => Utils::Mnu_Soccer,
                'Odds' => $Odds,
                'LineType' => $row['LineType'],
                'BetType' => $row['BetType'],
                'voucher' => Utils::show_voucher($row['LineType'], $row['ID']),
                'Middle' => $row['Middle'],
                'BetScore' => $row['BetScore'],
                'd_point' => $d_point,
                'c_point' => $c_point,
                'b_point' => $b_point,
                'a_point' => $a_point,
                'turn' => $turn,
                'g_res' => $g_res,
                'actual_amount' => $members,
                'agents' => $agents,
                'world' => $world,
                'corprator' => $corprator,
                'pay_type' => $row['Pay_Type'],
                'memname' => $row['M_Name'],
                'BetScore' => $row['BetScore'],
                'id' => $row['ID'],
                'mb_inball' => $mb_in_score,
                'tg_inball' => $tg_in_score,
                'mb_inball_v' => $mb_in_score_v,
                'tg_inball_v' => $tg_in_score_v,
                'gtype' => $item['Type'],
                'gid' => $gid,
            );
            array_push($data, $temp);
        }
        return json_encode($data);
    }

    public function autoFTCheckScore()
    {
        $web_system_data = WebSystemData::all();
        $settime = $web_system_data[0]['udp_ft_score'];
        $time = $web_system_data[0]['udp_ft_results'];
        $date = date('Y-m-d', time() - $time * 60 * 60);
        $mDate = date('Y-m-d', time() - $time * 60 * 60);
        $bc_arr = array('VRC', 'VRH', 'VMN', 'VMC', 'VMH', 'VOUH', 'VOUC', 'VRMH', 'VRMC', 'VRMN', 'VROUH', 'VROUC', 'VRRH', 'VRRC');
        $Score_arr = array();
        $Score_arr[1] = '取消';
        $Score_arr[2] = '赛事腰斩';
        $Score_arr[3] = '赛事改期';
        $Score_arr[4] = '赛事延期';
        $Score_arr[5] = '赛事延赛';
        $Score_arr[6] = '赛事取消';
        $Score_arr[7] = '赛事无PK加时';
        $Score_arr[8] = '球员弃权';
        $Score_arr[9] = '队名错误';
        $Score_arr[10] = '主客场错误';
        $Score_arr[11] = '先发投手更换';
        $Score_arr[12] = '选手更换';
        $Score_arr[13] = '联赛名称错误';
        $Score_arr[19] = '提前开赛';

        $match_sports = Sport::where("Type", "FT")
            ->where("M_Date", $mDate)
            ->where("MB_Inball", "!=", "")
            ->where("Score", 0)
            ->orderBy('M_Start', 'asc')
            ->orderBy('MID', 'asc')
            ->get(['MID', 'MB_MID', 'TG_MID', 'MB_Team', 'TG_Team', 'MB_Inball', 'TG_Inball', 'MB_Inball_HR', 'TG_Inball_HR']);

        foreach ($match_sports as $match_sport) {

            $gid = $match_sport['MID'];
            $mb_in_score = $match_sport['MB_Inball'];
            $tg_in_score = $match_sport['TG_Inball'];
            $mb_in_score_v = $match_sport['MB_Inball_HR'];
            $tg_in_score_v = $match_sport['TG_Inball_HR'];

            Utils::ProcessUpdate($gid, 3);  //防止并发处理

            $reports = Report::select('ID', 'MID', 'OrderID', 'Active', 'M_Name', 'LineType', 'OpenType', 'ShowType', 'Mtype', 'Gwin', 'VGOLD', 'TurnRate', 'BetType', 'M_Place', 'M_Rate', 'Middle', 'BetScore', 'A_Rate', 'B_Rate', 'C_Rate', 'D_Rate', 'A_Point', 'B_Point', 'C_Point', 'D_Point', 'Pay_Type', 'Checked')
                ->whereRaw('FIND_IN_SET(?, MID) > 0', [$gid])
                ->whereIn('Active', [1, 11])
                ->where('LineType', '!=', 8)
                ->where('Cancel', '!=', 1)
                ->where('Checked', 0)
                ->orderBy('LineType', 'asc')
                ->get();

            foreach ($reports as $row_index => $row) {
                $mtype = $row['Mtype'];
                $id = $row['ID'];
                $user = $row['M_Name'];
                switch ($row['LineType']) {
                    case 1:
                        $graded = Utils::win_chk($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 50:
                        $graded = Utils::win_chk($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 2:
                        $graded = Utils::odds_letb($mb_in_score, $tg_in_score, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 51:
                        $graded = Utils::odds_letb($mb_in_score, $tg_in_score, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 3:
                        $graded = Utils::odds_dime($mb_in_score, $tg_in_score, $row['M_Place'], $row['Mtype']);
                        break;
                    case 52:
                        $graded = Utils::odds_dime($mb_in_score, $tg_in_score, $row['M_Place'], $row['Mtype']);
                        break;
                    case 4:
                        $graded = Utils::odds_pd($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 5:
                        $graded = Utils::odds_eo($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 6:
                        $graded = Utils::odds_t($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 7:
                        $graded = Utils::odds_half($mb_in_score_v, $tg_in_score_v, $mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 9:
                        $score = explode('<FONT color=red><b>', $row['Middle']);
                        $msg = explode("</b></FONT><br>", $score[1]);
                        $bcd = explode(":", $msg[0]);
                        $m_in = $bcd[0];
                        $t_in = $bcd[1];
                        if ($row['ShowType'] == 'H') {
                            $mbinscore1 = $mb_in_score - $m_in;
                            $tginscore1 = $tg_in_score - $t_in;
                        } else {
                            $mbinscore1 = $mb_in_score - $t_in;
                            $tginscore1 = $tg_in_score - $m_in;
                        }
                        $graded = Utils::odds_letb_rb($mbinscore1, $tginscore1, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 19:
                        $score = explode('<FONT color=red><b>', $row['Middle']);
                        $msg = explode("</b></FONT><br>", $score[1]);
                        $bcd = explode(":", $msg[0]);
                        $m_in = $bcd[0];
                        $t_in = $bcd[1];
                        if ($row['ShowType'] == 'H') {
                            $mbinscore1 = $mb_in_score_v - $m_in;
                            $tginscore1 = $tg_in_score_v - $t_in;
                        } else {
                            $mbinscore1 = $mb_in_score_v - $t_in;
                            $tginscore1 = $tg_in_score_v - $m_in;
                        }
                        $graded = Utils::odds_letb_vrb($mbinscore1, $tginscore1, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 10:
                        $graded = Utils::odds_dime_rb($mb_in_score, $tg_in_score, $row['M_Place'], $row['Mtype']);
                        break;
                    case 20:
                        $graded = Utils::odds_dime_vrb($mb_in_score_v, $tg_in_score_v, $row['M_Place'], $row['Mtype']);
                        break;
                    case 21:
                        $graded = Utils::win_chk_rb($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 31:
                        $graded = Utils::win_chk_vrb($mb_in_score_v, $tg_in_score_v, $row['Mtype']);
                        break;
                    case 11:
                        $graded = Utils::win_chk_v($mb_in_score_v, $tg_in_score_v, $row['Mtype']);
                        break;
                    case 12:
                        $graded = Utils::odds_letb_v($mb_in_score_v, $tg_in_score_v, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 13:
                        $graded = Utils::odds_dime_v($mb_in_score_v, $tg_in_score_v, $row['M_Place'], $row['Mtype']);
                        break;
                    case 14:
                        $graded = Utils::odds_pd_v($mb_in_score_v, $tg_in_score_v, $row['Mtype']);
                        break;
                }
                //echo $graded."-----------<br>";
                $num = 0;
                if (floatval($row['M_Rate']) < 0) {
                    $num = str_replace("-", "", $row['M_Rate']);
                } else if (floatval($row['M_Rate']) > 0) {
                    $num = 1;
                }
                switch ($graded) {
                    case 1:
                        $g_res = $row['Gwin'];
                        break;
                    case 0.5:
                        $g_res = $row['Gwin'] * 0.5;
                        break;
                    case -0.5:
                        $g_res = -$row['BetScore'] * 0.5 * $num;
                        break;
                    case -1:
                        $g_res = -$row['BetScore'] * $num;
                        break;
                    case 0:
                        $g_res = 0;
                        break;
                }

                /*$vgold=abs($graded)*$row['BetScore'];
                $betscore=$row['BetScore'];
                $turn=abs($graded)*$row['BetScore']*$row['TurnRate']/100;*/

                $betscore = $row['BetScore'];  //投注金额
                $vgold = $row['VGOLD']; //有效金额
                if (empty($vgold) or $vgold <> 0) {
                    $vgold = abs($graded) * $row['BetScore'];
                } else {
                    $vgold = 0;
                }
                $turn = abs($graded) * $vgold * intval($row['TurnRate']) / 100;  //返水

                $d_point = intval($row['D_Point']) / 100;
                $c_point = intval($row['C_Point']) / 100;
                $b_point = intval($row['B_Point']) / 100;
                $a_point = intval($row['A_Point']) / 100;

                $members = $g_res + $turn; //和会员结帐的金额

                $agents = $g_res * (1 - $d_point) + (1 - $d_point) * intval($row['D_Rate']) / 100 * intval($row['BetScore']) * abs($graded); //上缴总代理结帐的金额
                $world = $g_res * (1 - $c_point - $d_point) + (1 - $c_point - $d_point) * intval($row['C_Rate']) / 100 * $row['BetScore'] * abs($graded); //上缴股东结帐
                if (1 - $b_point - $c_point - $d_point != 0) {
                    $corprator = $g_res * (1 - $b_point - $c_point - $d_point) + (1 - $b_point - $c_point - $d_point) * intval($row['B_Rate']) / 100 * $row['BetScore'] * abs($graded); //上缴公司结帐
                } else {
                    $corprator = $g_res * ($b_point + $a_point) + ($b_point + $a_point) * intval($row['B_Rate']) / 100 * $row['BetScore'] * abs($graded); //和公司结帐
                }
                $super = $g_res * $a_point + $a_point * intval($row['A_Rate']) / 100 * $row['BetScore'] * abs($graded); //和公司结帐
                $agent = $g_res * 1 + 1 * intval($row['D_Rate']) / 100 * $row['BetScore'] * abs($graded); //公司退水帐目


                $previousAmount = Utils::GetField($user, 'Money');
                $user_id = Utils::GetField($user, 'id');
                $datetime = date("Y-m-d H:i:s", time() + 12 * 3600);
                $q1 = 0;
                if (in_array($mtype, $bc_arr)) {
                    $isQC = 0;
                } else {
                    $isQC = 1;
                }  //是否全场赛事注单
                if ($mb_in_score_v < 0 and $mb_in_score < 0) {
                    $BiFen = "半场:" . $Score_arr[abs($mb_in_score_v)] . " 全场:" . $Score_arr[abs($mb_in_score)];
                } elseif ($mb_in_score < 0) {
                    $BiFen = "半场:$mb_in_score_v-$tg_in_score_v 全场:" . $Score_arr[abs($mb_in_score)];
                } else {
                    $BiFen = "半场:$mb_in_score_v-$tg_in_score_v 全场:$mb_in_score-$tg_in_score";
                }
                if (($mb_in_score < 0 and $isQC == 1) or $mb_in_score_v < 0) {  //取消注单  全场比分为“取消”只取消全场  半场比分取消：全部取消
                    if ($row['Checked'] == 0) {
                        if ($row['Pay_Type'] == 1) {
                            $cash = $row['BetScore'];
                            Utils::ProcessUpdate($user);  //防止并发
                            $q1 = User::where('UserName', $user)->increment('Money', $cash);
                        }
                    }

                    if ($q1 == 1) {
                        $currentAmount = Utils::GetField($user, 'Money');
                        $Order_Code = $row['OrderID'];
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = "$Order_Code";
                        $new_log->about =  "loginname" . "系统取消赛事($BiFen)<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        $new_log->update_time = $datetime;
                        $new_log->type = $row['Middle'];
                        $new_log->order_value = $cash;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        $new_log->save();

                        $for_update = Report::where('ID', $id)->whereIn('active', [1, 11])->where('LineType', '!=', 8)
                            ->update([
                                'VGOLD' => 0,
                                'M_Result' => 0,
                                'D_Result' => 0,
                                'C_Result' => 0,
                                'B_Result' => 0,
                                'A_Result' => 0,
                                'T_Result' => 0,
                                'Cancel' => 1,
                                'Checked' => 1,
                                'Confirmed' => $mb_in_score
                            ]);
                    }
                } else {  //结算注单
                    $cash = 0;
                    if ($row['Checked'] == 0) {
                        if ($row['Pay_Type'] == 1) {
                            $cash = $row['BetScore'] + $members;
                            Utils::ProcessUpdate($user);  //防止并发
                            $q1 = User::where('UserName', $user)->increment('Money', $cash);
                        }
                    }
                    if ($q1 == 1 or $cash == 0) {
                        $currentAmount = Utils::GetField($user, 'Money');
                        $Order_Code = $row['OrderID'];
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = "$Order_Code";
                        $new_log->update_time = $datetime;
                        $new_log->type = $row['Middle'];
                        $new_log->order_value = $cash;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        if ($cash < $row['BetScore']) {
                            $new_log->about =  "系统取消赛事($BiFen)<br>输<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        } else {
                            $new_log->about =  "系统取消赛事($BiFen)<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        }
                        $new_log->save();

                        $for_update = Report::where('ID', $id)
                            ->update([
                                'VGOLD' => $vgold,
                                'M_Result' => $members,
                                'D_Result' => $agents,
                                'C_Result' => $world,
                                'B_Result' => $corprator,
                                'A_Result' => $super,
                                'T_Result' => $agent,
                                'Checked' => 1
                            ]);
                    }
                }
            }

            Sport::where('Type', 'FT')->where('MID', $gid)->update(['Score' => 1]);
        }
    }

    public function autoBKCheckScore()
    {
        $web_system_data = WebSystemData::all();
        $settime = $web_system_data[0]['udp_ft_score'];
        $time = $web_system_data[0]['udp_ft_results'];
        $date = date('Y-m-d', time() - $time * 60 * 60);
        $mDate = date('Y-m-d', time() - $time * 60 * 60);
        $bc_arr = array('VRC', 'VRH', 'VMN', 'VMC', 'VMH', 'VOUH', 'VOUC', 'VRMH', 'VRMC', 'VRMN', 'VROUH', 'VROUC', 'VRRH', 'VRRC');
        $Score_arr = array();
        $Score_arr[1] = '取消';
        $Score_arr[2] = '赛事腰斩';
        $Score_arr[3] = '赛事改期';
        $Score_arr[4] = '赛事延期';
        $Score_arr[5] = '赛事延赛';
        $Score_arr[6] = '赛事取消';
        $Score_arr[7] = '赛事无PK加时';
        $Score_arr[8] = '球员弃权';
        $Score_arr[9] = '队名错误';
        $Score_arr[10] = '主客场错误';
        $Score_arr[11] = '先发投手更换';
        $Score_arr[12] = '选手更换';
        $Score_arr[13] = '联赛名称错误';
        $Score_arr[19] = '提前开赛';

        $match_sports = Sport::where("Type", "BK")
            ->where("M_Date", $mDate)
            ->where("MB_Inball", "!=", "")
            ->where("Score", 0)
            ->orderBy('M_Start', 'asc')
            ->orderBy('MID', 'asc')
            ->get(['MID', 'MB_MID', 'TG_MID', 'MB_Team', 'TG_Team', 'MB_Inball', 'TG_Inball', 'MB_Inball_HR', 'TG_Inball_HR']);

        foreach ($match_sports as $match_sport) {

            $gid = $match_sport['MID'];
            $mb_in_score = $match_sport['MB_Inball'];
            $tg_in_score = $match_sport['TG_Inball'];
            $mb_in_score_v = $match_sport['MB_Inball_HR'];
            $tg_in_score_v = $match_sport['TG_Inball_HR'];

            Utils::ProcessUpdate($gid, 3);  //防止并发处理

            $reports = Report::select('ID', 'MID', 'OrderID', 'Active', 'M_Name', 'LineType', 'OpenType', 'ShowType', 'Mtype', 'Gwin', 'VGOLD', 'TurnRate', 'BetType', 'M_Place', 'M_Rate', 'Middle', 'BetScore', 'A_Rate', 'B_Rate', 'C_Rate', 'D_Rate', 'A_Point', 'B_Point', 'C_Point', 'D_Point', 'Pay_Type', 'Checked')
                ->whereRaw('FIND_IN_SET(?, MID) > 0', [$gid])
                ->whereIn('Active', [2, 22])
                ->where('LineType', '!=', 8)
                ->where('Cancel', '!=', 1)
                ->where('Checked', 0)
                ->orderBy('LineType', 'asc')
                ->get();

            foreach ($reports as $row_index => $row) {
                $mtype = $row['Mtype'];
                $id = $row['ID'];
                $user = $row['M_Name'];
                switch ($row['LineType']) {
                    case 2:
                        $graded = Utils::odds_letb($mb_in_score, $tg_in_score, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 3:
                        $graded = Utils::odds_dime($mb_in_score, $tg_in_score, $row['M_Place'], $row['Mtype']);
                        break;
                    case 5:
                        $graded = Utils::odds_eo($mb_in_score, $tg_in_score, $row['Mtype']);
                        break;
                    case 9:
                        $score = explode('<FONT color=red><b>', $row['Middle']);
                        $msg = explode("</b></FONT><br>", $score[1]);
                        $bcd = explode(":", $msg[0]);
                        $m_in = $bcd[0];
                        $t_in = $bcd[1];
                        if ($row['ShowType'] == 'H') {
                            $mbinscore1 = $mb_in_score - $m_in;
                            $tginscore1 = $tg_in_score - $t_in;
                        } else {
                            $mbinscore1 = $mb_in_score - $t_in;
                            $tginscore1 = $tg_in_score - $m_in;
                        }
                        $graded = Utils::odds_letb_rb($mbinscore1, $tginscore1, $row['ShowType'], $row['M_Place'], $row['Mtype']);
                        break;
                    case 10:
                        $graded = Utils::odds_dime_rb($mb_in_score, $tg_in_score, $row['M_Place'], $row['Mtype']);
                        break;
                }
                //echo $graded."-----------<br>";
                $num = 0;
                if (floatval($row['M_Rate']) < 0) {
                    $num = str_replace("-", "", $row['M_Rate']);
                } else if (floatval($row['M_Rate']) > 0) {
                    $num = 1;
                }
                switch ($graded) {
                    case 1:
                        $g_res = $row['Gwin'];
                        break;
                    case 0.5:
                        $g_res = $row['Gwin'] * 0.5;
                        break;
                    case -0.5:
                        $g_res = -$row['BetScore'] * 0.5 * $num;
                        break;
                    case -1:
                        $g_res = -$row['BetScore'] * $num;
                        break;
                    case 0:
                        $g_res = 0;
                        break;
                }

                /*$vgold=abs($graded)*$row['BetScore'];
                $betscore=$row['BetScore'];
                $turn=abs($graded)*$row['BetScore']*$row['TurnRate']/100;*/

                $betscore = $row['BetScore'];  //投注金额
                $vgold = $row['VGOLD']; //有效金额
                if (empty($vgold) or $vgold <> 0) {
                    $vgold = abs($graded) * $row['BetScore'];
                } else {
                    $vgold = 0;
                }
                $turn = abs($graded) * $vgold * intval($row['TurnRate']) / 100;  //返水

                $d_point = intval($row['D_Point']) / 100;
                $c_point = intval($row['C_Point']) / 100;
                $b_point = intval($row['B_Point']) / 100;
                $a_point = intval($row['A_Point']) / 100;

                $members = $g_res + $turn; //和会员结帐的金额

                $agents = $g_res * (1 - $d_point) + (1 - $d_point) * intval($row['D_Rate']) / 100 * intval($row['BetScore']) * abs($graded); //上缴总代理结帐的金额
                $world = $g_res * (1 - $c_point - $d_point) + (1 - $c_point - $d_point) * intval($row['C_Rate']) / 100 * $row['BetScore'] * abs($graded); //上缴股东结帐
                if (1 - $b_point - $c_point - $d_point != 0) {
                    $corprator = $g_res * (1 - $b_point - $c_point - $d_point) + (1 - $b_point - $c_point - $d_point) * intval($row['B_Rate']) / 100 * $row['BetScore'] * abs($graded); //上缴公司结帐
                } else {
                    $corprator = $g_res * ($b_point + $a_point) + ($b_point + $a_point) * intval($row['B_Rate']) / 100 * $row['BetScore'] * abs($graded); //和公司结帐
                }
                $super = $g_res * $a_point + $a_point * intval($row['A_Rate']) / 100 * $row['BetScore'] * abs($graded); //和公司结帐
                $agent = $g_res * 1 + 1 * intval($row['D_Rate']) / 100 * $row['BetScore'] * abs($graded); //公司退水帐目


                $previousAmount = Utils::GetField($user, 'Money');
                $user_id = Utils::GetField($user, 'id');
                $datetime = date("Y-m-d H:i:s", time() + 12 * 3600);
                $q1 = 0;
                if (in_array($mtype, $bc_arr)) {
                    $isQC = 0;
                } else {
                    $isQC = 1;
                }  //是否全场赛事注单
                if ($mb_in_score_v < 0 and $mb_in_score < 0) {
                    $BiFen = "半场:" . $Score_arr[abs($mb_in_score_v)] . " 全场:" . $Score_arr[abs($mb_in_score)];
                } elseif ($mb_in_score < 0) {
                    $BiFen = "半场:$mb_in_score_v-$tg_in_score_v 全场:" . $Score_arr[abs($mb_in_score)];
                } else {
                    $BiFen = "半场:$mb_in_score_v-$tg_in_score_v 全场:$mb_in_score-$tg_in_score";
                }
                //取消注单  全场比分为“取消”只取消全场  半场比分取消：全部取消
                if (($mb_in_score < 0 and $isQC == 1) or $mb_in_score_v < 0) {
                    if ($row['Checked'] == 0) {
                        if ($row['Pay_Type'] == 1) {
                            $cash = $row['BetScore'];
                            Utils::ProcessUpdate($user);  //防止并发
                            $q1 = User::where('UserName', $user)->increment('Money', $cash);
                        }
                    }

                    if ($q1 == 1) {
                        $currentAmount = Utils::GetField($user, 'Money');
                        $Order_Code = $row['OrderID'];
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = "$Order_Code";
                        $new_log->about =  "loginname" . "系统取消赛事($BiFen)<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        $new_log->update_time = $datetime;
                        $new_log->type = $row['Middle'];
                        $new_log->order_value = $cash;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        $new_log->save();

                        $for_update = Report::where('ID', $id)->whereIn('active', [1, 11])->where('LineType', '!=', 8)
                            ->update([
                                'VGOLD' => 0,
                                'M_Result' => 0,
                                'D_Result' => 0,
                                'C_Result' => 0,
                                'B_Result' => 0,
                                'A_Result' => 0,
                                'T_Result' => 0,
                                'Cancel' => 1,
                                'Checked' => 1,
                                'Confirmed' => $mb_in_score
                            ]);
                    }
                } else {  //结算注单
                    $cash = 0;
                    if ($row['Checked'] == 0) {
                        if ($row['Pay_Type'] == 1) {
                            $cash = $row['BetScore'] + $members;
                            Utils::ProcessUpdate($user);  //防止并发
                            $q1 = User::where('UserName', $user)->increment('Money', $cash);
                        }
                    }
                    if ($q1 == 1 or $cash == 0) {
                        $currentAmount = Utils::GetField($user, 'Money');
                        $Order_Code = $row['OrderID'];
                        $new_log = new MoneyLog;
                        $new_log->user_id = $user_id;
                        $new_log->order_num = "$Order_Code";
                        $new_log->update_time = $datetime;
                        $new_log->type = $row['Middle'];
                        $new_log->order_value = $cash;
                        $new_log->assets = $previousAmount;
                        $new_log->balance = $currentAmount;
                        if ($cash < $row['BetScore']) {
                            $new_log->about =  "系统取消赛事($BiFen)<br>输<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        } else {
                            $new_log->about =  "系统取消赛事($BiFen)<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                        }
                        $new_log->save();

                        $for_update = Report::where('ID', $id)
                            ->update([
                                'VGOLD' => $vgold,
                                'M_Result' => $members,
                                'D_Result' => $agents,
                                'C_Result' => $world,
                                'B_Result' => $corprator,
                                'A_Result' => $super,
                                'T_Result' => $agent,
                                'Checked' => 1
                            ]);
                    }
                }
            }

            Sport::where('Type', 'BK')->where('MID', $gid)->update(['Score' => 1]);
        }
    }

    public function autoFTParlayCheckScore()
    {

        $web_system_data = WebSystemData::all();
        $settime = $web_system_data[0]['udp_ft_score'];
        $time = $web_system_data[0]['udp_ft_results'];
        $date = date('Y-m-d', time() - $time * 60 * 60);
        $mDate = date('Y-m-d', time() - $time * 60 * 60);

        $reports = Report::select('ID', 'MID', 'OrderID', 'Active', 'M_Name', 'LineType', 'OpenType', 'ShowType', 'Mtype', 'Gwin', 'VGOLD', 'TurnRate', 'BetType', 'M_Place', 'M_Rate', 'Middle', 'BetScore', 'A_Rate', 'B_Rate', 'C_Rate', 'D_Rate', 'A_Point', 'B_Point', 'C_Point', 'D_Point', 'Pay_Type', 'Checked')
            ->whereIn('Active', [1, 11])
            ->where('LineType', 8)
            ->where('Cancel', 0)
            ->where('Checked', 0)
            ->get();

        foreach ($reports as $report) {
            $notgraded = 0;
            $id = $report['ID'];
            $user = $report['M_Name'];
            $winrate = 1;
            $mid = explode(',', $report['MID']);
            $mtype = explode(',', $report['Mtype']);
            $rate = explode(',', $report['M_Rate']);
            $letb = explode(',', $report['M_Place']);
            $show = explode(',', $report['ShowType']);
            $count = sizeof($mid);

            for ($i = 0; $i < $count; $i++) {

                $match_sport = Sport::where("Type", "FT")
                    ->where("M_Date", "<=", $mDate)
                    ->where("MB_Inball", "!=", "")
                    ->where("MID", $mid[$i])
                    ->first(['MID', 'MB_MID', 'TG_MID', 'MB_Team', 'TG_Team', 'MB_Inball', 'TG_Inball', 'MB_Inball_HR', 'TG_Inball_HR']);

                $mb_in = $match_sport['MB_Inball'];
                $tg_in = $match_sport['TG_Inball'];
                $mb_in_v = $match_sport['MB_Inball_HR'];
                $tg_in_v = $match_sport['TG_Inball_HR'];

                if ($show[$i] == 'H') {
                    // echo "上半:".$mb_in_v."-".$tg_in_v."&nbsp;&nbsp;全场:".$mb_in."-".$tg_in."<BR>";
                } else {
                    // echo "上半:".$tg_in_v."-".$mb_in_v."&nbsp;&nbsp;全场:".$tg_in."-".$mb_in."<BR>";
                }

                if ($mb_in == '' || $tg_in == '') {
                    $graded = "99";
                    $notgraded = 1;
                    // echo '<font color=white>还有未完场赛事</font><br>';
                    // echo '<font color=white>'.$row['BetTime'].'-'.$row['M_Name'].'</font><br><br>';
                    break;
                } else if ($mb_in < 0) {

                    $graded = 88;
                } else {

                    if ($mtype[$i] == 'MH' or $mtype[$i] == 'MC' or $mtype[$i] == 'MN') {
                        $graded = win_chk($mb_in, $tg_in, $mtype[$i]);
                    } else if ($mtype[$i] == 'RMH' or $mtype[$i] == 'RMC' or $mtype[$i] == 'RMN') {
                        $graded = win_chk_rb($mb_in_v, $tg_in_v, $mtype[$i]);
                    } else if ($mtype[$i] == 'VMH' or $mtype[$i] == 'VMC' or $mtype[$i] == 'VMN') {
                        $graded = win_chk_v($mb_in_v, $tg_in_v, $mtype[$i]);
                    } else if ($mtype[$i] == 'OUH' or $mtype[$i] == 'OUC') {
                        $graded = odds_dime($mb_in, $tg_in, $letb[$i], $mtype[$i]);
                    } else if ($mtype[$i] == 'ROUH' or $mtype[$i] == 'ROUC') {
                        $graded = odds_dime_rb($mb_in, $tg_in, $letb[$i], $mtype[$i]);
                    } else if ($mtype[$i] == 'VOUH' or $mtype[$i] == 'VOUC') {
                        $graded = odds_dime_v($mb_in_v, $tg_in_v, $letb[$i], $mtype[$i]);
                    } else if ($mtype[$i] == 'RH' or $mtype[$i] == 'RC') {
                        $graded = odds_letb($mb_in, $tg_in, $show[$i], $letb[$i], $mtype[$i]);
                    } else if ($mtype[$i] == 'RRH' or $mtype[$i] == 'RRC') {
                        $graded = odds_letb_rb($mb_in, $tg_in, $show[$i], $letb[$i], $mtype[$i]);
                    } else if ($mtype[$i] == 'VRH' or $mtype[$i] == 'VRC') {
                        $graded = odds_letb_v($mb_in_v, $tg_in_v, $show[$i], $letb[$i], $mtype[$i]);
                    } else if ($mtype[$i] == 'ODD' or $mtype[$i] == 'EVEN') {
                        $graded = odds_eo($mb_in, $tg_in, $mtype[$i]);
                    }
                }
                // echo '&nbsp;'.$graded.'<br>';
                switch ($graded) {
                    case "1":
                        $winrate = $winrate * ($rate[$i]);
                        break;
                    case "-1":
                        $winrate = 0;
                        $notgraded = 0;
                        break;
                    case "0":
                        $winrate = $winrate;
                        break;
                    case "0.5":
                        $winrate = $winrate * (($rate[$i] - 1) * 0.5 + 1);
                        break;
                    case "-0.5":
                        $winrate = $winrate * 0.5;
                        break;
                    case "99":
                        $winrate = $winrate;
                        break;
                    case "88":
                        $winrate = $winrate;
                        break;
                }
            }

            if ($notgraded == 0) {

                $g_res = $report['BetScore'] * (abs($winrate) - 1);
                $vgold = $report['BetScore'];
                $turn = $report['BetScore'] * $report['TurnRate'] / 100;
                $d_point = $report['D_Point'] / 100;
                $c_point = $report['C_Point'] / 100;
                $b_point = $report['B_Point'] / 100;
                $a_point = $report['A_Point'] / 100;

                //和会员结帐的金额
                $members = $g_res + $turn;
                //上缴总代理结帐的金额
                $agents = $g_res * (1 - $d_point) + (1 - $d_point) * $report['D_Rate'] / 100 * $report['BetScore'];
                //上缴股东结帐
                $world = $g_res * (1 - $c_point - $d_point) + (1 - $c_point - $d_point) * $report['C_Rate'] / 100 * $report['BetScore'];
                //上缴公司结帐
                if (1 - $b_point - $c_point - $d_point != 0) {
                    $corprator = $g_res * (1 - $b_point - $c_point - $d_point) + (1 - $b_point - $c_point - $d_point) * $report['B_Rate'] / 100 * $report['BetScore'];
                } else {
                    //和公司结帐
                    $corprator = $g_res * ($b_point + $a_point) + ($b_point + $a_point) * $report['B_Rate'] / 100 * $report['BetScore'];
                }
                //和公司结帐
                $super = $g_res * $a_point + $a_point * $report['A_Rate'] / 100 * $report['BetScore'];
                //代理商退水总帐目
                $agent = $g_res + $report['D_Rate'] / 100 * $report['BetScore'];

                if ($report['Checked'] == 0) {
                    if ($report['Pay_Type'] == 1) {
                        $cash = $report['BetScore'] + $members;
                        $previousAmount = Utils::GetField($user, 'Money');
                        $user_id = Utils::GetField($user, 'ID');
                        $datetime = date("Y-m-d H:i:s", time() + 12 * 3600);
                        //ProcessUpdate($user);  //防止并发
                        $q1 = User::where('UserName', $user)->increment('Money', $cash);
                        if ($q1 == 1 or $cash == 0) {

                            $currentAmount = Utils::GetField($user, 'Money');
                            $Order_Code = $row['OrderID'];

                            $new_log = new MoneyLog;

                            $new_log->user_id = $user_id;
                            $new_log->order_num = "$Order_Code";
                            $new_log->update_time = $datetime;
                            $new_log->type = $row['Middle'];
                            $new_log->order_value = $cash;
                            $new_log->assets = $previousAmount;
                            $new_log->balance = $currentAmount;

                            if ($cash < $row['BetScore']) {
                                $new_log->about =  "系统取消赛事($BiFen)<br>输<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                            } else {
                                $new_log->about =  "系统取消赛事($BiFen)<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                            }

                            $new_log->save();
                        }
                    }
                }

                Report::where('ID', $id)
                    ->update([
                        'VGOLD' => $vgold,
                        'M_Result' => $members,
                        'D_Result' => $agents,
                        'C_Result' => $world,
                        'B_Result' => $corprator,
                        'A_Result' => $super,
                        'T_Result' => $agent,
                        'Checked' => 1
                    ]);

                // echo '<font color=white>'.$report['BetTime'].'--'.$report['M_Name'].'--</font><font color=red>('.$members.')</font><br><br>'; 

            } else {

                Report::where('ID', $id)
                    ->update([
                        'VGOLD' => '',
                        'M_Result' => '',
                        'D_Result' => '',
                        'C_Result' => '',
                        'B_Result' => '',
                        'A_Result' => '',
                        'T_Result' => '',
                        'Checked' => 1
                    ]);
            }
        }
    }

    public function autoBKParlayCheckScore()
    {

        $web_system_data = WebSystemData::all();
        $settime = $web_system_data[0]['udp_ft_score'];
        $time = $web_system_data[0]['udp_ft_results'];
        $date = date('Y-m-d', time() - $time * 60 * 60);
        $mDate = date('Y-m-d', time() - $time * 60 * 60);

        $reports = Report::select('ID', 'MID', 'OrderID', 'Active', 'M_Name', 'LineType', 'OpenType', 'ShowType', 'Mtype', 'Gwin', 'VGOLD', 'TurnRate', 'BetType', 'M_Place', 'M_Rate', 'Middle', 'BetScore', 'A_Rate', 'B_Rate', 'C_Rate', 'D_Rate', 'A_Point', 'B_Point', 'C_Point', 'D_Point', 'Pay_Type', 'Checked')
            ->whereIn('Active', [1, 11])
            ->where('LineType', 8)
            ->where('Cancel', 0)
            ->where('Checked', 0)
            ->get();

        foreach ($reports as $report) {
            $notgraded = 0;
            $id = $report['ID'];
            $user = $report['M_Name'];
            $winrate = 1;
            $mid = explode(',', $report['MID']);
            $mtype = explode(',', $report['Mtype']);
            $rate = explode(',', $report['M_Rate']);
            $letb = explode(',', $report['M_Place']);
            $show = explode(',', $report['ShowType']);
            $count = sizeof($mid);

            for ($i = 0; $i < $count; $i++) {

                $match_sport = Sport::where("Type", "BK")
                    ->where("M_Date", "<=", $mDate)
                    ->where("MB_Inball", "!=", "")
                    ->where("MID", $mid[$i])
                    ->first(['MID', 'MB_MID', 'TG_MID', 'MB_Team', 'TG_Team', 'MB_Inball', 'TG_Inball', 'MB_Inball_HR', 'TG_Inball_HR']);

                $mb_in = $match_sport['MB_Inball'];
                $tg_in = $match_sport['TG_Inball'];
                $mb_in_v = $match_sport['MB_Inball_HR'];
                $tg_in_v = $match_sport['TG_Inball_HR'];

                if ($show[$i] == 'H') {
                    // echo "上半:".$mb_in_v."-".$tg_in_v."&nbsp;&nbsp;全场:".$mb_in."-".$tg_in."<BR>";
                } else {
                    // echo "上半:".$tg_in_v."-".$mb_in_v."&nbsp;&nbsp;全场:".$tg_in."-".$mb_in."<BR>";
                }

                if ($mb_in == '' || $tg_in == '') {
                    $graded = "99";
                    $notgraded = 1;
                    // echo '<font color=white>还有未完场赛事</font><br>';
                    // echo '<font color=white>'.$row['BetTime'].'-'.$row['M_Name'].'</font><br><br>';
                    break;
                } else if ($mb_in < 0) {
                    $graded = 88;
                } else {
                    $abc = strtolower(substr($letb[$i], 0, 1));
                    $abcd = strtolower(substr($letb[$i], 0, 2));
                    if ($abcd == 'od' or $abc == 'ev'){
                        $graded = Utils::odds_eo($mb_in,$tg_in,$mtype[$i]);
                    }else if($abc == 'o' or $abc == 'u'){
                        $graded = Utils::odds_dime($mb_in, $tg_in,$letb[$i], $mtype[$i]);
                    }else{
                        $graded = Utils::odds_letb($mb_in, $tg_in,$show[$i], $letb[$i], $mtype[$i]);
                    }
                }
                // echo '&nbsp;'.$graded.'<br>';
                switch ($graded) {
                    case "1":
                        $winrate = $winrate * ($rate[$i]);
                        break;
                    case "-1":
                        $winrate = 0;
                        $notgraded = 0;
                        break;
                    case "0":
                        $winrate = $winrate;
                        break;
                    case "0.5":
                        $winrate = $winrate * (($rate[$i] - 1) * 0.5 + 1);
                        break;
                    case "-0.5":
                        $winrate = $winrate * 0.5;
                        break;
                    case "99":
                        $winrate = $winrate;
                        break;
                    case "88":
                        $winrate = $winrate;
                        break;
                }
            }

            if ($notgraded == 0) {

                $g_res = $report['BetScore'] * (abs($winrate) - 1);
                $vgold = $report['BetScore'];
                $turn = $report['BetScore'] * $report['TurnRate'] / 100;
                $d_point = $report['D_Point'] / 100;
                $c_point = $report['C_Point'] / 100;
                $b_point = $report['B_Point'] / 100;
                $a_point = $report['A_Point'] / 100;

                //和会员结帐的金额
                $members = $g_res + $turn;
                //上缴总代理结帐的金额
                $agents = $g_res * (1 - $d_point) + (1 - $d_point) * $report['D_Rate'] / 100 * $report['BetScore'];
                //上缴股东结帐
                $world = $g_res * (1 - $c_point - $d_point) + (1 - $c_point - $d_point) * $report['C_Rate'] / 100 * $report['BetScore'];
                //上缴公司结帐
                if (1 - $b_point - $c_point - $d_point != 0) {
                    $corprator = $g_res * (1 - $b_point - $c_point - $d_point) + (1 - $b_point - $c_point - $d_point) * $report['B_Rate'] / 100 * $report['BetScore'];
                } else {
                    //和公司结帐
                    $corprator = $g_res * ($b_point + $a_point) + ($b_point + $a_point) * $report['B_Rate'] / 100 * $report['BetScore'];
                }
                //和公司结帐
                $super = $g_res * $a_point + $a_point * $report['A_Rate'] / 100 * $report['BetScore'];
                //代理商退水总帐目
                $agent = $g_res + $report['D_Rate'] / 100 * $report['BetScore'];

                if ($report['Checked'] == 0) {
                    if ($report['Pay_Type'] == 1) {
                        $cash = $report['BetScore'] + $members;
                        $previousAmount = Utils::GetField($user, 'Money');
                        $user_id = Utils::GetField($user, 'ID');
                        $datetime = date("Y-m-d H:i:s", time() + 12 * 3600);
                        //ProcessUpdate($user);  //防止并发
                        $q1 = User::where('UserName', $user)->increment('Money', $cash);
                        if ($q1 == 1 or $cash == 0) {

                            $currentAmount = Utils::GetField($user, 'Money');
                            $Order_Code = $row['OrderID'];

                            $new_log = new MoneyLog;

                            $new_log->user_id = $user_id;
                            $new_log->order_num = "$Order_Code";
                            $new_log->update_time = $datetime;
                            $new_log->type = $row['Middle'];
                            $new_log->order_value = $cash;
                            $new_log->assets = $previousAmount;
                            $new_log->balance = $currentAmount;

                            if ($cash < $row['BetScore']) {
                                $new_log->about =  "系统取消赛事($BiFen)<br>输<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                            } else {
                                $new_log->about =  "系统取消赛事($BiFen)<br>MID:" . $row['MID'] . "<br>RID:" . $row['ID'];
                            }

                            $new_log->save();
                        }
                    }
                }

                Report::where('ID', $id)
                    ->update([
                        'VGOLD' => $vgold,
                        'M_Result' => $members,
                        'D_Result' => $agents,
                        'C_Result' => $world,
                        'B_Result' => $corprator,
                        'A_Result' => $super,
                        'T_Result' => $agent,
                        'Checked' => 1
                    ]);

                // echo '<font color=white>'.$report['BetTime'].'--'.$report['M_Name'].'--</font><font color=red>('.$members.')</font><br><br>'; 

            } else {

                Report::where('ID', $id)
                    ->update([
                        'VGOLD' => '',
                        'M_Result' => '',
                        'D_Result' => '',
                        'C_Result' => '',
                        'B_Result' => '',
                        'A_Result' => '',
                        'T_Result' => '',
                        'Checked' => 1
                    ]);
            }
        }
    }    

    public function getBetSlipList(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        $rules = [
            'gid' => 'required|string',
            'bet_date' => 'required|string',
            'gtype' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorResponse = validation_error_response($validator->errors()->toArray());
            return response()->json($errorResponse, $response['status']);
        }

        try {

            $uid = $request['uid'];
            $active = $request['active'];
            $id = $request['id'];
            $gid = $request['gid'];
            $gtype = $request['gtype'];
            $key = $request['key'];
            $confirmed = $request['confirmed'];
            $bet_date = $request['bet_date'];

            $Score = Utils::Scores;

            $table = [];

            switch ($gtype) {
                case 'FT':
                    $table = [1, 11];
                    break;
                case 'BK':
                    $table = [2, 22];
                    break;
                case 'BS':
                    $table = [3, 33];
                    break;
                case 'TN':
                    $table = [4, 44];
                    break;
                case 'VB':
                    $table = [5, 55];
                    break;
                case 'OP':
                    $table = [6, 66];
                    break;
                case 'FU':
                    $table = [7, 77];
                    break;
                case 'FS':
                    $table = [8];
                    break;
            }

            // 取消注单 - Cancel bet
            if ($key == 'cancel') {
                $rresult = Report::select('M_Name', 'Pay_Type', 'BetScore', 'M_Result')->where('MID', $gid)->where('ID', $id)->where('Pay_Type', 1);
                foreach ($rresult as $rrow_index => $rrow) {
                    $username = $rrow['M_Name'];
                    $betscore = $rrow['BetScore'];
                    $m_result = $rrow['M_Result'];
                    if ($rrow['Pay_Type'] == 1) { //结算之后的现金返回 - Cash back after settlement
                        if ($m_result == '') {
                            User::where('UserName', $username)->where('Pay_Type', 1)->increment('Money', $betscore) or die("操作失败11!");
                            Utils::MoneyToSsc($username);
                        } else {
                            User::where('UserName', $username)->where('Pay_Type', 1)->decrement('Money', $m_result) or die("操作失败22!");
                            Utils::MoneyToSsc($username);
                        }
                    }
                }
                Report::where('ID', $id)->update([
                    'VGOLD' => 0,
                    'M_Result' => 0,
                    'D_Result' => 0,
                    'C_Result' => 0,
                    'B_Result' => 0,
                    'A_Result' => 0,
                    'T_Result' => 0,
                    'Cancel' => 1,
                    'Checked' => 1,
                    'Danger' => 0,
                    'Confirmed' => $confirmed
                ]);
            }

            //恢复注单 - Resume bet
            if ($key == 'resume') {
                $rresult = Report::select('M_Name', 'Pay_Type', 'BetScore', 'M_Result', 'Checked')->where('MID', $gid)->where('ID', $id)->where('Pay_Type', 1)->get();
                foreach ($rresult as $rrow_index => $rrow) {
                    $username = $rrow['M_Name'];
                    $betscore = $rrow['BetScore'];
                    $m_result = $rrow['M_Result'];
                    if ($rrow['Pay_Type'] == 1) { //结算之后的现金返回
                        if ($rrow['Checked'] == 1) {
                            $cash = $betscore + $m_result;
                            User::where('UserName', $username)->where('Pay_Type', 1)->decrement('Money', $cash) or die("操作失败1!");
                            Utils::MoneyToSsc($username);
                        }
                    }
                }
                Report::where('id', $id)->update([
                    'VGOLD' => '',
                    'M_Result' => '',
                    'D_Result' => '',
                    'C_Result' => '',
                    'B_Result' => '',
                    'A_Result' => '',
                    'T_Result' => '',
                    'Cancel' => 0,
                    'Checked' => 0,
                    'Danger' => 0,
                    'Confirmed' => 0
                ]);
                // echo "<script languag='JavaScript'>self.location='showdata.php?uid=$uid&id=$id&gid=$gid&gtype=$gtype&langx=$langx'</script>";
            }

            $sport = Sport::where('Type', $gtype)->where('MID', $gid)->first();

            $report = Report::select('ID', 'MID', 'Active', 'LineType', 'Mtype', 'Pay_Type', 'M_Date', 'BetTime', 'BetScore', 'CurType', 'Middle', 'BetType', 'M_Place', 'M_Rate', 'M_Name', 'Gwin', 'Glost', 'VGOLD', 'M_Result', 'A_Result', 'B_Result', 'C_Result', 'D_Result', 'T_Result', 'TurnRate', 'OpenType', 'OddsType', 'ShowType', 'Cancel', 'Confirmed', 'Danger')
                ->whereRaw('FIND_IN_SET(?, MID) > 0', [$gid]);

            // return $report;

            if (count($table) > 0)
                $report = $report->whereIn('Active', $table);

            $report = $report->orderBy('BetTime', 'asc')
                ->orderBy('LineType', 'asc')
                ->orderBy('Mtype', 'asc')
                ->get();

            $data = [];

            foreach ($report as $key => $row) {
                switch ($row['Active']) {
                    case 1:
                        $Title = Utils::Mnu_Soccer;
                        break;
                    case 11:
                        $Title = Utils::Mnu_Soccer;
                        break;
                    case 2:
                        $Title = Utils::Mnu_BasketBall;
                        break;
                    case 22:
                        $Title = Utils::Mnu_BasketBall;
                        break;
                    case 3:
                        $Title = Utils::Mnu_Base;
                        break;
                    case 33:
                        $Title = Utils::Mnu_Base;
                        break;
                    case 4:
                        $Title = Utils::Mnu_Tennis;
                        break;
                    case 44:
                        $Title = Utils::Mnu_Tennis;
                        break;
                    case 5:
                        $Title = Utils::Mnu_Voll;
                        break;
                    case 55:
                        $Title = Utils::Mnu_Voll;
                        break;
                    case 6:
                        $Title = Utils::Mnu_Other;
                        break;
                    case 66:
                        $Title = Utils::Mnu_Other;
                        break;
                    case 7:
                        $Title = Utils::Mnu_Stock;
                        break;
                    case 77:
                        $Title = Utils::Mnu_Stock;
                        break;
                    case 8:
                        $Title = Utils::Mnu_Guan;
                        break;
                }
                switch ($row['OddsType']) {
                    case 'H':
                        $Odds = '<BR><font color =green>' . Utils::Rep_HK . '</font>';
                        break;
                    case 'M':
                        $Odds = '<BR><font color =green>' . Utils::Rep_Malay . '</font>';
                        break;
                    case 'I':
                        $Odds = '<BR><font color =green>' . Utils::Rep_Indo . '</font>';
                        break;
                    case 'E':
                        $Odds = '<BR><font color =green>' . Utils::Rep_Euro . '</font>';
                        break;
                    case '':
                        $Odds = '';
                        break;
                }

                $time = strtotime($row['BetTime']);

                $times = date("Y-m-d", $time) . '<br>' . date("H:i:s", $time);

                if ($row['Danger'] == 1 or $row['Cancel'] == 1) {

                    $bettimes = '<font color="#FFFFFF"><span style="background-color: #FF0000">' . $times . '</span></font>';

                    $betscore = '<S><font color=#cc0000>' . number_format($row['BetScore']) . '</font></S>';
                } else {

                    $bettimes = $times;

                    $betscore = number_format(floatval($row['BetScore']));
                }

                $temp = [
                    'bettimes' => $bettimes,
                    'Title' => $Title,
                    'Odds' => $Odds,
                    'M_Name' => $row['M_Name'],
                    'OpenType' => $row['OpenType'],
                    'TurnRate' => $row['TurnRate'],
                    'BetType' => $row['BetType'],
                    'LineType' => $row['LineType'],
                    'ID' => $row['ID'],
                    'voucher' => Utils::show_voucher($row['LineType'], $row['ID']),
                    'Middle' => $row['Middle'],
                    'betscore' => $betscore,
                    'Cancel' => $row['Cancel'],
                    'Confirmed' => $row['Confirmed'],
                    'M_Result' => number_format(floatval($row['M_Result']), 1),
                    'operate' => '<font color=blue><b>正常</b></font>',
                    'function' => []
                ];

                for ($i = 0; $i <= 21; $i++) {
                    array_push($temp['function'], ['value' => -$i, 'label' => $Score[20 + $i]]);
                }

                if ($row['Cancel']) {
                    switch ($row['Confirmed']) {
                        case 0:
                            $zt = $Score20;
                            break;
                        case -1:
                            $zt = $Score21;
                            break;
                        case -2:
                            $zt = $Score22;
                            break;
                        case -3:
                            $zt = $Score23;
                            break;
                        case -4:
                            $zt = $Score24;
                            break;
                        case -5:
                            $zt = $Score25;
                            break;
                        case -6:
                            $zt = $Score26;
                            break;
                        case -7:
                            $zt = $Score27;
                            break;
                        case -8:
                            $zt = $Score28;
                            break;
                        case -9:
                            $zt = $Score29;
                            break;
                        case -10:
                            $zt = $Score30;
                            break;
                        case -11:
                            $zt = $Score31;
                            break;
                        case -12:
                            $zt = $Score32;
                            break;
                        case -13:
                            $zt = $Score33;
                            break;
                        case -14:
                            $zt = $Score34;
                            break;
                        case -15:
                            $zt = $Score35;
                            break;
                        case -16:
                            $zt = $Score36;
                            break;
                        case -17:
                            $zt = $Score37;
                            break;
                        case -18:
                            $zt = $Score38;
                            break;
                        case -19:
                            $zt = $Score39;
                            break;
                        case -20:
                            $zt = $Score40;
                            break;
                        case -21:
                            $zt = $Score41;
                            break;
                        default:
                            break;
                    }
                    $temp['M_Result'] = $zt;
                    $temp['operate'] = '<a href="showdata.php?uid=' . $uid . '&id=' . $row['ID'] . '&gid=' . $row['MID'] . '&pay_type=' . $row['Pay_Type'] . '&key=resume&result=' . $row['M_Result'] . '&user=' . $row['M_Name'] . '&confirmed=0&gtype=' . $gtype . '"><font color=red><b>恢复</b></font></a>';
                }

                array_push($data, $temp);
            }

            $response['data'] = ['sport' => $sport, 'report' => $data];
            $response['message'] = 'BetSlip Data fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    // GET Betting Records API
    public function get_betting_records(Request $request)
    {

        $m_name = $request->post('m_name');

        $report_count = Report::where("M_Name", $m_name)->count();
        $items = Report::with('sport')->whereRaw("M_Name='$m_name'")->get();
        return response()->json([
            "success" => true,
            "data" => $items,
            "count" => $count
        ], 200);
    }

    // ADD Temp of BET
    public function addTemp(Request $request)
    {
        $temp = new WebReportTemp;
        $temp->type = $request->type;
        $temp->title = $request->title;
        $temp->league = $request->league;
        $temp->m_team = $request->m_team;
        $temp->t_team = $request->t_team;
        $temp->select_team = $request->select_team;
        $temp->text = $request->text;
        $temp->rate = $request->rate;
        $temp->gold = $request->gold;
        $temp->m_win = $request->m_win;
        $temp->uid = $request->uid;
        $temp->gid = $request->gid;
        $temp->line_type = $request->line_type;
        $temp->g_type = $request->g_type;
        $temp->active = $request->active;
        $temp->save();
        $responseMessage = "添加成功";
        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);
    }

    // DELETE Temps
    public function deleteTemps()
    {
        WebReportTemp::query()->delete();
        $responseMessage = "删除成功";
        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);
    }

    // GET Temps
    public function getTemps()
    {
        $temps = WebReportTemp::all();
        return response()->json([
            'success' => true,
            'data' => $temps
        ], 200);
    }

    // Edit Temp
    public function editTemp(Request $request)
    {
        $gold = $request->gold;

        $validator = Validator::make($request->all(), [
            'gold' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ]);
        WebReportTemp::where('id', $request->id)->update(['gold' => $gold, 'm_win' => $request->m_win]);
        $responseMessage = "更新成功";
        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);
    }
}
