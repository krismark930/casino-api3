<?php
namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\DatabaseManager\DD;
use App\Models\Web\Bank;
use Auth;
use Validator;
use App\Models\Web\Sys800;
use App\Models\Web\SysConfig;
use App\Models\User;
use App\Models\Web\BBINLogs;
use App\Models\Web\MoneyLog;
use Illuminate\Support\Facades\DB;
use App\Utils\BBIN\BBINUtils;
use App\Utils\Utils;
class TransferController extends Controller {

    public function __construct(){
        //$this->middleware("auth:api");
    }
    // BBIN handle
    function handleBBIN(Request $request, User $user, SysConfig $sysConfig ) {
        $BBIN_username = $user->BBIN_User;
        $BBIN_password = $user->BBIN_Pass;
        $username = $user->UserName;
        $alias = $user->Alias;
        $money = intval($request["money"]);
        $tp=str_replace("BB","",$request->type);
        //限制额度 开始
        $money2=0;
        if($tp=='IN') $money2=$money;  //转入加上转入额度

        $BBIN_Limit=intval($sysConfig['BBIN_Limit']);
        if($sysConfig['BBIN_Repair']==1 or $sysConfig['BBIN']==0 or $user['BBIN']==0){
            echo "<script>alert('真人平台维护中，请稍候再试......');history.go(-1);</script>";
        }

        $date=date("Y-m-d");
        //"select sum(Gold) from BBIN_logs where Type='IN' and left(DateTime,10)='$date'";
        $row2  = DB::select("select sum(Gold) as IN_Money from BBIN_logs where Type='IN' ");
        $IN_Money=intval($row2[0]->IN_Money);

        //"select sum(Gold) as OUT_Money from BBIN_logs where Type='OUT' and left(DateTime,10)='$date'";
        $row2  = DB::select("select sum(Gold) as OUT_Money from BBIN_logs where Type='OUT'");
        $OUT_Money=intval($row2[0]->OUT_Money);
        if(($IN_Money+$money2-$OUT_Money)>$BBIN_Limit){
            echo "<h1>额度转换维护中，请联系客服人员</h1><script>alert('额度转换维护中，请联系客服人员');window.open('/kf.html');</script>";
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
                $BBIN_username = BBINUtils::getpassword_bbin(10);
            }else{
                $BBIN_username=trim($user['UserName']).BBINUtils::getpassword_bbin(1);
            }
            $BBIN_username='h07'.$WebCode.$BBIN_username;
            $BBIN_username=strtolower($BBIN_username);
            $BBIN_password=strtolower(BBINUtils::getpassword_bbin(10));


            $result= BBINUtils::Addmember_BBIN($BBIN_username,$BBIN_password,1);
            if($result['info']=='0'){
                $msql="update web_member_data set BBIN_User='".$BBIN_username."',BBIN_Pass='".$BBIN_password."' where UserName='".$username."'";
                $update = User::where('UserName', $username)->update(['BBIN_User' => $BBIN_username,
                'BBIN_Pass' => $BBIN_password, 'BBIN_Pass' => $BBIN_password,]);
                if($update){

                }else{

                };// or die($msql);
                echo("<script>alert('恭喜您，真人账号激活成功！');</script>");
            }else{
                //echo("<script>alert('网络异常，请与在线客服联系！');window.location='/app/member/ed.php?uid=".$uid."'</script>");
            }
        }
        if($tp=="IN"){  //转入
            if($money>$user['Money']){  //检查金额
                echo "<script language='javascript'>alert('转账金额不能大于会员余额!');history.go(-1);</script>";
                exit;
            }else{  //转入前扣款
                $assets= $user['Money'];//GetField($username,'Money');
                $user_id=$user['ID'];//GetField($username,'ID');
                Utils::ProcessUpdate($username);  //防止并发
                $result = DB::update("update web_member_data set Money=Money-$money where Username='$username'");

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
                        "Order_Code" => $Order_Code,
                    ];
                    $sys800 = new Sys800;
                    $deposit = $sys800->create($data);
                    if ($deposit){
                        return response()->json(['success'=>$assets, 'user'=>$balance]);
                    }else{
                        //die ("操作失败!!!");
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
                    if ($moneyLog->create($moneyLogData)){
                        $ouid=$moneyLog->id;
                    }else{
                        //die($money_log_sql);
                    }
                }
            }
        }

        if($tp=="OUT"){  //转出
            $money2= BBINUtils::getMoney_BBIN($BBIN_username, $BBIN_password); //获取真人余额
            if($money>$money2){
                echo "<script language='javascript'>alert('转账金额不能大于真人账号余额!');history.go(-1);</script>";
                exit;
            }
        }

        return response()->json(['success'=>true, 'user'=> $IN_Money+$money2-$OUT_Money]);
    }

    /* Get bank info. */
    public function getSysConfig(Request $request) {
        $sysConfig = SysConfig::all()->first();
        return response()->json(['success'=>true, 'sysConfig' => $sysConfig]);
    }

    /* Transfer function. */
    public function transferMoney(Request $request) {
        $user = User::where('ID',$request->userId)->first();
        $sysConfig = SysConfig::all()->first();
        // if($cou==0){   oid='$uid'
        //     echo "<script>alert('登录超时!');window.open('/','_top');</script>";
        //     exit;
        // }

        $trtype = $request->type;
        if($trtype=='AGIN' or $trtype=='AGOUT'){
            if($sysConfig['AG_Repair']==1 or $sysConfig['AG']==0 or $user['AG_TR']==0){
                echo "<script>alert('真人平台维护中，请稍候再试......');history.go(-1);</script>";
            }
            require $member_dir."ag/tr.php";
        }

        if($trtype=='OGIN' or $trtype=='OGOUT'){
            if($sysConfig['sysConfig']==1 or $sysConfig['OG']==0 or $user['OG_TR']==0){
                echo "<script>alert('真人平台维护中，请稍候再试......');history.go(-1);</script>";
            }
            require $member_dir."OG/tr.php";
        }

        if($trtype=='BBIN' or $trtype=='BBOUT'){
            if($sysConfig['sysConfig']==1 or $sysConfig['sysConfig']==0 or $user['BBIN_TR']==0){
                echo "<script>alert('真人平台维护中，请稍候再试......');history.go(-1);</script>";
            }
            return $this->handleBBIN($request, $user, $sysConfig);
        }

        if($trtype=='MGIN' or $trtype=='MGOUT'){
            if($sysConfig['MG_Repair']==1 or $sysConfig['MG']==0 or $user['MG_TR']==0){
                echo "<script>alert('真人平台维护中，请稍候再试......');history.go(-1);</script>";
            }
            require $member_dir."MG/tr.php";
        }

        if($trtype=='PTIN' or $trtype=='PTOUT'){
            if($sysConfig['PT_Repair']==1 or $sysConfig['PT']==0 or $user['PT_TR']==0){
                echo "<script>alert('真人平台维护中，请稍候再试......');history.go(-1);</script>";
            }
            require $member_dir."PT/tr.php";
        }

        if($trtype=='KYIN' or $trtype=='KYOUT'){
            if($sysConfig['KY_Repair']==1 or $sysConfig['KY']==0 or $user['KY_TR']==0){
                echo "<scriKY>alert('开元棋牌维护中，请稍候再试......');history.go(-1);</scriKY>";
            }
            require $member_dir."chess/tr.php";
        }

        return response()->json(['success'=>true, 'user'=>$sysConfig['AG_Repair']]);
    }

}
