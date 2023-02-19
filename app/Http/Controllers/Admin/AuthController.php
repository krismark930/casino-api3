<?php
namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Admin\User;

class AuthController extends Controller {
    /* Login action. */
    public function login(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50',
            'password' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['success'=>false, 'message'=>$validator->messages()->toArray()]);

        if (!auth()->guard('admin')->attempt(compact('username', 'password'))) {
            auth()->guard('admin')->logout();
            return response()->json(['success'=>false, 'message'=>'Your credentials are incorrect.']);
        }

        $accessToken = auth()->guard('admin')->user()->createToken('adminToken')->accessToken;
        return $this->respondWithToken($accessToken, 'Login successfully.', auth()->guard('admin')->user());
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
