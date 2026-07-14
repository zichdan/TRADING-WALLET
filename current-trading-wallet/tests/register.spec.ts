import { test, expect } from '@playwright/test';

test('register page - country dropdown and form', async ({ page }) => {
    const consoleErrors: string[] = [];
    page.on('pageerror', error => consoleErrors.push(`PAGE ERROR: ${error.message}`));
    page.on('console', msg => {
        if (msg.type() === 'error') consoleErrors.push(`CONSOLE ERROR: ${msg.text()}`);
    });

    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle', timeout: 30000 });
    await page.screenshot({ path: 'screenshots/register.png', fullPage: true });
    
    // Check country dropdown
    const countrySelect = page.locator('select[name="country"]');
    const countryExists = await countrySelect.count();
    console.log('Country select exists:', countryExists);
    
    if (countryExists > 0) {
        const options = await countrySelect.locator('option').allTextContents();
        console.log('Country options count:', options.length);
        console.log('First 10 countries:', options.slice(0, 10).join(', '));
    }
    
    // Check all form fields
    const inputs = await page.locator('input, select, textarea').evaluateAll(els => 
        els.map(el => ({ tag: el.tagName, name: el.getAttribute('name'), type: el.getAttribute('type'), id: el.id }))
    );
    console.log('Form fields:', JSON.stringify(inputs, null, 2));
    
    console.log('Console errors:', consoleErrors);
    console.log('Page title:', await page.title());
});
