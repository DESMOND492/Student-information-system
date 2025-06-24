<?php
header("Content-Type: application/json");
require_once '../../db.php';

$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $enrollmentId = intval($_GET['id']);
            $stmt = $conn->prepare("
                SELECT e.*, s.name AS student_name, c.course_name 
                FROM enrollments e
                JOIN students s ON e.student_id = s.student_id
                JOIN courses c ON e.course_id = c.course_id
                WHERE e.enrollment_id = ?
            ");
            $stmt->bind_param("i", $enrollmentId);
            $stmt->execute();
            $result = $stmt->get_result();
            $enrollment = $result->fetch_assoc();
            
            if ($enrollment) {
                echo json_encode($enrollment);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Enrollment not found']);
            }
        } else {
            // Check if detailed view requested
            $details = isset($_GET['details']) && $_GET['details'] == 'true';
            
            if ($details) {
                $query = "
                    SELECT e.*, s.name AS student_name, c.course_name, c.course_code
                    FROM enrollments e
                    JOIN students s ON e.student_id = s.student_id
                    JOIN courses c ON e.course_id = c.course_id
                    ORDER BY e.enrollment_date DESC
                ";
            } else {
                $query = "SELECT * FROM enrollments ORDER BY enrollment_date DESC";
            }
            
            $result = $conn->query($query);
            $enrollments = [];
            while ($row = $result->fetch_assoc()) {
                $enrollments[] = $row;
            }
            echo json_encode($enrollments);
        }
        break;
        
    case 'POST':
        // Create new enrollment
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (empty($data['student_id']) || empty($data['course_id']) || empty($data['enrollment_date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit();
        }
        
        $stmt = $conn->prepare("
            INSERT INTO enrollments 
            (student_id, course_id, enrollment_date, status) 
            VALUES (?, ?, ?, ?)
        ");
        $status = $data['status'] ?? 'active';
        $stmt->bind_param(
            "iiss", 
            $data['student_id'], 
            $data['course_id'], 
            $data['enrollment_date'],
            $status
        );
        
        if ($stmt->execute()) {
            $enrollmentId = $conn->insert_id;
            http_response_code(201);
            echo json_encode([
                'message' => 'Enrollment created successfully', 
                'enrollment_id' => $enrollmentId
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to create enrollment']);
        }
        break;
        
    case 'PUT':
        // Update enrollment
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['enrollment_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Enrollment ID is required']);
            exit();
        }
        
        $stmt = $conn->prepare("
            UPDATE enrollments 
            SET student_id = ?, course_id = ?, enrollment_date = ?, status = ?
            WHERE enrollment_id = ?
        ");
        $status = $data['status'] ?? 'active';
        $stmt->bind_param(
            "iissi", 
            $data['student_id'], 
            $data['course_id'], 
            $data['enrollment_date'],
            $status,
            $data['enrollment_id']
        );
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Enrollment updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to update enrollment']);
        }
        break;
        
    case 'DELETE':
        // Delete enrollment
        $enrollmentId = intval($_GET['id']);
        
        $stmt = $conn->prepare("DELETE FROM enrollments WHERE enrollment_id = ?");
        $stmt->bind_param("i", $enrollmentId);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Enrollment deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to delete enrollment']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>