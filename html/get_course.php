<?php
include_once 'db.php';
header('Content-Type: application/json');


$course_id = $_GET['id'] ?? 0;
$query = "SELECT * FROM courses WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    echo json_encode($result->fetch_assoc());
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Course not found']);
}

$stmt->close();
$conn->close();
?>