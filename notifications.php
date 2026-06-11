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

if (isset($_POST['send'])) {
    $title = escape($conn, $_POST['title']);
    $message = escape($conn, $_POST['message']);
    $conn->query("INSERT INTO notifications (title, message) VALUES ('$title', '$message')");
    logAction($conn, $_SESSION['user_id'], "Sent notification: $title");
    $msg = "Notification sent to all farmers!";
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM notifications WHERE id = $id");
    $msg = "Notification removed.";
}

$notifications = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");

$page_title = "Notifications";
include("../includes/header.php");
?>

<h1 class="page-title">🔔 Notifications</h1>
<p class="page-subtitle">Broadcast announcements to all farmers</p>

<?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>

<div class="card">
    <h3>Send New Notification</h3>
    <form method="POST">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" placeholder="e.g. New maize price available" required>
        </div>
        <div class="form-group">
            <label>Message</label>
            <textarea name="message" placeholder="Notification details..." required></textarea>
        </div>
        <button type="submit" name="send" class="btn btn-primary">Send to Farmers</button>
    </form>
</div>

<div class="card">
    <h3>Sent Notifications</h3>
    <ul class="notif-list">
        <?php while ($n = $notifications->fetch_assoc()): ?>
            <li>
                <strong>⚠ <?php echo htmlspecialchars($n['title']); ?></strong><br>
                <?php echo htmlspecialchars($n['message']); ?>
                <div class="notif-date">
                    <?php echo $n['created_at']; ?>
                    <a href="?delete=<?php echo $n['id']; ?>" style="margin-left:12px; color:#c62828;"
                       onclick="return confirm('Delete?')">Remove</a>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<?php include("../includes/footer.php"); ?>
