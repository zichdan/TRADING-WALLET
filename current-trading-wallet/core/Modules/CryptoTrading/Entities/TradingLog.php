<?php

namespace Modules\CryptoTrading\Entities;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\CryptoTrading\Entities\TradingWallet;


class TradingLog extends Model
{
    use HasFactory;
    use CacheQueryBuilder;
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
        'profit',
        'finalz',
        'coinz',
        'status',
    ];
}