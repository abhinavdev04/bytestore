# ByteStore QA Report — Pass 2

**Date:** May 30, 2026  
**Scope:** Second QA pass — 10 fixes + polish + master database

---

## Summary

All 10 requested items were implemented. The storefront now has consistent navbar styling, improved GIF showcase with product links, enhanced category scrolling, stable Chart.js dashboards, readable promo buttons, a full discount management system, and a single master SQL install file.

---

## Bugs Fixed

| # | Issue | Fix |
|---|-------|-----|
| 1 | Categories button wrong color | `button.navbar__link` / `.navbar__dropdown-trigger` use theme variables; chevron icon; `aria-expanded` sync |
| 2 | GIF showcase not linked / wrong sort | GIFs first, WEBP second; product name matching; entire slide is `<a>` with hover effects |
| 3 | Category scroller limited | Visible scrollbar, wheel horizontal scroll, fade indicators, touch-friendly |
| 4 | Charts grow infinitely | Fixed `.chart-container` height; `maintainAspectRatio: false` in wrapper; single init via `dashboard-charts.js` |
| 5 | Promo buttons white-on-white | New `.btn--promo` with contrast on gradient cards |
| 7 | Variant text black in dark mode | `color: var(--color-text)` on `.variant-option`; surface background |
| 8 | Variant image → placeholder | Always pass fallback `data-image`; no auto-click clearing image; default product image used |

---

## New Features

| # | Feature |
|---|---------|
| 9 | **Discount management system** — DB fields, `getProductPricing()`, sale countdown, admin bulk discounts (`manage_discounts.php`), per-product sale fields in `edit_products.php` |
| 6 | **`database/bytestore_complete.sql`** — Full mysqldump for one-file fresh install |

---

## Files Modified

### CSS / JS
- `assets/css/style.css` — Navbar button, variants, promo buttons, category scroll base
- `assets/css/qa-fixes.css` — Charts, GIF showcase, categories fade, pricing UI
- `assets/js/dashboard-charts.js` — **New** stable Chart.js init
- `assets/js/categories-scroll.js` — **New** wheel + fade
- `assets/js/sale-countdown.js` — **New** sale timers
- `assets/js/main.js` — Dropdown `aria-expanded`

### PHP
- `includes/functions.php` — Pricing, sale expiry, GIF sort/link, product matching
- `includes/header.php` — Categories button, `expireProductSales()`
- `includes/footer.php` — New scripts
- `includes/product_card.php` — Unified pricing display
- `index.php` — GIF links, category wrap, promo buttons, FA scroll icons
- `customer/product.php` — Pricing, variant images, countdown
- `customer/cart.php`, `checkout.php`, `compare.php` — Sale pricing
- `employee/dashboard.php` — Chart containers + external JS
- `employee/edit_products.php` — Sale fields in forms
- `employee/manage_discounts.php` — **New** bulk + list discounts

### Database
- `database/discount_fields.sql` — **New** migration + demo sales
- `database/bytestore_complete.sql` — **New** master install (~99KB)

---

## Database Changes

New `product` columns:
- `discount_percent` DECIMAL(5,2)
- `sale_start_date` DATETIME
- `sale_end_date` DATETIME
- `is_sale_active` TINYINT(1)

**Fresh install:**
```powershell
Get-Content database\bytestore_complete.sql | mysql -u root
```

**Existing install:**
```powershell
Get-Content database\discount_fields.sql | mysql -u root bytestore
```

---

## Remaining Recommendations

| Priority | Item |
|----------|------|
| Medium | Apply sale discount % to variant prices automatically |
| Medium | Admin UI for uploading `product_image` gallery rows |
| Low | Persist order line item sale price at checkout time |
| Low | Email alerts when sales expire |

---

## Manual Test Checklist

- [ ] Categories dropdown matches Shop link styling (light + dark)
- [ ] GIF showcase: GIFs before WEBP; click opens product page
- [ ] Category row: scrollbar visible; mouse wheel scrolls horizontally
- [ ] Dashboard: charts stay fixed height after 5+ minutes
- [ ] Promo cards: buttons readable in light mode
- [ ] Product variants: readable in dark mode; image does not break on switch
- [ ] Active sale shows strikethrough, % OFF, countdown on product page
- [ ] `manage_discounts.php` bulk apply works
- [ ] Import `bytestore_complete.sql` on clean MySQL
