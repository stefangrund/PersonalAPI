<?php
/*
 *	Class: Modules
 *	Detects modules and manages all functions which interact with them.
 */

class Modules {

	private $modules;

	// When called find all modules
	public function __construct() {

		// Save modules' path & filename to array
		$this->modules = glob(ROOT . 'modules/*/*_def.php');

		// Include all module files
		foreach($this->modules as $module)
		{
			require_once $module;
		}

	}

	/* --- Genereal functions --- */

	// Return array with installed modules
	public function getModules() {

		$arrModules = array();

		foreach($this->modules as $module)
		{
			// Construct function name
			$arrModules[] = basename($module, "_def.php");
		}

		return $arrModules;

	}

	// Return array with all table definitons
	public function getTableDefintions() {

		$arrTables = array();

		foreach($this->modules as $module)
		{
			// Construct function name
			$table = basename($module, "_def.php") . '_table';

			if(function_exists($table)) {
    			$arrTables[] = $table(); // this is a variable function
			}
		}

		return $arrTables;

	}

	// Run specific job or all jobs
	public function runSaveJobs($name = NULL) {

			foreach($this->modules as $module)
			{
				// Construct function name
				$saveData = basename($module, "_def.php") . '_saveData';

				if(function_exists($saveData)) {
	    			
					try {
						echo "Updating " . ucfirst(basename($module, "_def.php")) . "... ";
						$arrTables[] = $saveData();
					}
					catch (Exception $e) {
						echo "Can't update " . ucfirst(basename($module, "_def.php")) . ": " . $e->getMessage();
					}

				}

				echo "<br/>";
			}

	}

	/* --- Functions for module's settings --- */

	// Returns array with settings for a specific module
	public function getSettings($name) {

		// Construct function name
			$settings = $name . '_settings';

		if(function_exists($settings)) {
    		return $settings();
		}

	}

	// Check if there is a database entry for a module
	public function moduleExists($name) {

		$database = new Database();
		$database->query("SELECT * FROM papi_modules WHERE name = :name LIMIT 1;");
		$database->bind(':name', $name);
		$options = $database->single();

		return $options;

	}

	// Adds a database entry for a module
	public function moduleAdd($name) {

		$database = new Database();
		$database->query("INSERT INTO papi_modules (name) VALUES(:name);");
		$database->bind(':name', $name);
		$database->execute();

	}

	// Retrieve saved options for module
	public function getOptions($name) {

		$database = new Database();
		$database->query("SELECT data FROM papi_modules WHERE name = :name LIMIT 1;");
		$database->bind(':name', $name);
		$options = $database->single();

		return $options['data'];

	}

	// Save options for module
	public function setOptions($name, $options) {

		$database = new Database();
		$database->query("UPDATE papi_modules SET data = :data WHERE name = :name");
		$database->bind(':data', $options);
		$database->bind(':name', $name);
		$database->execute();

	}

}

?>
