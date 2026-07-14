<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class DemoTradingWallet extends Model
{
    protected $table = 'demo_trading_wallets';

    protected $fillable = [
        'user_id',
        'symbol',
        'address',
        'balance',
        'icon',
    ];
}
