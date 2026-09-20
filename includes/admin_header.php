<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

// Check Admin Auth
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GreenLeaf</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #2E7D32; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 12px 20px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: #fff; }
        .main-content { padding: 20px; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar text-white" style="width: 250px;">
            <div class="p-3 mb-3 border-bottom border-light border-opacity-25">
                <h4 class="m-0"><i class="fa-solid fa-leaf"></i> GreenLeaf</h4>
                <small class="text-white-50">Admin Panel</small>
            </div>
            
            <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
            <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>"><i class="fa-solid fa-gauge fa-fw me-2"></i> Dashboard</a>
            <a href="products.php" class="<?= in_array($current_page, ['products.php', 'add_product.php', 'edit_product.php']) ? 'active' : '' ?>"><i class="fa-solid fa-box fa-fw me-2"></i> Products</a>
            <a href="categories.php" class="<?= $current_page == 'categories.php' ? 'active' : '' ?>"><i class="fa-solid fa-tags fa-fw me-2"></i> Categories</a>
            <a href="orders.php" class="<?= in_array($current_page, ['orders.php', 'order_detail.php']) ? 'active' : '' ?>"><i class="fa-solid fa-shopping-cart fa-fw me-2"></i> Orders</a>
            <a href="../index.php" target="_blank"><i class="fa-solid fa-store fa-fw me-2"></i> View Store</a>
            <a href="../logout.php" class="text-danger mt-5"><i class="fa-solid fa-sign-out-alt fa-fw me-2"></i> Logout</a>
        </div>
        
        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <nav class="navbar navbar-light bg-white rounded shadow-sm mb-4 px-3">
                <span class="navbar-brand mb-0 h1">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
            </nav>
