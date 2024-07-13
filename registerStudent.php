<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

require 'DB_connection.php';

$username = $_SESSION['username'] ?? ''; // Carrying over the username from session or previous form

// Fetching departments for the dropdown
$departments = $con->query("SELECT department_id, department_name FROM department");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentName = $_POST['studentName'];
    $enrollmentStatus = $_POST['enrollmentStatus'];
    $departmentDetails = explode('|', $_POST['departmentId']); // Splitting the combined value
    $departmentId = $departmentDetails[0];
    $departmentName = $departmentDetails[1]; // Assigning the department name

    $stmt = $con->prepare("INSERT INTO students (Student_Name, Enrollment_Status, department, department_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $studentName, $enrollmentStatus, $departmentName, $departmentId);
    if ($stmt->execute()) {
        // Get the newly inserted student ID
        $newStudentId = $con->insert_id;

        // Enroll the new student in all existing courses
        $courses = $con->query("SELECT Course_ID FROM courses");
        while ($course = $courses->fetch_assoc()) {
            $courseId = $course['Course_ID'];
            $stmt = $con->prepare("INSERT INTO enrollment_status (Student_ID, Course_ID, Semester) VALUES (?, ?, '1')");
            $stmt->bind_param("ii", $newStudentId, $courseId);
            $stmt->execute();
        }
        
        echo "Student registered and enrolled in all courses successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Student</title>
    <link rel="stylesheet" href="styles.css"> <!-- Ensure the CSS path is correct -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], select {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register as a Student</h2>
        <form method="POST" action="registerStudent.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>

            <label for="studentName">Student Name</label>
            <input type="text" id="studentName" name="studentName" required>

            <label for="enrollmentStatus">Enrollment Status</label>
            <input type="text" id="enrollmentStatus" name="enrollmentStatus" required>

            <label for="departmentId">Department</label>
            <select id="departmentId" name="departmentId">
                <?php while($row = $departments->fetch_assoc()): ?>
                    <option value="<?php echo $row['department_id'] . '|' . $row['department_name']; ?>">
                        <?php echo htmlspecialchars($row['department_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
 