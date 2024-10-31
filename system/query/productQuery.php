<?php
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