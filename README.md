# Caroline's Place — Luxury Sanctuary & Spa

A private members' club and luxury spa sanctuary in Lagos, Nigeria, built in honour of Dame Caroline Oladunni Adesubun.

---

## 🚀 Hostinger Launch & Deployment Guide

This project is fully structured with standard **HTML5, CSS3, JavaScript, and PHP** ready for immediate deployment on **Hostinger** (or any Apache / cPanel / LiteSpeed web hosting).

### Step 1: Upload Files to Hostinger

1. Log into your **Hostinger hPanel** (or cPanel) and open **File Manager** (or connect via FTP / SFTP).
2. Navigate to your website's root public directory:
   ```text
   public_html/
   ```
3. Upload all the project files and folders directly into `public_html/`:
   ```text
   public_html/
   ├── admin/               <- Admin dashboard, spa products manager & login
   ├── api/                 <- Backend APIs, database connector & SQLite DB
   ├── assets/              <- CSS, JavaScript, fonts, and luxury imagery
   ├── includes/            <- Shared header and footer PHP templates
   ├── .htaccess            <- Apache rewrite rules & security headers
   ├── clubhouse.php        <- The Club House page
   ├── confirmation.php     <- Booking confirmation & receipt page
   ├── index.php            <- Homepage
   ├── schema.sql           <- MySQL database schema & initial seed data
   ├── spa.php              <- The Nail Lounge & Spa page
   ├── spa_menu.php         <- Spa menu & booking review flow
   └── spa_review.php       <- Booking review endpoint
   ```

*(Note: Node.js files like `server.js`, `data.js`, `views/`, and `node_modules/` are only used for the AI Studio preview environment and can be omitted when uploading pure PHP/HTML to Hostinger).*

---

### Step 2: Database Setup (Two Easy Options)

#### Option A: Zero-Configuration (Plug-and-Play SQLite — Recommended for Instant Launch)
- The project comes pre-bundled with an active, pre-seeded SQLite database at `api/carolines.sqlite`.
- If Hostinger's PHP has SQLite / PDO SQLite enabled (enabled by default on all Hostinger shared hosting plans), **no setup is required**.
- All spa categories (7), services (130+), pricing options (174+), and booking records work automatically out of the box!

#### Option B: Hostinger MySQL Database (Optional)
If you prefer running on a Hostinger MySQL / MariaDB database:
1. In **Hostinger hPanel**, go to **Databases** → **MySQL Databases** and create a new database (e.g. `u123456789_caroline`). Note the Database Name, Username, and Password.
2. Open **phpMyAdmin** for that database.
3. Click **Import** and select `schema.sql` from your project folder. Click **Go** to import all tables and seed data.
4. Set your credentials in `api/db.php` (or create a `.env` file in your root folder):
   ```ini
   DB_HOST=localhost
   DB_NAME=u123456789_caroline
   DB_USER=u123456789_admin
   DB_PASS=YourStrongPasswordHere
   DB_PORT=3306
   ```

---

### Step 3: Admin Portal Access

Once uploaded to your domain:
- **Admin Login URL:** `https://yourdomain.com/admin/login.php` (or `https://yourdomain.com/admin/login`)
- **Spa Products & Pricing Manager:** `https://yourdomain.com/admin/spa_products.php` (or `https://yourdomain.com/admin/spa_products`)
- **Bookings Dashboard:** `https://yourdomain.com/admin/dashboard.php` (or `https://yourdomain.com/admin/dashboard`)

**Default Admin Credentials:**
- **Username:** `admin`
- **Password:** `Caroline@Sanctuary2026`

---

### Step 4: URL Rewriting & Security (`.htaccess`)
The included `.htaccess` file is pre-configured to:
- Enable clean extensionless URLs (e.g. `/spa` → `spa.php`, `/admin/spa_products` → `admin/spa_products.php`).
- Block direct browser access to sensitive files (`.sqlite`, `.sql`, `.env`, `.git`).
- Enable Gzip / browser caching for assets (CSS, JS, WebP, JPG, SVG).

---

## 💻 Local Development (AI Studio / Node.js)

If testing or previewing inside Google AI Studio or locally with Node:
```bash
npm install
npm run dev
```
The preview runs on port `3000`.

