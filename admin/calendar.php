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
<!DOCTYPE html>
<html lang="en" class="no-focus">
<head>
    <title>GNC</title>
    <link rel="stylesheet" href="assets/js/plugins/datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
</head>
<body>
    <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
        <?php include_once('includes/sidebar.php');?>
        <?php include_once('includes/header.php');?>
       
<main id="main-container">
    <div class="content">
        <div class="block">
            <div class="block-content block-content-full">
                <div class="form-group">
                    <label for="serviceSelect">Select Auditorium:</label>
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
                <div id="calendar"></div>
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
</body>
</html>
