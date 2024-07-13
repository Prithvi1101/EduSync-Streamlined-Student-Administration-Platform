<?php
require_once('TCPDF-main/tcpdf.php');
include 'DB_connection.php';

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch data
$total_users = $con->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_faculties = $con->query("SELECT COUNT(*) AS total FROM users WHERE role = 'faculty'")->fetch_assoc()['total'];
$total_students = $con->query("SELECT COUNT(*) AS total FROM users WHERE role = 'student'")->fetch_assoc()['total'];
$total_parents = $con->query("SELECT COUNT(*) AS total FROM users WHERE role = 'parent'")->fetch_assoc()['total'];
$total_courses = $con->query("SELECT COUNT(*) AS total FROM courses")->fetch_assoc()['total'];
$total_departments = $con->query("SELECT COUNT(*) AS total FROM department")->fetch_assoc()['total'];

$course_details = $con->query("SELECT * FROM courses");
$department_details = $con->query("SELECT * FROM department");
$student_details = $con->query("SELECT * FROM students");
$faculty_details = $con->query("SELECT * FROM faculties");

// Create PDF
class PDF extends TCPDF {
    // Page header
    public function Header() {
        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(0, 15, 'Admin Report', 0, 1, 'C');
        $this->SetFont('helvetica', 'I', 10);
        $this->Cell(0, 10, 'EduSync - Streamlined Education Platform', 0, 1, 'C');
        $this->Ln(5);
    }

    // Page footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();

// Summary
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Summary", 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);

$html = '
<style>
    .summary-table { border-collapse: collapse; width: 100%; }
    .summary-table th, .summary-table td { border: 1px solid #ddd; padding: 8px; }
    .summary-table th { background-color: #f2f2f2; text-align: left; }
    .details-table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    .details-table th, .details-table td { border: 1px solid #ddd; padding: 8px; }
    .details-table th { background-color: #f2f2f2; text-align: left; }
    h2 { color: #007BFF; }
</style>
<table class="summary-table">
    <tr>
        <th>Total Users</th>
        <td>' . $total_users . '</td>
    </tr>
    <tr>
        <th>Total Faculties</th>
        <td>' . $total_faculties . '</td>
    </tr>
    <tr>
        <th>Total Students</th>
        <td>' . $total_students . '</td>
    </tr>
    <tr>
        <th>Total Parents</th>
        <td>' . $total_parents . '</td>
    </tr>
    <tr>
        <th>Total Courses</th>
        <td>' . $total_courses . '</td>
    </tr>
    <tr>
        <th>Total Departments</th>
        <td>' . $total_departments . '</td>
    </tr>
</table>
';

// Course Details
$html .= '<h2>Course Details</h2>
<table class="details-table">
    <tr>
        <th>Course ID</th>
        <th>Course Name</th>
        <th>Course Details</th>
        <th>Department</th>
    </tr>';
while ($row = $course_details->fetch_assoc()) {
    $html .= '
    <tr>
        <td>' . $row['Course_ID'] . '</td>
        <td>' . $row['Course_Name'] . '</td>
        <td>' . $row['Course_Details'] . '</td>
        <td>' . $row['Department'] . '</td>
    </tr>';
}
$html .= '</table>';

// Department Details
$html .= '<h2>Department Details</h2>
<table class="details-table">
    <tr>
        <th>Department ID</th>
        <th>Department Name</th>
    </tr>';
while ($row = $department_details->fetch_assoc()) {
    $html .= '
    <tr>
        <td>' . $row['department_id'] . '</td>
        <td>' . $row['department_name'] . '</td>
    </tr>';
}
$html .= '</table>';

// Student Details
$html .= '<h2>Student Details</h2>
<table class="details-table">
    <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Enrollment Status</th>
        <th>Department</th>
    </tr>';
while ($row = $student_details->fetch_assoc()) {
    $html .= '
    <tr>
        <td>' . $row['Student_ID'] . '</td>
        <td>' . $row['Student_Name'] . '</td>
        <td>' . $row['Enrollment_Status'] . '</td>
        <td>' . $row['department'] . '</td>
    </tr>';
}
$html .= '</table>';

// Faculty Details
$html .= '<h2>Faculty Details</h2>
<table class="details-table">
    <tr>
        <th>Faculty ID</th>
        <th>Faculty Name</th>
        <th>Department</th>
        <th>Email</th>
        <th>Phone Number</th>
    </tr>';
while ($row = $faculty_details->fetch_assoc()) {
    $html .= '
    <tr>
        <td>' . $row['Faculty_ID'] . '</td>
        <td>' . $row['Faculty_Name'] . '</td>
        <td>' . $row['Department'] . '</td>
        <td>' . $row['Email_ID'] . '</td>
        <td>' . $row['Phone_Number'] . '</td>
    </tr>';
}
$html .= '</table>';

// Output the PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('admin_report.pdf', 'D');
?>
