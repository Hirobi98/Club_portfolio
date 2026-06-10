<?php
require_once 'db.php';

if (!isset($_GET['id'])) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM opportunities WHERE id = ?");
$stmt->execute([$_GET['id']]);
$opp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$opp) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($opp['title']); ?> | Spectrum</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-container { max-width: 900px; margin: 0 auto; padding: 120px 5% 80px; }
        .detail-hero { width: 100%; height: 400px; object-fit: cover; border-radius: 20px; border: 1px solid var(--glass-border); margin-bottom: 40px; }
        .detail-placeholder { width: 100%; height: 300px; border-radius: 20px; border: 1px solid var(--glass-border); margin-bottom: 40px; background: linear-gradient(135deg, #1a1a2e, #0d0d1a); display: flex; align-items: center; justify-content: center; }
        .detail-placeholder span { font-family:'Unbounded',sans-serif; color:var(--primary-gold); font-size:1.2rem; letter-spacing:3px; text-transform:uppercase; }
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
            <img src="spectrum-logo.jpg" alt="Logo" style="width:40px;height:40px;object-fit:contain;border-radius:50%;">
            SPECTRUM
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#opportunities" class="active">Opportunities</a></li>
        </ul>
    </nav>

    <div class="detail-container">
        <?php if (!empty($opp['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($opp['image']); ?>" alt="<?= htmlspecialchars($opp['title']); ?>" class="detail-hero">
        <?php else: ?>
            <div class="detail-placeholder">
                <span><?= htmlspecialchars($opp['title']); ?></span>
            </div>
        <?php endif; ?>

        <span class="detail-tag"><?= htmlspecialchars($opp['date_info']); ?></span>
        <h1 class="detail-title text-gold"><?= htmlspecialchars($opp['title']); ?></h1>
        <p class="detail-desc"><?= nl2br(htmlspecialchars($opp['description'])); ?></p>

        <?php if (!empty($opp['timeline'])): ?>
        <div class="detail-timeline">
            <h3>Details</h3>
            <?= nl2br(htmlspecialchars($opp['timeline'])); ?>
        </div>
        <?php endif; ?>

        <div style="margin-top:40px;">
            <a href="index.php#opportunities" class="btn-primary">← Back to Opportunities</a>
        </div>
    </div>
</body>
</html>
