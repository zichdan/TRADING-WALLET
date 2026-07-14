# Option 2: Admin Purchase-Code Verification On The New Domain

## Purpose

Use the app's own admin verification flow to attempt a legitimate re-verification of the purchase code against the new domain.

This option does not bypass licensing. It uses the built-in verification request already present in the application.

## Local Evidence

The canonical helper in `public_html/trading-wallet.net/core/app/Helpers/generalHelper.php`:

- Reads a `BASE_CURL` environment value.
- Builds a `/v1/purchase-code-verification` URL.
- Sends the current `HTTP_HOST` as the `request-origin`.
- Sends the submitted purchase code as a request header.

The admin controller in `public_html/trading-wallet.net/core/app/Http/Controllers/Admin/SystemController.php`:

- Requires the purchase code to be UUID-shaped.
- Calls `purchaseCodeVerification(...)`.
- Shows the vendor response message when verification fails.

## Step-By-Step Implementation

1. Confirm the new domain DNS points to the correct hosting server.
2. Confirm the web server virtual host is serving the intended app root.
3. Confirm the new domain opens the public site over HTTPS.
4. Confirm the Laravel `.env` file has the correct new-domain `APP_URL`.
5. Confirm the `.env` file still has a `BASE_CURL` value.
6. Do not print or expose the purchase code publicly.
7. Clear Laravel caches after domain and environment changes:

```powershell
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

8. Rebuild production cache only after the `.env` is correct:

```powershell
php artisan config:cache
php artisan route:cache
```

9. Log in as admin.
10. Open the admin system manager purchase-code verification page.
11. Submit the original purchase code.
12. Record the exact response message privately.
13. If the response succeeds, test:
   - User login.
   - User dashboard.
   - Trading wallet dashboard.
   - CryptoTrading module user routes.
   - Logout and re-login.
14. If the response fails because the endpoint is unreachable, continue to option 1, option 4, or option 5.
15. If the response fails because the new domain is not allowed, option 1 is the correct license-transfer path.

## Extra Diagnostic Checks

Run these checks on the server without printing secret values:

```powershell
php -v
php -m
php artisan about
php artisan route:list
```

Confirm:

- ionCube Loader is enabled.
- `curl` is enabled.
- `openssl` is enabled.
- `APP_URL` matches the new domain.
- `APP_DEBUG` is off in production.
- The current host shown by the web server is exactly the new domain expected by the license endpoint.

## Success Criteria

- The admin verification request returns success.
- User login and dashboard access work on the new domain.
- No encoded-file edits were made.

## Risks

- If the vendor endpoint no longer exists, this option cannot complete.
- If the vendor endpoint only permits the old domain, this option cannot add the new domain without vendor-side action.
- If the site has cached old environment values, verification may continue using stale configuration until caches are cleared.

## Decision

Use this option if the vendor verification endpoint still exists. It is fast, reversible, and uses the app's intended activation workflow.
