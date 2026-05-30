# ByteStore Database Schema

Database name: `bytestore`  
Install file: `database/bytestore_complete.sql`

## Entity Relationship Overview

- **customer** places **orders** containing **order_items** (products / variants).
- **product** belongs to **category**; has **product_variant**, **product_image**, **product_spec**, **product_review**.
- **customer** has **wishlist**, **cart**, **customer_address**, **customer_notification**, **support_ticket**.
- **employee** manages catalog and support (no separate admin table; employee ID 1 is super admin).

## Tables

### customer
Stores registered shoppers. Passwords are hashed with `password_hash()`. Optional `profile_photo` path.

### employee
Staff accounts for the employee panel (dashboard, products, orders, support).

### category
Product categories (Laptops, Phones, Gaming, etc.).

### product
Core catalog: price, stock, brand, featured/trending flags, ratings, discount fields (`discount_percent`, `sale_start_date`, `sale_end_date`, `is_sale_active`), `original_price` for MSRP display.

### product_variant
SKU-level options per product: name, type (Color, Storage, RAM), price, stock, optional `variant_image_path`.

### product_image
Additional gallery images per product (`sort_order`).

### product_spec
Key/value specification rows for product detail pages.

### orders / order_items
Customer orders with status, payment, shipping. Line items reference `product_id` and optional `variant_id`.

### cart
Session-persistent cart per customer with optional `variant_id`.

### wishlist
Saved products per customer (unique customer + product).

### product_review
Ratings and text reviews; `is_verified` for purchase-verified reviews; unique per customer + product.

### recently_viewed
Tracks last viewed products per customer.

### customer_address
Multiple shipping addresses per customer.

### customer_notification
In-app notifications (e.g. wishlist back-in-stock alerts).

### newsletter
Email newsletter subscribers.

### support_ticket / support_reply
Customer support messages with statuses: Open, Pending, Resolved, Closed. Replies from customer or employee.

## Foreign Keys

- `product.category_id` → `category.category_id`
- `product_variant.product_id` → `product.product_id` (CASCADE)
- `orders.customer_id` → `customer.customer_id` (CASCADE)
- `order_items` → `orders`, `product`, `product_variant`
- `wishlist`, `cart`, `reviews`, `notifications` → `customer` and `product` as applicable

## Demo Data Summary

| Table | Approx. rows |
|-------|----------------|
| product | 120 |
| customer | 121 |
| orders | 550 |
| product_review | 350 |
| wishlist | 320 |
| customer_notification | 120 |
| support_ticket | 40 |
