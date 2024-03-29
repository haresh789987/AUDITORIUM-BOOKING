<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['obbsuid']==0)) {
  header('location:logout.php');
  } else{
   

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
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<![endif]-->
</head>
<body>
	<!-- banner -->
	<div class="banner jarallax">
		<div class="agileinfo-dot">
		<?php include_once('includes/header.php');?>
			<div class="wthree-heading">
				<h2>View Booking</h2>
			</div>
		</div>
	</div>
	<!-- //banner -->
	<!-- about -->
	<!-- about-top -->
	<div class="about-top">
		<div class="container">
			<div class="wthree-services-bottom-grids">
				
				<p class="wow fadeInUp animated" data-wow-delay=".5s">View Your Booking Details.</p>
					<div class="bs-docs-example wow fadeInUp animated" data-wow-delay=".5s">
						 <?php
                  $uid=$_SESSION['obbsuid'];

$sql="SELECT tbluser.FullName,tbluser.MobileNumber,tbluser.Email,tblbooking.BookingID,tblbooking.BookingDate,tblbooking.BookingFrom,tblbooking.BookingTo,tblbooking.EventType,tblbooking.Numberofguest,tblbooking.Message, tblbooking.Remark,tblbooking.Status,tblbooking.UpdationDate,tblservice.ServiceName,Remark from tblbooking join tblservice on tblbooking.ServiceID=tblservice.ID join tbluser on tbluser.ID=tblbooking.UserID  where tblbooking.UserID=:uid";
$query = $dbh -> prepare($sql);
$query-> bindParam(':uid', $uid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $row)
{               ?>
                            <table border="1" class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                <tr>
                                    <th colspan="5" style="text-align: center;font-size: 20px;color: blue;">Booking Number: <?php  echo $row->BookingID;?>
                                        
                                    </th>
                                </tr>
                                            <tr>
    <th>Staff Name</th>
    <td><?php  echo $row->FullName;?></td>
            
        <th>Cancel the Booking</th>
        <td>
         
                <button style="color: red;" onclick="cancelBooking(<?php echo $row->BookingID; ?>)">Cancel Booking</button>
       
        </td>
    </tr>
<script>
function cancelBooking(BookingID) {
    var remark = prompt("Enter your remark:");

    if (remark !== null && remark !== "") {
        $.ajax({
            type: "GET",
            url: "cancel_booking.php",
            data: {
                BookingID: BookingID,
                remark: remark
            },
            success: function(response) {
                console.log(response);

                if (response === "already_cancelled") {
                    // If the booking is already canceled, display an alert
                    alert('This booking is already cancelled.');
                } else {
                    // If cancellation is successful, display success message
                    alert('Booking successfully cancelled.');

                    // Store the message in messagetbl
                    storeMessage(BookingID, 'Booking successfully cancelled.');
                }
            },
            error: function() {
                console.log("Error in AJAX request.");
            }
        });
    } else {
        alert("Please enter a remark before cancelling the booking.");
    }
}
</script>


                 
  <tr>
    
   <th>Email</th>
    <td><?php  echo $row->Email;?></td>
     <th>Booking From</th>
    <td><?php  echo $row->BookingFrom;?></td>
  </tr>

   <tr>
   <th>Booking To</th>
    <td><?php  echo $row->BookingTo;?></td>
    <th>Number of Guest</th>
    <td><?php  echo $row->Numberofguest;?></td>
  </tr>
 
  <tr>
    
    <th>Event Type</th>
    <td><?php  echo $row->EventType;?></td>
    <th>Message</th>
    <td><?php  echo $row->Message;?></td>
  </tr>
  <tr>
    
    <th>Auditorium Name</th>
    <td><?php  echo $row->ServiceName;?></td>

    <th>Apply Date</th>
    <td><?php  echo $row->BookingDate;?></td>
  </tr>

  <tr>
    
     <th>STATUS</th>

    <td> <?php  $status=$row->Status;
    
if($row->Status=="Approved")
{
  echo "Approved";
}

if($row->Status=="Cancelled")
{
 echo "Cancelled";
}


if($row->Status=="")
{
  echo "Not Response Yet";
}


     ;?></td>
     <th >Admin Remark</th>
    <?php if($row->Status==""){ ?>

                     <td><?php echo "Not Updated Yet"; ?></td>
<?php } else { ?>                  <td><?php  echo htmlentities($row->Remark);?>
                  </td>
                  <?php } ?>
  </tr>
  
 
<?php $cnt=$cnt+1;}} ?>

</table> 
					</div>
				<div class="clearfix"> </div>
			</div>
		</div>
	</div>
	<!-- //about-top -->
	
	<!-- //about -->
	<!-- footer -->
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</body>	
</html><?php }  ?>