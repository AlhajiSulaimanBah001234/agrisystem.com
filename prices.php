<?php
session_start();
$base_path = "../";
include("../config/db.php");
include("../includes/functions.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

$msg = "";

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM market_prices WHERE id = $id");
    logAction($conn, $_SESSION['user_id'], "Deleted market price ID: $id");
    $msg = "Price entry deleted.";
}

$prices = $conn->query("SELECT * FROM market_prices ORDER BY updated_at DESC");

$page_title = "Manage Prices";
include("../includes/header.php");
?>

<h1 class="page-title">📊 Manage Market Prices</h1>

<?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>

<p style="margin-bottom:16px;"><a href="add_price.php" class="btn btn-primary">+ Add Price</a></p>

<table class="data-table">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Unit</th>
        <th>Trend</th>
        <th>Location</th>
        <th>Updated</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $prices->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
        <td><?php echo htmlspecialchars($row['price']); ?></td>
        <td><?php echo htmlspecialchars($row['unit']); ?></td>
        <td><?php echo trendBadge($row['trend']); ?></td>
        <td><?php echo htmlspecialchars($row['location']); ?></td>
        <td><?php echo $row['updated_at']; ?></td>
        <td>
            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this price entry?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include("../includes/footer.php"); ?>
