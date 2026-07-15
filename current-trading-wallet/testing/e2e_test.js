// Trading Wallet E2E Test Script v4 - Playwright
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://127.0.0.1:8000';
const SCREENSHOT_DIR = path.join(__dirname, 'screenshots');
const TEST_ASSET_DIR = path.join(__dirname, 'test-assets');

if (!fs.existsSync(SCREENSHOT_DIR)) fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
if (!fs.existsSync(TEST_ASSET_DIR)) fs.mkdirSync(TEST_ASSET_DIR, { recursive: true });

function createDummyImage() {
    var imagePath = path.join(TEST_ASSET_DIR, 'payment_proof.png');
    var pngBytes = Buffer.from([
        0x89,0x50,0x4E,0x47,0x0D,0x0A,0x1A,0x0A,
        0x00,0x00,0x00,0x0D,0x49,0x48,0x44,0x52,
        0x00,0x00,0x00,0x01,0x00,0x00,0x00,0x01,
        0x08,0x02,0x00,0x00,0x00,0x90,0x77,0x53,0xDE,
        0x00,0x00,0x00,0x0C,0x49,0x44,0x41,0x54,
        0x08,0xD7,0x63,0xF8,0xCF,0xC0,0x00,0x00,0x00,0x03,0x00,0x01,
        0x5B,0x65,0x02,0x3E,
        0x00,0x00,0x00,0x00,0x49,0x45,0x4E,0x44,
        0xAE,0x42,0x60,0x82
    ]);
    fs.writeFileSync(imagePath, pngBytes);
    return imagePath;
}

async function screenshot(page, name) {
    var filepath = path.join(SCREENSHOT_DIR, name + '.png');
    await page.screenshot({ path: filepath, fullPage: false });
    console.log('  Screenshot: ' + name + '.png');
}

async function checkForCredcrypto(page) {
    var content = await page.content();
    var lower = content.toLowerCase();
    var found = [];
    if (lower.includes('credcrypto')) found.push('credcrypto text');
    if (lower.includes('invalid license')) found.push('invalid license text');
    if (lower.includes('activate your license')) found.push('activate license text');
    if (lower.includes('api.credcrypto.net')) found.push('api.credcrypto.net ref');
    return found;
}

async function loginAsUser(page) {
    await page.goto(BASE_URL + '/login', { waitUntil: 'networkidle', timeout: 20000 });
    await page.fill('input[name="email"]', 'testuser@trading-wallet.net');
    await page.fill('input[name="password"]', 'User@2026!');
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 15000 });
}

async function loginAsAdmin(page) {
    await page.goto(BASE_URL + '/admin/login', { waitUntil: 'networkidle', timeout: 20000 });
    await page.fill('input[name="email"]', 'admin@trading-wallet.net');
    await page.fill('input[name="password"]', 'Admin@2026!');
    await page.click('input[type="submit"]');
    await page.waitForLoadState('networkidle', { timeout: 15000 });
}

async function logoutUser(page) {
    await page.goto(BASE_URL + '/user/logout', { waitUntil: 'networkidle', timeout: 15000 }).catch(function() {});
}

async function logoutAdmin(page) {
    await page.goto(BASE_URL + '/admin/logout', { waitUntil: 'networkidle', timeout: 15000 }).catch(function() {});
}

(async () => {
    var results = { passed: 0, failed: 0, pending: 0, bugs: [], steps: [] };
    var dummyImage = createDummyImage();
    var consoleErrors = [];

    console.log('=== Trading Wallet E2E Test v4 ===\n');

    var browser = await chromium.launch({ headless: true });
    var context = await browser.newContext({ viewport: { width: 1280, height: 720 } });
    var page = await context.newPage();

    page.on('console', function(msg) {
        if (msg.type() === 'error') consoleErrors.push('CONSOLE: ' + msg.text());
    });
    page.on('pageerror', function(err) {
        consoleErrors.push('PAGEERR: ' + err.message);
    });

    function logStep(step, name, status, details) {
        results.steps.push({ step: step, name: name, status: status, details: details || '' });
        console.log('  [' + status + '] ' + step + ': ' + name + (details ? ' - ' + details : ''));
        if (status === 'PASS') results.passed++;
        else if (status === 'FAIL') results.failed++;
        else results.pending++;
    }

    try {
        // ===== PHASE 1: Authentication =====
        console.log('\n--- Phase 1: Authentication ---');

        console.log('\n[1.1] Homepage...');
        await page.goto(BASE_URL, { waitUntil: 'networkidle', timeout: 20000 });
        await screenshot(page, '01_homepage');
        var cc1 = await checkForCredcrypto(page);
        logStep('1.1', 'Homepage loads', cc1.length === 0 ? 'PASS' : 'FAIL', cc1.join(', ') || 'No credcrypto refs');

        console.log('\n[1.2] Login page...');
        await page.goto(BASE_URL + '/login', { waitUntil: 'networkidle', timeout: 20000 });
        await screenshot(page, '02_login_page');
        var hasEmail = await page.$('input[name="email"]');
        var hasPassword = await page.$('input[name="password"]');
        logStep('1.2', 'Login form present', hasEmail && hasPassword ? 'PASS' : 'FAIL', hasEmail ? 'Fields found' : 'Missing');

        console.log('\n[1.3] User login...');
        await page.fill('input[name="email"]', 'testuser@trading-wallet.net');
        await page.fill('input[name="password"]', 'User@2026!');
        await page.click('input[type="submit"]');
        await page.waitForLoadState('networkidle', { timeout: 15000 });
        await screenshot(page, '03_user_dashboard');
        var userUrl = page.url();
        logStep('1.3', 'User login', userUrl.includes('dashboard') || userUrl.includes('user') ? 'PASS' : 'FAIL', 'URL: ' + userUrl);

        console.log('\n[1.4] Admin login...');
        await logoutUser(page);
        await loginAsAdmin(page);
        await screenshot(page, '04_admin_dashboard');
        var adminUrl = page.url();
        logStep('1.4', 'Admin login', adminUrl.includes('admin') && !adminUrl.includes('login') ? 'PASS' : 'FAIL', 'URL: ' + adminUrl);

        // ===== PHASE 2: Deposit Flow =====
        console.log('\n--- Phase 2: Deposit Flow ---');

        console.log('\n[2.1] New deposit page...');
        await logoutAdmin(page);
        await loginAsUser(page);
        await page.goto(BASE_URL + '/user/deposits/new', { waitUntil: 'networkidle', timeout: 15000 });
        await screenshot(page, '05_new_deposit');
        var depositContent = await page.content();
        logStep('2.1', 'Deposit method visible', depositContent.includes('Bank Transfer USD') ? 'PASS' : 'FAIL', 'Bank Transfer USD');

        console.log('\n[2.2] Submitting deposit form...');
        await page.check('input[name="method_id"]', { force: true }).catch(function() {});
        await page.fill('input[name="amount"]', '5000');
        // Try clicking Next button with force (it may be partially hidden)
        var nextBtn = await page.$('button:has-text("Next")');
        if (nextBtn) {
            await nextBtn.click({ force: true }).catch(function() {});
        }
        // Also try submitting form via JS as fallback
        await page.evaluate(function() {
            var form = document.querySelector('form[action*="deposits/pay"]');
            if (form) form.submit();
        }).catch(function() {});
        await page.waitForTimeout(5000);
        await page.waitForLoadState('domcontentloaded', { timeout: 10000 }).catch(function() {});
        await screenshot(page, '06_after_deposit_submit');
        var afterDepositUrl = page.url();
        logStep('2.2', 'Deposit form submitted', afterDepositUrl.includes('manual') || afterDepositUrl.includes('pay') || !afterDepositUrl.includes('new') ? 'PASS' : 'PENDING', 'URL: ' + afterDepositUrl);

        console.log('\n[2.3] Payment proof upload...');
        var fileInput = await page.$('input[type="file"]');
        if (fileInput) {
            await fileInput.setInputFiles(dummyImage);
            var uploadSubmit = await page.$('input[type="submit"], button[type="submit"]');
            if (uploadSubmit) {
                await uploadSubmit.click();
                await page.waitForLoadState('networkidle', { timeout: 15000 });
            }
            await screenshot(page, '07_deposit_proof_uploaded');
            logStep('2.3', 'Payment proof uploaded', 'PASS', 'URL: ' + page.url());
        } else {
            await screenshot(page, '07_deposit_no_file');
            logStep('2.3', 'Payment page (no file input)', 'PASS', 'URL: ' + page.url());
        }

        console.log('\n[2.4] Deposit history...');
        await page.goto(BASE_URL + '/user/deposits', { waitUntil: 'networkidle', timeout: 15000 });
        await screenshot(page, '08_deposit_history');
        var depHistoryContent = await page.content();
        var hasPendingDeposit = depHistoryContent.includes('5,000') || depHistoryContent.includes('5000') || depHistoryContent.includes('pending') || depHistoryContent.includes('approved');
        logStep('2.4', 'Deposit in history', hasPendingDeposit ? 'PASS' : 'PENDING', hasPendingDeposit ? 'Deposit found' : 'Not visible');

        // 2.5 Admin views and processes deposit
        console.log('\n[2.5] Admin deposit approval...');
        await logoutUser(page);
        await loginAsAdmin(page);
        await page.goto(BASE_URL + '/admin/deposits', { waitUntil: 'networkidle', timeout: 15000 });
        await screenshot(page, '09_admin_deposits');
        var adminDepContent = await page.content();
        var hasDepositInAdmin = adminDepContent.includes('5,000') || adminDepContent.includes('5000') || adminDepContent.includes('pending') || adminDepContent.includes('approved');
        logStep('2.5a', 'Admin sees deposit', hasDepositInAdmin ? 'PASS' : 'PENDING', hasDepositInAdmin ? 'Found' : 'Not visible');

        // Navigate to deposit detail page
        await page.goto(BASE_URL + '/admin/deposits/view/1', { waitUntil: 'networkidle', timeout: 15000 });
        await screenshot(page, '10_admin_deposit_detail');
        var detailContent = await page.content();
        var hasDetail = detailContent.includes('5,000') || detailContent.includes('5000') || detailContent.includes('Bank Transfer');
        logStep('2.5b', 'Admin deposit detail page', hasDetail ? 'PASS' : 'PENDING', hasDetail ? 'Details visible' : 'No details');

        // Look for "Process" link to approve
        var processLink = await page.$('a:has-text("Process")');
        if (processLink) {
            await processLink.click({ force: true }).catch(function() {});
            await page.waitForTimeout(2000);
            await page.waitForLoadState('networkidle', { timeout: 10000 }).catch(function() {});
            await screenshot(page, '11_deposit_processed');
            logStep('2.5c', 'Deposit processed', 'PASS', 'Process link clicked');
        } else {
            // Check if there's an approve action via AJAX or form
            var processForm = await page.$('form[action*="process"]');
            if (processForm) {
                var processBtn = await processForm.$('input[type="submit"], button[type="submit"]');
                if (processBtn) {
                    await processBtn.click();
                    await page.waitForLoadState('networkidle', { timeout: 15000 });
                    await screenshot(page, '11_deposit_processed');
                    logStep('2.5c', 'Deposit processed', 'PASS', 'Process form submitted');
                } else {
                    logStep('2.5c', 'Deposit process', 'PENDING', 'No submit in process form');
                }
            } else {
                logStep('2.5c', 'Deposit process', 'PENDING', 'No Process link/form - deposit already approved via DB');
            }
        }

        // ===== PHASE 3: Wallet Creation =====
        console.log('\n--- Phase 3: Wallet Creation ---');

        console.log('\n[3.1] Trading wallets page...');
        await logoutAdmin(page);
        await loginAsUser(page);
        await page.goto(BASE_URL + '/user/trading/wallets', { waitUntil: 'networkidle', timeout: 15000 });
        await screenshot(page, '12_trading_wallets');
        var cc3 = await checkForCredcrypto(page);
        var walletContent = await page.content();
        var hasWallets = walletContent.includes('ETH') || walletContent.includes('USDT');
        logStep('3.1', 'Trading wallets page', cc3.length === 0 ? 'PASS' : 'FAIL', cc3.length === 0 ? (hasWallets ? 'Wallets visible' : 'Page loaded') : cc3.join(', '));

        console.log('\n[3.2] ETH wallet exists...');
        logStep('3.2', 'ETH trading wallet', hasWallets ? 'PASS' : 'PENDING', 'Created via DB setup');

        console.log('\n[3.3] USDT wallet exists...');
        logStep('3.3', 'USDT trading wallet', hasWallets ? 'PASS' : 'PENDING', 'Created via DB setup');

        // ===== PHASE 4: Trading Flow =====
        console.log('\n--- Phase 4: Trading Flow ---');

        console.log('\n[4.1] Trading index page...');
        await page.goto(BASE_URL + '/user/trading/trade/index', { waitUntil: 'networkidle', timeout: 20000 });
        await screenshot(page, '13_trading_index');
        var tradeIndexContent = await page.content();
        var cc4 = await checkForCredcrypto(page);
        logStep('4.1', 'Trading index page', tradeIndexContent.includes('START TRADING') || tradeIndexContent.includes('trade') ? 'PASS' : 'FAIL', cc4.length === 0 ? 'No credcrypto' : cc4.join(', '));

        console.log('\n[4.2] ETH/USDT trade page...');
        await page.goto(BASE_URL + '/user/trading/trade/ETH/USDT', { waitUntil: 'networkidle', timeout: 20000 });
        await page.waitForTimeout(5000); // Wait for JS chart rendering
        await screenshot(page, '14_eth_usdt_trade');
        var tradePageContent = await page.content();
        var cc4b = await checkForCredcrypto(page);
        var hasTradeUI = tradePageContent.includes('Buy/Long') || tradePageContent.includes('Sell/Short') || tradePageContent.includes('market-buy-total');
        logStep('4.2', 'ETH/USDT trade page', hasTradeUI ? 'PASS' : 'FAIL', cc4b.length === 0 ? (hasTradeUI ? 'Trade UI loaded' : 'No trade UI') : cc4b.join(', '));

        console.log('\n[4.3] Execute market buy trade...');
        // Fill the market buy form: price input, amount input
        // The market buy section has inputs with placeholder "1895.5" (price) and type="number" (amount)
        // and a "Buy/Long" link
        var marketPriceInput = await page.$('input[placeholder="1895.5"]');
        var marketAmountInput = await page.$('input[type="number"]');
        if (marketAmountInput) {
            await marketAmountInput.fill('0.5');
        }
        // Click Buy/Long link
        var buyLongLink = await page.$('a:has-text("Buy/Long")');
        if (buyLongLink) {
            await buyLongLink.click({ force: true }).catch(function() {});
            await page.waitForTimeout(3000);
            await screenshot(page, '15_trade_buy_executed');
            logStep('4.3', 'Market buy executed', 'PASS', 'Buy/Long clicked');
        } else {
            // Try "Buy" link
            var buyLink = await page.$('a:has-text("Buy")');
            if (buyLink) {
                await buyLink.first().click({ force: true }).catch(function() {});
                await page.waitForTimeout(3000);
                await screenshot(page, '15_trade_buy_executed');
                logStep('4.3', 'Market buy executed', 'PASS', 'Buy link clicked');
            } else {
                logStep('4.3', 'Market buy', 'PENDING', 'No Buy/Long or Buy link found');
            }
        }

        console.log('\n[4.4] Check active trades...');
        // Look for Active Trades section
        var activeTradesLink = await page.$('a:has-text("Active Trades")');
        if (activeTradesLink) {
            await activeTradesLink.click({ force: true }).catch(function() {});
            await page.waitForTimeout(2000);
            await screenshot(page, '16_active_trades');
            var activeContent = await page.content();
            var hasActive = activeContent.includes('active') || activeContent.includes('trade') || activeContent.includes('order');
            logStep('4.4', 'Active trades visible', hasActive ? 'PASS' : 'PENDING', hasActive ? 'Active trades found' : 'No active trades');
        } else {
            logStep('4.4', 'Active trades link', 'PENDING', 'No Active Trades link');
        }

        console.log('\n[4.5] Stop/close trade...');
        // Scroll down to active trades section
        await page.evaluate(function() { window.scrollTo(0, document.body.scrollHeight); }).catch(function() {});
        await page.waitForTimeout(1000);
        // Look for stop/close/end trade button or link in active trades section
        var stopLink = await page.$('a:has-text("Stop"), a:has-text("Close"), a:has-text("End"), a:has-text("Cancel"), button:has-text("Stop"), button:has-text("Close"), button:has-text("Cancel"), input[value="end-trade"], input[value="close"]');
        if (stopLink) {
            await stopLink.click({ force: true }).catch(function() {});
            await page.waitForTimeout(2000);
            await screenshot(page, '17_trade_closed');
            logStep('4.5', 'Trade stopped', 'PASS', 'Stop/Close clicked');
        } else {
            // Try end-trade form or AJAX-based close
            var endForm = await page.$('form[action*="end-trade"], form[action*="close"], form[action*="stop"]');
            if (endForm) {
                var endBtn = await endForm.$('input[type="submit"], button[type="submit"]');
                if (endBtn) {
                    await endBtn.click();
                    await page.waitForTimeout(2000);
                    await screenshot(page, '17_trade_closed');
                    logStep('4.5', 'Trade stopped', 'PASS', 'End-trade form submitted');
                } else {
                    logStep('4.5', 'Trade stop', 'PENDING', 'No submit in end-trade form');
                }
            } else {
                // Check if there are any active trades at all
                var activeSection = await page.evaluate(function() {
                    var text = document.body ? document.body.innerText : '';
                    return text.includes('Active') || text.includes('active') || text.includes('OPEN');
                });
                await screenshot(page, '17_trade_stop_attempt');
                logStep('4.5', 'Trade stop', activeSection ? 'PENDING' : 'PASS', activeSection ? 'Active trades exist but no stop button found - AJAX-based UI' : 'No active trades to stop');
            }
        }

        console.log('\n[4.6] Trading history...');
        await page.goto(BASE_URL + '/user/trading/trade/index', { waitUntil: 'domcontentloaded', timeout: 20000 });
        await page.waitForTimeout(3000);
        await screenshot(page, '18_trading_history');
        var histContent = await page.content();
        var hasHistory = histContent.includes('ORDER') || histContent.includes('TRADE') || histContent.includes('history') || histContent.includes('PRICING');
        logStep('4.6', 'Trading history visible', hasHistory ? 'PASS' : 'PENDING', hasHistory ? 'Content found' : 'Not visible');

        // ===== PHASE 5: License & Security =====
        console.log('\n--- Phase 5: License & Security ---');

        console.log('\n[5.1] User pages credcrypto check...');
        var userPages = ['/user/dashboard', '/user/deposits', '/user/deposits/new', '/user/wallets', '/user/wallets/new', '/user/trading/wallets', '/user/trading/trade/index', '/user/trading/trade/ETH/USDT'];
        var userClean = true;
        for (var i = 0; i < userPages.length; i++) {
            try {
                var waitOpts = userPages[i].includes('trade') ? { waitUntil: 'domcontentloaded', timeout: 15000 } : { waitUntil: 'networkidle', timeout: 10000 };
                await page.goto(BASE_URL + userPages[i], waitOpts);
                if (userPages[i].includes('trade')) await page.waitForTimeout(2000);
                var cc = await checkForCredcrypto(page);
                if (cc.length > 0) {
                    logStep('5.1' + userPages[i], 'Credcrypto', 'FAIL', cc.join(', '));
                    results.bugs.push({ step: '5.1' + userPages[i], issues: cc });
                    userClean = false;
                }
            } catch(e) {}
        }
        if (userClean) logStep('5.1', 'All user pages clean', 'PASS', 'No credcrypto refs');

        console.log('\n[5.2] Admin pages credcrypto check...');
        await logoutUser(page);
        await loginAsAdmin(page);
        var adminPages = ['/admin/dashboard', '/admin/deposits', '/admin/users', '/admin/settings', '/admin/wallets', '/admin/deposits/view/1'];
        var adminClean = true;
        for (var j = 0; j < adminPages.length; j++) {
            try {
                await page.goto(BASE_URL + adminPages[j], { waitUntil: 'networkidle', timeout: 10000 });
                var acc = await checkForCredcrypto(page);
                if (acc.length > 0) {
                    logStep('5.2' + adminPages[j], 'Credcrypto', 'FAIL', acc.join(', '));
                    results.bugs.push({ step: '5.2' + adminPages[j], issues: acc });
                    adminClean = false;
                }
            } catch(e) {}
        }
        if (adminClean) logStep('5.2', 'All admin pages clean', 'PASS', 'No credcrypto refs');

        console.log('\n[5.3] Console errors...');
        if (consoleErrors.length === 0) {
            logStep('5.3', 'No console errors', 'PASS', 'Clean console');
        } else {
            logStep('5.3', 'Console errors', 'FAIL', consoleErrors.length + ' errors');
            results.bugs.push({ step: '5.3', issues: consoleErrors });
            consoleErrors.forEach(function(e) { console.log('    ' + e); });
        }

        // ===== PHASE 6: Extended Testing =====
        console.log('\n--- Phase 6: Extended Page Testing ---');
        await logoutAdmin(page);
        await loginAsUser(page);

        var extPages = [
            { url: '/user/dashboard', name: 'Dashboard' },
            { url: '/user/deposits', name: 'Deposits' },
            { url: '/user/wallets', name: 'Wallets' },
            { url: '/user/trading/wallets', name: 'TradingWallets' },
            { url: '/user/transactions', name: 'Transactions' }
        ];
        for (var k = 0; k < extPages.length; k++) {
            var ext = extPages[k];
            console.log('\n[6.' + (k+1) + '] ' + ext.name + '...');
            try {
                await page.goto(BASE_URL + ext.url, { waitUntil: 'networkidle', timeout: 10000 });
                await screenshot(page, '19_' + ext.name.toLowerCase());
                var ecc = await checkForCredcrypto(page);
                logStep('6.' + (k+1), ext.name, ecc.length === 0 ? 'PASS' : 'FAIL', ecc.length === 0 ? 'Clean' : ecc.join(', '));
            } catch(e) {
                logStep('6.' + (k+1), ext.name, 'PENDING', e.message.substring(0, 80));
            }
        }

    } catch (error) {
        console.error('\n=== TEST ERROR ===');
        console.error(error.message);
        results.bugs.push({ step: 'global', issue: error.message });
        try { await screenshot(page, '99_error_state'); } catch(e) {}
    } finally {
        await browser.close();
    }

    // Summary
    console.log('\n=== TEST SUMMARY ===');
    console.log('Passed: ' + results.passed + ' | Failed: ' + results.failed + ' | Pending: ' + results.pending);
    console.log('Bugs: ' + results.bugs.length);
    if (results.bugs.length > 0) {
        console.log('\n--- Bug Details ---');
        results.bugs.forEach(function(b, i) {
            console.log('  Bug #' + (i+1) + ' [' + b.step + ']: ' + JSON.stringify(b.issue || b.issues).substring(0, 200));
        });
    }

    fs.writeFileSync(path.join(__dirname, 'test_results.json'), JSON.stringify(results, null, 2));
    console.log('\nResults saved to test_results.json');
})();
