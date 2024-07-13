<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'parent') {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';

$message = ""; // Message variable to display any feedback

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $parent_name = $_POST['parent_name'];
    $student_id = $_POST['student_id'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Insert parent details into parents table
    $stmt = $con->prepare("INSERT INTO parents (parent_name, student_id, email, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $parent_name, $student_id, $email, $phone);
    if ($stmt->execute()) {
        $message = "Parent registered successfully!";
    } else {
        $message = "Failed to register parent details.";
    }
}

// Fetch all students for the dropdown
$stmt = $con->prepare("SELECT Student_ID, Student_Name FROM students");
$stmt->execute();
$result = $stmt->get_result();
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Parent</title>
    <link rel="stylesheet" href="style.css"> <!-- Make sure your path to CSS file is correct -->
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
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register Parent</h1>
        <?php if (!empty($message)): ?>
            <p class="error"><?= $message; ?></p>
        <?php endif; ?>
        <form action="registerParents.php" method="post">
            <label for="parent_name">Parent Name:</label>
            <input type="text" id="parent_name" name="parent_name" required>

            <label for="student_id">Select Student:</label>
            <select id="student_id" name="student_id">
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['Student_ID'] ?>"><?= $student['Student_Name'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= $_SESSION['username']; ?>" readonly>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?= $_SESSION['password']; ?>" readonly>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
