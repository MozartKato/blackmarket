<?php 
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] === false) {
    header("Location: /pages/login");
    exit();
}

$_SESSION['status'] = false;
session_unset();
session_destroy();

header("Location: /pages/login");
exit();
?>
