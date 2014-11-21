<?php
/*
 *	Definition of the standard tables
 *	In particular: papi_log, papi_user, papi_tokens, papi_modules
 */

// Define standard tables / SQL statements for columns
$tblStandard = array(

	// Table Log
	// Contains the API's event logs.
	array('papi_log',"
		id INT AUTO_INCREMENT primary key NOT NULL,
		date DATETIME NOT NULL,
		file VARCHAR(256),
		function VARCHAR(256),
		action VARCHAR(256) NOT NULL
	"),

	// Table User
	// Contains the user settings.
	array('papi_user',"
		id INT AUTO_INCREMENT primary key NOT NULL,
		username VARCHAR(32) NOT NULL,
		hash TEXT NOT NULL,
		display_name VARCHAR(32) NOT NULL,
		display_url VARCHAR(256),
		UNIQUE (username)
	"),

	// Table Tokens
	// Contains the access tokens.
	array('papi_tokens',"
		id INT AUTO_INCREMENT primary key NOT NULL,
		date datetime NOT NULL,
		token text NOT NULL,
		author VARCHAR(32) NOT NULL,
		requests INT,
		p_create TINYINT(1) NOT NULL,
		p_read TINYINT(1) NOT NULL,
		p_update TINYINT(1) NOT NULL,
		p_delete TINYINT(1) NOT NULL
	"),

	// Table Modules
	// Containts the module settings.
	array('papi_modules',"
		id INT AUTO_INCREMENT primary key NOT NULL,
		name VARCHAR(32) NOT NULL,
		data TEXT,
		active BOOL NOT NULL DEFAULT '0',
		private BOOL NOT NULL DEFAULT '0'
	")

);

?>
