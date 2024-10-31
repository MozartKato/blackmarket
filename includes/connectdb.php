<?php

$serverAddress = "localhost";
$databaseName = "blackmarket";
$username = "root";
$password = "";

try{
     $database = new PDO(
          "mysql:host={$serverAddress};dbname={$databaseName}",
          $username,
          $password,
     );
}catch(\Exception $e){
     die("Gagal koneksi => ".$e->getMessage());
}