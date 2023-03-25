<?php

namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sport;
use Validator;

class ResultController extends Controller
{
    

    public function getResultFT(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'date' => 'required|string',
                'active' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $active = $request_data["active"];
            $date = $request_data["date"];

            switch($active) {
                case 0:
                    $sports = Sport::where("Type", "FT")
                    ->where("M_Date", $date)
                    ->where("RB_Show", 1)
                    ->get(['Type', 'MB_Team', 'TG_Team', 'M_Start', 'M_Date', 'M_Time', 'M_League', 'MB_Inball_HR', 'TG_Inball_HR', 'MB_Ball', 'TG_Ball']);
                    break;
                case 1:
                    $sports = Sport::where("Type", "FT")
                    ->where("M_Date", $date)
                    ->where("MB_Inball", "!=", "-")
                    ->where("MB_Inball", "!=", "")
                    ->get(['Type', 'MB_Team', 'TG_Team', 'M_Start', 'M_Date', 'M_Time', 'M_League', 'MB_Inball', 'TG_Inball', 'MB_Inball_HR', 'TG_Inball_HR']);
                    break;
                case 2:
                    $sports = [];
                    break;
            }

            

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

        $validator = Validator::make($request->all(),[
            'date' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }
        
        $date = $request->post('date');    
               
        $items = Sport::selectRaw('Type,MB_Team,TG_Team,M_Start,M_Date,M_Time,M_League,MB_Inball,TG_Inball,MB_Inball_HR,TG_Inball_HR')->whereRaw("Type='FT' and M_Date='". $date."' and Score=1 and  Locate('-',MB_Team)>0 order by M_League desc")->get();
        $count = count($items);
        return response()->json([
            "success" => true,
            "count" => $count,
            "data" => $items
        ], 200);    
    }
}



    