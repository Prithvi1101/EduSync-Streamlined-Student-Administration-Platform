<?php
require_once('TCPDF-main/tcpdf.php');
include 'DB_connection.php';

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'parent') {
    header('Location: login.php');
    exit;
}

$parent_username = $_SESSION['username'];

// Get parent details using username
$sql = "SELECT * FROM parents WHERE parent_name = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $parent_username);
$stmt->execute();
$result = $stmt->get_result();
$parent = $result->fetch_assoc();

if ($parent) {
    $parent_name = $parent['parent_name'];
    $student_id = $parent['student_id'];
} else {
    echo "Parent details not found.";
    exit;
}

// Fetch academic data
$marks_sql = "
    SELECT 
        c.Course_Name, 
        et.Exam_Type_Name, 
        m.Obtained_Marks, 
        m.Total_Marks,
        (SELECT MAX(Obtained_Marks) FROM marks WHERE Course_ID = m.Course_ID AND Exam_ID = m.Exam_ID) AS Highest_Marks
    FROM 
        marks m 
    JOIN 
        courses c ON m.Course_ID = c.Course_ID 
    JOIN 
        examinations e ON m.Exam_ID = e.Exam_ID 
    JOIN 
        exam_types et ON e.Exam_Type_ID = et.Exam_Type_ID 
    WHERE 
        m.Student_ID = ?";
$marks_stmt = $con->prepare($marks_sql);
$marks_stmt->bind_param("i", $student_id);
$marks_stmt->execute();
$marks_result = $marks_stmt->get_result();

// Fetch attendance data
$attendance_sql = "
    SELECT 
        c.Course_Name, 
        COUNT(*) AS Total_Classes, 
        SUM(CASE WHEN a.Status = 'Present' THEN 1 ELSE 0 END) AS Classes_Attended,
        ROUND(SUM(CASE WHEN a.Status = 'Present' THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS Attendance_Percentage
    FROM 
        attendance a 
    JOIN 
        courses c ON a.Course_ID = c.Course_ID 
    WHERE 
        a.Student_ID = ?
    GROUP BY 
        a.Course_ID";
$attendance_stmt = $con->prepare($attendance_sql);
$attendance_stmt->bind_param("i", $student_id);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();

// Create PDF
$pdf = new TCPDF();
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "Student Academic and Attendance Report", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 10, "Parent: " . $parent_name, 0, 1);
$pdf->Cell(0, 10, "Student ID: " . $student_id, 0, 1);

// Academic Report
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, "Academic Report", 0, 1);
$pdf->SetFont('helvetica', '', 10);

if ($marks_result->num_rows > 0) {
    $pdf->Cell(50, 10, "Course", 1);
    $pdf->Cell(30, 10, "Exam Type", 1);
    $pdf->Cell(30, 10, "Obtained Marks", 1);
    $pdf->Cell(30, 10, "Total Marks", 1);
    $pdf->Cell(30, 10, "Highest Marks", 1);
    $pdf->Ln();
    
    while ($row = $marks_result->fetch_assoc()) {
        $pdf->Cell(50, 10, $row['Course_Name'], 1);
        $pdf->Cell(30, 10, $row['Exam_Type_Name'], 1);
        $pdf->Cell(30, 10, $row['Obtained_Marks'], 1);
        $pdf->Cell(30, 10, $row['Total_Marks'], 1);
        $pdf->Cell(30, 10, $row['Highest_Marks'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, "No academic data available.", 1, 1);
}

// Attendance Report
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, "Attendance Report", 0, 1);
$pdf->SetFont('helvetica', '', 10);

if ($attendance_result->num_rows > 0) {
    $pdf->Cell(60, 10, "Course", 1);
    $pdf->Cell(40, 10, "Total Classes", 1);
    $pdf->Cell(40, 10, "Classes Attended", 1);
    $pdf->Cell(40, 10, "Attendance (%)", 1);
    $pdf->Ln();
    
    while ($row = $attendance_result->fetch_assoc()) {
        $pdf->Cell(60, 10, $row['Course_Name'], 1);
        $pdf->Cell(40, 10, $row['Total_Classes'], 1);
        $pdf->Cell(40, 10, $row['Classes_Attended'], 1);
        $pdf->Cell(40, 10, $row['Attendance_Percentage'] . '%', 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, "No attendance data available.", 1, 1);
}

$pdf->Output('student_report.pdf', 'D');
?>

