const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle', timeout: 30000 });
    console.log('URL:', page.url());
    console.log('TITLE:', await page.title());
    const bodyText = await page.evaluate(() => document.body ? document.body.innerText.substring(0, 2000) : 'NO BODY');
    console.log('BODY TEXT:', bodyText);
    const html = await page.content();
    console.log('HTML LENGTH:', html.length);
    console.log('HTML SNIPPET:', html.substring(0, 3000));
    
    await page.screenshot({ path: 'screenshots/inspect_login.png' });
    
    await browser.close();
})();
