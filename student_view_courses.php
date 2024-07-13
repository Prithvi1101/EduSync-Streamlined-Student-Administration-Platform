<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';
$student_name = $_SESSION['username'];

$stmt = $con->prepare("SELECT Student_ID, department_id FROM students WHERE Student_Name = ?");
$stmt->bind_param("s", $student_name);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_id = $student['Student_ID'];
$department_id = $student['department_id'];

$sql = "SELECT distinct c.* FROM courses c
        JOIN enrollment_status e ON c.Course_ID = e.Course_ID
        WHERE c.department_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Courses</title>
    <link rel="stylesheet" href="style.css">
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
            margin-top: 50px;
        }
        .course-box {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .course-box h2 {
            margin-top: 0;
        }
        .dashboard-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .dashboard-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Courses</h1>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="course-box">
                <h2><?php echo htmlspecialchars($row['Course_Name']); ?></h2>
                <p><strong>Course ID:</strong> <?php echo htmlspecialchars($row['Course_ID']); ?></p>
                <p><?php echo htmlspecialchars($row['Course_Details']); ?></p>
            </div>
        <?php endwhile; ?>
        <a href="student_dashboard.php" class="dashboard-button">Go to Dashboard</a>
    </div>
</body>
</html>
