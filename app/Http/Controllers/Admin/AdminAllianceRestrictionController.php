<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Web\MatchLeague;

class AdminAllianceRestrictionController extends Controller
{
  public function getItems(Request $request) {
    $page = $request['page'];
    $type = $request['type'] ?? 'FT';
    $league = $request['league'];
    try {
      $rows = MatchLeague::where('Type', $type);
      if($league) {
        $rows = $rows->where('M_League', 'like', '%'.$league.'%');
      }
      $totalCount = $rows->count();
      $rows = $rows->orderBy('M_League', 'desc')
        ->offset($page * 20 - 20)->limit(20)->get();

      $data = array();
      foreach($rows as $row) {
        array_push($data, array(
          'id' => $row['ID'],
          'mLeague' => $row['M_League'],
          'R' => $row['R'],
          'OU' => $row['OU'],
          'VR' => $row['VR'],
          'VOU' => $row['VOU'],
          'RB' => $row['RB'],
          'ROU' => $row['ROU'],
          'VRB' => $row['VRB'],
          'VROU' => $row['VROU'],
          'CS' => $row['CS'],
        ));
      }
      return array(
        'data' => $data,
        'totalCount' => $totalCount,
      );
    } catch(Exception $e) {
      return $e;
    }
  }

  public function getItem(Request $request) {
    $id = $request['id'];
    $rows = MatchLeague::where('id', $id)->get()[0];
    return $rows;
  }

  public function setItem(Request $request) {
    $data = $request['data'];
    try {
      $affectRows = MatchLeague::where('ID', $data['ID'])->update([
        'R' => $data['R'],
        'RB' => $data['RB'],
        'M' => $data['M'],
        'EO' => $data['EO'],
        'OU' => $data['OU'],
        'ROU' => $data['ROU'],
        'VM' => $data['VM'],
        'PD' => $data['PD'],
        'VR' => $data['VR'],
        'VRB' => $data['VRB'],
        'RM' => $data['RM'],
        'T' => $data['T'],
        'VOU' => $data['VOU'],
        'VROU' => $data['VROU'],
        'VRM' => $data['VRM'],
        'F' => $data['F'],
      ]);
      return response()->json('success', 200);
    } catch(Exception $e) {
      return response()->json('failed', 500);
    }
  }

  public function handleDeleteEvent(Request $request){
    $id = $request['id'];
    try {
      MatchLeague::where('id', $id)->delete();
    } catch (Exception $e) {
      return 'fail';
    }
  }
}