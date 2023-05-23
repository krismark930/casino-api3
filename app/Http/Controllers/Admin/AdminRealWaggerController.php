<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Web\MatchSports;
use App\Models\Web\Report;
use App\Models\Web\System;
use App\Utils\Utils;
use Auth;

class AdminRealWaggerController extends Controller {

  public function getLeagueList(Request $request) {
    $ltype = $request['ltype'];
    $gtype = $request['gtype'];
    $ptype = $request['ptype'];

    $m_date = date('Y-m-d');
    switch ($gtype){
      case "FT":
          $datetimeOp="=";
        break;
      case "FU":
          $datetimeOp=">";
        break;	
      case "BK":
          $datetimeOp="=";
        break;
      case "BU":
          $datetimeOp=">";
        break;		
      case "BS":
          $datetimeOp="=";
        break;		
      case "BE":
          $datetimeOp=">";
        break;		
      case "TN":
          $datetimeOp="=";
        break;
      case "TU":
          $datetimeOp=">";
        break;
      case "VB":
          $datetimeOp="=";
        break;		
      case "VU":
          $datetimeOp=">";
        break;		
      case "OP":
          $datetimeOp="=";
        break;
      case "OM":
          $datetimeOp=">";
        break;
    }

    if ($ptype=='PL'){
      $show = "S_Show";
    }else{
      $show = $ptype."_Show";
    }

    if ($ptype=='RB' or $ptype=='PL'){
      $startOp='<';
    }else{
      $startOp='>';
    }

    try{
      $rows = MatchSports::where('Type', $gtype)
        ->where('M_Start', $startOp, date('Y-m-d H:i:s'))
        ->where('M_Date', $datetimeOp, $m_date)
        ->where($show, '1')
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

  public function getResultTableData(Request $request) {
    $gtype = $request['gtype'];
    $flag  = $request['flag'];

    if ($flag=='Y'){
      $bdate=date('Y-m-d',time()-24*60*60);
      $date=date('m-d',time()-24*60*60);
    }else if($flag==''){
      $bdate=date('Y-m-d');
      $date=date('m-d');
    }

    $rows = MatchSports::where('Type', $gtype)
      ->where('M_Date', $bdate)
      ->where('MB_Inball', '!=', '')
      ->orderBy('M_Start')
      ->orderBy('MB_MID')->get();
    
    $data = array();
    foreach($rows as $row) {
      $mb_inball=$row['MB_Inball'];
      $tg_inball=$row['TG_Inball'];
      $mb_inball_1st=$row['MB_Inball_HR'];
      $tg_inball_1st=$row['TG_Inball_HR'];
      $mb_inball_2nd=intval($row['MB_Inball'])-intval($row['MB_Inball_HR']);
      $tg_inball_2nd=intval($row['TG_Inball'])-intval($row['TG_Inball_HR']);
      if ($mb_inball=='-1' and $mb_inball_1st=='-1'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score1;
        $tg_inball_1st=$Score1;
        $tg_inball_2nd=$Score1;	
      }else if ($mb_inball=='-2' and $mb_inball_1st=='-2'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score2;
        $tg_inball_1st=$Score2;
        $tg_inball_2nd=$Score2;	
      }else if ($mb_inball=='-3' and $mb_inball_1st=='-3'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score3;
        $tg_inball_1st=$Score3;
        $tg_inball_2nd=$Score3;	
      }else if ($mb_inball=='-4' and $mb_inball_1st=='-4'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score4;
        $tg_inball_1st=$Score4;
        $tg_inball_2nd=$Score4;	
      }else if ($mb_inball=='-5' and $mb_inball_1st=='-5'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score5;
        $tg_inball_1st=$Score5;
        $tg_inball_2nd=$Score5;	
      }else if ($mb_inball=='-6' and $mb_inball_1st=='-6'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score6;
        $tg_inball_1st=$Score6;
        $tg_inball_2nd=$Score6;	
      }else if ($mb_inball=='-7' and $mb_inball_1st=='-7'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score7;
        $tg_inball_1st=$Score7;
        $tg_inball_2nd=$Score7;	
      }else if ($mb_inball=='-8' and $mb_inball_1st=='-8'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score8;
        $tg_inball_1st=$Score8;
        $tg_inball_2nd=$Score8;	
      }else if ($mb_inball=='-9' and $mb_inball_1st=='-9'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score9;
        $tg_inball_1st=$Score9;
        $tg_inball_2nd=$Score9;	
      }else if ($mb_inball=='-10' and $mb_inball_1st=='-10'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score10;
        $tg_inball_1st=$Score10;
        $tg_inball_2nd=$Score10;	
      }else if ($mb_inball=='-11' and $mb_inball_1st=='-11'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score11;
        $tg_inball_1st=$Score11;
        $tg_inball_2nd=$Score11;	
      }else if ($mb_inball=='-12' and $mb_inball_1st=='-12'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score12;
        $tg_inball_1st=$Score12;
        $tg_inball_2nd=$Score12;	
      }else if ($mb_inball=='-13' and $mb_inball_1st=='-13'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score13;
        $tg_inball_1st=$Score13;
        $tg_inball_2nd=$Score13;	
      }else if ($mb_inball=='-14' and $mb_inball_1st=='-14'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score14;
        $tg_inball_1st=$Score14;
        $tg_inball_2nd=$Score14;	
      }else if ($mb_inball=='-15' and $mb_inball_1st=='-15'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score15;
        $tg_inball_1st=$Score15;
        $tg_inball_2nd=$Score15;	
      }else if ($mb_inball=='-16' and $mb_inball_1st=='-16'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score16;
        $tg_inball_1st=$Score16;
        $tg_inball_2nd=$Score16;	
      }else if ($mb_inball=='-17' and $mb_inball_1st=='-17'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score17;
        $tg_inball_1st=$Score17;
        $tg_inball_2nd=$Score17;	
      }else if ($mb_inball=='-18' and $mb_inball_1st=='-18'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score18;
        $tg_inball_1st=$Score18;
        $tg_inball_2nd=$Score18;	
      }else if ($mb_inball=='-19' and $mb_inball_1st=='-19'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score19;
        $tg_inball_1st=$Score19;
        $tg_inball_2nd=$Score19;	
      }else if ($mb_inball=='-20' and $mb_inball_1st=='-20'){
        $mb_inball='';
        $mb_inball_1st='';
        $mb_inball_2nd='';
        $tg_inball=$Score20;
        $tg_inball_1st=$Score20;
        $tg_inball_2nd=$Score20;	
      }
      array_push($data, array(
        'time' => $date.'<br/>'.$row['M_Time'],
        'sessions' => $row['MB_MID'].'<br/>'.$row['TG_MID'],
        'team' => $row['MB_Team'].'<br/>'.$row['TG_Team'],
        'topHalf' => '<font color="#cc0000"><b><span style="overflow: hidden;">'.$mb_inball_1st.'</span><br/><span style="overflow: hidden;">'.$tg_inball_1st.'</span></b></font>',
        'finishTheRace' => '<font color="#cc0000"><b><span style="overflow: hidden;">'.$mb_inball.'</span><br/><span style="overflow: hidden;">'.$tg_inball.'</span></b></font>',
      ));
    }
    return $data;
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
        $change_rate=number_format($c_rate-$t_rate, 3);
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
    $page = $request['page'];
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = $request['gtype'];
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
      $totalCount = $rows->count();
      $rows = $rows->orderBy('M_Start')
        ->orderBy('M_League')
        ->orderBy('MB_MID')
        ->offset($page * 20 - 20)->limit(20)->get();

      $responseData = array();
      foreach($rows as $row) {
        $MB_Win_Rate=Utils::num_rate('', $row["MB_Win_Rate"]);
        $TG_Win_Rate=Utils::num_rate('', $row["TG_Win_Rate"]);
        $M_Flat_Rate=Utils::num_rate('', $row["M_Flat_Rate"]);
        $MB_LetB_Rate=$this->change_rate($open,$row['MB_LetB_Rate']);
        $TG_LetB_Rate=$this->change_rate($open,$row['TG_LetB_Rate']);
        $MB_Dime_Rate=$this->change_rate($open,$row["MB_Dime_Rate"]);
        $TG_Dime_Rate=$this->change_rate($open,$row["TG_Dime_Rate"]);
        $S_Single_Rate=Utils::num_rate('', $row['S_Single_Rate']);
        $S_Double_Rate=Utils::num_rate('', $row['S_Double_Rate']);
		
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
        array_push($responseData, array(
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
      return array(
        'data' => $responseData,
        'totalCount' => $totalCount,
      );
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function getHTableData(Request $request) {
    $page = $request['page'];
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = $request['gtype'];
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
      $totalCount = $rows->count();
      $rows = $rows->orderBy('M_Start')
        ->orderBy('M_League')
        ->orderBy('MID')
        ->offset($page * 20 - 20)->limit(20)->get();

      $responseData = array();
      foreach($rows as $row) {
        $MB_LetB_Rate_H=$this->change_rate($open,$row['MB_LetB_Rate_H']);
        $TG_LetB_Rate_H=$this->change_rate($open,$row['TG_LetB_Rate_H']);
        $MB_Dime_Rate_H=$this->change_rate($open,$row["MB_Dime_Rate_H"]);
        $TG_Dime_Rate_H=$this->change_rate($open,$row["TG_Dime_Rate_H"]);
        $MB_Win_Rate_H=Utils::num_rate('', $row["MB_Win_Rate_H"]);
        $TG_Win_Rate_H=Utils::num_rate('', $row["TG_Win_Rate_H"]);
        $M_Flat_Rate_H=Utils::num_rate('', $row["M_Flat_Rate_H"]);
		
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
        array_push($responseData, array(
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
      return array(
        'data' => $responseData,
        'totalCount' => $totalCount,
      );
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function getRBTableData(Request $request) {
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = $request['gtype'];
    $ptype = 'RB';
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
        ->get();

      $responseData = array();
      foreach($rows as $row) {
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
        $h9c=0;
        $h9s=0;
        $c9c=0;
        $c9s=0;		
        $c10c=0;
        $c10s=0;
        $h10c=0;
        $h10s=0;
        $n21c=0;
        $n21s=0;
        $h21c=0;
        $h21s=0;
        $c21c=0;
        $c21s=0;
        
        $c19c=0;
        $c19s=0;
        $h19c=0;
        $h19s=0;
        $c20c=0;
        $c20s=0;
        $h20c=0;
        $h20s=0;
        $n31c=0;
        $n31s=0;
        $h31c=0;
        $h31s=0;
        $c31c=0;
        $c31s=0;

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
        $temp = array(
          'mid' => $row['MID'],
          'dateTime' => $date.'<br/>'.$row['M_Time'].$running,
          'alliance' => '<br/>'.$row['M_League'],
          'sessions' => $row['MB_MID'].'<br/>'.$row['TG_MID'],
          'team' => $row['MB_Team'].'&nbsp;&nbsp;'.$row['MB_Ball'].'<br/>'.$row['TG_Team'].'&nbsp;&nbsp;'.$row['TG_Ball'].'&nbsp;&nbsp;<div style="align: right; color: #009900;">和局</div>',
          'winBet' => '<div style="display: flex;"><div style="flex-grow: 1; text-align: left;"> </div><font color="#cc0000">'.$h21c.'/'.$h21s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;"> </div><font color="#cc0000">'.$c21c.'/'.$c21s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;"> </div><font color="#cc0000">'.$n21c.'/'.$n21s.'</font></div>',
          'fullTimeHandicap' => '<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$ratio_h.'&nbsp;'.$ioratio_h.'</div><font style="flex-grow: 1; text-align: right;" color="#cc0000; ">'.$h9c.'/'.$h9s.'</font></div>'
            .'<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$ratio_c.'&nbsp;'.$ioratio_c.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c9c.'/'.$c9s.'</font></div>',
          'fullSizeBetSlip' => '<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">&nbsp;'.$OU_ior[1].'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c10c.'/'.$c10s.'</font></div>'
            .'<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;"></div><font style="flex-grow: 1; text-align: right;" color="red">'.$h10c.'/'.$h10s.'</font></div>',
          'firstHalfWin' => '<div style="display: flex;"><div style="flex-grow: 1; text-align: left;"></div><font style="flex-grow: 1; text-align: right;" color="red">'.$h31c.'/'.$h31s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">&nbsp;</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c31c.'/'.$c31s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;">&nbsp;</div><font style="flex-grow: 1; text-align: right;" color="red">'.$n31c.'/'.$n31s.'</font></div>',
        );
        //開始寫入賠率
				if ('' == "H") {	//強隊是主隊
					$ratio_h = $row['M_LetB'];
					$ratio_c = "&nbsp";
					$ioratio_h = $HR_ior[0];
					$ioratio_c = $HR_ior[1];
				} else {	//強隊是客隊
					$ratio_h = "&nbsp";
					$ratio_c = '';
					$ioratio_h = $HR_ior[0];
					$ioratio_c = $HR_ior[1];
				}
        $temp['firstHalfHandicap'] = '<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$ratio_h.'&nbsp;'.$ioratio_h.'</div><font style="flex-grow: 1; text-align: right;" color="#cc0000; ">'.$h19c.'/'.$h19s.'</font></div>'
            .'<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">'.$ratio_c.'&nbsp;'.$ioratio_c.'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c19c.'/'.$c19s.'</font></div>';
        $temp['upperHalfSize'] = '<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;">&nbsp;'.$OU_ior[1].'</div><font style="flex-grow: 1; text-align: right;" color="red">'.$c20c.'/'.$c20s.'</font></div>'
          .'<div style="display: inline-grid; grid-template-columns: 2fr 1fr; width: 100%"><div style="text-align: right;"></div><font style="flex-grow: 1; text-align: right;" color="red">'.$h20c.'/'.$h20s.'</font></div>';
        array_push($responseData, $temp);
      }
      return $responseData;
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function getPDTableData(Request $request) {
    $page = $request['page'];
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = $request['gtype'];
    $ptype = 'PD';
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
        ->where('PD_Show', '1');
      if($league_id) {
        $rows = $rows->where('M_League', $league_id);
      }
      $totalCount = $rows->count();
      $rows = $rows->orderBy('M_Start')
        ->orderBy('MID')
        ->offset($page * 20 - 20)->limit(20)->get();

      $responseData = array();
      foreach($rows as $row) {
        $mid=$row['MID'];
        $res_data = Report::whereRaw("FIND_IN_SET($mid, MID) > 0")
          ->where('LineType', '4')
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
          ->get();
        $n4c=0;
        $n4s=0;
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
          $n4c+=$i;
          $n4s+=$betscore+0;
        }
        if ($row['PD_Show']==1){
          $show='Y';
        }else{
          $show='N';
        }
        if ($row['M_Type']==1){
          $running='<br><font color=red></font>';
        }else{
          $running='';
        }
        $date=date('m-d',strtotime($row['M_Date']));
        array_push($responseData, array(
          'mid' => $row['MID'],
          'dateTime' => $date.'<br/>'.$row['M_Time'].$running,
          'alliance' => $row['M_League'],
          'hostGuestTeam' => $row['MB_Team'].'<br/>'.$row['TG_Team'],
          'cholesterol' => '<font color="#cc0000">'.$n4c.'/'.$n4s.'</font>',
        ));
      }
      return array(
        'data' => $responseData,
        'totalCount' => $totalCount,
      );
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function getHPDTableData(Request $request) {
    $page = $request['page'];
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = $request['gtype'];
    $ptype = 'HPD';
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
        ->where('HPD_Show', '1');
      if($league_id) {
        $rows = $rows->where('M_League', $league_id);
      }
      $totalCount = $rows->count();
      $rows = $rows->orderBy('M_Start')
        ->orderBy('MID')
        ->offset($page * 20 - 20)->limit(20)->get();

      $responseData = array();
      foreach($rows as $row) {
        $mid=$row['MID'];
        $res_data = Report::whereRaw("FIND_IN_SET($mid, MID) > 0")
          ->where('LineType', '14')
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
          ->get();
        $n14c=0;
        $n14s=0;
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
          $n14c+=$i;
          $n14s+=$betscore+0;
        }
        if ($row['HPD_Show']==1){
          $show='Y';
        }else{
          $show='N';
        }
        $date=date('m-d',strtotime($row['M_Date']));
        $Res_Half='上半';
        array_push($responseData, array(
          'mid' => $row['MID'],
          'dateTime' => $date.'<br/>'.$row['M_Time'],
          'alliance' => $row['M_League'],
          'hostGuestTeam' => $row['MB_Team'].'<font color="gray"> - ['.$Res_Half.']</font><br/>'.$row['TG_Team'].'<font color="gray"> - ['.$Res_Half.']</font>',
          'cholesterol' => '<font color="#cc0000">'.$n14c.'/'.$n14s.'</font>',
        ));
      }
      return array(
        'data' => $responseData,
        'totalCount' => $totalCount,
      );
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function getTTableData(Request $request) {
    $page = $request['page'];
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = $request['gtype'];
    $ptype = 'T';
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
        ->where('T_Show', '1');
      if($league_id) {
        $rows = $rows->where('M_League', $league_id);
      }
      $totalCount = $rows->count();
      $rows = $rows->orderBy('M_Start')
        ->orderBy('MID')
        ->get();

      $responseData = array();
      foreach($rows as $row) {
        $S_Single_Rate=Utils::num_rate('', $row['S_Single_Rate']);
        $S_Double_Rate=Utils::num_rate('', $row['S_Double_Rate']);
		
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
          ->where('LineType', 6)
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
        $h51c=0;
        $h51s=0;
        $h52c=0;
        $h52s=0;
        $h53c=0;
        $h53s=0;
        $h54c=0;
        $h54s=0;
        $h55c=0;
        $h55s=0;
        $h56c=0;
        $h56s=0;
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
          if ($data["Mtype"]=='0~1'){
            $h53c+=$i;
            $h53s+=$betscore+0;
          } else if($data["Mtype"]=='2~3'){
            $h54c+=$i;
            $h54s+=$betscore+0;
          } else if($data["Mtype"]=='4~6'){
            $h55c+=$i;
            $h55s+=$betscore+0;
          } else if($data["Mtype"]=='OVER'){
            $h56c+=$i;
            $h56s+=$betscore+0;
          }
        }
        if ($row['T_Show']==1){
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

        if($league_id != "" && $league_id != $row['M_League']) {
          continue;
        }

        array_push($responseData, array(
          'mid' => $row['MID'],
          'dateTime' => $date.'<br/>'.$row['M_Time'].$running,
          'alliance' => '<br/>'.$row['M_League'],
          'team' => $row['MB_Team'].'<br/>'.$row['TG_Team'],
          'range_0_1' => '<div>'.$row['S_0_1'].'<br/><font color="red">'.$h53c.'/'.$h53s.'</font></div>',
          'range_2_3' => '<div>'.$row['S_2_3'].'<br/><font color="red">'.$h54c.'/'.$h54s.'</font></div>',
          'range_4_6' => '<div>'.$row['S_4_6'].'<br/><font color="red">'.$h55c.'/'.$h55s.'</font></div>',
          'range_7OrMore' => '<div>'.$row['S_7UP'].'<br/><font color="red">'.$h56c.'/'.$h56s.'</font></div>',
        ));
      }
      return array(
        'data' => $responseData,
        'totalCount' => $totalCount,
      );
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function getFTableData(Request $request) {
    $page = $request['page'];
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = $request['gtype'];
    $ptype = 'F';
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
        ->where('PD_Show', '1');
      if($league_id) {
        $rows = $rows->where('M_League', $league_id);
      }
      $totalCount = $rows->count();
      $rows = $rows->orderBy('M_Start')
        ->orderBy('MID')
        ->offset($page * 20 - 20)->limit(20)->get();

      $responseData = array();
      foreach($rows as $row) {
        $mid=$row['MID'];
        $res_data = Report::whereRaw("FIND_IN_SET($mid, MID) > 0")
          ->where('LineType', '4')
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
          ->get();
        $n7c=0;
        $n7s=0;
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
          $n7c+=$i;
          $n7s+=$betscore+0;
        }
        if ($row['F_Show']==1){
          $show='Y';
        }else{
          $show='N';
        }
        if ($row['M_Type']==1){
          $running='<br><font color=red></font>';
        }else{
          $running='';
        }
        $date=date('m-d',strtotime($row['M_Date']));
        array_push($responseData, array(
          'mid' => $row['MID'],
          'dateTime' => $date.'<br/>'.$row['M_Time'].$running,
          'alliance' => $row['M_League'],
          'hostGuestTeam' => $row['MB_Team'].'<br/>'.$row['TG_Team'],
          'halfTime' => '<font color="#cc0000">'.$n7c.'/'.$n7s.'</font>',
        ));
      }
      return array(
        'data' => $responseData,
        'totalCount' => $totalCount,
      );
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function getPTableData(Request $request) {
    $page = $request['page'];
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = $request['gtype'];
    $ptype = 'P';
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
      $totalCount = $rows->count();
      $rows = $rows->orderBy('M_Start')
        ->orderBy('M_League')
        ->orderBy('MB_MID')
        ->get();

      $responseData = array();
      foreach($rows as $row) {
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
        $n8c=0;
        $n8s=0;
        foreach($res_data as $data) {
          $pdata=explode(",",$data['MID']);
          $place=explode(",",$data['Mtype']);
          $cou=count($place);
          for ($i=0;$i<$cou;$i++){
            if ($pdata[$i]==$mid){
              $n8c=$n8c+$data["cou"]+0;
              $n8s=$n8s+$data["BetScore"]+0;
            }
          }
        }
        if ($row['P3_Show']==1){
          $show='Y';
        }else{
          $show='N';
        }
        $date=date('m-d',strtotime($row['M_Date']));
        
        if($league_id != "" && $league_id != $row['M_League']) {
          continue;
        }
        array_push($responseData, array(
          'mid' => $row['MID'],
          'dateTime' => $date.'<br/>'.$row['M_Time'],
          'alliance' => $row['M_League'],
          'sessions' => $row['MB_MID'].'<br/>'.$row['TG_MID'],
          'team' => $row['MB_Team'].'<br/>'.$row['TG_Team'],
          'passLevel' => '<div style="display: flex;"><div style="flex-grow: 1; text-align: left;"></div><font color="red">'.$h1c.'/'.$h1s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;"></div><font color="red">'.$c1c.'/'.$c1s.'</font></div>'
            .'<div style="display: flex;"><div style="flex-grow: 1; text-align: left;"></div><font color="red">'.$n1c.'/'.$n1s.'</font></div>',
          'comprehensivePass' => '<font style="flex-grow: 1; text-align: right;" color="#cc0000">'.$n8c.'/'.$n8s.'</font>',
        ));
      }
      return array(
        'data' => $responseData,
        'totalCount' => $totalCount,
      );
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }

  public function getPLTableData(Request $request) {
    $page = $request['page'];
    $ltype = $request['ltype'];
    $league_id = $request['league_id'];
    $set_account = $request['set_account'];
    $gtype = $request['gtype'];
    $ptype = 'PL';
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
        ->where('M_Date', date('Y-m-d'));
      if($league_id) {
        $rows = $rows->where('M_League', $league_id);
      }
      $totalCount = $rows->count();
      $rows = $rows->orderBy('M_Start')
        ->orderBy('M_League')
        ->orderBy('MID')
        ->offset($page * 20 - 20)->limit(20)->get();
      
      $responseData = array();
      foreach($rows as $row) {
        $MB_LetB_Rate=$this->change_rate($open,$row['MB_LetB_Rate']);
        $TG_LetB_Rate=$this->change_rate($open,$row['TG_LetB_Rate']);
		
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
        $h2c=0;
        $h2s=0;
        $c2c=0;
        $c2s=0;
        
        $h3c=0;
        $h3s=0;
        $c3c=0;
        $c3s=0;
    
        $h9c=0;
        $h9s=0;
        $c9c=0;
        $c9s=0;
        
        $h10c=0;
        $h10s=0;
        $c10c=0;
        $c10s=0;
        
        $h53c=0;
        $h53s=0;
        $c53c=0;
        $c53s=0;
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
            case "2":
              if ($data["Mtype"]=='RH'){
                $h2c+=$i;
                $h2s+=$betscore+0;
              }else if($data["Mtype"]=='RC'){
                $c2c+=$i;
                $c2s+=$betscore+0;
              }			
              break;
            case "3":
              if ($data["Mtype"]=='OUH'){
                $h3c+=$i;
                $h3s+=$betscore+0;
              }else if($data["Mtype"]=='OUC'){
                $c3c+=$i;
                $c3s+=$betscore+0;
              }	
              break;
            case "9":
              if ($data["Mtype"]=='RRH'){
                $h9c+=$i;
                $h9s+=$betscore+0;
              }else if($data["Mtype"]=='RRC'){
                $c9c+=$i;
                $c9s+=$betscore+0;
              }			
              break;
            case "10":
              if ($data["Mtype"]=='ROUH'){
                $h53c+=$i;
                $h53s+=$betscore+0;
              }else if($data["Mtype"]=='ROUC'){
                $c53c+=$i;
                $c53s+=$betscore+0;
              }	
              break;
          }
        }
        if ($row['MB_Inball']==1){
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

        //開始寫入賠率
				if ($row['ShowTypeR'] == "H") {	//強隊是主隊
					$ratio_RH = $row['M_LetB'];
					$ratio_RC = "&nbsp";
				} else {	//強隊是客隊
					$ratio_RH = "&nbsp";
					$ratio_RC = $row['M_LetB'];
				}
        array_push($responseData, array(
          'mid' => $row['MID'],
          'dateTime' => $date.'<br/>'.$row['M_Time'].$running,
          'alliance' => '<br/>'.$row['M_League'],
          'sessions' => $row['MB_MID'].'<br/>'.$row['TG_MID'],
          'team' => $row['MB_Team'].'<br/>'.$row['TG_Team'].'<div style="align: right; color: #009900;">和局</div>',
          'handicap' => '<div style="display: inline-grid; grid-template-columns: 1fr 1fr 2fr;"><div style="text-align: right;">'.$ratio_RH.'</div><div style="text-align: right;">'.$MB_LetB_Rate.'</div><font color="red">'.$h2c.'/'.$h2s.'</font></div>'
            .'<div style="display: inline-grid; grid-template-columns: 1fr 1fr 2fr;"><div style="text-align: right;">'.$ratio_RC.'</div><div style="text-align: right;">'.$TG_LetB_Rate.'</div><font color="red">'.$c2c.'/'.$c2s.'</font></div>',
          'bigSmallPlates' => '<font style="flex-grow: 1; text-align: right;" color="red">'.$h3c.'/'.$h3s.'<br/>'.$c3c.'/'.$c3s.'</font>',
          'rollBall' => '<font style="flex-grow: 1; text-align: right;" color="red">'.$h9c.'/'.$h9s.'<br/>'.$c9c.'/'.$c9s.'</font>',
          'rollBallSize' => '<font style="flex-grow: 1; text-align: right;" color="red">'.$h53c.'/'.$h53s.'<br/>'.$c53c.'/'.$c53s.'</font>',
        ));
      }
      return array(
        'data' => $responseData,
        'totalCount' => $totalCount,
      );
    } catch(Exception $e) {
      return response()->json($e, 500);
    }
  }
}
