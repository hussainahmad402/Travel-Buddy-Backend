<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function getProfile(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        $userData = $user->toArray();

        // Replace localhost with your PC's LAN IP
        $pcIp = '192.168.1.107'; // <-- your PC LAN IP here

        if ($user->profile_picture) {
            $path = $user->profile_picture;
            $userData['profile_picture_url'] = "http://{$pcIp}/storage/{$path}";
        } else {
            $userData['profile_picture_url'] = null;
        }

        return response()->json([
            'status' => true,
            'message' => 'User profile fetched successfully',
            'data' => $userData
        ], 200);

    } catch (\Exception $e) {
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
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');

            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $image->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        if ($request->filled('first_name')) $user->first_name = $request->first_name;
        if ($request->filled('last_name')) $user->last_name = $request->last_name;
        if ($request->filled('phone')) $user->phone = $request->phone;
        if ($request->filled('address')) $user->address = $request->address;

        if (Schema::hasColumn('users', 'name')) {
            $user->name = trim(implode(' ', array_filter([$user->first_name, $user->last_name])));
        }

        $user->save();
        $user->refresh();

        $pcIp = '192.168.1.107'; // <-- your PC LAN IP
        $userData = $user->toArray();
        $userData['profile_picture_url'] = $user->profile_picture
            ? "http://{$pcIp}/storage/{$user->profile_picture}"
            : null;

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $userData
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    
    public function deleteProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $user->delete();
            return response()->json([
                'status' => true
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function getAllUsers(Request $request)
{
    try {
        $currentUser = $request->user(); // authenticated user

        // Get all users except the current one
        $users = \App\Models\User::select('id', 'name', 'email')
            ->where('id', '!=', $currentUser->id)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Users fetched successfully',
            'users' => $users
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error fetching users: ' . $e->getMessage()
        ], 500);
    }
}


}
