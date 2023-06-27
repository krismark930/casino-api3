<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\WebSystemData;
use App\Models\User;
use App\Models\Web\Sys800;
use App\Utils\Utils;
use App\Models\Web\MoneyLog;
use App\Models\WebPaymentData;
use App\Models\Web\Bank;
use App\Models\Web\SysConfig;

class AdminBankController extends Controller
{

    public function getWebBankData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "lv" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $lv = $request_data["lv"];

            $role = "";

            switch ($lv){
                case 'M':
                    $role='Admin';
                    break;  
                case 'A':
                    $role='Super';
                    break;
                case 'B':
                    $role='Corprator';
                    break;
                case 'C':
                    $role='World';
                    break;
                case 'D':
                    $role='Agents';
                    break;
            }

            $name = "admin";

            $user_count = User::where($role, $name)->where("Pay_Type", 1)->count();

            if ($user_count == 0) {
                $response["message"] = "目前还没有会员，请添加后再操作!!";
                return response()->json($response, $response['status']);
            }

            $sysConfig = SysConfig::all()->first();

            $SysConfig["tjck"] = $sysConfig["tjck"] == 1 ? true : false;

            $result = Bank::orderBy("sort", "asc")->orderBy("ID", "asc")->get();

            $response["data"] = $result;
            $response["usdt_rate"] = $sysConfig;
            $response['message'] = "Web Bank Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addWebBankData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "bankname" => "required|string",
                "bankno" => "required|string",
                "bankaddress" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ID = $request_data["ID"] ?? "";
            $bankname = $request_data["bankname"];
            $alias = $request_data["alias"] ?? "";
            $bankno = $request_data["bankno"];
            $bankaddress = $request_data["bankaddress"];
            $vip = $request_data["vip"] ?? "";
            $min_amount = $request_data["min_amount"] ?? "";
            $max_amount = $request_data["max_amount"] ?? "";

            $new_data = array(
                "bankname" => $bankname,
                "alias" => $alias,
                "bankno" => $bankno,
                "bankaddress" => $bankaddress,
                "vip" => $vip,
                "min_amount" => $min_amount,
                "max_amount" => $max_amount,
            );

            if ($ID == "") {
                $bank = new Bank;
                $bank->create($new_data);
            } else {
                Bank::where("ID", $ID)
                    ->update($new_data);
            }
            
            $response['message'] = "Web Bank Data saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function useWebBankData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "ID" => "required|numeric",
                "open" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ID = $request_data["ID"];
            $open = $request_data["open"];

            Bank::where("ID", $ID)->update(["open" => $open]);
            
            $response['message'] = "Web Bank Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteWebBankData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "ID" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $ID = $request_data["ID"];

            Bank::where("ID", $ID)->delete();
            
            $response['message'] = "Web Bank Data deleted successfully!";
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

