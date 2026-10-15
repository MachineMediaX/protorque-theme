// Screenshot one element: node sect.mjs <url> <selector> <out> [width]
import puppeteer from 'puppeteer-core'; import chromium from '@sparticuz/chromium';
const [url,sel,out,w='1440']=process.argv.slice(2);
const b=await puppeteer.launch({executablePath:await chromium.executablePath(),args:chromium.args,headless:true});
const p=await b.newPage(); await p.setViewport({width:+w,height:900});
await p.goto(url,{waitUntil:'networkidle0'});
await p.evaluate(()=>document.querySelectorAll('img[loading=lazy]').forEach(i=>i.loading='eager'));
const el=await p.$(sel); await el.scrollIntoView(); await new Promise(r=>setTimeout(r,1200));
await el.screenshot({path:out}); console.log('ok', await el.evaluate(e=>e.getBoundingClientRect().height)); await b.close();
