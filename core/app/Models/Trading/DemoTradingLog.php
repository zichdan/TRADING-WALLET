<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class DemoTradingLog extends Model
{
    protected $table = 'demo_trading_logs';

    protected $fillable = [
        'user_id',
        'trade_start',
        'trade_stop',
        'amount',
        'amount_converted',
        'price',
        'pair',
        'order',
        'order_type',
        'leverage',
        'tp',
        'sl',
        'finalz',
        'coinz',
        'profit',
        'status',
    ];
}
