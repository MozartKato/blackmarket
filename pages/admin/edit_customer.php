<?php
session_start();
require __DIR__ . "../../../includes/connectdb.php";

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'];
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];

// Update customer
$stmt = $database->prepare("UPDATE customers SET Name = ?, Email = ?, No_telepon = ? WHERE Id = ?");
$stmt->execute([$name, $email, $phone, $id]);

http_response_code(200); // OK
?>
