<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sport;

class MatchSportBKController extends Controller
{
    public function saveBKDefaultToday(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();
            $new_data = [
                "Type" => $request_data["Type"],
                "LID" => $request_data["LID"] ?? "",                
                "ECID" => $request_data["ECID"] ?? "",
                "M_Date" => $request_data["M_Date"] ?? date("Y-m-d"),
                "M_Time" => $request_data["M_Time"] ?? date("H:i:s"),
                "M_Start" => $request_data["M_Start"] ?? date("Y-m-d")." ".date("H:i:s"),
                "MB_Team" => $request_data["MB_Team"],
                "TG_Team" => $request_data["TG_Team"],
                "MB_Team_tw" => $request_data["MB_Team_tw"],
                "TG_Team_tw" => $request_data["TG_Team_tw"],
                "MB_Team_en" => $request_data["MB_Team_en"],
                "TG_Team_en" => $request_data["TG_Team_en"],
                "M_League" => $request_data["M_League"],
                "M_League_tw" => $request_data["M_League_tw"],
                "M_League_en" => $request_data["M_League_en"],
                "MB_MID" => $request_data["MB_MID"],
                "TG_MID" => $request_data["TG_MID"],
                "M_LetB" => $request_data["M_LetB"] ?? "",
                "MB_LetB_Rate" => $request_data["MB_LetB_Rate"] ?? 0,
                "TG_LetB_Rate" => $request_data["TG_LetB_Rate"] ?? 0,
                "MB_Dime" => $request_data["MB_Dime"] ?? "",
                "TG_Dime" => $request_data["TG_Dime"] ?? "",
                "MB_Dime_Rate" => $request_data["MB_Dime_Rate"] ?? 0,
                "TG_Dime_Rate" => $request_data["TG_Dime_Rate"] ?? 0,
                "S_Single_Rate" => $request_data["S_Single_Rate"] ?? 0,
                "S_Double_Rate" => $request_data["S_Double_Rate"] ?? 0,
                "FLAG_CLASS" => $request_data['FLAG_CLASS'],
                "Eventid" => $request_data["Eventid"] ?? "",
                "ShowTypeR" => $request_data['ShowTypeR'],
                "MID" => $request_data["MID"],
                "S_Show" => $request_data["S_Show"],
            ];
            $sport = Sport::where("MID", $request_data['MID'])->first();
            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'BK Data updated successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } else {
                $sport = new Sport;
                $sport->create($new_data);
                $response['message'] = 'BK Today Data added successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            }

            $MB_LetB_Rate = $request_data["MB_LetB_Rate"];
            $TG_LetB_Rate = $request_data["TG_LetB_Rate"];

            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/matchsport_bk_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n$MB_LetB_Rate\r\n$TG_LetB_Rate\r\n");
            fclose($f);
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveBKDefaultInplay(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();
            $new_data = [
                "Type" => $request_data["Type"],
                "Retime" => $request_data["Retime"] ?? "",
                "LID" => $request_data["LID"] ?? "",
                "ECID" => $request_data["ECID"] ?? "",
                "M_Date" => $request_data["M_Date"] ?? date("Y-m-d"),
                "M_Time" => $request_data["M_Time"] ?? date("H:i:s"),
                "M_Start" => $request_data["M_Start"] ?? date("Y-m-d")." ".date("H:i:s"),
                "MB_Team" => $request_data["MB_Team"],
                "TG_Team" => $request_data["TG_Team"],
                "MB_Team_tw" => $request_data["MB_Team_tw"],
                "TG_Team_tw" => $request_data["TG_Team_tw"],
                "MB_Team_en" => $request_data["MB_Team_en"],
                "TG_Team_en" => $request_data["TG_Team_en"],
                "M_League" => $request_data["M_League"],
                "M_League_tw" => $request_data["M_League_tw"],
                "M_League_en" => $request_data["M_League_en"],
                "MB_MID" => $request_data["MB_MID"],
                "TG_MID" => $request_data["TG_MID"],
                "MB_Ball" => $request_data["MB_Ball"] ?? 0,
                "TG_Ball" => $request_data["TG_Ball"] ?? 0,
                "M_LetB_RB" => $request_data["M_LetB_RB"] ?? "",
                "MB_LetB_Rate_RB" => $request_data["MB_LetB_Rate_RB"] ?? 0,
                "TG_LetB_Rate_RB" => $request_data["TG_LetB_Rate_RB"] ?? 0,
                "MB_Dime_RB" => $request_data["MB_Dime_RB"] ?? "",
                "TG_Dime_RB" => $request_data["TG_Dime_RB"] ?? "",
                "MB_Dime_Rate_RB" => $request_data["MB_Dime_Rate_RB"] ?? 0,
                "TG_Dime_Rate_RB" => $request_data["TG_Dime_Rate_RB"] ?? 0,
                "S_Single_Rate" => $request_data["S_Single_Rate"] ?? 0,
                "S_Double_Rate" => $request_data["S_Double_Rate"] ?? 0,
                "ShowTypeRB" => $request_data['ShowTypeRB'],
                "FLAG_CLASS" => $request_data['FLAG_CLASS'] ?? "",
                "Eventid" => $request_data["Eventid"] ?? "",
                "Hot" => $request_data["Hot"] ?? 0,
                "Play" => $request_data["Play"] ?? 0,
                "MID" => $request_data["MID"],
                "RB_Show" => $request_data["RB_Show"] ?? 0,
            ];
            $sport = Sport::where("MID", $request_data['MID'])->first();
            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'BK Data updated successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } else {
                $sport = new Sport;
                $sport->create($new_data);
                $response['message'] = 'BK Inplay Data added successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            }
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveBKDefaultParlay(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();
            $new_data = [
                "Type" => $request_data["Type"],
                "LID" => $request_data["LID"] ?? "",
                "ECID" => $request_data["ECID"] ?? "",
                "M_Date" => $request_data["M_Date"] ?? date("Y-m-d"),
                "M_Time" => $request_data["M_Time"] ?? date("H:i:s"),
                "M_Start" => $request_data["M_Start"] ?? date("Y-m-d")." ".date("H:i:s"),
                "MB_Team" => $request_data["MB_Team"],
                "TG_Team" => $request_data["TG_Team"],
                "MB_Team_tw" => $request_data["MB_Team_tw"],
                "TG_Team_tw" => $request_data["TG_Team_tw"],
                "MB_Team_en" => $request_data["MB_Team_en"],
                "TG_Team_en" => $request_data["TG_Team_en"],
                "M_League" => $request_data["M_League"],
                "M_League_tw" => $request_data["M_League_tw"],
                "M_League_en" => $request_data["M_League_en"],
                "MB_MID" => $request_data["MB_MID"],
                "TG_MID" => $request_data["TG_MID"],
                "M_P_LetB" => $request_data["M_P_LetB"] ?? "",
                "MB_P_LetB_Rate" => $request_data["MB_P_LetB_Rate"] ?? 0,
                "TG_P_LetB_Rate" => $request_data["TG_P_LetB_Rate"] ?? 0,
                "MB_P_Dime" => $request_data["MB_P_Dime"] ?? "",
                "TG_P_Dime" => $request_data["TG_P_Dime"] ?? "",
                "MB_P_Dime_Rate" => $request_data["MB_P_Dime_Rate"] ?? 0,
                "TG_P_Dime_Rate" => $request_data["TG_P_Dime_Rate"] ?? 0,
                "S_P_Single_Rate" => $request_data["S_P_Single_Rate"] ?? 0,
                "S_P_Double_Rate" => $request_data["S_P_Double_Rate"] ?? 0,
                "ShowTypeP" => $request_data['ShowTypeP'],
                "FLAG_CLASS" => $request_data['FLAG_CLASS'],
                "Eventid" => $request_data["Eventid"] ?? "",
                "MID" => $request_data["MID"],
                "P3_Show" => $request_data["P3_Show"],
            ];
            $sport = Sport::where("MID", $request_data['MID'])->first();
            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'BK Data updated successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } else {
                $sport = new Sport;
                $sport->create($new_data);
                $response['message'] = 'BK Today Data added successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            }
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getBKData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $newDate = now()->subMinutes(4 * 60 + 90);
            $sports = Sport::where("Type", "FT")->where("M_Start", ">=", $newDate)->get();
            $response['data'] = $sports;
            $response['message'] = 'Match Sport Data fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getBKInPlayData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $newDate = now()->subMinutes(4 * 60 + 90);
            $sports = Sport::where("Type", "FT")->where("M_Date", date("Y-m-d"))->where("isSub", 1)->where("RB_Show", 1)->where("M_Start", ">=", $newDate)->get();
            $response['data'] = $sports;
            $response['message'] = 'Match Sport Data fetched successfully!';
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
