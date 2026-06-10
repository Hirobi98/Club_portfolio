<?php
// Bootstrap: if accessed directly (not included from index.php), fetch DB data
if (!isset($settings)) {
    require_once 'db.php';
    try {
        $settings_raw = $pdo->query("SELECT * FROM homepage_settings")->fetchAll(PDO::FETCH_ASSOC);
        $settings = [];
        foreach ($settings_raw as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Exception $e) { $settings = []; }

    try {
        $db_events = $pdo->query("SELECT * FROM events ORDER BY created_at DESC")->fetchAll();
    } catch (Exception $e) { $db_events = []; }

    try {
        $db_opportunities = $pdo->query("SELECT * FROM opportunities ORDER BY created_at DESC LIMIT 3")->fetchAll();
    } catch (Exception $e) { $db_opportunities = []; }

    try {
        $db_achievements = $pdo->query("SELECT * FROM achievements ORDER BY display_order ASC, created_at DESC")->fetchAll();
    } catch (Exception $e) { $db_achievements = []; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Spectrum | KUET</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <nav class="navbar-container glass">
    <a href="#" class="brand-logo">
      <img src="spectrum-logo.jpg" alt="Spectrum Logo" style="width: 40px; height: 40px; object-fit: contain; border-radius: 50%;">
      SPECTRUM
    </a>
    <ul class="nav-links">
      <li><a href="#home" class="active">Home</a></li>
      <li><a href="#about">About</a></li>
      <li><a href="#events">Events</a></li>
      <li><a href="#opportunities">Opportunities</a></li>
      <li><a href="#achievements">Achievements</a></li>
    </ul>
  </nav>

  <header id="home" class="hero">
    <h1 class="reveal"><?= htmlspecialchars($settings['home_hero_title'] ?? 'SPECTRUM'); ?></h1>
    <p class="reveal"><?= htmlspecialchars($settings['home_hero_subtitle'] ?? 'Spectrum is a professional skill development club of KUET. We bridge the gap between academic theory and industry-ready competence, preparing you for the corporate world.'); ?></p>
    <a href="#events" class="btn-primary reveal">Discover Events</a>
  </header>

  <section id="about" class="section-padding">
    <h2 class="section-title text-gold reveal">About Us</h2>
    <div class="about-content reveal" style="display: flex; flex-direction: column; align-items: center; gap: 30px;">
      <p>
        <?= htmlspecialchars($settings['about_description'] ?? 'Spectrum serves as a vital bridge between academic theory and industry-ready competence. As the dedicated club partner and host of the Hult Prize, we cultivate a culture of social entrepreneurship on campus. Our mission is to empower students with the skills they need to excel in the professional world.'); ?>
      </p>
      <img src="https://via.placeholder.com/800x400/05080f/FFD400?text=KUET+Campus+Image" alt="KUET Campus" style="width: 100%; max-width: 800px; border-radius: 20px; border: 1px solid var(--glass-border);">
    </div>

    <!-- Stats Bar -->
    <?php if (!empty($settings['achievements_count']) || !empty($settings['members_count'])): ?>
    <div class="reveal" style="display: flex; justify-content: center; gap: 60px; margin-top: 50px; flex-wrap: wrap;">
      <?php if (!empty($settings['achievements_count'])): ?>
      <div style="text-align: center;">
        <span class="text-gold" style="font-family:'Unbounded',sans-serif; font-size: 2rem; font-weight: 900; display: block;"><?= htmlspecialchars($settings['achievements_count']); ?></span>
        <span style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px;">Achievements</span>
      </div>
      <?php endif; ?>
      <?php if (!empty($settings['members_count'])): ?>
      <div style="text-align: center;">
        <span class="text-gold" style="font-family:'Unbounded',sans-serif; font-size: 2rem; font-weight: 900; display: block;"><?= htmlspecialchars($settings['members_count']); ?></span>
        <span style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px;">Members</span>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </section>

  <section id="events" class="section-padding">
    <h2 class="section-title reveal">Flagship <span class="text-gold">Events</span></h2>
    <div class="events-grid">

      <?php if (!empty($db_events)): ?>
        <?php foreach ($db_events as $event): ?>
          <a href="view-event.php?id=<?= $event['id']; ?>" class="event-card glass reveal">
            <img src="uploads/<?= htmlspecialchars($event['cover_image']); ?>" alt="<?= htmlspecialchars($event['title']); ?>" class="event-card-image">
            <div class="event-card-body">
              <span class="event-date"><?= htmlspecialchars($event['tag']); ?></span>
              <h3 class="text-gold"><?= htmlspecialchars($event['title']); ?></h3>
              <p><?= htmlspecialchars($event['description']); ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="reveal" style="text-align:center; color:var(--text-muted); grid-column: 1/-1;">No events added yet. Check back soon!</p>
      <?php endif; ?>

    </div>
  </section>

  <section id="opportunities" class="section-padding" style="background-color: var(--bg-darker);">
    <h2 class="section-title reveal">Student <span class="text-gold">Opportunities</span></h2>
    <div class="events-grid">

      <?php if (!empty($db_opportunities)): ?>
        <?php foreach ($db_opportunities as $opp): ?>
          <a href="view-opportunity.php?id=<?= $opp['id']; ?>" class="event-card glass reveal">
            <?php if (!empty($opp['image'])): ?>
              <img src="uploads/<?= htmlspecialchars($opp['image']); ?>" alt="<?= htmlspecialchars($opp['title']); ?>" class="event-card-image">
            <?php else: ?>
              <div class="event-card-image opp-placeholder-img" style="background: linear-gradient(135deg, #1a1a2e 0%, #0d0d1a 100%); display:flex; align-items:center; justify-content:center; height:200px;">
                <span style="font-family:'Unbounded',sans-serif; color:var(--primary-gold); font-size:0.75rem; text-align:center; padding:0 20px; letter-spacing:2px; text-transform:uppercase;"><?= htmlspecialchars($opp['title']); ?></span>
              </div>
            <?php endif; ?>
            <div class="event-card-body">
              <span class="event-date"><?= htmlspecialchars($opp['date_info']); ?></span>
              <h3 class="text-gold"><?= htmlspecialchars($opp['title']); ?></h3>
              <p><?= htmlspecialchars($opp['description']); ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="reveal" style="text-align:center; color:var(--text-muted); grid-column: 1/-1;">No opportunities added yet. Check back soon!</p>
      <?php endif; ?>

    </div>
    <div style="text-align: center; margin-top: 40px;" class="reveal">
      <a href="opportunities.php" class="btn-primary">View All Opportunities</a>
    </div>
  </section>

  <section id="achievements" class="section-padding">
    <h2 class="section-title reveal">Our <span class="text-gold">Achievements</span></h2>
    <div class="about-content reveal">
      <?php if (!empty($db_achievements)): ?>
        <p style="font-size: 1.1rem; text-align: left;">
          <?php foreach ($db_achievements as $i => $ach): ?>
            <strong class="text-gold">★ <?= htmlspecialchars($ach['title']); ?>:</strong> <?= htmlspecialchars($ach['description']); ?>
            <?php if ($i < count($db_achievements) - 1): ?><br><br><?php endif; ?>
          <?php endforeach; ?>
        </p>
      <?php else: ?>
        <p style="font-size: 1.1rem; text-align: left;">
          <strong class="text-gold">★ Hult Prize Success:</strong> Successfully hosted multiple on-campus rounds, leading to KUET teams participating in regional and global summits.<br><br>
          <strong class="text-gold">★ JobSpecs Excellence:</strong> Facilitated direct recruitment of hundreds of students by partnering with top multinational and local companies.<br><br>
          <strong class="text-gold">★ Empowering Startups:</strong> Mentored and guided numerous student-led startups to secure funding and recognition on national platforms.
        </p>
      <?php endif; ?>
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

  <?php if (!empty($is_admin_preview)): ?>
    <a href="dashboard.php" style="position:fixed; bottom:30px; right:30px; background:var(--primary-gold); color:var(--bg-darker); padding:12px 24px; border-radius:30px; font-weight:bold; z-index:9999; box-shadow: 0 4px 15px rgba(0,0,0,0.5); text-decoration:none; font-family:'Inter',sans-serif;">← Back to Dashboard</a>
  <?php endif; ?>

  <script src="script.js"></script>
</body>
</html>