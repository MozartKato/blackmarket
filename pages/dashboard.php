<?php
session_start();

if ($_SESSION['status'] == false) {
    header('Location: login');
    exit();
}

require "../includes/connectdb.php";
require "../system/query/dashboardQuery.php";

// Data summary untuk penjualan dan order


// Filter rentang tanggal untuk diagram order
$filterFromDate = isset($_GET['from']) ? $_GET['from'] : '2024-01-01';
$filterToDate = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

$filteredOrders->execute([
    ':fromDate' => $filterFromDate,
    ':toDate' => $filterToDate
]);
$orderData = $filteredOrders->fetchAll();

include "../includes/header.php";
?>

<div class="dashboard-container">
    <!-- Ringkasan Data -->
    <div class="summary">
        <?php
        $cards = [
            ['Total Penjualan Hari Ini', "Rp. " . number_format($todaySales ?? 0, 0, ',', '.')],
            ['Total Penjualan Minggu Ini', "Rp. " . number_format($weekSales ?? 0, 0, ',', '.')],
            ['Total Penjualan Bulan Ini', "Rp. " . number_format($monthSales ?? 0, 0, ',', '.')],
            ['Total Pendapatan', "Rp. " . number_format($totalRevenue ?? 0, 0, ',', '.')],
            ['Total Order', $totalOrders ?? 0],
            ['Total Customer', $totalCustomers ?? 0],
            ['Total Produk', $totalProducts ?? 0],
            ['Total Kategori', $totalCategories ?? 0]
        ];
        foreach ($cards as $card): ?>
            <div class="card">
                <h3><?= $card[0] ?></h3>
                <p><?= $card[1] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Diagram Penjualan dan Transaksi -->
    <div class="charts-grid">
        <div class="kelompok-charts">
            <div class="chart-container">
                <canvas id="salesBarChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="topCategoriesChart"></canvas>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="adminPieChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="customerPieChart"></canvas>
        </div>
    </div>

    <div class="populer-products">
        <h3>Produk Populer</h3>
        <table>
            <thead>
                <tr>
                    <td>Nama Produk</td>
                    <td>Kategori</td>
                    <td>Jumlah Terjual</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topProducts as $product): ?>
                    <tr>
                        <td><?= $product['ProductName'] ?></td>
                        <td><?= $product['Category'] ?></td>
                        <td><?= $product['TotalSold'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Tabel Order Terbaru -->
    <div class="latest-orders">
        <h3>Order Terbaru</h3>
        <table>
            <thead>
                <tr>
                    <th>ID Order</th>
                    <th>Customer</th>
                    <th>Tanggal</th>
                    <th>Total Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latestOrders as $order): ?>
                    <tr>
                        <td><?= $order['Id'] ?></td>
                        <td><?= $order['CustomerName'] ? $order['CustomerName'] : 'Bukan Member' ?></td>
                        <td><?= $order['Created_at'] ?></td>
                        <td>Rp. <?= number_format($order['Total_payment'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .populer-products {
        width: 100%;
    }

    .kelompok-charts {
        display: flex;
        flex-direction: column;
    }

    .dashboard-container {
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: #1e1e1e;
        color: #fff;
    }

    .summary {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        width: 100%;
        margin-bottom: 20px;
    }

    .card {
        padding: 20px;
        background-color: #2b2b2b;
        border-radius: 8px;
        text-align: center;
        margin: 10px;
        flex: 0 1 calc(25% - 20px);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        width: 100%;
    }

    .latest-orders {
        width: 100%;
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    table,
    th,
    td {
        border: 1px solid #444;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #333;
    }

    .chart-container {
        width: 100%;
        max-width: 600px;
        margin: 20px 0;
    }

    canvas {
        background-color: #2b2b2b;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
</style>

<!-- Tambahkan script untuk chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk Diagram Batang Penjualan
    const ctxBar = document.getElementById('salesBarChart').getContext('2d');
    const salesBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Hari Ini', 'Minggu Ini', 'Bulan Ini'],
            datasets: [{
                label: 'Total Penjualan (Rp)',
                data: [<?= $todaySales ?>, <?= $weekSales ?>, <?= $monthSales ?>],
                backgroundColor: ['rgba(75, 192, 192, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Data untuk Diagram Lingkaran Transaksi Admin
    const ctxPie = document.getElementById('adminPieChart').getContext('2d');
    const adminPieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: [<?php foreach ($adminTransactions as $admin) {
                            echo "'" . $admin['AdminName'] . "',";
                        } ?>],
            datasets: [{
                label: 'Transaksi oleh Admin',
                data: [<?php foreach ($adminTransactions as $admin) {
                            echo $admin['TransactionCount'] . ",";
                        } ?>],
                backgroundColor: ['#4bc0c0', '#36a2eb', '#ffcd56', '#ff6384', '#4bc0c0'],
                borderWidth: 1
            }]
        }
    });

    // Data untuk Diagram Lingkaran Member/Non-member
    const ctxCustomerPie = document.getElementById('customerPieChart').getContext('2d');
    const customerPieChart = new Chart(ctxCustomerPie, {
        type: 'pie',
        data: {
            labels: ['Non-Member', 'Member'],
            datasets: [{
                label: 'Transaksi',
                data: [<?php foreach ($memberTransactions as $transaction) {
                            echo $transaction['TransactionCount'] . ",";
                        } ?>],
                backgroundColor: ['#ff6384', '#36a2eb'],
                borderWidth: 1
            }]
        }
    });

    // Data untuk Diagram Kategori Produk Terlaris
    const ctxTopCategories = document.getElementById('topCategoriesChart').getContext('2d');
    const topCategoriesChart = new Chart(ctxTopCategories, {
        type: 'bar',
        data: {
            labels: [<?php foreach ($topCategories as $category) {
                            echo "'" . $category['CategoryName'] . "',";
                        } ?>],
            datasets: [{
                label: 'Kategori terjual',
                data: [<?php foreach ($topCategories as $category) {
                            echo $category['TotalSold'] . ",";
                        } ?>],
                backgroundColor: ['#4bc0c0', '#36a2eb', '#ffcd56', '#ff6384', '#4bc0c0'],
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include "../includes/footer.php"; ?>