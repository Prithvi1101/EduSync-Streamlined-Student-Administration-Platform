<?php
session_start();
include 'DB_connection.php'; // Ensure this path is correct

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Fetching courses for the dropdown
$courses = $con->query("SELECT Course_ID, Course_Name FROM courses");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course'])) {
    $courseId = $_POST['course_id'];

    // Begin transaction
    $con->begin_transaction();

    try {
        // Delete from enrollment_status
        $stmt = $con->prepare("DELETE FROM enrollment_status WHERE Course_ID = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();

        // Delete from examinations
        $stmt = $con->prepare("DELETE FROM examinations WHERE Course_ID = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();

        // Delete from attendance
        $stmt = $con->prepare("DELETE FROM attendance WHERE Course_ID = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();

        // Delete from courses
        $stmt = $con->prepare("DELETE FROM courses WHERE Course_ID = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();

        // Commit transaction
        $con->commit();
        
        echo "<p>Course and associated records deleted successfully.</p>";
    } catch (Exception $e) {
        // Rollback transaction
        $con->rollback();
        echo "Error deleting course: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Course</title>
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
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button, .back-button {
            width: 100%;
            padding: 8px;
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
        .back-button-container {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Delete Course</h1>
        <form method="POST">
            <label for="course_id">Select Course:</label>
            <select id="course_id" name="course_id" required>
                <?php while($row = $courses->fetch_assoc()): ?>
                    <option value="<?php echo $row['Course_ID']; ?>">
                        <?php echo htmlspecialchars($row['Course_Name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="delete_course">Delete Course</button>
        </form>
        <div class="back-button-container">
            <a href="admin_dashboard.php" class="back-button">Go Back</a>
        </div>
    </div>
</body>
</html>
