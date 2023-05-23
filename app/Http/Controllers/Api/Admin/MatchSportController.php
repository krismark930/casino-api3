<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sport;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MatchSportController extends Controller
{
    public function saveFTDefaultToday(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $request_data = $request->all();
            $new_data = [
                "MID" => $request_data['MID'],
                "ECID" => $request_data["ECID"] ?? "",
                "Type" => $request_data['Type'],
                "LID" => $request_data['LID'],
                "MB_MID" => $request_data['MB_MID'],
                "TG_MID" => $request_data['TG_MID'],
                "MB_Team" => $request_data['MB_Team'],
                "TG_Team" => $request_data['TG_Team'],
                "MB_Team_tw" => $request_data['MB_Team_tw'],
                "TG_Team_tw" => $request_data['TG_Team_tw'],
                "MB_Team_en" => $request_data['MB_Team_en'],
                "TG_Team_en" => $request_data['TG_Team_en'],
                "M_League" => $request_data['M_League'],
                "M_League_tw" => $request_data['M_League_tw'],
                "M_League_en" => $request_data['M_League_en'],
                "M_Date" => $request_data['M_Date'] ?? date("Y-m-d"),
                "M_Time" => $request_data['M_Time'] ?? date("H:i:s"),
                "M_Start" => $request_data['M_Start'] ?? date("Y-m-d")." ".date("H:i:s"),
                "Play" => $request_data['Play'] ?? 0,
                "S_Show" => $request_data['S_Show'] ?? 0,
                "M_Type" => $request_data['M_Type'] ?? 0,
                'ShowTypeR' => $request_data['ShowTypeR'] ?? "H",
                "MB_Win_Rate" => $request_data['MB_Win_Rate'] ?? 0,
                "TG_Win_Rate" => $request_data['TG_Win_Rate'] ?? 0,
                "M_Flat_Rate" => $request_data['M_Flat_Rate'] ?? 0,
                "M_LetB" => $request_data['M_LetB'] ?? "",
                "MB_LetB_Rate" => $request_data['MB_LetB_Rate'] ?? 0,
                "TG_LetB_Rate" => $request_data['TG_LetB_Rate'] ?? 0,
                "MB_Dime" => $request_data['MB_Dime'] ?? "",
                "TG_Dime" => $request_data['TG_Dime'] ?? "",
                "MB_Dime_Rate" => $request_data['MB_Dime_Rate'] ?? 0,
                "TG_Dime_Rate" => $request_data['TG_Dime_Rate'] ?? 0,
                "ShowTypeHR" => $request_data['ShowTypeHR'] ?? "",
                "MB_Win_Rate_H" => $request_data['MB_Win_Rate_H'] ?? 0,
                "TG_Win_Rate_H" => $request_data['TG_Win_Rate_H'] ?? 0,
                "M_Flat_Rate_H" => $request_data['M_Flat_Rate_H'] ?? 0,
                "M_LetB_H" => $request_data['M_LetB_H'] ?? "",
                "MB_LetB_Rate_H" => $request_data['MB_LetB_Rate_H'] ?? 0,
                "TG_LetB_Rate_H" => $request_data['TG_LetB_Rate_H'] ?? 0,
                "MB_Dime_H" => $request_data['MB_Dime_H'] ?? "",
                "TG_Dime_H" => $request_data['TG_Dime_H'] ?? "",
                "MB_Dime_Rate_H" => $request_data['MB_Dime_Rate_H'] ?? 0,
                "TG_Dime_Rate_H" => $request_data['TG_Dime_Rate_H'] ?? 0,
                "ShowTypeRB" => $request_data['ShowTypeRB'],                
                "FLAG_CLASS" => $request_data['FLAG_CLASS'],
                "M_LetB_1" => $request_data['M_LetB_1'] ?? "",
                "MB_LetB_Rate_1" => $request_data['MB_LetB_Rate_1'] ?? 0,
                "TG_LetB_Rate_1" => $request_data['TG_LetB_Rate_1'] ?? 0,
                "MB_Dime_1" => $request_data['MB_Dime_1'] ?? "",
                "TG_Dime_1" => $request_data['TG_Dime_1'] ?? "",
                "MB_Dime_Rate_1" => $request_data['MB_Dime_Rate_1'] ?? 0,
                "TG_Dime_Rate_1" => $request_data['TG_Dime_Rate_1'] ?? 0,
                "M_LetB_2" => $request_data['M_LetB_2'] ?? "",
                "MB_LetB_Rate_2" => $request_data['MB_LetB_Rate_2'] ?? 0,
                "TG_LetB_Rate_2" => $request_data['TG_LetB_Rate_2'] ?? 0,
                "MB_Dime_2" => $request_data['MB_Dime_2'] ?? "",
                "TG_Dime_2" => $request_data['TG_Dime_2'] ?? "",
                "MB_Dime_Rate_2" => $request_data['MB_Dime_Rate_2'] ?? 0,
                "TG_Dime_Rate_2" => $request_data['TG_Dime_Rate_2'] ?? 0,
                "M_LetB_3" => $request_data['M_LetB_3'] ?? "",
                "MB_LetB_Rate_3" => $request_data['MB_LetB_Rate_3'] ?? 0,
                "TG_LetB_Rate_3" => $request_data['TG_LetB_Rate_3'] ?? 0,
                "MB_Dime_3" => $request_data['MB_Dime_3'] ?? "",
                "TG_Dime_3" => $request_data['TG_Dime_3'] ?? "",
                "MB_Dime_Rate_3" => $request_data['MB_Dime_Rate_3'] ?? 0,
                "TG_Dime_Rate_3" => $request_data['TG_Dime_Rate_3'] ?? 0,
                "S_Single_Rate" => $request_data["S_Single_Rate"] ?? 0,
                "S_Double_Rate" => $request_data["S_Double_Rate"] ?? 0,
                "S_Single_Rate_H" => $request_data["S_Single_Rate_H"] ?? 0,
                "S_Double_Rate_H" => $request_data["S_Double_Rate_H"] ?? 0, 
                "MBMB" => $request_data['MBMB'] ?? 0,
                "MBFT" => $request_data['MBFT'] ?? 0,
                "MBTG" => $request_data['MBTG'] ?? 0,
                "FTMB" => $request_data['FTMB'] ?? 0,
                "FTFT" => $request_data['FTFT'] ?? 0,
                "FTTG" => $request_data['FTTG'] ?? 0,
                "TGMB" => $request_data['TGMB'] ?? 0,
                "TGTG" => $request_data['TGTG'] ?? 0,
                "TGFT" => $request_data['TGFT'] ?? 0,
                "S_0_1" => $request_data['S_0_1'] ?? 0,
                "S_2_3" => $request_data['S_2_3'] ?? 0,
                "S_4_6" => $request_data['S_4_6'] ?? 0,
                "S_7UP" => $request_data['S_7UP'] ?? 0,
                "T_Show" => $request_data['T_Show'] ?? 0,
                "F_Show" => $request_data['F_Show'] ?? 0,
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

    public function saveFTDefaultInplay(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();
            $new_data = [
                "Type" => $request_data["Type"],
                "Retime" => $request_data["Retime"],
                "ECID" => $request_data["ECID"] ?? "",
                "LID" => $request_data["LID"],
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
                "ShowTypeRB" => $request_data["ShowTypeRB"] ?? "",
                "M_LetB_RB" => $request_data["M_LetB_RB"] ?? "",
                "MB_LetB_Rate_RB" => $request_data["MB_LetB_Rate_RB"] ?? 0,
                "TG_LetB_Rate_RB" => $request_data["TG_LetB_Rate_RB"] ?? 0,
                "MB_Dime_RB" => $request_data["MB_Dime_RB"] ?? "",
                "TG_Dime_RB" => $request_data["TG_Dime_RB"] ?? "",
                "MB_Dime_Rate_RB" => $request_data["MB_Dime_Rate_RB"] ?? 0,
                "TG_Dime_Rate_RB" => $request_data["TG_Dime_Rate_RB"] ?? 0,
                "ShowTypeHRB" => $request_data["ShowTypeHRB"] ?? "",
                "M_LetB_RB_H" => $request_data["M_LetB_RB_H"] ?? "",
                "MB_LetB_Rate_RB_H" => $request_data["MB_LetB_Rate_RB_H"] ?? 0,
                "TG_LetB_Rate_RB_H" => $request_data["TG_LetB_Rate_RB_H"] ?? 0,
                "MB_Dime_RB_H" => $request_data["MB_Dime_RB_H"] ?? "",
                "TG_Dime_RB_H" => $request_data["TG_Dime_RB_H"] ?? "",
                "MB_Dime_Rate_RB_H" => $request_data["MB_Dime_Rate_RB_H"] ?? 0,
                "TG_Dime_Rate_RB_H" => $request_data["TG_Dime_Rate_RB_H"] ?? 0,
                "MB_Ball" => $request_data["MB_Ball"] ?? 0,
                "TG_Ball" => $request_data["TG_Ball"] ?? 0,
                "MB_Win_Rate_RB" => $request_data["MB_Win_Rate_RB"] ?? 0,
                "TG_Win_Rate_RB" => $request_data["TG_Win_Rate_RB"] ?? 0,
                "M_Flat_Rate_RB" => $request_data["M_Flat_Rate_RB"] ?? 0,
                "MB_Win_Rate_RB_H" => $request_data["MB_Win_Rate_RB_H"] ?? 0,
                "TG_Win_Rate_RB_H" => $request_data["TG_Win_Rate_RB_H"] ?? 0,
                "M_Flat_Rate_RB_H" => $request_data["M_Flat_Rate_RB_H"] ?? 0,
                "FLAG_CLASS" => $request_data['FLAG_CLASS'],
                // "Eventid" => $request_data["Eventid"],
                "Hot" => $request_data["Hot"] ?? 0,
                "Play" => $request_data["Play"] ?? 0,
                "MID" => $request_data["MID"],
                "RB_Show" => $request_data["RB_Show"] ?? 0,
                "S_Show" => $request_data["S_Show"] ?? 0,
                "isSub" => $request_data["isSub"] ?? 0,
                "MB_Card" => $request_data["MB_Card"] ?? "",
                "TG_Card" => $request_data["TG_Card"] ?? "",
                "RETIME_SET" => $request_data["RETIME_SET"],
                "HDP_OU" => $request_data['HDP_OU'] ?? 0,
                "CORNER" => $request_data['CORNER'] ?? 0,
                "M_LetB_RB_1" => $request_data['M_LetB_RB_1'] ?? "",
                "MB_LetB_Rate_RB_1" => $request_data['MB_LetB_Rate_RB_1'] ?? 0,
                "TG_LetB_Rate_RB_1" => $request_data['TG_LetB_Rate_RB_1'] ?? 0,
                "MB_Dime_RB_1" => $request_data['MB_Dime_RB_1'] ?? "",
                "TG_Dime_RB_1" => $request_data['TG_Dime_RB_1'] ?? "",
                "MB_Dime_Rate_RB_1" => $request_data['MB_Dime_Rate_RB_1'] ?? 0,
                "TG_Dime_Rate_RB_1" => $request_data['TG_Dime_Rate_RB_1'] ?? 0,
                "M_LetB_RB_2" => $request_data['M_LetB_RB_2'] ?? "",
                "MB_LetB_Rate_RB_2" => $request_data['MB_LetB_Rate_RB_2'] ?? 0,
                "TG_LetB_Rate_RB_2" => $request_data['TG_LetB_Rate_RB_2'] ?? 0,
                "MB_Dime_RB_2" => $request_data['MB_Dime_RB_2'] ?? "",
                "TG_Dime_RB_2" => $request_data['TG_Dime_RB_2'] ?? "",
                "MB_Dime_Rate_RB_2" => $request_data['MB_Dime_Rate_RB_2'] ?? 0,
                "TG_Dime_Rate_RB_2" => $request_data['TG_Dime_Rate_RB_2'] ?? 0,
                "M_LetB_RB_3" => $request_data['M_LetB_RB_3'] ?? "",
                "MB_LetB_Rate_RB_3" => $request_data['MB_LetB_Rate_RB_3'] ?? 0,
                "TG_LetB_Rate_RB_3" => $request_data['TG_LetB_Rate_RB_3'] ?? 0,
                "MB_Dime_RB_3" => $request_data['MB_Dime_RB_3'] ?? "",
                "TG_Dime_RB_3" => $request_data['TG_Dime_RB_3'] ?? "",
                "MB_Dime_Rate_RB_3" => $request_data['MB_Dime_Rate_RB_3'] ?? 0,
                "TG_Dime_Rate_RB_3" => $request_data['TG_Dime_Rate_RB_3'] ?? 0,
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

    public function saveFTDefaultParlay(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $request_data = $request->all();
            $new_data = [
                "MID" => $request_data['MID'],
                "Type" => $request_data['Type'],
                "LID" => $request_data['LID'],
                "ECID" => $request_data["ECID"] ?? "",
                "MB_MID" => $request_data['MB_MID'],
                "TG_MID" => $request_data['TG_MID'],
                "P3_Show" => $request_data['P3_Show'],
                "MB_Team" => $request_data['MB_Team'],
                "TG_Team" => $request_data['TG_Team'],
                "MB_Team_tw" => $request_data['MB_Team_tw'],
                "TG_Team_tw" => $request_data['TG_Team_tw'],
                "MB_Team_en" => $request_data['MB_Team_en'],
                "TG_Team_en" => $request_data['TG_Team_en'],
                "M_Date" => $request_data["M_Date"] ?? date("Y-m-d"),
                "M_Time" => $request_data["M_Time"] ?? date("H:i:s"),
                "M_Start" => $request_data["M_Start"] ?? date("Y-m-d")." ".date("H:i:s"),
                "M_League" => $request_data['M_League'],
                "M_League_tw" => $request_data['M_League_tw'],
                "M_League_en" => $request_data['M_League_en'],
                'ShowTypeP' => $request_data['ShowTypeP'] ?? "",
                'ShowTypeHP' => $request_data['ShowTypeHP'] ?? "",
                "MB_P_Win_Rate" => $request_data['MB_P_Win_Rate'] ?? 0,
                "TG_P_Win_Rate" => $request_data['TG_P_Win_Rate'] ?? 0,
                "M_P_Flat_Rate" => $request_data['M_P_Flat_Rate'] ?? 0,
                "M_P_LetB" => $request_data['M_P_LetB'] ?? "",
                "MB_P_LetB_Rate" => $request_data['MB_P_LetB_Rate'] ?? 0,
                "TG_P_LetB_Rate" => $request_data['TG_P_LetB_Rate'] ?? 0,
                "MB_P_Dime" => $request_data['MB_P_Dime'] ?? "",
                "TG_P_Dime" => $request_data['TG_P_Dime'] ?? "",
                "MB_P_Dime_Rate" => $request_data['MB_P_Dime_Rate'] ?? 0,
                "TG_P_Dime_Rate" => $request_data['TG_P_Dime_Rate'] ?? 0,
                "MB_P_Win_Rate_H" => $request_data['MB_P_Win_Rate_H'] ?? 0,
                "TG_P_Win_Rate_H" => $request_data['TG_P_Win_Rate_H'] ?? 0,
                "M_P_Flat_Rate_H" => $request_data['M_P_Flat_Rate_H'] ?? 0,
                "M_P_LetB_H" => $request_data['M_P_LetB_H'] ?? "",
                "MB_P_LetB_Rate_H" => $request_data['MB_P_LetB_Rate_H'] ?? 0,
                "TG_P_LetB_Rate_H" => $request_data['TG_P_LetB_Rate_H'] ?? 0,
                "MB_P_Dime_H" => $request_data['MB_P_Dime_H'] ?? "",
                "TG_P_Dime_H" => $request_data['TG_P_Dime_H'] ?? "",
                "MB_P_Dime_Rate_H" => $request_data['MB_P_Dime_Rate_H'] ?? 0,
                "TG_P_Dime_Rate_H" => $request_data['TG_P_Dime_Rate_H'] ?? 0,      
                "FLAG_CLASS" => $request_data['FLAG_CLASS'],
                "M_P_LetB_1" => $request_data['M_P_LetB_1'] ?? "",
                "MB_P_LetB_Rate_1" => $request_data['MB_P_LetB_Rate_1'] ?? 0,
                "TG_P_LetB_Rate_1" => $request_data['TG_P_LetB_Rate_1'] ?? 0,
                "MB_P_Dime_1" => $request_data['MB_P_Dime_1'] ?? "",
                "TG_P_Dime_1" => $request_data['TG_P_Dime_1'] ?? "",
                "MB_P_Dime_Rate_1" => $request_data['MB_P_Dime_Rate_1'] ?? 0,
                "TG_P_Dime_Rate_1" => $request_data['TG_P_Dime_Rate_1'] ?? 0,
                "M_P_LetB_2" => $request_data['M_P_LetB_2'] ?? "",
                "MB_P_LetB_Rate_2" => $request_data['MB_P_LetB_Rate_2'] ?? 0,
                "TG_P_LetB_Rate_2" => $request_data['TG_P_LetB_Rate_2'] ?? 0,
                "MB_P_Dime_2" => $request_data['MB_P_Dime_2'] ?? "",
                "TG_P_Dime_2" => $request_data['TG_P_Dime_2'] ?? "",
                "MB_P_Dime_Rate_2" => $request_data['MB_P_Dime_Rate_2'] ?? 0,
                "TG_P_Dime_Rate_2" => $request_data['TG_P_Dime_Rate_2'] ?? 0,
                "M_P_LetB_3" => $request_data['M_P_LetB_3'] ?? "",
                "MB_P_LetB_Rate_3" => $request_data['MB_P_LetB_Rate_3'] ?? 0,
                "TG_P_LetB_Rate_3" => $request_data['TG_P_LetB_Rate_3'] ?? 0,
                "MB_P_Dime_3" => $request_data['MB_P_Dime_3'] ?? "",
                "TG_P_Dime_3" => $request_data['TG_P_Dime_3'] ?? "",
                "MB_P_Dime_Rate_3" => $request_data['MB_P_Dime_Rate_3'] ?? 0,
                "TG_P_Dime_Rate_3" => $request_data['TG_P_Dime_Rate_3'] ?? 0,
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
                "MID" => $request_data['MID'],
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
                "MB1TG0H" => $request_data['MB1TG0H'] ?? 0,
                "MB2TG0H" => $request_data['MB2TG0H'] ?? 0,
                "MB2TG1H" => $request_data['MB2TG1H'] ?? 0,
                "MB3TG0H" => $request_data['MB3TG0H'] ?? 0,
                "MB3TG1H" => $request_data['MB3TG1H'] ?? 0,
                "MB3TG2H" => $request_data['MB3TG2H'] ?? 0,
                "MB4TG0H" => $request_data['MB4TG0H'] ?? 0,
                "MB4TG1H" => $request_data['MB4TG1H'] ?? 0,
                "MB4TG2H" => $request_data['MB4TG2H'] ?? 0,
                "MB4TG3H" => $request_data['MB4TG3H'] ?? 0,
                "MB0TG0H" => $request_data['MB0TG0H'] ?? 0,
                "MB1TG1H" => $request_data['MB1TG1H'] ?? 0,
                "MB2TG2H" => $request_data['MB2TG2H'] ?? 0,
                "MB3TG3H" => $request_data['MB3TG3H'] ?? 0,
                "MB4TG4H" => $request_data['MB4TG4H'] ?? 0,
                "MB0TG1H" => $request_data['MB0TG1H'] ?? 0,
                "MB0TG2H" => $request_data['MB0TG2H'] ?? 0,
                "MB1TG2H" => $request_data['MB1TG2H'] ?? 0,
                "MB0TG3H" => $request_data['MB0TG3H'] ?? 0,
                "MB1TG3H" => $request_data['MB1TG3H'] ?? 0,
                "MB2TG3H" => $request_data['MB2TG3H'] ?? 0,
                "MB0TG4H" => $request_data['MB0TG4H'] ?? 0,
                "MB1TG4H" => $request_data['MB1TG4H'] ?? 0,
                "MB2TG4H" => $request_data['MB2TG4H'] ?? 0,
                "MB3TG4H" => $request_data['MB3TG4H'] ?? 0,
                "UP5" => $request_data['UP5'] ?? 0,
                "PD_Show" => 1,                
                "UP5H" => $request_data['UP5H'] ?? 0,
                "HPD_Show" => 1,
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

    public function saveFT_CORNER_INPLAY (Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $request_data = $request->all();
            $new_data = [
                "Type" => $request_data["Type"],
                "Retime" => $request_data["Retime"],
                "ECID" => $request_data["ECID"] ?? "",
                "LID" => $request_data["LID"] ?? "",
                "MID" => $request_data["MID"],
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
                "ShowTypeRB" => $request_data["ShowTypeRB"] ?? "",
                "MB_Dime_RB" => $request_data["MB_Dime_RB"] ?? "",
                "TG_Dime_RB" => $request_data["TG_Dime_RB"] ?? "",
                "MB_Dime_Rate_RB" => $request_data["MB_Dime_Rate_RB"] ?? 0,
                "TG_Dime_Rate_RB" => $request_data["TG_Dime_Rate_RB"] ?? 0,
                "ShowTypeHRB" => $request_data["ShowTypeHRB"] ?? "",
                "MB_Dime_RB_H" => $request_data["MB_Dime_RB_H"] ?? "",
                "TG_Dime_RB_H" => $request_data["TG_Dime_RB_H"] ?? "",
                "MB_Dime_Rate_RB_H" => $request_data["MB_Dime_Rate_RB_H"] ?? 0,
                "TG_Dime_Rate_RB_H" => $request_data["TG_Dime_Rate_RB_H"] ?? 0,
                "S_Single_Rate" => $request_data["S_Single_Rate"] ?? 0,
                "S_Double_Rate" => $request_data["S_Double_Rate"] ?? 0,
                "S_Single_Rate_H" => $request_data["S_Single_Rate_H"] ?? 0,
                "S_Double_Rate_H" => $request_data["S_Double_Rate_H"] ?? 0, 
                "MB_Ball" => $request_data["MB_Ball"] ?? 0,
                "TG_Ball" => $request_data["TG_Ball"] ?? 0,
                "S_Show" => $request_data["S_Show"] ?? 0,
                "isSub" => $request_data["isSub"] ?? 0
            ];

            $sport = Sport::where("MID", $request_data['MID'])->first();

            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'Match Sport Corner Data updated successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } else {
                $sport = new Sport;
                $sport->create($new_data);
                $response['message'] = 'Match Sport Corner Data added successfully!';
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
                "Type" => $request_data["Type"],
                "ECID" => $request_data["ECID"] ?? "",
                "LID" => $request_data["LID"] ?? "",
                "MID" => $request_data["MID"],
                "MB_MID" => $request_data["MB_MID"],
                "TG_MID" => $request_data["TG_MID"],
                "MB_Team" => $request_data["MB_Team"],
                "TG_Team" => $request_data["TG_Team"],
                "MB_Team_tw" => $request_data["MB_Team_tw"],
                "TG_Team_tw" => $request_data["TG_Team_tw"],
                "MB_Team_en" => $request_data["MB_Team_en"],
                "TG_Team_en" => $request_data["TG_Team_en"],
                "M_Date" => $request_data["M_Date"] ?? date("Y-m-d"),
                "M_Time" => $request_data["M_Time"] ?? date("H:i:s"),
                "M_Start" => $request_data["M_Start"] ?? date("Y-m-d")." ".date("H:i:s"),
                "M_Type" => $request_data["M_Type"],
                "M_League" => $request_data["M_League"],
                "M_League_tw" => $request_data["M_League_tw"],
                "M_League_en" => $request_data["M_League_en"],
                "ShowTypeR" => $request_data["ShowTypeR"] ?? "",
                "ShowTypeHR" => $request_data["ShowTypeHR"] ?? "",
                "MB_Win_Rate" => $request_data["MB_Win_Rate"] ?? 0,
                "TG_Win_Rate" => $request_data["TG_Win_Rate"] ?? 0,
                "M_Flat_Rate" => $request_data["M_Flat_Rate"] ?? "",
                "MB_Win_Rate_H" => $request_data["MB_Win_Rate_H"] ?? 0,
                "TG_Win_Rate_H" => $request_data["TG_Win_Rate_H"] ?? 0,
                "M_Flat_Rate_H" => $request_data["M_Flat_Rate_H"] ?? "",
                "M_LetB" => $request_data["M_LetB"] ?? "",
                "MB_LetB_Rate" => $request_data["MB_LetB_Rate"] ?? 0,
                "TG_LetB_Rate" => $request_data["TG_LetB_Rate"] ?? 0,
                "M_LetB_H" => $request_data["M_LetB_H"] ?? "",
                "MB_LetB_Rate_H" => $request_data["MB_LetB_Rate_H"] ?? 0,
                "TG_LetB_Rate_H" => $request_data["TG_LetB_Rate_H"] ?? 0,
                "MB_Dime" => $request_data["MB_Dime"] ?? "",
                "TG_Dime" => $request_data["TG_Dime"] ?? "",
                "MB_Dime_Rate" => $request_data["MB_Dime_Rate"] ?? 0,
                "TG_Dime_Rate" => $request_data["TG_Dime_Rate"] ?? 0,
                "MB_Dime_H" => $request_data["MB_Dime_H"] ?? "",
                "TG_Dime_H" => $request_data["TG_Dime_H"] ?? "",
                "MB_Dime_Rate_H" => $request_data["MB_Dime_Rate_H"] ?? 0,
                "TG_Dime_Rate_H" => $request_data["TG_Dime_Rate_H"] ?? 0,
                "S_Show" => $request_data["S_Show"] ?? 0,
            ];
            $sport = Sport::where("MID", $request_data['MID'])->first();

            if (isset($sport)) {
                Sport::where("MID", $request_data['MID'])->update($new_data);
                $response['message'] = 'Match Sport Corner Data updated successfully!';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } else {
                $sport = new Sport;
                $sport->create($new_data);
                $response['message'] = 'Match Sport Corner Data added successfully!';
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
