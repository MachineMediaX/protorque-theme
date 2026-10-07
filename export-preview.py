#!/usr/bin/env python3
"""Export built pages as a static multi-page preview: python3 export-preview.py
Writes preview/site/{index.html, about.html, assets/..., uploads/...}. Links to pages that are not built yet show a notice."""
import re, os, shutil, subprocess, urllib.parse
BASE='http://localhost:8080'
PAGES={'/': 'index.html', '/about/': 'about.html'}
OUT='preview/site'
THEME='/home/claude/protorque-wp/wp/wp-content/themes/protorque'
UPLOADS='/home/claude/protorque-wp/wp/wp-content/uploads'
shutil.rmtree(OUT, ignore_errors=True); os.makedirs(OUT)
copied=set()
def copy_asset(url):
    """Map a local asset URL to a relative path in the export, copying the file."""
    path=urllib.parse.urlparse(url).path
    if path.startswith('/wp-content/themes/protorque/'):
        rel='assets/'+path[len('/wp-content/themes/protorque/assets/'):] if '/assets/' in path else None
        src=THEME+path[len('/wp-content/themes/protorque'):]
    elif path.startswith('/wp-content/uploads/'):
        rel='uploads/'+path[len('/wp-content/uploads/'):]; src=UPLOADS+path[len('/wp-content/uploads'):]
    else:
        return None
    if rel and os.path.isfile(src):
        dst=os.path.join(OUT,rel)
        if dst not in copied:
            os.makedirs(os.path.dirname(dst),exist_ok=True); shutil.copy(src,dst); copied.add(dst)
        return rel
    return None
notice='''<script>document.addEventListener('click',function(e){var a=e.target.closest('a[data-notbuilt]');if(!a)return;e.preventDefault();var t=document.getElementById('pt-notbuilt');if(!t){t=document.createElement('div');t.id='pt-notbuilt';t.style.cssText='position:fixed;left:50%;bottom:48px;transform:translateX(-50%);background:#0F172A;color:#F1F5F9;font:14px/1.4 Calibri,Arial,sans-serif;padding:12px 18px;border-radius:4px;z-index:1000;box-shadow:0 10px 30px rgba(0,0,0,.3)';document.body.appendChild(t);}t.textContent='Not built yet: '+a.getAttribute('data-notbuilt');clearTimeout(t._h);t._h=setTimeout(function(){t.remove();},2200);});</script>'''
banner='<div style="position:fixed;left:0;right:0;bottom:0;z-index:999;background:#0F172A;color:#F1F5F9;font:12px/1.4 Calibri,Arial,sans-serif;padding:8px 16px;text-align:center">Development preview. Pages built so far: Home, About. Other links show a notice. Hero still is a placeholder pending the video.</div>'
for path,fname in PAGES.items():
    html=subprocess.run(['curl','-s','--noproxy','*',BASE+path],capture_output=True,text=True).stdout
    html=re.sub(r'<!-- Google Tag Manager -->.*?<!-- End Google Tag Manager -->','',html,flags=re.S)
    html=re.sub(r'<noscript><iframe src="https://www.googletagmanager.com[^<]*</iframe></noscript>','',html)
    html=re.sub(r"<link rel='stylesheet' id='(?!pt-)[^']*'[^>]*>\n?",'',html)
    html=re.sub(r"<style id='(?!pt-)[^']*'[^>]*>.*?</style>\n?",'',html,flags=re.S)
    html=re.sub(r'<script[^>]*src="http://localhost:8080/wp-includes[^"]*"[^>]*></script>\n?','',html)
    html=re.sub(r'<link[^>]*rel="(EditURI|wlwmanifest|alternate|shortlink|canonical)"[^>]*>\n?','',html)
    html=re.sub(r'<link rel="https://api.w.org/"[^>]*>\n?','',html)
    html=re.sub(r'<meta name="generator"[^>]*>\n?','',html)
    html=re.sub(r"<script[^>]*>(?:(?!</script>).)*?_wpemojiSettings(?:(?!</script>).)*?</script>\n?",'',html,flags=re.S)
    html=re.sub(r"<style[^>]*>(?:(?!</style>).)*?img\.wp-smiley(?:(?!</style>).)*?</style>\n?",'',html,flags=re.S)
    html=re.sub(r'srcset="('+re.escape(BASE)+r'/wp-content/[^" ]+)( 2x)"', lambda m: f'srcset="{copy_asset(m.group(1).split("?")[0]) or m.group(1)}{m.group(2)}"', html)
    html=re.sub(r"href='"+re.escape(BASE)+r"/'", "href='index.html'", html)
    # assets: css/js/img/fonts/uploads
    def asset(m):
        q=m.group(1); rel=copy_asset(m.group(2).split('?')[0]) or m.group(2)
        return f'{m.group(0)[:m.group(0).index(q)+1]}{rel}{q}'
    html=re.sub(r'(?:href|src|poster|data-image)=(["\'])('+re.escape(BASE)+r'/wp-content/[^"\']+)\1', lambda m: m.group(0).replace(m.group(2), copy_asset(m.group(2).split('?')[0]) or m.group(2)), html)
    html=re.sub(r"<link rel='stylesheet' id='pt-[^']*' href='("+re.escape(BASE)+r"/wp-content/[^']*)'", lambda m: m.group(0).replace(m.group(1), copy_asset(m.group(1).split('?')[0]) or m.group(1)), html)
    # page links
    def link(m):
        href=m.group(1); p=urllib.parse.urlparse(href).path or '/'
        if p in PAGES: return f'href="{PAGES[p]}"'
        if p.startswith('/wp-json') or p.startswith('/?s') : return 'href="#"'
        label=p.strip('/').split('/')[-1].replace('-',' ') or 'page'
        return f'href="#" data-notbuilt="{label}"'
    html=re.sub(r'href="('+re.escape(BASE)+r'[^"]*)"', link, html)
    html=html.replace(f'action="{BASE}/"','action="#" onsubmit="return false"').replace(f'"{BASE}"','"#"')
    html=html.replace('</body>', banner+notice+'</body>')
    open(os.path.join(OUT,fname),'w').write(html)
    print(fname, len(html)//1024,'KB, localhost refs', html.count('localhost:8080'))
# CSS url() references inside the copied stylesheets are relative to assets/css already (../fonts, ../img): copy those too
for root,_,files in os.walk(THEME+'/assets'):
    for f in files:
        src=os.path.join(root,f); rel='assets/'+os.path.relpath(src,THEME+'/assets')
        if rel.startswith('assets/img/placeholder/texture') or '/fonts/' in rel or rel.endswith(('.svg','.css','.js')):
            dst=os.path.join(OUT,rel); os.makedirs(os.path.dirname(dst),exist_ok=True); shutil.copy(src,dst)
total=sum(os.path.getsize(os.path.join(r,f)) for r,_,fs in os.walk(OUT) for f in fs)
print('files', sum(len(fs) for _,_,fs in os.walk(OUT)), 'total MB', round(total/1048576,1))
