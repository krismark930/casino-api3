<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\Utils\Utils;
use App\Models\WebAgent;
use App\Models\WebMemberLogs;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {

    protected $user;

    public function __construct() {

        $this->middleware("auth:api",["except" => ["login","register"]]);

        $this->user = new User;
    }

    public function register(Request $request){

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'UserName' => 'required|string',
                'password' => 'required|min:6',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $inviter = $request_data['inviter_id'];            
            $user_name = $request_data['UserName'];
            $password = $request_data["password"];
            $intro = $request_data["intro"] ?? "";

            $inviter_id = 0;

            if($inviter <> ''){
                $inviter_id = User::where('invite_url', '/login'.'/'.$inviter)->first()->id;
            }

            $InviteUrl = 'IU'.date("YmdHis",time()+12*3600).mt_rand(100, 999);

            if ( $intro == '' || $intro == null){
                $agent = 'ddm999';
            } else {
                $agent = $intro;
            }

            $web_agent = WebAgent::where("UserName", $agent)->first();

            if (!isset($web_agent)) {
                $web_agent = WebAgent::where("UserName", "ddm99")->first();
            }

            $world = $web_agent['World'];
            $corprator = $web_agent['Corprator'];
            $super = $web_agent['Super'];
            $admin = $web_agent['Admin'];
            $sports = $web_agent['Sports'];
            $lottery = $web_agent['Lottery'];

            $FT_Turn_R_A = $web_agent['FT_Turn_R_A'];
            $FT_Turn_R_B = $web_agent['FT_Turn_R_B'];
            $FT_Turn_R_C = $web_agent['FT_Turn_R_C'];
            $FT_Turn_R_D = $web_agent['FT_Turn_R_D'];
            $FT_R_Bet = $web_agent['FT_R_Bet'];
            $FT_R_Scene = $web_agent['FT_R_Scene'];
            $FT_Turn_OU_A = $web_agent['FT_Turn_OU_A'];
            $FT_Turn_OU_B = $web_agent['FT_Turn_OU_B'];
            $FT_Turn_OU_C = $web_agent['FT_Turn_OU_C'];
            $FT_Turn_OU_D = $web_agent['FT_Turn_OU_D'];
            $FT_OU_Bet = $web_agent['FT_OU_Bet'];
            $FT_OU_Scene = $web_agent['FT_OU_Scene'];
            $FT_Turn_VR_A = $web_agent['FT_Turn_VR_A'];
            $FT_Turn_VR_B = $web_agent['FT_Turn_VR_B'];
            $FT_Turn_VR_C = $web_agent['FT_Turn_VR_C'];
            $FT_Turn_VR_D = $web_agent['FT_Turn_VR_D'];
            $FT_VR_Bet = $web_agent['FT_VR_Bet'];
            $FT_VR_Scene = $web_agent['FT_VR_Scene'];
            $FT_Turn_VOU_A = $web_agent['FT_Turn_VOU_A'];
            $FT_Turn_VOU_B = $web_agent['FT_Turn_VOU_B'];
            $FT_Turn_VOU_C = $web_agent['FT_Turn_VOU_C'];
            $FT_Turn_VOU_D = $web_agent['FT_Turn_VOU_D'];
            $FT_VOU_Bet = $web_agent['FT_VOU_Bet'];
            $FT_VOU_Scene = $web_agent['FT_VOU_Scene'];
            $FT_Turn_RE_A = $web_agent['FT_Turn_RE_A'];
            $FT_Turn_RE_B = $web_agent['FT_Turn_RE_B'];
            $FT_Turn_RE_C = $web_agent['FT_Turn_RE_C'];
            $FT_Turn_RE_D = $web_agent['FT_Turn_RE_D'];
            $FT_RE_Bet = $web_agent['FT_RE_Bet'];
            $FT_RE_Scene = $web_agent['FT_RE_Scene'];
            $FT_Turn_ROU_A = $web_agent['FT_Turn_ROU_A'];
            $FT_Turn_ROU_B = $web_agent['FT_Turn_ROU_B'];
            $FT_Turn_ROU_C = $web_agent['FT_Turn_ROU_C'];
            $FT_Turn_ROU_D = $web_agent['FT_Turn_ROU_D'];
            $FT_ROU_Bet = $web_agent['FT_ROU_Bet'];
            $FT_ROU_Scene = $web_agent['FT_ROU_Scene'];
            $FT_Turn_VRE_A = $web_agent['FT_Turn_VRE_A'];
            $FT_Turn_VRE_B = $web_agent['FT_Turn_VRE_B'];
            $FT_Turn_VRE_C = $web_agent['FT_Turn_VRE_C'];
            $FT_Turn_VRE_D = $web_agent['FT_Turn_VRE_D'];
            $FT_VRE_Bet = $web_agent['FT_VRE_Bet'];
            $FT_VRE_Scene = $web_agent['FT_VRE_Scene'];
            $FT_Turn_VROU_A = $web_agent['FT_Turn_VROU_A'];
            $FT_Turn_VROU_B = $web_agent['FT_Turn_VROU_B'];
            $FT_Turn_VROU_C = $web_agent['FT_Turn_VROU_C'];
            $FT_Turn_VROU_D = $web_agent['FT_Turn_VROU_D'];
            $FT_VROU_Bet = $web_agent['FT_VROU_Bet'];
            $FT_VROU_Scene = $web_agent['FT_VROU_Scene'];
            $FT_Turn_EO_A = $web_agent['FT_Turn_EO_A'];
            $FT_Turn_EO_B = $web_agent['FT_Turn_EO_B'];
            $FT_Turn_EO_C = $web_agent['FT_Turn_EO_C'];
            $FT_Turn_EO_D = $web_agent['FT_Turn_EO_D'];
            $FT_EO_Bet = $web_agent['FT_EO_Bet'];
            $FT_EO_Scene = $web_agent['FT_EO_Scene'];
            $FT_Turn_RM = $web_agent['FT_Turn_RM'];
            $FT_RM_Bet = $web_agent['FT_RM_Bet'];
            $FT_RM_Scene = $web_agent['FT_RM_Scene'];
            $FT_Turn_M = $web_agent['FT_Turn_M'];
            $FT_M_Bet = $web_agent['FT_M_Bet'];
            $FT_M_Scene = $web_agent['FT_M_Scene'];
            $FT_Turn_PD = $web_agent['FT_Turn_PD'];
            $FT_PD_Bet = $web_agent['FT_PD_Bet'];
            $FT_PD_Scene = $web_agent['FT_PD_Scene'];
            $FT_Turn_T = $web_agent['FT_Turn_T'];
            $FT_T_Bet = $web_agent['FT_T_Bet'];
            $FT_T_Scene = $web_agent['FT_T_Scene'];
            $FT_Turn_F = $web_agent['FT_Turn_F'];
            $FT_F_Bet = $web_agent['FT_F_Bet'];
            $FT_F_Scene = $web_agent['FT_F_Scene'];
            $FT_Turn_P = $web_agent['FT_Turn_P'];
            $FT_P_Bet = $web_agent['FT_P_Bet'];
            $FT_P_Scene = $web_agent['FT_P_Scene'];
            $FT_Turn_PR = $web_agent['FT_Turn_PR'];
            $FT_PR_Bet = $web_agent['FT_PR_Bet'];
            $FT_PR_Scene = $web_agent['FT_PR_Scene'];
            $FT_Turn_P3 = $web_agent['FT_Turn_P3'];
            $FT_P3_Bet = $web_agent['FT_P3_Bet'];
            $FT_P3_Scene = $web_agent['FT_P3_Scene'];

            $BK_Turn_R_A = $web_agent['BK_Turn_R_A'];
            $BK_Turn_R_B = $web_agent['BK_Turn_R_B'];
            $BK_Turn_R_C = $web_agent['BK_Turn_R_C'];
            $BK_Turn_R_D = $web_agent['BK_Turn_R_D'];
            $BK_R_Bet = $web_agent['BK_R_Bet'];
            $BK_R_Scene = $web_agent['BK_R_Scene'];
            $BK_Turn_OU_A = $web_agent['BK_Turn_OU_A'];
            $BK_Turn_OU_B = $web_agent['BK_Turn_OU_B'];
            $BK_Turn_OU_C = $web_agent['BK_Turn_OU_C'];
            $BK_Turn_OU_D = $web_agent['BK_Turn_OU_D'];
            $BK_OU_Bet = $web_agent['BK_OU_Bet'];
            $BK_OU_Scene = $web_agent['BK_OU_Scene'];
            $BK_Turn_VR_A = $web_agent['BK_Turn_VR_A'];
            $BK_Turn_VR_B = $web_agent['BK_Turn_VR_B'];
            $BK_Turn_VR_C = $web_agent['BK_Turn_VR_C'];
            $BK_Turn_VR_D = $web_agent['BK_Turn_VR_D'];
            $BK_VR_Bet = $web_agent['BK_VR_Bet'];
            $BK_VR_Scene = $web_agent['BK_VR_Scene'];
            $BK_Turn_VOU_A = $web_agent['BK_Turn_VOU_A'];
            $BK_Turn_VOU_B = $web_agent['BK_Turn_VOU_B'];
            $BK_Turn_VOU_C = $web_agent['BK_Turn_VOU_C'];
            $BK_Turn_VOU_D = $web_agent['BK_Turn_VOU_D'];
            $BK_VOU_Bet = $web_agent['BK_VOU_Bet'];
            $BK_VOU_Scene = $web_agent['BK_VOU_Scene'];

            $BK_Turn_RE_A = $web_agent['BK_Turn_RE_A'];
            $BK_Turn_RE_B = $web_agent['BK_Turn_RE_B'];
            $BK_Turn_RE_C = $web_agent['BK_Turn_RE_C'];
            $BK_Turn_RE_D = $web_agent['BK_Turn_RE_D'];
            $BK_RE_Bet = $web_agent['BK_RE_Bet'];
            $BK_RE_Scene = $web_agent['BK_RE_Scene'];
            $BK_Turn_ROU_A = $web_agent['BK_Turn_ROU_A'];
            $BK_Turn_ROU_B = $web_agent['BK_Turn_ROU_B'];
            $BK_Turn_ROU_C = $web_agent['BK_Turn_ROU_C'];
            $BK_Turn_ROU_D = $web_agent['BK_Turn_ROU_D'];
            $BK_ROU_Bet = $web_agent['BK_ROU_Bet'];
            $BK_ROU_Scene = $web_agent['BK_ROU_Scene'];
            $BK_Turn_VRE_A = $web_agent['BK_Turn_VRE_A'];
            $BK_Turn_VRE_B = $web_agent['BK_Turn_VRE_B'];
            $BK_Turn_VRE_C = $web_agent['BK_Turn_VRE_C'];
            $BK_Turn_VRE_D = $web_agent['BK_Turn_VRE_D'];
            $BK_VRE_Bet = $web_agent['BK_VRE_Bet'];
            $BK_VRE_Scene = $web_agent['BK_VRE_Scene'];
            $BK_Turn_VROU_A = $web_agent['BK_Turn_VROU_A'];
            $BK_Turn_VROU_B = $web_agent['BK_Turn_VROU_B'];
            $BK_Turn_VROU_C = $web_agent['BK_Turn_VROU_C'];
            $BK_Turn_VROU_D = $web_agent['BK_Turn_VROU_D'];
            $BK_VROU_Bet = $web_agent['BK_VROU_Bet'];
            $BK_VROU_Scene = $web_agent['BK_VROU_Scene'];
            $BK_Turn_EO_A = $web_agent['BK_Turn_EO_A'];
            $BK_Turn_EO_B = $web_agent['BK_Turn_EO_B'];
            $BK_Turn_EO_C = $web_agent['BK_Turn_EO_C'];
            $BK_Turn_EO_D = $web_agent['BK_Turn_EO_D'];
            $BK_EO_Bet = $web_agent['BK_EO_Bet'];
            $BK_EO_Scene = $web_agent['BK_EO_Scene'];
            $BK_Turn_PR = $web_agent['BK_Turn_PR'];
            $BK_PR_Bet = $web_agent['BK_PR_Bet'];
            $BK_PR_Scene = $web_agent['BK_PR_Scene'];
            $BK_Turn_P3 = $web_agent['BK_Turn_P3'];
            $BK_P3_Bet = $web_agent['BK_P3_Bet'];
            $BK_P3_Scene = $web_agent['BK_P3_Scene'];

            $BS_Turn_R_A = $web_agent['BS_Turn_R_A'];
            $BS_Turn_R_B = $web_agent['BS_Turn_R_B'];
            $BS_Turn_R_C = $web_agent['BS_Turn_R_C'];
            $BS_Turn_R_D = $web_agent['BS_Turn_R_D'];
            $BS_R_Bet = $web_agent['BS_R_Bet'];
            $BS_R_Scene = $web_agent['BS_R_Scene'];
            $BS_Turn_OU_A = $web_agent['BS_Turn_OU_A'];
            $BS_Turn_OU_B = $web_agent['BS_Turn_OU_B'];
            $BS_Turn_OU_C = $web_agent['BS_Turn_OU_C'];
            $BS_Turn_OU_D = $web_agent['BS_Turn_OU_D'];
            $BS_OU_Bet = $web_agent['BS_OU_Bet'];
            $BS_OU_Scene = $web_agent['BS_OU_Scene'];
            $BS_Turn_VR_A = $web_agent['BS_Turn_VR_A'];
            $BS_Turn_VR_B = $web_agent['BS_Turn_VR_B'];
            $BS_Turn_VR_C = $web_agent['BS_Turn_VR_C'];
            $BS_Turn_VR_D = $web_agent['BS_Turn_VR_D'];
            $BS_VR_Bet = $web_agent['BS_VR_Bet'];
            $BS_VR_Scene = $web_agent['BS_VR_Scene'];
            $BS_Turn_VOU_A = $web_agent['BS_Turn_VOU_A'];
            $BS_Turn_VOU_B = $web_agent['BS_Turn_VOU_B'];
            $BS_Turn_VOU_C = $web_agent['BS_Turn_VOU_C'];
            $BS_Turn_VOU_D = $web_agent['BS_Turn_VOU_D'];
            $BS_VOU_Bet = $web_agent['BS_VOU_Bet'];
            $BS_VOU_Scene = $web_agent['BS_VOU_Scene'];
            $BS_Turn_RE_A = $web_agent['BS_Turn_RE_A'];
            $BS_Turn_RE_B = $web_agent['BS_Turn_RE_B'];
            $BS_Turn_RE_C = $web_agent['BS_Turn_RE_C'];
            $BS_Turn_RE_D = $web_agent['BS_Turn_RE_D'];
            $BS_RE_Bet = $web_agent['BS_RE_Bet'];
            $BS_RE_Scene = $web_agent['BS_RE_Scene'];
            $BS_Turn_ROU_A = $web_agent['BS_Turn_ROU_A'];
            $BS_Turn_ROU_B = $web_agent['BS_Turn_ROU_B'];
            $BS_Turn_ROU_C = $web_agent['BS_Turn_ROU_C'];
            $BS_Turn_ROU_D = $web_agent['BS_Turn_ROU_D'];
            $BS_ROU_Bet = $web_agent['BS_ROU_Bet'];
            $BS_ROU_Scene = $web_agent['BS_ROU_Scene'];
            $BS_Turn_VRE_A = $web_agent['BS_Turn_VRE_A'];
            $BS_Turn_VRE_B = $web_agent['BS_Turn_VRE_B'];
            $BS_Turn_VRE_C = $web_agent['BS_Turn_VRE_C'];
            $BS_Turn_VRE_D = $web_agent['BS_Turn_VRE_D'];
            $BS_VRE_Bet = $web_agent['BS_VRE_Bet'];
            $BS_VRE_Scene = $web_agent['BS_VRE_Scene'];
            $BS_Turn_VROU_A = $web_agent['BS_Turn_VROU_A'];
            $BS_Turn_VROU_B = $web_agent['BS_Turn_VROU_B'];
            $BS_Turn_VROU_C = $web_agent['BS_Turn_VROU_C'];
            $BS_Turn_VROU_D = $web_agent['BS_Turn_VROU_D'];
            $BS_VROU_Bet = $web_agent['BS_VROU_Bet'];
            $BS_VROU_Scene = $web_agent['BS_VROU_Scene'];
            $BS_Turn_EO_A = $web_agent['BS_Turn_EO_A'];
            $BS_Turn_EO_B = $web_agent['BS_Turn_EO_B'];
            $BS_Turn_EO_C = $web_agent['BS_Turn_EO_C'];
            $BS_Turn_EO_D = $web_agent['BS_Turn_EO_D'];
            $BS_EO_Bet = $web_agent['BS_EO_Bet'];
            $BS_EO_Scene = $web_agent['BS_EO_Scene'];
            $BS_Turn_1X2_A = $web_agent['BS_Turn_1X2_A'];
            $BS_Turn_1X2_B = $web_agent['BS_Turn_1X2_B'];
            $BS_Turn_1X2_C = $web_agent['BS_Turn_1X2_C'];
            $BS_Turn_1X2_D = $web_agent['BS_Turn_1X2_D'];
            $BS_1X2_Bet = $web_agent['BS_1X2_Bet'];
            $BS_1X2_Scene = $web_agent['BS_1X2_Scene'];
            $BS_Turn_M = $web_agent['BS_Turn_M'];
            $BS_M_Bet = $web_agent['BS_M_Bet'];
            $BS_M_Scene = $web_agent['BS_M_Scene'];
            $BS_Turn_PD = $web_agent['BS_Turn_PD'];
            $BS_PD_Bet = $web_agent['BS_PD_Bet'];
            $BS_PD_Scene = $web_agent['BS_PD_Scene'];
            $BS_Turn_T = $web_agent['BS_Turn_T'];
            $BS_T_Bet = $web_agent['BS_T_Bet']; 
            $BS_T_Scene = $web_agent['BS_T_Scene'];
            $BS_Turn_P = $web_agent['BS_Turn_P'];
            $BS_P_Bet = $web_agent['BS_P_Bet'];
            $BS_P_Scene = $web_agent['BS_P_Scene'];
            $BS_Turn_PR = $web_agent['BS_Turn_PR'];
            $BS_PR_Bet = $web_agent['BS_PR_Bet'];
            $BS_PR_Scene = $web_agent['BS_PR_Scene'];
            $BS_Turn_P3 = $web_agent['BS_Turn_P3'];
            $BS_P3_Bet = $web_agent['BS_P3_Bet'];
            $BS_P3_Scene = $web_agent['BS_P3_Scene'];


            $TN_Turn_R_A = $web_agent['TN_Turn_R_A'];
            $TN_Turn_R_B = $web_agent['TN_Turn_R_B'];
            $TN_Turn_R_C = $web_agent['TN_Turn_R_C'];
            $TN_Turn_R_D = $web_agent['TN_Turn_R_D'];
            $TN_R_Bet = $web_agent['TN_R_Bet'];
            $TN_R_Scene = $web_agent['TN_R_Scene'];
            $TN_Turn_OU_A = $web_agent['TN_Turn_OU_A'];
            $TN_Turn_OU_B = $web_agent['TN_Turn_OU_B'];
            $TN_Turn_OU_C = $web_agent['TN_Turn_OU_C'];
            $TN_Turn_OU_D = $web_agent['TN_Turn_OU_D'];
            $TN_OU_Bet = $web_agent['TN_OU_Bet'];
            $TN_OU_Scene = $web_agent['TN_OU_Scene'];
            $TN_Turn_RE_A = $web_agent['TN_Turn_RE_A'];
            $TN_Turn_RE_B = $web_agent['TN_Turn_RE_B'];
            $TN_Turn_RE_C = $web_agent['TN_Turn_RE_C'];
            $TN_Turn_RE_D = $web_agent['TN_Turn_RE_D'];
            $TN_RE_Bet = $web_agent['TN_RE_Bet'];
            $TN_RE_Scene = $web_agent['TN_RE_Scene'];
            $TN_Turn_ROU_A = $web_agent['TN_Turn_ROU_A'];
            $TN_Turn_ROU_B = $web_agent['TN_Turn_ROU_B'];
            $TN_Turn_ROU_C = $web_agent['TN_Turn_ROU_C'];
            $TN_Turn_ROU_D = $web_agent['TN_Turn_ROU_D'];
            $TN_ROU_Bet = $web_agent['TN_ROU_Bet'];
            $TN_ROU_Scene = $web_agent['TN_ROU_Scene'];
            $TN_Turn_EO_A = $web_agent['TN_Turn_EO_A'];
            $TN_Turn_EO_B = $web_agent['TN_Turn_EO_B'];
            $TN_Turn_EO_C = $web_agent['TN_Turn_EO_C'];
            $TN_Turn_EO_D = $web_agent['TN_Turn_EO_D'];
            $TN_EO_Bet = $web_agent['TN_EO_Bet'];
            $TN_EO_Scene = $web_agent['TN_EO_Scene'];
            $TN_Turn_M = $web_agent['TN_Turn_M'];
            $TN_M_Bet = $web_agent['TN_M_Bet'];
            $TN_M_Scene = $web_agent['TN_M_Scene'];
            $TN_Turn_PD = $web_agent['TN_Turn_PD'];
            $TN_PD_Scene = $web_agent['TN_PD_Scene'];
            $TN_PD_Bet = $web_agent['TN_PD_Bet'];
            $TN_Turn_P = $web_agent['TN_Turn_P'];
            $TN_P_Scene = $web_agent['TN_P_Scene'];
            $TN_P_Bet = $web_agent['TN_P_Bet'];
            $TN_Turn_PR = $web_agent['TN_Turn_PR'];
            $TN_PR_Bet = $web_agent['TN_PR_Bet'];
            $TN_PR_Scene = $web_agent['TN_PR_Scene'];
            $TN_Turn_P3 = $web_agent['TN_Turn_P3'];
            $TN_P3_Bet = $web_agent['TN_P3_Bet'];
            $TN_P3_Scene = $web_agent['TN_P3_Scene'];


            $VB_Turn_R_A = $web_agent['VB_Turn_R_A'];
            $VB_Turn_R_B = $web_agent['VB_Turn_R_B'];
            $VB_Turn_R_C = $web_agent['VB_Turn_R_C'];
            $VB_Turn_R_D = $web_agent['VB_Turn_R_D'];
            $VB_R_Bet = $web_agent['VB_R_Bet'];
            $VB_R_Scene = $web_agent['VB_R_Scene'];
            $VB_Turn_OU_A = $web_agent['VB_Turn_OU_A'];
            $VB_Turn_OU_B = $web_agent['VB_Turn_OU_B'];
            $VB_Turn_OU_C = $web_agent['VB_Turn_OU_C'];
            $VB_Turn_OU_D = $web_agent['VB_Turn_OU_D'];
            $VB_OU_Bet = $web_agent['VB_OU_Bet'];
            $VB_OU_Scene = $web_agent['VB_OU_Scene'];
            $VB_Turn_RE_A = $web_agent['VB_Turn_RE_A'];
            $VB_Turn_RE_B = $web_agent['VB_Turn_RE_B'];
            $VB_Turn_RE_C = $web_agent['VB_Turn_RE_C'];
            $VB_Turn_RE_D = $web_agent['VB_Turn_RE_D'];
            $VB_RE_Bet = $web_agent['VB_RE_Bet'];
            $VB_RE_Scene = $web_agent['VB_RE_Scene'];
            $VB_Turn_ROU_A = $web_agent['VB_Turn_ROU_A'];
            $VB_Turn_ROU_B = $web_agent['VB_Turn_ROU_B'];
            $VB_Turn_ROU_C = $web_agent['VB_Turn_ROU_C'];
            $VB_Turn_ROU_D = $web_agent['VB_Turn_ROU_D'];
            $VB_ROU_Bet = $web_agent['VB_ROU_Bet'];
            $VB_ROU_Scene = $web_agent['VB_ROU_Scene'];
            $VB_Turn_EO_A = $web_agent['VB_Turn_EO_A'];
            $VB_Turn_EO_B = $web_agent['VB_Turn_EO_B'];
            $VB_Turn_EO_C = $web_agent['VB_Turn_EO_C'];
            $VB_Turn_EO_D = $web_agent['VB_Turn_EO_D'];
            $VB_EO_Bet = $web_agent['VB_EO_Bet'];
            $VB_EO_Scene = $web_agent['VB_EO_Scene'];
            $VB_Turn_M = $web_agent['VB_Turn_M'];
            $VB_M_Bet = $web_agent['VB_M_Bet'];
            $VB_M_Scene = $web_agent['VB_M_Scene'];
            $VB_Turn_PD = $web_agent['VB_Turn_PD'];
            $VB_PD_Bet = $web_agent['VB_PD_Bet'];
            $VB_PD_Scene = $web_agent['VB_PD_Scene'];
            $VB_Turn_P = $web_agent['VB_Turn_P'];
            $VB_P_Bet = $web_agent['VB_P_Bet'];
            $VB_P_Scene = $web_agent['VB_P_Scene'];
            $VB_Turn_PR = $web_agent['VB_Turn_PR'];
            $VB_PR_Bet = $web_agent['VB_PR_Bet'];
            $VB_PR_Scene = $web_agent['VB_PR_Scene'];
            $VB_Turn_P3 = $web_agent['VB_Turn_P3'];
            $VB_P3_Bet = $web_agent['VB_P3_Bet'];
            $VB_P3_Scene = $web_agent['VB_P3_Scene'];


            $OP_Turn_R_A = $web_agent['OP_Turn_R_A'];
            $OP_Turn_R_B = $web_agent['OP_Turn_R_B'];
            $OP_Turn_R_C = $web_agent['OP_Turn_R_C'];
            $OP_Turn_R_D = $web_agent['OP_Turn_R_D'];
            $OP_R_Bet = $web_agent['OP_R_Bet'];
            $OP_R_Scene = $web_agent['OP_R_Scene'];
            $OP_Turn_OU_A = $web_agent['OP_Turn_OU_A'];
            $OP_Turn_OU_B = $web_agent['OP_Turn_OU_B'];
            $OP_Turn_OU_C = $web_agent['OP_Turn_OU_C'];
            $OP_Turn_OU_D = $web_agent['OP_Turn_OU_D'];
            $OP_OU_Bet = $web_agent['OP_OU_Bet'];
            $OP_OU_Scene = $web_agent['OP_OU_Scene'];
            $OP_Turn_VR_A = $web_agent['OP_Turn_VR_A'];
            $OP_Turn_VR_B = $web_agent['OP_Turn_VR_B'];
            $OP_Turn_VR_C = $web_agent['OP_Turn_VR_C'];
            $OP_Turn_VR_D = $web_agent['OP_Turn_VR_D'];
            $OP_VR_Bet = $web_agent['OP_VR_Bet'];
            $OP_VR_Scene = $web_agent['OP_VR_Scene'];
            $OP_Turn_VOU_A = $web_agent['OP_Turn_VOU_A'];
            $OP_Turn_VOU_B = $web_agent['OP_Turn_VOU_B'];
            $OP_Turn_VOU_C = $web_agent['OP_Turn_VOU_C'];
            $OP_Turn_VOU_D = $web_agent['OP_Turn_VOU_D'];
            $OP_VOU_Bet = $web_agent['OP_VOU_Bet'];
            $OP_VOU_Scene = $web_agent['OP_VOU_Scene'];
            $OP_Turn_RE_A = $web_agent['OP_Turn_RE_A'];
            $OP_Turn_RE_B = $web_agent['OP_Turn_RE_B'];
            $OP_Turn_RE_C = $web_agent['OP_Turn_RE_C'];
            $OP_Turn_RE_D = $web_agent['OP_Turn_RE_D'];
            $OP_RE_Bet = $web_agent['OP_RE_Bet'];
            $OP_RE_Scene = $web_agent['OP_RE_Scene'];
            $OP_Turn_ROU_A = $web_agent['OP_Turn_ROU_A'];
            $OP_Turn_ROU_B = $web_agent['OP_Turn_ROU_B'];
            $OP_Turn_ROU_C = $web_agent['OP_Turn_ROU_C'];
            $OP_Turn_ROU_D = $web_agent['OP_Turn_ROU_D'];
            $OP_ROU_Bet = $web_agent['OP_ROU_Bet'];
            $OP_ROU_Scene = $web_agent['OP_ROU_Scene'];
            $OP_Turn_VRE_A = $web_agent['OP_Turn_VRE_A'];
            $OP_Turn_VRE_B = $web_agent['OP_Turn_VRE_B'];
            $OP_Turn_VRE_C = $web_agent['OP_Turn_VRE_C'];
            $OP_Turn_VRE_D = $web_agent['OP_Turn_VRE_D'];
            $OP_VRE_Bet = $web_agent['OP_VRE_Bet'];
            $OP_VRE_Scene = $web_agent['OP_VRE_Scene'];
            $OP_Turn_VROU_A = $web_agent['OP_Turn_VROU_A'];
            $OP_Turn_VROU_B = $web_agent['OP_Turn_VROU_B'];
            $OP_Turn_VROU_C = $web_agent['OP_Turn_VROU_C'];
            $OP_Turn_VROU_D = $web_agent['OP_Turn_VROU_D'];
            $OP_VROU_Bet = $web_agent['OP_VROU_Bet'];
            $OP_VROU_Scene = $web_agent['OP_VROU_Scene'];
            $OP_Turn_EO_A = $web_agent['OP_Turn_EO_A'];
            $OP_Turn_EO_B = $web_agent['OP_Turn_EO_B'];
            $OP_Turn_EO_C = $web_agent['OP_Turn_EO_C'];
            $OP_Turn_EO_D = $web_agent['OP_Turn_EO_D'];
            $OP_EO_Bet = $web_agent['OP_EO_Bet'];
            $OP_EO_Scene = $web_agent['OP_EO_Scene'];
            $OP_Turn_M = $web_agent['OP_Turn_M'];
            $OP_M_Bet = $web_agent['OP_M_Bet'];
            $OP_M_Scene = $web_agent['OP_M_Scene'];
            $OP_Turn_PD = $web_agent['OP_Turn_PD'];
            $OP_PD_Bet = $web_agent['OP_PD_Bet'];
            $OP_PD_Scene = $web_agent['OP_PD_Scene'];
            $OP_Turn_T = $web_agent['OP_Turn_T'];
            $OP_T_Bet = $web_agent['OP_T_Bet'];
            $OP_T_Scene = $web_agent['OP_T_Scene'];
            $OP_Turn_F = $web_agent['OP_Turn_F'];
            $OP_F_Bet = $web_agent['OP_F_Bet'];
            $OP_F_Scene = $web_agent['OP_F_Scene'];
            $OP_Turn_P = $web_agent['OP_Turn_P'];
            $OP_P_Bet = $web_agent['OP_P_Bet'];
            $OP_P_Scene = $web_agent['OP_P_Scene'];
            $OP_Turn_PR = $web_agent['OP_Turn_PR'];
            $OP_PR_Bet = $web_agent['OP_PR_Bet'];
            $OP_PR_Scene = $web_agent['OP_PR_Scene'];
            $OP_Turn_P3 = $web_agent['OP_Turn_P3'];
            $OP_P3_Bet = $web_agent['OP_P3_Bet'];
            $OP_P3_Scene = $web_agent['OP_P3_Scene'];


            $FU_Turn_OU_A = $web_agent['FU_Turn_OU_A'];
            $FU_Turn_OU_B = $web_agent['FU_Turn_OU_B'];
            $FU_Turn_OU_C = $web_agent['FU_Turn_OU_C'];
            $FU_Turn_OU_D = $web_agent['FU_Turn_OU_D'];
            $FU_OU_Bet = $web_agent['FU_OU_Bet'];
            $FU_OU_Scene = $web_agent['FU_OU_Scene'];
            $FU_Turn_EO_A = $web_agent['FU_Turn_EO_A'];
            $FU_Turn_EO_B = $web_agent['FU_Turn_EO_B'];
            $FU_Turn_EO_C = $web_agent['FU_Turn_EO_C'];
            $FU_Turn_EO_D = $web_agent['FU_Turn_EO_D'];
            $FU_EO_Bet = $web_agent['FU_EO_Bet'];
            $FU_EO_Scene = $web_agent['FU_EO_Scene'];
            $FU_Turn_PD = $web_agent['FU_Turn_PD'];
            $FU_PD_Bet = $web_agent['FU_PD_Bet'];
            $FU_PD_Scene = $web_agent['FU_PD_Scene'];

            $FS_Turn_FS = $web_agent['FS_Turn_FS'];
            $FS_FS_Bet = $web_agent['FS_FS_Bet'];
            $FS_FS_Scene = $web_agent['FS_FS_Scene'];

            $opentype = 'C';  //返水盘口
            $opentype2 = $opentype; //会员盘口

            $ip_addr = Utils::get_ip();

            $user = User::where("UserName", $user_name)->first();

            if (isset($user)) {
                $response['message'] = '帐户已经有人使用，请重新注册！';
                return response()->json($response, $response['status']);
            }

            $users = User::where("LoginIP", $ip_addr)->get();

            if (count($users) > 100) {
                $response['message'] = '您已经注册过了，请不要重复注册！';                
                return response()->json($response, $response['status']);
            }

            $AddDate = date('Y-m-d H:i:s'); //新增日期

            $user = new User();

            $user["UserName"] = $user_name;
            $user["LoginName"] = $user_name;
            $user["password"] = Hash::make($password);
            $user["invite_url"] = '/login'.'/'.$InviteUrl;
            $user["inviter_id"] = $inviter_id;
            $user["Credit"] = 0;
            $user["Money"] = 0;
            $user["Alias"] = $alias ?? "";
            $user["Sports"] = $sports;
            $user["Lottery"] = $lottery;
            $user["AddDate"] = $AddDate;
            $user["EditDate"] = date('Y-m-d');
            $user["LoginDate"] = date('Y-m-d');
            $user["LoginTime"] = date('Y-m-d H:i:s');
            $user["LogoutTime"] = date('Y-m-d H:i:s');
            $user["OnlineTime"] = date('Y-m-d H:i:s');
            $user["Status"] = 0;
            $user["CurType"] = "RMB";
            $user["Pay_Type"] = 1;
            $user["Opentype"] = $opentype2;
            $user["agents"] = $agent;
            $user["World"] = $world;
            $user["Corprator"] = $corprator;
            $user["Super"] = $super;
            $user["Admin"] = $admin;
            $user["Phone"] = $phone ?? "";
            $user["Notes"] = $notes ?? "";
            $user["Address"] = $address ?? "";
            $user["LoginIP"] = $ip_addr ?? "";
            $user["Reg"] = 1;
            $user["FT_Turn_R"] = "FT_Turn_R_".$opentype;
            $user["FT_R_Bet"] = $FT_R_Bet;
            $user["FT_R_Scene"] = $FT_R_Scene;
            $user["FT_Turn_OU"] = "FT_Turn_OU_".$opentype;
            $user["FT_OU_Bet"] = $FT_OU_Bet;
            $user["FT_OU_Scene"] = $FT_OU_Scene;
            $user["FT_Turn_VR"] = "FT_Turn_VR_".$opentype;
            $user["FT_VR_Bet"] = $FT_VR_Bet;
            $user["FT_VR_Scene"] = $FT_VR_Scene;
            $user["FT_Turn_VOU"] = "FT_Turn_VOU_".$opentype;
            $user["FT_VOU_Bet"] = $FT_VOU_Bet;
            $user["FT_VOU_Scene"] = $FT_VOU_Scene;
            $user["FT_Turn_RE"] = "FT_Turn_RE_".$opentype;
            $user["FT_RE_Bet"] = $FT_RE_Bet;
            $user["FT_RE_Scene"] = $FT_RE_Scene;                
            $user["FT_Turn_ROU"] = "FT_Turn_ROU_".$opentype;
            $user["FT_ROU_Bet"] = $FT_ROU_Bet;
            $user["FT_ROU_Scene"] = $FT_ROU_Scene;
            $user["FT_Turn_VRE"] = "FT_Turn_VRE_".$opentype;
            $user["FT_VRE_Bet"] = $FT_VRE_Bet;
            $user["FT_VRE_Scene"] = $FT_VRE_Scene;                
            $user["FT_Turn_VROU"] = "FT_Turn_VROU_".$opentype;
            $user["FT_VROU_Bet"] = $FT_VROU_Bet;
            $user["FT_VROU_Scene"] = $FT_VROU_Scene;
            $user["FT_Turn_EO"] = "FT_Turn_EO_".$opentype;
            $user["FT_EO_Bet"] = $FT_EO_Bet;
            $user["FT_EO_Scene"] = $FT_EO_Scene;
            $user["FT_Turn_RM"] = $FT_Turn_RM;
            $user["FT_RM_Bet"] = $FT_RM_Bet;
            $user["FT_RM_Scene"] = $FT_RM_Scene;
            $user["FT_Turn_M"] = $FT_Turn_M;
            $user["FT_M_Bet"] = $FT_M_Bet;
            $user["FT_M_Scene"] = $FT_M_Scene;
            $user["FT_Turn_PD"] = $FT_Turn_PD;
            $user["FT_PD_Bet"] = $FT_PD_Bet;
            $user["FT_PD_Scene"] = $FT_PD_Scene;
            $user["FT_Turn_T"] = $FT_Turn_T;
            $user["FT_T_Bet"] = $FT_T_Bet;
            $user["FT_T_Scene"] = $FT_T_Scene;
            $user["FT_Turn_F"] = $FT_Turn_F;
            $user["FT_F_Bet"] = $FT_F_Bet;
            $user["FT_F_Scene"] = $FT_F_Scene;
            $user["FT_Turn_P"] = $FT_Turn_P;
            $user["FT_P_Bet"] = $FT_P_Bet;
            $user["FT_P_Scene"] = $FT_P_Scene;
            $user["FT_Turn_PR"] = $FT_Turn_PR;
            $user["FT_PR_Bet"] = $FT_PR_Bet;
            $user["FT_PR_Scene"] = $FT_PR_Scene;
            $user["FT_Turn_P3"] = $FT_Turn_P3;
            $user["FT_P3_Bet"] = $FT_P3_Bet;
            $user["FT_P3_Scene"] = $FT_P3_Scene;
            $user["BK_Turn_R"] = "BK_Turn_R_".$opentype;
            $user["BK_R_Bet"] = $BK_R_Bet;
            $user["BK_R_Scene"] = $BK_R_Scene;
            $user["BK_Turn_OU"] = "BK_Turn_OU_".$opentype;
            $user["BK_OU_Bet"] = $BK_OU_Bet;
            $user["BK_OU_Scene"] = $BK_OU_Scene;                
            $user["BK_Turn_VR"] = "BK_Turn_VR_".$opentype;
            $user["BK_VR_Bet"] = $BK_VR_Bet;
            $user["BK_VR_Scene"] = $BK_VR_Scene;
            $user["BK_Turn_VOU"] = "BK_Turn_VOU_".$opentype;
            $user["BK_VOU_Bet"] = $BK_VOU_Bet;
            $user["BK_VOU_Scene"] = $BK_VOU_Scene;                
            $user["BK_Turn_RE"] = "BK_Turn_RE_".$opentype;
            $user["BK_RE_Bet"] = $BK_RE_Bet;
            $user["BK_RE_Scene"] = $BK_RE_Scene;                
            $user["BK_Turn_ROU"] = "BK_Turn_ROU_".$opentype;
            $user["BK_ROU_Bet"] = $BK_ROU_Bet;
            $user["BK_ROU_Scene"] = $BK_ROU_Scene;                
            $user["BK_Turn_VRE"] = "BK_Turn_VRE_".$opentype;
            $user["BK_VRE_Bet"] = $BK_VRE_Bet;
            $user["BK_VRE_Scene"] = $BK_VRE_Scene;                
            $user["BK_Turn_VROU"] = "BK_Turn_VROU_".$opentype;
            $user["BK_VROU_Bet"] = $BK_VROU_Bet;
            $user["BK_VROU_Scene"] = $BK_VROU_Scene;                
            $user["BK_Turn_EO"] = "BK_Turn_EO_".$opentype;
            $user["BK_EO_Bet"] = $BK_EO_Bet;
            $user["BK_EO_Scene"] = $BK_EO_Scene;
            $user["BK_Turn_PR"] = $BK_Turn_PR;
            $user["BK_PR_Bet"] = $BK_PR_Bet;
            $user["BK_PR_Scene"] = $BK_PR_Scene;
            $user["BK_Turn_P3"] = $BK_Turn_P3;
            $user["BK_P3_Bet"] = $BK_P3_Bet;
            $user["BK_P3_Scene"] = $BK_P3_Scene;                
            $user["BS_Turn_R"] = "BS_Turn_R_".$opentype;
            $user["BS_R_Bet"] = $BS_R_Bet;
            $user["BS_R_Scene"] = $BS_R_Scene;                
            $user["BS_Turn_OU"] = "BS_Turn_OU_".$opentype;
            $user["BS_OU_Scene"] = $BS_OU_Scene;
            $user["BS_OU_Bet"] = $BS_OU_Bet;                
            $user["BS_Turn_VR"] = "BS_Turn_VR_".$opentype;
            $user["BS_VR_Bet"] = $BS_VR_Bet;
            $user["BS_VR_Scene"] = $BS_VR_Scene;                
            $user["BS_Turn_VOU"] = "BS_Turn_VOU_".$opentype;
            $user["BS_VOU_Scene"] = $BS_VOU_Scene;
            $user["BS_VOU_Bet"] = $BS_VOU_Bet;                
            $user["BS_Turn_RE"] = "BS_Turn_RE_".$opentype;
            $user["BS_RE_Bet"] = $BS_RE_Bet;
            $user["BS_RE_Scene"] = $BS_RE_Scene;                
            $user["BS_Turn_ROU"] = "BS_Turn_ROU_".$opentype;
            $user["BS_ROU_Bet"] = $BS_ROU_Bet;
            $user["BS_ROU_Scene"] = $BS_ROU_Scene;                
            $user["BS_Turn_VRE"] = "BS_Turn_VRE_".$opentype;
            $user["BS_VRE_Bet"] = $BS_VRE_Bet;
            $user["BS_VRE_Scene"] = $BS_VRE_Scene;                
            $user["BS_Turn_VROU"] = "BS_Turn_VROU_".$opentype;
            $user["BS_VROU_Bet"] = $BS_VROU_Bet;
            $user["BS_VROU_Scene"] = $BS_VROU_Scene;
            $user["BS_Turn_EO"] = "BS_Turn_VROU_".$opentype;
            $user["BS_EO_Bet"] = $BS_EO_Bet;
            $user["BS_EO_Scene"] = $BS_EO_Scene;                
            $user["BS_Turn_1X2"] = "BS_Turn_1X2_".$opentype;
            $user["BS_1X2_Bet"] = $BS_1X2_Bet;
            $user["BS_1X2_Scene"] = $BS_1X2_Scene;
            $user["BS_Turn_M"] = $BS_Turn_M;
            $user["BS_M_Bet"] = $BS_M_Bet;
            $user["BS_M_Scene"] = $BS_M_Scene;
            $user["BS_Turn_PD"] = $BS_Turn_PD;
            $user["BS_PD_Bet"] = $BS_PD_Bet;
            $user["BS_PD_Scene"] = $BS_PD_Scene;
            $user["BS_Turn_T"] = $BS_Turn_T;
            $user["BS_T_Bet"] = $BS_T_Bet;  
            $user["BS_T_Scene"] = $BS_T_Scene;
            $user["BS_Turn_P"] = $BS_Turn_P;
            $user["BS_P_Bet"] = $BS_P_Bet;
            $user["BS_P_Scene"] = $BS_P_Scene;
            $user["BS_Turn_PR"] = $BS_Turn_PR;
            $user["BS_PR_Bet"] = $BS_PR_Bet;
            $user["BS_PR_Scene"] = $BS_PR_Scene;
            $user["BS_Turn_P3"] = $BS_Turn_P3;
            $user["BS_P3_Bet"] = $BS_P3_Bet;
            $user["BS_P3_Scene"] = $BS_P3_Scene;                
            $user["TN_Turn_R"] = "TN_Turn_R_".$opentype;
            $user["TN_R_Bet"] = $TN_R_Bet;
            $user["TN_R_Scene"] = $TN_R_Scene;                
            $user["TN_Turn_OU"] = "TN_Turn_OU_".$opentype;
            $user["TN_OU_Bet"] = $TN_OU_Bet;
            $user["TN_OU_Scene"] = $TN_OU_Scene;                
            $user["TN_Turn_RE"] = "TN_Turn_RE_".$opentype;
            $user["TN_RE_Bet"] = $TN_RE_Bet;
            $user["TN_RE_Scene"] = $TN_RE_Scene;                
            $user["TN_Turn_ROU"] = "TN_Turn_ROU_".$opentype;
            $user["TN_ROU_Bet"] = $TN_ROU_Bet;
            $user["TN_ROU_Scene"] = $TN_ROU_Scene;                
            $user["TN_Turn_EO"] = "TN_Turn_EO_".$opentype;
            $user["TN_EO_Bet"] = $TN_EO_Bet;
            $user["TN_EO_Scene"] = $TN_EO_Scene;
            $user["TN_Turn_M"] = $TN_Turn_M;
            $user["TN_M_Bet"] = $TN_M_Bet;
            $user["TN_M_Scene"] = $TN_M_Scene;
            $user["TN_Turn_PD"] = $TN_Turn_PD;
            $user["TN_PD_Bet"] = $TN_PD_Bet;
            $user["TN_PD_Scene"] = $TN_PD_Scene;
            $user["TN_Turn_P"] = $TN_Turn_P;
            $user["TN_P_Bet"] = $TN_P_Bet;
            $user["TN_P_Scene"] = $TN_P_Scene;
            $user["TN_Turn_PR"] = $TN_Turn_PR;
            $user["TN_PR_Bet"] = $TN_PR_Bet;
            $user["TN_PR_Scene"] = $TN_PR_Scene;
            $user["TN_Turn_P3"] = $TN_Turn_P3;
            $user["TN_P3_Bet"] = $TN_P3_Bet;
            $user["TN_P3_Scene"] = $TN_P3_Scene;                
            $user["VB_Turn_R"] = "VB_Turn_R_".$opentype;
            $user["VB_R_Bet"] = $VB_R_Bet;
            $user["VB_R_Scene"] = $VB_R_Scene;                
            $user["VB_Turn_OU"] = "VB_Turn_OU_".$opentype;
            $user["VB_OU_Bet"] = $VB_OU_Bet;
            $user["VB_OU_Scene"] = $VB_OU_Scene;                
            $user["VB_Turn_RE"] = "VB_Turn_RE_".$opentype;
            $user["VB_RE_Bet"] = $VB_RE_Bet;
            $user["VB_RE_Scene"] = $VB_RE_Scene;                
            $user["VB_Turn_ROU"] = "VB_Turn_ROU_".$opentype;
            $user["VB_ROU_Bet"] = $VB_ROU_Bet;
            $user["VB_ROU_Scene"] = $VB_ROU_Scene;                
            $user["VB_Turn_EO"] = "VB_Turn_EO_".$opentype;
            $user["VB_EO_Bet"] = $VB_EO_Bet;
            $user["VB_EO_Scene"] = $VB_EO_Scene;
            $user["VB_Turn_M"] = $VB_Turn_M;
            $user["VB_M_Bet"] = $VB_M_Bet;
            $user["VB_M_Scene"] = $VB_M_Scene;
            $user["VB_Turn_PD"] = $VB_Turn_PD;
            $user["VB_PD_Bet"] = $VB_PD_Bet;
            $user["VB_PD_Scene"] = $VB_PD_Scene;
            $user["VB_Turn_P"] = $VB_Turn_P;
            $user["VB_P_Bet"] = $VB_P_Bet;
            $user["VB_P_Scene"] = $VB_P_Scene;
            $user["VB_Turn_PR"] = $VB_Turn_PR;
            $user["VB_PR_Bet"] = $VB_PR_Bet;
            $user["VB_PR_Scene"] = $VB_PR_Scene;
            $user["VB_Turn_P3"] = $VB_Turn_P3;
            $user["VB_P3_Bet"] = $VB_P3_Bet;
            $user["VB_P3_Scene"] = $VB_P3_Scene;                
            $user["OP_Turn_R"] = "OP_Turn_R_".$opentype;
            $user["OP_R_Bet"] = $OP_R_Bet;
            $user["OP_R_Scene"] = $OP_R_Scene;                
            $user["OP_Turn_OU"] = "OP_Turn_OU_".$opentype;
            $user["OP_OU_Bet"] = $OP_OU_Bet;
            $user["OP_OU_Scene"] = $OP_OU_Scene;                
            $user["OP_Turn_VR"] = "OP_Turn_VR_".$opentype;
            $user["OP_VR_Bet"] = $OP_VR_Bet;
            $user["OP_VR_Scene"] = $OP_VR_Scene;                
            $user["OP_Turn_VOU"] = "OP_Turn_VOU_".$opentype;
            $user["OP_VOU_Bet"] = $OP_VOU_Bet;
            $user["OP_VOU_Scene"] = $OP_VOU_Scene;                
            $user["OP_Turn_RE"] = "OP_Turn_RE_".$opentype;
            $user["OP_RE_Bet"] = $OP_RE_Bet;
            $user["OP_RE_Scene"] = $OP_RE_Scene;                
            $user["OP_Turn_ROU"] = "OP_Turn_ROU_".$opentype;
            $user["OP_ROU_Bet"] = $OP_ROU_Bet;
            $user["OP_ROU_Scene"] = $OP_ROU_Scene;                
            $user["OP_Turn_VRE"] = "OP_Turn_VRE_".$opentype;
            $user["OP_VRE_Bet"] = $OP_VRE_Bet;
            $user["OP_VRE_Scene"] = $OP_VRE_Scene;                
            $user["OP_Turn_VROU"] = "OP_Turn_VROU_".$opentype;
            $user["OP_VROU_Bet"] = $OP_VROU_Bet;
            $user["OP_VROU_Scene"] = $OP_VROU_Scene;                
            $user["OP_Turn_EO"] = "OP_Turn_EO_".$opentype;
            $user["OP_EO_Bet"] = $OP_EO_Bet;
            $user["OP_EO_Scene"] = $OP_EO_Scene;
            $user["OP_Turn_M"] = $OP_Turn_M;
            $user["OP_M_Bet"] = $OP_M_Bet;
            $user["OP_M_Scene"] = $OP_M_Scene;
            $user["OP_Turn_PD"] = $OP_Turn_PD;
            $user["OP_PD_Bet"] = $OP_PD_Bet;
            $user["OP_PD_Scene"] = $OP_PD_Scene;
            $user["OP_Turn_T"] = $OP_Turn_T;
            $user["OP_T_Bet"] = $OP_T_Bet;
            $user["OP_T_Scene"] = $OP_T_Scene;
            $user["OP_Turn_F"] = $OP_Turn_F;
            $user["OP_F_Bet"] = $OP_F_Bet;
            $user["OP_F_Scene"] = $OP_F_Scene;
            $user["OP_Turn_P"] = $OP_Turn_P;
            $user["OP_P_Bet"] = $OP_P_Bet;
            $user["OP_P_Scene"] = $OP_P_Scene;
            $user["OP_Turn_PR"] = $OP_Turn_PR;
            $user["OP_PR_Bet"] = $OP_PR_Bet;
            $user["OP_PR_Scene"] = $OP_PR_Scene;
            $user["OP_Turn_P3"] = $OP_Turn_P3;
            $user["OP_P3_Bet"] = $OP_P3_Bet;
            $user["OP_P3_Scene"] = $OP_P3_Scene;                
            $user["FU_Turn_OU"] = "FU_Turn_OU_".$opentype;
            $user["FU_OU_Bet"] = $FU_OU_Bet;
            $user["FU_OU_Scene"] = $FU_OU_Scene;                
            $user["FU_Turn_EO"] = "FU_Turn_EO_".$opentype;
            $user["FU_EO_Bet"] = $FU_EO_Bet;
            $user["FU_EO_Scene"] = $FU_EO_Scene;
            $user["FU_Turn_PD"] = $FU_Turn_PD;
            $user["FU_PD_Bet"] = $FU_PD_Bet;
            $user["FU_PD_Scene"] = $FU_PD_Scene;
            $user["FS_Turn_FS"] = $FS_Turn_FS;
            $user["FS_FS_Bet"] = $FS_FS_Bet;
            $user["FS_FS_Scene"] = $FS_FS_Scene;
            $user["Bank_Address"] = $bankname ?? "";
            $user["Bank_Account"] = $bankno ?? "";
            $user["E_Mail"] = $e_mail ?? "";
            $user["question"] = $question ?? "";
            $user["answer"] = $answer ?? "";

            $user->save();
            
            $response['data'] = $user;
            $response['message'] = 'User registerd successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function login( Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        $rules = [
            'UserName' => 'required|string',
            'password' => 'required|min:6',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorResponse = validation_error_response($validator->errors()->toArray());
            return response()->json($errorResponse, $response['status']);
        }

        try {
        
            $credentials = $request->only(["UserName","password"]);

            $user = User::where('UserName',$credentials['UserName'])->first();

            if (!isset($user)) {
                $response['message'] = 'Please enter valid registered UserName.';
                return response()->json($response, $response['status']);
            }

            if(!auth()->attempt($credentials)) {
                if (intval($user['ErrorTimes']) >= 8) {
                    $response['message'] = 'Due to too many wrong passwords, your account has been locked!';
                    return response()->json($response, $response['status']);
                }

                $datetime = date("Y-m-d H:i:s");

                $new_data = [
                    "UserName" => $credentials['UserName'],
                    "Status" => 0,
                    "LoginIP" => Utils::get_ip(),
                    "DateTime" => $datetime,
                    "Contect" => '登录失败(pass:'.$credentials['password'].')',
                    "Url" => Utils::get_browser_ip()
                ];

                $web_member_logs = new WebMemberLogs;

                $web_member_logs->create($new_data);

                User::where('UserName',$credentials['UserName'])
                    ->update(["ErrorTimes" => (int)$user["ErrorTimes"] + 1]);

                $response['message'] = 'Incorrect name or password';
                return response()->json($response, $response['status']);
            }

            $accessToken = auth()->user()->createToken('authToken')->accessToken;

            $user->access_token = $accessToken;

            $str = time();

            $uid=strtolower(substr(md5($str),0,10).substr(md5($credentials['UserName']),0,10).'ra'.rand(0,9));
            $ip_addr=Utils::get_ip();
            $browser_ip = Utils::get_browser_ip();
            $date=date("Y-m-d");
            $datetime=date("Y-m-d H:i:s");
            $MachineCode=substr(strtoupper(md5('newhg'.$str.mt_rand(1,9999))),8,20);

            User::where('UserName',$credentials['UserName'])
                ->where("Status", "<=", 1)
                ->update([
                    "Oid" => $uid,
                    "MachineCode" => $MachineCode,
                    "ErrorTimes" => 0,
                    "LoginDate" => $date,
                    "LoginTime" => now(),
                    "OnlineTime" => now(),
                    "Online" => 1,
                    "LoginIP" => $ip_addr,                    
                    "Url" => $browser_ip,
                ]);

            $new_data = [
                "UserName" => $credentials['UserName'],
                "Status" => 1,
                "LoginIP" => Utils::get_ip(),
                "DateTime" => $datetime,
                "Contect" => '登录成功',
                "Url" => Utils::get_browser_ip()
            ];

            $web_member_logs = new WebMemberLogs;
                          
            $web_member_logs->create($new_data);

            $response['message'] = "Login successfully";
            $response['data'] = $user;
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());

            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function viewProfile() {

        $response = [];
        $response['success'] = FALSE;

        try {
            $user = Auth::guard("api")->user();
            $user = User::where("UserName", $user["UserName"])->first();
            $response['data'] = $user;
            $response['message'] = 'Profile detail fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function logout(){
        
        $response = [];
        $response['success'] = FALSE;

        try {        
            $user = Auth::guard("api")->user()->token();
            $user->revoke();
            $response['message'] = 'Logout successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
}
