<?php
session_start();

require __DIR__."../../includes/connectdb.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Gunakan prepared statement untuk keamanan
    $sql = "SELECT Id, Password, role FROM admins WHERE Email = :email";
    $stmt = $database->prepare($sql);
    $stmt->execute([':email' => $email]);

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['Password'])) {
        $_SESSION['status'] = true;
        $_SESSION['admin_role'] = $admin['role'];
        header('Location: dashboard');
        exit();
    } else {
        $error = "Email atau password salah!";
    }
}
?>