<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Reports</title>
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
    .reports-section {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(79,91,213,0.07);
      padding: 28px 24px 24px 24px;
    }
    .reports-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }
    .reports-header h2 {
      font-size: 1.2em;
      font-weight: 700;
      margin: 0;
      color: #222;
    }
    .export-btns {
      display: flex;
      gap: 12px;
    }
    .export-btn {
      background: #f5f6fa;
      color: #222;
      border: none;
      border-radius: 8px;
      padding: 10px 18px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .export-btn:hover {
      background: #e3e8fd;
      color: #4f5bd5;
    }
    .filters-bar {
      display: flex;
      gap: 32px;
      margin-bottom: 18px;
      align-items: flex-end;
      flex-wrap: wrap;
    }
    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .filter-group label {
      font-size: 0.98em;
      font-weight: 600;
      color: #888;
    }
    .filter-group select, .filter-group input[type="text"] {
      padding: 10px 12px;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      font-size: 1em;
      min-width: 180px;
      background: #f7f8fa;
    }
    .filter-group input[type="text"] {
      min-width: 220px;
    }
    .reports-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 1em;
    }
    .reports-table th, .reports-table td {
      padding: 14px 8px;
      text-align: left;
    }
    .reports-table th {
      color: #888;
      font-weight: 600;
      font-size: 0.98em;
      border-bottom: 2px solid #f0f0f0;
    }
    .reports-table tr:not(:last-child) td {
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
    .status-badge.failed {
      color: #e85a71;
      background: #fde3e3;
    }
    @media (max-width: 900px) {
      .main-content { padding: 24px 8px; }
      .sidebar { width: 60px; }
      .sidebar-title { display: none; }
      .sidebar-header { padding: 24px 8px 16px 8px; }
      .sidebar-link { padding: 10px 8px; font-size: 0.95em; }
      .filters-bar { flex-direction: column; gap: 12px; }
    }
    @media (max-width: 600px) {
      .container { flex-direction: column; }
      .sidebar { flex-direction: row; width: 100vw; height: 60px; }
      .sidebar-nav { flex-direction: row; gap: 2px; margin-top: 0; }
      .main-content { padding: 12px 2vw; }
      .filters-bar { flex-direction: column; gap: 12px; }
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
        <a class="sidebar-link active" href="reports.php"><span class="sidebar-icon">📊</span> Reports</a>
        <a class="sidebar-link" href="login.php"><span class="sidebar-icon">🚪</span> Logout</a>
      </nav>
    </aside>
    <!-- Main Content -->
    <main class="main-content">
      <header class="dashboard-header">
        <h1>System Reports</h1>
        <div class="user-profile">
          <span class="user-avatar">AU</span>
          <span class="user-name">Admin User</span>
        </div>
      </header>
      <section class="reports-section">
        <div class="reports-header">
          <h2>System Reports</h2>
          <div class="export-btns">
            <button class="export-btn" id="exportCsvBtn">
              <span>📄</span> Export CSV
            </button>
            <button class="export-btn" id="exportPdfBtn">
              <span>📄</span> Export PDF
            </button>
          </div>
        </div>
        <div class="filters-bar">
          <div class="filter-group">
            <label for="reportType">Report Type</label>
            <select id="reportType">
              <option value="student">Student Report</option>
              <option value="course">Course Report</option>
            </select>
          </div>
          <div class="filter-group">
            <label for="timePeriod">Time Period</label>
            <select id="timePeriod">
              <option value="current">Current Semester</option>
              <option value="previous">Previous Semester</option>
              <option value="year">Current Year</option>
            </select>
          </div>
          <div class="filter-group">
            <label for="searchInput">Search</label>
            <input type="text" id="searchInput" placeholder="Search by name, course, or ID...">
          </div>
        </div>
        <table class="reports-table" id="reportsTable">
          <thead>
            <tr>
              <th>STUDENT ID</th>
              <th>NAME</th>
              <th>COURSE</th>
              <th>GRADE</th>
              <th>STATUS</th>
            </tr>
          </thead>
          <tbody>
            <!-- Report rows will be rendered here -->
          </tbody>
        </table>
      </section>
    </main>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script>
    // Demo data
    let reports = [
      { id: "10001", name: "Charles Sego", course: "Advanced Mathes", grade: "A", status: "Passed" },
      { id: "10002", name: "Emily Davis", course: "Computer Science Fundamentals", grade: "B+", status: "Passed" },
      { id: "10004", name: "Lisa Anderson", course: "Organic Chemistry", grade: "A-", status: "Passed" },
      { id: "10003", name: "Robert Johnson", course: "Modern World History", grade: "C", status: "Failed" },
      { id: "10002", name: "Emily Davis", course: "Organic Chemistry", grade: "B+", status: "Passed" },
      { id: "10003", name: "Robert Johnson", course: "Modern World History", grade: "C", status: "Failed" },
      { id: "10005", name: "Desmond Mawunyo", course: "organic Chemistry", grade: "B", status: "Passed" },
      { id: "10006", name: "DZifa Ama", course: "Organic chemistry", grade: "B-", status: "Passed" },
      { id: "10005", name: "Desmond Mawunyo", course: "Advance Maths", grade: "B", status: "Passed" },
      { id: "10007", name: "David Sayram", course: "Advance Maths", grade: "A+", status: "Passed" },
      { id: "10001", name: "charles Sego", course: "computer science Fundamentals", grade: "B+", status: "Passed" }
    ];

    // For demo, course report is just a different view of the same data
    let courseReports = [
      { id: "MATH101", name: "Advanced Mathematics", instructor: "Dr. James Wilson", enrolled: 142, avgGrade: "A-" },
      { id: "CS201", name: "Computer Science Fundamentals", instructor: "Prof. Sarah Thompson", enrolled: 89, avgGrade: "B+" },
      { id: "CHEM301", name: "Organic Chemistry", instructor: "Dr. Michael Brown", enrolled: 67, avgGrade: "B" },
      { id: "HIST202", name: "Modern World History", instructor: "Prof. Amanda Johnson", enrolled: 104, avgGrade: "B-" }
    ];

    const reportsTable = document.getElementById('reportsTable').getElementsByTagName('tbody')[0];
    const reportType = document.getElementById('reportType');
    const timePeriod = document.getElementById('timePeriod');
    const searchInput = document.getElementById('searchInput');
    const exportCsvBtn = document.getElementById('exportCsvBtn');
    const exportPdfBtn = document.getElementById('exportPdfBtn');

    function renderReports() {
      reportsTable.innerHTML = '';
      let filtered = [];
      if (reportType.value === 'student') {
        filtered = reports.filter(r => {
          const q = searchInput.value.toLowerCase();
          return (
            r.id.toLowerCase().includes(q) ||
            r.name.toLowerCase().includes(q) ||
            r.course.toLowerCase().includes(q)
          );
        });
        filtered.forEach(r => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${r.id}</td>
            <td>${r.name}</td>
            <td>${r.course}</td>
            <td>${r.grade}</td>
            <td>
              <span class="status-badge${r.status === 'Failed' ? ' failed' : ''}">
                ${r.status}
              </span>
            </td>
          `;
          reportsTable.appendChild(tr);
        });
      } else {
        // Course Report
        filtered = courseReports.filter(r => {
          const q = searchInput.value.toLowerCase();
          return (
            r.id.toLowerCase().includes(q) ||
            r.name.toLowerCase().includes(q) ||
            r.instructor.toLowerCase().includes(q)
          );
        });
        // Change table header
        reportsTable.parentElement.querySelector('thead').innerHTML = `
          <tr>
            <th>COURSE CODE</th>
            <th>COURSE NAME</th>
            <th>INSTRUCTOR</th>
            <th>ENROLLED</th>
            <th>AVG. GRADE</th>
          </tr>
        `;
        filtered.forEach(r => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${r.id}</td>
            <td>${r.name}</td>
            <td>${r.instructor}</td>
            <td>${r.enrolled}</td>
            <td>${r.avgGrade}</td>
          `;
          reportsTable.appendChild(tr);
        });
        return;
      }
      // Restore student report header if needed
      reportsTable.parentElement.querySelector('thead').innerHTML = `
        <tr>
          <th>STUDENT ID</th>
          <th>NAME</th>
          <th>COURSE</th>
          <th>GRADE</th>
          <th>STATUS</th>
        </tr>
      `;
    }

    reportType.onchange = renderReports;
    timePeriod.onchange = renderReports;
    searchInput.oninput = renderReports;

    // Export CSV
    exportCsvBtn.onclick = function() {
      let csv = '';
      if (reportType.value === 'student') {
        csv += 'STUDENT ID,NAME,COURSE,GRADE,STATUS\n';
        reportsTable.querySelectorAll('tr').forEach(tr => {
          const tds = tr.querySelectorAll('td');
          if (tds.length)
            csv += Array.from(tds).map(td => `"${td.textContent.trim()}"`).join(',') + '\n';
        });
      } else {
        csv += 'COURSE CODE,COURSE NAME,INSTRUCTOR,ENROLLED,AVG. GRADE\n';
        reportsTable.querySelectorAll('tr').forEach(tr => {
          const tds = tr.querySelectorAll('td');
          if (tds.length)
            csv += Array.from(tds).map(td => `"${td.textContent.trim()}"`).join(',') + '\n';
        });
      }
      const blob = new Blob([csv], { type: 'text/csv' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = (reportType.value === 'student' ? 'student_report' : 'course_report') + '.csv';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    };

    // Export PDF
    exportPdfBtn.onclick = function() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();
      let y = 15;
      if (reportType.value === 'student') {
        doc.text('Student Report', 10, y);
        y += 10;
        doc.text('STUDENT ID', 10, y);
        doc.text('NAME', 40, y);
        doc.text('COURSE', 90, y);
        doc.text('GRADE', 140, y);
        doc.text('STATUS', 160, y);
        y += 7;
        reportsTable.querySelectorAll('tr').forEach(tr => {
          const tds = tr.querySelectorAll('td');
          if (tds.length) {
            doc.text(tds[0].textContent.trim(), 10, y);
            doc.text(tds[1].textContent.trim(), 40, y);
            doc.text(tds[2].textContent.trim(), 90, y);
            doc.text(tds[3].textContent.trim(), 140, y);
            doc.text(tds[4].textContent.trim(), 160, y);
            y += 7;
          }
        });
      } else {
        doc.text('Course Report', 10, y);
        y += 10;
        doc.text('COURSE CODE', 10, y);
        doc.text('COURSE NAME', 40, y);
        doc.text('INSTRUCTOR', 90, y);
        doc.text('ENROLLED', 140, y);
        doc.text('AVG. GRADE', 170, y);
        y += 7;
        reportsTable.querySelectorAll('tr').forEach(tr => {
          const tds = tr.querySelectorAll('td');
          if (tds.length) {
            doc.text(tds[0].textContent.trim(), 10, y);
            doc.text(tds[1].textContent.trim(), 40, y);
            doc.text(tds[2].textContent.trim(), 90, y);
            doc.text(tds[3].textContent.trim(), 140, y);
            doc.text(tds[4].textContent.trim(), 170, y);
            y += 7;
          }
        });
      }
      doc.save((reportType.value === 'student' ? 'student_report' : 'course_report') + '.pdf');
    };

    // Initial render
    renderReports();
  </script>
</body>
</html>