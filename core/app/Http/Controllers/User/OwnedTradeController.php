<?php

namespace App\Http\Controllers\User;

use App\Models\Transaction;
use App\Models\Trading\TradingBot;
use App\Models\Trading\TradingBotActivation;
use App\Models\Trading\TradingBotTrade;
use App\Models\Trading\TradingLog;
use App\Models\Trading\TradingWallet;
use App\Models\Trading\TradingWalletTransaction;
use App\Support\Trading\OwnedTradingMarketData;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OwnedTradeController extends Controller
{
    public function index()
    {
        $page_title = 'Dashboard';

        return view('themes.' . websiteInfo('theme') . '.user.trade.trade.index', compact('page_title'));
    }

    public function trade()
    {
        $symbol_1 = strtoupper(request()->route('symbol1'));
        $symbol_2 = strtoupper(request()->route('symbol2'));
        $pair = $symbol_1 . '_' . $symbol_2;
        $page_title = 'Trade ' . $symbol_1 . '/' . $symbol_2;

        $symbols = OwnedTradingMarketData::currencies();
        $wallets = TradingWallet::where('user_id', user('id'))->orderBy('symbol')->get();
        $base = $wallets->where('symbol', $symbol_1)->first();
        $quote = $wallets->where('symbol', $symbol_2)->first();
        $pair_info = OwnedTradingMarketData::ticker($pair);
        $candles = OwnedTradingMarketData::candles($pair, 10);
        $market_trades = OwnedTradingMarketData::recentTrades($pair, 10);
        $prices = OwnedTradingMarketData::prices();
        $trades = TradingLog::where('pair', $pair)
            ->where('user_id', user('id'))
            ->orderBy('id', 'DESC')
            ->get();
        $is_trade = $trades->where('status', 'active');

        return view('themes.' . websiteInfo('theme') . '.user.trade.trade.trade', compact('page_title', 'symbol_1', 'symbol_2', 'pair_info', 'candles', 'market_trades', 'wallets', 'base', 'quote', 'prices', 'trades', 'is_trade'));
    }

    public function tradeValidate()
    {
        request()->validate([
            'order' => ['required', 'in:market,limit,stop-limit'],
            'order_type' => ['required', 'in:buy,sell'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'base' => ['required', 'string', 'max:20'],
            'quote' => ['required', 'string', 'max:20'],
            'leverage' => ['required', 'string', 'max:20'],
        ]);

        $base = strtoupper(request('base'));
        $quote = strtoupper(request('quote'));
        $pair = $base . '_' . $quote;
        $price = OwnedTradingMarketData::price($pair);
        $amount = (float) request('amount');
        $total = $price * $amount;
        $tradeStatus = request('order') === 'market' ? 'active' : 'win';
        $amountConverted = 0;

        DB::transaction(function () use ($base, $quote, $pair, $price, $amount, $total, $tradeStatus, &$amountConverted) {
            $baseWallet = $this->firstOrCreateWallet($base);
            $quoteWallet = $this->firstOrCreateWallet($quote);

            if ($tradeStatus === 'win') {
                if (request('order_type') === 'buy') {
                    if ((float) $quoteWallet->balance < $total) {
                        abort(422, 'Insufficient quote wallet balance');
                    }

                    $quoteWallet->balance = (float) $quoteWallet->balance - $total;
                    $quoteWallet->save();
                    $this->recordCoinTransaction(user('id'), $quoteWallet->id, $quote, 'debit', request('order_type'), $total);

                    $baseWallet->balance = (float) $baseWallet->balance + $amount;
                    $baseWallet->save();
                    $this->recordCoinTransaction(user('id'), $baseWallet->id, $base, 'credit', request('order_type'), $amount);

                    $amountConverted = OwnedTradingMarketData::price($quote . '_USDT') * $amount;
                } else {
                    if ((float) $baseWallet->balance < $amount) {
                        abort(422, 'Insufficient base wallet balance');
                    }

                    $baseWallet->balance = (float) $baseWallet->balance - $amount;
                    $baseWallet->save();
                    $this->recordCoinTransaction(user('id'), $baseWallet->id, $base, 'debit', request('order_type'), $amount);

                    $quoteWallet->balance = (float) $quoteWallet->balance + $total;
                    $quoteWallet->save();
                    $this->recordCoinTransaction(user('id'), $quoteWallet->id, $quote, 'credit', request('order_type'), $total);

                    $amountConverted = OwnedTradingMarketData::price($base . '_USDT') * $amount;
                }
            }

            TradingLog::create([
                'user_id' => user('id'),
                'trade_start' => now()->timestamp,
                'trade_stop' => now()->addMinutes(random_int(5, 6))->timestamp,
                'amount' => $amount,
                'amount_converted' => $amountConverted,
                'price' => $price,
                'pair' => $pair,
                'order' => request('order'),
                'order_type' => request('order_type'),
                'leverage' => request('leverage'),
                'tp' => request('tp') ?? 0,
                'sl' => request('sl') ?? 0,
                'finalz' => $price + (float) user('tcal'),
                'coinz' => (float) user('tcal'),
                'profit' => 0,
                'status' => $tradeStatus,
            ]);
        });

        return back()->with('success', 'Trade recorded successfully');
    }

    public function bot()
    {
        $symbol_1 = strtoupper(request()->route('symbol1'));
        $symbol_2 = strtoupper(request()->route('symbol2'));
        $pair = $symbol_1 . '_' . $symbol_2;
        $page_title = 'Trade ' . $symbol_1 . '/' . $symbol_2;

        $symbols = OwnedTradingMarketData::currencies();
        $wallets = TradingWallet::where('user_id', user('id'))->orderBy('symbol')->get();
        $base = $wallets->where('symbol', $symbol_1)->first();
        $quote = $wallets->where('symbol', $symbol_2)->first();
        $pair_info = OwnedTradingMarketData::ticker($pair);
        $candles = OwnedTradingMarketData::candles($pair, 20);
        $market_trades = OwnedTradingMarketData::recentTrades($pair, 20);
        $prices = OwnedTradingMarketData::prices();
        $trades = TradingBotTrade::where('user_id', user('id'))->orderBy('id', 'DESC')->get();
        $bots = TradingBot::orderBy('price', 'DESC')->where('status', 'enabled')->get();
        $bot_history = Transaction::where('user_id', user('id'))->where('remark', 'Bot Trade')->get();
        $get_status = TradingBotTrade::where('user_id', user('id'))->where('pair', $pair)->first();
        $running_bot = '';
        $bot_status = false;

        if ($get_status && $get_status->status === 'running') {
            $bot_status = true;
            $running_bot = TradingBot::where('id', $get_status->bot_id)->first();
        }

        return view('themes.' . websiteInfo('theme') . '.user.trade.trade.bot', compact('page_title', 'running_bot', 'symbol_1', 'symbol_2', 'pair_info', 'candles', 'market_trades', 'wallets', 'base', 'quote', 'prices', 'trades', 'bots', 'bot_status', 'bot_history'));
    }

    public function botActivate()
    {
        request()->validate([
            'bot_id' => ['required', 'integer'],
            'key' => ['required', 'string', 'max:255'],
        ]);

        $activation = TradingBotActivation::where('key', request('key'))
            ->where('user_id', user('id'))
            ->where('bot_id', request('bot_id'))
            ->where('status', 'enabled')
            ->first();

        if (!$activation) {
            return back()->with('fail', 'Invalid Bot Activation key');
        }

        $activation->user_activated = 'yes';
        $activation->save();

        return back()->with('success', 'Bot Activated successfully');
    }

    public function botTrade()
    {
        request()->validate([
            'bot_id' => ['required', 'integer'],
            'pairs' => ['required', 'string', 'max:50'],
            'type' => ['required', 'in:start,stop'],
        ]);

        $activation = TradingBotActivation::where('bot_id', request('bot_id'))
            ->where('user_id', user('id'))
            ->where('user_activated', 'yes')
            ->where('status', 'enabled')
            ->first();

        if (!$activation) {
            abort(422, 'Bot is not activated for this user');
        }

        $status = request('type') === 'start' ? 'running' : 'stopped';

        TradingBotTrade::updateOrCreate(
            [
                'pair' => strtoupper(str_replace('/', '_', request('pairs'))),
                'user_id' => user('id'),
            ],
            [
                'bot_id' => request('bot_id'),
                'status' => $status,
                'next_trade_time' => now()->addMinutes(30)->timestamp,
            ]
        );

        return back()->with('success', 'Bot is ' . $status);
    }

    public function endTrade()
    {
        request()->validate([
            'id' => ['required'],
        ]);

        $id = Crypt::decryptString(request('id'));

        DB::transaction(function () use ($id) {
            $trade = TradingLog::where('user_id', user('id'))
                ->where('id', $id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->firstOrFail();

            if ($trade->order !== 'market') {
                $trade->status = 'win';
                $trade->save();
                return;
            }

            $currentPrice = OwnedTradingMarketData::price($trade->pair) + (float) $trade->coinz;
            $entryPrice = (float) $trade->price;
            $profitLoss = (float) str_replace('%', '', $trade->leverage) * (($currentPrice - $entryPrice) * (float) $trade->amount);

            if ($trade->order_type === 'buy') {
                $tradeStatus = $entryPrice < $currentPrice ? 'win' : 'lose';
            } else {
                $tradeStatus = $currentPrice < $entryPrice ? 'win' : 'lose';
            }

            $trade->status = $tradeStatus;
            $trade->finalz = $currentPrice;
            $trade->profit = $profitLoss;
            $trade->save();

            $walletSymbol = explode('_', $trade->pair)[1] ?? 'USDT';
            $wallet = TradingWallet::where('symbol', $walletSymbol)
                ->where('user_id', $trade->user_id)
                ->lockForUpdate()
                ->first();

            if ($wallet) {
                $wallet->balance = (float) $wallet->balance + $profitLoss;
                $wallet->save();
                $this->recordCoinTransaction($trade->user_id, $wallet->id, $walletSymbol, 'credit', 'market', $trade->amount);
            }
        });

        return back()->with('success', 'Trade ended successfully');
    }

    private function firstOrCreateWallet(string $symbol): TradingWallet
    {
        return TradingWallet::firstOrCreate(
            [
                'user_id' => user('id'),
                'symbol' => $symbol,
            ],
            [
                'balance' => '0',
                'address' => Str::random(34),
                'icon' => strtolower($symbol) . '.svg',
            ]
        );
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
}
