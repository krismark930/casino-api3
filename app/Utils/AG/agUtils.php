<?php

namespace App\Utils\AG;

use App\Utils\AG\des;

class AGUtils {

    var $AG_agent;
    var $md5key;
    var $deskey;
    var $giurl;
    var $gciurl;

    public function __construct($sysConfig) {
        $this->AG_agent = env('AG_AGENT');
        $this->md5key = env('AG_MD5KEY');
        $this->deskey = env('AG_DESKEY');
        $this->giurl = env('AG_GI_URL');
        $this->gciurl = env('AG_GCI_URL');
    }

    function Addmember($username,$password,$tp=1){
        $crypt = new DES($this->deskey);
        $para="cagent=".$this->AG_agent."/\\\\/loginname=".$username."/\\\\/method=lg/\\\\/actype=".$tp."/\\\\/password=".$password."/\\\\/oddtype=A/\\\\/cur=CNY";
        $params=$crypt->encrypt($para);
        $key=md5($params.$this->md5key);
        $url=$this->giurl."doBusiness.do?params=".$params."&key=".$key;
        // return $url;
        $xmlcode=$this->getUrl($url);
        // return $xmlcode;
        $result=$this->getResult($xmlcode);
        if($result['info']<>'0'){
            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/storage/tmp/ag_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n会员开户\r\n$xmlcode\r\n\r\n");
            fclose($f);
        }
        return $result;
    }

    function getGameUrl($username,$password,$oddtype="A",$dm="www.bbin-api.com",$tp=1,$gameType=1){
        $crypt = new DES($this->deskey);
        $para="cagent=".$this->AG_agent."/\\\\/loginname=".$username."/\\\\/actype=".$tp."/\\\\/password=".$password."/\\\\/dm=".$dm."/\\\\/sid=".$this->AG_agent.date("ymdhis").rand(1000,9999)."/\\\\/lang=1/\\\\/gameType=".$gameType."/\\\\/oddtype=".$oddtype."/\\\\/cur=CNY";
        $params=$crypt->encrypt($para);
        $key=md5($params.$this->md5key);
        // return array("key" => $this->deskey, "params" => $params);
        $url=$this->gciurl."forwardGame.do?params=".$params."&key=".$key;
        return  $url;
    }

    function getMoney($username,$password,$tp=1){
        $crypt = new DES($this->deskey);
        $para="cagent=".$this->AG_agent."/\\\\/loginname=".$username."/\\\\/method=gb/\\\\/actype=".$tp."/\\\\/password=".$password."/\\\\/cur=CNY";
        $params=$crypt->encrypt($para);
        $key=md5($params.$this->md5key);
        $url=$this->giurl."doBusiness.do?params=".$params."&key=".$key;
        $xmlcode=$this->getUrl($url);
        $result=$this->getResult($xmlcode);
        return  intval($result['info']);
    }

    // d存款 w提款 vd VIP存款 vw VIP提款
    function Deposit($username,$password,$Gold,$tp="IN")
    {
        $crypt = new DES($this->deskey);
        $billno=date("YmdHis").rand(1000,9999);
        if($tp=="IN"){
            $billno=$billno."8";
        }else{
            $billno=$billno."0";
        }
        $para="cagent=".$this->AG_agent."/\\\\/method=tc/\\\\/loginname=".$username."/\\\\/billno=".$billno."/\\\\/type=".$tp."/\\\\/credit=".number_format($Gold,2,null,"")."/\\\\/actype=1/\\\\/password=".$password."/\\\\/cur=CNY";
        $params=$crypt->encrypt($para);
        $key=md5($params.$this->md5key);
        $url=$this->giurl."doBusiness.do?params=".$params."&key=".$key;
        $xmlcode=$this->getUrl($url);

        $t=date("Y-m-d H:i:s");
        $tmpfile=$_SERVER['DOCUMENT_ROOT']."/storage/tmp/ag_".date("Ymd").".txt";
        $f=fopen($tmpfile,'a');
        fwrite($f,"预转账$t\r\n会员号:$username  金额:$Gold  定单号:$billno\r\n$xmlcode\r\n\r\n");
        fclose($f);

        $result=$this->getResult($xmlcode);
        if($result['info']=='0' and $result['msg']==""){
            $para="cagent=".$this->AG_agent."/\\\\/loginname=".$username."/\\\\/method=tcc/\\\\/billno=".$billno."/\\\\/type=".$tp."/\\\\/credit=".number_format($Gold,2,null,"")."/\\\\/actype=1/\\\\/flag=1/\\\\/password=".$password."/\\\\/cur=CNY";
            $params=$crypt->encrypt($para);
            $key=md5($params.$this->md5key);
            $url=$this->giurl."doBusiness.do?params=".$params."&key=".$key;
            unset($xmlcode);
            $xmlcode=$this->getUrl($url);

            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/storage/tmp/ag_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,"确认$t\r\n会员号:$username  金额:".$Gold."  定单号:$billno\r\n$xmlcode\r\n\r\n");
            fclose($f);

            unset($result);
            $result=$this->getResult($xmlcode);
        }
        $result['billno']=$billno;
        return $result;
    }

    function QosBillno($billno){
        $crypt = new DES($this->deskey);
        $para="cagent=".$this->AG_agent."/\\\\/billno=".$billno."/\\\\/method=qos/\\\\/actype=1/\\\\/cur=CNY";
        $params=$crypt->encrypt($para);
        $key=md5($params.$this->md5key);
        $url=$this->giurl."doBusiness.do?params=".$params."&key=".$key;
        $xmlcode=$this->getUrl($url);
        return $this->getResult($xmlcode);
    }

    function getUrl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);  //超时60秒
        curl_setopt($ch, CURLOPT_USERAGENT, ' WEB_LIB_GI_'.$this->AG_agent);  //设置浏览器类型，含代理号
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 2);
        $html = curl_exec($ch);
        return $html;
    }

    function getTransactionUrl($url, $ip=null, $timeout=20) {
        $ch = curl_init();

        //需要获取的URL地址，也可以在PHP的curl_init()函数中设置
        curl_setopt($ch, CURLOPT_URL,$url);

        //启用时会设置HTTP的method为GET，因为GET是默认是，所以只在被修改的情况下使用s
        curl_setopt($ch, CURLOPT_HTTPGET,true);

        //在启用CURLOPT_RETURNTRANSFER时候将获取数据返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

        //bind to specific ip address if it is sent trough arguments
        if($ip)
        {
            //在外部网络接口中使用的名称，可以是一个接口名，IP或者主机名
            curl_setopt($ch,CURLOPT_INTERFACE,$ip);
        }

        //设置curl允许执行的最长秒数  $timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        //执行一个curl会话
        $result = curl_exec($ch);

        curl_close($ch);

        if(curl_errno($ch)) {
            return false;
        } else {
            return $result;
        }
    }

    function getResult($content){
        $info=$this->getContent($content,'info="','"',1);
        $msg=$this->getContent($content,'msg="','"',1);
        $result=array();
        $result['info']=$info;
        $result['msg']=$msg;
        if($content=="" or !strpos($content,'info=')>0){
            $result['info']='error';
            $result['msg']='网络异常!';
        }
        return $result;
    }

    function getContent($sourceStr,$star,$end,$flag ){
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

    function getpassword($len=10)
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
