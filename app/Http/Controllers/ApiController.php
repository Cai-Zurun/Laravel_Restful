<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAuthRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use Psy\Util\Str;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiController extends Controller
{
    public $loginAfterSignUp = false;

    public function register(RegisterAuthRequest $request)
    //RegisterAuthRequest是我们自定义的接受表单请求的文件，里面有我们自定义的规则，laravel默认是用Request
    {
        $user = new User(); //User其实是我们规定的model
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->activation_token = \Str::random(60);    //Todo: 为什么需要\,而不能直接 Str::
        $user->is_active = 0;
        $user->save();

        Mail::raw(
            '请点击链接激活您的账号'.route('user.activation',['activation_token'=>$user->activation_token])
            ,function($message) use($user){
            $message->from('1042503039@qq.com','zurun')
                ->subject('注册激活邮件')
                ->to($user->email);
        });

        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
//        为什么返回时没有password信息，是因为model:User中规定了hidden字段，其中包括了password
    }

    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $is_active = DB::table('users')->where('email',$request->input('email'))->value('is_active');
        $jwt_token = null;

        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }

        if (!$is_active){
            return response()->json([
                'success' => false,
                'message' => 'Account inactive',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

    public function getAuthUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

    public function activation($activation_token)
    {
        DB::table('users')->where('activation_token',$activation_token)->update(['is_active'=>1]);
        return '激活成功';
    }

}
