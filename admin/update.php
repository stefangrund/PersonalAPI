<?php
/*
 *	Update Admin Page
 *	Allows the user update the database manually.
 */

$title = "Update the Database";
require_once '../inc/config.php';
require_once 'auth_check.php'; // check if logged in
include '../inc/header.php';

$include = true;
?>

<p>You can open this page to update your database manually, but it's recommended to run the update process as a cron job. Point your cron job to the following URL and run it daily or several times a day for best results:</p>

<div class="codebox"><?php echo BASEURL . "update.php?token=" . getToken(1); ?></div>

<p>Now let's try to update the database manually:</p>

<div class="codebox">
	<?php include '../update.php'; ?>
</div>

<?php include '../inc/footer.php'; ?>
