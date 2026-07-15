const { chromium } = require('playwright');
const fs = require('fs');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    // Login as admin and check deposit detail page
    await page.goto('http://127.0.0.1:8000/admin/login', { waitUntil: 'networkidle', timeout: 15000 });
    await page.fill('input[name="email"]', 'admin@trading-wallet.net');
    await page.fill('input[name="password"]', 'Admin@2026!');
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 15000 });
    
    await page.goto('http://127.0.0.1:8000/admin/deposits/view/1', { waitUntil: 'networkidle', timeout: 15000 });
    
    var forms = await page.$$eval('form', els => els.map(e => ({action: e.action, method: e.method, id: e.id})));
    var inputs = await page.$$eval('input', els => els.map(e => ({name: e.name, type: e.type, value: e.value, id: e.id})).filter(i => !i.name.includes('goog')));
    var buttons = await page.$$eval('button', els => els.map(e => ({type: e.type, text: e.textContent.trim().substring(0, 80), class: e.className.substring(0, 60)})));
    var links = await page.$$eval('a', els => els.map(e => ({href: e.href, text: e.textContent.trim().substring(0, 60)})).filter(a => a.text.length > 0 && a.text.length < 60 && !a.href.includes('google')));
    var bodyText = await page.evaluate(() => document.body ? document.body.innerText.substring(0, 3000) : '');
    
    var output = '=== ADMIN DEPOSIT DETAIL PAGE ===\n';
    output += 'URL: ' + page.url() + '\n\n';
    output += 'FORMS: ' + JSON.stringify(forms, null, 2) + '\n\n';
    output += 'INPUTS: ' + JSON.stringify(inputs, null, 2) + '\n\n';
    output += 'BUTTONS: ' + JSON.stringify(buttons, null, 2) + '\n\n';
    output += 'LINKS: ' + JSON.stringify(links, null, 2) + '\n\n';
    output += 'BODY TEXT:\n' + bodyText + '\n';
    
    // Now check ETH/USDT trade page
    await page.goto('http://127.0.0.1:8000/admin/logout', { waitUntil: 'networkidle', timeout: 15000 }).catch(() => {});
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle', timeout: 15000 });
    await page.fill('input[name="email"]', 'testuser@trading-wallet.net');
    await page.fill('input[name="password"]', 'User@2026!');
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 15000 });
    
    await page.goto('http://127.0.0.1:8000/user/trading/trade/ETH/USDT', { waitUntil: 'networkidle', timeout: 20000 });
    await page.waitForTimeout(5000); // Wait longer for JS rendering
    
    var tradeForms = await page.$$eval('form', els => els.map(e => ({action: e.action, method: e.method, id: e.id})));
    var tradeInputs = await page.$$eval('input', els => els.map(e => ({name: e.name, type: e.type, placeholder: e.placeholder, id: e.id})).filter(i => !i.name.includes('goog') && !i.id.includes('goog')));
    var tradeButtons = await page.$$eval('button', els => els.map(e => ({type: e.type, text: e.textContent.trim().substring(0, 80), class: e.className.substring(0, 80)})).filter(b => b.text.length > 0));
    var tradeLinks = await page.$$eval('a', els => els.map(e => ({href: e.href, text: e.textContent.trim().substring(0, 60)})).filter(a => a.text.length > 0 && a.text.length < 60 && !a.href.includes('google')));
    var tradeBody = await page.evaluate(() => document.body ? document.body.innerText.substring(0, 3000) : '');
    var tradeIframes = await page.$$eval('iframe', els => els.map(e => ({src: e.src, id: e.id, name: e.name})));
    
    output += '\n\n=== ETH/USDT TRADE PAGE ===\n';
    output += 'URL: ' + page.url() + '\n\n';
    output += 'IFRAMES: ' + JSON.stringify(tradeIframes, null, 2) + '\n\n';
    output += 'FORMS: ' + JSON.stringify(tradeForms, null, 2) + '\n\n';
    output += 'INPUTS: ' + JSON.stringify(tradeInputs, null, 2) + '\n\n';
    output += 'BUTTONS: ' + JSON.stringify(tradeButtons, null, 2) + '\n\n';
    output += 'LINKS: ' + JSON.stringify(tradeLinks.slice(0, 20), null, 2) + '\n\n';
    output += 'BODY TEXT:\n' + tradeBody + '\n';
    
    fs.writeFileSync('trade_detail_inspection.txt', output);
    console.log('Done - check trade_detail_inspection.txt');
    
    await browser.close();
})();
