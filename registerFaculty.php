<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'faculty' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

require 'DB_connection.php';

$username = $_SESSION['username'] ?? ''; // Carrying over the username from session or previous form

// Fetching departments for the dropdown
$departments = $con->query("SELECT department_id, department_name FROM department");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $facultyName = $_POST['facultyName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $departmentDetails = explode('|', $_POST['departmentId']); // Splitting the combined value
    $departmentId = $departmentDetails[0];
    $departmentName = $departmentDetails[1]; // Assigning the department name

    // Insert faculty into the faculties table
    $insertFaculty = $con->prepare("INSERT INTO faculties (Faculty_Name, Department, Email_ID, Phone_Number) VALUES (?, ?, ?, ?)");
    $insertFaculty->bind_param("ssss", $facultyName, $departmentName, $email, $phone);
    if ($insertFaculty->execute()) {
        echo "Faculty registered successfully!";
    } else {
        echo "Error: " . $insertFaculty->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Faculty</title>
    <link rel="stylesheet" href="styles.css"> <!-- Ensure the CSS path is correct -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 50%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="tel"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"], .logout-button {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        input[type="submit"]:hover, .logout-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register as a Faculty</h2>
        <form method="POST" action="registerFaculty.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>

            <label for="facultyName">Faculty Name</label>
            <input type="text" id="facultyName" name="facultyName" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="departmentId">Department</label>
            <select id="departmentId" name="departmentId">
                <?php while($row = $departments->fetch_assoc()): ?>
                    <option value="<?php echo $row['department_id'] . '|' . $row['department_name']; ?>">
                        <?php echo htmlspecialchars($row['department_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="submit" value="Register">
        </form>
        <a href="login.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
