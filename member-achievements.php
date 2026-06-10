<?php
require_once 'db.php';

// Fetch ALL member achievements from database (no limit)
try {
    $db_member_achievements = $pdo->query("SELECT * FROM member_achievements ORDER BY created_at DESC")->fetchAll();
} catch (Exception $e) {
    $db_member_achievements = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Member Achievements | Spectrum KUET</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .opportunities-hero {
      padding: 140px 5% 60px;
      text-align: center;
      position: relative;
      background-color: rgba(5, 8, 15, 0.85);
    }
    .opportunities-hero h1 {
      font-size: clamp(2rem, 5vw, 3.5rem);
      margin-bottom: 15px;
      text-shadow: 0 4px 30px rgba(0, 0, 0, 0.8);
    }
    .opportunities-hero p {
      color: var(--text-muted);
      font-size: 1.1rem;
      max-width: 650px;
      margin: 0 auto;
    }
    .opp-section {
      padding: 60px 5% 100px;
      background-color: rgba(5, 8, 15, 0.88);
    }
    .opp-placeholder-img {
      width: 100%;
      height: 250px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--primary-gold);
      text-decoration: none;
      font-weight: 600;
      font-size: 0.95rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: var(--transition-smooth);
      margin-top: 30px;
    }
    .back-link:hover { color: #fff; gap: 12px; }
    .back-link::before { content: '←'; font-size: 1.2rem; }
  </style>
</head>
<body>

  <nav class="navbar-container glass">
    <a href="first_page.php" class="brand-logo">
      <img src="spectrum-logo.jpg" alt="Spectrum Logo" style="width: 40px; height: 40px; object-fit: contain; border-radius: 50%;">
      SPECTRUM
    </a>
    <ul class="nav-links">
      <li><a href="first_page.php#home">Home</a></li>
      <li><a href="first_page.php#about">About</a></li>
      <li><a href="first_page.php#events">Events</a></li>
      <li><a href="first_page.php#opportunities">Opportunities</a></li>
      <li><a href="first_page.php#achievements">Achievements</a></li>
      <li><a href="login.php" style="color:var(--primary-gold);">Admin Login</a></li>
    </ul>
  </nav>

  <section class="opportunities-hero">
    <h1 class="reveal">Member <span class="text-gold">Achievements</span></h1>
    <p class="reveal">Celebrating the outstanding successes, milestones, and brilliant accomplishments of our dedicated Spectrum members.</p>
  </section>

  <section class="opp-section">
    <div class="events-grid" style="max-width:1200px; margin:0 auto;">

      <?php if (!empty($db_member_achievements)): ?>
        <?php foreach ($db_member_achievements as $ma): ?>
          <div class="event-card glass reveal" style="cursor: default; transform: none; box-shadow: none;">
            <?php if (!empty($ma['image'])): ?>
              <img src="uploads/<?= htmlspecialchars($ma['image']); ?>" alt="<?= htmlspecialchars($ma['name']); ?>" class="event-card-image" style="height: 250px; transform: none; filter: none;">
            <?php else: ?>
              <div class="event-card-image opp-placeholder-img" style="background: linear-gradient(135deg, #1a1a2e 0%, #0d0d1a 100%); display:flex; align-items:center; justify-content:center; height:250px;">
                <span style="font-family:'Unbounded',sans-serif; color:var(--primary-gold); font-size:0.75rem; text-align:center; padding:0 20px; letter-spacing:2px; text-transform:uppercase;"><?= htmlspecialchars($ma['name']); ?></span>
              </div>
            <?php endif; ?>
            <div class="event-card-body">
              <h3 class="text-gold" style="margin-top: 0; margin-bottom: 5px; font-size: 1.4rem;"><?= htmlspecialchars($ma['name']); ?></h3>
              <h4 style="color: #fff; margin-bottom: 15px; font-size: 1.1rem; font-family: 'Inter', sans-serif; font-weight: 500;"><?= htmlspecialchars($ma['title']); ?></h4>
              <p style="font-size: 0.95rem;"><?= nl2br(htmlspecialchars($ma['description'])); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="reveal" style="text-align:center; color:var(--text-muted); grid-column: 1/-1;">No member achievements added yet. Check back soon!</p>
      <?php endif; ?>

    </div>

    <div style="text-align: center; margin-top: 50px;">
      <a href="first_page.php#member-achievements" class="back-link">Back to Home</a>
    </div>
  </section>

  <footer>
    <h2 class="brand-logo" style="justify-content: center; margin-bottom: 15px;">
      <img src="spectrum-logo.jpg" alt="Spectrum Logo" style="width: 30px; height: 30px; object-fit: contain; border-radius: 50%;">
      SPECTRUM
    </h2>
    <p style="color: var(--text-muted);">Stay ahead of the curve.</p>
    <div class="footer-socials">
      <a href="#">Facebook</a>
      <a href="#">LinkedIn</a>
      <a href="#">Instagram</a>
    </div>
    <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 20px;">&copy; 2025 Spectrum KUET. All rights reserved.</p>
  </footer>

  <script src="script.js"></script>
</body>
</html>
