<?php

namespace App\Utils\PT;

use App\Models\Web\Sys800;
use App\Utils\AG\DES1;
use App\Utils\Utils;
use Exception;
use Illuminate\Support\Facades\DB;

class PTUtils {

    var $X_Operator;
    var $X_Key;
    var $OG_Token;
    var $Token_Uptime;
    var $ApiUrl="https://mog326.haa477.com"; //服务APIURL
    var $ApiUrl2='https://tigerapi.oriental-game.com:38888  ';  //获取数据APIURL

    function OGUtils($sysConfig) {
        $this->X_Operator=$sysConfig['OG_Agent'];
        $this->X_Key=$sysConfig['OG_Key'];
        $this->OG_Token=$sysConfig['OG_Token'];
        $this->Token_Uptime=$sysConfig['Token_Uptime'];
     }
    function Add_OG_Member($username,$fullname='',$email=''){  //开户
        $postdata=array();
        $postdata['username']=$username;
        $postdata['country']='china';
        if($fullname){
            $postdata['fullname']=$fullname;
        }else{
            $postdata['fullname']=$username;
        }
        if($email){
            $postdata['email']=$email;
        }else{
            $postdata['email']=$username.'@qq.com';
        }
        $postdata['language']='cn';
        $postdata['birthdate']=date("Y-m-d");

        $OG_Host=str_replace('https://','', $this->ApiUrl);
        $OG_Host=str_replace('http://','',$OG_Host);
        $header = array();
        $header[] = 'Host: '.$OG_Host;
        $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0 FirePHP/0.7.4';
        $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $header[] = 'Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3';
        $header[] = 'Referer: '. $this->ApiUrl.'/';
        $header[] = 'x-insight: activate';
        $header[] = 'Connection: keep-alive';
        $header[] = 'X-Token: '. $this->OG_Token;
        $url= $this->ApiUrl.'/register';
        $htmlcode= $this->curl_info_s($url,null,null,$postdata, $this->ApiUrl,$header);
        //echo $htmlcode;exit;
        $json_data=json_decode($htmlcode,true);
        //print_r($json_data);exit;
        if($json_data['status']<>'success'){
            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/og_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n会员开户\r\n$htmlcode\r\n\r\n");
            fclose($f);
            return 0;
        }else{
            return 1;
        }
    }

    function OG_GameUrl($username){  //获取游戏连接

        $OG_Host=str_replace('https://','', $this->ApiUrl);
        $OG_Host=str_replace('http://','',$OG_Host);
        $header = array();
        $header[] = 'Host: '.$OG_Host;
        $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0 FirePHP/0.7.4';
        $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $header[] = 'Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3';
        $header[] = 'Referer: '. $this->ApiUrl.'/';
        $header[] = 'x-insight: activate';
        $header[] = 'Connection: keep-alive';
        $header[] = 'X-Token: '. $this->OG_Token;
        $url= $this->ApiUrl.'/game-providers/30/games/ogplus/key?username='.$username;  //获取游戏key
        $htmlcode= $this->getUrl_OG($url,15,$header);
        $json_data=json_decode($htmlcode,true);
        if($json_data['status']<>'success'){
            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/og_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n获取游戏金钥\r\n$htmlcode\r\n\r\n");
            fclose($f);
            return '';
        }
        $key=$json_data['data']['key'];
        if(Utils::Mobile()){
            $type='mobile';
        }else{
            $type='desktop';
        }
        $url= $this->ApiUrl.'/game-providers/30/play?key='.$key.'&type='.$type;  //获取游戏key
        $htmlcode= $this->getUrl_OG($url,15);
        $json_data=json_decode($htmlcode,true);
        //print_r($json_data);exit;
        if($json_data['status']<>'success'){
            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/og_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n获取游戏网址\r\n$htmlcode\r\n\r\n");
            fclose($f);
            return '';
        }else{
            return $json_data['data']['url'];
        }
    }

    function OG_GameUrl2(){  //试玩
        $this->ApiUrl="https://marsapi-test.oriental-game.com:8443"; //测试环境
        $this->X_Operator='mog326jty';
        $this->X_Key='DHqJTQRSjQvwZjFC';
        $this->GetToken();  //获取测试环境Token
        $username= $this->getpassword_OG();
        $result= $this->Add_OG_Member($username,"TryAccount",$username."@qq.com");
        if($result==0) return '';
        $result= $this->OG_Deposit($username,'',10000,$action="IN");
        if($result['result']==0) return '';
        $GameUrl= $this->OG_GameUrl($username);
        return $GameUrl;
    }

    function OG_Money($username,$password=''){  //获取余额
        global $ApiUrl,$OG_Token;
        $OG_Host=str_replace('https://','',$ApiUrl);
        $OG_Host=str_replace('http://','',$OG_Host);
        $header = array();
        $header[] = 'Host: '.$OG_Host;
        $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0 FirePHP/0.7.4';
        $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $header[] = 'Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3';
        $header[] = 'Referer: '.$ApiUrl.'/';
        $header[] = 'x-insight: activate';
        $header[] = 'Connection: keep-alive';
        $header[] = 'X-Token: '.$OG_Token;
        $url=$ApiUrl.'/game-providers/30/balance?username='.$username;
        $htmlcode= $this->getUrl_OG($url,15,$header);
        $json_data=json_decode($htmlcode,true);
        if($json_data['status']<>'success'){
            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/og_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n获取余额\r\n$htmlcode\r\n\r\n");
            fclose($f);
            return 0;
        }else{
            return $json_data['data']['balance'];
        }
    }

    function OG_Deposit($username,$transferId,$balance,$action="IN"){  //转换额度 transferId定单号  balance金额
        global $ApiUrl,$OG_Token;
        $OG_Host=str_replace('https://','',$ApiUrl);
        $OG_Host=str_replace('http://','',$OG_Host);
        $header = array();
        $header[] = 'Host: '.$OG_Host;
        $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0 FirePHP/0.7.4';
        $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $header[] = 'Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3';
        $header[] = 'Referer: '.$ApiUrl.'/';
        $header[] = 'x-insight: activate';
        $header[] = 'Connection: keep-alive';
        $header[] = 'X-Token: '.$OG_Token;
        if($transferId=='') $transferId=substr($action,0,1).date("YmdHis").mt_rand(1000,9999);
        $postdata=array();
        $postdata['username']=$username;
        $postdata['balance']=$balance;
        $postdata['action']=$action;
        $postdata['transferId']=$transferId;
        $url=$ApiUrl.'/game-providers/30/balance';
        $htmlcode= $this->curl_info_s($url,null,null,$postdata,$ApiUrl,$header);
        $json_data=json_decode($htmlcode,true);
        //print_r($json_data);exit;
        $result=array();
        $result['billno']=$transferId;
        if($json_data['status']<>'success'){
            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/og_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n转账\r\n$htmlcode\r\n\r\n");
            fclose($f);
            $result['result']=0;
        }else{
            $result['result']=1;
        }
        return $result;
    }


    function OG_QosBillno($username,$transferId, $action = ''){  //查询额度转换定单状态
        $OG_Host=str_replace('https://','',$this->ApiUrl);
        $OG_Host=str_replace('http://','',$OG_Host);
        $header = array();
        $header[] = 'Host: '.$OG_Host;
        $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0 FirePHP/0.7.4';
        $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $header[] = 'Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3';
        $header[] = 'Referer: '.$this->ApiUrl.'/';
        $header[] = 'x-insight: activate';
        $header[] = 'Connection: keep-alive';
        $header[] = 'X-Token: '.$this->OG_Token;
        if($transferId=='') $transferId=substr($action,0,1).date("YmdHis").mt_rand(1000,9999);
        $postdata=array();
        $postdata['username']=$username;
        $postdata['transferId']=$transferId;
        $url=$this->ApiUrl.'/game-providers/30/confirm-transfer';
        $htmlcode= $this->curl_info_s($url,null,null,$postdata,$this->ApiUrl,$header);
        $json_data=json_decode($htmlcode,true);
        if($json_data['status']<>'success'){
            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/og_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n转账查询\r\n$htmlcode\r\n\r\n");
            fclose($f);
            return 0;
        }else{
            return 1;
        }
    }

    function OG_Limit($username='default',$min=10,$max=10000){  //修改限红
        $OG_Host=str_replace('https://','',$this->ApiUrl);
        $OG_Host=str_replace('http://','',$OG_Host);
        $header = array();
        $header[] = 'Host: '.$OG_Host;
        $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0 FirePHP/0.7.4';
        $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $header[] = 'Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3';
        $header[] = 'Referer: '.$this->ApiUrl.'/';
        $header[] = 'x-insight: activate';
        $header[] = 'Connection: keep-alive';
        $header[] = 'X-Token: '.$this->OG_Token;
        $postdata=array();
        $postdata['min']=$min;
        $postdata['max']=$max;
        if($username=='default'){  //默认为预设会员
            $url=$this->ApiUrl.'/game-providers/30/operator-bet-limit';
        }else{
            $url=$this->ApiUrl.'/game-providers/30/user-bet-limit';
            $postdata['username']=$username;
        }
        $htmlcode= $this->curl_info_s($url,null,null,$postdata,$this->ApiUrl,$header);
        $json_data=json_decode($htmlcode,true);
        //print_r($json_data);exit;
        if($json_data['status']<>'success'){
            $t=date("Y-m-d H:i:s");
            $tmpfile=$_SERVER['DOCUMENT_ROOT']."/tmp/og_".date("Ymd").".txt";
            $f=fopen($tmpfile,'a');
            fwrite($f,$t."\r\n修改限红\r\n$htmlcode\r\n\r\n");
            fclose($f);
            return 0;
        }else{
            return 1;
        }
    }


    function GetToken($type=1){  //获取TOKEN type=1正常  type:2 试玩

        $OG_Host=str_replace('https://','',$this->ApiUrl);
        $OG_Host=str_replace('http://','',$OG_Host);
        $header = array();
        $header[] = 'Host: '.$OG_Host;
        $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0 FirePHP/0.7.4';
        $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $header[] = 'Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3';
        $header[] = 'Referer: '.$this->ApiUrl.'/';
        $header[] = 'x-insight: activate';
        $header[] = 'Connection: keep-alive';
        $header[] = 'X-Operator: '.$this->X_Operator;
        $header[] = 'X-key: '.$this->X_Key;
        //print_r($header);exit;
        $url=$this->ApiUrl.'/token';
        $htmlcode= $this->curl_info_s($url,null,null,null,$this->ApiUrl,$header);
        $json_data=json_decode($htmlcode,true);
        //print_r($json_data);
        if($json_data['status']=='success'){
            $OG_Token=$json_data['data']['token'];
            $Token_Uptime=time();
            if($type==1){
                DB::update("update sys_config set OG_Token='$OG_Token',Token_Uptime='$Token_Uptime'");
            }
        }
    }


    function getUrl_OG($url,$timeout=60,$header=null, $post = null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if($header){  //设置header
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if($post){  //启用POST提交
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);  //设置POST提交的字符串
        }
        curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);  //超时60秒
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  //设置浏览器类型，含代理号
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        return $html;
    }


    function curl_info_s($info_url, $cookie_jar = null, $post_cookiefile = null, $post = null, $referer = null, $header = null, $returnCooke = null, $proxy = null){
        try{
            $ch = curl_init();
            if($proxy){ //设置代理
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
            }
            //curl_setopt($ch,CURLOPT_PROXYUSERPWD,"user:pwd");
            curl_setopt($ch, CURLOPT_URL, $info_url);  //设置网址
            if ($cookie_jar){  //设置COOKIE
                curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);  //当会话结束的时候保存一个Cookie
                curl_setopt($ch, CURLOPT_COOKIE, $cookie_jar);  // 传递一个包含HTTP cookie的头连接
            }
            if ($post_cookiefile){  //传递一个包含cookie数据的文件的名字的字符串
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
            }
            if ($post){  //启用POST提交
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);  //设置POST提交的字符串
            }
            if ($header){  //设置header
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            }
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  //模拟用户使用的浏览器
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  //自动跳转
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //要求结果保存到字符串中还是输出到屏幕上 0为输出1为保存字符串
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // 超时的时间（秒）
            if($returnCooke){  //如果你想把一个头包含在输出中，设置这个选项为一个非零值
                curl_setopt($ch, CURLOPT_HEADER, true);
            }else{
                curl_setopt($ch, CURLOPT_HEADER, false);
            }
            curl_setopt($ch, CURLOPT_NOBODY, false);  //如果你不想在输出中包含body部分，设置这个选项为一个非零值
            curl_setopt($ch, CURLOPT_REFERER, $referer);  //设置referer
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
            $content = curl_exec($ch); //请求网页
            $result=$content;
            if($returnCooke){
                // 解析HTTP数据流
                list($header, $body) = explode("\r\n\r\n", $content);
                // 解析COOKIE
                if($returnCooke== 'cookie'){
                    preg_match_all("/set\-cookie:([^\r\n]*)/i", $header, $matches);
                    $result=implode('; ', $matches[1]);
                }else if($returnCooke== 'location'){
                    preg_match("/Location:([^\r\n]*)/i", $header, $matches);
                    $result=$matches[1];
                }
            }
            return $result;
            // 关闭URL请求
            curl_close($ch);
        } catch (Exception $e){
            return 'error';
        }
        return trim(ltrim($content));
    }

    function getpassword_OG($len=10){
        $key='0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
        $kk=explode(",",$key);
        $pass="";
        for($i=1;$i<=$len;$i++){
            $pass=$pass.$kk[mt_rand(0,sizeof($kk)-1)];
        }
        return $pass;
    }
}
