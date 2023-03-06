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
class AccountController extends Controller {

    public function __construct(){
        //$this->middleware("auth:api");
    }
    /* Get bank info. */
    public function getUserBankAccounts(Request $request) {
        $bankList = UserAccount::where('user_id', intval($request->userId))->get();
        return response()->json(['success'=>true, 'bankList' => $bankList]);
    }

    /* Add Bank function. */
    public function addBankAccount(Request $request) {
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
            return response()->json(['success'=>true, 'bankList' => $bankList]);
        }else{
            return response()->json(['success'=>false, 'message' => `操作失败!!!`]);
        }
    }

    /* Add Edit function. */
    public function editBankAccount(Request $request) {
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
            return response()->json(['success'=>true, 'bankList' => $bankList]);
        }else{
            return response()->json(['success'=>false, 'message' => `操作失败!!!`]);
        };

    }

}
