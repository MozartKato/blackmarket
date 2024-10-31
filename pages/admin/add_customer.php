<?php
session_start();
require __DIR__ . "../../../includes/connectdb.php";

$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];

$stmt = $database->prepare("INSERT INTO customers (Name, Email, No_telepon) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $phone]);

http_response_code(201); // Created
?>
