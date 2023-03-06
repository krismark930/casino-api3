<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\User;
use App\Models\Config;
use App\Utils\Utils;
use DB;
use Validator;
include("include.php");
class SportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        
    }
    public function index(Request $request)
    {
        //
        $limit = $request->query('limit');
        if ($limit == '' || $limit == null) $limit = 10;
        return Sport::paginate($limit);
    }
    public function match_count()
    {
        return Sport::count();
    }

    public function get_item_date(Request $request)
    {

        $m_date = $request->post('m_date');
        $type = $request->post('type');
        $get_type = $request->post('get_type');

        if($m_date == '')
        {
            $m_date = '2021-07-11';  //////temp date
            if($get_type == 'count') //////get number of item
            {
                if($type != '')
                {
                    $items = Sport::selectRaw('*')->whereRaw("Type='$type' and `M_Date` >='$m_date'")->count();
                }
                else
                {
                    $items = Sport::selectRaw('MID')->whereRaw("`M_Date` >='$m_date'")->count();
                }

                return $items;
            }

            if($get_type == '')  //////get data
            {
                if($type != '')
                {
                    $items = Sport::selectRaw('*')->whereRaw("Type='$type' and `M_Date` >='$m_date'")->get();
                }
                else
                {
                    $items = Sport::selectRaw('*')->whereRaw("`M_Date` >='$m_date'")->get();
                }

                return $items;
            }
        }
        else
        {
            if($get_type == 'count') //////get number of item
            {
                if($type != '')
                {
                    $items = Sport::selectRaw('*')->whereRaw("Type='$type' and `M_Date` = '$m_date'")->count();
                }
                else
                {
                    $items = Sport::selectRaw('MID')->whereRaw("`M_Date` = '$m_date'")->count();
                }

                return $items;
            }

            if($get_type == '')  //////get data
            {
                
                if($type != '')
                {
                    $items = Sport::selectRaw('*')->whereRaw("Type='$type' and `M_Date` = '$m_date'")->get();
                }
                else
                {
                    $items = Sport::selectRaw('*')->whereRaw("`M_Date` = '$m_date'")->get();
                }

                return $items;
            }
        }

    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sport  $sport
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //
        $sport = Sport::findOrFail($id);
        return $sport;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sport  $sport
     * @return \Illuminate\Http\Response
     */
    public function edit(Sport $sport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sport  $sport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sport  $sport
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sport $sport)
    {
        //
    }

    public function betFt(Request $request) {
        //todo: should come from index.php
        $FT_Order = ""; //temp variable
        $mb_ball = 0;
        $tg_ball = 0;
        $gnum = 0;
        $strong = "";
        $ioradio_r_h = 1;
        $odd_f_type = "E";
        $rtype = "r";
        $langx = "zh-cn"; //come from session

        //todo:change language
        $Order_1st_Half = '上半';
        $Order_2nd_Half = '下半';
        $Order_1_x_2_betting_order = '单式独赢交易单';
        $Order_Handicap_betting_order = '单式让球交易单';
        $Order_Over_Under_betting_order = '单式大小交易单';
        $Order_1st_Half_1_x_2_betting_order = '上半场独赢交易单';
        $Order_1st_Half_Handicap_betting_order = '上半场让球交易单';
        $Order_1st_Half_Over_Under_betting_order = '上半场大小交易单';
        $Order_1st_Half_Correct_Score_betting_order = '上半波胆交易单';
        $validator = Validator::make($request->all(),[
            'gold' => 'required|Integer',
        ]);
        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }
        $Order_Other_Score = '其它比分';
        $id = $request->post('id');
        $gold = $request->post('gold');
        $gid = $request->post('gid');
        $line = $request->post('line_type');
        $type=$request->post('type');
        $active= $request->post('active');	//not used but final sql
        $member = User::selectRaw('*')->whereRaw("id='".$id."' and Status=0")->get()[0];
        $sport = Sport::selectRaw('*')->whereRaw("`M_Start`>now() and `MID`='".$gid."' and Cancel!=1 and Open=1 and MB_Team!='' and MB_Team_tw!='' and MB_Team_en!=''")->get();
        
        $config = Config::selectRaw('HG_Confirm,BadMember,kf4 as BadMember2,kf3 as BadMember3,BadMember_JQ as BadMember_JQ')->get();
        $badname_jq=explode(",",$config[0]['BadMember_JQ']);
       
        $gtype="R";
        if($line=='1') $gtype='M';   //独赢
        if($line=='2') $gtype='R';   //让球
        if($line=='3') $gtype='OU';  //大小
        if($line=='4') $gtype='PD';  //波胆
        if($line=='5') $gtype='R';  //单双
        if($line=='6') $gtype='T';  //总入球
        if($line=='7') $gtype='F';   //半全场
        if($line=='11') $gtype='M';  //半场独赢
        if($line=='12') $gtype='R';  //半场让球
        if($line=='13') $gtype='OU';  //半场大小
        if($line=='14') $gtype='PD';  //半场波胆
        
        $open = $member['OpenType'];
        $pay_type =$member['Pay_Type'];
        
        $memname =$member['UserName']; 
        
        $agents=$member['Agents'];
        $world=$member['World'];
        $corprator=$member['Corprator'];
        $super=$member['Super'];
        $admin=$member['Admin'];
        $w_ratio=$member['ratio'];
        $w_current=$member['CurType'];
       
        $btset=Utils::singleset($gtype);
        
        if($btset[0]>0) $XianEr=$btset[0];
        if(count($sport) == 0 ){
            //TOdo: Eror Handling, no sport
            return response()->json([
                'success' => false,
                'message' => "Not found"
            ], 404);
        }
        
        $w_tg_team=$sport[0]['TG_Team'];
        $w_tg_team_tw=$sport[0]['TG_Team_tw'];
        $w_tg_team_en=$sport[0]['TG_Team_en'];
       
        //取出四种语言的主队名称,并去掉其中的“主”和“中”字样
        $w_mb_team=Utils::filiter_team(trim($sport[0]['MB_Team']));
        $w_mb_team_tw=Utils::filiter_team(trim($sport[0]['MB_Team_tw']));
        $w_mb_team_en=Utils::filiter_team(trim($sport[0]['MB_Team_en']));
        $w_mb_mid=$sport[0]['MB_MID'];
        $w_tg_mid=$sport[0]['TG_MID'];
        
        
        if(strpos($w_tg_team,'角球') or strpos($w_mb_team,'角球') or strpos($w_tg_team,'点球') or strpos($w_mb_team,'点球')){  //屏蔽角球、点球投注
            if(in_array($memname,$badname_jq)){
                return response()->json([
                    'success' => false,
                    'message' => "赛程已关闭,无法进行交易!!"
                ], 404);
            }
        }
       
        $s_mb_team=Utils::filiter_team($sport[0]['MB_Team']);
	    $s_tg_team=Utils::filiter_team($sport[0]['TG_team']);
        if ($gold<=0){
            return response()->json([
                'success' => false,
                'message' => "非法参数！"
            ], 404);
        }
       
        if($gold<$XianEr){
            return response()->json([
                'success' => false,
                'message' => "最低投注額是RMB"
            ], 404);
        }
        
        
        //下注时间
        $m_date=$sport[0]["M_Date"];
        $showtype=$sport[0]["ShowTypeR"];
        if($line=='12' or $line=='13' or $line=='14'){  //获取半场
            $showtype=$sport[0]["ShowTypeHR"];
        }
        
        $bettime=date('Y-m-d H:i:s');
        $m_start=strtotime($sport[0]['M_Start']);
        $datetime=time();
        
        if ($m_start-$datetime<120){
            return response()->json([
                'success' => false,
                'message' => "赛程已关闭,无法进行交易!!"
            ], 404);
        }
        
        $s_sleague=$sport[0]["M_League"];
        switch($line){
        case 1:
            $bet_type='独赢';
            $bet_type_tw='獨贏';
            
            $bet_type_en="1x2";
            $caption=$FT_Order.$Order_1_x_2_betting_order;
            $turn_rate="FT_Turn_M";
            $turn="FT_Turn_M";
           
            switch ($type){
            case "H":
                $w_m_place=$w_mb_team;
                $w_m_place_tw=$w_mb_team_tw;
                $w_m_place_en=$w_mb_team_en;
                $s_m_place=$s_mb_team;
                $w_m_rate=Utils::num_rate($open,$sport[0]["MB_Win_Rate"]);
                $mtype='MH';
                break;
            case "C":
                $w_m_place=$w_tg_team;
                $w_m_place_tw=$w_tg_team_tw;
                $w_m_place_en=$w_tg_team_en;
                $s_m_place=$s_tg_team;
                $w_m_rate=Utils::num_rate($open,$sport[0]["TG_Win_Rate"]);
                $mtype='MC';
                break;
            case "N":
                $w_m_place="和局";
                $w_m_place_tw="和局";
                $w_m_place_en="Flat";
                $s_m_place=$Draw;
                $w_m_rate=Utils::num_rate($open,$sport[0]["M_Flat_Rate"]);
                $mtype='MN';
                break;
            }
            $Sign="VS.";
            $grape="";
            $gwin=($w_m_rate-1)*$gold;
            $ptype='M';
            break;
        case 2:
            $bet_type='让球';
            $bet_type_tw="讓球";
            $bet_type_en="Handicap";	
            $caption=$FT_Order.$Order_Handicap_betting_order;
            $turn_rate="FT_Turn_R_".$open;
            $rate=Utils::get_other_ioratio($odd_f_type,$sport[0]["MB_LetB_Rate"],$sport[0]["TG_LetB_Rate"],100);
            switch ($type){
            case "H":
                $w_m_place=$w_mb_team;
                $w_m_place_tw=$w_mb_team_tw;
                $w_m_place_en=$w_mb_team_en;
                $s_m_place=$s_mb_team;
                $w_m_rate=Utils::change_rate($open,$rate[0]);
                $turn_url="/app/member/FT_order/FT_order_r.php?gid=".$gid."&uid=".$id."&type=".$type."&gnum=".$gnum."&strong=".$strong."&odd_f_type=".$odd_f_type;
                $mtype='RH';
                break;
            case "C":
                $w_m_place=$w_tg_team;
                $w_m_place_tw=$w_tg_team_tw;
                $w_m_place_en=$w_tg_team_en;
                $s_m_place=$s_tg_team;
                $w_m_rate=Utils::change_rate($open,$rate[1]);
                $turn_url="/app/member/FT_order/FT_order_r.php?gid=".$gid."&uid=".$id."&type=".$type."&gnum=".$gnum."&strong=".$strong."&odd_f_type=".$odd_f_type;
                $mtype='RC';
                break;
            }
            $Sign=$sport[0]['M_LetB'];
            $grape=$Sign;
            if ($showtype=="H"){
                $l_team=$s_mb_team;
                $r_team=$s_tg_team;
                $w_l_team=$w_mb_team;
                $w_l_team_tw=$w_mb_team_tw;
                $w_l_team_en=$w_mb_team_en;
                $w_r_team=$w_tg_team;
                $w_r_team_tw=$w_tg_team_tw;
                $w_r_team_en=$w_tg_team_en;
            }else{
                $r_team=$s_mb_team;
                $l_team=$s_tg_team;
                $w_r_team=$w_mb_team;
                $w_r_team_tw=$w_mb_team_tw;
                $w_r_team_en=$w_mb_team_en;
                $w_l_team=$w_tg_team;
                $w_l_team_tw=$w_tg_team_tw;
                $w_l_team_en=$w_tg_team_en;
            }
            $s_mb_team=$l_team;
            $s_tg_team=$r_team;
            $w_mb_team=$w_l_team;
            $w_mb_team_tw=$w_l_team_tw;
            $w_mb_team_en=$w_l_team_en;
            $w_tg_team=$w_r_team;
            $w_tg_team_tw=$w_r_team_tw;
            $w_tg_team_en=$w_r_team_en;
            
            $turn="FT_Turn_R";
            if ($odd_f_type=='H'){
                $gwin=($w_m_rate)*$gold;
            }else if ($odd_f_type=='M' or $odd_f_type=='I'){
                if ($w_m_rate<0){
                    $gwin=$gold;
                }else{
                    $gwin=($w_m_rate)*$gold;
                }
            }else if ($odd_f_type=='E'){
                $gwin=($w_m_rate-1)*$gold;
            }
            $ptype='R';
        break;
        case 3:
            $bet_type='大小';
            $bet_type_tw="大小";
            $bet_type_en="Over/Under";
            $caption=$FT_Order.$Order_Over_Under_betting_order;
            $turn_rate="FT_Turn_OU_".$open;
            $rate=Utils::get_other_ioratio($odd_f_type,$sport[0]["MB_Dime_Rate"],$sport[0]["TG_Dime_Rate"],100);
            switch ($type){
            case "C":
                $w_m_place=$sport[0]["MB_Dime"];
                $w_m_place=str_replace('O','大&nbsp;',$w_m_place);
                $w_m_place_tw=$sport[0]["MB_Dime"];
                $w_m_place_tw=str_replace('O','大&nbsp;',$w_m_place_tw);
                $w_m_place_en=$sport[0]["MB_Dime"];
                $w_m_place_en=str_replace('O','over&nbsp;',$w_m_place_en);
                
                $m_place=$sport[0]["MB_Dime"];
                
                $s_m_place=$sport[0]["MB_Dime"];
                if ($langx=="zh-cn"){
                    $s_m_place=str_replace('O','大&nbsp;',$s_m_place);
                }else if ($langx=="zh-tw"){
                    $s_m_place=str_replace('O','大&nbsp;',$s_m_place);
                }else if ($langx=="en-us" or $langx=="th-tis"){
                    $s_m_place=str_replace('O','over&nbsp;',$s_m_place);
                }
                $w_m_rate=Utils::change_rate($open,$rate[0]);
                $turn_url="/app/member/FT_order/FT_order_ou.php?gid=".$gid."&uid=".$id."&type=".$type."&gnum=".$gnum."&odd_f_type=".$odd_f_type;
                $mtype='OUH';			
                break; 
            case "H":
                $w_m_place=$sport[0]["TG_Dime"];
                $w_m_place=str_replace('U','小&nbsp;',$w_m_place);
                $w_m_place_tw=$sport[0]["TG_Dime"];
                $w_m_place_tw=str_replace('U','小&nbsp;',$w_m_place_tw);
                $w_m_place_en=$sport[0]["TG_Dime"];
                $w_m_place_en=str_replace('U','under&nbsp;',$w_m_place_en);
                
                $m_place=$sport[0]["TG_Dime"];
                
                $s_m_place=$sport[0]["TG_Dime"];
                if ($langx=="zh-cn"){
                    $s_m_place=str_replace('U','小&nbsp;',$s_m_place);
                }else if ($langx=="zh-tw"){
                    $s_m_place=str_replace('U','小&nbsp;',$s_m_place);
                }else if ($langx=="en-us" or $langx=="th-tis"){
                    $s_m_place=str_replace('U','under&nbsp;',$s_m_place);
                }
                
                $w_m_rate=Utils::change_rate($open,$rate[1]);
                $turn_url="/app/member/FT_order/FT_order_ou.php?gid=".$gid."&uid=".$id."&type=".$type."&gnum=".$gnum."&odd_f_type=".$odd_f_type;
                $mtype='OUC';		
                break;
            }
            $Sign="VS.";
            $grape=$m_place;
            $turn="FT_Turn_OU";
            if ($odd_f_type=='H'){
                $gwin=($w_m_rate)*$gold;
            }else if ($odd_f_type=='M' or $odd_f_type=='I'){
                if ($w_m_rate<0){
                    $gwin=$gold;
                }else{
                    $gwin=($w_m_rate)*$gold;
                }
            }else if ($odd_f_type=='E'){
                $gwin=($w_m_rate-1)*$gold;
            }
            $ptype='OU';		
        break;    
        case 4:
            $bet_type='波胆';
          $bet_type_tw="波膽";
          $bet_type_en="Correct Score";
          $caption=$FT_Order.$Order_Correct_Score_betting_order;
          $turn_rate="FT_Turn_PD";
          if($rtype!='OVH'){
          $rtype=str_replace('C','TG',str_replace('H','MB',$rtype));
          $w_m_rate=$sport[0][$rtype];
          }else{
          $w_m_rate=$sport[0]['UP5'];
          }
          if ($rtype=="OVH"){		
              $s_m_place=$Order_Other_Score;
              $w_m_place='其它比分';
              $w_m_place_tw='其它比分';
              $w_m_place_en='Other Score';
              $Sign="VS.";
          }else{
              $M_Place="";
              $M_Sign=$rtype;
              $M_Sign=str_replace("MB","",$M_Sign);
              $M_Sign=str_replace("TG",":",$M_Sign);
              $Sign=$M_Sign."";
          }
          $grape="";
          $turn="FT_Turn_PD";
          $gwin=($w_m_rate-1)*$gold;		
          $ptype='PD';		
          $mtype=$rtype;
      break;
      case 5:
          $bet_type='单双';
          $bet_type_tw="單雙";
          $bet_type_en="Odd/Even";
          $caption=$FT_Order.$Order_Odd_Even_betting_order;
          $turn_rate="FT_Turn_EO_".$open;
          switch ($rtype){
          case "ODD":
              $w_m_place='单';
              $w_m_place_tw='單';
              $w_m_place_en='odd';
              $s_m_place='('.$Order_Odd.')';
              $w_m_rate=Utils::num_rate($open,$sport[0]["S_Single_Rate"]);
              break;
          case "EVEN":
              $w_m_place='双';
              $w_m_place_tw='雙';
              $w_m_place_en='even';
              $s_m_place='('.$Order_Even.')';
              $w_m_rate=Utils::num_rate($open,$sport[0]["S_Double_Rate"]);
              break;
          }
          $Sign="VS.";
          $turn="FT_Turn_EO";
          $gwin=($w_m_rate-1)*$gold;
          $ptype='EO';	
          $mtype=$rtype;
      break;
      case 6:
          $bet_type='总入球';
          $bet_type_tw="總入球";
          $bet_type_en="Total";
          $caption=$FT_Order.$Order_Total_Goals_betting_order;
          $turn_rate="FT_Turn_T";
          switch ($rtype){
          case "0~1":
              $w_m_place='0~1';
              $w_m_place_tw='0~1';
              $w_m_place_en='0~1';
              $s_m_place='(0~1)';
              $w_m_rate=$sport[0]["S_0_1"];
              break;
          case "2~3":
              $w_m_place='2~3';
              $w_m_place_tw='2~3';
              $w_m_place_en='2~3';
              $s_m_place='(2~3)';
              $w_m_rate=$sport[0]["S_2_3"];
              break;
          case "4~6":
              $w_m_place='4~6';
              $w_m_place_tw='4~6';
              $w_m_place_en='4~6';
              $s_m_place='(4~6)';
              $w_m_rate=$sport[0]["S_4_6"];
              break;
          case "OVER":
              $w_m_place='7up';
              $w_m_place_tw='7up';
              $w_m_place_en='7up';
              $s_m_place='(7up)';
              $w_m_rate=$sport[0]["S_7UP"];
              break;
          }
          $turn="FT_Turn_T";
          $Sign="VS.";
          $gwin=($w_m_rate-1)*$gold;
          $ptype='T';
          $mtype=$rtype;				
      break;
      case 7:
          $bet_type='半全场';
          $bet_type_tw="半全場";
          $bet_type_en="Half/Full Time";	
          $caption=$FT_Order.$Order_Half_Full_Time_betting_order;
          $turn_rate="FT_Turn_F";
          switch ($rtype){
          case "FHH":
              $w_m_place=$w_mb_team.'&nbsp;/&nbsp;'.$w_mb_team;
              $w_m_place_tw=$w_mb_team_tw.'&nbsp;/&nbsp;'.$w_mb_team_tw;
              $w_m_place_en=$w_mb_team_en.'&nbsp;/&nbsp;'.$w_mb_team_en;		
              $s_m_place=$sport[0]["MB_Team"].'&nbsp;/&nbsp;'.$sport[0]["MB_Team"];
              $w_m_rate=$sport[0]["MBMB"];
              break;
          case "FHN":
              $w_m_place=$w_mb_team.'&nbsp;/&nbsp;和局';
              $w_m_place_tw=$w_mb_team_tw.'&nbsp;/&nbsp;和局';
              $w_m_place_en=$w_mb_team_en.'&nbsp;/&nbsp;Flat';		
              $s_m_place=$sport[0]["MB_Team"].'&nbsp;/&nbsp;'.$Draw;		
              $w_m_rate=$sport[0]["MBFT"];
              break;
          case "FHC":
              $w_m_place=$w_mb_team.'&nbsp;/&nbsp;'.$w_tg_team;
              $w_m_place_tw=$w_mb_team_tw.'&nbsp;/&nbsp;'.$w_tg_team_tw;
              $w_m_place_en=$w_mb_team_en.'&nbsp;/&nbsp;'.$w_tg_team_en;
              $s_m_place=$sport[0]["MB_Team"].'&nbsp;/&nbsp;'.$sport[0]["TG_Team"];
              $w_m_rate=$sport[0]["MBTG"];
              break;
          case "FNH":
              $w_m_place='和局&nbsp;/&nbsp;'.$w_mb_team;
              $w_m_place_tw='和局&nbsp;/&nbsp;'.$w_mb_team_tw;
              $w_m_place_en='Flat&nbsp;/&nbsp;'.$w_mb_team_en;
              $s_m_place=$Draw.'&nbsp;/&nbsp;'.$sport[0]["MB_Team"];
              $w_m_rate=$sport[0]["FTMB"];
              break;
          case "FNN":
              $w_m_place='和局&nbsp;/&nbsp;和局';
              $w_m_place_tw='和局&nbsp;/&nbsp;和局';
              $w_m_place_en='Flat&nbsp;/&nbsp;Flat';
              $s_m_place=$Draw.'&nbsp;/&nbsp;'.$Draw;
              $w_m_rate=$sport[0]["FTFT"];
              break;
          case "FNC":
              $w_m_place='和局&nbsp;/&nbsp;'.$w_tg_team;
              $w_m_place_tw='和局&nbsp;/&nbsp;'.$w_tg_team_tw;
              $w_m_place_en='Flat&nbsp;/&nbsp;'.$w_tg_team_en;
              $s_m_place=$Draw.'&nbsp;/&nbsp;'.$sport[0]["TG_Team"];	
              $w_m_rate=$sport[0]["FTTG"];
              break;
          case "FCH":
              $w_m_place=$w_tg_team.'&nbsp;/&nbsp;'.$w_mb_team;
              $w_m_place_tw=$w_tg_team_tw.'&nbsp;/&nbsp;'.$w_mb_team_tw;
              $w_m_place_en=$w_tg_team_en.'&nbsp;/&nbsp;'.$w_mb_team_en;
              $s_m_place=$sport[0]["TG_Team"].'&nbsp;/&nbsp;'.$sport[0]["MB_Team"];
              $w_m_rate=$sport[0]["TGMB"];
              break;
          case "FCN":
              $w_m_place=$w_tg_team.'&nbsp;/&nbsp;和局';
              $w_m_place_tw=$w_tg_team_tw.'&nbsp;/&nbsp;和局';
              $w_m_place_en=$w_tg_team_en.'&nbsp;/&nbsp;Flat';
              $s_m_place=$sport[0]["TG_Team"].'&nbsp;/&nbsp;'.$Draw;
              $w_m_rate=$sport[0]["TGFT"];
              break;
          case "FCC":
              $w_m_place=$w_tg_team.'&nbsp;/&nbsp;'.$w_tg_team;
              $w_m_place_tw=$w_tg_team_tw.'&nbsp;/&nbsp;'.$w_tg_team_tw;
              $w_m_place_en=$w_tg_team_en.'&nbsp;/&nbsp;'.$w_tg_team_en;
              $s_m_place=$sport[0]["TG_Team"].'&nbsp;/&nbsp;'.$sport[0]["TG_Team"];
              $w_m_rate=$sport[0]["TGTG"];
              break;
          }
          $Sign="VS.";
          $turn="FT_Turn_F";
          $gwin=($w_m_rate-1)*$gold;		
          $ptype='F';
          $mtype=$rtype;								
      break;	
      case 11:
          $bet_type='半场独赢';
          $bet_type_tw="半場獨贏";
          $bet_type_en="1st Half 1x2";
          $btype="-&nbsp;<font color=red><b>[$Order_1st_Half]</b></font>";
          $caption=$FT_Order.$Order_1st_Half_1_x_2_betting_order;
          $turn_rate="FT_Turn_M";
          $turn="FT_Turn_M";
          switch ($type){
          case "H":
              $w_m_place=$w_mb_team;
              $w_m_place_tw=$w_mb_team_tw;
              $w_m_place_en=$w_mb_team_en;
              $s_m_place=$sport[0]["MB_Team"];
              $w_m_rate=Utils::num_rate($open,$sport[0]["MB_Win_Rate_H"]);
              $mtype='VMH';
              break;
          case "C":
              $w_m_place=$w_tg_team;
              $w_m_place_tw=$w_tg_team_tw;
              $w_m_place_en=$w_tg_team_en;
              $s_m_place=$sport[0]["TG_Team"];
              $w_m_rate=Utils::num_rate($open,$sport[0]["TG_Win_Rate_H"]);
              $mtype='VMC';
              break;
          case "N":
              $w_m_place="和局";
              $w_m_place_tw="和局";
              $w_m_place_en="Flat";
              $s_m_place=$Draw;
              $w_m_rate=Utils::num_rate($open,$sport[0]["M_Flat_Rate_H"]);
              $mtype='VMN';
              break;
          }
          $Sign="VS.";
          $grape="";
          $gwin=($w_m_rate-1)*$gold;
          $ptype='VM';
      break;
      case 12:
          $bet_type='半场让球';
          $bet_type_tw="半場讓球";
          $bet_type_en="1st Half Handicap";
          $btype="-&nbsp;<font color=red><b>[$Order_1st_Half]</b></font>";
          $caption=$FT_Order.$Order_1st_Half_Handicap_betting_order;
          $turn_rate="FT_Turn_R_".$open;
          $rate=Utils::get_other_ioratio($odd_f_type,$sport[0]["MB_LetB_Rate_H"],$sport[0]["TG_LetB_Rate_H"],100);
          switch ($type){
          case "H":
              $w_m_place=$w_mb_team;
              $w_m_place_tw=$w_mb_team_tw;
              $w_m_place_en=$w_mb_team_en;
              $s_m_place=$sport[0]["MB_Team"];
              $w_m_rate=Utils::change_rate($open,$rate[0]);
              $turn_url="/app/member/FT_order/FT_order_hr.php?gid=".$gid."&uid=".$id."&type=".$type."&gnum=".$gnum."&strong=".$strong."&odd_f_type=".$odd_f_type;
              $mtype='VRH';
              break;
          case "C":
              $w_m_place=$w_tg_team;
              $w_m_place_tw=$w_tg_team_tw;
              $w_m_place_en=$w_tg_team_en;
              $s_m_place=$sport[0]["TG_Team"];
              $w_m_rate=Utils::change_rate($open,$rate[1]);
              $turn_url="/app/member/FT_order/FT_order_hr.php?gid=".$gid."&uid=".$id."&type=".$type."&gnum=".$gnum."&strong=".$strong."&odd_f_type=".$odd_f_type;
              $mtype='VRC';
              break;
          }
          $Sign=$sport[0]['M_LetB_H'];
          $grape=$Sign;
          if ($showtype=="H"){
              $l_team=$s_mb_team;
              $r_team=$s_tg_team;
              
              $w_l_team=$w_mb_team;
              $w_l_team_tw=$w_mb_team_tw;
              $w_l_team_en=$w_mb_team_en;
              $w_r_team=$w_tg_team;
              $w_r_team_tw=$w_tg_team_tw;
              $w_r_team_en=$w_tg_team_en;
          }else{
              $r_team=$s_mb_team;
              $l_team=$s_tg_team;
              $w_r_team=$w_mb_team;
              $w_r_team_tw=$w_mb_team_tw;
              $w_r_team_en=$w_mb_team_en;
              $w_l_team=$w_tg_team;
              $w_l_team_tw=$w_tg_team_tw;
              $w_l_team_en=$w_tg_team_en;
          }
          $s_mb_team=$l_team;
          $s_tg_team=$r_team;
          $w_mb_team=$w_l_team;
          $w_mb_team_tw=$w_l_team_tw;
          $w_mb_team_en=$w_l_team_en;
          $w_tg_team=$w_r_team;
          $w_tg_team_tw=$w_r_team_tw;
          $w_tg_team_en=$w_r_team_en;
          $turn="FT_Turn_R";
          if ($odd_f_type=='H'){
              $gwin=($w_m_rate)*$gold;
          }else if ($odd_f_type=='M' or $odd_f_type=='I'){
              if ($w_m_rate<0){
                  $gwin=$gold;
              }else{
                  $gwin=($w_m_rate)*$gold;
              }
          }else if ($odd_f_type=='E'){
              $gwin=($w_m_rate-1)*$gold;
          }
          $ptype='VR';
      break;
      case 13:
            $bet_type='半场大小';
          $bet_type_tw="半場大小";
          $bet_type_en="1st Half Over/Under";
          $caption=$FT_Order.$Order_1st_Half_Over_Under_betting_order;
          $btype="-&nbsp;<font color=red><b>[$Order_1st_Half]</b></font>";
          $turn_rate="FT_Turn_OU_".$open;
          $rate=Utils::get_other_ioratio($odd_f_type,$sport[0]["MB_Dime_Rate_H"],$sport[0]["TG_Dime_Rate_H"],100);
          switch ($type){
          case "C":
              $w_m_place=$sport[0]["MB_Dime_H"];
              $w_m_place=str_replace('O','大&nbsp;',$w_m_place);
              $w_m_place_tw=$sport[0]["MB_Dime_H"];
              $w_m_place_tw=str_replace('O','大&nbsp;',$w_m_place_tw);
              $w_m_place_en=$sport[0]["MB_Dime_H"];
              $w_m_place_en=str_replace('O','over&nbsp;',$w_m_place_en);
              
              $m_place=$sport[0]["MB_Dime_H"];
              
              $s_m_place=$sport[0]["MB_Dime_H"];
              if ($langx=="zh-cn"){
                  $s_m_place=str_replace('O','大&nbsp;',$s_m_place);
              } else if ($langx=="zh-tw"){
                  $s_m_place=str_replace('O','大&nbsp;',$s_m_place);
              } else if ($langx=="en-us" or $langx=="th-tis"){
                  $s_m_place=str_replace('O','over&nbsp;',$s_m_place);
              }
              $w_m_rate=Utils::change_rate($open,$rate[0]);
              $turn_url="/app/member/FT_order/FT_order_hou.php?gid=".$gid."&uid=".$id."&type=".$type."&gnum=".$gnum."&odd_f_type=".$odd_f_type;
              $mtype='VOUH';		
              break;
          case "H":
              $w_m_place=$sport[0]["TG_Dime_H"];
              $w_m_place=str_replace('U','小&nbsp;',$w_m_place);
              $w_m_place_tw=$sport[0]["TG_Dime_H"];
              $w_m_place_tw=str_replace('U','小&nbsp;',$w_m_place_tw);
              $w_m_place_en=$sport[0]["TG_Dime_H"];
              $w_m_place_en=str_replace('U','under&nbsp;',$w_m_place_en);
              
              $m_place=$sport[0]["TG_Dime_H"];
              
              $s_m_place=$sport[0]["TG_Dime_H"];
              if ($langx=="zh-cn"){
                  $s_m_place=str_replace('U','小&nbsp;',$s_m_place);
              }else if ($langx=="zh-tw"){
                  $s_m_place=str_replace('U','小&nbsp;',$s_m_place);
              }else if ($langx=="en-us" or $langx=="th-tis"){
                  $s_m_place=str_replace('U','under&nbsp;',$s_m_place);
              }
              $w_m_rate=Utils::change_rate($open,$rate[1]);	
              $turn_url="/app/member/FT_order/FT_order_hou.php?gid=".$gid."&uid=".$id."&type=".$type."&gnum=".$gnum."&odd_f_type=".$odd_f_type;
              $mtype='VOUC';
              break;
          }
          $Sign="VS.";
          $grape=$m_place;
          $turn="FT_Turn_OU";
          if ($odd_f_type=='H'){
              $gwin=($w_m_rate)*$gold;
          }else if ($odd_f_type=='M' or $odd_f_type=='I'){
              if ($w_m_rate<0){
                  $gwin=$gold;
              }else{
                  $gwin=($w_m_rate)*$gold;
              }
          }else if ($odd_f_type=='E'){
              $gwin=($w_m_rate-1)*$gold;
          }
          $ptype='VOU';		
      break;
      case 14:
          $bet_type='半场波胆';
          $bet_type_tw="半場波膽";
          $bet_type_en="1st Half Correct Score";
          $caption=$FT_Order.$Order_1st_Half_Correct_Score_betting_order;
          $btype="-&nbsp;<font color=red><b>[$Order_1st_Half]</b></font>";
          $turn_rate="FT_Turn_PD";
          if($rtype!='OVH'){
          $rtype=str_replace('C','TG',str_replace('H','MB',$rtype));
          $w_m_rate=$sport[0]["UP5H"];
          }else{
          $w_m_rate=$sport[0]['UP5H'];
          }
          if ($rtype=="OVH"){		
              $s_m_place=$Order_Other_Score;
              $w_m_place='其它比分';
              $w_m_place_tw='其它比分';
              $w_m_place_en='Other Score';
              $Sign="VS.";
          }else{
              $s_m_place=$Order_Other_Score;
              $M_Place="";
              $M_Sign=$rtype;
              $M_Sign=str_replace("MB","",$M_Sign);
              $M_Sign=str_replace("TG",":",$M_Sign);
              $Sign=$M_Sign."";
          }
          $grape="";
          $turn="FT_Turn_PD";
          $gwin=($w_m_rate-1)*$gold;		
          $ptype='VPD';		
          $mtype=$rtype;           
        }
        
        if ($line==11 or $line==12 or $line==13 or $line==14){
            $bottom1_cn="-&nbsp;<font color=#666666>[上半]</font>&nbsp;";
            $bottom1_tw="-&nbsp;<font color=#666666>[上半]</font>&nbsp;";
            $bottom1_en="-&nbsp;<font color=#666666>[1st Half]</font>&nbsp;";
        }
        
        if ($line==2 or $line==3 or $line==12 or $line==13){
            // if ($w_m_rate!=$ioradio_r_h){
            //     $turn_url=$turn_url.'&error_flag=1';
            //     echo "<script language='javascript'>self.location='$turn_url';</script>";
            //     //exit();
            //     //Todo:
            // }
            $oddstype=$odd_f_type;
        }else{
            $oddstype='';
        }
        $s_m_place=Utils::filiter_team(trim($s_m_place));
        
        $w_mid="<br>[".$sport[0]['MB_MID']."]vs[".$sport[0]['TG_MID']."]<br>";
        $lines=$sport[0]['M_League'].$w_mid.$w_mb_team."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team."<br>";
        // $lines=$lines."<FONT color=#cc0000>".$w_m_place."</FONT>&nbsp;".$bottom1_cn."@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";	
        // $lines_tw=$sport[0]['M_League_tw'].$w_mid.$w_mb_team_tw."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team_tw."<br>";
        // $lines_tw=$lines_tw."<FONT color=#cc0000>".$w_m_place_tw."</FONT>&nbsp;".$bottom1_tw."@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";
        // $lines_en=$sport[0]['M_League_en'].$w_mid.$w_mb_team_en."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team_en."<br>";
        // $lines_en=$lines_en."<FONT color=#cc0000>".$w_m_place_en."</FONT>&nbsp;".$bottom1_en."@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";	
    
        if($w_m_rate=='' or $gwin<=0 or $gwin==''){
            return response()->json([
                'success' => false,
                'message' => "赛程已关闭,无法进行交易!!"
            ], 404);
        }
       
        $ip_addr = Utils::get_ip();
        
        $msql = "select $turn as M_turn from web_member_data where UserName='$memname'";
        $mrow = DB::select($msql)[0];
        $m_turn=$mrow->M_turn+0;
        
        $asql = "select $turn_rate as A_turn from web_agents_data where UserName='$super'";
        $arow = DB::select($asql)[0];
        $a_rate=$arow->A_turn+0;
        
        $bsql = "select $turn_rate as B_turn from web_agents_data where UserName='$corprator'";
        $brow = DB::select($bsql)[0];
        $b_rate=$brow->B_turn+0;
        
        $csql = "select $turn_rate as C_turn from web_agents_data where UserName='$world'";
        $crow = DB::select($csql)[0];
        $c_rate=$crow->C_turn+0;
        
        $dsql = "select $turn_rate as D_turn from web_agents_data where UserName='$agents'";
        $drow = DB::select($dsql)[0];
        $d_rate=$drow->D_turn+0;
        
        $psql = "select * from web_agents_data where UserName='$agents'";
        $prow = DB::select($psql)[0];
        $a_point=$prow->A_Point+0;
        $b_point=$prow->B_Point+0;
        $c_point=$prow->C_Point+0;
        $d_point=$prow->D_Point+0;
        //  return "aaaaaasssss";
        $max_sql = "select max(ID) max_id from web_report_data where BetTime<'$bettime'";
        $max_row = DB::select($max_sql)[0];
        $max_id=$max_row->max_id;
        $num=rand(10,50);
       
        $newid=$max_id+$num;
        $OrderID=Utils::show_voucher($line,$newid);  //定单号
        if($oddstype=='') $oddstype='H';
        
        
        $sql = "INSERT INTO web_report_data	(ID,OrderID,MID,Active,LineType,Mtype,M_Date,BetTime,BetScore,Middle,BetType,M_Place,M_Rate,M_Name,Gwin,TurnRate,OpenType,OddsType,ShowType,Agents,World,Corprator,Super,Admin,A_Rate,B_Rate,C_Rate,D_Rate,A_Point,B_Point,C_Point,D_Point,BetIP,Ptype,Gtype,CurType,Ratio,MB_MID,TG_MID,Pay_Type,MB_Ball,TG_Ball)                values ('$newid','$OrderID','$gid','$active','$line','$mtype','$m_date','$bettime','$gold','$lines',   '$bet_type','$grape','$w_m_rate','$memname','$gwin','$m_turn','$open','$oddstype','$showtype', '$agents','$world','$corprator','$super','$admin','$a_rate','$b_rate','$c_rate','$d_rate','$a_point', '$b_point', '$c_point','$d_point','$ip_addr','$ptype','FT','$w_current','$w_ratio','$w_mb_mid', '$w_tg_mid','$pay_type','$mb_ball','$tg_ball')";
        DB::insert($sql);
        
        $ouid = DB::getPdo()->lastInsertId();
        // return  $ouid; 
        $assets=Utils::GetField($memname,'Money');
        $user_id=Utils::GetField($memname,'ID');
        $datetime=date("Y-m-d H:i:s",time()+12*3600);
        
        Utils::ProcessUpdate($memname);  //防止并发
        $hMoney = $member['Money'];
        $rMoney = $hMoney - $gold;
        
        $q1 = User::where('id', $id)->update(['Money' => $rMoney]);
        
        if($q1==1){
            $balance=Utils::GetField($memname,'Money');
            $money_log_sql="insert into money_log set user_id='$user_id',order_num='$OrderID',about='投注足球<br>gid:$gid<br>RID:$ouid',update_time='$datetime',type='$lines',order_value='-$gold',assets=$assets,balance=$balance";
            DB::insert($money_log_sql);
        }else{
            DB::raw("delete from web_report_data where id=".$ouid);
            echo "<script>alert('投注不成功,请联系客服!');top.location.reload();</script>";
            //todo: exit() 
        }
        // return "aaaassssaaaaa";
        $t=date("Y-m-d H:i:s");
        $pp=file_get_contents("php://input");
        $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/FT-".date("Ymd").".txt";
        if(file_exists($tmpfile)){
            $f=fopen($tmpfile,'a');
        }else{
            $f=fopen($tmpfile,'w');
        }
        fwrite($f,$t."\r\n".$sql."\r\n");
        fwrite($f,$pp."\r\n\r\n");
        fclose($f);
        return response()->json([
            "success" => true,
            "data" => $rMoney
        ], 200);
    }
}
