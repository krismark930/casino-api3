<?php
function GetUrl_HG($url,$post=null,$timeout=60,$refe_url=null,$cookie=null,$header=null,$ip_address=null){
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
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
	curl_setopt($ch, CURLOPT_USERAGENT, $server_agent);  //设置浏览器类型，含代理号
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$html = curl_exec($ch);
	return $html;
}

