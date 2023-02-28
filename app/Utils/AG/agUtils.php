<?php

namespace App\Utils\AG;

use App\Utils\AG\DES1;

class AGUtils {

    static function Addmember($username,$password,$tp=1){
        global $AG_agent;
        global $giurl;
        global $md5key;
        global $deskey;
        $crypt = new DES1($deskey);
        $para="cagent=".$AG_agent."/\\\\/loginname=".$username."/\\\\/method=lg/\\\\/actype=".$tp."/\\\/password=".$password."/\\\\/oddtype=A/\\\\/cur=CNY";
        $params=$crypt->encrypt($para);
        $key=md5($params.$md5key);
        $url=$giurl."doBusiness.do?params=".$params."&key=".$key;
        if($_GET['debug']=='2'){echo $url;exit;}
        $xmlcode=AGUtils::getUrl($url);
        if($_GET['debug']=='1'){echo $xmlcode;exit;}
        $result=AGUtils::getResult($xmlcode);
        if($result['info']<>'0'){
            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/ag_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n会员开户\r\n$xmlcode\r\n\r\n");
            fclose($f);
        }
        return $result;
    }

    static function getGameUrl($username,$password,$oddtype="A",$dm="www.pj6678.com",$tp=1,$gameType=1){
        global $AG_agent;
        global $gciurl;
        global $md5key;
        global $deskey;
        $crypt = new DES1($deskey);
        $para="cagent=".$AG_agent."/\\\\/loginname=".$username."/\\\\/actype=".$tp."/\\\\/password=".$password."/\\\\/dm=".$dm."/\\\\/sid=".$AG_agent.date("ymdhis").rand(1000,9999)."/\\\\/lang=1/\\\\/gameType=".$gameType."/\\\\/oddtype=".$oddtype."/\\\\/cur=CNY";
        $params=$crypt->encrypt($para);
        $key=md5($params.$md5key);
        $url=$gciurl."forwardGame.do?params=".$params."&key=".$key;
        if($_GET['debug']=='2'){echo $url;exit;}
        return  $url;
    }

    static function getMoney($username,$password,$tp=1){
        global $AG_agent;
        global $giurl;
        global $md5key;
        global $deskey;
        $crypt = new DES1($deskey);
        $para="cagent=".$AG_agent."/\\\\/loginname=".$username."/\\\\/method=gb/\\\\/actype=".$tp."/\\\\/password=".$password."/\\\\/cur=CNY";
        $params=$crypt->encrypt($para);
        $key=md5($params.$md5key);
        $url=$giurl."doBusiness.do?params=".$params."&key=".$key;
        if($_GET['debug']=='2'){echo $url;exit;}
        $xmlcode=AGUtils::getUrl($url);
        if($_GET['debug']=='1'){echo $xmlcode;exit;}
        $result=AGUtils::getResult($xmlcode);
        return  intval($result['info']);
    }

    static function Deposit($username,$password,$Gold,$tp="IN") // d存款 w提款 vd VIP存款 vw VIP提款
    {
        global $AG_agent;
        global $giurl;
        global $md5key;
        global $deskey;
        $crypt = new DES1($deskey);
        $billno=date("YmdHis").rand(1000,9999);
        if($tp=="IN"){
            $billno=$billno."8";
        }else{
            $billno=$billno."0";
        }
        $para="cagent=".$AG_agent."/\\\\/method=tc/\\\\/loginname=".$username."/\\\\/billno=".$billno."/\\\\/type=".$tp."/\\\\/credit=".number_format($Gold,2,null,"")."/\\\\/actype=1/\\\\/password=".$password."/\\\\/cur=CNY";
        $params=$crypt->encrypt($para);
        $key=md5($params.$md5key);
        $url=$giurl."doBusiness.do?params=".$params."&key=".$key;
        if($_GET['debug']=='2'){echo $url;exit;}
        $xmlcode=AGUtils::getUrl($url);

        $t=date("Y-m-d H:i:s");
        $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/ag_".date("Ymd").".txt";
        $f=fopen($tmpfile,'a');
        fwrite($f,"预转账$t\r\n会员号:$username  金额:$Gold  定单号:$billno\r\n$xmlcode\r\n\r\n");
        fclose($f);

        $result=AGUtils::getResult($xmlcode);
        if($result['info']=='0' and $result['msg']==""){
            $para="cagent=".$AG_agent."/\\\\/loginname=".$username."/\\\\/method=tcc/\\\\/billno=".$billno."/\\\\/type=".$tp."/\\\\/credit=".number_format($Gold,2,null,"")."/\\\\/actype=1/\\\\/flag=1/\\\\/password=".$password."/\\\\/cur=CNY";
            $params=$crypt->encrypt($para);
            $key=md5($params.$md5key);
            $url=$giurl."doBusiness.do?params=".$params."&key=".$key;
            unset($xmlcode);
            $xmlcode=AGUtils::getUrl($url);

            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/ag_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,"确认$t\r\n会员号:$username  金额:".$Gold."  定单号:$billno\r\n$xmlcode\r\n\r\n");
            fclose($f);

            unset($result);
            $result=AGUtils::getResult($xmlcode);
        }
        $result['billno']=$billno;
        return $result;
    }

    static function QosBillno($billno){
        global $AG_agent;
        global $giurl;
        global $md5key;
        global $deskey;
        $crypt = new DES1($deskey);
        $para="cagent=".$AG_agent."/\\\\/billno=".$billno."/\\\\/method=qos/\\\\/actype=1/\\\\/cur=CNY";
        $params=$crypt->encrypt($para);
        $key=md5($params.$md5key);
        $url=$giurl."doBusiness.do?params=".$params."&key=".$key;
        if($_GET['debug']=='2'){echo $url;exit;}
        $xmlcode=AGUtils::getUrl($url);
        if($_GET['debug']=='1'){echo $xmlcode;exit;}
        return AGUtils::getResult($xmlcode);
    }

    static function getUrl($url){
        global $AG_agent;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);  //超时60秒
        curl_setopt($ch, CURLOPT_USERAGENT, ' WEB_LIB_GI_'.$AG_agent);  //设置浏览器类型，含代理号
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        return $html;
    }

    static function getResult($content){
        $info=AGUtils::getContent($content,'info="','"',1);
        $msg=AGUtils::getContent($content,'msg="','"',1);
        $result=array();
        $result['info']=$info;
        $result['msg']=$msg;
        if($content=="" or !strpos($content,'info=')>0){
            $result['info']='error';
            $result['msg']='网络异常!';
        }
        return $result;
    }

    static function getContent($sourceStr,$star,$end,$flag ){
      switch ($flag) {
        case 0:  //取指定字符前面的
            echo strrpos( $sourceStr, $end );
            echo '-----'.strlen( $end );
            $content = substr( $sourceStr, 0, strrpos( $sourceStr, $end ) + strlen( $end ) );
            break;
        case 1:  //取指定字符之间的,不包括指定字符
            $content = substr( $sourceStr, strpos( $sourceStr,$star)+ strlen( $star ));
            $content = substr( $content, 0, strpos( $content, $end ) );
            break;
        case 2:  //取指定字符之间的，包括指定字符
            $content =strstr( $sourceStr, $star );
            $content = substr( $content, 0, strpos( $content, $end ) + strlen( $end ) );
            break;
        case 3:  //取指定字符之后的，不包括指定字符
            $content = substr( $sourceStr, strrpos( $sourceStr,$star)+ strlen( $star ));
            break;
        case 4:  //取指定字符之后的，包括指定字符
            $content =strstr( $sourceStr, $star );
            break;
      }
      return $content;
    }

    static function getpassword($len=10)
    {
     $key='0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
     $kk=explode(",",$key);
     $pass="";
     for($i=1;$i<=$len;$i++){
         $pass=$pass.$kk[mt_rand(0,sizeof($kk)-1)];
     }
     return $pass;
    }
}
