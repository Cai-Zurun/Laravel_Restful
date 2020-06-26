<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ResetPasswordController extends Controller
{
    public function SendEmail(Request $request){
        $email = $request->input('email');
        $verification_code = random_int(100000,999999);

        Redis::set($verification_code,$email,'EX',60);

        Mail::raw(
            '重置密码的验证码：'.$verification_code
            ,function($message) use($email){
            $message->from('1042503039@qq.com','zurun')
                ->subject('重置密码邮件')
                ->to($email);
        });

        return response()->json([
            'success' => true,
            'message' => 'send successfully',
        ], 200);
    }


    public function ResetPassword(Request $request){
        $password = $request->input('password');
        $email = $request->input('email');
        $verification_code = $request->input('verification_code');

        $email_exactly = Redis::get($verification_code);
        if($email!=$email_exactly){
            return response()->json([
                'success' => false,
                'message' => 'wrong verification_code'
            ], 401);
        }

        $result = DB::table('users')->where('email',$email)->update(['password'=>$password]);
        if(!$result){
            return response()->json([
                'success' => false,
                'message' => 'Reset password unsuccessfully'
            ], 401);
        }
        return response()->json([
            'success' => true,
            'message' => 'Reset password successfully'
        ], 200);
    }
}
