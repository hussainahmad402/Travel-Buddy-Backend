<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getProfile(Request $request){
        try {
            return response()->json([
            'status'=> true,
            'message'=>'User profiel fetched successfully',
            'data'=> Auth::user()
        ],200);
        } catch (Exception $e) {
            return response()->json([
                'status'=> false,
                'message'=> $e->getMessage()
                ],500);
        }
    }
}
