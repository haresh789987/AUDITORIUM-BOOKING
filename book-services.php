<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['obbsuid']) == 0 || !isset($_GET['bookid'])) {
    header('location:logout.php');
} else {
    $bid = $_GET['bookid'];
    $uid = $_SESSION['obbsuid'];

    if (isset($_POST['submit'])) {
       
    
        $bookingfrom_date = htmlentities($_POST['bookingfrom']);
        $bookingfrom_time = htmlentities($_POST['bookingfrom_time']);
        $bookingfrom_ampm = htmlentities($_POST['bookingfrom_ampm']);
        $bookingto_date = htmlentities($_POST['bookingto']);
        $bookingto_time = htmlentities($_POST['bookingto_time']);
        $bookingto_ampm = htmlentities($_POST['bookingto_ampm']);

        $bookingfrom = $bookingfrom_date . ' ' . $bookingfrom_time . ' ' . $bookingfrom_ampm;
        $bookingto = $bookingto_date . ' ' . $bookingto_time . ' ' . $bookingto_ampm;

        $eventtype = htmlentities($_POST['eventtype']);
        $nop = htmlentities($_POST['nop']);
        $message = htmlentities($_POST['message']);
        $bookingid = mt_rand(100000000, 999999999);

        // Check if the selected time slot is available
        $checkAvailabilitySQL = "SELECT * FROM tblbooking WHERE ServiceID = :bid
                                AND (
                                    (BookingFrom >= :bookingfrom AND BookingFrom < :bookingto)
                                    OR
                                    (BookingTo > :bookingfrom AND BookingTo <= :bookingto)
                                    OR
                                    (BookingFrom <= :bookingfrom AND BookingTo >= :bookingto)
                                )";
        $checkAvailabilityQuery = $dbh->prepare($checkAvailabilitySQL);
        $checkAvailabilityQuery->bindParam(':bid', $bid, PDO::PARAM_STR);
        $checkAvailabilityQuery->bindParam(':bookingfrom', $bookingfrom, PDO::PARAM_STR);
        $checkAvailabilityQuery->bindParam(':bookingto', $bookingto, PDO::PARAM_STR);
        $checkAvailabilityQuery->execute();
        $availabilityResult = $checkAvailabilityQuery->fetchAll(PDO::FETCH_ASSOC);

        if (count($availabilityResult) > 0) {
            echo '<script>alert("This time slot is already booked. Please choose a different time.")</script>';
        } else {
            
                  $fetchDepartmentSQL = "SELECT department FROM tbluser WHERE ID = :uid";
    $fetchDepartmentQuery = $dbh->prepare($fetchDepartmentSQL);
    $fetchDepartmentQuery->bindParam(':uid', $uid, PDO::PARAM_STR);
    $fetchDepartmentQuery->execute();
    $userResult = $fetchDepartmentQuery->fetch(PDO::FETCH_ASSOC);
 
        if ($userResult && isset($userResult['department'])) {
        $department = $userResult['department'];
        }

             $sql = "INSERT INTO tblbooking(BookingID, ServiceID, UserID, BookingFrom, BookingTo, EventType, Numberofguest, Message, department) 
        VALUES(:bookingid, :bid, :uid, :bookingfrom, :bookingto, :eventtype, :nop, :message, :department)";
$query = $dbh->prepare($sql);

$query->bindParam(':bookingid', $bookingid, PDO::PARAM_STR);
$query->bindParam(':bid', $bid, PDO::PARAM_STR);
$query->bindParam(':uid', $uid, PDO::PARAM_STR);
$query->bindParam(':bookingfrom', $bookingfrom, PDO::PARAM_STR);
$query->bindParam(':bookingto', $bookingto, PDO::PARAM_STR);
$query->bindParam(':eventtype', $eventtype, PDO::PARAM_STR);
$query->bindParam(':nop', $nop, PDO::PARAM_STR);
$query->bindParam(':message', $message, PDO::PARAM_STR);
 $query->bindParam(':department', $department, PDO::PARAM_STR);
            try {
                $query->execute();
                $LastInsertId = $dbh->lastInsertId();

                if ($LastInsertId > 0) {
                    echo '<script>alert("Your Booking Request Has Been Sent. We Will Contact You Soon")</script>';
                    echo "<script>window.location.href ='services.php'</script>";
                    exit;
                } else {
                    echo '<script>alert("Something Went Wrong. Please try again")</script>';
                }
            } catch (PDOException $e) {
                // Handle database errors
                echo '<script>alert("Database Error. Please try again")</script>';
                // Log the error for debugging purposes if needed: echo $e->getMessage();
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>GNC</title>

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


</head>
<body>
	<!-- banner -->
	<div class="banner jarallax">
		<div class="agileinfo-dot">
			<?php include_once('includes/header.php');?>
			<div class="wthree-heading">
				<h2>Book Auditorium</h2>
			</div>
		</div>
	</div>
	<!-- //banner -->
	<!-- contact -->
        <div class="contact">
            <style>
                .contact{
margin: auto;
width:100%;
}
            </style>
		<div class="container">
			<div class="agile-contact-form">
				
				<div class="col-md-6 contact-form-right">
					<div class="contact-form-top">
						<h3>Book Auditorium </h3>
					</div>
					<div class="agileinfo-contact-form-grid">
						<form method="post">
	

                                <div class="form-group row">
                                    <label class="col-form-label col-md-4">Type of Event:</label>
                                    <div class="col-md-10">
                                       <select type="text" class="form-control" name="eventtype" required="true" >
							 	<option value="">Choose Event Type</option>
							 	<?php 

$sql2 = "SELECT * from   tbleventtype ";
$query2 = $dbh -> prepare($sql2);
$query2->execute();
$result2=$query2->fetchAll(PDO::FETCH_OBJ);

foreach($result2 as $row)
{          
    ?>  
<option value="<?php echo htmlentities($row->EventType);?>"><?php echo htmlentities($row->EventType);?></option>
 <?php } ?>
							 </select>
                                    </div>
                                </div>
                                                     <div class="form-group row">
        <label class="col-form-label col-md-4">Booking From:</label>
        <div class="col-md-10">
            <input type="date" class="form-control" style="font-size: 20px" required="true" name="bookingfrom"><BR>
            <input type="time" class="form-control" style="font-size: 20px" required="true" name="bookingfrom_time">
      
        </div>
    </div>

    <div class="form-group row">
        <label class="col-form-label col-md-4">Booking To:</label>
        <div class="col-md-10">
            <input type="date" class="form-control" style="font-size: 20px" required="true" name="bookingto"><br>
            <input type="time" class="form-control" style="font-size: 20px" required="true" name="bookingto_time">
           
        </div>
    </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-md-4">Number of Guest:</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" style="font-size: 20px" required="true" name="nop">
                                    </div>
                                </div>
                                                    

                                                 <div class="form-group row">
                                    <label class="col-form-label col-md-4">Requirements(if any)</label>
                                    <div class="col-md-10">
                                        <textarea  class="form-control"  required="true" name="message" style="font-size: 20px" ></textarea> 
                                    </div>
                                </div>
                                                
                                              <br>
                                                <div class="tp">
                                                    
                                                     <button type="submit" class="btn btn-primary" name="submit">Book</button>
                                                </div>
                            </form>

					</div>
				</div>
				
				<div class="clearfix"> </div>
			</div>
			
		
		</div>
	</div>
	<!-- //contact -->
	<?php include_once('includes/footer.php');?>
	<!-- jarallax -->
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
</html><?php  ?>