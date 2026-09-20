<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;
$qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;

if ($id > 0) {
    if ($action === 'add') {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $qty;
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    } elseif ($action === 'update') {
        if ($qty > 0) {
            $_SESSION['cart'][$id] = $qty;
        } else {
            unset($_SESSION['cart'][$id]);
        }
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$id]);
    }
}

// Redirect back to referring page or cart
$referer = $_SERVER['HTTP_REFERER'] ?? 'cart.php';
if ($action === 'add') {
    // Optional: flash message logic could go here
    header("Location: $referer");
} else {
    header("Location: cart.php");
}
exit();
?>
