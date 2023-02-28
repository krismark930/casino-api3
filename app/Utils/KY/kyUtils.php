<?php

namespace App\Utils\KY;

use App\Utils\KY\DES;

class KYUtils {

    var $agent;
    var $md5Key;
    var $aesKey;
    var $apiUrl="https://api.dxx28.com/channelHandle";  //正式环境
    var $apiUrl2="https://wc1-api.hddv1.com/channelHandle";  //测试环境

    function KYUtils($row) {
        $this->agent = $row['KY_Agent'];
        $this->md5Key = $row['KY_md5key'];
        $this->aesKey = $row['KY_aeskey'];
     }
    function Add_KY_member($username){  //成功返回1 失败返回0

        $aes = new DES();
        $lineCode=$_SERVER['HTTP_HOST'];
        $timestamp = str_pad($aes->getMillisecond(),13,0);	//时间戳
        $orderid = $aes->getOrderId($this->agent);	//订单号
        $loginip = $this->getip_KY();
        $params="s=0&account=$username&money=0&orderid=$orderid&ip=$loginip&lineCode=$lineCode&KindID=0";
        $aes->set_key($this->aesKey);
        $aes->require_pkcs5();
        $param = urlencode($aes->encrypt($params));	//参数加密字符串
        $key = md5 ($this->agent.$timestamp.$this->md5Key);	//MD5校验字符串
        $url=$this->apiUrl.'?agent='.$this->agent.'&timestamp='.$timestamp.'&param='.$param.'&key='.$key;
        if($_GET['debug']==2){echo $url;exit;}
        $jsonStr=$this->getUrl_KY($url);
        $jsonData=json_decode($jsonStr,true);
        if($_GET['debug']=='1'){print_r($jsonData);exit;}
        if($jsonData['d']['code']==0 and count($jsonData,1)>1){
            return 1;
        }else{
            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/ky_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n会员开户\r\n$jsonStr\r\n\r\n");
            fclose($f);
            return 0;
        }
    }


    function KY_isOnline($username){  //检查玩家是否在线  -1不存在 0不在线 1在线 2封停
        global $agent;
        global $apiUrl;
        global $md5Key;
        global $aesKey;
        $aes = new DES();
        $lineCode=$_SERVER['HTTP_HOST'];
        $timestamp = str_pad($aes->getMillisecond(),13,0);	//时间戳
        $params="s=5&account=$username";
        $aes->set_key($aesKey);
        $aes->require_pkcs5();
        $param = urlencode($aes->encrypt($params));	//参数加密字符串
        $key = md5 ($agent.$timestamp.$md5Key);	//MD5校验字符串
        $url=$apiUrl.'?agent='.$agent.'&timestamp='.$timestamp.'&param='.$param.'&key='.$key;
        if($_GET['debug']==2){echo $url;exit;}
        $jsonStr=$this->getUrl_KY($url);
        $jsonData=json_decode($jsonStr,true);
        if($_GET['debug']=='1'){print_r($jsonData);exit;}
        if($jsonData['d']['code']==0 and count($jsonData,1)>1){
            return $jsonData['d']['status'];
        }else{
            return 0;
        }
    }

    function KY_Deposit($username,$Gold,$tp="IN"){  //成功返回1 失败返回0
        $aes = new DES();
        $lineCode=$_SERVER['HTTP_HOST'];
        $timestamp = str_pad($aes->getMillisecond(),13,0);	//时间戳
        $orderid = $aes->getOrderId($this->agent);	//订单号
        $result=array();
        $result['billno']=$orderid;
        if($tp=='IN'){
            $params="s=2&account=$username&money=$Gold&orderid=$orderid";
        }else{
            if($tp=='OUT'){
                $params="s=3&account=$username&money=$Gold&orderid=$orderid";
            }else{
                $result['result']=0;
                return $result;
            }
        }
        $aes->set_key($this->aesKey);
        $aes->require_pkcs5();
        $param = urlencode($aes->encrypt($params));	//参数加密字符串
        $key = md5 ($this->agent.$timestamp.$this->md5Key);	//MD5校验字符串
        $url=$this->apiUrl.'?agent='.$this->agent.'&timestamp='.$timestamp.'&param='.$param.'&key='.$key;
        if($_GET['debug']==2){echo $url;exit;}
        $jsonStr=$this->getUrl_KY($url);
        $time=date("Y-m-d H:i:s");
        $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/ky_".date("Ymd").".txt";
        $f=fopen($tmpfile,'a');
        if($tp=='IN'){
            fwrite($f,$time."转入\r\n会员号:$username  金额:$Gold  定单号:$orderid\r\n$jsonStr\r\n\r\n");
        }else{
            fwrite($f,$time."转出\r\n会员号:$username  金额:$Gold  定单号:$orderid\r\n$jsonStr\r\n\r\n");
        }
        fclose($f);
        $jsonData=json_decode($jsonStr,true);
        if($_GET['debug']=='1'){print_r($jsonData);exit;}
        if($jsonData['d']['code']==0 and count($jsonData,1)>1){
            $result['result']=1;
        }else{
            $result['result']=0;
        }
        return $result;
    }


    function KY_Money($username){  //成功返回余额，失败返回0
        global $agent;
        global $apiUrl;
        global $md5Key;
        global $aesKey;
        $aes = new DES();
        $lineCode=$_SERVER['HTTP_HOST'];
        $timestamp = str_pad($aes->getMillisecond(),13,0);	//时间戳
        $params="s=1&account=$username";
        $aes->set_key($aesKey);
        $aes->require_pkcs5();
        $param = urlencode($aes->encrypt($params));	//参数加密字符串
        $key = md5 ($agent.$timestamp.$md5Key);	//MD5校验字符串
        $url=$apiUrl.'?agent='.$agent.'&timestamp='.$timestamp.'&param='.$param.'&key='.$key;
        if($_GET['debug']==2){echo $url;exit;}
        $jsonStr=$this->getUrl_KY($url);
        $jsonData=json_decode($jsonStr,true);
        if($_GET['debug']=='1'){print_r($jsonData);exit;}
        if($jsonData['d']['code']==0 and count($jsonData,1)>1){
            return $jsonData['d']['money'];
        }else{
            return 0;
        }
    }

    function KY_Money2($username,$type=2){  //成功返回数组，失败返回0
        global $agent;
        global $apiUrl;
        global $md5Key;
        global $aesKey;
        $aes = new DES();
        $lineCode=$_SERVER['HTTP_HOST'];
        $timestamp = str_pad($aes->getMillisecond(),13,0);	//时间戳
        $params="s=7&account=$username";
        $aes->set_key($aesKey);
        $aes->require_pkcs5();
        $param = urlencode($aes->encrypt($params));	//参数加密字符串
        $key = md5 ($agent.$timestamp.$md5Key);	//MD5校验字符串
        $url=$apiUrl.'?agent='.$agent.'&timestamp='.$timestamp.'&param='.$param.'&key='.$key;
        if($_GET['debug']==2){echo $url;exit;}
        $jsonStr=$this->getUrl_KY($url);
        $jsonData=json_decode($jsonStr,true);
        if($_GET['debug']=='1'){print_r($jsonData);exit;}
        if($jsonData['d']['code']==0 and count($jsonData,1)>1){
            if($type==1){
                return $jsonData['d']['totalMoney'];  //总余额
            }elseif($type==2){
                return $jsonData['d']['freeMoney'];  //可用余额
            }elseif($type==3){
                return $jsonData['d']['status'];  //在线状态 -1不存在 0不在线 1在线 2封停
            }elseif($type==4){
                return $jsonData['d']['gameStatus'];  //游戏状态 0不在游戏中  1正在游戏中
            }elseif($type==5){
                return $jsonData['d']['account'];  //游戏账号
            }
        }else{
            return 0;
        }
    }

    function KY_Money3(){  //成功返回金额，失败返回0
        global $agent;
        global $apiUrl;
        global $md5Key;
        global $aesKey;
        $aes = new DES();
        $lineCode=$_SERVER['HTTP_HOST'];
        $timestamp = str_pad($aes->getMillisecond(),13,0);	//时间戳
        $params="s=14";
        $aes->set_key($aesKey);
        $aes->require_pkcs5();
        $param = urlencode($aes->encrypt($params));	//参数加密字符串
        $key = md5 ($agent.$timestamp.$md5Key);	//MD5校验字符串
        $url=$apiUrl.'?agent='.$agent.'&timestamp='.$timestamp.'&param='.$param.'&key='.$key;
        if($_GET['debug']==2){echo $url;exit;}
        $jsonStr=$this->getUrl_KY($url);
        $jsonData=json_decode($jsonStr,true);
        if($_GET['debug']=='1'){print_r($jsonData);exit;}
        if($jsonData['d']['code']==0 and count($jsonData,1)>1){
            return $jsonData['d']['money'];
        }else{
            return 0;
        }
    }

    function KY_GameUrl($username,$KindID=0){  //成功返回网址，失败返回空值
        global $agent;
        global $apiUrl;
        global $md5Key;
        global $aesKey;
        $aes = new DES();
        $lineCode=$_SERVER['HTTP_HOST'];
        $timestamp = str_pad($aes->getMillisecond(),13,0);	//时间戳
        $orderid = $aes->getOrderId($agent);	//订单号
        $loginip=$this->getip_KY();
        $params="s=0&account=$username&money=0&orderid=$orderid&ip=$loginip&lineCode=$lineCode&KindID=$KindID";
        $aes->set_key($aesKey);
        $aes->require_pkcs5();
        $param = urlencode($aes->encrypt($params));	//参数加密字符串
        $key = md5 ($agent.$timestamp.$md5Key);	//MD5校验字符串
        $url=$apiUrl.'?agent='.$agent.'&timestamp='.$timestamp.'&param='.$param.'&key='.$key;
        if($_GET['debug']==2){echo $url;exit;}
        $jsonStr=$this->getUrl_KY($url);
        $jsonData=json_decode($jsonStr,true);
        if($_GET['debug']=='1'){print_r($jsonData);exit;}
        if($jsonData['d']['code']==0 and count($jsonData,1)>1){
            $url=$jsonData['d']['url'];
            if($KindID>0) $url.='&jumpType=0';  //直接进入游戏不显示大厅
            return $url;
        }else{
            return "";
        }
    }

    function KY_GameUrl2($money=2000,$KindID=0){  //成功返回网址，失败返回空值
        global $apiUrl2;
        $agent='70948';$aesKey='F0646A04C0C11EF7';$md5Key='9FD736F9E1478C14';
        $aes = new DES();
        $username=$this->getpassword_KY(10);
        $lineCode=$_SERVER['HTTP_HOST'];
        $timestamp = str_pad($aes->getMillisecond(),13,0);	//时间戳
        $orderid = $aes->getOrderId($agent);	//订单号
        $loginip=$this->getip_KY();
        $params="s=0&account=$username&money=$money&orderid=$orderid&ip=$loginip&lineCode=$lineCode&KindID=$KindID";
        $aes->set_key($aesKey);
        $aes->require_pkcs5();
        $param = urlencode($aes->encrypt($params));	//参数加密字符串
        $key = md5 ($agent.$timestamp.$md5Key);	//MD5校验字符串
        $url=$apiUrl2.'?agent='.$agent.'&timestamp='.$timestamp.'&param='.$param.'&key='.$key;
        if($_GET['debug']==2){echo $url;exit;}
        $jsonStr=$this->getUrl_KY($url);
        $jsonData=json_decode($jsonStr,true);
        if($_GET['debug']=='1'){print_r($jsonData);exit;}
        if($jsonData['d']['code']==0 and count($jsonData,1)>1){
            return $jsonData['d']['url'];
        }else{
            return "";
        }
    }

    function Get_Orderid_Status($orderid,$type=1){  //检查定单状态 -88 网络故障  －1不存在 1成功 2失败 3处理中
        global $agent;
        global $apiUrl;
        global $md5Key;
        global $aesKey;
        $aes = new DES();
        $lineCode=$_SERVER['HTTP_HOST'];
        $timestamp = str_pad($aes->getMillisecond(),13,0);	//时间戳
        $params="s=4&orderid=$orderid";
        $aes->set_key($aesKey);
        $aes->require_pkcs5();
        $param = urlencode($aes->encrypt($params));	//参数加密字符串
        $key = md5 ($agent.$timestamp.$md5Key);	//MD5校验字符串
        $url=$apiUrl.'?agent='.$agent.'&timestamp='.$timestamp.'&param='.$param.'&key='.$key;
        if($_GET['debug']==2){echo $url;exit;}
        $jsonStr=$this->getUrl_KY($url);
        $jsonData=json_decode($jsonStr,true);
        if($_GET['debug']=='1'){print_r($jsonData);exit;}
        if($jsonData['d']['status']==0 and count($jsonData,1)>1) $jsonData['d']['status']=1;  //成功改为1
        if(strlen($jsonStr)<5) return -88;
        if($type==1){
            return $jsonData['d']['status'];  //返回状态
        }elseif($type==2){
            return $jsonData['d']['money'];  //返回金额
        }else{
            return -1;
        }
    }

    function KY_Offline($username){  //成功1，失败返回0
        global $agent;
        global $apiUrl;
        global $md5Key;
        global $aesKey;
        $aes = new DES();
        $lineCode=$_SERVER['HTTP_HOST'];
        $timestamp = str_pad($aes->getMillisecond(),13,0);	//时间戳
        $params="s=8&account=$username";
        $aes->set_key($aesKey);
        $aes->require_pkcs5();
        $param = urlencode($aes->encrypt($params));	//参数加密字符串
        $key = md5 ($agent.$timestamp.$md5Key);	//MD5校验字符串
        $url=$apiUrl.'?agent='.$agent.'&timestamp='.$timestamp.'&param='.$param.'&key='.$key;
        if($_GET['debug']==2){echo $url;exit;}
        $jsonStr=$this->getUrl_KY($url);
        $jsonData=json_decode($jsonStr,true);
        if($_GET['debug']=='1'){print_r($jsonData);exit;}
        if($jsonData['d']['code']==0 and count($jsonData,1)>1){
            return 1;
        }else{
            return 0;
        }
    }


    function getUrl_KY($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);  //超时60秒
        //curl_setopt($ch, CURLOPT_USERAGENT, ' WEB_LIB_GI_'.$AG_agent);  //设置浏览器类型，含代理号
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        return $html;
    }

    function getpassword_KY($len=10){
     $key='0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
     $kk=explode(",",$key);
     $pass="";
     for($i=1;$i<=$len;$i++){
         $pass=$pass.$kk[mt_rand(0,sizeof($kk)-1)];
     }
     return $pass;
    }


    function getip_KY(){
        if($_SERVER['HTTP_X_FORWARDED_FOR']){
            $onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $c_agentip=1;
        }elseif($_SERVER['HTTP_CLIENT_IP']){
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
}
