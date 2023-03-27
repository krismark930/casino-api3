<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Web\MatchSports;
use App\Models\Web\Report;
use App\Models\Web\System;
use Auth;

class AdminRealWaggerController extends Controller {

  public function getLeagueList(Request $request) {
    $ltype = $request['ltype'];
    $gtype = 'FT';
    $m_date = date('Y-m-d');
    try{
      $rows = MatchSports::where('Type', $gtype)
        ->where('M_Start', '>', date('Y-m-d H:i:s'))
        ->where('M_Date', $m_date)
        ->where('S_Show', '1')
        ->select('M_League')
        ->distinct()->get();
      $data = array();
      foreach($rows as $row) {
        array_push($data, array(
          'value' => $row['M_League'],
          'label' => $row['M_League'],
        ));
      }
      return $data;
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function change_rate($c_type,$c_rate){
    switch($c_type){
    case 'A':
      $t_rate='0.03';
      break;
    case 'B':
      $t_rate='0.01';
      break;
    case 'C':
      $t_rate='0';
      break;
    case 'D':
      $t_rate='-0.01';
      break;
    }
    if ($c_rate!='' && $c_rate!='0'){
        $change_rate=number_format($c_rate-$t_rate,3);
        if ($change_rate<=0 && $change_rate>=-0.03){
          $change_rate='';
        }
    }else{
        $change_rate='';
    }
    return $change_rate;
  }

  public function get_other_ioratio($odd_type, $iorH, $iorC, $showior) {
    $out = array();
    if ($iorH !== "" || $iorC !== "") {
        $out = $this->chg_ior($odd_type, $iorH, $iorC, $showior);
    } else {
        $out[0] = $iorH;
        $out[1] = $iorC;
    }
    return $out;
  }

  public function chg_ior($odd_f, $iorH, $iorC, $showior) {
      $iorpoints = 3;
      $iorH = floatval($iorH);
      $iorC = floatval($iorC);
      $ior = array();
      if ($iorH < 3) $iorH *= 1000;
      if ($iorC < 3) $iorC *= 1000;
      $iorH = floatval($iorH);
      $iorC = floatval($iorC);
      switch ($odd_f) {
          case "H":
              $ior = $this->get_HK_ior($iorH, $iorC);
              break;
          case "M":
              $ior = $this->get_MA_ior($iorH, $iorC);
              break;
          case "I":
              $ior = $this->get_IND_ior($iorH, $iorC);
              break;
          case "E":
              $ior = $this->get_EU_ior($iorH, $iorC);
              break;
          default:
              $ior[0] = $iorH;
              $ior[1] = $iorC;
      }
      $ior[0] /= 1000;
      $ior[1] /= 1000;

      $ior[0] = sprintf($this->Decimal_point($ior[0], $showior), $iorpoints);
      $ior[1] = sprintf($this->Decimal_point($ior[1], $showior), $iorpoints);

      return $ior;
  }

  public function Decimal_point($tmpior, $show) {
      $sign = "";
      $sign = ($tmpior < 0) ? "Y" : "N";
      $tmpior = (floor(abs($tmpior) * $show + 1 / $show)) / $show;
      return ($tmpior * (($sign == "Y") ? -1 : 1));
  }

  public function get_HK_ior($H_ratio, $C_ratio) {
    $out_ior = array();
    $line = 2000 - ($H_ratio + $C_ratio);
    $nowType = "";
    if ($H_ratio <= 1000 && $C_ratio <= 1000) {
        $out_ior[0] = $H_ratio;
        $out_ior[1] = $C_ratio;
        return $out_ior;
    }
    if ($H_ratio > $C_ratio) {
        $lowRatio = $C_ratio;
        $nowType = "C";
    } else {
        $lowRatio = $H_ratio;
        $nowType = "H";
    }
    if (((2000 - $line) - $lowRatio) > 1000) {
        $nowRatio = ($lowRatio + $line) * (-1);
    } else {
        $nowRatio = 2000 - $line - $lowRatio;
    }
    if ($nowRatio < 0) {
        $highRatio = floor(abs(1000 / $nowRatio) * 1000);
    } else {
        $highRatio = 2000 - $line - $nowRatio;
    }
    if ($nowType == "H") {
        $out_ior[0] = $lowRatio;
        $out_ior[1] = $highRatio;
    } else {
        $out_ior[0] = $highRatio;
        $out_ior[1] = $lowRatio;
    }
    return $out_ior;
  }

  public function get_MA_ior($H_ratio, $C_ratio) {
      $out_ior = array();
      $line = 2000 - ($H_ratio + $C_ratio);
      $nowType = "";
      if ($H_ratio <= 1000 && $C_ratio <= 1000) {
          $out_ior[0] = $H_ratio;
          $out_ior[1] = $C_ratio;
          return $out_ior;
      }
      if ($H_ratio > $C_ratio) {
          $lowRatio = $C_ratio;
          $nowType = "C";
      } else {
          $lowRatio = $H_ratio;
          $nowType = "H";
      }
      $highRatio = ($lowRatio + $line) * (-1);
      if ($nowType == "H") {
          $out_ior[0] = $lowRatio;
          $out_ior[1] = $highRatio;
      } else {
          $out_ior[0] = $highRatio;
          $out_ior[1] = $lowRatio;
      }
      return $out_ior;
  }

  public function get_IND_ior($H_ratio, $C_ratio) {
    $out_ior = $this->get_HK_ior($H_ratio, $C_ratio);
    $H_ratio = $out_ior[0];
    $C_ratio = $out_ior[1];
    $H_ratio /= 1000;
    $C_ratio /= 1000;
    if ($H_ratio < 1) {
      $H_ratio = (-1) / $H_ratio;
    }
    if ($C_ratio < 1) {
      $C_ratio = (-1) / $C_ratio;
    }
    $out_ior[0] = $H_ratio * 1000;
    $out_ior[1] = $C_ratio * 1000;
    return $out_ior;
  }
  
  public function get_EU_ior($H_ratio, $C_ratio) {
    $out_ior = $this->get_HK_ior($H_ratio, $C_ratio);
    $H_ratio = $out_ior[0];
    $C_ratio = $out_ior[1];       
    $out_ior[0] = $H_ratio + 1000;
    $out_ior[1] = $C_ratio + 1000;
    return $out_ior;
  }

  public function getSTableData(Request $request) {
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = 'FT';
    $ptype = 'S';
    $user = Auth::guard("admin")->user();
    if ($ltype==''){
      $ltype=3;
      $open='C';
    }else if($ltype==1){
      $open='A';
    }else if($ltype==2){
      $open='B';
    }else if($ltype==3){
      $open='C';
    }else if($ltype==4){
      $open='D';
    }
    try{
      $rows = MatchSports::where('Type', $gtype)
        ->where('M_Start', '>', date('Y-m-d H:i:s'))
        ->where('S_Show', '1');
      if($league_id) {
        $rows = $rows->where('M_League', $league_id);
      }
      $rows = $rows->orderBy('M_Start')
        ->orderBy('M_League')
        ->orderBy('MB_MID')
        ->limit(60)->get();

      $data = array();
      foreach($rows as $row) {
        $MB_Win_Rate=number_format($row["MB_Win_Rate"], 2);
        $TG_Win_Rate=number_format($row["TG_Win_Rate"], 2);
        $M_Flat_Rate=number_format($row["M_Flat_Rate"], 2);
        $MB_LetB_Rate=$this->change_rate($open,$row['MB_LetB_Rate']);
        $TG_LetB_Rate=$this->change_rate($open,$row['TG_LetB_Rate']);
        $MB_Dime_Rate=$this->change_rate($open,$row["MB_Dime_Rate"]);
        $TG_Dime_Rate=$this->change_rate($open,$row["TG_Dime_Rate"]);
        $S_Single_Rate=number_format($row['S_Single_Rate'], 2);
        $S_Double_Rate=number_format($row['S_Double_Rate'], 2);
		
        $Rel_Odd='单';
        $Rel_Even='双';
        if ($S_Double_Rate=='' || $S_Double_Rate==''){
          $Single='';
          $Double='';
        }else{
          $Single=$Rel_Odd;
          $Double=$Rel_Even;
        }
        $mid=$row['MID'];
        $res_data = Report::whereRaw("FIND_IN_SET($mid, MID) > 0")
          ->whereIn('LineType', [1, 2, 3, 5])
          ->where('OpenType', $open);
        switch($user['Level']) {
          case 'M':
            $res_data = $res_data->where('Admin', $user['UserName']);
            break;
          case 'A':
            $res_data = $res_data->where('Super', $user['UserName']);
            break;
          case 'B':
            $res_data = $res_data->where('Corprator', $user['UserName']);
            break;
          case 'C':
            $res_data = $res_data->where('World', $user['UserName']);
            break;
          case 'D':
            $res_data = $res_data->where('Agents', $user['UserName']);
            break;
        }
        $res_data = $res_data->orderBy('LineType')
          ->orderBy('Mtype')
          ->get();
        $n1c=0;
        $n1s=0;
        $h1c=0;
        $h1s=0;
        $c1c=0;
        $c1s=0;
        $c2c=0;
        $c2s=0;
        $h2c=0;
        $h2s=0;
        $c3c=0;
        $c3s=0;
        $h3c=0;
        $h3s=0;
        $c5c=0;
        $c5s=0;
        $h5c=0;
        $h5s=0;
        $i=1;
        foreach($res_data as $data) {
          $level = $user['Level'];
          if ($set_account==1){
            if ($level=='M'){
              $Point=1;//管理员
            }else if ($level=='A'){
              $Point=$data['A_Point']/100;//公司
            }else if ($level=='B'){
              $Point=$data['B_Point']/100;//股东
            }else if ($level=='C'){
              $Point=$data['C_Point']/100;//总代理
            }else if ($level=='D'){
              $Point=$data['D_Point']/100;//代理商
            }
          }else{
            $Point=1;
          }
          $betscore=$data['BetScore']*$Point;
          switch ($data['LineType']){
            case "1":
              if ($data['Mtype']=='MH'){
                $h1c+=$i;
                $h1s+=$betscore+0;
              }else if($data['Mtype']=='MC'){
                $c1c+=$i;
                $c1s+=$betscore+0;
              }else if($data['Mtype']=='MN'){
                $n1c+=$i;
                $n1s+=$betscore+0;
              }
              break;
            case "2":
              if ($data['Mtype']=='RH'){
                $h2c+=$i;
                $h2s+=$betscore+0;
              }else if($data['Mtype']=='RC'){
                $c2c+=$i;
                $c2s+=$betscore+0;
              }			
              break;
            case "3":
              if ($data['Mtype']=='OUC'){
                $h3c+=$i;
                $h3s+=$betscore+0;
              }else if($data['Mtype']=='OUH'){
                $c3c+=$i;
                $c3s+=$betscore+0;
              }	
              break;
            case "5":
              if ($data["Mtype"]=='ODD'){
                $h5c+=$i;
                $h5s+=$betscore+0;
              }else if($data["Mtype"]=='EVEN'){
                $c5c+=$i;
                $c5s+=$betscore+0;
              }	
              break;
          }
        }
        if ($row['S_Show']==1){
          $show='Y';
        }else{
          $show='N';
        }
        if ($row['M_Type']==1){
          $running='<br><font color=red>滚球</font>';
        }else{
          $running='';
        }
        $date=date('m-d',strtotime($row['M_Date']));
        
        $odd_f_type = "H";
        $show_ior = 1000;
        $R_ior  = $this->get_other_ioratio($odd_f_type, $MB_LetB_Rate, $TG_LetB_Rate, $show_ior);
        $OU_ior = $this->get_other_ioratio($odd_f_type, $TG_Dime_Rate, $MB_Dime_Rate, $show_ior);

        //開始寫入賠率
				if ($row['ShowTypeR'] == "H") {	//強隊是主隊
					$ratio_h = $row['M_LetB'];
					$ratio_c = "&nbsp";
					$ioratio_h = $R_ior[0];
					$ioratio_c = $R_ior[1];
				} else {	//強隊是客隊
					$ratio_h = "&nbsp";
					$ratio_c = $row['M_LetB'];
					$ioratio_h = $R_ior[0];
					$ioratio_c = $R_ior[1];
				}
        array_push($data, array(
          'mid' => $row['MID'],
          'dateTime' => $date.'<br/>'.$row['M_Time'].$running,
          'alliance' => '<br/>'.$row['M_League'],
          'sessions' => $row['MB_MID'].'<br/>'.$row['TG_MID'],
          'team' => $row['MB_Team'].'<br/>'.$row['TG_Team'].'<div style="align: right; color: #009900;">和局</div>',
          'winBet' => '<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">'.$MB_Win_Rate.'</div><font color="red">'.$h1c.'/'.$h1s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">'.$TG_Win_Rate.'</div><font color="red">'.$c1c.'/'.$c1s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">'.$M_Flat_Rate.'</div><font color="red">'.$n1c.'/'.$n1s.'</font></div>',
          'handicapBet' => '<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$ratio_h.'&nbsp;'.$ioratio_h.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$h2c.'/'.$h2s.'</font></div>'
            .'<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$ratio_c.'&nbsp;'.$ioratio_c.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c2c.'/'.$c2s.'</font></div>',
          'bigSmallPlateBetSlip' => '<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$row['MB_Dime'].'&nbsp;'.$OU_ior[1].'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c3c.'/'.$c3s.'</font></div>'
            .'<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$OU_ior[0].'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$h3c.'/'.$h3s.'</font></div>',
          'oddEvenBet' => '<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">'.$Single.'&nbsp;'.$S_Single_Rate.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$h5c.'/'.$h5s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">'.$Double.'&nbsp;'.$S_Double_Rate.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c5c.'/'.$c5s.'</font></div>',
        ));
      }
      return $data;
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function getHTableData(Request $request) {
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = 'FT';
    $ptype = 'H';
    $user = Auth::guard("admin")->user();
    if ($ltype==''){
      $ltype=3;
      $open='C';
    }else if($ltype==1){
      $open='A';
    }else if($ltype==2){
      $open='B';
    }else if($ltype==3){
      $open='C';
    }else if($ltype==4){
      $open='D';
    }
    try{
      $rows = MatchSports::where('Type', $gtype)
        ->where('M_Start', '>', date('Y-m-d H:i:s'))
        ->where('H_Show', '1');
      if($league_id) {
        $rows = $rows->where('M_League', $league_id);
      }
      $rows = $rows->orderBy('M_Start')
        ->orderBy('M_League')
        ->orderBy('MID')
        ->limit(60)->get();

      $data = array();
      foreach($rows as $row) {
        $MB_LetB_Rate_H=$this->change_rate($open,$row['MB_LetB_Rate_H']);
        $TG_LetB_Rate_H=$this->change_rate($open,$row['TG_LetB_Rate_H']);
        $MB_Dime_Rate_H=$this->change_rate($open,$row["MB_Dime_Rate_H"]);
        $TG_Dime_Rate_H=$this->change_rate($open,$row["TG_Dime_Rate_H"]);
        $MB_Win_Rate_H=number_format($row["MB_Win_Rate_H"], 2);
        $TG_Win_Rate_H=number_format($row["TG_Win_Rate_H"], 2);
        $M_Flat_Rate_H=number_format($row["M_Flat_Rate_H"], 2);
		
        $Rel_Odd='单';
        $Rel_Even='双';
        $Res_Half='上半';
        $mid=$row['MID'];
        $res_data = Report::whereRaw("FIND_IN_SET($mid, MID) > 0")
          ->whereIn('LineType', [11, 12, 13])
          ->where('OpenType', $open);
        switch($user['Level']) {
          case 'M':
            $res_data = $res_data->where('Admin', $user['UserName']);
            break;
          case 'A':
            $res_data = $res_data->where('Super', $user['UserName']);
            break;
          case 'B':
            $res_data = $res_data->where('Corprator', $user['UserName']);
            break;
          case 'C':
            $res_data = $res_data->where('World', $user['UserName']);
            break;
          case 'D':
            $res_data = $res_data->where('Agents', $user['UserName']);
            break;
        }
        $res_data = $res_data->orderBy('LineType')
          ->orderBy('Mtype')
          ->get();
        $n11c=0;
        $n11s=0;
        $h11c=0;
        $h11s=0;
        $c11c=0;
        $c11s=0;
        $c12c=0;
        $c12s=0;
        $h12c=0;
        $h12s=0;
        $c13c=0;
        $c13s=0;
        $h13c=0;
        $h13s=0;
        $i=1;
        foreach($res_data as $data) {
          $level = $user['Level'];
          if ($set_account==1){
            if ($level=='M'){
              $Point=1;//管理员
            }else if ($level=='A'){
              $Point=$data['A_Point']/100;//公司
            }else if ($level=='B'){
              $Point=$data['B_Point']/100;//股东
            }else if ($level=='C'){
              $Point=$data['C_Point']/100;//总代理
            }else if ($level=='D'){
              $Point=$data['D_Point']/100;//代理商
            }
          }else{
            $Point=1;
          }
          $betscore=$data['BetScore']*$Point;
          switch ($data['LineType']){
            case "11":
              if ($data['Mtype']=='VMH'){
                $h11c+=$i;
                $h11s+=$betscore+0;
              }else if($data['Mtype']=='VMC'){
                $c11c+=$i;
                $c11s+=$betscore+0;
              }else if($data['Mtype']=='VMN'){
                $n11c+=$i;
                $n11s+=$betscore+0;
              }
              break;
            case "12":
              if ($data['Mtype']=='VRH'){
                $h12c+=$i;
                $h12s+=$betscore+0;
              }else if($data['Mtype']=='VRC'){
                $c12c+=$i;
                $c12s+=$betscore+0;
              }			
              break;
            case "13":
              if ($data['Mtype']=='VOUC'){
                $h13c+=$i;
                $h13s+=$betscore+0;
              }else if($data['Mtype']=='VOUH'){
                $c13c+=$i;
                $c13s+=$betscore+0;
              }	
              break;
          }
        }
        if ($row['H_Show']==1){
          $show='Y';
        }else{
          $show='N';
        }
        if ($row['M_Type']==1){
          $running='<br><font color=red>滚球</font>';
        }else{
          $running='';
        }
        $date=date('m-d',strtotime($row['M_Date']));
        
        $odd_f_type = "H";
        $show_ior = 1000;
        $HR_ior  = $this->get_other_ioratio($odd_f_type, $MB_LetB_Rate, $TG_LetB_Rate, $show_ior);
        $HOU_ior = $this->get_other_ioratio($odd_f_type, $TG_Dime_Rate, $MB_Dime_Rate, $show_ior);

        //開始寫入賠率
				if ($row['ShowTypeHR'] == "H") {	//強隊是主隊
					$ratio_h = $row['M_LetB_H'];
					$ratio_c = "&nbsp";
					$ioratio_h = $HR_ior[0];
					$ioratio_c = $HR_ior[1];
				} else {	//強隊是客隊
					$ratio_h = "&nbsp";
					$ratio_c = $row['M_LetB_H'];
					$ioratio_h = $HR_ior[0];
					$ioratio_c = $HR_ior[1];
				}
        array_push($data, array(
          'mid' => $row['MID'],
          'dateTime' => $date.'<br/>'.$row['M_Time'].$running,
          'alliance' => '<br/>'.$row['M_League'],
          'sessions' => $row['MB_MID'].'<br/>'.$row['TG_MID'],
          'team' => $row['MB_Team'].'<font color="gray"> - ['.$Res_Half.']</font><br/>'.$row['TG_Team'].'<font color="gray"> - ['.$Res_Half.']</font><div style="align: right; color: #009900;">和局</div>',
          'winBet' => '<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">'.$MB_Win_Rate_H.'</div><font color="red">'.$h11c.'/'.$h11s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">'.$TG_Win_Rate_H.'</div><font color="red">'.$c11c.'/'.$c11s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">'.$M_Flat_Rate_H.'</div><font color="red">'.$n11c.'/'.$n11s.'</font></div>',
          'handicapBet' => '<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$ratio_h.'&nbsp;'.$ioratio_h.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$h12c.'/'.$h12s.'</font></div>'
            .'<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$ratio_c.'&nbsp;'.$ioratio_c.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c12c.'/'.$c12s.'</font></div>',
          'bigSmallPlateBetSlip' => '<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$row['MB_Dime_H'].'&nbsp;'.$OU_ior[1].'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c13c.'/'.$c13s.'</font></div>'
            .'<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$OU_ior[0].'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$h13c.'/'.$h13s.'</font></div>',
        ));
      }
      return $data;
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function getRBTableData(Request $request) {  
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = 'FT';
    $ptype = 'S';
    $user = Auth::guard("admin")->user();
    if ($ltype==''){
      $ltype=3;
      $open='C';
    }else if($ltype==1){
      $open='A';
    }else if($ltype==2){
      $open='B';
    }else if($ltype==3){
      $open='C';
    }else if($ltype==4){
      $open='D';
    }
    try{
      $rows = MatchSports::where('Type', $gtype)
        ->where('M_Start', '>', date('Y-m-d H:i:s'))
        ->where('S_Show', '1');
      if($league_id) {
        $rows = $rows->where('M_League', $league_id);
      }
      $rows = $rows->orderBy('M_Start')
        ->orderBy('M_League')
        ->orderBy('MB_MID')
        ->limit(60)->get();

      $data = array();
      foreach($rows as $row) {
        $MB_Win_Rate=number_format($row["MB_Win_Rate"], 2);
        $TG_Win_Rate=number_format($row["TG_Win_Rate"], 2);
        $M_Flat_Rate=number_format($row["M_Flat_Rate"], 2);
        $MB_LetB_Rate=$this->change_rate($open,$row['MB_LetB_Rate']);
        $TG_LetB_Rate=$this->change_rate($open,$row['TG_LetB_Rate']);
        $MB_Dime_Rate=$this->change_rate($open,$row["MB_Dime_Rate"]);
        $TG_Dime_Rate=$this->change_rate($open,$row["TG_Dime_Rate"]);
        $S_Single_Rate=number_format($row['S_Single_Rate'], 2);
        $S_Double_Rate=number_format($row['S_Double_Rate'], 2);
		
        $Rel_Odd='单';
        $Rel_Even='双';
        if ($S_Double_Rate=='' || $S_Double_Rate==''){
          $Single='';
          $Double='';
        }else{
          $Single=$Rel_Odd;
          $Double=$Rel_Even;
        }
        $mid=$row['MID'];
        $res_data = Report::whereRaw("FIND_IN_SET($mid, MID) > 0")
          ->whereIn('LineType', [1, 2, 3, 5])
          ->where('OpenType', $open);
        switch($user['Level']) {
          case 'M':
            $res_data = $res_data->where('Admin', $user['UserName']);
            break;
          case 'A':
            $res_data = $res_data->where('Super', $user['UserName']);
            break;
          case 'B':
            $res_data = $res_data->where('Corprator', $user['UserName']);
            break;
          case 'C':
            $res_data = $res_data->where('World', $user['UserName']);
            break;
          case 'D':
            $res_data = $res_data->where('Agents', $user['UserName']);
            break;
        }
        $res_data = $res_data->orderBy('LineType')
          ->orderBy('Mtype')
          ->get();
        $n1c=0;
        $n1s=0;
        $h1c=0;
        $h1s=0;
        $c1c=0;
        $c1s=0;
        $c2c=0;
        $c2s=0;
        $h2c=0;
        $h2s=0;
        $c3c=0;
        $c3s=0;
        $h3c=0;
        $h3s=0;
        $c5c=0;
        $c5s=0;
        $h5c=0;
        $h5s=0;
        $i=1;
        foreach($res_data as $data) {
          $level = $user['Level'];
          if ($set_account==1){
            if ($level=='M'){
              $Point=1;//管理员
            }else if ($level=='A'){
              $Point=$data['A_Point']/100;//公司
            }else if ($level=='B'){
              $Point=$data['B_Point']/100;//股东
            }else if ($level=='C'){
              $Point=$data['C_Point']/100;//总代理
            }else if ($level=='D'){
              $Point=$data['D_Point']/100;//代理商
            }
          }else{
            $Point=1;
          }
          $betscore=$data['BetScore']*$Point;
          switch ($data['LineType']){
            case "1":
              if ($data['Mtype']=='MH'){
                $h1c+=$i;
                $h1s+=$betscore+0;
              }else if($data['Mtype']=='MC'){
                $c1c+=$i;
                $c1s+=$betscore+0;
              }else if($data['Mtype']=='MN'){
                $n1c+=$i;
                $n1s+=$betscore+0;
              }
              break;
            case "2":
              if ($data['Mtype']=='RH'){
                $h2c+=$i;
                $h2s+=$betscore+0;
              }else if($data['Mtype']=='RC'){
                $c2c+=$i;
                $c2s+=$betscore+0;
              }			
              break;
            case "3":
              if ($data['Mtype']=='OUC'){
                $h3c+=$i;
                $h3s+=$betscore+0;
              }else if($data['Mtype']=='OUH'){
                $c3c+=$i;
                $c3s+=$betscore+0;
              }	
              break;
            case "5":
              if ($data["Mtype"]=='ODD'){
                $h5c+=$i;
                $h5s+=$betscore+0;
              }else if($data["Mtype"]=='EVEN'){
                $c5c+=$i;
                $c5s+=$betscore+0;
              }	
              break;
          }
        }
        if ($row['S_Show']==1){
          $show='Y';
        }else{
          $show='N';
        }
        if ($row['M_Type']==1){
          $running='<br><font color=red>滚球</font>';
        }else{
          $running='';
        }
        $date=date('m-d',strtotime($row['M_Date']));
        
        $odd_f_type = "H";
        $show_ior = 1000;
        $R_ior  = $this->get_other_ioratio($odd_f_type, '', '', $show_ior);
        $OU_ior = $this->get_other_ioratio($odd_f_type, '', '', $show_ior);
        $HR_ior  = $this->get_other_ioratio($odd_f_type, '', '', $show_ior);
        $HOU_ior = $this->get_other_ioratio($odd_f_type, '', '', $show_ior);

        $game_wagger = $h9c*1 + $c9c*1 + $h10c*1 + $c10c*1 + $h19c*1 + $c19c*1 + $h20c*1 + $c20c*1 + $h21c*1 + $c21c*1 + $n21c*1 + $h31c*1 + $c31c*1 + $n31c*1;
        if($game_wagger == 0) {
          continue;
        }
        if($league_id != "" && $league_id != $row['M_League']) {
          continue;
        }

        //開始寫入賠率
				if ($row['ShowTypeRB'] == "H") {	//強隊是主隊
					$ratio_h = $row['M_LetB'];
					$ratio_c = "&nbsp";
					$ioratio_h = $R_ior[0];
					$ioratio_c = $R_ior[1];
				} else {	//強隊是客隊
					$ratio_h = "&nbsp";
					$ratio_c = $row['M_LetB'];
					$ioratio_h = $R_ior[0];
					$ioratio_c = $R_ior[1];
				}
        array_push($data, array(
          'mid' => $row['MID'],
          'dateTime' => $date.'<br/>'.$row['M_Time'].$running,
          'alliance' => '<br/>'.$row['M_League'],
          'sessions' => $row['MB_MID'].'<br/>'.$row['TG_MID'],
          'team' => $row['MB_Team'].'&nbsp;&nbsp;'.$row['MB_Ball'].'<br/>'.$row['TG_Team'].'&nbsp;&nbsp;'.$row['TG_Ball'].'&nbsp;&nbsp;<div style="align: right; color: #009900;">和局</div>',
          'winBet' => '<div style="display: flex;"><div style="flex-grow: 1; text-align: left;"> </div><font color="#cc0000">'.$h21c.'/'.$h21s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;"> </div><font color="#cc0000">'.$c21c.'/'.$c21s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;"> </div><font color="#cc0000">'.$n21c.'/'.$n21s.'</font></div>',
          'handicapBet' => '<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$ratio_h.'&nbsp;'.$ioratio_h.'</div><font style="flex-grow: 1; text-align: right;" color="#cc0000; ">'.$h9c.'/'.$h2s.'</font></div>'
            .'<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$ratio_c.'&nbsp;'.$ioratio_c.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c29c.'/'.$c2s.'</font></div>',
          'bigSmallPlateBetSlip' => '<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$row['MB_Dime'].'&nbsp;'.$OU_ior[1].'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c3c.'/'.$c3s.'</font></div>'
            .'<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$OU_ior[0].'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$h3c.'/'.$h3s.'</font></div>',
          'oddEvenBet' => '<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">'.$Single.'&nbsp;'.$S_Single_Rate.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$h5c.'/'.$h5s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">'.$Double.'&nbsp;'.$S_Double_Rate.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c5c.'/'.$c5s.'</font></div>',
        ));
      }
      return $data;
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }
}
