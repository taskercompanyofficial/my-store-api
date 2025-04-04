<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleLoginController extends Controller
{
    /**
     * Handle Google login request
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'googleId' => 'required|string',
            'image' => 'nullable|string',
        ]);

        // Find existing user or create a new one
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Create new user if not exists
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(Str::random(16)),
                'google_id' => $request->googleId,
                'image' => $request->image,
            ]);
        } else {
            if (empty($user->google_id)) {
                $user->google_id = $request->googleId;
                $user->save();
            }
        }
        // Generate token for the user
        $token = $user->createToken('google-auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Google login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'token' => $token,
                'role' => $user->role ?? 'user',
                'image' => $user->profile_image ?? '',
            ]
        ], 200);
    }
}
