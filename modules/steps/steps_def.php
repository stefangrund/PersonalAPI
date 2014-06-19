<?php
/*
 *	Module: Steps
 * 	Gets the number of your daily steps from Fitbit and adds them to the database.
 */

// Define Table / SQL statements for columns
function steps_table() {

	$table = array('papi_type_steps',"
		id INT AUTO_INCREMENT primary key NOT NULL,
		date DATETIME NOT NULL,
		steps INT NOT NULL,
		org_service VARCHAR(32)
	");

	return $table;

}

// Define settings
function steps_settings() {

	$settings = array(
		"description" => "daily number of steps",
		"service" => "Fitbit",
		"forms" => array("Consumer key", "Consumer secret", "User ID"),
		"help" => "<ol>
		<li><a href='https://dev.fitbit.com/apps/new'>Register</a> a new Fitbit app and insert the consumer key and secret here.</li>
		<li>Get your user ID (from your profile page, e.g. https://www.fitbit.com/user/23CN9Q &rarr; user id: 23CN9Q) and insert it here.</li>
		<li>Set your activities to public.</li>
		<li>Save and you're done.</li>
		</ol>"
	);

	return $settings;

}

// Get data from external service (Fitbit)
function steps_getData() {

	// Get saved options from database
	$m = new Modules;
	$options = $m->getOptions('steps');
	$opt = json_decode($options);

	// Throw exception if no data in database
	if($opt[0] == NULL) throw new Exception('Module credentials missing.');

	// Connect to Fitbit through third party library
	require 'fitbitphp.php';

	$fitbit = new FitBitPHP($opt[0], $opt[1]); // The $opt[0] etc. match the forms array above
	$fitbit->setResponseFormat('json');
	$fitbit->setUser($opt[2]);

	// Get steps from yesterday
	$yesterday = date("Y-m-d", strtotime('yesterday'));

	$response = $fitbit->getTimeSeries(steps,$yesterday,'1d');
	$response = $response[0];

	$date = date('Y-m-d H:i:s', strtotime($response->dateTime));

	$steps = $response->value;

	$fields = array(
		'date' => $date,
		'steps' => $steps,
		'org_service' => 'Fitbit'
	);

	return $fields;

}

// Save data to database
function steps_saveData() {

	// Construct URL
	$url = BASEURL . "v1/steps?token=" . getToken(1);

	// Get existing data
	$response = doGetRequest($url);
	$lastDate = date('Y-m-d', strtotime($response[0]['date'])); // last item's date

	$yesterday = date("Y-m-d", strtotime('yesterday'));

	// If the last item is not from yesterday, then POST new data
	if($lastDate != $yesterday) {

		$fields = steps_getData();
		doPostRequest($url, $fields);

	}

}

?>
