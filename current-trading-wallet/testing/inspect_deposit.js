const { chromium } = require('playwright');
const fs = require('fs');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle', timeout: 15000 });
    await page.fill('input[name="email"]', 'testuser@trading-wallet.net');
    await page.fill('input[name="password"]', 'User@2026!');
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 15000 });
    
    await page.goto('http://127.0.0.1:8000/user/deposits/new', { waitUntil: 'networkidle', timeout: 15000 });
    
    // Get all clickable elements and forms
    var inputs = await page.$$eval('input', els => els.map(e => ({name: e.name, type: e.type, value: e.value, id: e.id, class: e.className.substring(0,50)})));
    var selects = await page.$$eval('select', els => els.map(e => ({name: e.name, id: e.id, class: e.className.substring(0,50)})));
    var links = await page.$$eval('a', els => els.map(e => ({href: e.href, text: e.textContent.trim().substring(0,50)})).filter(a => a.text.length > 0));
    var forms = await page.$$eval('form', els => els.map(e => ({action: e.action, method: e.method, id: e.id, class: e.className.substring(0,50)})));
    var buttons = await page.$$eval('button', els => els.map(e => ({type: e.type, text: e.textContent.trim().substring(0,50), class: e.className.substring(0,50)})));
    
    var output = '=== DEPOSIT NEW PAGE ===\n';
    output += 'URL: ' + page.url() + '\n\n';
    output += 'FORMS: ' + JSON.stringify(forms, null, 2) + '\n\n';
    output += 'INPUTS: ' + JSON.stringify(inputs, null, 2) + '\n\n';
    output += 'SELECTS: ' + JSON.stringify(selects, null, 2) + '\n\n';
    output += 'BUTTONS: ' + JSON.stringify(buttons, null, 2) + '\n\n';
    output += 'LINKS: ' + JSON.stringify(links.slice(0, 20), null, 2) + '\n';
    
    // Also get the visible text to understand the page structure
    var bodyText = await page.evaluate(() => document.body ? document.body.innerText.substring(0, 3000) : '');
    output += '\nBODY TEXT:\n' + bodyText + '\n';
    
    fs.writeFileSync('deposit_page_inspection.txt', output);
    console.log('Done - check deposit_page_inspection.txt');
    
    await browser.close();
})();
