<?php 
include "../../system/reportController.php"
?>

<div class="report-index-container">
    <div class="chart-container">
        <div class="container-all-chart">
            <canvas id="salesChart" width="300" height="300"></canvas>
        </div>
        <div class="container-all-chart">
            <canvas id="weeklySalesChart" width="300" height="300"></canvas>
        </div>
        <div class="container-all-chart">
            <canvas id="monthlySalesChart" width="300" height="300"></canvas>
        </div>
        <div class="container-all-chart">
            <h3>Tipe Customer</h3>
            <canvas id="customerPieChart" width="300" height="300"></canvas>
        </div>
    </div>

    <div class="report-container">
        <div class="filter-container">
            <form method="GET" action="">
                <input type="text" name="order_id" placeholder="Search by Order ID" value="<?= htmlspecialchars($orderIdSearch) ?>" class="filter-input">

                <select name="admin" id="admin-select" class="filter-select">
                    <option value="">Filter by Admin</option>
                    <?php foreach ($admins as $admin): ?>
                        <option value="<?= $admin['Id'] ?>" <?= $adminFilter == $admin['Id'] ? 'selected' : '' ?>><?= htmlspecialchars($admin['Name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="customer" id="customer-select" class="filter-select">
                    <option value="">Filter by Customer</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['Id'] ?>" <?= $customerFilter == $customer['Id'] ? 'selected' : '' ?>><?= htmlspecialchars($customer['Name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="filter-date">
                <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="filter-date">

                <button type="submit" class="filter-button">Filter</button>
                <a href="/pages/report/index" class="reset-button">Hapus Filter</a>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <td>ID Order</td>
                    <td>Admin</td>
                    <td>Customer</td>
                    <td>Created at</td>
                    <td>Total Payment</td>
                    <td>Diskon</td>
                    <td>Aksi</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?= $report['Id'] ?></td>
                        <td><?= $report['AdminName'] ?></td>
                        <td><?= $report['CustomerName'] ? $report['CustomerName'] : 'Bukan Member' ?></td>
                        <td><?= $report['Created_at'] ?></td>
                        <td>Rp. <?= number_format($report['Total_payment'], 0, ',', '.') ?></td>
                        <td><?= $report['Discount'] ?>%</td>
                        <td>
                            <button class="btn-detail" data-order-id="<?= $report['Id'] ?>">Detail</button>
                            <a href="generate_pdf.php?order_id=<?= $report['Id'] ?>" class="btn-download">Download PDF</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
<style>
    .report-index-container {
        display: grid;
        grid-template-columns: 1fr 4fr;
        gap: 20px;
        padding: 20px;
        background-color: #1e1e1e;
        color: #eaeaea;
    }

    .filter-container {
        background-color: #2a2a2a;
        padding: 20px;
        border-radius: 8px;
    }

    .filter-input,
    .filter-select,
    .filter-date {
        margin-bottom: 10px;
        padding: 10px;
        border: 1px solid #555;
        border-radius: 4px;
        background-color: #333;
        color: #fff;
    }

    .filter-button,
    .reset-button {
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }

    .filter-button {
        background-color: #28a745;
        color: white;
    }

    .reset-button {
        background-color: #dc3545;
        color: white;
    }

    .filter-button:hover,
    .reset-button:hover {
        opacity: 0.9;
    }

    .report-container {
        background-color: #2a2a2a;
        padding: 20px;
        border-radius: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #333;
    }

    th,
    td {
        padding: 12px;
        border: 1px solid #444;
    }

    th {
        background-color: #444;
        color: #fff;
    }

    td {
        color: #eaeaea;
    }

    .btn-detail {
        padding: 5px 10px;
        background-color: #007bff;
        color: white;
        border-radius: 4px;
        border: none;
        cursor: pointer;
    }

    .btn-download {
        padding: 5px 10px;
        background-color: #28a745;
        color: white;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-download:hover,
    .btn-detail:hover {
        opacity: 0.8;
    }

    .chart-container {
        margin: 20px;
        padding: 20px;
        background-color: #2a2a2a;
        border-radius: 8px;
        max-height: max-content;
    }

    .container-all-chart {
        text-align: center;
        margin-top: 20px;
        padding: 5px;
        border: solid 1px #fff;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#admin-select').select2({
            placeholder: "Admin",
            allowClear: true
        });

        $('#customer-select').select2({
            placeholder: "Customer",
            allowClear: true
        });

        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');

                fetch(`/pages/report/order_details.php?order_id=${orderId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            let orderDetails = `
                                <table style="width:max-content; border-collapse: collapse;">
                                    <thead style="background-color: #007bff; color: #000;">
                                        <tr>
                                            <th style="padding: 10px; border: 1px solid #ddd;">Product</th>
                                            <th style="padding: 10px; border: 1px solid #ddd;">Quantity</th>
                                            <th style="padding: 10px; border: 1px solid #ddd;">Price</th>
                                            <th style="padding: 10px; border: 1px solid #ddd;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                            data.details.forEach(detail => {
                                orderDetails += `
                                    <tr>
                                        <td style="padding: 10px; border: 1px solid #ddd;">${detail.product_name}</td>
                                        <td style="padding: 10px; border: 1px solid #ddd;">${detail.Quantity}</td>
                                        <td style="padding: 10px; border: 1px solid #ddd;">Rp. ${detail.Price}</td>
                                        <td style="padding: 10px; border: 1px solid #ddd;">Rp. ${detail.Subtotal}</td>
                                    </tr>
                                `;
                            });

                            orderDetails += `
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" style="text-align: right;"><strong>Total Subtotal</strong></td>
                                            <td>Rp. ${data.totalSubtotal}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="text-align: right;"><strong>Diskon (${data.discount}%)</strong></td>
                                            <td>Rp. ${data.totalSubtotal * (data.discount / 100)}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="text-align: right;"><strong>Total Akhir</strong></td>
                                            <td>Rp. ${data.totalAfterDiscount}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            `;

                            Swal.fire({
                                title: 'Order Details',
                                html: orderDetails,
                                icon: 'info',
                                width: '600px',
                                showCloseButton: true,
                                confirmButtonText: 'Close',
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'No details found for this order.',
                                icon: 'error',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching order details:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                        });
                    });
            });
        });
    });
</script>

<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar', // Ubah tipe menjadi 'doughnut'
        data: {
            labels: ['Hari Ini', 'Minggu Ini', 'Bulan Ini'],
            datasets: [{
                label: 'Total Penjualan (Rp)',
                data: [<?= $todaySales ?>, <?= $weekSales ?>, <?= $monthSales ?>],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 10 // Ukuran font label
                        }
                    }
                }
            },
            maintainAspectRatio: false, // Menjaga ukuran canvas kecil tetap proporsional
            responsive: true
        }
    });
    const ctxCustomerPie = document.getElementById('customerPieChart').getContext('2d');
    const customerPieChart = new Chart(ctxCustomerPie, {
        type: 'doughnut',
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
    const ctxWeekly = document.getElementById('weeklySalesChart').getContext('2d');
    const weeklySalesChart = new Chart(ctxWeekly, {
        type: 'line',
        data: {
            labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu', 'Minggu'],
            datasets: [{
                label: 'Penjualan Mingguan (Rp)',
                data: [<?= implode(', ', $salesData) ?>], // Data penjualan per hari
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Penjualan (Rp)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Hari'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 12
                        }
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
    var ctxMonthly = document.getElementById('monthlySalesChart').getContext('2d');
    var chart = new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5'], // Label minggu
            datasets: [{
                label: 'Penjualan Bulan Ini',
                data: <?= json_encode($salesDataMonthly); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
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
<?php
require __DIR__ . "../../../includes/footer.php";
?>