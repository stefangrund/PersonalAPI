<?php
/*
 *	Module: Places
 *	Gets your latest Foursquare checkins and adds them to the database.
 */

// Define Table / SQL statements for columns
function places_table() {

	$table = array('papi_type_places',"
		id INT AUTO_INCREMENT primary key NOT NULL,
		date DATETIME NOT NULL,
		place TEXT NOT NULL,
		comment TEXT,
		lat DECIMAL(10, 8) NOT NULL,
		lng DECIMAL(11, 8) NOT NULL,
		org_service VARCHAR(32),
		org_id VARCHAR(32)
	");

	return $table;

}

// Define settings
function places_settings() {

	// Get saved options from database
	$m = new Modules;
	$options = $m->getOptions('places');
	$opt = json_decode($options);

	$help = "<ol>
	<li><a href='https://foursquare.com/developers/register'>Register</a> a new Foursquare app. Important: Set Redirect URI to <a href='" . $_SERVER['PHP_SELF'] . "'>this page</a>.</li>
		<li>Copy the Client ID and secret and insert it here.</li>
		<li>Save the module.</li>
		<li>A new link apperars under the Client secret field. Follow the link and authenticate your Personal API with your Foursquare login.</li>
		<li>You will be redirected to this page and you're done.</li>
		</ol>";

	$settings = array(
		"description" => "check-ins and locations (with geographic coordinates)",
		"service" => "Foursquare",
		"forms" => array("Client ID", "Client Secret"),
		"help" => $help
	);

	return $settings;

}

// Optional function to authenticate directly with the service (Foursquare)
function places_authenticate() {

	// Get saved options from database
	$m = new Modules;
	$options = $m->getOptions('places');
	$opt = json_decode($options);

	// If no options set
	if(sizeof($opt) == NULL) {
		echo "Save Client ID and Secret, then authenticate with Foursquare (link will be displayed here).<br/>";
	}
	// If Client ID and Secret are set, but there is no Access Token
	elseif(sizeof($opt) == 2) {

		// Set Foursquare client key and secret
		$clientKey = $opt[0];
		$clientSecret = $opt[1];
		$redirectUri = BASEURL . "admin/modules.php?saved=places"; // parameter set to "open" the places module on the modules admin page

		// Load the Foursquare API library
		require_once('FoursquareAPI.php');
		$foursquare = new FoursquareAPI($clientKey,$clientSecret);

		// If the link has been clicked and Foursquare replied with a code, use it to request a token
		if(array_key_exists("code",$_GET)){
			$token = $foursquare->GetToken($_GET['code'],$redirectUri);
		}

		// If token received, save it
		if(isset($token)) {

			echo "<label>Access Token:</label> <input type='text' name='places_2' value='" . $token . "' readonly /><br/>";
			$opt[] = $token; // add token to options array
			$dataStr = json_encode($opt); // convert to JSON
			$m->setOptions('places', $dataStr); // save to database
		}
		// If there is no token, display the link for Foursquare webauth
		else {
			echo "<a href='".$foursquare->AuthenticationLink($redirectUri)."'>Authenticate with Foursquare</a><br/>";
		}

	}
	// If Client Id, Secret and Access Token are set
	elseif(sizeof($opt) == 3) {
		echo "<label>Access Token:</label> <input type='text' name='places_2' value='" . $opt[2] . "' readonly /><br/>";
	}

}

// Get data from external service (Foursquare)
function places_getData() {

	require_once('FoursquareAPI.php');

	// Get saved options from database
	$m = new Modules;
	$options = $m->getOptions('places');
	$opt = json_decode($options);

	// Set Foursquare client key and secret
	$clientKey = $opt[0];
	$clientSecret = $opt[1];
	$accessToken = $opt[2];

	// Throw exception if no keys saved/authorization missing
	if($clientKey == NULL) throw new Exception('Module credentials missing.');
	if($accessToken == NULL) throw new Exception('Foursquare access token missing.');

	// Load the Foursquare API library
	$foursquare = new FoursquareAPI($client_key,$client_secret);
	$foursquare->SetAccessToken($accessToken);
	
	// Perform a request to a authenticated-only resource
	$response = $foursquare->GetPrivate("users/self/checkins");
	$checkins = json_decode($response);

	// Add item with needed informations to array
	foreach($checkins->response->checkins->items as $item) {

		$date = date('Y-m-d H:i:s', $item->createdAt); // convert Unix timestamp to YYYY-MM-DD HH:MM:SS

		$fields[] = array(
			'date' => $date,
			'place' => $item->venue->name, 
			'comment' => $item->shout,
			'lat' => $item->venue->location->lat,
			'lng' => $item->venue->location->lng,
			'org_service' => 'Foursquare',
			'org_id' => $item->id
		);

	}

	return array_reverse($fields);

}

// Save data to database
function places_saveData() {

	// Get array with new data
	$fields = places_getData();
	$count = sizeof($fields); // count items

	// Construct GET URL
	$url = BASEURL . "v1/places?count=" . $count . "&token=" . getToken(1);

	// GET existing data
	$response = array_reverse(doGetRequest($url));

	// Iterate through all new items and if their ID isn't found in the request with the latest data, add them
	for ($i=0; $i < $count; $i++) { 

		$newId = $fields[$i]['org_id']; // original ID of a new item
		
		if($response['code'] == 404) {
			doPostRequest($url, $fields[$i]);
		}
		elseif(!in_array_r($newId, $response)) {
			doPostRequest($url, $fields[$i]);
		}
	
	}

}

?>
