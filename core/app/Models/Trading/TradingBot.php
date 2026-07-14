<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class TradingBot extends Model
{
    protected $table = 'trading_bots';

    protected $fillable = [
        'name',
        'price',
        'lose_count',
        'return_min',
        'return_max',
        'icon',
        'status',
    ];
}
