<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

// Handle Item Deletions
if (isset($_GET['delete_event'])) {
    $id = (int)$_GET['delete_event'];
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard.php');
    exit;
}

if (isset($_GET['delete_opp'])) {
    $id = (int)$_GET['delete_opp'];
    $stmt = $pdo->prepare("DELETE FROM opportunities WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard.php');
    exit;
}

$events = $pdo->query("SELECT * FROM events ORDER BY created_at DESC")->fetchAll();
$opportunities = $pdo->query("SELECT * FROM opportunities ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Control Panel</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(rgba(5, 8, 15, 0.9), rgba(5, 8, 15, 0.9)), 
                        url('hero-bg.jpg') center center / cover no-repeat fixed !important;
        }

        .dashboard-container { padding: 60px 8%; }
        
        /* 2. Update your table sections to have a darker, blurred glass backing */
        .table-section { 
            margin-top: 40px; 
            background: rgba(10, 15, 28, 0.75); /* Darker solid tint */
            padding: 25px; 
            border-radius: 10px; 
            border: 1px solid var(--glass-border); 
            backdrop-filter: blur(12px); /* Premium frost blur effect */
            -webkit-backdrop-filter: blur(12px); /* Safari support */
        }
        
        /* 3. Ensure the text inside the tables stands out perfectly */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid var(--glass-border); }
        th { color: var(--primary-gold); }
        td { color: #ffffff; } /* Forces clean white visibility for the table data rows */
        
        .action-btn { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; margin-right: 5px; display: inline-block;}
        .btn-edit { background: #007bff; color: white; }
        .btn-delete { background: #dc3545; color: white; }
        .hdr-flex { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="hdr-flex">
            <h1>Spectrum Management <span class="text-gold">Dashboard</span></h1>
            <a href="logout.php" class="btn-primary" style="background:#cc0000;">Logout</a>
        </div>

        <div class="table-section">
            <div class="hdr-flex">
                <h2>Manage Events</h2>
                <a href="manage-event.php" class="btn-primary" style="padding: 8px 16px; font-size:0.9rem;">+ Add New Event</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Tag</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $e): ?>
                    <tr>
                        <td><img src="uploads/<?= htmlspecialchars($e['cover_image']); ?>" style="width:60px; height:40px; object-fit:cover; border-radius:4px;"></td>
                        <td><?= htmlspecialchars($e['title']); ?></td>
                        <td><?= htmlspecialchars($e['tag']); ?></td>
                        <td><?= $e['is_flagship'] ? 'Flagship' : 'Normal'; ?></td>
                        <td>
                            <a href="manage-event.php?id=<?= $e['id']; ?>" class="action-btn btn-edit">Edit</a>
                            <a href="dashboard.php?delete_event=<?= $e['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete event?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-section">
            <div class="hdr-flex">
                <h2>Manage Opportunities</h2>
                <a href="manage-opp.php" class="btn-primary" style="padding: 8px 16px; font-size:0.9rem;">+ Add Opportunity</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Date Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($opportunities as $o): ?>
                    <tr>
                        <td><img src="uploads/<?= htmlspecialchars($o['image']); ?>" style="width:60px; height:40px; object-fit:cover; border-radius:4px;"></td>
                        <td><?= htmlspecialchars($o['title']); ?></td>
                        <td><?= htmlspecialchars($o['date_info']); ?></td>
                        <td>
                            <a href="manage-opp.php?id=<?= $o['id']; ?>" class="action-btn btn-edit">Edit</a>
                            <a href="dashboard.php?delete_opp=<?= $o['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete item?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>