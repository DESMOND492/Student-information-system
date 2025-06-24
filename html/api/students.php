<?php
header("Content-Type: application/json");
require_once '../../db.php';

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':

        if (isset($_GET['id'])) {
            $studentId = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            $student = $result->fetch_assoc();
            
            if ($student) {
                echo json_encode($student);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Student not found']);
            }
        } else {
            $result = $conn->query("SELECT * FROM students");
            $students = [];
            while ($row = $result->fetch_assoc()) {
                // Add status (you might need to calculate this based on your business logic)
                $row['status'] = 'Active'; // Default status
                $students[] = $row;
            }
            echo json_encode($students);
        }
        break;
        
    case 'POST':
        // Create new student
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $conn->prepare("INSERT INTO students (name, email, phone, department_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $data['name'], $data['email'], $data['phone'], $data['department_id']);
        
        if ($stmt->execute()) {
            $studentId = $conn->insert_id;
            
            // Also create a user account
            $userStmt = $conn->prepare("INSERT INTO users (student_id, username, password, role) VALUES (?, ?, ?, 'student')");
            $username = strtolower(str_replace(' ', '.', $data['name']));
            $password = password_hash('default123', PASSWORD_DEFAULT);
            $userStmt->bind_param("iss", $studentId, $username, $password);
            $userStmt->execute();
            
            http_response_code(201);
            echo json_encode(['message' => 'Student created successfully', 'student_id' => $studentId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to create student']);
        }
        break;
        
    case 'PUT':
        // Update student
        $data = json_decode(file_get_contents('php://input'), true);
        $studentId = intval($data['student_id']);
        
        $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, phone = ? WHERE student_id = ?");
        $stmt->bind_param("sssi", $data['name'], $data['email'], $data['phone'], $studentId);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Student updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to update student']);
        }
        break;
        
    case 'DELETE':
        // Delete student
        $studentId = intval($_GET['id']);
        
        // First delete the user account (due to foreign key constraint)
        $userStmt = $conn->prepare("DELETE FROM users WHERE student_id = ?");
        $userStmt->bind_param("i", $studentId);
        $userStmt->execute();
        
        // Then delete the student
        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $studentId);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Student deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to delete student']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>