<?php
namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Web\Bank;
use App\Models\Web\UserBankAccount;
use App\Models\Web\UserAccount;
use App\Models\Web\Sys800;
use App\Models\Web\SysConfig;
use App\Models\User;
use Carbon\Carbon;

class DepositController extends Controller {

    /* Get bank info. */
    public function getCrypto(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $crypto_type = $request_data["crypto_type"];

            $user = $request->user();

            $crypto = UserAccount::where("user_id", $user["id"])
                    ->where("bank_account", $crypto_type)->first();

            $response["data"] = $crypto;
            $response['message'] = "Crypto Data fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    /* Get bank info. */
    public function getBank(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $request_data = $request->all();

            $bank_card_type = $request_data["bank_card_type"];

            $user = $request->user();

            $user_bank = UserBankAccount::where("user_id", $user["id"])->where("bank_card_type", $bank_card_type)->get();

            $response["bankList"] = $user_bank;
            $response['message'] = "Bank List fetched successfully!";
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    /* Deposit function. */
    public function addMoney(Request $request) {
        $user = User::find($request->userId);
        $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
        $validator = Validator::make($request->all(),[
            'isCrypto' => 'required',
            'money' => 'required',
            'name' => 'required',
            'bank' => 'required',
            'bankAddress' => 'required',
            'bankAccount' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }
        error_log($request->isCrypto);
        //$user = Auth::guard("api")->user();
        $date=Carbon::now('Asia/Hong_Kong')->format('Y-m-d');;
        $payWay="W";
        $type="S";
        $data = [
            "Payway" => $payWay,
            "Gold" => $request->money,
            "AddDate" => $date,
            "Type" => $type,
            "Type2" => "1",
            "UserName" => $user->UserName,
            "Agents" => $user->Agents,
            "World" => $user->World,
            "Corprator" => $user->Corprator,
            "Super" => $user->Super,
            "Admin" => $user->Admin,
            "CurType" => 'RMB',
            "Name" => $request->isCrypto ? $user->Alias : $request->name,//$user->Alias,
            "Bank" => $request->bank,
            "Bank_Address" => $request->bankAddress,
            "Bank_Account" => $request->bankAccount,
            "Order_Code" => $Order_Code,
        ];
        $deposit = new Sys800;
        if ($deposit->create($data)){
            $ckfanli = SysConfig::select('ckfanli')->first()->ckfanli;
            error_log($ckfanli);
            if($ckfanli>0){
                $money= $request->money*$ckfanli/100;
                $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                $new_data = [
                    "Payway" => $payWay,
                    "Gold" => $money,
                    "AddDate" => $date,
                    "Type" => $type,
                    "Type2" => "2",
                    "UserName" => $user->UserName,
                    "Agents" => $user->Agents,
                    "World" => $user->World,
                    "Corprator" => $user->Corprator,
                    "Super" => $user->Super,
                    "Admin" => $user->Admin,
                    "CurType" => 'RMB',
                    "Name" => $user->Alias,
                    "Bank" => "彩金",
                    "Bank_Address" => "彩金",
                    "Bank_Account" => "彩金",
                    "Order_Code" => $Order_Code,
                ];

                $deposit = new Sys800;

                if ($deposit->create($new_data)){
                    return response()->json(['success'=>true, 'order_code'=> $Order_Code, 'message'=>'deposit successfully.'], 200);
                }else {
                    return response()->json(['success'=>false, 'message'=>'rebate 提款成功!!!']);
                }
            }else{
                return response()->json(['success'=>true, 'order_code'=> $Order_Code, 'message'=>'deposit successfully.'], 200);
            }
        }
        else
            return response()->json(['success'=>false, 'message'=>'提款成功!!!']);
    }
}
