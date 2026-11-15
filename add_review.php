<?php
session_start();

// Only logged-in users can post a review
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit();
}

require "includes/db_connect.php";

// The user_id comes from the session, never from the form
$user_id = $_SESSION['user_id'];
$spot_id = (int) ($_POST['spot_id'] ?? 0);
$rating = (int) ($_POST['rating'] ?? 0);
$review_text = trim($_POST['review_text'] ?? '');

// Server-side validation
$valid = true;

// The study spot has to exist in the database
$sql = "SELECT spot_id FROM study_spots WHERE spot_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $spot_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) == 0) {
    $valid = false;
}
mysqli_stmt_close($stmt);

if ($rating < 1 || $rating > 5) {
    $valid = false;
}
if ($review_text === "" || strlen($review_text) > 2000) {
    $valid = false;
}

if (!$valid) {
    mysqli_close($conn);
    header("Location: dashboard.php?error=1");
    exit();
}

// Save the review
$sql = "INSERT INTO reviews (user_id, spot_id, rating, review_text) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiis", $user_id, $spot_id, $rating, $review_text);
mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);
mysqli_close($conn);

header("Location: dashboard.php");
exit();
