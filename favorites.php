<?php
session_start();
$base_path = "../";
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "farmer") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['remove'])) {
    $post_id = (int) $_GET['remove'];
    $conn->query("DELETE FROM favorites WHERE user_id=$user_id AND post_id=$post_id");
}

$favorites = $conn->query("
    SELECT posts.*, categories.name AS category_name, favorites.created_at AS saved_at
    FROM favorites
    JOIN posts ON favorites.post_id = posts.id
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE favorites.user_id = $user_id
    ORDER BY favorites.created_at DESC
");

$page_title = "My Favorites";
include("../includes/header.php");
?>

<h1 class="page-title">⭐ My Favorite Articles</h1>
<p class="page-subtitle">Articles you saved for quick reference</p>

<?php if ($favorites->num_rows === 0): ?>
    <div class="alert alert-info">
        No saved articles yet. <a href="posts.php">Browse farming tips</a> and save your favorites.
    </div>
<?php else: ?>
    <div class="post-grid">
        <?php while ($post = $favorites->fetch_assoc()): ?>
            <div class="post-card">
                <?php if ($post['image']): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="">
                <?php endif; ?>
                <div class="post-card-body">
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="post-meta"><?php echo htmlspecialchars($post['category_name'] ?? 'General'); ?></p>
                    <div class="actions">
                        <a href="post_view.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">Read</a>
                        <a href="?remove=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Remove from favorites?')">Remove</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php include("../includes/footer.php"); ?>
