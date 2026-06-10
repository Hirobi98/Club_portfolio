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
    $tag = trim($_POST['tag'] ?? 'EVENT');
    $description = trim($_POST['description'] ?? '');
    $timeline = trim($_POST['timeline'] ?? '');
    $is_flagship = isset($_POST['is_flagship']) ? 1 : 0;
    $edit_id = $_POST['edit_id'] ?? null;

    if (empty($title) || empty($description)) {
        $error = "Title and description are required.";
    } else {
        try {
            // Handle image upload
            $cover_image = null;
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($ext, $allowed)) {
                    $filename = 'event_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                    move_uploaded_file($_FILES['cover_image']['tmp_name'], 'uploads/' . $filename);
                    $cover_image = $filename;
                } else {
                    $error = "Invalid image format. Use JPG, PNG, GIF, or WebP.";
                }
            }

            if (empty($error)) {
                if ($edit_id) {
                    // Update existing event
                    if ($cover_image) {
                        $stmt = $pdo->prepare("UPDATE events SET title=?, tag=?, cover_image=?, description=?, timeline=?, is_flagship=? WHERE id=?");
                        $stmt->execute([$title, $tag, $cover_image, $description, $timeline, $is_flagship, $edit_id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE events SET title=?, tag=?, description=?, timeline=?, is_flagship=? WHERE id=?");
                        $stmt->execute([$title, $tag, $description, $timeline, $is_flagship, $edit_id]);
                    }
                    $message = "Event updated successfully!";
                } else {
                    // Insert new event
                    if (!$cover_image) {
                        $error = "Cover image is required for new events.";
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO events (title, tag, cover_image, description, timeline, is_flagship) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$title, $tag, $cover_image, $description, $timeline, $is_flagship]);
                        $message = "Event created successfully!";
                    }
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

// If editing, fetch the event
$event = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $event ? 'Edit' : 'Add' ?> Event | Admin</title>
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
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; font-family: inherit; font-size: 1rem; outline: none; transition: border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--primary-gold); }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        .checkbox-group input[type="checkbox"] { width: 20px; height: 20px; accent-color: var(--primary-gold); }
        .btn-row { display: flex; gap: 15px; margin-top: 25px; }
        .alert-box { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: rgba(0, 255, 0, 0.12); border: 1px solid rgba(0, 255, 0, 0.3); color: #99ff99; }
        .alert-error { background: rgba(255, 0, 0, 0.12); border: 1px solid rgba(255, 80, 80, 0.3); color: #ff9999; }
        .current-img { border-radius: 10px; border: 1px solid var(--glass-border); margin-top: 8px; }
        .hdr-flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    </style>
</head>
<body>
    <div class="manage-container">
        <div class="hdr-flex">
            <h1><?= $event ? 'Edit' : 'Add New' ?> <span class="text-gold">Event</span></h1>
            <a href="dashboard.php" class="btn-primary" style="padding: 10px 20px; font-size: 0.85rem;">← Back to Dashboard</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-box alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="manage-form">
            <form action="manage-event.php" method="POST" enctype="multipart/form-data">
                <?php if ($event): ?>
                    <input type="hidden" name="edit_id" value="<?= $event['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Event Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($event['title'] ?? ''); ?>" required placeholder="e.g. JobSpecs 2025">
                </div>

                <div class="form-group">
                    <label>Tag / Date Label</label>
                    <input type="text" name="tag" value="<?= htmlspecialchars($event['tag'] ?? ''); ?>" required placeholder="e.g. Jan 12-13, 2025">
                </div>

                <div class="form-group">
                    <label>Cover Image <?= $event ? '(leave empty to keep current)' : '' ?></label>
                    <input type="file" name="cover_image" accept="image/*" <?= $event ? '' : 'required' ?>>
                    <?php if ($event && !empty($event['cover_image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($event['cover_image']); ?>" class="current-img" style="width: 120px; height: 80px; object-fit: cover;">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Short Description</label>
                    <textarea name="description" rows="3" required placeholder="Brief summary shown on card..."><?= htmlspecialchars($event['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Full Timeline / Details (displayed on detail page)</label>
                    <textarea name="timeline" rows="6" placeholder="Detailed event information..."><?= htmlspecialchars($event['timeline'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_flagship" id="is_flagship" <?= (!empty($event['is_flagship'])) ? 'checked' : ''; ?>>
                        <label for="is_flagship" style="margin-bottom: 0; cursor: pointer;">Mark as Flagship Event</label>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn-primary" style="cursor: pointer; padding: 14px 30px;"><?= $event ? 'Update Event' : 'Create Event' ?></button>
                    <a href="dashboard.php" class="btn-primary" style="background: transparent; border: 1px solid var(--glass-border); color: var(--text-muted); text-decoration: none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
