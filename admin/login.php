<?php
/*
 *	Login to Admin
 *	Allows the user log into the administration panel.
 */

$title = "Login";
require_once '../inc/config.php';
session_start();
include '../inc/header.php';
?>

<p>Login to the Admin Dashboard to control your Personal API.</p>

<?php if(isset($_SESSION['wrong']) AND $_SESSION['wrong'] == true) {
	echo '<div id="tip" class="wrong">Username and/or password are incorrect.</div>';
} ?>

<form action="auth.php" method="POST">
<?php if(isset($_GET['redirect'])) {
	echo '<input type="hidden" name="redirect" value="' . $_GET['redirect'] . '" />';
} ?>
	<label>Username:</label> <input type="text" name="user" maxlength="32" /><br>
	<label>Password:</label> <input type="password" name="pass" /><br>
	<input type="submit" value="Login" />
</form>

<?php include '../inc/footer.php'; ?>
