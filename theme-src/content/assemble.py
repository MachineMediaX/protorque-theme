"""Turn dev-site WPBakery pages into structured section data for the ProTorque theme.

Reads the WXR export, resolves images from the local uploads, copies referenced images into the theme,
and writes wp-content/themes/protorque/inc/content/pages.json (+ tables, sliders, tabs, jobs).

Run from theme-src/content:  python3 -I assemble.py
"""
import sys, os, re, json, html, shutil, hashlib

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from vcparse import load_items, parse_rows

XML = '/home/claude/protorque-assets/export/protorque.WordPress.2026-10-06.xml'
UPLOADS = '/home/claude/protorque-wp/wp/wp-content/uploads'
THEME = '/home/claude/protorque-wp/wp/wp-content/themes/protorque'
OUT_IMG = os.path.join(THEME, 'assets/img/content')
OUT_JSON = os.path.join(THEME, 'inc/content')
TABLE_MAP = {1: 694, 2: 750, 3: 752, 4: 764, 5: 774, 6: 795, 7: 804, 8: 818, 9: 827, 10: 831}

PAGES = {
    # slug: kind
    'tubular-running-services': 'landing', 'drilling-services': 'landing', 'equipment-and-innovation': 'landing',
    'casing-running-and-power-tong-services': 'service', 'crt-redress-and-maintenance-program': 'service',
    'tubular-thread-cleaning-and-inspection': 'service', 'vacuum-drifting-and-casing-id-confirmation': 'service',
    'catm-for-power-tongs': 'service', 'catm-for-top-drive': 'service', 'ccs-casing-operations': 'service',
    'fiber-optic-casing-installation': 'service',
    'top-drive-and-hook-load-verification': 'service', 'torque-verification': 'service', 'iron-roughneck-torque-testing': 'service',
    'real-time-drilling-data': 'service', 'data-center-water-drilling': 'service', 'remote-technology-operations-center': 'service',
    'midstream-construction': 'service', 'plant-and-pipeline-maintenance': 'service',
    'facilities-electrical-instrumentation-mechanical': 'service', 'environmental-and-water-management': 'service',
    'row-services': 'service', 'specialty-piping': 'service',
    'rcd-press': 'equipment', 'crt-tether-ring': 'equipment', 'mobile-bha-frame': 'equipment', 'mobile-bucking-frame': 'equipment',
    'electric-hydraulic-power-unit': 'equipment', 'vacuum-drift-system': 'equipment', 'break-out-vise': 'equipment',
    'crt-nubbin': 'equipment', 'pipe-roller': 'equipment', 'flush-mounted-spider': 'equipment',
    'contact-us': 'contact', 'careers': 'careers', 'news': 'news',
}

items = load_items(XML)
by_slug = {it['name']: it for it in items.values() if it['type'] == 'page'}
attach = {pid: it for pid, it in items.items() if it['type'] == 'attachment'}
os.makedirs(OUT_IMG, exist_ok=True)
os.makedirs(OUT_JSON, exist_ok=True)


# ---------- PHP serialize reader (enough for the CPT meta) ----------
def php_unserialize(s):
    pos = 0

    def read():
        nonlocal pos
        t = s[pos]
        if t == 'i':
            end = s.index(';', pos)
            v = int(s[pos + 2:end]); pos = end + 1; return v
        if t == 'b':
            end = s.index(';', pos)
            v = s[pos + 2:end] == '1'; pos = end + 1; return v
        if t == 'd':
            end = s.index(';', pos)
            v = float(s[pos + 2:end]); pos = end + 1; return v
        if t == 'N':
            pos += 2; return None
        if t == 's':
            colon = s.index(':', pos + 2)
            n = int(s[pos + 2:colon])
            start = colon + 2
            # n is a byte length
            b = s[start:].encode('utf-8')[:n].decode('utf-8')
            pos = start + len(b) + 2
            return b
        if t == 'a':
            colon = s.index(':', pos + 2)
            n = int(s[pos + 2:colon])
            pos = colon + 2
            d = {}
            for _ in range(n):
                k = read(); v = read(); d[k] = v
            pos += 1  # }
            if all(isinstance(k, int) for k in d) and list(d.keys()) == list(range(len(d))):
                return [d[i] for i in range(len(d))]
            return d
        raise ValueError('bad token %r at %d' % (t, pos))
    return read()


# ---------- images ----------
copied = {}


def img_path(aid):
    """Copy upload file into the theme and return the theme-relative path."""
    aid = int(aid)
    if aid in copied:
        return copied[aid]
    it = attach.get(aid)
    rel = it['meta'].get('_wp_attached_file') if it else None
    if not rel and it:
        rel = re.sub(r'^.*?/wp-content/uploads/', '', it['attachment_url'])
    if not rel:
        print('  !! no attachment', aid, file=sys.stderr); copied[aid] = None; return None
    src = os.path.join(UPLOADS, rel)
    if not os.path.exists(src):
        print('  !! missing file', aid, rel, file=sys.stderr); copied[aid] = None; return None
    base = os.path.basename(rel)
    dst = os.path.join(OUT_IMG, base)
    if os.path.exists(dst) and open(dst, 'rb').read(64) != open(src, 'rb').read(64):
        base = hashlib.md5(rel.encode()).hexdigest()[:6] + '-' + base
        dst = os.path.join(OUT_IMG, base)
    if not os.path.exists(dst):
        shutil.copyfile(src, dst)
    copied[aid] = 'content/' + base
    return copied[aid]


# ---------- text helpers ----------
def strip_tags(h):
    return html.unescape(re.sub(r'<[^>]+>', ' ', h)).replace('\xa0', ' ').strip()


def norm_space(t):
    return re.sub(r'\s+', ' ', t).strip()


def paras(h):
    """Split a column-text html into paragraphs (keeps inline markup: strong, em, a, br)."""
    h = re.sub(r'\[[^\]]+\]', '', h)
    h = re.sub(r'<(h[1-6])[^>]*>.*?</\1>', '', h, flags=re.S)
    h = re.sub(r'<br\s*/?>', '\n', h)
    h = re.sub(r'</?(p|div)[^>]*>', '\n', h)
    out = []
    for chunk in re.split(r'\n\s*\n|\n', h):
        c = chunk.strip()
        c = re.sub(r'<(h[1-6])[^>]*>.*?</\1>', '', c, flags=re.S).strip()
        if c and c != '&nbsp;':
            out.append(norm_space(c))
    return out


def headings(h):
    return [(m.group(1), strip_tags(m.group(2))) for m in re.finditer(r'<(h[1-6])[^>]*>(.*?)</\1>', h, re.S)]


def link(h):
    m = re.search(r'<a[^>]*href="([^"]*)"[^>]*>(.*?)</a>', h, re.S)
    return (html.unescape(m.group(1)), strip_tags(m.group(2))) if m else None


def clean_url(u):
    u = re.sub(r'^https?://protorque\.s3-devsite\.com', '', u)
    return u


def titled_block(h):
    """'<hN>Title</hN> body' -> (title, [paras]); also handles '<h2>1</h2><h4>Title</h4> body'."""
    hs = headings(h)
    title = ''
    for tag, t in hs:
        if re.fullmatch(r'\d+|"|“', t.strip()):
            continue
        title = norm_space(t); break
    body = paras(h)
    if not hs and body:
        title, body = body[0], body[1:]
    return title, body


def is_num(b):
    return b['t'] == 'text' and re.fullmatch(r'\s*(<h\d>)?\s*\d+\s*(</h\d>)?\s*', b['html'] or '') is not None


# ---------- section assembly ----------
def flatten(rows):
    out = []
    for r in rows:
        out.append({'t': 'row', 'cls': r['cls']})
        for b in r['blocks']:
            if b['t'] == 'text':
                # tables inside column text
                for m in re.finditer(r'\[table id=(\d+)', b['html']):
                    out.append({'t': 'table', 'id': int(m.group(1))})
                hh = re.sub(r'\[[^\]]*\]', '', b['html']).strip()
                if not strip_tags(hh):
                    continue
                b = dict(b, html=hh)
            if b['t'] == 'other' and b.get('tag') in ('dt_gap', 'ultimate_icon_list'):
                continue
            out.append(b)
    return out


def assemble(slug, kind):
    it = by_slug[slug]
    blocks = flatten(parse_rows(it['content']))
    page = {'slug': slug, 'kind': kind, 'title': it['title'], 'id': it['id'], 'sections': []}
    sec = None
    row_cls = ''

    def new(kind_, title='', eyebrow=''):
        nonlocal sec
        sec = {'kind': kind_, 'title': title, 'eyebrow': eyebrow, 'paras': [], 'items': [], 'cards': [], 'steps': [],
               'tiles': [], 'benefits': [], 'images': [], 'quote': '', 'intro': []}
        page['sections'].append(sec)
        return sec

    pending_eyebrow = ''
    i = 0
    while i < len(blocks):
        b = blocks[i]
        t = b['t']
        if t == 'row':
            row_cls = b['cls']
            # CTA rows: red gradient or black row with button and no heading
            rest = []
            j = i + 1
            while j < len(blocks) and blocks[j]['t'] != 'row':
                rest.append(blocks[j]); j += 1
            if any(x['t'] == 'btn' for x in rest) and not any(x['t'] == 'h' and x['tag'] in ('h1', 'h2') for x in rest):
                texts = [x for x in rest if x['t'] == 'text']
                cta = new('cta')
                if texts and 'cta-row' in texts[0]['cls']:
                    hh = re.sub(r'\[[^\]]*\]', '', texts[0]['html'])
                    hs_ = headings(hh)
                    if hs_:
                        cta['title'] = norm_space(hs_[0][1])
                        cta['paras'] = [norm_space(x[1]) for x in hs_[1:]] + paras(hh)
                    else:
                        ps = paras(hh)
                        cta['title'] = ps[0] if ps else ''
                        cta['paras'] = ps[1:]
                else:
                    cta['title'] = strip_tags(texts[0]['html']) if texts else ''
                    cta['paras'] = [p for x in texts[1:] for p in paras(x['html'])]
                cta['buttons'] = [{'text': x['text'], 'url': clean_url(x['url'])} for x in rest if x['t'] == 'btn' and not x['url'].startswith('tel:')]
                i = j
                sec = None
                continue
            i += 1
            continue
        if t == 'h':
            text = norm_space(b['text'])
            if b['tag'] == 'h1':
                s = new('hero', text)
                s['sub'] = ''
            elif b['tag'] in ('h2', 'h3') and not re.fullmatch(r'["“”]|\d+', text):
                if sec and sec['kind'] == 'hero' and b['tag'] == 'h3':
                    pass
                s = new('section', text, pending_eyebrow)
                pending_eyebrow = ''
            else:
                if sec is not None:
                    sec['paras'].append(text)
            i += 1
            continue
        if t == 'text':
            cls = b['cls']
            h = b['html']
            if 'eyebrow' in cls:
                pending_eyebrow = strip_tags(h)
                if sec and sec['kind'] in ('section',) and not sec['items'] and not sec['paras'] and not sec['eyebrow']:
                    pass
                i += 1; continue
            if sec is None:
                new('section', '', pending_eyebrow); pending_eyebrow = ''
            if sec['kind'] == 'hero':
                if 'subheader' in cls or 'page-title-text' in cls:
                    sec['sub'] = norm_space(strip_tags(h))
                else:
                    sec['paras'] += paras(h)
                i += 1; continue
            # numbered services list on landings: text "N" + title + desc + link
            if is_num(b) and i + 3 < len(blocks) and blocks[i + 1]['t'] == 'text' and blocks[i + 2]['t'] == 'text':
                title = strip_tags(blocks[i + 1]['html'])
                desc = blocks[i + 2]['html']
                lk = link(blocks[i + 3]['html']) if i + 3 < len(blocks) and blocks[i + 3]['t'] == 'text' else None
                lead = ''
                m = re.search(r'<strong>(.*?)</strong>', desc, re.S)
                if m:
                    lead = strip_tags(m.group(1)); desc = desc.replace(m.group(0), '')
                sec['cards'].append({'n': strip_tags(b['html']), 'title': title, 'lead': lead, 'text': norm_space(strip_tags(desc)),
                                     'cta': lk[1] if lk else '', 'url': clean_url(lk[0]) if lk else ''})
                i += 4 if lk else 3
                continue
            if '<li' in h:
                lis = re.findall(r'<li[^>]*>(.*?)</li>', h, re.S)
                h_wo = re.sub(r'<[uo]l[^>]*>.*?</[uo]l>', '', h, flags=re.S)
                sec['paras'] += paras(h_wo)
                for tag, tt in headings(h_wo):
                    sec['items'].append({'kind': 'subhead', 'text': norm_space(tt)})
                for li in lis:
                    sec['items'].append({'kind': 'li', 'text': norm_space(strip_tags(li))})
                i += 1; continue
            hs = headings(h)
            # landing numbered list in a single block: <h2>N</h2><h4>Title</h4> lead text <a>
            if hs and re.fullmatch(r'\d+', hs[0][1].strip()) and len(hs) >= 2 and link(h):
                lk = link(h)
                body = re.sub(r'<a[^>]*>.*?</a>', '', h, flags=re.S)
                m = re.search(r'<strong>(.*?)</strong>', body, re.S)
                lead = strip_tags(m.group(1)) if m else ''
                if m:
                    body = body.replace(m.group(0), '')
                sec['cards'].append({'n': hs[0][1].strip(), 'title': norm_space(hs[1][1]), 'lead': lead, 'text': ' '.join(paras(body)),
                                     'cta': lk[1], 'url': clean_url(lk[0])})
                i += 1; continue
            # process step: <h2>N</h2><h4>Title</h4><p>body</p>
            if hs and re.fullmatch(r'\d+', hs[0][1].strip()) and len(hs) >= 2:
                title, body = titled_block(h)
                if re.search(r'why operators', sec['title'], re.I):
                    sec['benefits'].append({'title': title, 'text': ' '.join(body)})
                else:
                    sec['steps'].append({'title': title, 'text': ' '.join(body)})
                i += 1; continue
            # pull quote
            if (hs and hs[0][1].strip() in ('"', '“', '”')):
                sec['quote'] = ' '.join(paras(h))
                i += 1; continue
            # stat tile: <h2|h5>Big</h2> label  (white text in overview rows)
            if 'hide-on-all' in cls:
                i += 1; continue
            if hs and 'text-white' in cls and len(hs) == 1 and len(strip_tags(hs[0][1])) <= 14 and paras(h):
                sec['tiles'].append({'value': norm_space(hs[0][1]), 'label': ' '.join(paras(h))})
                i += 1; continue
            # location card (contact)
            if 'location-card' in cls:
                sec['items'].append({'kind': 'location', 'html': h})
                i += 1; continue
            # benefit block (why operators use it): <h4>Title</h4><p>text</p>
            if hs and re.search(r'why operators', sec['title'], re.I):
                title, body = titled_block(h)
                sec['benefits'].append({'title': title, 'text': ' '.join(body)})
                i += 1; continue
            # related card: an image followed by 1-3 text blocks (title / text / link)
            if sec['images'] and i > 0 and blocks[i - 1]['t'] == 'img':
                j = i; parts = []
                while j < len(blocks) and blocks[j]['t'] == 'text' and len(parts) < 3:
                    parts.append(blocks[j]['html']); j += 1
                    if link(parts[-1]):
                        break
                combined = '\n'.join(parts)
                lk = link(combined)
                title, body = titled_block(re.sub(r'<a[^>]*>.*?</a>', '', combined, flags=re.S))
                sec['cards'].append({'img': sec['images'].pop(), 'title': title, 'text': ' '.join(body),
                                     'cta': lk[1] if lk else '', 'url': clean_url(lk[0]) if lk else ''})
                i = j; continue
            # list heading inside text (h5 Applications:)
            if hs and not paras(h):
                sec['items'].append({'kind': 'subhead', 'text': norm_space(hs[0][1])})
                i += 1; continue
            # plain paragraphs (intro if list items follow later, else paras); stray headings become subheads
            for tag_, tt_ in hs:
                sec['items'].append({'kind': 'subhead', 'text': norm_space(tt_)})
            sec['paras'] += paras(h)
            i += 1; continue
        if t == 'li':
            if sec is None:
                new('section')
            sec['items'].append({'kind': 'li', 'text': norm_space(strip_tags(b['html']))})
            i += 1; continue
        if t == 'img':
            if sec is None:
                new('section')
            p = img_path(b['id'])
            if sec['kind'] == 'hero':
                sec['image'] = p
            else:
                sec['images'].append(p)
            i += 1; continue
        if t == 'message':
            sec['quote'] = ' '.join(paras(b['html'])); i += 1; continue
        if t == 'btn':
            sec.setdefault('buttons', []).append({'text': b['text'], 'url': clean_url(b['url'])}); i += 1; continue
        if t == 'tabs':
            sec['tabs'] = tabsets.get(b['slug'], []); i += 1; continue
        if t == 'slider':
            sec['slides'] = sliders.get(b['id'], []); i += 1; continue
        if t == 'table':
            sec.setdefault('tables', []).append(tables.get(TABLE_MAP.get(b['id']))); i += 1; continue
        if t == 'video':
            m = re.search(r'(?:v=|youtu\.be/)([\w-]+)', b['url'])
            sec['video'] = m.group(1) if m else b['url']; i += 1; continue
        if t == 'gallery':
            sec['gallery'] = [img_path(x) for x in b['ids']]; i += 1; continue
        if t == 'cf7':
            sec['form'] = b['title']; i += 1; continue
        if t in ('jobs', 'map'):
            sec[t] = True; i += 1; continue
        if t == 'other':
            sec = sec or new('section')
            sec.setdefault('other', []).append(b.get('tag')); i += 1; continue
        i += 1

    # classify sections
    for s in page['sections']:
        if s['kind'] != 'section':
            continue
        title = s['title'].lower()
        if s['steps']:
            s['kind'] = 'process'
        elif s['benefits']:
            s['kind'] = 'benefits'
        elif s.get('tables'):
            s['kind'] = 'specs'
        elif s.get('tabs') is not None:
            s['kind'] = 'tabs'
        elif s.get('slides') is not None:
            s['kind'] = 'slider'
        elif s['cards'] and 'n' in s['cards'][0]:
            s['kind'] = 'services'
        elif s['cards']:
            s['kind'] = 'related'
        elif s.get('form'):
            s['kind'] = 'form'
        elif s.get('jobs'):
            s['kind'] = 'jobs'
        elif s.get('map') or any(x['kind'] == 'location' for x in s['items']):
            s['kind'] = 'locations'
        elif s['items']:
            s['kind'] = 'list'
        elif 'why this matters' in title or s['quote']:
            s['kind'] = 'why'
        else:
            s['kind'] = 'overview'
        if s['kind'] in ('list',) and s['paras']:
            s['intro'], s['paras'] = s['paras'], []
        if s['kind'] == 'related' and s['paras']:
            s['intro'], s['paras'] = s['paras'], []
        if s['kind'] == 'services' and s['paras']:
            s['intro'], s['paras'] = s['paras'], []
    # drop empty keys for readability
    for s in page['sections']:
        for k in list(s.keys()):
            if s[k] in ([], '', None, {}):
                del s[k]
    return page


# ---------- CPT data ----------
tabsets = {}
sliders = {}
tables = {}
jobs = []
for pid, it in items.items():
    if it['type'] == 'pt_tabset':
        tabs = php_unserialize(it['meta']['_pt_tabs'])
        tabsets[it['name']] = [{'label': t['label'], 'eyebrow': t.get('eyebrow', ''), 'title': t['header'], 'text': t['text'],
                               'img': img_path(t['image_id']) if t.get('image_id') else None} for t in tabs]
    elif it['type'] == 'pt_slider':
        sl = php_unserialize(it['meta']['_pt_slider_slides'])
        sliders[pid] = [{'img': img_path(s['image_id']) if s.get('image_id') else None, 'title': html.unescape(s['title']), 'subtitle': s.get('subtitle', ''),
                         'text': s.get('body', ''), 'cta': html.unescape(s.get('link_text', '')), 'url': clean_url(s.get('link_url', ''))} for s in sl]
    elif it['type'] == 'tablepress_table':
        rows = json.loads(it['content'])
        opts = json.loads(it['meta'].get('_tablepress_table_options', '{}'))
        tables[pid] = {'title': it['title'], 'rows': rows, 'head': bool(opts.get('table_head'))}
    elif it['type'] == 'pt_job':
        m = it['meta']
        jobs.append({'title': it['title'], 'slug': it['name'], 'date': it['date'], 'content': it['content'],
                     'country': m.get('_pt_job_country', ''), 'region': m.get('_pt_job_region', ''), 'city': m.get('_pt_job_city', ''),
                     'discipline': m.get('_pt_job_discipline', ''), 'employment': m.get('_pt_job_employment_type', ''),
                     'closing': m.get('_pt_job_closing_date', ''), 'email': m.get('_pt_job_apply_email', ''),
                     'status': it['status']})


# ---------- link resolver for '#' placeholders ----------
def page_url(it):
    parts = [it['name']]
    par = it['parent']
    while par:
        parts.append(items[par]['name']); par = items[par]['parent']
    return '/' + '/'.join(reversed(parts)) + '/'


def key(t):
    t = html.unescape(t).lower()
    t = re.sub(r'\(.*?\)', ' ', t)
    t = t.replace('&', ' and ').replace('+', ' and ')
    t = re.sub(r'[^a-z0-9]+', ' ', t).strip()
    t = re.sub(r'\b(the|and|for|of|program|services|service|to|by|prostar)\b', ' ', t)
    return re.sub(r'\s+', ' ', t).strip()


title_urls = {}
for it in items.values():
    if it['type'] == 'page' and it['status'] in ('publish', 'private'):
        title_urls[key(it['title'])] = page_url(it)
ALIASES = {
    'engineered equipment': '/equipment-and-innovation/', 'equipment': '/equipment-and-innovation/',
    'equipment innovation': '/equipment-and-innovation/', 'hook load verification': '/drilling-services/top-drive-and-hook-load-verification/',
    'top drive hook load verification': '/drilling-services/top-drive-and-hook-load-verification/',
    'crt redress': '/tubular-running-services/crt-redress-and-maintenance-program/',
    'crt redress maintenance': '/tubular-running-services/crt-redress-and-maintenance-program/',
    'thread cleaning': '/tubular-running-services/tubular-thread-cleaning-and-inspection/',
    'casing running': '/tubular-running-services/casing-running-and-power-tong-services/',
    'vacuum drifting': '/tubular-running-services/vacuum-drifting-and-casing-id-confirmation/',
    'iron roughneck': '/drilling-services/iron-roughneck-torque-testing/',
    'real time data': '/drilling-services/real-time-drilling-data/',
    'catm': '/tubular-running-services/catm-for-power-tongs/',
    'ccs casing': '/tubular-running-services/ccs-casing-operations/',
    'facilities e i': '/midstream-services/facilities-electrical-instrumentation-mechanical/',
    'facilities e i mechanical': '/midstream-services/facilities-electrical-instrumentation-mechanical/',
    'specialty piping fiber glass': '/midstream-services/specialty-piping/',
    'ehpu': '/equipment-and-innovation/electric-hydraulic-power-unit/',
    'electric hydraulic power unit ehpu': '/equipment-and-innovation/electric-hydraulic-power-unit/',
    'vacuum drifting casing id confirmation': '/tubular-running-services/vacuum-drifting-and-casing-id-confirmation/',
}
# CATM pages live under TRS on the dev site; the nav puts them under Drilling. Keep the dev-site URLs (the pages are children of TRS).
unresolved = []


def resolve(url, title, cta=''):
    if url and url != '#':
        if url in title_urls.values() or url in ALIASES.values() or not url.startswith('/'):
            return url
        print('  fixing dead link', url, file=sys.stderr)
    for cand in (key(title), key(re.sub(r'^explore\s+', '', cta or '', flags=re.I))):
        if cand in title_urls:
            return title_urls[cand]
        if cand in ALIASES:
            return ALIASES[cand]
    unresolved.append((title, cta))
    return '#'


CARD_IMG = {
    'casing-running-and-power-tong-services': 'content/casing-running-card.jpg',
    'crt-redress-and-maintenance-program': 'content/crt-redress-2.jpg',
    'tubular-thread-cleaning-and-inspection': 'content/teeth-inspection.jpg',
    'vacuum-drifting-and-casing-id-confirmation': 'content/vacuum-drift-card.jpg',
    'catm-for-power-tongs': 'content/catm-for-power-tongs.jpg',
    'catm-for-top-drive': 'content/catm-for-top-drive.jpg',
    'top-drive-and-hook-load-verification': 'content/card-hook-load.jpg',
    'torque-verification': 'content/card-torque-verification.jpg',
    'iron-roughneck-torque-testing': 'content/irong-roughneck-card-2.jpg',
    'real-time-drilling-data': 'content/real-time-drilling-data-card.jpg',
    'midstream-construction': 'content/midstream-card.jpg',
    'plant-and-pipeline-maintenance': 'content/plant-and-pipeline-card.jpg',
    'facilities-electrical-instrumentation-mechanical': 'content/facilities-electrical-card.jpg',
    'environmental-and-water-management': 'content/environmental-water-card.jpg',
    'specialty-piping': 'content/specialty-piping-card.jpg',
    'crt-tether-ring': 'content/crt-tether-ring.jpg',
    'crt-nubbin': 'content/crt-nubbin-card.jpg',
    'mobile-bucking-frame': 'content/mobile-bucking-frame-card.jpg',
    'mobile-bha-frame': 'content/mobile-bha-800.png',
    'pipe-roller': 'content/pipe-rolloer-2.png',
    'flush-mounted-spider': 'content/flush-mounted-spider-800.png',
    'break-out-vise': 'content/break-out-vise-800.png',
    'electric-hydraulic-power-unit': 'content/ehpu-800.png',
    'rcd-press': 'content/rcd-press-800.png',
    'vacuum-drift-system': 'content/vacuum-drift-800.png',
    'equipment-and-innovation': 'content/bucking-frame-4.jpg',
    'drilling-services': 'content/card-drilling-services.jpg',
    'midstream-services': 'content/midstream-operations.jpg',
    'tubular-running-services': 'content/tubular-running-services.jpg',
}


def post(page):
    hero_img = page['sections'][0].get('image') if page['sections'] else None
    for s_ in page['sections']:
        if s_.get('gallery'):
            s_['gallery'] = [g for g in s_['gallery'] if g and 'place-holder' not in g and g != hero_img]
            if not s_['gallery']:
                del s_['gallery']
    for s_ in page['sections']:
        for c in s_.get('cards', []):
            c['url'] = resolve(c.get('url', ''), c.get('title', ''), c.get('cta', ''))
            target = c['url'].rstrip('/').split('/')[-1]
            if 'img' in c and target in CARD_IMG:
                c['img'] = CARD_IMG[target]
        if s_['kind'] in ('related',):
            seen = set()
            for c in s_['cards']:
                if c.get('img') in seen:
                    print('  !! duplicate card image on', page['slug'], c['img'], file=sys.stderr)
                seen.add(c.get('img'))
        if s_['kind'] == 'locations':
            offices, cur = [], None
            for itx in s_['items']:
                if itx['kind'] != 'location':
                    continue
                h = itx['html']
                if '<h4>' in h:
                    cur = {'name': norm_space(headings(h)[0][1]), 'lines': [], 'phones': [], 'mail': [], 'email': '', 'directions': ''}
                    offices.append(cur)
                    body = re.sub(r'<h4>.*?</h4>', '', h, flags=re.S)
                    body = re.sub(r'<h5>(.*?)</h5>', r'\1', body)
                    for m in re.finditer(r'<a href="tel:([^"]+)"[^>]*>(.*?)</a>', body):
                        cur['phones'].append(norm_space(strip_tags(m.group(2))))
                    cur['lines'] = [norm_space(x) for x in re.sub(r'<a[^>]*>.*?</a>', '', body, flags=re.S).replace('<strong>', '\n').replace('</strong>', '\n').split('\n') if norm_space(strip_tags(x)) and norm_space(strip_tags(x)) not in ('+',)]
                    cur['lines'] = [strip_tags(x) for x in cur['lines']]
                elif 'Mailing' in h and cur:
                    body = re.sub(r'<h5>.*?</h5>', '', h, flags=re.S)
                    m = re.search(r'mailto:([^"]+)', body)
                    cur['email'] = html.unescape(m.group(1)) if m else ''
                    body = re.sub(r'<a[^>]*>.*?</a>', '', body, flags=re.S)
                    cur['mail'] = [norm_space(strip_tags(x)) for x in body.split('\n') if norm_space(strip_tags(x))]
                elif 'directions' in h.lower() and cur:
                    m = re.search(r'href="([^"]+)"', h)
                    cur['directions'] = html.unescape(m.group(1)) if m else ''
            s_['offices'] = offices
            del s_['items']
    return page


pages = {}
for slug, kind in PAGES.items():
    if slug not in by_slug:
        print('!! no page', slug, file=sys.stderr); continue
    print('==', slug, file=sys.stderr)
    pages[slug] = post(assemble(slug, kind))
# equipment landing cards get the product photo from each equipment page
for s_ in pages['equipment-and-innovation']['sections']:
    if s_['kind'] == 'services':
        for c in s_['cards']:
            target = c['url'].rstrip('/').split('/')[-1]
            if target in pages:
                hero = pages[target]['sections'][0]
                c['img'] = hero.get('image')
for u in unresolved:
    print('!! unresolved link', u, file=sys.stderr)

json.dump(pages, open(os.path.join(OUT_JSON, 'pages.json'), 'w'), indent=1, ensure_ascii=False)
json.dump(jobs, open(os.path.join(OUT_JSON, 'jobs.json'), 'w'), indent=1, ensure_ascii=False)
json.dump({'tables': tables, 'sliders': sliders, 'tabs': tabsets}, open(os.path.join(OUT_JSON, 'data.json'), 'w'), indent=1, ensure_ascii=False)
print('pages:', len(pages), 'images copied:', len([v for v in copied.values() if v]), file=sys.stderr)
