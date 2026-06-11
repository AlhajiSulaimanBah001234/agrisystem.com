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

if (isset($_POST['add'])) {
    $product_name = escape($conn, $_POST['product_name']);
    $price = escape($conn, $_POST['price']);
    $unit = escape($conn, $_POST['unit']);
    $trend = escape($conn, $_POST['trend']);
    $location = escape($conn, $_POST['location']);

    $sql = "INSERT INTO market_prices (product_name, price, unit, trend, location)
            VALUES ('$product_name', '$price', '$unit', '$trend', '$location')";
    $conn->query($sql);
    logAction($conn, $_SESSION['user_id'], "Added market price: $product_name at $location");

    $notif_title = "New price: $product_name";
    $notif_msg = "$product_name is now $price per $unit in $location (Trend: $trend)";
    $conn->query("INSERT INTO notifications (title, message) VALUES ('$notif_title', '$notif_msg')");

    $msg = "Market price added successfully!";
}

$page_title = "Add Market Price";
include("../includes/header.php");
?>

<h1 class="page-title">💰 Add Market Price</h1>
<p class="page-subtitle">Update crop prices for farmers by location</p>

<?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>

<div class="card">
    <form method="POST">
        <div class="form-group">
            <label>Crop / Product Name</label>
            <input type="text" name="product_name" placeholder="e.g. Rice" required>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="text" name="price" placeholder="e.g. 300" required>
        </div>
        <div class="form-group">
            <label>Unit</label>
            <select name="unit">
                <option value="Kg">Kg</option>
                <option value="Bag">Bag</option>
                <option value="Bunch">Bunch</option>
                <option value="Crate">Crate</option>
                <option value="Le">Le (flat)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Price Trend</label>
            <select name="trend">
                <option value="Stable">Stable</option>
                <option value="Increasing">Increasing</option>
                <option value="Decreasing">Decreasing</option>
            </select>
        </div>
        <div class="form-group">
            <label>Location / District</label>
            <input type="text" name="location" placeholder="e.g. Freetown, Bo, Kenema" required>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Save Price</button>
        <a href="prices.php" class="btn btn-secondary">Manage Prices</a>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
