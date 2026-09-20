<?php
require_once '../includes/admin_header.php';

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    // Delete image file if exists
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();
    if ($product && $product['image'] && file_exists('../uploads/products/' . $product['image'])) {
        unlink('../uploads/products/' . $product['image']);
    }
    
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: products.php?msg=deleted");
    exit();
}

$stmt = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Products</h2>
        <a href="add_product.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Add New Product</a>
    </div>
    
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success">Product deleted successfully.</div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td class="ps-4"><?= $product['id'] ?></td>
                            <td>
                                <img src="<?= $product['image'] ? (file_exists('../uploads/products/'.$product['image']) ? '../uploads/products/'.$product['image'] : 'https://placehold.co/50x50/F7F9F4/2E7D32?text=Img') : 'https://placehold.co/50x50/F7F9F4/2E7D32?text=No' ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['category_name']) ?></td>
                            <td>฿<?= number_format($product['price'], 2) ?></td>
                            <td>
                                <?php if($product['stock'] > 10): ?>
                                    <span class="badge bg-success"><?= $product['stock'] ?></span>
                                <?php elseif($product['stock'] > 0): ?>
                                    <span class="badge bg-warning text-dark"><?= $product['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-edit"></i></a>
                                <a href="products.php?delete=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fa-solid fa-trash"></i></a>
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
