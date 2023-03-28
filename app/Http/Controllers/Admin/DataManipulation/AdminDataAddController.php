<?php

namespace App\Http\Controllers\Admin\DataManipulation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Web\MatchSports;

class AdminDataAddController extends Controller {
  public function addItem(Request $request) {
    $mid = $request['mid'];
    $gtype = $request['gtype'] ?? "FT";
    $date = $request['date'];
    $time = $request['time'];
    $start = $request['start'];
    $mbTeam = $request['mb_team'];
    $tgTeam = $request['tg_team'];
    $mbTeamTw = $request['mb_team_tw'];
    $tgTeamTw = $request['tg_team_tw'];
    $mbTeamEn = $request['mb_team_en'];
    $tgTeamEn = $request['tg_team_en'];
    $mLeague = $request['m_league'];
    $mLeagueTw = $request['m_league_tw'];
    $mLeagueEn = $request['m_league_en'];
    try {
        
      MatchSports::create([
          'MID' => $mid,
          'Type' => $gtype,
          'M_Date' => $date,
          'M_Time' => $time,
          'M_Start' => $start,
          'MB_Team' => $mbTeam,
          'TG_Team' => $tgTeam,
          'MB_Team_tw' => $mbTeamTw,
          'TG_Team_tw' => $tgTeamTw,
          'MB_Team_en' => $mbTeamEn,
          'TG_Team_en' => $tgTeamEn,
          'M_League' => $mLeague,
          'M_League_tw' => $mLeagueTw,
          'M_League_en' => $mLeagueEn
      ]);
      return response()->json('success', 200);
    } catch (Exception $e) {
      return response()->json($e, 500);
    }
  }
}