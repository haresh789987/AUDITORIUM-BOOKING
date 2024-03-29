<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');


?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>GNC
</title>

<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- bootstrap-css -->
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
<!--// bootstrap-css -->
<!-- css -->
<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
<!--// css -->
<!-- font-awesome icons -->
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- //font-awesome icons -->
<!-- font -->
<link href="//fonts.googleapis.com/css?family=Josefin+Sans:100,100i,300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,700italic,700,400italic,300italic,300' rel='stylesheet' type='text/css'>
<!-- //font -->
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(".scroll").click(function(event){		
			event.preventDefault();
			$('html,body').animate({scrollTop:$(this.hash).offset().top},1000);
		});
	});
</script> 
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<![endif]-->
</head>
<body>
	<!-- banner -->
	<div class="banner jarallax">
		<div class="agileinfo-dot">
			<?php include_once('includes/header.php');?>
			<div class="w3layouts-banner">
				<div class="container">
					<?php
include('includes/dbconnection.php');

$query = "SELECT tblbooking.*, tblservice.servicename FROM tblbooking
          LEFT JOIN tblservice ON tblbooking.ServiceId = tblservice.ID";
$result = $dbh->query($query);

$events = array();
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $statusColor = ($row['Status'] == 'Approved') ? 'lightgreen' : 'gray ';
    $startDateTime = new DateTime($row['BookingFrom']);
    $endDateTime = new DateTime($row['BookingTo']);

    // Format time as 'H:i:s' (hours:minutes:seconds)
    $startFormattedTime = $startDateTime->format('H:i:s');
    $endFormattedTime = $endDateTime->format('H:i:s');

    // Use these formatted times in your code as needed

    $events[] = array(
        'id' => $row['ID'], // Assuming you have an ID column in your table
        'start' => $row['BookingFrom'],
        'end' => $row['BookingTo'],
        'startFormattedTime' => $startFormattedTime, // Use formatted time here
        'endFormattedTime' => $endFormattedTime,     // Use formatted time here
        'status' => $row['Status'], 
        'statusColor' => $statusColor,
        'department' => $row['department'],
        'serviceName' => $row['servicename'], // Add service name to the events array
    );
}

// Close the PDO connection (not necessary if $dbh is a persistent connection)
$dbh = null;
?>

       
<main id="main-container">
    <div class="content">
        <div class="block">
            <div class="block-content block-content-full">
                <div class="form-group">
                    <label for="serviceSelect" style="color: white;">Select Auditorium:</label>
                    <select class="form-control" id="serviceSelect">
                        <option value="">All Auditorium</option>
                        <!-- Add service options dynamically based on your data -->
                        <?php
                        // Assuming you have an array of unique services
                        $uniqueServices = array_unique(array_column($events, 'serviceName'));
                        foreach ($uniqueServices as $service) {
                            echo "<option value='$service'>$service</option>";
                        }
                        ?>
                    </select>
                </div>
                <div id="calendar" style="background-color: white;"></div>
            </div>
        </div>
    </div>
</main>


    </div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Include FullCalendar -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />

<script src="assets/js/core/jquery.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="assets/js/core/jquery.slimscroll.min.js"></script>
<script src="assets/js/core/jquery.scrollLock.min.js"></script>
<script src="assets/js/core/jquery.appear.min.js"></script>
<script src="assets/js/core/jquery.countTo.min.js"></script>
<script src="assets/js/core/js.cookie.min.js"></script>
<script src="assets/js/codebase.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<script>
    var calendar; // Declare the calendar variable outside the document ready scope

    $(document).ready(function() {
        calendar = $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events: <?php echo json_encode($events); ?>,
            eventRender: function(event, element) {
                var statusColor = event.statusColor || 'gray';
                element.find('.fc-title').append('<br>' + event.startFormattedTime + ' to ' + event.endFormattedTime);
                element.find('.fc-title').append('<br>' + event.department);
                element.find('.fc-title').append('<br>' + event.serviceName); // Display service name
                element.css('background-color', statusColor);
            },
        });

        // Add department filter functionality
        $('#departmentSelect').on('change', function() {
            var selectedDepartment = $(this).val();
            calendar.fullCalendar('removeEvents');

            // Filter events based on the selected department
            var filteredEvents = <?php echo json_encode($events); ?>.filter(function(event) {
                return selectedDepartment === '' || event.department === selectedDepartment;
            });

            // Add filtered events to the calendar
            calendar.fullCalendar('addEventSource', filteredEvents);
        });
    });

    // Outside the document ready scope, you can access the calendar variable
    $('#serviceSelect').on('change', function() {
        var selectedService = $(this).val();
        calendar.fullCalendar('removeEvents');

        // Filter events based on the selected service
        var filteredEvents = <?php echo json_encode($events); ?>.filter(function(event) {
            return selectedService === '' || event.serviceName === selectedService;
        });

        // Add filtered events to the calendar
        calendar.fullCalendar('addEventSource', filteredEvents);
    });
</script>

<style>
    .fc-title {
        color: white !important;
    }
</style>


				</div>
			</div>
			<div class="w3ls-banner-info-bottom">
				<div class="container">
					<div class="banner-address">
						<?php
$sql="SELECT * from tblpage where PageType='contactus'";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $row)
{               ?>
						<div class="col-md-4 banner-address-left">
							<p><i class="fa fa-map-marker" aria-hidden="true"></i> <?php  echo htmlentities($row->PageDescription);?>.</p>
						</div>
						<div class="col-md-4 banner-address-left">
							<p><i class="fa fa-envelope" aria-hidden="true"></i> <?php  echo htmlentities($row->Email);?></p>
						</div>
						<div class="col-md-4 banner-address-left">
							<p><i class="fa fa-phone" aria-hidden="true"></i> +<?php  echo htmlentities($row->MobileNumber);?></p>
						</div>
						<div class="clearfix"> </div>
					<?php $cnt=$cnt+1;}} ?></div>
				</div>
			</div>

	
	<script src="js/jarallax.js"></script>
	<script src="js/SmoothScroll.min.js"></script>
	<script type="text/javascript">
		/* init Jarallax */
		$('.jarallax').jarallax({
			speed: 0.5,
			imgWidth: 1366,
			imgHeight: 768
		})
	</script>
	<!-- //jarallax -->
	<script src="js/SmoothScroll.min.js"></script>
	<script type="text/javascript" src="js/move-top.js"></script>
	<script type="text/javascript" src="js/easing.js"></script>
	<!-- here stars scrolling icon -->
	<script type="text/javascript">
		$(document).ready(function() {
			/*
				var defaults = {
				containerID: 'toTop', // fading element id
				containerHoverID: 'toTopHover', // fading element hover id
				scrollSpeed: 1200,
				easingType: 'linear' 
				};
			*/
								
			$().UItoTop({ easingType: 'easeOutQuart' });
								
			});
	</script>
<!-- //here ends scrolling icon -->
<script src="js/modernizr.custom.js"></script>

</body>	
</html>