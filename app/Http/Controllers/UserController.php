<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getProfile(Request $request)
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'User profiel fetched successfully',
                'data' => Auth::user()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255'
            ]);

            $user = Auth::user();
            $user->update([
                'name' => $request->name ?? $user->name,
                'phone' => $request->phone ?? $user->phone,
                'address' => $request->address ?? $user->address
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Profile Updated Successfully',
                'data' => $user
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
