<?php
namespace App\Http\Controllers\Admin\SystemSetting;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\SysConfig;

class SystemParametersController extends Controller {
    /* Set pc, mobile urls */
    public function set_urls(Request $request) {
        $pcUrl = $request->input('pcurl');
        $mobileUrl = $request->input('wapurl');

        $validator = Validator::make($request->all(), [
            'pcurl' => 'required',
            'wapurl' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['success' => false, 'message' => $validator->messages()->toArray()]);

        DB::table('config')->where('id', 1)->update(['PCURL' => $pcUrl, 'WAPURL' => $mobileUrl]);

        return $this->respondData([]);
    }

    /* Get pc, mobile urls */
    public function get_urls(Request $request) {
        $config = Config::selectRaw('*')->first();
        return $this->respondData(['pcurl' => $config['PCURL'], 'wapurl' => $config['WAPURL']]);
    }
    /* Set Reg_Status, AG_Repair, OG_Repair, BBIN_Repair, MG_Repair, PT_Repair, KY_Repair */
    public function set_turnservices(Request $request) {
        $isReg = $request->input('isReg');
        $AG_Repair = $request->input('AG_Repair');
        $OG_Repair = $request->input('OG_Repair');
        $BBIN_Repair = $request->input('BBIN_Repair');
        $MG_Repair = $request->input('MG_Repair');
        $PT_Repair = $request->input('PT_Repair');
        $KY_Repair = $request->input('KY_Repair');

        $validator = Validator::make($request->all(), [
            'isReg' => 'required',
            'AG_Repair' => 'required',
            'OG_Repair' => 'required',
            'BBIN_Repair' => 'required',
            'MG_Repair' => 'required',
            'PT_Repair' => 'required',
            'KY_Repair' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['success' => false, 'message' => $validator->messages()->toArray()]);

        DB::table('sys_config')->where('id', 1)->update(['isReg' => $isReg, 'AG_Repair' => $AG_Repair,
        'OG_Repair' => $OG_Repair,'BBIN_Repair' => $BBIN_Repair,'MG_Repair' => $MG_Repair,'PT_Repair' => $PT_Repair,'KY_Repair' => $KY_Repair]);

        return $this->respondData([]);
    }
    //get turn on/off services
    public function get_turnservices(Request $request) {
        $sys_config = SysConfig::selectRaw('*')->first();
        return $this->respondData(['isReg' => $sys_config['isReg'], 'AG_Repair' => $sys_config['AG_Repair'],
        'OG_Repair' => $sys_config['OG_Repair'], 'BBIN_Repair' => $sys_config['BBIN_Repair'], 'MG_Repair' => $sys_config['MG_Repair'],
        'PT_Repair' => $sys_config['PT_Repair'] , 'KY_Repair' => $sys_config['KY_Repair']]);
    }
}
