<?php

namespace App\Utils\PT;

use App\Utils\MG\des;
use Illuminate\Support\Facades\Storage;

class PTUtils
{

    public $PT_agent;
    public $md5key_PT;
    public $deskey_PT;
    public $giurl_PT;
    public $gciurl_PT;

    public function __construct($row)
    {

        $this->PT_agent = env('PT_AGENT');
        $this->md5key_PT = env('AG_MD5KEY');
        $this->deskey_PT = env('AG_DESKEY');
        $this->giurl_PT = env('AG_GI_URL');
        $this->gciurl_PT = env('AG_GCI_URL');
    }

    function Addmember_PT($username, $password, $tp = 1)
    {
        $crypt = new DES($this->deskey_PT);
        $para = "cagent=" . $this->PT_agent . "/\\\\/loginname=" . $username . "/\\\\/method=lg/\\\\/actype=" . $tp . "/\\\\/password=" . $password . "/\\\\/oddtype=A/\\\\/cur=CNY";
        $params = $crypt->encrypt($para);
        $key = md5($params . $this->md5key_PT);
        $url = $this->giurl_PT . "doBusiness.do?params=" . $params . "&key=" . $key;
        // return $url;
        $xmlcode = $this->getUrl_PT($url);
        $result = $this->getResult_PT($xmlcode);
        if ($result['info'] != '0') {
            $t = date("Y-m-d H:i:s");
            if (!Storage::exists('public/tmp')) {
                Storage::makeDirectory("public/tmp");
            }
            $tmpfile = storage_path('app/public/tmp/pt_') . date("Ymd") . ".txt";
            $f = fopen($tmpfile, 'a');
            fwrite($f, $t . "\r\n会员开户\r\n$xmlcode\r\n\r\n");
            fclose($f);
        }
        return $result;
    }

    function getGameUrl_PT($username, $password, $oddtype = "A", $dm = "www.bbin-api8.com", $tp = 1, $gameType = 0)
    {
        $crypt = new DES($this->deskey_PT);
        $para = "cagent=" . $this->PT_agent . "/\\\\/loginname=" . $username . "/\\\\/actype=" . $tp . "/\\\\/password=" . $password . "/\\\\/dm=" . $dm . "/\\\\/sid=" . $this->PT_agent . date("ymdhis") . rand(1000, 9999) . "/\\\\/lang=1/\\\\/gameType=" . $gameType . "/\\\\/oddtype=" . $oddtype . "/\\\\/cur=CNY";
        $params = $crypt->encrypt($para);
        $key = md5($params . $this->md5key_PT);
        $url = $this->gciurl_PT . "forwardGame.do?params=" . $params . "&key=" . $key;
        return $url;
    }

    function getMoney_PT($username, $password, $tp = 1)
    {
        $crypt = new DES($this->deskey_PT);
        $para = "cagent=" . $this->PT_agent . "/\\\\/loginname=" . $username . "/\\\\/method=gb/\\\\/actype=" . $tp . "/\\\\/password=" . $password . "/\\\\/cur=CNY";
        $params = $crypt->encrypt($para);
        $key = md5($params . $this->md5key_PT);
        $url = $this->giurl_PT . "doBusiness.do?params=" . $params . "&key=" . $key;
        $xmlcode = $this->getUrl_PT($url);
        $result = $this->getResult_PT($xmlcode);
        return intval($result['info']);
    }
    // d存款 w提款 vd VIP存款 vw VIP提款
    function Deposit_PT($username, $password, $Gold, $tp = "IN")
    {
        $crypt = new DES($this->deskey_PT);
        $billno = date("YmdHis") . rand(1000, 9999);
        if ($tp == "IN") {
            $billno = $billno . "8";
        } else {
            $billno = $billno . "0";
        }
        $para = "cagent=" . $this->PT_agent . "/\\\\/method=tc/\\\\/loginname=" . $username . "/\\\\/billno=" . $billno . "/\\\\/type=" . $tp . "/\\\\/credit=" . number_format($Gold, 2, null, "") . "/\\\\/actype=1/\\\\/password=" . $password . "/\\\\/cur=CNY";
        $params = $crypt->encrypt($para);
        $key = md5($params . $this->md5key_PT);
        $url = $this->giurl_PT . "doBusiness.do?params=" . $params . "&key=" . $key;
        $xmlcode = $this->getUrl_PT($url);

        $t = date("Y-m-d H:i:s");
        if (!Storage::exists('public/tmp')) {
            Storage::makeDirectory("public/tmp");
        }
        $tmpfile = storage_path('app/public/tmp/PT_') . date("Ymd") . ".txt";
        $f = fopen($tmpfile, 'a');
        fwrite($f, "预转账$t\r\n会员号:$username  金额:$Gold  定单号:$billno\r\n$xmlcode\r\n\r\n");
        fclose($f);

        $result = $this->getResult_PT($xmlcode);
        if ($result['info'] == '0' and $result['msg'] == "") {
            $para = "cagent=" . $this->PT_agent . "/\\\\/loginname=" . $username . "/\\\\/method=tcc/\\\\/billno=" . $billno . "/\\\\/type=" . $tp . "/\\\\/credit=" . number_format($Gold, 2, null, "") . "/\\\\/actype=1/\\\\/flag=1/\\\\/password=" . $password . "/\\\\/cur=CNY";
            $params = $crypt->encrypt($para);
            $key = md5($params . $this->md5key_PT);
            $url = $this->giurl_PT . "doBusiness.do?params=" . $params . "&key=" . $key;
            unset($xmlcode);
            $xmlcode = $this->getUrl_PT($url);

            $t = date("Y-m-d H:i:s");
            if (!Storage::exists('public/tmp')) {
                Storage::makeDirectory("public/tmp");
            }
            $tmpfile = storage_path('app/public/tmp/PT_') . date("Ymd") . ".txt";
            $f = fopen($tmpfile, 'a');
            fwrite($f, "确认$t\r\n会员号:$username  金额:" . $Gold . "  定单号:$billno\r\n$xmlcode\r\n\r\n");
            fclose($f);

            unset($result);
            $result = $this->getResult_PT($xmlcode);
        }
        $result['billno'] = $billno;
        return $result;
    }

    function QosBillno_PT($billno)
    {
        $crypt = new DES($this->deskey_PT);
        $para = "cagent=" . $this->PT_agent . "/\\\\/billno=" . $billno . "/\\\\/method=qos/\\\\/actype=1/\\\\/cur=CNY";
        $params = $crypt->encrypt($para);
        $key = md5($params . $this->md5key_PT);
        $url = $this->giurl_PT . "doBusiness.do?params=" . $params . "&key=" . $key;
        $xmlcode = $this->getUrl_PT($url);
        return $this->getResult_PT($xmlcode);
    }

    function getUrl_PT($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //超时60秒
        curl_setopt($ch, CURLOPT_USERAGENT, ' WEB_LIB_GI_' . $this->PT_agent); //设置浏览器类型，含代理号
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 2);
        $html = curl_exec($ch);
        return $html;
    }

    function getResult_PT($content)
    {
        $info = $this->getContent_PT($content, 'info="', '"', 1);
        $msg = $this->getContent_PT($content, 'msg="', '"', 1);
        $result = array();
        $result['info'] = $info;
        $result['msg'] = $msg;
        if ($content == "" or !strpos($content, 'info=') > 0) {
            $result['info'] = 'error';
            $result['msg'] = '网络异常!';
        }
        return $result;
    }

    function getContent_PT($sourceStr, $star, $end, $flag)
    {
        switch ($flag) {
            case 0: //取指定字符前面的
                echo strrpos($sourceStr, $end);
                echo '-----' . strlen($end);
                $content = substr($sourceStr, 0, strrpos($sourceStr, $end) + strlen($end));
                break;
            case 1: //取指定字符之间的,不包括指定字符
                $content = substr($sourceStr, strpos($sourceStr, $star) + strlen($star));
                $content = substr($content, 0, strpos($content, $end));
                break;
            case 2: //取指定字符之间的，包括指定字符
                $content = strstr($sourceStr, $star);
                $content = substr($content, 0, strpos($content, $end) + strlen($end));
                break;
            case 3: //取指定字符之后的，不包括指定字符
                $content = substr($sourceStr, strrpos($sourceStr, $star) + strlen($star));
                break;
            case 4: //取指定字符之后的，包括指定字符
                $content = strstr($sourceStr, $star);
                break;
        }
        return $content;
    }

    function getpassword_PT($len = 10)
    {
        $key = '0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
        $kk = explode(",", $key);
        $pass = "";
        for ($i = 1; $i <= $len; $i++) {
            $pass = $pass . $kk[mt_rand(0, sizeof($kk) - 1)];
        }
        return $pass;
    }
}
