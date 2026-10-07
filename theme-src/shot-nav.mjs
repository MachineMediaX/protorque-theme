// Screenshot the header with a mega menu open: node shot-nav.mjs <url> <out> <services|equipment> [tabIndex|itemIndex]
import chromium from '@sparticuz/chromium';
import puppeteer from 'puppeteer-core';
const [,, url, out, which, idx = '0'] = process.argv;
const browser = await puppeteer.launch({ args: chromium.args, executablePath: await chromium.executablePath(), headless: true, defaultViewport: { width: 1440, height: 760 } });
const page = await browser.newPage();
await page.setRequestInterception(true);
page.on('request', r => /googletagmanager/.test(r.url()) ? r.abort() : r.continue());
await page.goto(url, { waitUntil: 'networkidle0', timeout: 60000 });
const sel = which === 'services' ? '.pt-nav__item--split' : '.pt-nav__item--equipment';
await page.evaluate(s => { const li = document.querySelector(s); li.classList.add('is-open'); }, sel);
await new Promise(r => setTimeout(r, 300));
if (which === 'services') { const tabs = await page.$$(sel + ' .pt-mega__tab'); if (tabs[+idx]) await tabs[+idx].click(); }
else { const links = await page.$$(sel + ' .pt-mega__links a'); if (links[+idx]) await links[+idx].focus(); }
await new Promise(r => setTimeout(r, 300));
await page.screenshot({ path: out });
await browser.close(); console.log('saved', out);
