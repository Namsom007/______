<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Fetch Featured Categories (Limit 4)
$stmt = $conn->query("SELECT * FROM categories LIMIT 4");
$categories = $stmt->fetchAll();

// Fetch Featured Products (Limit 8)
$stmt = $conn->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 8");
$featured_products = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-3 fw-bold text-success mb-4">Bring Nature Into Your Home</h1>
        <p class="lead mb-5">เลือกต้นไม้ที่ใช่ เติมความสดชื่นให้ทุกพื้นที่ของคุณ</p>
        <a href="products.php" class="btn btn-success btn-lg me-2">Shop Now</a>
        <a href="products.php" class="btn btn-outline-success btn-lg bg-white">View Products</a>
    </div>
</section>

<!-- Featured Categories -->
<section class="container my-5">
    <h2 class="text-center text-success mb-4 fw-bold">Featured Categories</h2>
    <div class="row g-4">
        <?php foreach($categories as $category): ?>
        <div class="col-6 col-md-3">
            <a href="products.php?category=<?= $category['id'] ?>" class="text-decoration-none">
                <div class="card bg-dark text-white text-center category-card">
                    <img src="https://placehold.co/400x300/2E7D32/FFF?text=<?= urlencode($category['name']) ?>" class="card-img category-img opacity-50" alt="<?= htmlspecialchars($category['name']) ?>">
                    <div class="card-img-overlay d-flex align-items-center justify-content-center">
                        <h5 class="card-title fw-bold m-0"><?= htmlspecialchars($category['name']) ?></h5>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products -->
<section class="container my-5">
    <h2 class="text-center text-success mb-4 fw-bold">Featured Products</h2>
    <div class="row g-4">
        <?php foreach($featured_products as $product): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100">
                <img src="https://placehold.co/400x400/F7F9F4/2E7D32?text=<?= urlencode($product['name']) ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-light text-success border border-success mb-2 align-self-start"><?= htmlspecialchars($product['category_name']) ?></span>
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text text-success fw-bold fs-5">฿<?= number_format($product['price'], 2) ?></p>
                    
                    <div class="mt-auto">
                        <p class="text-muted small mb-2">Stock: <?= $product['stock'] ?></p>
                        <div class="d-flex justify-content-between">
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-outline-success btn-sm w-50 me-1">View Detail</a>
                            <?php if($product['stock'] > 0): ?>
                                <a href="cart_action.php?action=add&id=<?= $product['id'] ?>&qty=1" class="btn btn-success btn-sm w-50 ms-1">Add to Cart</a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm w-50 ms-1" disabled>Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
