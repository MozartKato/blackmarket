<?php
require __DIR__ . "../../../includes/connectdb.php";
require __DIR__. "/../../system/query/order_detailsQuery.php";

$order_id = $_GET['order_id'] ?? 0;

// Mengambil detail order dari tabel order_details

$query->execute([$order_id]);

$order_details = $query->fetchAll(PDO::FETCH_ASSOC);

// Mengambil informasi order, termasuk diskon
$orderQuery->execute([$order_id]);
$orderInfo = $orderQuery->fetch(PDO::FETCH_ASSOC);

$discount = $orderInfo ? $orderInfo['Discount'] : 0;

// Hitung total subtotal
$totalSubtotal = array_sum(array_column($order_details, 'Subtotal'));

// Menghitung total setelah diskon
$totalAfterDiscount = $totalSubtotal - ($totalSubtotal * ($discount / 100));

// Persiapkan response
if ($order_details) {
    echo json_encode([
        'success' => true,
        'details' => $order_details,
        'discount' => $discount,
        'totalSubtotal' => $totalSubtotal,
        'totalAfterDiscount' => $totalAfterDiscount
    ]);
} else {
    echo json_encode([
        'success' => false
    ]);
}
?>