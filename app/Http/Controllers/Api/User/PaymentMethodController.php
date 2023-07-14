<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Utils\LYPAY\payConfig;

class PaymentMethodController extends Controller
{

    public function submitLYPay(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "g_type" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $amount = $request_data['amount'];
            $username = $request_data['username'];
            $bankcode = $request_data['bankcode'];
            $BillNO = "LY" . Carbon::now("Asia/Hong_Kong")->format("YmdHis") . mt_rand(10000, 99999);
            $Date = date("Y-m-d");
            $DateTime = date("Y-m-d H:i:s");
            $PayID = $request_data['PayID'] ?? 6;
            $paytype = $request_data['paytype'] ?? "";

            $Config = new PayConfig();

            $sql = "insert into web_payment_billno2 set BillNo='$BillNO',Gold=$amount,UserName='$username',Platform='LYPay',Date='$Date',DateTime='$DateTime',Checked=0";
            DB::select($sql);

            if ($PayID == '') {
                $PayConfig = $Config->GetPayInfo2($paytype);
            } else {
                $PayConfig = $Config->GetPayInfo($PayID);
            }
            $paytype = $PayConfig['Type'];
            $mch_id = $PayConfig['Business'];
            $PayKey = $PayConfig['Keys'];

            if ($paytype == 1)    $trade_type = '10';

            if ($paytype == 2) {
                if ($Config->isMobile()) {
                    $trade_type = '08';
                } else {
                    $trade_type = '23';
                }
            }

            if ($paytype == 3) {
                if ($Config->isMobile()) {
                    $trade_type = '02';
                } else {
                    $trade_type = '13';
                }
            }

            if ($paytype == 4)    $trade_type = '12';

            if ($paytype == 5) {
                $trade_type = '05';
            }

            if ($paytype == 7)    $trade_type = '07';

            if ($paytype == 8)    $trade_type = '11';

            $trade_type = '23';

            $out_trade_no = $BillNO;
            $body = 'QB';
            $attach = '';
            $total_fee = $amount * 100;
            $bankArray = array(
                'ICBC' => '01020000',
                'ABC' => '01030000',
                'BOC' => '01040000',
                'CCB' => '01050000',
                'BCOM' => '03010000',
                'ECITIC' => '03020000',
                'CEBB' => '03030000',
                'HXB' => '03040000',
                'CMBC' => '03050000',
                'GDB' => '03060000',
                'SPABANK' => '03070000',
                'CMB' => '03080000',
                'CIB' => '03090000',
                'SPDB' => '03100000',
                'PSBC' => '04030000'
            );
            $bank_id = $bankArray[$bankcode];
            $return_url = env("USER_URL") . "/deposit";
            $notify_url = env("APP_URL") . "/api/user/third-party-payment/ly-notify";
            $time_start = date("YmdHis", time() + 12 * 3600);
            $nonce_str = $username;
            $PayInfo = array(
                'mch_id' => $mch_id,
                'trade_type' => $trade_type,
                'out_trade_no' => $out_trade_no,
                'body' => $body,
                'attach' => $attach,
                'total_fee' => $total_fee,
                'bank_id' => $bank_id,
                'notify_url' => $notify_url,
                'return_url' => $return_url,
                'time_start' => $time_start,
                'nonce_str' => $nonce_str
            );
            $signStr = "";
            $PayInfo2 = $PayInfo;
            ksort($PayInfo2);
            $SignKeys = explode(",", "mch_id,trade_type,out_trade_no,total_fee,bank_id,notify_url,return_url,time_start,nonce_str");
            foreach ($PayInfo2 as $key => $value) {
                if (in_array($key, $SignKeys)) {
                    if ($key == 'notify_url' or $key == 'return_url') {
                        $value = urlencode($value);
                    }
                    $signStr = $signStr .  $key  . '='  . $value .  '&';
                }
            }

            $signStr = $signStr . 'key=' . $PayKey;

            $sign = strtoupper(md5(preg_replace('/\s+/', '', $signStr)));

            $PayInfo['sign'] = $sign;

            // return $PayInfo;

            $PayInfo["signStr"] = preg_replace('/\s+/', '', $signStr);

            $result = $Config->fetchPost(env('LY_PAY_URL'), $PayInfo);

            $response["data"] = $result;
            $response["config"] = $PayInfo;
            $response['message'] = "LY PayInfo Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function notifyLY(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                // "g_type" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $Config = new payConfig();

            $t = date("Y-m-d H:i:s");

            $pp = file_get_contents("php://input");

            if ($pp <> "") {
                $tmpfile = $_SERVER['DOCUMENT_ROOT'] . "/tmp/post_" . date("Ymd") . ".txt";
                $f = fopen($tmpfile, 'a');
                fwrite($f, $t . "\r\nnotify\r\n" . $pp . "\r\n");
                fclose($f);
            }

            $request_data = $request->all();

            $mch_id = $request_data['mch_id'];  //商户号
            $out_trade_no = $request_data['out_trade_no'];  //商户订单号
            $trade_no = $request_data['trade_no'];  //系统定单号
            $trade_type = $request_data['trade_type'];  //订单日期
            $trade_state = $request_data['trade_state'];  //SUCCESS—支付成功
            $total_fee = $request_data['total_fee'];  //金额，以分为单位
            $nonce_str = $request_data['nonce_str'];
            $time_end = $request_data['time_end'];
            $sign = $request_data['sign'];  //签名

            $Music = $Config->GetMusic($mch_id);
            $PayKey = $Config->GetPayKey($mch_id);

            if ($mch_id == '' or $PayKey == '') {
                exit;
            }

            $PayInfo = array(
                'mch_id' => $mch_id,
                'nonce_str' => $nonce_str,
                'out_trade_no' => $out_trade_no,
                'time_end' => $time_end,
                'total_fee' => $total_fee,
                'trade_no' => $trade_no,
                'trade_state' => $trade_state,
                'trade_type' => $trade_type
            );

            $SignKeys = explode(",", "mch_id,nonce_str,out_trade_no,time_end,total_fee,trade_no,trade_state,trade_type");

            $signStr = "";

            foreach ($PayInfo as $key => $value) {
                if (in_array($key, $SignKeys)) {
                    $signStr = $signStr .  $key  . '='  . $value .  '&';
                }
            }

            $signStr .= "key=" . $PayKey;

            $signature = strtoupper(md5($signStr));

            if ($trade_state == 'SUCCESS') {
                $Config->InsertPayLog($request_data, $out_trade_no, $total_fee / 100, '', 1, 'LYPAY', $Music);
            } else {
                $response['message'] = "LY Notify Data failed!";
                return response()->json($response, $response['status']);
            }

            $response['message'] = "LY Notify Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return "SUCCESS";
    }
}
