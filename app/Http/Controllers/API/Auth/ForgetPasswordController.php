<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\OTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgetPasswordController extends Controller
{
    use OTP;

    public function forget_password(Request $request)
    {

        $validated = Validator::make($request->all(), [
            "email" => "required|email"
        ]);

        if ($validated->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid request."
            ], 422);
        }

        $user = User::where("email", $request->email)->first();

        if (!$user || $user->deleted_at) {
            return response()->json([
                "status" => "error",
                "message" => "User not found"
            ], 404);
        }

        $this->generateAndSendOtp($user);


        return response()->json([
            "status" => "success",
            "message" => "OTP sent to your email"
        ]);
    }


    public function otp_verification(Request $request)
    {
        $validated = Validator::make($request->all(), [
            "email" => 'required|email|exists:users,email',
            "otp" => "required|string",
        ]);

        if ($validated->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid request."
            ], 422);
        }

        $otpResult = $this->verify_otp($request->otp);

        if (!$otpResult['status']) {
            return response()->json([
                'status' => 'error',
                'message' => "Invalid OTP.",
            ], 400);
        }

        $user = $otpResult['user'];

        $resetToken = bin2hex(random_bytes(32));

        Cache::put("reset_token:{$resetToken}", $user->email, now()->addMinutes(15));

        return response()->json([
            "status" => "success",
            "message" => "OTP verified successfully.",
            "reset_token" => $resetToken
        ]);
    }


    public function reset_password(Request $request)
    {
        $validated = Validator::make($request->all(), [
            "reset_token" => 'required|string|size:64',
            "email" => 'required|email|exists:users,email',
            "password" => 'required|string|min:8',
            "confirm_password" => 'required|string|same:password',
        ]);

        if ($validated->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validated->errors()->first()
            ], 422);
        }

        $resetToken = $request->reset_token;
        $emailFromCache = Cache::get("reset_token:{$resetToken}");

        if (!$emailFromCache || $emailFromCache !== $request->email) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid or expired reset token."
            ], 403);
        }

        Cache::forget("reset_token:{$resetToken}");

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "User not found."
            ], 404);
        }

        $user->update([
            "password" => Hash::make($request->password)
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Password has been reset successfully."
        ], 200);
    }
}
