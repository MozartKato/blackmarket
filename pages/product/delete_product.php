<?php
session_start();
require __DIR__ . "/../../includes/connectdb.php";

// Cek apakah session status ada atau tidak
if (!isset($_SESSION['status']) || $_SESSION['status'] == false) {
    header('Location: ../login');
    exit();
}

// Ambil ID produk dari request
$productId = $_GET['id'] ?? null;

if ($productId) {
    // Query untuk menghapus produk
    $stmt = $database->prepare("DELETE FROM products WHERE Id = ?");
    if ($stmt->execute([$productId])) {
        http_response_code(200); // Sukses
    } else {
        http_response_code(500); // Gagal
    }
} else {
    http_response_code(400); // Bad Request
}
?>
