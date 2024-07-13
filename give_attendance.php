<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'faculty' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include the database connection file
include 'DB_connection.php';

// Fetch faculty ID from session or initialize appropriately
$faculty_name = $_SESSION['username'];  // Ensure this session variable is set during authentication
$sql = "SELECT faculty_id FROM faculties WHERE faculty_name = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $faculty_name);
$stmt->execute();
$result = $stmt->get_result();
$faculty_id = $result->fetch_assoc()['faculty_id'];

// Fetch courses taught by the logged-in faculty
$sql = "SELECT Course_ID, Course_Name FROM courses WHERE course_faculty_id = ?";
$stmt = $con->prepare($sql);
if (!$stmt) {
    die('Prepare failed: ' . $con->error);
}
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Dashboard - Give Attendance</title>
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
        .action-links {
            display: flex;
            justify-content: space-between;
        }
        .action-link {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Give Attendance</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Course_ID']); ?></td>
                            <td><?php echo htmlspecialchars($row['Course_Name']); ?></td>
                            <td class="action-links">
                                <a href="manage_attendance.php?course_id=<?php echo $row['Course_ID']; ?>" class="action-link">Give Attendance</a>
                                <a href="update_attendance.php?course_id=<?php echo $row['Course_ID']; ?>" class="action-link">Update Attendance</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No courses assigned for attendance. Please contact the administrator.</p>
        <?php endif; ?>
    </div>
</body>
</html>
