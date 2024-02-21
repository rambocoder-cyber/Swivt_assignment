<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\CancelBet;
use App\Models\Game;
use App\Models\Round;
use App\Models\SettleBet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class OperatorIntegrationController extends Controller
{
    // Authenticate Username/Password
    public function authenticate(Request $request)
    {
        $request_data = $request->all();

        $request->validate([
            'hash' => 'required|string',
            'password' => 'required|string',
            'username' => 'required|string',
        ]);

        // Verify hash and authenticate user
        if (!verifyHash($request_data)) {
            return response()->json(['message' => 'Invalid', 'status' => 401]);
        }

        // Validate user with username and password
        $user = User::where('username', $request_data['username'])->first();
        if (!$user || !Hash::check($request_data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials', 'status' => 401]);
        }

        // Generate token using Laravel Passport
        $token = $user->createToken('token')->accessToken;
        $balance = $user->balance;

        return response()->json([
            'Token' => $token,
            'Balance' => $balance,
            'Message' => 'Success',
            'Status' => 0,
        ]);
    }

    public function getBalance(Request $request)
    {
        $request_data = $request->all();
        $request->validate([
            'appid' => 'required|string',
            'hash' => 'required|string',
            'username' => 'required|string',
        ]);

        // Verify hash
        if (!verifyHash($request_data)) {
            return response()->json(['Message' => 'Invalid, hash code does not match', 'Status' => 401]);
        }

        // Get the user and retrieve balance
        $user = User::where('username', $request_data['username'])->first();
        if ($user) {
            return response()->json([
                'Balance' => $user->balance,
                'Message' => 'Success',
                'Status' => 0
            ]);
        } else {
            return response()->json(['Message' => 'Invalid credentials', 'Status' => 7]);
        }
    }

    public function placeBet(Request $request)
    {
        $request_data = $request->all(); 
        $request->validate([
            'amount' => 'required|numeric',
            'gamecode' => 'required|string',
            'hash' => 'required|string',
            'id' => 'required|string',
            'roundid' => 'required|string',
            'username' => 'required|string',
        ]);

        // Verify hash
        if (!verifyHash($request_data)) {
            return response()->json(['Message' => 'Invalid, hash code does not match', 'Status' => 401]);
        }

        $user = User::where('username', $request_data['username'])->first();
        if (!$user) {
            return response()->json(['Message' => 'Invalid Credentials', 'Status' => 7]);
        }

        try {
            // Validate user's balance
            if ($request_data['amount'] >= $user->balance) {
                throw new Exception("Insufficient fund", 100);
            }

            DB::transaction(function () use ($user, $request_data) {
                $game = $user->games()->firstOrCreate(['gamecode' => $request_data['gamecode']]);
                $round = $game->rounds()->firstOrCreate(['roundid' => $request_data['roundid']]);
                $bet = Bet::create([
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                    'round_id' => $round->id,
                    'bet_identifier' => $request_data['id'],
                    'amount' => $request_data['amount'],
                ]);

                // Place the bet and reduce the bet amount from user balance
                $user->balance -= $request_data['amount'];
                $user->save();
            });

            return response()->json(['Message' => 'Bet placed successfully', 'Status' => 0]);
        } catch (\Throwable $th) {
            return response()->json(['Message' => $th->getMessage(), 'Status' => 1000]);
        }
    }

    public function settleBet(Request $request)
    {
        $request_data = $request->all();
        $request->validate([
            'amount' => 'required|numeric',
            'gamecode' => 'required|string',
            'hash' => 'required|string',
            'id' => 'required|string',
            'roundid' => 'required|string',
            'username' => 'required|string',
        ]);
        
        // Verify hash
        if (!verifyHash($request_data)) {
            return response()->json(['Message' => 'Invalid, hash code does not match', 'Status' => 401]);
        }

        $user = User::where('username', $request_data['username'])->first();
        $game = Game::where('gamecode', $request_data['gamecode'])->first();
        $round = Round::where('roundid', $request_data['roundid'])->first();

        if (!$user || !$game || !$round) {
            return response()->json(['Message' => 'Invalid user, game, or round', 'Status' => 404]);
        }

        $settleBet = SettleBet::where('id',$request_data['id'])->first();        
        if ($settleBet) {
            return response()->json(['error' => 'Bet is already settled'], 400);
        } else {
            $settleBet = SettleBet::create([
                'id' => $request_data['id'],
                'amount' => $request_data['amount'],
                'description' => $request_data['description'],
                'type' => $request_data['type'],
                'user_id' => $user->id,
                'game_id' => $game->id,
                'round_id' => $round->id
            ]);

            $user->balance += $request->amount; // Add the settled amount to the user's balance
            $user->save();

            return response()->json(['Message' => 'Bet settled successfully', 'Status' => 0]);
        }
        
    }

    public function cancelBet(Request $request)
    {
        $request_data = $request->all();
        $request->validate([
            'amount' => 'required|numeric',
            'gamecode' => 'required|string',
            'hash' => 'required|string',
            'id' => 'required|string',
            'roundid' => 'required|string',
            'username' => 'required|string',
        ]);

        // Verify hash
        if (!verifyHash($request_data)) {
            return response()->json(['Message' => 'Invalid, hash code does not match', 'Status' => 401]);
        }

        $bet = Bet::where('bet_identifier', $request['betid'])->first();
        if (!$bet) {
            return response()->json(['error' => 'Bet not found'], 404);
        }

        // Store the cancelled bet
        $cancelBet = CancelBet::create([
            'bet_id' => $bet->id,
            'cancel_bet_identifier' => $request_data['id'],
        ]);

        $user = $bet->user;
        $user->balance += $bet->amount; // Add the settled amount to the user's balance
        $user->save();

        return response()->json(['Message' => 'Bet canceled successfully', 'Status' => 0]);
    }

}
