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
use App\Models\Web\BBINLogs;
use App\Models\Web\MoneyLog;
use App\Models\Web\AGLogs;
use App\Models\Web\OGLogs;
use App\Models\Web\MGLogs;
use App\Models\Web\PTLogs;
use App\Models\Web\KYLogs;
use Illuminate\Support\Facades\DB;
use App\Utils\BBIN\bbinUtils;
use App\Utils\AG\agUtils;
use App\Utils\OG\ogUtils;
use App\Utils\MG\mgUtils;
use App\Utils\PT\ptUtils;
use App\Utils\KY\kyUtils;
use App\Utils\Utils;
class TransferController extends Controller {

    public function __construct() {
        //$this->middleware("auth:api");
    }
    // BBIN handle
    function handleBBIN(Request $request, User $user, SysConfig $sysConfig ) {
        $BBINUtils = new BBINUtils($sysConfig);
        $BBIN_username = $user->BBIN_User;
        $BBIN_password = $user->BBIN_Pass;
        $username = $user->UserName;
        $alias = $user->Alias;
        $money = intval($request["money"]);
        $tp=str_replace("BB","",$request->type);
        if(!is_numeric($money) || intval($money)<=0 || ($tp<>'IN' and $tp<>'OUT')){
            return response()->json(['success'=>false, 'message'=> '']);
        }
        //限制额度 开始
        $money2=0;
        if($tp=='IN') $money2=$money;  //转入加上转入额度

        $BBIN_Limit=intval($sysConfig['BBIN_Limit']);
        if($sysConfig['BBIN_Repair']==1 or $sysConfig['BBIN']==0 or $user['BBIN']==0){
            return response()->json(['success'=>false, 'message'=> '真人平台维护中，请稍候再试......']);
        }

        $date=date("Y-m-d");

        $row2  = DB::select("select sum(Gold) as IN_Money from bbin_logs where Type='IN' and left(DateTime,10)='$date'");
        $IN_Money=intval($row2[0]->IN_Money);

        $row2  = DB::select("select sum(Gold) as OUT_Money from bbin_logs where Type='OUT' and left(DateTime,10)='$date'");
        $OUT_Money=intval($row2[0]->OUT_Money);
        if(($IN_Money+$money2-$OUT_Money)>$BBIN_Limit){
            return response()->json(['success'=>false, 'message'=> '额度转换维护中，请联系客服人员']);
        }

        //限制额度 结束
        $adddate=date("Y-m-d");
        $date=date("Y-m-d H:i:s");
        $curtype=$user['CurType'];
        $agents=$user['Agents'];
        $world=$user['World'];
        $corprator =$user['Corprator'];
        $super=$user['Super'];
        $admin=$user['Admin'];
        $phone=$user['Phone'];
        $name=$user['Alias'];

        if($BBIN_username==null or $BBIN_username==""){
            $WebCode =ltrim(trim($sysConfig['AG_User']));
            if(!preg_match("/^[A-Za-z0-9]{4,12}$/", $user['UserName'])){
                $BBIN_username = $BBINUtils->getpassword_bbin(10);
            }else{
                $BBIN_username=trim($user['UserName']).$BBINUtils->getpassword_bbin(1);
            }
            $BBIN_username='h07'.$WebCode.$BBIN_username;
            $BBIN_username=strtolower($BBIN_username);
            $BBIN_password=strtolower($BBINUtils->getpassword_bbin(10));


            $result= $BBINUtils->Addmember_BBIN($BBIN_username,$BBIN_password,1);
            if($result['info']=='0'){
                $msql="update web_member_data set BBIN_User='".$BBIN_username."',BBIN_Pass='".$BBIN_password."' where UserName='".$username."'";
                $update = User::where('UserName', $username)->update(['BBIN_User' => $BBIN_username,
                'BBIN_Pass' => $BBIN_password]);
            }else{
                return response()->json(['success'=>false, 'message'=> '网络异常，请与在线客服联系！']);
            }
        }
        if($tp=="IN"){  //转入
            if($money>$user['Money']){  //检查金额
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于会员余额!']);
            }
        }

        if($tp=="OUT"){  //转出
            $money2 = $BBINUtils->getMoney_BBIN($BBIN_username, $BBIN_password); //获取真人余额
            if($money>$money2){
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于真人账号余额!']);
            }
        }


        $bbinLogData = [
            "Username" => $username,
            "Type" => $tp,
            "Gold" => $money,
            "Billno" => date("YmdHis"),
            "DateTime" => date("Y-m-d H:i:s",time()),
            "Result" => '0',
            "Checked" => '0',
        ];
        $bbinLog = new BBINLogs();
        $result = $bbinLog->create($bbinLogData);
        if ($result){
            $ouid2=$result->id;
        }else{

        }
        //转换操作
        $results= $BBINUtils->Deposit_BBIN($BBIN_username,$BBIN_password,$money,$tp);
        $billno=$results['billno'];
        $result=0;
        if($results['info']=='0') $result=1;

        //更新状态

        BBINLogs::where('id', $ouid2)->update(['Billno' => $billno,
        'Result' => $result, 'Checked' => $result,]);

        if($result==1){
            if($tp=='IN'){
                $assets= $user['Money'];//GetField($username,'Money');
                $user_id=$user['id'];//GetField($username,'id');
                $bbin_money = $user["BBIN_Money"];
                $money2 = $BBINUtils->getMoney_BBIN($BBIN_username, $BBIN_password); //获取真人余额
                Utils::ProcessUpdate($username);  //防止并发
                $result = DB::update("update web_member_data set Money=Money-$money, BBIN_Money=$money2 where Username='$username'");

                if($result){
                    $balance = $assets-$money;
                    $datetime = date("Y-m-d H:i:s",time()+12*3600);
                    $bank_account = "转入BBIN真人账号";
                    $bank_Address = "";
                    $Order_Code='TK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);

                    $data = [
                        "Gold" => $money,
                        "previousAmount" => $assets,
                        "currentAmount" => $balance,
                        "AddDate" => $adddate,
                        "Type" => 'T',
                        "Type2" => "3",
                        "UserName" => $username,
                        "Agents" => $agents,
                        "World" => $world,
                        "Corprator" => $corprator,
                        "Super" => $super,
                        "Admin" => $admin,
                        "CurType" => $curtype,
                        "Date" => $date,
                        "Phone" => $phone,
                        "Contact" => '',
                        "Name" => $name,
                        "User" => $username,
                        "Bank" => "",
                        "Bank_Address" => $bank_Address,
                        "Bank_Account" => $bank_account,
                        "Order_Code" => $Order_Code,
                        "Checked" => 1,
                        "Music" => 1,
                    ];
                    $sys800 = new Sys800;
                    $deposit = $sys800->create($data);
                    if ($deposit){
                        //return response()->json(['success'=>$assets, 'user'=>$balance]);
                    }else{
                        return response()->json(['success'=>false, 'message'=> '操作失败!!!']);
                    }
                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "转入BBIN真人平台",
                        "update_time" => $datetime,
                        "type" => "转入BBIN真人平台",
                        "order_value" => -$money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        $ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=> '操作失败!!!']);
                    }
                }
                MoneyLog::where('id', $ouid)->update(['about' => '转入BBIN真人平台<br>billno:'.$billno]);
            }
            if($tp=='OUT'){
                $bank_account="BBIN真人账号转出";
                $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                $previousAmount=Utils::GetField($username,'Money');
                $currentAmount=$previousAmount+$money;
                $data = [
                    "Checked" => 1,
                    "Gold" => $money,
                    "previousAmount" => $previousAmount,
                    "currentAmount" => $currentAmount,
                    "AddDate" => $adddate,
                    "Type" => 'S',
                    "Type2" => "3",
                    "UserName" => $username,
                    "Agents" => $agents,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => 'RMB',
                    "Date" => $date,
                    "Name" => $name,
                    "User" => $username,
                    "Bank_Account" => $bank_account,
                    "Music" => 1,
                    "Order_Code" => $Order_Code,
                ];
                $sys800 = new Sys800;
                $deposit = $sys800->create($data);
                if ($deposit){
                    //return response()->json(['success'=>$assets, 'user'=>$balance]);
                }else{
                    return response()->json(['success'=>false, 'message'=>"操作失败!!!"]);
                }

                $assets=Utils::GetField($username,'Money');
                $bbin_money = Utils::GetField($username,'BBIN_Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2 = $BBINUtils->getMoney_BBIN($BBIN_username, $BBIN_password); //获取真人余额
                $q1 = User::where('Username', $username)->update([
                    'Money' => $assets+$money,
                    "BBIN_Money" => $money2
                ]);
                if ($q1) {
                    $balance=Utils::GetField($username,'Money');
                    $datetime=date("Y-m-d H:i:s",time()+12*3600);

                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "BBIN真人账号转出<br>billno:$billno",
                        "update_time" => $datetime,
                        "type" => "BBIN真人账号转出",
                        "order_value" => $money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        //$ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=>'mon_log_insert error']);
                    }
                }
            }
            return response()->json(['success'=>true, 'message'=>'转账成功!']);
        }else{
            return response()->json(['success'=>false, 'message'=>'网络异常，请稍后再试!']);
        }
    }
    // AG handle
    function handleAG(Request $request, User $user, SysConfig $sysConfig ) {
        $AGUtils = new AGUtils($sysConfig);
        $ag_username = $user->AG_User;
        $ag_password = $user->AG_Pass;
        $username = $user->UserName;
        $alias = $user->Alias;
        $money = intval($request["money"]);
        $tp=str_replace("AG","",$request->type);
        $money2=0;
        if($tp=='IN') $money2=$money;  //转入加上转入额度

        $AG_Limit=intval($sysConfig['AG_Limit']);

        if($sysConfig['AG_Repair']==1 or $sysConfig['AG']==0 or $user['AG']==0){
            return response()->json(['success'=>false, 'message'=> '真人平台维护中，请稍候再试......']);
        }

        $date=date("Y-m-d");
        $row2  = DB::select("select sum(Gold) as IN_Money from ag_logs where Type='IN' and left(DateTime,10)='$date'");
        $IN_Money=intval($row2[0]->IN_Money);
        $row2  = DB::select("select sum(Gold) as OUT_Money from ag_logs where Type='OUT' and left(DateTime,10)='$date'");
        $OUT_Money=intval($row2[0]->OUT_Money);
        if(($IN_Money+$money2-$OUT_Money)>$AG_Limit){
            return response()->json(['success'=>false, 'message'=> '额度转换维护中，请联系客服人员']);
        }

        //限制额度 结束
        $adddate=date("Y-m-d");
        $date=date("Y-m-d H:i:s");
        $curtype=$user['CurType'];
        $agents=$user['Agents'];
        $world=$user['World'];
        $corprator =$user['Corprator'];
        $super=$user['Super'];
        $admin=$user['Admin'];
        $phone=$user['Phone'];
        $name=$user['Alias'];

        if($ag_username==null or $ag_username==""){
            $WebCode =ltrim(trim($sysConfig['AG_User']));
            if(!preg_match("/^[A-Za-z0-9]{4,12}$/", $user['UserName'])){
                $ag_username = $AGUtils->getpassword(10);
            }else{
                $ag_username=trim($user['UserName']).$AGUtils->getpassword(1);
            }
            $ag_username=strtoupper($ag_username);
            $ag_password=strtolower($AGUtils->getpassword(10));

            $result= $AGUtils->Addmember($ag_username,$ag_password,1);

            if($result['info']=='0'){
                $update = User::where('UserName', $username)->update(['AG_User' => $ag_username,
                'AG_Pass' => $ag_password]);
            }else{
                return response()->json(['success'=>false, 'message'=> '网络异常，请与在线客服联系！']);
            }
        }
        if($tp=="IN"){  //转入
            if($money>$user['Money']){  //检查金额
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于会员余额!']);
            }
        }

        if($tp=="OUT"){  //转出
            $money2= $AGUtils->getMoney($ag_username, $ag_password); //获取真人余额
            if($money>$money2){
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于真人账号余额!']);
            }
        }

        $agLogData = [
            "Username" => $username,
            "Type" => $tp,
            "Gold" => $money,
            "Billno" => date("YmdHis"),
            "DateTime" => date("Y-m-d H:i:s",time()),
            "Result" => '0',
            "Checked" => '0',
        ];

        $agLog = new AGLogs();

        $result = $agLog->create($agLogData);

        if ($result){
            $ouid2=$result->id;
        }

        //转换操作
        $results= $AGUtils->Deposit($ag_username,$ag_password,$money,$tp);
        $billno=$results['billno'];
        $result=0;
        if($results['info']=='0') $result=1;

        //更新状态

        AGLogs::where('id', $ouid2)->update(['Billno' => $billno,
        'Result' => $result, 'Checked' => $result]);

        if ($result==1) {
            if($tp=='IN'){
                $assets= Utils::GetField($username,'Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2= $AGUtils->getMoney($ag_username, $ag_password); //获取真人余额

                $result = DB::update("update web_member_data set Money=Money-$money, AG_Money=$money2 where Username='$username'");

                if($result){
                    $balance = Utils::GetField($username,'Money');
                    $datetime = date("Y-m-d H:i:s",time()+12*3600);
                    $bank_account = "转入AG真人账号";
                    $bank_Address = "";
                    $Order_Code='TK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);

                    $data = [
                        "Gold" => $money,
                        "previousAmount" => $assets,
                        "currentAmount" => $balance,
                        "AddDate" => $adddate,
                        "Type" => 'T',
                        "Type2" => "3",
                        "UserName" => $username,
                        "Agents" => $agents,
                        "World" => $world,
                        "Corprator" => $corprator,
                        "Super" => $super,
                        "Admin" => $admin,
                        "CurType" => $curtype,
                        "Date" => $date,
                        "Phone" => $phone,
                        "Contact" => '',
                        "Name" => $name,
                        "User" => $username,
                        "Bank" => "",
                        "Bank_Address" => $bank_Address,
                        "Bank_Account" => $bank_account,
                        "Order_Code" => $Order_Code,
                        "Checked" => 1,
                        "Music" => 1,
                    ];
                    $sys800 = new Sys800;
                    $deposit = $sys800->create($data);
                    if ($deposit){
                        //return response()->json(['success'=>$assets, 'user'=>$balance]);
                    }else{
                        return response()->json(['success'=>false, 'message'=> '操作失败!!!']);
                    }
                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "转入AG真人平台",
                        "update_time" => $datetime,
                        "type" => "转入AG真人平台",
                        "order_value" => -$money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        $ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=> 'moneyLogData insert bug']);
                    }
                }
                MoneyLog::where('id', $ouid)->update(['about' => '转入AG真人平台<br>billno:'.$billno]);
            }
            if($tp=='OUT'){
                $bank_account="AG真人账号转出";
                $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                $previousAmount=Utils::GetField($username,'Money');
                $currentAmount=$previousAmount+$money;

                $data = [
                    "Checked" => 1,
                    "Gold" => $money,
                    "previousAmount" => $previousAmount,
                    "currentAmount" => $currentAmount,
                    "AddDate" => $adddate,
                    "Type" => 'S',
                    "Type2" => "3",
                    "UserName" => $username,
                    "Agents" => $agents,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => 'RMB',
                    "Date" => $date,
                    "Name" => $name,
                    "User" => $username,
                    "Bank_Account" => $bank_account,
                    "Music" => 1,
                    "Order_Code" => $Order_Code,
                ];
                $sys800 = new Sys800;
                $deposit = $sys800->create($data);
                if ($deposit){
                    //return response()->json(['success'=>$assets, 'user'=>$balance]);
                }else{
                    return response()->json(['success'=>false, 'message'=>"操作失败!!!"]);
                }

                $assets = Utils::GetField($username,'Money');
                $ag_money = Utils::GetField($username,'AG_Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2= $AGUtils->getMoney($ag_username, $ag_password); //获取真人余额
                $q1 = User::where('Username', $username)->update([
                    'Money' => $assets+$money,
                    'AG_Money' => $money2
                ]);
                if($q1){
                    $balance=Utils::GetField($username,'Money');
                    $datetime=date("Y-m-d H:i:s",time()+12*3600);

                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "AG真人账号转出<br>billno:$billno",
                        "update_time" => $datetime,
                        "type" => "AG真人账号转出",
                        "order_value" => $money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        //$ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=>'mon_log_insert error']);
                    }
                }
            }
            return response()->json(['success'=>true, 'message'=>'转账成功!']);
        }else{
            return response()->json(['success'=>false, 'message'=>'网络异常，请稍后再试!']);
        }
    }
    // OG handle
    function handleOG(Request $request, User $user, SysConfig $sysConfig ) {
        $OGUtils = new OGUtils($sysConfig);
        $og_username = $user->OG_User;
        $OG_Limit1=$user['OG_Limit1']; //OG密码
        $OG_Limit2=$user['OG_Limit2']; //OG密码
        $username = $user->UserName;
        $alias = $user->Alias;
        $money = intval($request["money"]);
        $tp=str_replace("OG","",$request->type);
        if(!is_numeric($money) || intval($money)<=0 || ($tp<>'IN' and $tp<>'OUT')){

        }
        //限制额度 开始
        $money2=0;
        if($tp=='IN') $money2=$money;  //转入加上转入额度

        $OG_Limit=intval($sysConfig['OG_Limit']);

        $date=date("Y-m-d");

        $row2  = DB::select("select sum(Gold) as IN_Money from og_logs where Type='IN' and left(DateTime,10)='$date'");

        $IN_Money=intval($row2[0]->IN_Money);

        $row2  = DB::select("select sum(Gold) as OUT_Money from og_logs where Type='OUT' and left(DateTime,10)='$date'");

        $OUT_Money=intval($row2[0]->OUT_Money);

        if(($IN_Money + $money2 - $OUT_Money) > $OG_Limit){
            return response()->json(['success'=>false, 'message'=> '额度转换维护中，请联系客服人员']);
        }

        //限制额度 结束
        $adddate=date("Y-m-d");
        $date=date("Y-m-d H:i:s");
        $curtype=$user['CurType'];
        $agents=$user['Agents'];
        $world=$user['World'];
        $corprator =$user['Corprator'];
        $super=$user['Super'];
        $admin=$user['Admin'];
        $phone=$user['Phone'];
        $name=$user['Alias'];

        if ($og_username==null || $og_username=="") {
            $og_username=$username.'_'.$OGUtils->getpassword_OG(3);
            $og_username=strtoupper($og_username);
            $result=$OGUtils->Add_OG_Member($og_username);
            sleep(1);
            $OGUtils->OG_Limit($og_username,$OG_Limit1,$OG_Limit2);
            if($result==1) {
                User::where("UserName", $username)->update(["OG_User" => $og_username]);
            } else {
                $response["message"] = '网络异常，请与在线客服联系！';
                return response()->json($response, $response['status']);
            }
        }

        if($tp=="IN"){  //转入
            if($money>$user['Money']){  //检查金额
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于会员余额!']);
            }
        }

        if($tp=="OUT"){  //转出
            $money2= $OGUtils->OG_Money($og_username); //获取真人余额
            if($money>$money2){
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于真人账号余额!']);
            }
        }

        $ogLogData = [
            "Username" => $username,
            "Type" => $tp,
            "Gold" => $money,
            "Billno" => date("YmdHis"),
            "DateTime" => date("Y-m-d H:i:s",time()),
            "Result" => '0',
            "Checked" => '0',
        ];

        $ogLog = new OGLogs();

        $result = $ogLog->create($ogLogData);
        if ($result){
            $ouid2=$result->id;
        }else{

        }
        //转换操作
        $results= $OGUtils->OG_Deposit($og_username,'',$money,$tp);
        $billno=$results['billno'];
        $result=intval($results['result']);

        //更新状态

        OGLogs::where('id', $ouid2)->update(['Billno' => $billno,
        'Result' => $result, 'Checked' => $result]);

        if($result==1){
            if($tp=='IN'){
                $assets= Utils::GetField($username,'Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2= $OGUtils->OG_Money($og_username); //获取真人余额
                $result = DB::update("update web_member_data set Money=Money-$money, OG_Money=$money2 where Username='$username'");

                if($result){
                    $balance = Utils::GetField($username,'Money');
                    $datetime = date("Y-m-d H:i:s",time()+12*3600);
                    $bank_account = "转入OG真人账号";
                    $bank_Address = "";
                    $Order_Code='TK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);

                    $data = [
                        "Gold" => $money,
                        "previousAmount" => $assets,
                        "currentAmount" => $balance,
                        "AddDate" => $adddate,
                        "Type" => 'T',
                        "Type2" => "3",
                        "UserName" => $username,
                        "Agents" => $agents,
                        "World" => $world,
                        "Corprator" => $corprator,
                        "Super" => $super,
                        "Admin" => $admin,
                        "CurType" => $curtype,
                        "Date" => $date,
                        "Phone" => $phone,
                        "Contact" => '',
                        "Name" => $name,
                        "User" => $username,
                        "Bank" => "",
                        "Bank_Address" => $bank_Address,
                        "Bank_Account" => $bank_account,
                        "Order_Code" => $Order_Code,
                        "Checked" => 1,
                        "Music" => 1,
                    ];
                    $sys800 = new Sys800;
                    $deposit = $sys800->create($data);
                    if ($deposit){
                        //return response()->json(['success'=>$assets, 'user'=>$balance]);
                    }else{
                        return response()->json(['success'=>false, 'message'=> '操作失败!!!']);
                    }
                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "转入AG真人平台",
                        "update_time" => $datetime,
                        "type" => "转入AG真人平台",
                        "order_value" => -$money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        $ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=> 'moneyLogData insert bug']);
                    }
                }
                MoneyLog::where('id', $ouid)->update(['about' => '转入OG真人平台<br>billno:'.$billno]);
            }
            if($tp=='OUT'){
                $bank_account="OG真人账号转出";
                $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                $previousAmount=Utils::GetField($username,'Money');
                $currentAmount=$previousAmount+$money;

                $data = [
                    "Checked" => 1,
                    "Gold" => $money,
                    "previousAmount" => $previousAmount,
                    "currentAmount" => $currentAmount,
                    "AddDate" => $adddate,
                    "Type" => 'S',
                    "Type2" => "3",
                    "UserName" => $username,
                    "Agents" => $agents,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => 'RMB',
                    "Date" => $date,
                    "Name" => $name,
                    "User" => $username,
                    "Bank_Account" => $bank_account,
                    "Music" => 1,
                    "Order_Code" => $Order_Code,
                ];
                $sys800 = new Sys800;
                $deposit = $sys800->create($data);
                if ($deposit){
                    //return response()->json(['success'=>$assets, 'user'=>$balance]);
                }else{
                    return response()->json(['success'=>false, 'message'=>"操作失败!!!"]);
                }

                $assets=Utils::GetField($username,'Money');
                $og_money = Utils::GetField($username,'OG_Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2= $OGUtils->OG_Money($og_username); //获取真人余额
                $q1 = User::where('Username', $username)->update([
                    'Money' => $assets + $money,
                    'OG_Money' => $money2,
                ]);

                if ($q1 == 1) {

                    $balance=Utils::GetField($username,'Money');
                    $datetime=date("Y-m-d H:i:s",time()+12*3600);

                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "OG真人账号转出<br>billno:$billno",
                        "update_time" => $datetime,
                        "type" => "OG真人账号转出",
                        "order_value" => $money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        //$ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=>'mon_log_insert error']);
                    }
                }
            }
            return response()->json(['success'=>true, 'message'=>'转账成功!']);
        }else{
            return response()->json(['success'=>false, 'message'=>'网络异常，请稍后再试!']);
        }
    }
    // MG handle
    function handleMG(Request $request, User $user, SysConfig $sysConfig ) {
        $MGUtils = new MGUtils($sysConfig);
        $MG_username = $user->MG_User;
        $MG_password = $user->MG_Pass;
        $username = $user->UserName;
        $alias = $user->Alias;
        $money = intval($request["money"]);
        $tp=str_replace("MG","",$request->type);
        if(!is_numeric($money) || intval($money)<=0 || ($tp<>'IN' and $tp<>'OUT')){
            die(".");
        }
        //限制额度 开始
        $money2=0;
        if($tp=='IN') $money2=$money;  //转入加上转入额度

        $MG_Limit=intval($sysConfig['MG_Limit']);
        if($sysConfig['MG_Repair']==1 or $sysConfig['MG']==0 or $user['MG']==0){
            return response()->json(['success'=>false, 'message'=> '真人平台维护中，请稍候再试......']);
        }

        $date=date("Y-m-d");
        $row2  = DB::select("select sum(Gold) as IN_Money from mg_logs where Type='IN' and left(DateTime,10)='$date'");
        $IN_Money=intval($row2[0]->IN_Money);

        $row2  = DB::select("select sum(Gold) as OUT_Money from mg_logs where Type='OUT' and left(DateTime,10)='$date'");
        $OUT_Money=intval($row2[0]->OUT_Money);
        if(($IN_Money+$money2-$OUT_Money)>$MG_Limit){
            return response()->json(['success'=>false, 'message'=> '额度转换维护中，请联系客服人员']);
        }

        //限制额度 结束
        $adddate=date("Y-m-d");
        $date=date("Y-m-d H:i:s");
        $curtype=$user['CurType'];
        $agents=$user['Agents'];
        $world=$user['World'];
        $corprator =$user['Corprator'];
        $super=$user['Super'];
        $admin=$user['Admin'];
        $phone=$user['Phone'];
        $name=$user['Alias'];

        if($MG_username==null or $MG_username==""){
            $WebCode =ltrim(trim($sysConfig['AG_User']));
            if(!preg_match("/^[A-Za-z0-9]{4,12}$/", $user['UserName'])){
                $MG_username = $MGUtils->getpassword_MG(10);
            }else{
                $MG_username=trim($user['UserName']).$MGUtils->getpassword_MG(1);
            }
            $MG_username='h07'.$WebCode.$MG_username;
            $MG_username=strtolower($MG_username);
            $MG_password=strtolower($MGUtils->getpassword_MG(10));
            $result= $MGUtils->Addmember_MG($MG_username,$MG_password,1);
            if($result['info']=='0'){
                $update = User::where('UserName', $username)->update(['MG_User' => $MG_username,
                'MG_Pass' => $MG_password]);
            }else{
                return response()->json(['success'=>false, 'message'=> '网络异常，请与在线客服联系！']);
            }
        }
        if($tp=="IN"){  //转入
            if($money>$user['Money']){  //检查金额
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于会员余额!']);
            }
        }

        if($tp=="OUT"){  //转出
            $money2= $MGUtils->getMoney_MG($MG_username, $MG_password); //获取真人余额
            if($money>$money2){
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于真人账号余额!']);
            }
        }

        $mgLogData = [
            "Username" => $username,
            "Type" => $tp,
            "Gold" => $money,
            "Billno" => date("YmdHis"),
            "DateTime" => date("Y-m-d H:i:s",time()),
            "Result" => '0',
            "Checked" => '0',
        ];
        $mgLog = new MGLogs();
        $result = $mgLog->create($mgLogData);
        if ($result){
            $ouid2=$result->id;
        }

        //转换操作
        $results= $MGUtils->Deposit_MG($MG_username,$MG_password,$money,$tp);
        $billno=$results['billno'];
        $result = 0;
        if($results['info']=='0') $result=1;

        MGLogs::where('id', $ouid2)->update(['Billno' => $billno,
        'Result' => $result, 'Checked' => $result]);

        if($result==1){
            if($tp=='IN'){
                $assets= Utils::GetField($username,'Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2= $MGUtils->getMoney_MG($MG_username, $MG_password); //获取真人余额
                $result = DB::update("update web_member_data set Money=Money-$money, MG_Money=$money2 where Username='$username'");

                if($result){
                    $balance = Utils::GetField($username,'Money');
                    $datetime = date("Y-m-d H:i:s",time()+12*3600);
                    $bank_account = "转入MG真人账号";
                    $bank_Address = "";
                    $Order_Code='TK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);

                    $data = [
                        "Gold" => $money,
                        "previousAmount" => $assets,
                        "currentAmount" => $balance,
                        "AddDate" => $adddate,
                        "Type" => 'T',
                        "Type2" => "3",
                        "UserName" => $username,
                        "Agents" => $agents,
                        "World" => $world,
                        "Corprator" => $corprator,
                        "Super" => $super,
                        "Admin" => $admin,
                        "CurType" => $curtype,
                        "Date" => $date,
                        "Phone" => $phone,
                        "Contact" => '',
                        "Name" => $name,
                        "User" => $username,
                        "Bank" => "",
                        "Bank_Address" => $bank_Address,
                        "Bank_Account" => $bank_account,
                        "Order_Code" => $Order_Code,
                        "Checked" => 1,
                        "Music" => 1,
                    ];
                    $sys800 = new Sys800;
                    $deposit = $sys800->create($data);
                    if ($deposit){
                        //return response()->json(['success'=>$assets, 'user'=>$balance]);
                    }else{
                        return response()->json(['success'=>false, 'message'=> '操作失败!!!']);
                    }
                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "转入MG真人平台",
                        "update_time" => $datetime,
                        "type" => "转入MG真人平台",
                        "order_value" => -$money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        $ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=> 'moneyLogData insert bug']);
                    }
                }
                MoneyLog::where('id', $ouid)->update(['about' => '转入MG真人平台<br>billno:'.$billno]);
            }
            if($tp=='OUT'){
                $bank_account="MG真人账号转出";
                $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                $previousAmount=Utils::GetField($username,'Money');
                $currentAmount=$previousAmount+$money;

                $data = [
                    "Checked" => 1,
                    "Gold" => $money,
                    "previousAmount" => $previousAmount,
                    "currentAmount" => $currentAmount,
                    "AddDate" => $adddate,
                    "Type" => 'S',
                    "Type2" => "3",
                    "UserName" => $username,
                    "Agents" => $agents,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => 'RMB',
                    "Date" => $date,
                    "Name" => $name,
                    "User" => $username,
                    "Bank_Account" => $bank_account,
                    "Music" => 1,
                    "Order_Code" => $Order_Code,
                ];
                $sys800 = new Sys800;
                $deposit = $sys800->create($data);
                if ($deposit){
                    //return response()->json(['success'=>$assets, 'user'=>$balance]);
                }else{
                    return response()->json(['success'=>false, 'message'=>"操作失败!!!"]);
                }

                $assets = Utils::GetField($username,'Money');
                $mg_money = Utils::GetField($username,'MG_Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2= $MGUtils->getMoney_MG($MG_username, $MG_password); //获取真人余额
                $q1 = User::where('Username', $username)->update([
                    'Money' => $assets+$money,
                    "MG_Money" => $money2
                ]);
                if($q1){
                    $balance=Utils::GetField($username,'Money');
                    $datetime=date("Y-m-d H:i:s",time()+12*3600);

                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "MG真人账号转出<br>billno:$billno",
                        "update_time" => $datetime,
                        "type" => "MG真人账号转出",
                        "order_value" => $money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        //$ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=>'mon_log_insert error']);
                    }
                }
            }
            return response()->json(['success'=>true, 'message'=>'转账成功!']);
        }else{
            return response()->json(['success'=>false, 'message'=>'网络异常，请稍后再试!']);
        }
    }
    // PT handle
    function handlePT(Request $request, User $user, SysConfig $sysConfig ) {
        $PTUtils = new PTUtils($sysConfig);
        $PT_username = $user->PT_User;
        $PT_password = $user->PT_Pass;
        $username = $user->UserName;
        $alias = $user->Alias;
        $money = intval($request["money"]);
        $tp=str_replace("PT","",$request->type);
        if(!is_numeric($money) || intval($money)<=0 || ($tp<>'IN' and $tp<>'OUT')){
            die(".");
        }
        //限制额度 开始
        $money2=0;
        if($tp=='IN') $money2=$money;  //转入加上转入额度

        $PT_Limit=intval($sysConfig['PT_Limit']);
        if($sysConfig['PT_Repair']==1 or $sysConfig['PT']==0 or $user['PT']==0){
            return response()->json(['success'=>false, 'message'=> '真人平台维护中，请稍候再试......']);
        }

        $date=date("Y-m-d");
        $row2  = DB::select("select sum(Gold) as IN_Money from pt_logs where Type='IN' and left(DateTime,10)='$date'");
        $IN_Money=intval($row2[0]->IN_Money);

        $row2  = DB::select("select sum(Gold) as OUT_Money from pt_logs where Type='OUT' and left(DateTime,10)='$date'");
        $OUT_Money=intval($row2[0]->OUT_Money);
        if(($IN_Money+$money2-$OUT_Money)>$PT_Limit){
            return response()->json(['success'=>false, 'message'=> '额度转换维护中，请联系客服人员']);
        }

        //限制额度 结束
        $adddate=date("Y-m-d");
        $date=date("Y-m-d H:i:s");
        $curtype=$user['CurType'];
        $agents=$user['Agents'];
        $world=$user['World'];
        $corprator =$user['Corprator'];
        $super=$user['Super'];
        $admin=$user['Admin'];
        $phone=$user['Phone'];
        $name=$user['Alias'];

        if($PT_username==null or $PT_username==""){
            $WebCode =ltrim(trim($sysConfig['AG_User']));
            if(!preg_match("/^[A-Za-z0-9]{4,12}$/", $user['UserName'])){
                $PT_username = $PTUtils->getpassword_PT(10);
            }else{
                $PT_username=trim($user['UserName']).$PTUtils->getpassword_PT(1);
            }
            $PT_username='h07'.$WebCode.$PT_username;
            $PT_username=strtolower($PT_username);
            $PT_password=strtolower($PTUtils->getpassword_PT(10));
            $result= $PTUtils->Addmember_PT($PT_username,$PT_password,1);
            if($result['info']=='0') {
                $update = User::where('UserName', $username)->update([
                    'PT_User' => $PT_username,
                    'PT_Pass' => $PT_password
                ]);
            }else{
                return response()->json(['success'=>false, 'message'=> '网络异常，请与在线客服联系！']);
            }
        }
        if($tp=="IN"){  //转入
            if($money>$user['Money']){  //检查金额
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于会员余额!']);
            }
        }

        if($tp=="OUT"){  //转出
            $money2 = $PTUtils->getMoney_PT($PT_username, $PT_password); //获取真人余额
            if($money>$money2){
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于真人账号余额!']);
            }
        }

        $ptLogData = [
            "Username" => $username,
            "Type" => $tp,
            "Gold" => $money,
            "Billno" => date("YmdHis"),
            "DateTime" => date("Y-m-d H:i:s",time()),
            "Result" => '0',
            "Checked" => '0',
        ];

        $ptLog = new PTLogs();
        $result = $ptLog->create($ptLogData);
        if ($result){
            $ouid2=$result->id;
        }

        //转换操作
        $results= $PTUtils->Deposit_PT($PT_username,$PT_password,$money,$tp);
        $billno=$results['billno'];
        $result=0;
        if($results['info']=='0') $result=1;

        //更新状态
        MGLogs::where('id', $ouid2)->update([
            'Billno' => $billno,
            'Result' => $result,
            'Checked' => $result
        ]);

        if($result==1){
            if($tp=='IN'){
                $assets= Utils::GetField($username,'Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2 = $PTUtils->getMoney_PT($PT_username, $PT_password); //获取真人余额
                $result = DB::update("update web_member_data set Money=Money-$money, PT_Money=$money2 where Username='$username'");

                if($result){
                    $balance = Utils::GetField($username,'Money');
                    $datetime = date("Y-m-d H:i:s",time()+12*3600);
                    $bank_account = "转入PT真人账号";
                    $bank_Address = "";
                    $Order_Code='TK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);

                    $data = [
                        "Gold" => $money,
                        "previousAmount" => $assets,
                        "currentAmount" => $balance,
                        "AddDate" => $adddate,
                        "Type" => 'T',
                        "Type2" => "3",
                        "UserName" => $username,
                        "Agents" => $agents,
                        "World" => $world,
                        "Corprator" => $corprator,
                        "Super" => $super,
                        "Admin" => $admin,
                        "CurType" => $curtype,
                        "Date" => $date,
                        "Phone" => $phone,
                        "Contact" => '',
                        "Name" => $name,
                        "User" => $username,
                        "Bank" => "",
                        "Bank_Address" => $bank_Address,
                        "Bank_Account" => $bank_account,
                        "Order_Code" => $Order_Code,
                        "Checked" => 1,
                        "Music" => 1,
                    ];
                    $sys800 = new Sys800;
                    $deposit = $sys800->create($data);
                    if ($deposit){
                        //return response()->json(['success'=>$assets, 'user'=>$balance]);
                    }else{
                        return response()->json(['success'=>false, 'message'=> '操作失败!!!']);
                    }
                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "转入PT真人平台",
                        "update_time" => $datetime,
                        "type" => "转入PT真人平台",
                        "order_value" => -$money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        $ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=> 'moneyLogData insert bug']);
                    }
                }
                MoneyLog::where('id', $ouid)->update([
                    'about' => '转入PT真人平台<br>billno:'.$billno
                ]);
            }
            if($tp=='OUT'){
                $bank_account="PT真人账号转出";
                $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                $previousAmount=Utils::GetField($username,'Money');
                $currentAmount=$previousAmount+$money;

                $data = [
                    "Checked" => 1,
                    "Gold" => $money,
                    "previousAmount" => $previousAmount,
                    "currentAmount" => $currentAmount,
                    "AddDate" => $adddate,
                    "Type" => 'S',
                    "Type2" => "3",
                    "UserName" => $username,
                    "Agents" => $agents,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => 'RMB',
                    "Date" => $date,
                    "Name" => $name,
                    "User" => $username,
                    "Bank_Account" => $bank_account,
                    "Music" => 1,
                    "Order_Code" => $Order_Code,
                ];
                $sys800 = new Sys800;
                $deposit = $sys800->create($data);
                if ($deposit){
                    //return response()->json(['success'=>$assets, 'user'=>$balance]);
                }else{
                    return response()->json(['success'=>false, 'message'=>"操作失败!!!"]);
                }

                $assets=Utils::GetField($username,'Money');
                $pt_money=Utils::GetField($username,'PT_Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2 = $PTUtils->getMoney_PT($PT_username, $PT_password); //获取真人余额
                $q1 = User::where('Username', $username)->update([
                    'Money' => $assets + $money,
                    'PT_Money' => $money2
                ]);
                if($q1){
                    $balance=Utils::GetField($username,'Money');
                    $datetime=date("Y-m-d H:i:s",time()+12*3600);

                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "PT真人账号转出<br>billno:$billno",
                        "update_time" => $datetime,
                        "type" => "PT真人账号转出",
                        "order_value" => $money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        //$ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=>'mon_log_insert error']);
                    }
                }
            }
            return response()->json(['success'=>true, 'message'=>'转账成功!']);
        }else{
            return response()->json(['success'=>false, 'message'=>'网络异常，请稍后再试!']);
        }
    }
    // KY handle
    function handleKY(Request $request, User $user, SysConfig $sysConfig ) {
        $KYUtils = new KYUtils($sysConfig);
        $ky_username = $user->KY_User;
        $username = $user->UserName;
        $alias = $user->Alias;
        $money = intval($request["money"]);
        $tp=str_replace("KY","",$request->type);
        if(!is_numeric($money) || intval($money)<=0 || ($tp<>'IN' and $tp<>'OUT')){
            die(".");
        }

        $KY_Limit=intval($sysConfig['KY_Limit']);
        if($sysConfig['KY_Repair']==1 or $sysConfig['KY']==0 or $user['KY']==0){
            return response()->json(['success'=>false, 'message'=> '真人平台维护中，请稍候再试......']);
        }

        //限制额度 结束
        $adddate=date("Y-m-d");
        $date=date("Y-m-d H:i:s");
        $curtype=$user['CurType'];
        $agents=$user['Agents'];
        $world=$user['World'];
        $corprator =$user['Corprator'];
        $super=$user['Super'];
        $admin=$user['Admin'];
        $phone=$user['Phone'];
        $name=$user['Alias'];

        if($ky_username==null or $ky_username=="") {
            $WebCode =ltrim(trim($sysConfig['AG_User']));
            if(!preg_match("/^[A-Za-z0-9]{4,12}$/", $username)){
                $ky_username=$WebCode.'_'.$KYUtils->getpassword_KY(10);
            }else{
                $ky_username=$WebCode.'_'.trim($user['UserName'].$KYUtils->getpassword_KY(1));
            }
            $ky_username=strtoupper($ky_username);
            error_log($ky_username);
            $result=$KYUtils->Add_KY_member($ky_username);
            if(intval($result)==1){
                $result = User::where('UserName', $username)->update(['KY_User'=>$ky_username]);
            }else{
                return response()->json(['success'=>false, 'message'=> '网络异常，请与在线客服联系！！']);
            }
        }
        if($tp=="IN"){  //转入
            if($money>$user['Money']){  //检查金额
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于会员余额!']);
            }
        }

        if ($tp=="OUT") {  //转出
            $money2= $KYUtils->KY_Money2($ky_username); //获取真人余额
            if($money>$money2){
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于真人账号余额!']);
            }
        }

        $kyLogData = [
            "Username" => $username,
            "Type" => $tp,
            "Gold" => $money,
            "Billno" => date("YmdHis"),
            "DateTime" => date("Y-m-d H:i:s",time()),
            "Result" => '0',
            "Checked" => '0',
        ];

        $kyLog = new KYLogs();

        $result = $kyLog->create($kyLogData);

        if ($result){
            $ouid2=$result->id;
        }

        //转换操作
        $results= $KYUtils->KY_Deposit($ky_username,$money,$tp);
        $billno=$results['billno'];
        $result=intval($results['result']);

        //更新状态

        KYLogs::where('id', $ouid2)->update(['Billno' => $billno,
        'Result' => $result, 'Checked' => $result]);

        if($result==1){
            if($tp=='IN'){
                $assets= Utils::GetField($username,'Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2= $KYUtils->KY_Money2($ky_username); //获取真人余额
                $result = DB::update("update web_member_data set Money=Money-$money, KY_Money=$money2 where Username='$username'");

                if($result){
                    $balance = Utils::GetField($username,'Money');
                    $datetime = date("Y-m-d H:i:s",time()+12*3600);
                    $bank_account = "转入开元棋牌账号";
                    $bank_Address = "";
                    $Order_Code='TK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);

                    $data = [
                        "Gold" => $money,
                        "previousAmount" => $assets,
                        "currentAmount" => $balance,
                        "AddDate" => $adddate,
                        "Type" => 'T',
                        "Type2" => "3",
                        "UserName" => $username,
                        "Agents" => $agents,
                        "World" => $world,
                        "Corprator" => $corprator,
                        "Super" => $super,
                        "Admin" => $admin,
                        "CurType" => $curtype,
                        "Date" => $date,
                        "Phone" => $phone,
                        "Contact" => '',
                        "Name" => $name,
                        "User" => $username,
                        "Bank" => "",
                        "Bank_Address" => $bank_Address,
                        "Bank_Account" => $bank_account,
                        "Order_Code" => $Order_Code,
                        "Checked" => 1,
                        "Music" => 1,
                    ];
                    $sys800 = new Sys800;
                    $deposit = $sys800->create($data);
                    if ($deposit){
                        //return response()->json(['success'=>$assets, 'user'=>$balance]);
                    }else{
                        return response()->json(['success'=>false, 'message'=> '操作失败!!!']);
                    }
                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "转入开元棋牌",
                        "update_time" => $datetime,
                        "type" => "转入开元棋牌",
                        "order_value" => -$money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        $ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=> 'moneyLogData insert bug']);
                    }
                }
                MoneyLog::where('id', $ouid)->update(['about' => '转入开元棋牌<br>billno:'.$billno]);
            }
            if($tp=='OUT'){
                $bank_account="开元棋牌账号转出";
                $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                $previousAmount=Utils::GetField($username,'Money');
                $currentAmount=$previousAmount+$money;

                $data = [
                    "Checked" => 1,
                    "Gold" => $money,
                    "previousAmount" => $previousAmount,
                    "currentAmount" => $currentAmount,
                    "AddDate" => $adddate,
                    "Type" => 'S',
                    "Type2" => "3",
                    "UserName" => $username,
                    "Agents" => $agents,
                    "World" => $world,
                    "Corprator" => $corprator,
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => 'RMB',
                    "Date" => $date,
                    "Name" => $name,
                    "User" => $username,
                    "Bank_Account" => $bank_account,
                    "Music" => 1,
                    "Order_Code" => $Order_Code,
                ];
                $sys800 = new Sys800;
                $deposit = $sys800->create($data);
                if ($deposit){
                    //return response()->json(['success'=>$assets, 'user'=>$balance]);
                }else{
                    return response()->json(['success'=>false, 'message'=>"操作失败!!!"]);
                }

                $assets=Utils::GetField($username,'Money');
                $ky_money=Utils::GetField($username,'KY_Money');
                $user_id=Utils::GetField($username,'id');
                Utils::ProcessUpdate($username);  //防止并发
                $money2= $KYUtils->KY_Money2($ky_username); //获取真人余额
                $q1 = User::where('Username', $username)->update([
                    'Money' => $assets+$money,
                    'KY_Money' => $money2
                ]);
                if($q1 == 1){

                    $balance=Utils::GetField($username,'Money');
                    $datetime=date("Y-m-d H:i:s",time()+12*3600);

                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "开元棋牌账号转出<br>billno:$billno",
                        "update_time" => $datetime,
                        "type" => "开元棋牌账号转出",
                        "order_value" => $money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        //$ouid=$result->id;
                    }else{
                        return response()->json(['success'=>false, 'message'=>'mon_log_insert error']);
                    }
                }
            }
            return response()->json(['success'=>true, 'message'=>'转账成功!']);
        }else{
            return response()->json(['success'=>false, 'message'=>'网络异常，请稍后再试!']);
        }
    }
    /* Get bank info. */
    public function getSysConfig(Request $request) {
        $sysConfig = SysConfig::all()->first();
        return response()->json(['success'=>true, 'sysConfig' => $sysConfig]);
    }
    /* Transfer function. */
    public function transferMoney(Request $request) {
        $user = User::where('id',$request->userId)->first();
        $sysConfig = SysConfig::all()->first();
        $trtype = $request->type;
        if($trtype=='AGIN' or $trtype=='AGOUT'){
            if($sysConfig['AG_Repair']==1 or $sysConfig['AG']==0 or $user['AG_TR']==0){
                return response()->json(['success'=>false, 'message'=> '真人平台维护中，请稍候再试......']);
            }
            return $this->handleAG($request, $user, $sysConfig);
        }

        if($trtype=='OGIN' or $trtype=='OGOUT'){
            if($sysConfig['OG_Repair']==1 or $sysConfig['OG']==0 or $user['OG_TR']==0){
                return response()->json(['success'=>false, 'message'=> '真人平台维护中，请稍候再试......']);
            }
            return $this->handleOG($request, $user, $sysConfig);
        }

        if($trtype=='BBIN' or $trtype=='BBOUT'){
            if($sysConfig['BBIN_Repair']==1 or $sysConfig['BBIN']==0 or $user['BBIN_TR']==0){
                return response()->json(['success'=>false, 'message'=> '真人平台维护中，请稍候再试......']);
            }
            return $this->handleBBIN($request, $user, $sysConfig);
        }

        if($trtype=='MGIN' or $trtype=='MGOUT'){
            if($sysConfig['MG_Repair']==1 or $sysConfig['MG']==0 or $user['MG_TR']==0){
                return response()->json(['success'=>false, 'message'=> '真人平台维护中，请稍候再试......']);
            }
            return $this->handleMG($request, $user, $sysConfig);
        }

        if($trtype=='PTIN' or $trtype=='PTOUT'){
            if($sysConfig['PT_Repair']==1 or $sysConfig['PT']==0 or $user['PT_TR']==0){
                return response()->json(['success'=>false, 'message'=> '真人平台维护中，请稍候再试......']);
            }
            return $this->handlePT($request, $user, $sysConfig);
        }

        if($trtype=='KYIN' or $trtype=='KYOUT'){
            if($sysConfig['KY_Repair']==1 or $sysConfig['KY']==0 or $user['KY_TR']==0){
                return response()->json(['success'=>false, 'message'=> '开元棋牌维护中，请稍候再试......']);
            }
            return $this->handleKY($request, $user, $sysConfig);
        }
    }
}

