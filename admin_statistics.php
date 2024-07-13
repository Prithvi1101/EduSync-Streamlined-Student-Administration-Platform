<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';

// Fetch counts
$total_users = $con->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
$total_faculties = $con->query("SELECT COUNT(*) AS count FROM faculties")->fetch_assoc()['count'];
$total_students = $con->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];
$total_parents = $con->query("SELECT COUNT(*) AS count FROM parents")->fetch_assoc()['count'];
$total_courses = $con->query("SELECT COUNT(*) AS count FROM courses")->fetch_assoc()['count'];
$total_departments = $con->query("SELECT COUNT(*) AS count FROM department")->fetch_assoc()['count'];

// Fetch course details
$course_details_result = $con->query("SELECT Course_Name, course_faculty_id FROM courses");
$course_details = [];
while ($row = $course_details_result->fetch_assoc()) {
    $course_details[] = $row;
}

// Fetch department names
$department_names_result = $con->query("SELECT department_name FROM department");
$department_names = [];
while ($row = $department_names_result->fetch_assoc()) {
    $department_names[] = $row['department_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Statistics</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            width: 80%;
            max-width: 1200px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .stat-box {
            display: inline-block;
            width: 200px;
            padding: 20px;
            margin: 10px;
            background-color: #007BFF;
            color: white;
            border-radius: 10px;
            font-size: 20px;
        }
        .chart-container {
            width: 100%;
            height: 400px;
            margin-top: 30px;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .list-container {
            margin-top: 30px;
            text-align: left;
        }
        .list-container h2 {
            margin-top: 20px;
            color: #333;
        }
        .list-container ul {
            list-style-type: none;
            padding: 0;
        }
        .list-container li {
            margin-bottom: 5px;
            color: #555;
        }
    </style>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Admin Statistics</h1>
        <div class="stat-box">Total Users: <?php echo $total_users; ?></div>
        <div class="stat-box">Faculties: <?php echo $total_faculties; ?></div>
        <div class="stat-box">Students: <?php echo $total_students; ?></div>
        <div class="stat-box">Parents: <?php echo $total_parents; ?></div>
        <div class="stat-box">Courses: <?php echo $total_courses; ?></div>
        <div class="stat-box">Departments: <?php echo $total_departments; ?></div>

        <div id="chartContainer1" class="chart-container"></div>
        <div id="chartContainer2" class="chart-container"></div>
        <div id="chartContainer3" class="chart-container"></div>
        <div id="chartContainer4" class="chart-container"></div>

        <button onclick="window.location.href='generate_admin_report.php'">Download Report</button>

        <div class="list-container">
            <h2>Courses:</h2>
            <ul>
                <?php foreach ($course_details as $course): ?>
                    <li><?php echo htmlspecialchars($course['Course_Name']); ?></li>
                <?php endforeach; ?>
            </ul>

            <h2>Departments:</h2>
            <ul>
                <?php foreach ($department_names as $department_name): ?>
                    <li><?php echo htmlspecialchars($department_name); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        window.onload = function () {
            var chart1 = new CanvasJS.Chart("chartContainer1", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "User Distribution"
                },
                data: [{
                    type: "pie",
                    indexLabel: "{label} - {y}",
                    dataPoints: [
                        { y: <?php echo $total_faculties; ?>, label: "Faculties" },
                        { y: <?php echo $total_students; ?>, label: "Students" },
                        { y: <?php echo $total_parents; ?>, label: "Parents" }
                    ]
                }]
            });
            chart1.render();

            var chart2 = new CanvasJS.Chart("chartContainer2", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Courses Distribution by Faculty"
                },
                data: [{
                    type: "column",
                    dataPoints: [
                        <?php
                        foreach ($course_details as $course) {
                            $faculty_id = $course['course_faculty_id'];
                            echo "{ label: '" . addslashes($course['Course_Name']) . "', y: " . ($faculty_id ? $faculty_id : 0) . " },";
                        }
                        ?>
                    ]
                }]
            });
            chart2.render();

            var chart3 = new CanvasJS.Chart("chartContainer3", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Students and Faculties Over Time"
                },
                axisY: {
                    title: "Count"
                },
                data: [{
                    type: "line",
                    name: "Faculties",
                    showInLegend: true,
                    dataPoints: [
                        { x: new Date(2024, 0), y: 2 },
                        { x: new Date(2024, 1), y: 2 },
                        { x: new Date(2024, 2), y: 2 },
                        { x: new Date(2024, 3), y: 2 },
                        { x: new Date(2024, 4), y: 2 }
                    ]
                }, {
                    type: "line",
                    name: "Students",
                    showInLegend: true,
                    dataPoints: [
                        { x: new Date(2024, 0), y: 5 },
                        { x: new Date(2024, 1), y: 5 },
                        { x: new Date(2024, 2), y: 5 },
                        { x: new Date(2024, 3), y: 5 },
                        { x: new Date(2024, 4), y: 5 }
                    ]
                }]
            });
            chart3.render();

            var chart4 = new CanvasJS.Chart("chartContainer4", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Departments"
                },
                data: [{
                    type: "bar",
                    dataPoints: [
                        <?php
                        foreach ($department_names as $department_name) {
                            echo "{ label: '" . addslashes($department_name) . "', y: 1 },";
                        }
                        ?>
                    ]
                }]
            });
            chart4.render();
        }
    </script>
</body>
</html>
