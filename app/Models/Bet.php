<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;
    protected $fillable = ['bet_identifier', 'round_identifier', 'amount','user_id','round_id','game_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function settleBet()
    {
        return $this->hasOne(SettleBet::class);
    }

    public function cancelBet()
    {
        return $this->hasOne(CancelBet::class);
    }
}
