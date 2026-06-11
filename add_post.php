<?php
session_start();
$base_path = "../";
include("../config/db.php");
include("../includes/functions.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$msg = "";
$error = "";

if (isset($_POST['add_post'])) {
    $title = escape($conn, $_POST['title']);
    $content = escape($conn, $_POST['content']);
    $category_id = (int) $_POST['category_id'];
    $status = escape($conn, $_POST['status']);
    $created_by = $_SESSION['user_id'];
    $image = "";

    if (!empty($_FILES['image']['name'])) {
        $uploaded = uploadImage($_FILES['image'], 'post');
        if ($uploaded === false) {
            $error = "Invalid image format. Use JPG, PNG, GIF, or WebP.";
        } else {
            $image = $uploaded;
        }
    }

    if (!$error) {
        $sql = "INSERT INTO posts (title, content, image, category_id, created_by, status)
                VALUES ('$title', '$content', '$image', $category_id, $created_by, '$status')";
        $conn->query($sql);
        logAction($conn, $_SESSION['user_id'], "Created post: $title ($status)");
        $msg = "Post created successfully!";
    }
}

$page_title = "Add Post";
include("../includes/header.php");
?>

<h1 class="page-title">📝 Add Farming Post</h1>
<p class="page-subtitle">Publish tips and guides for farmers</p>

<?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

<div class="card">
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Post Title</label>
            <input type="text" name="title" placeholder="Post Title" required>
        </div>
        <div class="form-group">
            <label>Content</label>
            <textarea name="content" placeholder="Write your farming guide..." required></textarea>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php while ($row = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="Published">Published</option>
                <option value="Draft">Draft</option>
            </select>
        </div>
        <div class="form-group">
            <label>Image (optional)</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" name="add_post" class="btn btn-primary">Publish Post</button>
        <a href="posts.php" class="btn btn-secondary">Manage Posts</a>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
