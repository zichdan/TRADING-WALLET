import { test, expect } from '@playwright/test';

const USER_EMAIL = 'user@trading-wallet.net';
const USER_PASSWORD = 'user12345';
const ADMIN_EMAIL = 'admin@trading-wallet.net';
const ADMIN_PASSWORD = 'admin12345';

// User: login, dashboard, trade page, deposits, withdrawals, transactions, account
test('user full navigation', async ({ page }) => {
    const errors: string[] = [];
    page.on('pageerror', e => errors.push(`PAGE: ${e.message}`));
    page.on('console', msg => { if (msg.type() === 'error') errors.push(`CONSOLE: ${msg.text()}`); });

    // Login
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle', timeout: 30000 });
    await page.fill('input[name="email"]', USER_EMAIL);
    await page.fill('input[name="password"]', USER_PASSWORD);
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 30000 });
    console.log('User login URL:', page.url());
    await page.screenshot({ path: 'screenshots/user-dashboard.png', fullPage: true });

    // Collect all sidebar links
    const sidebarLinks = await page.locator('.sidenav-link, .sidebar a, aside a, nav a').evaluateAll(els =>
        els.map(el => ({ href: el.getAttribute('href'), text: el.innerText?.trim() || '' }))
        .filter(l => l.href && !l.href.includes('logout'))
    );
    console.log('User sidebar links:', JSON.stringify(sidebarLinks, null, 2));

    // Navigate to trade page
    await page.goto('http://localhost:8000/user/trading/trade/BTC/USDT', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Trade URL:', page.url(), 'Title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-trade.png', fullPage: true });

    // Navigate to deposits
    await page.goto('http://localhost:8000/user/deposits', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Deposits URL:', page.url(), 'Title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-deposits.png', fullPage: true });

    // Navigate to withdrawals
    await page.goto('http://localhost:8000/user/withdrawals', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Withdrawals URL:', page.url(), 'Title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-withdrawals.png', fullPage: true });

    // Navigate to transactions
    await page.goto('http://localhost:8000/user/transactions', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Transactions URL:', page.url(), 'Title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-transactions.png', fullPage: true });

    // Navigate to wallets
    await page.goto('http://localhost:8000/user/wallets', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Wallets URL:', page.url(), 'Title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-wallets.png', fullPage: true });

    // Navigate to account
    await page.goto('http://localhost:8000/user/account', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Account URL:', page.url(), 'Title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-account.png', fullPage: true });

    // Navigate to investments
    await page.goto('http://localhost:8000/user/investments', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Investments URL:', page.url(), 'Title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-investments.png', fullPage: true });

    console.log('User errors:', errors);
});

// Admin: login, dashboard, manage users, manage deposits, withdrawals, settings, etc.
test('admin full navigation', async ({ page }) => {
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
    await page.screenshot({ path: 'screenshots/admin-dashboard.png', fullPage: true });

    // Collect all admin sidebar links
    const sidebarLinks = await page.locator('aside a, .sidebar a, nav a, .sidenav-link').evaluateAll(els =>
        els.map(el => ({ href: el.getAttribute('href'), text: el.innerText?.trim() || '' }))
        .filter(l => l.href && l.href.includes('/admin/') && !l.href.includes('logout'))
    );
    console.log('Admin sidebar links:', JSON.stringify(sidebarLinks, null, 2));

    // Navigate key admin pages
    const adminPages = [
        { url: 'http://localhost:8000/admin/users', name: 'users' },
        { url: 'http://localhost:8000/admin/deposits', name: 'deposits' },
        { url: 'http://localhost:8000/admin/withdrawals', name: 'withdrawals' },
        { url: 'http://localhost:8000/admin/withdrawals/pending', name: 'withdrawals-pending' },
        { url: 'http://localhost:8000/admin/transactions', name: 'transactions' },
        { url: 'http://localhost:8000/admin/investments', name: 'investments' },
        { url: 'http://localhost:8000/admin/investment-plans', name: 'investment-plans' },
        { url: 'http://localhost:8000/admin/wallets', name: 'wallets' },
        { url: 'http://localhost:8000/admin/settings/core', name: 'settings-core' },
        { url: 'http://localhost:8000/admin/settings/email-templates', name: 'email-templates' },
        { url: 'http://localhost:8000/admin/settings/gateways', name: 'gateways' },
        { url: 'http://localhost:8000/admin/settings/misc', name: 'settings-misc' },
        { url: 'http://localhost:8000/admin/settings/logo-seo', name: 'logo-seo' },
        { url: 'http://localhost:8000/admin/settings/sections', name: 'sections' },
        { url: 'http://localhost:8000/admin/blogs', name: 'blogs' },
        { url: 'http://localhost:8000/admin/faqs', name: 'faqs' },
        { url: 'http://localhost:8000/admin/teams', name: 'teams' },
        { url: 'http://localhost:8000/admin/testimonials', name: 'testimonials' },
        { url: 'http://localhost:8000/admin/addons', name: 'addons' },
        { url: 'http://localhost:8000/admin/system-manager', name: 'system-manager' },
        { url: 'http://localhost:8000/admin/account', name: 'account' },
    ];

    for (const p of adminPages) {
        try {
            await page.goto(p.url, { waitUntil: 'networkidle', timeout: 20000 });
            const status = page.url().includes('404') || (await page.title()).includes('Not Found') ? '404' : 'OK';
            console.log(`Admin ${p.name}: ${status} - ${page.url()} - ${await page.title()}`);
            await page.screenshot({ path: `screenshots/admin-${p.name}.png`, fullPage: true });
        } catch (e: any) {
            console.log(`Admin ${p.name}: ERROR - ${e.message?.substring(0, 100)}`);
        }
    }

    console.log('Admin errors:', errors);
});
