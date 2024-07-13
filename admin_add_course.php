<?php
session_start();
include 'DB_connection.php'; // Ensure this path is correct

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$admin_name = $_SESSION['username'];
$stmt = $con->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $admin_name);
$stmt->execute();
$result = $stmt->get_result();
$user_id = $result->fetch_assoc()['user_id'];

// Fetching departments for the dropdown
$departments = $con->query("SELECT department_id, department_name FROM department");

// Fetching faculties for the dropdown
$faculties = $con->query("SELECT Faculty_ID, Faculty_Name FROM faculties");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $courseId = $_POST['course_id'];
    $courseName = $_POST['course_name'];
    $courseDetails = $_POST['course_details'];
    $departmentDetails = explode('|', $_POST['department_id']); // Splitting the combined value
    $departmentId = $departmentDetails[0];
    $departmentName = $departmentDetails[1];
    $semester = $_POST['semester'];
    $facultyDetails = explode('|', $_POST['faculty_id']); // Splitting the combined value
    $facultyId = $facultyDetails[0];

    // Insert new course into the database
    $stmt = $con->prepare("INSERT INTO courses (Course_ID, Course_Name, Course_Details, Department, department_id, course_faculty_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssii", $courseId, $courseName, $courseDetails, $departmentName, $departmentId, $facultyId);
    if ($stmt->execute()) {
        // Enroll all existing students in the new course
        $students = $con->query("SELECT Student_ID FROM students");
        while ($student = $students->fetch_assoc()) {
            $studentId = $student['Student_ID'];
            $stmt = $con->prepare("INSERT INTO enrollment_status (Student_ID, Course_ID, Semester) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $studentId, $courseId, $semester);
            $stmt->execute();
        }
        
        echo "<p>Course and student enrollments added successfully.</p>";
    } else {
        echo "Error adding course: " . $con->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Course</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 400px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button, .back-button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover, .back-button:hover {
            background-color: #0056b3;
        }
        p {
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Course</h1>
        <form method="POST">
            <label for="course_id">Course ID:</label>
            <input type="number" id="course_id" name="course_id" required>
            
            <label for="course_name">Course Name:</label>
            <input type="text" id="course_name" name="course_name" required>
            
            <label for="course_details">Course Details:</label>
            <textarea id="course_details" name="course_details" required></textarea>
            
            <label for="department_id">Department:</label>
            <select id="department_id" name="department_id">
                <?php while($row = $departments->fetch_assoc()): ?>
                    <option value="<?php echo $row['department_id'] . '|' . $row['department_name']; ?>">
                        <?php echo htmlspecialchars($row['department_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            
            <label for="faculty_id">Faculty:</label>
            <select id="faculty_id" name="faculty_id">
                <?php while($row = $faculties->fetch_assoc()): ?>
                    <option value="<?php echo $row['Faculty_ID'] . '|' . $row['Faculty_Name']; ?>">
                        <?php echo htmlspecialchars($row['Faculty_Name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            
            <label for="semester">Semester:</label>
            <input type="text" id="semester" name="semester" required>
            
            <button type="submit" name="add_course">Add Course</button>
        </form>
        <a href="admin_dashboard.php" class="back-button">Go Back</a>
    </div>
</body>
</html>
