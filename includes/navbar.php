<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand text-success fw-bold" href="index.php">
            <i class="fa-solid fa-leaf"></i> GreenLeaf
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
            </ul>
            
            <form class="d-flex me-3" action="products.php" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="search" placeholder="Search plants..." aria-label="Search">
                    <button class="btn btn-outline-success" type="submit"><i class="fa-solid fa-search"></i></button>
                </div>
            </form>

            <ul class="navbar-nav">
                <li class="nav-item me-2">
                    <a class="nav-link" href="cart.php">
                        <i class="fa-solid fa-shopping-cart"></i> Cart
                        <?php 
                        $cart_count = 0;
                        if(isset($_SESSION['cart'])){
                            foreach($_SESSION['cart'] as $qty){
                                $cart_count += $qty;
                            }
                        }
                        if($cart_count > 0): ?>
                            <span class="badge bg-success rounded-pill"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="my_orders.php">My Orders</a></li>
                            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin/index.php">Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-success ms-2" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
