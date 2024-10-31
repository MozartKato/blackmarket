<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Black Market'; ?></title>
    <link rel="icon" type="image/png" href="/assets/images/Anonymous-Transparent-PNG.png">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="#">Black<span>Market</span></a>
            </div>
            <div class="hamburger" onclick="toggleMenu()">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <ul class="nav-links">
                <li><a href="/pages/dashboard">Dashboard</a></li>
                <li><a href="/pages/report/index">Laporan</a></li>
                <li><a href="/pages/transaction/index">Transaksi</a></li>
                <li><a href="/pages/product/index">Kelola Produk</a></li>
                <li><a href="/pages/admin/index">Admin</a></li>
                <li><a href="/pages/logout">Logout</a></li>
            </ul>
        </nav>
    </header>
    <script>
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }
</script>
