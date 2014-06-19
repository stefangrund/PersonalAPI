<?php 
/*
 *	Personal API Index
 *	This is the public index and documentation of your Personal API.
 */

$title = NULL;
require_once 'inc/config.php';
include 'inc/header.php';

?>

<?php if (tableExists() == false) : ?>

	<?php require_once 'inc/install_process.php'; ?>

<?php else : ?>

	<?php

	$m = new Modules;
	$modules = $m->getModules();

	// Generate array with all used services
	foreach ($modules as $module) {
		$settings = $m->getSettings($module);
		$services[] = $settings['service'];
	}

	?>

	<p>Welcome. This <abbr title="Application Programming Interface">API</abbr> collects social media and quantified self data by <?php echo getName(); ?>. Right now there are <?php echo sizeof($modules); ?> endpoints available to interact with data from <?php echo readableList($services); ?>. Here is a short documentation of the public API calls. (If you're logged in, you can view the <a href="admin/docs.php">full documentation here</a>.)</p>

	<h2>Authentication</h2>

	<p>While the API supports all <abbr title="Create Read Update Delete">CRUD</abbr> operations, only reading data is allowed for the public. You'll need to add the public access token <code><?php echo getToken(2); ?></code> to your request in order to perform it, like this:</p>

	<pre><code><?php echo BASEURL . "v1/" . $modules[0] . "?token=" . getToken(2); ?></code></pre>

	<h2>Resources and Parameters</h2>

	<p>You can call the following resources. Use the parameter <code>date=YYYY-MM-DD</code> to limit the timespan of your request. Use <code>format</code> to determine the format of the response (you can choose between the default <code>json</code> or <code>xml</code>) and <code>count</code> to determine the number of items in the response (default is 25, maximum is 200).</p>

	<?php foreach ($modules as $module) {

		$settings = $m->getSettings($module);
		$samplefile = "modules/" . $module . "/" . $module . "_sample.json";
		
		echo "<p><strong>/v1/" . $module . "</strong> – My " . $settings['description'] . ".</p>";
		echo "<pre><code>";
		include $samplefile;
		echo "</code></pre>";

	} ?>

	<h2>Example API Calls</h2>

	<p>A complete GET request for <strong>/v1/<?php echo $modules[1]; ?></strong> with all parameters will look like this:</p>

	<pre><code><?php echo BASEURL . "v1/" . $modules[1] . "?date=" . date('Y-m-d') . "&amp;count=10&amp;format=xml&amp;token=" . getToken(2); ?></code></pre>

	<p>You can also request single items from a resource. Just add <strong>/id</strong> to a call:</p>

	<pre><code><?php echo BASEURL . "v1/" . end($modules) . "/35?token=" . getToken(2); ?></code></pre>

<?php endif; ?>

<?php include 'inc/footer.php'; ?>
