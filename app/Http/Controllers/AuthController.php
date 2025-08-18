<?php

namespace App\Http\Controllers;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    protected $otpservice;

    public function __construct(OtpService $otpservice) {
        $this->otpservice = $otpservice;
    }
    public function sendOtp(Request $request) {

        try {
            $request->validate([ 'email'=>'required|email']);
        $otp = $this->otpservice->generateOtp( $request->email );
        Mail::to($request->email)->send(new OtpMail($otp));


        return response()->json(['message'=>'OTP send successfully','otp' => $otp]);
        } catch (\Exception $th) {
            return response()->json(['message'=> $th->getMessage()]);
        }

    }

    public function verifyOtp(Request $request) {
        $request->validate(['email'=> 'required|email','otp'=> 'required']);

        if($this->otpservice->verifyOtp($request->email, $request->otp )) {
            return response()->json(['message'=> 'OTP verified'],200);
        }
        return response()->json(['message'=> 'Invalid OTP'],422);
}
}