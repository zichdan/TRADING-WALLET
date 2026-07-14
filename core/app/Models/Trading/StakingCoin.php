<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class StakingCoin extends Model
{
    protected $table = 'staking_coins';

    protected $fillable = [
        'coin',
        'symbol',
        'price',
        'duration',
        'total',
        'staked',
        'daily_return',
        'max_stake',
        'min_stake',
        'apr',
        'status',
        'icon',
    ];
}
