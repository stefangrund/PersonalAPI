<?php
/*
 *	API Functions
 *	Functions for the different requests, the response and error handling.
 */

// Returns JSON/XML response
function output($format, $array) {

	// Convert numeric values (up to six-figure numbers) to integers
	// dumped for speed reason...
	/*for ($i=0; $i < sizeof($array); $i++) { 

		$array[$i] = array_map(function($var) {
			return strlen($var) <= 6 && is_numeric($var) ? (int)$var : $var;
		}, $array[$i]);

	}*/

	// JSON as default format
	if($format == NULL) $format = 'json';

	// Select format and return response
	switch ($format) {
		case 'json':
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($array);
			break;
		case 'xml':
			header('Content-Type: application/xml; charset=utf-8');
			$xml = new Array2xml('response');
			$xml->createNode($array);
			echo $xml;
			break;
	}

	exit;

}

// Returns HTTP response with JSON/XML body
// for status code definitions see: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
function outputError($format, $code, $description) {

	switch ($code) {
		case 200:
			$message = "OK";
			break;
		case 201:
			$message = "Created";
			break;
		case 304:
			$message = "Not Modified";
			break;
		case 400:
			$message = "Bad Request";
			break;
		case 401:
			$message = "Unauthorized";
			break;
		case 404:
			$message = "Not Found";
			break;
		case 500:
			$message = "Internal Server Error";
			break;
	}

	http_response_code($code);
	$body = array('code' => $code, 'message' => $message, 'description' => $description);
	//throw new Exception($description); // debug; remove from production code
	output($format, $body);

}

// Check by token if someone is allowed to use a method or endpoint
function checkToken($token) {

	$arrToken = array();

	$database = new Database();
	$database->query("SELECT * FROM papi_tokens WHERE token = :token LIMIT 1;");
	$database->bind(':token', $token);
	$query = $database->single();

	if($token == $query['token']) { $arrToken[] = true; } else { $arrToken[] = false; }

	// Check CRUD permissions
	if($query['p_create'] == true) { $arrToken[] = true; } else { $arrToken[] = false; }
	if($query['p_read']   == true) { $arrToken[] = true; } else { $arrToken[] = false; }
	if($query['p_update'] == true) { $arrToken[] = true; } else { $arrToken[] = false; }
	if($query['p_delete'] == true) { $arrToken[] = true; } else { $arrToken[] = false; }

	return $arrToken;

}

// Check if parameter's data type is correct
function checkDatatype($datatype, $parameter) {

	$typeCheck = false;

	if(strpos($datatype, "datetime") !== false AND validateDate($parameter) === true) $typeCheck = true;
	if(strpos($datatype, "int") !== false AND is_numeric($parameter) === true) $typeCheck = true;
	if(strpos($datatype, "decimal") !== false AND is_numeric($parameter) === true) $typeCheck = true;
	if(strpos($datatype, "text") !== false AND is_string($parameter) === true) $typeCheck = true;
	if(strpos($datatype, "varchar") !== false AND is_string($parameter) === true) $typeCheck = true;

	if($typeCheck != true) {
		outputError($format, 400, "Wrong data type in one of your parameters.");
	}

}

// Check if parameter's column is nullable
function checkNullable($nullable, $parameter) {

	if(empty($parameter) AND $nullable == 'NO') {
		outputError($format, 400, "Parameter is not allowed to be NULL.");
	}

}

// Get column names from a specified table
function getTableColumns($type) {

	$database = new Database();
	$database->query("SELECT column_name, column_type, is_nullable FROM information_schema.columns WHERE table_name=:table;");
	$database->bind(':table', 'papi_type_' . $type);
	$database->execute();
	$columns = $database->all();

	foreach ($columns as $column)
	{
		$arrColumns[] = array('name' => $column['column_name'], 'datatype' => $column['column_type'], 'nullable' => $column['is_nullable'], 'value' => NULL);
	}

	return $arrColumns;

}


// Answers and execution for the different request methods
// All CRUD opertions are supported (through POST, GET, PUT, DELETE)

// Create
function requestPOST($request, $type) {

	$resource = explode('/', $request['url']);
	$format = $request['format'];
	$arrColumns = array();
	$arrColumnsBind = array();
	$arrValues = array();

	$columns = getTableColumns($type); // Get array with column name, datatype and is_nullable

	// Get parameter values via column names
	for ($i=1; $i < sizeof($columns); $i++) { 

		$name = $columns[$i]['name']; // id, date, ...
		$datatype = $columns[$i]['datatype']; // datetime, int, varchar, text...
		$nullable = $columns[$i]['nullable']; // YES or NO
		$parameter = $_POST[$name];

		if($parameter != NULL) {

			checkNullable($nullable, $parameter);
			checkDatatype($datatype, $parameter); 

			// Save column names and parameter values in arrays
			$arrColumns[] = $name;
			$arrColumnsBind[] = ":" . $name;
			$arrValues[] = $parameter;

		}

	}

	// Construct SQL query from these arrays
	$strColumns = implode(", ", $arrColumns);
	$strColumnsBind = implode(", ", $arrColumnsBind);
	$strValues = implode(", ", $arrValues);
	$sql = "INSERT INTO papi_type_" . $type . " (" . $strColumns . ") VALUES(" . $strColumnsBind . ");";

	// Create new element if only one resource is selected
	if(sizeof($resource) == 1) {

		$database = new Database();
		$database->query($sql);
		for ($i=0; $i < sizeof($arrColumns); $i++) { 
			$database->bind($arrColumnsBind[$i], $arrValues[$i]);
		}
		$database->execute();

		$action = "Element '" . $resource[0] . "/" . $database->lastInsertId() . "' successfully created.";
		outputError($format, 201, $action);
		logAction(__FILE__, __FUNCTION__, $action);

	}
	else {
		outputError($format, 400, "You can't assign an id for new elements. Use '" . BASEURL . "v1/" . $resource[0] . "' to create new elements instead.");		
	}

}

// Read
function requestGET($request, $type) {

	$resource = explode('/', $request['url']);
	$format = $request['format'];
	$date = $request['date'];
	$order = $request['order'];
	$count = $request['count'];

	// Do query based on number of resources
	if(sizeof($resource) == 1) {

		// Set order
		if($order == 'ASC' OR $order == 'asc') {
			$order = 'ASC';
		}
		else {
			$order = 'DESC';
		}

		// Set number of items to display
		if($count == NULL OR $count > 200) {
			$count = 25;
		}

		// Query with parameters or not
		if(!empty($date) AND validateDate($date, 'Y-m-d') === true) {
			$sql = "SELECT * FROM papi_type_" . $type . " WHERE date BETWEEN '" . $date . " 00:00:00' AND '" . $date . " 23:59:59' ORDER BY date " . $order . ";";
		} else {
			$sql = "SELECT * FROM papi_type_" . $type . " ORDER BY id " . $order . " LIMIT " . $count .";";
		}

		$database = new Database();
		$database->query($sql);
		$database->execute();
		$result = $database->all();

	}
	elseif(sizeof($resource) == 2) {

		$sql = "SELECT * FROM papi_type_" . $type . " WHERE id = :identifier LIMIT 1;";

		$database = new Database();
		$database->query($sql);
		$database->bind(':identifier', $resource[1]);
		$database->execute();
		$result = $database->all();

	}
	elseif(sizeof($resource) > 2) {
		outputError($format, 404, "This collection or element doesn't exist.");
	}

	// Display result or not
	if(!empty($result)) {
		output($format, $result);
	}
	else {
		outputError($format, 404, "This collection or element doesn't exist.");
	}

}

// Update
function requestPUT($request, $type) {

	$_PUT = array(); // create a pseudo-superglobal for the PUT parameters
	parse_str(file_get_contents('php://input'), $_PUT);

	$resource = explode('/', $request['url']);
	$format = $request['format'];
	$arrQuery = array();

	// Error handling
	if(sizeof($resource) != 2) outputError($format, 304, "You can't update a whole collection.");
	elseif(empty($_PUT)) outputError($format, 304, "No PUT parameters sent.");

	$columns = getTableColumns($type); // Get array with column name, datatype and is_nullable

	// Get parameter values via column names
	for ($i=1; $i < sizeof($columns); $i++) { 

		$name = $columns[$i]['name']; // id, date, ...
		$datatype = $columns[$i]['datatype']; // datetime, int, varchar, text...
		$nullable = $columns[$i]['nullable']; // YES or NO
		$parameter = $_PUT[$name];

		if($parameter != NULL) {

			checkDatatype($datatype, $parameter);

			// Save SET clauses in array
			$strQuery = $name . "='" . $parameter . "'";
			$arrQuery[] = $strQuery;

		}

	}

	// Construct SQL query from array
	$updates = implode(", ", $arrQuery);
	$sql = "UPDATE papi_type_" . $type . " SET " . $updates . " WHERE id = :identifier;";

	$database = new Database();
	$database->query($sql);
	$database->bind(':identifier', $resource[1]);
	$database->execute();

	outputError($format, 200, "Element '" . $resource[0] . "/" . $resource[1] . "' successfully updated.");
	logAction(__FILE__, __FUNCTION__, "Updated '" . $request['url'] . "' with token '" . $request['token'] . "'.");

}

// Delete
function requestDELETE($request, $type) {

	$resource = explode('/', $request['url']);
	$format = $request['format'];

	if(sizeof($resource) == 2) {

		$sql = "DELETE FROM papi_type_" . $type . " WHERE id = :identifier;";

		$database = new Database();
		$database->query($sql);
		$database->bind(':identifier', $resource[1]);
		$database->execute();

		outputError($format, 200, "Item '" . $resource[1] . "' deleted successfully.");
		logAction(__FILE__, __FUNCTION__, "Deleted '" . $request['url'] . "' with token '" . $request['token'] . "'.");

	}
	else {
		outputError($format, 400, "You can't delete a whole collection.");
	}

}

?>
