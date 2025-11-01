<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EmailVerifiedNotification;
use App\Traits\OTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SignUpController extends Controller
{
    use OTP;

    public function signUp(Request $request)
    {
        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required'],
                'type' => ['required', 'in:user,provider'],
                'password' => ['required', 'min:8'],
                'confirm_password' => ['required', 'same:password'],
                'location' => ['required_if:type,provider', 'string'],
            ],
            [
                'location.required_if' => 'Location is required when signing up as a provider.',
            ]
        );

        if ($validatedData->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validatedData->errors()->first(),
            ], 400);
        }

        $data = $validatedData->validated();
        $data['password'] = Hash::make($data['password']);

        $user = null;
        DB::transaction(function () use ($data, &$user) {
            $user = User::create($data);
            $this->generateAndSendOtp($user);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Account created successfully',
        ], 200);
    }


    public function changeEmail(Request $request)
    {
        $validated = Validator::make($request->all(), [
            "old_email" => 'required|email',
            "new_email" => 'required|email|unique:users,email',
        ]);

        if ($validated->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validated->errors()->first()
            ], 400);
        }

        $user = User::where('email', $request->old_email)->first();


        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "User not found With that email"
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                "status" => "error",
                "message" => "Email already verified, can't change email"
            ], 401);
        }

        $user->update([
            "email" => $request->new_email,
        ]);

        $this->generateAndSendOtp($user);

        return response()->json([
            "status" => "success",
            "message" => "Email Changed OTP Sent",
            "data" => $user
        ], 200);
    }

    public function verifyEmail(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'otp' => 'required',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validated->errors()->first(),
            ], 422);
        }

        $otpResult = $this->verify_otp($request->otp);

        if (!$otpResult['status']) {
            return response()->json([
                'status' => 'error',
                'message' => $otpResult['error'],
            ], 400);
        }

        $user = $otpResult['user'];

        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Email verified successfully.',
            'data' => $user,
        ]);
    }
}
