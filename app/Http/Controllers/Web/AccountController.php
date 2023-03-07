<?php
namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Web\Bank;
use Auth;
use Validator;
use App\Models\Web\Sys800;
use App\Models\Web\SysConfig;
use App\Models\User;
use App\Utils\Utils;
use Illuminate\Support\Facades\DB;
use App\Models\Web\MoneyLog;
use App\Models\Web\UserAccount;
use App\Models\Web\UserBankAccount;
class AccountController extends Controller {

    public function __construct(){
        //$this->middleware("auth:api");
    }
    /* Get crypto info. */
    public function getUserCryptoAccounts(Request $request) {
        $bankList = UserAccount::where('user_id', intval($request->userId))->get();
        return response()->json(['success'=>true, 'bankList' => $bankList]);
    }
    /* Get bank info. */
    public function getUserBankAccounts(Request $request) {
        $bankList = UserBankAccount::where('user_id', intval($request->userId))->get();
        return response()->json(['success'=>true, 'bankList' => $bankList]);
    }
    /* Add Crypto Account function. */
    public function addCryptoAccount(Request $request) {
        $bank_account=Utils::SafeString($request["bank_account"]);
	    $bank_address=Utils::SafeString($request["bank_address"]);
        $bank = $request["bank"];
        $bank_type = $request["bank_type"];

        if($bank_account=="" or $bank_address=="" or $bank==""){
            return response()->json(['success'=>false, 'message' => "非法参数!"]);
        }
        $originbank = UserAccount::where('bank_address',$bank_address)->first();
        if($originbank){
            return response()->json(['success'=>false, 'message' => "非法参数!"]);
        }
        $data = [
            "bank" => $bank,
            "bank_account" => $bank_account,
            "bank_address" => $bank_address,
            "bank_type" => $bank_type,
            "user_id" => $request->userId
        ];
        $deposit = new UserAccount;
        $result = $deposit->create($data);
        if ($result){
            $bankList = UserAccount::where('user_id', intval($request->userId))->get();
            return response()->json(['success'=>true, 'message'=>'操作成功', 'bankList' => $bankList]);
        }else{
            return response()->json(['success'=>false, 'message' => '操作失败!!!']);
        }
    }

    /* Edit Crypto Account function. */
    public function editCryptoAccount(Request $request) {
        $bank_id = Utils::SafeString($request["bank_id"]);
        $bank_account=Utils::SafeString($request["bank_account"]);
	    $bank_address=Utils::SafeString($request["bank_address"]);
        $bank = $request["bank"];
        $bank_type = $request["bank_type"];

        if($bank_account=="" or $bank_address=="" or $bank==""){
            return response()->json(['success'=>false, 'message' => "非法参数!"]);
        }

        $data = [
            "bank" => $bank,
            "bank_account" => $bank_account,
            "bank_address" => $bank_address,
            "bank_type" => $bank_type,
            "user_id" => $request->userId
        ];
        $update = UserAccount::where('id', intval($bank_id))->update($data);
        if($update){
            $bankList = UserAccount::where('user_id', intval($request->userId))->get();
            return response()->json(['success'=>true,'message'=>'操作成功',  'bankList' => $bankList]);
        }else{
            return response()->json(['success'=>false, 'message' => '操作失败!!!']);
        };

    }

    /* Add Bank Account function. */
    public function addBankAccount(Request $request) {
        $bank_card_owner = $request["bank_card_owner"];
        $bank_card_type = $request["bank_card_type"];
        $bank_type = $request["bank_type"];
        $bank_account = Utils::SafeString($request["bank_account"]);
        $bank_address = Utils::SafeString($request["bank_address"]);

        if($bank_account=="" or $bank_address=="" or $bank_card_owner=="" or $bank_card_type==""){
            return response()->json(['success'=>false, 'message' => "非法参数!"]);
        }

        $originbank = UserBankAccount::where('bank_account',$bank_account)->first();
        if($originbank){
            return response()->json(['success'=>false, 'message' => "非法参数!"]);
        }
        $data = [
            "bank_card_owner" => $bank_card_owner,
            "bank_card_type" => $bank_card_type,
            "bank_account" => $bank_account,
            "bank_address" => $bank_address,
            "bank_type" => $bank_type,
            "user_id" => $request->userId,
            "status" => 1
        ];
        $deposit = new UserBankAccount;
        $result = $deposit->create($data);
        if ($result){
            $bankList = UserBankAccount::where('user_id', intval($request->userId))->get();
            return response()->json(['success'=>true,'message'=>'操作成功',  'bankList' => $bankList]);
        }else{
            return response()->json(['success'=>false, 'message' => '操作失败!!!']);
        }
    }

    /* Edit Crypto Account function. */
    public function editBankAccount(Request $request) {

        $bank_id = Utils::SafeString($request["bank_id"]);
        $bank_card_owner = $request["bank_card_owner"];
        $bank_type = $request["bank_type"];
        $bank_account=Utils::SafeString($request["bank_account"]);

        if($bank_account=="" or $bank_type=="" or $bank_card_owner==""){
            return response()->json(['success'=>false, 'message' => "非法参数!"]);
        }

        $data = [
            "bank_card_owner" => $bank_card_owner,
            "bank_account" => $bank_account,
            "bank_type" => $bank_type,
            "user_id" => $request->userId,
            "status" => 1
        ];
        $update = UserBankAccount::where('id', intval($bank_id))->update($data);
        if($update){
            $bankList = UserBankAccount::where('user_id', intval($request->userId))->get();
            return response()->json(['success'=>true,'message'=>'操作成功',  'bankList' => $bankList]);
        }else{
            return response()->json(['success'=>false, 'message' => '操作失败!!!']);
        };
    }
}
