# Trading Wallet Local Testing Documentation

## 1. Local Setup Scope, Research, And Installed Runtime

This document records the local development setup and browser verification performed for the Trading Wallet backup in `C:\Users\FASHIONISTAR\Downloads\backup_full_website`.

The canonical application paths used for testing are:

- Web root: `public_html/trading-wallet.net`
- Laravel root: `public_html/trading-wallet.net/core`
- Testing evidence folder: `TRADING WALLET TESTING`
- Testing report: `TRADING-WALLET-TESTING.MD`

Live setup research was based on the current official guidance for Laravel 9 deployment/runtime requirements, Composer installation, PHP/XAMPP on Windows, and ionCube Loader for encoded PHP modules:

- Laravel deployment and server requirements: <https://laravel.com/docs/9.x/deployment>
- Composer installation guidance: <https://getcomposer.org/doc/00-intro.md>
- Composer installer endpoint: <https://getcomposer.org/installer>
- ionCube Loader downloads: <https://www.ioncube.com/loaders.php>

Installed and verified local runtime:

- PHP: `8.1.25`, installed through XAMPP at `C:\xampp\php\php.exe`
- MariaDB: `10.4.32`, installed through XAMPP at `C:\xampp\mysql`
- Laravel: `9.40.1`
- Composer: `2.10.1`, stored locally at `TRADING WALLET TESTING/tools/composer.phar`
- Node.js: `v24.14.1`
- npm: `11.11.0`
- Playwright: `1.60.0`
- ionCube Loader: `15.5.0`, installed for PHP 8.1 at `C:\xampp\php\ext\ioncube_loader_win_8.1.dll`

PHP extension/runtime checks passed for the Laravel app and encoded modules using `ionCube`, `gd`, and `gmp`. Encoded module route files were linted successfully after the ionCube loader was installed.

Dependency setup completed:

- Composer dependencies installed from `composer.lock`.
- Composer package discovery completed.
- npm dependencies installed with `--legacy-peer-deps`.
- `webpack` was pinned to `5.60.0` and `webpack-cli` to `4.9.1` to match `laravel-mix@6.0.49`.
- `npm run dev` completed successfully and generated the Laravel Mix assets.
- Playwright Chromium was installed and used for browser verification.

Database and application initialization completed:

- Local MariaDB was started and confirmed alive.
- A local-only database was created for testing.
- A local-only testing environment file was added at `public_html/trading-wallet.net/core/.env.testing`.
- Laravel migrations completed successfully against the local database.
- A minimal local smoke seed was added at `TRADING WALLET TESTING/tools/seed-local-smoke.php`.
- Storage was linked so `public_html/trading-wallet.net/public/storage` points to `core/storage/app/public`.
- The previously empty public storage folder was preserved as `public_html/trading-wallet.net/public/storage.empty-before-codex-link`.
- A local PHP built-in server router was added at `public_html/trading-wallet.net/core/storage/local-dev-router.php` so static assets are served correctly while Laravel handles dynamic routes.

Security handling during setup:

- Production `.env` values, mail credentials, payment keys, certificates, cryptographic key material, license keys, and database secrets were not copied into this document.
- Testing used a local database and local-only environment overrides.
- Mail delivery was forced to local logging for the test run.
- Real payment, real email, and live external financial flows were not executed.
- Public maintenance routes such as `/php`, `/queue`, `/clear-cache`, and `/cron` were not exercised during browser smoke testing.
- A formal deep security scan report was not run in this setup task; security posture was used only to identify setup risks and avoid unsafe testing behavior.

Local server status:

- URL: <http://127.0.0.1:8087>
- Server process: PHP built-in server with ionCube, `gd`, and `gmp` enabled.
- Static asset checks passed for CSS, JavaScript, JSON, and PNG assets after the local router was corrected.

## 2. Verification Results And Browser Evidence

The following setup and verification checks were completed:

| Check | Result |
| --- | --- |
| PHP runtime | Passed with PHP `8.1.25` and ionCube Loader `15.5.0` |
| Laravel version | Passed, Laravel Framework `9.40.1` |
| MariaDB service | Passed, `mysqld is alive` |
| Composer install | Passed |
| Composer package discovery | Passed |
| Laravel migrations | Passed |
| Local smoke seed | Passed |
| Storage link | Passed |
| npm install | Passed with `--legacy-peer-deps` |
| npm build | Passed with `npm run dev` |
| Route inventory | Passed, `289` routes loaded under the local testing environment |
| PHPUnit | Passed, `2 tests, 2 assertions` |
| HTTP smoke | Passed for `/`, `/login`, `/register`, and `/admin/login` |
| Browser smoke | Passed for 5 pages with `0` console messages, `0` page errors, and `0` request failures |
| Browser interaction | Passed, the home-page `Open Login` action navigated to `/login` |

Fresh browser screenshots were captured in `TRADING WALLET TESTING`:

- [Home desktop](TRADING%20WALLET%20TESTING/01-home-desktop.png)
- [Login desktop](TRADING%20WALLET%20TESTING/02-login-desktop.png)
- [Register desktop](TRADING%20WALLET%20TESTING/03-register-desktop.png)
- [Admin login desktop](TRADING%20WALLET%20TESTING/04-admin-login-desktop.png)
- [Home mobile](TRADING%20WALLET%20TESTING/05-home-mobile.png)

The browser smoke result file is:

- [Browser smoke JSON](TRADING%20WALLET%20TESTING/browser-smoke-results.json)

Useful local rerun commands, with sensitive values intentionally omitted:

```powershell
cd C:\Users\FASHIONISTAR\Downloads\backup_full_website\public_html\trading-wallet.net\core

C:\xampp\php\php.exe -d zend_extension=C:\xampp\php\ext\ioncube_loader_win_8.1.dll -d extension=gd -d extension=gmp artisan --version

C:\xampp\php\php.exe -d zend_extension=C:\xampp\php\ext\ioncube_loader_win_8.1.dll -d extension=gd -d extension=gmp vendor\bin\phpunit --colors=never

npm run dev
```

Browser smoke can be rerun from the backup root:

```powershell
cd C:\Users\FASHIONISTAR\Downloads\backup_full_website
node ".\TRADING WALLET TESTING\tools\browser-smoke.mjs"
```

Authenticated Option 4 trading replacement smoke can be rerun from the backup root:

```powershell
cd C:\Users\FASHIONISTAR\Downloads\backup_full_website
node ".\TRADING WALLET TESTING\tools\trading-owned-smoke.mjs"
```

Option 4 owned trading replacement verification was completed after disabling the encoded `CryptoTrading` module and registering first-party routes with the existing `user.trading.*` route names.

| Option 4 Check | Result |
| --- | --- |
| Route cutover | Passed, all 17 `/user/trading/...` routes resolve to owned `App\Http\Controllers\User\Owned...` controllers |
| Schema compatibility migration | Passed, `2026_06_18_060000_add_owned_trading_schema_compatibility` ran |
| Registration regression | Passed, browser registration redirects to `/user/dashboard` with no `tcal` SQL error |
| Trading wallets page | Passed, HTTP 200 and no license/fatal text |
| Live trade page | Passed, HTTP 200 and no license/fatal text |
| Demo trade page | Passed, HTTP 200 and no license/fatal text |
| Trading bot page | Passed, HTTP 200 and no license/fatal text |

Option 4 screenshots were captured in `TRADING WALLET TESTING`:

- [Owned wallets](TRADING%20WALLET%20TESTING/07-owned-wallets.png)
- [Owned live trade](TRADING%20WALLET%20TESTING/08-owned-live-trade.png)
- [Owned demo trade](TRADING%20WALLET%20TESTING/09-owned-demo-trade.png)
- [Owned bot trade](TRADING%20WALLET%20TESTING/10-owned-bot-trade.png)

The local server can be restarted with PHP 8.1, ionCube, `gd`, and `gmp`; use the local testing environment values already stored on this machine rather than production credentials.

## 3. Findings, Blockers, And Redeployment Recommendations

The local setup is running and browser-verified at <http://127.0.0.1:8087>. The public home page, user login page, registration page, admin login page, and mobile home page all rendered successfully in a real browser session.

Important findings:

- No importable production SQL dump was found in the backup, so the current local database is a migrated and minimally seeded test database, not a full production data restoration.
- The application includes ionCube-encoded module files, so local PHP must load the correct ionCube Loader before Artisan, PHPUnit, routes, or browser tests can be trusted.
- `php artisan test` is not the recommended local command until PHP is globally configured with ionCube, because Artisan's test wrapper can spawn a PHP process without the required loader flags. Direct PHPUnit with explicit PHP flags passed.
- The PHP built-in server needed a local router file to serve static assets correctly from the cPanel-style web root.
- npm audit currently reports `19` vulnerabilities: `5` low, `10` moderate, `3` high, and `1` critical.
- Composer audit currently reports `23` security advisories affecting `12` packages.
- The verified setup intentionally avoids real email, real payment, live wallet, cron, queue, and admin mutation flows until credentials and deployment hardening are reviewed.

Professional recommendations before redeployment:

1. Restore from a verified production SQL dump or a sanitized staging dump before testing authenticated trading, wallet, deposit, withdrawal, KYC, support-ticket, and admin workflows.
2. Upgrade vulnerable Composer and npm dependencies in a controlled branch, then rerun migrations, PHPUnit, browser smoke, and selected authenticated workflows.
3. Move local testing permanently to `.env.testing` or a dedicated staging `.env`, never the restored production `.env`.
4. Configure ionCube in the active PHP `php.ini` if this machine will be used repeatedly for Artisan test commands, queue workers, scheduler runs, and module maintenance.
5. Disable or protect public maintenance/debug surfaces before redeployment, especially debug, cache-clear, queue, cron, installer, file-manager, and system-manager routes.
6. Add a reusable local bootstrap script that starts MariaDB, exports local environment values, starts the PHP server, and runs the smoke test.
7. Add an authenticated Playwright suite for user login, admin login, registration validation, wallet dashboard visibility, KYC entry points, manual-deposit entry points, and support-ticket entry points after a real staging dataset is available.
8. Run a separate formal repository security scan before redeployment; this setup report documents risk areas but is not a full security assessment.
9. Replace any production secrets present in the backup with rotated staging credentials before sharing, committing, or uploading the restored project.
10. Keep the `TRADING WALLET TESTING` evidence folder as the local setup record, but do not deploy screenshots, tool downloads, server logs, or smoke helper scripts to production.
