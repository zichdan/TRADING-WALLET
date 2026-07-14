<?php

namespace App\Models\Trading;

use Illuminate\Database\Eloquent\Model;

class Staking extends Model
{
    protected $table = 'stakings';

    protected $fillable = [
        'coin_id',
        'user_id',
        'amount',
        'staked',
        'daily_return',
        'returned',
        'returnable',
        'next_return',
        'last_return',
    ];
}
