<?php
/*
 *	Documentation
 *	Overview of the API's functionalities.
 */

$title = "Documentation";
require_once '../inc/config.php';
require_once 'auth_check.php'; // check if logged in
include '../inc/header.php';

$m = new Modules;
$modules = $m->getModules();

// Generate array with all used services
foreach ($modules as $module) {
	$settings = $m->getSettings($module);
	$services[] = $settings['service'];
}

?>

<p>While your API's <a href="../index.php">public front page</a> only explains how to read data, there are actually a lot more functions. Your Personal API is a full featured <abbr title="Representational State Transfer">REST</abbr>ful API, which enables you to create, read, update and delete data with simple HTTP requests. Here you'll find a full documentation for these functions and how to set up your API properly.</p>

<h2 id="modules">Configuring Modules</h2>

<p>After you've successfully installed your API, you'll need to <a href="modules.php">configure the modules</a> in order to get data from external services into the API's database. You'll find instructions for every service in the module selection.</p>

<h2 id="updating">Updating your Database</h2>

<p>After you've entered the required API keys, usernames, etc. into the modules' settings, you'll need to update your database. This will load your data from the external services and add it to your API's database. You can update your database <a href="update.php">manually by visiting this page</a> or automatically by creating a cron job for this URL:</p>

<pre><code><?php echo BASEURL . "update.php?token=" . getToken(1); ?></code></pre>

<p>Run this cron job daily or several times a day for best results.</p>

<h2 id="authentication">Authentication</h2>

<p>As you've learned on the front page, access tokens are required for any interaction with your API. You can <a href="tokens.php">create new tokens here</a> and give them different permissions but two tokens are already created after you've installed your API:</p>

<ul>
	<li>A <strong>public token</strong> (<code><?php echo getToken(2); ?></code>) which is displayed on the public front page and enables anyone to read data from your database.</li>
	<li>And a <strong>master token</strong> (<code><?php echo getToken(1); ?></code>) which enables you (and only you - keep it secret!) to perform any action on your database like creating, reading, updating and deleting data.</li>
</ul>

<p>Just append the <code>token</code> parameter to your requested URL like this (otherwise you'll get an response with HTTP status code 401 "Unauthorized"):</p>

<pre><code><?php echo BASEURL . "v1/" . $modules[0] . "?token=" . getToken(2); ?></code></pre>

<p>You can see the number of API requests of each token on the <a href="tokens.php">token page</a> under "Usage".</p>

<h2 id="resources">Resources / URL Design</h2>

<p>The API is modeled around the different resources. A resource is a data type controlled by a module. Right now <strong><?php echo readableList($modules); ?></strong> are available in your API. Every resource has two types of URLs:</p>

<pre><code><?php echo BASEURL . "v1/" . $modules[0] ?></code></pre>

<pre><code><?php echo BASEURL . "v1/" . $modules[0] . "/1234" ?></code></pre>

<p>The first URL represents the whole collection, the second one is specific for one element in this collection. Therefore <code>/<?php echo $modules[0] . "/1234"; ?></code> represents the 1234th element in the resource/collection <?php echo ucfirst($modules[0]); ?>.</p>

<h2 id="requests">Requests</h2>

<p>To operate on the resources you can use the HTTP verbs POST, GET, PUT and DELETE which match the four <abbr title="Create Read Update Delete">CRUD</abbr> operations (Create, Read, Update, Delete). Not every request method is allowed with every resource, e.g. you can't delete a whole collection for security reasons. Use the request methods like this:</p>

<table summary="Requests" width="100%" id="requestsTable">
	<thead>
		<tr>
			<th width="20%"><strong>Resource</strong></th>
			<th width="20%"><strong>POST</strong></th>
			<th width="20%"><strong>GET</strong></th>
			<th width="20%"><strong>PUT</strong></th>
			<th width="20%"><strong>DELETE</strong></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>/statuses</td>
			<td>Creates new element</td>
			<td>Shows<br/>all elements</td>
			<td>-</td>
			<td>-</td>
		</tr>
		<tr>
			<td>/statuses/123</td>
			<td>-</td>
			<td>Shows<br/>element 123</td>
			<td>Updates<br/>element 123</td>
			<td>Deletes<br/>element 123</td>
		</tr>
	</tbody>
</table>

<h2 id="errors">Error Handling</h2>

<p>If there is an error or unauthorized behaviour the API will answer with a following status codes and a error message. These status codes are supported:</p>

<table summary="Error Codes" width="100%">
	<thead>
		<tr>
			<th width="15%"><strong>Code</strong></th>
			<th width="30%"><strong>Message</strong></th>
			<th width="55%"><strong>Description</strong></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>200</td>
			<td>OK</td>
			<td>Will be sent with every working request.</td>
		</tr>
		<tr>
			<td>201</td>
			<td>Created</td>
			<td>Resource successfully created.</td>
		</tr>
		<tr>
			<td>304</td>
			<td>Not Modified</td>
			<td>Resource couldn't be updated.</td>
		</tr>
		<tr>
			<td>400</td>
			<td>Bad Request</td>
			<td>Something is wrong with the request.</td>
		</tr>
		<tr>
			<td>401</td>
			<td>Unauthorized</td>
			<td>No permission for this action.</td>
		</tr>
		<tr>
			<td>404</td>
			<td>Not found</td>
			<td>Resource couldn't be found.</td>
		</tr>
		<tr>
			<td>500</td>
			<td>Internal Server Error</td>
			<td>Something wrent wrong within the API.</td>
		</tr>
	</tbody>
</table>

<p>The API error message response will look like this:</p>

<pre><code>{
     "code": 401,
     "message": "Unauthorized",
     "description": "Your token is missing or not valid."
}</code></pre>


<?php include '../inc/footer.php'; ?>
