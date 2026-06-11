<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = isset($base_path) ? $base_path : '';

$role = $_SESSION['role'] ?? '';
$name = $_SESSION['name'] ?? '';

$css_path = $base . 'assets/css/style.css';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?>
        Smart Agriculture System
    </title>

    <link rel="stylesheet" href="<?php echo $css_path; ?>">
</head>

<body>

<?php if (!empty($_SESSION['user_id'])): ?>

<header class="app-header">

    <div class="header-inner">

        <a href="<?php echo $base . ($role === 'admin' ? 'admin/dashboard.php' : 'farmer/dashboard.php'); ?>" class="logo">
            🌾 Smart Agriculture
            <span>
                <?php echo $role === 'admin' ? 'Admin Portal' : 'Farmer Portal'; ?>
            </span>
        </a>

        <nav>
            <ul class="nav-links">

                <?php if ($role === 'admin'): ?>

                    <li><a href="<?php echo $base; ?>admin/dashboard.php">Dashboard</a></li>
                    <li><a href="<?php echo $base; ?>admin/posts.php">Posts</a></li>
                    <li><a href="<?php echo $base; ?>admin/users.php">Users</a></li>
                    <li><a href="<?php echo $base; ?>admin/prices.php">Prices</a></li>
                    <li><a href="<?php echo $base; ?>admin/questions.php">Questions</a></li>
                    <li><a href="<?php echo $base; ?>admin/reports.php">Reports</a></li>
                    <li><a href="<?php echo $base; ?>admin/notifications.php">Notifications</a></li>

                <?php else: ?>

                    <li><a href="<?php echo $base; ?>farmer/dashboard.php">Dashboard</a></li>
                    <li><a href="<?php echo $base; ?>farmer/posts.php">Articles</a></li>
                    <li><a href="<?php echo $base; ?>farmer/market_prices.php">Market Prices</a></li>
                    <li><a href="<?php echo $base; ?>farmer/questions.php">Ask Expert</a></li>
                    <li><a href="<?php echo $base; ?>farmer/favorites.php">Favorites</a></li>
                    <li><a href="<?php echo $base; ?>farmer/profile.php">Profile</a></li>

                <?php endif; ?>

                <li>
                    <a href="<?php echo $base; ?>logout.php">
                        Logout (<?php echo htmlspecialchars($name); ?>)
                    </a>
                </li>

            </ul>
        </nav>

    </div>

</header>

<?php endif; ?>

<main class="main-content">