<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';

$student_name = $_SESSION['username'];
$stmt = $con->prepare("SELECT Student_ID FROM students WHERE Student_name = ?");
$stmt->bind_param("s", $student_name);
$stmt->execute();
$result = $stmt->get_result();
$student_id = $result->fetch_assoc()['Student_ID'];

$course_id = $_GET['course_id'] ?? 0;  // Ensure this is being passed correctly

// Fetch course name
$stmt = $con->prepare("SELECT Course_Name FROM courses WHERE Course_ID = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course_result = $stmt->get_result();
$course_name = ($course_result->num_rows > 0) ? $course_result->fetch_assoc()['Course_Name'] : 'Unknown Course';

// Fetch marks for the student for a specific course
$stmt = $con->prepare("SELECT e.Exam_Type_Name, m.Total_Marks, m.Obtained_Marks
                        FROM marks m
                        JOIN examinations ex ON m.Exam_ID = ex.Exam_ID
                        JOIN exam_types e ON ex.Exam_Type_ID = e.Exam_Type_ID
                        WHERE m.Student_ID = ? AND m.Course_ID = ?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$marks = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Course Marks</title>
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
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn-back {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }
        .btn-back:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Marks for <?php echo htmlspecialchars($course_name); ?></h1>
        <?php if ($marks->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Exam Type</th>
                        <th>Total Marks</th>
                        <th>Obtained Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mark = $marks->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mark['Exam_Type_Name']); ?></td>
                            <td><?php echo htmlspecialchars($mark['Total_Marks']); ?></td>
                            <td><?php echo htmlspecialchars($mark['Obtained_Marks']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No marks available for this course. Please check back later.</p>
        <?php endif; ?>
        <a href="student_view_marks.php" class="btn-back">Back to Courses</a>
    </div>
</body>
</html>
