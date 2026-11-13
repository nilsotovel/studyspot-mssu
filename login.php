<?php
session_start();
require "includes/db_connect.php";

// Only accept the login form (POST). Anything else goes back to the login page.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit();
}

// Get information submitted from the HTML form
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Server-side check: both fields are required
if ($username === "" || $password === "") {
    header("Location: login.html?error=1");
    exit();
}

// Find the username in the users table (prepared statement = no SQL injection)
$sql = "SELECT user_id, username, password_hash FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) == 1) {
    mysqli_stmt_bind_result($stmt, $user_id, $stored_username, $stored_hash);
    mysqli_stmt_fetch($stmt);

    // Compare the typed password with the stored hash
    if (password_verify($password, $stored_hash)) {
        // New session id after login (stops session fixation attacks)
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $stored_username;

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        header("Location: dashboard.php");
        exit();
    }
}

// Login failed. Same message for wrong username or wrong password,
// so nobody can find out which usernames exist.
mysqli_stmt_close($stmt);
mysqli_close($conn);

header("Location: login.html?error=1");
exit();
