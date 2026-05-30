# ByteStore Installation Guide

## Fresh Installation

### Requirements

- XAMPP 8.x (PHP 8.0+, MySQL 5.7+ / MariaDB 10.4+)
- Web browser (Chrome, Firefox, Edge)

### Steps

1. **Copy project** to `htdocs/bytestore`.

2. **Create database** (automatic via SQL file):
   ```powershell
   Get-Content database\bytestore_complete.sql | mysql -u root
   ```

3. **Configure** `config/config.php`:
   ```php
   $host = 'localhost';
   $user = 'root';
   $pass = '';
   $dbname = 'bytestore';
   ```

4. **Permissions** — ensure these folders are writable:
   - `assets/uploads/`
   - `assets/uploads/products/`
   - `assets/uploads/variants/`
   - `assets/uploads/profiles/`

5. **Verify** — open `http://localhost/bytestore/setup_check.php`

6. **Login** — use demo accounts from `README.md`

## Existing Installation Upgrade

If you have an older ByteStore database:

1. Back up your database first.
2. For a clean demo environment, import `database/bytestore_complete.sql` (replaces all data).
3. To preserve data, contact your developer or manually add missing tables from `database/schema_base.sql` (development reference only).

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Blank page | Enable `display_errors` in `php.ini`; check Apache error log |
| Database connection failed | Start MySQL in XAMPP; verify `config/config.php` |
| Images not loading | Confirm `assets/uploads/` exists; check product image paths |
| Charts grow infinitely | Clear browser cache; ensure `assets/js/dashboard-charts.js` is loaded |
| Support page error | Import latest `bytestore_complete.sql` (includes `support_ticket` tables) |

## Regenerating Demo Data (Developers)

```powershell
c:\xampp\php\php.exe database\build_complete_database.php
```

This rebuilds `database/bytestore_complete.sql` with 120+ products, 500+ orders, 350+ reviews, and full analytics seed data.
