import { test, expect } from '@playwright/test';

test('homepage displays with CSS and theme', async ({ page }) => {
    // Capture console errors
    const consoleErrors: string[] = [];
    page.on('pageerror', error => consoleErrors.push(`PAGE ERROR: ${error.message}`));
    page.on('console', msg => {
        if (msg.type() === 'error') consoleErrors.push(`CONSOLE ERROR: ${msg.text()}`);
    });

    await page.goto('http://localhost:8000', { waitUntil: 'networkidle', timeout: 30000 });
    
    // Take screenshot
    await page.screenshot({ path: 'screenshots/homepage.png', fullPage: true });
    
    // Check if CSS is loaded by verifying computed styles
    const bodyBg = await page.evaluate(() => {
        const body = document.querySelector('body');
        return body ? window.getComputedStyle(body).backgroundColor : 'no body';
    });
    console.log('Body background:', bodyBg);
    
    // Check if any CSS files failed to load
    const failedResources: string[] = [];
    page.on('response', response => {
        if (response.status() >= 400) {
            failedResources.push(`${response.status()}: ${response.url()}`);
        }
    });
    
    // Log page title and any errors
    console.log('Page title:', await page.title());
    console.log('Console errors:', consoleErrors);
    console.log('Failed resources:', failedResources);
    
    // Check if the page has visible content
    const bodyText = await page.locator('body').innerText();
    console.log('Body text length:', bodyText.length);
    console.log('Body text preview:', bodyText.substring(0, 500));
});
