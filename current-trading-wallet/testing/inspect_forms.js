const { chromium } = require('playwright');
const fs = require('fs');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    
    // Check user login page
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle', timeout: 15000 });
    const inputs = await page.$$eval('input', els => els.map(e => ({name: e.name, type: e.type, id: e.id, placeholder: e.placeholder})));
    const buttons = await page.$$eval('button', els => els.map(e => ({type: e.type, text: e.textContent.trim().substring(0,50)})));
    const forms = await page.$$eval('form', els => els.map(e => ({action: e.action, method: e.method, id: e.id})));
    
    let output = '=== USER LOGIN PAGE ===\n';
    output += 'URL: ' + page.url() + '\n';
    output += 'Title: ' + await page.title() + '\n';
    output += 'INPUTS: ' + JSON.stringify(inputs, null, 2) + '\n';
    output += 'BUTTONS: ' + JSON.stringify(buttons, null, 2) + '\n';
    output += 'FORMS: ' + JSON.stringify(forms, null, 2) + '\n';
    
    // Check admin login page
    await page.goto('http://127.0.0.1:8000/admin/login', { waitUntil: 'networkidle', timeout: 15000 });
    const adminInputs = await page.$$eval('input', els => els.map(e => ({name: e.name, type: e.type, id: e.id, placeholder: e.placeholder})));
    const adminButtons = await page.$$eval('button', els => els.map(e => ({type: e.type, text: e.textContent.trim().substring(0,50)})));
    const adminForms = await page.$$eval('form', els => els.map(e => ({action: e.action, method: e.method, id: e.id})));
    
    output += '\n=== ADMIN LOGIN PAGE ===\n';
    output += 'URL: ' + page.url() + '\n';
    output += 'Title: ' + await page.title() + '\n';
    output += 'INPUTS: ' + JSON.stringify(adminInputs, null, 2) + '\n';
    output += 'BUTTONS: ' + JSON.stringify(adminButtons, null, 2) + '\n';
    output += 'FORMS: ' + JSON.stringify(adminForms, null, 2) + '\n';
    
    // Check register page
    await page.goto('http://127.0.0.1:8000/register', { waitUntil: 'networkidle', timeout: 15000 });
    const regInputs = await page.$$eval('input', els => els.map(e => ({name: e.name, type: e.type, id: e.id, placeholder: e.placeholder})));
    
    output += '\n=== REGISTER PAGE ===\n';
    output += 'URL: ' + page.url() + '\n';
    output += 'INPUTS: ' + JSON.stringify(regInputs, null, 2) + '\n';
    
    // Check deposit new page (will need login)
    await page.goto('http://127.0.0.1:8000/user/deposits/new', { waitUntil: 'networkidle', timeout: 15000 });
    output += '\n=== DEPOSIT NEW (unauthenticated) ===\n';
    output += 'URL: ' + page.url() + '\n';
    
    fs.writeFileSync('page_inspection.txt', output);
    console.log('Done - check page_inspection.txt');
    
    await browser.close();
})();
