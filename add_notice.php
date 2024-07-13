<?php
session_start();
include 'DB_connection.php'; // Make sure this path is correct

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['notice_details']) && !empty($_POST['notice_details'])) {
    $noticeDetails = $_POST['notice_details'];

    // Prepare SQL query to insert notice
    $sql = "INSERT INTO notices (notice_details, notice_date) VALUES (?, NOW())";
    $stmt = $con->prepare($sql);

    // Check if the statement was prepared correctly
    if ($stmt === false) {
        die('MySQL prepare error: ' . $con->error);
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("s", $noticeDetails);
    if ($stmt->execute()) {
        // Redirect to the admin notice board or show a success message
        header("Location: admin_notice_board.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Notice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
        }
        button, .cancel-btn {
            flex: 1;
            padding: 10px;
            margin: 0 5px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .cancel-btn {
            background-color: #f00;
        }
        .cancel-btn:hover {
            background-color: #d00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Notice</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="notice_details">Notice Details:</label>
            <textarea id="notice_details" name="notice_details" rows="4" cols="50"></textarea>
            <div class="buttons">
                <button type="submit">Save</button>
                <a href="admin_notice_board.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
