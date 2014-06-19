<?php
/*
 *	Authentication Check
 *	Checks if the user's session is valid. Otherwise redirects him to the public index page.
 */

session_start();

if (tableExists() == true) {

	// If session is not logged in
	if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
		header('Location: ../admin/login.php?redirect=' . $_SERVER['PHP_SELF']);
		exit;
	}

	// If session timeout (10min)
	if ($_SESSION['timeout'] + 10 * 60 < time()) {
		header('Location: ../admin/login.php?redirect=' . $_SERVER['PHP_SELF']);
		exit;
	}

	// Generate new session id
	session_regenerate_id();

}
else {
	header('Location: ' . BASEURL);
	exit;
}

?>
