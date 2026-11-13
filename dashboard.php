<?php
session_start();

// Only logged-in users can see the dashboard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

require "includes/db_connect.php";

$user_id = $_SESSION['user_id'];

// Load the study spots for the dropdown
$spots = [];
$result = mysqli_query($conn, "SELECT spot_id, name FROM study_spots ORDER BY spot_id");
while ($row = mysqli_fetch_assoc($result)) {
    $spots[$row['spot_id']] = $row['name'];
}

// Load every review, newest first, with the author's username and the spot name.
// JOIN combines rows from three tables using the foreign keys.
$sql = "SELECT r.review_id, r.user_id, r.rating, r.review_text, u.username, s.name AS spot_name
        FROM reviews r
        JOIN users u ON r.user_id = u.user_id
        JOIN study_spots s ON r.spot_id = s.spot_id
        ORDER BY r.created_at DESC, r.review_id DESC";
$result = mysqli_query($conn, $sql);

$reviews = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reviews[] = $row;
}

mysqli_close($conn);

// Show an error if add_review.php sent the user back
$show_error = isset($_GET['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySpot MSSU - Dashboard</title>
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
            <h2 class="section-title">Add a review</h2>

            <div class="form-box">
                <div id="error-box" class="error-box" <?php if ($show_error) echo 'style="display: block;"'; ?>>
                    <?php if ($show_error) echo "Your review could not be saved. Please check every field."; ?>
                </div>

                <form id="review-form" action="add_review.php" method="post">

                    <label for="spot_id">Study spot</label>
                    <select id="spot_id" name="spot_id">
                        <option value="">Select a study spot</option>
                        <?php foreach ($spots as $id => $name) { ?>
                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                        <?php } ?>
                    </select>

                    <span class="field-label">Rating</span>
                    <div class="star-rating">
                        <?php for ($i = 5; $i >= 1; $i--) { ?>
                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>">
                            <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stars">&#9733;</label>
                        <?php } ?>
                    </div>

                    <label for="review_text">Review</label>
                    <textarea id="review_text" name="review_text" rows="4" placeholder="What made it a good (or bad) place to study?"></textarea>

                    <button type="submit" class="post-btn">Post review</button>
                </form>
            </div>
        </section>

        <section>
            <h2 class="section-title">Reviews <span class="count">(<?php echo count($reviews); ?>)</span></h2>

            <?php if (count($reviews) === 0) { ?>
                <p class="empty-note">No reviews yet. Be the first to post one!</p>
            <?php } ?>

            <?php foreach ($reviews as $review) { ?>
                <div class="review-card">
                    <div class="review-top">
                        <h3><?php echo htmlspecialchars($review['spot_name']); ?></h3>
                        <span class="stars"><?php echo str_repeat("&#9733;", $review['rating']) . str_repeat("&#9734;", 5 - $review['rating']); ?></span>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                    <p class="review-author">Posted by <?php echo htmlspecialchars($review['username']); ?></p>

                    <?php if ($review['user_id'] == $user_id) { ?>
                        <!-- Only the author sees these buttons. The PHP files also check ownership. -->
                        <div class="review-buttons">
                            <a href="edit_review.php?id=<?php echo $review['review_id']; ?>" class="edit-btn">Edit</a>
                            <form action="delete_review.php" method="post" class="delete-form">
                                <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>

    </div>

    <script src="js/script.js"></script>
</body>
</html>
