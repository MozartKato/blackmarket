<?php 
include "../system/loginController.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="icon" type="image/png" href="/assets/images/Anonymous-Transparent-PNG.png">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>

<body>
    <div class="logo">
        <a href="#">Black<span>Market</span></a>
    </div>
    <div class="login-form">
        <form action="login.php" method="post">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Masukkan Email" required>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Masukkan Password" required>
            <button type="submit">Login</button>
            <?php if (isset($error)): ?>
                <p> <?= $error ?></p>
            <?php endif ?>
        </form>
    </div>
</body>

</html>
