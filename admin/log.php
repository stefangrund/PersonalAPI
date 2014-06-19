<?php
/*
 *	Log Admin Page
 *	Allows the user view the saved logs.
 */

$title = "View Log";
require_once '../inc/config.php';
require_once 'auth_check.php'; // check if logged in
include '../inc/header.php';
?>

<p>Here's the log file of your Personal API. It contains almost every of it's operations. Hover over a row with your mouse to see the file and/or function which performed the action.</p>

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
			<th width="27%"><strong>Time</strong></th>
			<th width="73%"><strong>Action</strong></th>
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

		$database = new Database();
		$database->query("SELECT * FROM papi_log ORDER BY date DESC LIMIT :rows;");
		$database->bind(':rows', $rows);
		$result = $database->all();

		foreach ($result as $data) {
			$hover = $data['file'] . " " . $data['function'];
			echo "<tr><td><span title='" . $hover . "'>" . $data['date'] . "</span></td><td><span title='" . $hover . "'>" . $data['action'] . "</span></td></tr>";
		}

	?>
	</tbody>
</table>

<?php include '../inc/footer.php'; ?>
