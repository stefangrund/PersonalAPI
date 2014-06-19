<?php
/*
 *	Modules Admin Page
 *	Allows the user configurate the modules.
 */

$title = "Modules";
require_once '../inc/config.php';
require_once 'auth_check.php'; // check if logged in
include '../inc/header.php';
?>

<p>Modules are responsible for any interaction with external services. They fetch your data and add it to this database. For a fully functional API you'll need to set them up here first. Just select a module, follow the displayed instructions and enter the required information (e.g. API keys or usernames). Select a module to display it's configuration details.</p>

<?php if(isset($_GET['saved'])) {
	echo '<div id="tip" class="saved">Changes in "' . ucfirst($_GET['saved']) . '" saved.</div>';
} ?>

<h2>Installed Modules</h2>

<?php 
$m = new Modules;
$modules = $m->getModules();

foreach ($modules as $module) {

	// If module isn't found in the database, add it
	if($m->moduleExists($module) == false) {
		$m->moduleAdd($module);
	}

	// Get settings and saved options of a module
	$options = $m->getOptions($module);
	$settings = $m->getSettings($module);
	$forms = $settings['forms'];

	// Decode saved options (they are saved as a JSON string in the database)
	$opt = json_decode($options);

	// Display module name, description and help
	echo '<div class="module ' . $module . '">';
	echo '<div class="module-select"><a href="#" onclick="display(\'' . $module . '\');"><div class="icon-module index-icon" style="font-size: 40px; margin: 2px 15px 10px 0;"></div><strong>' . ucfirst($module) . ' via ' . $settings['service'] . '</strong></a><br/>My ' . $settings['description'] . '.</div>';
	//echo '<div class="config">';
	if($_GET['saved'] == $module) {
		echo '<div class="config">';
	}
	else {
		echo '<div class="config" style="display: none;">'; 
	}
	echo '<div class="instructions"><strong>Instructions:</strong> ' . $settings['help'] . '</div>';

	// Display forms
	echo "<form action='modules_save.php' method='POST'>";
	echo "<input type='hidden' name='name' value='" . $module . "' />";
	for ($i=0; $i < sizeof($forms); $i++) { 
		
		echo "<label>" . $forms[$i] . ":</label> <input type='text' name='" . $module . "_" . $i . "' value='" . $opt[$i] . "' /><br/>";

	}

	// Display authentication function
	$authfunc = $module . "_authenticate";
	if(function_exists($authfunc)) {
		$authfunc();
	}

	// Save it
	echo '<input type="submit" value="Save" />';
	echo "</form>";
	echo "</div>";
	echo "</div>";

}

?>

<h2>Installing New Modules</h2>

<p>Installing new modules is easy: Just upload the new module's folder to  <strong>/modules</strong> in your Personal API directory. Then visit this page, set it up and you're done. The Personal API keeps track of the database setup for you.</p>

<?php include '../inc/footer.php'; ?>
