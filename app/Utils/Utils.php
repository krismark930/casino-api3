<?php

namespace App\Utils;

use App\Models\Web\UpdateLog;
use App\Models\User;
use DB;
class Utils {
    const Scores = [
        '',
        '取消',
        '赛事腰斩',
        '赛事改期',
        '赛事延期',
        '赛事延赛',
        '赛事取消',
        '赛事无PK加时',
        '球员弃权',
        '队名错误',
        '主客场错误',
        '先发投手更换',
        '选手更换',
        '联赛名称错',
        '', '', '', '', '',
        '提前开赛',
        '[注单确认]',
        '[取消]',
        '[赛事腰斩]',
        '[赛事改期]',
        '[赛事延期]',
        '[赛事延赛]',
        '[赛事取消]',
        '[赛事无PK加时]',
        '[球员弃权]',
        '[队名错误]',
        '[主客场错误]',
        '[先发投手更换]',
        '[选手更换]',
        '[联赛名称错误]',
        '[盘口错误]',
        '[提前开赛]',
        '[赛果错误]',
        '[未接受注单]',
        '[进球取消]',
        '[红卡取消]',
        '[非正常投注]',
        '[赔率错误]'
    ];
    
    const Rep_HK = '香港盤';
    const Rep_Malay = '馬來盤';
    const Rep_Indo = '印尼盤';
    const Rep_Euro = '歐洲盤';

    const Mnu_Soccer = '足球';
    const Mnu_Bask = '篮球';
    const Mnu_Base = '棒球';
    const Mnu_Tennis = '网球';
    const Mnu_Voll = '排球';
    const Mnu_Other = '其它';
    const Mnu_BasketBall = '籃球';
    const Mnu_Stock = '指数';
    const Mnu_Outright = '冠军';
    const Mnu_MarkSix = '六合彩';
    const Mnu_EarlyMarket = '早餐';
    const Mnu_Guan = 'Mnu_Guan';

    static function ProcessUpdate($username){
        global $dbname;
        do{
            $flag=1;
            $MD5String=md5($username.date("YmdHis",time()+12*3600));
            $DateTime=time();
            //$sql = "insert into web_update_log set MD5String='$MD5String',DateTime='$DateTime'";
            $data = [
                "MD5String" => $MD5String,
                "DateTime" => $DateTime,
            ];
            $updateLog = new UpdateLog;
            if ($updateLog->create($data)){
            }else{
                $flag=0;
            }
            if($flag==0) sleep(1);  //暂停1秒
        }while($flag==0);
    }

    static function GetField($username, $Field){
        $user = User::where('UserName',$username)->first();
        if(!$user){
            return "";
        }
        return $user[$Field];
    }

    static function Mobile(){
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])){
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
       if (isset ($_SERVER['HTTP_USER_AGENT'])){
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
                );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])){
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
                return true;
            }
        }
        return false;
    }
    static function SafeString($inString){
        $inString=str_replace('"','',$inString);
        $inString=str_replace("'","",$inString);
        $inString=str_replace('<','',$inString);
        $inString=str_replace('>','',$inString);
        $inString=str_replace('=','',$inString);
        //$inString=str_replace(' ','',$inString);
        if(strlen($inString)>50) return '';
        if(strpos(strtolower($inString),'script')) return '';
        if(strpos(strtolower($inString),'frame')) return '';
        if(strpos(strtolower($inString),'update')) return '';
        if(strpos(strtolower($inString),'%20')) return '';
        if(strpos(strtolower($inString),'*')) return '';
        if(strpos(strtolower($inString),"'")) return '';
        if(strpos(strtolower($inString),"select")) return '';
        if(strpos(strtolower($inString),"convert")) return '';
        if(strpos(strtolower($inString),"concat")) return '';
        if(strpos(strtolower($inString),"0x")) return '';
        return $inString;
    }

    static function filiter_team($repteam){
        //$repteam=trim(str_replace(" ","",$repteam));
        $repteam=trim(str_replace("[H]","",$repteam));
        $repteam=trim(str_replace("[主]","",$repteam));
        $repteam=trim(str_replace("[中]","",$repteam));
        $repteam=trim(str_replace("[主]","",$repteam));
        $repteam=trim(str_replace("[中]","",$repteam));
        $repteam=trim(str_replace("[Home]","",$repteam));
        $repteam=trim(str_replace("[Mid]","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [上半场]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [下半场]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [上半場]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [下半場]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [1st]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [2nd]</font>","",$repteam));

        $repteam=trim(str_replace("<font color=gray> - [上半]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [下半]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [第1节]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [第2节]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [第3节]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [第4节]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [上半]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [下半]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [第1節]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [第2節]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [第3節]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [第4節]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [1st Half]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [2nd Half]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [Q1]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [Q2]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [Q3]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [Q4]</font>","",$repteam));

        $filiter_team=$repteam;
        return $filiter_team;
    }

    static function num_rate($c_type,$c_rate){
        switch($c_type){
            case 'A':
                $t_rate='0';
                break;
            case 'B':
                $t_rate='0';
                break;
            case 'C':
                $t_rate='0';
                break;
            case 'D':
                $t_rate='0';
                break;
        }
        if($c_rate!=''){
            $num_rate=number_format($c_rate-$t_rate,2);
            if ($num_rate<=0){
                $num_rate='';
            }
        }else{
            $num_rate='';
        }
        return $num_rate;
    }

    static function change_rate($c_type,$c_rate){
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
        if($c_rate!='' and $c_rate!='0'){
            $change_rate=number_format($c_rate-$t_rate,3);
            if ($change_rate<=0 and $change_rate>=-0.03){
                $change_rate='';
            }
        }else{
            $change_rate='';
        }
        return $change_rate;
    }
    static function fileter0($rate){
        for($i=1;$i<strlen($rate);$i++){
            if (substr($rate, -$i, 1)<>'0'){
                if (substr($rate, -$i, 1)=='.'){
                    $fileter0=substr($rate,0,strlen($rate)-$i);
                }else{
                    $fileter0=substr($rate,0,strlen($rate)-$i+1);
                }
                break;
            }
        }
        return $fileter0;
    }

    static function singleset($ptype){
        //require ("config.inc.php");

        $sql="select $ptype as P3,R,MAX from web_system_data where ID=1";
        $row = DB::select($sql)[0];
        $p=$row->P3;
        $pmax=$row->MAX;
        return array($p,$pmax);
    }

    static function show_voucher($line,$id){
        //require ("config.inc.php");
        $sql="select OUID,DTID,PMID from web_system_data";
        $row = DB::select($sql)['0'];
        $ouid=$row->OUID;
        $dtid=$row->DTID;
        $pmid=$row->PMID;
        switch($line){
            case 1:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 2:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 3:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 4:
                $show_voucher='DT'.($id+$dtid);
                break;
            case 5:
                $show_voucher='DT'.($id+$dtid);
                break;
            case 6:
                $show_voucher='DT'.($id+$dtid);
                break;
            case 7:
                $show_voucher='DT'.($id+$dtid);
                break;
            case 8:
                $show_voucher='PM'.($id+$pmid);
                break;
            case 9:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 10:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 11:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 12:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 13:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 14:
                $show_voucher='DT'.($id+$dtid);
                break;
            case 15:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 16:
                $show_voucher='DT'.($id+$dtid);
                break;
            case 19:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 20:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 21:
                $show_voucher='OU'.($id+$ouid);
                break;
            case 31:
                $show_voucher='OU'.($id+$ouid);
                break;
            }
            return $show_voucher;
        }
    static function get_ip(){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ){
            $onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $c_agentip=1;
        }elseif(isset($_SERVER['HTTP_CLIENT_IP'] ) ){
            $onlineip = $_SERVER['HTTP_CLIENT_IP'];
            $c_agentip=1;
        }else{
            $onlineip = $_SERVER['REMOTE_ADDR'];
            $c_agentip=0;
        }
        $a=array("'",'"',";","and","or","update","select","insert","table");
        $b=array("","","","","","","","","");
        $onlineip=str_replace($a,$b,$onlineip);
        $onlineip=substr($onlineip,0,20);
        //$c_agentip记录是否为代理ip
        return $onlineip;
    }
    static function chg_ior($odd_f,$iorH,$iorC,$showior){
        $ior=Array();
        if($iorH < 3) $iorH *=1000;
        if($iorC < 3) $iorC *=1000;
        $iorH=$iorH;
        $iorC=$iorC;
        switch($odd_f){
        case "H":	//香港變盤(輸水盤)
            $ior = Utils::get_HK_ior($iorH,$iorC);
            break;
        case "M":	//馬來盤
            $ior = Utils::get_MA_ior($iorH,$iorC);
            break;
        case "I" :	//印尼盤
            $ior = Utils::get_IND_ior($iorH,$iorC);
            break;
        case "E":	//歐洲盤
            $ior = Utils::get_EU_ior($iorH,$iorC);
            break;
        default:	//香港盤
            $ior[0]=$iorH ;
            $ior[1]=$iorC ;
        }
        $ior[0]/=1000;
        $ior[1]/=1000;
        $ior[0]=Utils::Decimal_point($ior[0],$showior);
        $ior[1]=Utils::Decimal_point($ior[1],$showior);
        //$ior[0]=number(Decimal_point($ior[0],$showior),3);
        //$ior[1]=number(Decimal_point($ior[1],$showior),3);
        return $ior;
    }
    /*
    * 選擇多盤口時 轉換成該選擇賠率
    * @param odd_type 	選擇盤口
    * @param iorH		主賠率
    * @param iorC		客賠率
    * @param show		顯示位數
    * @return		回傳陣列 0-->H  ,1-->C
    */
    static function get_other_ioratio($odd_type,$iorH,$iorC,$showior){
        $out=Array();
        if($iorH!="" ||$iorC!=""){
            $out =Utils::chg_ior($odd_type,$iorH,$iorC,$showior);
        }else{
            $out[0]=$iorH;
            $out[1]=$iorC;
        }
        return $out;
    }
    /*
    去正負號做小數第幾位捨去
    進來的值是小數值
    */
    static function Decimal_point($tmpior,$show){
        $sign="";
        $sign =(($tmpior < 0)?"Y":"N");
        $tmpior = (floor(abs($tmpior) * $show + 1 / $show )) / $show;
        return ($tmpior * (($sign =="Y")? -1:1));
    }
    /**
     * 換算成歐洲盤賠率
     * @param H_ratio
     * @param C_ratio
     * @return
     */
    static function get_EU_ior($H_ratio, $C_ratio){
        $out_ior=Array();
        $out_ior = Utils::get_HK_ior($H_ratio,$C_ratio);
        $H_ratio=$out_ior[0];
        $C_ratio=$out_ior[1];
        $out_ior[0]=$H_ratio+1000;
        $out_ior[1]=$C_ratio+1000;
        return $out_ior;
    }
    static function get_HK_ior($H_ratio,$C_ratio){
        $out_ior=Array();
        $line="";
        $lowRatio="";
        $nowRatio="";
        $highRatio="";
        $nowType="";
        if ($H_ratio <= 1000 && $C_ratio <= 1000){
            $out_ior[0]=$H_ratio;
            $out_ior[1]=$C_ratio;
            return $out_ior;
        }
        $line=2000 - ( $H_ratio + $C_ratio );
        if ($H_ratio > $C_ratio){
            $lowRatio=$C_ratio;
            $nowType = "C";
        }else{
            $lowRatio = $H_ratio;
            $nowType = "H";
        }
        if (((2000 - $line) - $lowRatio) > 1000){
            //對盤馬來盤
            $nowRatio = ($lowRatio + $line) * (-1);
        }else{
            //對盤香港盤
            $nowRatio=(2000 - $line) - $lowRatio;
        }
        if ($nowRatio < 0){
            $highRatio = (abs(1000 / $nowRatio) * 1000) ;
        }else{
            $highRatio = (2000 - $line - $nowRatio) ;
        }
        if ($nowType == "H"){
            $out_ior[0]=$lowRatio;
            $out_ior[1]=$highRatio;
        }else{
            $out_ior[0]=$highRatio;
            $out_ior[1]=$lowRatio;
        }
        return $out_ior;
    }
    /**
     * 換算成馬來盤賠率
     * @param H_ratio
     * @param C_ratio
     * @return
     */
    static function get_MA_ior( $H_ratio, $C_ratio){
        $out_ior=Array();
        $line="";
        $lowRatio="";
        $highRatio="";
        $nowType="";
        if (($H_ratio <= 1000 && $C_ratio <= 1000)){
            $out_ior[0]=$H_ratio;
            $out_ior[1]=$C_ratio;
            return $out_ior;
        }
        $line=2000 - ( $H_ratio + $C_ratio );
        if ($H_ratio > $C_ratio){
            $lowRatio = $C_ratio;
            $nowType = "C";
        }else{
            $lowRatio = $H_ratio;
            $nowType = "H";
        }
        $highRatio = ($lowRatio + $line) * (-1);
        if ($nowType == "H"){
            $out_ior[0]=$lowRatio;
            $out_ior[1]=$highRatio;
        }else{
            $out_ior[0]=$highRatio;
            $out_ior[1]=$lowRatio;
        }
        return $out_ior;
    }
    /**
     * 換算成印尼盤賠率
     * @param H_ratio
     * @param C_ratio
     * @return
     */
    static function get_IND_ior( $H_ratio, $C_ratio){
        $out_ior=Array();
        $out_ior = get_HK_ior($H_ratio,$C_ratio);
        $H_ratio=$out_ior[0];
        $C_ratio=$out_ior[1];
        $H_ratio /= 1000;
        $C_ratio /= 1000;
        if($H_ratio < 1){
            $H_ratio=(-1) / $H_ratio;
        }
        if($C_ratio < 1){
            $C_ratio=(-1) / $C_ratio;
        }
        $out_ior[0]=$H_ratio*1000;
        $out_ior[1]=$C_ratio*1000;
        return $out_ior;
    }

    /****************************************************************************************** */

    static function MoneyToSsc($t_user){
        $result = User::select('UserName', 'Money', 'Credit')->where('UserName', $t_user)->get();
        $rowuser = $result[0];
        if(!empty($rowuser['UserName']))
        {
            DB::table('g_user')->where('g_name', $rowuser['UserName'])
                ->update(['g_money' => $rowuser['Credit'], 'g_money_yes' => $rowuser['Money']]);
        }
    }

    // static function show_voucher($line, $id)
    // {  //生成注单号
    //     switch ($line) {
    //         case 1:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //         case 2:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //         case 3:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //         case 4:
    //             $show_voucher = 'DT' . ($id + 29027142);
    //             break;
    //         case 5:
    //             $show_voucher = 'DT' . ($id + 29027142);
    //             break;
    //         case 6:
    //             $show_voucher = 'DT' . ($id + 29027142);
    //             break;
    //         case 7:
    //             $show_voucher = 'P' . ($id + 29027142);
    //             break;
    //         case 8:
    //             $show_voucher = 'PR' . ($id + 29657821);
    //             break;
    //         case 9:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //         case 10:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //         case 11:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //         case 12:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //         case 13:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //         case 14:
    //             $show_voucher = 'DT' . ($id - 29127142);
    //             break;
    //         case 15:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //         case 19:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //         case 20:
    //             $show_voucher = 'OU' . ($id + 29027142);
    //             break;
    //     }
    //     return $show_voucher;
    // }
    //大小球计算：
    static function odds_dime($mbin1, $tgin1, $dime, $mtype)
    {
        if ($mbin1 < 0 or $tgin1 < 0) return 0;
        $dime = str_replace('大', '', $dime);
        $dime = str_replace('小', '', $dime);
        $dime = str_replace('O', '', $dime);
        $dime = str_replace('U', '', $dime);
        $total_inball = $mbin1 + $tgin1;
        $dime_odds = explode("/", $dime);
        switch (sizeof($dime_odds)) {
            case 1:
                $odds_inball = $total_inball - $dime_odds[0];
                switch ($mtype) { //下大
                    case 'OUH':
                        if ($odds_inball > 0) {
                            $grape = 1;
                        } else if ($odds_inball < 0) {
                            $grape = -1;
                        } else {
                            $grape = 0;
                        }
                        break;
                    case 'OUC': //下小
                        if ($odds_inball > 0) {
                            $grape = -1;
                        } else if ($odds_inball < 0) {
                            $grape = 1;
                        } else {
                            $grape = 0;
                        }
                        break;
                }
                break;
            case 2:
                if (ceil($dime_odds[0]) == $dime_odds[0]) {
                    $odds_inball = $total_inball - $dime_odds[0];
                    switch ($mtype) {
                        case "OUH":
                            if ($odds_inball > 0) {
                                $grape = 1;
                            } else if ($odds_inball < 0) {
                                $grape = -1;
                            } else if ($odds_inball == 0) {
                                $grape = -0.5;
                            }
                            break;
                        case "OUC":
                            if ($odds_inball > 0) {
                                $grape = -1;
                            } else if ($odds_inball < 0) {
                                $grape = 1;
                            } else if ($odds_inball == 0) {
                                $grape = 0.5;
                            }
                            break;
                    }
                } else {
                    $odds_inball = $total_inball - $dime_odds[1];
                    switch ($mtype) {
                        case "OUH":
                            if ($odds_inball > 0) {
                                $grape = 1;
                            } else if ($odds_inball < 0) {
                                $grape = -1;
                            } else if ($odds_inball == 0) {
                                $grape = 0.5;
                            }
                            break;
                        case "OUC":
                            if ($odds_inball > 0) {
                                $grape = -1;
                            } else if ($odds_inball < 0) {
                                $grape = 1;
                            } else if ($odds_inball == 0) {
                                $grape = -0.5;
                            }
                            break;
                    }
                }
                break;
        }
        $odds_dime = $grape;
        return $odds_dime;
    }
    //让球计算:
    static function odds_letb($mbin, $tgin, $showtype, $dime, $mtype)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        if (mb_eregi("[+]", $dime)) {
            $letb_odds = explode("+", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){//半球在后1/1.5
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else if (mb_eregi("[-]", $dime)) {
            $letb_odds = explode("-", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else { //这里指的是另外的。注意这里
            $letb_odds = explode("/", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'RH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'RC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        }
        $odds_letb = $grade;
        return $odds_letb;
    }
    //上半大小球计算：
    static function odds_dime_v($mbin1, $tgin1, $dime, $mtype)
    {
        if ($mbin1 < 0 or $tgin1 < 0) return 0;
        $dime = str_replace('大', '', $dime);
        $dime = str_replace('小', '', $dime);
        $dime = str_replace('O', '', $dime);
        $dime = str_replace('U', '', $dime);
        $total_inball = $mbin1 + $tgin1;
        $dime_odds = explode("/", $dime);
        switch (sizeof($dime_odds)) {
            case 1:
                $odds_inball = $total_inball - $dime_odds[0];
                switch ($mtype) { //下大
                    case 'VOUH':
                        if ($odds_inball > 0) {
                            $grape = 1;
                        } else if ($odds_inball < 0) {
                            $grape = -1;
                        } else {
                            $grape = 0;
                        }
                        break;
                    case 'VOUC': //下小
                        if ($odds_inball > 0) {
                            $grape = -1;
                        } else if ($odds_inball < 0) {
                            $grape = 1;
                        } else {
                            $grape = 0;
                        }
                        break;
                }
                break;
            case 2:
                if (ceil($dime_odds[0]) == $dime_odds[0]) {
                    $odds_inball = $total_inball - $dime_odds[0];
                    switch ($mtype) {
                        case "VOUH":
                            if ($odds_inball > 0) {
                                $grape = 1;
                            } else if ($odds_inball < 0) {
                                $grape = -1;
                            } else if ($odds_inball == 0) {
                                $grape = -0.5;
                            }
                            break;
                        case "VOUC":
                            if ($odds_inball > 0) {
                                $grape = -1;
                            } else if ($odds_inball < 0) {
                                $grape = 1;
                            } else if ($odds_inball == 0) {
                                $grape = 0.5;
                            }
                            break;
                    }
                } else {
                    $odds_inball = $total_inball - $dime_odds[1];
                    switch ($mtype) {
                        case "VOUH":
                            if ($odds_inball > 0) {
                                $grape = 1;
                            } else if ($odds_inball < 0) {
                                $grape = -1;
                            } else if ($odds_inball == 0) {
                                $grape = 0.5;
                            }
                            break;
                        case "VOUC":
                            if ($odds_inball > 0) {
                                $grape = -1;
                            } else if ($odds_inball < 0) {
                                $grape = 1;
                            } else if ($odds_inball == 0) {
                                $grape = -0.5;
                            }
                            break;
                    }
                }
                break;
        }
        $odds_dime = $grape;
        return $odds_dime;
    }
    //上半让球计算:
    static function odds_letb_v($mbin, $tgin, $showtype, $dime, $mtype)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        if (mb_eregi("[+]", $dime)) {
            $letb_odds = explode("+", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){//半球在后1/1.5
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else if (mb_eregi("[-]", $dime)) {
            $letb_odds = explode("-", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else { //这里指的是另外的。注意这里
            $letb_odds = explode("/", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'VRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'VRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        }
        $odds_letb = $grade;
        return $odds_letb;
    }
    //滚球大小球计算：
    static function odds_dime_rb($mbin1, $tgin1, $dime, $mtype)
    {
        if ($mbin1 < 0 or $tgin1 < 0) return 0;
        $dime = str_replace('大', '', $dime);
        $dime = str_replace('小', '', $dime);
        $dime = str_replace('O', '', $dime);
        $dime = str_replace('U', '', $dime);
        $total_inball = $mbin1 + $tgin1;
        $dime_odds = explode("/", $dime);
        switch (sizeof($dime_odds)) {
            case 1:
                $odds_inball = $total_inball - $dime_odds[0];
                switch ($mtype) { //下大
                    case 'ROUH':
                        if ($odds_inball > 0) {
                            $grape = 1;
                        } else if ($odds_inball < 0) {
                            $grape = -1;
                        } else {
                            $grape = 0;
                        }
                        break;
                    case 'ROUC': //下小
                        if ($odds_inball > 0) {
                            $grape = -1;
                        } else if ($odds_inball < 0) {
                            $grape = 1;
                        } else {
                            $grape = 0;
                        }
                        break;
                }
                break;
            case 2:
                if (ceil($dime_odds[0]) == $dime_odds[0]) {
                    $odds_inball = $total_inball - $dime_odds[0];
                    switch ($mtype) {
                        case "ROUH":
                            if ($odds_inball > 0) {
                                $grape = 1;
                            } else if ($odds_inball < 0) {
                                $grape = -1;
                            } else if ($odds_inball == 0) {
                                $grape = -0.5;
                            }
                            break;
                        case "ROUC":
                            if ($odds_inball > 0) {
                                $grape = -1;
                            } else if ($odds_inball < 0) {
                                $grape = 1;
                            } else if ($odds_inball == 0) {
                                $grape = 0.5;
                            }
                            break;
                    }
                } else {
                    $odds_inball = $total_inball - $dime_odds[1];
                    switch ($mtype) {
                        case "ROUH":
                            if ($odds_inball > 0) {
                                $grape = 1;
                            } else if ($odds_inball < 0) {
                                $grape = -1;
                            } else if ($odds_inball == 0) {
                                $grape = 0.5;
                            }
                            break;
                        case "ROUC":
                            if ($odds_inball > 0) {
                                $grape = -1;
                            } else if ($odds_inball < 0) {
                                $grape = 1;
                            } else if ($odds_inball == 0) {
                                $grape = -0.5;
                            }
                            break;
                    }
                }
                break;
        }
        $odds_dime = $grape;
        return $odds_dime;
    }
    //滚球让球计算:
    static function odds_letb_rb($mbin, $tgin, $showtype, $dime, $mtype)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        if (mb_eregi("[+]", $dime)) {
            $letb_odds = explode("+", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //		if (strlen($letb_odds[1])>2){//半球在后1/1.5
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else if (mb_eregi("[-]", $dime)) {
            $letb_odds = explode("-", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else { //这里指的是另外的。注意这里
            $letb_odds = explode("/", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'RRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'RRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        }
        $odds_letb = $grade;
        return $odds_letb;
    }
    //滚球上半大小球计算：
    static function odds_dime_vrb($mbin1, $tgin1, $dime, $mtype)
    {
        if ($mbin1 < 0 or $tgin1 < 0) return 0;
        $dime = str_replace('大', '', $dime);
        $dime = str_replace('小', '', $dime);
        $dime = str_replace('O', '', $dime);
        $dime = str_replace('U', '', $dime);
        $total_inball = $mbin1 + $tgin1;
        $dime_odds = explode("/", $dime);
        switch (sizeof($dime_odds)) {
            case 1:
                $odds_inball = $total_inball - $dime_odds[0];
                switch ($mtype) { //下大
                    case 'VROUH':
                        if ($odds_inball > 0) {
                            $grape = 1;
                        } else if ($odds_inball < 0) {
                            $grape = -1;
                        } else {
                            $grape = 0;
                        }
                        break;
                    case 'VROUC': //下小
                        if ($odds_inball > 0) {
                            $grape = -1;
                        } else if ($odds_inball < 0) {
                            $grape = 1;
                        } else {
                            $grape = 0;
                        }
                        break;
                }
                break;
            case 2:
                if (ceil($dime_odds[0]) == $dime_odds[0]) {
                    $odds_inball = $total_inball - $dime_odds[0];
                    switch ($mtype) {
                        case "VROUH":
                            if ($odds_inball > 0) {
                                $grape = 1;
                            } else if ($odds_inball < 0) {
                                $grape = -1;
                            } else if ($odds_inball == 0) {
                                $grape = -0.5;
                            }
                            break;
                        case "VROUC":
                            if ($odds_inball > 0) {
                                $grape = -1;
                            } else if ($odds_inball < 0) {
                                $grape = 1;
                            } else if ($odds_inball == 0) {
                                $grape = 0.5;
                            }
                            break;
                    }
                } else {
                    $odds_inball = $total_inball - $dime_odds[1];
                    switch ($mtype) {
                        case "VROUH":
                            if ($odds_inball > 0) {
                                $grape = 1;
                            } else if ($odds_inball < 0) {
                                $grape = -1;
                            } else if ($odds_inball == 0) {
                                $grape = 0.5;
                            }
                            break;
                        case "VROUC":
                            if ($odds_inball > 0) {
                                $grape = -1;
                            } else if ($odds_inball < 0) {
                                $grape = 1;
                            } else if ($odds_inball == 0) {
                                $grape = -0.5;
                            }
                            break;
                    }
                }
                break;
        }
        $odds_dime = $grape;
        return $odds_dime;
    }
    //滚球上半让球计算:
    static function odds_letb_vrb($mbin, $tgin, $showtype, $dime, $mtype)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        if (mb_eregi("[+]", $dime)) {
            $letb_odds = explode("+", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){//半球在后1/1.5
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = -1;
                                            } else {
                                                $grade = 1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            if ($letb_odds[1] == 100) {
                                                $grade = 1;
                                            } else {
                                                $grade = -1 * $letb_odds[1] / 100;
                                            }
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else if (mb_eregi("[-]", $dime)) {
            $letb_odds = explode("-", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin;
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = 1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < $letb_odds[0]) {
                                            $grade = -1;
                                        } else if ($abcd > $letb_odds[0]) {
                                            $grade = 1;
                                        } else if ($abcd == $letb_odds[0]) {
                                            $grade = -1 * $letb_odds[1] / 100;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        } else { //这里指的是另外的。注意这里
            $letb_odds = explode("/", $dime);
            switch (sizeof($letb_odds)) {
                case 1:
                    if (strlen($letb_odds[0]) > 2) {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 2:
                    if (floatval($letb_odds[1]) <> intval($letb_odds[1])) { //半球在后1/1.5
                        //if (strlen($letb_odds[1])>2){
                        switch ($showtype) {
                            case "H": //让球方是主队
                                $abcd = $mbin - $tgin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[0];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    } else {
                        switch ($showtype) {
                            case "H": //让球方是主队0.5/1
                                $abcd = $mbin - $tgin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                }
                                break;
                            case "C": //让球方是客队
                                $abcd = $tgin - $mbin - $letb_odds[1];
                                switch ($mtype) {
                                    case 'VRRH':
                                        if ($abcd < 0) {
                                            $grade = 1;
                                        } else if ($abcd > 0) {
                                            $grade = -1;
                                        } else if ($abcd == 0) {
                                            $grade = -0.5;
                                        }
                                        break;
                                    case 'VRRC':
                                        if ($abcd < 0) {
                                            $grade = -1;
                                        } else if ($abcd > 0) {
                                            $grade = 1;
                                        } else if ($abcd == 0) {
                                            $grade = 0.5;
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
            }
        }
        $odds_letb = $grade;
        return $odds_letb;
    }
    //波胆计算：
    static function odds_pd($mb_in_score, $tg_in_score, $m_place)
    {
        if ($mb_in_score < 0 or $tg_in_score < 0) return 0;
        $betplace = 'MB' . $mb_in_score . 'TG' . $tg_in_score;
        if (($m_place == 'OVMB' or $m_place == 'OVH') and $mb_in_score > 4) {
            $grade = 1;
        } elseif (($m_place == 'OVMB' or $m_place == 'OVH') and $tg_in_score > 4) {
            $grade = 1;
        } elseif ($m_place == $betplace) {
            $grade = 1;
        } else {
            $grade = -1;
        }

        $odds_pd = $grade;
        return $odds_pd;
    }
    //上半波胆计算：
    static function odds_pd_v($mb_in_score_v, $tg_in_score_v, $m_place)
    {
        if ($mb_in_score_v < 0 or $tg_in_score_v < 0) return 0;
        $betplace = 'MB' . $mb_in_score_v . 'TG' . $tg_in_score_v;
        if (($m_place == 'OVMB' or $m_place == 'OVH') and $mb_in_score_v > 4) {
            $grade = 1;
        } elseif (($m_place == 'OVMB' or $m_place == 'OVH') and $tg_in_score_v > 4) {
            $grade = 1;
        } elseif ($m_place == $betplace) {
            $grade = 1;
        } else {
            $grade = -1;
        }

        $odds_pd_v = $grade;
        return $odds_pd_v;
    }
    //单双计算:
    static function odds_eo($mb_in_score, $tg_in_score, $m_place)
    {
        if ($mb_in_score < 0 or $tg_in_score < 0) return 0;
        $inball = ($mb_in_score + $tg_in_score);
        switch ($inball % 2) {
            case 1:
                if ($m_place == 'ODD') {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 0:
                if ($m_place == 'EVEN') {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
        }
        $odds_eo = $grade;
        return $odds_eo;
    }
    //入球数计算:
    static function odds_t($mb_in_score, $tg_in_score, $m_place)
    {
        if ($mb_in_score < 0 or $tg_in_score < 0) return 0;
        $inball = $mb_in_score + $tg_in_score;
        if ($inball >= 0 and $inball <= 1) {
            $goin_place = "0~1";
        } else if ($inball >= 2 and $inball <= 3) {
            $goin_place = "2~3";
        } else if ($inball >= 4 and $inball <= 6) {
            $goin_place = "4~6";
        } else if ($inball >= 7) {
            $goin_place = "OVER";
        }
        if ($m_place == $goin_place) {
            $grade = 1;
        } else {
            $grade = -1;
        }
        $odds_t = $grade;
        return $odds_t;
    }
    //入球数计算:
    static function odds_bst($mb_in_score, $tg_in_score, $m_place)
    {
        if ($mb_in_score < 0 or $tg_in_score < 0) return 0;
        $inball = $mb_in_score + $tg_in_score;
        if ($inball >= 1 and $inball <= 2) {
            $goin_place = "1~2";
        } else if ($inball >= 3 and $inball <= 4) {
            $goin_place = "3~4";
        } else if ($inball >= 5 and $inball <= 6) {
            $goin_place = "5~6";
        } else if ($inball >= 5 and $inball <= 6) {
            $goin_place = "5~6";
        } else if ($inball >= 7 and $inball <= 8) {
            $goin_place = "7~8";
        } else if ($inball >= 9 and $inball <= 10) {
            $goin_place = "9~10";
        } else if ($inball >= 11 and $inball <= 12) {
            $goin_place = "11~12";
        } else if ($inball >= 13 and $inball <= 14) {
            $goin_place = "13~14";
        } else if ($inball >= 15 and $inball <= 16) {
            $goin_place = "15~16";
        } else if ($inball >= 17 and $inball <= 18) {
            $goin_place = "17~18";
        } else if ($inball >= 19) {
            $goin_place = "19UP";
        }
        if ($m_place == $goin_place) {
            $grade = 1;
        } else {
            $grade = -1;
        }
        $odds_bst = $grade;
        return $odds_bst;
    }
    //半全计算：
    static function odds_half($mb_in_score_v, $tg_in_score_v, $mb_in_score, $tg_in_score, $m_place)
    {
        if ($mb_in_score < 0 or $tg_in_score < 0) return 0;
        $grade = 0;
        if ($mb_in_score_v > $tg_in_score_v) {
            $m_w1 = "H";
        } elseif ($mb_in_score_v == $tg_in_score_v) {
            $m_w1 = "N";
        } else {
            $m_w1 = "C";
        }

        if ($mb_in_score > $tg_in_score) {
            $m_w2 = "H";
        } elseif ($mb_in_score == $tg_in_score) {
            $m_w2 = "N";
        } else {
            $m_w2 = "C";
        }
        $m_w = "F$m_w1$m_w2";
        if ($m_place == $m_w) {
            $grade = 1;
        } else {
            $grade = -1;
        }
        $odds_half = $grade;
        return $odds_half;
    }
    //独赢计算：
    static function win_chk($mbin, $tgin, $m_type)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        $grade = 0;
        switch ($m_type) {
            case 'MH':
                if ($mbin > $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'MC':
                if ($mbin < $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'MN':
                if ($mbin == $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
        }
        $win_chk = $grade;
        return $win_chk;
    }
    //上半独赢计算：
    static function win_chk_v($mbin, $tgin, $m_type)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        $grade = 0;
        switch ($m_type) {
            case 'VMH':
                if ($mbin > $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'VMC':
                if ($mbin < $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'VMN':
                if ($mbin == $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
        }
        $win_chk = $grade;
        return $win_chk;
    }
    //独赢计算：
    static function win_chk_rb($mbin, $tgin, $m_type)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        $grade = 0;
        switch ($m_type) {
            case 'RMH':
                if ($mbin > $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'RMC':
                if ($mbin < $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'RMN':
                if ($mbin == $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
        }
        $win_chk = $grade;
        return $win_chk;
    }
    //上半独赢计算：
    static function win_chk_vrb($mbin, $tgin, $m_type)
    {
        if ($mbin < 0 or $tgin < 0) return 0;
        $grade = 0;
        switch ($m_type) {
            case 'VRMH':
                if ($mbin > $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'VRMC':
                if ($mbin < $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
            case 'VRMN':
                if ($mbin == $tgin) {
                    $grade = 1;
                } else {
                    $grade = -1;
                }
                break;
        }
        $win_chk = $grade;
        return $win_chk;
    }
    //标准过关计算：
    static function odds_p($mid, $mtype, $mrate)
    {
        $winrate = 1;
        $mid = explode(',', $mid);
        $mtype = explode(',', $mtype);
        $rate1 = explode(',', $mrate);
        for ($i = 0; $i < sizeof($mid); $i++) {
            $result1 = DB::table('foot_match')->select('MB_Inball', 'TG_Inball')->where('ID', $mid[$i])->get();
            $rowr = $result1[0];
            $mb_in = $rowr['MB_Inball'];
            $tg_in = $rowr['TG_Inball'];
            if ($mb_in <> '' and $tg_in <> '') {
                $graded = win_chk($mb_in, $tg_in, $mtype[$i]);
                switch ($graded) {
                    case "1":
                        $winrate = $winrate * ($rate1[$i]);
                        break;
                    case "-1":
                        $winrate = 0;
                        break;
                    case "0":
                        $winrate = 0;
                        break;
                }
            } else {
                $winrate = 0;
            }
        }
        $odd_p = $winrate;
        return $odd_p;
    }
    //让球过关计算：
    static function odd_pr($mid, $mtype, $mrate, $mplace, $showtype)
    {
        $winrate = 1;
        $mid = explode(',', $mid);
        $mtype = explode(',', $mtype);
        $rate = explode(',', $mrate);
        $letb = explode(',', $mplace);
        $show = explode(',', $showtype);
        $cou = sizeof($mid);
        $count = 0;
        for ($i = 0; $i < $cou; $i++) {
            $result1 = DB::table('foot_match')->select('MB_Inball', 'TG_Inball')->where('ID', $mid[$i])->get();
            $rowr = $result1[0];
            $mb_in = $rowr['MB_Inball'];
            $tg_in = $rowr['TG_Inball'];
            $graded = letb_chk($mb_in, $tg_in, $show[$i], $letb[$i], $mtype[$i]);
            switch ($graded) {
                case "1":
                    $winrate = $winrate * (1 + $rate[i]);
                    break;
                case "-1":
                    $winrate = 0;
                    break;
                case "0":
                    $winrate = $winrate;
                    break;
                case "0.5":
                    $winrate = $winrate * (1 + $rate[i] / 2);
                    break;
                case "-0.5":
                    if ($count > 1) {
                        $winrate = 0;
                    } else {
                        $winrate = $winrate * (1 / 2);
                    }
                    $count = $count + 1;
                    break;
            }
        }
        $odd_pr = $winrate;
        return $odd_pr;
    }

    static function encrypt($input) {
        $size = mcrypt_get_block_size('des', 'ecb');
        $input = $this->pkcs5_pad($input, $size);
        $key = "Facai168Facai168";
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
         @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
         mcrypt_generic_deinit($td);
         mcrypt_module_close($td);
        $data = base64_encode($data);
        return preg_replace("/\s*/", '',$data);
    }

    static function decrypt($encrypted) {
        $key = 'Facai168Facai168'; // Replace with your own key
        $iv = ""; // Set the IV to all zeroes for ECB mode

        $encrypted = base64_decode($encrypted);
        if (!$encrypted) {
            error_log("Invalid base64 string: $encrypted");
            return false;
        }

        $decrypted = openssl_decrypt($encrypted, 'des-ecb', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) {
            $error = openssl_error_string();
            error_log("Decryption error: $error");
            return false;
        }

        // $decrypted = static::pkcs5_unpad($decrypted);
        // if ($decrypted === false) {
        //     error_log("Padding error");
        //     return false;
        // }

        return $decrypted;
    }

    static function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    static function pkcs5_unpad($text) {

        $pad_length = ord($text[strlen($text) - 1]);
        if ($pad_length > strlen($text)) {
            return false;
        }

        return substr($text, 0, -1 * $pad_length);
    }    
}
