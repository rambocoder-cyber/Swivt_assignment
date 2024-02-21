<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettleBet extends Model
{
    use HasFactory;
    protected $fillable = ['id','amount','description','type','user_id','game_id','round_id'];
    protected $primaryKey = 'id';

    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }
}
