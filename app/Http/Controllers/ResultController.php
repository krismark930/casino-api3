<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Sport;
use Validator;
class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        
    }
    public function index(Request $request)
    {
        //
        $limit = $request->query('limit');
        if ($limit == '' || $limit == null) $limit = 10;
        return Sport::paginate($limit);
    }
    public function match_count()
    {
        return Sport::count();
    }

    public function getResultFt(Request $request)
    {

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



    