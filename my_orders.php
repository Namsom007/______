<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute(['uid' => $user_id]);
$orders = $stmt->fetchAll();
?>

<div class="container my-5">
    <h2 class="text-success fw-bold mb-4">My Orders</h2>
    
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">Your order has been placed successfully!</div>
    <?php endif; ?>

    <?php if(empty($orders)): ?>
        <div class="alert alert-info">You have no orders yet. <a href="products.php" class="alert-link">Start shopping</a></div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></td>
                                <td>฿<?= number_format($order['total_price'], 2) ?></td>
                                <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                <td>
                                    <?php
                                    $badge = 'bg-secondary';
                                    if ($order['status'] == 'Pending') $badge = 'bg-warning text-dark';
                                    if ($order['status'] == 'Confirmed') $badge = 'bg-info text-dark';
                                    if ($order['status'] == 'Shipping') $badge = 'bg-primary';
                                    if ($order['status'] == 'Completed') $badge = 'bg-success';
                                    if ($order['status'] == 'Cancelled') $badge = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= $order['status'] ?></span>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-success">View Details</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
