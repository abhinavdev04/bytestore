# ByteStore

ByteStore is a premium electronics e-commerce platform built for the Nepal market. It provides a full shopping experience for customers and a powerful management panel for staff, including analytics, inventory, discounts, variants, and customer support.

## Features

- User authentication (customers and employees)
- Product catalog with categories and brands
- Product variants (color, storage, RAM, etc.)
- Wishlist and product comparison
- Verified purchase reviews and ratings
- Shopping cart and checkout
- eSewa payment integration
- Customer account dashboard
- Employee admin dashboard with Chart.js analytics
- Discount and sale management with countdown timers
- Support ticket system
- AI-style shopping chatbot
- Dark mode / light mode
- Live search (products, categories, brands)

## Technology Stack

- PHP 8+
- MySQL / MariaDB
- HTML5, CSS3, JavaScript
- Chart.js (admin analytics)
- Font Awesome 6

## Installation Guide

1. Install [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP).
2. Copy this project folder to `C:\xampp\htdocs\bytestore` (or your web root).
3. Import the database (single file):
   ```bash
   mysql -u root < database/bytestore_complete.sql
   ```
   On Windows PowerShell:
   ```powershell
   Get-Content database\bytestore_complete.sql | mysql -u root
   ```
4. Configure database connection in `config/config.php` if needed (default: `localhost`, user `root`, database `bytestore`).
5. Start **Apache** and **MySQL** from the XAMPP Control Panel.
6. Open `http://localhost/bytestore/setup_check.php` to verify installation.
7. Visit `http://localhost/bytestore`

## Project Structure

| Path | Description |
|------|-------------|
| `index.php` | Homepage, hero, categories, featured products |
| `customer/` | Shop, product detail, cart, checkout, account |
| `employee/` | Admin dashboard, products, orders, discounts, support |
| `includes/` | Header, footer, helpers, product card, modals |
| `api/` | Wishlist, compare, live search JSON endpoints |
| `assets/css/` | Design system and QA styles |
| `assets/js/` | Theme, search, chatbot, charts, modals |
| `pages/` | About, contact, FAQ, policies |
| `database/` | `bytestore_complete.sql` (full install) |
| `config/` | Database configuration |

## Demo Accounts

After importing `database/bytestore_complete.sql`:

| Role | Email | Password |
|------|-------|----------|
| Customer | `Akraj2024@bytestore.com` | `Password@123` |
| Customer (alt) | `customer1@bytestore.com` | `Password@123` |
| Staff / Admin | `Superadmin1@bytestore.com` | `Password@123` |

## Screenshots

_Add homepage, shop, product page, cart, checkout, employee dashboard, and support ticket screenshots here._

## Future Improvements

- Payment gateway expansion (Khalti, Fonepay)
- Email notifications for orders and support
- Advanced inventory and warehouse modules
- Product import/export (CSV)
- PWA and mobile app API

## License

Academic / educational project — see your institution's guidelines for use and distribution.
