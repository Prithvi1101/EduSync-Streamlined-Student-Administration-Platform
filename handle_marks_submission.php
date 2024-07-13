<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'faculty' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $exam_type_name = $_POST['exam_id'];
    $exam_type_id = getExamTypeId($con, $exam_type_name);

    // Get the current date and time for the schedule
    $schedule = date('Y-m-d H:i:s');
    // Get the semester from the enrollment_status table
    $semester = getSemester($con, $course_id);

    // Insert or get the exam ID from the examinations table
    $exam_id = getExamId($con, $course_id, $exam_type_id, $semester, $schedule);

    foreach ($_POST['marks'] as $student_id => $marks) {
        $total_marks = $marks['total'];
        $obtained_marks = $marks['obtained'];
        // Insert or update marks
        $stmt = $con->prepare("INSERT INTO marks (Exam_ID, Course_ID, Student_ID, Total_Marks, Obtained_Marks) 
            VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE Total_Marks = VALUES(Total_Marks), Obtained_Marks = VALUES(Obtained_Marks)");
        if (!$stmt) {
            die("Prepare failed: (" . $con->errno . ") " . $con->error);
        }
        $stmt->bind_param("iiiii", $exam_id, $course_id, $student_id, $total_marks, $obtained_marks);
        $stmt->execute();
    }

    echo "Marks updated successfully!";
    // Redirect to dashboard after updating marks
    header("Location: faculty_dashboard.php");
    exit;
} else {
    // If not a POST request, redirect to the dashboard
    header("Location: faculty_dashboard.php");
    exit;
}

// Utility function to fetch Exam Type ID
function getExamTypeId($con, $exam_type_name) {
    $stmt = $con->prepare("SELECT Exam_Type_ID FROM exam_types WHERE Exam_Type_Name = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $con->errno . ") " . $con->error);
    }
    $stmt->bind_param("s", $exam_type_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['Exam_Type_ID'];
    } else {
        die("Exam type not found.");
    }
}

// Utility function to fetch or insert Exam ID
function getExamId($con, $course_id, $exam_type_id, $semester, $schedule) {
    // Check if exam already exists
    $stmt = $con->prepare("SELECT Exam_ID FROM examinations WHERE Course_ID = ? AND Exam_Type_ID = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $con->errno . ") " . $con->error);
    }
    $stmt->bind_param("ii", $course_id, $exam_type_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['Exam_ID'];
    } else {
        // Insert new exam if it does not exist
        $stmt = $con->prepare("INSERT INTO examinations (Course_ID, Exam_Type_ID, Semester, Schedule) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: (" . $con->errno . ") " . $con->error);
        }
        $stmt->bind_param("iiss", $course_id, $exam_type_id, $semester, $schedule);
        $stmt->execute();
        return $stmt->insert_id;
    }
}

// Utility function to fetch Semester from enrollment_status
function getSemester($con, $course_id) {
    $stmt = $con->prepare("SELECT Semester FROM enrollment_status WHERE Course_ID = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $con->errno . ") " . $con->error);
    }
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['Semester'];
    } else {
        die("Semester not found.");
    }
}
?>
