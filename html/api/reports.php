<?php
header("Content-Type: application/json");
require_once '../db.php';

try {
    // Get request parameters
    $type = $_GET['type'] ?? 'student';
    $export = $_GET['export'] ?? null;
    $currentDate = date('Y-m-d');

    // Set academic year
    $currentYear = date('Y');
    $currentMonth = date('m');
    $academicYear = ($currentMonth >= 8) ? $currentYear : $currentYear - 1;

    // Helper function to determine semester
    function getSemesterStatus($startDate, $endDate) {
        $currentDate = date('Y-m-d');
        $isCurrent = ($currentDate >= $startDate && $currentDate <= $endDate);
        $isPrevious = ($currentDate > $endDate);
        return ['is_current' => $isCurrent, 'is_previous' => $isPrevious];
    }

    // Check database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    if ($export === 'csv') {
        // Handle CSV export
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $type . '_report.csv"');
        
        $output = fopen('php://output', 'w');
        
        if ($type === 'student') {
            // Student report CSV
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
            
            while ($row = $result->fetch_assoc()) {
                $status = getSemesterStatus($row['start_date'], $row['end_date']);
                unset($row['start_date'], $row['end_date']);
                fputcsv($output, $row);
            }
        } else {
            // Course report CSV
            fputcsv($output, ['Course Code', 'Course Name', 'Instructor', 'Enrolled', 'Avg Grade', 'Semester']);
            
            $query = "
                SELECT 
                    c.course_code,
                    c.course_name,
                    c.instructor,
                    COUNT(e.enrollment_id) AS enrollment_count,
                    AVG(CASE 
                        WHEN g.grade = 'A' THEN 4.0
                        WHEN g.grade = 'B' THEN 3.0
                        WHEN g.grade = 'C' THEN 2.0
                        WHEN g.grade = 'D' THEN 1.0
                        ELSE 0
                    END) AS average_grade,
                    CONCAT(sem.name, ' ', sem.academic_year) AS semester
                FROM courses c
                LEFT JOIN enrollments e ON e.course_id = c.course_id
                LEFT JOIN grades g ON g.enrollment_id = e.enrollment_id
                LEFT JOIN semesters sem ON e.semester_id = sem.semester_id
                GROUP BY c.course_id, sem.semester_id
                ORDER BY c.course_name
                LIMIT 1000
            ";
            
            $result = $conn->query($query);
            if (!$result) {
                throw new Exception("Query failed: " . $conn->error);
            }
            
            while ($row = $result->fetch_assoc()) {
                $row['average_grade'] = number_format($row['average_grade'], 2);
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
        exit();
    } elseif ($export === 'pdf') {
        // Handle PDF export
        require_once '../vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        
        if ($type === 'student') {
            // Student report PDF
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
        } else {
            // Course report PDF
            $html = '<h1>Course Report</h1>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<tr><th>Course Code</th><th>Course Name</th><th>Instructor</th><th>Enrolled</th><th>Avg Grade</th></tr>';
            
            $query = "
                SELECT 
                    c.course_code,
                    c.course_name,
                    c.instructor,
                    COUNT(e.enrollment_id) AS enrollment_count,
                    AVG(CASE 
                        WHEN g.grade = 'A' THEN 4.0
                        WHEN g.grade = 'B' THEN 3.0
                        WHEN g.grade = 'C' THEN 2.0
                        WHEN g.grade = 'D' THEN 1.0
                        ELSE 0
                    END) AS average_grade
                FROM courses c
                LEFT JOIN enrollments e ON e.course_id = c.course_id
                LEFT JOIN grades g ON g.enrollment_id = e.enrollment_id
                GROUP BY c.course_id
                ORDER BY c.course_name
                LIMIT 100
            ";
            
            $result = $conn->query($query);
            if (!$result) {
                throw new Exception("Query failed: " . $conn->error);
            }
            
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($row['course_code']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['course_name']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['instructor']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['enrollment_count']) . '</td>';
                $html .= '<td>' . number_format($row['average_grade'], 2) . '</td>';
                $html .= '</tr>';
            }
            
            $html .= '</table>';
        }
        
        $mpdf->WriteHTML($html);
        $mpdf->Output($type . '_report.pdf', 'D');
        exit();
    } else {
        // Handle JSON response for the reports page
        if ($type === 'student') {
            // Student report data
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
                $status = getSemesterStatus($row['start_date'], $row['end_date']);
                $row['is_current'] = $status['is_current'];
                $row['is_previous'] = $status['is_previous'];
                unset($row['start_date'], $row['end_date']);
                $reports[] = $row;
            }
        } else {
            // Course report data
            $query = "
                SELECT 
                    c.course_code,
                    c.course_name,
                    c.instructor,
                    COUNT(e.enrollment_id) AS enrollment_count,
                    AVG(CASE 
                        WHEN g.grade = 'A' THEN 4.0
                        WHEN g.grade = 'B' THEN 3.0
                        WHEN g.grade = 'C' THEN 2.0
                        WHEN g.grade = 'D' THEN 1.0
                        ELSE 0
                    END) AS average_grade,
                    sem.academic_year,
                    sem.start_date,
                    sem.end_date
                FROM courses c
                LEFT JOIN enrollments e ON e.course_id = c.course_id
                LEFT JOIN grades g ON g.enrollment_id = e.enrollment_id
                LEFT JOIN semesters sem ON e.semester_id = sem.semester_id
                GROUP BY c.course_id, sem.semester_id
                ORDER BY c.course_name
                LIMIT 1000
            ";
            
            $result = $conn->query($query);
            if (!$result) {
                throw new Exception("Query failed: " . $conn->error);
            }
            
            $reports = [];
            while ($row = $result->fetch_assoc()) {
                $status = getSemesterStatus($row['start_date'], $row['end_date']);
                $row['is_current'] = $status['is_current'];
                $row['is_previous'] = $status['is_previous'];
                $row['average_grade'] = number_format($row['average_grade'], 2);
                unset($row['start_date'], $row['end_date']);
                $reports[] = $row;
            }
        }
        
        echo json_encode($reports);
    }
} catch (Exception $e) {
    // Proper error handling that returns JSON
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit();
}
?>