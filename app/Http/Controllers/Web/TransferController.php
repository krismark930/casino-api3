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

class TransferController extends Controller {

    public function __construct(){
        //$this->middleware("auth:api");
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
        $tp = $request->type;
        if($tp=='AGIN' or $tp=='AGOUT'){
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
            //handleBBIN($request, $user, $sysConfig);
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

        $BBIN_username = $user->BBIN_User;
        $BBIN_password = $user->BBIN_Pass;
        $username = $user->UserName;
        $alias = $user->Alias;
        $money = $request->money;

        $tp=str_replace("BB","",$tp);

        return response()->json(['success'=>true, 'user'=>$sysConfig['AG_Repair']]);
    }
    // BBIN handle
    public function handleBBIN(Request $request, User $user, SysConfig $sysConfig ) {

    }
}
