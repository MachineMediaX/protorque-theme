import chromium from '@sparticuz/chromium';
import puppeteer from 'puppeteer-core';
const [,, url, out, width = '1440'] = process.argv;
const browser = await puppeteer.launch({
  args: chromium.args, executablePath: await chromium.executablePath(), headless: true,
  defaultViewport: { width: Number(width), height: 900, deviceScaleFactor: 1 },
});
const page = await browser.newPage();
await page.goto(url, { waitUntil: 'networkidle0', timeout: 60000 });
await page.screenshot({ path: out, fullPage: true });
await browser.close();
console.log('saved', out);
