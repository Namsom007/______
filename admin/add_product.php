<?php
require_once '../includes/admin_header.php';

$stmt = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = trim($_POST['description']);
    $care = trim($_POST['care_instruction']);
    $light = trim($_POST['light_requirement']);
    $water = trim($_POST['water_requirement']);
    
    $image_name = '';

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed_ext)) {
            $image_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $image_name);
        } else {
            $error = "Invalid image format. Allowed: jpg, jpeg, png, webp";
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, care_instruction, light_requirement, water_requirement, price, stock, image) VALUES (:cid, :name, :desc, :care, :light, :water, :price, :stock, :img)");
        if ($stmt->execute([
            'cid' => $category_id,
            'name' => $name,
            'desc' => $description,
            'care' => $care,
            'light' => $light,
            'water' => $water,
            'price' => $price,
            'stock' => $stock,
            'img' => $image_name
        ])) {
            $success = "Product added successfully.";
        } else {
            $error = "Failed to add product.";
        }
    }
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Add New Product</h2>
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
            <form method="POST" action="add_product.php" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Price (฿)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" name="stock" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <div class="form-text">Allowed formats: JPG, JPEG, PNG, WEBP.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Care Instruction</label>
                        <textarea name="care_instruction" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Light Requirement</label>
                        <input type="text" name="light_requirement" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Water Requirement</label>
                        <input type="text" name="water_requirement" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-success"><i class="fa-solid fa-save"></i> Save Product</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
