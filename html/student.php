<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Management</title>
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
    .student-section {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(79,91,213,0.07);
      padding: 28px 24px 24px 24px;
    }
    .student-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }
    .student-header h2 {
      font-size: 1.2em;
      font-weight: 700;
      margin: 0;
      color: #222;
    }
    .add-student-btn {
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
    .add-student-btn:hover {
      background: #3b47b2;
    }
    .student-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 1em;
    }
    .student-table th, .student-table td {
      padding: 14px 8px;
      text-align: left;
    }
    .student-table th {
      color: #888;
      font-weight: 600;
      font-size: 0.98em;
      border-bottom: 2px solid #f0f0f0;
    }
    .student-table tr:not(:last-child) td {
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
        <a class="sidebar-link active" href="student.php"><span class="sidebar-icon">🎓</span> Students</a>
        <a class="sidebar-link" href="courses.php"><span class="sidebar-icon">📚</span> Courses</a>
        <a class="sidebar-link" href="enrollment.php"><span class="sidebar-icon">📝</span> Enrollments</a>
        <a class="sidebar-link" href="reports.php"><span class="sidebar-icon">📊</span> Reports</a>
        <a class="sidebar-link" href="login.php"><span class="sidebar-icon">🚪</span> Logout</a>
      </nav>
    </aside>
    <!-- Main Content -->
    <main class="main-content">
      <header class="dashboard-header">
        <h1>Student Management</h1>
        <div class="user-profile">
          <span class="user-avatar">AU</span>
          <span class="user-name">Admin User</span>
        </div>
      </header>
      <section class="student-section">
        <div class="student-header">
          <h2>Student Management</h2>
          <button class="add-student-btn" id="addStudentBtn">+ Add New Student</button>
        </div>
        <table class="student-table" id="studentTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>STUDENT NAME</th>
              <th>EMAIL</th>
              <th>PHONE</th>
              <th>DEPARTMENT</th>
              <th>STATUS</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <!-- Students will be rendered here -->
          </tbody>
        </table>
      </section>
    </main>
  </div>
  <!-- Modal for Add/Edit Student -->
  <div class="modal" id="studentModal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.18);align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#fff;border-radius:12px;padding:28px 32px 22px 32px;min-width:320px;box-shadow:0 4px 24px rgba(79,91,213,0.13);display:flex;flex-direction:column;gap:16px;position:relative;">
      <button class="modal-close" id="closeModalBtn" style="position:absolute;right:18px;top:14px;font-size:1.2em;color:#888;background:none;border:none;cursor:pointer;">&times;</button>
      <h3 id="modalTitle">Add Student</h3>
      <form id="studentForm">
        <input type="hidden" id="studentId" name="studentId">
        <label for="studentName">Student Name</label>
        <input type="text" id="studentName" name="studentName" required pattern="^[A-Za-z\s\-']+$" title="Only letters, spaces, hyphens, and apostrophes allowed">
        <label for="studentEmail">Email</label>
        <input type="email" id="studentEmail" name="studentEmail" required>
        <label for="studentPhone">Phone</label>
        <input type="text" id="studentPhone" name="studentPhone" required>
        <label for="studentDept">Department</label>
        <select id="studentDept" name="studentDept" required>
          <option value="">Loading...</option>
        </select>
        <label for="studentStatus">Status</label>
        <select id="studentStatus" name="studentStatus">
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
        <div class="modal-actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;">
          <button type="button" class="modal-btn cancel" id="cancelBtn" style="padding:7px 18px;border-radius:7px;border:none;font-weight:600;font-size:1em;cursor:pointer;background:#f0f0f0;color:#222;">Cancel</button>
          <button type="submit" class="modal-btn save" style="padding:7px 18px;border-radius:7px;border:none;font-weight:600;font-size:1em;cursor:pointer;background:#4f5bd5;color:#fff;">Save</button>
        </div>
      </form>
    </div>
  </div>
  <script>
  // DOM Elements
  const studentTable = document.getElementById('studentTable').getElementsByTagName('tbody')[0];
  const studentModal = document.getElementById('studentModal');
  const addStudentBtn = document.getElementById('addStudentBtn');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const studentForm = document.getElementById('studentForm');
  const modalTitle = document.getElementById('modalTitle');
  const studentIdInput = document.getElementById('studentId');
  const studentNameInput = document.getElementById('studentName');
  const studentEmailInput = document.getElementById('studentEmail');
  const studentPhoneInput = document.getElementById('studentPhone');
  const studentDeptInput = document.getElementById('studentDept');
  const studentStatusInput = document.getElementById('studentStatus');

  let students = [];
  let departments = [];

  // Fetch departments from API and populate dropdown
  async function fetchDepartments() {
    try {
      const response = await fetch('api/departments.php');
      if (!response.ok) throw new Error('Failed to fetch departments');
      departments = await response.json();
      studentDeptInput.innerHTML = '<option value="">Select Department</option>';
      departments.forEach(d => {
        const opt = document.createElement('option');
        opt.value = d.department_id;
        opt.textContent = d.department_name;
        studentDeptInput.appendChild(opt);
      });
    } catch (error) {
      studentDeptInput.innerHTML = '<option value="">Error loading departments</option>';
      console.error(error);
    }
  }

  // Helper to get department name by id
  function departmentName(id) {
    const dept = departments.find(d => String(d.department_id) === String(id));
    return dept ? dept.department_name : '';
  }

  // Fetch students from database
  async function fetchStudents() {
    try {
      const response = await fetch('api/students.php');
      if (!response.ok) throw new Error('Network response was not ok');
      students = await response.json();
      renderStudents();
    } catch (error) {
      console.error('Error fetching students:', error);
      alert('Failed to load student data. Please try again later.');
    }
  }

  // Render students in the table
  function renderStudents() {
    studentTable.innerHTML = '';
    if (!students.length) {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td colspan="7" style="text-align: center; padding: 20px;">No students found</td>`;
      studentTable.appendChild(tr);
      return;
    }
    students.forEach(student => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${student.student_id}</td>
        <td>${student.name}</td>
        <td>${student.email}</td>
        <td>${student.phone || 'N/A'}</td>
        <td>${departmentName(student.department_id)}</td>
        <td>
          <span class="status-badge ${student.status === 'Active' ? 'active' : 'inactive'}">
            ${student.status}
          </span>
        </td>
        <td>
          <button class="action-btn edit" title="Edit" data-id="${student.student_id}">&#128393;</button>
          <button class="action-btn delete" title="Delete" data-id="${student.student_id}">&#128465;</button>
        </td>
      `;
      studentTable.appendChild(tr);
    });

    // Add event listeners to action buttons
    document.querySelectorAll('.action-btn.edit').forEach(btn => {
      btn.addEventListener('click', () => {
        const studentId = btn.getAttribute('data-id');
        openEditModal(studentId);
      });
    });
    document.querySelectorAll('.action-btn.delete').forEach(btn => {
      btn.addEventListener('click', () => {
        const studentId = btn.getAttribute('data-id');
        deleteStudent(studentId);
      });
    });
  }

  // Modal functions
  function openModal() {
    studentModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  function closeModal() {
    studentModal.style.display = 'none';
    document.body.style.overflow = 'auto';
    studentForm.reset();
    studentIdInput.value = '';
  }

  // Open modal for adding new student
  addStudentBtn.addEventListener('click', () => {
    modalTitle.textContent = "Add New Student";
    studentForm.reset();
    studentIdInput.value = '';
    openModal();
  });

  // Open modal for editing student
  async function openEditModal(studentId) {
    try {
      const response = await fetch(`api/students.php?id=${studentId}`);
      if (!response.ok) throw new Error('Failed to fetch student data');
      const student = await response.json();
      modalTitle.textContent = "Edit Student";
      studentIdInput.value = student.student_id;
      studentNameInput.value = student.name;
      studentEmailInput.value = student.email;
      studentPhoneInput.value = student.phone || '';
      studentDeptInput.value = student.department_id || '';
      studentStatusInput.value = student.status || 'Active';
      openModal();
    } catch (error) {
      console.error('Error fetching student:', error);
      alert('Failed to load student data. Please try again.');
    }
  }

  // Delete student
  async function deleteStudent(studentId) {
    if (!confirm("Are you sure you want to delete this student?")) return;
    try {
      const response = await fetch(`api/students.php?id=${studentId}`, { method: 'DELETE' });
      if (!response.ok) throw new Error('Failed to delete student');
      await fetchStudents();
      alert('Student deleted successfully');
    } catch (error) {
      console.error('Error deleting student:', error);
      alert('Failed to delete student. Please try again.');
    }
  }

  // Handle form submission
  studentForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = {
      student_id: studentIdInput.value,
      name: studentNameInput.value,
      email: studentEmailInput.value,
      phone: studentPhoneInput.value,
      department_id: studentDeptInput.value,
      status: studentStatusInput.value
    };
    try {
      const url = 'api/students.php';
      const method = formData.student_id ? 'PUT' : 'POST';
      const response = await fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      });
      if (!response.ok) throw new Error('Failed to save student');
      closeModal();
      await fetchStudents();
      alert('Student saved successfully');
    } catch (error) {
      console.error('Error saving student:', error);
      alert('Failed to save student. Please try again.');
    }
  });

  // Event listeners for modal
  closeModalBtn.addEventListener('click', closeModal);
  cancelBtn.addEventListener('click', closeModal);
  window.addEventListener('click', (event) => {
    if (event.target === studentModal) closeModal();
  });

  // Initialize
  document.addEventListener('DOMContentLoaded', async () => {
    await fetchDepartments();
    await fetchStudents();
  });

  studentNameInput.addEventListener('input', function() {
    this.value = this.value.replace(/[^A-Za-z\s\-']/g, '');
  });
  </script>
</body>
</html>