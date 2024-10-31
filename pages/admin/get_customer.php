<?php
session_start();

require __DIR__."../../../includes/connectdb.php";

if (!isset($_SESSION['status']) || $_SESSION['status'] == false) {
    http_response_code(403);
    echo json_encode(['message' => 'Unauthorized']);
    exit();
}

$customerId = $_GET['id'];

$sql = "SELECT * FROM customers WHERE Id = :id";
$stmt = $database->prepare($sql);
$stmt->execute([':id' => $customerId]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if ($customer) {
    echo json_encode($customer);
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Customer not found']);
}
?>
