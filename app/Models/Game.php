<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'gamecode','appid'
    ];

    public function user()
    {
        return $this->belongsToMany(User::class,'game_user');
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function settleBets()
    {
        return $this->hasMany(SettleBet::class);
    }
}
