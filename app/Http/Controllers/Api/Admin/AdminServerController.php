<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

function GetUrl_HG($url,$header=null,$post=null,$timeout=60,$refe_url=null,$cookie=null,$ip_address=null){
    $server_agent='Mozilla/5.0 (Windows NT 5.2; rv:32.0) Gecko/20100101 Firefox/32.0';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if($header){  //设置header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    if($post){  //启用POST提交
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);  //设置POST提交的字符串
    }
    if($refe_url){
        curl_setopt($ch, CURLOPT_REFERER, $refe_url); 
    }
    if($ip_address){
        curl_setopt($ch,CURLOPT_INTERFACE,$ip_address);  //绑定IP
    }
    if($cookie){
        curl_setopt ($ch, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);  //超时60秒
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_USERAGENT, $server_agent);  //设置浏览器类型，含代理号
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 2);
    $html = curl_exec($ch);
    // header('Content-Type: image/jpeg');
    header("Access-Control-Allow-Origin: *");
    curl_close($ch);
    flush();
    return $html;
}

class AdminServerController extends Controller
{

    public function updateServer(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $username="4056hg";
            $password="wang8899";
            $postdata="p=chk_login&langx=zh-cn&ver=-3ed5-ipv6-0427-95882ae5676be2&username=$username&password=$password&app=N&auto=GZCZHC";
            $ip_address = "27.126.187.128";
            $site_new = "https://199.26.97.191";
            $url=$site_new."/transform.php?ver=-3ed5-ipv6-0427-95882ae5676be2";
            $htmlcode=GetUrl_HG($url,'',$postdata,10,$site_new,'',$ip_address);
            return $htmlcode;
            $htmlcode=GetUrl_HG('http://www.zq0666.com/gl888/servers.php','','',5,$site,'',$ip_address);
            $site_arr2=explode("\r\n",$htmlcode);  //接水服务器组
            $site_arr2[]=$site;
            $site_arr2=array_unique($site_arr2);
            $servers=array();
            foreach($site_arr2 as $key=>$value){
                if(!strpos($value,'*') and trim($value)<>''){
                    $servers[]=$value;
                }
            }
            shuffle($servers);      //打乱顺序
            foreach($servers as $key=>$site_new){
                unset($htmlcode);unset($ver);
                $site_new=trim($site_new,'/');
                $site_new =
                $htmlcode=GetUrl_HG($site_new,'',$postdata,5,$site_new,'',$ip_address);
                    return $htmlcode;
                $ver=urlencode(GetKey($htmlcode,"top.ver = '","';"));
                if($ver<>'' and $htmlcode){
                }
            }     

            $response['message'] = "Server Data updated successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
}
