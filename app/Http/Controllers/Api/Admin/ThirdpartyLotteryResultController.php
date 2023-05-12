<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utils\Utils;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\LotteryResultAZXY5;
use App\Models\LotteryResultAZXY10;
use App\Models\LotteryResultBJKN;
use App\Models\LotteryResultBJPK;
use App\Models\LotteryResultCQ;
use App\Models\LotteryResultCQSF;
use App\Models\LotteryResultD3;
use App\Models\LotteryResultFFC5;
use App\Models\LotteryResultGD11;
use App\Models\LotteryResultGDSF;
use App\Models\LotteryResultGXSF;
use App\Models\LotteryResultJX;
use App\Models\LotteryResultP3;
use App\Models\LotteryResultT3;
use App\Models\LotteryResultTJ;
use App\Models\LotteryResultTJSF;
use App\Models\LotteryResultTWSSC;
use App\Models\LotteryResultTXSSC;
use App\Models\LotteryResultXYFT;
use Carbon\Carbon;

class ThirdpartyLotteryResultController extends Controller
{
    public function getLotteryResult(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        $request_data = $request->all();

        $htmlcode = Utils::decrypt($request_data["cryptedData"]);

        $data = json_decode($htmlcode, true);

        foreach($data as $item) {

            $code=$item['type'];

            $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

            if ($code == 'cqssc') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $result = LotteryResultCQ::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultCQ;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'gdklsf') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $ball_6 = $tempNum[5];
                $ball_7 = $tempNum[6];
                $ball_8 = $tempNum[7];
                $result = LotteryResultGDSF::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultGDSF;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->ball_6 = $ball_6;
                    $result->ball_7 = $ball_7;
                    $result->ball_8 = $ball_8;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'gxklsf') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $result = LotteryResultGXSF::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultGXSF;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'tcpl3') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $result = LotteryResultP3::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultP3;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'shssl') {
                if (strlen($item["qishu"]) == 11) {
                    $item["qishu"] = substr($item["qishu"],0,8).substr($item["qishu"],9,2);
                }
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $result = LotteryResultT3::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultT3;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'tjklsf') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $ball_6 = $tempNum[5];
                $ball_7 = $tempNum[6];
                $ball_8 = $tempNum[7];
                $result = LotteryResultTJSF::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultTJSF;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->ball_6 = $ball_6;
                    $result->ball_7 = $ball_7;
                    $result->ball_8 = $ball_8;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'tjssc') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $result = LotteryResultTJ::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultTJ;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'xjssc') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $result = LotteryResultJX::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultJX;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'bjkl8') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $ball_6 = $tempNum[5];
                $ball_7 = $tempNum[6];
                $ball_8 = $tempNum[7];
                $ball_9 = $tempNum[8];
                $ball_10 = $tempNum[9];
                $ball_11 = $tempNum[10];
                $ball_12 = $tempNum[11];
                $ball_13 = $tempNum[12];
                $ball_14 = $tempNum[13];
                $ball_15 = $tempNum[14];
                $ball_16 = $tempNum[15];
                $ball_17 = $tempNum[16];
                $ball_18 = $tempNum[17];
                $ball_19 = $tempNum[18];
                $ball_20 = $tempNum[19];
                $result = LotteryResultBJKN::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultBJKN;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->ball_6 = $ball_6;
                    $result->ball_7 = $ball_7;
                    $result->ball_8 = $ball_8;
                    $result->ball_9 = $ball_9;
                    $result->ball_10 = $ball_10;
                    $result->ball_11 = $ball_11;
                    $result->ball_12 = $ball_12;
                    $result->ball_13 = $ball_13;
                    $result->ball_14 = $ball_14;
                    $result->ball_15 = $ball_15;
                    $result->ball_16 = $ball_16;
                    $result->ball_17 = $ball_17;
                    $result->ball_18 = $ball_18;
                    $result->ball_19 = $ball_19;
                    $result->ball_20 = $ball_20;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'bjpk10') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $ball_6 = $tempNum[5];
                $ball_7 = $tempNum[6];
                $ball_8 = $tempNum[7];
                $ball_9 = $tempNum[8];
                $ball_10 = $tempNum[9];

                $result = LotteryResultBJPK::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultBJPK;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->ball_6 = $ball_6;
                    $result->ball_7 = $ball_7;
                    $result->ball_8 = $ball_8;
                    $result->ball_9 = $ball_9;
                    $result->ball_10 = $ball_10;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'cqklsf') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $ball_6 = $tempNum[5];
                $ball_7 = $tempNum[6];
                $ball_8 = $tempNum[7];
                $result = LotteryResultCQSF::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultCQSF;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->ball_6 = $ball_6;
                    $result->ball_7 = $ball_7;
                    $result->ball_8 = $ball_8;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'fc3d') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $result = LotteryResultD3::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultD3;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'gd115') {
                if (strlen($item["qishu"]) == 11) {
                    $item["qishu"] = substr($item["qishu"],0,8).substr($item["qishu"],9,2);
                }
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $result = LotteryResultGD11::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultGD11;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'hn5fc') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $result = LotteryResultFFC5::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultFFC5;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'txssc') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $result = LotteryResultTXSSC::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultTXSSC;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'twssc') {

                $tempNum = explode(",", $item['ball']);

                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];

                $result = LotteryResultTWSSC::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultTWSSC;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'azxy5') {
                $tempNum = explode(",", $item['ball']);
                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $result = LotteryResultAZXY5::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultAZXY5;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'azxy10') {

                $tempNum = explode(",", $item['ball']);

                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $ball_6 = $tempNum[5];
                $ball_7 = $tempNum[6];
                $ball_8 = $tempNum[7];
                $ball_9 = $tempNum[8];
                $ball_10 = $tempNum[9];

                $result = LotteryResultAZXY10::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultAZXY10;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->ball_6 = $ball_6;
                    $result->ball_7 = $ball_7;
                    $result->ball_8 = $ball_8;
                    $result->ball_9 = $ball_9;
                    $result->ball_10 = $ball_10;
                    $result->state = 0;
                    $result->save();
                }
            }

            if ($code == 'xyft') {
                $tempNum = explode(",", $item['ball']);

                $ball_1 = $tempNum[0];
                $ball_2 = $tempNum[1];
                $ball_3 = $tempNum[2];
                $ball_4 = $tempNum[3];
                $ball_5 = $tempNum[4];
                $ball_6 = $tempNum[5];
                $ball_7 = $tempNum[6];
                $ball_8 = $tempNum[7];
                $ball_9 = $tempNum[8];
                $ball_10 = $tempNum[9];
                $result = LotteryResultXYFT::where("qishu", $item["qishu"])->first();                
                if (!isset($result)) {
                    $result = new LotteryResultXYFT;
                    $result->create_time = $current_time;
                    $result->qishu = $item["qishu"];
                    $result->datetime = $item["datetime"];
                    $result->ball_1 = $ball_1;
                    $result->ball_2 = $ball_2;
                    $result->ball_3 = $ball_3;
                    $result->ball_4 = $ball_4;
                    $result->ball_5 = $ball_5;
                    $result->ball_6 = $ball_6;
                    $result->ball_7 = $ball_7;
                    $result->ball_8 = $ball_8;
                    $result->ball_9 = $ball_9;
                    $result->ball_10 = $ball_10;
                    $result->state = 0;
                    $result->save();
                }
            }
        }

        if (isset($data)) {
            $response['data'] = $data;
            $response['message'] = 'Lottery Result fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } else {
            $response['message'] = 'Lottery Result Data can not found!';
        }
        return response()->json($response, $response['status']); 
    }

    public function getLotteryResultTCPL3(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        $request_data = $request->all();

        $data = $request_data["data"];

        $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

        foreach($data as $item) {

            $tempNum = explode(",", $item['code']);
            $ball_1 = $tempNum[0];
            $ball_2 = $tempNum[1];
            $ball_3 = $tempNum[2];
            $result = LotteryResultP3::where("qishu", $item["issue"])->first();                
            if (!isset($result)) {
                $result = new LotteryResultP3;
                $result->create_time = $current_time;
                $result->qishu = $item["issue"];
                $result->datetime = $item["opendate"];
                $result->ball_1 = $ball_1;
                $result->ball_2 = $ball_2;
                $result->ball_3 = $ball_3;
                $result->state = 0;
                $result->save();
            }
        }

        if (isset($data)) {
            $response['data'] = $data;
            $response['message'] = 'Lottery Result fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } else {
            $response['message'] = 'Lottery Result Data can not found!';
        }
        return response()->json($response, $response['status']); 
    }

    public function getLotteryResultFC3D(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        $request_data = $request->all();

        $data = $request_data["data"];

        $current_time = Carbon::now('Asia/Hong_Kong')->format('Y-m-d H:i:s');

        foreach($data as $item) {
            
            $tempNum = explode(",", $item['code']);
            $ball_1 = $tempNum[0];
            $ball_2 = $tempNum[1];
            $ball_3 = $tempNum[2];
            $result = LotteryResultD3::where("qishu", $item["issue"])->first();                
            if (!isset($result)) {
                $result = new LotteryResultD3;
                $result->create_time = $current_time;
                $result->qishu = $item["issue"];
                $result->datetime = $item["opendate"];
                $result->ball_1 = $ball_1;
                $result->ball_2 = $ball_2;
                $result->ball_3 = $ball_3;
                $result->state = 0;
                $result->save();
            }
        }

        if (isset($data)) {
            $response['data'] = $data;
            $response['message'] = 'Lottery Result fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } else {
            $response['message'] = 'Lottery Result Data can not found!';
        }
        return response()->json($response, $response['status']); 
    }

    public function checkoutAZXY10(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultAZXY10::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm    = array();
            $hm[]  = $rs['ball_1'];
            $hm[]  = $rs['ball_2'];
            $hm[]  = $rs['ball_3'];
            $hm[]  = $rs['ball_4'];
            $hm[]  = $rs['ball_5'];
            $hm[]  = $rs['ball_6'];
            $hm[]  = $rs['ball_7'];
            $hm[]  = $rs['ball_8'];
            $hm[]  = $rs['ball_9'];
            $hm[]  = $rs['ball_10'];
            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("AZXY10" , $qishu, "0");

            foreach($orders as $order) {
                $order = get_object_vars($order);
                $user_id = $order["user_id"];
                $datereg = $order["order_num"];

                //开始结算冠军
                if($order['quick_type']=='冠军'){
                    $ds = Bjsc_Ds($rs['ball_1']);
                    $dx = Bjsc_Dx($rs['ball_1']);
                    $longhu=Bjsc_Auto($hm,4);
                    if($order['number']==$rs['ball_1'] || $order['number']==$ds || $order['number']==$dx ||  $order['number']==$longhu){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算亚军
                if($order['quick_type']=='亚军'){
                    $ds = Bjsc_Ds($rs['ball_2']);
                    $dx = Bjsc_Dx($rs['ball_2']);
                    $longhu=Bjsc_Auto($hm,5);
                    if($order['number']==$rs['ball_2'] || $order['number']==$ds || $order['number']==$dx ||  $order['number']==$longhu){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三名
                if($order['quick_type']=='第三名'){
                    $ds = Bjsc_Ds($rs['ball_3']);
                    $dx = Bjsc_Dx($rs['ball_3']);
                    $longhu=Bjsc_Auto($hm,6);
                    if($order['number']==$rs['ball_3'] || $order['number']==$ds || $order['number']==$dx ||  $order['number']==$longhu){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四名
                if($order['quick_type']=='第四名'){
                    $ds = Bjsc_Ds($rs['ball_4']);
                    $dx = Bjsc_Dx($rs['ball_4']);
                    $longhu=Bjsc_Auto($hm,7);
                    if($order['number']==$rs['ball_4'] || $order['number']==$ds || $order['number']==$dx ||  $order['number']==$longhu){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五名
                if($order['quick_type']=='第五名'){
                    $ds = Bjsc_Ds($rs['ball_5']);
                    $dx = Bjsc_Dx($rs['ball_5']);
                    $longhu=Bjsc_Auto($hm,8);
                    if($order['number']==$rs['ball_5'] || $order['number']==$ds || $order['number']==$dx ||  $order['number']==$longhu){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第六名
                if($order['quick_type']=='第六名'){
                    $ds = Bjsc_Ds($rs['ball_6']);
                    $dx = Bjsc_Dx($rs['ball_6']);
                    if($order['number']==$rs['ball_6'] || $order['number']==$ds || $order['number']==$dx){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第七名
                if($order['quick_type']=='第七名'){
                    $ds = Bjsc_Ds($rs['ball_7']);
                    $dx = Bjsc_Dx($rs['ball_7']);
                    if($order['number']==$rs['ball_7'] || $order['number']==$ds || $order['number']==$dx){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第八名
                if($order['quick_type']=='第八名'){
                    $ds = Bjsc_Ds($rs['ball_8']);
                    $dx = Bjsc_Dx($rs['ball_8']);
                    if($order['number']==$rs['ball_8'] || $order['number']==$ds || $order['number']==$dx){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第九名
                if($order['quick_type']=='第九名'){
                    $ds = Bjsc_Ds($rs['ball_9']);
                    $dx = Bjsc_Dx($rs['ball_9']);
                    if($order['number']==$rs['ball_9'] || $order['number']==$ds || $order['number']==$dx){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第十名
                if($order['quick_type']=='第十名'){
                    $ds = Bjsc_Ds($rs['ball_10']);
                    $dx = Bjsc_Dx($rs['ball_10']);
                    if($order['number']==$rs['ball_10'] || $order['number']==$ds || $order['number']==$dx){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算冠、亚军和
                if($order['quick_type']=='冠亚军和' && $order['number']>=3 && $order['number']<=19){
                    $zonghe = Bjsc_Auto($hm,1);
                    if($order['number']==$zonghe){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算冠、亚军和大小
                if($order['quick_type']=='冠亚军和' && ($order['number']=='大' || $order['number']=='小')){
                    $zonghe = Bjsc_Auto($hm,2);
                    if($zonghe=='和'){  //和局,退还本金
                        Utils::BalanceToAccount($order,2,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($order['number']==$zonghe){ 
                            Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算冠、亚军和单双
                if($order['quick_type']=='冠亚军和' && ($order['number']=='单' || $order['number']=='双')){
                    $zonghe = Bjsc_Auto($hm,3);
                    if($zonghe=='和'){  //和局,退还本金
                        Utils::BalanceToAccount($order,2,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($order['number']==$zonghe){
                            Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算1V10 龙虎
                if($order['quick_type']=='1V10龙虎'){
                    $longhu = Bjsc_Auto($hm,4);
                    if($order['number']==$longhu){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算2V9 龙虎
                if($order['quick_type']=='2V9龙虎'){
                    $longhu = Bjsc_Auto($hm,5);
                    if($order['number']==$longhu){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算3V8 龙虎
                if($order['quick_type']=='3V8龙虎'){
                    $longhu = Bjsc_Auto($hm,6);
                    if($order['number']==$longhu){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算4V7 龙虎
                if($order['quick_type']=='4V7龙虎'){
                    $longhu = Bjsc_Auto($hm,7);
                    if($order['number']==$longhu){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算5V6 龙虎
                if($order['quick_type']=='5V6龙虎'){
                    $longhu = Bjsc_Auto($hm,8);
                    if($order['number']==$longhu){
                        Utils::BalanceToAccount($order,1,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($order,0,'澳洲幸运10');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultAZXY10::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "AZXY10 Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function checkoutAZXY5(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultAZXY5::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm    = array();
            $hm[]  = $rs['ball_1'];
            $hm[]  = $rs['ball_2'];
            $hm[]  = $rs['ball_3'];
            $hm[]  = $rs['ball_4'];
            $hm[]  = $rs['ball_5'];
            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("azxy5" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);
                $user_id = $rows["user_id"];
                $datereg = $rows["order_num"];

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds($rs['ball_1']);
                    $dx = Ssc_Dx($rs['ball_1']);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds($rs['ball_2']);
                    $dx = Ssc_Dx($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds($rs['ball_3']);
                    $dx = Ssc_Dx($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第三球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Ssc_Ds($rs['ball_4']);
                    $dx = Ssc_Dx($rs['ball_4']);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第四球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Ssc_Ds($rs['ball_5']);
                    $dx = Ssc_Dx($rs['ball_5']);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto($hm,2);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){
                        Utils::BalanceToAccount($rows,2,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='前三'){
                    $qiansan = Ssc_Auto($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算中三
                if($rows['quick_type']=='中三'){
                    $zhongsan = Ssc_Auto($hm,6);
                    if($rows['number']==$zhongsan){
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算后三
                if($rows['quick_type']=='后三'){
                    $housan = Ssc_Auto($hm,7);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                    //开始结算牛牛
                if($rows['quick_type']=='牛牛'){
                    $housan = Ssc_Auto($hm,8);
                    $dsniu = Ssc_Auto($hm,9);
                    $dxniu = Ssc_Auto($hm,10);
                    if($rows['number']==$housan || $rows['number']==$dsniu || $rows['number']==$dxniu){
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                //开始结算梭哈
                if($rows['quick_type']=='梭哈'){
                    $housan = Ssc_Auto($hm,9);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'澳洲幸运5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultAZXY5::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "AZXY5 Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function checkoutBJKN(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultBJKN::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 20; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("BJKN" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算 选一
                if($rows['quick_type']=='选一'){
                    if(in_array($rows['number'],$hm) and CheckNumber($rows['number'])){
                        Utils::BalanceToAccount($rows,1,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京快乐8'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算 选二
                if($rows['quick_type']=='选二'){
                    $tt=explode(',',$rows['number']);
                    if(in_array($tt[0],$hm) and in_array($tt[1],$hm) and CheckNumber($rows['number'])){
                        Utils::BalanceToAccount($rows,1,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京快乐8'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算 选三
                if($rows['quick_type']=='选三'){
                    $bet_rate=explode(',',$rows['bet_rate']);
                    $tt=explode(',',$rows['number']);
                    $cc=0;
                    for($i=0;$i<count($tt);$i++){
                        if(in_array($tt[$i],$hm)) $cc++;
                    }
                    if($cc==3 and CheckNumber($rows['number'])){  //3中3
                        Utils::BalanceToAccount($rows,1,'北京快乐8',$bet_rate[0]);  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }elseif($cc==2 and CheckNumber($rows['number'])){  //3中2
                        Utils::BalanceToAccount($rows,1,'北京快乐8',$bet_rate[1]);  //is_win值:0未中奖 1中奖 2和局 3赢一半           
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京快乐8'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }


                //开始结算 选四
                if($rows['quick_type']=='选四'){
                    $bet_rate=explode(',',$rows['bet_rate']);
                    $tt=explode(',',$rows['number']);
                    $cc=0;
                    for($i=0;$i<count($tt);$i++){
                        if(in_array($tt[$i],$hm)) $cc++;
                    }
                    if($cc==4 and CheckNumber($rows['number'])){  //4中4
                        Utils::BalanceToAccount($rows,1,'北京快乐8',$bet_rate[0]);  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }elseif($cc==3 and CheckNumber($rows['number'])){  //4中3
                        Utils::BalanceToAccount($rows,1,'北京快乐8',$bet_rate[1]);  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }elseif($cc==2 and CheckNumber($rows['number'])){  //4中2
                        Utils::BalanceToAccount($rows,1,'北京快乐8',$bet_rate[2]);  //is_win值:0未中奖 1中奖 2和局 3赢一半                   
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京快乐8'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算 选五
                if($rows['quick_type']=='选五'){
                    $bet_rate=explode(',',$rows['bet_rate']);
                    $tt=explode(',',$rows['number']);
                    $cc=0;
                    for($i=0;$i<count($tt);$i++){
                        if(in_array($tt[$i],$hm)) $cc++;
                    }
                    if($cc==5 and CheckNumber($rows['number'])){  //5中5
                        Utils::BalanceToAccount($rows,1,'北京快乐8',$bet_rate[0]);  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }elseif($cc==4 and CheckNumber($rows['number'])){  //5中4
                        Utils::BalanceToAccount($rows,1,'北京快乐8',$bet_rate[1]);  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }elseif($cc==3 and CheckNumber($rows['number'])){  //5中3
                        Utils::BalanceToAccount($rows,1,'北京快乐8',$bet_rate[2]);  //is_win值:0未中奖 1中奖 2和局 3赢一半                   
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京快乐8'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto_BJKN($hm,2);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算总和810
                if($rows['number']=='总和810'){
                    $zonghe = Ssc_Auto_BJKN($hm,2);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto_BJKN($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }


                //开始结算 奇和偶
                if($rows['quick_type']=='奇和偶'){
                    $jiwohe=Ssc_Auto_BJKN($hm,4);
                    if($rows['number']==$jiwohe){  //买"奇偶"开"和"不退本金
                        Utils::BalanceToAccount($rows,1,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                //开始结算 上中下
                if($rows['quick_type']=='上中下'){
                    $shangzhongxia=Ssc_Auto_BJKN($hm,5);
                    if($rows['number']==$shangzhongxia){  //买"上下"开"中"不退本金
                        Utils::BalanceToAccount($rows,1,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京快乐8');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultBJKN::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "BJKN Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function checkoutBJPK10(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultBJPK::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 10; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("BJPK" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算冠军
                if($rows['quick_type']=='冠军') {
                    $ds = Bjsc_Ds($rs['ball_1']);
                    $dx = Bjsc_Dx($rs['ball_1']);
                    $longhu=Bjsc_Auto($hm,4);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx ||  $rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算亚军
                if($rows['quick_type']=='亚军'){
                    $ds = Bjsc_Ds($rs['ball_2']);
                    $dx = Bjsc_Dx($rs['ball_2']);
                    $longhu=Bjsc_Auto($hm,5);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx ||  $rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算第三名
                if($rows['quick_type']=='第三名'){
                    $ds = Bjsc_Ds($rs['ball_3']);
                    $dx = Bjsc_Dx($rs['ball_3']);
                    $longhu=Bjsc_Auto($hm,6);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx ||  $rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四名
                if($rows['quick_type']=='第四名'){
                    $ds = Bjsc_Ds($rs['ball_4']);
                    $dx = Bjsc_Dx($rs['ball_4']);
                    $longhu=Bjsc_Auto($hm,7);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx ||  $rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五名
                if($rows['quick_type']=='第五名'){
                    $ds = Bjsc_Ds($rs['ball_5']);
                    $dx = Bjsc_Dx($rs['ball_5']);
                    $longhu=Bjsc_Auto($hm,8);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx ||  $rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第六名
                if($rows['quick_type']=='第六名'){
                    $ds = Bjsc_Ds($rs['ball_6']);
                    $dx = Bjsc_Dx($rs['ball_6']);
                    if($rows['number']==$rs['ball_6'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第七名
                if($rows['quick_type']=='第七名'){
                    $ds = Bjsc_Ds($rs['ball_7']);
                    $dx = Bjsc_Dx($rs['ball_7']);
                    if($rows['number']==$rs['ball_7'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第八名
                if($rows['quick_type']=='第八名'){
                    $ds = Bjsc_Ds($rs['ball_8']);
                    $dx = Bjsc_Dx($rs['ball_8']);
                    if($rows['number']==$rs['ball_8'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第九名
                if($rows['quick_type']=='第九名'){
                    $ds = Bjsc_Ds($rs['ball_9']);
                    $dx = Bjsc_Dx($rs['ball_9']);
                    if($rows['number']==$rs['ball_9'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第十名
                if($rows['quick_type']=='第十名'){
                    $ds = Bjsc_Ds($rs['ball_10']);
                    $dx = Bjsc_Dx($rs['ball_10']);
                    if($rows['number']==$rs['ball_10'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算冠、亚军和
                if($rows['quick_type']=='冠亚军和' && $rows['number']>=3 && $rows['number']<=19){
                    $zonghe = Bjsc_Auto($hm,1);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算冠、亚军和大小
                if($rows['quick_type']=='冠亚军和' && ($rows['number']=='大' || $rows['number']=='小')){
                    $zonghe = Bjsc_Auto($hm,2);
                    if($zonghe=='和'){  //和局,退还本金
                        Utils::BalanceToAccount($rows,2,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$zonghe){ 
                            Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算冠、亚军和单双
                if($rows['quick_type']=='冠亚军和' && ($rows['number']=='单' || $rows['number']=='双')){
                    $zonghe = Bjsc_Auto($hm,3);
                    if($zonghe=='和'){  //和局,退还本金
                        Utils::BalanceToAccount($rows,2,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$zonghe){
                            Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算1V10 龙虎
                if($rows['quick_type']=='1V10龙虎'){
                    $longhu = Bjsc_Auto($hm,4);
                    if($rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算2V9 龙虎
                if($rows['quick_type']=='2V9龙虎'){
                    $longhu = Bjsc_Auto($hm,5);
                    if($rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算3V8 龙虎
                if($rows['quick_type']=='3V8龙虎'){
                    $longhu = Bjsc_Auto($hm,6);
                    if($rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算4V7 龙虎
                if($rows['quick_type']=='4V7龙虎'){
                    $longhu = Bjsc_Auto($hm,7);
                    if($rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算5V6 龙虎
                if($rows['quick_type']=='5V6龙虎'){
                    $longhu = Bjsc_Auto($hm,8);
                    if($rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'北京PK拾');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultBJPK::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "BJPK Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function checkoutCQSF(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultCQSF::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 8; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("CQSF" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Klsf_Ds($rs['ball_1']);
                    $dx = Klsf_Dx($rs['ball_1']);
                    $wdx = Klsf_Wdx($rs['ball_1']);
                    $hds = Klsf_Hdx($rs['ball_1']);
                    $zfb = Klsf_Zfb($rs['ball_1']);
                    $dnxb = Klsf_Dnxb($rs['ball_1']);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Klsf_Ds($rs['ball_2']);
                    $dx = Klsf_Dx($rs['ball_2']);
                    $wdx = Klsf_Wdx($rs['ball_2']);
                    $hds = Klsf_Hdx($rs['ball_2']);
                    $zfb = Klsf_Zfb($rs['ball_2']);
                    $dnxb = Klsf_Dnxb($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Klsf_Ds($rs['ball_3']);
                    $dx = Klsf_Dx($rs['ball_3']);
                    $wdx = Klsf_Wdx($rs['ball_3']);
                    $hds = Klsf_Hdx($rs['ball_3']);
                    $zfb = Klsf_Zfb($rs['ball_3']);
                    $dnxb = Klsf_Dnxb($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Klsf_Ds($rs['ball_4']);
                    $dx = Klsf_Dx($rs['ball_4']);
                    $wdx = Klsf_Wdx($rs['ball_4']);
                    $hds = Klsf_Hdx($rs['ball_4']);
                    $zfb = Klsf_Zfb($rs['ball_4']);
                    $dnxb = Klsf_Dnxb($rs['ball_4']);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Klsf_Ds($rs['ball_5']);
                    $dx = Klsf_Dx($rs['ball_5']);
                    $wdx = Klsf_Wdx($rs['ball_5']);
                    $hds = Klsf_Hdx($rs['ball_5']);
                    $zfb = Klsf_Zfb($rs['ball_5']);
                    $dnxb = Klsf_Dnxb($rs['ball_5']);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第六球
                if($rows['quick_type']=='第六球'){
                    $ds = Klsf_Ds($rs['ball_6']);
                    $dx = Klsf_Dx($rs['ball_6']);
                    $wdx = Klsf_Wdx($rs['ball_6']);
                    $hds = Klsf_Hdx($rs['ball_6']);
                    $zfb = Klsf_Zfb($rs['ball_6']);
                    $dnxb = Klsf_Dnxb($rs['ball_6']);
                    if($rows['number']==$rs['ball_6'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第七球
                if($rows['quick_type']=='第七球'){
                    $ds = Klsf_Ds($rs['ball_7']);
                    $dx = Klsf_Dx($rs['ball_7']);
                    $wdx = Klsf_Wdx($rs['ball_7']);
                    $hds = Klsf_Hdx($rs['ball_7']);
                    $zfb = Klsf_Zfb($rs['ball_7']);
                    $dnxb = Klsf_Dnxb($rs['ball_7']);
                    if($rows['number']==$rs['ball_7'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第八球
                if($rows['quick_type']=='第八球'){
                    $ds = Klsf_Ds($rs['ball_8']);
                    $dx = Klsf_Dx($rs['ball_8']);
                    $wdx = Klsf_Wdx($rs['ball_8']);
                    $hds = Klsf_Hdx($rs['ball_8']);
                    $zfb = Klsf_Zfb($rs['ball_8']);
                    $dnxb = Klsf_Dnxb($rs['ball_8']);
                    if($rows['number']==$rs['ball_8'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Klsf_Auto($hm,2);
                    if($zonghe=='总和和'){  //和局,退还本金
                        Utils::BalanceToAccount($rows,2,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$zonghe){
                            Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Klsf_Auto($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和尾大小
                if($rows['number']=='总和尾大' || $rows['number']=='总和尾小'){
                    $zonghe = Klsf_Auto($hm,4);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎
                if($rows['number']=='龙' || $rows['number']=='虎'){
                    $longhu = Klsf_Auto($hm,5);
                    if($longhu=='和'){  //和局,退还本金
                        Utils::BalanceToAccount($rows,2,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'重庆快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
            }

            LotteryResultCQSF::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "CQSF Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }   

    public function checkoutCQ(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultCQ::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 5; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("CQ" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds($rs['ball_1']);
                    $dx = Ssc_Dx($rs['ball_1']);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆时时彩'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds($rs['ball_2']);
                    $dx = Ssc_Dx($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds($rs['ball_3']);
                    $dx = Ssc_Dx($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第三球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Ssc_Ds($rs['ball_4']);
                    $dx = Ssc_Dx($rs['ball_4']);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第四球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Ssc_Ds($rs['ball_5']);
                    $dx = Ssc_Dx($rs['ball_5']);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto($hm,2);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){
                        Utils::BalanceToAccount($rows,2,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='前三'){
                    $qiansan = Ssc_Auto($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算中三
                if($rows['quick_type']=='中三'){
                    $zhongsan = Ssc_Auto($hm,6);
                    if($rows['number']==$zhongsan){
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算后三
                if($rows['quick_type']=='后三'){
                    $housan = Ssc_Auto($hm,7);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                    //开始结算牛牛
                if($rows['quick_type']=='牛牛'){
                    $housan = Ssc_Auto($hm,8);
                    $dsniu = Ssc_Auto($hm,9);
                    $dxniu = Ssc_Auto($hm,10);
                    if($rows['number']==$housan || $rows['number']==$dsniu || $rows['number']==$dxniu){
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                //开始结算梭哈
                if($rows['quick_type']=='梭哈'){
                    $housan = Ssc_Auto($hm,9);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'重庆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultCQ::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "CQ Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }      

    public function checkoutD3(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultD3::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 3; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("D3" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds($rs['ball_1']);
                    $dx = Ssc_Dx($rs['ball_1']);

                    
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds($rs['ball_2']);
                    $dx = Ssc_Dx($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds($rs['ball_3']);
                    $dx = Ssc_Dx($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto_D3($hm,2);
                    //echo $zonghe;exit;
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto_D3($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto_D3($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){  //买龙虎,开和退本金
                        Utils::BalanceToAccount($rows,2,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='三连'){
                    $qiansan=Ssc_Auto_D3($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                
                    //开始结算牛牛
                if($rows['quick_type']=='跨度'){
                    $numSpan=max(abs($rs['ball_1']-$rs['ball_2']),abs($rs['ball_1']-$rs['ball_3']),abs($rs['ball_2']-$rs['ball_3']));
                    if($rows['number']==$numSpan){
                        Utils::BalanceToAccount($rows,1,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'福彩3D');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultD3::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "D3 Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }        

    public function checkoutFFC5(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultFFC5::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 5; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("FFC5" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds($rs['ball_1']);
                    $dx = Ssc_Dx($rs['ball_1']);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'五分彩'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds($rs['ball_2']);
                    $dx = Ssc_Dx($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds($rs['ball_3']);
                    $dx = Ssc_Dx($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第三球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Ssc_Ds($rs['ball_4']);
                    $dx = Ssc_Dx($rs['ball_4']);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第四球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Ssc_Ds($rs['ball_5']);
                    $dx = Ssc_Dx($rs['ball_5']);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto($hm,2);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){
                        Utils::BalanceToAccount($rows,2,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='前三'){
                    $qiansan = Ssc_Auto($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算中三
                if($rows['quick_type']=='中三'){
                    $zhongsan = Ssc_Auto($hm,6);
                    if($rows['number']==$zhongsan){
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算后三
                if($rows['quick_type']=='后三'){
                    $housan = Ssc_Auto($hm,7);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                    //开始结算牛牛
                if($rows['quick_type']=='牛牛'){
                    $housan = Ssc_Auto($hm,8);
                    $dsniu = Ssc_Auto($hm,9);
                    $dxniu = Ssc_Auto($hm,10);
                    if($rows['number']==$housan || $rows['number']==$dsniu || $rows['number']==$dxniu){
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                //开始结算梭哈
                if($rows['quick_type']=='梭哈'){
                    $housan = Ssc_Auto($hm,9);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'五分彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultFFC5::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "FFC5 Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }      

    public function checkoutGD11X5(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultGD11::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 5; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("GD11" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds_GD11($rs['ball_1']);
                    $dx = Ssc_Dx_GD11($rs['ball_1']);
                    if($rs['ball_1']==11 and ($rows['number']=='大' or $rows['number']=='小' or $rows['number']=='单' or $rows['number']=='双')){
                        Utils::BalanceToAccount($rows,2,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                            Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广东11选5'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds_GD11($rs['ball_2']);
                    $dx = Ssc_Dx_GD11($rs['ball_2']);
                    if($rs['ball_2']==11 and ($rows['number']=='大' or $rows['number']=='小' or $rows['number']=='单' or $rows['number']=='双')){
                        Utils::BalanceToAccount($rows,2,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                            Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广东11选5'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds_GD11($rs['ball_3']);
                    $dx = Ssc_Dx_GD11($rs['ball_3']);
                    if($rs['ball_3']==11 and ($rows['number']=='大' or $rows['number']=='小' or $rows['number']=='单' or $rows['number']=='双')){
                        Utils::BalanceToAccount($rows,2,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                            Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广东11选5'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Ssc_Ds_GD11($rs['ball_4']);
                    $dx = Ssc_Dx_GD11($rs['ball_4']);
                    if($rs['ball_4']==11 and ($rows['number']=='大' or $rows['number']=='小' or $rows['number']=='单' or $rows['number']=='双')){
                        Utils::BalanceToAccount($rows,2,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx){
                            Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广东11选5'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Ssc_Ds_GD11($rs['ball_5']);
                    $dx = Ssc_Dx_GD11($rs['ball_5']);
                    if($rs['ball_5']==11 and ($rows['number']=='大' or $rows['number']=='小' or $rows['number']=='单' or $rows['number']=='双')){
                        Utils::BalanceToAccount($rows,2,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx){
                            Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广东11选5'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto_GD11($hm,2);
                    if($zonghe=='总和和'){
                        Utils::BalanceToAccount($rows,2,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$zonghe){
                            Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            //注单未中奖，返水金额大于0
                            Utils::BalanceToAccount($rows,0,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto_GD11($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto_GD11($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){
                        Utils::BalanceToAccount($rows,2,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='前三'){
                    $qiansan = Ssc_Auto_GD11($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算中三
                if($rows['quick_type']=='中三'){
                    $zhongsan = Ssc_Auto_GD11($hm,6);
                    if($rows['number']==$zhongsan){
                        Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算后三
                if($rows['quick_type']=='后三'){
                    $housan = Ssc_Auto_GD11($hm,7);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东11选5');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultGD11::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "GD11 Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }       

    public function checkoutGDSF(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultGDSF::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 8; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("GDSF" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Klsf_Ds($rs['ball_1']);
                    $dx = Klsf_Dx($rs['ball_1']);
                    $wdx = Klsf_Wdx($rs['ball_1']);
                    $hds = Klsf_Hdx($rs['ball_1']);
                    $zfb = Klsf_Zfb($rs['ball_1']);
                    $dnxb = Klsf_Dnxb($rs['ball_1']);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Klsf_Ds($rs['ball_2']);
                    $dx = Klsf_Dx($rs['ball_2']);
                    $wdx = Klsf_Wdx($rs['ball_2']);
                    $hds = Klsf_Hdx($rs['ball_2']);
                    $zfb = Klsf_Zfb($rs['ball_2']);
                    $dnxb = Klsf_Dnxb($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Klsf_Ds($rs['ball_3']);
                    $dx = Klsf_Dx($rs['ball_3']);
                    $wdx = Klsf_Wdx($rs['ball_3']);
                    $hds = Klsf_Hdx($rs['ball_3']);
                    $zfb = Klsf_Zfb($rs['ball_3']);
                    $dnxb = Klsf_Dnxb($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Klsf_Ds($rs['ball_4']);
                    $dx = Klsf_Dx($rs['ball_4']);
                    $wdx = Klsf_Wdx($rs['ball_4']);
                    $hds = Klsf_Hdx($rs['ball_4']);
                    $zfb = Klsf_Zfb($rs['ball_4']);
                    $dnxb = Klsf_Dnxb($rs['ball_4']);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Klsf_Ds($rs['ball_5']);
                    $dx = Klsf_Dx($rs['ball_5']);
                    $wdx = Klsf_Wdx($rs['ball_5']);
                    $hds = Klsf_Hdx($rs['ball_5']);
                    $zfb = Klsf_Zfb($rs['ball_5']);
                    $dnxb = Klsf_Dnxb($rs['ball_5']);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第六球
                if($rows['quick_type']=='第六球'){
                    $ds = Klsf_Ds($rs['ball_6']);
                    $dx = Klsf_Dx($rs['ball_6']);
                    $wdx = Klsf_Wdx($rs['ball_6']);
                    $hds = Klsf_Hdx($rs['ball_6']);
                    $zfb = Klsf_Zfb($rs['ball_6']);
                    $dnxb = Klsf_Dnxb($rs['ball_6']);
                    if($rows['number']==$rs['ball_6'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第七球
                if($rows['quick_type']=='第七球'){
                    $ds = Klsf_Ds($rs['ball_7']);
                    $dx = Klsf_Dx($rs['ball_7']);
                    $wdx = Klsf_Wdx($rs['ball_7']);
                    $hds = Klsf_Hdx($rs['ball_7']);
                    $zfb = Klsf_Zfb($rs['ball_7']);
                    $dnxb = Klsf_Dnxb($rs['ball_7']);
                    if($rows['number']==$rs['ball_7'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第八球
                if($rows['quick_type']=='第八球'){
                    $ds = Klsf_Ds($rs['ball_8']);
                    $dx = Klsf_Dx($rs['ball_8']);
                    $wdx = Klsf_Wdx($rs['ball_8']);
                    $hds = Klsf_Hdx($rs['ball_8']);
                    $zfb = Klsf_Zfb($rs['ball_8']);
                    $dnxb = Klsf_Dnxb($rs['ball_8']);
                    if($rows['number']==$rs['ball_8'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Klsf_Auto($hm,2);
                    if($zonghe=='总和和'){  //和局,退还本金
                        Utils::BalanceToAccount($rows,2,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$zonghe){
                            Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }

                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Klsf_Auto($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和尾大小
                if($rows['number']=='总和尾大' || $rows['number']=='总和尾小'){
                    $zonghe = Klsf_Auto($hm,4);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎
                if($rows['number']=='龙' || $rows['number']=='虎'){
                    $longhu = Klsf_Auto($hm,5);
                    if($longhu=='和'){  //和局,退还本金
                        Utils::BalanceToAccount($rows,2,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
            }

            LotteryResultGDSF::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "GDSF Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }        

    public function checkoutGXSF(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultGXSF::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 5; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("GXSF" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds_GXSF($rs['ball_1']);
                    $dx = Ssc_Dx_GXSF($rs['ball_1']);
                    if($hm[0]==21 and ($rows['number']=='大' or $rows['number']=='小' or $rows['number']=='单' or $rows['number']=='双')){
                            Utils::BalanceToAccount($rows,2,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                            Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds_GXSF($rs['ball_2']);
                    $dx = Ssc_Dx_GXSF($rs['ball_2']);
                    if($hm[1]==21 and ($rows['number']=='大' or $rows['number']=='小' or $rows['number']=='单' or $rows['number']=='双')){
                            Utils::BalanceToAccount($rows,2,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                            Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds_GXSF($rs['ball_3']);
                    $dx = Ssc_Dx_GXSF($rs['ball_3']);
                    if($hm[2]==21 and ($rows['number']=='大' or $rows['number']=='小' or $rows['number']=='单' or $rows['number']=='双')){
                            Utils::BalanceToAccount($rows,2,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                            Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Ssc_Ds_GXSF($rs['ball_4']);
                    $dx = Ssc_Dx_GXSF($rs['ball_4']);
                    if($hm[3]==21 and ($rows['number']=='大' or $rows['number']=='小' or $rows['number']=='单' or $rows['number']=='双')){
                            Utils::BalanceToAccount($rows,2,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx){
                            Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Ssc_Ds_GXSF($rs['ball_5']);
                    $dx = Ssc_Dx_GXSF($rs['ball_5']);
                    if($hm[4]==21 and ($rows['number']=='大' or $rows['number']=='小' or $rows['number']=='单' or $rows['number']=='双')){
                            Utils::BalanceToAccount($rows,2,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx){
                            Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto_GXSF($hm,2);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto_GXSF($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto_GXSF($hm,4);
                    if($longhu=='和' and($rows['number']=='龙' || $rows['number']=='虎')){
                        Utils::BalanceToAccount($rows,2,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='前三'){
                    $qiansan = Ssc_Auto_GXSF($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算中三
                if($rows['quick_type']=='中三'){
                    $zhongsan = Ssc_Auto_GXSF($hm,6);
                    if($rows['number']==$zhongsan){
                        Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算后三
                if($rows['quick_type']=='后三'){
                    $housan = Ssc_Auto_GXSF($hm,7);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广西快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultGXSF::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "GXSF Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }       

    public function checkoutP3(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultP3::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 3; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("P3" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds($rs['ball_1']);
                    $dx = Ssc_Dx($rs['ball_1']);

                    
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds($rs['ball_2']);
                    $dx = Ssc_Dx($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds($rs['ball_3']);
                    $dx = Ssc_Dx($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto_D3($hm,2);
                    //echo $zonghe;exit;
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto_D3($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto_D3($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){  //买龙虎,开和退本金
                        Utils::BalanceToAccount($rows,2,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='三连'){
                    $qiansan=Ssc_Auto_D3($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                
                    //开始结算牛牛
                if($rows['quick_type']=='跨度'){
                    $numSpan=max(abs($rs['ball_1']-$rs['ball_2']),abs($rs['ball_1']-$rs['ball_3']),abs($rs['ball_2']-$rs['ball_3']));
                    if($rows['number']==$numSpan){
                        Utils::BalanceToAccount($rows,1,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'排列3');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultP3::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "P3 Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }       

    public function checkoutT3(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultT3::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 3; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("T3" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds($rs['ball_1']);
                    $dx = Ssc_Dx($rs['ball_1']);

                    
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds($rs['ball_2']);
                    $dx = Ssc_Dx($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds($rs['ball_3']);
                    $dx = Ssc_Dx($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto_D3($hm,2);
                    //echo $zonghe;exit;
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto_D3($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto_D3($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){  //买龙虎,开和退本金
                        Utils::BalanceToAccount($rows,2,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='三连'){
                    $qiansan=Ssc_Auto_D3($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                
                    //开始结算牛牛
                if($rows['quick_type']=='跨度'){
                    $numSpan=max(abs($rs['ball_1']-$rs['ball_2']),abs($rs['ball_1']-$rs['ball_3']),abs($rs['ball_2']-$rs['ball_3']));
                    if($rows['number']==$numSpan){
                        Utils::BalanceToAccount($rows,1,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'上海时时乐');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultT3::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "T3 Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

    public function checkoutTJSF(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultTJSF::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 8; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("TJSF" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Klsf_Ds($rs['ball_1']);
                    $dx = Klsf_Dx($rs['ball_1']);
                    $wdx = Klsf_Wdx($rs['ball_1']);
                    $hds = Klsf_Hdx($rs['ball_1']);
                    $zfb = Klsf_Zfb($rs['ball_1']);
                    $dnxb = Klsf_Dnxb($rs['ball_1']);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Klsf_Ds($rs['ball_2']);
                    $dx = Klsf_Dx($rs['ball_2']);
                    $wdx = Klsf_Wdx($rs['ball_2']);
                    $hds = Klsf_Hdx($rs['ball_2']);
                    $zfb = Klsf_Zfb($rs['ball_2']);
                    $dnxb = Klsf_Dnxb($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Klsf_Ds($rs['ball_3']);
                    $dx = Klsf_Dx($rs['ball_3']);
                    $wdx = Klsf_Wdx($rs['ball_3']);
                    $hds = Klsf_Hdx($rs['ball_3']);
                    $zfb = Klsf_Zfb($rs['ball_3']);
                    $dnxb = Klsf_Dnxb($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Klsf_Ds($rs['ball_4']);
                    $dx = Klsf_Dx($rs['ball_4']);
                    $wdx = Klsf_Wdx($rs['ball_4']);
                    $hds = Klsf_Hdx($rs['ball_4']);
                    $zfb = Klsf_Zfb($rs['ball_4']);
                    $dnxb = Klsf_Dnxb($rs['ball_4']);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Klsf_Ds($rs['ball_5']);
                    $dx = Klsf_Dx($rs['ball_5']);
                    $wdx = Klsf_Wdx($rs['ball_5']);
                    $hds = Klsf_Hdx($rs['ball_5']);
                    $zfb = Klsf_Zfb($rs['ball_5']);
                    $dnxb = Klsf_Dnxb($rs['ball_5']);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第六球
                if($rows['quick_type']=='第六球'){
                    $ds = Klsf_Ds($rs['ball_6']);
                    $dx = Klsf_Dx($rs['ball_6']);
                    $wdx = Klsf_Wdx($rs['ball_6']);
                    $hds = Klsf_Hdx($rs['ball_6']);
                    $zfb = Klsf_Zfb($rs['ball_6']);
                    $dnxb = Klsf_Dnxb($rs['ball_6']);
                    if($rows['number']==$rs['ball_6'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第七球
                if($rows['quick_type']=='第七球'){
                    $ds = Klsf_Ds($rs['ball_7']);
                    $dx = Klsf_Dx($rs['ball_7']);
                    $wdx = Klsf_Wdx($rs['ball_7']);
                    $hds = Klsf_Hdx($rs['ball_7']);
                    $zfb = Klsf_Zfb($rs['ball_7']);
                    $dnxb = Klsf_Dnxb($rs['ball_7']);
                    if($rows['number']==$rs['ball_7'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第八球
                if($rows['quick_type']=='第八球'){
                    $ds = Klsf_Ds($rs['ball_8']);
                    $dx = Klsf_Dx($rs['ball_8']);
                    $wdx = Klsf_Wdx($rs['ball_8']);
                    $hds = Klsf_Hdx($rs['ball_8']);
                    $zfb = Klsf_Zfb($rs['ball_8']);
                    $dnxb = Klsf_Dnxb($rs['ball_8']);
                    if($rows['number']==$rs['ball_8'] || $rows['number']==$ds || $rows['number']==$dx || $rows['number']==$wdx || $rows['number']==$hds || $rows['number']==$zfb || $rows['number']==$dnxb){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Klsf_Auto($hm,2);
                    if($zonghe=='总和和'){  //和局,退还本金
                        Utils::BalanceToAccount($rows,2,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$zonghe){
                            Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }

                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Klsf_Auto($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和尾大小
                if($rows['number']=='总和尾大' || $rows['number']=='总和尾小'){
                    $zonghe = Klsf_Auto($hm,4);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎
                if($rows['number']=='龙' || $rows['number']=='虎'){
                    $longhu = Klsf_Auto($hm,5);
                    if($longhu=='和'){  //和局,退还本金
                        Utils::BalanceToAccount($rows,2,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'广东快乐十分');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
            }

            LotteryResultTJSF::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "TJSF Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }    

    public function checkoutTJ(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultTJ::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 5; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("TJ" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds($rs['ball_1']);
                    $dx = Ssc_Dx($rs['ball_1']);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'天津时时彩'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds($rs['ball_2']);
                    $dx = Ssc_Dx($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds($rs['ball_3']);
                    $dx = Ssc_Dx($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第三球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Ssc_Ds($rs['ball_4']);
                    $dx = Ssc_Dx($rs['ball_4']);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第四球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Ssc_Ds($rs['ball_5']);
                    $dx = Ssc_Dx($rs['ball_5']);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto($hm,2);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){
                        Utils::BalanceToAccount($rows,2,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='前三'){
                    $qiansan = Ssc_Auto($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算中三
                if($rows['quick_type']=='中三'){
                    $zhongsan = Ssc_Auto($hm,6);
                    if($rows['number']==$zhongsan){
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算后三
                if($rows['quick_type']=='后三'){
                    $housan = Ssc_Auto($hm,7);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                    //开始结算牛牛
                if($rows['quick_type']=='牛牛'){
                    $housan = Ssc_Auto($hm,8);
                    $dsniu = Ssc_Auto($hm,9);
                    $dxniu = Ssc_Auto($hm,10);
                    if($rows['number']==$housan || $rows['number']==$dsniu || $rows['number']==$dxniu){
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                //开始结算梭哈
                if($rows['quick_type']=='梭哈'){
                    $housan = Ssc_Auto($hm,9);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'天津时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultTJ::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "TJ Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }     

    public function checkoutTWSSC(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultTWSSC::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 5; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("TWSSC" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds($rs['ball_1']);
                    $dx = Ssc_Dx($rs['ball_1']);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'台湾时时彩'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds($rs['ball_2']);
                    $dx = Ssc_Dx($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds($rs['ball_3']);
                    $dx = Ssc_Dx($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第三球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Ssc_Ds($rs['ball_4']);
                    $dx = Ssc_Dx($rs['ball_4']);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第四球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Ssc_Ds($rs['ball_5']);
                    $dx = Ssc_Dx($rs['ball_5']);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto($hm,2);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){
                        Utils::BalanceToAccount($rows,2,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='前三'){
                    $qiansan = Ssc_Auto($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算中三
                if($rows['quick_type']=='中三'){
                    $zhongsan = Ssc_Auto($hm,6);
                    if($rows['number']==$zhongsan){
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算后三
                if($rows['quick_type']=='后三'){
                    $housan = Ssc_Auto($hm,7);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                    //开始结算牛牛
                if($rows['quick_type']=='牛牛'){
                    $housan = Ssc_Auto($hm,8);
                    $dsniu = Ssc_Auto($hm,9);
                    $dxniu = Ssc_Auto($hm,10);
                    if($rows['number']==$housan || $rows['number']==$dsniu || $rows['number']==$dxniu){
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                //开始结算梭哈
                if($rows['quick_type']=='梭哈'){
                    $housan = Ssc_Auto($hm,9);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'台湾时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultTWSSC::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "TWSSC Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }     

    public function checkoutTXSSC(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultTXSSC::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 5; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("TXSSC" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds($rs['ball_1']);
                    $dx = Ssc_Dx($rs['ball_1']);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds($rs['ball_2']);
                    $dx = Ssc_Dx($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds($rs['ball_3']);
                    $dx = Ssc_Dx($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第三球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Ssc_Ds($rs['ball_4']);
                    $dx = Ssc_Dx($rs['ball_4']);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第四球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Ssc_Ds($rs['ball_5']);
                    $dx = Ssc_Dx($rs['ball_5']);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto($hm,2);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){
                        Utils::BalanceToAccount($rows,2,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='前三'){
                    $qiansan = Ssc_Auto($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算中三
                if($rows['quick_type']=='中三'){
                    $zhongsan = Ssc_Auto($hm,6);
                    if($rows['number']==$zhongsan){
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算后三
                if($rows['quick_type']=='后三'){
                    $housan = Ssc_Auto($hm,7);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                    //开始结算牛牛
                if($rows['quick_type']=='牛牛'){
                    $housan = Ssc_Auto($hm,8);
                    $dsniu = Ssc_Auto($hm,9);
                    $dxniu = Ssc_Auto($hm,10);
                    if($rows['number']==$housan || $rows['number']==$dsniu || $rows['number']==$dxniu){
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                //开始结算梭哈
                if($rows['quick_type']=='梭哈'){
                    $housan = Ssc_Auto($hm,9);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'腾讯时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultTXSSC::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "TXSSC Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }     

    public function checkoutXJSSC(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultJX::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 5; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("JX" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算第一球
                if($rows['quick_type']=='第一球'){
                    $ds = Ssc_Ds($rs['ball_1']);
                    $dx = Ssc_Dx($rs['ball_1']);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'新疆时时彩'); //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第二球
                if($rows['quick_type']=='第二球'){
                    $ds = Ssc_Ds($rs['ball_2']);
                    $dx = Ssc_Dx($rs['ball_2']);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三球
                if($rows['quick_type']=='第三球'){
                    $ds = Ssc_Ds($rs['ball_3']);
                    $dx = Ssc_Dx($rs['ball_3']);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第三球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四球
                if($rows['quick_type']=='第四球'){
                    $ds = Ssc_Ds($rs['ball_4']);
                    $dx = Ssc_Dx($rs['ball_4']);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第四球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五球
                if($rows['quick_type']=='第五球'){
                    $ds = Ssc_Ds($rs['ball_5']);
                    $dx = Ssc_Dx($rs['ball_5']);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx){
                        //如果投注内容等于第一球开奖号码，则视为中奖
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和大小
                if($rows['number']=='总和大' || $rows['number']=='总和小'){
                    $zonghe = Ssc_Auto($hm,2);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        //注单未中奖，返水金额大于0
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算总和单双
                if($rows['number']=='总和单' || $rows['number']=='总和双'){
                    $zonghe = Ssc_Auto($hm,3);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算龙虎和
                if($rows['number']=='龙' || $rows['number']=='虎' || $rows['number']=='和'){
                    $longhu = Ssc_Auto($hm,4);
                    if($longhu=='和' and ($rows['number']=='龙' || $rows['number']=='虎')){
                        Utils::BalanceToAccount($rows,2,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$longhu){
                            Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算前三
                if($rows['quick_type']=='前三'){
                    $qiansan = Ssc_Auto($hm,5);
                    if($rows['number']==$qiansan){
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算中三
                if($rows['quick_type']=='中三'){
                    $zhongsan = Ssc_Auto($hm,6);
                    if($rows['number']==$zhongsan){
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算后三
                if($rows['quick_type']=='后三'){
                    $housan = Ssc_Auto($hm,7);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                    //开始结算牛牛
                if($rows['quick_type']=='牛牛'){
                    $housan = Ssc_Auto($hm,8);
                    $dsniu = Ssc_Auto($hm,9);
                    $dxniu = Ssc_Auto($hm,10);
                    if($rows['number']==$housan || $rows['number']==$dsniu || $rows['number']==$dxniu){
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                
                //开始结算梭哈
                if($rows['quick_type']=='梭哈'){
                    $housan = Ssc_Auto($hm,9);
                    if($rows['number']==$housan){
                        Utils::BalanceToAccount($rows,1,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'新疆时时彩');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultJX::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "JX Lottery Result checkouted successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }     

    public function checkoutXYFT(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $rs = LotteryResultXYFT::where("state", 0)->orderBy("qishu", "desc")->first();

            if (!isset($rs)) {
                return;
            }

            $hm = array();

            for ($i=1; $i <= 10; $i++) {
                $hm[]=$rs['ball_'.$i];
            }

            $qishu = $rs['qishu'];

            $orders = Utils::getOrdersByStatus("XYFT" , $qishu, "0");

            foreach($orders as $rows) {

                $rows = get_object_vars($rows);

                //开始结算冠军
                if($rows['quick_type']=='冠军'){
                    $ds = Bjsc_Ds($rs['ball_1']);
                    $dx = Bjsc_Dx($rs['ball_1']);
                    $longhu=Bjsc_Auto($hm,4);
                    if($rows['number']==$rs['ball_1'] || $rows['number']==$ds || $rows['number']==$dx ||  $rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算亚军
                if($rows['quick_type']=='亚军'){
                    $ds = Bjsc_Ds($rs['ball_2']);
                    $dx = Bjsc_Dx($rs['ball_2']);
                    $longhu=Bjsc_Auto($hm,5);
                    if($rows['number']==$rs['ball_2'] || $rows['number']==$ds || $rows['number']==$dx ||  $rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第三名
                if($rows['quick_type']=='第三名'){
                    $ds = Bjsc_Ds($rs['ball_3']);
                    $dx = Bjsc_Dx($rs['ball_3']);
                    $longhu=Bjsc_Auto($hm,6);
                    if($rows['number']==$rs['ball_3'] || $rows['number']==$ds || $rows['number']==$dx ||  $rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第四名
                if($rows['quick_type']=='第四名'){
                    $ds = Bjsc_Ds($rs['ball_4']);
                    $dx = Bjsc_Dx($rs['ball_4']);
                    $longhu=Bjsc_Auto($hm,7);
                    if($rows['number']==$rs['ball_4'] || $rows['number']==$ds || $rows['number']==$dx ||  $rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第五名
                if($rows['quick_type']=='第五名'){
                    $ds = Bjsc_Ds($rs['ball_5']);
                    $dx = Bjsc_Dx($rs['ball_5']);
                    $longhu=Bjsc_Auto($hm,8);
                    if($rows['number']==$rs['ball_5'] || $rows['number']==$ds || $rows['number']==$dx ||  $rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第六名
                if($rows['quick_type']=='第六名'){
                    $ds = Bjsc_Ds($rs['ball_6']);
                    $dx = Bjsc_Dx($rs['ball_6']);
                    if($rows['number']==$rs['ball_6'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第七名
                if($rows['quick_type']=='第七名'){
                    $ds = Bjsc_Ds($rs['ball_7']);
                    $dx = Bjsc_Dx($rs['ball_7']);
                    if($rows['number']==$rs['ball_7'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第八名
                if($rows['quick_type']=='第八名'){
                    $ds = Bjsc_Ds($rs['ball_8']);
                    $dx = Bjsc_Dx($rs['ball_8']);
                    if($rows['number']==$rs['ball_8'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第九名
                if($rows['quick_type']=='第九名'){
                    $ds = Bjsc_Ds($rs['ball_9']);
                    $dx = Bjsc_Dx($rs['ball_9']);
                    if($rows['number']==$rs['ball_9'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算第十名
                if($rows['quick_type']=='第十名'){
                    $ds = Bjsc_Ds($rs['ball_10']);
                    $dx = Bjsc_Dx($rs['ball_10']);
                    if($rows['number']==$rs['ball_10'] || $rows['number']==$ds || $rows['number']==$dx){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算冠、亚军和
                if($rows['quick_type']=='冠亚军和' && $rows['number']>=3 && $rows['number']<=19){
                    $zonghe = Bjsc_Auto($hm,1);
                    if($rows['number']==$zonghe){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }

                //开始结算冠、亚军和大小
                if($rows['quick_type']=='冠亚军和' && ($rows['number']=='大' || $rows['number']=='小')){
                    $zonghe = Bjsc_Auto($hm,2);
                    if($zonghe=='和'){  //和局,退还本金
                        Utils::BalanceToAccount($rows,2,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$zonghe){ 
                            Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算冠、亚军和单双
                if($rows['quick_type']=='冠亚军和' && ($rows['number']=='单' || $rows['number']=='双')){
                    $zonghe = Bjsc_Auto($hm,3);
                    if($zonghe=='和'){  //和局,退还本金
                        Utils::BalanceToAccount($rows,2,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        if($rows['number']==$zonghe){
                            Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }else{
                            Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                        }
                    }
                }
                //开始结算1V10 龙虎
                if($rows['quick_type']=='1V10龙虎'){
                    $longhu = Bjsc_Auto($hm,4);
                    if($rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算2V9 龙虎
                if($rows['quick_type']=='2V9龙虎'){
                    $longhu = Bjsc_Auto($hm,5);
                    if($rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算3V8 龙虎
                if($rows['quick_type']=='3V8龙虎'){
                    $longhu = Bjsc_Auto($hm,6);
                    if($rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算4V7 龙虎
                if($rows['quick_type']=='4V7龙虎'){
                    $longhu = Bjsc_Auto($hm,7);
                    if($rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
                //开始结算5V6 龙虎
                if($rows['quick_type']=='5V6龙虎'){
                    $longhu = Bjsc_Auto($hm,8);
                    if($rows['number']==$longhu){
                        Utils::BalanceToAccount($rows,1,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }else{
                        Utils::BalanceToAccount($rows,0,'幸运飞艇');  //is_win值:0未中奖 1中奖 2和局 3赢一半
                    }
                }
            }

            LotteryResultXYFT::where("qishu", $qishu)
                ->update(["state" => 1]);

            $response['message'] = "XYFT Lottery Result checkouted successfully!";
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
