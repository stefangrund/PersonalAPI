<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Personal API <?php if(getName() != NULL) if($title == NULL) { echo "of " . getName(); } else { echo " | " . $title; } ?></title>	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="<?php echo BASEURL; ?>css/style.css">
	<link rel="stylesheet" href="<?php echo BASEURL; ?>css/highlight.css">
	<script src="<?php echo BASEURL; ?>js/jquery-1.11.0.min.js"></script>
	<script src="<?php echo BASEURL; ?>js/highlight.pack.js"></script>
	<script>hljs.initHighlightingOnLoad();</script>
	<script src="<?php echo BASEURL; ?>js/script.js"></script>
</head>
<body>

	<div id="header">
		<a href="<?php echo BASEURL; ?>">
			<img src="<?php echo BASEURL; ?>img/logo/blue.svg" onerror="this.onerror=null; this.src='<?php echo BASEURL; ?>img/logo/blue.png'"> 
		</a>
	</div>

	<div id="content">

		<?php if($title == NULL) {
			echo '<div style="float: right"><a href="' . BASEURL . 'admin">Admin</a></div>';
		} ?>

		<section>
			<header>
				<h1>
					<a href="<?php echo BASEURL; ?>">Personal API</a> 
					<?php if(getName() != NULL) {
						if(strpos($_SERVER['REQUEST_URI'],'admin') !== false) { echo ' / <a href="' . BASEURL . 'admin/">Admin</a> '; }
						
						if($title == NULL) {
							echo 'of <a href="' . getURL() . '">' . getName() . '</a>';
						}
						else {
							echo '/ ' . $title;
						}
					} ?>
				</h1>
			</header>
		</section>
