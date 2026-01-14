# ByteStore Setup Guide

## Quick Start

### Step 1: Start XAMPP
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** services

### Step 2: Import Database
1. Open your browser
2. Go to: `http://localhost/phpmyadmin`
3. Click on "New" to create a database (or it will be created automatically)
4. Click on "Import" tab
5. Click "Choose File" and select: `bytestore/database/bytestore.sql`
6. Click "Go" to import (this loads sample products, carts, and pending orders)

### Step 3: Access the Website
1. Open your browser
2. Go to: `http://localhost/bytestore/`
3. You should see the homepage!

## Testing the System

### Test as Customer:
1. Click "Register" to create a new account
2. Or use one of the seeded accounts:
   - `Akraj@bytestore.com` / `123`
   - `Arpan@bytestore.com` / `123`
3. Browse products, add to cart, and place an order



### Test as Employee:
1. Go to: `http://localhost/bytestore/employee/login.php`
2. Login with:
   - Email: `emp1@bytestore.com`
   - Password: `emp123`
3. You can add, edit, and delete products, and view orders

## Adding Product Images

1. Place your product images in: `bytestore/assets/images/`
2. When adding a product, use the path: `assets/images/your-image.jpg`
3. Example: If you have `laptop.jpg` in the images folder, use: `assets/images/laptop.jpg`

## Common Issues

### Issue: "Database connection failed"
**Solution**: Make sure MySQL is running in XAMPP Control Panel

### Issue: "Page not found" or blank page
**Solution**: 
- Make sure the folder is in `C:\xampp\htdocs\bytestore\`
- Check that Apache is running

### Issue: Images not showing
**Solution**: 
- Check the image path in the database
- Make sure the image file exists in the specified location
- Use relative paths like `assets/images/product.jpg`

### Issue: Can't login
**Solution**: 
- Make sure the database is imported correctly
- Check that the default admin/employee accounts exist
- Try registering a new customer account

## Database Structure

The system uses the following main tables:
- `customer` - Customer accounts
- `admin` - Admin accounts
- `employee` - Employee accounts
- `product` - Product catalog
- `cart` - Shopping cart items
- `orders` - Customer orders
- `order_items` - Items in each order
- `admin_manages_product` - Tracks admin product actions
- `admin_manages_customer` - Tracks admin customer actions
- `admin_manages_employee` - Tracks admin employee actions
- `employee_manages_product` - Tracks employee product actions

## Code Explanation

All code is kept simple and straightforward:
- **PHP**: Used for server-side logic, database operations, and session management
- **MySQL**: Stores all data in relational tables
- **HTML/CSS**: Creates the user interface
- **JavaScript**: Minimal use (can be added for better interactivity)

The code follows a simple structure:
- Each page is a separate PHP file
- Database connection is in `config/config.php`
- Shared header/footer in `includes/` folder
- Authentication checks in `includes/auth.php`

## Next Steps

1. Test all features (register, login, add to cart, checkout, etc.)
2. Add your own product images
3. Customize the design if needed
4. Prepare your presentation explaining the code

Good luck with your project!

