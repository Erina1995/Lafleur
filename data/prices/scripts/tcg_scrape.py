import json, sys, time, urllib.request
lines={"pokemon":"Pokemon","pokemon-japan":"Pokemon Japan","one-piece-card-game":"One Piece Card Game"}
def post(body,tries=4):
    req=urllib.request.Request("https://mp-search-api.tcgplayer.com/v1/search/request?q=&isList=false",data=json.dumps(body).encode(),headers={"Content-Type":"application/json","User-Agent":"Mozilla/5.0"})
    for i in range(tries):
        try:
            with urllib.request.urlopen(req,timeout=40) as r: return json.load(r)
        except Exception as e:
            print("retry",e,file=sys.stderr); time.sleep(2*(i+1))
    return None
allp=[]
for ln,label in lines.items():
    frm=0
    while True:
        body={"algorithm":"sales_synonym_v2","from":frm,"size":50,"filters":{"term":{"productLineName":[ln],"productTypeName":["Sealed Products"]},"range":{},"match":{}},"listingSearch":{"context":{"cart":{}},"filters":{"term":{"sellerStatus":"Live","channelId":0},"range":{"quantity":{"gte":1}},"exclude":{"channelExclusion":0}}},"context":{"cart":{},"shippingCountry":"US"},"settings":{"useFuzzySearch":True,"didYouMean":{}},"sort":{}}
        d=post(body)
        if not d: break
        r=d["results"][0]; total=r.get("totalResults",0)
        for p in r["results"]:
            allp.append({"line":label,"productId":int(p["productId"]),"name":p["productName"],"set":p.get("setName"),"setUrl":p.get("setUrlName"),"productUrl":p.get("productUrlName"),"market":p.get("marketPrice"),"median":p.get("medianPrice"),"lowest":p.get("lowestPrice"),"lowestShip":p.get("lowestPriceWithShipping"),"listings":p.get("totalListings"),"release":(p.get("customAttributes") or {}).get("releaseDate")})
        print(label,frm,"/",total,file=sys.stderr,flush=True)
        frm+=50
        if frm>=total or not r["results"]: break
        time.sleep(0.4)
json.dump(allp,open("tcg_all.json","w"))
print("TOTAL",len(allp),file=sys.stderr)
