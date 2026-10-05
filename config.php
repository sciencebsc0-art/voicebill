<?php

// Razorpay TEST API credentials
define('RAZORPAY_KEY_ID', 'rzp_test_...');
define('RAZORPAY_KEY_SECRET', '...');

// MySQL
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pay_app');

function db(): mysqli
{
    $conn = new mysqli(
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME
    );

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

    return $conn;
}
?>