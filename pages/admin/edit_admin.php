<?php
session_start();
require __DIR__ . "../../../includes/connectdb.php";

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'];
$name = $data['name'];
$email = $data['email'];
$password = $data['password'] ? password_hash($data['password'], PASSWORD_DEFAULT) : null;
$role = $data['role'];

// Update admin
$sql = "UPDATE admins SET Name = ?, Email = ?, role = ?" . ($password ? ", Password = ?" : "") . " WHERE Id = ?";
$stmt = $database->prepare($sql);
$stmt->execute(array_filter([$name, $email, $role, $password, $id]));

http_response_code(200); // OK
?>
