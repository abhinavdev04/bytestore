# ByteStore - PHP E-Commerce Application

ByteStore is a local e-commerce web application built with PHP, MySQL, HTML, and CSS for a BCS 4th semester project. It supports customer shopping, shopping cart checkout, and staff management of products, customers, and orders.

## Key Features

### Customer Features
- Customer registration and login
- Browse products with search support
- View product details
- Add products to cart
- Update item quantity and remove cart items
- Checkout and place orders

### Staff Features
- Staff login and dashboard
- Add new products
- Edit existing products
- Delete products
- Manage customer records
- Manage employee accounts
- View orders and order details
- Dashboard statistics for products, customers, employees, and orders

## Setup Instructions

1. Copy the `bytestore` folder into `C:\xampp\htdocs\`
2. Start Apache and MySQL in the XAMPP Control Panel
3. Open phpMyAdmin at `http://localhost/phpmyadmin`
4. Import `bytestore/database/bytestore.sql`
5. Open the application at `http://localhost/bytestore/`

## Default Accounts

### Staff
- `Superadmin1@bytestore.com` / `Superadmin1@bytestore.com`
- `Employee1@bytestore.com` / `Employee1@bytestore.com`
- `Pratima1@bytestore.com` / `Pratima1@bytestore.com`
- `Sunita1@bytestore.com` / `Sunita1@bytestore.com`
- `Arjun1@bytestore.com` / `Arjun1@bytestore.com`

### Customers
- `Akraj2024@bytestore.com` / `Akraj2024@bytestore.com`
- `Arpan1@bytestore.com` / `Arpan1@bytestore.com`
- `Pramisha1@bytestore.com` / `Pramisha1@bytestore.com`
- `Sujit1@bytestore.com` / `Sujit1@bytestore.com`
- `Sajal1@bytestore.com` / `Sajal1@bytestore.com`
- `Rohan1@bytestore.com` / `Rohan1@bytestore.com`
- `Customer1@bytestore.com` / `Customer1@bytestore.com`

> For all seeded accounts, the password is the same as the email address.

## Sample Data Included

- 7 seeded customers
- 5 seeded staff accounts (including a super admin)
- 24 seeded products
- 6 sample orders

## Project Structure

```
bytestore/
├── admin/                # Legacy admin support files
│   └── logout.php
├── assets/               # CSS and static assets
│   └── css/
│       └── style.css
├── config/
│   └── config.php
├── customer/             # Customer-facing pages
│   ├── add_to_cart.php
│   ├── cart.php
│   ├── checkout.php
│   ├── login.php
│   ├── logout.php
│   ├── product.php
│   ├── register.php
│   └── shop.php
├── database/
│   └── bytestore.sql
├── employee/             # Staff pages and management tools
│   ├── add_products.php
│   ├── dashboard.php
│   ├── delete_products.php
│   ├── edit_products.php
│   ├── login.php
│   ├── logout.php
│   ├── manage_customers.php
│   ├── manage_employees.php
│   ├── order_details.php
│   └── view_orders.php
├── includes/             # Shared includes
│   ├── auth.php
│   ├── footer.php
│   ├── header.php
│   ├── logo.php
│   ├── product_form.php
│   └── product_handler.php
├── index.php
├── README.md
├── PROJECT_SUMMARY.md
└── SETUP_GUIDE.md
```

## Security Notes

- Passwords are hashed using PHP `password_hash()` and verified with `password_verify()`
- Input values are escaped with `mysqli_real_escape_string()` to reduce SQL injection risk
- The app is designed for academic use and should be hardened before production

## Future Improvements

- Add image upload support
- Add payment gateway integration
- Improve responsive layout
- Add customer profile settings
- Add order status management and notifications
- Add filters and category navigation

## Author

Abhinav Sapkota (002)
4th Semester
Advanced College of Engineering and Management

## Supervisor

Govinda Gautam

