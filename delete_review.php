<?php
session_start();

// Only logged-in users can delete
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Deleting changes data, so it must come from the form (POST), not a link
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit();
}

require "includes/db_connect.php";

$review_id = (int) ($_POST['review_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// "AND user_id = ?" means you can only delete your OWN review.
// If someone changes the hidden review_id, nothing matches and nothing is deleted.
$sql = "DELETE FROM reviews WHERE review_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $review_id, $user_id);
mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);
mysqli_close($conn);

header("Location: dashboard.php");
exit();
