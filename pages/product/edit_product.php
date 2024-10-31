<?php
session_start();

// Cek apakah session status ada atau tidak
if (!isset($_SESSION['status']) || $_SESSION['status'] == false) {
    http_response_code(403); // Forbidden
    exit();
}

require __DIR__ . "/../../includes/connectdb.php";

// Ambil data JSON dari permintaan
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'], $data['name'], $data['stock'], $data['price'])) {
    $id = $data['id'];
    $name = $data['name'];
    $stock = $data['stock'];
    $price = $data['price'];

    // Query untuk memperbarui produk
    $stmt = $database->prepare("UPDATE products SET Name = ?, Stock = ?, Price = ? WHERE Id = ?");
    $stmt->execute([$name, $stock, $price, $id]);

    // Mengirimkan respons
    echo json_encode(['success' => true]);
} else {
    http_response_code(400); // Bad request
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
