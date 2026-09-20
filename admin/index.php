<?php
require_once '../includes/admin_header.php';

// Fetch Statistics
$stmt = $conn->query("SELECT COUNT(*) FROM products");
$total_products = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
$total_users = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM orders");
$total_orders = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'");
$pending_orders = $stmt->fetchColumn();

$stmt = $conn->query("SELECT SUM(total_price) FROM orders WHERE status = 'Completed'");
$total_sales = $stmt->fetchColumn() ?: 0;
?>

<div class="container-fluid">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card text-white bg-primary h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="fs-1 me-3"><i class="fa-solid fa-box"></i></div>
                    <div>
                        <h6 class="card-title mb-0">Total Products</h6>
                        <h3 class="mb-0 fw-bold"><?= $total_products ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card text-white bg-info h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="fs-1 me-3"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <h6 class="card-title mb-0">Customers</h6>
                        <h3 class="mb-0 fw-bold"><?= $total_users ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card text-dark bg-warning h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="fs-1 me-3"><i class="fa-solid fa-shopping-cart"></i></div>
                    <div>
                        <h6 class="card-title mb-0">Orders / Pending</h6>
                        <h3 class="mb-0 fw-bold"><?= $total_orders ?> / <?= $pending_orders ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card text-white bg-success h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="fs-1 me-3"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div>
                        <h6 class="card-title mb-0">Total Sales</h6>
                        <h3 class="mb-0 fw-bold">฿<?= number_format($total_sales, 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
