<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'faculty' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include the database connection file
include 'DB_connection.php';

$faculty_name = $_SESSION['username'];
$sql = "SELECT Faculty_ID FROM faculties WHERE Faculty_Name = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $faculty_name);
$stmt->execute();
$result = $stmt->get_result();
$faculty_id = $result->fetch_assoc()['Faculty_ID'];

$course_id = $_GET['course_id'] ?? 0;
$date = $_GET['date'] ?? date('Y-m-d');

// Fetch students enrolled in this course
$sql = "SELECT s.Student_ID, s.Student_Name, a.Status 
        FROM students s
        JOIN enrollment_status e ON s.Student_ID = e.Student_ID
        LEFT JOIN attendance a ON a.Student_ID = s.Student_ID AND a.course_id = ? AND a.attendance_date = ?
        WHERE e.Course_ID = ? AND s.department_id = 
            (SELECT department_id FROM courses WHERE Course_ID = ?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("isis", $course_id, $date, $course_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50; /* Green background */
            color: white; /* White text */
            text-decoration: none; /* No underline */
            border-radius: 5px; /* Rounded borders */
            cursor: pointer; /* Pointer cursor on hover */
        }
        .btn-back:hover {
            background-color: #367B48; /* Darker green on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Attendance for Course ID: <?php echo htmlspecialchars($course_id); ?></h1>
        <form action="" method="get">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <label for="date">Choose a date:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
            <button type="submit">Load Attendance</button>
        </form>
        <form action="save_updated_attendance.php" method="post">
            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course_id); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
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
                            <td><input type="radio" name="attendance[<?php echo $row['Student_ID']; ?>]" value="Present" <?php echo ($row['Status'] == 'Present') ? 'checked' : ''; ?>></td>
                            <td><input type="radio" name="attendance[<?php echo $row['Student_ID']; ?>]" value="Absent" <?php echo ($row['Status'] == 'Absent') ? 'checked' : ''; ?>></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div style="text-align: center; margin-top: 20px;">
                <button type="submit">Save Changes</button>
            </div>
            </form>
        </div>
    </body>
</html>

