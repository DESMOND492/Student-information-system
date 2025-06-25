<?php
header('Content-Type: application/json');
require_once '../../db.php';

$type = $_GET['type'] ?? 'student';

if ($type === 'student') {
    // Student report: student id, name, course, grade, status
    $sql = "SELECT 
                s.student_id AS id,
                s.name,
                c.course_name AS course,
                e.score AS grade,
                CASE WHEN e.score >= 50 THEN 'Passed' ELSE 'Failed' END AS status
            FROM enrollments e
            JOIN students s ON e.student_id = s.student_id
            JOIN courses c ON e.course_id = c.course_id";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else if ($type === 'course') {
    // Course report: course code, name, instructor, enrolled, avgGrade
    $sql = "SELECT 
                c.course_code AS id,
                c.course_name AS name,
                c.instructor,
                COUNT(e.enrollment_id) AS enrolled,
                ROUND(AVG(e.score), 2) AS avgGrade
            FROM courses c
            LEFT JOIN enrollments e ON c.course_id = e.course_id
            GROUP BY c.course_id";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['avgGrade'] = $row['avgGrade'] ? $row['avgGrade'] : 'N/A';
        $data[] = $row;
    }
    echo json_encode($data);
}
$conn->close();
?>