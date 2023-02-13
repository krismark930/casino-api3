<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    public function register(Request $request){
        $validate=Validator::make($request->all(), [
            'UserName' => ['required', 'max:70'],
            'PassWord'  => ['required', 'max:70'],
            'E_Mail' => ['required'],
        ],[
            'UserName.required' => 'Username is must.',
            'PassWord.required' => 'Password is must.',
            'E_Mail.required' => 'E_Mail is must.',
        ]);
        // return $request->all();
        if($validate->fails()){
             return $validate->errors();
        }
        $value=$request->all();
        $user=UserModel::create([
            'UserName'=>$value['UserName'],
            'PassWord'=>$value['PassWord'],
            'E_Mail'=>$value['E_Mail'],
        ]);
        return $request->all();
    }
}
