<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class TradingPair extends Model
{
    protected $table = 'trading_pairs';

    protected $fillable = [
        'pairs',
    ];
}
