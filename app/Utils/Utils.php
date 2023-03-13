<?php

namespace App\Utils;

use App\Models\Web\UpdateLog;
use App\Models\User;
use DB;
class Utils {
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
}
