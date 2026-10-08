// Mobile: tap the second capability card open, screenshot the cards area.
import puppeteer from 'puppeteer-core'; import chromium from '@sparticuz/chromium';
const [url,out,w]=process.argv.slice(2);
const b=await puppeteer.launch({executablePath:await chromium.executablePath(),args:chromium.args,headless:true});
const p=await b.newPage(); await p.setViewport({width:+w||390,height:800,deviceScaleFactor:1,isMobile:true,hasTouch:true});
await p.goto(url,{waitUntil:'networkidle0'}); await p.addStyleTag({content:'.pt-header{position:static}'});
await p.evaluate(()=>document.querySelectorAll('img[loading=lazy]').forEach(i=>i.loading='eager'));
const btns=await p.$$('.pt-card__toggle'); await btns[1].tap(); await new Promise(r=>setTimeout(r,500));
const el=await p.$('.pt-cards'); await el.screenshot({path:out}); console.log('saved',out); await b.close();
