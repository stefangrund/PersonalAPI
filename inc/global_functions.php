<?php
/*
 *	Global functions
 *	Functions that are used everywhere, e.g. to log events, do requests or check tokens.
 */

require_once 'config.php';

/* // Display errors wihle conneting to the database
function catchError($e, $case) {
	if($case == 1) {
		echo "<h1>Could not connect to the database.</h1>";
	}
	elseif ($case == 2) {
		echo "<strong>Error while connecting to the database.</strong><br/>";
	}
	echo $e->getMessage(); // Remove in production code, un-comment to display errors
	exit;
} */

// Check if table 'papi_user' and first user exists
function tableExists() {

	try {
		$database = new Database();
		$database->query("SELECT 1 FROM papi_user LIMIT 1;");
		$database->execute();
		return true;
	}
	catch (Exception $e) {
		return false;
	}

}

// Check if date format is correct
function validateDate($date, $format = 'Y-m-d H:i:s') {

    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;

}

// Write log to table 'papi_log'
function logAction($filepath, $function, $action) {

	$date = date("Y-m-d H:i:s");
	$file = basename($filepath);

	$database = new Database();
	$database->query("INSERT INTO papi_log (date, file, function, action) VALUES(:date, :file, :function, :action);");
	$database->bind(':date', $date);
	$database->bind(':file', $file);
	$database->bind(':function', $function);
	$database->bind(':action', $action);
	$database->execute();

}


// Save password as hash to database
// based on http://alias.io/2010/01/store-passwords-safely-with-php-and-mysql/
function encrypt($password) {

	// A higher "cost" is more secure but consumes more processing power
	$cost = 10;

	// Create a random salt
	$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');

	// Prefix information about the hash so PHP knows how to verify it later.
	// "$2a$" means we're using the Blowfish algorithm. The following two digits are the cost parameter.
	$salt = sprintf("$2a$%02d$", $cost) . $salt;

	// Hash the password with the salt
	$hash = crypt($password, $salt);

	return $hash;

}

// Get 'display_name' form 'papi_user'
// option '1' (= name) or '2' (= url)
function getNameOrURL($option) {

	if (tableExists() == false) {
		return NULL;
	}
	else {

		$database = new Database();
		$database->query("SELECT display_name, display_url FROM papi_user LIMIT 1;");
		$row = $database->single();

		if($option == 1) { 
			return $row['display_name'];
		}
		elseif($option == 2) { 
			return $row['display_url'];
		}

	}
}

function getName() {
	return getNameOrURL(1);
}

function getURL() {
	return getNameOrURL(2);
}

// Get token by id; used to display master or public token
function getToken($id) {

	$database = new Database();
	$database->query("SELECT token FROM papi_tokens WHERE id = :id LIMIT 1;");
	$database->bind(':id', $id);
	$result = $database->single();

	return $result['token'];

}

// Get token author = user of this token
function getTokenAuthor($token) {

	$database = new Database();
	$database->query("SELECT author FROM papi_tokens WHERE token = :token LIMIT 1;");
	$database->bind(':token', $token);
	$result = $database->single();

	return $result['author'];

}

// Add 1 to request count of a token
function addTokenRequest($token) {

	$database = new Database();
	$database->query("UPDATE papi_tokens SET requests = IFNULL(requests, 0)+1 WHERE token = :token;");
	$database->bind(':token', $token);
	$database->execute();

}

// Perform a GET request to resource with JSON response
function doGetRequest($url) {

	$ch = curl_init();
	curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url));

	$response = curl_exec($ch);
	curl_close($ch);

	return json_decode($response, true); // returns JSON object as array

}

// Perform a POST request
function doPostRequest($url, $parameters) {

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));

	$response = curl_exec($ch);
	curl_close($ch);

	return json_decode($response, true); // returns JSON object as array

}

// Search in multidimensional array
function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

// Convert array to readable list (add "and" to the last item)
function readableList($array) {

	sort($array);
	$list = join(' and ', array_filter(array_merge(array(join(', ', array_slice($array, 0, -1))), array_slice($array, -1))));

	return $list;

}

?>
