<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function playGame(Request $request){
        $user = Auth::guard('api')->setToken($request->token)->user();
        if ($user) {
            $gameUrl = config('services.gaming.url') . '/playGame?' . http_build_query([
                'token' => $request->token,
                'appID' => $request->appID,
                'gameCode' => $request->gameCode,
                'language' => $request->language,
                'mobile' => $request->mobile,
                'redirectUrl' => $request->redirectUrl,
            ]);
            // Redirect the user to the game URL
            return redirect()->away($gameUrl);
        }
        return response()->json([
            'Message' => 'Invalid Token',
            'Status' => 3
        ]);
    }

    public function listGames(Request $request)
    {
        $request_data = $request->all();
        // Validate the incoming JSON request
        $request->validate([
            'AppID' => 'required|string',
            'Hash' => 'required|string',
            'Timestamp' => 'required|numeric',
        ]);

        // Validate the hash 
        if (!verifyHash($request_data)) {
            return response()->json(['Message' => 'Invalid, hash code does not match', 'Status' => 401]);
        }

        // Perform logic to retrieve and list games
        $games = $this->retrieveGames($request_data['AppID']);

        return response()->json([
            'Error' => 0,
            'Description' => 'Ok',
            'Games' => $games,
        ]);
    }

    private function retrieveGames($appID)
    {
        $games = Game::where('appid',$appID)->get();        
        return $games;
    }
}
