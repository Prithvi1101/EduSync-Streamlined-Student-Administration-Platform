<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include the database connection file
include 'DB_connection.php';

// Fetch student ID from session
$student_name = $_SESSION['username'];
$sql = "SELECT Student_ID FROM students WHERE Student_name = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $student_name);
$stmt->execute();
$result = $stmt->get_result();
$student_id = $result->fetch_assoc()['Student_ID'];

// Fetch department ID of the student
$sql = "SELECT department_id FROM students WHERE Student_ID = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$department_id = $result->fetch_assoc()['department_id'];

$course_id = $_GET['course_id'] ?? 0;
$date = $_GET['date'] ?? ''; // Allow empty date to fetch all records

$sql = "SELECT a.Status, c.Course_Name, a.attendance_date FROM attendance a
        JOIN courses c ON a.Course_ID = c.Course_ID
        WHERE a.Student_ID = ? AND a.Course_ID = ?";
$params = ["ii", $student_id, $course_id];

if (!empty($date)) {
    $sql .= " AND a.attendance_date = ?";
    $params[0] .= 's';
    $params[] = $date;
}

$stmt = $con->prepare($sql);
$stmt->bind_param(...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance</title>
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
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-back:hover {
            background-color: #367B48;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View Attendance</h1>
        <form method="get" action="">
            <label for="course_id">Select Course:</label>
            <select id="course_id" name="course_id">
                <!-- Options will be populated here -->
                <?php
                // Fetch available courses for the student
                $sql = "SELECT Course_ID, Course_Name FROM courses WHERE department_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $department_id);
                $stmt->execute();
                $courses = $stmt->get_result();
                while ($course = $courses->fetch_assoc()) {
                    echo "<option value='" . $course['Course_ID'] . "'" . ($course_id == $course['Course_ID'] ? " selected" : "") . ">" . htmlspecialchars($course['Course_Name']) . "</option>";
                }
                ?>
            </select>
            <label for="date">Date (optional):</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
            <button type="submit">View Attendance</button>
        </form>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Course_Name']); ?></td>
                            <td><?php echo htmlspecialchars($row['Status']); ?></td>
                            <td><?php echo htmlspecialchars($row['attendance_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No attendance records found for the selected course/date.</p>
        <?php endif; ?>
        <a href="student_dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>
</html>
