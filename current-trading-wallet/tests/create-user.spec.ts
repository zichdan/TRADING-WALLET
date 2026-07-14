import { test, expect } from '@playwright/test';

const TEST_USER = {
    first_name: 'John',
    last_name: 'Doe',
    email: `john.doe.${Date.now()}@testwallet.com`,
    phone_no: '+1234567890',
    country: 'United States',
    password: 'TestPass123!',
};

test('create user account via registration form', async ({ page }) => {
    const consoleErrors: string[] = [];
    page.on('pageerror', error => consoleErrors.push(`PAGE ERROR: ${error.message}`));
    page.on('console', msg => {
        if (msg.type() === 'error') consoleErrors.push(`CONSOLE: ${msg.text()}`);
    });

    // Go to register page
    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle', timeout: 30000 });
    
    // Fill the registration form
    await page.fill('input[name="first_name"]', TEST_USER.first_name);
    await page.fill('input[name="last_name"]', TEST_USER.last_name);
    await page.fill('input[name="email"]', TEST_USER.email);
    await page.fill('input[name="phone_no"]', TEST_USER.phone_no);
    await page.selectOption('select[name="country"]', TEST_USER.country);
    await page.fill('input[name="password"]', TEST_USER.password);
    await page.fill('input[name="password_confirmation"]', TEST_USER.password);
    
    // Take screenshot before submit
    await page.screenshot({ path: 'screenshots/register-filled.png', fullPage: true });
    
    // Submit the form
    await page.click('input[type="submit"]');
    
    // Wait for navigation or response
    await page.waitForLoadState('networkidle', { timeout: 30000 });
    
    // Take screenshot after submit
    await page.screenshot({ path: 'screenshots/register-result.png', fullPage: true });
    
    console.log('URL after register:', page.url());
    console.log('Page title:', await page.title());
    console.log('Console errors:', consoleErrors);
    
    // Check if we got redirected to login or dashboard
    const bodyText = await page.locator('body').innerText();
    console.log('Body text preview:', bodyText.substring(0, 500));
    
    // Save credentials for next tests
    console.log('TEST_USER_EMAIL=' + TEST_USER.email);
    console.log('TEST_USER_PASSWORD=' + TEST_USER.password);
});
