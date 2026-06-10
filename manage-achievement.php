<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $edit_id = $_POST['edit_id'] ?? null;

    if (empty($title) || empty($description)) {
        $error = "Title and description are required.";
    } else {
        try {
            if ($edit_id) {
                $stmt = $pdo->prepare("UPDATE achievements SET title=?, description=?, display_order=? WHERE id=?");
                $stmt->execute([$title, $description, $display_order, $edit_id]);
                $message = "Achievement updated successfully!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO achievements (title, description, display_order) VALUES (?, ?, ?)");
                $stmt->execute([$title, $description, $display_order]);
                $message = "Achievement created successfully!";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }

    if (!empty($message)) {
        header("Location: dashboard.php?msg=" . urlencode($message));
        exit;
    }
}

$achievement = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM achievements WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $achievement = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $achievement ? 'Edit' : 'Add' ?> Achievement | Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: linear-gradient(rgba(5,8,15,0.92),rgba(5,8,15,0.92)), url('hero-bg.jpg') center/cover no-repeat fixed !important; }
        .manage-container { padding: 60px 8%; max-width: 800px; margin: 0 auto; }
        .manage-form { background: rgba(10,15,28,0.75); padding: 35px; border-radius: 16px; border: 1px solid var(--glass-border); backdrop-filter: blur(12px); margin-top: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: var(--primary-gold); font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; font-family: inherit; font-size: 1rem; outline: none; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--primary-gold); }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .btn-row { display: flex; gap: 15px; margin-top: 25px; }
        .alert-error { background: rgba(255,0,0,0.12); border: 1px solid rgba(255,80,80,0.3); color: #ff9999; padding: 12px; border-radius: 10px; margin-bottom: 20px; }
        .hdr-flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    </style>
</head>
<body>
    <div class="manage-container">
        <div class="hdr-flex">
            <h1><?= $achievement ? 'Edit' : 'Add New' ?> <span class="text-gold">Achievement</span></h1>
            <a href="dashboard.php" class="btn-primary" style="padding:10px 20px;font-size:0.85rem;">← Back to Dashboard</a>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div class="manage-form">
            <form action="manage-achievement.php" method="POST">
                <?php if ($achievement): ?>
                    <input type="hidden" name="edit_id" value="<?= $achievement['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>Achievement Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($achievement['title'] ?? ''); ?>" required placeholder="e.g. Hult Prize Success">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?= htmlspecialchars($achievement['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Display Order (lower = first)</label>
                    <input type="number" name="display_order" value="<?= htmlspecialchars($achievement['display_order'] ?? '0'); ?>" min="0">
                </div>
                <div class="btn-row">
                    <button type="submit" class="btn-primary" style="cursor:pointer;padding:14px 30px;"><?= $achievement ? 'Update' : 'Create' ?> Achievement</button>
                    <a href="dashboard.php" class="btn-primary" style="background:transparent;border:1px solid var(--glass-border);color:var(--text-muted);text-decoration:none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
