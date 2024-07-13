<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';

// Fetch faculty performance data
$sql = "SELECT f.Faculty_Name, 
               AVG(m.Obtained_Marks) AS Average_Marks,
               COUNT(DISTINCT m.Student_ID) AS Students_Taught,
               COUNT(DISTINCT c.Course_ID) AS Courses_Taught
        FROM marks m 
        JOIN courses c ON m.Course_ID = c.Course_ID 
        JOIN faculties f ON c.course_faculty_id = f.Faculty_ID
        GROUP BY f.Faculty_Name";
$result = $con->query($sql);
$faculty_data = [];

while ($row = $result->fetch_assoc()) {
    $faculty_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Performance Analysis</title>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</head>
<body>
    <h1>Faculty Performance Analysis</h1>
    <div id="facultyPerformanceChart" style="height: 370px; width: 100%;"></div>
    <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("facultyPerformanceChart", {
                animationEnabled: true,
                theme: "light2",
                title:{
                    text: "Faculty Performance"
                },
                axisY: {
                    title: "Average Marks"
                },
                data: [{
                    type: "bar",
                    indexLabelFontSize: 16,
                    dataPoints: [
                        <?php
                        foreach ($faculty_data as $data) {
                            echo "{ label: '" . $data['Faculty_Name'] . "', y: " . $data['Average_Marks'] . " },";
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
