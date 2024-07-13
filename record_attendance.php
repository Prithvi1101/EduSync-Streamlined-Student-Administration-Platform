<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'faculty' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include the database connection file
include 'DB_connection.php';

$faculty_id = $_SESSION['faculty_id'];  // Assuming this is set during login and stored in session
$course_id = $_POST['course_id'] ?? 0;  // Default to 0 if not provided
$attendance_data = $_POST['attendance'] ?? [];
$date_attended = date('Y-m-d');  // Use the current date or a date passed by the form

// Prepare the SQL query for inserting or updating attendance records
$sql = "INSERT INTO attendance (student_id, course_id, attendance_date, status) VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status)";

$stmt = $con->prepare($sql);
if (!$stmt) {
    die('SQL prepare failed: ' . $con->error);
}

// Execute the query for each student's attendance status
foreach ($attendance_data as $student_id => $status) {
    $stmt->bind_param("iiss", $student_id, $course_id, $date_attended, $status);
    if (!$stmt->execute()) {
        die('Execute failed: ' . $stmt->error);
    }
}

// Redirect to manage attendance page with a success message
header("Location: manage_attendance.php?course_id=$course_id&success=1");
exit;
?>
