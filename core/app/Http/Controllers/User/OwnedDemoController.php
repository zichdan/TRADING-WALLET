<?php

namespace App\Http\Controllers\User;

use App\Models\Trading\DemoTradingLog;
use App\Models\Trading\DemoTradingWallet;
use App\Support\Trading\OwnedTradingMarketData;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OwnedDemoController extends Controller
{
    public function index()
    {
        $this->ensureDemoWallet('BTC');
        $this->ensureDemoWallet('USDT');

        $page_title = 'Trading Chart';
        $trades = DemoTradingLog::where('user_id', user('id'))->orderBy('id', 'DESC')->get();
        $wallets = DemoTradingWallet::where('user_id', user('id'))->orderBy('symbol')->get();

        return view('themes.' . websiteInfo('theme') . '.user.trade.demo.index', compact('page_title', 'trades', 'wallets'));
    }

    public function trade()
    {
        $symbol_1 = strtoupper(request()->route('symbol1'));
        $symbol_2 = strtoupper(request()->route('symbol2'));
        $pair = $symbol_1 . '_' . $symbol_2;
        $page_title = 'Trade History ' . $symbol_1 . '/' . $symbol_2;

        $this->ensureDemoWallet($symbol_1);
        $this->ensureDemoWallet($symbol_2);

        $symbols = OwnedTradingMarketData::currencies();
        $wallets = DemoTradingWallet::where('user_id', user('id'))->orderBy('symbol')->get();
        $base = $wallets->where('symbol', $symbol_1)->first();
        $quote = $wallets->where('symbol', $symbol_2)->first();
        $pair_info = OwnedTradingMarketData::ticker($pair);
        $candles = OwnedTradingMarketData::candles($pair, 13);
        $market_trades = OwnedTradingMarketData::recentTrades($pair, 13);
        $prices = OwnedTradingMarketData::prices();
        $trades = DemoTradingLog::where('pair', $pair)
            ->where('user_id', user('id'))
            ->orderBy('id', 'DESC')
            ->get();
        $is_trade = $trades->where('status', 'active');

        return view('themes.' . websiteInfo('theme') . '.user.trade.demo.trade', compact('page_title', 'symbol_1', 'symbol_2', 'pair_info', 'candles', 'market_trades', 'wallets', 'base', 'quote', 'prices', 'trades', 'is_trade'));
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
            $baseWallet = $this->ensureDemoWallet($base, true);
            $quoteWallet = $this->ensureDemoWallet($quote, true);

            if ($tradeStatus === 'win') {
                if (request('order_type') === 'buy') {
                    if ((float) $quoteWallet->balance < $total) {
                        abort(422, 'Insufficient demo quote wallet balance');
                    }

                    $quoteWallet->balance = (float) $quoteWallet->balance - $total;
                    $quoteWallet->save();

                    $baseWallet->balance = (float) $baseWallet->balance + $amount;
                    $baseWallet->save();

                    $amountConverted = OwnedTradingMarketData::price($quote . '_USDT') * $amount;
                } else {
                    if ((float) $baseWallet->balance < $amount) {
                        abort(422, 'Insufficient demo base wallet balance');
                    }

                    $baseWallet->balance = (float) $baseWallet->balance - $amount;
                    $baseWallet->save();

                    $quoteWallet->balance = (float) $quoteWallet->balance + $total;
                    $quoteWallet->save();

                    $amountConverted = OwnedTradingMarketData::price($base . '_USDT') * $amount;
                }
            }

            DemoTradingLog::create([
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

        return back()->with('success', 'Demo trade recorded successfully');
    }

    public function endTrade()
    {
        request()->validate([
            'id' => ['required'],
        ]);

        $id = Crypt::decryptString(request('id'));

        DB::transaction(function () use ($id) {
            $trade = DemoTradingLog::where('user_id', user('id'))
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
            $wallet = $this->ensureDemoWallet($walletSymbol, true);
            $wallet->balance = (float) $wallet->balance + $profitLoss;
            $wallet->save();
        });

        return back()->with('success', 'Demo trade ended successfully');
    }

    private function ensureDemoWallet(string $symbol, bool $lock = false): DemoTradingWallet
    {
        $query = DemoTradingWallet::where('user_id', user('id'))->where('symbol', $symbol);

        if ($lock) {
            $query->lockForUpdate();
        }

        $wallet = $query->first();

        if ($wallet) {
            return $wallet;
        }

        return DemoTradingWallet::create([
            'user_id' => user('id'),
            'symbol' => $symbol,
            'address' => Str::random(34),
            'balance' => $symbol === 'USDT' ? '50000' : '0',
            'icon' => strtolower($symbol) . '.svg',
        ]);
    }
}
