<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Sport;

class SportReportController extends Controller
{

    public function getSportReport(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $Rep_readme0='赛事尚有{}场未输入完毕';
            $Rep_readme1='赛事结果已输入完毕';
            $Rep_readme2='没有赛事';

            $mdate_t=date('Y-m-d');
            $mdate_y=date('Y-m-d',time()-24*60*60);

            $cou = Sport::where("Type", "FT")->where("M_Date", $mdate_t)->where("MB_Inball", "!=", "")->count();
            $cou1 = Sport::where("Type", "FT")->where("M_Date", $mdate_t)->count();
            if ($cou1==0){
                $ft_caption=$Rep_readme2;//今日没有比赛
            }else if ($cou1-$cou==0){           
                $ft_caption=$Rep_readme1;//今日输入完毕
            }else{          
                $ft_caption=str_replace('{}',$cou1-$cou,$Rep_readme0);//今日尚有多少场未输入完毕
            }
            $cou2 = Sport::where("Type", "FT")->where("M_Date", $mdate_y)->where("MB_Inball", "!=", "")->count();
            $cou3 = Sport::where("Type", "FT")->where("M_Date", $mdate_y)->count();
            if ($cou3==0){      
                $ft_caption1=$Rep_readme2;//昨日没有比赛
            }else if ($cou3-$cou2==0){      
                $ft_caption1=$Rep_readme1;//昨日输入完毕
            }else{  
                $ft_caption1=str_replace('{}',$cou3-$cou2,$Rep_readme0);//昨日尚有多少场未输入完毕
            }
            
            $cou = Sport::where("Type", "BK")->where("M_Date", $mdate_t)->where("MB_Inball", "!=", "")->count();
            $cou1 = Sport::where("Type", "BK")->where("M_Date", $mdate_t)->count();
            if ($cou1==0){
                $bk_caption=$Rep_readme2;//今日没有比赛
            }else if ($cou1-$cou==0){           
                $bk_caption=$Rep_readme1;//今日输入完毕
            }else{          
                $bk_caption=str_replace('{}',$cou1-$cou,$Rep_readme0);//今日尚有多少场未输入完毕
            }
            $cou2 = Sport::where("Type", "BK")->where("M_Date", $mdate_y)->where("MB_Inball", "!=", "")->count();
            $cou3 = Sport::where("Type", "BK")->where("M_Date", $mdate_y)->count();
            if ($cou3==0){      
                $bk_caption1=$Rep_readme2;//昨日没有比赛
            }else if ($cou3-$cou2==0){      
                $bk_caption1=$Rep_readme1;//昨日输入完毕
            }else{  
                $bk_caption1=str_replace('{}',$cou3-$cou2,$Rep_readme0);//昨日尚有多少场未输入完毕
            }

            $data = array("ft_caption" => $ft_caption, "ft_caption1" => $ft_caption1, "bk_caption" => $bk_caption, "bk_caption1" => $bk_caption1);

            $response["data"] = $data;
            $response['message'] = 'Sport Report Data fetched successfully';
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