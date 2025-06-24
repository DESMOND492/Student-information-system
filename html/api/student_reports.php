<?php
header("Content-Type: application/json");
require_once '../../db.php';

try {
    // Check database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    $export = $_GET['export'] ?? null;
    $currentDate = date('Y-m-d');
    $currentYear = date('Y');

    if ($export === 'csv') {
        // Handle CSV export
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="student_report.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student ID', 'Name', 'Course', 'Grade', 'Status', 'Enrollment Date']);
        
        $query = "
            SELECT 
                s.student_id, 
                s.name AS student_name, 
                c.course_name, 
                g.grade, 
                CASE WHEN g.grade IN ('F', 'U') THEN 'failed' ELSE 'passed' END AS status,
                e.enrollment_date,
                sem.start_date,
                sem.end_date,
                sem.academic_year
            FROM enrollments e
            JOIN students s ON e.student_id = s.student_id
            JOIN courses c ON e.course_id = c.course_id
            LEFT JOIN grades g ON g.enrollment_id = e.enrollment_id
            JOIN semesters sem ON e.semester_id = sem.semester_id
            ORDER BY e.enrollment_date DESC
            LIMIT 1000
        ";
        
        $result = $conn->query($query);
        if (!$result) {
            throw new Exception("Query failed: " . $conn->error);
        }
        
        while ($row = $result->fetch_assoc()) {
            unset($row['start_date'], $row['end_date']);
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit();
    } elseif ($export === 'pdf') {
        // Handle PDF export
        require_once '../vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        
        $html = '<h1>Student Report</h1>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0">';
        $html .= '<tr><th>Student ID</th><th>Name</th><th>Course</th><th>Grade</th><th>Status</th></tr>';
        
        $query = "
            SELECT 
                s.student_id, 
                s.name AS student_name, 
                c.course_name, 
                g.grade, 
                CASE WHEN g.grade IN ('F', 'U') THEN 'failed' ELSE 'passed' END AS status
            FROM enrollments e
            JOIN students s ON e.student_id = s.student_id
            JOIN courses c ON e.course_id = c.course_id
            LEFT JOIN grades g ON g.enrollment_id = e.enrollment_id
            ORDER BY e.enrollment_date DESC
            LIMIT 100
        ";
        
        $result = $conn->query($query);
        if (!$result) {
            throw new Exception("Query failed: " . $conn->error);
        }
        
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['student_id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['student_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['course_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['grade'] ?? 'N/A') . '</td>';
            $html .= '<td>' . ucfirst($row['status'] ?? 'N/A') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        $mpdf->WriteHTML($html);
        $mpdf->Output('student_report.pdf', 'D');
        exit();
    } else {
        // Return JSON data
        $query = "
            SELECT 
                s.student_id, 
                s.name AS student_name, 
                c.course_name, 
                g.grade, 
                CASE WHEN g.grade IN ('F', 'U') THEN 'failed' ELSE 'passed' END AS status,
                sem.academic_year,
                sem.start_date,
                sem.end_date
            FROM enrollments e
            JOIN students s ON e.student_id = s.student_id
            JOIN courses c ON e.course_id = c.course_id
            LEFT JOIN grades g ON g.enrollment_id = e.enrollment_id
            JOIN semesters sem ON e.semester_id = sem.semester_id
            ORDER BY e.enrollment_date DESC
            LIMIT 1000
        ";
        
        $result = $conn->query($query);
        if (!$result) {
            throw new Exception("Query failed: " . $conn->error);
        }
        
        $reports = [];
        while ($row = $result->fetch_assoc()) {
            $isCurrent = ($currentDate >= $row['start_date'] && $currentDate <= $row['end_date']);
            $isPrevious = ($currentDate > $row['end_date']);
            
            $reports[] = [
                'student_id' => $row['student_id'],
                'student_name' => $row['student_name'],
                'course_name' => $row['course_name'],
                'grade' => $row['grade'],
                'status' => $row['status'],
                'academic_year' => $row['academic_year'],
                'is_current' => $isCurrent,
                'is_previous' => $isPrevious
            ];
        }
        
        echo json_encode($reports);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit();
}
?>