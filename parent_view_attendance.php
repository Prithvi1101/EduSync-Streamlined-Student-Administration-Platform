<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'parent') {
    header('Location: login.php');
    exit;
}
include 'DB_connection.php';

$parent_username = $_SESSION['username'];

// Get parent details using username
$sql = "SELECT * FROM parents WHERE parent_name = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $parent_username);
$stmt->execute();
$result = $stmt->get_result();
$parent = $result->fetch_assoc();

if ($parent) {
    $parent_name = $parent['parent_name'];
    $student_id = $parent['student_id'];
} else {
    // Handle the case when parent details are not found
    echo "Parent details not found.";
    exit;
}

// Fetch attendance data
$sql = "SELECT a.*, c.Course_Name 
        FROM attendance a
        JOIN courses c ON a.Course_ID = c.Course_ID
        WHERE a.Student_ID = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$attendance_result = $stmt->get_result();

// Calculate attendance summary
$attendance_summary = [];
while ($row = $attendance_result->fetch_assoc()) {
    $course_id = $row['Course_ID'];
    $course_name = $row['Course_Name'];

    if (!isset($attendance_summary[$course_id])) {
        $attendance_summary[$course_id] = [
            'course_name' => $course_name,
            'total_classes' => 0,
            'total_present' => 0,
            'total_absent' => 0
        ];
    }

    $attendance_summary[$course_id]['total_classes']++;
    if ($row['Status'] === 'Present') {
        $attendance_summary[$course_id]['total_present']++;
    } else {
        $attendance_summary[$course_id]['total_absent']++;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #eee;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Attendance for Student ID:<?php echo htmlspecialchars($student_id); ?></h1>
        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Total Classes</th>
                    <th>Total Present</th>
                    <th>Total Absent</th>
                    <th>Attendance Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_summary as $course_id => $summary): 
                    $attendance_percentage = ($summary['total_present'] / $summary['total_classes']) * 100;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($summary['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($summary['total_classes']); ?></td>
                        <td><?php echo htmlspecialchars($summary['total_present']); ?></td>
                        <td><?php echo htmlspecialchars($summary['total_absent']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($attendance_percentage, 2)) . '%'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
