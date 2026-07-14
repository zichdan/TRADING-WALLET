<?php

namespace App\Support\Trading;

use App\Models\Trading\TradingWallet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OwnedTradingMarketData
{
    private const DEFAULT_SYMBOLS = [
        'BTC' => 'Bitcoin',
        'ETH' => 'Ethereum',
        'USDT' => 'Tether',
        'ETC' => 'Ethereum Classic',
        'BNB' => 'BNB',
        'ADA' => 'Cardano',
        'DOGE' => 'Dogecoin',
        'LTC' => 'Litecoin',
        'TRX' => 'TRON',
        'XRP' => 'XRP',
    ];

    private const FALLBACK_USDT_PRICES = [
        'BTC' => 65000,
        'ETH' => 3500,
        'USDT' => 1,
        'ETC' => 25,
        'BNB' => 600,
        'ADA' => 0.45,
        'DOGE' => 0.12,
        'LTC' => 85,
        'TRX' => 0.12,
        'XRP' => 0.55,
    ];

    public static function currencies(): Collection
    {
        $symbols = collect(self::DEFAULT_SYMBOLS);

        TradingWallet::query()
            ->select('symbol')
            ->distinct()
            ->orderBy('symbol')
            ->pluck('symbol')
            ->filter()
            ->each(function ($symbol) use (&$symbols) {
                $upper = strtoupper((string) $symbol);
                if (!$symbols->has($upper)) {
                    $symbols->put($upper, $upper);
                }
            });

        return $symbols
            ->map(fn ($name, $symbol) => (object) [
                'symbol' => $symbol,
                'name' => $name,
            ])
            ->values();
    }

    public static function price(string $pair): float
    {
        $pair = strtoupper(str_replace('/', '_', $pair));

        if ($pair === 'USDT_USDT') {
            return 1.0;
        }

        $remote = self::poloniex("/markets/{$pair}/price");
        $price = data_get($remote, 'price');

        if (is_numeric($price) && (float) $price > 0) {
            return (float) $price;
        }

        [$base, $quote] = array_pad(explode('_', $pair, 2), 2, 'USDT');
        $basePrice = self::FALLBACK_USDT_PRICES[$base] ?? 1;
        $quotePrice = self::FALLBACK_USDT_PRICES[$quote] ?? 1;

        return $quotePrice > 0 ? (float) ($basePrice / $quotePrice) : (float) $basePrice;
    }

    public static function ticker(string $pair): object
    {
        $pair = strtoupper(str_replace('/', '_', $pair));
        $remote = self::poloniex("/markets/{$pair}/ticker24h");
        $close = data_get($remote, 'close');

        if (is_numeric($close)) {
            return (object) [
                'close' => (string) $close,
                'dailyChange' => (string) (data_get($remote, 'dailyChange') ?? '0'),
                'high' => (string) (data_get($remote, 'high') ?? $close),
                'low' => (string) (data_get($remote, 'low') ?? $close),
                'tradeCount' => (string) (data_get($remote, 'tradeCount') ?? '0'),
            ];
        }

        $price = self::price($pair);

        return (object) [
            'close' => (string) $price,
            'dailyChange' => '0',
            'high' => (string) ($price * 1.01),
            'low' => (string) ($price * 0.99),
            'tradeCount' => '0',
        ];
    }

    public static function candles(string $pair, int $limit = 10): array
    {
        $pair = strtoupper(str_replace('/', '_', $pair));
        $remote = self::poloniex("/markets/{$pair}/candles", [
            'interval' => 'MINUTE_1',
            'limit' => $limit,
        ]);

        if (is_array($remote) && count($remote) > 0 && is_array($remote[0] ?? null)) {
            return $remote;
        }

        $price = self::price($pair);
        $now = time();
        $candles = [];

        for ($i = $limit; $i > 0; $i--) {
            $open = $price * (1 + (($i % 3) - 1) / 1000);
            $close = $price * (1 + (($i % 5) - 2) / 1000);
            $high = max($open, $close) * 1.001;
            $low = min($open, $close) * 0.999;
            $volume = 10 + $i;
            $timestamp = $now - ($i * 60);
            $candles[] = [0, 0, 0, $open, $close, 0, $volume, $high, $low, 0, 0, 0, $timestamp];
        }

        return $candles;
    }

    public static function recentTrades(string $pair, int $limit = 10): array
    {
        $pair = strtoupper(str_replace('/', '_', $pair));
        $remote = self::poloniex("/markets/{$pair}/trades", [
            'limit' => $limit,
        ]);

        if (is_array($remote) && count($remote) > 0) {
            return collect($remote)
                ->map(fn ($trade) => (object) [
                    'createTime' => (int) ((data_get($trade, 'createTime') ?? time() * 1000) / 1000),
                    'takerSide' => data_get($trade, 'takerSide') ?? 'BUY',
                    'price' => data_get($trade, 'price') ?? self::price($pair),
                    'amount' => data_get($trade, 'amount') ?? '0',
                ])
                ->all();
        }

        $price = self::price($pair);
        $now = time();

        return collect(range(1, $limit))
            ->map(fn ($i) => (object) [
                'createTime' => $now - ($i * 45),
                'takerSide' => $i % 2 === 0 ? 'SELL' : 'BUY',
                'price' => (string) ($price * (1 + (($i % 4) - 2) / 1000)),
                'amount' => (string) (0.1 * $i),
            ])
            ->all();
    }

    public static function prices(): array
    {
        $remote = self::poloniex('/markets/price');

        if (is_array($remote) && count($remote) > 0) {
            return collect($remote)
                ->filter(fn ($price) => data_get($price, 'symbol') && data_get($price, 'price'))
                ->map(fn ($price) => (object) [
                    'symbol' => data_get($price, 'symbol'),
                    'price' => data_get($price, 'price'),
                    'dailyChange' => data_get($price, 'dailyChange') ?? '0',
                ])
                ->values()
                ->all();
        }

        return collect(self::DEFAULT_SYMBOLS)
            ->keys()
            ->reject(fn ($symbol) => $symbol === 'USDT')
            ->map(fn ($symbol) => (object) [
                'symbol' => "{$symbol}_USDT",
                'price' => (string) (self::FALLBACK_USDT_PRICES[$symbol] ?? 1),
                'dailyChange' => '0',
            ])
            ->values()
            ->all();
    }

    public static function fiatValue(string $symbol, $amount): float
    {
        return (float) $amount * self::price(strtoupper($symbol) . '_USDT');
    }

    private static function poloniex(string $path, array $query = []): mixed
    {
        try {
            $response = Http::timeout(6)
                ->withoutVerifying()
                ->get('https://api.poloniex.com' . $path, $query);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $exception) {
            return null;
        }

        return null;
    }
}
