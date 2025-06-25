<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require_once '../../db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Fetch all grades
    $result = $conn->query("SELECT g.grade_id, s.name AS student, c.course_name AS course, c.instructor, g.grade 
        FROM grades g
        JOIN students s ON g.student_id = s.student_id
        JOIN courses c ON g.course_id = c.course_id
        ORDER BY g.grade_id DESC");
    $grades = [];
    while ($row = $result->fetch_assoc()) {
        $grades[] = $row;
    }
    echo json_encode($grades);
} elseif ($method === 'POST') {
    // Add a new grade
    $data = json_decode(file_get_contents("php://input"), true);
    $student_id = intval($data['student_id'] ?? 0);
    $course_id = intval($data['course_id'] ?? 0);
    $grade = $conn->real_escape_string($data['grade'] ?? '');

    // Validate input
    if (!$student_id || !$course_id || $grade === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO grades (student_id, course_id, grade) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $student_id, $course_id, $grade);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $stmt->close();
}
$conn->close();
?>