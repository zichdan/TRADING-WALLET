import { test, expect } from '@playwright/test';

const USER_EMAIL = 'user@trading-wallet.net';
const USER_PASSWORD = 'user12345';

test('user demo and staking pages', async ({ page }) => {
    const errors: string[] = [];
    page.on('pageerror', e => errors.push(`PAGE: ${e.message}`));
    page.on('console', msg => { if (msg.type() === 'error') errors.push(`CONSOLE: ${msg.text()}`); });

    // Login
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle', timeout: 30000 });
    await page.fill('input[name="email"]', USER_EMAIL);
    await page.fill('input[name="password"]', USER_PASSWORD);
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 30000 });

    // Demo trade page
    await page.goto('http://localhost:8000/user/trading/demo', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Demo URL:', page.url(), 'Title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-full-trading-demo.png', fullPage: true });

    // Staking page
    await page.goto('http://localhost:8000/user/trading/coin-stakings', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Staking URL:', page.url(), 'Title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-full-trading-staking.png', fullPage: true });

    console.log('Errors:', errors);
});
