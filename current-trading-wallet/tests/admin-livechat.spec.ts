import { test, expect } from '@playwright/test';

const ADMIN_EMAIL = 'admin@trading-wallet.net';
const ADMIN_PASSWORD = 'admin12345';

test('admin livechat page', async ({ page }) => {
    const errors: string[] = [];
    page.on('pageerror', e => errors.push(`PAGE: ${e.message}`));
    page.on('console', msg => { if (msg.type() === 'error') errors.push(`CONSOLE: ${msg.text()}`); });

    await page.goto('http://localhost:8000/admin/login', { waitUntil: 'networkidle', timeout: 30000 });
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 30000 });

    await page.goto('http://localhost:8000/admin/settings/livechat', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('Livechat URL:', page.url());
    console.log('Livechat title:', await page.title());
    await page.screenshot({ path: 'screenshots/admin-full-settings-livechat.png', fullPage: true });
    console.log('Errors:', errors);
});
