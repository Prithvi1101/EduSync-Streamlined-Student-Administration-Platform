<?php
include 'DB_connection.php'; // Ensure this path is correct

// Fetch all students
$students = $con->query("SELECT Student_ID FROM students");

// Fetch all courses
$courses = $con->query("SELECT Course_ID FROM courses");

while ($student = $students->fetch_assoc()) {
    $studentId = $student['Student_ID'];
    while ($course = $courses->fetch_assoc()) {
        $courseId = $course['Course_ID'];
        // Check if the student is already enrolled in the course
        $checkEnrollment = $con->prepare("SELECT * FROM enrollment_status WHERE Student_ID = ? AND Course_ID = ?");
        $checkEnrollment->bind_param("ii", $studentId, $courseId);
        $checkEnrollment->execute();
        $enrollmentResult = $checkEnrollment->get_result();
        if ($enrollmentResult->num_rows == 0) {
            // Enroll the student in the course
            $stmt = $con->prepare("INSERT INTO enrollment_status (Student_ID, Course_ID, Semester) VALUES (?, ?, '1')");
            $stmt->bind_param("ii", $studentId, $courseId);
            $stmt->execute();
        }
    }
    // Reset courses pointer for next student
    $courses->data_seek(0);
}

echo "All students have been enrolled in all courses.";
?>
