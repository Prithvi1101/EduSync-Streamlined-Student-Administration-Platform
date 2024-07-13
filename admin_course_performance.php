<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';

// Fetch course performance data
$sql = "SELECT c.Course_Name, 
               AVG(m.Obtained_Marks) AS Average_Marks,
               MAX(m.Obtained_Marks) AS Highest_Marks,
               MIN(m.Obtained_Marks) AS Lowest_Marks
        FROM marks m 
        JOIN courses c ON m.Course_ID = c.Course_ID 
        GROUP BY c.Course_Name";
$result = $con->query($sql);
$course_data = [];

while ($row = $result->fetch_assoc()) {
    $course_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Performance Analysis</title>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</head>
<body>
    <h1>Course Performance Analysis</h1>
    <div id="coursePerformanceChart" style="height: 370px; width: 100%;"></div>
    <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("coursePerformanceChart", {
                animationEnabled: true,
                theme: "light2",
                title:{
                    text: "Course Performance"
                },
                axisY: {
                    title: "Marks"
                },
                data: [{
                    type: "column",
                    indexLabelFontSize: 16,
                    dataPoints: [
                        <?php
                        foreach ($course_data as $data) {
                            echo "{ label: '" . $data['Course_Name'] . "', y: " . $data['Average_Marks'] . " },";
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
