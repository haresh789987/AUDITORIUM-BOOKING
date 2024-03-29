<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('includes/dbconnection.php');

if (strlen($_SESSION['odmsaid']) == 0) {
    header('location:logout.php');
} else {

    // Add debug statements
    if (isset($_GET['delid'])) {
        $rid = intval($_GET['delid']);
        $sql = "DELETE FROM tbldept WHERE id = :rid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_INT);

        // Debug statement
        var_dump($query);

        if (!$query->execute()) {
            // Debug statement
            print_r($query->errorInfo());

            echo "Delete query failed.";
        } else {
            // Debug statement
            echo "Delete query executed successfully.";

            echo "<script>alert('Data deleted');</script>";
            echo "<script>window.location.href = 'manage-department.php'</script>";
        }
    }
?>

<!doctype html>
<html lang="en" class="no-focus"> <!--<![endif]-->
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
            <h2 class="content-heading">Manage Department</h2>
            
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Manage Department</h3>
                </div>
                <div class="block-content block-content-full">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                        <thead>
                        <tr>
                            <th class="text-center"></th>
                            <th>Department Name</th>
                            <th class="d-none d-sm-table-cell" style="width: 15%;">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT * FROM tbldept";
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
                                    <td class="d-none d-sm-table-cell">
                                        <a href="#" onclick="confirmDelete(<?php echo $row->id; ?>);">
                                            <i class="fa fa-trash fa-delete" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php $cnt = $cnt + 1;
                            }
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include_once('includes/footer.php'); ?>
</div>

<script src="assets/js/core/jquery.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="assets/js/core/jquery.slimscroll.min.js"></script>
<script src="assets/js/core/jquery.scrollLock.min.js"></script>
<script src="assets/js/core/jquery.appear.min.js"></script>
<script src="assets/js/core/jquery.countTo.min.js"></script>
<script src="assets/js/core/js.cookie.min.js"></script>
<script src="assets/js/codebase.js"></script>
<script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>

<script>
    function confirmDelete(id) {
        if (confirm('Do you really want to delete?')) {
            window.location.href = 'manage-department.php?delid=' + id;
        }
    }

    $(document).ready(function () {
        $('.js-dataTable-full-pagination').DataTable();
    });
</script>
</body>
</html>
<?php } ?>
