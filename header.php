<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle ?? 'eShop'; ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<header>
    <div class="logo"><a href="index.php"><img src="assets/images/logo.jpg" alt="Logo"></a></div>
    <nav>
        <a href="product_catalog.php">Products</a>
        <a href="cart.php">Cart</a>
        <a href="checkout.php">Checkout</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="account.php">My Account</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Sign Up</a>
        <?php endif; ?>
    </nav>
</header>
