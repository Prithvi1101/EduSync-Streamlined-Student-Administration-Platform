<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'faculty' || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

include 'DB_connection.php';

$faculty_name = $_SESSION['username'];

// Fetch faculty ID from session
$stmt = $con->prepare("SELECT faculty_id FROM faculties WHERE faculty_name = ?");
if (!$stmt) {
    die("Prepare failed: (" . $con->errno . ") " . $con->error);
}
$stmt->bind_param("s", $faculty_name);
$stmt->execute();
$result = $stmt->get_result();
$faculty = $result->fetch_assoc();
$faculty_id = $faculty['faculty_id'];

$examTypes = [
    'Class test-1',
    'Class test-2',
    'Class test-3',
    'Class test-4',
    'Assignment/Presentation/Term paper',
    'Mid Term',
    'Attendance'
];

$selected_course_id = null;
$students = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['load_students'])) {
        $selected_course_id = $_POST['course_id'];

        // Fetch students enrolled in this course
        $stmt = $con->prepare("SELECT s.Student_ID, s.Student_Name 
                               FROM students s 
                               JOIN enrollment_status e ON s.Student_ID = e.Student_ID 
                               WHERE e.Course_ID = ? AND s.department_id = (SELECT department_id FROM courses WHERE Course_ID = ?)");
        if (!$stmt) {
            die("Prepare failed: (" . $con->errno . ") " . $con->error);
        }
        $stmt->bind_param("ii", $selected_course_id, $selected_course_id);
        $stmt->execute();
        $students = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provide Marks</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { width: 80%; margin: auto; background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #eee; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        select, button { padding: 8px; margin-top: 10px; margin-right: 10px; }
        .footer { margin-top: 20px; text-align: center; }
        .btn { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #45a049; }
        .back-btn { background-color: #d3d3d3; color: black; }
        .back-btn:hover { background-color: #c0c0c0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Provide Marks</h1>
        <form action="" method="post">
            <label for="course_id">Select Course:</label>
            <select id="course_id" name="course_id">
                <?php
                $stmt = $con->prepare("SELECT Course_ID, Course_Name FROM courses WHERE course_faculty_id = ?");
                if (!$stmt) {
                    die("Prepare failed: (" . $con->errno . ") " . $con->error);
                }
                $stmt->bind_param("i", $faculty_id);
                $stmt->execute();
                $courses = $stmt->get_result();
                while ($course = $courses->fetch_assoc()) {
                    $selected = ($selected_course_id == $course['Course_ID']) ? 'selected' : '';
                    echo '<option value="' . $course['Course_ID'] . '" ' . $selected . '>' . htmlspecialchars($course['Course_Name']) . '</option>';
                }
                ?>
            </select>
            <?php if ($selected_course_id): ?>
                <span>Course ID: <?php echo $selected_course_id; ?></span>
            <?php endif; ?>
            <button type="submit" name="load_students" class="btn">Load Students</button>
        </form>
        <?php if (!empty($students)): ?>
            <form action="handle_marks_submission.php" method="post">
                <input type="hidden" name="course_id" value="<?php echo $selected_course_id; ?>">
                <label for="exam_id">Select Exam:</label>
                <select id="exam_id" name="exam_id">
                    <?php foreach ($examTypes as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                    <?php endforeach; ?>
                </select>
                <table>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Total Marks</th>
                        <th>Obtained Marks</th>
                    </tr>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $student['Student_ID']; ?></td>
                            <td><?php echo htmlspecialchars($student['Student_Name']); ?></td>
                            <td><input type="text" name="marks[<?php echo $student['Student_ID']; ?>][total]" required></td>
                            <td><input type="text" name="marks[<?php echo $student['Student_ID']; ?>][obtained]" required></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <button type="submit" name="submit_marks" class="btn">Submit Marks</button>
            </form>
        <?php endif; ?>
        <div class="footer">
            <a href="faculty_dashboard.php" class="btn back-btn">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
