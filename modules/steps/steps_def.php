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

	// Get steps starting from yesterday
	$yesterday = date("Y-m-d", strtotime('yesterday'));

	$response = $fitbit->getTimeSeries(steps,$yesterday,'30d'); // timespan to get
	
	foreach($response as $item) {
		$date = date('Y-m-d H:i:s', strtotime($item->dateTime));
		$steps = $item->value;

		$fields[] = array(
			'date' => $date,
			'steps' => $steps,
			'org_service' => 'Fitbit'
		);
	}

	return $fields;

}

// Save data to database
function steps_saveData() {

	// Get new data
	$fitbit_data = steps_getData(); // new steps
	$fitbit_data_size = count($fitbit_data);

	// Construct URL for GET request
	$get_url = BASEURL . 'v1/steps?count=' . $fitbit_data_size . '&token=' . getToken(1);

	// Get existing data
	$papi_data = array_reverse(doGetRequest($get_url));

	for ($i=0; $i < $fitbit_data_size; $i++) { 

		// Construct URL for POST request
		$post_url = BASEURL . 'v1/steps?token=' . getToken(1);

		// Get current item's id
		$item_id = $papi_data[$i]['id'];

		// Construct URL for PUT request
		$put_url = BASEURL . 'v1/steps/' . $item_id .'?token=' . getToken(1);

		// New item's parameters
		$fields = $fitbit_data[$i];


		// If day isn't in the database yet, add it
		if (!in_array_r($fitbit_data[$i]['date'], $papi_data)) {
			doPostRequest($post_url, $fields);
		}
		else {
			// Prevent updating when out of sync AND useless updating
			if($fitbit_data[$i]['date'] == $papi_data[$i]['date'] AND $fitbit_data[$i]['steps'] != $papi_data[$i]['steps']) {
				doPutRequest($put_url, $fields);
			}
			/*
				The updating process needs two steps:
				1. update: add all missing days to db
				2. update: correct all days with outdated steps
			*/
		}

	}

}

?>
