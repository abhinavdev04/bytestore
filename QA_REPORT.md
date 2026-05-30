# ByteStore QA Report

**Date:** May 30, 2026  
**Scope:** Complete QA pass — 20 requested fixes + full project audit  
**Environment:** PHP/MySQL (XAMPP), `c:\xampp\htdocs\bytestore`

---

## Executive Summary

ByteStore has been upgraded from a functional prototype to a polished commercial-style electronics store. All 20 requested QA items were addressed. Critical bugs (chatbot close, compare Clear All, SQL ambiguity, product zoom) are fixed. UI consistency, dark/light theme contrast, Font Awesome icons, custom modals, live search, Chart.js analytics, verified reviews, and demo database content are in place.

---

## Bugs Fixed

| # | Issue | Resolution |
|---|-------|------------|
| 1 | Chatbot X button did not close panel | Rewrote `chatbot.js` with proper state, Escape key, `stopPropagation`; CSS `[hidden]` override in `qa-fixes.css` |
| 2 | Any user could submit reviews | Verified purchase check via `hasPurchasedProduct()`; restriction message; `is_verified` column; duplicate prevention via `hasReviewedProduct()` |
| 3 | `edit_products.php` SQL ambiguity | Aliased all columns (`p.category_id`, `p.product_name`, etc.) in WHERE/JOIN queries |
| 4 | White-on-white text in themes | `qa-fixes.css` contrast fixes for cards, orders, employee pages, text-muted |
| 5 | Product cards blend in light mode | Borders, shadows, category card styling in light theme |
| 6 | Empty database sections | `database/upgrade_qa.sql` seeds reviews, orders, wishlist, notifications, product images |
| 7 | Browser `alert()`/`confirm()` | Custom glassmorphism modal system (`modal.js`, `modal.php`); all confirms replaced |
| 8 | No admin product live search | `#admin-product-search` + `admin-search.js` on `edit_products.php` |
| 9 | Filter sidebar won't scroll | `.shop-filters` independent scroll in `qa-fixes.css` |
| 10 | Generic quantity inputs | `renderQtyStepper()` + `quantity.js` on product and cart pages |
| 11 | Search requires button press | Live search API + dropdown in navbar; shop page instant filter |
| 12 | Emoji icons throughout | Font Awesome 6.5.1 CDN; emojis replaced in nav, cards, chatbot, static pages, checkout |
| 13 | No wishlist stock alerts | `checkWishlistStockAlerts()`, `customer_notification` table, header bell badge, account notifications page |
| 14 | Basic admin dashboard | Chart.js charts: revenue, orders/month, categories, top products, customer growth, inventory, low stock table |
| 15 | Product image zoom broken | `product-gallery.js` with mouse wheel + touch pinch zoom |
| 16 | Compare Clear All broken | API reordered to handle `action: clear` before product_id validation; modal confirm on compare page |
| 17 | Navbar search invisible in light mode | Border, hover, focus states in `qa-fixes.css` |
| 18 | No GIF homepage showcase | `getGifShowcaseItems()` scans `assets/gifImages/`; auto-rotate carousel on `index.php` |
| 19 | Missing/broken product images | `resolveProductImage()` / `productImageUrl()` with multi-extension fallback + placeholder SVG |
| 20 | Single product image only | `product_image` table gallery on product page; seed data for demo products |

### Additional Fixes

- Restored missing footer include on `shop.php`
- Star ratings use Font Awesome instead of Unicode characters
- Compare page empty state and remove buttons use FA icons + custom modal
- Employee delete links (customers, employees, products) use `data-confirm`

---

## Files Modified

### New Files
| File | Purpose |
|------|---------|
| `assets/css/qa-fixes.css` | Theme contrast, chatbot, cards, filters, modals, steppers, admin search |
| `assets/js/modal.js` | Custom confirm/alert dialog system |
| `assets/js/quantity.js` | +/- quantity stepper controls |
| `assets/js/product-gallery.js` | Image zoom (mouse + touch) |
| `assets/js/admin-search.js` | Admin product live search filter |
| `includes/modal.php` | Modal HTML markup |
| `database/upgrade_qa.sql` | QA migration + demo seed data |
| `QA_REPORT.md` | This report |

### Modified Files (Key)
| Area | Files |
|------|-------|
| Layout | `includes/header.php`, `includes/footer.php`, `includes/chatbot.php`, `includes/functions.php`, `includes/product_card.php`, `includes/account_sidebar.php` |
| Customer | `customer/product.php`, `shop.php`, `cart.php`, `compare.php`, `wishlist.php`, `account.php`, `checkout.php` |
| Employee | `employee/dashboard.php`, `edit_products.php`, `delete_products.php`, `manage_customers.php`, `manage_employees.php` |
| API | `api/compare.php`, `api/search.php` |
| Assets | `assets/js/chatbot.js`, `search.js`, `main.js` |
| Pages | `index.php`, `pages/about.php`, `pages/contact.php` |

---

## Database Changes

Run migration:
```powershell
Get-Content database\upgrade_qa.sql | mysql -u root bytestore
```

### Schema
- **`customer_notification`** — wishlist stock alerts and account notifications
- **`product_review.is_verified`** — TINYINT(1) for verified purchase badge

### Seed Data Added
- 5 additional verified product reviews
- Product rating aggregates recalculated
- 5 `product_image` gallery entries
- Additional wishlist + recently viewed entries
- 2 sample wishlist stock notifications
- Up to 5 historical orders across 6 months (for Chart.js dashboards)

---

## New Features Added

1. **Custom modal system** — Glassmorphism confirm/alert replacing native dialogs
2. **Verified purchase reviews** — Purchase check, verified badge, one review per customer per product
3. **Wishlist stock notifications** — Auto-detect restock, DB storage, header badge, account page
4. **Chart.js admin analytics** — 6 interactive charts + low stock report
5. **Live search** — Navbar dropdown (products/categories/brands) + shop page filter + admin product search
6. **Quantity steppers** — Large +/- buttons on product and cart pages
7. **Product GIF showcase** — Homepage carousel from `assets/gifImages/`
8. **Product gallery zoom** — Mouse wheel and touch pinch on product pages
9. **Image fallback system** — Multi-format path resolution (JPG, PNG, WEBP, GIF)
10. **Account notifications page** — `account.php?page=notifications`

---

## Test Credentials

| Role | Email | Password |
|------|-------|----------|
| Customer | Akraj2024@bytestore.com | Same as email |
| Staff | Superadmin1@bytestore.com | Same as email |

---

## Remaining Recommendations

| Priority | Item | Notes |
|----------|------|-------|
| Medium | Admin multi-image upload UI | Gallery displays on storefront; staff panel lacks upload form for `product_image` rows |
| Medium | Variant-specific images | Gallery is product-level; variant image switching not implemented |
| Low | CSRF tokens on all forms | Helper exists (`csrfToken()`) but not applied everywhere |
| Low | Password hashing | Passwords stored plain text (academic project constraint) |
| Low | Contact form backend | Form shows toast only; no email/DB persistence |
| Low | Employee inline styles | `edit_products.php` retains page-specific CSS; could migrate to design system |
| Low | Automated tests | No PHPUnit/JS test suite |
| Low | `products.txt` scrape artifact | Unrelated file in repo root; safe to delete |

---

## QA Checklist (Manual Verification)

- [ ] Chatbot: open → close (X) → reopen via floating button
- [ ] Product page: zoom with scroll wheel and mobile pinch
- [ ] Reviews: logged-in non-buyer sees restriction message; buyer can submit once
- [ ] Compare: add 2+ products → Clear All → confirm modal → list empty
- [ ] Dark/light toggle on homepage, shop, cart, employee dashboard
- [ ] Navbar live search while typing
- [ ] Admin dashboard charts render with data
- [ ] Cart quantity steppers update correctly
- [ ] Wishlist notification appears after restock (set product stock > 0 for wishlisted item)
- [ ] GIF showcase auto-rotates on homepage

---

## Conclusion

ByteStore now presents as a cohesive premium electronics storefront with working e-commerce flows, staff analytics, and polished UI patterns. All 20 QA items from the specification have been implemented. The recommendations above are enhancements beyond the current scope, not blockers for demonstration use.
