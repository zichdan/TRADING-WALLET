# IonCube Extraction & Rewrite Master Plan

## Project: Trading-Wallet.net — Full ionCube Replacement

**Scope:** `current-trading-wallet/core/` and `core/` folders
**Objective:** Replace all 92 ionCube-encoded PHP files with native, editable, fully-owned PHP/Laravel code
**Secondary Objective:** Remove all external API calls to `api.credcrypto.net` and replace with internal logic
**Framework:** Laravel 9, PHP 8.1+, `nwidart/laravel-modules`

---

## 1. Executive Summary

The Trading-Wallet.net application contains **92 ionCube-encoded PHP files** across 5 modules and the app core admin layer. These files cannot be read, audited, modified, or debugged without the ionCube Loader extension. The encoded files cover routes, providers, middleware, controllers, models/entities, migrations, seeders, helpers, and configuration — meaning the entire business logic of the trading platform is locked behind proprietary encoding.

Additionally, the helper files (`AddonHelpers.php`, `generalHelper.php`) contain **external API calls** to `api.credcrypto.net` for license verification, country lists, currency conversion, blog fetching, loan processing, KYC processing, and addon validation. These calls disable TLS verification and create a hard dependency on a third-party server.

This document provides the complete extraction inventory, rewrite assignment plan, and architectural patterns to follow.

---

## 2. Encoded File Inventory — Complete List

### 2.1 App Core Admin Copies (4 files — ionCube encoded in `core/app/Http/Controllers/Admin/`)

| # | File Path | Status | Rewrite Assignment |
|---|-----------|--------|-------------------|
| 1 | `core/app/Http/Controllers/Admin/AdminBotController.php` | Encoded | Coordinator A |
| 2 | `core/app/Http/Controllers/Admin/AdminStakingController.php` | Encoded | Coordinator A |
| 3 | `core/app/Http/Controllers/Admin/AdminTradingWalletController.php` | Encoded | Coordinator A |
| 4 | `core/app/Http/Controllers/Admin/TradingSignalController.php` | Encoded | Coordinator A |

**Rewrite approach:** These admin controllers manage trading bots, staking coins, trading wallets, and trading signals. They must be rewritten as native PHP controllers following the pattern established in `OwnedTradeController.php`. The rewritten controllers should use `App\Models\Trading\*` models instead of `Modules\CryptoTrading\Entities\*`.

### 2.2 CryptoTrading Module (43 encoded files)

#### Routes (2 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 5 | `core/Modules/CryptoTrading/Routes/api.php` | Coordinator B |
| 6 | `core/Modules/CryptoTrading/Routes/web.php` | Coordinator B |

#### Providers (2 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 7 | `core/Modules/CryptoTrading/Providers/CryptoTradingServiceProvider.php` | Coordinator B |
| 8 | `core/Modules/CryptoTrading/Providers/RouteServiceProvider.php` | Coordinator B |

#### Middleware (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 9 | `core/Modules/CryptoTrading/Http/Middleware/CryptoTradingMiddleWare.php` | Coordinator B |

#### Admin Controllers (4 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 10 | `core/Modules/CryptoTrading/Http/Controllers/Admin/AdminBotController.php` | Coordinator A |
| 11 | `core/Modules/CryptoTrading/Http/Controllers/Admin/AdminStakingController.php` | Coordinator A |
| 12 | `core/Modules/CryptoTrading/Http/Controllers/Admin/AdminTradingWalletController.php` | Coordinator A |
| 13 | `core/Modules/CryptoTrading/Http/Controllers/Admin/TradingSignalController.php` | Coordinator A |

#### User Controllers (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 14 | `core/Modules/CryptoTrading/Http/Controllers/User/CoinStakingController.php` | Coordinator C |

#### Installer Controller (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 15 | `core/Modules/CryptoTrading/Http/Controllers/CryptoTradingInstallerController.php` | Coordinator B |

#### Cron Controller (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 16 | `core/Modules/CryptoTrading/Http/Controllers/Cron/CryptoTradingCronController.php` | Coordinator C |

#### Helper (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 17 | `core/Modules/CryptoTrading/Helpers/AddonHelpers.php` | Coordinator C |

#### Entities/Models (12 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 18 | `core/Modules/CryptoTrading/Entities/DemoTradingLog.php` | Coordinator D |
| 19 | `core/Modules/CryptoTrading/Entities/DemoTradingWallet.php` | Coordinator D |
| 20 | `core/Modules/CryptoTrading/Entities/Staking.php` | Coordinator D |
| 21 | `core/Modules/CryptoTrading/Entities/StakingCoin.php` | Coordinator D |
| 22 | `core/Modules/CryptoTrading/Entities/TradingBot.php` | Coordinator D |
| 23 | `core/Modules/CryptoTrading/Entities/TradingBotActivation.php` | Coordinator D |
| 24 | `core/Modules/CryptoTrading/Entities/TradingBotTrade.php` | Coordinator D |
| 25 | `core/Modules/CryptoTrading/Entities/TradingCurrency.php` | Coordinator D |
| 26 | `core/Modules/CryptoTrading/Entities/TradingPair.php` | Coordinator D |
| 27 | `core/Modules/CryptoTrading/Entities/TradingSignal.php` | Coordinator D |
| 28 | `core/Modules/CryptoTrading/Entities/TradingWallet.php` | Coordinator D |
| 29 | `core/Modules/CryptoTrading/Entities/TradingWalletTransaction.php` | Coordinator D |

#### Migrations (16 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 30 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_12_235220_create_trading_wallets_table.php` | Coordinator D |
| 31 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_18_142048_create_trading_wallet_transactions_table.php` | Coordinator D |
| 32 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_18_232321_create_trading_signals_table.php` | Coordinator D |
| 33 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_19_234113_create_trading_pairs_table.php` | Coordinator D |
| 34 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_20_162143_create_trading_logs_table.php` | Coordinator D |
| 35 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_20_215535_create_stakings_table.php` | Coordinator D |
| 36 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_21_193109_create_stakings_table.php` | Coordinator D |
| 37 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_22_121454_create_trading_bots_table.php` | Coordinator D |
| 38 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_22_121916_create_trading_bot_activations_table.php` | Coordinator D |
| 39 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_29_002937_add_user_activated_table.php` | Coordinator D |
| 40 | `core/Modules/CryptoTrading/Database/Migrations/2022_10_29_114155_create_trading_bot_trades_table.php` | Coordinator D |
| 41 | `core/Modules/CryptoTrading/Database/Migrations/2022_11_01_223442_trading_bot_trades.php` | Coordinator D |
| 42 | `core/Modules/CryptoTrading/Database/Migrations/2022_12_02_020259_create_demo_trading_wallets_table.php` | Coordinator D |
| 43 | `core/Modules/CryptoTrading/Database/Migrations/2022_12_02_021951_create_demo_trading_logs_table.php` | Coordinator D |
| 44 | `core/Modules/CryptoTrading/Database/Migrations/2022_12_03_220832_add_to_demo.php` | Coordinator D |
| 45 | `core/Modules/CryptoTrading/Database/Migrations/2022_12_07_022611_add_to_signals.php` | Coordinator D |

#### Seeder (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 46 | `core/Modules/CryptoTrading/Database/Seeders/CryptoTradingDatabaseSeeder.php` | Coordinator D |

#### Config (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 47 | `core/Modules/CryptoTrading/Config/config.php` | Coordinator B |

### 2.3 KYC Module (11 encoded files)

#### Routes (2 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 48 | `core/Modules/KYC/Routes/api.php` | Coordinator E |
| 49 | `core/Modules/KYC/Routes/web.php` | Coordinator E |

#### Providers (2 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 50 | `core/Modules/KYC/Providers/KYCServiceProvider.php` | Coordinator E |
| 51 | `core/Modules/KYC/Providers/RouteServiceProvider.php` | Coordinator E |

#### Middleware (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 52 | `core/Modules/KYC/Http/Middleware/KycMiddleware.php` | Coordinator E |

#### Controllers (3 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 53 | `core/Modules/KYC/Http/Controllers/Admin/AdminIdController.php` | Coordinator E |
| 54 | `core/Modules/KYC/Http/Controllers/KycInstallerController.php` | Coordinator E |
| 55 | `core/Modules/KYC/Http/Controllers/User/IdController.php` | Coordinator E |

#### Entity/Model (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 56 | `core/Modules/KYC/Entities/IdVerification.php` | Coordinator E |

#### Seeder (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 57 | `core/Modules/KYC/Database/Seeders/KYCDatabaseSeeder.php` | Coordinator E |

#### Config (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 58 | `core/Modules/KYC/Config/config.php` | Coordinator E |

### 2.4 ManualDeposit Module (10 encoded files)

#### Routes (2 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 59 | `core/Modules/ManualDeposit/Routes/api.php` | Coordinator F |
| 60 | `core/Modules/ManualDeposit/Routes/web.php` | Coordinator F |

#### Providers (2 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 61 | `core/Modules/ManualDeposit/Providers/ManualDepositServiceProvider.php` | Coordinator F |
| 62 | `core/Modules/ManualDeposit/Providers/RouteServiceProvider.php` | Coordinator F |

#### Middleware (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 63 | `core/Modules/ManualDeposit/Http/Middleware/ManualDepositMiddleware.php` | Coordinator F |

#### Controllers (3 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 64 | `core/Modules/ManualDeposit/Http/Controllers/Admin/DepositMethodController.php` | Coordinator F |
| 65 | `core/Modules/ManualDeposit/Http/Controllers/ManualDepositInstallerController.php` | Coordinator F |
| 66 | `core/Modules/ManualDeposit/Http/Controllers/User/ManualDepositController.php` | Coordinator F |

#### Seeder (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 67 | `core/Modules/ManualDeposit/Database/Seeders/ManualDepositDatabaseSeeder.php` | Coordinator F |

#### Config (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 68 | `core/Modules/ManualDeposit/Config/config.php` | Coordinator F |

### 2.5 P2pTransfer Module (11 encoded files)

#### Routes (2 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 69 | `core/Modules/P2pTransfer/Routes/api.php` | Coordinator G |
| 70 | `core/Modules/P2pTransfer/Routes/web.php` | Coordinator G |

#### Providers (2 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 71 | `core/Modules/P2pTransfer/Providers/P2pTransferServiceProvider.php` | Coordinator G |
| 72 | `core/Modules/P2pTransfer/Providers/RouteServiceProvider.php` | Coordinator G |

#### Middleware (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 73 | `core/Modules/P2pTransfer/Http/Middleware/P2pTransferMiddleware.php` | Coordinator G |

#### Controllers (3 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 74 | `core/Modules/P2pTransfer/Http/Controllers/Admin/AdminBalanceTransferController.php` | Coordinator G |
| 75 | `core/Modules/P2pTransfer/Http/Controllers/P2pTransferInstallerController.php` | Coordinator G |
| 76 | `core/Modules/P2pTransfer/Http/Controllers/User/BalanceTransferController.php` | Coordinator G |

#### Entity/Model (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 77 | `core/Modules/P2pTransfer/Entities/Transfer.php` | Coordinator G |

#### Seeder (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 78 | `core/Modules/P2pTransfer/Database/Seeders/P2pTransferDatabaseSeeder.php` | Coordinator G |

#### Config (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 79 | `core/Modules/P2pTransfer/Config/config.php` | Coordinator G |

### 2.6 SupportTicket Module (13 encoded files)

#### Routes (2 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 80 | `core/Modules/SupportTicket/Routes/api.php` | Coordinator H |
| 81 | `core/Modules/SupportTicket/Routes/web.php` | Coordinator H |

#### Providers (2 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 82 | `core/Modules/SupportTicket/Providers/RouteServiceProvider.php` | Coordinator H |
| 83 | `core/Modules/SupportTicket/Providers/SupportTicketServiceProvider.php` | Coordinator H |

#### Middleware (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 84 | `core/Modules/SupportTicket/Http/Middleware/SupportTicketMiddleware.php` | Coordinator H |

#### Controllers (3 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 85 | `core/Modules/SupportTicket/Http/Controllers/Admin/AdminTicketController.php` | Coordinator H |
| 86 | `core/Modules/SupportTicket/Http/Controllers/SupportTicketInstallerController.php` | Coordinator H |
| 87 | `core/Modules/SupportTicket/Http/Controllers/User/TicketController.php` | Coordinator H |

#### Entities/Models (3 files)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 88 | `core/Modules/SupportTicket/Entities/SupportTicket.php` | Coordinator H |
| 89 | `core/Modules/SupportTicket/Entities/SupportTicketAttachment.php` | Coordinator H |
| 90 | `core/Modules/SupportTicket/Entities/SupportTicketReply.php` | Coordinator H |

#### Seeder (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 91 | `core/Modules/SupportTicket/Database/Seeders/SupportTicketDatabaseSeeder.php` | Coordinator H |

#### Config (1 file)
| # | File Path | Rewrite Assignment |
|---|-----------|-------------------|
| 92 | `core/Modules/SupportTicket/Config/config.php` | Coordinator H |

---

## 3. Already Rewritten Native Code (Reference Patterns)

The following files in `core/` have already been rewritten as native PHP and serve as the **architectural reference patterns** for all remaining rewrites:

### 3.1 Native Models (`core/app/Models/Trading/`)
- `TradingWallet.php` — Eloquent model, `$table = 'trading_wallets'`, `$fillable` array
- `TradingBot.php` — Same pattern, simple fillable model
- `Staking.php` — Same pattern
- `DemoTradingLog.php`, `DemoTradingWallet.php`, `StakingCoin.php`, `TradingBotActivation.php`, `TradingBotTrade.php`, `TradingLog.php`, `TradingPair.php`, `TradingWalletTransaction.php`

**Pattern:** Namespace `App\Models\Trading`, extends `Illuminate\Database\Eloquent\Model`, explicit `$table` and `$fillable`.

### 3.2 Native Support Service (`core/app/Support/Trading/OwnedTradingMarketData.php`)
- Replaces external API calls with Poloniex public API + hardcoded fallback prices
- Static methods: `currencies()`, `price()`, `ticker()`, `candles()`, `recentTrades()`, `prices()`, `fiatValue()`
- Uses Laravel `Http` facade with timeout and graceful fallback
- No dependency on `api.credcrypto.net`

### 3.3 Native Helper (`core/app/Helpers/ownedTradingHelper.php`)
- Functions: `ownedTradingEnabled()`, `ownedTradingWallets()`, `ownedTradingWalletFiatValue()`, `ownedTradingBotName()`, `ownedTradingBotActivated()`, `ownedTradingCurrentStake()`
- Uses `App\Models\Trading\*` models
- No external API calls

### 3.4 Native User Controllers (`core/app/Http/Controllers/User/`)
- `OwnedTradeController.php` — Full trade logic with DB transactions, `lockForUpdate()`, `firstOrCreateWallet()`, `recordCoinTransaction()`
- `OwnedCoinStakingController.php` — Staking logic
- `OwnedDemoController.php` — Demo trading logic
- `OwnedTradingWalletController.php` — Wallet management

**Pattern:** Namespace `App\Http\Controllers\User`, extends `Illuminate\Routing\Controller`, uses `request()` validation, `DB::transaction()` for atomicity, `OwnedTradingMarketData` for market data.

---

## 4. External API Call Inventory — `api.credcrypto.net` Removal

### 4.1 `AddonHelpers.php` — Functions Calling External API

| Function | API Endpoint | Purpose | Replacement Strategy |
|----------|-------------|---------|---------------------|
| `checkAddon($addon)` | `api.credcrypto.net/v1/addons/{addon}` | License verification | Return `['status' => 'ok']` always; remove license gating |
| `invalidLicense($message)` | N/A (writes to theme files) | Destroys theme layout files on license failure | Remove entirely; no destructive license enforcement |
| `updateAuthorizeNet()` | via `checkAddon('authorizenet')` | Payment method update | Remove `checkAddon` call; keep env update + model save |
| `updateCashmaal()` | via `checkAddon('cashmaal')` | Payment method update | Same pattern |
| `updateCoinbase()` | via `checkAddon('coinbase')` | Payment method update | Same pattern |
| `updateCoingate()` | via `checkAddon('coingate')` | Payment method update | Same pattern |
| `updateFlutterwave()` | via `checkAddon('flutterwave')` | Payment method update | Same pattern |
| `updateMonnify()` | via `checkAddon('monnify')` | Payment method update | Same pattern |
| `updatePayPal()` | via `checkAddon('paypal')` | Payment method update | Same pattern |
| `updateRazorPay()` | via `checkAddon('razorpay')` | Payment method update | Same pattern |
| `updateStripe()` | via `checkAddon('stripe')` | Payment method update | Same pattern |
| `processLoan($loan_id, $loan_action, $rpd)` | `env('BASE_CURL')/v1/loan` | Loan processing | Internal loan processing logic |
| `processUserId($document_id, $action, $comment)` | `env('BASE_CURL')/v1/id` | KYC verification | Internal KYC verification logic |

### 4.2 `generalHelper.php` — Functions Calling External API

| Function | API Endpoint | Purpose | Replacement Strategy |
|----------|-------------|---------|---------------------|
| `countryList()` | `env('BASE_CURL')/v1/country` | Country list | Hardcoded array or database table |
| `currencyConverter($ac, $mc, $amount)` | `env('BASE_CURL')/v1/convert/{ac}/{mc}/{amount}` | Currency conversion | Internal conversion using stored rates or CoinMarketCap |
| `getCurrency()` | `env('BASE_CURL')/v1/currency` | Currency list | Hardcoded array or database table |
| `fetchBlogs()` | `env('BASE_CURL')/v1/blog` | Blog fetching | Use local `Blog::all()` model |
| `purchaseCodeVerification($purchase_code)` | `env('BASE_CURL')/v1/purchase-code-verification` | License verification | Return success always; remove license gating |

### 4.3 Environment Variables to Remove

- `BASE_CURL` — Remove from `.env` and all references
- `BASE_TEST` — Remove from `.env` and all references

---

## 5. Rewrite Coordinator Assignments

| Coordinator | Module/Area | Files Count | Priority | Dependencies |
|-------------|------------|-------------|----------|--------------|
| **Coordinator A** | App Core Admin Controllers (Trading) | 4+4=8 | P1 — Critical | Requires Coordinator D models |
| **Coordinator B** | CryptoTrading Infrastructure (routes, providers, middleware, config, installer) | 8 | P1 — Critical | None (foundational) |
| **Coordinator C** | CryptoTrading User Controllers + Cron + Helper | 3 | P1 — Critical | Requires Coordinator D models |
| **Coordinator D** | CryptoTrading Entities/Models + Migrations + Seeder | 29 | P0 — Highest | None (foundational, all others depend on this) |
| **Coordinator E** | KYC Module | 11 | P2 — High | None |
| **Coordinator F** | ManualDeposit Module | 10 | P2 — High | None |
| **Coordinator G** | P2pTransfer Module | 11 | P3 — Medium | None (module disabled) |
| **Coordinator H** | SupportTicket Module | 13 | P2 — High | None |

### Execution Order

1. **Phase 0 (Foundation):** Coordinator D — Models, Migrations, Seeder
2. **Phase 1 (Infrastructure):** Coordinator B — Routes, Providers, Middleware, Config
3. **Phase 2 (Controllers):** Coordinators A + C — Admin and User Controllers
4. **Phase 3 (Parallel Modules):** Coordinators E, F, H — KYC, ManualDeposit, SupportTicket
5. **Phase 4 (Disabled Module):** Coordinator G — P2pTransfer
6. **Phase 5 (API Removal):** All coordinators — Remove `api.credcrypto.net` calls
7. **Phase 6 (Integration Testing):** All coordinators — End-to-end verification

---

## 6. Architectural Patterns for Rewritten Code

### 6.1 Model Pattern
```php
<?php
namespace App\Models\Trading;
use Illuminate\Database\Eloquent\Model;

class TradingWallet extends Model
{
    protected $table = 'trading_wallets';
    protected $fillable = ['user_id', 'symbol', 'balance', 'address', 'icon'];
}
```

### 6.2 Controller Pattern
```php
<?php
namespace App\Http\Controllers\User;
use App\Models\Trading\TradingWallet;
use App\Support\Trading\OwnedTradingMarketData;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class OwnedTradeController extends Controller
{
    public function trade()
    {
        $pair = $symbol_1 . '_' . $symbol_2;
        $pair_info = OwnedTradingMarketData::ticker($pair);
        return view('themes.' . websiteInfo('theme') . '.user.trade.trade.trade', compact(...));
    }
    
    public function tradeValidate()
    {
        request()->validate([...]);
        DB::transaction(function () use (...) {
            // Atomic balance mutations with lockForUpdate()
        });
        return back()->with('success', '...');
    }
}
```

### 6.3 Helper Pattern
```php
<?php
if (!function_exists('ownedTradingWallets')) {
    function ownedTradingWallets()
    {
        return TradingWallet::where('user_id', user('id'))->orderBy('symbol')->get();
    }
}
```

### 6.4 Market Data Service Pattern
- Use `App\Support\Trading\OwnedTradingMarketData` for all price/ticker/candle data
- Poloniex public API as primary source with hardcoded fallback prices
- No external dependency on `api.credcrypto.net`
- All methods are static, return typed values
- Graceful degradation with try/catch

### 6.5 Route Pattern
```php
<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['web', 'auth', 'kyc'])->group(function () {
    Route::get('user/trading', [OwnedTradeController::class, 'index'])->name('user.trading.index');
});
```

### 6.6 Provider Pattern
```php
<?php
namespace Modules\CryptoTrading\Providers;
use Illuminate\Support\ServiceProvider;

class CryptoTradingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'cryptotrading');
    }
    
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'cryptotrading');
    }
}
```

### 6.7 Migration Pattern
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trading_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('symbol', 20);
            $table->decimal('balance', 18, 8)->default(0);
            $table->string('address', 64)->nullable();
            $table->string('icon', 50)->nullable();
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('trading_wallets');
    }
};
```

---

## 7. File Path Preservation Strategy

All rewritten files must maintain their existing paths to avoid:
- Autoloader breakage (PSR-4 namespace mapping)
- Route resolution failures
- View resolution failures
- Service provider registration failures
- Module dependency chain breakage

**Rule:** Replace the content of each encoded file in-place. Do not move or rename files.

---

## 8. Testing Verification Plan

After each phase of rewrites:

1. Run `php artisan route:list` to verify all routes resolve
2. Run `php artisan migrate` to verify all migrations execute
3. Run `php artisan tinker` to verify models instantiate
4. Browser smoke test: home, login, register, admin login
5. Authenticated smoke test: dashboard, trading pages, wallet pages
6. Admin smoke test: admin dashboard, bot management, staking management
7. Module-specific tests: KYC upload, manual deposit, support ticket creation

---

## 9. Risk Assessment

| Risk | Severity | Mitigation |
|------|----------|------------|
| Encoded migration schema mismatch | High | Reverse-engineer schema from database or readable model usage |
| Missing business logic in encoded controllers | High | Use readable view files, route names, and JS to infer functionality |
| External API removal breaks dependent features | Medium | Replace with internal logic before removing API calls |
| Module interdependency breakage | Medium | Rewrite in dependency order (models first, then controllers) |
| ionCube Loader requirement remains | Low | After all files are native, remove ionCube Loader from php.ini |

---

## 10. Post-Rewrite Cleanup

After all 92 files are rewritten:

1. Remove `ionCube Loader` from PHP configuration
2. Remove `BASE_CURL` and `BASE_TEST` from `.env` files
3. Remove `invalidLicense()` function entirely
4. Remove `checkAddon()` function or replace with no-op returning success
5. Remove `purchaseCodeVerification()` or replace with no-op
6. Remove all `CURLOPT_SSL_VERIFYHOST` and `CURLOPT_SSL_VERIFYPEER` false settings
7. Run full test suite without ionCube Loader
8. Verify application boots and all routes load without ionCube
9. Update `composer.json` and `phpunit.xml` if ionCube-specific config exists
10. Document the fully-owned codebase in updated README

---

## Document Status

- **Created:** 2026-06-19
- **Last Updated:** 2026-06-19
- **Status:** Active — Phase 0 (Foundation) ready to begin
- **Owner:** Trading-Wallet.net Development Team
