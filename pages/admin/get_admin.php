<?php
session_start();

require __DIR__."../../../includes/connectdb.php";

if (!isset($_SESSION['status']) || $_SESSION['status'] == false) {
    http_response_code(403);
    echo json_encode(['message' => 'Unauthorized']);
    exit();
}

$adminId = $_GET['id'];

$sql = "SELECT * FROM admins WHERE Id = :id";
$stmt = $database->prepare($sql);
$stmt->execute([':id' => $adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    echo json_encode($admin);
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Admin not found']);
}
?>
