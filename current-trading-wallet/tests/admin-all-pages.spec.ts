import { test, expect } from '@playwright/test';

const ADMIN_EMAIL = 'admin@trading-wallet.net';
const ADMIN_PASSWORD = 'admin12345';

test('admin all sidebar pages', async ({ page }) => {
    const errors: string[] = [];
    page.on('pageerror', e => errors.push(`PAGE: ${e.message}`));
    page.on('console', msg => { if (msg.type() === 'error') errors.push(`CONSOLE: ${msg.text()}`); });

    // Login to admin
    await page.goto('http://localhost:8000/admin/login', { waitUntil: 'networkidle', timeout: 30000 });
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 30000 });
    console.log('Admin login URL:', page.url());

    // All admin sidebar pages from the collected links
    const adminPages = [
        { url: 'http://localhost:8000/admin/dashbaord', name: 'dashboard' },
        { url: 'http://localhost:8000/admin/users', name: 'users' },
        { url: 'http://localhost:8000/admin/users?users_query=status&users_by=active', name: 'users-active' },
        { url: 'http://localhost:8000/admin/users?users_query=status&users_by=suspended', name: 'users-suspended' },
        { url: 'http://localhost:8000/admin/users/email', name: 'users-email' },
        { url: 'http://localhost:8000/admin/trading/wallets', name: 'trading-wallets' },
        { url: 'http://localhost:8000/admin/trading/signals', name: 'trading-signals' },
        { url: 'http://localhost:8000/admin/trading/staking-coins', name: 'trading-staking' },
        { url: 'http://localhost:8000/admin/trading/trading-bots', name: 'trading-bots' },
        { url: 'http://localhost:8000/admin/deposits', name: 'deposits' },
        { url: 'http://localhost:8000/admin/deposit-method', name: 'deposit-method' },
        { url: 'http://localhost:8000/admin/deposit-method/new', name: 'deposit-method-new' },
        { url: 'http://localhost:8000/admin/settings/gateways', name: 'gateways' },
        { url: 'http://localhost:8000/admin/withdrawals', name: 'withdrawals' },
        { url: 'http://localhost:8000/admin/withdrawals/pending', name: 'withdrawals-pending' },
        { url: 'http://localhost:8000/admin/wallets', name: 'wallets' },
        { url: 'http://localhost:8000/admin/investments', name: 'investments' },
        { url: 'http://localhost:8000/admin/investment-plans', name: 'investment-plans' },
        { url: 'http://localhost:8000/admin/investment-plans/new', name: 'investment-plans-new' },
        { url: 'http://localhost:8000/admin/transactions', name: 'transactions' },
        { url: 'http://localhost:8000/admin/faqs', name: 'faqs' },
        { url: 'http://localhost:8000/admin/faqs/new', name: 'faqs-new' },
        { url: 'http://localhost:8000/admin/tickets', name: 'tickets' },
        { url: 'http://localhost:8000/admin/tickets/new', name: 'tickets-new' },
        { url: 'http://localhost:8000/admin/testimonials', name: 'testimonials' },
        { url: 'http://localhost:8000/admin/testimonials/new', name: 'testimonials-new' },
        { url: 'http://localhost:8000/admin/teams', name: 'teams' },
        { url: 'http://localhost:8000/admin/teams/new', name: 'teams-new' },
        { url: 'http://localhost:8000/admin/blogs', name: 'blogs' },
        { url: 'http://localhost:8000/admin/blogs/new', name: 'blogs-new' },
        { url: 'http://localhost:8000/admin/system-manager', name: 'system-manager' },
        { url: 'http://localhost:8000/admin/settings/core', name: 'settings-core' },
        { url: 'http://localhost:8000/admin/settings/logo-seo', name: 'settings-logo-seo' },
        { url: 'http://localhost:8000/admin/settings/sections', name: 'settings-sections' },
        { url: 'http://localhost:8000/admin/settings/security-otp', name: 'settings-security-otp' },
        { url: 'http://localhost:8000/admin/settings/email-config', name: 'settings-email-config' },
        { url: 'http://localhost:8000/admin/settings/email-templates', name: 'settings-email-templates' },
        { url: 'http://localhost:8000/admin/settings/livechat', name: 'settings-livechat' },
        { url: 'http://localhost:8000/admin/settings/theme-manager', name: 'settings-theme-manager' },
        { url: 'http://localhost:8000/admin/settings/theme-manager/exports', name: 'settings-theme-exports' },
        { url: 'http://localhost:8000/admin/settings/theme-manager/new-theme', name: 'settings-theme-new' },
        { url: 'http://localhost:8000/admin/settings/misc', name: 'settings-misc' },
        { url: 'http://localhost:8000/admin/settings/custom-css', name: 'settings-custom-css' },
        { url: 'http://localhost:8000/admin/settings/custom-js', name: 'settings-custom-js' },
        { url: 'http://localhost:8000/admin/settings/custom-php', name: 'settings-custom-php' },
        { url: 'http://localhost:8000/admin/addons', name: 'addons' },
        { url: 'http://localhost:8000/admin/account', name: 'account' },
    ];

    let ok = 0, fail = 0;
    for (const p of adminPages) {
        try {
            await page.goto(p.url, { waitUntil: 'networkidle', timeout: 20000 });
            const title = await page.title();
            const is404 = page.url().includes('404') || title.includes('Not Found');
            const isError = title.includes('Error') || title.includes('Exception');
            const status = is404 ? '404' : isError ? 'ERROR' : 'OK';
            if (status === 'OK') ok++; else fail++;
            console.log(`[${status}] ${p.name}: ${page.url()} - ${title}`);
            await page.screenshot({ path: `screenshots/admin-full-${p.name}.png`, fullPage: true });
        } catch (e: any) {
            fail++;
            console.log(`[FAIL] ${p.name}: ${e.message?.substring(0, 100)}`);
        }
    }

    console.log(`\n=== Summary: ${ok} OK, ${fail} FAIL out of ${adminPages.length} pages ===`);
    console.log('Errors:', errors);
});
