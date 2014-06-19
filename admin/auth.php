<?php
/*
 *	Authentication
 *	Checks if entered username/password is valid.
 */

require_once '../inc/config.php';

session_start();

// Session data
$_SESSION['loggedin'] = false;

$username = $_POST['user'];
$password = $_POST['pass'];

// Query for user
$database = new Database();
$database->query("SELECT hash FROM papi_user WHERE username = :username LIMIT 1;");
$database->bind(':username', $username);
$user = $database->single();

// Hashing the password with its hash as the salt returns the same hash
if (crypt($password, $user['hash']) == $user['hash']) {

	$output = "User '" . $username . "' logged in successfully (IP: " . $_SERVER['REMOTE_ADDR'] . ").";
	logAction(__FILE__, NULL, $output);

	$_SESSION['wrong'] = false;
	$_SESSION['loggedin'] = true; // set session to be logged in
	$_SESSION['timeout'] = time(); // set last time the user made a request

	if(isset($_POST['redirect'])) {
		header("location: " . $_POST['redirect']);
	}
	else {
		header("location: ../admin/");
	}

}
else {

	$output = "User '" . $username . "' tried and failed to log in (IP: " . $_SERVER['REMOTE_ADDR'] . ").";
	logAction(__FILE__, NULL, $output);

	$_SESSION['wrong'] = true;
	session_write_close();
	header("location: ../admin/");
	exit();
	
}
 
?>
