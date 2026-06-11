<?php
session_start();

/* =======================
   REDIRECT LOGGED-IN USERS
======================= */
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    }

    if ($_SESSION['role'] === 'farmer') {
        header("Location: farmer/dashboard.php");
        exit();
    }
}

$page_title = "Home";

include("includes/header.php");
?>

<div class="hero">

    <h1>🌾 Smart Agriculture Information System</h1>

    <p>
        Empowering farmers with knowledge, market prices,
        and expert guidance for sustainable agriculture in Sierra Leone.
    </p>

    <a href="login.php" class="btn">Login</a>

    <a href="register.php" class="btn">
        Register as Farmer
    </a>

</div>

<div class="features">

    <div class="feature-card">
        <div class="icon">📖</div>

        <h3>Farming Articles</h3>

        <p>
            Access expert tips on crops,
            pests, soil health, and irrigation.
        </p>
    </div>

    <div class="feature-card">
        <div class="icon">💰</div>

        <h3>Market Prices</h3>

        <p>
            Track crop prices by location
            and make informed selling decisions.
        </p>
    </div>

    <div class="feature-card">
        <div class="icon">💬</div>

        <h3>Ask Experts</h3>

        <p>
            Submit questions and get answers
            from agricultural administrators.
        </p>
    </div>

    <div class="feature-card">
        <div class="icon">🌍</div>

        <h3>Digital Public Good</h3>

        <p>
            Open data platform supporting
            UN SDG 2 — Zero Hunger.
        </p>
    </div>

</div>

<?php include("includes/footer.php"); ?>