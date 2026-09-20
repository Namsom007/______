<?php
require_once '../includes/admin_header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = $_GET['id'];
$error = '';
$success = '';

$stmt = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = trim($_POST['description']);
    $care = trim($_POST['care_instruction']);
    $light = trim($_POST['light_requirement']);
    $water = trim($_POST['water_requirement']);
    
    $image_query_part = "";
    $params = [
        'cid' => $category_id,
        'name' => $name,
        'desc' => $description,
        'care' => $care,
        'light' => $light,
        'water' => $water,
        'price' => $price,
        'stock' => $stock,
        'id' => $id
    ];

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed_ext)) {
            $image_name = uniqid() . '.' . $file_ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $image_name)) {
                $image_query_part = ", image = :img";
                $params['img'] = $image_name;
                
                // Delete old image
                $old_stmt = $conn->prepare("SELECT image FROM products WHERE id = :id");
                $old_stmt->execute(['id' => $id]);
                $old_img = $old_stmt->fetchColumn();
                if ($old_img && file_exists('../uploads/products/' . $old_img)) {
                    unlink('../uploads/products/' . $old_img);
                }
            }
        } else {
            $error = "Invalid image format. Allowed: jpg, jpeg, png, webp";
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("UPDATE products SET category_id = :cid, name = :name, description = :desc, care_instruction = :care, light_requirement = :light, water_requirement = :water, price = :price, stock = :stock {$image_query_part} WHERE id = :id");
        if ($stmt->execute($params)) {
            $success = "Product updated successfully.";
        } else {
            $error = "Failed to update product.";
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found.";
    exit();
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Product</h2>
        <a href="products.php" class="btn btn-secondary">Back to Products</a>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="edit_product.php?id=<?= $id ?>" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Price (฿)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required value="<?= htmlspecialchars($product['price']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" name="stock" class="form-control" required value="<?= htmlspecialchars($product['stock']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <div class="form-text">Leave blank if you don't want to change the image.</div>
                    <?php if($product['image']): ?>
                        <div class="mt-2">
                            <img src="<?= file_exists('../uploads/products/'.$product['image']) ? '../uploads/products/'.$product['image'] : 'https://placehold.co/100x100?text=Img' ?>" style="width: 100px; height: 100px; object-fit: cover;" class="rounded border">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Care Instruction</label>
                        <textarea name="care_instruction" class="form-control" rows="2"><?= htmlspecialchars($product['care_instruction']) ?></textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Light Requirement</label>
                        <input type="text" name="light_requirement" class="form-control" value="<?= htmlspecialchars($product['light_requirement']) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Water Requirement</label>
                        <input type="text" name="water_requirement" class="form-control" value="<?= htmlspecialchars($product['water_requirement']) ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Product</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
