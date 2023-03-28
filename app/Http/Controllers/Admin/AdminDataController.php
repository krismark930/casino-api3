<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Web\System;
use Auth;

class AdminDataController extends Controller {
  public function getData(Request $request) {
    $user = Auth::guard("admin")->user();
    try{
      $row = System::where('LoginName', $user['LoginName'])->where('Oid', $user['Oid'])->get()[0];
      return $row;
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function setData(Request $request) {
    $user = Auth::guard("admin")->user();
    $data = $request['data'];
    try {
      $affectedRows = System::where('UserName', $user['UserName'])->update($data);
      return response()->json($affectedRows, 200);
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }
}