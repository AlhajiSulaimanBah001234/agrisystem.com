<?php
session_start();

include("config/db.php");
include("includes/functions.php");

/* Redirect if already logged in */
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] === "admin") {
        header("Location: admin/dashboard.php");
        exit();
    }

    if ($_SESSION['role'] === "farmer") {
        header("Location: farmer/dashboard.php");
        exit();
    }
}

$msg = "";
$error = "";

if (isset($_POST['register'])) {

    $name = escape($conn, $_POST['name'] ?? '');
    $email = escape($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {

        $error = "Please fill all fields.";

    } elseif ($password !== $confirm) {

        $error = "Passwords do not match.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } else {

        $check = $conn->query("
            SELECT id 
            FROM users 
            WHERE email='$email' 
            LIMIT 1
        ");

        if ($check && $check->num_rows > 0) {

            $error = "Email already registered.";

        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "
                INSERT INTO users (name, email, password, role)
                VALUES ('$name', '$email', '$hash', 'farmer')
            ";

            if ($conn->query($sql)) {

                $msg = "Registration successful! You can now login.";

            } else {

                $error = "Registration failed. Please try again.";

            }
        }
    }
}

$page_title = "Register";

include("includes/header.php");
?>

<div class="auth-page">

    <div class="auth-box">

        <h1>🌾 Register</h1>

        <p class="subtitle">Join as a farmer</p>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($msg); ?>
                <br><br>
                <a href="login.php">Login now</a>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="Your full name">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="your@email.com">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required minlength="6">
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required minlength="6">
            </div>

            <button type="submit" name="register" class="btn btn-primary" style="width:100%;">
                Register
            </button>

        </form>

        <p style="text-align:center; margin-top:16px;">
            Already have an account? <a href="login.php">Login</a>
        </p>

    </div>

</div>

<?php include("includes/footer.php"); ?>