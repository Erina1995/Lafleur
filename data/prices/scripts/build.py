import json,re,collections,csv
from statistics import median
pk=json.load(open('pc_pokemon_all.json')); op=json.load(open('pc_onepiece_all.json')); tcg=json.load(open('tcg_all.json'))

CARDCODE=re.compile(r'\b(OP|ST|EB|P|PRB|SP|DON)\d{0,2}-\d{2,3}\b|#\d|\bDON!*\s*Card\b',re.I)
SEALED_KW=re.compile(r'\b(box|pack|packs|tin|tins|bundle|blister|blisters|collection|coffret|deck|decks|case|display|kit|chest|stadium|set|academy|premium|booster|carton|treasure|gift)\b',re.I)
ACC=re.compile(r'\b(sleeves?|playmat|binder|portfolio|album|dice|coin|lanyard|plush|deck shield|deck box|card box|storage|figure only|poster only|damage counter|token)\b',re.I)
def bare(n): return re.sub(r'\s+',' ',re.sub(r'\[.*?\]|\(.*?\)','',n)).strip()
def classify(name):
    n=bare(name).lower()
    if ACC.search(n) and not re.search(r'booster|pack|collection|bundle',n): return 'Accessory'
    if re.search(r'\b(case|display|carton)\b',n) and not re.search(r'display box$',n): return 'Case/Display'
    if re.search(r'\bdeck\b|\bstarter set\b|\bbattle academy\b|\btrainer kit\b|\bleague battle\b|\bstart deck\b|\bstarter\b',n) and not re.search(r'deck bundle|deck box',n): return 'Deck'
    if re.search(r'\b(booster box|half booster box|booster display|display box)\b',n): return 'Box'
    if re.search(r'\b(booster pack|sleeved booster|blister|checklane|hanger|fun pack|event pack|mini booster|promo pack|pack)\b',n) and not re.search(r'bundle|collection|tin|box|chest|kit|\bpacks?\s*(box|set)\b|\d+-pack|pack case',n): return 'Pack'
    if re.search(r'\b(box|tin|tins|bundle|collection|coffret|chest|kit|stadium|set|premium|gift|treasure|\d+-pack)\b',n): return 'Coffret'
    return 'Other'
def price(s):
    s=(s or '').strip().replace('$','').replace(',','')
    return float(s) if s else None
def set_norm(s):
    s=s.lower()
    s=re.sub(r'^(pokemon|one[- ]piece|one piece card game)[- :]*','',s)
    s=re.sub(r'^(sv\d*|swsh\d*|sm|xy|me|bw|dp|hgss|ex|op\d*|st\d*|eb\d*|prb\d*)\s*[:-]?\s*','',s)
    s=re.sub(r'[^a-z0-9]+',' ',s).strip()
    s=re.sub(r'\b(the|of|and|&)\b','',s); s=re.sub(r'\s+',' ',s).strip()
    return s
def name_norm(n):
    n=n.lower().replace('&','and').replace('é','e')
    n=re.sub(r'\s*\[(.*?)\]',lambda m:' '+m.group(1),n)
    n=re.sub(r'[^a-z0-9]+',' ',n); n=re.sub(r'\b(the|of|and)\b','',n); n=re.sub(r'\s+',' ',n).strip()
    return n
rows=[]
# PriceCharting
for game,data in (('Pokemon',pk),('One Piece',op)):
    for p in data:
        n=p['name']
        if CARDCODE.search(n) or not SEALED_KW.search(bare(n)): continue
        sn=set_norm(p['set']); nn=name_norm(n)
        if sn and nn.startswith(sn+' '): nn=nn[len(sn)+1:]
        rows.append(dict(source='PriceCharting',game=game,set=p['set'],setn=sn,name=n,namen=nn,cat=classify(n),pc=price(p['price1']),
            url=f"https://www.pricecharting.com/game/{p['set']}/{p['uri']}"))
for p in tcg:
    game='One Piece' if p['line'].startswith('One') else 'Pokemon'
    n=p['name']
    sn=set_norm(p['set'] or ''); nn=name_norm(n)
    for tok in (sn, set_norm(re.sub(r'^[A-Z0-9]+:\s*','',p['set'] or ''))):
        if tok and nn.startswith(tok+' '): nn=nn[len(tok)+1:]
    nn=re.sub(r'^(scarlet and violet|sword and shield|sun and moon|mega evolution)\s+','',nn)
    rows.append(dict(source='TCGplayer',game=game,set=p['set'] or '',setn=sn,name=n,namen=nn,cat=classify(n),
        tm=p['market'],tmed=p['median'],tlow=p['lowest'],url=f"https://www.tcgplayer.com/product/{p['productId']}"))
print('candidate rows',len(rows),collections.Counter(r['source'] for r in rows))
print(collections.Counter((r['source'],r['cat']) for r in rows))
# merge across sources by (game, setn, namen)
merged=collections.OrderedDict()
for r in rows:
    key=(r['game'],r['setn'],r['namen'])
    m=merged.setdefault(key,dict(game=r['game'],name=bare(r['name']) if False else r['name'],cat=r['cat'],set=None,pc=None,pcurl=None,tm=None,tmed=None,tlow=None,tcgurl=None,sources=set()))
    m['sources'].add(r['source'])
    if r['source']=='PriceCharting':
        m['pc']=r['pc']; m['pcurl']=r['url']; m['pcset']=r['set']
    else:
        m['tm'],m['tmed'],m['tlow'],m['tcgurl'],m['tset']=r['tm'],r['tmed'],r['tlow'],r['url'],r['set']
    if r['source']=='TCGplayer': m['name']=r['name']; m['set']=r['set']
    elif m['set'] is None: m['set']=r['set']
out=[]; dropped=0
for m in merged.values():
    pts=[x for x in (m['pc'],m['tm'],m['tmed'],m['tlow']) if x]
    if not pts: dropped+=1; continue
    m['n']=len(pts); out.append(m)
print('merged',len(merged),'kept',len(out),'dropped(no price)',dropped,'both sources',sum(1 for m in out if len(m['sources'])==2))
print(collections.Counter(m['cat'] for m in out))
for m in [x for x in out if len(x['sources'])==2][:15]: print('  MATCH',m['game'],'|',m['pcset'],'|',m['tset'],'|',m['name'],m['pc'],m['tm'])
json.dump(out,open('merged.json','w'),default=list)
