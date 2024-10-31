<?php
require('../../includes/fpdf.php'); // Pastikan kamu mengunduh dan menyertakan FPDF

class PDF extends FPDF {
    // Header
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Order Details', 0, 1, 'C');
    }

    // Footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Ambil order_id dari URL
$orderId = isset($_GET['order_id']) ? $_GET['order_id'] : '';

// Koneksi ke database
require __DIR__ . "../../../includes/connectdb.php";

if ($orderId) {
    // Ambil data order dan detail dari database
    $stmt = $database->prepare("
        SELECT orders.Id, orders.Total_payment, orders.Discount, customers.Name AS CustomerName
        FROM orders
        LEFT JOIN customers ON orders.Customer_id = customers.Id
        WHERE orders.Id = :order_id
    ");
    $stmt->bindValue(':order_id', $orderId);
    $stmt->execute();
    $order = $stmt->fetch();

    // Cek apakah ada data order
    if ($order) {
        // Ambil detail produk untuk order ini
        $stmtDetails = $database->prepare("
            SELECT products.Name AS product_name, order_details.Quantity, products.Price, (order_details.Quantity * products.Price) AS Subtotal
            FROM order_details
            JOIN products ON order_details.Product_id = products.Id
            WHERE order_details.Order_id = :order_id
        ");
        $stmtDetails->bindValue(':order_id', $orderId);
        $stmtDetails->execute();
        $orderDetails = $stmtDetails->fetchAll();

        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);

        // Tampilkan informasi order
        $pdf->Cell(40, 10, 'Order ID: ' . $order['Id'], 0, 1);
        $pdf->Cell(40, 10, 'Customer: ' . ($order['CustomerName'] ? $order['CustomerName'] : 'Bukan Member'), 0, 1);
        $pdf->Ln(10); // Jarak sebelum tabel

        // Tabel header
        $pdf->Cell(40, 10, 'Product', 1);
        $pdf->Cell(30, 10, 'Quantity', 1);
        $pdf->Cell(30, 10, 'Price', 1);
        $pdf->Cell(30, 10, 'Subtotal', 1);
        $pdf->Ln();

        $totalSubtotal = 0;

        // Tampilkan detail produk
        foreach ($orderDetails as $detail) {
            $pdf->Cell(40, 10, $detail['product_name'], 1);
            $pdf->Cell(30, 10, $detail['Quantity'], 1);
            $pdf->Cell(30, 10, 'Rp. ' . number_format($detail['Price'], 0, ',', '.'), 1);
            $pdf->Cell(30, 10, 'Rp. ' . number_format($detail['Subtotal'], 0, ',', '.'), 1);
            $pdf->Ln();
            $totalSubtotal += $detail['Subtotal'];
        }

        // Menampilkan total
        $pdf->Cell(100, 10, 'Total Subtotal:', 1);
        $pdf->Cell(30, 10, 'Rp. ' . number_format($totalSubtotal, 0, ',', '.'), 1);
        $pdf->Ln();
        
        // Menghitung dan menampilkan diskon
        $discountAmount = $totalSubtotal * ($order['Discount'] / 100);
        $totalAfterDiscount = $totalSubtotal - $discountAmount;

        $pdf->Cell(100, 10, 'Diskon (' . $order['Discount'] . '%):', 1);
        $pdf->Cell(30, 10, 'Rp. ' . number_format($discountAmount, 0, ',', '.'), 1);
        $pdf->Ln();

        $pdf->Cell(100, 10, 'Total Akhir:', 1);
        $pdf->Cell(30, 10, 'Rp. ' . number_format($totalAfterDiscount, 0, ',', '.'), 1);

        $pdf->Output('D', 'order_details_id_'.$orderId.'.pdf');
    } else {
        echo 'Order tidak ditemukan.';
    }
} else {
    echo 'Order ID tidak valid.';
}
?>
