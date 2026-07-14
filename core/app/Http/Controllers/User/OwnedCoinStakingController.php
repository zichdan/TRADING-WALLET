<?php

namespace App\Http\Controllers\User;

use App\Models\Trading\Staking;
use App\Models\Trading\StakingCoin;
use App\Models\Trading\TradingWallet;
use App\Models\Trading\TradingWalletTransaction;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class OwnedCoinStakingController extends Controller
{
    public function index()
    {
        $page_title = 'Coin Stakings';
        $coins = StakingCoin::where('status', 'enabled')->orderBy('coin')->get();

        return view('themes.' . websiteInfo('theme') . '.user.trade.staking.index', compact('page_title', 'coins'));
    }

    public function stake()
    {
        request()->validate([
            'coin_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ]);

        DB::transaction(function () {
            $coin = StakingCoin::where('id', request('coin_id'))->lockForUpdate()->firstOrFail();
            $amount = (float) request('amount');

            if ($coin->status !== 'enabled') {
                abort(422, 'This staking coin is disabled');
            }

            if ($amount < (float) $coin->min_stake || $amount > (float) $coin->max_stake) {
                abort(422, 'Stake amount is outside the allowed range');
            }

            if (((float) $coin->staked + $amount) > (float) $coin->total) {
                abort(422, 'This staking pool does not have enough capacity');
            }

            $wallet = TradingWallet::where('user_id', user('id'))
                ->where('symbol', strtoupper($coin->symbol))
                ->lockForUpdate()
                ->first();

            if (!$wallet || (float) $wallet->balance < $amount) {
                abort(422, 'Insufficient trading wallet balance for staking');
            }

            $wallet->balance = (float) $wallet->balance - $amount;
            $wallet->save();

            $coin->staked = (float) $coin->staked + $amount;
            $coin->save();

            Staking::create([
                'coin_id' => $coin->id,
                'user_id' => user('id'),
                'amount' => $amount,
                'staked' => $amount,
                'daily_return' => $coin->daily_return,
                'returned' => '0',
                'returnable' => '0',
                'next_return' => now()->addDay()->timestamp,
                'last_return' => now()->timestamp,
            ]);

            TradingWalletTransaction::create([
                'user_id' => user('id'),
                'wallet_id' => $wallet->id,
                'symbol' => strtoupper($coin->symbol),
                'type' => 'debit',
                'order_type' => 'staking',
                'amount' => $amount,
            ]);
        });

        return back()->with('success', 'Coin staked successfully');
    }
}
