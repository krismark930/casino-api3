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
class WithdrawController extends Controller {

    public function __construct(){
        //$this->middleware("auth:api");
    }
    /* Get bank info. */
    public function getBank(Request $request) {
        $bank = Bank::all();
        return response()->json(['success'=>true, 'bankList' => $bank]);
    }
    /* withdraw function. */
    public function quickWithdraw(Request $request) {
        $money=intval($request["money"]);
        $adddate=date("Y-m-d");
        $date=date("Y-m-d H:i:s");
        $user = User::where('id',$request->userId)->first();
        $sysConfig = SysConfig::all()->first();
        $min_amount2 = $sysConfig['min_qukuan_money'];
        $username=$user['UserName'];
        $curtype=$user['CurType'];
        $agents=$user['Agents'];
        $world=$user['World'];
        $corprator =$user['Corprator'];
        $super=$user['Super'];
        $admin=$user['Admin'];
        $phone=$user['Phone'];
        $alias=$user['Alias'];

        $bank_account=Utils::SafeString($request["Bank_Account"]);
	    $bank_Address=Utils::SafeString($request["Bank_Address"]);
        if($bank_account=="" or $bank_Address==""){
            return response()->json(['success'=>false, 'message' => "非法参数!"]);
        }
        $bank="";
        $Order_Code='TK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
        if($money==""){
            return response()->json(['success'=>false, 'message' => "你提交非法参数！"]);
        }
        if($money<=0)
            return response()->json(['success'=>false, 'message' => "你提交非法参数！"]);
        if($money<$min_amount2){
            return response()->json(['success'=>false, 'message' => "提款失败!原因:提款金额不能低于{$min_amount2}元."]);
        }
        if($money>$user['Money']){
            return response()->json(['success'=>false, 'message' => "提款失败!原因:提款金额大于账户资金."]);
        }

        //if ($key=="Y"){ $key=$_REQUEST["Key"];
        //if($contact==$user['Address'] or md5($contact)==$user['Address']){
            $previousAmount=Utils::GetField($username,'Money');
            $currentAmount=$previousAmount-$money;
            $data = [
                "Gold" => $money,
                "previousAmount" => $previousAmount,
                "currentAmount" => $currentAmount,
                "AddDate" => $adddate,
                "Type" => 'T',
                "UserName" => $username,
                "Agents" => $agents,
                "World" => $world?$world:'',
                "Corprator" => $corprator?$corprator:'',
                "Super" => $super?$super:'',
                "Admin" => $admin?$admin:'',
                "CurType" => $curtype,
                "Date" => $date,
                "Name" => $alias,
                "User" => $username,
                "Phone" => $phone,
                "Contact" => $user['Address'],
                "Bank" => $bank?$bank:'',
                "Bank_Address" => $bank_Address?$bank_Address:'',
                "Bank_Account" => $bank_account,
                "Order_Code" => $Order_Code,
            ];
            $deposit = new Sys800;
            $result = $deposit->create($data);
            if ($result){
            }else{
                return response()->json(['success'=>false, 'message' => '操作失败!!!']);
            }
            $ouid = $result->id;

            $assets=$previousAmount;
            Utils::ProcessUpdate($username);  //防止并发
            $result = DB::update("update web_member_data set Money=Money-".$money.",Credit=Credit-".$money." where Username='".$username."'");

            if($result){
                $balance=Utils::GetField($username,'Money');
                $datetime=date("Y-m-d H:i:s",time()+12*3600);
                $moneyLogData = [
                    "user_id" => $user['id'],
                    "order_num" => $Order_Code,
                    "about" => "会员申请提款",
                    "update_time" => $datetime,
                    "type" => "会员申请提款",
                    "order_value" => -$money,
                    "assets" => $assets,
                    "balance" => $balance,
                ];

                $moneyLog = new MoneyLog;
                $result = $moneyLog->create($moneyLogData);
                if ($result){
                }else{
                    return response()->json(['success'=>false, 'message'=> '操作失败!!!']);
                }
            }else{
                $res=Sys800::where('id',$ouid)->delete();
                return response()->json(['success'=>false, 'message'=> '提款不成功,请联系在线客服!']);
            }
            return response()->json(['success'=>true, 'message' => "提款成功!!!"]);
            //MoneyToSsc($username);
        // }else{
        //     echo "<Script language=javascript>alert('提款失败!原因:提款密码不正确.');location.href='record.php?uid=$uid&langx=$langx&username=$username';</script>";
        // }
        //}
    }

    /* Get transaction history. */
    public function getTransactionHistory(Request $request) {
        if($request->type == "transfer"){
            $withdrawHistory = Sys800::where('UserName', $request->username)->where('Type2', $request->type2)->orderBy('id', 'desc')->get();
            return response()->json(['success'=>true, 'historyList' => $withdrawHistory]);
        }
        $withdrawHistory = Sys800::where('UserName', $request->username)->where('Type', $request->type)->where('Type2', $request->type2)->orderBy('id', 'desc')->get();
        return response()->json(['success'=>true, 'historyList' => $withdrawHistory]);
    }
}
