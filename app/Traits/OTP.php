<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\OTPSentNotification;
use App\Notifications\signUp\OtpToUser;
use Illuminate\Support\Facades\Mail;

trait OTP
{
    public function generateAndSendOtp($user)
    {
        $otp = rand(1000, 9999);
        $expire = now()->addMinutes(5);

        $user->update([
            "verification_code" => $otp,
            "verification_code_expires_at" => $expire,
        ]);

        $notificationData = [
            "otp" => $otp,
            "user" => $user->first_name,
        ];

        Mail::send('email-templates.otp-email', ['data' => $notificationData], function ($message) use ($user) {
            $message->to($user->email)
                ->subject(env("APP_NAME") . " - OTP");
        });


        return [
            "otp" => $user->verification_code,
            "expire" => $user->verification_code_expires_at
        ];
    }

    // App/Traits/OTP.php
    public function verify_otp($otp): array
    {
        $user = User::where("verification_code", $otp)->first();

        if (!$user) {
            return [
                'status' => false,
                'error' => 'Invalid OTP',
            ];
        }

        if ($user->verification_code_expires_at < now()) {
            return [
                'status' => false,
                'error' => 'OTP Expired',
            ];
        }

        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        return [
            'status' => true,
            'user' => $user,
        ];
    }
}
