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
        header('Content-Disposition: attachment; filename="course_report.csv"');
        
        $output = fopen('php://output', 'w');
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
                CONCAT(sem.name, ' ', sem.academic_year) AS semester,
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
        
        while ($row = $result->fetch_assoc()) {
            $row['average_grade'] = number_format($row['average_grade'], 2);
            unset($row['start_date'], $row['end_date']);
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit();
    } elseif ($export === 'pdf') {
        // Handle PDF export
        require_once '../vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        
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
        $mpdf->WriteHTML($html);
        $mpdf->Output('course_report.pdf', 'D');
        exit();
    } else {
        // Return JSON data
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
            $isCurrent = ($currentDate >= $row['start_date'] && $currentDate <= $row['end_date']);
            $isPrevious = ($currentDate > $row['end_date']);
            
            $reports[] = [
                'course_code' => $row['course_code'],
                'course_name' => $row['course_name'],
                'instructor' => $row['instructor'],
                'enrollment_count' => $row['enrollment_count'],
                'average_grade' => number_format($row['average_grade'], 2),
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