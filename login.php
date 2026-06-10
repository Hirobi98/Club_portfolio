<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && $password === $admin['password']) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin'] = $admin['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } catch (Exception $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Spectrum</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background: linear-gradient(rgba(5, 8, 15, 0.92), rgba(5, 8, 15, 0.92)),
                  url('hero-bg.jpg') center center / cover no-repeat fixed !important;
    }
  </style>
</head>
<body>
  <div class="section-padding" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
    <div class="event-card glass admin-box" style="padding: 40px; max-width: 420px; width: 100%;">
      <h2 class="text-gold" style="margin-bottom: 30px;">Admin Login</h2>

      <?php if (!empty($error)): ?>
        <div style="background: rgba(255, 0, 0, 0.15); border: 1px solid rgba(255, 80, 80, 0.4); color: #ff9999; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; text-align: center;">
          <?= htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST" autocomplete="off" style="display: flex; flex-direction: column; gap: 18px; text-align: left;">
        <div>
          <label style="display: block; margin-bottom: 6px; color: var(--text-muted); font-size: 0.9rem;">Username</label>
          <input type="text" name="username" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" required
                 style="width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; font-size: 1rem; outline: none; transition: border-color 0.3s;"
                 onfocus="this.removeAttribute('readonly');this.style.borderColor='var(--primary-gold)'" onblur="this.style.borderColor='var(--glass-border)'">
        </div>
        <div>
          <label style="display: block; margin-bottom: 6px; color: var(--text-muted); font-size: 0.9rem;">Password</label>
          <input type="password" name="password" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" required
                 style="width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; font-size: 1rem; outline: none; transition: border-color 0.3s;"
                 onfocus="this.removeAttribute('readonly');this.style.borderColor='var(--primary-gold)'" onblur="this.style.borderColor='var(--glass-border)'">
        </div>
        <button type="submit" class="btn-primary" style="margin-top: 8px; width: 100%; cursor: pointer; font-size: 1rem; padding: 14px;">Login</button>
        <a href="first_page.php" class="btn-primary" style="background: transparent; border: 1px solid var(--glass-border); text-decoration: none; text-align: center; width: 100%; color: var(--text-muted); font-size: 0.9rem; padding: 12px;">← Back to Home</a>
      </form>
    </div>
  </div>
</body>
</html>