<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "your_db_name"); // Change your_db_name

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT student_id, student_name, score, status FROM enrollment";
$result = $conn->query($sql);

$enrollments = [];
while ($row = $result->fetch_assoc()) {
    $enrollments[] = [
        "id" => $row["student_id"],
        "name" => $row["student_name"],
        "grade" => (int)$row["score"],
        "status" => $row["status"]
    ];
}
echo json_encode($enrollments);
$conn->close();
?>