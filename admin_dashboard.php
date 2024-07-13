<?php
session_start(); 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
include 'DB_connection.php';

$admin_username = $_SESSION['username'];

// Get the profile picture path
$stmt = $con->prepare("SELECT picture FROM users WHERE username = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$picturePath = $user['picture'] ?? 'images/default.jpg'; // Default picture if none is found
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

		.profile-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007BFF;
        }
		
        .container {
            width: 80%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        h2 {
            margin-top: 30px;
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        li {
            margin-bottom: 10px;
        }
        a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }
        a:hover {
            color: #0056b3;
        }
        form {
            text-align: center;
            margin-top: 30px;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="profile-container">
        <img src="<?php echo htmlspecialchars($picturePath); ?>" alt="Profile Picture" class="profile-picture">
    </div>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

        <h2>Manage Courses:</h2>
        <ul>
            <li><a href="admin_add_course.php">Add Course</a></li>
            <li><a href="admin_delete_course.php">Delete Course</a></li>
            <li><a href="admin_update_course.php">Update Course</a></li>
        </ul>

        <h2>See Statistics:</h2>
        <ul>
            <li><a href="admin_attendance_analysis.php">Attendance Analysis</a></li>
            <li><a href="admin_course_performance.php">Course Performance</a></li>
            <li><a href="admin_statistics.php">User Statistics</a></li>
            <li><a href="admin_patterns.php">See Patterns/Trends</a></li>
        </ul>

        <h2>Reports:</h2>
        <ul>
            <li><a href="generate_admin_report.php">Generate Admin Report</a></li>
        </ul>
        
        <h2>Notices/Announcements:</h2>
        <ul>
            <li><a href="admin_notice_board.php">Manage Notices</a></li>
        </ul>

        <form action="logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
