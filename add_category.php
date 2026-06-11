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
    $name = escape($conn, $_POST['name']);
    $conn->query("INSERT INTO categories (name) VALUES ('$name')");
    logAction($conn, $_SESSION['user_id'], "Added category: $name");
    $msg = "Category added successfully!";
}

$page_title = "Add Category";
include("../includes/header.php");
?>

<h1 class="page-title">➕ Add Category</h1>
<p class="page-subtitle">Organize farming articles by topic</p>

<?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>

<div class="card">
    <form method="POST">
        <div class="form-group">
            <label>Category Name</label>
            <input type="text" name="name" placeholder="e.g. Pest Control" required>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Add Category</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
