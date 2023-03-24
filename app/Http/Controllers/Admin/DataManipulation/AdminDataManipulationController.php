<?php

namespace App\Http\Controllers\Admin\DataManipulation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Web\MatchSports;
use App\Models\Web\Report;

class AdminDataManipulationController extends Controller
{
  public function scheduledata_getItems(Request $request) {
    $gtype = $request['gtype'] ?? 'FT';
    $date_start = $request['date_start'] ?? date('Y-m-d');
    $league = $request['league'];

    $rows = MatchSports::where('Type', $gtype)
      ->where('M_Date', $date_start)
      ->where('MB_Inball', '');
    if($league) {
      $rows = $rows->where('M_League', $league);
    }

    $rows = $rows->limit(20)
      ->orderBy('M_Start')
      ->orderBy('M_League')
      ->orderBy('MB_Team')
      ->get();

    $data = array();
    foreach($rows as $row) {
      array_push($data, array(
        'datetime' => $row['M_Date'],
        'sessions' => $row['MID'],
        'team' => $row['MB_Team'].'<br/>'.$row['TG_Team'],
        'winner' => '<font color="red" align="right">'.$row['MB_Win_Rate'].'<br/>'.$row['M_Flat_Rate'].'<br/>'.$row['TG_Win_Rate'].'</font>',
        'handicap' => '<font color="red">'.$row['MB_LetB'].'&nbsp;&nbsp;'.$row['MB_LebB_Rate'].'</font>'.'<br/><font color="red">'.$row['TG_LetB'].'&nbsp;&nbsp;'.$row['TG_LetB_Rate'],
        'fullFieldGoal' => $row['MB_Dime'].'&nbsp;&nbsp;<font color="red">'.$row['MB_Dime_Rate'].'</font><br/>'.$row['TG_Dime'].'&nbsp;&nbsp;<font color="red">'.$row['TG_Dime_Rate'].'</font>',
        'odd' => ($row['S_Single_Rate'] ? '单' : '').'&nbsp;&nbsp;<font color="red">'.$row['S_Single_Rate'].'</font><br/>'.($row['S_Double_Rate'] ? '双' : '').'&nbsp;&nbsp;<font color="red">'.$row['S_Double_Rate'].'</font>',
        'winAtHalfTime' => '<font color="red">'.$row['MB_Win_Rate_H'].'<br/>'.$row['TG_Win_Rate_H'].'<br/>'.$row['M_Flat_Rate_H'].'</font>',
        'halfTimeHandicap' => $row['MB_LetB_H'].'&nbsp;&nbsp;<font color="red">'.$row['MB_LetB_Rate_H'].'</font><br/>'.$row['TG_LetB_H'].'&nbsp;&nbsp;<font color="red">'.$row['TG_LetB_Rate_H'].'</font>',
        'halfCourtSize' => $row['MB_Dime_H'].'&nbsp;&nbsp;<font color="red">'.$row['MB_Dime_Rate_H'].'<br/>'.$row['TG_Dime_H'].'&nbsp;&nbsp;<font color="red">'.$row['TG_Dime_Rate_H'].'</font>',
      ));
    }

    return $data;
  }

  public function scheduledata_getItem(Request $request) {
    $id = $request['id'];
    $gtype = $request['gtype'] ?? 'FT';
    $league = $request['league'];

    try {
      $row = MatchSports::where('MID', $id)->where('Type', $gtype)->get()[0];
  
      $data = array(
        'mDate' => $row['M_Date'],
        'mTime' => $row['M_Time'],
        'mLetb' => $row['M_LetB'],
        'mbLetbRate' => $row['MB_LetB_Rate'],
        'tgLetbRate' => $row['TG_LetB_Rate'],
        'mbDime' => $row['MB_Dime'],
        'tgDime' => $row['TG_Dime'],
        'mbDimeRate' => $row['MB_Dime_Rate'],
        'tgDimeRate' => $row['TG_Dime_Rate'],
        'mLetbH' => $row['M_LetB_H'],
        'mbLetbRateH' => $row['MB_LetB_Rate_H'],
        'tgLetbRateH' => $row['TG_LetB_Rate_H'],
        'mbDimeH' => $row['MB_Dime_H'],
        'tgDimeH' => $row['TG_Dime_H'],
        'mbDimeRateH' => $row['MB_Dime_Rate_H'],
        'tgDimeRateH' => $row['TG_Dime_Rate_H'],
      );
  
      return $data;
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function scheduledata_setItem(Request $request) {
    $id = $request['id'];
    $data = $request['data'];

    try {
      $affectedRows = MatchSports::where('MID', $id)->update(array(
        'M_Date' => $data['mDate'],
        'M_Time' => $data['mTime'],
        'M_LetB' => $data['mLetb'],
        'MB_LetB_Rate' => $data['mbLetbRate'],
        'TG_LetB_Rate' => $data['tgLetbRate'],
        'MB_Dime' => $data['mbDime'],
        'TG_Dime' => $data['tgDime'],
        'MB_Dime_Rate' => $data['mbDimeRate'],
        'TG_Dime_Rate' => $data['tgDimeRate'],
        'M_LetB_H' => $data['mLetbH'],
        'MB_LetB_Rate_H' => $data['mbLetbRateH'],
        'TG_LetB_Rate_H' => $data['tgLetbRateH'],
        'MB_Dime_H' => $data['mbDimeH'],
        'TG_Dime_H' => $data['tgDimeH'],
        'MB_Dime_Rate_H' => $data['mbDimeRateH'],
        'TG_Dime_Rate_H' => $data['tgDimeRateH'],
      ));
  
      return response()->json('success', 200);
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function scheduledata_getAllianceTypes(Request $request) {
    $gtype = $request['gtype'] ?? 'FT';
    $date_start = $request['date_start'] ?? date('Y-m-d');
    $rows = MatchSports::where('Type', $gtype)
      ->where('M_Date', $date_start)
      ->where('MB_Inball', '')
      ->select('M_League')
      ->distinct()->get();
    $data = array();
    array_push($data, array(
      'value' => '',
      'label' => '全部',
    ));
    foreach($rows as $row) {
      array_push($data, array(
        'value' => $row['M_League'],
        'label' => $row['M_League'],
      ));
    }
    return $data;
  }

  public function scheduledata_closeBet(Request $request) {
    $id = $request['id'];
    $type = $request['type'];

    try {
      MatchSports::where('MID', $id)
        ->where('Type', $type)
        ->update(array(
          'Open' => '1'
        ));
      return response()->json('success', 200);
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function scheduledata_deleteEvent(Request $request) {
    $id = $request['id'];
    $type = $request['type'];

    try {
      MatchSports::where('MID', $id)
        ->where('Type', $type)
        ->delete();
      return response()->json('success', 200);
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }
}