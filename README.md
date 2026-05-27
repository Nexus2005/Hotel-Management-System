# 🏨 FitStay Hotel Management System

[![PHP Version](https://img.shields.io/badge/PHP-7.4%20%7C%208.0%20%7C%208.1%20%7C%208.2-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%20%7C%205.7-orange.svg)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

FitStay is a comprehensive, web-based **Hotel Management System** designed to simplify hotel administration. It provides a visual dashboard for hotel owners and staff to manage rooms, guests, bookings, and payments. The frontend is styled using custom vanilla CSS for a clean, modern look, combined with a robust PHP backend and MySQL database.

---

## ✨ Key Features

- **📊 Central Dashboard**: View real-time operation summaries at a glance:
  - Total Available Rooms
  - Active Bookings
  - Registered Guests
  - Total Revenue generated
- **🛏️ Room Management**: Track and manage rooms (Single, Double, Suite) along with pricing, description, and status (`Available`, `Occupied`, `Maintenance`).
- **👥 Guest Directory**: Keep a record of guest details including names, contact numbers, email, addresses, and ID proofs.
- **📅 Booking & Reservations**: Seamlessly book rooms for guests, calculate nights stayed, manage check-in/check-out dates, and track booking statuses (`Booked`, `Checked In`, `Checked Out`, `Cancelled`).
- **💳 Payments Ledger**: Log payments for each booking, handle multiple payment methods (`Cash`, `Card`, `UPI`), and update transaction status (`Paid`, `Pending`, `Partial`).
- **📁 XML Integration**: Export and structured backup of guest data utilizing DTD and XSD schemas (`xml/` folder) for data consistency and validation.

---

## 🛠️ Tech Stack & Dependencies

- **Frontend**: HTML5, Vanilla CSS3 (Custom Variables, Flexbox/Grid), Google Fonts (Inter).
- **Backend**: PHP (OOP-style database access with secure PDO connection handling).
- **Database**: MySQL (clean relation mappings, foreign keys, and cascading deletes).
- **Data Schemas**: XML, XSD (XML Schema Definition), DTD (Document Type Definition).

---

## 📁 Project Structure

```text
Hotel-Management-System/
│
├── assets/                # Core frontend styling, scripts, and images
│   ├── css/
│   │   └── style.css      # Custom stylesheet for the application layout & views
│   └── js/                # Client-side scripts
│
├── config/                # System configuration files
│   ├── db.php             # Local database connection settings (git-ignored)
│   └── db.example.php     # Template configuration file for reference
│
├── includes/              # Shared UI sections
│   ├── header.php         # Document head, CSS stylesheets, and common layouts
│   ├── footer.php         # Script tags and closing HTML
│   └── sidebar.php        # Central navigation sidebar for administrative pages
│
├── modules/               # Modular features of the application
│   ├── bookings/          # Room booking, tracking, and reservation views
│   ├── guests/            # Guest details and registration directory
│   ├── payments/          # Billing and checkout payment options
│   └── rooms/             # Room inventory, types, and pricing editor
│
├── xml/                   # Guest reports data models
│   ├── guests.xml         # XML database/export of guest records
│   ├── guests.dtd         # Document Type Definition for guest records
│   └── guests.xsd         # XML Schema Definition validation schema
│
├── index.php              # Secure login landing page for admins
├── dashboard.php          # Central workspace displaying KPI reports
├── logout.php             # Admin session teardown helper
├── update_pass.php        # Admin utility to update/change account passwords
├── schema.sql             # SQL database script (tables creation & default seed data)
├── .gitignore             # Config to ignore local credential files
└── README.md              # Technical documentation (this file)
```

---

## 🚀 Step-by-Step Setup Guide

Follow these steps to set up and run the Hotel Management System locally on your computer:

### 1. Prerequisites
Ensure you have a local development environment. **[XAMPP](https://www.apachefriends.org/)** (v7.4 or newer) is recommended.

### 2. Project Location
Extract or clone the project directory into your XAMPP's public folder:
* Windows: `C:\xampp\htdocs\hotel`
* macOS: `/Applications/XAMPP/xamppfiles/htdocs/hotel`

### 3. Database Creation & Import
1. Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. Open your web browser and go to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
3. Click on the **Import** tab at the top navigation bar.
4. Click **Choose File** and select the `schema.sql` file in the root of the project folder.
5. Click **Go** / **Import** at the bottom. This creates the database `hotel_management` and sets up pre-populated seed data.

### 4. Connection Configuration (Local Credentials)
To prevent local credentials from leaking online, we ignore the connection file in Git:
1. Navigate to the `config/` directory.
2. Duplicate `db.example.php` and rename the copy to `db.php`.
3. Open `db.php` in your text editor and modify your credentials as needed:
   ```php
   $host = 'localhost';
   $dbname = 'hotel_management';
   $username = 'root';
   $password = 'YOUR_MYSQL_PASSWORD'; // Replace with your MySQL password
   ```

### 5. Accessing the Application
Navigate to the local URL: **[http://localhost/hotel/index.php](http://localhost/hotel/index.php)**.

---

## 🔐 Credentials & Password Administration

### Pre-Seeded Admin User Details
Use the following credentials to access the panel immediately after database import:
- **Email**: `admin@hotel.com`
- **Password**: `Admin@123`

### Security Best Practices
- **Password Encryption**: All password entries are encrypted using `password_hash()` (Bcrypt algorithm).
- **Git Protection**: `config/db.php` is listed in `.gitignore` to prevent developers from accidentally pushing root passwords to public version control systems.
- **SQL Injection Prevention**: Queries are executed using PDO Prepared Statements.

hotel-management-system, web-technology, wt-project, php, xampp, angularjs, javascript, mysql, full-stack, apache-server, single-page-application, crud-application, sppu, mumbai-university, btech-project, college-lab gym-management-system, fitness-management, web-technology, wt-project, php, xampp, angularjs, javascript, mysql, full-stack, apache-server, single-page-application, crud-application, sppu, mumbai-university, te-project, college-lab, Sppu -syllabus
