<?php
include_once '../db.php';
session_start();

// // Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit();
// }




// Get current user info
$user = [];
$query = "SELECT username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
}
$stmt->close();

// Get all courses
$courses = [];
$query = "SELECT c.course_id, c.course_code, c.course_name, c.instructor, 
          COUNT(e.enrollment_id) AS enrolled_count
          FROM courses c
          LEFT JOIN enrollments e ON c.course_id = e.course_id
          GROUP BY c.course_id
          ORDER BY c.course_code";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
   <style>
    body {
      margin: 0;
      font-family: 'Poppins', Arial, sans-serif;
      background: #f7f8fa;
      color: #222;
    }
    .container {
      display: flex;
      min-height: 100vh;
    }
    .sidebar {
      width: 250px;
      min-height: 96vh;
      background: rgba(35, 53, 248, 0.75);
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 0;
      /* Glassmorphism effect */
      backdrop-filter: blur(16px) saturate(180%);
      -webkit-backdrop-filter: blur(16px) saturate(180%);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18), 0 1.5px 8px 0 rgba(44,62,80,0.10);
      border-radius: 24px;
      margin: 18px 0 18px 18px;
      border: 1.5px solid rgba(255,255,255,0.18);
      transition: box-shadow 0.3s;
    }
    .sidebar:hover {
      box-shadow: 0 12px 32px 0 rgba(31, 38, 135, 0.28), 0 2px 12px 0 rgba(44,62,80,0.13);
    }
    .sidebar-header {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 32px 24px 24px 24px;
      border-bottom: 1px solid rgba(255,255,255,0.13);
    }
    .sidebar-logo {
      width: 40px;
      height: 38px;
      filter: drop-shadow(0 2px 8px rgba(44,62,80,0.10));
    }
    .sidebar-title {
      font-size: 1.3rem;
      font-weight: 700;
      letter-spacing: 1px;
      color: white;
      text-shadow: 0 2px 8px rgba(44,62,80,0.10);
    }
    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 28px;
      padding: 0 10px;
    }
    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 13px 22px;
      color: #e3e3e3;
      text-decoration: none;
      font-weight: 500;
      border-radius: 14px;
      font-size: 1.08rem;
      background: rgba(255,255,255,0.08);
      box-shadow: 0 2px 8px 0 rgba(44,62,80,0.08);
      transition: 
        background 0.2s, 
        color 0.2s, 
        box-shadow 0.2s, 
        transform 0.18s;
      border: 1px solid rgba(255,255,255,0.10);
      backdrop-filter: blur(2px);
      position: relative;
    }
    .sidebar-link.active, .sidebar-link:hover {
      background: rgba(255,255,255,0.22);
      color: #4f5bd5;
      box-shadow: 0 4px 16px 0 rgba(44,62,80,0.13);
      transform: translateY(-2px) scale(1.03);
      border: 1.5px solid #fff;
      z-index: 2;
    }
    .sidebar-link .sidebar-icon {
      font-size: 1.25em;
      filter: drop-shadow(0 1px 4px rgba(44,62,80,0.10));
      transition: color 0.2s;
    }
    .sidebar-link.active .sidebar-icon,
    .sidebar-link:hover .sidebar-icon {
      color: #4f5bd5;
      text-shadow: 0 2px 8px rgba(44,62,80,0.10);
    }
    .main-content {
      flex: 1;
      padding: 36px 40px 36px 40px;
      background: #f7f8fa;
      min-width: 0;
    }
    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 28px;
    }
    .dashboard-header h1 {
      font-size: 2rem;
      font-weight: 700;
      margin: 0;
      color: #222;
    }
    .user-profile {
      display: flex;
      align-items: center;
      gap: 12px;
      background: #fff;
      padding: 8px 18px 8px 8px;
      border-radius: 24px;
      box-shadow: 0 2px 8px rgba(79,91,213,0.07);
    }
    .user-avatar {
      background: #4f5bd5;
      color: #fff;
      font-weight: 700;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1em;
      margin-right: 4px;
    }
    .user-name {
      font-size: 1em;
      color: #222;
      font-weight: 500;
    }
    .course-section {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(79,91,213,0.07);
      padding: 28px 24px 24px 24px;
    }
    .course-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }
    .course-header h2 {
      font-size: 1.2em;
      font-weight: 700;
      margin: 0;
      color: #222;
    }
    .add-course-btn {
      background: #4f5bd5;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 24px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }
    .add-course-btn:hover {
      background: #3b47b2;
    }
    .course-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 1em;
    }
    .course-table th, .course-table td {
      padding: 14px 8px;
      text-align: left;
    }
    .course-table th {
      color: #888;
      font-weight: 600;
      font-size: 0.98em;
      border-bottom: 2px solid #f0f0f0;
    }
    .course-table tr:not(:last-child) td {
      border-bottom: 1px solid #f0f0f0;
    }
    .action-btn {
      border: none;
      background: none;
      cursor: pointer;
      font-size: 1.15em;
      margin-right: 6px;
      padding: 4px 6px;
      border-radius: 6px;
      transition: background 0.15s;
    }
    .action-btn.edit {
      color: #4f5bd5;
      background: #e3e8fd;
    }
    .action-btn.edit:hover {
      background: #d1d8fa;
    }
    .action-btn.delete {
      color: #e85a71;
      background: #fde3e3;
    }
    .action-btn.delete:hover {
      background: #fbcaca;
    }
    @media (max-width: 900px) {
      .main-content { padding: 24px 8px; }
      .sidebar { width: 60px; }
      .sidebar-title { display: none; }
      .sidebar-header { padding: 24px 8px 16px 8px; }
      .sidebar-link { padding: 10px 8px; font-size: 0.95em; }
    }
    @media (max-width: 600px) {
      .container { flex-direction: column; }
      .sidebar { flex-direction: row; width: 100vw; height: 60px; }
      .sidebar-nav { flex-direction: row; gap: 2px; margin-top: 0; }
      .main-content { padding: 12px 2vw; }
    }
  </style>
</head>
<body>
  <div style="width:100%;background:#f7f8fa;padding:14px 0 0 0;text-align:right;">
    <a href="dashboard.php" style="color:#4f5bd5;background:none;padding:8px 18px 8px 10px;border-radius:6px;text-decoration:none;font-weight:600;font-size:1.08em;display:inline-flex;align-items:center;gap:6px;transition:background 0.2s;">
      <span style="font-size:1.2em;">&#8592;</span>
      <span>Home</span>
    </a>
  </div>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <img src="../icons and images/school_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.svg" class="sidebar-logo">
        <span class="sidebar-title">SIS<br>Dashboard</span>
      </div>
      <nav class="sidebar-nav">
        <a class="sidebar-link" href="dashboard.php"><span class="sidebar-icon">🏠</span> Dashboard</a>
        <a class="sidebar-link" href="student.php"><span class="sidebar-icon">🎓</span> Students</a>
        <a class="sidebar-link active" href="courses.php"><span class="sidebar-icon">📚</span> Courses</a>
        <a class="sidebar-link" href="enrollment.php"><span class="sidebar-icon">📝</span> Enrollments</a>
        <a class="sidebar-link" href="grades.php"><span class="sidebar-icon">✅</span> grades</a>
        <a class="sidebar-link" href="reports.php"><span class="sidebar-icon">📊</span> Reports</a>
        <a class="sidebar-link" href="logout.php"><span class="sidebar-icon">🚪</span> Logout</a>
      </nav>
    </aside>
    <!-- Main Content -->
    <main class="main-content">
      <header class="dashboard-header">
        <h1>Course Management</h1>
        <div class="user-profile">
          <span class="user-avatar"><?php echo substr($user['username'] ?? "User", 0, 2); ?></span>
          <span class="user-name"><?php echo htmlspecialchars($user['username'] ?? "User"); ?></span>
        </div>
      </header>
      <section class="course-section">
        <div class="course-header">
          <h2>Course Management</h2>
          <button class="add-course-btn" id="addCourseBtn">+ Add New Course</button>
        </div>
        <table class="course-table" id="courseTable">
          <thead>
            <tr>
              <th>COURSE CODE</th>
              <th>COURSE NAME</th>
              <th>INSTRUCTOR</th>
              <th>ENROLLED</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($courses as $course): ?>
            <tr>
              <td><?php echo htmlspecialchars($course['course_code']); ?></td>
              <td><?php echo htmlspecialchars($course['course_name']); ?></td>
              <td><?php echo htmlspecialchars($course['instructor']); ?></td>
              <td><?php echo $course['enrolled_count']; ?></td>
              <td>
                <button class="action-btn edit" title="Edit" data-id="<?php echo $course['course_id']; ?>">&#128393;</button>
                <button class="action-btn delete" title="Delete" data-id="<?php echo $course['course_id']; ?>">&#128465;</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <!-- Modal for Add/Edit Course -->
  <div class="modal" id="courseModal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.18);align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#fff;border-radius:12px;padding:28px 32px 22px 32px;min-width:320px;box-shadow:0 4px 24px rgba(79,91,213,0.13);display:flex;flex-direction:column;gap:16px;position:relative;">
      <button class="modal-close" id="closeModalBtn" style="position:absolute;right:18px;top:14px;font-size:1.2em;color:#888;background:none;border:none;cursor:pointer;">&times;</button>
      <h3 id="modalTitle">Add Course</h3>
      <form id="courseForm" action="process_course.php" method="POST">
        <input type="hidden" id="courseId" name="course_id">
        <label for="courseCode">Course Code</label>
        <input type="text" id="courseCode" name="course_code" required>
        <label for="courseName">Course Name</label>
        <input type="text" id="courseName" name="course_name" required pattern="^[A-Za-z\s\-']+$" title="Only letters, spaces, hyphens, and apostrophes allowed">

        <label for="instructor">Instructor</label>
        <input type="text" id="instructor" name="instructor" required pattern="^[A-Za-z\s\-']+$" title="Only letters, spaces, hyphens, and apostrophes allowed">
        <div class="modal-actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;">
          <button type="button" class="modal-btn cancel" id="cancelBtn" style="padding:7px 18px;border-radius:7px;border:none;font-weight:600;font-size:1em;cursor:pointer;background:#f0f0f0;color:#222;">Cancel</button>
          <button type="submit" class="modal-btn save" style="padding:7px 18px;border-radius:7px;border:none;font-weight:600;font-size:1em;cursor:pointer;background:#4f5bd5;color:#fff;">Save</button>
        </div>
      </form>
    </div>
  </div>

<script>
    // Modal controls
    const courseModal = document.getElementById('courseModal');
    const addCourseBtn = document.getElementById('addCourseBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const courseForm = document.getElementById('courseForm');
    const modalTitle = document.getElementById('modalTitle');
    const courseIdInput = document.getElementById('courseId');
    const courseCodeInput = document.getElementById('courseCode');
    const courseNameInput = document.getElementById('courseName');
    const instructorInput = document.getElementById('instructor');

    function openModal() {
        courseModal.style.display = 'flex';
    }
    function closeModal() {
        courseModal.style.display = 'none';
        courseForm.reset();
        courseIdInput.value = '';
    }

    addCourseBtn.onclick = () => {
        modalTitle.textContent = "Add Course";
        openModal();
    };

    // Edit button functionality
    document.querySelectorAll('.action-btn.edit').forEach(btn => {
        btn.onclick = async function() {
            const courseId = this.getAttribute('data-id');
            
            try {
                const response = await fetch(`get_course.php?id=${courseId}`);
                if (!response.ok) throw new Error('Failed to fetch course');
                
                const course = await response.json();
                
                modalTitle.textContent = "Edit Course";
                courseIdInput.value = course.course_id;
                courseCodeInput.value = course.course_code;
                courseNameInput.value = course.course_name;
                instructorInput.value = course.instructor;
                
                openModal();
            } catch (error) {
                console.error('Error:', error);
                alert('Error loading course data');
            }
        };
    });

    // Delete button functionality
    document.querySelectorAll('.action-btn.delete').forEach(btn => {
        btn.onclick = function() {
            const courseId = this.getAttribute('data-id');
            if (confirm("Are you sure you want to delete this course?")) {
                fetch('delete_course.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `course_id=${courseId}`
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        throw new Error('Failed to delete course');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting course');
                });
            }
        };
    });

    closeModalBtn.onclick = closeModal;
    cancelBtn.onclick = closeModal;
    window.onclick = function(event) {
        if (event.target === courseModal) closeModal();
    };
</script>
</body>
</html>