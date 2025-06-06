<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    function RegistrationPage(){
        return view('pages.auth.registration-page');
    }

    function LoginPage(){
        return view('pages.auth.login-page');
    }

    function SendOTPPage(){
        return view('pages.auth.send-otp-page');
    }

    function VerifyOtpPage(){
        return view('pages.auth.verify-otp-page');
    }

    function ResetPasswordPage(){
        return view('pages.auth.registration-page');
    }

    function UserRegistration(Request $request)
    {
        try {
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password'),
            ]);
            return response()->json([
                'status' => 'Success',
                'message' => 'User Registration Successful'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Failed',
                'message' => $e->getMessage()
            ]);
        }
    }

    function UserLogin(Request $request)
    {
        $count = User::where('email', '=', $request->input('email'))
            ->where('password', '=', $request->input('password'))
            ->count();

        if ($count == 1) {
            $token = JWTToken::CreateToken($request->input('email'));
            return response()->json([
                'status' => 'Success',
                'message' => 'Login Successfully',
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Unauthorized Access'
            ], 401);
        }
    }

    function SendOTPCode(Request $request)
    {
        $email = $request->input('email');
        $otp = rand(1000, 9999);
        $count = User::where('email', '=', $email)->count();

        if ($count == 1) {
            Mail::to($email)->send(new OTPMail($otp));
            User::where('email', '=', $email)->update(['otp' => $otp]);

            return response()->json([
                'status' => 'Success',
                'message' => '4 Digit OTP code sent to your email'
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Unauthorized Access'
            ], 401);
        }
    }

    function VerifyOTPCode(Request $request)
    {
        $email = $request->input('email');
        $otp = $request->input('otp');
        $count = User::where('email', '=', $email)
            ->where('otp', '=', $otp)
            ->count();

        if ($count == 1) {
            // Otp code update on db
            User::where('email', '=', $email)->update(['otp' => '0']);

            // Password reset token generate
            $token = JWTToken::CreateTokenForResetPass($request->input('email'));
            return response()->json([
                'status' => 'Success',
                'message' => 'OTP verification success',
                'token' => $token
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'OTP verification failed'
            ]);
        }
    }

    function ResetPassword(Request $request)
    {
        try {
            $email = $request->header('email');
            $password = $request->input('password');
            User::where('email', '=', $email)->update(['password' => $password]);

            return response()->json([
                'status' => 'Success',
                'message' => 'Password Reset Successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ]);
        }
    }
}
