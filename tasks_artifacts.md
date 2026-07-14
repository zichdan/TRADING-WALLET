# Tasks & Artifacts — ionCube Replacement Project

## Trading-Wallet.net | Task Breakdown for Full ionCube Rewrite

**Focus:** Complete replacement of all 92 ionCube-encoded PHP files and removal of all `api.credcrypto.net` external API dependencies.

---

## Task ID Convention

Tasks are numbered as `T{phase}.{sequence}`. Phases map to the implementation plan phases:
- Phase 0: Foundation (Models, Migrations, Seeder)
- Phase 1: Infrastructure (Routes, Providers, Middleware, Config)
- Phase 2: Controllers (Admin and User)
- Phase 3: Parallel Modules (KYC, ManualDeposit, SupportTicket)
- Phase 4: Disabled Module (P2pTransfer)
- Phase 5: External API Removal
- Phase 6: Integration Testing & Verification

---

## Phase 0: Foundation — Models, Migrations, Seeder

### T0.1 — Reverse-Engineer Database Schema
- **Coordinator:** D
- **Priority:** P0 (Critical Path)
- **Dependencies:** None
- **Estimated effort:** 4 hours
- **Description:** Connect to the existing database and run `SHOW CREATE TABLE` for every trading-related table. Document all columns, types, indexes, and constraints.
- **Artifact:** `database/schema-reference.md` — complete schema documentation
- **Acceptance criteria:** Every trading table is documented with exact column definitions

### T0.2 — Rewrite TradingWallet Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Rewrite `Modules/CryptoTrading/Entities/TradingWallet.php` as native PHP. Already exists at `app/Models/Trading/TradingWallet.php` — verify and sync.
- **Artifact:** Native `TradingWallet.php` model
- **Acceptance criteria:** Model instantiates, `$table` and `$fillable` correct, CRUD works

### T0.3 — Rewrite TradingBot Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Rewrite as native PHP. Already exists at `app/Models/Trading/TradingBot.php` — verify and sync.
- **Artifact:** Native `TradingBot.php` model
- **Acceptance criteria:** Model instantiates, CRUD works

### T0.4 — Rewrite Staking Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Rewrite as native PHP. Already exists at `app/Models/Trading/Staking.php` — verify and sync.
- **Artifact:** Native `Staking.php` model
- **Acceptance criteria:** Model instantiates, CRUD works

### T0.5 — Rewrite StakingCoin Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Rewrite `Modules/CryptoTrading/Entities/StakingCoin.php` as native PHP at `app/Models/Trading/StakingCoin.php`.
- **Artifact:** Native `StakingCoin.php` model
- **Acceptance criteria:** Model instantiates, `$fillable` matches schema

### T0.6 — Rewrite TradingBotActivation Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Rewrite as native PHP at `app/Models/Trading/TradingBotActivation.php`.
- **Artifact:** Native `TradingBotActivation.php` model
- **Acceptance criteria:** Model instantiates, relationships to User and TradingBot defined

### T0.7 — Rewrite TradingBotTrade Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Rewrite as native PHP at `app/Models/Trading/TradingBotTrade.php`.
- **Artifact:** Native `TradingBotTrade.php` model
- **Acceptance criteria:** Model instantiates, `$fillable` matches schema

### T0.8 — Rewrite TradingPair Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Rewrite as native PHP at `app/Models/Trading/TradingPair.php`.
- **Artifact:** Native `TradingPair.php` model
- **Acceptance criteria:** Model instantiates, scope for active pairs defined

### T0.9 — Rewrite TradingSignal Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Rewrite as native PHP at `app/Models/Trading/TradingSignal.php`.
- **Artifact:** Native `TradingSignal.php` model
- **Acceptance criteria:** Model instantiates, `$fillable` matches schema

### T0.10 — Rewrite TradingCurrency Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Rewrite as native PHP at `app/Models/Trading/TradingCurrency.php`.
- **Artifact:** Native `TradingCurrency.php` model
- **Acceptance criteria:** Model instantiates, scope for active currencies

### T0.11 — Rewrite TradingWalletTransaction Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Already exists at `app/Models/Trading/TradingWalletTransaction.php` — verify and sync.
- **Artifact:** Native `TradingWalletTransaction.php` model
- **Acceptance criteria:** Model instantiates, CRUD works

### T0.12 — Rewrite DemoTradingLog Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Already exists at `app/Models/Trading/DemoTradingLog.php` — verify and sync.
- **Artifact:** Native `DemoTradingLog.php` model
- **Acceptance criteria:** Model instantiates, CRUD works

### T0.13 — Rewrite DemoTradingWallet Model
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 30 minutes
- **Description:** Already exists at `app/Models/Trading/DemoTradingWallet.php` — verify and sync.
- **Artifact:** Native `DemoTradingWallet.php` model
- **Acceptance criteria:** Model instantiates, CRUD works

### T0.14 — Rewrite All 16 Encoded Migrations
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.1
- **Estimated effort:** 8 hours
- **Description:** Rewrite all 16 encoded migration files using the schema reference from T0.1. Each migration must produce an identical table structure. Preserve original filenames for migration ordering.
- **Artifact:** 16 native PHP migration files
- **Acceptance criteria:** `php artisan migrate:fresh` succeeds, all tables created correctly

### T0.15 — Rewrite CryptoTrading Database Seeder
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.14
- **Estimated effort:** 2 hours
- **Description:** Rewrite the seeder to populate trading bots, staking coins, trading pairs, and trading currencies with initial data. Infer seed data from Blade views and JavaScript.
- **Artifact:** Native `CryptoTradingDatabaseSeeder.php`
- **Acceptance criteria:** `php artisan db:seed --class=CryptoTradingDatabaseSeeder` succeeds

### T0.16 — Create Compatibility Migration
- **Coordinator:** D
- **Priority:** P0
- **Dependencies:** T0.14
- **Estimated effort:** 1 hour
- **Description:** Create a new migration to add any missing columns required by rewritten models.
- **Artifact:** `2026_06_18_060000_add_owned_trading_schema_compatibility.php`
- **Acceptance criteria:** Migration runs without errors on existing database

### T0.17 — Write Model Unit Tests
- **Coordinator:** D
- **Priority:** P1
- **Dependencies:** T0.2–T0.13
- **Estimated effort:** 4 hours
- **Description:** Write PHPUnit tests for each model verifying instantiation, table name, fillable, and CRUD.
- **Artifact:** `tests/Unit/Models/Trading/` test files
- **Acceptance criteria:** All model tests pass

---

## Phase 1: Infrastructure — Routes, Providers, Middleware, Config

### T1.1 — Rewrite CryptoTrading Routes (web.php)
- **Coordinator:** B
- **Priority:** P1
- **Dependencies:** Phase 0 complete
- **Estimated effort:** 2 hours
- **Description:** Rewrite `Modules/CryptoTrading/Routes/web.php` with all trading routes preserving route names.
- **Artifact:** Native `web.php` route file
- **Acceptance criteria:** All `user.trading.*` routes resolve

### T1.2 — Rewrite CryptoTrading Routes (api.php)
- **Coordinator:** B
- **Priority:** P1
- **Dependencies:** T1.1
- **Estimated effort:** 1 hour
- **Description:** Rewrite `Modules/CryptoTrading/Routes/api.php` with API routes.
- **Artifact:** Native `api.php` route file
- **Acceptance criteria:** API routes resolve with `auth:sanctum`

### T1.3 — Rewrite CryptoTrading ServiceProvider
- **Coordinator:** B
- **Priority:** P1
- **Dependencies:** Phase 0 complete
- **Estimated effort:** 1 hour
- **Description:** Rewrite `CryptoTradingServiceProvider.php` with standard register/boot pattern.
- **Artifact:** Native service provider
- **Acceptance criteria:** Provider registers correctly, config published

### T1.4 — Rewrite CryptoTrading RouteServiceProvider
- **Coordinator:** B
- **Priority:** P1
- **Dependencies:** T1.1, T1.2
- **Estimated effort:** 30 minutes
- **Description:** Rewrite `RouteServiceProvider.php` to load module routes.
- **Artifact:** Native route service provider
- **Acceptance criteria:** Routes loaded via provider

### T1.5 — Rewrite CryptoTrading Middleware
- **Coordinator:** B
- **Priority:** P1
- **Dependencies:** None
- **Estimated effort:** 30 minutes
- **Description:** Rewrite `CryptoTradingMiddleWare.php` as simple access control middleware.
- **Artifact:** Native middleware class
- **Acceptance criteria:** Middleware passes authenticated requests, blocks unauthenticated

### T1.6 — Rewrite CryptoTrading Config
- **Coordinator:** B
- **Priority:** P1
- **Dependencies:** None
- **Estimated effort:** 30 minutes
- **Description:** Rewrite `Config/config.php` as simple array.
- **Artifact:** Native config file
- **Acceptance criteria:** Config accessible via `config('cryptotrading.*')`

### T1.7 — Rewrite CryptoTrading Installer Controller
- **Coordinator:** B
- **Priority:** P1
- **Dependencies:** T1.3
- **Estimated effort:** 1 hour
- **Description:** Rewrite `CryptoTradingInstallerController.php` as simple setup wizard.
- **Artifact:** Native installer controller
- **Acceptance criteria:** Installer runs migrations and seeders

### T1.8 — Route Integration Test
- **Coordinator:** B
- **Priority:** P1
- **Dependencies:** T1.1–T1.7
- **Estimated effort:** 1 hour
- **Description:** Run `php artisan route:list` and verify all routes resolve.
- **Artifact:** Route list output
- **Acceptance criteria:** All pre-rewrite routes present post-rewrite

---

## Phase 2: Controllers — Admin and User

### T2.1 — Rewrite AdminBotController (App Core)
- **Coordinator:** A
- **Priority:** P1
- **Dependencies:** Phase 0, Phase 1
- **Estimated effort:** 3 hours
- **Description:** Rewrite `core/app/Http/Controllers/Admin/AdminBotController.php` as native PHP. CRUD for trading bots.
- **Artifact:** Native `AdminBotController.php`
- **Acceptance criteria:** Admin can create, edit, delete, enable/disable trading bots

### T2.2 — Rewrite AdminStakingController (App Core)
- **Coordinator:** A
- **Priority:** P1
- **Dependencies:** Phase 0, Phase 1
- **Estimated effort:** 4 hours
- **Description:** Rewrite `core/app/Http/Controllers/Admin/AdminStakingController.php` as native PHP. CRUD for staking coins and configurations.
- **Artifact:** Native `AdminStakingController.php`
- **Acceptance criteria:** Admin can manage staking coins, rates, minimums

### T2.3 — Rewrite AdminTradingWalletController (App Core)
- **Coordinator:** A
- **Priority:** P1
- **Dependencies:** Phase 0, Phase 1
- **Estimated effort:** 2 hours
- **Description:** Rewrite `core/app/Http/Controllers/Admin/AdminTradingWalletController.php` as native PHP. List, view, manage user wallets.
- **Artifact:** Native `AdminTradingWalletController.php`
- **Acceptance criteria:** Admin can list and view user trading wallets

### T2.4 — Rewrite TradingSignalController (App Core)
- **Coordinator:** A
- **Priority:** P1
- **Dependencies:** Phase 0, Phase 1
- **Estimated effort:** 3 hours
- **Description:** Rewrite `core/app/Http/Controllers/Admin/TradingSignalController.php` as native PHP. CRUD for trading signals.
- **Artifact:** Native `TradingSignalController.php`
- **Acceptance criteria:** Admin can create, edit, delete trading signals

### T2.5 — Rewrite Module Admin Controllers (CryptoTrading)
- **Coordinator:** A
- **Priority:** P1
- **Dependencies:** T2.1–T2.4
- **Estimated effort:** 4 hours
- **Description:** Rewrite the 4 module-level admin controllers in `Modules/CryptoTrading/Http/Controllers/Admin/` to match the app core versions.
- **Artifact:** 4 native module admin controllers
- **Acceptance criteria:** Module admin routes resolve and function correctly

### T2.6 — Rewrite CoinStakingController (User)
- **Coordinator:** C
- **Priority:** P1
- **Dependencies:** Phase 0, Phase 1
- **Estimated effort:** 3 hours
- **Description:** Rewrite `Modules/CryptoTrading/Http/Controllers/User/CoinStakingController.php` as native PHP. User staking operations.
- **Artifact:** Native `CoinStakingController.php`
- **Acceptance criteria:** Users can stake coins, view stakes, claim returns

### T2.7 — Rewrite CryptoTradingCronController
- **Coordinator:** C
- **Priority:** P1
- **Dependencies:** Phase 0, Phase 1
- **Estimated effort:** 3 hours
- **Description:** Rewrite `Modules/CryptoTrading/Http/Controllers/Cron/CryptoTradingCronController.php` as native PHP. Scheduled tasks using `OwnedTradingMarketData`.
- **Artifact:** Native cron controller
- **Acceptance criteria:** Cron executes bot trades, staking returns, signal updates

### T2.8 — Rewrite CryptoTrading AddonHelpers
- **Coordinator:** C
- **Priority:** P1
- **Dependencies:** Phase 0
- **Estimated effort:** 2 hours
- **Description:** Rewrite `Modules/CryptoTrading/Helpers/AddonHelpers.php` as native PHP. Remove external API calls.
- **Artifact:** Native helper file
- **Acceptance criteria:** No external API calls, all helper functions work

---

## Phase 3: Parallel Modules — KYC, ManualDeposit, SupportTicket

### T3.1 — Rewrite KYC Routes
- **Coordinator:** E
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 1 hour
- **Description:** Rewrite `Modules/KYC/Routes/web.php` and `api.php`.
- **Artifact:** Native route files
- **Acceptance criteria:** KYC routes resolve

### T3.2 — Rewrite KYC Providers
- **Coordinator:** E
- **Priority:** P2
- **Dependencies:** T3.1
- **Estimated effort:** 1 hour
- **Description:** Rewrite `KYCServiceProvider.php` and `RouteServiceProvider.php`.
- **Artifact:** Native provider files
- **Acceptance criteria:** Providers register correctly

### T3.3 — Rewrite KYC Middleware
- **Coordinator:** E
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 30 minutes
- **Description:** Rewrite `KycMiddleware.php` as simple access control.
- **Artifact:** Native middleware
- **Acceptance criteria:** Middleware enforces KYC check

### T3.4 — Rewrite KYC IdVerification Model
- **Coordinator:** E
- **Priority:** P2
- **Dependencies:** T0.1 (schema reference)
- **Estimated effort:** 1 hour
- **Description:** Rewrite `Modules/KYC/Entities/IdVerification.php` as native PHP.
- **Artifact:** Native `IdVerification.php` model
- **Acceptance criteria:** Model instantiates, CRUD works

### T3.5 — Rewrite KYC Controllers
- **Coordinator:** E
- **Priority:** P2
- **Dependencies:** T3.1–T3.4
- **Estimated effort:** 4 hours
- **Description:** Rewrite `AdminIdController.php`, `KycInstallerController.php`, `IdController.php`. Remove `processUserId()` external call.
- **Artifact:** 3 native controller files
- **Acceptance criteria:** KYC upload, review, approve/reject works without external API

### T3.6 — Rewrite KYC Seeder and Config
- **Coordinator:** E
- **Priority:** P2
- **Dependencies:** T3.2
- **Estimated effort:** 1 hour
- **Description:** Rewrite `KYCDatabaseSeeder.php` and `Config/config.php`.
- **Artifact:** Native seeder and config
- **Acceptance criteria:** Seeder runs, config accessible

### T3.7 — Rewrite ManualDeposit Routes
- **Coordinator:** F
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 1 hour
- **Description:** Rewrite `Modules/ManualDeposit/Routes/web.php` and `api.php`.
- **Artifact:** Native route files
- **Acceptance criteria:** Manual deposit routes resolve

### T3.8 — Rewrite ManualDeposit Providers
- **Coordinator:** F
- **Priority:** P2
- **Dependencies:** T3.7
- **Estimated effort:** 1 hour
- **Description:** Rewrite service and route providers.
- **Artifact:** Native provider files
- **Acceptance criteria:** Providers register correctly

### T3.9 — Rewrite ManualDeposit Middleware
- **Coordinator:** F
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 30 minutes
- **Description:** Rewrite `ManualDepositMiddleware.php`.
- **Artifact:** Native middleware
- **Acceptance criteria:** Middleware functions correctly

### T3.10 — Rewrite ManualDeposit Controllers
- **Coordinator:** F
- **Priority:** P2
- **Dependencies:** T3.7–T3.9
- **Estimated effort:** 4 hours
- **Description:** Rewrite `DepositMethodController.php`, `ManualDepositInstallerController.php`, `ManualDepositController.php`. Remove `checkAddon()` calls.
- **Artifact:** 3 native controller files
- **Acceptance criteria:** Deposit method CRUD and user deposit submission work

### T3.11 — Rewrite ManualDeposit Seeder and Config
- **Coordinator:** F
- **Priority:** P2
- **Dependencies:** T3.8
- **Estimated effort:** 1 hour
- **Description:** Rewrite seeder and config.
- **Artifact:** Native seeder and config
- **Acceptance criteria:** Seeder runs, config accessible

### T3.12 — Rewrite SupportTicket Routes
- **Coordinator:** H
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 1 hour
- **Description:** Rewrite `Modules/SupportTicket/Routes/web.php` and `api.php`.
- **Artifact:** Native route files
- **Acceptance criteria:** Support ticket routes resolve

### T3.13 — Rewrite SupportTicket Providers
- **Coordinator:** H
- **Priority:** P2
- **Dependencies:** T3.12
- **Estimated effort:** 1 hour
- **Description:** Rewrite service and route providers.
- **Artifact:** Native provider files
- **Acceptance criteria:** Providers register correctly

### T3.14 — Rewrite SupportTicket Middleware
- **Coordinator:** H
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 30 minutes
- **Description:** Rewrite `SupportTicketMiddleware.php`.
- **Artifact:** Native middleware
- **Acceptance criteria:** Middleware functions correctly

### T3.15 — Rewrite SupportTicket Models
- **Coordinator:** H
- **Priority:** P2
- **Dependencies:** T0.1
- **Estimated effort:** 2 hours
- **Description:** Rewrite `SupportTicket.php`, `SupportTicketAttachment.php`, `SupportTicketReply.php` as native PHP.
- **Artifact:** 3 native model files
- **Acceptance criteria:** Models instantiate, CRUD works, relationships defined

### T3.16 — Rewrite SupportTicket Controllers
- **Coordinator:** H
- **Priority:** P2
- **Dependencies:** T3.12–T3.15
- **Estimated effort:** 4 hours
- **Description:** Rewrite `AdminTicketController.php`, `SupportTicketInstallerController.php`, `TicketController.php`.
- **Artifact:** 3 native controller files
- **Acceptance criteria:** Ticket creation, reply, attachment, admin review work

### T3.17 — Rewrite SupportTicket Seeder and Config
- **Coordinator:** H
- **Priority:** P2
- **Dependencies:** T3.13
- **Estimated effort:** 1 hour
- **Description:** Rewrite seeder and config.
- **Artifact:** Native seeder and config
- **Acceptance criteria:** Seeder runs, config accessible

---

## Phase 4: Disabled Module — P2pTransfer

### T4.1 — Rewrite P2pTransfer Routes
- **Coordinator:** G
- **Priority:** P3
- **Dependencies:** None
- **Estimated effort:** 1 hour
- **Description:** Rewrite `Modules/P2pTransfer/Routes/web.php` and `api.php`.
- **Artifact:** Native route files
- **Acceptance criteria:** P2P routes resolve (module remains disabled)

### T4.2 — Rewrite P2pTransfer Providers
- **Coordinator:** G
- **Priority:** P3
- **Dependencies:** T4.1
- **Estimated effort:** 1 hour
- **Description:** Rewrite service and route providers.
- **Artifact:** Native provider files
- **Acceptance criteria:** Providers register correctly

### T4.3 — Rewrite P2pTransfer Middleware
- **Coordinator:** G
- **Priority:** P3
- **Dependencies:** None
- **Estimated effort:** 30 minutes
- **Description:** Rewrite `P2pTransferMiddleware.php`.
- **Artifact:** Native middleware
- **Acceptance criteria:** Middleware functions correctly

### T4.4 — Rewrite P2pTransfer Transfer Model
- **Coordinator:** G
- **Priority:** P3
- **Dependencies:** T0.1
- **Estimated effort:** 1 hour
- **Description:** Rewrite `Modules/P2pTransfer/Entities/Transfer.php` as native PHP.
- **Artifact:** Native `Transfer.php` model
- **Acceptance criteria:** Model instantiates, CRUD works

### T4.5 — Rewrite P2pTransfer Controllers
- **Coordinator:** G
- **Priority:** P3
- **Dependencies:** T4.1–T4.4
- **Estimated effort:** 4 hours
- **Description:** Rewrite `AdminBalanceTransferController.php`, `P2pTransferInstallerController.php`, `BalanceTransferController.php`. Fix non-atomic balance mutation bugs. Use DB transactions.
- **Artifact:** 3 native controller files
- **Acceptance criteria:** Transfer creation with atomic balance updates works

### T4.6 — Rewrite P2pTransfer Seeder and Config
- **Coordinator:** G
- **Priority:** P3
- **Dependencies:** T4.2
- **Estimated effort:** 1 hour
- **Description:** Rewrite seeder and config.
- **Artifact:** Native seeder and config
- **Acceptance criteria:** Seeder runs, config accessible

---

## Phase 5: External API Removal

### T5.1 — Remove `checkAddon()` External Call
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** Phases 2–4 complete
- **Estimated effort:** 1 hour
- **Description:** Replace `checkAddon()` in `AddonHelpers.php` with no-op returning `['status' => 'ok']`.
- **Artifact:** Updated `AddonHelpers.php`
- **Acceptance criteria:** No calls to `api.credcrypto.net` from `checkAddon()`

### T5.2 — Remove `invalidLicense()` Function
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** T5.1
- **Estimated effort:** 30 minutes
- **Description:** Remove `invalidLicense()` function entirely from `AddonHelpers.php`.
- **Artifact:** Updated `AddonHelpers.php`
- **Acceptance criteria:** Function removed, no references remain

### T5.3 — Remove All `update*()` External Calls
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** T5.1
- **Estimated effort:** 3 hours
- **Description:** Remove `checkAddon()` calls from all 10 `update*()` functions. Keep env updates and model saves.
- **Artifact:** Updated `AddonHelpers.php`
- **Acceptance criteria:** Payment method updates work without external API

### T5.4 — Replace `countryList()` with Internal Data
- **Coordinator:** All
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 2 hours
- **Description:** Replace external API call with hardcoded country array.
- **Artifact:** Updated `generalHelper.php`
- **Acceptance criteria:** Country list returns without external API

### T5.5 — Replace `currencyConverter()` with Internal Logic
- **Coordinator:** All
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 2 hours
- **Description:** Replace external API call with internal conversion using CoinMarketCap or stored rates.
- **Artifact:** Updated `generalHelper.php`
- **Acceptance criteria:** Currency conversion works without external API

### T5.6 — Replace `getCurrency()` with Internal Data
- **Coordinator:** All
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 1 hour
- **Description:** Replace external API call with hardcoded currency array.
- **Artifact:** Updated `generalHelper.php`
- **Acceptance criteria:** Currency list returns without external API

### T5.7 — Replace `fetchBlogs()` with Local Query
- **Coordinator:** All
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 30 minutes
- **Description:** Replace external API call with `Blog::all()`.
- **Artifact:** Updated `generalHelper.php`
- **Acceptance criteria:** Blogs fetched from local database

### T5.8 — Replace `processLoan()` with Internal Logic
- **Coordinator:** All
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 2 hours
- **Description:** Replace external API call with internal loan processing logic.
- **Artifact:** Updated `AddonHelpers.php`
- **Acceptance criteria:** Loan approval/rejection works without external API

### T5.9 — Replace `processUserId()` with Internal Logic
- **Coordinator:** All
- **Priority:** P2
- **Dependencies:** T3.5
- **Estimated effort:** 1 hour
- **Description:** Replace external API call with internal KYC verification logic.
- **Artifact:** Updated `AddonHelpers.php`
- **Acceptance criteria:** KYC verification works without external API

### T5.10 — Replace `pairPrice()` with OwnedTradingMarketData
- **Coordinator:** All
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 30 minutes
- **Description:** Replace `api.latoken.com` call with `OwnedTradingMarketData::price()`.
- **Artifact:** Updated `AddonHelpers.php`
- **Acceptance criteria:** Pair prices returned from Poloniex/fallback

### T5.11 — Remove `purchaseCodeVerification()` External Call
- **Coordinator:** All
- **Priority:** P2
- **Dependencies:** None
- **Estimated effort:** 30 minutes
- **Description:** Replace with no-op returning success.
- **Artifact:** Updated `generalHelper.php`
- **Acceptance criteria:** Function returns success without external API

### T5.12 — Remove `BASE_CURL` and `BASE_TEST` from `.env`
- **Coordinator:** All
- **Priority:** P2
- **Dependencies:** T5.1–T5.11
- **Estimated effort:** 30 minutes
- **Description:** Remove environment variables from `.env`, `.env.example`, `.env.testing`.
- **Artifact:** Updated `.env` files
- **Acceptance criteria:** No `BASE_CURL` or `BASE_TEST` in any `.env` file

### T5.13 — Remove All SSL Verification Disabling
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** None
- **Estimated effort:** 2 hours
- **Description:** Search and remove all `CURLOPT_SSL_VERIFYHOST, false` and `CURLOPT_SSL_VERIFYPEER, false` settings.
- **Artifact:** Updated PHP files
- **Acceptance criteria:** No SSL verification disabling in any PHP file

### T5.14 — Full External API Audit
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** T5.1–T5.13
- **Estimated effort:** 1 hour
- **Description:** Search entire codebase for `api.credcrypto.net`, `BASE_CURL`, `BASE_TEST`, `credcrypto`. Zero results expected.
- **Artifact:** Audit report
- **Acceptance criteria:** Zero external API references found

---

## Phase 6: Integration Testing & Verification

### T6.1 — Remove ionCube Loader
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** All phases complete
- **Estimated effort:** 30 minutes
- **Description:** Remove `zend_extension=ioncube_loader` from `php.ini`.
- **Artifact:** Updated `php.ini`
- **Acceptance criteria:** PHP runs without ionCube Loader

### T6.2 — Full Route Audit
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** T6.1
- **Estimated effort:** 1 hour
- **Description:** Run `php artisan route:list` without ionCube. Compare with pre-rewrite.
- **Artifact:** Route list comparison report
- **Acceptance criteria:** All routes present and resolving

### T6.3 — Full Migration Test
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** T6.1
- **Estimated effort:** 1 hour
- **Description:** Run `php artisan migrate:fresh` and all seeders.
- **Artifact:** Migration output
- **Acceptance criteria:** All tables created, all seeders run

### T6.4 — PHPUnit Test Suite
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** T6.1
- **Estimated effort:** 4 hours
- **Description:** Write and run PHPUnit tests for all rewritten controllers.
- **Artifact:** `tests/Feature/` test files
- **Acceptance criteria:** All tests pass

### T6.5 — Playwright Browser Smoke Tests
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** T6.1
- **Estimated effort:** 4 hours
- **Description:** Extend Playwright suite for trading, wallets, staking, KYC, deposits, tickets, admin.
- **Artifact:** Updated Playwright test scripts
- **Acceptance criteria:** All browser tests pass

### T6.6 — ionCube Marker Audit
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** T6.1
- **Estimated effort:** 1 hour
- **Description:** Search for `ionCube`, `//002cd`, `ionCube Loader` in codebase.
- **Artifact:** Audit report
- **Acceptance criteria:** No ionCube markers in PHP files

### T6.7 — Financial Integrity Test
- **Coordinator:** All
- **Priority:** P1
- **Dependencies:** T6.4
- **Estimated effort:** 4 hours
- **Description:** Test concurrent trade and transfer operations for race conditions.
- **Artifact:** Test results
- **Acceptance criteria:** No race conditions, all balances correct

### T6.8 — Final Sign-Off
- **Coordinator:** All
- **Priority:** P0
- **Dependencies:** T6.1–T6.7
- **Estimated effort:** 1 hour
- **Description:** Full application boot and smoke test without ionCube Loader.
- **Artifact:** Sign-off document
- **Acceptance criteria:** Application fully functional without ionCube

---

## Task Dependency Summary

```
T0.1 → T0.2–T0.13 (models)
T0.1 → T0.14 (migrations)
T0.14 → T0.15 (seeder)
T0.14 → T0.16 (compatibility migration)
T0.2–T0.13 → T0.17 (model tests)

Phase 0 complete → Phase 1 (T1.1–T1.8)
Phase 0 + Phase 1 → Phase 2 (T2.1–T2.8)
Phase 0 → Phase 3 (T3.1–T3.17) [parallel]
Phase 0 → Phase 4 (T4.1–T4.6) [parallel]
Phases 2–4 → Phase 5 (T5.1–T5.14)
Phase 5 → Phase 6 (T6.1–T6.8)
```

---

## Resource Allocation

| Coordinator | Tasks | Estimated Hours | Phase |
|-------------|-------|----------------|-------|
| D | T0.1–T0.17 | 24 | Phase 0 |
| B | T1.1–T1.8 | 6 | Phase 1 |
| A | T2.1–T2.5 | 16 | Phase 2 |
| C | T2.6–T2.8 | 8 | Phase 2 |
| E | T3.1–T3.6 | 8 | Phase 3 |
| F | T3.7–T3.11 | 7 | Phase 3 |
| H | T3.12–T3.17 | 9 | Phase 3 |
| G | T4.1–T4.6 | 8 | Phase 4 |
| All | T5.1–T5.14 | 16 | Phase 5 |
| All | T6.1–T6.8 | 16 | Phase 6 |
| **Total** | | **~118 hours** | |

---

## Document Status

- **Created:** 2026-06-19
- **Focus:** ionCube replacement tasks only
- **Status:** Active — Ready for execution
- **Owner:** Trading-Wallet.net Development Team
