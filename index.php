<?php
require_once 'db.php';

// Fetch dynamic homepage text configuration settings
try {
    $settings_raw = $pdo->query("SELECT * FROM homepage_settings")->fetchAll(PDO::FETCH_ASSOC);
    $settings = [];
    foreach ($settings_raw as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    $settings = [];
}

// Fetch live events from database
try {
    $stmtEvents = $pdo->query("SELECT * FROM events ORDER BY created_at DESC");
    $db_events = $stmtEvents->fetchAll();
} catch (Exception $e) {
    $db_events = [];
}

// Fetch live opportunities from database
try {
    $stmtOpps = $pdo->query("SELECT * FROM opportunities ORDER BY created_at DESC LIMIT 3");
    $db_opportunities = $stmtOpps->fetchAll();
} catch (Exception $e) {
    $db_opportunities = [];
}

// Fetch achievements from database
try {
    $stmtAch = $pdo->query("SELECT * FROM achievements ORDER BY display_order ASC, created_at DESC");
    $db_achievements = $stmtAch->fetchAll();
} catch (Exception $e) {
    $db_achievements = [];
}
// Fetch member achievements
try {
    $stmtMem = $pdo->query("SELECT * FROM member_achievements ORDER BY created_at DESC");
    $db_member_achievements = $stmtMem->fetchAll();
} catch (Exception $e) {
    $db_member_achievements = [];
}

// Flag: this is the admin preview (accessed via dashboard "View Website")
$is_admin_preview = true;

// Include the main homepage template
include 'first_page.php';
?>