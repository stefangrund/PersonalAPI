<?php
/*
 *	User Settings Admin Page
 *	The user can change his username, password, displayed name or URL here.
 */

$title = "User Settings";
require_once '../inc/config.php';
require_once 'auth_check.php'; // check if logged in

// Get saved user settings
function getUserSettings() {

	$database = new Database();
	$database->query("SELECT * FROM papi_user WHERE id=1;");
	$database->bind(':rows', $rows);
	return $database->single();

}

// Update user settings
function updateUser($username, $hash, $displayName, $displayURL) {

	$database = new Database();
	// If password was not changed
	if($hash == NULL) {
		$database->query("UPDATE papi_user SET username=:username, display_name=:display_name, display_url=:display_url WHERE id=1;");
		$database->bind(':username', $username);
		$database->bind(':display_name', $displayName);
		$database->bind(':display_url', $displayURL);
	}
	// If password was changed
	else {
		$database->query("UPDATE papi_user SET username=:username, hash=:hash, display_name=:display_name, display_url=:display_url WHERE id=1;");
		$database->bind(':username', $username);
		$database->bind(':hash', $hash);
		$database->bind(':display_name', $displayName);
		$database->bind(':display_url', $displayURL);
	}
	$database->execute();

}

$user = getUserSettings();

// If is saved
if (isset($_POST['submit'])) {

	$username = $_POST['user'];
	$password = $_POST['pass'];
	$displayName = $_POST['name'];
	$displayURL = $_POST['url'];

	if($password != NULL) { 
		$hash = encrypt($password);
		updateUser($username, $hash, $displayName, $displayURL);
	}
	else {
		updateUser($username, NULL, $displayName, $displayURL);
	}
	header('Location: ' . $_SERVER['PHP_SELF'] . '?saved=true');

}

include '../inc/header.php';
?>

<p>You can change your username, password, displayed name or URL here.</p>

<?php if(isset($_GET['saved'])) {
	echo '<div id="tip" class="saved">Changes saved.</div>';
} ?>


	<form method="post" class="user">
		<input type="hidden" name="saved" value="true" />
		<label>Username:</label> <input name="user" type="text" maxlength="32" value="<?php echo $user['username']; ?>" /><br/>
		<label>New Password:</label> <input name="pass" type="password" /><br/>
		<label>Displayed Name:</label> <input name="name" type="text" maxlength="32" value="<?php echo $user['display_name']; ?>" /><br/>
		<label>Displayed URL:</label> <input name="url" type="text" maxlength="256" value="<?php echo $user['display_url']; ?>" /><br/>
		<input name="submit" type="submit" />
	</form>

<?php include '../inc/footer.php'; ?>
