<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';

// Fetch attendance data
$sql = "SELECT c.Course_Name, 
               MONTH(a.attendance_date) as Month, 
               SUM(CASE WHEN a.Status = 'Present' THEN 1 ELSE 0 END) AS Classes_Attended,
               COUNT(*) AS Total_Classes,
               ROUND(SUM(CASE WHEN a.Status = 'Present' THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS Attendance_Percentage
        FROM attendance a 
        JOIN courses c ON a.Course_ID = c.Course_ID 
        GROUP BY c.Course_Name, MONTH(a.attendance_date)";
$result = $con->query($sql);
$attendance_data = [];

while ($row = $result->fetch_assoc()) {
    $attendance_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Analysis</title>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</head>
<body>
    <h1>Attendance Analysis</h1>
    <div id="attendanceChart" style="height: 370px; width: 100%;"></div>
    <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("attendanceChart", {
                animationEnabled: true,
                theme: "light2",
                title:{
                    text: "Attendance Analysis"
                },
                axisY: {
                    title: "Attendance Percentage"
                },
                data: [{
                    type: "line",
                    indexLabelFontSize: 16,
                    dataPoints: [
                        <?php
                        foreach ($attendance_data as $data) {
                            echo "{ label: '" . $data['Course_Name'] . " (" . $data['Month'] . ")', y: " . $data['Attendance_Percentage'] . " },";
                        }
                        ?>
                    ]
                }]
            });
            chart.render();
        }
    </script>
</body>
</html>
