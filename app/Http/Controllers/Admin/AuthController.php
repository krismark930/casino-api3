<?php
namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Admin\User;

class AuthController extends Controller {


    /**
    * @OA\Post(
    * path="/api/admin/login",
    * operationId="adminLogin",
    * tags={"Admin Login"},
    * summary="Admin Login",
    * description="This is Admin Login API",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"username", "password"},
    *               @OA\Property(property="username", type="username"),
    *               @OA\Property(property="password", type="password")
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=201,
    *          description="Login Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Login Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
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

    /**
     * @OA\Post(
     ** path="/api/admin/register",
     *   tags={"Admin Register"},
     *   summary="Admin Register",
     *   operationId="adminRegister",
     *   description="This is Admin Register API",
     *   @OA\Parameter(
     *      name="username",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/
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
