<?php
session_start();
require __DIR__ . "../../../includes/connectdb.php";

$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $database->prepare("INSERT INTO admins (Name, Email, Password) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $password]);

http_response_code(201); // Created
?>
