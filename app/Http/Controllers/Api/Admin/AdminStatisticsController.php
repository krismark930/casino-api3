<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebAgent;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminStatisticsController extends Controller
{
    public function getDevidendDetails(Request $request) {

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

            $id = $request_data["id"] ?? "";
            $start_time = $request_data["start_time"] ?? "";
            $end_time = $request_data["end_time"] ?? "";

            $web_agent = WebAgent::where("level", "D");

            if ($id != "") {
                $web_agent = $web_agent->where("id", $id);
            }

            $web_agent = $web_agent->first();

            $AG_UserName=$web_agent['UserName'];  //代理商账号
            $zc=intval($web_agent['D_Point']);   //占成比率

            //获取充值会员列表
            $UserName=$this->getUsers("select distinct(UserName) FROM web_sys800_data where AddDate>='$start_time' and AddDate<='$end_time'",$AG_UserName);  
            $sql="select sum(gold) from web_sys800_data where UserName in ($UserName) and  FIND_IN_SET(`Bank_Account`,'彩金,体验金,返水,返利,银行返利,银行卡返利') and AddDate>='".$start_time."' and AddDate<='".$end_time."' and Type='S' and Cancel=0";

            $result = DB::select($sql);
            $row = $result[0];
            $caijin=empty($row[0]) ? 0 : $row[0];

            $response['message'] = "Devidend Details Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    private function getUsers($sql, $Agents='ddm999') {
        $result = DB::select($sql)[0];
        $Users="";
        $Users = $Users.$result[0];
        $Users = trim($Users,",");
        unset($result);
        $sql="SELECT UserName  FROM `web_member_data` where Agents='$Agents' and UserName in(".$Users.") order by UserName";
        $result = DB::select($sql);
        $Users='';
        foreach($result as $item) {
            $item = get_object_vars($item);
            $Users=$Users."'".$item['UserName']."',";
        }
        $Users = trim($Users,",");
        return $Users;
    }
}