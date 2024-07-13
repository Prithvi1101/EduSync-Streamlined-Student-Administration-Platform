<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EduSync - Streamlined Education Platform</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #4b6cb7, #182848);
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            color: white;
        }
        .logo {
            width: 500px;
            margin-bottom: 20px;
        }
        .buttons {
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="images/EduSync.webp" alt="EduSync Logo" class="logo">
        <h1>Welcome to EduSync</h1>
        <p>Streamlined Education Platform</p>
        <div class="buttons">
            <a href="login.php" class="button">Login</a>
            <a href="registerUser.php" class="button">Register</a>
        </div>
    </div>
</body>
</html>
