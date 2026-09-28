import json, sys, time, urllib.request, urllib.error
from concurrent.futures import ThreadPoolExecutor
UA={"User-Agent":"Mozilla/5.0 (X11; Linux x86_64) Chrome/120"}
def get(url, tries=4):
    for i in range(tries):
        try:
            with urllib.request.urlopen(urllib.request.Request(url,headers=UA),timeout=40) as r:
                return json.load(r)
        except Exception as e:
            time.sleep(2*(i+1))
    print("FAIL",url,file=sys.stderr); return None
def scrape(slug):
    out=[]; cursor="0"
    while True:
        d=get(f"https://www.pricecharting.com/console/{slug}?sort=name&cursor={cursor}&format=json")
        if not d or not d.get("products"): break
        for p in d["products"]:
            out.append({"set":slug,"name":p["productName"],"uri":p["productUri"],"id":p["id"],"price1":p["price1"],"price2":p["price2"],"price3":p["price3"]})
        cursor=d.get("cursor")
        if not cursor: break
        time.sleep(0.2)
    print(slug,len(out),file=sys.stderr,flush=True)
    return out
game=sys.argv[1]
slugs=[l.strip() for l in open(f"slugs_{game}.txt") if l.strip()]
with ThreadPoolExecutor(6) as ex:
    res=list(ex.map(scrape,slugs))
allp=[p for r in res for p in r]
json.dump(allp,open(f"pc_{game}_all.json","w"))
print("TOTAL",game,len(allp),file=sys.stderr)
