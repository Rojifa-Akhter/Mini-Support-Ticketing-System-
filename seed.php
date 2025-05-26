<?php
require_once 'models/User.php';
require_once 'models/Department.php';

if (User::findByEmail('admin@gmail.com') === null) {
    User::create('Admin User', 'admin@gmail.com', '12345678', 'admin');
    echo "Admin user created\n";
}

$departments = Department::all();
if (count($departments) === 0) {
    Department::create('Support');
    Department::create('Sales');
    Department::create('IT');
    echo "Departments created\n";
}
