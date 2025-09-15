<?php
// Database connection and functions

function getDbConnection() {
    global $blog_config;
    $host = $blog_config['db_host'] ?? 'localhost';
    $user = $blog_config['db_user'] ?? 'root';
    $pass = $blog_config['db_pass'] ?? '';
    $dbname = $blog_config['db_name'] ?? 'blog_db';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Log error or handle gracefully
        error_log("DB Connection failed: " . $e->getMessage());
        return null;
    }
}

function incrementPostViews($slug) {
    $pdo = getDbConnection();
    if (!$pdo) return 0;

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $cooldown_hours = 24; // Only count unique views per IP every 24 hours

    try {
        // Check if this IP viewed this post recently
        $stmt = $pdo->prepare("SELECT id FROM view_logs WHERE post_slug = ? AND ip_address = ? AND viewed_at > DATE_SUB(NOW(), INTERVAL ? HOUR)");
        $stmt->execute([$slug, $ip, $cooldown_hours]);
        $recent_view = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$recent_view) {
            // Log the view
            $stmt = $pdo->prepare("INSERT INTO view_logs (post_slug, ip_address) VALUES (?, ?)");
            $stmt->execute([$slug, $ip]);

            // Increment views
            $stmt = $pdo->prepare("INSERT INTO page_views (post_slug, views) VALUES (?, 1) ON DUPLICATE KEY UPDATE views = views + 1");
            $stmt->execute([$slug]);
        }

        // Get current views
        $stmt = $pdo->prepare("SELECT views FROM page_views WHERE post_slug = ?");
        $stmt->execute([$slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['views'] : 0;
    } catch (PDOException $e) {
        error_log("Increment views failed: " . $e->getMessage());
        return 0;
    }
}

function getPostViews($slug) {
    $pdo = getDbConnection();
    if (!$pdo) return 0;

    try {
        $stmt = $pdo->prepare("SELECT views FROM page_views WHERE post_slug = ?");
        $stmt->execute([$slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['views'] : 0;
    } catch (PDOException $e) {
        error_log("Get views failed: " . $e->getMessage());
        return 0;
    }
}
?>