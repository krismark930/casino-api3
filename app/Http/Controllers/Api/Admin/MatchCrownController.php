<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MatchCrown;

class MatchCrownController extends Controller
{
    public function getMatchCrownDataByMID(Request $request, $MID, $Gid) {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        $match_crown_data = MatchCrown::where("MID", $MID)->where("Gid", $Gid)->first();
        if (isset($match_crown_data)) {
            $response['data'] = $match_crown_data;
            $response['message'] = 'Match Crown Data fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } else {
            $response['message'] = 'Match Crown Data can not found!';
            $response['status'] = STATUS_OK;
        }
        return response()->json($response, $response['status']);
    }

    public function addMatchCrownData(Request $request) {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        $request_data = $request->all();        
        $match_crown = MatchCrown::where("MID", $request_data['MID'])->where("Gid", $request_data['Gid'])->first();
        if (!isset($match_crown)) {
            $match_crown = new MatchCrown();
        } else {
            $match_crown = MatchCrown::find($match_crown['id']);
        }
        $match_crown->MID = $request_data['MID'];
        $match_crown->uptime = $request_data['uptime'];
        $match_crown->M_Start = $request_data['M_Start'];
        $match_crown->MB_Team_tw = $request_data['MB_Team_tw'];
        $match_crown->M_League_tw = $request_data['M_League_tw'];
        $match_crown->M_Item_tw = $request_data['M_Item_tw'];
        $match_crown->MB_Team = $request_data['MB_Team'];
        $match_crown->M_League = $request_data['M_League'];
        $match_crown->M_Item = $request_data['M_Item'];
        $match_crown->MB_Team_en = $request_data['MB_Team_en'];
        $match_crown->M_League_en = $request_data['M_League_en'];
        $match_crown->M_Item_en = $request_data['M_Item_en'];
        $match_crown->M_Area = $request_data['M_Area'];
        $match_crown->M_Rate = $request_data['M_Rate'];
        $match_crown->Gid = $request_data['Gid'];
        $match_crown->mcount = $request_data['mcount'];
        $match_crown->Gtype = $request_data['Gtype'];
        $match_crown->mshow = $request_data['mshow'];
        $match_crown->mshow2 = $request_data['mshow2'];
        if ($match_crown->save()) {
            $response['data'] = $match_crown;
            if (isset($match_crown)) {
                $response['message'] = 'Match Crown Data updated successfully!';
            } else {
                $response['message'] = 'Match Crown Data saved successfully!';
            }
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } else {
            $response['message'] = 'Match Crown Data cannot save!';
            $response['status'] = STATUS_OK;
        }
        return response()->json($response, $response['status']);
    }

    public function updateMatchCrownDataByMID(Request $request, $MID, $Gid) {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        $request_data = $request->all();
        $match_crown = MatchCrown::where("MID", $MID)->where("Gid", $Gid)->first();
        $match_crown->uptime = $request_data['uptime'];
        $match_crown->M_Start = $request_data['M_Start'];
        $match_crown->MB_Team_tw = $request_data['MB_Team_tw'];
        $match_crown->M_League_tw = $request_data['M_League_tw'];
        $match_crown->M_Item_tw = $request_data['M_Item_tw'];
        $match_crown->MB_Team = $request_data['MB_Team'];
        $match_crown->M_League = $request_data['M_League'];
        $match_crown->M_Item = $request_data['M_Item'];
        $match_crown->MB_Team_en = $request_data['MB_Team_en'];
        $match_crown->M_League_en = $request_data['M_League_en'];
        $match_crown->M_Item_en = $request_data['M_Item_en'];
        $match_crown->M_Area = $request_data['M_Area'];
        $match_crown->M_Rate = $request_data['M_Rate'];
        $match_crown->mcount = $request_data['mcount'];
        $match_crown->Gtype = $request_data['Gtype'];
        $match_crown->mshow = $request_data['mshow'];
        $match_crown->mshow2 = $request_data['mshow2'];
        if ($match_crown->save()) {
            $response['data'] = $match_crown;
            $response['message'] = 'Match Crown Data updated successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } else {
            $response['message'] = 'Match Crown Data cannot update!';
            $response['status'] = STATUS_OK;
        }
        return response()->json($response, $response['status']);
    }
}
