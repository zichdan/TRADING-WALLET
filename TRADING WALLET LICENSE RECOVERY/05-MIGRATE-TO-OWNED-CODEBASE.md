# Option 5: Migrate To A Fully Owned Replacement Codebase

## Purpose

Move the business away from the abandoned vendor script and into a codebase you control completely.

This is the best long-term option when the vendor is gone, critical user functionality is encoded, and dependency/security advisories remain unresolved.

## Why This Option Exists

The current backup can be made to run locally, but it has practical long-term risks:

- Critical module code is ionCube-protected.
- The original vendor licensing endpoint may be unavailable.
- The new domain may not be accepted by the old activation service.
- Some dependency advisories already need production hardening.
- Future PHP/Laravel upgrades will be difficult if core business logic remains encoded.

## Step-By-Step Implementation

1. Freeze the current production site.
2. Back up the database.
3. Back up uploaded files.
4. Export a schema map:
   - Users.
   - Wallets.
   - Deposits.
   - Withdrawals.
   - Trading wallets.
   - Trading logs.
   - KYC.
   - Support tickets.
   - Manual deposits.
5. Build a new Laravel application or modern full-stack app under your ownership.
6. Recreate authentication with fresh, audited code.
7. Recreate wallet read models first.
8. Recreate transaction history.
9. Recreate KYC and support ticket flows.
10. Recreate admin management screens.
11. Recreate trading screens only after wallet/accounting rules are documented.
12. Write migration scripts from the old database to the new schema.
13. Run migration on staging.
14. Test user login and dashboard on the new domain.
15. Test admin workflows.
16. Test deposits and withdrawals in sandbox mode only.
17. Run security review.
18. Run dependency audit.
19. Prepare DNS cutover.
20. Launch the new domain on the owned codebase.

## What To Reuse

Allowed reuse should be limited to material you are licensed to use:

- Your database data.
- Your uploaded media.
- Your public branding and copy.
- Open-source dependencies under their licenses.
- Public Blade templates/assets only if your purchase license permits reuse.

Do not reuse decrypted or reverse-engineered source from encoded files.

## Success Criteria

- The new domain runs without vendor license dependency.
- User login and dashboard work from owned source code.
- Admin workflows are maintainable.
- Future upgrades do not depend on an unavailable vendor.

## Risks

- This is the largest effort.
- Wallet and trading logic require careful accounting review.
- A staged migration is necessary to avoid downtime and data loss.

## Decision

Choose this option if the business needs a durable, auditable platform and cannot risk future lockouts from abandoned encoded code.
