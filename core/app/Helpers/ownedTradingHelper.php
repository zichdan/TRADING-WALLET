<?php

use App\Models\Trading\Staking;
use App\Models\Trading\TradingBot;
use App\Models\Trading\TradingBotActivation;
use App\Models\Trading\TradingWallet;
use App\Support\Trading\OwnedTradingMarketData;

if (!function_exists('ownedTradingEnabled')) {
    function ownedTradingEnabled(): bool
    {
        return websiteInfo('trader_mode') == 'enabled';
    }
}

if (!function_exists('ownedTradingWallets')) {
    function ownedTradingWallets()
    {
        return TradingWallet::where('user_id', user('id'))->orderBy('symbol')->get();
    }
}

if (!function_exists('ownedTradingWalletFiatValue')) {
    function ownedTradingWalletFiatValue($wallet): float
    {
        if (!$wallet) {
            return 0.0;
        }

        return OwnedTradingMarketData::fiatValue($wallet->symbol, $wallet->balance);
    }
}

if (!function_exists('ownedTradingBotName')) {
    function ownedTradingBotName($botId): string
    {
        $bot = TradingBot::where('id', $botId)->first();
        return $bot->name ?? 'Trading Bot';
    }
}

if (!function_exists('ownedTradingBotActivated')) {
    function ownedTradingBotActivated($botId): bool
    {
        return TradingBotActivation::where('bot_id', $botId)
            ->where('user_id', user('id'))
            ->where('user_activated', 'yes')
            ->exists();
    }
}

if (!function_exists('ownedTradingCurrentStake')) {
    function ownedTradingCurrentStake($coinId)
    {
        return Staking::where('user_id', user('id'))
            ->where('coin_id', $coinId)
            ->sum('amount');
    }
}
