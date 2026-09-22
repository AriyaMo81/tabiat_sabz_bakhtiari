<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'cellphone' => ['required', 'regex:/^09[0-9]{9}$/']
        ]);

        try {
            $cellphone = $request->cellphone;

            // شماره مجاز پلن Free
            $allowedCellphone = env('GHASEDAK_ALLOWED_CELLPHONE');

            if ($cellphone !== $allowedCellphone) {
                return response()->json([
                    'message' => 'در پلن Free قاصدک فقط امکان ارسال کد به شماره مجاز وجود دارد.'
                ], 403);
            }

            // پیدا کردن کاربر
            $user = User::where('cellphone', $cellphone)->first();

            // تولید کد 5 رقمی
            $otpCode = random_int(10000, 99999);

            // تولید توکن
            $loginToken = Hash::make(
                'FBScslddsh$&clsdc*dcncd@DCD'
            );

            // ارسال OTP توسط helper
            sendOtpSms($cellphone, $otpCode);

            // ذخیره اطلاعات
            if ($user) {
                $user->update([
                    'otp' => $otpCode,
                    'login_token' => $loginToken,
                ]);
            } else {
                $user = User::create([
                    'cellphone' => $cellphone,
                    'otp' => $otpCode,
                    'login_token' => $loginToken,
                ]);
            }

            return response()->json([
                'message' => 'کد تایید ارسال شد.',
                'login_token' => $loginToken,
            ], 200);
        } catch (\Throwable $ex) {
            return response()->json([
                'message' => 'خطایی در ارسال کد تایید رخ داد.',
                'errors' => $ex->getMessage(),
            ], 500);
        }
    }





    public function checkOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:5'],
            'login_token' => ['required'],
        ]);

        try {

            $user = User::where(
                'login_token',
                $request->login_token
            )->first();

            if (!$user) {
                return response()->json([
                    'message' => 'اطلاعات ورود نامعتبر است.'
                ], 401);
            }

            if ((string) $user->otp !== (string) $request->otp) {
                return response()->json([
                    'message' => 'کد ورود نادرست است.'
                ], 422);
            }

            // ورود کاربر با گارد web
            Auth::guard('web')->login($user, true);

            // ساخت مجدد session
            $request->session()->regenerate();

            // حذف OTP و login token
            $user->update([
                'otp' => null,
                'login_token' => null,
            ]);

            return response()->json([
                'message' => 'ورود با موفقیت انجام شد.'
            ], 200);
        } catch (\Throwable $ex) {

            return response()->json([
                'message' => 'خطایی در بررسی کد ورود رخ داد.',
                'errors' => $ex->getMessage(),
            ], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'login_token' => 'required'
        ]);

        try {

            // پیدا کردن کاربر با login_token فعلی
            $user = User::where(
                'login_token',
                $request->login_token
            )->firstOrFail();

            // تولید OTP جدید 5 رقمی
            $otpCode = random_int(10000, 99999);

            // تولید login token جدید
            $loginToken = Hash::make(
                'FBScslddsh$&clsdc*dcncd@DCD'
            );

            // ذخیره OTP و login token جدید
            $user->update([
                'otp' => $otpCode,
                'login_token' => $loginToken
            ]);

            // ارسال SMS جدید
            sendOtpSms(
                $user->cellphone,
                $otpCode
            );

            return response()->json([
                'message' => 'کد تایید جدید ارسال شد.',
                'login_token' => $loginToken
            ], 200);
        } catch (\Exception $ex) {

            return response()->json([
                'message' => 'ارسال مجدد کد با خطا مواجه شد.',
                'errors' => $ex->getMessage()
            ], 500);
        }
    }
}
