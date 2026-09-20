# GreenLeaf Plant Shop 🌿

GreenLeaf Plant Shop is a small e-commerce website for a student project built with native PHP and MySQL. It features a modern, minimal, and responsive design for selling indoor/outdoor plants and gardening accessories.

## Features

**Customer Features:**
- User Registration & Login (Password Hashed)
- View Products with Categories, Search, Price Filter, and Sorting
- Product Details with Stock management
- Shopping Cart System (Session-based)
- Checkout (Cash on Delivery or Bank Transfer)
- Order History (My Orders)
- Profile Management

**Admin Features:**
- Admin Dashboard with statistics
- Manage Products (CRUD + Image Upload)
- Manage Categories (CRUD)
- Manage Orders (Update order status: Pending, Confirmed, Shipping, Completed, Cancelled)

## Technologies Used
- **Backend:** Native PHP (no framework), PDO for Database Connection
- **Frontend:** HTML5, CSS3, Bootstrap 5, Font Awesome, JavaScript
- **Database:** MySQL

## Folder Structure
```text
greenleaf-shop/
├── admin/                  # Admin panel pages
├── assets/                 # CSS, JS, and UI images
├── config/                 # Database configuration
├── includes/               # Reusable components (Header, Navbar, Footer)
├── uploads/                # Uploaded product images
├── database.sql            # SQL file for database import
├── index.php               # Homepage
├── products.php            # Products listing
├── product_detail.php      # Product detail page
├── cart.php                # Shopping cart
├── checkout.php            # Checkout page
├── my_orders.php           # User order history
├── order_detail.php        # Customer order detail
├── profile.php             # User profile page
├── login.php               # Login page
├── register.php            # Registration page
├── logout.php              # Logout script
└── README.md               # Project documentation
```

## How to Install and Run via XAMPP

1. **Clone or Copy the Project:**
   Copy the `shopflower` (or `greenleaf-shop`) folder into your XAMPP `htdocs` directory.
   Example: `C:/xampp/htdocs/greenleaf-shop`

2. **Start XAMPP:**
   Open the XAMPP Control Panel and start **Apache** and **MySQL**.

3. **Import Database:**
   - Open your browser and go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
   - Create a new database named `greenleaf_shop` (Collation: `utf8mb4_unicode_ci`)
   - Click on the `greenleaf_shop` database, then go to the **Import** tab.
   - Choose the `database.sql` file included in the root folder of this project and click **Go**.

4. **Access the Website:**
   - Open your browser and navigate to: [http://localhost/greenleaf-shop](http://localhost/greenleaf-shop) (Change `greenleaf-shop` to your actual folder name if different).

## Admin Account Details

For demonstration purposes, a default admin account has been created:

- **Username:** `admin`
- **Email:** `admin@greenleaf.com`
- **Password:** `admin123`

*(Note: The system uses a secure MD5 to Bcrypt automatic migration on the first login for the seeded data, ensuring modern security standards with `password_hash()` and `password_verify()`)*

## Security Measures Implemented
- **SQL Injection Prevention:** Uses PHP PDO Prepared Statements.
- **XSS Prevention:** Outputs are escaped using `htmlspecialchars()`.
- **Password Protection:** Uses `password_hash()` and `password_verify()`.
- **Access Control:** Verifies session variables and user roles before granting access to Admin pages or restricted customer pages.

Enjoy using GreenLeaf Plant Shop! 🌱
# ______
