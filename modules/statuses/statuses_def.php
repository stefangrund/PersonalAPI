<?php
/*
 *	Module: Statuses 
 *	Gets your latest status updates from Twitter and adds them to the database.
 */

// Define Table / SQL statements for columns
function statuses_table() {

	$table = array('papi_type_statuses',"
		id INT AUTO_INCREMENT primary key NOT NULL,
		date DATETIME NOT NULL,
		status TEXT NOT NULL,
		org_service VARCHAR(32),
		org_id VARCHAR(32)
	");

	return $table;

}

// Define settings
function statuses_settings() {

	$settings = array(
		"description" => "status updates",
		"service" => "Twitter",
		"forms" => array("API key", "API secret", "Access token", "Access token secret", "Username"),
		"help" => "<ol>
		<li><a href='https://apps.twitter.com/app/new'>Register</a> a new Twitter app.</li>
		<li>Select the &quot;API Keys&quot; tab and generate your access token.</li>
		<li>Copy the API key, API secret, Access token and Access token secret and insert it here.</li>
		<li>Save and you're done.</li>
		</ol>"
	);

	return $settings;

}

// Get data from external service (Twitter)
function statuses_getData() {

	// Get saved options from database
	$m = new Modules;
	$options = $m->getOptions('statuses');
	$opt = json_decode($options);

	// Throw exception if no data in database
	if($opt[0] == NULL) throw new Exception('Module credentials missing.');

	// Connect to Twitter through third party library
	require('TwitterAPIExchange.php');

	// Access token, Access token secret, API key, API secret
	$settings = array(
	    'oauth_access_token' => $opt[2],
	    'oauth_access_token_secret' => $opt[3],
	    'consumer_key' => $opt[0],
	    'consumer_secret' => $opt[1]
	);
	$username = $opt[4];
	$replies = 'false'; // Exclude @Replies?
	$retweets = 'true'; // Include Retweets?

	// Query
	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	$getfield = '?screen_name=' . $username . '&count=50&exclude_replies=' . $replies . '&include_rts=' . $retweets;
	$requestMethod = 'GET';

	$twitter = new TwitterAPIExchange($settings);
	$response = $twitter->setGetfield($getfield)
	                    ->buildOauth($url, $requestMethod)
	                    ->performRequest();

	// Parse JSON data
	$content = json_decode($response);

	foreach($content as $tweet) {
		$date = date('Y-m-d H:i:s', strtotime($tweet->created_at)); // convert to YYYY-MM-DD HH:MM:SS
		$text = $tweet->text;
		$id = $tweet->id_str;
		
		$fields[] = array(
			'date' => $date,
			'status' => $text, 
			'org_service' => 'Twitter',
			'org_id' => $id
		);
	}

	return array_reverse($fields);
 
}

// Save data to database
function statuses_saveData() {

	// Get array with new data
	$fields = statuses_getData();
	$count = sizeof($fields); // count items

	// Construct URL
	$url = BASEURL . "v1/statuses?count=" . $count . "&token=" . getToken(1);

	// Get existing data
	$response = array_reverse(doGetRequest($url));

	// Iterate through all new items and if their ID isn't found in the request with the latest data, add them
	for ($i=0; $i < 50; $i++) { 

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
