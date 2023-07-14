<?php

namespace App\Utils\LYPAY;

use App\Models\WebPaymentData;
use Illuminate\Support\Facades\DB;
use App\Utils\Utils;
use App\Models\WebPaymentBillNo;

class PayConfig {

    public function __construct() {
    }

	function getPath()
	{

		if ($_SERVER["HTTPS"] == 'on' or $_SERVER['SERVER_PORT'] == 443) {
			$https = 'https://';
		} else {
			if ($_SERVER['HTTP_X_FORWARDED_PROTO'] <> '') {
				$https = $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://';
			} else {
				$https = 'http://';
			}
		}

		$path = $https . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];

		$path = dirname($path);

		return $path;
	}

	function IIF2($expr, $value1, $value2)
	{
		if ($expr) return $value1;
		else return $value2;
	}

	function GetPayInfo($PayID)
	{
		$result = WebPaymentData::where("ID", $PayID)->first();
		return $result;
	}

	function GetPayInfo2($PayType)
	{
		$payrow = WebPaymentData::where("Type", $PayType)->where("Switch", 1)->first();
		return $payrow;
	}


	function GetPayKey($Business)
	{
		$payrow = WebPaymentData::where("Business", $Business)->first();
		return $payrow['Keys'];
	}

	function GetMusic($Business)
	{
		$payrow = WebPaymentData::where("Business", $Business)->first();
		$Music = intval($payrow['Music']);
		if ($Music == 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function SignStr($dict)
	{
		$signStr = "";
		foreach ($dict as $key => $value) {
			$signStr .= $key . '=' . $value . '&';
		}
		return $signStr;
	}


	function fetchPost($url, $data, $cookie = '')
	{
		// 模拟提交数据函数
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); 
		curl_setopt($curl, CURLOPT_POST, true); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Post提交的数据包
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($curl); // 执行操作
		if (curl_errno($curl)) {
			echo 'Errno' . curl_error($curl); //捕抓异常
		}
		curl_close($curl); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}


	function InsertPayLog($request_data, $BillNO, $Gold, $UserName = "", $Check = 1, $Platform = 'RCPAY', $Music = 1)
	{
		global $dbname;
		if ($UserName == "") {
			$sql = "select UserName from  `web_payment_billno2` where `BillNo`='$BillNO'";
			$result = DB::select($sql);
			$row = get_object_vars($result[0]);
			$UserName = $row['UserName'];
		}
		$pp = http_build_query($request_data);
		if ($pp == '') file_get_contents("php://input");
		$result = WebPaymentBillNo::where("BillNo", $BillNO)->first();
		if (isset($result)) return;
		$sql = "insert into `web_payment_billno` set `BillNo`='$BillNO',Gold=$Gold,UserName = '$UserName',Platform='$Platform',`Date`='" . Date("Y-m-d") . "',`Checked`=0,Memo='$pp'";
		DB::select($sql);
		$newid_billno = DB::getPdo()->lastInsertId();
		// return $newid_billno;
		if ($Check == 1) {
			$sql = "select Gold from  `web_payment_billno2` where `BillNo`='$BillNO' and UserName='$UserName' and Checked=0";
			$result = DB::select($sql);
			$row = get_object_vars($result[0]);
			if (intval($row['Gold']) <> $Gold) {
				echo "支付失败，请与我们的客服联系入款!";
				exit;
			} else {
				$sql = "update `web_payment_billno2` set Checked=1 where `BillNo`='$BillNO'";
				DB::select($sql);
			}
			unset($row);
			unset($result);
		}

		$sql = "select * from web_member_data where UserName = '" . $UserName . "'";
		$result = DB::select($sql);
		$row = get_object_vars($result[0]);
		$agents = $row['Agents'];
		$world = $row['World'];
		$corprator = $row['Corprator'];
		$super = $row['Super'];
		$admin = $row['Admin'];
		$alias = $row['Alias'];
		$Waterno = $Platform;
		$sql = "select ID from web_sys800_data where Order_Code = '" . $BillNO . "'";
		$result = DB::select($sql);
		$cou = count($result);
		if ($cou == 0) {
			$date = date("Y-m-d");
			$datetime = date("Y-m-d H:i:s");
			$previousAmount = Utils::GetField($UserName, "Money");
			$currentAmount = $previousAmount + $Gold;
			$sql = "insert into web_sys800_data set Checked=1,Payway='W',Gold='$Gold',previousAmount='$previousAmount',currentAmount='$currentAmount',AddDate='$date',Type='S',UserName='$UserName',Agents='$agents',World='$world',Corprator='$corprator',Super='$super',Admin='$admin',CurType='RMB',Date='$datetime',Name='$alias',User='$UserName',Waterno='$Waterno',Order_Code='$BillNO',Music=$Music,Bank_Account='$Waterno',Bank_Address='$Waterno'";
			DB::select($sql);
			$newid_sys800 = DB::getPdo()->lastInsertId();
			$assets = Utils::GetField($UserName, 'Money');
			$user_id = Utils::GetField($UserName, 'id');
			$datetime_bj = date("Y-m-d H:i:s", time() + 12 * 3600);
			$sql_amt = "UPDATE web_member_data SET Credit = Credit + ?, Money = Money + ? WHERE UserName = ?";
			$q1 = DB::update($sql_amt, [$Gold, $Gold, $UserName]);
			// return $q1;
			if ($q1 > 0) {
				$balance = Utils::GetField($UserName, 'Money');
				$money_log_sql = "insert into money_log set user_id='$user_id',order_num='$BillNO',about='在线充值',update_time='$datetime_bj',type='第3方在线充值($Platform)',order_value='$Gold',assets=$assets,balance=$balance";
				DB::select($money_log_sql);
			} else {
				DB::select("delete from `web_payment_billno` where ID=" . $newid_billno);
				DB::select("delete from web_sys800_data where ID=" . $newid_sys800);
			}
		}
	}


	/*移动端判断*/
	function isMobile()
	{
		// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
		if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
			return true;
		}
		// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
		if (isset($_SERVER['HTTP_VIA'])) {
			// 找不到为flase,否则为true
			return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		}
		// 脑残法，判断手机发送的客户端标志,兼容性有待提高
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$clientkeywords = array(
				'nokia',
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
			if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
				return true;
			}
		}
		// 协议法，因为有可能不准确，放到最后判断
		if (isset($_SERVER['HTTP_ACCEPT'])) {
			// 如果只支持wml并且不支持html那一定是移动设备
			// 如果支持wml和html但是wml在html之前则是移动设备
			if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
				return true;
			}
		}
		return false;
	}
}
