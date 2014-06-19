<?php
/*
 *	Save Module Settings
 *	Saves the user's input to the module table in the database.	
 */

require_once '../inc/config.php';

$m = new Modules;
$modules = $m->getModules();

$module = $_POST['name'];

$settings = $m->getSettings($module);
$forms = $settings['forms'];

// Get entered options
for ($i=0; $i < sizeof($forms); $i++) { 

	$parameterName = $module . "_" . $i;
	$parameter = $_POST[$parameterName];

	if($parameter != NULL) {
		$saveData[$module][] = $parameter;
	}

}

// Save data as JSON string to the database
$data = $saveData[$module];
$dataStr = json_encode($data);

$m->setOptions($module, $dataStr);

// Redirect to Admin Module page
header("location: ../admin/modules.php?saved=" . $module);
exit;

?>
