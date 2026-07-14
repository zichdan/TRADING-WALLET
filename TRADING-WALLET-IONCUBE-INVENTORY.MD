# Trading Wallet ionCube Inventory

Scope: `public_html/trading-wallet.net`

Scan date: 2026-06-19

This inventory lists PHP files that match the standard ionCube protected-file stub:

- First line begins with `<?php //00...`
- Early file body checks for `ionCube Loader`

Readable files that only mention ionCube are listed separately. Encoded payloads, secrets, license keys, and environment values are not copied here.

## Summary Counts

Total files scanned under `public_html/trading-wallet.net`: 2,547

True ionCube-encoded PHP files found: 92

All encoded files are under `core`.

No ionCube-encoded Blade view/page files were found.

No ionCube marker hits were found under:

- `public_html/trading-wallet.net/public`
- `public_html/trading-wallet.net/Compressed trading wallet`

Installed modules under `core/Modules`:

| Module | Total files in module | Encoded PHP files |
| --- | ---: | ---: |
| CryptoTrading | 116 | 43 |
| KYC | 21 | 11 |
| ManualDeposit | 21 | 10 |
| P2pTransfer | 21 | 11 |
| SupportTicket | 24 | 13 |
| App core admin copies | n/a | 4 |

Encoded surface counts:

| Surface | Count |
| --- | ---: |
| Routes | 10 |
| Providers | 10 |
| Middleware | 5 |
| Admin controllers | 12 |
| User controllers | 5 |
| Installer controllers | 5 |
| Cron controllers | 1 |
| Entities / models | 17 |
| Helpers | 1 |
| Migrations | 16 |
| Seeders | 5 |
| Config | 5 |

Class-like encoded files: 76

Encoded helper files: 1

Encoded model/entity files: 17

Encoded Blade/page files: 0

Internal function names inside encoded files cannot be honestly enumerated from the source tree because the PHP bodies are ionCube protected. The file-level inventory below identifies every protected route, provider, controller, middleware, model/entity, helper, migration, seeder, and config file.

## Encoded Files By Module

### App Core Admin Copies

Encoded admin controllers:

- `core/app/Http/Controllers/Admin/AdminBotController.php`
- `core/app/Http/Controllers/Admin/AdminStakingController.php`
- `core/app/Http/Controllers/Admin/AdminTradingWalletController.php`
- `core/app/Http/Controllers/Admin/TradingSignalController.php`

### CryptoTrading

Encoded routes:

- `core/Modules/CryptoTrading/Routes/api.php`
- `core/Modules/CryptoTrading/Routes/web.php`

Encoded providers:

- `core/Modules/CryptoTrading/Providers/CryptoTradingServiceProvider.php`
- `core/Modules/CryptoTrading/Providers/RouteServiceProvider.php`

Encoded middleware:

- `core/Modules/CryptoTrading/Http/Middleware/CryptoTradingMiddleWare.php`

Encoded admin controllers:

- `core/Modules/CryptoTrading/Http/Controllers/Admin/AdminBotController.php`
- `core/Modules/CryptoTrading/Http/Controllers/Admin/AdminStakingController.php`
- `core/Modules/CryptoTrading/Http/Controllers/Admin/AdminTradingWalletController.php`
- `core/Modules/CryptoTrading/Http/Controllers/Admin/TradingSignalController.php`

Encoded user controllers:

- `core/Modules/CryptoTrading/Http/Controllers/User/CoinStakingController.php`

Encoded installer controller:

- `core/Modules/CryptoTrading/Http/Controllers/CryptoTradingInstallerController.php`

Encoded cron controller:

- `core/Modules/CryptoTrading/Http/Controllers/Cron/CryptoTradingCronController.php`

Encoded helper:

- `core/Modules/CryptoTrading/Helpers/AddonHelpers.php`

Encoded entities/models:

- `core/Modules/CryptoTrading/Entities/DemoTradingLog.php`
- `core/Modules/CryptoTrading/Entities/DemoTradingWallet.php`
- `core/Modules/CryptoTrading/Entities/Staking.php`
- `core/Modules/CryptoTrading/Entities/StakingCoin.php`
- `core/Modules/CryptoTrading/Entities/TradingBot.php`
- `core/Modules/CryptoTrading/Entities/TradingBotActivation.php`
- `core/Modules/CryptoTrading/Entities/TradingBotTrade.php`
- `core/Modules/CryptoTrading/Entities/TradingCurrency.php`
- `core/Modules/CryptoTrading/Entities/TradingPair.php`
- `core/Modules/CryptoTrading/Entities/TradingSignal.php`
- `core/Modules/CryptoTrading/Entities/TradingWallet.php`
- `core/Modules/CryptoTrading/Entities/TradingWalletTransaction.php`

Encoded migrations:

- `core/Modules/CryptoTrading/Database/Migrations/2022_10_12_235220_create_trading_wallets_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_10_18_142048_create_trading_wallet_transactions_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_10_18_232321_create_trading_signals_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_10_19_234113_create_trading_pairs_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_10_20_162143_create_trading_logs_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_10_20_215535_create_stakings_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_10_21_193109_create_stakings_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_10_22_121454_create_trading_bots_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_10_22_121916_create_trading_bot_activations_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_10_29_002937_add_user_activated_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_10_29_114155_create_trading_bot_trades_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_11_01_223442_trading_bot_trades.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_12_02_020259_create_demo_trading_wallets_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_12_02_021951_create_demo_trading_logs_table.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_12_03_220832_add_to_demo.php`
- `core/Modules/CryptoTrading/Database/Migrations/2022_12_07_022611_add_to_signals.php`

Encoded seeder:

- `core/Modules/CryptoTrading/Database/Seeders/CryptoTradingDatabaseSeeder.php`

Encoded config:

- `core/Modules/CryptoTrading/Config/config.php`

### KYC

Encoded routes:

- `core/Modules/KYC/Routes/api.php`
- `core/Modules/KYC/Routes/web.php`

Encoded providers:

- `core/Modules/KYC/Providers/KYCServiceProvider.php`
- `core/Modules/KYC/Providers/RouteServiceProvider.php`

Encoded middleware:

- `core/Modules/KYC/Http/Middleware/KycMiddleware.php`

Encoded controllers:

- `core/Modules/KYC/Http/Controllers/Admin/AdminIdController.php`
- `core/Modules/KYC/Http/Controllers/KycInstallerController.php`
- `core/Modules/KYC/Http/Controllers/User/IdController.php`

Encoded entity/model:

- `core/Modules/KYC/Entities/IdVerification.php`

Encoded seeder:

- `core/Modules/KYC/Database/Seeders/KYCDatabaseSeeder.php`

Encoded config:

- `core/Modules/KYC/Config/config.php`

### ManualDeposit

Encoded routes:

- `core/Modules/ManualDeposit/Routes/api.php`
- `core/Modules/ManualDeposit/Routes/web.php`

Encoded providers:

- `core/Modules/ManualDeposit/Providers/ManualDepositServiceProvider.php`
- `core/Modules/ManualDeposit/Providers/RouteServiceProvider.php`

Encoded middleware:

- `core/Modules/ManualDeposit/Http/Middleware/ManualDepositMiddleware.php`

Encoded controllers:

- `core/Modules/ManualDeposit/Http/Controllers/Admin/DepositMethodController.php`
- `core/Modules/ManualDeposit/Http/Controllers/ManualDepositInstallerController.php`
- `core/Modules/ManualDeposit/Http/Controllers/User/ManualDepositController.php`

Encoded seeder:

- `core/Modules/ManualDeposit/Database/Seeders/ManualDepositDatabaseSeeder.php`

Encoded config:

- `core/Modules/ManualDeposit/Config/config.php`

### P2pTransfer

Encoded routes:

- `core/Modules/P2pTransfer/Routes/api.php`
- `core/Modules/P2pTransfer/Routes/web.php`

Encoded providers:

- `core/Modules/P2pTransfer/Providers/P2pTransferServiceProvider.php`
- `core/Modules/P2pTransfer/Providers/RouteServiceProvider.php`

Encoded middleware:

- `core/Modules/P2pTransfer/Http/Middleware/P2pTransferMiddleware.php`

Encoded controllers:

- `core/Modules/P2pTransfer/Http/Controllers/Admin/AdminBalanceTransferController.php`
- `core/Modules/P2pTransfer/Http/Controllers/P2pTransferInstallerController.php`
- `core/Modules/P2pTransfer/Http/Controllers/User/BalanceTransferController.php`

Encoded entity/model:

- `core/Modules/P2pTransfer/Entities/Transfer.php`

Encoded seeder:

- `core/Modules/P2pTransfer/Database/Seeders/P2pTransferDatabaseSeeder.php`

Encoded config:

- `core/Modules/P2pTransfer/Config/config.php`

### SupportTicket

Encoded routes:

- `core/Modules/SupportTicket/Routes/api.php`
- `core/Modules/SupportTicket/Routes/web.php`

Encoded providers:

- `core/Modules/SupportTicket/Providers/RouteServiceProvider.php`
- `core/Modules/SupportTicket/Providers/SupportTicketServiceProvider.php`

Encoded middleware:

- `core/Modules/SupportTicket/Http/Middleware/SupportTicketMiddleware.php`

Encoded controllers:

- `core/Modules/SupportTicket/Http/Controllers/Admin/AdminTicketController.php`
- `core/Modules/SupportTicket/Http/Controllers/SupportTicketInstallerController.php`
- `core/Modules/SupportTicket/Http/Controllers/User/TicketController.php`

Encoded entities/models:

- `core/Modules/SupportTicket/Entities/SupportTicket.php`
- `core/Modules/SupportTicket/Entities/SupportTicketAttachment.php`
- `core/Modules/SupportTicket/Entities/SupportTicketReply.php`

Encoded seeder:

- `core/Modules/SupportTicket/Database/Seeders/SupportTicketDatabaseSeeder.php`

Encoded config:

- `core/Modules/SupportTicket/Config/config.php`

## Encoded Pages And Views

No encoded Blade templates were found.

Count: 0

The page/view files are not themselves ionCube encoded. However, several user-facing and admin-facing page flows are controlled by encoded route/controller/model files, especially in these modules:

- `CryptoTrading`
- `KYC`
- `ManualDeposit`
- `P2pTransfer`
- `SupportTicket`

## Readable Files That Mention ionCube But Are Not Encoded

These files contain readable ionCube checks/status logic, but they did not match the protected-file stub:

- `core/app/Http/Controllers/Admin/SystemController.php`
- `core/app/Http/Controllers/Admin/AddonInstaller.php`

## Practical Interpretation

The most heavily protected module is `CryptoTrading`, with 43 encoded PHP files. It contains the only encoded helper file, all encoded trading-related models/entities, and all 16 encoded migrations found in this scan.

The other four modules are also protected at the route, provider, middleware, controller, seeder, and config layers:

- `KYC`
- `ManualDeposit`
- `P2pTransfer`
- `SupportTicket`

From a development and recovery standpoint, any replacement or rewrite plan should treat these five modules as protected add-ons rather than ordinary editable Laravel modules.
