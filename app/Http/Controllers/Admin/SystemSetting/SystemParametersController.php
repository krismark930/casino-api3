<?php
namespace App\Http\Controllers\Admin\SystemSetting;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Config;

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
}
