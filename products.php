<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Fetch Categories for filter
$cat_stmt = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $cat_stmt->fetchAll();

// Build Query
$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

// Search
if (!empty($_GET['search'])) {
    $query .= " AND p.name LIKE :search";
    $params['search'] = '%' . $_GET['search'] . '%';
}

// Category
if (!empty($_GET['category'])) {
    $query .= " AND p.category_id = :category";
    $params['category'] = $_GET['category'];
}

// Price Filter
if (!empty($_GET['min_price'])) {
    $query .= " AND p.price >= :min_price";
    $params['min_price'] = $_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $query .= " AND p.price <= :max_price";
    $params['max_price'] = $_GET['max_price'];
}

// Sorting
$sort = $_GET['sort'] ?? 'newest';
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'newest':
    default:
        $query .= " ORDER BY p.created_at DESC";
        break;
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 mb-4">
            <div class="card p-3 sticky-top" style="top: 80px; z-index: 1;">
                <h5 class="fw-bold text-success mb-3">Filters</h5>
                <form method="GET" action="products.php">
                    <!-- Preserve search -->
                    <?php if(!empty($_GET['search'])): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Category</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Price Range</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>">
                            <span>-</span>
                            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Sort By</label>
                        <select name="sort" class="form-select form-select-sm">
                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest</option>
                            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success btn-sm w-100">Apply Filters</button>
                    <a href="products.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">Clear Filters</a>
                </form>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <h2 class="text-success fw-bold mb-4">Our Plants</h2>
            
            <?php if(empty($products)): ?>
                <div class="alert alert-warning text-center">No products found matching your criteria.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach($products as $product): ?>
                    <div class="col-sm-6 col-md-6 col-lg-4">
                        <div class="card h-100">
                            <img src="<?= $product['image'] ? (file_exists('uploads/products/'.$product['image']) ? 'uploads/products/'.$product['image'] : 'https://placehold.co/400x400/F7F9F4/2E7D32?text='.urlencode($product['name'])) : 'https://placehold.co/400x400/F7F9F4/2E7D32?text=No+Image' ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($product['name']) ?>">
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
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
