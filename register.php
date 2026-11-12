<!DOCTYPE html>
<html lang="en">
	<body>
	<?php
	// Get information submitted from HTML form
	$username = trim($_POST['username'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirm_password = $_POST['confirm_password'] ?? '';
	
	$servername = getenv('DB_HOST');
	$db_username = getenv('DB_USER');
	$db_password = getenv('DB_PASS');
	$dbname = getenv('DB_NAME');

	$conn = mysqli_connect($servername, $db_username, $db_password, $dbname);
	if (!$conn) {
		die ("Connection failed: " .mysqli_connect_error());
	}
	
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

	$sql = "INSERT INTO Users (username, password_hash) VALUES (?, ?)";	
	// prepares the sql command
	$stmt = mysqli_prepare($conn, $sql);
	
	// supplies the username and password hash
	mysqli_stmt_bind_param(
		$stmt,
		"ss",
		$username,
		$password_hash
	);
	
    if (mysqli_stmt_execute($stmt)) {
		echo "Account successfully created!";
	} else {
		echo "Unable to create account. The username may already exist.";
	}

	// Closing connections
	mysqli_stmt_close($stmt);
	mysqli_close($conn);
	?>
    </body>
</html>
