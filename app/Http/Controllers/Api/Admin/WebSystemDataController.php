<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebSystemData;

class WebSystemDataController extends Controller
{
    public function getWebSystemData(Request $request) {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        $web_system_data = WebSystemData::all();
        if (isset($web_system_data)) {
            $response['data'] = $web_system_data[0];
            $response['message'] = 'WebSystemData fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } else {
            $response['message'] = 'WebSystemData can not found!';
        }
        return response()->json($response, $response['status']); 
    }

    public function updateWebSystemData(Request $request, $id) {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        $request_data = $request->all();
        $web_system_data = WebSystemData::find($id);
        $web_system_data->Uid = $request_data['uid'];
        $web_system_data->ver = $request_data['version'];
        if ($web_system_data->save()) {
            $response['data'] = $web_system_data;
            $response['message'] = 'WebSystemData updated successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } else {
            $response['message'] = 'WebSystemData can not update!';
        }
        return response()->json($response, $response['status']);
    }
}
