<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelBet extends Model
{
    use HasFactory;
    protected $fillable = ['cancel_bet_identifier','bet_id'];

    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }
}
