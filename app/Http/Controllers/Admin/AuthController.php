<?php
namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Utils\Utils;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller {

    /* Login action. */
    public function login(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails())
        {
            return response()->json(['success'=>false, 'message'=>$validator->errors()->all()]);
        }

        $level = "M";

        $username = $request->username;

        $str = date('Ymd');
        $uid=substr(md5($username.rand(0,30)),0,6).ucfirst(substr(md5($str),0,rand(50,50))).'ag'.rand(0,1);

        $a=array(
            "'",
            '"',
            ";",
            "and",
            "update",
            "where",
            "set",
            "user",
            "pass",
            "insert"
        );
        $b=array("","","","","","","","","","");
        $host=str_replace($a,$b,strtolower($_SERVER['HTTP_HOST']));
        $host=substr($host,0,30);

        if(isset($_SERVER["HTTPS"]) or $_SERVER['SERVER_PORT']==443){
            $https='https://';
        }else{
            if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                $https=$_SERVER['HTTP_X_FORWARDED_PROTO'].'://';
            }else{
                $https='http://';
            }
        }

        $browser_ip = $https.$host;
        $date=date("Y-m-d");

        $ip_addr = Utils::get_ip();
        $loginfo='用户登入成功';
        switch ($level){
        case 'M':
            $lv='管理员';
            break;
        case 'A':
            $lv='公司';
            break;
        case 'B':
            $lv='股东';
            break;
        case 'C':
            $lv='总代理';
            break;
        case 'D':
            $lv='代理商';
            break;
        }

        $user = User::where('username', $request->username)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {                
                $sql="update web_system_data set Level='$level',Oid='$uid',LoginDate='$date',LoginTime=now(),OnlineTime=now(),Online='1',Url='".$browser_ip."',LoginIP='$ip_addr' where username='".$username."'";
                DB::select($sql);
                $mysql="insert into web_mem_log_data(UserName,LoginIP,LoginTime,ConText,Url,Level)values('$username','$ip_addr',now(),'$loginfo','".$browser_ip."','$lv')";
                DB::select($mysql);
                $token = $user->createToken('adminToken')->accessToken;
                return $this->respondWithToken($token, 'Login successfully.', auth()->guard('admin')->user());
            } else {
                return response()->json(['success'=>false, 'message'=>'Password mismatch']);
            }
        } else {
            return response()->json(['success'=>false, 'message'=>'User does not exist']);
        }
    }

    /* Register action. */
    public function register(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50',
            'password' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['success'=>false, 'message'=>$validator->messages()->toArray()]);

        $user = User::where('username', $username)->first();
        if ($user)
            return response()->json(['success'=>false, 'message'=>'Your username already exists.']);

        $user = new User;
        $user->username = $username;
        $user->password = Hash::make($password);

        if ($user->save())
            return response()->json(['success'=>true, 'message'=>'Register successfully.']);
        else
            return response()->json(['success'=>false, 'message'=>'Register operation failure.']);
    }

    /* Get user info. */
    public function user(Request $request) {
        return $request->user();
    }
}
