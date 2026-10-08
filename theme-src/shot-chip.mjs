import puppeteer from 'puppeteer-core'; import chromium from '@sparticuz/chromium';
const b=await puppeteer.launch({executablePath:await chromium.executablePath(),args:chromium.args,headless:true});
const p=await b.newPage(); await p.setViewport({width:390,height:800,isMobile:true,hasTouch:true});
await p.goto('http://localhost:8080/about/',{waitUntil:'networkidle0'});
const chips=await p.$$('.pt-map__chip'); await chips[3].tap(); await new Promise(r=>setTimeout(r,300));
console.log(await p.evaluate(()=>[...document.querySelectorAll('.pt-map__chip')].map(c=>c.textContent+':'+c.classList.contains('is-active')).join(' ')+' | open cards: '+document.querySelectorAll('.pt-map__card.is-open').length+' '+document.querySelector('.pt-map__card.is-open .pt-map__city').textContent));
await chips[3].tap(); await new Promise(r=>setTimeout(r,300));
console.log('after second tap, open cards: '+await p.evaluate(()=>document.querySelectorAll('.pt-map__card.is-open').length));
await b.close();
