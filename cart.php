<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';

$cart_items = [];
$grand_total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    // Prepared statement safe for IN clause
    $in = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $stmt = $conn->prepare("SELECT id, name, price, stock, image FROM products WHERE id IN ($in)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll();

    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $qty;
        $grand_total += $subtotal;
        
        $p['qty'] = $qty;
        $p['subtotal'] = $subtotal;
        $cart_items[] = $p;
    }
}
?>

<div class="container my-5">
    <h2 class="text-success fw-bold mb-4"><i class="fa-solid fa-cart-shopping"></i> Shopping Cart</h2>

    <?php if(empty($cart_items)): ?>
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <i class="fa-solid fa-basket-shopping fa-4x text-muted mb-3"></i>
            <h4>Your cart is empty</h4>
            <p class="text-muted">Looks like you haven't added any plants to your cart yet.</p>
            <a href="products.php" class="btn btn-success mt-3">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-4">Product</th>
                                        <th scope="col">Price</th>
                                        <th scope="col" style="width: 120px;">Quantity</th>
                                        <th scope="col">Subtotal</th>
                                        <th scope="col" class="pe-4 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($cart_items as $item): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= $item['image'] ? (file_exists('uploads/products/'.$item['image']) ? 'uploads/products/'.$item['image'] : 'https://placehold.co/100x100/F7F9F4/2E7D32?text=Plant') : 'https://placehold.co/100x100/F7F9F4/2E7D32?text=No+Image' ?>" class="rounded me-3" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 fw-bold"><a href="product_detail.php?id=<?= $item['id'] ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($item['name']) ?></a></h6>
                                                    <small class="text-muted">Stock: <?= $item['stock'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>฿<?= number_format($item['price'], 2) ?></td>
                                        <td>
                                            <form action="cart_action.php" method="GET" class="d-flex">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <input type="number" name="qty" class="form-control form-control-sm text-center me-1" value="<?= $item['qty'] ?>" min="1" max="<?= $item['stock'] ?>" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="fw-bold">฿<?= number_format($item['subtotal'], 2) ?></td>
                                        <td class="pe-4 text-end">
                                            <a href="cart_action.php?action=remove&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">฿<?= number_format($grand_total, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span class="fw-bold text-success">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Grand Total</span>
                            <span class="fw-bold fs-5 text-success">฿<?= number_format($grand_total, 2) ?></span>
                        </div>
                        
                        <a href="checkout.php" class="btn btn-success w-100 mb-2 py-2">Proceed to Checkout</a>
                        <a href="products.php" class="btn btn-outline-secondary w-100 py-2">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
