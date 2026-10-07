import chromium from '@sparticuz/chromium';
import puppeteer from 'puppeteer-core';
const [,, url, out, width = '1440'] = process.argv;
const browser = await puppeteer.launch({
  args: chromium.args, executablePath: await chromium.executablePath(), headless: true,
  defaultViewport: { width: Number(width), height: 900, deviceScaleFactor: 1 },
});
const page = await browser.newPage();
await page.setRequestInterception(true);
page.on('request', r => /googletagmanager|google-analytics/.test(r.url()) ? r.abort() : r.continue());
await page.goto(url, { waitUntil: 'networkidle0', timeout: 60000 });
// force lazy images to load, then wait for them (bounded)
await page.evaluate(() => document.querySelectorAll('img[loading="lazy"]').forEach(i => { i.loading = 'eager'; }));
await page.evaluate(async () => {
  for (let y = 0; y < document.body.scrollHeight; y += 700) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 40)); }
  window.scrollTo(0, 0);
});
await Promise.race([
  page.evaluate(() => Promise.all([...document.images].map(i => i.complete ? null : new Promise(r => { i.onload = i.onerror = r; })))),
  new Promise(r => setTimeout(r, 8000)),
]);
await page.addStyleTag({ content: '.pt-header{position:static !important}' });
await new Promise(r => setTimeout(r, 300));
const w = await page.evaluate(() => document.documentElement.scrollWidth);
await page.screenshot({ path: out, fullPage: true });
await browser.close();
console.log('saved', out, 'scrollWidth', w);
