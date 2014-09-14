<?php
/*
 *	Installation Process
 *	Called on first run to create database tables, users and tokens.
 */

require_once 'install_functions.php';
?>

<?php if (!isset($_POST['submit'])) : ?>

	<p>Welcome to the installation of your Personal API. You've entered the database credentials correctly, so let's move on and create a user login for you.</p>

	<p>Just enter the following informations (you can change them later). Be sure to choose a password you can remember, because there's no way to recover it, if you forget it.</p>

	<form method="post" class="user">
		<label>Username:</label> <input name="user" type="text" maxlength="32" /><br/>
		<label>Password:</label> <input name="pass" type="text" /><br/>
		<label>Displayed Name:</label> <input name="name" type="text" maxlength="32" /><br/>
		<label>Displayed URL:</label> <input name="url" type="text" maxlength="256" placeholder="http://" /><br/>
		<input name="submit" type="submit" />
	</form>

<?php else : ?>

	<p>Okay, thank you. Now let's create the needed database tables, your account and the first access tokens:</p>

	<div class="codebox">
	<?php

		$username = $_POST['user'];
		$password = $_POST['pass'];
		$displayName = $_POST['name'];
		$displayURL = $_POST['url'];

		// Save password just as hash to database
		$hash = encrypt($password);

		// Create tables, user and tokens
		createTables(true);
		createUser($username, $hash, $displayName, $displayURL);
		createToken(array(true, true, true, true), 'master', true); // master token with all permissions
		createToken(array(false, true, false, false), 'public', true); // public token with only read permission

	?>
	</div>

	<p>Your Personal API is installed now but the modules aren't configured yet. This is required to fetch and add data from external services to your database. You'll need to enter your API keys, usernames, etc., into the modules' settings in order for them to work. There are instructions for getting these keys when you select a module.</p>

	<p><a href="admin/modules.php"><strong>Okay, let me configure the modules.</strong></a></p>
	
<?php endif; ?>
