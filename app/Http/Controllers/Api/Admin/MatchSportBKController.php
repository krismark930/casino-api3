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
                "LID" => $request_data["LID"],
                "M_Date" => $request_data["M_Date"],
                "M_Time" => $request_data["M_Time"],
                "M_Start" => $request_data["M_Start"],
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
                "MB_Points_1" => $request_data["MB_Points_1"] ?? "",
                "TG_Points_1" => $request_data["TG_Points_1"] ?? "",
                "MB_Points_Rate_1" => $request_data["MB_Points_Rate_1"] ?? 0,
                "TG_Points_Rate_1" => $request_data["TG_Points_Rate_1"] ?? 0,
                "MB_Points_2" => $request_data["MB_Points_2"] ?? "",
                "TG_Points_2" => $request_data["TG_Points_2"] ?? "",
                "MB_Points_Rate_2" => $request_data["MB_Points_Rate_2"] ?? 0,
                "TG_Points_Rate_2" => $request_data["TG_Points_Rate_2"] ?? 0,
                "FLAG_CLASS" => $request_data['FLAG_CLASS'],
                "Eventid" => $request_data["Eventid"] ?? "",
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
                "Retime" => $request_data["Retime"],
                "LID" => $request_data["LID"],
                "M_Date" => $request_data["M_Date"],
                "M_Time" => $request_data["M_Time"],
                "M_Start" => $request_data["M_Start"],
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
                "MB_Ball" => $request_data["MB_Ball"],
                "TG_Ball" => $request_data["TG_Ball"],
                "M_LetB_RB" => $request_data["M_LetB_RB"] ?? "",
                "MB_LetB_Rate_RB" => $request_data["MB_LetB_Rate_RB"] ?? 0,
                "TG_LetB_Rate_RB" => $request_data["TG_LetB_Rate_RB"] ?? 0,
                "MB_Dime_RB" => $request_data["MB_Dime_RB"] ?? "",
                "TG_Dime_RB" => $request_data["TG_Dime_RB"] ?? "",
                "MB_Dime_Rate_RB" => $request_data["MB_Dime_Rate_RB"] ?? 0,
                "TG_Dime_Rate_RB" => $request_data["TG_Dime_Rate_RB"] ?? 0,
                "MB_Points_RB_1" => $request_data["MB_Points_RB_1"] ?? "",
                "TG_Points_RB_1" => $request_data["TG_Points_RB_1"] ?? "",
                "MB_Points_Rate_RB_1" => $request_data["MB_Points_Rate_RB_1"] ?? 0,
                "TG_Points_Rate_RB_1" => $request_data["TG_Points_Rate_RB_1"] ?? 0,
                "MB_Points_RB_2" => $request_data["MB_Points_RB_2"] ?? "",
                "TG_Points_RB_2" => $request_data["TG_Points_RB_2"] ?? "",
                "MB_Points_Rate_RB_2" => $request_data["MB_Points_Rate_RB_2"] ?? 0,
                "TG_Points_Rate_RB_2" => $request_data["TG_Points_Rate_RB_2"] ?? 0,
                "FLAG_CLASS" => $request_data['FLAG_CLASS'],
                "Eventid" => $request_data["Eventid"] ?? "",
                "Hot" => $request_data["Hot"] ?? 0,
                "Play" => $request_data["Play"] ?? 0,
                "MID" => $request_data["MID"],
                "RB_Show" => $request_data["RB_Show"],
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
                "LID" => $request_data["LID"],
                "M_Date" => $request_data["M_Date"],
                "M_Time" => $request_data["M_Time"],
                "M_Start" => $request_data["M_Start"],
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
                "MB_P_Points_1" => $request_data["MB_P_Points_1"] ?? "",
                "TG_P_Points_1" => $request_data["TG_P_Points_1"] ?? "",
                "MB_P_Points_Rate_1" => $request_data["MB_P_Points_Rate_1"] ?? 0,
                "TG_P_Points_Rate_1" => $request_data["TG_P_Points_Rate_1"] ?? 0,
                "MB_P_Points_2" => $request_data["MB_P_Points_2"] ?? "",
                "TG_P_Points_2" => $request_data["TG_P_Points_2"] ?? "",
                "MB_P_Points_Rate_2" => $request_data["MB_P_Points_Rate_2"] ?? 0,
                "TG_P_Points_Rate_2" => $request_data["TG_P_Points_Rate_2"] ?? 0,
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
