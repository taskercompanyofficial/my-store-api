<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email|string',
            'password' => 'required|min:8',
        ]);

        $staff = null;
        if ($request->email) {
            $staff = User::where('email', $request->email)->first();
        } else if ($request->phone) {
            $staff = User::where('phone', $request->phone)->first();
        }

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email|string',
            'password' => 'required|min:8',
        ]);

        $staff = null;
        if ($request->email) {
            $staff = User::where('email', $request->email)->first();
        } else if ($request->phone) {
            $staff = User::where('phone', $request->phone)->first();
        }

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $staff->createToken('auth_token')->plainTextToken;
        $user = $staff->only(['id', 'name', 'email', 'phone', 'role', 'status']);
        $user['token'] = $token;
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $user,
        ]);
    }
}
