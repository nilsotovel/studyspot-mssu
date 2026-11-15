<?php
// Home page: send logged-in users to the dashboard, everyone else to login.
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.html");
}
exit();
