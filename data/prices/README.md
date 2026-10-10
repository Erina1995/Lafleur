# Pokemon & One Piece sealed product prices

`pokemon-onepiece-sealed-prices.xlsx` (and the same main sheet as CSV) lists sealed
Pokemon and One Piece Card Game products (boxes, packs, coffrets) with prices from
PriceCharting and TCGplayer, collected on 2026-10-10. Import the .xlsx into Google Sheets
with **File > Import** (or upload it to Google Drive and open it with Sheets).

Sheets:

- **Prices** – Box / Pack / Coffret products. Columns: Name, Price, Median Price, Reduced
  (30 % off the median, driven by the `Settings!B2` discount cell), Kraken Price (median
  minus the Kraken discount in `Settings!B3`, 30 % by default), then Game, Category, Set,
  the raw price points from each site, and links.
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

## Automatic updates

`scripts/refresh.sh` runs every day (a scheduled Claude routine) and pushes the new
files to the branch `claude/pokemon-onepiece-price-sheet-m5i8xr`. To have a Google Sheet
that follows those updates on its own, put this formula in cell A1 of an empty sheet:

```
=IMPORTDATA("https://raw.githubusercontent.com/Erina1995/Lafleur/claude/pokemon-onepiece-price-sheet-m5i8xr/data/prices/pokemon-onepiece-sealed-prices.csv")
```

Google Sheets re-fetches the CSV roughly every hour, so the sheet stays current without
any manual import.
