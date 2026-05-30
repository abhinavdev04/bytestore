# ByteStore — Project Report

## Introduction

ByteStore is a web-based electronics e-commerce system developed as a BCA-level project. It simulates a commercial online store in Nepal, offering laptops, smartphones, accessories, and related products with modern UX patterns including dark mode, live search, and admin analytics.

## Objectives

- Provide a secure online shopping platform for customers
- Enable staff to manage products, orders, inventory, discounts, and support
- Demonstrate full-stack skills using PHP, MySQL, and front-end technologies
- Deliver realistic demo data for presentations and testing

## Scope

**In scope:** Product catalog, variants, cart, checkout, eSewa hook, customer account, employee panel, analytics, reviews, wishlist, compare, support tickets, chatbot.

**Out of scope:** Native mobile apps, multi-vendor marketplace, automated shipping APIs.

## Features

See `README.md` for the complete feature list.

## Technologies Used

- **Backend:** PHP 8, MySQL
- **Frontend:** HTML5, CSS3 (custom design system), JavaScript
- **Libraries:** Chart.js, Font Awesome 6
- **Server:** Apache (XAMPP)

## Database Design

Normalized relational schema with 18+ tables. Single install file `database/bytestore_complete.sql` includes schema and seed data. See `docs/DATABASE_SCHEMA.md`.

## System Architecture

```
Browser → Apache/PHP pages → MySQL
         ↘ JSON APIs (search, wishlist, compare)
```

Shared includes (`header`, `footer`, `functions.php`) provide authentication, pricing, and image resolution. Employee and customer areas share the same design system.

## Screenshots

_Insert screenshots of homepage, shop, product detail, cart, employee dashboard, and support tickets._

## Challenges

- Chart.js canvas resize loops — fixed with fixed-height containers and single init
- Variant image paths pointing to missing files — resolved with fallback logic
- Consolidating multiple SQL files into one reproducible database build script

## Future Enhancements

- Email/SMS notifications
- Khalti and other payment gateways
- REST API for mobile clients
- Automated product CSV import

## Conclusion

ByteStore demonstrates a production-style electronics store with comprehensive customer and admin functionality, suitable for academic evaluation and portfolio demonstration.
