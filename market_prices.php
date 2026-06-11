<?php
session_start();
$base_path = "../";
include("../config/db.php");
include("../includes/functions.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "farmer") {
    header("Location: ../login.php");
    exit();
}

$search = escape($conn, $_GET['search'] ?? '');
$location = escape($conn, $_GET['location'] ?? '');

$where = ["1=1"];
if ($search) $where[] = "product_name LIKE '%$search%'";
if ($location) $where[] = "location LIKE '%$location%'";
$where_sql = implode(' AND ', $where);

$prices = $conn->query("SELECT * FROM market_prices WHERE $where_sql ORDER BY product_name, location");

$locations = $conn->query("SELECT DISTINCT location FROM market_prices ORDER BY location");

$compare = [];
if (isset($_GET['compare']) && !empty($_GET['products'])) {
    foreach ($_GET['products'] as $p) {
        $p = escape($conn, $p);
        $r = $conn->query("SELECT * FROM market_prices WHERE product_name='$p' ORDER BY location");
        while ($row = $r->fetch_assoc()) $compare[] = $row;
    }
}

$products_list = $conn->query("SELECT DISTINCT product_name FROM market_prices ORDER BY product_name");

$page_title = "Market Prices";
include("../includes/header.php");
?>

<h1 class="page-title">💰 Market Price Tracker</h1>
<p class="page-subtitle">Compare crop prices across districts</p>

<form method="GET" class="filter-bar card" style="padding:16px;">
    <div class="form-group">
        <label>Search Product</label>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="e.g. Rice">
    </div>
    <div class="form-group">
        <label>Filter by District</label>
        <select name="location">
            <option value="">All Locations</option>
            <?php while ($loc = $locations->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($loc['location']); ?>"
                    <?php echo $location === $loc['location'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($loc['location']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="market_prices.php" class="btn btn-secondary">Clear</a>
</form>

<table class="data-table">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Unit</th>
        <th>Trend</th>
        <th>Location</th>
        <th>Updated</th>
    </tr>
    <?php while ($row = $prices->fetch_assoc()): ?>
    <tr>
        <td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
        <td><?php echo htmlspecialchars($row['price']); ?></td>
        <td><?php echo htmlspecialchars($row['unit']); ?></td>
        <td><?php echo trendBadge($row['trend']); ?></td>
        <td><?php echo htmlspecialchars($row['location']); ?></td>
        <td><?php echo $row['updated_at']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<div class="card" style="margin-top:24px;">
    <h3>Compare Prices by Product</h3>
    <form method="GET">
        <input type="hidden" name="compare" value="1">
        <div class="form-group">
            <label>Select products to compare across locations</label>
            <select name="products[]" multiple style="min-height:100px;">
                <?php while ($p = $products_list->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($p['product_name']); ?>">
                        <?php echo htmlspecialchars($p['product_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <small style="color:#666;">Hold Ctrl to select multiple</small>
        </div>
        <button type="submit" class="btn btn-primary">Compare</button>
    </form>

    <?php if (!empty($compare)): ?>
        <table class="data-table" style="margin-top:16px;">
            <tr><th>Product</th><th>Price</th><th>Unit</th><th>Trend</th><th>Location</th></tr>
            <?php foreach ($compare as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($c['price']); ?></td>
                    <td><?php echo htmlspecialchars($c['unit']); ?></td>
                    <td><?php echo trendBadge($c['trend']); ?></td>
                    <td><?php echo htmlspecialchars($c['location']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<a href="dashboard.php" class="btn btn-secondary" style="margin-top:16px;">Back to Dashboard</a>

<?php include("../includes/footer.php"); ?>
