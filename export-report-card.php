<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Student') {
    exit("Unauthorized");
}

$student_id = $_SESSION['user_id'];

// Fetch grades and subjects for this student
$sql = "SELECT g.course_code, s.subject_name, g.grade, g.status
        FROM grades g
        JOIN subjects s ON g.course_code = s.course_code
        WHERE g.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// GPA calculation map
$gpa_map = [
    'A+' => 4.0, 'A' => 4.0, 'A-' => 3.7,
    'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
    'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
    'D+' => 1.3, 'D' => 1.0, 'E' => 0.0,
];

$total_gpa = 0;
$count_gpa = 0;

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=report_card.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Course Code', 'Subject', 'Grade', 'Status']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['course_code'], $row['subject_name'], $row['grade'], $row['status']]);
    if (isset($gpa_map[$row['grade']])) {
        $total_gpa += $gpa_map[$row['grade']];
        $count_gpa++;
    }
}

// Calculate GPA
$gpa = $count_gpa > 0 ? round($total_gpa / $count_gpa, 2) : 'N/A';

// Add an empty row, then GPA row
fputcsv($output, []);
fputcsv($output, ['', '', 'GPA', $gpa]);

fclose($output);
exit;
?>