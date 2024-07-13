<?php
session_start();
include 'DB_connection.php'; // Ensure this path is correct

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$admin_name = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {
    $departmentId = $_POST['department_id'];
    $departmentName = $_POST['department_name'];

    // Insert new department into the database
    $stmt = $con->prepare("INSERT INTO department (department_id, department_name) VALUES (?, ?)");
    $stmt->bind_param("is", $departmentId, $departmentName);
    if ($stmt->execute()) {
        echo "<p>Department added successfully.</p>";
    } else {
        echo "Error adding department: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Department</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 400px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button, .back-button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover, .back-button:hover {
            background-color: #0056b3;
        }
        p {
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Department</h1>
        <form method="POST">
            <label for="department_id">Department ID:</label>
            <input type="number" id="department_id" name="department_id" required>

            <label for="department_name">Department Name:</label>
            <input type="text" id="department_name" name="department_name" required>

            <button type="submit" name="add_department">Add Department</button>
        </form>
        <a href="admin_dashboard.php" class="back-button">Go Back</a>
    </div>
</body>
</html>
