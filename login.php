<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - EduSync</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #4b6cb7, #182848);
            animation: gradient 15s ease infinite;
            background-size: 400% 400%;
            font-family: Arial, sans-serif;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
		.register-btn {
            display: block;
            width: 100%;
            padding: 10px 1px;
            background-color: #f00;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        .logout-btn:hover {
            background-color: #d00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login to EduSync</h2>
        <form method="POST" action="authenticate.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
			
        </form>
		<a href="registerUser.php" class="register-btn">SignUp</a>
    </div>
</body>
</html>
