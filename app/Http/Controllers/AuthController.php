<?php

namespace App\Http\Controllers;
use App\Services\OtpService;
use Exception;
use Illuminate\Http\Request;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// use App\Http\Controllers\Log;

class AuthController extends Controller
{
    protected $otpservice;

    public function __construct(OtpService $otpservice)
    {
        $this->otpservice = $otpservice;
    }

    public function register(Request $request)
    {
        try {
            $request->validate(['name' => 'required|string|max:255', 'email' => 'required|string|email|unique:users,email', 'password' => 'required|string|min:6|confirmed']);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $otp = $this->otpservice->generateOtp($user->email);
            Mail::to($user->email)->send(new OtpMail($otp));

            return response()->json([
                'status' => true,
                'message' => 'User register successfully, OTP send to you email',
                'data' => $user
            ]);
        } catch (Exception $error) {
            return response()->json(['status' => 'failed', 'message' => $error->getMessage()]);
        }
    }






    public function sendOtp(Request $request)
    {

        try {
            $request->validate(['email' => 'required|email']);
            $otp = $this->otpservice->generateOtp($request->email);
            Mail::to($request->email)->send(new OtpMail($otp));


            return response()->json([
                'status'=> true,
                'message' => 'OTP send successfully', 'otp' => $otp]);
        } catch (\Exception $th) {
            return response()->json([
                'status'=>false,
                'message' => $th->getMessage()]);
        }

    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required']);
        $user = User::where('email', $request->email)->first();

        if ($this->otpservice->verifyOtp($request->email, $request->otp)) {
            $user = User::where('email', $request->email)->first();
            $user->email_verified_at = now();
            $user->email_verified = true;
            $user->save();
            return response()->json([
                'status'=> true,
                'message' => 'OTP verified successfully , email confirmed'], 200);
        }
        return response()->json([
            'status'=>false,
            'message' => 'Invalid OTP'], 422);
    }

    public function login(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email', 'password' => 'required|string|min:6']);
            $user = User::where('email', $request->email)->first();

            if (!$token = auth()->attempt($request->only('email', 'password'))) {
                return response()->json(data: ['error' => 'Invalid email or password'], status: 401);
            }
            $user = auth()->user();
            if (is_null($user->email_verified_at)) {
                return response()->json(['error' => 'Please verify your email first.'], 403);
            }
        } catch (Exception $e) {
            response()->json(['message' => 'Login Failed', 'error' => $e], 200);

        }

        return response()->json([
            'status'=> true,
            'message' => 'Login Success', 'token' => $token, 'user' => auth()->user()], 200);
    }

    public function forgotpassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                // 'name'=>'required|string'
            ]);
            // $user = User::where('email', $request->email)->first();
            $user = User::where('email', $request->email)->first();
            // if ($user) {
            //     $email = $user->email;
            //     return response()->json([
            //         'email' => $user->email,
            //     ]);
            // }

            if (!$user) {
                // Log::warning('User not found: ' . $request->email);
                return response()->json(['message' => 'User not found'], 404);
            }

            $otp = $this->otpservice->generateOtp($user->email);
            Mail::to($user->email)->send(new OtpMail($otp));

            Mail::to($user->email)->send(new OtpMail($otp));

            return response()->json([
                'status' => true,
                'message' => 'OTP has been send to your email'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'OTP not send you the mail']);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|int',
                'email' => 'required|email|exists:users,email',
                'new_password' => 'required|string|min:6',
            ]);

            $user = User::where('email', $request->email)->first();
            // verify the otp 
           if ( !$this->otpservice->verifyOtp($user->email, $request->otp)) {
            
            return response()->json(['message'=> 'Invalid OTP'], 404);
           }

            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'Password updated Successfully'
            ]);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage()
            ]);
        }
        ;

    }
}