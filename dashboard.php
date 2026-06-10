<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$message = $_GET['msg'] ?? '';

// Handle Homepage Content Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_homepage'])) {
    try {
        $updates = [
            'home_hero_title' => $_POST['home_hero_title'],
            'home_hero_subtitle' => $_POST['home_hero_subtitle'],
            'about_description' => $_POST['about_description'],
            'achievements_count' => $_POST['achievements_count'],
            'members_count' => $_POST['members_count']
        ];

        $stmt = $pdo->prepare("UPDATE homepage_settings SET setting_value = :value WHERE setting_key = :key");
        foreach ($updates as $key => $value) {
            $stmt->execute(['value' => $value, 'key' => $key]);
        }
        header("Location: dashboard.php?msg=" . urlencode("Homepage content updated successfully!"));
        exit;
    } catch (Exception $e) {
        $message = "Error updating content: " . $e->getMessage();
    }
}

// Handle Deletions
if (isset($_GET['delete_event'])) {
    $id = (int)$_GET['delete_event'];
    // Delete cover image file
    $stmt = $pdo->prepare("SELECT cover_image FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && !empty($row['cover_image']) && file_exists('uploads/' . $row['cover_image'])) {
        unlink('uploads/' . $row['cover_image']);
    }
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard.php?msg=' . urlencode('Event deleted.'));
    exit;
}

if (isset($_GET['delete_opp'])) {
    $id = (int)$_GET['delete_opp'];
    $stmt = $pdo->prepare("SELECT image FROM opportunities WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && !empty($row['image']) && file_exists('uploads/' . $row['image'])) {
        unlink('uploads/' . $row['image']);
    }
    $stmt = $pdo->prepare("DELETE FROM opportunities WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard.php?msg=' . urlencode('Opportunity deleted.'));
    exit;
}

if (isset($_GET['delete_achievement'])) {
    $id = (int)$_GET['delete_achievement'];
    $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard.php?msg=' . urlencode('Achievement deleted.'));
    exit;
}

if (isset($_GET['delete_member_achievement'])) {
    $id = (int)$_GET['delete_member_achievement'];
    $stmt = $pdo->prepare("SELECT image FROM member_achievements WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && !empty($row['image']) && file_exists('uploads/' . $row['image'])) {
        unlink('uploads/' . $row['image']);
    }
    $stmt = $pdo->prepare("DELETE FROM member_achievements WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard.php?msg=' . urlencode('Member achievement deleted.'));
    exit;
}

// Fetch all content
$events = $pdo->query("SELECT * FROM events ORDER BY created_at DESC")->fetchAll();
$opportunities = $pdo->query("SELECT * FROM opportunities ORDER BY created_at DESC")->fetchAll();

try {
    $achievements = $pdo->query("SELECT * FROM achievements ORDER BY display_order ASC, created_at DESC")->fetchAll();
} catch (Exception $e) {
    $achievements = [];
}

try {
    $member_achievements = $pdo->query("SELECT * FROM member_achievements ORDER BY created_at DESC")->fetchAll();
} catch (Exception $e) {
    $member_achievements = [];
}

$settings_raw = $pdo->query("SELECT * FROM homepage_settings")->fetchAll(PDO::FETCH_ASSOC);
$settings = [];
foreach ($settings_raw as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Control Panel</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: linear-gradient(rgba(5,8,15,0.92),rgba(5,8,15,0.92)), url('hero-bg.jpg') center/cover no-repeat fixed !important; }
        .dashboard-container { padding: 60px 8%; }
        .table-section { margin-top: 40px; background: rgba(10,15,28,0.75); padding: 25px; border-radius: 16px; border: 1px solid var(--glass-border); backdrop-filter: blur(12px); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid var(--glass-border); }
        th { color: var(--primary-gold); }
        td { color: #fff; }
        .action-btn { padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; margin-right: 5px; display: inline-block; transition: all 0.3s; }
        .btn-edit { background: #007bff; color: white; }
        .btn-edit:hover { background: #0056b3; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-delete:hover { background: #a71d2a; }
        .hdr-flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group.full-width { grid-column: span 2; }
        .form-group label { color: var(--primary-gold); font-size: 0.9rem; font-weight: 600; }
        .form-group input, .form-group textarea { padding: 10px; background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); border-radius: 8px; color: white; font-family: inherit; outline: none; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--primary-gold); }
        .alert-box { background: rgba(0,255,0,0.12); border: 1px solid rgba(0,255,0,0.3); color: #99ff99; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .empty-state { color: var(--text-muted); text-align: center; padding: 30px; font-style: italic; }
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full-width { grid-column: span 1; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="hdr-flex">
            <h1>Spectrum <span class="text-gold">Dashboard</span></h1>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="index.php" class="btn-primary" style="padding:10px 20px;font-size:0.85rem;background:transparent;border:1px solid var(--glass-border);color:var(--text-muted);">View Website</a>
                <a href="logout.php" class="btn-primary" style="background:#cc0000;padding:10px 20px;font-size:0.85rem;">Logout</a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert-box">✅ <?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- HOMEPAGE CONTENT -->
        <div class="table-section">
            <h2>Modify Homepage <span class="text-gold">Content</span></h2>
            <form action="dashboard.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Hero Title</label>
                        <input type="text" name="home_hero_title" value="<?= htmlspecialchars($settings['home_hero_title'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Hero Subtitle</label>
                        <input type="text" name="home_hero_subtitle" value="<?= htmlspecialchars($settings['home_hero_subtitle'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group full-width">
                        <label>About Us Description</label>
                        <textarea name="about_description" rows="4" required><?= htmlspecialchars($settings['about_description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Achievements Metric</label>
                        <input type="text" name="achievements_count" value="<?= htmlspecialchars($settings['achievements_count'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Members Metric</label>
                        <input type="text" name="members_count" value="<?= htmlspecialchars($settings['members_count'] ?? ''); ?>" required>
                    </div>
                </div>
                <button type="submit" name="update_homepage" class="btn-primary" style="margin-top:20px;cursor:pointer;">Save Homepage Changes</button>
            </form>
        </div>

        <!-- EVENTS -->
        <div class="table-section">
            <div class="hdr-flex">
                <h2>Manage <span class="text-gold">Events</span></h2>
                <a href="manage-event.php" class="btn-primary" style="padding:8px 16px;font-size:0.9rem;">+ Add Event</a>
            </div>
            <?php if (empty($events)): ?>
                <p class="empty-state">No events yet. Add your first event above.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>Image</th><th>Title</th><th>Tag</th><th>Type</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $e): ?>
                    <tr>
                        <td><img src="uploads/<?= htmlspecialchars($e['cover_image']); ?>" style="width:60px;height:40px;object-fit:cover;border-radius:6px;"></td>
                        <td><?= htmlspecialchars($e['title']); ?></td>
                        <td><?= htmlspecialchars($e['tag']); ?></td>
                        <td><?= $e['is_flagship'] ? '★ Flagship' : 'Normal'; ?></td>
                        <td>
                            <a href="manage-event.php?id=<?= $e['id']; ?>" class="action-btn btn-edit">Edit</a>
                            <a href="dashboard.php?delete_event=<?= $e['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this event?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- OPPORTUNITIES -->
        <div class="table-section">
            <div class="hdr-flex">
                <h2>Manage <span class="text-gold">Opportunities</span></h2>
                <a href="manage-opp.php" class="btn-primary" style="padding:8px 16px;font-size:0.9rem;">+ Add Opportunity</a>
            </div>
            <?php if (empty($opportunities)): ?>
                <p class="empty-state">No opportunities yet. Add your first opportunity above.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>Image</th><th>Title</th><th>Date Info</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($opportunities as $o): ?>
                    <tr>
                        <td>
                            <?php if (!empty($o['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($o['image']); ?>" style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                            <?php else: ?>
                                <span style="color:var(--text-muted);font-size:0.8rem;">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($o['title']); ?></td>
                        <td><?= htmlspecialchars($o['date_info']); ?></td>
                        <td>
                            <a href="manage-opp.php?id=<?= $o['id']; ?>" class="action-btn btn-edit">Edit</a>
                            <a href="dashboard.php?delete_opp=<?= $o['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this opportunity?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- ACHIEVEMENTS -->
        <div class="table-section">
            <div class="hdr-flex">
                <h2>Manage <span class="text-gold">Achievements</span></h2>
                <a href="manage-achievement.php" class="btn-primary" style="padding:8px 16px;font-size:0.9rem;">+ Add Achievement</a>
            </div>
            <?php if (empty($achievements)): ?>
                <p class="empty-state">No achievements yet. Add your first achievement above.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>#</th><th>Title</th><th>Description</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($achievements as $a): ?>
                    <tr>
                        <td style="color:var(--primary-gold);font-weight:600;"><?= $a['display_order']; ?></td>
                        <td><?= htmlspecialchars($a['title']); ?></td>
                        <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($a['description']); ?></td>
                        <td>
                            <a href="manage-achievement.php?id=<?= $a['id']; ?>" class="action-btn btn-edit">Edit</a>
                            <a href="dashboard.php?delete_achievement=<?= $a['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this achievement?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- MEMBER ACHIEVEMENTS -->
        <div class="table-section">
            <div class="hdr-flex">
                <h2>Manage <span class="text-gold">Member Achievements</span></h2>
                <a href="manage-member-achievement.php" class="btn-primary" style="padding:8px 16px;font-size:0.9rem;">+ Add Member Achievement</a>
            </div>
            <?php if (empty($member_achievements)): ?>
                <p class="empty-state">No member achievements yet. Add your first one above.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>Image</th><th>Name</th><th>Title</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($member_achievements as $ma): ?>
                    <tr>
                        <td>
                            <?php if (!empty($ma['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($ma['image']); ?>" style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                            <?php else: ?>
                                <span style="color:var(--text-muted);font-size:0.8rem;">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($ma['name']); ?></td>
                        <td><?= htmlspecialchars($ma['title']); ?></td>
                        <td>
                            <a href="manage-member-achievement.php?id=<?= $ma['id']; ?>" class="action-btn btn-edit">Edit</a>
                            <a href="dashboard.php?delete_member_achievement=<?= $ma['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this member achievement?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>