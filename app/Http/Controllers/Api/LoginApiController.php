<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
class LoginApiController extends Controller
{
     public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid login details'
            ], 401);
        }

        /** @var Account $account */
        $account = Auth::user(); // returns logged-in Account

        // Generate Sanctum token
        $token = $account->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => 'success',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'         => $account->id,
                'first_name' => $account->first_name,
                'last_name'  => $account->last_name,
                'email'      => $account->email,
                'role'       => $account->role,
                'status'     => $account->status,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // revoke only the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully'
        ]);
    }
}
