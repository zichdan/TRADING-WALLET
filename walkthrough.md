# Walkthrough — Step-by-Step ionCube Replacement Guide

## Trading-Wallet.net | Complete Rewrite Execution Manual

**Focus:** Step-by-step guide for rewriting all 92 ionCube-encoded PHP files into native PHP/Laravel and removing all `api.credcrypto.net` external API dependencies.

---

## Prerequisites

- PHP 8.1+ with ionCube Loader currently installed
- MySQL/MariaDB database with existing schema
- Composer dependencies installed
- Node.js and npm installed
- Access to the `current-trading-wallet/core/` and `core/` folders
- Backup of the entire project and database before starting

---

## Step 1: Backup and Preparation

### 1.1 Create a Full Backup
```bash
# Backup the entire project
cp -r current-trading-wallet/ current-trading-wallet-backup-$(date +%Y%m%d)/

# Backup the database
mysqldump -u root -p trading_wallet > backup_$(date +%Y%m%d).sql
```

### 1.2 Document the Current Route List
```bash
cd current-trading-wallet/core
php artisan route:list > pre-rewrite-routes.txt
```
This captures all existing routes for post-rewrite comparison.

### 1.3 Document the Current Database Schema
```bash
mysqldump -u root -p --no-data trading_wallet > pre-rewrite-schema.sql
```
Alternatively, connect to the database and run:
```sql
SHOW TABLES;
-- For each trading-related table:
SHOW CREATE TABLE trading_wallets;
SHOW CREATE TABLE trading_logs;
SHOW CREATE TABLE trading_bots;
SHOW CREATE TABLE trading_bot_activations;
SHOW CREATE TABLE trading_bot_trades;
SHOW CREATE TABLE trading_signals;
SHOW CREATE TABLE trading_pairs;
SHOW CREATE TABLE stakings;
SHOW CREATE TABLE staking_coins;
SHOW CREATE TABLE trading_wallet_transactions;
SHOW CREATE TABLE demo_trading_wallets;
SHOW CREATE TABLE demo_trading_logs;
```

### 1.4 Record ionCube-Encoded File List
```bash
# Find all files with ionCube markers
grep -rl "//002cd" current-trading-wallet/core/ > ioncube-files.txt
grep -rl "ionCube Loader" current-trading-wallet/core/ >> ioncube-files.txt
```

---

## Step 2: Phase 0 — Foundation (Models, Migrations, Seeder)

### 2.1 Reverse-Engineer Schema (Task T0.1)
Connect to the database and document every trading-related table's schema. Create a reference file:

```
core/database/schema-reference.md
```

List each table with: column name, type, nullable, default, indexes, foreign keys.

### 2.2 Rewrite Trading Models (Tasks T0.2–T0.13)

For each encoded entity file in `Modules/CryptoTrading/Entities/`, create or verify a native model at `app/Models/Trading/`.

**Already rewritten (verify only):**
- `TradingWallet.php` — verify `$table = 'trading_wallets'` and `$fillable` match schema
- `TradingBot.php` — verify `$table = 'trading_bots'` and `$fillable` match schema
- `Staking.php` — verify `$table = 'stakings'` and `$fillable` match schema
- `TradingBotActivation.php` — verify and sync
- `TradingBotTrade.php` — verify and sync
- `TradingLog.php` — verify and sync
- `TradingPair.php` — verify and sync
- `TradingWalletTransaction.php` — verify and sync
- `DemoTradingLog.php` — verify and sync
- `DemoTradingWallet.php` — verify and sync

**Need creation:**
- `StakingCoin.php` — create at `app/Models/Trading/StakingCoin.php`
- `TradingSignal.php` — create at `app/Models/Trading/TradingSignal.php`
- `TradingCurrency.php` — create at `app/Models/Trading/TradingCurrency.php`

**Pattern to follow:**
```php
<?php
namespace App\Models\Trading;
use Illuminate\Database\Eloquent\Model;

class StakingCoin extends Model
{
    protected $table = 'staking_coins';
    protected $fillable = ['symbol', 'name', 'daily_return', 'minimum', 'maximum', 'status'];
}
```

### 2.3 Rewrite All 16 Encoded Migrations (Task T0.14)

For each encoded migration file in `Modules/CryptoTrading/Database/Migrations/`:

1. Read the schema reference from Step 2.1
2. Write a new native PHP migration that creates an identical table
3. Preserve the original filename for migration ordering
4. Use anonymous class syntax (Laravel 9 pattern)

**Pattern:**
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

**Handle the duplicate stakings migration:** Investigate which of the two stakings migrations (`2022_10_20_215535` and `2022_10_21_193109`) is the authoritative one by checking the live database schema. Merge into a single clean migration.

### 2.4 Rewrite the Database Seeder (Task T0.15)

Create `Modules/CryptoTrading/Database/Seeders/CryptoTradingDatabaseSeeder.php`:

```php
<?php
namespace Modules\CryptoTrading\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trading\TradingBot;
use App\Models\Trading\StakingCoin;
use App\Models\Trading\TradingPair;

class CryptoTradingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed trading bots
        TradingBot::create([
            'name' => 'Basic Bot',
            'price' => 0,
            'lose_count' => 3,
            'return_min' => 1,
            'return_max' => 5,
            'icon' => 'bot-basic.svg',
            'status' => 'enabled',
        ]);
        
        // Seed staking coins
        // Seed trading pairs
        // Infer data from Blade views and JS files
    }
}
```

### 2.5 Create Compatibility Migration (Task T0.16)

Create `2026_06_18_060000_add_owned_trading_schema_compatibility.php` to add any missing columns:

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add columns that rewritten models expect but encoded migrations may not have created
        // Check each model's $fillable against the schema reference
    }
    public function down(): void {}
};
```

### 2.6 Verify Phase 0
```bash
# Run migrations on a test database
php artisan migrate:fresh

# Run seeders
php artisan db:seed --class=CryptoTradingDatabaseSeeder

# Test model instantiation
php artisan tinker
>>> App\Models\Trading\TradingWallet::first();
>>> App\Models\Trading\TradingBot::first();
>>> App\Models\Trading\Staking::first();
```

---

## Step 3: Phase 1 — Infrastructure (Routes, Providers, Middleware, Config)

### 3.1 Rewrite CryptoTrading Routes (Tasks T1.1–T1.2)

Rewrite `Modules/CryptoTrading/Routes/web.php`:

```php
<?php
use Illuminate\Support\Facades\Route;
use Modules\CryptoTrading\Http\Controllers\User\CoinStakingController;
use Modules\CryptoTrading\Http\Controllers\Cron\CryptoTradingCronController;

Route::middleware(['web', 'auth'])->group(function () {
    // Trading routes — preserve all existing route names
    Route::get('user/trading', 'OwnedTradeController@index')->name('user.trading.index');
    Route::get('user/trading/{symbol1}/{symbol2}', 'OwnedTradeController@trade')->name('user.trading.trade');
    Route::post('user/trading/validate', 'OwnedTradeController@tradeValidate')->name('user.trading.validate');
    Route::post('user/trading/end', 'OwnedTradeController@endTrade')->name('user.trading.end');
    Route::get('user/trading/{symbol1}/{symbol2}/bot', 'OwnedTradeController@bot')->name('user.trading.bot');
    Route::post('user/trading/bot/activate', 'OwnedTradeController@botActivate')->name('user.trading.bot.activate');
    Route::post('user/trading/bot/trade', 'OwnedTradeController@botTrade')->name('user.trading.bot.trade');
    
    // Staking routes
    Route::get('user/staking', [CoinStakingController::class, 'index'])->name('user.staking.index');
    Route::post('user/staking', [CoinStakingController::class, 'store'])->name('user.staking.store');
    Route::post('user/staking/claim', [CoinStakingController::class, 'claim'])->name('user.staking.claim');
});

// Cron routes (no auth, protected by secret token)
Route::get('cron/trading', [CryptoTradingCronController::class, 'handle'])->name('cron.trading');
```

**Critical:** Match every route name exactly to the pre-rewrite route list from Step 1.2.

### 3.2 Rewrite Service Providers (Tasks T1.3–T1.4)

Rewrite `Modules/CryptoTrading/Providers/CryptoTradingServiceProvider.php`:

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
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }
}
```

Rewrite `RouteServiceProvider.php`:
```php
<?php
namespace Modules\CryptoTrading\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();
    }
    
    public function map(): void
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }
    
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Modules\CryptoTrading\Http\Controllers')
            ->group(__DIR__ . '/../Routes/web.php');
    }
    
    protected function mapApiRoutes(): void
    {
        Route::middleware('api')
            ->namespace('Modules\CryptoTrading\Http\Controllers')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
```

### 3.3 Rewrite Middleware (Task T1.5)

Rewrite `Modules/CryptoTrading/Http/Middleware/CryptoTradingMiddleWare.php`:

```php
<?php
namespace Modules\CryptoTrading\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CryptoTradingMiddleWare
{
    public function handle(Request $request, Closure $next)
    {
        // Simple access control — no license verification
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
```

### 3.4 Rewrite Module Config (Task T1.6)

Rewrite `Modules/CryptoTrading/Config/config.php`:

```php
<?php
return [
    'name' => 'CryptoTrading',
    'description' => 'Cryptocurrency trading module',
    'version' => '1.0.0',
    'enabled' => true,
];
```

### 3.5 Verify Phase 1
```bash
php artisan route:list | grep trading
# Compare with pre-rewrite-routes.txt
```

---

## Step 4: Phase 2 — Controllers (Admin and User)

### 4.1 Rewrite Admin Controllers (Tasks T2.1–T2.5)

For each encoded admin controller in `core/app/Http/Controllers/Admin/`:

**AdminBotController.php pattern:**
```php
<?php
namespace App\Http\Controllers\Admin;

use App\Models\Trading\TradingBot;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminBotController extends Controller
{
    public function index()
    {
        $bots = TradingBot::orderBy('id', 'DESC')->get();
        $page_title = 'Trading Bots';
        return view('admin.trading.bots.index', compact('bots', 'page_title'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|gt:0',
            'lose_count' => 'required|integer|gt:0',
            'return_min' => 'required|numeric|gt:0',
            'return_max' => 'required|numeric|gt:0',
            'icon' => 'nullable|string|max:255',
        ]);
        
        TradingBot::create($request->only(['name', 'price', 'lose_count', 'return_min', 'return_max', 'icon']) + ['status' => 'enabled']);
        return back()->with('success', 'Bot created successfully');
    }
    
    public function update(Request $request, $id)
    {
        $bot = TradingBot::findOrFail($id);
        $request->validate([...]);
        $bot->update($request->only([...]));
        return back()->with('success', 'Bot updated successfully');
    }
    
    public function destroy($id)
    {
        TradingBot::findOrFail($id)->delete();
        return back()->with('success', 'Bot deleted successfully');
    }
    
    public function status($id)
    {
        $bot = TradingBot::findOrFail($id);
        $bot->status = $bot->status === 'enabled' ? 'disabled' : 'enabled';
        $bot->save();
        return back()->with('success', 'Bot status updated');
    }
}
```

Apply similar patterns to:
- `AdminStakingController.php` — CRUD for staking coins
- `AdminTradingWalletController.php` — List/view user wallets
- `TradingSignalController.php` — CRUD for trading signals

### 4.2 Rewrite User Controllers (Tasks T2.6–T2.8)

**CoinStakingController.php pattern:**
```php
<?php
namespace Modules\CryptoTrading\Http\Controllers\User;

use App\Models\Trading\Staking;
use App\Models\Trading\StakingCoin;
use App\Models\Trading\TradingWallet;
use App\Support\Trading\OwnedTradingMarketData;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoinStakingController extends Controller
{
    public function index()
    {
        $coins = StakingCoin::where('status', 'enabled')->get();
        $stakes = Staking::where('user_id', user('id'))->get();
        $page_title = 'Coin Staking';
        return view('cryptotrading::user.staking.index', compact('coins', 'stakes', 'page_title'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'coin_id' => 'required|integer',
            'amount' => 'required|numeric|gt:0',
        ]);
        
        $coin = StakingCoin::where('status', 'enabled')->findOrFail($request->coin_id);
        
        if ($request->amount < $coin->minimum || $request->amount > $coin->maximum) {
            return back()->with('fail', 'Amount out of staking range');
        }
        
        DB::transaction(function () use ($coin, $request) {
            $wallet = TradingWallet::where('user_id', user('id'))
                ->where('symbol', $coin->symbol)
                ->lockForUpdate()
                ->first();
            
            if (!$wallet || (float) $wallet->balance < $request->amount) {
                abort(422, 'Insufficient balance');
            }
            
            $wallet->balance = (float) $wallet->balance - $request->amount;
            $wallet->save();
            
            Staking::create([
                'user_id' => user('id'),
                'coin_id' => $coin->id,
                'amount' => $request->amount,
                'staked' => 1,
                'daily_return' => $coin->daily_return,
                'returned' => 0,
                'returnable' => 0,
                'next_return' => now()->addDay()->timestamp,
                'last_return' => now()->timestamp,
            ]);
        });
        
        return back()->with('success', 'Staked successfully');
    }
}
```

### 4.3 Rewrite Cron Controller (Task T2.7)

```php
<?php
namespace Modules\CryptoTrading\Http\Controllers\Cron;

use App\Models\Trading\Staking;
use App\Models\Trading\TradingBotTrade;
use App\Support\Trading\OwnedTradingMarketData;
use Illuminate\Routing\Controller;

class CryptoTradingCronController extends Controller
{
    public function handle()
    {
        $this->processStakingReturns();
        $this->processBotTrades();
        echo "EXECUTED";
    }
    
    private function processStakingReturns(): void
    {
        $stakes = Staking::where('staked', 1)
            ->where('next_return', '<=', now()->timestamp)
            ->get();
        
        foreach ($stakes as $stake) {
            // Calculate return, update stake, credit wallet
        }
    }
    
    private function processBotTrades(): void
    {
        $trades = TradingBotTrade::where('status', 'running')
            ->where('next_trade_time', '<=', now()->timestamp)
            ->get();
        
        foreach ($trades as $trade) {
            // Execute bot trade using OwnedTradingMarketData
        }
    }
}
```

### 4.4 Verify Phase 2
```bash
# Test admin routes
php artisan tinker
>>> route('admin.trading.bots.index');

# Test user routes
>>> route('user.trading.index');
>>> route('user.staking.index');
```

---

## Step 5: Phase 3 — Parallel Modules (KYC, ManualDeposit, SupportTicket)

### 5.1 KYC Module (Tasks T3.1–T3.6)

**Rewrite `IdVerification` model:**
```php
<?php
namespace Modules\KYC\Entities;

use Illuminate\Database\Eloquent\Model;

class IdVerification extends Model
{
    protected $table = 'id_verifications';
    protected $fillable = ['user_id', 'document_type', 'document_front', 'document_back', 'status', 'comment'];
}
```

**Rewrite `IdController` (user):**
```php
<?php
namespace Modules\KYC\Http\Controllers\User;

use Modules\KYC\Entities\IdVerification;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class IdController extends Controller
{
    public function index()
    {
        $verification = IdVerification::where('user_id', user('id'))->first();
        $page_title = 'KYC Verification';
        return view('kyc::user.index', compact('verification', 'page_title'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string',
            'document_front' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'document_back' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        // Store files securely
        $frontPath = $request->file('document_front')->store('kyc/' . user('id'), 'private');
        $backPath = $request->file('document_back')?->store('kyc/' . user('id'), 'private');
        
        IdVerification::updateOrCreate(
            ['user_id' => user('id')],
            [
                'document_type' => $request->document_type,
                'document_front' => $frontPath,
                'document_back' => $backPath,
                'status' => 'pending',
            ]
        );
        
        return back()->with('success', 'Documents submitted for verification');
    }
}
```

**Rewrite `AdminIdController`:**
```php
<?php
namespace Modules\KYC\Http\Controllers\Admin;

use Modules\KYC\Entities\IdVerification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminIdController extends Controller
{
    public function index()
    {
        $verifications = IdVerification::orderBy('id', 'DESC')->get();
        $page_title = 'KYC Verifications';
        return view('kyc::admin.index', compact('verifications', 'page_title'));
    }
    
    public function approve(Request $request, $id)
    {
        $verification = IdVerification::findOrFail($id);
        $verification->status = 'approved';
        $verification->comment = $request->comment ?? '';
        $verification->save();
        
        // Update user's id_verified status
        $verification->user()->update(['id_verified' => 1]);
        
        return back()->with('success', 'KYC approved');
    }
    
    public function reject(Request $request, $id)
    {
        $verification = IdVerification::findOrFail($id);
        $verification->status = 'rejected';
        $verification->comment = $request->comment ?? '';
        $verification->save();
        
        return back()->with('success', 'KYC rejected');
    }
}
```

### 5.2 ManualDeposit Module (Tasks T3.7–T3.11)

Follow the same pattern: rewrite routes, providers, middleware, controllers, seeder, config. Remove `checkAddon()` calls from deposit method update functions.

### 5.3 SupportTicket Module (Tasks T3.12–T3.17)

**Rewrite SupportTicket models:**
```php
<?php
namespace Modules\SupportTicket\Entities;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $table = 'support_tickets';
    protected $fillable = ['user_id', 'subject', 'status', 'priority'];
    
    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id');
    }
    
    public function attachments()
    {
        return $this->hasMany(SupportTicketAttachment::class, 'ticket_id');
    }
}
```

**Rewrite TicketController (user):**
```php
<?php
namespace Modules\SupportTicket\Http\Controllers\User;

use Modules\SupportTicket\Entities\SupportTicket;
use Modules\SupportTicket\Entities\SupportTicketReply;
use Modules\SupportTicket\Entities\SupportTicketAttachment;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', user('id'))->orderBy('id', 'DESC')->get();
        $page_title = 'Support Tickets';
        return view('supportticket::user.index', compact('tickets', 'page_title'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);
        
        $ticket = SupportTicket::create([
            'user_id' => user('id'),
            'subject' => $request->subject,
            'status' => 'open',
            'priority' => $request->priority,
        ]);
        
        SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => user('id'),
            'message' => $request->message,
        ]);
        
        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/' . $ticket->id, 'private');
                SupportTicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }
        
        return back()->with('success', 'Ticket created successfully');
    }
}
```

---

## Step 6: Phase 4 — P2pTransfer Module (Tasks T4.1–T4.6)

Rewrite all P2pTransfer files following the same patterns. **Critical fix:** Use DB transactions with `lockForUpdate()` for all balance transfers:

```php
<?php
namespace Modules\P2pTransfer\Http\Controllers\User;

use App\Models\User;
use Modules\P2pTransfer\Entities\Transfer;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceTransferController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'amount' => 'required|numeric|gt:0',
        ]);
        
        DB::transaction(function () use ($request) {
            $sender = User::where('id', user('id'))->lockForUpdate()->first();
            $receiver = User::where('id', $request->receiver_id)->lockForUpdate()->first();
            
            if (!$receiver) {
                abort(422, 'Receiver not found');
            }
            
            if ((float) $sender->balance < $request->amount) {
                abort(422, 'Insufficient balance');
            }
            
            $fee = ($request->amount / 100) * gs()->transfer_fee;
            $total = $request->amount + $fee;
            
            $sender->balance = (float) $sender->balance - $total;
            $sender->save();
            
            $receiver->balance = (float) $receiver->balance + $request->amount;
            $receiver->save();
            
            Transfer::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'amount' => $request->amount,
                'fee' => $fee,
                'total' => $total,
                'status' => 'completed',
                'txn_id' => uniqid('TRF'),
            ]);
        });
        
        return back()->with('success', 'Transfer completed');
    }
}
```

---

## Step 7: Phase 5 — External API Removal (Tasks T5.1–T5.14)

### 7.1 Remove `checkAddon()` (Task T5.1)

In `AddonHelpers.php`, replace the function body:

```php
function checkAddon($addon)
{
    // No-op: application is fully owned, no external license verification needed
    return ['status' => 'ok'];
}
```

### 7.2 Remove `invalidLicense()` (Task T5.2)

Delete the entire `invalidLicense()` function from `AddonHelpers.php`. Remove all references to it.

### 7.3 Remove `update*()` External Calls (Task T5.3)

For each `update*()` function (updateAuthorizeNet, updateCashmaal, updateCoinbase, etc.):

1. Remove the `checkAddon()` call
2. Remove the `$module->disable()` call
3. Remove the `invalidLicense()` call
4. Keep only the env update and model save logic

**Before:**
```php
function updateAuthorizeNet($request) {
    $addon = checkAddon('authorizenet');
    if ($addon['status'] !== 'ok') {
        $module = Module::find('ManualDeposit');
        $module->disable();
        invalidLicense('AuthorizeNet module disabled');
    }
    // ... env update and save
}
```

**After:**
```php
function updateAuthorizeNet($request) {
    // ... env update and save only
}
```

### 7.4 Replace `countryList()` (Task T5.4)

In `generalHelper.php`:

```php
function countryList() {
    return [
        'US' => 'United States',
        'GB' => 'United Kingdom',
        'CA' => 'Canada',
        'AU' => 'Australia',
        'DE' => 'Germany',
        'FR' => 'France',
        // ... full country list
    ];
}
```

### 7.5 Replace `currencyConverter()` (Task T5.5)

```php
function currencyConverter($ac, $mc, $amount) {
    // Use stored exchange rates or CoinMarketCap API
    $gs = gs();
    $rate = \App\Models\CryptoCurrency::where('symbol', $ac)->value('price') ?? 1;
    $mcRate = 1; // Fiat conversion rate
    return $amount * $rate * $mcRate;
}
```

### 7.6 Replace `getCurrency()` (Task T5.6)

```php
function getCurrency() {
    return [
        ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
        ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
        ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
        // ... full currency list
    ];
}
```

### 7.7 Replace `fetchBlogs()` (Task T5.7)

```php
function fetchBlogs() {
    return \App\Models\Blog::all()->toArray();
}
```

### 7.8 Replace `processLoan()` (Task T5.8)

Replace external API call with internal logic:
```php
function processLoan($loan_id, $loan_action, $rpd) {
    $loan = \App\Models\Loan::findOrFail($loan_id);
    
    if ($loan_action === 'approve') {
        $loan->status = 'approved';
        $loan->save();
        
        $user = User::find($loan->user_id);
        $user->balance += $loan->amount;
        $user->save();
        
        // Record transaction
    } elseif ($loan_action === 'reject') {
        $loan->status = 'rejected';
        $loan->save();
    }
}
```

### 7.9 Replace `processUserId()` (Task T5.9)

Replace external API call with internal logic:
```php
function processUserId($document_id, $action, $comment) {
    $verification = \Modules\KYC\Entities\IdVerification::findOrFail($document_id);
    
    if ($action === 'approve') {
        $verification->status = 'approved';
        $verification->comment = $comment;
        $verification->save();
        
        User::where('id', $verification->user_id)->update(['id_verified' => 1]);
    } elseif ($action === 'reject') {
        $verification->status = 'rejected';
        $verification->comment = $comment;
        $verification->save();
    }
}
```

### 7.10 Replace `pairPrice()` (Task T5.10)

```php
function pairPrice($pair) {
    return \App\Support\Trading\OwnedTradingMarketData::price($pair);
}
```

### 7.11 Remove `purchaseCodeVerification()` (Task T5.11)

```php
function purchaseCodeVerification($purchase_code) {
    return (object) ['status' => 'ok', 'message' => 'Verified'];
}
```

### 7.12 Remove Environment Variables (Task T5.12)

Edit `.env`, `.env.example`, and `.env.testing`:
- Remove `BASE_CURL=https://api.credcrypto.net`
- Remove `BASE_TEST=...`

### 7.13 Remove SSL Verification Disabling (Task T5.13)

Search and remove all:
```php
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
```

Replace cURL calls with Laravel `Http` facade where possible.

### 7.14 Full External API Audit (Task T5.14)

```bash
grep -r "api.credcrypto.net" core/
grep -r "BASE_CURL" core/
grep -r "BASE_TEST" core/
grep -r "credcrypto" core/
# Expected: zero results
```

---

## Step 8: Phase 6 — Integration Testing & Verification

### 8.1 Remove ionCube Loader (Task T6.1)

Edit `php.ini`:
```ini
; Remove or comment out this line:
; zend_extension = /path/to/ioncube_loader.so
```

Restart PHP/Web server.

### 8.2 Full Route Audit (Task T6.2)
```bash
php artisan route:list > post-rewrite-routes.txt
diff pre-rewrite-routes.txt post-rewrite-routes.txt
# Expected: no missing routes
```

### 8.3 Full Migration Test (Task T6.3)
```bash
php artisan migrate:fresh
php artisan db:seed
```

### 8.4 PHPUnit Tests (Task T6.4)
```bash
php artisan test
# or
./vendor/bin/phpunit
```

### 8.5 Playwright Browser Tests (Task T6.5)
```bash
npx playwright test
```

Test the following user journeys:
1. Home page loads
2. User registration and login
3. User dashboard
4. Trading page (chart, order form, trade history)
5. Wallet page (balances, transactions)
6. Staking page (stake coins, view stakes)
7. KYC upload page
8. Manual deposit page
9. Support ticket creation
10. Admin login and dashboard
11. Admin trading bot management
12. Admin staking management
13. Admin KYC review
14. Admin support ticket management

### 8.6 ionCube Marker Audit (Task T6.6)
```bash
grep -rl "//002cd" core/
grep -rl "ionCube" core/ --include="*.php"
# Expected: zero results in .php files
```

### 8.7 Financial Integrity Test (Task T6.7)
Test concurrent operations:
- Multiple simultaneous trades
- Multiple simultaneous transfers
- Concurrent deposits and withdrawals
- Verify all balances are correct after concurrent operations

### 8.8 Final Sign-Off (Task T6.8)

**Acceptance criteria checklist:**
- [ ] Application boots without ionCube Loader
- [ ] All routes resolve (`php artisan route:list` matches pre-rewrite)
- [ ] All migrations run (`php artisan migrate:fresh` succeeds)
- [ ] All seeders run (`php artisan db:seed` succeeds)
- [ ] All PHPUnit tests pass
- [ ] All Playwright browser tests pass
- [ ] No `api.credcrypto.net` references in codebase
- [ ] No `BASE_CURL` or `BASE_TEST` in `.env` files
- [ ] No ionCube markers in PHP files
- [ ] No SSL verification disabling in PHP files
- [ ] Trading pages functional (chart, order, history)
- [ ] Wallet pages functional (balances, transactions)
- [ ] Staking pages functional (stake, claim)
- [ ] KYC upload and review functional
- [ ] Manual deposit functional
- [ ] Support ticket creation and reply functional
- [ ] Admin panel fully functional
- [ ] Cron jobs execute without errors

---

## Step 9: Post-Rewrite Cleanup

### 9.1 Update Documentation
- Update `TRADING-WALLET-IONCUBE-INVENTORY.MD` — mark all files as "Rewritten — Native PHP"
- Update `TRADING-WALLET.MD` — remove ionCube references from architecture overview
- Update `TRADING-WALLET-TESTING.MD` — remove ionCube loader setup instructions
- Create changelog listing all rewritten files

### 9.2 Clean Up PHP Configuration
- Remove ionCube Loader from `php.ini`
- Remove ionCube-specific `.user.ini` settings
- Remove `ioncube` from any Composer dependencies

### 9.3 Security Hardening
- Enable TLS verification on all HTTP calls
- Remove any hardcoded API keys from PHP files (move to `.env`)
- Audit file permissions on all rewritten files
- Run `composer audit` for dependency vulnerabilities

### 9.4 Performance Optimization
- Add database indexes on frequently queried columns
- Enable Laravel config caching: `php artisan config:cache`
- Enable route caching: `php artisan route:cache`
- Enable view caching: `php artisan view:cache`

---

## Quick Reference: File Rewrite Checklist

For each encoded file, follow this checklist:

1. **Read** the encoded file to confirm it's ionCube-protected (starts with `<?php //002cd`)
2. **Identify** the file's purpose from its path, route name, or view references
3. **Determine** the correct native PHP pattern (model, controller, migration, etc.)
4. **Write** the native replacement following established patterns
5. **Verify** the file is syntactically valid: `php -l path/to/file.php`
6. **Test** the file's functionality via route or tinker
7. **Mark** the file as rewritten in the inventory

---

## Troubleshooting

### Application won't boot after removing ionCube
- Check `php -m` to confirm ionCube is not loaded
- Check `php artisan route:list` for failing routes
- Check Laravel log: `storage/logs/laravel.log`
- Verify all module service providers are registered in `modules_statuses.json`

### Migration fails
- Check for duplicate migration class names
- Check for missing dependencies (foreign key references)
- Verify column types match existing data

### Route not found
- Verify the route file is loaded by the service provider
- Check route name spelling against pre-rewrite route list
- Verify controller class exists and is autoloadable

### Model not found
- Check namespace matches directory structure (PSR-4)
- Run `composer dump-autoload`
- Verify `$table` property matches database table name

### External API call still present
- Run `grep -r "api.credcrypto.net" core/` to find remaining references
- Run `grep -r "BASE_CURL" core/` to find env variable references
- Check helper files: `AddonHelpers.php`, `generalHelper.php`

---

## Document Status

- **Created:** 2026-06-19
- **Focus:** Step-by-step ionCube replacement and external API removal
- **Status:** Active — Ready for execution
- **Owner:** Trading-Wallet.net Development Team
