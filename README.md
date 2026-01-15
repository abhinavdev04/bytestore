# ByteStore - Tech E-Commerce System

A simple and clean e-commerce website built with PHP, MySQL, HTML, CSS, and JavaScript for BCS 4th Semester Project.

## Features

### Customer Features
- User registration and login
- Browse and search products
- Add products to cart
- View shopping cart
- Place orders
- Simple checkout process


### Employee Features
- Employee login
- Dashboard
- Add new products
- Edit existing products
- Delete products
- View orders and order details

## Database Setup

1. Open phpMyAdmin in your browser (http://localhost/phpmyadmin)
2. Import the database file: `database/bytestore.sql`
3. The database will be created with all necessary tables and sample data

Seed data included:
- Products (5 items)
- Pending orders (2) with order items
- Cart items for the sample customers

## Default Login Credentials

### Admin
- Email: `Superadmin1@bytestore.com`
- Password: `Superadmin1@bytestore.com`

### Employee
- Email: `emp1@bytestore.com`
- Password: `emp123`

### Customers (sample data)
- Akraj: `Akraj@bytestore.com` / `123`
- Arpan: `Arpan@bytestore.com` / `123`

## Installation Steps

1. Make sure XAMPP is installed and running
2. Copy the `bytestore` folder to `C:\xampp\htdocs\`
3. Start Apache and MySQL from XAMPP Control Panel
4. Import the database from `database/bytestore.sql` using phpMyAdmin
5. Open your browser and go to: `http://localhost/bytestore/`

## Project Structure

```
bytestore/
├── customer/           # Customer pages
│   ├── register.php
│   ├── login.php
│   ├── shop.php
│   ├── cart.php
│   ├── checkout.php
│   └── add_to_cart.php
├── employee/          # Employee pages
│   ├── login.php
│   ├── dashboard.php
│   ├── add_products.php
│   ├── edit_products.php
│   ├── delete_products.php
│   ├── view_orders.php
│   └── order_details.php
├── assets/            # CSS, images, etc.
│   └── css/
│       └── style.css
├── config/            # Configuration files
│   └── config.php
├── database/          # Database SQL file
│   └── bytestore.sql
├── includes/          # Shared files
│   ├── header.php
│   ├── footer.php
│   └── auth.php
└── index.php          # Homepage
```

## Database Relations

- **customer** orders **product** (via orders and order_items tables)
- **employee** manages **product** (via employee_manages_product table)
- **admin** manages **employee** (via admin_manages_employee table)
- **admin** manages **customer** (via admin_manages_customer table)
- **admin** manages **product** (via admin_manages_product table)

## CRUD Operations

### Products
- **Create**: Admin and Employee can add products
- **Read**: All users can view products
- **Update**: Admin and Employee can edit products
- **Delete**: Admin and Employee can delete products

### Customers
- **Create**: Customers can register themselves
- **Read**: Admin can view all customers
- **Update**: (Not implemented - can be added)
- **Delete**: Admin can delete customers

### Employees
- **Create**: Admin can add employees
- **Read**: Admin can view all employees
- **Update**: (Not implemented - can be added)
- **Delete**: Admin can delete employees

### Orders
- **Create**: Customers can place orders
- **Read**: Admin and Employee can view orders
- **Update**: (Not implemented - can be added for status updates)
- **Delete**: (Not implemented - orders are typically not deleted)

## Notes

- Password hashing uses Password default for simplicity 
- Image paths are stored as text in the database (you can upload images to `assets/images/` folder)
- Payment processing is simplified (Cash on Delivery only)
- All code is kept simple and easy to understand for academic purposes

## Troubleshooting

1. **Database connection error**: Check if MySQL is running in XAMPP
2. **Page not found**: Make sure the folder is in `htdocs` directory
3. **Images not showing**: Check image paths in the database or add placeholder images
4. **Login not working**: Make sure the database is imported correctly

## Future Enhancements

- Implement proper password hashing (password_hash)
- Add image upload functionality
- Implement payment gateway integration
- Add order status management
- Add product categories
- Add customer profile management
- Add search and filter functionality
- Add responsive design improvements

## Author

Abhinav Sapkota (002)
4th Semester
Advanced College of Engineering and Management

## Supervisor

Govinda Gautam

