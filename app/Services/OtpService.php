<?php

namespace App\Services;

use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OtpService
{
    public function generateOtp(string $email, int $length = 6, int $expiryMinutes = 5): string
    {
        $otp = mt_rand(100000, 999999);
        $expireAt = Carbon::now()->addMinutes($expiryMinutes);

        Otp::create([
            'email' => $email,
            'otp' => $otp,
            'expiry_at' => $expireAt
        ]);

        return $otp;
    }

    public function verifyOtp(string $email, int $otp): bool
    {
        $record = Otp::where('email', $email)->where('otp', $otp)->where('expire_at', '>', Carbon::now())->first();
        if ($record) {
            $record->delete();
            return true;
        }
        return false;
    }

    public function cleanupExpired()
    {
        Otp::where('expire_at', '<', Carbon::now())->delete();
    }
}