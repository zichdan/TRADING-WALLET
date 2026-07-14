<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class TradingBotActivation extends Model
{
    protected $table = 'trading_bot_activations';

    protected $fillable = [
        'bot_id',
        'user_id',
        'user_activated',
        'key',
        'status',
    ];
}
