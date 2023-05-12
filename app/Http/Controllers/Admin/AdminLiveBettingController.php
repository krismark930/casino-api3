<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Web\Report;
use App\Models\Web\System;
use App\Utils\Utils;
use App\Models\Sport;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


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
      $page = $request['page'];
      $m_date = $request['m_date'] ?? date('Y-m-d');
      $sort = $request['sort'] ?? 'BetTime';
      $active = $request['match'];
      $m_name = $request['memname'];
    
    $data = array();
    
    $mids = Report::where('M_Date', $m_date);

    if ($active == 1) {
        $mids = $mids->where("Gtype", "FT")->orWhere("Ptype", "FT");
    } else if ($active == 2) {
        $mids = $mids->where("Gtype", "BK")->orWhere("Ptype", "BK");
    }

      $mids = $mids->where(function($query) {
        $query->where('LineType', 9)
        ->orWhere('LineType', 19)
        ->orWhere('LineType', 10)
        ->orWhere('LineType', 5)
        ->orWhere('LineType', 15)
        ->orWhere('LineType', 50)
        ->orWhere('LineType', 51)
        ->orWhere('LineType', 52)
        ->orWhere('LineType', 53)
        ->orWhere('LineType', 54)
        ->orWhere('LineType', 55)
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
      
      $totalCount = $mids->count();
      $mids = $mids->offset($page * 20 - 20)->limit(20)->orderBy($sort, "desc")->get();
      foreach($mids as $row){

        $time = strtotime($row['BetTime']);
        $times = date("Y-m-d", $time).'<br>'.date("H:i:s", $time);        

        if($row['Danger']==1 or $row['Cancel']==1) {
            $bettimes='<font color="#FFFFFF"><span style="background-color: #FF0000">'.$times.'</span></font>';
        }else{
            $bettimes=$times;
        }
        if ($row['Cancel']==1) {
            $betscore='<S><font color=#cc0000>'.number_format($row['BetScore']).'</font></S>';
        } else {
            $betscore=number_format($row['BetScore']);
        }

        if ($row['Cancel'] == 1) {
            $operate = '<font color=red><b>已注销</b></font></a>';
        } else {
            $operate = '<font color=blue><b>正常</b></font>';
        }

        if ($row["Cancel"] == 1) {

            switch($row['Confirmed']) {
                case 0:
                    $M_Result = Score20;
                    break;
                case -1:
                    $M_Result= Score21;
                    break;
                case -2:
                    $M_Result= Score22;
                    break;
                case -3:
                    $M_Result= Score23;
                    break;
                case -4:
                    $M_Result= Score24;
                    break;
                case -5:
                    $M_Result= Score25;
                    break;
                case -6:
                    $M_Result= Score26;
                    break;
                case -7:
                    $M_Result= Score27;
                    break;
                case -8:
                    $M_Result= Score28;
                    break;
                case -9:
                    $M_Result= Score29;
                    break;
                case -10:
                    $M_Result= Score30;
                    break;
                case -11:
                    $M_Result= Score31;
                    break;
                case -12:
                    $M_Result= Score32;
                    break;
                case -13:
                    $M_Result= Score33;
                    break;
                case -14:
                    $M_Result= Score34;
                    break;
                case -15:
                    $M_Result= Score35;
                    break;
                case -16:
                    $M_Result= Score36;
                    break;
                case -17:
                    $M_Result= Score37;
                    break;
                case -18:
                    $M_Result= Score38;
                    break;
                case -19:
                    $M_Result= Score39;
                    break;
                case -20:
                    $M_Result= Score40;
                    break;
                case -21:
                    $M_Result= Score41;
                    break;
            }

            $M_Result = "<font color=red>".$M_Result."</font>";

        } else {

            $M_Result = "<font>".$row["M_Result"]."</font>";
        }

        if ($row["Checked"] == 0) {

            if($row['LineType']==8){
                $state="<font color=red>未结算</font>";
            }elseif($row['LineType']==16){
                $state="<font color=blue>结算注单</font>";
            }else{
                $state="<font color=blue>结算注单</font>";
                $sport = Sport::where("MID", $row["MID"])->first("isSub");
                if($sport['isSub'] ==1 ) $state.='<br><font color=red>副盘</font>';
            }

        } else {                    
            $state = '<font color=red>未结算</font>';
            if($row['TurnRate']==0){
                if($row['isFS']==1){
                    $state="<font color='blue'>已结算<br>已返水</font>";
                }else{
                    $state="<font color='red'>已结算<br>未返水</font>";
                }
            }else{
                $state="<font color='blue'>已结算</font>";
            }
        }
        
        $temp = array(
          'id' => $row->ID,
          'OpenType' => $row['OpenType'],
            'OrderID' => $row['OrderID'],
          'TurnRate' => $row['TurnRate'],
          'betTime' => $bettimes,
          'userName' => $row['M_Name'],
          'gameType' => $row["Gtype"] == "FT" ? "足球".$row['BetType'] : "篮球".$row['BetType'],
          'content' => $row['Middle'],
          'state' => $state,
          'betAmount' => $betscore,
          'winableAmount' => $row['Gwin'],
          'result' => $M_Result,
          'operate' => $operate,
          'function' => 'function',
        );
        array_push($data, $temp);
      }

        $response['data'] = $data;
        $response['totalCount'] = $totalCount;
        $response['message'] = 'Live Betting Data updated successfully';
        $response['success'] = TRUE;
        $response['status'] = STATUS_OK;

    }catch (Exception $e) {
      $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
      // $response['message'] = "ok";
      Log::error($e->getTraceAsString());
      $response['status'] = STATUS_GENERAL_ERROR;
    }

    return response()->json($response, $response['status']);
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
        'danger' => '0',
        'Checked' => '0'
      ]);
      return 'success';
    } catch(Exception $e) {
      return 'failed';
    }
  }
}
