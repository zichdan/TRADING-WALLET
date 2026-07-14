import { test, expect } from '@playwright/test';

const USER_EMAIL = 'user@trading-wallet.net';
const USER_PASSWORD = 'user12345';

test('user all sidebar pages', async ({ page }) => {
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

    // All user sidebar pages
    const userPages = [
        { url: 'http://localhost:8000/user/dashboard', name: 'dashboard' },
        { url: 'http://localhost:8000/user/trading/wallets', name: 'trading-wallets' },
        { url: 'http://localhost:8000/user/trading/trade/BTC/USDT', name: 'trading-trade' },
        { url: 'http://localhost:8000/user/trading/demo/BTC/USDT', name: 'trading-demo' },
        { url: 'http://localhost:8000/user/trading/staking', name: 'trading-staking' },
        { url: 'http://localhost:8000/user/trading/trade/bot/BTC/USDT', name: 'trading-bot' },
        { url: 'http://localhost:8000/user/deposits', name: 'deposits' },
        { url: 'http://localhost:8000/user/deposits/new', name: 'deposits-new' },
        { url: 'http://localhost:8000/user/withdrawals', name: 'withdrawals' },
        { url: 'http://localhost:8000/user/withdrawals/new', name: 'withdrawals-new' },
        { url: 'http://localhost:8000/user/wallets', name: 'wallets' },
        { url: 'http://localhost:8000/user/wallets/new', name: 'wallets-new' },
        { url: 'http://localhost:8000/user/investments', name: 'investments' },
        { url: 'http://localhost:8000/user/investments/new', name: 'investments-new' },
        { url: 'http://localhost:8000/user/transactions', name: 'transactions' },
        { url: 'http://localhost:8000/user/account', name: 'account' },
        { url: 'http://localhost:8000/user/account/edit', name: 'account-edit' },
    ];

    let ok = 0, fail = 0;
    for (const p of userPages) {
        try {
            await page.goto(p.url, { waitUntil: 'networkidle', timeout: 20000 });
            const title = await page.title();
            const is404 = page.url().includes('404') || title.includes('Not Found');
            const isError = title.includes('Error') || title.includes('Exception');
            const status = is404 ? '404' : isError ? 'ERROR' : 'OK';
            if (status === 'OK') ok++; else fail++;
            console.log(`[${status}] ${p.name}: ${page.url()} - ${title}`);
            await page.screenshot({ path: `screenshots/user-full-${p.name}.png`, fullPage: true });
        } catch (e: any) {
            fail++;
            console.log(`[FAIL] ${p.name}: ${e.message?.substring(0, 100)}`);
        }
    }

    console.log(`\n=== Summary: ${ok} OK, ${fail} FAIL out of ${userPages.length} pages ===`);
    console.log('Errors:', errors);
});
