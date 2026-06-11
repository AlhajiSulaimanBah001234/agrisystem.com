<?php
session_start();
$base_path = "../";
include("../config/db.php");
include("../includes/functions.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "farmer") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = (int) ($_GET['id'] ?? 0);

$post = $conn->query("
    SELECT posts.*, categories.name AS category_name
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE posts.id = $id AND posts.status = 'Published'
")->fetch_assoc();

if (!$post) {
    header("Location: posts.php");
    exit();
}

$msg = "";
if (isset($_GET['favorite'])) {
    $check = $conn->query("SELECT id FROM favorites WHERE user_id=$user_id AND post_id=$id");
    if ($check->num_rows > 0) {
        $conn->query("DELETE FROM favorites WHERE user_id=$user_id AND post_id=$id");
        $msg = "Removed from favorites.";
    } else {
        $conn->query("INSERT INTO favorites (user_id, post_id) VALUES ($user_id, $id)");
        $msg = "Added to favorites!";
    }
}

$is_fav = $conn->query("SELECT id FROM favorites WHERE user_id=$user_id AND post_id=$id")->num_rows > 0;

$page_title = $post['title'];
include("../includes/header.php");
?>

<?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>

<div class="card">
    <p class="post-meta">
        <b><?php echo htmlspecialchars($post['category_name'] ?? 'General'); ?></b>
        &middot; <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
    </p>
    <h1 class="page-title"><?php echo htmlspecialchars($post['title']); ?></h1>

    <?php if ($post['image']): ?>
        <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>"
             style="max-width:100%; border-radius:12px; margin:16px 0;" alt="">
    <?php endif; ?>

    <div style="font-size:1.05rem; line-height:1.8;">
        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
    </div>

    <div class="actions" style="margin-top:24px;">
        <a href="?id=<?php echo $id; ?>&favorite=1" class="btn btn-secondary">
            <?php echo $is_fav ? '⭐ Remove from Favorites' : '☆ Save to Favorites'; ?>
        </a>
        <a href="posts.php" class="btn btn-secondary">← Back to Articles</a>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
