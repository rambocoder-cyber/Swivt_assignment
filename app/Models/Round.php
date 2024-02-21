<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    use HasFactory;

    protected $fillable = ['roundid'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    public function settleBets()
    {
        return $this->hasMany(SettleBet::class);
    }
}
