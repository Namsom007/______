<?php
require_once '../includes/admin_header.php';

$stmt = $conn->query("SELECT o.*, u.first_name, u.last_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
$orders = $stmt->fetchAll();
?>

<div class="container-fluid">
    <h2 class="mb-4">Manage Orders</h2>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Order ID</th>
                            <th>Customer</th>
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
                            <td>
                                <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?>
                                <br><small class="text-muted"><?= htmlspecialchars($order['email']) ?></small>
                            </td>
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
                                <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">View / Update</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
