<?php
session_start(); 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
include 'DB_connection.php';

// Queries to identify patterns and trends
$attendance_query = "SELECT DAYOFWEEK(attendance_date) AS Day, AVG(Status = 'Present') AS Average_Attendance FROM attendance GROUP BY DAYOFWEEK(attendance_date) ORDER BY Average_Attendance DESC";
$performance_query = "SELECT e.Semester, AVG(m.Obtained_Marks) AS Average_Marks FROM marks m JOIN enrollment_status e ON m.Student_ID = e.Student_ID AND m.Course_ID = e.Course_ID GROUP BY e.Semester ORDER BY e.Semester";
$faculty_activity_query = "SELECT c.course_faculty_id AS Faculty_ID, COUNT(*) AS Number_of_Submissions FROM marks m JOIN courses c ON m.Course_ID = c.Course_ID GROUP BY c.course_faculty_id ORDER BY Number_of_Submissions DESC";
$student_attendance_query = "SELECT s.Student_Name, AVG(a.Status = 'Present') AS Average_Attendance FROM attendance a JOIN students s ON a.Student_ID = s.Student_ID GROUP BY s.Student_Name ORDER BY s.Student_Name";
$department_popularity_query = "SELECT d.department_name, COUNT(s.Student_ID) AS Student_Count FROM department d JOIN students s ON d.department_id = s.department_id GROUP BY d.department_name ORDER BY Student_Count DESC";
$marks_distribution_query = "SELECT c.Course_Name, AVG(m.Obtained_Marks) AS Average_Marks FROM marks m JOIN courses c ON m.Course_ID = c.Course_ID GROUP BY c.Course_Name ORDER BY c.Course_Name";
$department_performance_query = "SELECT d.department_name, AVG(m.Obtained_Marks) AS Average_Marks FROM marks m JOIN students s ON m.Student_ID = s.Student_ID JOIN department d ON s.department_id = d.department_id GROUP BY d.department_name ORDER BY Average_Marks DESC";
$exam_performance_query = "SELECT et.Exam_Type_Name, AVG(m.Obtained_Marks) AS Average_Marks FROM marks m JOIN examinations e ON m.Exam_ID = e.Exam_ID JOIN exam_types et ON e.Exam_Type_ID = et.Exam_Type_ID GROUP BY et.Exam_Type_Name ORDER BY Average_Marks DESC";

$attendance_result = mysqli_query($con, $attendance_query);
$performance_result = mysqli_query($con, $performance_query);
$faculty_activity_result = mysqli_query($con, $faculty_activity_query);
$student_attendance_result = mysqli_query($con, $student_attendance_query);
$department_popularity_result = mysqli_query($con, $department_popularity_query);
$marks_distribution_result = mysqli_query($con, $marks_distribution_query);
$department_performance_result = mysqli_query($con, $department_performance_query);
$exam_performance_result = mysqli_query($con, $exam_performance_query);

// Error handling for queries
if (!$attendance_result) {
    die("Attendance Query Failed: " . mysqli_error($con));
}
if (!$performance_result) {
    die("Performance Query Failed: " . mysqli_error($con));
}
if (!$faculty_activity_result) {
    die("Faculty Activity Query Failed: " . mysqli_error($con));
}
if (!$student_attendance_result) {
    die("Student Attendance Query Failed: " . mysqli_error($con));
}
if (!$department_popularity_result) {
    die("Department Popularity Query Failed: " . mysqli_error($con));
}
if (!$marks_distribution_result) {
    die("Marks Distribution Query Failed: " . mysqli_error($con));
}
if (!$department_performance_result) {
    die("Department Performance Query Failed: " . mysqli_error($con));
}
if (!$exam_performance_result) {
    die("Exam Performance Query Failed: " . mysqli_error($con));
}

$attendance_patterns = [];
while ($row = mysqli_fetch_assoc($attendance_result)) {
    $attendance_patterns[] = $row;
}

$performance_patterns = [];
while ($row = mysqli_fetch_assoc($performance_result)) {
    $performance_patterns[] = $row;
}

$faculty_activity_patterns = [];
while ($row = mysqli_fetch_assoc($faculty_activity_result)) {
    $faculty_activity_patterns[] = $row;
}

$student_attendance_patterns = [];
while ($row = mysqli_fetch_assoc($student_attendance_result)) {
    $student_attendance_patterns[] = $row;
}

$department_popularity_patterns = [];
while ($row = mysqli_fetch_assoc($department_popularity_result)) {
    $department_popularity_patterns[] = $row;
}

$marks_distribution_patterns = [];
while ($row = mysqli_fetch_assoc($marks_distribution_result)) {
    $marks_distribution_patterns[] = $row;
}

$department_performance_patterns = [];
while ($row = mysqli_fetch_assoc($department_performance_result)) {
    $department_performance_patterns[] = $row;
}

$exam_performance_patterns = [];
while ($row = mysqli_fetch_assoc($exam_performance_result)) {
    $exam_performance_patterns[] = $row;
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Patterns/Trends</title>
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
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        h2 {
            margin-top: 30px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .back-button {
            display: block;
            width: 100px;
            margin: 30px auto;
            padding: 10px;
            text-align: center;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Patterns and Trends</h1>

        <h2>Attendance Patterns by Day of the Week:</h2>
        <table>
            <tr>
                <th>Day of the Week</th>
                <th>Average Attendance</th>
            </tr>
            <?php foreach ($attendance_patterns as $pattern): ?>
            <tr>
                <td><?php echo $pattern['Day']; ?></td>
                <td><?php echo number_format($pattern['Average_Attendance'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Average Marks by Semester:</h2>
        <table>
            <tr>
                <th>Semester</th>
                <th>Average Marks</th>
            </tr>
            <?php foreach ($performance_patterns as $pattern): ?>
            <tr>
                <td><?php echo $pattern['Semester']; ?></td>
                <td><?php echo number_format($pattern['Average_Marks'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Faculty Activity Patterns:</h2>
        <table>
            <tr>
                <th>Faculty ID</th>
                <th>Number of Submissions</th>
            </tr>
            <?php foreach ($faculty_activity_patterns as $pattern): ?>
            <tr>
                <td><?php echo $pattern['Faculty_ID']; ?></td>
                <td><?php echo $pattern['Number_of_Submissions']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Student Attendance Patterns:</h2>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Average Attendance</th>
            </tr>
            <?php foreach ($student_attendance_patterns as $pattern): ?>
            <tr>
                <td><?php echo $pattern['Student_Name']; ?></td>
                <td><?php echo number_format($pattern['Average_Attendance'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Department Popularity:</h2>
        <table>
            <tr>
                <th>Department Name</th>
                <th>Student Count</th>
            </tr>
            <?php foreach ($department_popularity_patterns as $pattern): ?>
            <tr>
                <td><?php echo $pattern['department_name']; ?></td>
                <td><?php echo $pattern['Student_Count']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Average Marks Distribution by Course:</h2>
        <table>
            <tr>
                <th>Course Name</th>
                <th>Average Marks</th>
            </tr>
            <?php foreach ($marks_distribution_patterns as $pattern): ?>
            <tr>
                <td><?php echo $pattern['Course_Name']; ?></td>
                <td><?php echo number_format($pattern['Average_Marks'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Department Performance:</h2>
        <table>
            <tr>
                <th>Department Name</th>
                <th>Average Marks</th>
            </tr>
            <?php foreach ($department_performance_patterns as $pattern): ?>
            <tr>
                <td><?php echo $pattern['department_name']; ?></td>
                <td><?php echo number_format($pattern['Average_Marks'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Exam Performance by Type:</h2>
        <table>
            <tr>
                <th>Exam Type</th>
                <th>Average Marks</th>
            </tr>
            <?php foreach ($exam_performance_patterns as $pattern): ?>
            <tr>
                <td><?php echo $pattern['Exam_Type_Name']; ?></td>
                <td><?php echo number_format($pattern['Average_Marks'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <a href="admin_dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
