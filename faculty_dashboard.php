<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'faculty' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
include 'DB_connection.php';
$faculty_name = $_SESSION['username'];

// Get the profile picture path
$stmt = $con->prepare("SELECT picture FROM users WHERE username = ?");
$stmt->bind_param("s", $faculty_name);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$picturePath = $user['picture'] ?? 'images/default.jpg'; // Default picture if none is found
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Ensure CSS path is correct -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
        h1 {
            text-align: center;
        }
        .dashboard {
            width: 80%;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            padding: 10px;
            margin-top: 5px;
            background-color: #ddd;
            border-radius: 5px;
        }
        a {
            text-decoration: none;
            color: #000;
        }
        a:hover {
            color: #333;
        }
        a.logout-btn {
            display: block;
            width: 50%;
            margin: 20px auto;
            padding: 10px;
            background-color: #f00;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }
        a.logout-btn:hover {
            background-color: #d00;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <img src="<?php echo htmlspecialchars($picturePath); ?>" alt="Profile Picture" class="profile-picture">
    </div>
    <h1>Welcome, <?php echo htmlspecialchars($faculty_name); ?></h1>
    <div class="dashboard">
        <h1>Faculty Dashboard</h1>
        <ul>
            <li><a href="give_attendance.php">Give Attendance</a></li>
            <li><a href="faculty_provide_marks.php">Provide Marks</a></li>
            <li><a href="view_all_notices.php">View Notice Board</a></li>
            <li><a href="add_course.php">Add New Course</a></li> <!-- New Link for Adding Courses -->
        </ul>
        <a href="login.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
