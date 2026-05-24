# FitStay Hotel Management System

## Overview
FitStay is a Hotel Management System built using PHP and MySQL. It provides an administrative interface to manage rooms, guests, room bookings, and payments.

## Project Structure
The project is organized into the following directories and key files:

- `index.php`: The main entry point and login page for the admin panel.
- `dashboard.php`: The central admin dashboard displaying a summary of the hotel's operations.
- `logout.php`: Script to securely log the admin out of the session.
- `update_pass.php`: Utility script for updating admin passwords.
- `schema.sql`: Contains the database structure and initial seed data (default admin user, sample rooms, guests, bookings, and payments).
- `assets/`: Stores static assets such as CSS stylesheets (`style.css`), JavaScript files, and images.
- `config/`: Contains configuration files. For example, `db.php` holds the PDO database connection settings.
- `includes/`: Houses reusable layout components like the header, footer (`footer.php`), and navigation sidebar.
- `modules/`: Contains the core logic and user interface views for different sections of the application, such as rooms, guests, bookings, and payments.
- `xml/`: Contains XML structure definitions (`guests.xsd`, `guests.dtd`) and XML files used for guest data export and reporting purposes.

## Requirements
To run this project, you will need a local server environment.
- XAMPP, WAMP, or MAMP (XAMPP is recommended and assumed in the setup instructions)
- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB

## How to Run

1. **Start Local Server (XAMPP):**
   - Open the XAMPP Control Panel.
   - Start the **Apache** and **MySQL** modules.

2. **Setup the Database:**
   - Open your web browser and navigate to `http://localhost/phpmyadmin`.
   - You can create the database and tables by importing the provided SQL schema:
     - Click on the **Import** tab at the top.
     - Click **Choose File** and select the `schema.sql` file located in the project root directory (`c:\xampp\htdocs\hotel\schema.sql`).
     - Scroll down and click **Go** to execute the script.
   - *Note: The `schema.sql` script automatically creates the `hotel_management` database, necessary tables, and inserts default data for testing.*

3. **Database Configuration (If necessary):**
   - The application expects a default XAMPP MySQL setup (username: `root`, password: ``).
   - If your MySQL credentials are different, update them in `config/db.php`.

4. **Access the Application:**
   - Ensure the project folder is placed inside the `htdocs` directory of your XAMPP installation (`c:\xampp\htdocs\hotel`).
   - Open your web browser.
   - Navigate to the application URL: `http://localhost/hotel/` or `http://localhost/hotel/index.php`.
   - You will be presented with the FitStay Admin Panel login page.

5. **Login Credentials:**
   - Use the default admin credentials provided in the database seed:
     - **Email:** `admin@hotel.com`
     - **Password:** `Admin@123`

## Features
- **Dashboard:** Provides a quick overview of total available rooms, active bookings, registered guests, and total revenue.
- **Room Management:** Add new rooms, update details, delete rooms, and track room statuses (Available, Occupied, Maintenance).
- **Guest Management:** Register new guests, update their information, maintain a directory, and export guest data using XML.
- **Booking Management:** Create new room bookings, assign rooms to guests, manage check-in/check-out dates, and monitor booking status.
- **Payment Management:** Record payments against bookings, log payment methods (Cash, Card, UPI), and track paid, pending, or partial statuses.
