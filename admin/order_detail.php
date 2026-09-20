<?php
require_once '../includes/admin_header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['id'];
$success = '';

// Update Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
    if ($stmt->execute(['status' => $new_status, 'id' => $order_id])) {
        $success = "Order status updated successfully.";
    }
}

// Fetch Order
$stmt = $conn->prepare("SELECT o.*, u.first_name, u.last_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = :id");
$stmt->execute(['id' => $order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch Items
$stmt_items = $conn->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :oid");
$stmt_items->execute(['oid' => $order_id]);
$items = $stmt_items->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0">Order #<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?> Details</h2>
        <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
    </div>

    <?php if($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

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
                                            <img src="<?= $item['image'] ? (file_exists('../uploads/products/'.$item['image']) ? '../uploads/products/'.$item['image'] : 'https://placehold.co/50x50/F7F9F4/2E7D32?text=Plant') : 'https://placehold.co/50x50/F7F9F4/2E7D32?text=No+Image' ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
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
                    <h5 class="mb-0 fw-bold">Update Status</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                <?php
                                $statuses = ['Pending', 'Confirmed', 'Shipping', 'Completed', 'Cancelled'];
                                foreach ($statuses as $st) {
                                    $selected = ($st == $order['status']) ? 'selected' : '';
                                    echo "<option value=\"$st\" $selected>$st</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Customer Info</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Account:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p class="mb-0"><strong>Registered Date:</strong> <?= date('d M Y', strtotime($order['created_at'])) ?></p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Shipping Details</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Receiver:</strong> <?= htmlspecialchars($order['receiver_name']) ?></p>
                    <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                    <p class="mb-1"><strong>Address:</strong><br><?= nl2br(htmlspecialchars($order['address'])) ?></p>
                    <p class="mb-1"><strong>Province:</strong> <?= htmlspecialchars($order['province']) ?></p>
                    <p class="mb-0"><strong>Postal Code:</strong> <?= htmlspecialchars($order['postal_code']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
