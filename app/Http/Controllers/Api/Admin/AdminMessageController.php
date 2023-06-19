<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\WebMessageData;
use Carbon\Carbon;

class AdminMessageController extends Controller
{
    public function getWebMessageData(Request $request) {

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

            $user = $request->user();
            $request_data = $request->all();

            $user_name = $request_data["user_name"] ?? "";
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $result = WebMessageData::query();

            if ($user_name != "") {
                $result = $result->where("UserName", $user_name);
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                    ->orderBy("ID", "desc")
                    ->take($limit)->get();

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response['message'] = "Web Message Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addWebMessageData(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "Message" => "required|string",
                "Subject" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $Message=$request_data['Message'];
            $Subject=$request_data['Subject'];
            $UserName=$request_data['UserName'] ?? "";

            // if ($UserName != "") {
                $new_data = array(
                    "UserName" => $UserName,
                    "Subject" => $Subject,
                    "Message" => $Message,
                    "Date" => Carbon::now("Asia/Hong_Kong")->format("Y-m-d"),
                    "Time" => Carbon::now("Asia/Hong_Kong")->format("Y-m-d H:i:s"),
                );
                $data = new WebMessageData;
                $data->create($new_data);
            // } else {
            //     $result = WebMessageData::all();
            //     foreach($result as $item) {
            //         $new_data = array(
            //             "UserName" => $item["UserName"],
            //             "Subject" => $Subject,
            //             "Message" => $Message,
            //             "Date" => Carbon::now("Asia/Hong_Kong")->format("Y-m-d"),
            //             "Time" => Carbon::now("Asia/Hong_Kong")->format("Y-m-d H:i:s"),
            //         );
            //         $data = new WebMessageData;
            //         $data->create($new_data);                    
            //     }
            // }

            $response['message'] = "Web Message Data added successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteWebMessageData(Request $request) {

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

            $user = $request->user();
            $request_data = $request->all();
            $ID = $request_data["ID"];

            WebMessageData::where("ID", $ID)->delete();

            $response['message'] = "Web Message Data deleted successfully!";
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
