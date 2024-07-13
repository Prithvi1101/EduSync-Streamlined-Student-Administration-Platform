<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'parent' || !isset($_SESSION['username'])) {
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

// Fetch attendance and marks data for charts
$attendance_data = [];
$marks_data = [];

$sql = "SELECT c.Course_Name, a.Status, COUNT(a.Status) as count 
        FROM attendance a 
        JOIN courses c ON a.Course_ID = c.Course_ID 
        WHERE a.Student_ID = ? 
        GROUP BY c.Course_Name, a.Status";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $attendance_data[] = $row;
}

$sql = "SELECT c.Course_Name, AVG(m.Obtained_Marks) as average_marks 
        FROM marks m 
        JOIN courses c ON m.Course_ID = c.Course_ID 
        WHERE m.Student_ID = ? 
        GROUP BY c.Course_Name";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $marks_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Charts</title>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #007BFF;
        }
        .chart {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Charts</h1>
        <div id="attendanceChart" class="chart"></div>
        <div id="marksChart" class="chart"></div>
    </div>

    <script>
        window.onload = function () {
            var attendanceData = <?php echo json_encode($attendance_data); ?>;
            var marksData = <?php echo json_encode($marks_data); ?>;

            var attendanceChart = new CanvasJS.Chart("attendanceChart", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Attendance Overview"
                },
                data: [{
                    type: "pie",
                    indexLabel: "{label} - {y}",
                    dataPoints: [
                        { label: "Present", y: attendanceData.filter(item => item.Status === "Present").reduce((a, b) => a + b.count, 0) },
                        { label: "Absent", y: attendanceData.filter(item => item.Status === "Absent").reduce((a, b) => a + b.count, 0) }
                    ]
                }]
            });
            attendanceChart.render();

            var marksChart = new CanvasJS.Chart("marksChart", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Average Marks Per Course"
                },
                axisY: {
                    title: "Marks"
                },
                data: [{
                    type: "column",
                    dataPoints: marksData.map(item => ({
                        label: item.Course_Name,
                        y: item.average_marks
                    }))
                }]
            });
            marksChart.render();
        }
    </script>
</body>
</html>
