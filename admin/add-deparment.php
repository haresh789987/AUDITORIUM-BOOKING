<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (!isset($_SESSION['odmsaid']) || strlen($_SESSION['odmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        $department = $_POST['department'];

        $sql = "INSERT INTO tbldept (department) VALUES (:department)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':department', $department, PDO::PARAM_STR);

        try {
            $query->execute();
            $LastInsertId = $dbh->lastInsertId();
            if ($LastInsertId > 0) {
                echo '<script>alert("Department has been added.")</script>';
                echo "<script>window.location.href ='add-deparment.php'</script>";
            } else {
                echo '<script>alert("Something Went Wrong. Please try again")</script>';
            }
        } catch (PDOException $e) {
            echo '<script>alert("Error: ' . $e->getMessage() . '")</script>';
        }
    }
?>

<!doctype html>
<html lang="en" class="no-focus"> <!--<![endif]-->
<head>
    <title>GNC</title>
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
</head>

<body>
    <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
        <?php include_once('includes/sidebar.php');?>
        <?php include_once('includes/header.php');?>

        <!-- Main Container -->
        <main id="main-container">
            <!-- Page Content -->
            <div class="content">
                <!-- Register Forms -->
                <h2 class="content-heading">Add Department</h2>
                <div class="row">
                    <div class="col-md-12">
                        <!-- Bootstrap Register -->
                        <div class="block block-themed">
                            <div class="block-header bg-gd-emerald">
                                <h3 class="block-title">Add Department</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                        <i class="si si-refresh"></i>
                                    </button>
                                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
                                </div>
                            </div>
                            <div class="block-content">
                                <form method="post">
<div class="form-group row">
    <label class="col-12" for="register1-email">Department Name:</label>
    <div class="col-12">
        <input type="text" class="form-control" name="department" value="" required='true' oninput="convertToUppercase(this)">
    </div>
</div>

<script>
    function convertToUppercase(element) {
        element.value = element.value.toUpperCase();
    }
</script>


                                    <div class="form-group row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-alt-success" name="submit">
                                                <i class="fa fa-plus mr-5"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- END Bootstrap Register -->
                    </div>
                </div>
            </div>
            <!-- END Page Content -->
        </main>
        <!-- END Main Container -->
        <?php include_once('includes/footer.php');?>
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
</body>
</html>
<?php } ?>
