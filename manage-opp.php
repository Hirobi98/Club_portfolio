<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $tag = trim($_POST['tag'] ?? 'OPPORTUNITY');
    $date_info = trim($_POST['date_info'] ?? 'Ongoing');
    $description = trim($_POST['description'] ?? '');
    $timeline = trim($_POST['timeline'] ?? '');
    $edit_id = $_POST['edit_id'] ?? null;

    if (empty($title) || empty($description)) {
        $error = "Title and description are required.";
    } else {
        try {
            // Handle image upload
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($ext, $allowed)) {
                    $filename = 'opp_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $filename);
                    $image = $filename;
                } else {
                    $error = "Invalid image format. Use JPG, PNG, GIF, or WebP.";
                }
            }

            if (empty($error)) {
                if ($edit_id) {
                    if ($image) {
                        $stmt = $pdo->prepare("UPDATE opportunities SET title=?, tag=?, image=?, date_info=?, description=?, timeline=? WHERE id=?");
                        $stmt->execute([$title, $tag, $image, $date_info, $description, $timeline, $edit_id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE opportunities SET title=?, tag=?, date_info=?, description=?, timeline=? WHERE id=?");
                        $stmt->execute([$title, $tag, $date_info, $description, $timeline, $edit_id]);
                    }
                    $message = "Opportunity updated successfully!";
                } else {
                    if (!$image) {
                        $image = ''; // Allow no image for opportunities
                    }
                    $stmt = $pdo->prepare("INSERT INTO opportunities (title, tag, image, date_info, description, timeline) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $tag, $image, $date_info, $description, $timeline]);
                    $message = "Opportunity created successfully!";
                }
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

// If editing, fetch the opportunity
$opp = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM opportunities WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $opp = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $opp ? 'Edit' : 'Add' ?> Opportunity | Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(rgba(5, 8, 15, 0.92), rgba(5, 8, 15, 0.92)),
                        url('hero-bg.jpg') center center / cover no-repeat fixed !important;
        }
        .manage-container { padding: 60px 8%; max-width: 800px; margin: 0 auto; }
        .manage-form { background: rgba(10, 15, 28, 0.75); padding: 35px; border-radius: 16px; border: 1px solid var(--glass-border); backdrop-filter: blur(12px); margin-top: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: var(--primary-gold); font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; font-family: inherit; font-size: 1rem; outline: none; transition: border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--primary-gold); }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .btn-row { display: flex; gap: 15px; margin-top: 25px; }
        .alert-box { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-error { background: rgba(255, 0, 0, 0.12); border: 1px solid rgba(255, 80, 80, 0.3); color: #ff9999; }
        .current-img { border-radius: 10px; border: 1px solid var(--glass-border); margin-top: 8px; }
        .hdr-flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    </style>
</head>
<body>
    <div class="manage-container">
        <div class="hdr-flex">
            <h1><?= $opp ? 'Edit' : 'Add New' ?> <span class="text-gold">Opportunity</span></h1>
            <a href="dashboard.php" class="btn-primary" style="padding: 10px 20px; font-size: 0.85rem;">← Back to Dashboard</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-box alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="manage-form">
            <form action="manage-opp.php" method="POST" enctype="multipart/form-data">
                <?php if ($opp): ?>
                    <input type="hidden" name="edit_id" value="<?= $opp['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Opportunity Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($opp['title'] ?? ''); ?>" required placeholder="e.g. Unilever Career Collaboration">
                </div>

                <div class="form-group">
                    <label>Tag</label>
                    <input type="text" name="tag" value="<?= htmlspecialchars($opp['tag'] ?? ''); ?>" placeholder="e.g. Workshop, Challenge">
                </div>

                <div class="form-group">
                    <label>Date Info</label>
                    <input type="text" name="date_info" value="<?= htmlspecialchars($opp['date_info'] ?? ''); ?>" placeholder="e.g. Ongoing, March 2025">
                </div>

                <div class="form-group">
                    <label>Image <?= $opp ? '(leave empty to keep current)' : '(optional)' ?></label>
                    <input type="file" name="image" accept="image/*">
                    <?php if ($opp && !empty($opp['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($opp['image']); ?>" class="current-img" style="width: 120px; height: 80px; object-fit: cover;">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Short Description</label>
                    <textarea name="description" rows="3" required placeholder="Brief summary shown on card..."><?= htmlspecialchars($opp['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Full Details / Timeline (displayed on detail page)</label>
                    <textarea name="timeline" rows="6" placeholder="Detailed opportunity information..."><?= htmlspecialchars($opp['timeline'] ?? ''); ?></textarea>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn-primary" style="cursor: pointer; padding: 14px 30px;"><?= $opp ? 'Update Opportunity' : 'Create Opportunity' ?></button>
                    <a href="dashboard.php" class="btn-primary" style="background: transparent; border: 1px solid var(--glass-border); color: var(--text-muted); text-decoration: none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
