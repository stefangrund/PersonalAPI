<?php
/*
 *	Install Functions
 *	Functions to create the tables, user and tokens in the database.
 */

require_once 'config.php';

// Create new tables in database
function createTables($feedback = false) {

	// Array with all tables, must be filled now
	$tables = array();

	// Get standard tables
	require_once 'install_tables.php'; // name is $tblStandard

	// Get modules' tables
	$module = new Modules;
	$tblModules = $module->getTableDefintions();

	// Merge them
	$tables = array_merge($tblStandard, $tblModules);

	// Create all the tables (if they don't exist already)
	$tablesCount = count($tables) - 1;
	for ($i = 0; $i <= $tablesCount; $i++) {

		$tableName = $tables[$i][0];
		$sql = "CREATE TABLE IF NOT EXISTS " . $tableName . " (" . $tables[$i][1] . ");";

		try { 
			$database = new Database();
			$database->query($sql);
			$database->execute();
			
			$output = "Table '" . $tableName . "' created.";
			if ($feedback == true) {
				logAction(__FILE__, __FUNCTION__, $output);
				echo $output . "<br/>";
			}

		}
		catch (Exception $e) {
			$outut = "Could not create table '" . $tableName . "'.";
			if ($feedback == true) {
				logAction(__FILE__, __FUNCTION__, $output);
				echo $output . "<br/>";
			}
		}

	}

}

// Create new user (only one user supported, because it's his Personal API)
function createUser($user, $hash, $displayName, $displayURL) {

    try {
		$database = new Database();
		$database->query("INSERT INTO papi_user (username, hash, display_name, display_url) VALUES(:username, :hash, :display_name, :display_url);");
		$database->bind(':username', $user, PDO::PARAM_STR, 32);
		$database->bind(':hash', $hash, PDO::PARAM_STR);
		$database->bind(':display_name', $displayName, PDO::PARAM_STR, 32);
		$database->bind(':display_url', $displayURL, PDO::PARAM_STR, 256);
		$database->execute();
  		
  		$output = "User '" . $user . "' created.";
  		logAction(__FILE__, __FUNCTION__, $output);
  		echo $output . "<br/>";
    }
    catch (Exception $e) {
		$output = "User '" . $user . "' could not be created.";
  		logAction(__FILE__, __FUNCTION__, $output);
  		echo $output . "<br/>";
    }

}

// Create new token (CRUD permissions as bool in array)
function createToken($permissions, $author, $feedback = false) {

	$date = date("Y-m-d H:i:s");

	// Generate new token with unique id based on microseconds and a random value
	$token = uniqid(mt_rand());

	// Get CRUD permissions from array
	$create = $permissions[0];
	$read   = $permissions[1];
	$update = $permissions[2];
	$delete = $permissions[3];

	try {
		$database = new Database();
		$database->query("INSERT INTO papi_tokens (date, token, author, p_create, p_read, p_update, p_delete) VALUES(:date, :token, :author, :create, :read, :update, :delete);");
		$database->bind(':date', $date);
		$database->bind(':token', $token, PDO::PARAM_STR);
		$database->bind(':author', $author, PDO::PARAM_STR, 32);
		$database->bind(':create', $create, PDO::PARAM_INT, 1);
		$database->bind(':read', $read, PDO::PARAM_INT, 1);
		$database->bind(':update', $update, PDO::PARAM_INT, 1);
		$database->bind(':delete', $delete, PDO::PARAM_INT, 1);
		$database->execute();
  		
  		$output = "Token '" . $author . "' created.";
  		logAction(__FILE__, __FUNCTION__, $output);
  		if ($feedback == true) echo $output . "<br/>";
    }
    catch (Exception $e) {
		$output = "Token '" . $author . "' could not be created.";
  		logAction(__FILE__, __FUNCTION__, $output);
  		if ($feedback == true) echo $output . "<br/>";
    }

}

?>
