<?php
session_start();

// Cek apakah session cart ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    $newQuantity = $_POST['quantity'];

    // Cek apakah produk ada di cart
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $productId) {
            $item['quantity'] = $newQuantity;
            break;
        }
    }
}

// Redirect kembali ke halaman sebelumnya
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>
