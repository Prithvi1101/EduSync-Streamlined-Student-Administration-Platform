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
    echo "Parent details not found.";
    exit;
}

// Updated Query
$sql = "
    SELECT 
		m.Total_Marks,
        m.Obtained_Marks, 
        c.Course_Name, 
        et.Exam_Type_Name,
        (SELECT MAX(Obtained_Marks) FROM marks WHERE Course_ID = m.Course_ID AND Exam_ID = m.Exam_ID) AS Highest_Marks,
        f.Faculty_Name
    FROM 
        marks m
    JOIN 
        courses c ON m.Course_ID = c.Course_ID
    JOIN 
        examinations e ON m.Exam_ID = e.Exam_ID
    JOIN 
        exam_types et ON e.Exam_Type_ID = et.Exam_Type_ID
    JOIN 
        faculties f ON c.course_faculty_id = f.Faculty_ID
    WHERE 
        m.Student_ID = ?
";
$stmt = $con->prepare($sql);

if (!$stmt) {
    echo "Error preparing statement: " . $con->error;
    exit;
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$marks_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Marks</title>
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
        <h1>Marks for <?php echo htmlspecialchars($parent_name); ?>'s Child</h1>
        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Exam Type</th>
                    <th>Obtained Marks</th>
					<th>Total Marks</th>
                    <th>Highest Marks</th>
                    <th>Faculty Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $marks_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Course_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Exam_Type_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Obtained_Marks']); ?></td>
						<td><?php echo htmlspecialchars($row['Total_Marks']); ?></td>
                        <td><?php echo htmlspecialchars($row['Highest_Marks']); ?></td>
                        <td><?php echo htmlspecialchars($row['Faculty_Name']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
