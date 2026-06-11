<?php
session_start();
include("config/db.php");

/* =========================
   UPDATE LAST SEEN SAFELY
========================= */
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {

    $id = (int) $_SESSION['user_id'];

    /* ONLY update if column exists (prevents crash) */
    $check = $conn->query("SHOW COLUMNS FROM users LIKE 'last_seen'");

    if ($check && $check->num_rows > 0) {

        $stmt = $conn->prepare("
            UPDATE users 
            SET last_seen = NOW() 
            WHERE id = ?
        ");

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
}

/* =========================
   CLEAR SESSION
========================= */
$_SESSION = [];

/* destroy session cookie safely */
if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

/* =========================
   REDIRECT
========================= */
header("Location: login.php");
exit();
?>