# DTC Student System

A web-based Student Management System developed as part of academic requirements for ICCT Colleges.

## 📌 Overview
The DTC Student System is a PHP and MySQL-based web application designed to handle student records and document request processing. It supports multiple user roles and implements role-based access control to ensure proper system security and workflow separation.

## 👥 User Roles
- Admin
- Registrar
- Student

## ✨ Features
- Secure login system for all user roles
- Role-based access control with automatic redirection
- Student dashboard for viewing and tracking requests
- Registrar module for processing document requests
- Admin panel for system management
- CRUD operations for managing records (Create, Read, Update, Delete)
- MySQL database integration with relational tables
- Document request management and tracking system

## 🛠️ Tech Stack
- PHP (Core PHP)
- MySQL
- HTML, CSS, JavaScript
- XAMPP / Localhost environment

## 📂 Project Structure
- `/admin` - Admin panel and system management
- `/student` - Student dashboard and request handling
- `/registrar` - Registrar processing module
- `/assets` - Frontend resources (CSS, JS)
- `/config` - Database connection settings
- `/database` - SQL database file

## ⚙️ Setup Instructions
1. Clone or download the repository
2. Place the project folder inside your `htdocs` directory (XAMPP/Laragon)
3. Start Apache and MySQL in XAMPP/Laragon
4. Import the database:
   - Open phpMyAdmin
   - Create a new database
   - Import the SQL file inside `/database`
5. Configure database connection:
   - Open `/config/connect.php`
   - Update database name, username, and password if needed
6. Open the system in your browser: http://localhost/dtc-system


## 🔐 Access Control
Each user role has restricted access to specific modules. After login, users are redirected to their respective dashboards based on their role.

## 📌 Notes
- This system is developed for educational purposes only
- Built using core PHP without frameworks

## 📄 License
For academic use only. Not intended for commercial deployment.
