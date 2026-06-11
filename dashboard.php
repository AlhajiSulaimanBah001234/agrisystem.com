<?php
session_start();
$base_path = "../";

include("../config/db.php");
include("../includes/auth.php");

/* ================= SECURITY (CENTRALIZED) ================= */
requireLogin();
requireRole("farmer");

$user_id = $_SESSION['user_id'];

/* ================= STATS ================= */
$stats = [
    'articles' => $conn->query("SELECT COUNT(*) AS c FROM posts WHERE status='Published'")->fetch_assoc()['c'],
    'prices' => $conn->query("SELECT COUNT(*) AS c FROM market_prices")->fetch_assoc()['c'],
    'my_questions' => $conn->query("SELECT COUNT(*) AS c FROM questions WHERE user_id=$user_id")->fetch_assoc()['c'],
    'favorites' => $conn->query("SELECT COUNT(*) AS c FROM favorites WHERE user_id=$user_id")->fetch_assoc()['c'],
];

/* ================= DATA ================= */
$notifications = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");

$latest_posts = $conn->query("
    SELECT posts.*, categories.name AS category_name
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE posts.status = 'Published'
    ORDER BY posts.created_at DESC
    LIMIT 3
");

$page_title = "Farmer Dashboard";
include("../includes/header.php");
?>

<h1 class="page-title">🌾 Farmer Dashboard</h1>

<p class="page-subtitle">
    Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> — your agriculture hub
</p>

<!-- ================= STATS ================= -->
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-value"><?php echo $stats['articles']; ?></div>
        <div class="stat-label">Farming Articles</div>
    </div>

    <div class="stat-card orange">
        <div class="stat-value"><?php echo $stats['prices']; ?></div>
        <div class="stat-label">Market Prices</div>
    </div>

    <div class="stat-card blue">
        <div class="stat-value"><?php echo $stats['my_questions']; ?></div>
        <div class="stat-label">My Questions</div>
    </div>

    <div class="stat-card purple">
        <div class="stat-value"><?php echo $stats['favorites']; ?></div>
        <div class="stat-label">Saved Articles</div>
    </div>

</div>

<!-- ================= QUICK LINKS + NOTIFICATIONS ================= -->
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">

    <div class="card">
        <h3>Quick Links</h3>
        <ul class="quick-links">
            <li><a href="posts.php">📖 Browse Farming Tips</a></li>
            <li><a href="market_prices.php">💰 View Market Prices</a></li>
            <li><a href="questions.php">💬 Ask an Expert</a></li>
            <li><a href="favorites.php">⭐ My Favorites</a></li>
            <li><a href="profile.php">👤 My Profile</a></li>
        </ul>
    </div>

    <div class="card">
        <h3>Latest Announcements</h3>

        <?php if ($notifications->num_rows === 0): ?>
            <p style="color:#666;">No announcements yet.</p>
        <?php else: ?>
            <ul class="notif-list">
                <?php while ($n = $notifications->fetch_assoc()): ?>
                    <li>
                        <strong>⚠ <?php echo htmlspecialchars($n['title']); ?></strong><br>
                        <?php echo htmlspecialchars($n['message']); ?>
                        <div class="notif-date"><?php echo $n['created_at']; ?></div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>

</div>

<!-- ================= LATEST ARTICLES ================= -->
<div class="card" style="margin-top:20px;">
    <h3>Latest Articles</h3>

    <div class="post-grid">
        <?php while ($post = $latest_posts->fetch_assoc()): ?>
            <div class="post-card">

                <?php if (!empty($post['image'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="">
                <?php endif; ?>

                <div class="post-card-body">
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>

                    <p class="post-meta">
                        <?php echo htmlspecialchars($post['category_name'] ?? 'General'); ?>
                    </p>

                    <a href="post_view.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">
                        Read More
                    </a>
                </div>

            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- ================= UPCOMING FEATURES ================= -->
<div class="card" style="margin-top:20px;">
    <h3>🚀 Upcoming Features</h3>

    <p style="color:#666;">
        New improvements coming soon to improve your farming experience:
    </p>

    <ul class="notif-list">
        <li>🌦️ Weather Forecast System — real-time weather updates</li>
        <li>📱 SMS Alert System — farming tips via phone</li>
        <li>🐛 Pest & Disease Detection — AI crop diagnosis</li>
        <li>💰 Price Prediction System — smarter selling decisions</li>
        <li>📲 Mobile App Version — access anywhere anytime</li>
    </ul>
</div>

<?php include("../includes/footer.php"); ?>