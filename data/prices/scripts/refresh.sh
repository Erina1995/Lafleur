#!/usr/bin/env bash
# Re-collect PriceCharting + TCGplayer prices and rebuild the sheet files in data/prices/.
set -euo pipefail
cd "$(dirname "$0")"
python3 -c "import openpyxl" 2>/dev/null || pip install -q openpyxl
python3 pc_scrape.py pokemon &
python3 pc_scrape.py onepiece &
python3 tcg_scrape.py &
wait
python3 build.py
python3 make_xlsx.py
python3 inject_values.py pokemon-onepiece-sealed-prices.xlsx
mv -f pokemon-onepiece-sealed-prices.xlsx pokemon-onepiece-sealed-prices.csv ..
rm -f pc_pokemon_all.json pc_onepiece_all.json tcg_all.json merged.json
sed -i -E "s/collected on [0-9]{4}-[0-9]{2}-[0-9]{2}/collected on $(date -u +%F)/" ../README.md
echo "refresh done: $(date -u)"
