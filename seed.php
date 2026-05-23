<?php
include(__DIR__ . '/config/connect.php');

// ADMIN
$admin_pass = password_hash("admin123", PASSWORD_DEFAULT);
mysqli_query($conn, "
INSERT INTO users 
(student_number, first_name, last_name, middle_name, email, password, role, course, year_level, contact_number, account_status)
VALUES
('ADMIN001', 'System', 'Admin', '', 'admin@dtc.com', '$admin_pass', 'admin', 'N/A', 'N/A', '0000000000', 'active')
");

// REGISTRAR
$reg_pass = password_hash("reg123", PASSWORD_DEFAULT);
mysqli_query($conn, "
INSERT INTO users 
(student_number, first_name, last_name, middle_name, email, password, role, course, year_level, contact_number, account_status)
VALUES
('REG001', 'System', 'Registrar', '', 'registrar@dtc.com', '$reg_pass', 'registrar', 'N/A', 'N/A', '0000000000', 'active')
");

// STUDENT
$stud_pass = password_hash("stud123", PASSWORD_DEFAULT);
mysqli_query($conn, "
INSERT INTO users 
(student_number, first_name, last_name, middle_name, email, password, role, course, year_level, contact_number, account_status)
VALUES
('SU202400001', 'Test', 'Student', 'L', 'student@dtc.com', '$stud_pass', 'student', 'BSIT', '1st Year', '09123456789', 'active')
");

echo "Seed data inserted successfully!";
?>