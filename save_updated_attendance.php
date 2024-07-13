<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'faculty' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include the database connection file
include 'DB_connection.php';

$course_id = $_POST['course_id'] ?? 0;
$date = $_POST['date'] ?? date('Y-m-d');
$attendance_data = $_POST['attendance'] ?? [];

// Prepare the SQL query for inserting or updating attendance records
$sql = "INSERT INTO attendance (student_id, course_id, attendance_date, status) 
        VALUES (?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE status = VALUES(status)";

$stmt = $con->prepare($sql);
if (!$stmt) {
    die('SQL prepare failed: ' . $con->error);
}

// Execute the query for each student's attendance status
foreach ($attendance_data as $student_id => $status) {
    $stmt->bind_param("iiss", $student_id, $course_id, $date, $status);
    if (!$stmt->execute()) {
        die('Execute failed: ' . $stmt->error);
    }
}

// Redirect to manage attendance page with success message
header("Location: update_attendance.php?course_id=$course_id&date=$date&success=1");
exit;
?>
