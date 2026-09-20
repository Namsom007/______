<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    // Save intended destination
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

$cart_items = [];
$grand_total = 0;

$in = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
$stmt_cart = $conn->prepare("SELECT id, name, price, stock FROM products WHERE id IN ($in)");
$stmt_cart->execute(array_keys($_SESSION['cart']));
$products = $stmt_cart->fetchAll();

foreach ($products as $p) {
    $qty = $_SESSION['cart'][$p['id']];
    // verify stock
    if ($qty > $p['stock']) {
        $qty = $p['stock'];
        $_SESSION['cart'][$p['id']] = $qty; // adjust cart to max stock
    }
    $subtotal = $p['price'] * $qty;
    $grand_total += $subtotal;
    
    $p['qty'] = $qty;
    $p['subtotal'] = $subtotal;
    $cart_items[] = $p;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_name = trim($_POST['receiver_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $province = trim($_POST['province']);
    $postal_code = trim($_POST['postal_code']);
    $payment_method = $_POST['payment_method'];

    if(empty($receiver_name) || empty($phone) || empty($address) || empty($province) || empty($postal_code) || empty($payment_method)) {
        $error = "Please fill in all shipping details.";
    } else {
        try {
            $conn->beginTransaction();
            
            // Create Order
            $stmt_order = $conn->prepare("INSERT INTO orders (user_id, receiver_name, phone, address, province, postal_code, payment_method, total_price, status) VALUES (:uid, :rname, :ph, :addr, :prov, :zip, :pm, :total, 'Pending')");
            $stmt_order->execute([
                'uid' => $user_id,
                'rname' => $receiver_name,
                'ph' => $phone,
                'addr' => $address,
                'prov' => $province,
                'zip' => $postal_code,
                'pm' => $payment_method,
                'total' => $grand_total
            ]);
            
            $order_id = $conn->lastInsertId();
            
            // Insert Order Items and Update Stock
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (:oid, :pid, :qty, :price, :sub)");
            $stmt_stock = $conn->prepare("UPDATE products SET stock = stock - :qty WHERE id = :pid");

            foreach ($cart_items as $item) {
                $stmt_item->execute([
                    'oid' => $order_id,
                    'pid' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'sub' => $item['subtotal']
                ]);
                
                $stmt_stock->execute([
                    'qty' => $item['qty'],
                    'pid' => $item['id']
                ]);
            }
            
            $conn->commit();
            
            // Clear cart
            unset($_SESSION['cart']);
            
            // Redirect to order success/history
            header("Location: my_orders.php?success=1");
            exit();

        } catch (PDOException $e) {
            $conn->rollBack();
            $error = "Failed to place order. " . $e->getMessage();
        }
    }
}
?>

<div class="container my-5">
    <h2 class="text-success fw-bold mb-4">Checkout</h2>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Shipping Information</h5>
                    <form id="checkoutForm" method="POST" action="checkout.php">
                        <div class="mb-3">
                            <label class="form-label">Receiver Name</label>
                            <input type="text" name="receiver_name" class="form-control" required value="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" required value="<?= htmlspecialchars($user['phone']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($user['address']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Province</label>
                                <input type="text" name="province" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="postal_code" class="form-control" required>
                            </div>
                        </div>
                        
                        <h5 class="fw-bold mb-3 mt-4">Payment Method</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="Cash on Delivery" checked>
                            <label class="form-check-label" for="cod">
                                Cash on Delivery (COD)
                            </label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="payment_method" id="bank" value="Bank Transfer">
                            <label class="form-check-label" for="bank">
                                Bank Transfer
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    
                    <ul class="list-group list-group-flush mb-4">
                        <?php foreach($cart_items as $item): ?>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                <small class="text-muted">Qty: <?= $item['qty'] ?></small>
                            </div>
                            <span>฿<?= number_format($item['subtotal'], 2) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
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
                        <span class="fw-bold fs-5">Total to Pay</span>
                        <span class="fw-bold fs-5 text-success">฿<?= number_format($grand_total, 2) ?></span>
                    </div>
                    
                    <button type="button" onclick="document.getElementById('checkoutForm').submit();" class="btn btn-success btn-lg w-100">Place Order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
