<?php
header("Content-Type: application/json");
require_once '../../db.php';

$result = $conn->query("SELECT department_id, department_name FROM departments");
$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}
echo json_encode($departments);
?>