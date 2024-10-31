<?php

$query = $database->prepare("
    SELECT products.Name AS product_name, order_details.Quantity, order_details.Price, order_details.Subtotal
    FROM order_details
    JOIN products ON order_details.Product_id = products.Id
    WHERE order_details.Order_id = ?
");

$orderQuery = $database->prepare("
    SELECT Discount
    FROM orders
    WHERE Id = ?
");

?>