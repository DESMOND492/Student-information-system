<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enrollment Management</title>
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
      background: rgba(40, 57, 242, 0.75);
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
      width: 38px;
      height: 38px;
      filter: drop-shadow(0 2px 8px rgba(44,62,80,0.10));
    }
    .sidebar-title {
      font-size: 1.3rem;
      font-weight: 700;
      letter-spacing: 1px;
      color: #fff;
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
    .enrollment-section {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(79,91,213,0.07);
      padding: 28px 24px 24px 24px;
    }
    .enrollment-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }
    .enrollment-header h2 {
      font-size: 1.2em;
      font-weight: 700;
      margin: 0;
      color: #222;
    }
    .add-enrollment-btn {
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
    .add-enrollment-btn:hover {
      background: #3b47b2;
    }
    .enrollment-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 1em;
    }
    .enrollment-table th, .enrollment-table td {
      padding: 14px 8px;
      text-align: left;
    }
    .enrollment-table th {
      color: #888;
      font-weight: 600;
      font-size: 0.98em;
      border-bottom: 2px solid #f0f0f0;
    }
    .enrollment-table tr:not(:last-child) td {
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
        <a class="sidebar-link active" href="enrollment.php"><span class="sidebar-icon">📝</span> Enrollments</a>
        <a class="sidebar-link" href="grades.php"><span class="sidebar-icon">✅</span> grades</a>
        <a class="sidebar-link" href="reports.php"><span class="sidebar-icon">📊</span> Reports</a>
        <a class="sidebar-link" href="login.php"><span class="sidebar-icon">🚪</span> Logout</a>
      </nav>
    </aside>
    <!-- Main Content -->
    <main class="main-content">
      <header class="dashboard-header">
        <h1>Enrollment Management</h1>
        <div class="user-profile">
          <span class="user-avatar">AU</span>
          <span class="user-name">Admin User</span>
        </div>
      </header>
      <section class="enrollment-section">
        <div class="enrollment-header">
          <h2>Enrollment Management</h2>
          <button class="add-enrollment-btn" id="addEnrollmentBtn">+ Add Enrollment</button>
        </div>
        <table class="enrollment-table" id="enrollmentTable">
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
            <!-- Enrollments will be rendered here -->
          </tbody>
        </table>
      </section>
    </main>
  </div>
  <!-- Modal for Add/Edit Enrollment -->
  <div class="modal" id="enrollmentModal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.18);align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#fff;border-radius:12px;padding:28px 32px 22px 32px;min-width:320px;box-shadow:0 4px 24px rgba(79,91,213,0.13);display:flex;flex-direction:column;gap:16px;position:relative;">
      <button class="modal-close" id="closeModalBtn" style="position:absolute;right:18px;top:14px;font-size:1.2em;color:#888;background:none;border:none;cursor:pointer;">&times;</button>
      <h3 id="modalTitle">Add Enrollment</h3>
      <form id="enrollmentForm">
        <label for="studentName">Student Name</label>
        <select id="studentName" name="studentName" required></select>
        <label for="courseName">Course</label>
        <select id="courseName" name="courseName" required></select>
        <label for="enrollDate">Date</label>
        <input type="date" id="enrollDate" name="enrollDate" required>
        <label for="status">Status</label>
        <select id="status" name="status">
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
  const enrollmentTable = document.getElementById('enrollmentTable').getElementsByTagName('tbody')[0];
  const enrollmentModal = document.getElementById('enrollmentModal');
  const addEnrollmentBtn = document.getElementById('addEnrollmentBtn');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const enrollmentForm = document.getElementById('enrollmentForm');
  const modalTitle = document.getElementById('modalTitle');
  const studentNameInput = document.getElementById('studentName');
  const courseNameInput = document.getElementById('courseName');
  const enrollDateInput = document.getElementById('enrollDate');
  const statusInput = document.getElementById('status');

  let enrollments = [];
  let students = [];
  let courses = [];
  let editingId = null;

  // Fetch data from database
  async function fetchData() {
    try {
      // Fetch enrollments with student and course details
      const enrollResponse = await fetch('api/enrollments.php?details=true');
      if (!enrollResponse.ok) throw new Error('Failed to load enrollments');
      enrollments = await enrollResponse.json();

      // Fetch students for dropdown
      const studentResponse = await fetch('api/students.php');
      if (!studentResponse.ok) throw new Error('Failed to load students');
      students = await studentResponse.json();

      // Fetch courses for dropdown
      const courseResponse = await fetch('api/courses.php');
      if (!courseResponse.ok) throw new Error('Failed to load courses');
      courses = await courseResponse.json();

      renderEnrollments();
    } catch (error) {
      console.error('Error fetching data:', error);
      alert('Failed to load data. Please try again later.');
    }
  }

  // Render enrollments in the table
  function renderEnrollments() {
    enrollmentTable.innerHTML = '';
    
    if (enrollments.length === 0) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td colspan="5" style="text-align: center; padding: 20px;">
          No enrollments found
        </td>
      `;
      enrollmentTable.appendChild(tr);
      return;
    }
    
    enrollments.forEach(enroll => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${enroll.student_name}</td>
        <td>${enroll.course_name}</td>
        <td>${formatDate(enroll.enrollment_date)}</td>
        <td>
          <span class="status-badge ${enroll.status === 'active' ? 'active' : 'inactive'}">
            ${enroll.status.charAt(0).toUpperCase() + enroll.status.slice(1)}
          </span>
        </td>
        <td>
          <button class="action-btn edit" title="Edit" data-id="${enroll.enrollment_id}">&#128393;</button>
          <button class="action-btn delete" title="Delete" data-id="${enroll.enrollment_id}">&#128465;</button>
        </td>
      `;
      enrollmentTable.appendChild(tr);
    });
    
    // Add event listeners to action buttons
    document.querySelectorAll('.action-btn.edit').forEach(btn => {
      btn.addEventListener('click', () => {
        const enrollmentId = btn.getAttribute('data-id');
        openEditModal(enrollmentId);
      });
    });
    
    document.querySelectorAll('.action-btn.delete').forEach(btn => {
      btn.addEventListener('click', () => {
        const enrollmentId = btn.getAttribute('data-id');
        deleteEnrollment(enrollmentId);
      });
    });
  }

  function formatDate(dateStr) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateStr).toLocaleDateString('en-US', options);
  }

  // Modal functions
  function openModal() {
    enrollmentModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    enrollmentModal.style.display = 'none';
    document.body.style.overflow = 'auto';
    enrollmentForm.reset();
    editingId = null;
  }

  // Open modal for adding new enrollment
  addEnrollmentBtn.addEventListener('click', () => {
    modalTitle.textContent = "Add New Enrollment";
    enrollmentForm.reset();
    populateDropdowns();
    enrollDateInput.valueAsDate = new Date(); // Set default to today
    openModal();
  });

  // Populate student and course dropdowns
  function populateDropdowns() {
    // Clear existing dropdowns
    studentNameInput.innerHTML = '';
    courseNameInput.innerHTML = '';
    
    // Add default option
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = '-- Select --';
    defaultOption.disabled = true;
    defaultOption.selected = true;
    studentNameInput.appendChild(defaultOption.cloneNode(true));
    courseNameInput.appendChild(defaultOption.cloneNode(true));
    
    // Add students
    students.forEach(student => {
      const option = document.createElement('option');
      option.value = student.student_id;
      option.textContent = `${student.name} (${student.student_id})`;
      studentNameInput.appendChild(option);
    });
    
    // Add courses
    courses.forEach(course => {
      const option = document.createElement('option');
      option.value = course.course_id;
      option.textContent = `${course.course_name} (${course.course_code})`;
      courseNameInput.appendChild(option);
    });
  }

  // Open modal for editing enrollment
  async function openEditModal(enrollmentId) {
    try {
      const response = await fetch(`api/enrollments.php?id=${enrollmentId}`);
      if (!response.ok) throw new Error('Failed to fetch enrollment');
      const enrollment = await response.json();
      
      modalTitle.textContent = "Edit Enrollment";
      populateDropdowns();
      
      // Set form values
      studentNameInput.value = enrollment.student_id;
      courseNameInput.value = enrollment.course_id;
      enrollDateInput.value = enrollment.enrollment_date.split(' ')[0]; // Remove time if present
      statusInput.value = enrollment.status;
      editingId = enrollmentId;
      
      openModal();
    } catch (error) {
      console.error('Error fetching enrollment:', error);
      alert('Failed to load enrollment data. Please try again.');
    }
  }

  // Delete enrollment
  async function deleteEnrollment(enrollmentId) {
    if (!confirm("Are you sure you want to delete this enrollment?")) return;
    
    try {
      const response = await fetch(`api/enrollments.php?id=${enrollmentId}`, {
        method: 'DELETE'
      });
      
      if (!response.ok) throw new Error('Failed to delete enrollment');
      

      await fetchData();
      alert('Enrollment deleted successfully');
    } catch (error) {
      console.error('Error deleting enrollment:', error);
      alert('Failed to delete enrollment. Please try again.');
    }
  }

  enrollmentForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
      student_id: studentNameInput.value,
      course_id: courseNameInput.value,
      enrollment_date: enrollDateInput.value,
      status: statusInput.value
    };
    
    if (editingId) formData.enrollment_id = editingId;
    
    try {
      const url = 'api/enrollments.php';
      const method = editingId ? 'PUT' : 'POST';
      
      const response = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      });
      
      if (!response.ok) throw new Error('Failed to save enrollment');
      
      const result = await response.json();
      closeModal();
      await fetchData();
      alert(`Enrollment ${editingId ? 'updated' : 'created'} successfully`);
    } catch (error) {
      console.error('Error saving enrollment:', error);
      alert('Failed to save enrollment. Please try again.');
    }
  });

  // Event listeners for modal
  closeModalBtn.addEventListener('click', closeModal);
  cancelBtn.addEventListener('click', closeModal);
  enrollmentModal.addEventListener('click', (e) => {
    if (e.target === enrollmentModal) closeModal();
  });

  // Initialize
  document.addEventListener('DOMContentLoaded', fetchData);
</script>
</body>
</html>