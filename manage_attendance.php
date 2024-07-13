<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'faculty' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include the database connection file
include 'DB_connection.php';

// Set the timezone to Dhaka, Bangladesh
date_default_timezone_set('Asia/Dhaka');

// Fetch faculty ID from session or initialize appropriately
$faculty_name = $_SESSION['username'];  // Ensure this session variable is set during authentication
$sql = "SELECT Faculty_ID FROM faculties WHERE Faculty_Name = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $faculty_name);
$stmt->execute();
$faculty_result = $stmt->get_result();
if ($faculty_result->num_rows > 0) {
    $faculty_id = $faculty_result->fetch_assoc()['Faculty_ID'];
} else {
    die("Faculty ID not found. Please check the faculty name in the session.");
}

$course_id = $_GET['course_id'] ?? 0;  // Default to 0 if not set

// Fetch students enrolled in this course
$sql = "SELECT s.Student_ID, s.Student_Name 
        FROM students s
        JOIN enrollment_status e ON s.Student_ID = e.Student_ID
        WHERE e.Course_ID = ? AND s.department_id = 
            (SELECT department_id FROM courses WHERE Course_ID = ?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $course_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error in SQL query: " . $con->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="style.css"> <!-- Make sure the path to your CSS file is correct -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn {
            padding: 8px 16px;
            margin-top: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
        .date-display {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Attendance for Course ID: <?php echo $course_id; ?></h1>
        <div class="date-display">Date: <?php echo date("Y-m-d"); ?></div>
        <form action="record_attendance.php" method="post">
            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course_id); ?>">
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Present</th>
                        <th>Absent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Student_ID']); ?></td>
                            <td><?php echo htmlspecialchars($row['Student_Name']); ?></td>
                            <td><input type="radio" name="attendance[<?php echo $row['Student_ID']; ?>]" value="Present"></td>
                            <td><input type="radio" name="attendance[<?php echo $row['Student_ID']; ?>]" value="Absent"></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="attendance-controls">
                <button type="submit" class="btn" name="action" value="save">Save Attendance</button>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="faculty_dashboard.php" class="btn-back">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>
