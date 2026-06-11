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
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
$msg = "";
$error = "";

if (isset($_POST['update_profile'])) {
    $name = escape($conn, $_POST['name']);
    $email = escape($conn, $_POST['email']);
    $profile_image = $user['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $uploaded = uploadImage($_FILES['profile_image'], 'profile');
        if ($uploaded === false) {
            $error = "Invalid image format.";
        } else {
            $profile_image = $uploaded;
        }
    }

    if (!$error) {
        $check = $conn->query("SELECT id FROM users WHERE email='$email' AND id != $user_id");
        if ($check->num_rows > 0) {
            $error = "Email already in use.";
        } else {
            $conn->query("UPDATE users SET name='$name', email='$email', profile_image='$profile_image' WHERE id=$user_id");
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $msg = "Profile updated successfully!";
            $user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
        }
    }
}

if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif ($new_pass !== $confirm) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_pass) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hash' WHERE id=$user_id");
        $msg = "Password changed successfully!";
    }
}

$page_title = "My Profile";
include("../includes/header.php");
?>

<h1 class="page-title">👤 My Profile</h1>

<?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
    <div class="card">
        <h3>Profile Information</h3>
        <div style="text-align:center; margin-bottom:20px;">
            <?php if ($user['profile_image']): ?>
                <img src="../uploads/<?php echo htmlspecialchars($user['profile_image']); ?>" class="profile-avatar" alt="">
            <?php else: ?>
                <div class="profile-avatar" style="background:#e8f5e9; display:inline-flex; align-items:center; justify-content:center; font-size:2rem;">👨‍🌾</div>
            <?php endif; ?>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Profile Picture</label>
                <input type="file" name="profile_image" accept="image/*">
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary">Save Profile</button>
        </form>
    </div>

    <div class="card">
        <h3>Change Password</h3>
        <form method="POST">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required minlength="6">
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required minlength="6">
            </div>
            <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
        </form>

        <div style="margin-top:24px; padding-top:16px; border-top:1px solid #e8f0e8;">
            <p><strong>Account Status:</strong> <?php echo statusBadge($user['status']); ?></p>
            <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
