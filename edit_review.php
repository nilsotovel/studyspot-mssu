<?php
session_start();

// Only logged-in users can edit
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

require "includes/db_connect.php";

$user_id = $_SESSION['user_id'];
$errors = [];

// Load the study spots for the dropdown
$spots = [];
$result = mysqli_query($conn, "SELECT spot_id, name FROM study_spots ORDER BY spot_id");
while ($row = mysqli_fetch_assoc($result)) {
    $spots[$row['spot_id']] = $row['name'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The user clicked "Save changes"
    $review_id = (int) ($_POST['review_id'] ?? 0);
    $spot_id = (int) ($_POST['spot_id'] ?? 0);
    $rating = (int) ($_POST['rating'] ?? 0);
    $review_text = trim($_POST['review_text'] ?? '');

    // Server-side validation (same rules as script.js)
    if (!isset($spots[$spot_id])) {
        $errors[] = "Please choose a study spot.";
    }
    if ($rating < 1 || $rating > 5) {
        $errors[] = "Please pick a star rating.";
    }
    if ($review_text === "" || strlen($review_text) > 2000) {
        $errors[] = "Please write a review.";
    }

    if (count($errors) === 0) {
        // "AND user_id = ?" means you can only update your OWN review
        $sql = "UPDATE reviews SET spot_id = ?, rating = ?, review_text = ? WHERE review_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iisii", $spot_id, $rating, $review_text, $review_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        header("Location: dashboard.php");
        exit();
    }
} else {
    // The user clicked "Edit" on the dashboard: load the review to fill in the form
    $review_id = (int) ($_GET['id'] ?? 0);

    $sql = "SELECT spot_id, rating, review_text FROM reviews WHERE review_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $review_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $spot_id, $rating, $review_text);

    // No row means the review does not exist or is not yours
    if (!mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: dashboard.php");
        exit();
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySpot MSSU - Edit Review</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="page">

        <header class="dash-header">
            <div>
                <h1>StudySpot MSSU</h1>
                <p>Campus study spot reviews</p>
            </div>
            <div class="user-info">
                <span>Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="logout.php" class="logout-btn">Log out</a>
            </div>
        </header>

        <section>
            <h2 class="section-title">Edit your review</h2>

            <div class="form-box">
                <!-- Server-side errors show here; script.js uses the same box -->
                <div id="error-box" class="error-box" <?php if (count($errors) > 0) echo 'style="display: block;"'; ?>>
                    <?php echo implode("<br>", $errors); ?>
                </div>

                <form id="review-form" action="edit_review.php" method="post">

                    <label for="spot_id">Study spot</label>
                    <select id="spot_id" name="spot_id">
                        <option value="">Select a study spot</option>
                        <?php foreach ($spots as $id => $name) { ?>
                            <option value="<?php echo $id; ?>" <?php if ($id == $spot_id) echo "selected"; ?>>
                                <?php echo htmlspecialchars($name); ?>
                            </option>
                        <?php } ?>
                    </select>

                    <span class="field-label">Rating</span>
                    <div class="star-rating">
                        <?php for ($i = 5; $i >= 1; $i--) { ?>
                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php if ($i == $rating) echo "checked"; ?>>
                            <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stars">&#9733;</label>
                        <?php } ?>
                    </div>

                    <label for="review_text">Review</label>
                    <textarea id="review_text" name="review_text" rows="4"><?php echo htmlspecialchars($review_text); ?></textarea>

                    <input type="hidden" name="review_id" value="<?php echo $review_id; ?>">

                    <div class="form-actions">
                        <button type="submit" class="post-btn">Save changes</button>
                        <a href="dashboard.php" class="edit-btn">Cancel</a>
                    </div>
                </form>
            </div>
        </section>

    </div>

    <script src="js/script.js"></script>
</body>
</html>
