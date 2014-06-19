<?php
/*
 *	Logout
 *	Ends the login session and redirects the user to the public index page.
 */

session_start();
session_destroy();

header('Location: ../');

?>
