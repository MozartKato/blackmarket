<?php
session_start();

if ($_SESSION['status'] == false || !isset($_SESSION['status'])) {
    header("Location: /pages/login");
    exit();
}

date_default_timezone_set('Asia/Jakarta');

require __DIR__ . "/../includes/connectdb.php";

// Ambil filter dari GET parameter
$adminFilter = isset($_GET['admin']) ? $_GET['admin'] : '';
$customerFilter = isset($_GET['customer']) ? $_GET['customer'] : '';
$orderIdSearch = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Siapkan query dasar untuk mendapatkan semua admin dan customer
$admins = $database->query("SELECT Id, Name FROM admins")->fetchAll();
$customers = $database->query("SELECT Id, Name FROM customers")->fetchAll();

// Siapkan query untuk laporan
$query = "
    SELECT orders.Id, admins.Name AS AdminName, customers.Name AS CustomerName, orders.Created_at, orders.Total_payment, orders.Discount
    FROM orders
    JOIN admins ON orders.Admin_id = admins.Id
    LEFT JOIN customers ON orders.Customer_id = customers.Id
";

$conditions = [];
if ($adminFilter) {
    $conditions[] = "admins.Id = :admin";
}
if ($customerFilter) {
    $conditions[] = "customers.Id = :customer";
}
if ($orderIdSearch) {
    $conditions[] = "orders.Id = :order_id";
}
if ($startDate) {
    $conditions[] = "orders.Created_at >= :start_date";
}
if ($endDate) {
    $conditions[] = "orders.Created_at <= :end_date";
}

if ($conditions) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$query .= " ORDER BY orders.Id DESC";

$stmt = $database->prepare($query);

// Bind parameter jika ada
if ($adminFilter) {
    $stmt->bindValue(':admin', $adminFilter);
}
if ($customerFilter) {
    $stmt->bindValue(':customer', $customerFilter);
}
if ($orderIdSearch) {
    $stmt->bindValue(':order_id', $orderIdSearch);
}
if ($startDate) {
    $stmt->bindValue(':start_date', $startDate . ' 00:00:00');
}
if ($endDate) {
    $stmt->bindValue(':end_date', $endDate . ' 23:59:59');
}

$stmt->execute();
$reports = $stmt->fetchAll();

// Penjualan hari ini
$todaySales = $database->query("
    SELECT SUM(Total_payment) AS Total
    FROM orders
    WHERE DATE(Created_at) = CURDATE()
")->fetchColumn();

// Penjualan minggu ini
$weekSales = $database->query("
    SELECT SUM(Total_payment) AS Total
    FROM orders
    WHERE YEARWEEK(Created_at, 1) = YEARWEEK(CURDATE(), 1)
")->fetchColumn();

// Penjualan bulan ini
$monthSales = $database->query("
    SELECT SUM(Total_payment) AS Total
    FROM orders
    WHERE MONTH(Created_at) = MONTH(CURDATE()) 
    AND YEAR(Created_at) = YEAR(CURDATE())
")->fetchColumn();

$memberTransactions = $database->query("
    SELECT
        CASE
            WHEN Customer_id IS NOT NULL THEN 'Member'
            ELSE 'Non-Member'
        END AS CustomerType,
        COUNT(Id) AS TransactionCount
    FROM orders
    GROUP BY CustomerType
")->fetchAll();

$weeklySales = $database->query("
    SELECT 
        WEEKDAY(Created_at) AS DayIndex, 
        SUM(Total_payment) AS Total 
    FROM orders 
    WHERE Created_at BETWEEN 
        -- Mulai dari Senin minggu ini (mulai jam 00:00:00)
        DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) 
        AND 
        -- Sampai akhir Minggu minggu ini (sampai jam 23:59:59)
        DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 6 DAY) + INTERVAL '23:59:59' HOUR_SECOND
    GROUP BY DayIndex
    ORDER BY DayIndex
")->fetchAll(PDO::FETCH_ASSOC);

$salesData = array_fill(0, 7, 0); // Inisialisasi array untuk Senin-Minggu
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

foreach ($weeklySales as $sale) {
    $dayIndex = $sale['DayIndex'];
    $salesData[$dayIndex] = $sale['Total'] ?? 0;
}

$monthlySales = $database->query("
    SELECT 
        WEEK(Created_at, 3) - WEEK(DATE_SUB(CURDATE(), INTERVAL DAY(CURDATE()) - 1 DAY), 3) + 1 AS WeekIndex, 
        SUM(Total_payment) AS Total 
    FROM orders 
    WHERE YEAR(Created_at) = YEAR(CURDATE()) 
        AND MONTH(Created_at) = MONTH(CURDATE())
    GROUP BY WeekIndex
    ORDER BY WeekIndex
")->fetchAll(PDO::FETCH_ASSOC);
$weeksInMonth = 5; // Anggap bulan memiliki 5 minggu, beberapa bulan mungkin hanya 4
$salesDataMonthly = array_fill(0, $weeksInMonth, 0); // Inisialisasi untuk minggu 1-5

foreach ($monthlySales as $sale) {
    $weekIndex = $sale['WeekIndex'] - 1; // WeekIndex mulai dari 1, ubah ke 0 untuk array
    $salesDataMonthly[$weekIndex] = $sale['Total'] ?? 0;
}
$numberOfWeeks = count(array_filter($salesDataMonthly, fn($week) => $week > 0));


include __DIR__ . "/../includes/header.php";
?>