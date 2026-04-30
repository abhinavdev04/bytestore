# ByteStore - Project Summary

## Overview
ByteStore is an academic e-commerce application built with PHP and MySQL for a BCS 4th semester project. It provides a complete shopping workflow for customers and a staff-driven management interface for products, orders, customers, and employees.

## Technology Stack
- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS
- **Local Server**: XAMPP (Apache + MySQL)

## Implemented Features

### Customer Features
- Customer registration and login
- Product browsing and search
- Product detail view
- Add products to cart
- View and update cart
- Remove cart items
- Checkout and place orders

### Staff Features
- Staff login
- Staff dashboard with statistics
- Add new products
- Edit existing products
- Delete products
- Manage customer records
- Manage employee accounts
- View orders and order details

## Data Model and Relationships

- **Customers** can place **orders**.
- **Orders** are associated with **products** through order detail records.
- **Staff** users manage products, customers, employees, and orders from the staff dashboard.

## CRUD Coverage

### Products
- **Create**: Staff can add products
- **Read**: Customers and staff can view products
- **Update**: Staff can edit products
- **Delete**: Staff can delete products

### Customers
- **Create**: Customer registration page
- **Read**: Staff can view customer records
- **Update**: Not implemented in the current UI
- **Delete**: Staff can delete customer records

### Employees
- **Create**: Staff can add employee accounts
- **Read**: Staff can view employee records
- **Update**: Staff can edit employee accounts
- **Delete**: Staff can delete employee accounts

### Orders
- **Create**: Customers can place orders
- **Read**: Staff can view orders and order details
- **Update**: Order status updates are limited in the current interface
- **Delete**: Order deletion is not implemented

## Project Structure

```
bytestore/
├── admin/                # Legacy admin support files
│   └── logout.php
├── assets/
│   └── css/style.css
|   └──images
|   └──uploads
├── config/
│   └── config.php
├── customer/
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
├── employee/
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
├── includes/
│   ├── auth.php
│   ├── footer.php
│   ├── header.php
│   ├── logo.php
│   ├── product_form.php
│   └── product_handler.php
├── index.php
├── README.md
├── SETUP_GUIDE.md
└── PROJECT_SUMMARY.md
```

## Security Notes

- Passwords are hashed using PHP `password_hash()`.
- Login verification uses PHP `password_verify()`.
- Input values are sanitized with `mysqli_real_escape_string()`.
- This project is intended for academic use and should be hardened before production deployment.

## Sample Data Included

- 7 seeded customer accounts
- 5 seeded staff accounts (including one super admin)
- 24 seeded products
- 6 seeded orders

## Known Limitations

- No payment gateway integration
- No advanced role separation beyond staff access
- Customer profile editing is not implemented
- Order status management is limited
- UI could be improved for mobile responsiveness

## Recommended Enhancements

- Add file upload support for product images
- Add category filters and product sorting
- Add customer profile pages and order history
- Add a proper admin role and permissions hierarchy
- Add CSRF protection and prepared statements
- Enable HTTPS for production use

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

> All seeded account passwords are identical to their email addresses.

## Author

Abhinav Sapkota (002)
4th Semester
Advanced College of Engineering and Management

## Supervisor

Govinda Gautam

