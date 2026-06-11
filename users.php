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
$error = "";

/* ================= DELETE USER ================= */
if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    if ($id === (int) $_SESSION['user_id']) {
        $error = "You cannot delete your own account.";
    } else {
        $conn->query("DELETE FROM users WHERE id = $id");
        logAction($conn, $_SESSION['user_id'], "Deleted user ID: $id");
        $msg = "User deleted.";
    }
}

/* ================= UPDATE USER ================= */
if (isset($_POST['update_user'])) {

    $id = (int) $_POST['user_id'];
    $name = escape($conn, $_POST['name']);
    $email = escape($conn, $_POST['email']);
    $role = escape($conn, $_POST['role']);
    $status = escape($conn, $_POST['status']);

    $conn->query("
        UPDATE users
        SET name='$name',
            email='$email',
            role='$role',
            status='$status'
        WHERE id=$id
    ");

    logAction($conn, $_SESSION['user_id'], "Updated user: $name");
    $msg = "User updated successfully.";
}

/* ================= GET USERS ================= */
$users = $conn->query("
    SELECT *
    FROM users
    ORDER BY created_at DESC
");

$edit_id = (int) ($_GET['edit'] ?? 0);
$edit_user = null;

if ($edit_id) {
    $edit_user = $conn->query("
        SELECT * FROM users WHERE id = $edit_id
    ")->fetch_assoc();
}

$page_title = "Manage Users";
include("../includes/header.php");
?>

<h1 class="page-title">👥 Manage Users</h1>

<p class="page-subtitle">
    View, edit roles, suspend, and track user activity
</p>

<?php if ($msg): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- ================= EDIT FORM ================= -->
<?php if ($edit_user): ?>

<div class="card">

<h3>Edit User</h3>

<form method="POST">

<input type="hidden" name="user_id"
value="<?php echo $edit_user['id']; ?>">

<div class="form-group">
<label>Name</label>
<input type="text" name="name"
value="<?php echo htmlspecialchars($edit_user['name']); ?>"
required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email"
value="<?php echo htmlspecialchars($edit_user['email']); ?>"
required>
</div>

<div class="form-group">
<label>Role</label>

<select name="role">

<option value="farmer"
<?php echo $edit_user['role']==='farmer'?'selected':''; ?>>
Farmer
</option>

<option value="admin"
<?php echo $edit_user['role']==='admin'?'selected':''; ?>>
Admin
</option>

</select>

</div>

<div class="form-group">
<label>Status</label>

<select name="status">

<option value="Active"
<?php echo $edit_user['status']==='Inactive'?'selected':''; ?>>
Active
</option>

<option value="Suspended"
<?php echo $edit_user['status']==='Suspended'?'selected':''; ?>>
Suspended
</option>

<option value="Offline"
<?php echo $edit_user['status']==='Offline'?'selected':''; ?>>
Offline
</option>

</select>

</div>

<button type="submit"
name="update_user"
class="btn btn-primary">

Save

</button>

<a href="users.php"
class="btn btn-secondary">

Cancel

</a>

</form>

</div>

<?php endif; ?>

<!-- ================= USERS TABLE ================= -->
<table class="data-table">

<tr>
<th>Profile</th>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Status</th>
<th>Last Seen</th>
<th>Joined</th>
<th>Actions</th>
</tr>

<?php while ($user = $users->fetch_assoc()): ?>

<tr>

<td>
<?php if (!empty($user['profile_image'])): ?>
<img src="../uploads/<?php echo htmlspecialchars($user['profile_image']); ?>"
style="width:40px;height:40px;border-radius:50%;">
<?php else: ?>
👨‍🌾
<?php endif; ?>
</td>

<td>
<?php echo htmlspecialchars($user['name']); ?>
</td>

<td>
<?php echo htmlspecialchars($user['email']); ?>
</td>

<td>
<?php echo ucfirst($user['role']); ?>
</td>

<td>
<?php echo statusBadge($user['status']); ?>
</td>

<td>
<?php
if (!empty($user['last_seen'])) {
    echo date("d M Y H:i", strtotime($user['last_seen']));
} else {
    echo "Never";
}
?>
</td>

<td>
<?php echo $user['created_at']; ?>
</td>

<td class="actions">

<a href="?edit=<?php echo $user['id']; ?>"
class="btn btn-secondary btn-sm">

Edit

</a>

<?php if ($user['id'] != $_SESSION['user_id']): ?>

<a href="?delete=<?php echo $user['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete user?')">

Delete

</a>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</table>

<?php include("../includes/footer.php"); ?>