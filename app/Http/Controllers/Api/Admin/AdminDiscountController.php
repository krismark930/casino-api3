<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Web\WebMemLogData;
use App\Utils\Utils;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminDiscountController extends Controller
{

    public function getDiscountData(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "type" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $type = $request_data["type"];

            $result = Discount::where("type", $type)->get();

            foreach($result as $row) {
                $row["image"] = env('APP_URL').Storage::url($row["image"]);
            }

            $login_info = '看到优惠';

            $loginname = $request->user()->UserName;
    
            $ip_addr = Utils::get_ip();
    
            $web_mem_log_data = new WebMemLogData();
    
            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";
    
            $web_mem_log_data->save();

            $response["data"] = $result;
            $response['message'] = "Discount Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function saveDiscountItem(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "type" => "required|numeric",
                "title" => "required|string",
                "content" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $type = $request_data["type"];
            $title = $request_data["title"];
            $content = $request_data["content"];

            $file_name = "";

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $file_name = $file->getClientOriginalName();

                // Move the uploaded file to the desired location
                $file->move(storage_path('app/public/upload/discount'), $file_name);
            }

            $discount = new Discount;
            $discount->title = $title;
            $discount->content = $content;
            $discount->type = $type;
            $discount->image = "upload/discount/" . $file_name;
            $discount->save();

            $login_info = '优惠添加';

            $loginname = $request->user()->UserName;
    
            $ip_addr = Utils::get_ip();
    
            $web_mem_log_data = new WebMemLogData();
    
            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";
    
            $web_mem_log_data->save();

            $response['message'] = "Discount Data saved successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updatedDiscountItem(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric",
                "type" => "required|numeric",
                "title" => "required|string",
                "content" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();
            $id = $request_data["id"];
            $type = $request_data["type"];
            $title = $request_data["title"];
            $content = $request_data["content"];

            $file_name = "";

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $file_name = $file->getClientOriginalName();

                // Move the uploaded file to the desired location
                $file->move(storage_path('app/public/upload/discount'), $file_name);
            }

            $discount = Discount::find($id);
            $discount->title = $title;
            $discount->content = $content;
            $discount->type = $type;
            if ($file_name != "") {
                $discount->image = "upload/discount/" . $file_name;                
            }

            $discount->save();

            $login_info = '优惠更新';

            $loginname = $request->user()->UserName;
    
            $ip_addr = Utils::get_ip();
    
            $web_mem_log_data = new WebMemLogData();
    
            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";
    
            $web_mem_log_data->save();

            $response['message'] = "Discount Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteDiscountItem(Request $request)
    {

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

            $request_data = $request->all();
            $id = $request_data["id"];

            Discount::where("id", $id)->delete();

            $login_info = '优惠删除';

            $loginname = $request->user()->UserName;
    
            $ip_addr = Utils::get_ip();
    
            $web_mem_log_data = new WebMemLogData();
    
            $web_mem_log_data->UserName = $loginname;
            $web_mem_log_data->LoginTime = now();
            $web_mem_log_data->Context = $login_info;
            $web_mem_log_data->LoginIP = $ip_addr;
            $web_mem_log_data->Url = Utils::get_browser_ip();
            $web_mem_log_data->Level = "管理员";
    
            $web_mem_log_data->save();

            $response['message'] = "Discount Data deleted successfully!";
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
