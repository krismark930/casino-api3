<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Web\Report;
use App\Models\Web\System;
use App\Utils\Utils;
use App\Models\Sport;
use Exception;


class AdminLiveBettingController extends Controller
{
  //
  public function getItems(Request $request)
  {

    $response = [];
    $response['success'] = FALSE;
    $response['status'] = STATUS_BAD_REQUEST;
    try {

      // Web System Data
      System::query()->update([
        'udp_ft_time' => $request['FT'],
        'udp_time_ft' => $request['FT1'],
        'udp_bs_time' => $request['BS'],
        'udp_time_bs' => $request['BS1'],
        'udp_op_time' => $request['OP'],
        'udp_time_op' => $request['OP1']
      ]);

      // Web Report Data
      $m_date = $request['m_date'] ?? date('Y-m-d');
      $sort = $request['sort'] ?? 'BetTime';
      $active = $request['match'];
      $m_name = $request['memname'];
      // $mids = Report::select('MID')->where('M_Date', $m_date)->get();

      $data = array();

      $mids = Report::where('M_Date', $m_date)->where(function($query) {
        $query->where('LineType', 9)
        ->orWhere('LineType', 19)
        ->orWhere('LineType', 10)
        ->orWhere('LineType', 20)
        ->orWhere('LineType', 21)
        ->orWhere('LineType', 31);
      });
      if($sort == 'Cancel') {
        $mids = $mids->where('Cancel', 1);
      }
      if($sort == 'Danger') {
        $mids = $mids->where('Danger', 1);
      }
      if($active) {
        $mids = $mids->where('Active', $active);
      }
      if($m_name) {
        $mids = $mids->where('M_Name', trim($m_name));
      }
      
      $mids = $mids->orderBy($sort, "desc")->get();
      foreach($mids as $row){
        
        
        if($row['Cancel'] == 1){
          $operate = '<font color=red><b>恢复</b></font></a>';
        }else {
          $operate = '<font color=blue><b>正常</b></font>';
        }
        
        // Web Sport Data
        $items = Sport::select('MID')->get();
        
        //  state
        if($row['Active'] == 0){
          $state = '结算';
        }else if($row['Active'] == 1){
          $state = '<font color=red>未结算</font>';
        }
        
        $temp = array(
          'id' => $row->ID,
          'betTime' => $row['BetTime'],
          'userName' => $row['M_Name'],
          'gameType' => $row['BetType'],
          'content' => $row['Middle'],
          'state' => $state,
        'betAmount' => $row['BetScore'],
        'winableAmount' => $row['Gwin'],
        'result' => '0',
        'operate' => $operate,
        'function' => 'function',
      );
      array_push($data, $temp);
    }
    return $data;
    }catch (Exception $e) {
      $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
      // $response['message'] = "ok";
      Log::error($e->getTraceAsString());
      $response['status'] = STATUS_GENERAL_ERROR;
    }
  }

  public function getFunctionItems() {
    $scors = Utils::Scores;
    $scors = array_splice($scors, 20, 23);
    $data = array();

    foreach($scors as $row) {
      $temp = array(
        'label' => $row,
        'value' => $row,
      );
      array_push($data, $temp);
    }
    return $data;
  }

  public function handleCancelEvent(Request $request) {
    $id = $request['id'];
    $confirmed = $request['confirmed'];
    try {
      Report::where('ID', $id)->update([
        'VGOLD' => '0',
        'M_Result' => '0',
        'A_Result' => '0',
        'B_Result' => '0',
        'C_Result' => '0',
        'D_Result' => '0',
        'T_Result' => '0',
        'Cancel' => '1',
        'Confirmed' => $confirmed,
        'danger' => '0'
      ]);
      return 'success';
    } catch(Exception $e) {
      return 'failed';
    }
  }

  public function handleResumeEvent(Request $request) {
    $id = $request['id'];
    try {
      Report::where('ID', $id)->update([
        'VGOLD' => '',
        'M_Result' => '',
        'A_Result' => '',
        'B_Result' => '',
        'C_Result' => '',
        'D_Result' => '',
        'T_Result' => '',
        'Cancel' => '0',
        'Confirmed' => '0',
        'danger' => '0'
      ]);
      return 'success';
    } catch(Exception $e) {
      return 'failed';
    }
  }
}
