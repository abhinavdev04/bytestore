# ByteStore — Final QA Report (Pass 3)

**Date:** May 30, 2026  
**Verification:** Automated tests (`tests/verify_qa_pass3.php`) — **22/22 passed** after importing `database/bytestore_complete.sql`

---

## Summary

QA Pass 3 addressed 14 requirement areas plus full documentation and a single master database. All items were implemented and verified with automated checks against the imported database.

---

## Bugs Fixed

| Item | Fix |
|------|-----|
| Duplicate sale countdown | Removed countdown from `renderProductPricingHtml()`; single call on product page; hidden when sale expired |
| Wishlist account route | Added `account.php?page=wishlist` handler |
| Variant placeholder images | `variantImageUrl()` + `resolveProductImagePath()` fallback chain |
| Navbar stray dot | Replaced `navbar__notif-dot` with standard `navbar__badge` count |
| Chart height growth | Fixed containers + `dashboard-charts.js` single init |
| Edit products UI | Redesigned with `employee-panel.css` and theme variables |

---

## New Features

| Feature | Location |
|---------|----------|
| Support tickets | `customer/support.php`, `employee/support_tickets.php`, DB tables |
| Variant management | `employee/manage_variants.php` (CRUD + image upload) |
| Master database | `database/bytestore_complete.sql` (~238 KB) |
| Setup checker | `setup_check.php` |
| Favicon | `assets/images/favicon.svg` |
| Documentation package | README, INSTALLATION, CHANGELOG, PROJECT_REPORT, DATABASE_SCHEMA |

---

## Database Changes

- **Install file:** `database/bytestore_complete.sql` only (legacy SQL files removed)
- **New tables:** `support_ticket`, `support_reply`
- **Demo scale:** 120 products, 121 customers, 550 orders, 350 reviews, 320 wishlist, 120 notifications, 40 support tickets
- **Regenerate:** `php database/build_complete_database.php` (dev tool; uses `schema_base.sql`)

**Import command:**
```powershell
Get-Content database\bytestore_complete.sql | mysql -u root
```

**Demo login:** `Password@123` (see README)

---

## Files Modified (Key)

| Area | Files |
|------|-------|
| Core | `includes/functions.php`, `includes/header.php`, `includes/account_sidebar.php`, `customer/account.php`, `customer/product.php` |
| Employee | `employee/edit_products.php`, `employee/dashboard.php`, `employee/manage_variants.php` (new), `employee/support_tickets.php` (new) |
| Customer | `customer/support.php` (new) |
| Assets | `assets/css/style.css`, `assets/css/qa-fixes.css`, `assets/css/employee-panel.css` (new), `assets/js/sale-countdown.js`, `assets/images/favicon.svg` (new) |
| Database | `database/bytestore_complete.sql`, `database/build_complete_database.php`, `database/schema_base.sql` |
| Docs | `README.md`, `INSTALLATION.md`, `CHANGELOG.md`, `PROJECT_REPORT.md`, `docs/DATABASE_SCHEMA.md` |
| Tests | `tests/verify_qa_pass3.php`, `setup_check.php` |

---

## Verified Test Results

```
[PASS] Pricing HTML has no embedded countdown
[PASS] Sale countdown renders once
[PASS] Expired sale countdown empty
[PASS] Variant image falls back to product
[PASS] Table product/customer/orders/reviews/wishlist/notifications/support_ticket counts
[PASS] account.php wishlist handler
[PASS] product.php single renderSaleCountdown call
```

---

## Remaining Recommendations

| Priority | Item |
|----------|------|
| Low | Add PHPUnit/browser tests for checkout and eSewa flow |
| Low | Email notifications for support ticket replies |
| Low | Bulk product CSV import in admin |
| Low | Screenshot assets for README and project report |

---

## Manual QA Checklist

- [ ] Product on sale shows **one** countdown; disappears after end date
- [ ] `account.php?page=wishlist` loads product grid
- [ ] Variant with no image shows product image (e.g. product ID 2)
- [ ] `employee/manage_variants.php?product_id=2` — add/edit/delete variant
- [ ] `employee/support_tickets.php` — filter and reply
- [ ] `setup_check.php` — all green after fresh import
- [ ] Dark/light mode on edit products and shop pages
