# Simple Inventory Management System

A beginner-friendly PHP and MySQL inventory app with login, registration, dashboard, product CRUD, search, and low-stock status.

## Requirements
- XAMPP (Apache and MySQL)
- A web browser
- VS Code (optional)

## Local setup
1. Copy the `inventory_management` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in XAMPP.
3. Open `http://localhost/phpmyadmin`.
4. Choose **Import**, select `database/schema.sql`, then click **Import** or **Go**.
5. Open `connection.php` and confirm your database settings:
   - host: `localhost`
   - username: `root`
   - password: blank by default in a standard XAMPP setup
   - database: `inventory_db`
6. Visit `http://localhost/inventory/`.
7. Click **Register** to create an account, then log in.

## Make the first account an admin
New registrations are standard users. After registering your own account, open phpMyAdmin, select `inventory_db`, click the SQL tab, and run the following query. Replace `your_username` with your actual username:

```sql
UPDATE users SET role = 'admin' WHERE username = 'noviebayal00@gmail.com';
```

Log out and log back in to refresh your session. The admin can view the Users page. Do not give admin access to untrusted users.

## Main files
- `connection.php` - database connection
- `register.php`, `login.php`, `logout.php` - account functions
- `dashboard.php` - inventory summary
- `products.php` - add, view, edit, delete, and search products
- `users.php` - admin-only account list
- `database/schema.sql` - database tables and sample products
- `assets/style.css` - page design
- `assets/script.js` - delete confirmation

## Hosting
Before uploading to a PHP/MySQL host:
1. Create a MySQL database in the host control panel.
2. Import `database/schema.sql` using the host's database tool. If the host does not allow `CREATE DATABASE`, create the database in the panel first, remove the `CREATE DATABASE` and `USE inventory_db` lines, and import the remaining table definitions.
3. Update `connection.php` with the exact database host, database name, username, and password provided by the host.
4. Upload the PHP files and folders into the hosting web root (often `htdocs`).
5. Test registration, login, product CRUD, and search on the live website.

## Security notes
- Passwords are stored using `password_hash()` and checked with `password_verify()`.
- Database inputs use prepared statements for account and product operations.
- Output text is escaped to reduce cross-site scripting risks.
- This is a simple learning project. For production use, add CSRF protection, stronger session cookie settings, and additional error handling.
