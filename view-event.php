<?php
require_once 'db.php';

if (!isset($_GET['id'])) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$_GET['id']]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title']); ?> | Spectrum</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-container { max-width: 900px; margin: 0 auto; padding: 120px 5% 80px; }
        .detail-hero { width: 100%; height: 400px; object-fit: cover; border-radius: 20px; border: 1px solid var(--glass-border); margin-bottom: 40px; }
        .detail-tag { display: inline-block; background: var(--primary-gold); color: var(--bg-darker); font-weight: 700; font-size: 0.8rem; padding: 6px 18px; border-radius: 20px; margin-bottom: 15px; }
        .detail-title { font-size: clamp(1.8rem, 4vw, 2.8rem); margin-bottom: 20px; }
        .detail-desc { color: var(--text-muted); font-size: 1.1rem; line-height: 1.9; margin-bottom: 30px; }
        .detail-timeline { background: rgba(10,15,28,0.6); padding: 30px; border-radius: 16px; border: 1px solid var(--glass-border); color: var(--text-muted); line-height: 1.8; white-space: pre-wrap; }
        .detail-timeline h3 { color: var(--primary-gold); margin-bottom: 15px; }
    </style>
</head>
<body>
    <nav class="navbar-container glass">
        <a href="index.php" class="brand-logo">
            <img src="spectrum-logo.jpg" alt="Spectrum Logo" style="width:40px;height:40px;object-fit:contain;border-radius:50%;">
            SPECTRUM
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#events" class="active">Events</a></li>
        </ul>
    </nav>

    <div class="detail-container">
        <img src="uploads/<?= htmlspecialchars($event['cover_image']); ?>" alt="<?= htmlspecialchars($event['title']); ?>" class="detail-hero">
        <span class="detail-tag"><?= htmlspecialchars($event['tag']); ?></span>
        <?php if ($event['is_flagship']): ?>
            <span class="detail-tag" style="background:transparent;border:1px solid var(--primary-gold);color:var(--primary-gold);margin-left:8px;">★ Flagship</span>
        <?php endif; ?>
        <h1 class="detail-title text-gold"><?= htmlspecialchars($event['title']); ?></h1>
        <p class="detail-desc"><?= nl2br(htmlspecialchars($event['description'])); ?></p>

        <?php if (!empty($event['timeline'])): ?>
        <div class="detail-timeline">
            <h3>Event Details</h3>
            <?= nl2br(htmlspecialchars($event['timeline'])); ?>
        </div>
        <?php endif; ?>

        <div style="margin-top:40px;">
            <a href="index.php#events" class="btn-primary">← Back to Events</a>
        </div>
    </div>
</body>
</html>
