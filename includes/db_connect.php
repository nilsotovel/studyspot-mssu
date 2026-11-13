<?php
// Connects to the StudySpot database. Every PHP page that needs the database
// includes this file with: require "includes/db_connect.php";
// These are the default XAMPP settings (user root, no password).

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "studyspot";

try {
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
} catch (mysqli_sql_exception $e) {
    // Show a simple message instead of PHP's full error (which reveals server details)
    die("Database connection failed. Is MySQL running in XAMPP, and was sql/studyspot.sql imported?");
}

mysqli_set_charset($conn, "utf8mb4");
