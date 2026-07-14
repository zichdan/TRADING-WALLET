<?php

namespace App\Http\Controllers\User;

use App\Models\Transaction;
use App\Models\Trading\TradingWallet;
use App\Models\Trading\TradingWalletTransaction;
use App\Models\User;
use App\Support\Trading\OwnedTradingMarketData;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OwnedTradingWalletController extends Controller
{
    public function index()
    {
        $page_title = 'My Trading Wallets';
        $wallets = TradingWallet::where('user_id', user('id'))->orderBy('symbol')->get();
        $trading_currencies = OwnedTradingMarketData::currencies();

        return view('themes.' . websiteInfo('theme') . '.user.trade.wallets.index', compact('page_title', 'wallets', 'trading_currencies'));
    }

    public function create()
    {
        request()->validate([
            'symbol' => ['required', 'string', 'max:20'],
        ]);

        $symbol = strtoupper(request('symbol'));

        $exists = TradingWallet::where('symbol', $symbol)
            ->where('user_id', user('id'))
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Wallet already created'], 422);
        }

        $wallet = TradingWallet::create([
            'user_id' => user('id'),
            'symbol' => $symbol,
            'balance' => '0',
            'address' => Str::random(34),
            'icon' => strtolower($symbol) . '.svg',
        ]);

        if (!$wallet) {
            return response()->json(['message' => 'Failed to create wallet'], 422);
        }

        return response()->json('Wallet Created successfully');
    }

    public function view()
    {
        $wallet = TradingWallet::where('address', request()->route('address'))
            ->where('user_id', user('id'))
            ->first();

        if (!$wallet) {
            return redirect(url()->previous())->with('fail', 'Wallet not found');
        }

        $wallet_transactions = TradingWalletTransaction::where('user_id', user('id'))
            ->where('wallet_id', $wallet->id)
            ->orderBy('id', 'DESC')
            ->get();

        $page_title = 'Wallet';
        $price = OwnedTradingMarketData::price($wallet->symbol . '_USDT');

        return view('themes.' . websiteInfo('theme') . '.user.trade.wallets.view', compact('page_title', 'wallet', 'wallet_transactions', 'price'));
    }

    public function fundWithdraw()
    {
        request()->validate([
            'action' => ['required', 'in:fund,withdraw'],
            'wallet' => ['required', 'string', 'max:20'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ]);

        $action = request('action');
        $symbol = strtoupper(request('wallet'));
        $amount = (float) request('amount');
        $rate = max(OwnedTradingMarketData::price($symbol . '_USDT'), 0.00000001);
        $coinAmount = $amount / $rate;

        DB::transaction(function () use ($action, $symbol, $amount, $coinAmount) {
            $wallet = TradingWallet::where('user_id', user('id'))
                ->where('symbol', $symbol)
                ->lockForUpdate()
                ->firstOrFail();

            $user = User::where('id', user('id'))->lockForUpdate()->firstOrFail();

            if ($action === 'fund') {
                if ((float) $user->account_bal < $amount) {
                    abort(422, 'Insufficient fiat balance');
                }

                $user->account_bal = (float) $user->account_bal - $amount;
                $user->save();

                $wallet->balance = (float) $wallet->balance + $coinAmount;
                $wallet->save();

                $this->recordFiatTransaction($user->id, 'debit', $amount, 'Funding', $user->account_bal, $symbol . ' Funding');
                $this->recordCoinTransaction($user->id, $wallet->id, $symbol, 'credit', 'funding', $coinAmount);

                return;
            }

            if ((float) $wallet->balance < $coinAmount) {
                abort(422, 'Insufficient trading wallet balance');
            }

            $wallet->balance = (float) $wallet->balance - $coinAmount;
            $wallet->save();

            $user->account_bal = (float) $user->account_bal + $amount;
            $user->save();

            $this->recordCoinTransaction($user->id, $wallet->id, $symbol, 'debit', 'withdrawal', $coinAmount);
            $this->recordFiatTransaction($user->id, 'credit', $amount, 'Withdrawal', $user->account_bal, $symbol . ' withdrawal to fiat');
        });

        return response()->json('Wallet updated successfully');
    }

    private function recordCoinTransaction($userId, $walletId, $symbol, $type, $orderType, $amount): void
    {
        TradingWalletTransaction::create([
            'user_id' => $userId,
            'wallet_id' => $walletId,
            'symbol' => $symbol,
            'type' => $type,
            'order_type' => $orderType,
            'amount' => $amount,
        ]);
    }

    private function recordFiatTransaction($userId, $type, $amount, $method, $balance, $remark): void
    {
        Transaction::create([
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'balance_after_transaction' => $balance,
            'method' => $method,
            'txn_id' => bin2hex(random_bytes(16)),
            'remark' => $remark,
        ]);
    }
}
