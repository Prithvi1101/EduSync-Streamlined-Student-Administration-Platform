<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';

$username = $_SESSION['username'];
$stmt = $con->prepare("SELECT picture FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$picturePath = $user['picture'] ?? 'images/default.jpg'; // Default picture if none is found
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            padding: 50px;
            position: relative;
            width: 300px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .container h1 {
            margin-bottom: 30px;
        }
        .container a {
            display: block;
            margin: 10px 0;
            padding: 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .container a:hover {
            background-color: #0056b3;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-container">
            <img src="<?php echo htmlspecialchars($picturePath); ?>" alt="Profile Picture" class="profile-picture">
        </div>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a href="student_view_courses.php">View Courses</a>
        <a href="student_view_attendance.php">View Attendance</a>
        <a href="student_view_marks.php">View Marks</a>
        <a href="view_all_notices.php">View Notice Board</a>
        <a href="login.php">Logout</a>
    </div>
</body>
</html>
