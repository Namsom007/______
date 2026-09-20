<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    if (empty($login) || empty($password)) {
        $error = "Please enter email/username and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :login OR username = :login LIMIT 1");
        $stmt->execute(['login' => $login]);
        $user = $stmt->fetch();

        if ($user) {
            $valid_password = false;

            // Verify with bcrypt
            if (password_verify($password, $user['password'])) {
                $valid_password = true;
            } 
            // Fallback for seed data (MD5) - update to bcrypt on success
            elseif ($user['password'] === md5($password)) {
                $valid_password = true;
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = :hash WHERE id = :id");
                $update_stmt->execute(['hash' => $new_hash, 'id' => $user['id']]);
            }

            if ($valid_password) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4">
                <h3 class="text-center text-success mb-4"><i class="fa-solid fa-sign-in-alt"></i> Login</h3>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label class="form-label">Email or Username</label>
                        <input type="text" name="login" class="form-control" required value="<?= isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '' ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>
                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="register.php" class="text-success text-decoration-none">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
