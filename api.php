<?php 
/*
 *	API Controller
 *	Identifies the request method and checks if the used token is valid
 */

require_once 'inc/config.php';
require_once 'inc/api_functions.php';

$request = $_GET;
$method = $_SERVER['REQUEST_METHOD'];
$format = $request['format'];
$token = $request['token'];

// Check if Personal API is installed
if (tableExists() == true) {

	// Check if token exists
	if($token == NULL) $token = 'NO_TOKEN';
	$arrToken = checkToken($token);
	$tokenAuthor = getTokenAuthor($token);

	if($arrToken[0] == true) {

		addTokenRequest($token); // +1 to the token's request count

		$resource = explode('/', $request['url']);
		$type = $resource[0]; // selected collection, e.g. '/steps'

		try {

			// call function based on request method
			switch ($method) {
				case 'POST':
					if($arrToken[1] != true) outputError($format, 400, "No permission to create elements.");
					if(!empty($_POST)) {
						requestPOST($request, $type);
					}
					else {
						outputError($format, 400, "No POST parameters sent.");
					} 
					break;
				case 'GET':
					if($arrToken[2] != true) outputError($format, 400, "No permission to read elements.");
					requestGET($request, $type);
					break;
				case 'PUT':
					if($arrToken[3] != true) outputError($format, 400, "No permission to update elements.");
					requestPUT($request, $type);
					break;
				case 'DELETE':
					if($arrToken[4] != true) outputError($format, 400, "No permission to delete elements.");
					requestDELETE($request, $type); 
					break;
				default:
					if($arrToken[2] != true) outputError($format, 400, "No permission to read elements.");
					requestGET($request, $type);
					break;
			}

		}
		catch (Exception $e) {
			echo $e->getMessage();
			//outputError($format, 400, "Invalid resource.");
			logAction(__FILE__, NULL, "Request of invalid resource ('" . $resource . "').");
		}

	}
	else {

		outputError($format, 401, "Your token is missing or not valid.");
		logAction(__FILE__, NULL, "Request with invalid token '" . $token . "' denied.");

	}

}
else {
	outputError($format, 500, "Personal API not yet installed.");
}

?>
