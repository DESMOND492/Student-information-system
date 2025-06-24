<?php
include_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit();
}


$course_id = $_POST['course_id'] ?? 0;
$query = "DELETE FROM courses WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->close();
$conn->close();

http_response_code(200);
?>