<!DOCTYPE html>
<html>
	<head>
		<title>Countdown</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		 
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker.min.css" rel="stylesheet">
		
		<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
		
		<script type="text/javascript">
			
			$(document).ready(function(e)
			{
				// initalize datepicker (https://github.com/eternicode/bootstrap-datepicker)
				$('#datepicker').datepicker(
				{
					orientation: "top auto",
					autoclose: true
				});
			});
			
		</script>
		
	</head>
	<body>
		
		<div class="container" style="margin-top: 50px">
		
			<?php
			
				// If the submit button has been pressed
				if(isset($_POST['submit']))
				{
					// get month, day and year from form field (mm/dd/yyyy)
					$date = explode('/', $_POST['date']);
					$month = $date[0];
					$day = $date[1];
					$year = $date[2];
					
					// Get future seconds (hour, minute, second, month, day, year);
					$future = mktime(00, 00, 00, $month, $day, $year);
					
					// Get current seconds since Unix Epock (midnight on January 1, 1970)
					$current = time();
					
					// Calculate difference of seconds
					$difference = $future - $current;
					
					// Calculate number of days
					$numdays = ceil($difference / 86400);
					
					// Check if the event is occurring today
					if($numdays > 0)
					{
						echo "<h1>Only $numdays days until {$_POST['event']}!</h1>";
					} elseif ($numdays == 0)  {
						echo "<h1>{$_POST['event']} is today!</h1>";
					} else {
						echo "<h1>{$_POST['event']} already happened!</h1>";	
					}
					
				}
	
			?>
				
			<!-- Countdown Form -->
			<form method="post" action="index.php">
			
				<div class="form-group">
					<label>Event Name:</label>
					<input name="event" type="text" class="form-control" />
				</div>
			    
			    <div class="form-group">
				    <label>Date of Event:</label>
				    <input id="datepicker" name="date" type="text" class="form-control" />
			    </div>
	
			    <input name="submit" type="submit" value="Calculate Days" class="btn btn-primary" />
			
			</form>
		
		</div>

	</body>
</html>