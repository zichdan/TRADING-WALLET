import fs from 'node:fs/promises';
import path from 'node:path';
import { createRequire } from 'node:module';

const baseUrl = process.env.TRADING_WALLET_URL || 'http://127.0.0.1:8087';
const outDir = path.resolve('TRADING WALLET TESTING');
const appRequire = createRequire(path.resolve('public_html/trading-wallet.net/core/package.json'));
const { chromium } = appRequire('playwright');

const screenshotTargets = [
  { name: '01-home-desktop.png', url: '/', viewport: { width: 1440, height: 1000 } },
  { name: '02-login-desktop.png', url: '/login', viewport: { width: 1440, height: 1000 } },
  { name: '03-register-desktop.png', url: '/register', viewport: { width: 1440, height: 1000 } },
  { name: '04-admin-login-desktop.png', url: '/admin/login', viewport: { width: 1440, height: 1000 } },
  { name: '05-home-mobile.png', url: '/', viewport: { width: 390, height: 844 } },
];

const browser = await chromium.launch();
const results = {
  baseUrl,
  generatedAt: new Date().toISOString(),
  browser: 'chromium',
  pages: [],
  interaction: null,
};

async function writeResults() {
  await fs.writeFile(
    path.join(outDir, 'browser-smoke-results.json'),
    JSON.stringify(results, null, 2),
    'utf8'
  );
}

async function captureTarget(target) {
  const context = await browser.newContext({ viewport: target.viewport });
  const page = await context.newPage();
  const consoleMessages = [];
  const pageErrors = [];
  const requestFailures = [];

  page.on('console', (msg) => {
    if (['error', 'warning'].includes(msg.type())) {
      consoleMessages.push({ type: msg.type(), text: msg.text() });
    }
  });
  page.on('pageerror', (error) => {
    pageErrors.push(error.message);
  });
  page.on('requestfailed', (request) => {
    requestFailures.push({
      url: request.url(),
      method: request.method(),
      failure: request.failure()?.errorText || 'unknown',
    });
  });

  const response = await page.goto(new URL(target.url, baseUrl).toString(), {
    waitUntil: 'domcontentloaded',
    timeout: 45000,
  });
  await page.waitForTimeout(1500);

  const title = await page.title();
  const bodyText = (await page.locator('body').innerText({ timeout: 5000 })).slice(0, 500);
  const screenshotPath = path.join(outDir, target.name);
  const finalUrl = page.url();
  await page.screenshot({ path: screenshotPath, fullPage: true });

  await context.close();
  return {
    url: target.url,
    finalUrl,
    status: response?.status() ?? null,
    title,
    viewport: target.viewport,
    screenshot: target.name,
    bodyPreview: bodyText,
    consoleMessages,
    pageErrors,
    requestFailures,
  };
}

for (const target of screenshotTargets) {
  results.pages.push(await captureTarget(target));
}

const interactionContext = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
const interactionPage = await interactionContext.newPage();
try {
  await interactionPage.goto(baseUrl, { waitUntil: 'domcontentloaded', timeout: 45000 });
  await interactionPage.waitForTimeout(1000);
  await interactionPage.getByRole('link', { name: /open login/i }).first().click({ timeout: 15000 });
  await interactionPage.waitForURL(/\/login(?:$|[?#])/, { timeout: 30000 });
  results.interaction = {
    name: 'Home hero Open Login link opens login page',
    ok: true,
    finalUrl: interactionPage.url(),
    title: await interactionPage.title(),
  };
} catch (error) {
  results.interaction = {
    name: 'Home hero Open Login link opens login page',
    ok: false,
    finalUrl: interactionPage.url(),
    title: await interactionPage.title().catch(() => ''),
    error: error.message,
  };
} finally {
  await interactionContext.close();
  await browser.close();
  await writeResults();
}

console.log(JSON.stringify({
  screenshots: results.pages.map((page) => page.screenshot),
  interaction: results.interaction,
  pages: results.pages.map((page) => ({
    url: page.url,
    status: page.status,
    title: page.title,
    consoleMessages: page.consoleMessages.length,
    pageErrors: page.pageErrors.length,
    requestFailures: page.requestFailures.length,
  })),
}, null, 2));
