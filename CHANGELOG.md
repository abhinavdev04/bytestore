# Changelog

## QA Pass 3 (2026-05-30)

### Added
- Single master database: `database/bytestore_complete.sql` with full demo analytics data
- Support ticket system (customer + employee)
- Variant management UI (`employee/manage_variants.php`)
- `setup_check.php` installation verifier
- Professional favicon (`assets/images/favicon.svg`)
- Documentation: README, INSTALLATION, DATABASE_SCHEMA, PROJECT_REPORT

### Fixed
- Duplicate sale countdown on product page
- Account wishlist route (`account.php?page=wishlist`)
- Variant image fallback chain (variant → product → placeholder)
- Navbar notification dot replaced with numeric badge
- Employee edit products page redesign (design system)

### Changed
- Removed legacy SQL migration files (use `bytestore_complete.sql` only)
- Larger navbar logo; custom category dropdown scrollbar
- Account avatar centering

## QA Pass 2 (2026-05-30)

- Chart.js dashboard stability, discount system, GIF showcase links, category scroller, promo buttons, live search, custom modals.

## QA Pass 1 / Transformation

- Full UI redesign, dark mode, wishlist, compare, reviews, chatbot, employee analytics, verified reviews.

## Initial Version

- Core PHP/MySQL e-commerce: cart, checkout, eSewa, employee panel, product variants.
