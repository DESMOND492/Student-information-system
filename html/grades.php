<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grades Management</title>
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
      width: 220px;
      background: linear-gradient(180deg, #4f5bd5 0%, #5f6ee6 100%);
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 0;
    }
    .sidebar-header {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 32px 24px 24px 24px;
    }
    .sidebar-logo {
      width: 32px;
      height: 32px;
    }
    .sidebar-title {
      font-size: 1.2rem;
      font-weight: 700;
      line-height: 1.1;
    }
    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-top: 24px;
    }
    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 24px;
      color: #e3e3e3;
      text-decoration: none;
      font-weight: 500;
      border-radius: 8px;
      transition: background 0.2s, color 0.2s;
      cursor: pointer;
      font-size: 1rem;
    }
    .sidebar-link.active, .sidebar-link:hover {
      background: #fff;
      color: #4f5bd5;
    }
    .sidebar-icon {
      font-size: 1.2em;
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
    .grades-section {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(79,91,213,0.07);
      padding: 28px 24px 24px 24px;
    }
    .grades-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }
    .grades-header h2 {
      font-size: 1.2em;
      font-weight: 700;
      margin: 0;
      color: #222;
    }
    .add-grade-btn {
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
    .add-grade-btn:hover {
      background: #3b47b2;
    }
    .grades-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 1em;
    }
    .grades-table th, .grades-table td {
      padding: 14px 8px;
      text-align: left;
    }
    .grades-table th {
      color: #888;
      font-weight: 600;
      font-size: 0.98em;
      border-bottom: 2px solid #f0f0f0;
    }
    .grades-table tr:not(:last-child) td {
      border-bottom: 1px solid #f0f0f0;
    }
    .grade-badge {
      display: inline-block;
      padding: 3px 14px;
      border-radius: 12px;
      font-size: 0.95em;
      font-weight: 600;
      color: #fff;
      background: #4f5bd5;
    }
    .grade-badge.fail {
      background: #e85a71;
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
    <a href="student-mng.html" style="color:#4f5bd5;background:none;padding:8px 18px 8px 10px;border-radius:6px;text-decoration:none;font-weight:600;font-size:1.08em;display:inline-flex;align-items:center;gap:6px;transition:background 0.2s;">
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
        <a class="sidebar-link" href="courses.php"><span class="sidebar-icon">📚</span> Courses</a>
        <a class="sidebar-link" href="enrollment.php"><span class="sidebar-icon">📝</span> Enrollments</a>
        <a class="sidebar-link" href="reports.php"><span class="sidebar-icon">📊</span> Reports</a>
        <a class="sidebar-link active" href="grades.php"><span class="sidebar-icon">📝</span> Grades</a>
        <a class="sidebar-link" href="login.php"><span class="sidebar-icon">🚪</span> Logout</a>
      </nav>
    </aside>
    <!-- Main Content -->
    <main class="main-content">
      <header class="dashboard-header">
        <h1>Grades Management</h1>
        <div class="user-profile">
          <span class="user-avatar">AU</span>
          <span class="user-name">Admin User</span>
        </div>
      </header>
      <section class="grades-section">
        <div class="grades-header">
          <h2>Grades Management</h2>
          <button class="add-grade-btn" id="addGradeBtn">+ Add New Grade</button>
        </div>
        <table class="grades-table" id="gradesTable">
          <thead>
            <tr>
              <th>STUDENT</th>
              <th>COURSE</th>
              <th>INSTRUCTOR</th>
              <th>GRADE</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <!-- Grades will be rendered here -->
          </tbody>
        </table>
      </section>
    </main>
  </div>
  <!-- Modal for Add/Edit Grade -->
  <div class="modal" id="gradeModal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.18);align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#fff;border-radius:12px;padding:28px 32px 22px 32px;min-width:320px;box-shadow:0 4px 24px rgba(79,91,213,0.13);display:flex;flex-direction:column;gap:16px;position:relative;">
      <button class="modal-close" id="closeModalBtn" style="position:absolute;right:18px;top:14px;font-size:1.2em;color:#888;background:none;border:none;cursor:pointer;">&times;</button>
      <h3 id="modalTitle">Add Grade</h3>
      <form id="gradeForm">
        <label for="studentName">Student Name</label>
        <input type="text" id="studentName" name="studentName" required>
        <label for="courseName">Course</label>
        <input type="text" id="courseName" name="courseName" required>
        <label for="instructor">Instructor</label>
        <input type="text" id="instructor" name="instructor" required>
        <label for="grade">Grade</label>
        <input type="text" id="grade" name="grade" required>
        <div class="modal-actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;">
          <button type="button" class="modal-btn cancel" id="cancelBtn" style="padding:7px 18px;border-radius:7px;border:none;font-weight:600;font-size:1em;cursor:pointer;background:#f0f0f0;color:#222;">Cancel</button>
          <button type="submit" class="modal-btn save" style="padding:7px 18px;border-radius:7px;border:none;font-weight:600;font-size:1em;cursor:pointer;background:#4f5bd5;color:#fff;">Save</button>
        </div>
      </form>
    </div>
  </div>
<script>
    // Demo data
    let grades = [
        { student: "John Smith", course: "Advanced Mathematics", instructor: "Dr. James Wilson", grade: "A" },
        { student: "Emily Davis", course: "Computer Science Fundamentals", instructor: "Prof. Sarah Thompson", grade: "B+" },
        { student: "Robert Johnson", course: "Organic Chemistry", instructor: "Dr. Michael Brown", grade: "C" },
        { student: "Lisa Anderson", course: "Modern World History", instructor: "Prof. Amanda Johnson", grade: "A-" }
    ];
    let editingIndex = null;

    const gradesTable = document.getElementById('gradesTable').getElementsByTagName('tbody')[0];
    const gradeModal = document.getElementById('gradeModal');
    const addGradeBtn = document.getElementById('addGradeBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const gradeForm = document.getElementById('gradeForm');
    const modalTitle = document.getElementById('modalTitle');
    const studentNameInput = document.getElementById('studentName');
    const courseNameInput = document.getElementById('courseName');
    const instructorInput = document.getElementById('instructor');
    const gradeInput = document.getElementById('grade');

    function renderGrades() {
        gradesTable.innerHTML = '';
        grades.forEach((gradeObj, idx) => {
            const isFail = gradeObj.grade.toUpperCase() === 'F' || gradeObj.grade.toUpperCase() === 'E';
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${gradeObj.student}</td>
                <td>${gradeObj.course}</td>
                <td>${gradeObj.instructor}</td>
                <td>
                    <span class="grade-badge${isFail ? ' fail' : ''}">
                        ${gradeObj.grade}
                    </span>
                </td>
                <td>
                    <button class="action-btn edit" title="Edit" data-idx="${idx}">&#128393;</button>
                    <button class="action-btn delete" title="Delete" data-idx="${idx}">&#128465;</button>
                </td>
            `;
            gradesTable.appendChild(tr);
        });
        document.querySelectorAll('.action-btn.edit').forEach(btn => {
            btn.onclick = () => openEditModal(btn.getAttribute('data-idx'));
        });
        document.querySelectorAll('.action-btn.delete').forEach(btn => {
            btn.onclick = () => deleteGrade(btn.getAttribute('data-idx'));
        });
    }

    function openModal() {
        gradeModal.style.display = 'flex';
    }
    function closeModal() {
        gradeModal.style.display = 'none';
        gradeForm.reset();
        editingIndex = null;
    }

    addGradeBtn.onclick = () => {
        modalTitle.textContent = "Add Grade";
        gradeForm.reset();
        editingIndex = null;
        openModal();
    };

    function openEditModal(idx) {
        const gradeObj = grades[idx];
        modalTitle.textContent = "Edit Grade";
        studentNameInput.value = gradeObj.student;
        courseNameInput.value = gradeObj.course;
        instructorInput.value = gradeObj.instructor;
        gradeInput.value = gradeObj.grade;
        editingIndex = idx;
        openModal();
    }

    function deleteGrade(idx) {
        if (confirm("Are you sure you want to delete this grade?")) {
            grades.splice(idx, 1);
            renderGrades();
        }
    }

    gradeForm.onsubmit = function(e) {
        e.preventDefault();
        const newGrade = {
            student: studentNameInput.value,
            course: courseNameInput.value,
            instructor: instructorInput.value,
            grade: gradeInput.value
        };
        if (editingIndex !== null) {
            grades[editingIndex] = newGrade;
        } else {
            grades.push(newGrade);
        }
        closeModal();
        renderGrades();
    };

    closeModalBtn.onclick = closeModal;
    cancelBtn.onclick = closeModal;
    window.onclick = function(event) {
        if (event.target === gradeModal) {
            closeModal();
        }
    };

    // Initial render
    renderGrades();
</script>
</body>
</html>