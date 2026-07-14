# Option 1: Official License Transfer And Proof Package

## Purpose

Use your ownership evidence to request a domain transfer, reactivation, replacement build, or written permission from any remaining vendor, successor, marketplace, reseller, hosting contact, or payment processor record.

This is the cleanest legal route if a vendor-side license database still exists somewhere, even if the public `cred-crypt.com` site is no longer under the original owner.

## Why This Option Exists

ionCube can bind encoded code to domains, IP addresses, server properties, expiry dates, or encrypted license files. If the original vendor encoded the module for the old domain or validates the purchase code through a remote endpoint, only the original rights holder or their successor can issue a proper new-domain authorization.

## Local Evidence To Include

- Proof that the codebase contains a purchase-code verification flow.
- Screenshot or text note of the admin page titled `Purchase Code Verification`.
- Path reference: `public_html/trading-wallet.net/core/resources/views/admin/system-manager/license.blade.php`.
- Path reference: `public_html/trading-wallet.net/core/app/Http/Controllers/Admin/SystemController.php`.
- Path reference: `public_html/trading-wallet.net/core/app/Helpers/generalHelper.php`.
- The purchase/license key itself, kept outside public documentation.
- Old domain name, new domain name, hosting account name, and server IP history.
- Purchase receipt, invoice, email receipt, bank/card receipt, marketplace receipt, or support tickets.
- Evidence that the old domain expired and was replaced, not that the site was redistributed.

## Step-By-Step Implementation

1. Create a private folder outside the public web root named `license-evidence-private`.
2. Put purchase receipt screenshots, invoice PDFs, old support emails, and domain ownership evidence inside that private folder.
3. Save the old domain name, new domain name, old server IP, new server IP, purchase date, and approximate installation date in a private note.
4. Open the admin system manager license page on the new domain and capture the exact message shown after entering the purchase code.
5. Do not publish the purchase code in screenshots unless the screenshots stay private.
6. Identify every possible legitimate contact:
   - Original seller email from your purchase receipt.
   - Marketplace account if the script was purchased through a marketplace.
   - Current domain owner or successor business for the original vendor domain.
   - Payment processor merchant name from the original transaction.
   - Hosting provider records if the vendor installed the script for you.
7. Send a short license-transfer request with proof of purchase and old/new domain details.
8. Ask specifically for one of these remedies:
   - Add the new domain to the existing purchase code.
   - Generate a new domain-bound build.
   - Issue a replacement license file if the product uses one.
   - Provide written permission to replace the encoded module because the license service is unavailable.
9. Keep all replies in the private evidence folder.
10. If no party can help, document the failed contact attempts and move to option 4 or option 5.

## Success Criteria

- You receive a valid new-domain activation, replacement package, or written permission.
- The user dashboard works on the new domain without code tampering.
- The license status can be explained cleanly during future audits or hosting reviews.

## Risks

- The original vendor may truly be unavailable.
- A current domain owner may not own the old license database.
- The license may be bound to a server or encoded build that cannot be changed without the original encoder project.

## Decision

Use this option first if you still have receipts and want the lowest legal risk. Use it in parallel with option 3 because domain/session cache repair can be tested immediately.
