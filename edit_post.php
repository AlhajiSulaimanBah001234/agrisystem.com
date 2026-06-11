<?php
session_start();
$base_path = "../";
include("../config/db.php");
include("../includes/functions.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

$id = (int) ($_GET['id'] ?? 0);
$post = $conn->query("SELECT * FROM posts WHERE id = $id")->fetch_assoc();

if (!$post) {
    header("Location: posts.php");
    exit();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$msg = "";
$error = "";

if (isset($_POST['update'])) {
    $title = escape($conn, $_POST['title']);
    $content = escape($conn, $_POST['content']);
    $category_id = (int) $_POST['category_id'];
    $status = escape($conn, $_POST['status']);
    $image = $post['image'];

    if (!empty($_FILES['image']['name'])) {
        $uploaded = uploadImage($_FILES['image'], 'post');
        if ($uploaded === false) {
            $error = "Invalid image format.";
        } else {
            $image = $uploaded;
        }
    }

    if (!$error) {
        $conn->query("UPDATE posts SET title='$title', content='$content', image='$image',
                      category_id=$category_id, status='$status' WHERE id=$id");
        logAction($conn, $_SESSION['user_id'], "Updated post: $title");
        $msg = "Post updated successfully!";
        $post = $conn->query("SELECT * FROM posts WHERE id = $id")->fetch_assoc();
    }
}

$page_title = "Edit Post";
include("../includes/header.php");
?>

<h1 class="page-title">✏️ Edit Post</h1>

<?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

<div class="card">
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        <div class="form-group">
            <label>Content</label>
            <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" required>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $post['category_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="Published" <?php echo $post['status'] === 'Published' ? 'selected' : ''; ?>>Published</option>
                <option value="Draft" <?php echo $post['status'] === 'Draft' ? 'selected' : ''; ?>>Draft</option>
            </select>
        </div>
        <?php if ($post['image']): ?>
            <p><img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" width="150" style="border-radius:8px;"></p>
        <?php endif; ?>
        <div class="form-group">
            <label>Replace Image (optional)</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
        <a href="posts.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
