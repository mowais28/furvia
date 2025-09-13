<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Traits\OTP;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticateController extends Controller
{
    use OTP;

    private function userAuthResponse($user)
    {
        $token = $user->createToken('api-authentication')->accessToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ];
    }


    private function captureFcmToken($user, $fcm_token)
    {
        $user->fcm_token = $fcm_token;
        $user->save();
    }

    public function login(Request $request)
    {

        $validated = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required"
        ]);

        if ($validated->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validated->errors()->first()
            ], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if (!$user->email_verified_at) {

                $this->generateAndSendOtp($user);

                return response()->json([
                    "message" => "Verification Code has been sent to your email",
                    "status" => "unverified",
                ], 200);
            }

            if ($user->status == "blocked") {
                return response([
                    "message" => "Your account is blocked"
                ], 401);
            }


            if (isset($request->fcm_token)) {
                $this->captureFcmToken($user, $request->fcm_token);
            };
        } else {
            return response([
                "message" => "Invalid credentials"
            ], 401);
        }

        return response([
            "data" => $this->userAuthResponse($user),
            "message" => "login Successfull"
        ], 200);
    }

    public function logout(Request $request)
    {
        try {
            // Revoke Token
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $user->fcm_token = null;
            $user->save();

            $res = $user->token()->revoke();


            if ($res == null) {
                throw new \ErrorException('Something went wrong');
            }

            $response = [
                'status' => 200,
                'message' => "Successfully logged out",
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 422,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function change_password(Request $request)
    {
        $validated = Validator::make($request->all(), [
            "old_password" => "required",
            "new_password" => "required|min:8",
            "confirm_password" => "required|same:new_password",
        ]);

        if ($validated->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validated->errors()->first()
            ], 400);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "User not authenticated."
            ], 401);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                "status" => "error",
                "message" => "Old password is incorrect."
            ], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            "status" => "success",
            "message" => "Password changed successfully."
        ]);
    }
}
