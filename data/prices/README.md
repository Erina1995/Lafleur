# Pokemon & One Piece sealed product prices

`pokemon-onepiece-sealed-prices.xlsx` (and the same main sheet as CSV) lists sealed
Pokemon and One Piece Card Game products (boxes, packs, coffrets) with prices from
PriceCharting and TCGplayer, collected on 2026-09-28. Import the .xlsx into Google Sheets
with **File > Import** (or upload it to Google Drive and open it with Sheets).

Sheets:

- **Prices** – Box / Pack / Coffret products. Columns: Name, Price, Median Price, Reduced
  (30 % off the median, driven by the `Settings!B2` discount cell), then Game, Category,
  Set, the raw price points from each site, and links.
- **Other Sealed** – decks, cases/displays and accessories, same layout.
- **Settings** – discount percentage and notes on how prices are computed.

Refreshing the data (run from `scripts/`):

```bash
python3 pc_scrape.py pokemon      # PriceCharting, all Pokemon sets  -> pc_pokemon_all.json
python3 pc_scrape.py onepiece     # PriceCharting, all One Piece sets -> pc_onepiece_all.json
python3 tcg_scrape.py             # TCGplayer sealed products        -> tcg_all.json
python3 build.py                  # classify + merge the two sources -> merged.json
python3 make_xlsx.py              # write the .xlsx and .csv
python3 inject_values.py pokemon-onepiece-sealed-prices.xlsx   # cache formula values
```
