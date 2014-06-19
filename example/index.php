<?php 

// Perform a GET request to a resource
function getRequest($url) {

	$ch = curl_init();
	curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url));
	$response = curl_exec($ch);
	curl_close($ch);

	return json_decode($response);

}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Personal API | Example</title>	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="style.css">
	<script src="jquery-1.11.0.min.js"></script>
	<script src="highcharts.js"></script>
	<script src="exporting.js"></script>
</head>
<body>

	<div id="content">

		<h1>Stefan's latest activities</h1>

		<div id="statuses">

			<h2>Recent status updates</h2>
			<?php 

			$statusesResource = 'http://api.stefangrund.de/v1/statuses?count=3&token=127542604153346df5d16cb';
			$statuses = getRequest($statusesResource);

			foreach($statuses as $status) {
				echo "<p>" . $status->status . "<br/>";
				echo '(<a href="http://twitter.com/eay/status/' . $status->org_id . '">' . $status->date . '</a>)</p>';
			}

			?>
		</div>

		<div id="steps">

			<h2>Last 10 day's steps</h2>

			<?php 

			$stepsResource = 'http://api.stefangrund.de/v1/steps?count=10&token=127542604153346df5d16cb';
			$steps = getRequest($stepsResource);

			foreach($steps as $step) {
				$arrSteps[] = intval($step->steps);
				$arrStepsDate[] = date("d.m.", strtotime($step->date));
			}

			?>

			<script type="text/javascript">
				$(function () {
			        $('#container').highcharts({
			            chart: {
			                type: 'column'
			            },
			            title: {
			                text: null
			            },
			            credits: {
						    enabled: false
						},
						legend: {
							enabled: false
						},
			       		xAxis: {
			                categories: <?php echo json_encode(array_reverse($arrStepsDate)); ?>
			            },
			            yAxis: {
			                min: 0,
			                title: {
			                    text: null
			                },
			                labels: {
						        formatter: function() {
						            return this.value;
						        }
						    }
			            },
			            tooltip: {
			                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
			                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
			                footerFormat: '</table>',
			                shared: true,
			                useHTML: true
			            },
			            plotOptions: {
			                column: {
			                    pointPadding: 0.1,
			                    borderWidth: 0
			                }
			            },
			            series: [{
			                name: 'Steps',
			                data: <?php echo json_encode(array_reverse($arrSteps)); ?>
			    
			            }]
			        });
			    });
			</script>

			<div id="container" style="min-width: 310px; height: 250px; margin: 0 auto"></div>

		</div>

		<div id="places">

			<h2>Last known location</h2>
			<?php 

			$placesResource = 'http://api.stefangrund.de/v1/places?count=1&token=127542604153346df5d16cb';
			$places = getRequest($placesResource);

			foreach($places as $place) {
				$placeDate = $place->date;
				$placeName = $place->place;
				$placeLng = $place->lng;
				$placeLat = $place->lat;
			}

			?>

			<p><?php echo $placeDate; ?> @ <b><?php echo $placeName; ?></b></p>



			<img src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $placeLat ?>,<?php echo $placeLng ?>&zoom=14&markers=color:red%7C<?php echo $placeLat ?>,<?php echo $placeLng ?>&size=400x400&sensor=false&key=AIzaSyDaCyAgxEtOfpJuEqNamz0E3O_rgXveRkI">

		</div>

		<footer>powered by <a href="http://personalapi.org/">Personal API</a></footer>
	
	</div>

</body>
</html>	
