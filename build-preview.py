#!/usr/bin/env python3
"""Build a self-contained HTML preview of a local page: python3 build-preview.py /about/ preview/about.html "Title" "banner text" """
import re, base64, mimetypes, subprocess, os, sys
path, out, title, banner_text = sys.argv[1], sys.argv[2], sys.argv[3], sys.argv[4]
html = subprocess.run(['curl','-s','--noproxy','*','http://localhost:8080'+path],capture_output=True,text=True).stdout
theme_dir='/home/claude/protorque-wp/wp/wp-content/themes/protorque'; theme_url='http://localhost:8080/wp-content/themes/protorque'
def data_uri(p):
    mt=mimetypes.guess_type(p)[0] or 'application/octet-stream'
    if p.endswith('.woff2'): mt='font/woff2'
    if p.endswith('.woff'): mt='font/woff'
    if p.endswith('.svg'): mt='image/svg+xml'
    return f"data:{mt};base64,"+base64.b64encode(open(p,'rb').read()).decode()
html=re.sub(r'<!-- Google Tag Manager -->.*?<!-- End Google Tag Manager -->','',html,flags=re.S)
html=re.sub(r'<noscript><iframe src="https://www.googletagmanager.com[^<]*</iframe></noscript>','',html)
html=re.sub(r"<link rel='stylesheet' id='(?!pt-)[^']*'[^>]*>\n?",'',html)
html=re.sub(r"<style id='(?!pt-)[^']*'[^>]*>.*?</style>\n?",'',html,flags=re.S)
html=re.sub(r'<script[^>]*src="http://localhost:8080/wp-includes[^"]*"[^>]*></script>\n?','',html)
html=re.sub(r'<link[^>]*rel="(EditURI|wlwmanifest|alternate|shortlink|canonical)"[^>]*>\n?','',html)
html=re.sub(r'<link rel="https://api.w.org/"[^>]*>\n?','',html)
html=re.sub(r'<meta name="generator"[^>]*>\n?','',html)
def inline_css(m):
    rel=m.group(1).split(theme_url+'/')[1].split('?')[0]
    css=open(os.path.join(theme_dir,rel)).read(); base=os.path.dirname(os.path.join(theme_dir,rel))
    css=re.sub(r'url\("([^"]+)"\)', lambda u: f'url("{data_uri(os.path.normpath(os.path.join(base,u.group(1))))}")', css)
    return f'<style data-src="{rel}">\n{css}\n</style>'
html=re.sub(r"<link rel='stylesheet' id='pt-[^']*' href='("+re.escape(theme_url)+r"[^']*)'[^>]*>", inline_css, html)
html=re.sub(r'<script[^>]*src="('+re.escape(theme_url)+r'[^"]*)"[^>]*></script>', lambda m: '<script>\n'+open(os.path.join(theme_dir,m.group(1).split(theme_url+'/')[1].split('?')[0])).read()+'\n</script>', html)
html=re.sub(r'\s*srcset="[^"]*"','',html)
for attr in ('src','poster'):
    html=re.sub(attr+r'="'+re.escape(theme_url)+r'/([^"]+)"', lambda m: f'{attr}="{data_uri(os.path.join(theme_dir,m.group(1).split("?")[0]))}"', html)
html=re.sub(r'href="http://localhost:8080/[^"]*"','href="#" onclick="return false"',html)
html=re.sub(r'action="http://localhost:8080/"','action="#" onsubmit="return false"',html)
html=html.replace('http://localhost:8080/wp-json/','#').replace('"http://localhost:8080"','"#"')
html=re.sub(r'<title>[^<]*</title>', f'<title>{title}</title>', html, 1)
banner=f'<div style="position:fixed;left:0;right:0;bottom:0;z-index:999;background:#0F172A;color:#F1F5F9;font:12px/1.4 Calibri,Arial,sans-serif;padding:8px 16px;text-align:center">{banner_text}</div>'
html=html.replace('</body>',banner+'</body>')
open(out,'w').write(html); print(out, len(html)//1024, 'KB; localhost refs', html.count('localhost:8080'))
