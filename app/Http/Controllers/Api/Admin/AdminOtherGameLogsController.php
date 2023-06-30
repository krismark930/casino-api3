<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Web\AGLogs;
use App\Models\Web\BBINLogs;
use App\Models\Web\MGLogs;
use App\Models\Web\PTLogs;
use App\Models\Web\OGLogs;
use App\Models\Web\KYLogs;
use App\Utils\Utils;

class AdminOtherGameLogsController extends Controller
{

    public function getAGLogs(Request $request) {

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

            $user_name = $request_data["user_name"];
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $result = AGLogs::query();

            if ($user_name != "") {
                $result = $result->where("UserName", "like", "%$user_name%");
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                    ->take($limit)->orderBy("id", "desc")->get();

            foreach($result as $row) {
                $row["time"] = date("Y-m-d H:i:s");
                $row["AG_User"] = Utils::GetField($row["UserName"], "AG_User");
                $row["Alias"] = Utils::GetField($row["UserName"], "Alias");
            }

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response['message'] = "AG Logs Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getBBINLogs(Request $request) {

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

            $user_name = $request_data["user_name"];
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $result = BBINLogs::query();

            if ($user_name != "") {
                $result = $result->where("UserName", "like", "%$user_name%");
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                    ->take($limit)->orderBy("id", "desc")->get();

            foreach($result as $row) {
                $row["time"] = date("Y-m-d H:i:s");
                $row["BBIN_User"] = Utils::GetField($row["UserName"], "BBIN_User");
                $row["Alias"] = Utils::GetField($row["UserName"], "Alias");
            }

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response['message'] = "BBIN Logs Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getMGLogs(Request $request) {

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

            $user_name = $request_data["user_name"];
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $result = MGLogs::query();

            if ($user_name != "") {
                $result = $result->where("UserName", "like", "%$user_name%");
                $row["MG_User"] = Utils::GetField($row["UserName"], "MG_User");
                $row["Alias"] = Utils::GetField($row["UserName"], "Alias");
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                    ->take($limit)->orderBy("id", "desc")->get();

            foreach($result as $row) {
                $row["time"] = date("Y-m-d H:i:s");
            }

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response['message'] = "MG Logs Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getPTLogs(Request $request) {

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

            $user_name = $request_data["user_name"];
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $result = PTLogs::query();

            if ($user_name != "") {
                $result = $result->where("UserName", "like", "%$user_name%");
                $row["PT_User"] = Utils::GetField($row["UserName"], "PT_User");
                $row["Alias"] = Utils::GetField($row["UserName"], "Alias");
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                    ->take($limit)->orderBy("id", "desc")->get();

            foreach($result as $row) {
                $row["time"] = date("Y-m-d H:i:s");
            }

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response['message'] = "PT Logs Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getOGLogs(Request $request) {

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

            $user_name = $request_data["user_name"];
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $result = OGLogs::query();

            if ($user_name != "") {
                $result = $result->where("UserName", "like", "%$user_name%");
                $row["OG_User"] = Utils::GetField($row["UserName"], "OG_User");
                $row["Alias"] = Utils::GetField($row["UserName"], "Alias");
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                    ->take($limit)->orderBy("id", "desc")->get();

            foreach($result as $row) {
                $row["time"] = date("Y-m-d H:i:s");
            }

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response['message'] = "OG Logs Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getKYLogs(Request $request) {

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

            $user_name = $request_data["user_name"];
            $page_no = $request_data["page_no"] ?? 1;
            $limit = $request_data["limit"] ?? 20;

            $result = KYLogs::query();

            if ($user_name != "") {
                $result = $result->where("UserName", "like", "%$user_name%");
                $row["KY_User"] = Utils::GetField($row["UserName"], "KY_User");
                $row["Alias"] = Utils::GetField($row["UserName"], "Alias");
            }

            $total_count = $result->count();

            $result = $result->offset(($page_no - 1) * $limit)
                    ->take($limit)->orderBy("id", "desc")->get();

            foreach($result as $row) {
                $row["time"] = date("Y-m-d H:i:s");
            }

            $response["total_count"] = $total_count;
            $response["data"] = $result;
            $response['message'] = "KY Logs Data fetched successfully!";
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
