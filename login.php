<?php
session_start();
require_once 'db.php';

$error = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect inputs and clean accidental leading/trailing spaces
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        try {
            // Find the administrator by username
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin) {
                // Direct Plain Text Matching (No hashing functions used)
                if ($password === $admin['password']) {
                    $_SESSION['admin_logged_in'] = true;
                    
                    $_SESSION['admin'] = $admin['username'];
                    
                    // Check if dashboard.php exists to prevent a 404 crash
                    if (file_exists('dashboard.php')) {
                        header("Location: dashboard.php"); 
                        exit();
                    } else {
                        $success_message = "Login successful! Note: Create 'dashboard.php' in your root directory next to view the panel.";
                    }
                } else {
                    // Mismatch debugging helper
                    $error = "Password mismatch! Database has: '" . htmlspecialchars($admin['password']) . "', you typed: '" . htmlspecialchars($password) . "'";
                }
            } else {
                $error = "No admin user found matching this username.";
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
</head>
<body>
  <div class="section-padding" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
    <div class="event-card glass admin-box" style="padding: 40px; max-width: 400px; width: 100%;">
      <h2 class="text-gold" style="margin-bottom: 20px;">Admin Login</h2>
      
      <?php if (!empty($error)): ?>
        <div style="background: rgba(255, 0, 0, 0.2); border: 1px solid red; color: #ff9999; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; text-align: left;">
          <?= $error; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($success_message)): ?>
        <div style="background: rgba(0, 255, 0, 0.15); border: 1px solid #00ff00; color: #99ff99; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; text-align: left;">
          <?= $success_message; ?>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST" style="display: flex; flex-direction: column; gap: 15px; text-align: left;">
        <div>
          <label style="display: block; margin-bottom: 5px; color: var(--text-muted);">Username</label>
          <input type="text" name="username" required style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; color: white;">
        </div>
        <div>
          <label style="display: block; margin-bottom: 5px; color: var(--text-muted);">Password</label>
          <input type="password" name="password" required style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; color: white;">
        </div>
        <button type="submit" class="btn-primary" style="margin-top: 10px; width: 100%; cursor: pointer;">Login</button>
      </form>
    </div>
  </div>
</body>
</html>