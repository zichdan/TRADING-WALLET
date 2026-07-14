# Option 3: Domain, Laravel Cache, And Session Repair

## Purpose

Fix domain-migration problems that can look like licensing failures, especially when admin login works but user login or the trading dashboard logs out.

This option is important because user logout can be caused by cookies, sessions, HTTPS, cached `.env` values, guard redirects, or old domain values.

## Local Evidence

- Both inspected `.env` files contain `APP_URL`.
- Neither inspected `.env` file has `SESSION_DOMAIN`.
- The canonical app uses Laravel session configuration from `config/session.php`.
- Laravel caches may preserve stale environment values after a domain migration.
- The app has user, admin, module, and trading routes that can behave differently depending on middleware and session state.

## Step-By-Step Implementation

1. Put the site in maintenance mode during production repair:

```powershell
php artisan down
```

2. Back up the live `.env` file without publishing it.
3. Back up the live database.
4. Confirm the intended canonical domain:
   - Decide whether the site should use `example.com` or `www.example.com`.
   - Use one canonical host.
   - Redirect the other host to the canonical host.
5. Update the `.env` domain settings:
   - `APP_URL` should match the canonical HTTPS domain.
   - Add `SESSION_DOMAIN` only if needed for subdomain sharing.
   - Keep `SESSION_DOMAIN` empty or absent if only one exact host is used.
6. Confirm HTTPS is valid for the new domain.
7. Confirm cookies are not being set for the old domain.
8. Confirm `config/session.php` is not hardcoded to the old domain.
9. Clear Laravel caches:

```powershell
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

10. Clear server cache, CDN cache, and browser cookies for the old and new domains.
11. Confirm storage permissions:

```powershell
php artisan storage:link
```

12. Rebuild production cache:

```powershell
php artisan config:cache
php artisan route:cache
```

13. Bring the site back:

```powershell
php artisan up
```

14. Test in a private/incognito browser:
   - Public home page.
   - User login.
   - User dashboard.
   - Trading wallet dashboard.
   - Admin login.
   - Admin system manager.
15. If user login still redirects or logs out, inspect:
   - `storage/logs/laravel.log`
   - Browser cookies for domain/path/secure flags.
   - User guard middleware.
   - CryptoTrading middleware.
   - Any old-domain redirects in `.htaccess`, database settings, or cached config.

## Success Criteria

- User login persists after page reload.
- User dashboard opens on the new domain.
- Trading wallet dashboard opens without redirecting back to login.
- Admin login still works.
- No ionCube or license bypass was attempted.

## Risks

- If the encoded module itself is domain-locked to the old domain, cache/session repair will not be enough.
- If the vendor endpoint rejects the new domain, option 2 or option 1 is still required.
- If the live database contains old-domain URLs in settings, those rows must be updated carefully after a database backup.

## Decision

Use this option first. It is the fastest safe repair path for the reported symptom and can be completed without changing protected code.
