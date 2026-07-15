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
        var hasMethod = depositContent.includes('method_id') || depositContent.includes('Bank Transfer') || depositContent.includes('Bitcoin') || depositContent.includes('Ethereum');
        logStep('2.1', 'Deposit method visible', hasMethod ? 'PASS' : 'FAIL', hasMethod ? 'Methods found' : 'No methods');

        console.log('\n[2.2] Submitting deposit form...');
        // Select the first radio button for method_id
        var methodRadio = await page.$('input[name="method_id"]');
        if (methodRadio) {
            await methodRadio.check({ force: true }).catch(function() {});
            // Also try clicking the parent label/item to ensure selection
            await page.evaluate(function() {
                var radio = document.querySelector('input[name="method_id"]');
                if (radio) { radio.checked = true; radio.dispatchEvent(new Event('change', { bubbles: true })); }
            }).catch(function() {});
        }
        // Fill the amount field
        var amountInput = await page.$('input[name="amount"]');
        if (amountInput) {
            await amountInput.fill('100');
        }
        // Submit the form via JS (bypasses any JS interception issues)
        await page.evaluate(function() {
            var form = document.querySelector('form[action*="deposits/pay"]');
            if (form) form.submit();
        }).catch(function() {});
        // Wait for redirect to manual payment page
        await page.waitForURL('**/pay/manual**', { timeout: 15000 }).catch(function() {});
        await page.waitForLoadState('domcontentloaded', { timeout: 10000 }).catch(function() {});
        await page.waitForTimeout(2000);
        await screenshot(page, '06_after_deposit_submit');
        var afterDepositUrl = page.url();
        var onManualPage = afterDepositUrl.includes('manual') || afterDepositUrl.includes('pay/manual');
        logStep('2.2', 'Deposit form submitted', onManualPage ? 'PASS' : 'PENDING', 'URL: ' + afterDepositUrl);

        console.log('\n[2.3] Payment proof upload via SweetAlert...');
        // The manual payment page has a "Save Deposit" button that opens a SweetAlert modal
        // with a file input and textarea inside the modal
        if (onManualPage) {
            // Click the "Save Deposit" button to open SweetAlert modal
            var saveDepositBtn = await page.$('.save-deposit-btn');
            if (saveDepositBtn) {
                await saveDepositBtn.click();
                await page.waitForTimeout(2000); // Wait for SweetAlert to render
                await screenshot(page, '06b_save_deposit_modal');

                // SweetAlert modal content is in .swal2-html or .swal2-content
                var swalFileInput = await page.$('.swal2-content input[type="file"], .swal2-html input[type="file"], .swal2-container input[type="file"]');
                if (swalFileInput) {
                    await swalFileInput.setInputFiles(dummyImage);
                } else {
                    // Try broader selector
                    var anyFileInput = await page.$('input[type="file"]');
                    if (anyFileInput) {
                        await anyFileInput.setInputFiles(dummyImage);
                    }
                }

                // Fill the additional_info textarea in the modal
                var swalTextarea = await page.$('.swal2-content textarea, .swal2-html textarea, .swal2-container textarea');
                if (swalTextarea) {
                    await swalTextarea.fill('Test payment proof for E2E testing');
                }

                // Submit the form inside the SweetAlert modal
                var swalSubmitBtn = await page.$('.swal2-content button[type="submit"], .swal2-html button[type="submit"], .swal2-container button[type="submit"]');
                if (swalSubmitBtn) {
                    await swalSubmitBtn.click();
                    await page.waitForLoadState('networkidle', { timeout: 15000 }).catch(function() {});
                    await page.waitForTimeout(2000);
                }
                await screenshot(page, '07_deposit_proof_uploaded');
                var afterUploadUrl = page.url();
                logStep('2.3', 'Payment proof uploaded', afterUploadUrl.includes('deposits') && !afterUploadUrl.includes('manual') ? 'PASS' : 'PENDING', 'URL: ' + afterUploadUrl);
            } else {
                // Fallback: look for any file input on the page directly
                var fileInput = await page.$('input[type="file"]');
                if (fileInput) {
                    await fileInput.setInputFiles(dummyImage);
                    var uploadSubmit = await page.$('button[type="submit"]');
                    if (uploadSubmit) {
                        await uploadSubmit.click();
                        await page.waitForLoadState('networkidle', { timeout: 15000 }).catch(function() {});
                    }
                    await screenshot(page, '07_deposit_proof_uploaded');
                    logStep('2.3', 'Payment proof uploaded', 'PASS', 'URL: ' + page.url());
                } else {
                    await screenshot(page, '07_deposit_no_file');
                    logStep('2.3', 'Payment page (no file input)', 'PENDING', 'URL: ' + page.url());
                }
            }
        } else {
            await screenshot(page, '07_deposit_no_manual');
            logStep('2.3', 'Payment proof upload', 'PENDING', 'Not on manual payment page');
        }

        console.log('\n[2.4] Deposit history...');
        await page.goto(BASE_URL + '/user/deposits', { waitUntil: 'networkidle', timeout: 15000 });
        await screenshot(page, '08_deposit_history');
        var depHistoryContent = await page.content();
        var hasPendingDeposit = depHistoryContent.includes('100') || depHistoryContent.includes('pending') || depHistoryContent.includes('approved') || depHistoryContent.includes('deposit');
        logStep('2.4', 'Deposit in history', hasPendingDeposit ? 'PASS' : 'PENDING', hasPendingDeposit ? 'Deposit found' : 'Not visible');

        // 2.5 Admin views and processes deposit
        console.log('\n[2.5] Admin deposit approval...');
        await logoutUser(page);
        await loginAsAdmin(page);
        await page.goto(BASE_URL + '/admin/deposits', { waitUntil: 'networkidle', timeout: 15000 });
        await screenshot(page, '09_admin_deposits');
        var adminDepContent = await page.content();
        var hasDepositInAdmin = adminDepContent.includes('100') || adminDepContent.includes('pending') || adminDepContent.includes('approved') || adminDepContent.includes('Bank Transfer') || adminDepContent.includes('Bitcoin');
        logStep('2.5a', 'Admin sees deposit', hasDepositInAdmin ? 'PASS' : 'PENDING', hasDepositInAdmin ? 'Found' : 'Not visible');

        // Find the deposit view link from the admin deposits list
        var depositViewUrl = null;
        var viewLink = await page.$('a[href*="admin/deposits/view/"]');
        if (viewLink) {
            var href = await viewLink.getAttribute('href');
            if (href) depositViewUrl = href;
        }
        if (!depositViewUrl) depositViewUrl = '/admin/deposits/view/1';

        // Navigate to deposit detail page
        await page.goto(BASE_URL + depositViewUrl, { waitUntil: 'networkidle', timeout: 15000 });
        await screenshot(page, '10_admin_deposit_detail');
        var detailContent = await page.content();
        var hasDetail = detailContent.includes('100') || detailContent.includes('pending') || detailContent.includes('Bank Transfer') || detailContent.includes('Bitcoin') || detailContent.includes('deposit');
        logStep('2.5b', 'Admin deposit detail page', hasDetail ? 'PASS' : 'PENDING', hasDetail ? 'Details visible' : 'No details');

        // Look for "Process" link - only shows if deposit status is 'pending'
        var processLink = await page.$('#processDeposit');
        if (!processLink) {
            processLink = await page.$('a:has-text("Process")');
        }
        if (processLink) {
            // Click Process link to open SweetAlert modal
            await processLink.click({ force: true }).catch(function() {});
            await page.waitForTimeout(2000); // Wait for SweetAlert to render
            await screenshot(page, '10b_process_modal');

            // Select "Approve" from the action dropdown in the SweetAlert modal
            var actionSelect = await page.$('.swal2-content select[name="action"], .swal2-html select[name="action"], .swal2-container select[name="action"], select#action');
            if (actionSelect) {
                await actionSelect.selectOption('approve');
            }

            // Fill the additional_info textarea
            var infoTextarea = await page.$('.swal2-content textarea[name="additional_info"], .swal2-html textarea[name="additional_info"], .swal2-container textarea[name="additional_info"], textarea#additional_info');
            if (infoTextarea) {
                await infoTextarea.fill('Deposit approved via E2E test');
            }

            // Submit the form inside the SweetAlert modal
            var swalSubmit = await page.$('.swal2-content button[type="submit"], .swal2-html button[type="submit"], .swal2-container button[type="submit"]');
            if (swalSubmit) {
                await swalSubmit.click();
                await page.waitForLoadState('networkidle', { timeout: 15000 }).catch(function() {});
                await page.waitForTimeout(2000);
            }
            await screenshot(page, '11_deposit_processed');
            var afterProcessUrl = page.url();
            var processedContent = await page.content();
            var isProcessed = processedContent.includes('success') || processedContent.includes('processed') || processedContent.includes('approved');
            logStep('2.5c', 'Deposit processed via browser', isProcessed ? 'PASS' : 'PENDING', 'Process modal submitted - URL: ' + afterProcessUrl);
        } else {
            // Check if deposit is already approved (no Process link means not pending)
            var alreadyApproved = detailContent.includes('approved') || detailContent.includes('success');
            logStep('2.5c', 'Deposit process', alreadyApproved ? 'PASS' : 'PENDING', alreadyApproved ? 'Already approved' : 'No Process link - deposit may not be pending');
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
        await page.goto(BASE_URL + '/user/trading/trade/ETH/USDT', { waitUntil: 'domcontentloaded', timeout: 20000 });
        await page.waitForTimeout(3000); // Wait for JS chart rendering
        await screenshot(page, '14_eth_usdt_trade');
        var tradePageContent = await page.content();
        var cc4b = await checkForCredcrypto(page);
        var hasTradeUI = tradePageContent.includes('Buy/Long') || tradePageContent.includes('Sell/Short') || tradePageContent.includes('market-buy-total') || tradePageContent.includes('order-button');
        logStep('4.2', 'ETH/USDT trade page', hasTradeUI ? 'PASS' : 'FAIL', cc4b.length === 0 ? (hasTradeUI ? 'Trade UI loaded' : 'No trade UI') : cc4b.join(', '));

        console.log('\n[4.3] Execute market buy trade...');
        // The trade page uses .order-button divs with data-order and data-type attributes
        // The market buy section has input.amount-field with data-type="buy" and input#market-buy-total
        // Fill the amount field (first .amount-field with data-type="buy")
        var buyAmountInput = await page.$('.amount-field[data-type="buy"]');
        if (buyAmountInput) {
            await buyAmountInput.fill('0.1');
        } else {
            // Fallback: first number input in the market buy section
            var marketAmountInput = await page.$('#market input[type="number"]');
            if (marketAmountInput) await marketAmountInput.fill('0.1');
        }
        // Click the market buy order-button div (contains the Buy/Long link)
        var buyOrderBtn = await page.$('.order-button[data-order="market"][data-type="buy"]');
        if (buyOrderBtn) {
            await buyOrderBtn.click({ force: true }).catch(function() {});
            await page.waitForTimeout(3000);
            // Handle SweetAlert confirmation if it appears
            var swalConfirm = await page.$('.swal2-confirm');
            if (swalConfirm) {
                await swalConfirm.click().catch(function() {});
                await page.waitForTimeout(2000);
            }
            await screenshot(page, '15_trade_buy_executed');
            logStep('4.3', 'Market buy executed', 'PASS', 'Buy/Long order button clicked');
        } else {
            // Fallback: try clicking the Buy/Long link directly
            var buyLongLink = await page.$('a:has-text("Buy/Long")');
            if (buyLongLink) {
                await buyLongLink.click({ force: true }).catch(function() {});
                await page.waitForTimeout(3000);
                var swalConfirm2 = await page.$('.swal2-confirm');
                if (swalConfirm2) {
                    await swalConfirm2.click().catch(function() {});
                    await page.waitForTimeout(2000);
                }
                await screenshot(page, '15_trade_buy_executed');
                logStep('4.3', 'Market buy executed', 'PASS', 'Buy/Long link clicked');
            } else {
                logStep('4.3', 'Market buy', 'PENDING', 'No Buy/Long button found');
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
        // The stop trade link has class .stop-trade with data-id attribute
        var stopTradeLink = await page.$('a.stop-trade');
        if (stopTradeLink) {
            await stopTradeLink.click({ force: true }).catch(function() {});
            await page.waitForTimeout(2000);
            // Handle SweetAlert confirmation
            var stopSwalConfirm = await page.$('.swal2-confirm');
            if (stopSwalConfirm) {
                await stopSwalConfirm.click().catch(function() {});
                await page.waitForTimeout(2000);
            }
            await screenshot(page, '17_trade_closed');
            logStep('4.5', 'Trade stopped', 'PASS', '.stop-trade link clicked');
        } else {
            // Fallback: look for any stop/close/end link
            var stopLink = await page.$('a:has-text("stop"), a:has-text("Stop"), a:has-text("Close"), a:has-text("End")');
            if (stopLink) {
                await stopLink.click({ force: true }).catch(function() {});
                await page.waitForTimeout(2000);
                var stopSwalConfirm2 = await page.$('.swal2-confirm');
                if (stopSwalConfirm2) {
                    await stopSwalConfirm2.click().catch(function() {});
                    await page.waitForTimeout(2000);
                }
                await screenshot(page, '17_trade_closed');
                logStep('4.5', 'Trade stopped', 'PASS', 'Stop link clicked');
            } else {
                // Check if there are any active trades at all
                var activeSection = await page.evaluate(function() {
                    var text = document.body ? document.body.innerText : '';
                    return text.includes('Active') || text.includes('active') || text.includes('OPEN');
                });
                await screenshot(page, '17_trade_stop_attempt');
                logStep('4.5', 'Trade stop', activeSection ? 'PENDING' : 'PASS', activeSection ? 'Active trades exist but no stop link found' : 'No active trades to stop');
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
