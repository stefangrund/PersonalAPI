<?php
/*
 *	Admin Dashboard
 *	The index page of the administration panel.
 */

$title = "Dashboard";
require_once '../inc/config.php';
require_once 'auth_check.php'; // check if logged in
include '../inc/header.php';
?>

<p>Hey <?php echo getName(); ?>, you're logged in now. This is your Admin Dashboard, where you can set up the different modules and get an overview of the last events in your Personal API.</p>

<div class="module-select"><a href="modules.php"><div class="icon-module index-icon" style="font-size: 40px; margin: 2px 15px 10px 0;"></div><strong>Modules</strong></a><br/>Set up connections to external services.</div>

<div class="module-select"><a href="tokens.php"><div class="icon-token index-icon" style="font-size: 34px; margin: 4px 18px 10px 3px;"></div><strong>Tokens</strong></a><br/>Manage the access tokens to interact with your API.</div>

<div class="module-select"><a href="update.php"><div class="icon-update index-icon" style="font-size: 36px; margin: 2px 17px 10px 2px;"></div><strong>Update the Database</strong></a><br/>Fetch and add data from external services.</div>

<div class="module-select"><a href="user.php"><div class="icon-user index-icon" style="font-size: 40px; margin: 1px 15px 10px 0;"></div><strong>User Settings</strong></a><br/>Change your password, displayed name or URL.</div>

<div class="module-select"><a href="log.php"><div class="icon-logs index-icon" style="font-size: 32px; margin: 7px 18px 10px 5px;"></div><strong>View Log</strong></a><br/>See what's happening within your API and database.</div>

<div class="module-select"><a href="docs.php"><div class="icon-docs index-icon" style="font-size: 36px; margin: 5px 18px 10px 2px;"></div><strong>Documentation</strong></a><br/>Learn how to set up and use your Personal API.</div>


<div class="module-select"><a href="logout.php"><div class="icon-logout index-icon" style="font-size: 36px; margin: 5px 17px 10px 3px;"></div><strong>Logout</strong></a><br/>Leave the Admin pages.</div>

<?php include '../inc/footer.php'; ?>
