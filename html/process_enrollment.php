<?php
include_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $student_id = intval($_POST['student_id']);
        $course_id = intval($_POST['course_id']);
        $enrollment_date = $_POST['enrollment_date'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id, enrollment_date, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $student_id, $course_id, $enrollment_date, $status);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php");
        exit;
    }

    if ($action === 'edit') {
        $enrollment_id = intval($_POST['enrollment_id']);
        $student_id = intval($_POST['student_id']);
        $course_id = intval($_POST['course_id']);
        $enrollment_date = $_POST['enrollment_date'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE enrollments SET student_id=?, course_id=?, enrollment_date=?, status=? WHERE enrollment_id=?");
        $stmt->bind_param("iissi", $student_id, $course_id, $enrollment_date, $status, $enrollment_id);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php");
        exit;
    }

    if ($action === 'delete') {
        $enrollment_id = intval($_POST['enrollment_id']);
        $stmt = $conn->prepare("DELETE FROM enrollments WHERE enrollment_id=?");
        $stmt->bind_param("i", $enrollment_id);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php");
        exit;
    }
}
?>