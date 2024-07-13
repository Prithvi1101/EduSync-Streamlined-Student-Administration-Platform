<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';

$student_name = $_SESSION['username'];
$stmt = $con->prepare("SELECT Student_ID, department_id FROM students WHERE Student_name = ?");
$stmt->bind_param("s", $student_name);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_id = $student['Student_ID'];
$department_id = $student['department_id'];

$stmt = $con->prepare("SELECT c.Course_ID, c.Course_Name FROM courses c 
                        JOIN enrollment_status e ON c.Course_ID = e.Course_ID 
                        WHERE e.Student_ID = ? AND c.department_id = ?");
$stmt->bind_param("ii", $student_id, $department_id);
$stmt->execute();
$courses = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Your Marks</title>
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
        .btn-view {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-view:hover {
            background-color: #45a049;
        }
        .btn-back {
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
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Courses and Marks</h1>
        <?php if ($courses->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['Course_Name']); ?></td>
                            <td>
                                <form action="student_view_coursemark.php" method="get">
                                    <input type="hidden" name="course_id" value="<?php echo $course['Course_ID']; ?>">
                                    <button type="submit" class="btn-view">View Marks</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No courses found. Please check back later.</p>
        <?php endif; ?>
        <a href="student_dashboard.php" class="btn-back">Go Back to Dashboard</a>
    </div>
</body>
</html>
