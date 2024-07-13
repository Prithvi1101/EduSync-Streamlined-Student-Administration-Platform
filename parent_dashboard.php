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

// Get the profile picture path
$stmt = $con->prepare("SELECT picture FROM users WHERE username = ?");
$stmt->bind_param("s", $parent_username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$picturePath = $user['picture'] ?? 'images/default.jpg'; // Default picture if none is found
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Ensure the CSS path is correct -->
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
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #007BFF;
        }
        .dashboard a, .logout-button {
            display: inline-block;
            margin: 10px 5px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .dashboard a:hover, .logout-button:hover {
            background-color: #0056b3;
        }
        .logout-button {
            background-color: #ff4d4d;
        }
        .logout-button:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <img src="<?php echo htmlspecialchars($picturePath); ?>" alt="Profile Picture" class="profile-picture">
    </div>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($parent_name); ?></h1>
        <div class="dashboard">
            <a href="parent_view_attendance.php">View Attendance</a>
            <a href="parent_view_marks.php">View Marks</a>
            <a href="view_all_notices.php">View Notice Board</a>
            <a href="generate_report.php">Download Report PDF</a>
            <a href="parent_view_statistics.php">See Statistics</a>
        </div>
        <a href="login.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
