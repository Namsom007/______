<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container my-5'><h2>Product not found</h2></div>";
    require_once 'includes/footer.php';
    exit();
}
?>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-success text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php" class="text-success text-decoration-none">Products</a></li>
            <li class="breadcrumb-item"><a href="products.php?category=<?= $product['category_id'] ?>" class="text-success text-decoration-none"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row bg-white p-4 rounded shadow-sm">
        <div class="col-md-6 mb-4 mb-md-0">
            <img src="<?= $product['image'] ? (file_exists('uploads/products/'.$product['image']) ? 'uploads/products/'.$product['image'] : 'https://placehold.co/600x600/F7F9F4/2E7D32?text='.urlencode($product['name'])) : 'https://placehold.co/600x600/F7F9F4/2E7D32?text=No+Image' ?>" class="img-fluid rounded w-100" alt="<?= htmlspecialchars($product['name']) ?>" style="object-fit: cover; max-height: 500px;">
        </div>
        
        <div class="col-md-6">
            <span class="badge bg-light text-success border border-success mb-2 fs-6"><?= htmlspecialchars($product['category_name']) ?></span>
            <h1 class="fw-bold text-dark"><?= htmlspecialchars($product['name']) ?></h1>
            <h2 class="text-success fw-bold mb-3">฿<?= number_format($product['price'], 2) ?></h2>
            
            <p class="text-muted">Stock: <?= $product['stock'] > 0 ? "<span class='text-success fw-bold'>{$product['stock']} Available</span>" : "<span class='text-danger fw-bold'>Out of Stock</span>" ?></p>
            
            <p class="mb-4"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            
            <div class="card bg-light mb-4 border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-leaf text-success"></i> Plant Care Guide</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fa-solid fa-sun text-warning me-2" style="width: 20px;"></i> <strong>Light:</strong> <?= htmlspecialchars($product['light_requirement']) ?></li>
                        <li class="mb-2"><i class="fa-solid fa-droplet text-info me-2" style="width: 20px;"></i> <strong>Water:</strong> <?= htmlspecialchars($product['water_requirement']) ?></li>
                        <li><i class="fa-solid fa-scissors text-secondary me-2" style="width: 20px;"></i> <strong>Care:</strong> <?= htmlspecialchars($product['care_instruction']) ?></li>
                    </ul>
                </div>
            </div>

            <?php if($product['stock'] > 0): ?>
            <form action="cart_action.php" method="GET" class="d-flex gap-3 align-items-center">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <div class="input-group w-auto">
                    <span class="input-group-text">Qty</span>
                    <input type="number" name="qty" class="form-control text-center" value="1" min="1" max="<?= $product['stock'] ?>" style="width: 80px;">
                </div>
                <button type="submit" class="btn btn-success btn-lg flex-grow-1"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
            </form>
            <?php else: ?>
            <button class="btn btn-secondary btn-lg w-100" disabled>Out of Stock</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
