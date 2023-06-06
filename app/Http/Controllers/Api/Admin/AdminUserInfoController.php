<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\User;
use App\Models\WebContactInfo;
use Carbon\Carbon;

class AdminUserInfoController extends Controller
{
    public function getUserInfo(Request $request) {

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

            $result = User::query();

            if ($user_name != "") {
                $result = $result->where("UserName", "like", "%$user_name%")
                    ->orWhere("Phone", "like", "%$user_name%");
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                    ->take($limit)->get();

            foreach($result as $row) {                
                $row['Phone']='138****9999';
                $row['PassWord']="********";
            }

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response['message'] = "User Info Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateUserInfo(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $id = $request_data["id"];
            $Notes=$request_data['Notes'] ?? "";
            $Phone=$request_data['Phone'] ?? "";
            $Alias=$request_data['Alias'] ?? "";

            $result = User::where("id", $id)->update([
                "Notes" => $Notes,
                "Phone" => $Phone,
                "Alias" => $Alias
            ]);

            $response['message'] = "User Info Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteUserInfo(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $user = $request->user();
            $request_data = $request->all();
            $id = $request_data["id"];

            User::where("id", $id)->delete();

            $response['message'] = "User Info Data deleted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getContactInfo(Request $request) {

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

            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $result = WebContactInfo::query();
            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                    ->take($limit)->get();

            foreach($result as $row) {
                $row["time"] = date("Y-m-d H:i:s");
            }

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response['message'] = "Contact Info Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteContactInfo(Request $request) {

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

            WebContactInfo::where("ID", $ID)->delete();

            $response['message'] = "Contact Info Data deleted successfully!";
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
