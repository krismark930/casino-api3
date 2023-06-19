<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebMessageData;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function getSystemSMS(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $type = $request_data["type"] ?? "";

            $user = $request->user();

            if ($type != "all") {

                $result = WebMessageData::where("UserName", $user["UserName"])->where("isDel", 0)->orderBy("Date", "desc")->get();

            } else {

                $result = WebMessageData::where("UserName", "")->where("isDel", 0)->orderBy("Date", "desc")->get();

            }

            $response["data"] = $result;
            $response['message'] = 'System SMS Data fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSystemSMSItemByID(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $ID = $request_data["ID"];

            WebMessageData::where("ID", $ID)->update([
                "isRead" => 1,
            ]);

            $result = WebMessageData::where("ID", $ID)->first();

            $response["data"] = $result;
            $response['message'] = 'System SMS Data fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteSystemSMSItemByID(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $ID = $request_data["ID"];

            // WebMessageData::where("ID", $ID)->update([
            //     "isDel" => 1,
            // ]);

            WebMessageData::where("ID", $ID)->delete();

            $response['message'] = 'System SMS Data deleted successfully!';
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
