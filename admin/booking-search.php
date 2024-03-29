<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['odmsaid']) == 0) {
    header('location:logout.php');
} else {
?>

<!DOCTYPE html>
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

    <!-- Main Container -->
    <main id="main-container">
        <!-- Page Content -->
        <div class="content">
            <h2 class="content-heading">Search Booking</h2>

            <!-- Dynamic Table Full Pagination -->
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Search Booking</h3>
                </div>
                <div class="block-content block-content-full">
                    <!-- DataTables init on table by adding .js-dataTable-full-pagination class, functionality initialized in js/pages/be_tables_datatables.js -->
                    <form id="basic-form" method="post">
                        <div class="form-group">
                            <label>Search by Staff Name</label>
                            <input id="searchdata" type="text" name="searchdata" required="true" class="form-control" placeholder="Staff Name">
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary" name="search" id="submit">Search</button>
                    </form>

                    <?php
                    if (isset($_POST['search'])) {
                        $sdata = $_POST['searchdata'];
                        ?>
                        <h4 align="center">Result against "<?php echo $sdata; ?>" keyword </h4>
                        <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                            <thead>
                            <tr>
                                <th class="text-center"></th>
                                <th>Department</th>
                                <th>Auditorium</th>
                                <th class="d-none d-sm-table-cell">Staff Name</th>
                                <th class="d-none d-sm-table-cell">Mobile Number</th>
                                <th class="d-none d-sm-table-cell">Email</th>
                                <th class="d-none d-sm-table-cell">Booking Date</th>
                                <th class="d-none d-sm-table-cell">Status</th>
                  
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sql = "SELECT *
FROM tblbooking
JOIN tbluser ON tbluser.ID = tblbooking.UserID
LEFT JOIN tblservice ON tblservice.ID = tblbooking.ServiceID
WHERE tblbooking.BookingID LIKE '$sdata%' OR tbluser.FullName LIKE '$sdata%' OR tbluser.MobileNumber LIKE '$sdata%'
";
                            $query = $dbh->prepare($sql);
                            $query->execute();
                            $results = $query->fetchAll(PDO::FETCH_OBJ);

                            $cnt = 1;
                            if ($query->rowCount() > 0) {
                                foreach ($results as $row) {
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo htmlentities($cnt); ?></td>
                                        <td class="font-w600"><?php echo htmlentities($row->department); ?></td>
                                        <td class="font-w600"><?php echo htmlentities($row->ServiceName); ?></td>
                                        <td class="font-w600"><?php echo htmlentities($row->FullName); ?></td>
                                        <td class="font-w600"><?php echo htmlentities($row->MobileNumber); ?></td>
                                        <td class="font-w600"><?php echo htmlentities($row->Email); ?></td>
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
                                        <?php } ?>
                                     
                                    </tr>
                                    <?php
                                    $cnt = $cnt + 1;
                                }
                            } else {
                                ?>
                           
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- END Main Container -->

    <?php include_once('includes/footer.php'); ?>
</div>
<!-- END Page Container -->

<!-- Codebase Core JS -->
<script src="assets/js/core/jquery.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="assets/js/core/jquery.slimscroll.min.js"></script>
<script src="assets/js/core/jquery.scrollLock.min.js"></script>
<script src="assets/js/core/jquery.appear.min.js"></script>
<script src="assets/js/core/jquery.countTo.min.js"></script>
<script src="assets/js/core/js.cookie.min.js"></script>
<script src="assets/js/codebase.js"></script>

<!-- Page JS Plugins -->
<script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page JS Code -->
<script src="assets/js/pages/be_tables_datatables.js"></script>
</body>
</html>

                    <?php }

?>
