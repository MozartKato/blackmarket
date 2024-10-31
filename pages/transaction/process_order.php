<?php
session_start();

require __DIR__ . "/../../includes/connectdb.php";

if (!empty($_SESSION['cart'])) {
    $adminId = $_SESSION['admin_id']; // Ambil admin_id dari session
    $customerId = isset($_POST['customer']) && $_POST['customer'] != 0 ? $_POST['customer'] : null; // Ambil customer_id jika ada
    $discount = isset($_POST['discount']) ? $_POST['discount'] : 0; // Ambil diskon dari form

    // Validasi diskon
    if (!is_numeric($discount) || $discount < 0 || $discount > 100) {
        $discount = 0; // Atur diskon ke 0 jika tidak valid
    }

    $totalPayment = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalPayment += $item['price'] * $item['quantity'];
    }

    // Terapkan diskon jika ada
    if ($discount > 0) {
        $totalPayment -= ($totalPayment * ($discount / 100));
    }

    // Insert ke tabel `orders` dengan atau tanpa customer_id
    // Tambahkan diskon ke pernyataan INSERT
    if ($customerId) {
        $stmt = $database->prepare("
            INSERT INTO orders (Admin_id, Customer_id, Created_at, Total_payment, Discount)
            VALUES (?, ?, NOW(), ?, ?)
        ");
        $stmt->execute([$adminId, $customerId, $totalPayment, $discount]);
    } else {
        $stmt = $database->prepare("
            INSERT INTO orders (Admin_id, Created_at, Total_payment, Discount)
            VALUES (?, NOW(), ?, ?)
        ");
        $stmt->execute([$adminId, $totalPayment, $discount]);
    }

    $orderId = $database->lastInsertId(); // Dapatkan ID pesanan yang baru dimasukkan

    // Kurangi stok produk
    foreach ($_SESSION['cart'] as $item) {
        $stmt = $database->prepare("UPDATE products SET Stock = Stock - ? WHERE Id = ?");
        $stmt->execute([$item['quantity'], $item['id']]);

        // Simpan detail order tanpa memasukkan Subtotal
        $stmt = $database->prepare("
            INSERT INTO order_details (Order_id, Product_id, Quantity, Price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
    }

    // Hapus keranjang setelah pemrosesan
    unset($_SESSION['cart']);
    $_SESSION['order_success'] = true; // Tandai sukses order
    header('Location: order_success.php');
    exit();
} else {
    header('Location: index.php'); // Jika keranjang kosong atau customer tidak dipilih
    exit();
}
