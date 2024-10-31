<?php
session_start();

if($_SESSION['status'] == false){
     header('Location: login');
     exit();
}
require __DIR__."../../../includes/connectdb.php";
?>

<?php include __DIR__."../../../includes/header.php"; ?>

<style>
    h1{
        text-align: center;
    }
</style>

<div class="penjualan">
     <h1>Order telah tersimpan!</h1>
</div>
<a href="/pages/report/index">Lihat disini</a>

<?php include __DIR__."../../../includes/footer.php"; ?>