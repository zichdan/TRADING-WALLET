# Trading Wallet License And New Domain Recovery Plan

This folder documents five lawful, practical ways to recover the Trading Wallet site after moving from the old licensed domain to a new domain.

The documents intentionally do not include instructions for decrypting ionCube files, patching encoded files, bypassing license enforcement, faking a vendor licensing server, or misrepresenting a domain. The goal is to restore service through legitimate configuration, license evidence, controlled module replacement, or clean migration.

## Local Evidence Summary

- The active CryptoTrading files opened in `www/Compressed trading wallet/core/Modules/CryptoTrading` are ionCube-protected PHP files.
- The canonical admin system manager has a purchase-code verification page at `public_html/trading-wallet.net/core/resources/views/admin/system-manager/license.blade.php`.
- `public_html/trading-wallet.net/core/app/Http/Controllers/Admin/SystemController.php` validates a UUID-shaped purchase code and calls `purchaseCodeVerification(...)`.
- `public_html/trading-wallet.net/core/app/Helpers/generalHelper.php` builds a request to `BASE_CURL + /v1/purchase-code-verification`, sends the live `HTTP_HOST` as `request-origin`, and sends the purchase code as a request header.
- The compressed copy has a different verification helper shape: it uses a `BASE_TEST` environment value for the request origin.
- `.env` values are present locally, but no secret values or license key values are printed in these documents.
- No obvious standalone `.lic` ionCube license file was found in the two Trading Wallet roots outside ordinary third-party license text files.
- `public_html/trading-wallet.net/core/modules_statuses.json` has `P2pTransfer` disabled, while `www/Compressed trading wallet/core/modules_statuses.json` has `P2pTransfer` enabled.

## Internet Research Summary

- ionCube supports domain/server restrictions for encoded PHP files through file-based restrictions such as `--allowed-server`.
- ionCube also supports encrypted license files; the Loader searches for expected license files relative to the encoded script and parent directories.
- ionCube license files can contain server restrictions, expiry information, and custom properties.
- Laravel recommends clearing and rebuilding configuration cache during deployment when environment values change.

Sources used:

- ionCube Encoder User Guide: <https://www.ioncube.com/sa/USER-GUIDE.pdf>
- ionCube GUI restrictions documentation: <https://www.ioncube.com/sa/gui_docs/settings_restrictions.html>
- ionCube support portal: <https://support.ioncube.com/>
- Laravel 9 configuration documentation: <https://laravel.com/docs/9.x/configuration>
- Laravel 9 session documentation: <https://laravel.com/docs/9.x/session>

## The Five Recovery Paths

1. [Official license transfer and proof package](01-OFFICIAL-LICENSE-TRANSFER-AND-EVIDENCE.md)
2. [Admin purchase-code verification on the new domain](02-ADMIN-PURCHASE-CODE-DOMAIN-VERIFICATION.md)
3. [Domain, Laravel cache, and session repair](03-DOMAIN-AND-LARAVEL-CONFIG-REPAIR.md)
4. [Replace the encoded CryptoTrading user module](04-REPLACE-ENCODED-TRADING-MODULE.md)
5. [Migrate to a fully owned replacement codebase](05-MIGRATE-TO-OWNED-CODEBASE.md)

## Recommended Order

Start with option 3 because the reported symptom includes user logout/dashboard restriction while admin still works. That can happen from session/cookie/cache/domain drift even when licensing is not the root cause.

Then try option 2 if the vendor verification endpoint still responds. If the vendor endpoint is gone, use option 1 to create a formal evidence package, and move toward option 4 or option 5 for long-term independence.
