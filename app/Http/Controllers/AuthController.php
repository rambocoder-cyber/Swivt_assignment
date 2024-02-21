<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class AuthController extends Controller
{
    public function normalLogin(Request $request){
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Call Operator Authenticate Integration API
        $operatorResponse = Http::timeout(60)->post(config('services.operator.api_url') . '/authenticate', [
            'username' => $request->username,
            'password' => $request->password,
        ]);

        // Check if the API request was successful
        if ($operatorResponse->successful()) {
            $operatorData = $operatorResponse->json();

            // Validate the response from the Operator API
            if ($operatorData['Status'] === 0) {
                // Authenticate the user in your system
                $user = User::where('username', $request->username)->first();

                if ($user) {
                    // Log the user in and return the token
                    auth()->login($user);

                    return response()->json([
                        'Token' => $operatorData['Token'],
                        'Balance' => $operatorData['Balance'],
                        'Message' => 'Success',
                        'Status' => 0,
                    ]);
                }
            }
        }

        // If authentication fails
        return response()->json([
            'Message' => 'Invalid credentials',
            'Status' => 7,
        ]);
           
    }

    public function signOut(Request $request){
        $request->validate([
            'AppID' => 'required|string',
            'Username' => 'required|string',
            'Hash' => 'required|string',
            'Timestamp' => 'required|numeric',
        ]);

        // Validate the hash
        if (!verifyHash($request->all())) {
            return response()->json(['Message' => 'Invalid hash code', 'Status' => 401]);
        }

        $user = User::where('username', $request->Username)->first();
        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json([
            'Description' => 'Sign-out successful',
            'Error' => 0,
        ]);
    }
}
