<?php
include("includes/dbconnection.php");

if (isset($_REQUEST['BookingID']) && isset($_REQUEST['remark'])) {
    $BookingID = intval($_REQUEST['BookingID']);
    $remark = $_REQUEST['remark']; // Get the remark from the request

    $query = "SELECT Status FROM tblbooking WHERE BookingID = :BookingID";
    $stmt = $dbh->prepare($query);

    if ($stmt) {
        $stmt->bindParam(":BookingID", $BookingID, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['Status'] == 'Cancelled') {
            echo json_encode(array("message" => "Booking is already cancelled"));
            // Add JavaScript alert
            echo "<script>alert('Booking is already cancelled.');</script>";
        } else {
            $updateQuery = "UPDATE tblbooking SET Status = 'Cancelled', Remark = :remark WHERE BookingID = :BookingID";
            $updateStmt = $dbh->prepare($updateQuery);

            if ($updateStmt) {
                $updateStmt->bindParam(":BookingID", $BookingID, PDO::PARAM_INT);
                $updateStmt->bindParam(":remark", $remark, PDO::PARAM_STR); // Bind remark parameter
                $updateStmt->execute();

                if ($updateStmt->rowCount() > 0) {
                    echo json_encode(array("message" => "Booking successfully cancelled"));
                    // Add JavaScript alert
                    echo "<script>alert('Booking successfully cancelled.');</script>";
                } else {
                    echo json_encode(array("message" => "Error updating booking status"));
                }

                $updateStmt->closeCursor();
            } else {
                echo json_encode(array("message" => "Error preparing update statement"));
            }
        }

        $stmt->closeCursor();
    } else {
        echo json_encode(array("message" => "Error preparing statement"));
    }

    $dbh = null; // Close the connection
} else {
    echo json_encode(array("message" => "BookingID or remark not provided"));
}
?>
