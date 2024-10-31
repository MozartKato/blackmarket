<?php
$todaySales = $database->query("SELECT SUM(Total_payment) FROM orders WHERE DATE(Created_at) = CURDATE()")->fetchColumn();
$weekSales = $database->query("SELECT SUM(Total_payment) FROM orders WHERE WEEK(Created_at) = WEEK(CURDATE())")->fetchColumn();
$monthSales = $database->query("SELECT SUM(Total_payment) FROM orders WHERE MONTH(Created_at) = MONTH(CURDATE())")->fetchColumn();
$totalOrders = $database->query("SELECT COUNT(Id) FROM orders")->fetchColumn();
$totalCustomers = $database->query("SELECT COUNT(Id) FROM customers")->fetchColumn();
$totalRevenue = $database->query("SELECT SUM(Total_payment) FROM orders")->fetchColumn();
$totalProducts = $database->query("SELECT COUNT(Id) FROM products")->fetchColumn();
$totalCategories = $database->query("SELECT COUNT(Id) FROM categories")->fetchColumn();

// 5 produk terlaris
$topProducts = $database->query("
    SELECT products.Name AS ProductName, categories.category AS Category, SUM(order_details.Quantity) AS TotalSold
FROM order_details
JOIN products ON order_details.Product_id = products.Id
JOIN categories ON products.Category_id = categories.Id
GROUP BY products.Name, categories.category
ORDER BY TotalSold DESC
LIMIT 5;
")->fetchAll();

$filteredOrders = $database->prepare("
    SELECT DATE(Created_at) AS OrderDate, COUNT(Id) AS OrderCount, SUM(Total_payment) AS TotalSales
    FROM orders
    WHERE DATE(Created_at) BETWEEN :fromDate AND :toDate
    GROUP BY OrderDate
    ORDER BY OrderDate
");

// Data transaksi oleh admin bulan ini
$adminTransactions = $database->query("
    SELECT admins.Name AS AdminName, COUNT(orders.Id) AS TransactionCount
    FROM orders
    JOIN admins ON orders.Admin_id = admins.Id
    WHERE MONTH(orders.Created_at) = MONTH(CURDATE())
    GROUP BY admins.Name
")->fetchAll();

// Order terbaru
$latestOrders = $database->query("
    SELECT orders.Id, customers.Name AS CustomerName, orders.Created_at, orders.Total_payment 
    FROM orders 
    LEFT JOIN customers ON orders.Customer_id = customers.Id
    ORDER BY Created_at DESC LIMIT 5
")->fetchAll();

// Transaksi member dan non-member
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

// Kategori produk paling laris
$topCategories = $database->query("
    SELECT categories.Category AS CategoryName, SUM(order_details.Quantity) AS TotalSold
    FROM order_details
    JOIN products ON order_details.Product_id = products.Id
    JOIN categories ON products.Category_id = categories.Id
    GROUP BY categories.Category
    ORDER BY TotalSold DESC
    LIMIT 5
")->fetchAll();
?>