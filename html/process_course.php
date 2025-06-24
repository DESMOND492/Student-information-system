<?php
include_once '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit();
}

$course_id = $_POST['course_id'] ?? 0;
$course_code = $_POST['course_code'] ?? '';
$course_name = $_POST['course_name'] ?? '';
$instructor = $_POST['instructor'] ?? '';
$department_id = $_POST['department_id'] ?? null;

if ($course_id) {
    // Update existing course
    $query = "UPDATE courses SET course_code=?, course_name=?, instructor=?, department_id=? WHERE course_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssii", $course_code, $course_name, $instructor, $department_id, $course_id);
} else {
    // Insert new course
    $query = "INSERT INTO courses (course_code, course_name, instructor, department_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $course_code, $course_name, $instructor, $department_id);
}

if ($stmt->execute()) {
    header('Location: courses.php');
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>