"""Insert cached <v> values for the formula cells written by openpyxl so previewers show numbers."""
import zipfile,re,shutil,sys,json
from statistics import median
src=sys.argv[1]; tmp=src+'.tmp'
zin=zipfile.ZipFile(src); zout=zipfile.ZipFile(tmp,'w',zipfile.ZIP_DEFLATED)
# map sheet name -> xml path
wbxml=zin.read('xl/workbook.xml').decode(); rels=zin.read('xl/_rels/workbook.xml.rels').decode()
rid2t={m.group(1):m.group(2) for m in re.finditer(r'<Relationship[^>]*Id="([^"]+)"[^>]*Target="([^"]+)"',rels)}
rid2t.update({m.group(2):m.group(1) for m in re.finditer(r'<Relationship[^>]*Target="([^"]+)"[^>]*Id="([^"]+)"',rels)})
name2path={}
for m in re.finditer(r'<sheet [^>]*name="([^"]+)"[^>]*r:id="([^"]+)"',wbxml):
    t=rid2t[m.group(2)]; name2path[m.group(1)]='xl/'+t.lstrip('/').replace('xl/','')
disc=0.30
def numcell(xml,ref):
    m=re.search(r'<c r="%s"[^>]*>(?:<f>.*?</f>)?<v>([^<]*)</v>'%ref,xml)
    return float(m.group(1)) if m else None
total=0
for item in zin.infolist():
    data=zin.read(item.filename)
    sheet=[n for n,p in name2path.items() if p==item.filename]
    if sheet and sheet[0] in ('Prices','Other Sealed'):
        xml=data.decode()
        rows=re.findall(r'<row r="(\d+)"[^>]*>(.*?)</row>',xml,re.S)
        for rnum,rxml in rows:
            r=int(rnum)
            if r==1: continue
            vals={}
            for col in 'HIJK':
                m=re.search(r'<c r="%s%d"[^>]*><v>([^<]*)</v>'%(col,r),rxml)
                if m: vals[col]=float(m.group(1))
            pts=[vals[c] for c in 'HIJK' if c in vals]
            if not pts: continue
            price=next(vals[c] for c in 'HIJK' if c in vals)
            med=median(pts); red=round(med*(1-disc)+1e-9,2)
            new=rxml
            for col,v in (('B',price),('C',med),('D',red)):
                new,n=re.subn(r'(<c r="%s%d"[^>]*>)(<f>.*?</f>)<v ?/>'%(col,r),lambda m:f'{m.group(1)}{m.group(2)}<v>{v!r}</v>',new,count=1)
                total+=n
            if new!=rxml: xml=xml.replace(rxml,new,1)
        data=xml.encode()
    zout.writestr(item,data)
zin.close(); zout.close(); shutil.move(tmp,src); print('cached values written:',total)
