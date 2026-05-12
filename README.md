# 🛡️ StaffVault - Secure Employee Management System

StaffVault is a secure web application built for the **Secure Software Development** course. It provides a robust platform for managing staff records while implementing industry-standard security practices to protect sensitive data.

## 🚀 Key Features
- **Secure Authentication**: Password hashing using `bcrypt` (via PHP's `password_hash`).
- **SQL Injection Prevention**: Full use of **PDO Prepared Statements** for all database interactions.
- **CSRF Protection**: Implementation of anti-Cross-Site Request Forgery tokens.
- **XSS Mitigation**: Output encoding and input sanitization.
- **Modern UI**: A premium, responsive interface designed with CSS and modern design principles.

---

## 🛠️ Prerequisites
To run this project locally, you will need:
1.  **WAMP Server** (or XAMPP/MAMP) with:
    - **PHP 7.4+**
    - **MySQL 5.7+**
    - **Apache Server**
2.  A web browser (Chrome, Firefox, or Edge).

---

## 📥 Installation & Setup

Follow these steps to set up the project on your device:

### 1. Clone or Download the Project
Download the source code and place the `staffvault` folder into your web server's root directory:
- For **WAMP**: `C:\wamp64\www\staffvault`
- For **XAMPP**: `C:\xampp\htdocs\staffvault`

### 2. Database Configuration
1.  Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2.  Create a new database named `staffvaultdb`.
3.  Click on the `staffvaultdb` database, go to the **Import** tab.
4.  Choose the file `sql/schema.sql` from the project directory and click **Go**.

### 3. Connection Settings
Ensure the database credentials in `config/db.php` match your local environment:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'staffvaultdb');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default is empty for WAMP/XAMPP
```

### 4. Running the Application
1.  Start your WAMP/XAMPP services (Apache and MySQL).
2.  Open your browser and navigate to:
    `http://localhost/staffvault/public/index.php`

---

## 📂 Project Structure
```text
staffvault/
├── config/         # Database connection logic
├── includes/       # Helper functions and security middleware
├── public/         # Publicly accessible files (entry point, CSS, JS)
│   ├── css/        # Stylesheets
│   ├── js/         # Client-side scripts
│   └── index.php   # Login Page
├── sql/            # Database schema files
└── README.md       # Project documentation
```

---

## 🔒 Security Implementation Details
- **Database Security**: Used `PDO` with `ATTR_EMULATE_PREPARES` set to `false` to ensure real prepared statements.
- **Session Management**: Secure session handling to prevent session hijacking.
- **Error Handling**: Custom error messages to prevent information leakage (no raw SQL errors shown to users).

---


---
*Created for the Secure Software Development University Project.*
