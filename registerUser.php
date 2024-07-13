<?php
session_start();
include 'DB_connection.php';

$error = ''; // Variable to hold error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // Consider hashing the password
    $role = trim($_POST['role']);

    // Handle file upload
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $new_target_file = $target_dir . $username . '.' . $imageFileType;

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["picture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $error = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["picture"]["size"] > 500000) {
        $error = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $error = "Sorry, your file was not uploaded.";
    } else {
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if (move_uploaded_file($_FILES["picture"]["tmp_name"], $new_target_file)) {
            // File is uploaded successfully
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }

    if (empty($error)) {
        // Check if username already exists
        $checkUser = "SELECT * FROM users WHERE username = ?";
        $stmt = $con->prepare($checkUser);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = 'Username already taken!';
        } else {
            // Insert user into the database
            $insertQuery = "INSERT INTO users (username, password, role, picture) VALUES (?, ?, ?, ?)";
            $stmt = $con->prepare($insertQuery);
            $stmt->bind_param("ssss", $username, $password, $role, $new_target_file);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['password'] = $password; // Store password in session
                $_SESSION['role'] = $role;

                // Redirect based on role
                switch ($role) {
                    case 'student':
                        header('Location: registerStudent.php');
                        exit;
                    case 'parent':
                        header('Location: registerParents.php');
                        exit;
                    case 'faculty':
                        header('Location: registerFaculty.php');
                        exit;
                    case 'admin':
                        header('Location: admin_dashboard.php');
                        exit;
                    default:
                        $error = "Unsupported role selected.";
                }
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register User</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(45deg, #66ffcc, #3399ff, #9966ff, #66ffcc);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            width: 350px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            text-align: center;
        }

        input[type="text"], input[type="password"], select, input[type="file"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register User</h2>
        <?php if ($error): ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
                <option value="parent">Parent</option>
                <option value="admin">Admin</option>
            </select>
            <label for="picture">Profile Picture:</label>
            <input type="file" id="picture" name="picture" accept="image/*" required>
            <button type="submit">Register</button>
        </form>
        <form action="login.php" method="post">
            <button type="submit" class="logout-button">Login</button>
        </form>
    </div>
</body>
</html>
