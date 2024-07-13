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

// Fetch academic data for charts
$marks_sql = "
    SELECT 
        c.Course_Name, 
        et.Exam_Type_Name, 
        m.Obtained_Marks
    FROM 
        marks m 
    JOIN 
        courses c ON m.Course_ID = c.Course_ID 
    JOIN 
        examinations e ON m.Exam_ID = e.Exam_ID 
    JOIN 
        exam_types et ON e.Exam_Type_ID = et.Exam_Type_ID 
    WHERE 
        m.Student_ID = ?";
$marks_stmt = $con->prepare($marks_sql);
$marks_stmt->bind_param("i", $student_id);
$marks_stmt->execute();
$marks_result = $marks_stmt->get_result();

$marks_data = [];
while ($row = $marks_result->fetch_assoc()) {
    $marks_data[] = $row;
}

// Fetch attendance data for charts
$attendance_sql = "
    SELECT 
        c.Course_Name, 
        COUNT(*) AS Total_Classes, 
        SUM(CASE WHEN a.Status = 'Present' THEN 1 ELSE 0 END) AS Classes_Attended,
        SUM(CASE WHEN a.Status = 'Absent' THEN 1 ELSE 0 END) AS Classes_Absent
    FROM 
        attendance a 
    JOIN 
        courses c ON a.Course_ID = c.Course_ID 
    WHERE 
        a.Student_ID = ?
    GROUP BY 
        a.Course_ID";
$attendance_stmt = $con->prepare($attendance_sql);
$attendance_stmt->bind_param("i", $student_id);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();

$attendance_data = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Statistics</title>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
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
        h1 {
            text-align: center;
            color: #007BFF;
        }
        .chart-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Student Statistics</h1>

        <div class="chart-container">
            <div id="marksChart" style="height: 370px; width: 100%;"></div>
        </div>
        
        <div class="chart-container">
            <div id="attendanceChart" style="height: 370px; width: 100%;"></div>
        </div>
    </div>

    <script>
        window.onload = function () {
            var marksData = <?php echo json_encode($marks_data); ?>;
            var attendanceData = <?php echo json_encode($attendance_data); ?>;

            var marksPoints = marksData.map(function(row) {
                return { label: row.Course_Name + " (" + row.Exam_Type_Name + ")", y: parseInt(row.Obtained_Marks) };
            });

            var totalClasses = attendanceData.reduce((a, b) => a + b.Total_Classes, 0);
            var classesAttended = attendanceData.reduce((a, b) => a + b.Classes_Attended, 0);
            var classesAbsent = attendanceData.reduce((a, b) => a + b.Classes_Absent, 0);

            var chart1 = new CanvasJS.Chart("marksChart", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Marks Per Course"
                },
                axisY: {
                    title: "Marks"
                },
                data: [{
                    type: "column",
                    dataPoints: marksPoints
                }]
            });

            var chart2 = new CanvasJS.Chart("attendanceChart", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Attendance Percentage"
                },
                data: [{
                    type: "pie",
                    showInLegend: true,
                    toolTipContent: "<b>{label}</b>: {y} (#percent%)",
                    indexLabel: "{label} - #percent%",
                    dataPoints: [
                        { y: classesAttended, label: "Classes Attended" },
                        { y: classesAbsent, label: "Classes Absent" }
                    ]
                }]
            });

            chart1.render();
            chart2.render();
        }
    </script>
</body>
</html>
