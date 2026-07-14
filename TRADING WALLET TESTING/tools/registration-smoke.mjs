import { createRequire } from 'node:module';
import path from 'node:path';

const repoRoot = process.cwd();
const appRoot = path.join(repoRoot, 'public_html', 'trading-wallet.net', 'core');
const requireFromApp = createRequire(path.join(appRoot, 'package.json'));
const { chromium } = requireFromApp('playwright');

const baseUrl = process.env.TRADING_WALLET_BASE_URL || 'http://127.0.0.1:8087';
const email = `local-smoke-${Date.now()}@example.test`;
const password = 'LocalSmoke123!';

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });

const result = {
  email,
  registerStatus: null,
  finalUrl: null,
  title: null,
  pageErrors: [],
  requestFailures: [],
  sqlErrorVisible: false,
  success: false,
};

page.on('pageerror', (error) => result.pageErrors.push(error.message));
page.on('requestfailed', (request) => {
  result.requestFailures.push(`${request.method()} ${request.url()} ${request.failure()?.errorText || ''}`);
});

try {
  const response = await page.goto(`${baseUrl}/register`, { waitUntil: 'networkidle' });
  result.registerStatus = response?.status() ?? null;

  await page.fill('input[name="first_name"]', 'Local');
  await page.fill('input[name="last_name"]', 'Smoke');
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="phone_no"]', '+15550001111');
  await page.selectOption('select[name="country"]', { index: 1 });
  await page.fill('input[name="referred_by"]', '');
  await page.fill('input[name="password"]', password);
  await page.fill('input[name="password_confirmation"]', password);

  await Promise.all([
    page.waitForLoadState('networkidle'),
    page.click('input[type="submit"]'),
  ]);

  result.finalUrl = page.url();
  result.title = await page.title();
  result.sqlErrorVisible = await page.getByText("Field 'tcal' doesn't have a default value").isVisible().catch(() => false);
  const bodyText = await page.locator('body').innerText();
  result.success = !result.sqlErrorVisible && !bodyText.includes('QueryException') && !bodyText.includes("Field 'tcal'");

  await page.screenshot({
    path: path.join(repoRoot, 'TRADING WALLET TESTING', '06-registration-after-fix.png'),
    fullPage: true,
  });
} finally {
  await browser.close();
}

console.log(JSON.stringify(result, null, 2));

if (!result.success || result.pageErrors.length || result.requestFailures.length) {
  process.exitCode = 1;
}
