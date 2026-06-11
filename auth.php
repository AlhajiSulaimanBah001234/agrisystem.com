<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   LOGIN CHECK (SECURE)
========================= */
function requireLogin() {

    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: /agri_system/login.php");
        exit();
    }

    /* OPTIONAL: SESSION TIMEOUT (30 minutes) */
    if (isset($_SESSION['last_activity'])) {

        if (time() - $_SESSION['last_activity'] > 1800) {
            session_unset();
            session_destroy();
            header("Location: /agri_system/login.php");
            exit();
        }
    }

    $_SESSION['last_activity'] = time();
}

/* =========================
   ROLE CHECK (SECURE)
========================= */
function requireRole($role) {

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: /agri_system/login.php");
        exit();
    }
}
?>