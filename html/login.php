<?php
session_start();

include_once "../db.php";

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $email = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            
            switch ($user['role']) {
                case 'admin':
                    header('Location: dashboard.php');
                    break;
                case 'teacher':
                    header('Location: dashboard.php');
                    break;
                case 'student':
                    header('Location: dashboard.php');
                    break;
                default:
                    header('Location: dashboard.php');
            }
            exit();
        } else {
            $login_error = "Invalid email or password";
        }
    } else {
        $login_error = "Invalid email or password";
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(90deg, #2e2eff 0%, #e0c3fc 100%);
      min-height: 100vh;
      font-family: 'Poppins', Arial, sans-serif;
    }
    .login-main-container {
      background: #fff;
      border-radius: 20px;
      margin: 32px auto;
      max-width: 1100px;
      min-height: 600px;
      display: flex;
      box-shadow: 0 8px 32px rgba(44, 62, 80, 0.15);
      overflow: hidden;
    }
    .login-left {
      flex: 1.1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 48px 40px 48px 48px;
    }
    .login-title {
      font-size: 2.2rem;
      font-weight: 700;
      margin-bottom: 8px;
      color: #222;
    }
    .login-desc {
      color: #666;
      margin-bottom: 32px;
      font-size: 1.05rem;
    }
    .login-form label {
      display: block;
      font-size: 1rem;
      margin-bottom: 6px;
      margin-top: 18px;
      color: #222;
    }
    .login-form input[type="text"],
    .login-form input[type="password"] {
      width: 100%;
      padding: 12px 18px;
      border-radius: 24px;
      border: 1.5px solid #bbb;
      font-size: 1rem;
      margin-bottom: 8px;
      outline: none;
      transition: border 0.2s;
    }
    .login-form input[type="email"]:focus,
    .login-form input[type="password"]:focus {
      border: 1.5px solid #2e2eff;
    }
    .login-remember {
      display: flex;
      align-items: center;
      margin: 12px 0 24px 0;
      font-size: 1rem;
    }
    .login-remember input[type="checkbox"] {
      accent-color: #2e2eff;
      margin-right: 8px;
      width: 18px;
      height: 18px;
    }
    .login-btn {
      width: 100%;
      padding: 14px 0;
      border-radius: 24px;
      border: none;
      background: linear-gradient(90deg, #2e2eff 0%, #e0c3fc 100%);
      color: #fff;
      font-size: 1.1rem;
      font-weight: 600;
      letter-spacing: 1px;
      margin-bottom: 28px;
      margin-top: 8px;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
    }
    .login-btn:hover {
      background: linear-gradient(90deg, #1a1aff 0%, #b993f4 100%);
      transform: scale(1.02);
    }
    .login-social-row {
      display: flex;
      gap: 24px;
      justify-content: center;
      margin-top: 12px;
    }
    .login-social-btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      border: 1.5px solid #bbb;
      border-radius: 24px;
      padding: 10px 0;
      background: #fff;
      color: #222;
      font-size: 1rem;
      cursor: pointer;
      transition: border 0.2s, background 0.2s;
    }
    .login-social-btn:hover {
      border: 1.5px solid #2e2eff;
      background: #f5f7ff;
    }
    .login-social-btn img {
      width: 22px;
      height: 22px;
    }
    .login-right {
      flex: 1.2;
      display: flex;
      align-items: center;
      justify-content: center;
      background: transparent;
      min-width: 320px;
      padding: 0 32px 0 0;
    }
    .login-right img {
      width: 100%;
      max-width: 420px;
      height: auto;
      display: block;
    }
    .login-designed {
      text-align: right;
      font-size: 0.95rem;
      color: #888;
      margin-top: 18px;
      margin-right: 12px;
    }
    @media (max-width: 1000px) {
      .login-main-container {
        flex-direction: column;
        max-width: 98vw;
        min-height: unset;
      }
      .login-right {
        padding: 0 0 32px 0;
        justify-content: center;
      }
      .login-left {
        padding: 32px 18px 18px 18px;
      }
    }
    @media (max-width: 700px) {
      .login-main-container {
        border-radius: 0;
        margin: 0;
      }
      .login-right img {
        max-width: 90vw;
      }
    }
  </style>
</head>
<body>
  <div class="login-main-container">
    <div class="login-left">
      <div class="login-title">Welcome to Student Information System🎓</div>
      <div class="login-desc">Please Login To Use the Platform</div>
      
      <?php if (!empty($login_error)): ?>
        <div class="login-error"><?php echo htmlspecialchars($login_error); ?></div>
      <?php endif; ?>
      
      <form class="login-form" method="POST" action="">
        <label for="username">Enter Username</label>
        <input type="text" id="username" name="username" placeholder="Enter Username" required>
        
        <label for="password">Enter Password</label>
        <input type="password" id="password" name="password" placeholder="Enter Password" required>
        
        <div class="login-remember">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember" style="margin:0;">Remember Me</label>
        </div>
        
        <button type="submit" class="login-btn">SIGN IN</button>
      </form>

      <div style="margin-top:18px;text-align:right;">
        <a href="#" id="showForgot" style="color:#2e2eff;text-decoration:underline;font-size:1em;cursor:pointer;">Forgot Password?</a>
      </div>

      <!-- Forgot Password Modal -->
      <div id="forgotModal" style="display:none;position:fixed;z-index:2000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.18);align-items:center;justify-content:center;">
        <div style="background:#fff;padding:32px 28px 24px 28px;border-radius:16px;max-width:350px;width:90vw;box-shadow:0 4px 24px rgba(44,62,80,0.10);position:relative;">
          <button id="closeForgot" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.3em;color:#888;cursor:pointer;">&times;</button>
          <h3 style="margin:0 0 18px 0;font-size:1.2em;color:#2e2eff;">Forgot Password</h3>
          <form id="forgotForm" method="POST" action="forgot-password.php">
            <label for="forgotName" style="font-size:1em;color:#222;">Enter Your Name</label>
            <input type="text" id="forgotName" name="name" required pattern="^[A-Za-z\s\-']+$" title="Only letters, spaces, hyphens, and apostrophes allowed" style="width:100%;padding:10px 14px;border-radius:8px;border:1.2px solid #bbb;margin-bottom:12px;margin-top:4px;">
            <label for="forgotEmail" style="font-size:1em;color:#222;">Enter Your Email</label>
            <input type="email" id="forgotEmail" name="email" required placeholder="Enter your email" style="width:100%;padding:10px 14px;border-radius:8px;border:1.2px solid #bbb;margin-bottom:18px;margin-top:4px;">
            <div style="font-size:0.98em;color:#666;margin-bottom:14px;">
              A code and link will be sent to your email to reset your password.
            </div>
            <button type="submit" style="width:100%;padding:10px 0;border-radius:8px;border:none;background:#2e2eff;color:#fff;font-size:1.08em;font-weight:600;cursor:pointer;">Send Code</button>
          </form>
        </div>
      </div>
    </div>
    <div class="login-right">
      <img src="../icons and images/p1.jpg" alt="Login Illustration">
    </div>
  </div>
  <div class="login-designed">
    designed by <b>DesTech</b>
  </div>
  <script>
    // Show/hide forgot password modal
    document.getElementById('showForgot').onclick = function() {
      document.getElementById('forgotModal').style.display = 'flex';
    };
    document.getElementById('closeForgot').onclick = function() {
      document.getElementById('forgotModal').style.display = 'none';
    };
    // Optional: close modal when clicking outside the box
    document.getElementById('forgotModal').onclick = function(e) {
      if (e.target === this) this.style.display = 'none';
    };

    // Handle forgot password form submission
    document.getElementById('forgotForm').onsubmit = function(e) {
      e.preventDefault();
      alert('A code will be sent to your email soon.');
      document.getElementById('forgotModal').style.display = 'none';
      this.reset();
    };
  </script>
</body>
</html>