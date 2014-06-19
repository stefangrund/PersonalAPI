<?php
/*
 *	Update Database
 *	This is the target of your cron job. It gets and saves new data to the database.
 *	Run this script daily or several times a day for best results.
 */

require_once 'inc/config.php';

if(tableExists() == false) {

	header('Location: ' . BASEURL);
	exit;

}
else {

	// If authenticated with master token
	if ($_GET["token"] == getToken(1) OR isset($include)) {

		echo "Updating...<br/>";

		$m = new Modules;
		$modules = $m->getModules();

		$m->runSaveJobs(); // let all modules get and save new data

		logAction(__FILE__, NULL, "Successfully updated database.");

		echo "Done.<br/>";

	}
	else {
		echo "Update not authenticated.";
	}

}

?>
