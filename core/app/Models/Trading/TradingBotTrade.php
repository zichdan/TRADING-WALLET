<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class TradingBotTrade extends Model
{
    protected $table = 'trading_bot_trades';

    protected $fillable = [
        'bot_id',
        'user_id',
        'pair',
        'status',
        'next_trade_time',
        'wins',
        'loses',
    ];
}
