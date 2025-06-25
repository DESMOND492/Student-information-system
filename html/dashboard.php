<?php  
include_once '../db.php';
session_start();



$stats = [];
$queries = [
    'totalStudents' => "SELECT COUNT(*) FROM students",
    'totalCourses' => "SELECT COUNT(*) FROM courses",
    'totalEnrollments' => "SELECT COUNT(*) FROM enrollments",
    'passRate' => "SELECT ROUND((SUM(CASE WHEN grade IN ('A', 'A-', 'B+', 'B', 'B-', 'C+', 'C') THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) 
                    FROM grades"
];

foreach ($queries as $key => $query) {
    $result = $conn->query($query);
    if ($result) {
        $stats[$key] = $result->fetch_row()[0];
    } else {
        $stats[$key] = 'N/A';
    }
}

$enrollments = [];
$query = "SELECT e.enrollment_id, s.name AS student_name, c.course_name, e.enrollment_date, e.status 
          FROM enrollments e
          JOIN students s ON e.student_id = s.student_id
          JOIN courses c ON e.course_id = c.course_id
          ORDER BY e.enrollment_date DESC LIMIT 5";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $enrollments[] = $row;
    }
}

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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
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
      min-height: 98vh; /* Make sidebar longer */
      background: rgba(45, 62, 251, 0.75);
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 0;
      /* Glassmorphism effect */
      backdrop-filter: blur(16px) saturate(180%);
      -webkit-backdrop-filter: blur(16px) saturate(180%);
      box-shadow: 0 8px 32px 0 rgba(116, 124, 238, 0.92), 0 1.5px 8px 0 rgba(44,62,80,0.10);
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
      width: 38px;
      height: 38px;
      filter: drop-shadow(0 2px 8px rgba(44,62,80,0.10));
    }
    .sidebar-title {
      font-size: 1.3rem;
      font-weight: 700;
      letter-spacing: 1px;
      color: #fff;
      text-shadow: 0 2px 8px rgba(95, 167, 239, 0.62);
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
    .dashboard-cards {
      display: flex;
      gap: 28px;
      margin-bottom: 32px;
      flex-wrap: wrap;
    }
    /* 3D Morph Glass Style for Dashboard Cards */
    .dashboard-card {
      background: rgba(255,255,255,0.18);
      border-radius: 22px;
      box-shadow:
        0 8px 32px 0 rgba(79,91,213,0.13),
        0 1.5px 8px 0 rgba(44,62,80,0.10),
        0 2px 16px 0 rgba(44,62,80,0.08);
      display: flex;
      align-items: center;
      gap: 18px;
      padding: 28px 38px;
      min-width: 210px;
      flex: 1 1 180px;
      backdrop-filter: blur(12px) saturate(160%);
      -webkit-backdrop-filter: blur(12px) saturate(160%);
      border: 1.5px solid rgba(255,255,255,0.22);
      transition: box-shadow 0.25s, transform 0.18s;
      position: relative;
      overflow: hidden;
    }
    .dashboard-card:hover {
      box-shadow:
        0 16px 48px 0 rgba(79,91,213,0.18),
        0 4px 24px 0 rgba(44,62,80,0.13);
      transform: translateY(-4px) scale(1.025);
      background: rgba(255,255,255,0.28);
    }
    .card-icon {
      font-size: 2.1em;
      border-radius: 16px;
      width: 54px;
      height: 54px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 0;
      box-shadow: 0 2px 12px rgba(79,91,213,0.10);
      background: rgba(255,255,255,0.32);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
    }
    .card-value {
      font-size: 1.6em;
      font-weight: 700;
      color: #222;
      text-shadow: 0 2px 8px rgba(44,62,80,0.07);
    }
    .card-label {
      font-size: 1em;
      color: #888;
      font-weight: 500;
      text-shadow: 0 1px 4px rgba(44,62,80,0.04);
    }
    .enrollments-section {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(79,91,213,0.07);
      padding: 28px 24px 24px 24px;
    }
    .enrollments-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }
    .enrollments-header h2 {
      font-size: 1.2em;
      font-weight: 700;
      margin: 0;
      color: #222;
    }
    .add-enrollment-btn {
      background: #fff;
      color: #4f5bd5;
      border: 1.5px solid #4f5bd5;
      border-radius: 8px;
      padding: 7px 18px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }
    .add-enrollment-btn:hover {
      background: #4f5bd5;
      color: #fff;
    }
    .enrollments-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 1em;
    }
    .enrollments-table th, .enrollments-table td {
      padding: 12px 8px;
      text-align: left;
    }
    .enrollments-table th {
      color: #888;
      font-weight: 600;
      font-size: 0.98em;
      border-bottom: 2px solid #f0f0f0;
    }
    .enrollments-table tr:not(:last-child) td {
      border-bottom: 1px solid #f0f0f0;
    }
    .status-badge {
      display: inline-block;
      padding: 3px 14px;
      border-radius: 12px;
      font-size: 0.95em;
      font-weight: 600;
      color: #3ecf8e;
      background: #e3f9ed;
    }
    .status-badge.inactive {
      color: #e85a71;
      background: #fde3e3;
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
    }
    .action-btn.edit:hover {
      background: #e3e8fd;
    }
    .action-btn.delete {
      color: #e85a71;
    }
    .action-btn.delete:hover {
      background: #fde3e3;
    }
    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0; width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.18);
      align-items: center;
      justify-content: center;
    }
    .modal.active {
      display: flex;
    }
    .modal-content {
      background: #fff;
      border-radius: 12px;
      padding: 28px 32px 22px 32px;
      min-width: 320px;
      box-shadow: 0 4px 24px rgba(79,91,213,0.13);
      display: flex;
      flex-direction: column;
      gap: 16px;
      position: relative;
    }
    .modal-content h3 {
      margin: 0 0 8px 0;
      font-size: 1.15em;
      font-weight: 700;
      color: #222;
    }
    .modal-content label {
      font-size: 0.98em;
      font-weight: 500;
      margin-bottom: 2px;
      color: #444;
    }
    .modal-content input, .modal-content select {
      padding: 7px 10px;
      border-radius: 6px;
      border: 1px solid #e0e0e0;
      font-size: 1em;
      margin-bottom: 10px;
      width: 100%;
    }
    .modal-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 8px;
    }
    .modal-btn {
      padding: 7px 18px;
      border-radius: 7px;
      border: none;
      font-weight: 600;
      font-size: 1em;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }
    .modal-btn.save {
      background: #4f5bd5;
      color: #fff;
    }
    .modal-btn.cancel {
      background: #f0f0f0;
      color: #222;
    }
    .modal-close {
      position: absolute;
      right: 18px;
      top: 14px;
      font-size: 1.2em;
      color: #888;
      background: none;
      border: none;
      cursor: pointer;
    }
    @media (max-width: 900px) {
      .dashboard-cards { flex-direction: column; gap: 18px; }
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
    <a href="student-mng.html" style="color:#4f5bd5;background:none;padding:8px 18px 8px 10px;border-radius:6px;text-decoration:none;font-weight:600;font-size:1.08em;display:inline-flex;align-items:center;gap:6px;transition:background 0.2s;">
      <span style="font-size:1.2em;">&#8592;</span>
      <span>Home</span>
    </a>
  </div>
  <div class="container">
    
    <aside class="sidebar">
      <div class="sidebar-header">
        <img src="../icons and images/school_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.svg" class="sidebar-logo">
        <span class="sidebar-title">SIS<br>Dashboard</span>
      </div>
      <nav class="sidebar-nav">
        <a class="sidebar-link active" href="dashboard.php"><span class="sidebar-icon">🏠</span> Dashboard</a>
        <a class="sidebar-link" href="student.php"><span class="sidebar-icon">👤</span> Students</a>
        <a class="sidebar-link" href="courses.php"><span class="sidebar-icon">📚</span> Courses</a>
        <a class="sidebar-link" href="enrollment.php"><span class="sidebar-icon">📝</span> Enrollments</a>
        <a class="sidebar-link" href="grades.php"><span class="sidebar-icon">✅</span> grades</a>
        <a class="sidebar-link" href="reports.php"><span class="sidebar-icon">📊</span> Reports</a>
        <a class="sidebar-link" href="logout.php"><span class="sidebar-icon">🚪</span> Logout</a>
      </nav>
    </aside>
    <main class="main-content">
      <header class="dashboard-header">
        <h1>Dashboard Overview</h1>
        <div class="user-profile">
          <span class="user-avatar"><?php echo substr($user['username'] ?? 'User', 0, 2); ?></span>
          <span class="user-name"><?php echo htmlspecialchars($user['username'] ?? 'User'); ?></span>
        </div>
      </header>
      <section class="dashboard-cards">
        <div class="dashboard-card">
          <div class="card-icon card-icon-blue">🎓</div>
          <div>
            <div class="card-value" id="totalStudents"><?php echo $stats['totalStudents']; ?></div>
            <div class="card-label">Total Students</div>
          </div>
        </div>
        <div class="dashboard-card">
          <div class="card-icon card-icon-pink">📄</div>
          <div>
            <div class="card-value" id="totalCourses"><?php echo $stats['totalCourses']; ?></div>
            <div class="card-label">Available Courses</div>
          </div>
        </div>
        <div class="dashboard-card">
          <div class="card-icon card-icon-green">📋</div>
          <div>
            <div class="card-value" id="totalEnrollments"><?php echo $stats['totalEnrollments']; ?></div>
            <div class="card-label">Course Enrollments</div>
          </div>
        </div>
        <div class="dashboard-card">
          <div class="card-icon card-icon-yellow">🏆</div>
          <div>
            <div class="card-value" id="passRate"><?php echo $stats['passRate'] ?? 0; ?>%</div>
            <div class="card-label">Pass Rate</div>
          </div>
        </div>
      </section>
      <section class="enrollments-section">
        <div class="enrollments-header">
          <h2>Recent Enrollments</h2>
          <!-- <button class="add-enrollment-btn" id="addEnrollmentBtn">+ Add Enrollment</button> -->
        </div>
        <table class="enrollments-table" id="enrollmentsTable">
          <thead>
            <tr>
              <th>STUDENT</th>
              <th>COURSE</th>
              <th>DATE</th>
              <th>STATUS</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($enrollments as $index => $enroll): ?>
            <tr data-id="<?php echo $enroll['enrollment_id']; ?>">
              <td><?php echo htmlspecialchars($enroll['student_name']); ?></td>
              <td><?php echo htmlspecialchars($enroll['course_name']); ?></td>
              <td><?php echo date('M j, Y', strtotime($enroll['enrollment_date'])); ?></td>
              <td>
                <span class="status-badge <?php echo $enroll['status'] === 'active' ? 'active' : 'inactive'; ?>">
                  <?php echo ucfirst($enroll['status']); ?>
                </span>
              </td>
              <td>
                <button class="action-btn edit" title="Edit">&#9998;</button>
                <button class="action-btn delete" title="Delete">&#128465;</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <!-- Modal for Add/Edit Enrollment -->
  <div class="modal" id="enrollmentModal">
    <div class="modal-content">
      <button class="modal-close" id="closeModalBtn">&times;</button>
      <h3 id="modalTitle">Add Enrollment</h3>
      <form id="enrollmentForm" action="process_enrollment.php" method="POST">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="enrollment_id" id="enrollmentId">
        <label for="studentName">Student Name</label>
        <select id="studentName" name="student_id" required>
          <?php
          $result = $conn->query("SELECT student_id, name FROM students ORDER BY name");
          while ($row = $result->fetch_assoc()) {
              echo "<option value='{$row['student_id']}'>{$row['name']}</option>";
          }
          $conn->close();
          ?>
        </select>
        <label for="courseName">Course</label>
        <select id="courseName" name="course_id" required>
          <?php
          $result = $conn->query("SELECT course_id, course_name FROM courses ORDER BY course_name");
          while ($row = $result->fetch_assoc()) {
              echo "<option value='{$row['course_id']}'>{$row['course_name']}</option>";
          }
          $conn->close();
          ?>
        </select>
        <label for="enrollDate">Date</label>
        <input type="date" id="enrollDate" name="enrollment_date" required value="<?php echo date('Y-m-d'); ?>">
        <label for="status">Status</label>
        <select id="status" name="status">
          <option value="active">Active</option>
          <option value="dropped">Dropped</option>
          <option value="completed">Completed</option>
        </select>
        <div class="modal-actions">
          <button type="button" class="modal-btn cancel" id="cancelBtn">Cancel</button>
          <button type="submit" class="modal-btn save">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
const enrollmentModal = document.getElementById('enrollmentModal');
const addEnrollmentBtn = document.getElementById('addEnrollmentBtn');
const closeModalBtn = document.getElementById('closeModalBtn');
const cancelBtn = document.getElementById('cancelBtn');
const enrollmentForm = document.getElementById('enrollmentForm');
const formAction = document.getElementById('formAction');
const enrollmentIdInput = document.getElementById('enrollmentId');

function openModal(mode = 'add', data = {}) {
  enrollmentModal.classList.add('active');
  formAction.value = mode;
  document.getElementById('modalTitle').textContent = mode === 'edit' ? "Edit Enrollment" : "Add Enrollment";
  if (mode === 'edit') {
    enrollmentIdInput.value = data.enrollment_id;
    document.getElementById('studentName').value = data.student_id;
    document.getElementById('courseName').value = data.course_id;
    document.getElementById('enrollDate').value = data.enrollment_date;
    document.getElementById('status').value = data.status;
  } else {
    enrollmentForm.reset();
    enrollmentIdInput.value = '';
    document.getElementById('enrollDate').value = (new Date()).toISOString().slice(0,10);
  }
}
function closeModal() {
  enrollmentModal.classList.remove('active');
}
addEnrollmentBtn.onclick = () => openModal('add');
closeModalBtn.onclick = closeModal;
cancelBtn.onclick = closeModal;
window.onclick = function(event) {
  if (event.target === enrollmentModal) closeModal();
};

// Edit button
document.querySelectorAll('.action-btn.edit').forEach(btn => {
  btn.onclick = function() {
    const row = this.closest('tr');
    const enrollment_id = row.getAttribute('data-id');
    // You need to fetch the rest of the data, ideally from the backend or by embedding more data attributes
    // For demo, you can use hidden columns or fetch via AJAX
    // Here, let's assume you have all data in the row's cells in order:
    const cells = row.children;
    openModal('edit', {
      enrollment_id,
      student_id: cells[0].getAttribute('data-student-id'), // You need to add this attribute in PHP
      course_id: cells[1].getAttribute('data-course-id'),   // You need to add this attribute in PHP
      enrollment_date: cells[2].getAttribute('data-date'),  // You need to add this attribute in PHP
      status: cells[3].textContent.trim().toLowerCase()
    });
  };
});

// Delete button
document.querySelectorAll('.action-btn.delete').forEach(btn => {
  btn.onclick = function() {
    const row = this.closest('tr');
    const enrollment_id = row.getAttribute('data-id');
    if (confirm("Are you sure you want to delete this enrollment?")) {
      // Create a form and submit
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'process_enrollment.php';
      form.innerHTML = `
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="enrollment_id" value="${enrollment_id}">
      `;
      document.body.appendChild(form);
      form.submit();
    }
  };
});
  </script>
</body>
</html>