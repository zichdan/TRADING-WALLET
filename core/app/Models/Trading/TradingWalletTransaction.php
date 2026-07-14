<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class TradingWalletTransaction extends Model
{
    protected $table = 'trading_wallet_transactions';

    protected $fillable = [
        'user_id',
        'wallet_id',
        'symbol',
        'type',
        'order_type',
        'amount',
    ];
}
