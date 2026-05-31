DTC Student System
A web-based Student Management System developed as part of academic requirements for ICCT Colleges.

📌 Overview

The DTC Student System is a PHP and MySQL-based web application designed to manage student records and process document requests.
It supports multiple user roles with role-based access control to ensure secure and structured workflows.

👥 User Roles
Admin – System management and user administration
Registrar – Handles and processes document requests
Student – Submits and tracks document requests

✨ Features
- Secure login system with session handling
- Role-based access control (RBAC)
- Automatic role-based dashboard redirection
- Student document request tracking system
- Registrar request processing module
- Admin panel for user and system management
- Full CRUD operations (Create, Read, Update, Delete)
- MySQL relational database integration
- CSRF protection and basic security middleware
- Flash message system for user feedback

🛠️ Tech Stack
PHP (Core PHP, no framework)
MySQL
HTML, CSS, JavaScript
XAMPP / Localhost environment

📂 Project Structure
/admin – Admin dashboard and management tools
/student – Student portal and request tracking
/registrar – Document processing dashboard
/assets – CSS, JavaScript, and frontend resources
/config – Database connection and configuration
/middleware – Authentication, CSRF, and access control
/includes – Reusable components (navbar, flash, etc.)
/database – SQL schema and database dump

⚙️ Setup Instructions
1. Clone the repository:
git clone https://github.com/your-username/dtc-system.git

2. Move the project to your local server:
- Place inside htdocs (XAMPP) or equivalent

3. Start services:
Apache
MySQL

4. Import database:
- Open phpMyAdmin
- Create a new database (e.g. dtc_system)
- Import SQL file from /database
5. Configure database connection:
- Open /config/Database.php
- Update credentials if needed
6. Run the project:
http://localhost/dtc-system

🔐 Security Features
- Session-based authentication
- Role-based access control
- CSRF token protection on POST requests
- Self-protection rules (no self-delete / self-reset restrictions)

📌 Notes
Built using pure PHP (no frameworks)
Developed for academic compliance (ICCT Colleges requirements)
Not intended for production deployment without improvements

📄 License
This project is licensed under the MIT License.

👨‍💻 Developer
Jethro Manuel
