<?php
session_start();
include 'DB_connection.php'; // Include your database connection file

// Check if notice ID is provided
if (isset($_POST['notice_id'])) {
    $notice_id = $_POST['notice_id'];

    // Prepare and execute SQL query to delete notice
    $sql_delete_notice = "DELETE FROM notices WHERE notice_id = ?";
    $stmt = $con->prepare($sql_delete_notice);
    $stmt->bind_param("i", $notice_id); // 'i' indicates integer type
    if ($stmt->execute()) {
        // Notice deleted successfully
        header("Location: delete_notice.php");
        exit();
    } else {
        // Error occurred during deletion
        echo "Error: " . $stmt->error;
    }
} else {
    // Redirect back if notice ID is not provided
    header("Location: delete_notice.php");
    exit();
}
?>
``
