<!DOCTYPE html>
<html lang="en">
	<body>
	<?php
	session_start();
	
	// Get information submitted from HTML form
	$username = trim($_POST['username'] ?? '');
	$password = $_POST['password'] ?? '';
	
	$servername = getenv('DB_HOST');
	$db_username = getenv('DB_USER');
	$db_password = getenv('DB_PASS');
	$dbname = getenv('DB_NAME');

	$conn = mysqli_connect($servername, $db_username, $db_password, $dbname);
	if (!$conn) {
		die ("Connection failed: " .mysqli_connect_error());
	}
	
	// Find the Username in the Users Table
	$sql = "SELECT user_id, username, password_hash FROM Users WHERE username = ?";
	
	// prepares the sql command
	$stmt = mysqli_prepare($conn, $sql);
	
	// supplies the username and password hash
	mysqli_stmt_bind_param($stmt,"s",$username);
	
	// executes the sql code
	mysqli_stmt_execute($stmt);
	
	mysqli_stmt_store_result($stmt);
	
	if (mysqli_stmt_num_rows($stmt) == 1) {
		mysqli_stmt_bind_result(
			$stmt,
			$user_id,
			$stored_username,
			$stored_password
		);
		mysqli_stmt_fetch($stmt);
		
		if (password_verify($password, $stored_password)) {
			// create login session
			session_regenerate_id(true);
			
			$_SESSION['user_id'] = $user_id;
			$_SESSION['username'] = $stored_username;
			
			// redirect to the dashboard page
			// input the dashboard page here when it gets made
			mysqli_stmt_close($stmt);
			mysqli_close($conn);
			
			header("Location: dashboard.html");
			exit();
		}
	}
	// if login fail
	mysqli_stmt_close($stmt);
	mysqli_close($conn);
	
	header("Location: login.html?error=1");
	exit();
	
	?>
    </body>
</html>
