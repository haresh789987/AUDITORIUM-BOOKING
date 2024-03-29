<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['odmsaid']) == 0) {
    header('location:logout.php');
} else {
    ?>
    <!doctype html>
    <html lang="en" class="no-focus">
        <head>
            <title>GNC</title>
            <link rel="stylesheet" href="assets/js/plugins/datatables/dataTables.bootstrap4.min.css">
            <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
           
        </head>
        <body>
            <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
                <?php include_once('includes/sidebar.php'); ?>
                <?php include_once('includes/header.php'); ?>
                <main id="main-container">
                    <div class="content">
                        <h2 class="content-heading">Search Auditorium</h2>
                        <div class="block">
                            <div class="block-header block-header-default">
                                <h3 class="block-title">Search Auditorium</h3>
                            </div>
                            <div class="block-content block-content-full">
                                <form id="basic-form" method="post">

                                    <div class="form-group">
                                        <label>Select Service</label>
                                        <select name="service" class="form-control">
                                            <?php
                                            // Fetch all service names from tblservice
                                            $serviceSql = "SELECT * FROM tblservice";
                                            $serviceQuery = $dbh->prepare($serviceSql);
                                            $serviceQuery->execute();
                                            $services = $serviceQuery->fetchAll(PDO::FETCH_OBJ);

                                            foreach ($services as $service) {
                                                echo '<option value="' . $service->ServiceName . '">' . $service->ServiceName . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Add the following code after the service dropdown -->
                                    <div class="form-group">
                                        <label>Select Month</label>
                                        <select name="month" class="form-control">
                                            <?php
                                            // Generate options for months
                                            for ($month = 1; $month <= 12; $month++) {
                                                echo '<option value="' . $month . '">' . date('F', mktime(0, 0, 0, $month, 1)) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <br>
                                    <button type="submit" class="btn btn-primary" name="search" id="submit">Search</button>
                                </form>
                                <?php
                                if (isset($_POST['search'])) {
                                    $selectedService = $_POST['service'];
                                    $selectedMonth = isset($_POST['month']) ? $_POST['month'] : '';

                                    ?>
                                    <h4 align="center">Result against "<?php echo $selectedService; ?>" keyword and selected month</h4>
                                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                        <thead>
                                            <tr>
                                                <th class="text-center"></th>
                                                <th>Department</th>
                                                <th class="d-none d-sm-table-cell">Staff Name</th>
                                                <th class="d-none d-sm-table-cell">Mobile Number</th>
                                                <th class="d-none d-sm-table-cell">Booking Date</th>
                                                <th class="d-none d-sm-table-cell">Status</th>
                                                 <th class="d-none d-sm-table-cell">Remark</th>
                                                <th class="d-none d-sm-table-cell">Auditorium Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                           $sql = "SELECT tbluser.department, tbluser.ID, tbluser.FullName, tbluser.MobileNumber, tblbooking.BookingDate, tblbooking.Status,tblbooking.ID,tblbooking.Remark , tblservice.ID, tblservice.ServiceName
        FROM tblbooking
        JOIN tbluser ON tbluser.ID = tblbooking.UserID
        LEFT JOIN tblservice ON tblservice.ID = tblbooking.ServiceID
        WHERE 
        tblservice.ServiceName = :selectedService 
        AND MONTH(tblbooking.BookingDate) = :selectedMonth
        AND tblbooking.Status = 'Cancelled'";


                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':selectedService', $selectedService, PDO::PARAM_STR);
                                            $query->bindParam(':selectedMonth', $selectedMonth, PDO::PARAM_INT);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);

                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $row) {
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?php echo htmlentities($cnt); ?></td>
                                                        <td class="font-w600"><?php echo htmlentities($row->department); ?></td>
                                                        <td class="font-w600"><?php echo htmlentities($row->FullName); ?></td>
                                                        <td class="font-w600"><?php echo htmlentities($row->MobileNumber); ?></td>
                                                        <td class="font-w600">
                                                            <span class="badge badge-primary"><?php echo htmlentities($row->BookingDate); ?></span>
                                                        </td>
                                                         <td class="d-none d-sm-table-cell">
                                                <?php
                                                $status = htmlentities($row->Status);
                                                $badgeClass = '';

                                                if ($status === 'Approved') {
                                                    $badgeClass = 'badge-success';
                                                } elseif ($status === 'Cancelled') {
                                                    $badgeClass = 'badge-danger';
                                                } else {
                                                    // If status is neither 'Approved' nor 'Cancelled', set it to 'Pending'
                                                    $status = 'Pending';
                                                    $badgeClass = 'badge-warning'; // You can set a different class for 'Pending' status
                                                }
                                                ?>

                                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                                            </td>

                                                       <td class="d-none d-sm-table-cell"><?php echo htmlentities($row->Remark); ?></td>
                                                        <td class="d-none d-sm-table-cell"><?php echo htmlentities($row->ServiceName); ?></td>
                                                    </tr>
                                                    <?php
                                                    $cnt = $cnt + 1;
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="8">No record found against this search</td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </main>
               

            </div>
        </body>
    </html>
    <?php
}
?>