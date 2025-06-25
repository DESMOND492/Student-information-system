<?php
header('Content-Type: application/json');
require_once '../../db.php';

$result = $conn->query("SELECT course_id, course_name, instructor FROM courses ORDER BY course_name");
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
echo json_encode($courses);
$conn->close();
?>