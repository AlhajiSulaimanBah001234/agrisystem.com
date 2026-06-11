<?php
session_start();
$base_path = "../";
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

$farmer_count = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='farmer'")->fetch_assoc()['c'];
$active_farmers = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='farmer' AND status='Active'")->fetch_assoc()['c'];

$popular_categories = $conn->query("
    SELECT categories.name, COUNT(posts.id) AS post_count
    FROM categories
    LEFT JOIN posts ON categories.id = posts.category_id
    GROUP BY categories.id
    ORDER BY post_count DESC
");

$recent_prices = $conn->query("SELECT * FROM market_prices ORDER BY updated_at DESC LIMIT 10");

$question_summary = $conn->query("
    SELECT status, COUNT(*) AS count FROM questions GROUP BY status
");

$page_title = "Reports";
include("../includes/header.php");
?>

<h1 class="page-title">📈 Reports & Analytics</h1>
<p class="page-subtitle">Platform insights for decision-making and DPG reporting</p>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?php echo $farmer_count; ?></div>
        <div class="stat-label">Registered Farmers</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-value"><?php echo $active_farmers; ?></div>
        <div class="stat-label">Active Farmers</div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
    <div class="card">
        <h3>Popular Categories</h3>
        <table class="data-table">
            <tr><th>Category</th><th>Articles</th></tr>
            <?php while ($cat = $popular_categories->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                    <td><?php echo $cat['post_count']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="card">
        <h3>Question Summary</h3>
        <table class="data-table">
            <tr><th>Status</th><th>Count</th></tr>
            <?php while ($qs = $question_summary->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $qs['status']; ?></td>
                    <td><?php echo $qs['count']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<div class="card" style="margin-top:20px;">
    <h3>Recent Price Updates</h3>
    <table class="data-table">
        <tr><th>Product</th><th>Price</th><th>Location</th><th>Updated</th></tr>
        <?php while ($p = $recent_prices->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['product_name']); ?></td>
                <td><?php echo htmlspecialchars($p['price']); ?> / <?php echo htmlspecialchars($p['unit']); ?></td>
                <td><?php echo htmlspecialchars($p['location']); ?></td>
                <td><?php echo $p['updated_at']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<p style="margin-top:16px;">
    <a href="export.php" class="btn btn-primary">Export Data (CSV / JSON)</a>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</p>

<?php include("../includes/footer.php"); ?>
