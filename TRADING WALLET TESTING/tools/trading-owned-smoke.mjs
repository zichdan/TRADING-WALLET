import { createRequire } from 'node:module';
import path from 'node:path';

const repoRoot = process.cwd();
const appRoot = path.join(repoRoot, 'public_html', 'trading-wallet.net', 'core');
const requireFromApp = createRequire(path.join(appRoot, 'package.json'));
const { chromium } = requireFromApp('playwright');

const baseUrl = process.env.TRADING_WALLET_BASE_URL || 'http://127.0.0.1:8087';
const email = `owned-trading-smoke-${Date.now()}@example.test`;
const password = 'LocalSmoke123!';

const pages = [
  ['wallets', '/user/trading/wallets', '07-owned-wallets.png'],
  ['trade', '/user/trading/trade/ETC/USDT', '08-owned-live-trade.png'],
  ['demo', '/user/trading/demo/ETC/USDT', '09-owned-demo-trade.png'],
  ['bot', '/user/trading/trade/bot/ETC/USDT', '10-owned-bot-trade.png'],
];

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage({ viewport: { width: 1366, height: 900 } });

const result = {
  email,
  registerStatus: null,
  pages: [],
  pageErrors: [],
  requestFailures: [],
  ignoredRequestFailures: [],
  success: false,
};

page.on('pageerror', (error) => result.pageErrors.push(error.message));
page.on('requestfailed', (request) => {
  const resourceType = request.resourceType();
  const failure = request.failure()?.errorText || '';
  const line = `${request.method()} ${request.url()} ${failure}`;

  if (failure === 'net::ERR_ABORTED' || ['image', 'font', 'stylesheet'].includes(resourceType)) {
    result.ignoredRequestFailures.push(line);
    return;
  }

  result.requestFailures.push(line);
});

try {
  const registerResponse = await page.goto(`${baseUrl}/register`, { waitUntil: 'networkidle' });
  result.registerStatus = registerResponse?.status() ?? null;

  await page.fill('input[name="first_name"]', 'Owned');
  await page.fill('input[name="last_name"]', 'Trading');
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="phone_no"]', '+15550002222');
  await page.selectOption('select[name="country"]', { index: 1 });
  await page.fill('input[name="referred_by"]', '');
  await page.fill('input[name="password"]', password);
  await page.fill('input[name="password_confirmation"]', password);

  await Promise.all([
    page.waitForLoadState('networkidle'),
    page.click('input[type="submit"]'),
  ]);

  for (const [name, url, screenshot] of pages) {
    const response = await page.goto(`${baseUrl}${url}`, { waitUntil: 'networkidle' });
    const bodyText = await page.locator('body').innerText();
    const hasFatalText = [
      'INVALID LICENSE KEY',
      'Invalid License',
      'QueryException',
      'ErrorException',
      'Fatal error',
      'Modules\\CryptoTrading\\Entities',
      'ionCube',
    ].some((needle) => bodyText.includes(needle));

    const pageResult = {
      name,
      status: response?.status() ?? null,
      url: page.url(),
      title: await page.title(),
      hasFatalText,
      screenshot,
    };

    result.pages.push(pageResult);

    await page.screenshot({
      path: path.join(repoRoot, 'TRADING WALLET TESTING', screenshot),
      fullPage: true,
    });
  }

  result.success = result.pages.every((item) => item.status && item.status < 400 && !item.hasFatalText)
    && result.pageErrors.length === 0
    && result.requestFailures.length === 0;
} finally {
  await browser.close();
}

console.log(JSON.stringify(result, null, 2));

if (!result.success) {
  process.exitCode = 1;
}
