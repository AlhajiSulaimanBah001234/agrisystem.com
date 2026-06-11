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
$search = escape($conn, $_GET['search'] ?? '');
$category = (int) ($_GET['category'] ?? 0);

$where = ["posts.status = 'Published'"];
if ($search) $where[] = "(posts.title LIKE '%$search%' OR posts.content LIKE '%$search%')";
if ($category) $where[] = "posts.category_id = $category";
$where_sql = implode(' AND ', $where);

$posts = $conn->query("
    SELECT posts.*, categories.name AS category_name,
           (SELECT COUNT(*) FROM favorites WHERE favorites.post_id = posts.id AND favorites.user_id = $user_id) AS is_fav
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE $where_sql
    ORDER BY posts.created_at DESC
");

$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$page_title = "Farming Articles";
include("../includes/header.php");
?>

<h1 class="page-title">📖 Farming Tips & Articles</h1>
<p class="page-subtitle">Search, filter, and save articles for later</p>

<form method="GET" class="filter-bar card" style="padding:16px;">
    <div class="form-group">
        <label>Search</label>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search articles...">
    </div>
    <div class="form-group">
        <label>Category</label>
        <select name="category">
            <option value="">All Categories</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="posts.php" class="btn btn-secondary">Clear</a>
</form>

<?php if ($posts->num_rows === 0): ?>
    <div class="alert alert-info">No articles found. Try different search terms.</div>
<?php else: ?>
    <div class="post-grid">
        <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="post-card">
                <?php if ($post['image']): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="">
                <?php endif; ?>
                <div class="post-card-body">
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="post-meta">
                        <b><?php echo htmlspecialchars($post['category_name'] ?? 'General'); ?></b>
                        &middot; <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                    </p>
                    <p><?php echo htmlspecialchars(substr($post['content'], 0, 120)); ?>...</p>
                    <div class="actions" style="margin-top:12px;">
                        <a href="post_view.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">Read Full Article</a>
                        <?php if ($post['is_fav']): ?>
                            <span class="badge badge-published">⭐ Saved</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php include("../includes/footer.php"); ?>
