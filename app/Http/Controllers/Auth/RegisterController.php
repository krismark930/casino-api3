<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'UserName' => ['required', 'string', 'max:255'],
            'LoginName' => ['required', 'string', 'max:255'],
            'E_Mail' => ['required', 'string', 'email', 'max:255', 'unique:web_member_data'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'Bank_Account' => ['required', 'string', 'max:255'],
            'Phone' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'UserName' => $data['UserName'],
            'LoginName' => $data['LoginName'],
            'E_Mail' => $data['E_Mail'],
            'PassWord' => Hash::make($data['password']),
            'Bank_Account' => $data['Bank_Account'],
            'Phone' => $data['Phone'],
        ]);
    }
}
