import { test, expect } from '@playwright/test';

const USER_EMAIL = 'user@trading-wallet.net';
const USER_PASSWORD = 'user12345';
const ADMIN_EMAIL = 'admin@trading-wallet.net';
const ADMIN_PASSWORD = 'admin12345';

test('user login and navigate trading page', async ({ page }) => {
    const errors: string[] = [];
    page.on('pageerror', e => errors.push(`PAGE ERROR: ${e.message}`));
    page.on('console', msg => { if (msg.type() === 'error') errors.push(`CONSOLE: ${msg.text()}`); });

    // Login
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle', timeout: 30000 });
    await page.fill('input[name="email"]', USER_EMAIL);
    await page.fill('input[name="password"]', USER_PASSWORD);
    await page.screenshot({ path: 'screenshots/user-login-filled.png' });
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 30000 });
    
    console.log('After login URL:', page.url());
    console.log('After login title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-dashboard.png', fullPage: true });
    
    // Navigate to trade page
    await page.goto('http://localhost:8000/user/trade', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Trade page URL:', page.url());
    console.log('Trade page title:', await page.title());
    await page.screenshot({ path: 'screenshots/user-trade.png', fullPage: true });
    
    const tradeBody = await page.locator('body').innerText();
    console.log('Trade page text preview:', tradeBody.substring(0, 500));
    console.log('Errors:', errors);
});

test('admin login and navigate dashboard pages', async ({ page }) => {
    const errors: string[] = [];
    page.on('pageerror', e => errors.push(`PAGE ERROR: ${e.message}`));
    page.on('console', msg => { if (msg.type() === 'error') errors.push(`CONSOLE: ${msg.text()}`); });

    // Login to admin
    await page.goto('http://localhost:8000/admin/login', { waitUntil: 'networkidle', timeout: 30000 });
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.screenshot({ path: 'screenshots/admin-login-filled.png' });
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 30000 });
    
    console.log('After admin login URL:', page.url());
    console.log('After admin login title:', await page.title());
    await page.screenshot({ path: 'screenshots/admin-dashboard.png', fullPage: true });
    
    // Navigate to manage users
    await page.goto('http://localhost:8000/admin/users', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Manage users URL:', page.url());
    await page.screenshot({ path: 'screenshots/admin-users.png', fullPage: true });
    
    // Navigate to manage trades
    await page.goto('http://localhost:8000/admin/trades', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Manage trades URL:', page.url());
    await page.screenshot({ path: 'screenshots/admin-trades.png', fullPage: true });
    
    // Collect all sidebar links
    const sidebarLinks = await page.locator('aside a, .sidebar a, nav a').evaluateAll(els => 
        els.map(el => ({ href: el.getAttribute('href'), text: el.innerText?.trim() || '' }))
        .filter(l => l.href && l.href.includes('/admin/'))
    );
    console.log('Sidebar links:', JSON.stringify(sidebarLinks, null, 2));
    console.log('Errors:', errors);
});
