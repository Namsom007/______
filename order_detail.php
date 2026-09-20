<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my_orders.php");
    exit();
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :oid AND user_id = :uid");
$stmt->execute(['oid' => $order_id, 'uid' => $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "<div class='container my-5'><h2>Order not found or access denied.</h2></div>";
    require_once 'includes/footer.php';
    exit();
}

$stmt_items = $conn->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :oid");
$stmt_items->execute(['oid' => $order_id]);
$items = $stmt_items->fetchAll();
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success fw-bold m-0">Order #<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></h2>
        <a href="my_orders.php" class="btn btn-outline-secondary">Back to Orders</a>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th class="pe-4 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($items as $item): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= $item['image'] ? (file_exists('uploads/products/'.$item['image']) ? 'uploads/products/'.$item['image'] : 'https://placehold.co/100x100/F7F9F4/2E7D32?text=Plant') : 'https://placehold.co/100x100/F7F9F4/2E7D32?text=No+Image' ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <span><?= htmlspecialchars($item['name']) ?></span>
                                        </div>
                                    </td>
                                    <td>฿<?= number_format($item['price'], 2) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td class="pe-4 text-end fw-bold">฿<?= number_format($item['subtotal'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-end py-3">
                    <h5 class="mb-0 fw-bold">Grand Total: <span class="text-success">฿<?= number_format($order['total_price'], 2) ?></span></h5>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Order Info</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Date:</strong> <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
                    <p class="mb-2"><strong>Status:</strong> <span class="badge bg-success"><?= $order['status'] ?></span></p>
                    <p class="mb-0"><strong>Payment:</strong> <?= $order['payment_method'] ?></p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Shipping Address</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong><?= htmlspecialchars($order['receiver_name']) ?></strong></p>
                    <p class="mb-1"><?= htmlspecialchars($order['phone']) ?></p>
                    <p class="mb-1"><?= nl2br(htmlspecialchars($order['address'])) ?></p>
                    <p class="mb-0"><?= htmlspecialchars($order['province']) ?> <?= htmlspecialchars($order['postal_code']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
