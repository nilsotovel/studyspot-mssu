<?php
require "includes/db_connect.php";

// Only accept the register form (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.html");
    exit();
}

// Get information submitted from the HTML form
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Server-side validation: the same rules as script.js,
// checked again here because JavaScript can be turned off or bypassed
if (strlen($username) < 3 || strlen($username) > 50) {
    header("Location: register.html?error=invalid");
    exit();
}
if (strlen($password) < 6) {
    header("Location: register.html?error=invalid");
    exit();
}
if ($password !== $confirm_password) {
    header("Location: register.html?error=mismatch");
    exit();
}

// Make sure the username is not already taken
$sql = "SELECT user_id FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
$taken = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

if ($taken) {
    mysqli_close($conn);
    header("Location: register.html?error=taken");
    exit();
}

// Never store the real password, only its hash
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $username, $password_hash);

try {
    mysqli_stmt_execute($stmt);
} catch (mysqli_sql_exception $e) {
    // Backup in case two people register the same name at the same moment
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: register.html?error=taken");
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

header("Location: login.html?registered=1");
exit();
