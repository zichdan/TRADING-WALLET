const { chromium } = require('playwright');
const fs = require('fs');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    
    // Login as user
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle', timeout: 15000 });
    await page.fill('input[name="email"]', 'testuser@trading-wallet.net');
    await page.fill('input[name="password"]', 'User@2026!');
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 15000 });
    
    // Go to deposit new page
    await page.goto('http://127.0.0.1:8000/user/deposits/new', { waitUntil: 'networkidle', timeout: 15000 });
    
    // Check the radio and fill amount
    await page.check('input[name="method_id"]', { force: true }).catch(() => {});
    await page.fill('input[name="amount"]', '5000');
    
    // Get the form action and all buttons
    var formData = await page.evaluate(() => {
        var form = document.querySelector('form[action*="deposits/pay"]');
        if (!form) return 'No form found';
        var btns = Array.from(form.querySelectorAll('button, input[type="submit"]')).map(b => ({
            tag: b.tagName, type: b.type, text: b.textContent.trim().substring(0, 80), 
            value: b.value, class: b.className.substring(0, 80), id: b.id
        }));
        return JSON.stringify(btns, null, 2);
    });
    
    var output = '=== DEPOSIT FORM BUTTONS ===\n' + formData + '\n\n';
    
    // Try clicking Next and see what happens
    var nextBtn = await page.$('button:has-text("Next")');
    if (nextBtn) {
        // Check if it's inside the right form
        var isInForm = await nextBtn.evaluate(el => {
            var form = el.closest('form');
            return form ? form.action : 'no form';
        });
        output += 'Next button form action: ' + isInForm + '\n';
        
        // Try clicking with force
        await nextBtn.click({ force: true }).catch(e => output += 'Click error: ' + e.message + '\n');
        await page.waitForTimeout(3000);
        output += 'After click URL: ' + page.url() + '\n';
        
        // Check page content after click
        var afterContent = await page.evaluate(() => document.body ? document.body.innerText.substring(0, 2000) : '');
        output += '\nAfter click body text:\n' + afterContent + '\n';
    }
    
    // Now check trading page
    await page.goto('http://127.0.0.1:8000/user/trading/trade/index', { waitUntil: 'networkidle', timeout: 20000 });
    await page.waitForTimeout(3000);
    
    var tradeInputs = await page.$$eval('input', els => els.map(e => ({name: e.name, type: e.type, id: e.id, placeholder: e.placeholder, class: e.className.substring(0,50)})));
    var tradeButtons = await page.$$eval('button', els => els.map(e => ({type: e.type, text: e.textContent.trim().substring(0, 80), class: e.className.substring(0, 80)})).filter(b => b.text.length > 0));
    var tradeLinks = await page.$$eval('a', els => els.map(e => ({href: e.href, text: e.textContent.trim().substring(0, 50)})).filter(a => a.text.length > 0 && a.text.length < 50));
    var tradeForms = await page.$$eval('form', els => els.map(e => ({action: e.action, method: e.method, id: e.id})));
    var tradeBody = await page.evaluate(() => document.body ? document.body.innerText.substring(0, 3000) : '');
    
    output += '\n=== TRADING PAGE ===\n';
    output += 'URL: ' + page.url() + '\n';
    output += 'FORMS: ' + JSON.stringify(tradeForms, null, 2) + '\n\n';
    output += 'INPUTS: ' + JSON.stringify(tradeInputs, null, 2) + '\n\n';
    output += 'BUTTONS: ' + JSON.stringify(tradeButtons, null, 2) + '\n\n';
    output += 'LINKS: ' + JSON.stringify(tradeLinks.slice(0, 30), null, 2) + '\n\n';
    output += 'BODY TEXT:\n' + tradeBody + '\n';
    
    // Also check trading wallets page
    await page.goto('http://127.0.0.1:8000/user/trading/wallets', { waitUntil: 'networkidle', timeout: 15000 });
    var walletForms = await page.$$eval('form', els => els.map(e => ({action: e.action, method: e.method, id: e.id})));
    var walletInputs = await page.$$eval('input', els => els.map(e => ({name: e.name, type: e.type, value: e.value, placeholder: e.placeholder})));
    var walletButtons = await page.$$eval('button', els => els.map(e => ({type: e.type, text: e.textContent.trim().substring(0, 80)})).filter(b => b.text.length > 0));
    var walletSelects = await page.$$eval('select', els => els.map(e => ({name: e.name, id: e.id, options: Array.from(e.options).map(o => o.text)})));
    var walletBody = await page.evaluate(() => document.body ? document.body.innerText.substring(0, 2000) : '');
    
    output += '\n=== TRADING WALLETS PAGE ===\n';
    output += 'FORMS: ' + JSON.stringify(walletForms, null, 2) + '\n\n';
    output += 'INPUTS: ' + JSON.stringify(walletInputs, null, 2) + '\n\n';
    output += 'SELECTS: ' + JSON.stringify(walletSelects, null, 2) + '\n\n';
    output += 'BUTTONS: ' + JSON.stringify(walletButtons, null, 2) + '\n\n';
    output += 'BODY TEXT:\n' + walletBody + '\n';
    
    fs.writeFileSync('trading_page_inspection.txt', output);
    console.log('Done - check trading_page_inspection.txt');
    
    await browser.close();
})();
