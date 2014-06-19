<?php
/*
 *	Tokens Admin Page
 *	Lists all existing tokens with permissions, author and number of requests and creates new tokens.
 */

$title = "Tokens";
require_once '../inc/config.php';
require_once 'auth_check.php'; // check if logged in
include '../inc/header.php';

// If new token was saved, create it
if (isset($_POST['permissions']) AND isset($_POST['author'])) {

	require_once '../inc/install_functions.php';
	
	switch($_POST['permissions']) {
		case '0100':
			$permissions = array(0,1,0,0);
			break;
		case '1100':
			$permissions = array(1,1,0,0);
			break;
		case '1110':
			$permissions = array(1,1,1,0);
			break;
		case '1111':
			$permissions = array(1,1,1,1);
			break;
	}

	$author = $_POST['author'];
	createToken($permissions, $author);

}
?>

<p>Here is the place to manage your tokens. These are required to access your API's contents. Tokens can have permissions to create, read, update and/or delete data.</p>

<p>Your private <strong>master token</strong> is <code><?php echo getToken(1); ?></code>. Keep this one a secret, because it has all permissions to create, read, update and delete content in your API. Your first <strong>public token</strong> is <code><?php echo getToken(2); ?></code>. It only has the permission to read your contents. This one is displayed on <a href="../">your API's front page</a>.</p>

<h2>Create a New Token</h2>

<p>Sometimes it's good to know who's accessing your data. Therefore you can create new unique tokens for different users/apps and give them different permissions.</p>

<form method="post">
	<div style="margin: 0 0 10px 0;">
		<label>Permissions:</label>
	    <select name="permissions">
	    	<option value="0100" selected>Read only</option>
	    	<option value="1100">Create, Read</option>
	    	<option value="1110">Create, Read, Update</option>
	    	<option value="1111">Create, Read, Update, Delete</option>
	    </select>
    </div>
    <label>Author:</label><input name="author" type="text" maxlength="32" placeholder="Who's using this token?" /><br/>
    <input name="submit" type="submit" />
</form>

<h2>List of all your Tokens</h2>

<p>Here's a list of all your tokens. The number after the author indicates the number of requests for this token. The public and master token should have the highest numbers.</p>

<form method="post">
	Number of rows to display:
	<select name="rows" onchange="this.form.submit()">
		<option value="25" <?php if(intval($_POST['rows'] == 25)) echo 'selected'; ?>>25</option>
		<option value="50" <?php if(intval($_POST['rows'] == 50)) echo 'selected'; ?>>50</option>
		<option value="100" <?php if(intval($_POST['rows'] == 100)) echo 'selected'; ?>>100</option>
		<option value="200" <?php if(intval($_POST['rows'] == 200)) echo 'selected'; ?>>200</option>
	</select>
    <br/>&nbsp;<br/>
</form>

<table summary="Log" width="100%">
	<thead>
		<tr>
			<th width="35%"><strong>Token</strong></th>
			<th width="37%"><strong>Permissions</strong></th>
			<th width="28%"><strong>Author (Usage)</strong></th>
		</tr>
	</thead>
	<tbody>
	<?php

		if (!isset($_POST['rows'])) {
			$rows = 25;
		}
		else {
			$rows = intval($_POST['rows']);
		}

		// Get tokens and display them
		$database = new Database();
		$database->query("SELECT * FROM papi_tokens ORDER BY id DESC LIMIT :rows;");
		$database->bind(':rows', $rows);
		$result = $database->all();

		foreach ($result as $data) {

			$arrPermissions = array();

			if($data['p_create'] == true) $arrPermissions[] = "Create";
			if($data['p_read']   == true) $arrPermissions[] = "Read";
			if($data['p_update'] == true) $arrPermissions[] = "Update";
			if($data['p_delete'] == true) $arrPermissions[] = "Delete";

			$strPermissions = implode(", ", $arrPermissions);
			$data['requests'] == NULL ? $requests = 0 : $requests = $data['requests'];

			if($data['author'] == NULL) { $author = "-"; } else { $author = $data['author']; }

			echo "<tr><td>" . $data['token'] . "</td><td>" . $strPermissions . "</td><td>" . $author . " (" . $requests . ")</td></tr>";
			
		}

	?>
	</tbody>
</table>

<?php include '../inc/footer.php'; ?>
