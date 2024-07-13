<?php
session_start();
include 'DB_connection.php'; // Ensure this path is correct

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Fetching courses for the dropdown
$courses = $con->query("SELECT Course_ID, Course_Name FROM courses");
$departments = $con->query("SELECT department_id, department_name FROM department");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {
    $courseId = $_POST['course_id'];
    $courseName = $_POST['course_name'];
    $courseDetails = $_POST['course_details'];
    $departmentDetails = explode('|', $_POST['department_id']);
    $departmentId = $departmentDetails[0];
    $departmentName = $departmentDetails[1];
    $semester = $_POST['semester'];

    // Begin transaction
    $con->begin_transaction();

    try {
        // Update courses table
        $stmt = $con->prepare("UPDATE courses SET Course_Name = ?, Course_Details = ?, Department = ?, department_id = ? WHERE Course_ID = ?");
        $stmt->bind_param("sssii", $courseName, $courseDetails, $departmentName, $departmentId, $courseId);
        $stmt->execute();

        // Update enrollment_status table
        $stmt = $con->prepare("UPDATE enrollment_status SET Semester = ? WHERE Course_ID = ?");
        $stmt->bind_param("si", $semester, $courseId);
        $stmt->execute();

        // Commit transaction
        $con->commit();
        
        echo "<p>Course updated successfully.</p>";
    } catch (Exception $e) {
        // Rollback transaction
        $con->rollback();
        echo "Error updating course: " . $e->getMessage();
    }
}

// Fetch course details if a course is selected
$selected_course = null;
if (isset($_GET['course_id'])) {
    $selected_course_id = $_GET['course_id'];
    $stmt = $con->prepare("SELECT * FROM courses WHERE Course_ID = ?");
    $stmt->bind_param("i", $selected_course_id);
    $stmt->execute();
    $selected_course = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Course</title>
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
        input, textarea, select {
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
        .back-button-container {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Course</h1>
        <form method="GET">
            <label for="course_id">Select Course:</label>
            <select id="course_id" name="course_id" onchange="this.form.submit()">
                <option value="">Select a course</option>
                <?php while($row = $courses->fetch_assoc()): ?>
                    <option value="<?php echo $row['Course_ID']; ?>" <?php if (isset($selected_course_id) && $selected_course_id == $row['Course_ID']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($row['Course_Name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
        <?php if ($selected_course): ?>
            <form method="POST">
                <input type="hidden" name="course_id" value="<?php echo $selected_course['Course_ID']; ?>">

                <label for="course_name">Course Name:</label>
                <input type="text" id="course_name" name="course_name" value="<?php echo htmlspecialchars($selected_course['Course_Name']); ?>" required>
                
                <label for="course_details">Course Details:</label>
                <textarea id="course_details" name="course_details" required><?php echo htmlspecialchars($selected_course['Course_Details']); ?></textarea>
                
                <label for="department_id">Department:</label>
                <select id="department_id" name="department_id">
                    <?php while($row = $departments->fetch_assoc()): ?>
                        <option value="<?php echo $row['department_id'] . '|' . $row['department_name']; ?>" <?php if ($selected_course['department_id'] == $row['department_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($row['department_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label for="semester">Semester:</label>
                <input type="text" id="semester" name="semester" value="<?php
                    $stmt = $con->prepare("SELECT Semester FROM enrollment_status WHERE Course_ID = ?");
                    $stmt->bind_param("i", $selected_course['Course_ID']);
                    $stmt->execute();
                    $semester_result = $stmt->get_result();
                    $semester = $semester_result->fetch_assoc()['Semester'];
                    echo htmlspecialchars($semester);
                ?>" required>
                
                <button type="submit" name="update_course">Update Course</button>
            </form>
        <?php endif; ?>
        <div class="back-button-container">
            <a href="admin_dashboard.php" class="back-button">Go Back</a>
        </div>
    </div>
</body>
</html>
