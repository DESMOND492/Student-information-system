<?php
header("Content-Type: application/json");
require_once '../../db.php';

$method = $_SERVER['REQUEST_METHOD'];

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        // Get single course or all courses
        if (isset($_GET['id'])) {
            $courseId = intval($_GET['id']);
            $stmt = $conn->prepare("
                SELECT c.*, d.department_name 
                FROM courses c
                JOIN departments d ON c.department_id = d.department_id
                WHERE c.course_id = ?
            ");
            $stmt->bind_param("i", $courseId);
            $stmt->execute();
            $result = $stmt->get_result();
            $course = $result->fetch_assoc();
            
            if ($course) {
                echo json_encode($course);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Course not found']);
            }
        } else {
            // Get all courses with optional filtering
            $departmentId = isset($_GET['department_id']) ? intval($_GET['department_id']) : null;
            
            $query = "
                SELECT c.*, d.department_name 
                FROM courses c
                JOIN departments d ON c.department_id = d.department_id
            ";
            
            if ($departmentId) {
                $query .= " WHERE c.department_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $departmentId);
            } else {
                $stmt = $conn->prepare($query);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $courses = [];
            
            while ($row = $result->fetch_assoc()) {
                $courses[] = $row;
            }
            
            echo json_encode($courses);
        }
        break;
        
    case 'POST':
        // Create new course
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (empty($data['course_code']) || empty($data['course_name']) || empty($data['department_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit();
        }
        
        $stmt = $conn->prepare("
            INSERT INTO courses 
            (course_code, course_name, instructor, department_id) 
            VALUES (?, ?, ?, ?)
        ");
        $instructor = $data['instructor'] ?? '';
        $stmt->bind_param(
            "sssi", 
            $data['course_code'], 
            $data['course_name'],
            $instructor,
            $data['department_id']
        );
        
        if ($stmt->execute()) {
            $courseId = $conn->insert_id;
            http_response_code(201);
            echo json_encode([
                'message' => 'Course created successfully', 
                'course_id' => $courseId
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to create course: ' . $conn->error]);
        }
        break;
        
    case 'PUT':
        // Update course
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['course_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Course ID is required']);
            exit();
        }
        
        $stmt = $conn->prepare("
            UPDATE courses 
            SET course_code = ?, course_name = ?, instructor = ?, department_id = ?
            WHERE course_id = ?
        ");
        $stmt->bind_param(
            "sssii", 
            $data['course_code'], 
            $data['course_name'],
            $data['instructor'],
            $data['department_id'],
            $data['course_id']
        );
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Course updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to update course: ' . $conn->error]);
        }
        break;
        
    case 'DELETE':
        // Delete course
        $courseId = intval($_GET['id']);
        
        // First check if there are any enrollments for this course
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?");
        $checkStmt->bind_param("i", $courseId);
        $checkStmt->execute();
        $checkStmt->bind_result($enrollmentCount);
        $checkStmt->fetch();
        $checkStmt->close();
        
        if ($enrollmentCount > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Cannot delete course with existing enrollments']);
            exit();
        }
        
        $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
        $stmt->bind_param("i", $courseId);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Course deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to delete course: ' . $conn->error]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>