<?php
session_start();

include("config/db.php");
include("includes/functions.php");

/* =======================
   ALREADY LOGGED IN
======================= */
if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) {

    if ($_SESSION['role'] === "admin") {
        header("Location: admin/dashboard.php");
        exit();
    }

    if ($_SESSION['role'] === "farmer") {
        header("Location: farmer/dashboard.php");
        exit();
    }
}

$error = "";

/* =======================
   LOGIN PROCESS
======================= */
if (isset($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === "" || $password === "") {

        $error = "Please enter email and password.";

    } else {

        $stmt = $conn->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {

            $user = $result->fetch_assoc();

            /* =======================
               CHECK ACCOUNT STATUS
            ======================= */
            if (strtolower($user['status']) === "suspended") {

                $error = "Your account has been suspended.";

            } elseif (password_verify($password, $user['password'])) {

                /* =======================
                   CREATE SECURE SESSION
                ======================= */
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = $user['role'];
                $_SESSION['last_activity'] = time();

                /* =======================
                   UPDATE LAST SEEN
                ======================= */
                $update = $conn->prepare("
                    UPDATE users
                    SET last_seen = NOW()
                    WHERE id = ?
                ");

                $update->bind_param("i", $user['id']);
                $update->execute();

                /* =======================
                   REDIRECT BY ROLE
                ======================= */
                if ($user['role'] === "admin") {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: farmer/dashboard.php");
                }

                exit();

            } else {

                $error = "Invalid email or password.";
            }

        } else {

            $error = "Invalid email or password.";
        }
    }
}

$page_title = "Login";
include("includes/header.php");
?>

<div class="auth-page">

    <div class="auth-box">

        <h1>🌾 Login</h1>

        <p class="subtitle">Access your agriculture portal</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" name="login" class="btn btn-primary" style="width:100%;">
                Login
            </button>

        </form>

        <p style="text-align:center; margin-top:15px;">
            No account? <a href="register.php">Register</a>
        </p>

    </div>

</div>

<?php include("includes/footer.php"); ?>