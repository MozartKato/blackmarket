<?php
session_start();
require __DIR__ . "../../../includes/connectdb.php";

$id = $_GET['id'];

$stmt = $database->prepare("DELETE FROM admins WHERE Id = ?");
$stmt->execute([$id]);

http_response_code(200); // OK
?>
