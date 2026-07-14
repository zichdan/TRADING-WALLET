<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class TradingWallet extends Model
{
    protected $table = 'trading_wallets';

    protected $fillable = [
        'user_id',
        'symbol',
        'balance',
        'address',
        'icon',
    ];
}
