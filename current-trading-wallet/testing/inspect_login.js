const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle', timeout: 15000 });
    
    const inputs = await page.$$eval('input', els => els.map(e => ({name: e.name, type: e.type, id: e.id, placeholder: e.placeholder, class: e.className})));
    console.log('INPUTS:', JSON.stringify(inputs, null, 2));
    
    const buttons = await page.$$eval('button', els => els.map(e => ({type: e.type, text: e.textContent.trim(), class: e.className})));
    console.log('BUTTONS:', JSON.stringify(buttons, null, 2));
    
    const forms = await page.$$eval('form', els => els.map(e => ({action: e.action, method: e.method, id: e.id})));
    console.log('FORMS:', JSON.stringify(forms, null, 2));
    
    // Also check admin login page
    await page.goto('http://127.0.0.1:8000/admin/login', { waitUntil: 'networkidle', timeout: 15000 });
    const adminInputs = await page.$$eval('input', els => els.map(e => ({name: e.name, type: e.type, id: e.id, placeholder: e.placeholder})));
    console.log('ADMIN INPUTS:', JSON.stringify(adminInputs, null, 2));
    
    await browser.close();
})();
