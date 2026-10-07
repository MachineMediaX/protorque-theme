"""Parse WPBakery page content from the dev-site WXR export into rows of simple blocks.

Usage: python3 -I vcparse.py <xml> dump <post_id>      (readable dump)
       python3 -I vcparse.py <xml> json <post_id>      (JSON rows)
"""
import sys, re, json, html

TAG = re.compile(r'\[(/?)([a-zA-Z0-9_-]+)((?:\s+[a-zA-Z0-9_-]+="[^"]*")*)\s*/?\]')
ROW_TAGS = {'vc_row'}
SKIP = {'vc_column', 'vc_column_inner', 'vc_row_inner', 'vc_empty_space', 'vc_tta_section', 'vc_tta_tabs', 'vc_tta_accordion'}
SELF = {'vc_single_image', 'dt_fancy_image', 'ult_buttons', 'pt_tabs', 'pt_slider', 'table', 'vc_video', 'contact-form-7',
        'pt_jobs', 'stat_counter', 'vc_custom_heading', 'dt_media_gallery_carousel', 'vc_goo_maps', 'vc_separator', 'vc_icon',
        'vc_btn', 'ultimate_icon_list', 'vc_message', 'vc_raw_html', 'vc_cta', 'icon_counter', 'ultimate_heading'}
CONTAINER = {'vc_column_text', 'ultimate_icon_list_item', 'ultimate_exp_section', 'vc_message', 'vc_raw_html', 'vc_cta', 'ultimate_heading'}


def attrs(s):
    return dict(re.findall(r'([a-zA-Z0-9_-]+)="([^"]*)"', s))


def load_items(xml):
    s = open(xml, encoding='utf-8').read()
    out = {}
    for it in re.findall(r'<item>(.*?)</item>', s, re.S):
        pid = int(re.search(r'<wp:post_id>(\d+)', it).group(1))
        rec = {
            'id': pid,
            'type': re.search(r'<wp:post_type><!\[CDATA\[(.*?)\]\]>', it).group(1),
            'title': html.unescape(re.sub(r'<!\[CDATA\[|\]\]>', '', re.search(r'<title>(.*?)</title>', it, re.S).group(1))),
            'name': re.search(r'<wp:post_name><!\[CDATA\[(.*?)\]\]>', it).group(1),
            'parent': int(re.search(r'<wp:post_parent>(\d+)', it).group(1)),
            'status': re.search(r'<wp:status><!\[CDATA\[(.*?)\]\]>', it).group(1),
            'date': re.search(r'<wp:post_date><!\[CDATA\[(.*?)\]\]>', it).group(1),
            'url': re.search(r'<link>(.*?)</link>', it).group(1),
            'content': (re.search(r'<content:encoded><!\[CDATA\[(.*?)\]\]></content:encoded>', it, re.S) or [None, ''])[1],
            'excerpt': (re.search(r'<excerpt:encoded><!\[CDATA\[(.*?)\]\]></excerpt:encoded>', it, re.S) or [None, ''])[1],
            'meta': {},
            'terms': re.findall(r'<category domain="([^"]+)" nicename="([^"]+)"><!\[CDATA\[(.*?)\]\]></category>', it),
        }
        m = re.search(r'<wp:attachment_url><!\[CDATA\[(.*?)\]\]>', it)
        if m:
            rec['attachment_url'] = m.group(1)
        for k, v in re.findall(r'<wp:meta_key><!\[CDATA\[(.*?)\]\]></wp:meta_key>\s*<wp:meta_value><!\[CDATA\[(.*?)\]\]></wp:meta_value>', it, re.S):
            rec['meta'][k] = v
        out[pid] = rec
    return out


def clean_html(h):
    h = h.strip()
    h = re.sub(r'<(/?)(h[1-6]|p|ul|ol|li|strong|em|b|i|a|br)\b([^>]*)>', lambda m: '<%s%s%s>' % (m.group(1), m.group(2), re.sub(r'\s+(class|style|data-[a-z-]+)="[^"]*"', '', m.group(3))), h)
    h = re.sub(r'<span class="pt-step-num">(.*?)</span>', r'<h2>\1</h2>', h)
    h = re.sub(r'<span class="pt-step-label">(.*?)</span>', r'<h4>\1</h4>', h)
    h = re.sub(r'<div class="pt-step-body">(.*?)</div>', r'<p>\1</p>', h, flags=re.S)
    h = re.sub(r'<span[^>]*>|</span>|<div[^>]*>|</div>', '', h)
    h = re.sub(r'<(h[1-6])>\s*<\1>', r'<\1>', h)
    h = re.sub(r'</(h[1-6])>\s*</\1>', r'</\1>', h)
    h = re.sub(r'\s*\n\s*', '\n', h)
    return h.strip()


def parse_rows(content):
    rows = []
    row = None
    stack = []  # (tag, attrs, start)
    pos = 0
    for m in TAG.finditer(content):
        close, tag, a = m.group(1), m.group(2), attrs(m.group(3))
        if tag in ROW_TAGS:
            if not close:
                row = {'cls': a.get('el_class', ''), 'blocks': []}
                rows.append(row)
            continue
        if tag in SKIP:
            continue
        if row is None:
            row = {'cls': '', 'blocks': []}
            rows.append(row)
        if tag in CONTAINER:
            if not close:
                stack.append((tag, a, m.end()))
            else:
                # pop matching
                for i in range(len(stack) - 1, -1, -1):
                    if stack[i][0] == tag:
                        t, oa, start = stack.pop(i)
                        inner = content[start:m.start()]
                        if t == 'vc_column_text' or t == 'vc_raw_html':
                            row['blocks'].append({'t': 'text', 'cls': oa.get('el_class', ''), 'html': clean_html(inner)})
                        elif t == 'ultimate_icon_list_item':
                            row['blocks'].append({'t': 'li', 'html': clean_html(inner)})
                        elif t == 'ultimate_exp_section':
                            row['blocks'].append({'t': 'accordion', 'title': oa.get('title', ''), 'html': clean_html(re.sub(r'\[/?[^\]]+\]', '', inner))})
                        elif t == 'vc_message':
                            row['blocks'].append({'t': 'message', 'html': clean_html(inner)})
                        elif t == 'vc_cta':
                            row['blocks'].append({'t': 'cta', 'h2': oa.get('h2', ''), 'html': clean_html(inner)})
                        break
            continue
        if close:
            continue
        if tag == 'vc_custom_heading':
            fc = dict(x.split(':', 1) for x in a.get('font_container', '').split('|') if ':' in x)
            row['blocks'].append({'t': 'h', 'tag': fc.get('tag', 'h2'), 'text': html.unescape(a.get('text', '')), 'cls': a.get('el_class', '')})
        elif tag in ('vc_single_image', 'dt_fancy_image'):
            row['blocks'].append({'t': 'img', 'id': int(a.get('image') or a.get('image_id') or 0), 'cls': a.get('el_class', '')})
        elif tag == 'ult_buttons':
            link = a.get('btn_link', '')
            url = ''
            for part in link.split('|'):
                if part.startswith('url:'):
                    url = html.unescape(re.sub(r'%2F', '/', part[4:]).replace('%3A', ':'))
            row['blocks'].append({'t': 'btn', 'text': html.unescape(a.get('btn_title', '')), 'url': url})
        elif tag == 'pt_tabs':
            row['blocks'].append({'t': 'tabs', 'slug': a.get('slug', '')})
        elif tag == 'pt_slider':
            row['blocks'].append({'t': 'slider', 'id': int(a.get('id', 0))})
        elif tag == 'table':
            row['blocks'].append({'t': 'table', 'id': int(a.get('id', 0))})
        elif tag == 'vc_video':
            row['blocks'].append({'t': 'video', 'url': a.get('link', ''), 'title': a.get('title', '')})
        elif tag == 'contact-form-7':
            row['blocks'].append({'t': 'cf7', 'id': a.get('id', ''), 'title': a.get('title', '')})
        elif tag == 'pt_jobs':
            row['blocks'].append({'t': 'jobs'})
        elif tag == 'stat_counter':
            row['blocks'].append({'t': 'stat', 'a': a})
        elif tag == 'dt_media_gallery_carousel':
            row['blocks'].append({'t': 'gallery', 'ids': [int(x) for x in a.get('include', '').split(',') if x.strip().isdigit()]})
        elif tag == 'vc_goo_maps':
            row['blocks'].append({'t': 'map'})
        else:
            row['blocks'].append({'t': 'other', 'tag': tag, 'a': {k: v[:60] for k, v in a.items()}})
    # drop empty rows
    return [r for r in rows if r['blocks']]


def dump(rows):
    for i, r in enumerate(rows):
        print(f"--- row {i} [{r['cls']}]")
        for b in r['blocks']:
            if b['t'] == 'h':
                print(f"  <{b['tag']}> {b['text']}  ({b['cls']})")
            elif b['t'] == 'text':
                print(f"  text({b['cls']}): {re.sub(r'<[^>]+>', ' ', b['html'])[:160].strip()}")
            elif b['t'] == 'li':
                print(f"  li: {re.sub(r'<[^>]+>', ' ', b['html'])[:120].strip()}")
            else:
                print('  ', json.dumps(b)[:160])


if __name__ == '__main__':
    xml, mode, pid = sys.argv[1], sys.argv[2], int(sys.argv[3])
    items = load_items(xml)
    rows = parse_rows(items[pid]['content'])
    if mode == 'dump':
        print('#', items[pid]['title'], items[pid]['name'])
        dump(rows)
    else:
        print(json.dumps(rows, indent=1))
