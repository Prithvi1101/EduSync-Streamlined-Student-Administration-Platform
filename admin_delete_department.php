<?php
session_start();
include 'DB_connection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Fetching departments for the dropdown
$departments = $con->query("SELECT department_id, department_name FROM department");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departmentId = $_POST['department_id'];

    if (empty($departmentId)) {
        $error = "Please select a department.";
    } else {
        // Delete department from the database
        $deleteQuery = "DELETE FROM department WHERE department_id = ?";
        $stmt = $con->prepare($deleteQuery);
        $stmt->bind_param("i", $departmentId);
        if ($stmt->execute()) {
            $success = "Department deleted successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Department</title>
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
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            background-color: #ff4d4d;
            color: white;
            padding: 14px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #cc0000;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Delete Department</h2>
        <?php if ($error): ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?= $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="department_id">Select Department:</label>
            <select id="department_id" name="department_id" required>
                <option value="">--Select Department--</option>
                <?php while($row = $departments->fetch_assoc()): ?>
                    <option value="<?php echo $row['department_id']; ?>">
                        <?php echo htmlspecialchars($row['department_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Delete Department</button>
        </form>
    </div>
</body>
</html>
