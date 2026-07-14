# Option 4 Implementation Log: Owned CryptoTrading Replacement

Date: 2026-06-18

## Safety Boundary

This implementation does not decrypt ionCube files, patch encoded payloads, fake a vendor license API, spoof domains, or remove protected vendor checks from encoded code. The implemented recovery path is a lawful replacement: disable the locked `CryptoTrading` module and provide first-party Laravel routes, controllers, models, helpers, and compatibility migrations for the same user-facing trading workflows.

## Internet Research Summary

The research confirms that ionCube-protected applications can be restricted by domain, IP, machine, expiration, or license-file rules. ionCube documents these as file/license restrictions and notes that license validation can be enforced by the Loader or by application code. That matches this backup: the visible production error is a domain/license validation failure, and local code has encoded route/entity files plus readable controllers that call addon/license checks.

Relevant current references:

- ionCube restrictions and license-file behavior: https://www.ioncube.com/sa/gui_docs/settings_restrictions.html
- Laravel 9 routing, route groups, route listing, and route files: https://laravel.com/docs/9.x/routing
- Laravel 9 migrations and schema changes: https://laravel.com/docs/9.x/migrations
- Laravel 9 Eloquent models: https://laravel.com/docs/9.x/eloquent
- Nwidart module methods, including enable/disable behavior: https://nwidart.com/laravel-modules/v6/advanced-tools/module-methods
- Laravel Modules v9 package overview: https://laravelmodules.com/docs/9

## Implemented Code Changes

Added first-party Eloquent models under `public_html/trading-wallet.net/core/app/Models/Trading/`:

- `TradingWallet`
- `TradingWalletTransaction`
- `TradingLog`
- `DemoTradingLog`
- `DemoTradingWallet`
- `TradingBot`
- `TradingBotActivation`
- `TradingBotTrade`
- `TradingPair`
- `StakingCoin`
- `Staking`

Added first-party market-data adapter:

- `app/Support/Trading/OwnedTradingMarketData.php`

It uses Poloniex market data when available and safe local fallbacks when the API is unavailable, so local testing does not break when external widgets or APIs are blocked.

Added first-party helper functions:

- `ownedTradingEnabled()`
- `ownedTradingWallets()`
- `ownedTradingWalletFiatValue()`
- `ownedTradingBotName()`
- `ownedTradingBotActivated()`
- `ownedTradingCurrentStake()`

Added owned user controllers:

- `app/Http/Controllers/User/OwnedTradingWalletController.php`
- `app/Http/Controllers/User/OwnedTradeController.php`
- `app/Http/Controllers/User/OwnedDemoController.php`
- `app/Http/Controllers/User/OwnedCoinStakingController.php`

Added compatibility migration:

- `database/migrations/2026_06_18_060000_add_owned_trading_schema_compatibility.php`

It adds missing `price`, `finalz`, `coinz`, and `profit` fields only when absent, preserving databases that already have those fields.

Updated route ownership:

- `routes/web.php` now registers all 17 `user.trading.*` routes through owned controllers.
- `modules_statuses.json` now sets `CryptoTrading` to `false`, preventing the encoded CryptoTrading module route provider from owning the user trading URLs.

Updated Blade files to avoid encoded module entities/helpers:

- `resources/views/themes/cryptic/user/dashboard.blade.php`
- `resources/views/themes/cryptic/includes/sidenav.blade.php`
- `resources/views/themes/cryptic/user/trade/trade/index.blade.php`
- `resources/views/themes/cryptic/user/trade/trade/bot.blade.php`
- `resources/views/themes/cryptic/user/trade/trade/bots.blade.php`
- `resources/views/themes/cryptic/user/trade/wallets/view.blade.php`
- `resources/views/themes/cryptic/user/trade/staking/index.blade.php`

## Route Cutover Evidence

Command verified:

```powershell
php artisan route:list --path=user/trading
```

Result: all 17 `/user/trading/...` routes now resolve to `User\OwnedTradingWalletController`, `User\OwnedTradeController`, `User\OwnedDemoController`, and `User\OwnedCoinStakingController`.

## Browser Verification Evidence

New smoke script:

- `TRADING WALLET TESTING/tools/trading-owned-smoke.mjs`

Verified pages:

- `http://127.0.0.1:8087/user/trading/wallets`
- `http://127.0.0.1:8087/user/trading/trade/ETC/USDT`
- `http://127.0.0.1:8087/user/trading/demo/ETC/USDT`
- `http://127.0.0.1:8087/user/trading/trade/bot/ETC/USDT`

Result:

- All pages returned HTTP 200.
- No page JavaScript errors.
- No `INVALID LICENSE KEY` text.
- No `QueryException`, `ErrorException`, `Fatal error`, or `Modules\CryptoTrading\Entities` text.

Screenshots saved:

- `TRADING WALLET TESTING/07-owned-wallets.png`
- `TRADING WALLET TESTING/08-owned-live-trade.png`
- `TRADING WALLET TESTING/09-owned-demo-trade.png`
- `TRADING WALLET TESTING/10-owned-bot-trade.png`

Registration regression was also rerun:

- `TRADING WALLET TESTING/tools/registration-smoke.mjs`
- Result: registration redirects to `/user/dashboard`, no `tcal` SQL error, no page errors.

## Five Lawful Paths To Finish This Once And For All

1. Keep Option 4 and harden it to production.
   Continue with the owned CryptoTrading replacement already implemented here, then add feature tests, seed production trading symbols/bots/staking coins, and perform a staging import from the live database.

2. Restore or reacquire the original licensed domain.
   If the old domain can be recovered or redirected legitimately, point it at the site and keep the vendor-encoded module only as a short-term bridge while migration continues.

3. Obtain a written license transfer or replacement license.
   Use purchase evidence, server history, invoices, and screenshots to request a formal license reissue from any successor, marketplace, or payment processor contact.

4. Replace remaining encoded integration points.
   The main remaining risk is any first-party helper or admin/deposit flow that imports `Modules\CryptoTrading\Entities\TradingWallet`. Replace those imports with `App\Models\Trading\TradingWallet` and owned transaction code.

5. Migrate the whole trading feature into an owned standalone domain module.
   Move trading wallet, trade, demo, bot, and staking logic into an app-owned namespace with tests and remove the encoded module folder from deployment after parity is proven.

## Immediate Next Steps

1. Import or seed real trading wallet, bot, staking, and currency data into the local database.
2. Test create-wallet, fund/withdraw, live order, demo order, bot activation, and staking POST flows with browser automation.
3. Replace `generalHelper.php` deposit-credit code that still imports `Modules\CryptoTrading\Entities\TradingWallet`.
4. Run a staging deployment with `CryptoTrading` disabled and verify user login, dashboard, deposits, trading pages, and transaction history.
5. Keep all old encoded files out of the request path until a legitimate vendor license transfer is obtained.
