<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sport;

class MatchSportController extends Controller
{
    public function saveFT_FU_R(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $request_data = $request->all();
            $new_data = [
                "MID" => $request_data['MID'],
                "Type" => $request_data['Type'],
                "LID" => $request_data['LID'],
                "MB_MID" => $request_data['MB_MID'],
                "TG_MID" => $request_data['TG_MID'],
                "Play" => $request_data['Play'],
                "S_Show" => $request_data['S_Show'],
                "MB_Team" => $request_data['MB_Team'],
                "TG_Team" => $request_data['TG_Team'],
                "MB_Team_tw" => $request_data['MB_Team_tw'],
                "TG_Team_tw" => $request_data['TG_Team_tw'],
                "MB_Team_en" => $request_data['MB_Team_en'],
                "TG_Team_en" => $request_data['TG_Team_en'],
                "M_Date" => $request_data['M_Date'],
                "M_Time" => $request_data['M_Time'],
                "M_Start" => $request_data['M_Start'],
                "M_League" => $request_data['M_League'],
                "M_League_tw" => $request_data['M_League_tw'],
                "M_League_en" => $request_data['M_League_en'],
                "M_Type" => $request_data['M_Type'],
                'ShowTypeR' => $request_data['ShowTypeR'],
                "MB_Win_Rate" => $request_data['MB_Win_Rate'],
                "TG_Win_Rate" => $request_data['TG_Win_Rate'],
                "M_Flat_Rate" => $request_data['M_Flat_Rate'],
                "M_LetB" => $request_data['M_LetB'],
                "MB_LetB_Rate" => $request_data['MB_LetB_Rate'],
                "TG_LetB_Rate" => $request_data['Type'],
                "MB_Dime" => $request_data['MB_Dime'],
                "TG_Dime" => $request_data['TG_Dime'],
                "MB_Dime_Rate" => $request_data['MB_Dime_Rate'],
                "TG_Dime_Rate" => $request_data['TG_Dime_Rate'],
                "ShowTypeHR" => $request_data['ShowTypeHR'],
                "MB_Win_Rate_H" => $request_data['MB_Win_Rate_H'],
                "TG_Win_Rate_H" => $request_data['TG_Win_Rate_H'],
                "M_Flat_Rate_H" => $request_data['M_Flat_Rate_H'],
                "M_LetB_H" => $request_data['M_LetB_H'],
                "MB_LetB_Rate_H" => $request_data['MB_LetB_Rate_H'],
                "TG_LetB_Rate_H" => $request_data['TG_LetB_Rate_H'],
                "MB_Dime_H" => $request_data['MB_Dime_H'],
                "TG_Dime_H" => $request_data['TG_Dime_H'],
                "MB_Dime_Rate_H" => $request_data['MB_Dime_Rate_H'],
                "TG_Dime_Rate_H" => $request_data['TG_Dime_Rate_H'],
                "ShowTypeRB" => $request_data['ShowTypeRB'],                
                "FLAG_CLASS" => $request_data['FLAG_CLASS'],
            ];
            $sport = Sport::where("MID", $request_data['MID'])->first();
            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'Match Sport Data updated successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } else {
                $sport = new Sport;
                $sport->create($new_data);
                $response['message'] = 'Match Sport Data added successfully!';
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

    public function saveFT_FU_R_INPLAY(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();
            $new_data = [
                "Type" => $request_data["Type"],
                "Retime" => $request_data["Retime"],
                "ECID" => $request_data["ECID"],
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
                "ShowTypeRB" => $request_data["ShowTypeRB"],
                "M_LetB_RB" => $request_data["M_LetB_RB"],
                "MB_LetB_Rate_RB" => $request_data["MB_LetB_Rate_RB"],
                "TG_LetB_Rate_RB" => $request_data["TG_LetB_Rate_RB"],
                "MB_Dime_RB" => $request_data["MB_Dime_RB"],
                "TG_Dime_RB" => $request_data["TG_Dime_RB"],
                "MB_Dime_Rate_RB" => $request_data["MB_Dime_Rate_RB"],
                "TG_Dime_Rate_RB" => $request_data["TG_Dime_Rate_RB"],
                "ShowTypeHRB" => $request_data["ShowTypeHRB"],
                "M_LetB_RB_H" => $request_data["M_LetB_RB_H"],
                "MB_LetB_Rate_RB_H" => $request_data["MB_LetB_Rate_RB_H"],
                "TG_LetB_Rate_RB_H" => $request_data["TG_LetB_Rate_RB_H"],
                "MB_Dime_RB_H" => $request_data["MB_Dime_RB_H"],
                "TG_Dime_RB_H" => $request_data["TG_Dime_RB_H"],
                "MB_Dime_Rate_RB_H" => $request_data["MB_Dime_Rate_RB_H"],
                "TG_Dime_Rate_RB_H" => $request_data["TG_Dime_Rate_RB_H"],
                "MB_Ball" => $request_data["MB_Ball"],
                "TG_Ball" => $request_data["TG_Ball"],
                "MB_Win_Rate_RB" => $request_data["MB_Win_Rate_RB"],
                "TG_Win_Rate_RB" => $request_data["TG_Win_Rate_RB"],
                "M_Flat_Rate_RB" => $request_data["M_Flat_Rate_RB"],
                "MB_Win_Rate_RB_H" => $request_data["MB_Win_Rate_RB_H"],
                "TG_Win_Rate_RB_H" => $request_data["TG_Win_Rate_RB_H"],
                "M_Flat_Rate_RB_H" => $request_data["M_Flat_Rate_RB_H"],
                "FLAG_CLASS" => $request_data['FLAG_CLASS'],
                // "Eventid" => $request_data["Eventid"],
                "Hot" => $request_data["Hot"],
                "Play" => $request_data["Play"],
                "MID" => $request_data["MID"],
                "RB_Show" => $request_data["RB_Show"],
                "S_Show" => $request_data["S_Show"],
                "isSub" => $request_data["isSub"],
                "MB_Card" => $request_data["MB_Card"] ?? "",
                "TG_Card" => $request_data["TG_Card"] ?? "",
                "RETIME_SET" => $request_data["RETIME_SET"],
                "HDP_OU" => $request_data['HDP_OU'],
                "CORNER" => $request_data['CORNER'],
            ];
            $sport = Sport::where("MID", $request_data['MID'])->first();
            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'Match Sport Data updated successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } else {
                $sport = new Sport;
                $sport->create($new_data);
                $response['message'] = 'Match Sport Data added successfully!';
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

    public function saveFT_CORRECT_SCORE(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $request_data = $request->all();
            $new_data = [
                "Type" => $request_data['Type'],
                "MID" => $request_data['MID'],
                "MB_Ball" => $request_data['MB_Ball'] ?? 0,
                "TG_Ball" => $request_data['MB_Ball'] ?? 0,
                "RETIME_SET" => $request_data['RETIME_SET'],
                "MB1TG0" => $request_data['MB1TG0'] ?? 0,
                "MB2TG0" => $request_data['MB2TG0'] ?? 0,
                "MB2TG1" => $request_data['MB2TG1'] ?? 0,
                "MB3TG0" => $request_data['MB3TG0'] ?? 0,
                "MB3TG1" => $request_data['MB3TG1'] ?? 0,
                "MB3TG2" => $request_data['MB3TG2'] ?? 0,
                "MB4TG0" => $request_data['MB4TG0'] ?? 0,
                "MB4TG1" => $request_data['MB4TG1'] ?? 0,
                "MB4TG2" => $request_data['MB4TG2'] ?? 0,
                "MB4TG3" => $request_data['MB4TG3'] ?? 0,
                "MB0TG0" => $request_data['MB0TG0'] ?? 0,
                "MB1TG1" => $request_data['MB1TG1'] ?? 0,
                "MB2TG2" => $request_data['MB2TG2'] ?? 0,
                "MB3TG3" => $request_data['MB3TG3'] ?? 0,
                "MB4TG4" => $request_data['MB4TG4'] ?? 0,
                "MB0TG1" => $request_data['MB0TG1'] ?? 0,
                "MB0TG2" => $request_data['MB0TG2'] ?? 0,
                "MB1TG2" => $request_data['MB1TG2'] ?? 0,
                "MB0TG3" => $request_data['MB0TG3'] ?? 0,
                "MB1TG3" => $request_data['MB1TG3'] ?? 0,
                "MB2TG3" => $request_data['MB2TG3'] ?? 0,
                "MB0TG4" => $request_data['MB0TG4'] ?? 0,
                "MB1TG4" => $request_data['MB1TG4'] ?? 0,
                "MB2TG4" => $request_data['MB2TG4'] ?? 0,
                "MB3TG4" => $request_data['MB3TG4'] ?? 0,
                "UP5" => $request_data['UP5'] ?? 0,
                "UP5H" => $request_data['UP5H'] ?? 0,
                "PD_Show" => 1,
            ];
            $sport = Sport::where("MID", $request_data['MID'])->first();
            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'Match Sport Data updated successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } else {
                $sport = new Sport;
                $sport->create($new_data);
                $response['message'] = 'Match Sport Data added successfully!';
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

    public function saveFT_HDP_OBT(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $request_data = $request->all();
            $new_data = [
                "MID" => $request_data['MID'],
                "RATIO_RE_HDP_0" => $request_data['RATIO_RE_HDP_0'] ?? "",
                "IOR_REH_HDP_0" => $request_data['IOR_REH_HDP_0'] ?? 0,
                "IOR_REC_HDP_0" => $request_data['IOR_REC_HDP_0'] ?? 0,
                "RATIO_ROUO_HDP_0" => $request_data['RATIO_ROUO_HDP_0'] ?? "",
                "RATIO_ROUU_HDP_0" => $request_data['RATIO_ROUU_HDP_0'] ?? "",
                "IOR_ROUH_HDP_0" => $request_data['IOR_ROUH_HDP_0'] ?? 0,
                "IOR_ROUC_HDP_0" => $request_data['IOR_ROUC_HDP_0'] ?? 0,
                "RATIO_RE_HDP_1" => $request_data['RATIO_RE_HDP_1'] ?? "",
                "IOR_REH_HDP_1" => $request_data['IOR_REH_HDP_1'] ?? 0,
                "IOR_REC_HDP_1" => $request_data['IOR_REC_HDP_1'] ?? 0,
                "RATIO_ROUO_HDP_1" => $request_data['RATIO_ROUO_HDP_1'] ?? "",
                "RATIO_ROUU_HDP_1" => $request_data['RATIO_ROUU_HDP_1'] ?? "",
                "IOR_ROUC_HDP_1" => $request_data['IOR_ROUC_HDP_1'] ?? 0,
                "IOR_ROUH_HDP_1" => $request_data['IOR_ROUH_HDP_1'] ?? 0,
                "RATIO_RE_HDP_2" => $request_data['RATIO_RE_HDP_2'] ?? "",
                "IOR_REH_HDP_2" => $request_data['IOR_REH_HDP_2'] ?? 0,
                "IOR_REC_HDP_2" => $request_data['IOR_REC_HDP_2'] ?? 0,
                "RATIO_ROUO_HDP_2" => $request_data['RATIO_ROUO_HDP_2'] ?? "",
                "RATIO_ROUU_HDP_2" => $request_data['RATIO_ROUU_HDP_2'] ?? "",
                "IOR_ROUH_HDP_2" => $request_data['IOR_ROUH_HDP_2'] ?? 0,
                "IOR_ROUC_HDP_2" => $request_data['IOR_ROUC_HDP_2'] ?? 0,
                "RATIO_RE_HDP_3" => $request_data['RATIO_RE_HDP_3'] ?? "",
                "IOR_REH_HDP_3" => $request_data['IOR_REH_HDP_3'] ?? 0,
                "IOR_REC_HDP_3" => $request_data['IOR_REC_HDP_3'] ?? 0,
                "RATIO_ROUO_HDP_3" => $request_data['RATIO_ROUO_HDP_3'] ?? "",
                "RATIO_ROUU_HDP_3" => $request_data['RATIO_ROUU_HDP_3'] ?? "",
                "IOR_ROUH_HDP_3" => $request_data['IOR_ROUH_HDP_3'] ?? 0,
                "IOR_ROUC_HDP_3" => $request_data['IOR_ROUC_HDP_3'] ?? 0,
            ];
            $sport = Sport::where("MID", $request_data['MID'])->first();
            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'Match Sport OBT Data updated successfully!';
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

    public function saveFT_CORNER_INPLAY (Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $request_data = $request->all();
            $new_data = [
                "MID" => $request_data['MID'],
                "RATIO_ROUO_CN" => $request_data['RATIO_ROUO_CN'] ?? "",
                "RATIO_ROUU_CN" => $request_data['RATIO_ROUU_CN'] ?? "",
                "IOR_ROUH_CN" => $request_data['IOR_ROUH_CN'] ?? 0,
                "IOR_ROUC_CN" => $request_data['IOR_ROUC_CN'] ?? 0,
                "RATIO_HROUO_CN" => $request_data['RATIO_HROUO_CN'] ?? "",
                "RATIO_HROUU_CN" => $request_data['RATIO_HROUU_CN'] ?? "",
                "IOR_HROUH_CN" => $request_data['IOR_HROUH_CN'] ?? 0,
                "IOR_HROUC_CN" => $request_data['IOR_HROUC_CN'] ?? 0,
                "STR_ODD_CN" => $request_data['STR_ODD_CN'] ?? "",
                "STR_EVEN_CN" => $request_data['STR_EVEN_CN'] ?? "",
                "IOR_REOO_CN" => $request_data['IOR_REOO_CN'] ?? 0,
                "IOR_REOE_CN" => $request_data['IOR_REOE_CN'] ?? 0,
                "STR_HODD_CN" => $request_data['STR_HODD_CN'] ?? "",
                "STR_HEVEN_CN" => $request_data['STR_HEVEN_CN'] ?? "",
                "IOR_HREOO_CN" => $request_data['IOR_HREOO_CN'] ?? 0,
                "IOR_HREOE_CN" => $request_data['IOR_HREOE_CN'] ?? 0,
                "WTYPE_CN" => $request_data['WTYPE_CN'] ?? "",
                "IOR_RNCH_CN" => $request_data['IOR_RNCH_CN'] ?? 0,
                "IOR_RNCC_CN" => $request_data['IOR_RNCC_CN'] ?? 0,
            ];
            $sport = Sport::where("MID", $request_data['MID'])->first();
            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'Match Sport CORNER Data updated successfully!';
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

    public function saveFT_CORNER_TODAY (Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $request_data = $request->all();
            $new_data = [
                "MID" => $request_data['MID'],
                "RATIO_R_CN" => $request_data['RATIO_R_CN'] ?? "",
                "IOR_RH_CN" => $request_data['IOR_RH_CN'] ?? "",
                "IOR_RC_CN" => $request_data['IOR_RC_CN'] ?? 0,
                "RATIO_OUO_CN" => $request_data['RATIO_OUO_CN'] ?? "",
                "RATIO_OUU_CN" => $request_data['RATIO_OUU_CN'] ?? "",
                "IOR_HRH_CN" => $request_data['IOR_HRH_CN'] ?? 0,
                "IOR_HRC_CN" => $request_data['IOR_HRC_CN'] ?? 0,
                "IOR_MH_CN" => $request_data['IOR_MH_CN'] ?? "",
                "IOR_MC_CN" => $request_data['IOR_MC_CN'] ?? "",
                "IOR_MN_CN" => $request_data['IOR_MN_CN'] ?? 0,
            ];
            $sport = Sport::where("MID", $request_data['MID'])->first();
            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'Match Sport CORNER Data updated successfully!';
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

    public function getFTData(Request $request) {

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

    public function getFTInPlayData(Request $request) {

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

    public function getFTCorrectScoreInPlayData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $newDate = now()->subMinutes(4 * 60 + 90);
            $sports = Sport::where("Type", "FT")->where("M_Date", date("Y-m-d"))->where("PD_Show", 1)->where("M_Start", ">=", $newDate)->get();
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
