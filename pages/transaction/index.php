<?php
session_start();

// Cek apakah session status ada atau tidak
if (!isset($_SESSION['status']) || $_SESSION['status'] == false) {
    header('Location: ../login');
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

require __DIR__ . "/../../includes/connectdb.php";

// Query untuk mengambil semua produk
$products = $database->query("
    SELECT p.*, c.Category 
    FROM products p
    JOIN categories c ON p.category_id = c.Id
    ORDER BY p.Id DESC
")->fetchAll();

// Query untuk mengambil semua customer
$customers = $database->query("SELECT Id, Name, Email, No_telepon FROM customers")->fetchAll();
?>

<?php include "../../includes/header.php"; ?>

<div class="container-transaction">
    <div class="Product">
        <?php foreach ($products as $product): ?>
            <div class="card-product">
                <img src="<?= $product['image_path'] ?>" alt="<?= $product['Name'] ?>" class="product-image">
                <h3><?= $product['Name'] ?></h3>
                <p class="price">Rp. <?= number_format($product['Price'], 0, ',', '.') ?></p>
                <p>Stock: <?= $product['Stock'] ?></p>
                <p>Kategori: <?= $product['Category'] ?></p>
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['Id'] ?>">
                    <input type="hidden" name="product_name" value="<?= $product['Name'] ?>">
                    <input type="hidden" name="product_price" value="<?= $product['Price'] ?>">
                    <input type="hidden" name="product_stock" value="<?= $product['Stock'] ?>">
                    <button type="submit" <?= $product['Stock'] <= 0 ? 'disabled' : '' ?> class="button-add-cart">Add to Cart</button>
                    <?php if ($product['Stock'] <= 0): ?>
                        <span style="color: red;">Stok habis</span>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="order">
        <h2>Order</h2>

        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="product-cart">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="list-product-cart">
                    <?php
                    $totalOrder = 0;
                    foreach ($_SESSION['cart'] as $index => $item):
                        $itemTotal = $item['price'] * $item['quantity'];
                        $totalOrder += $itemTotal;
                    ?>
                        <tr>
                            <td><?= $item['name'] ?></td>
                            <td>Rp. <?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td>
                                <form action="update_cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" required>
                                </form>
                            </td>
                            <td>Rp. <?= number_format($itemTotal, 0, ',', '.') ?></td>
                            <td>
                                <form action="remove_from_cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <button type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p>Total: Rp. <?= number_format($totalOrder, 0, ',', '.') ?></p>

            <!-- Form untuk memilih customer dan input diskon -->
            <!-- Form untuk memilih customer dan input diskon -->
            <form action="process_order.php" method="POST" onsubmit="return validateForm()">
                <label for="customer">Pilih Customer (Opsional):</label>
                <select name="customer" id="customer">
                    <option value="0">Bukan Member</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['Id'] ?>">
                            <?= $customer['Name'] ?> (<?= $customer['Email'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="discount">Persentase Diskon (%):</label>
                <input type="number" name="discount" id="discount" min="0" max="100" placeholder="Masukkan diskon" disabled>

                <button type="submit">Process Order</button>
            </form>

            <script>
                const customerSelect = document.getElementById('customer');
                const discountInput = document.getElementById('discount');

                customerSelect.addEventListener('change', function() {
                    discountInput.disabled = this.value == "0"; // Disable diskon jika tidak ada customer yang dipilih
                    discountInput.value = ''; // Reset nilai diskon saat customer diubah
                });

                function validateForm() {
                    if (customerSelect.value == "0" && discountInput.value > 0) {
                        alert("Diskon hanya dapat diberikan jika customer dipilih.");
                        return false; // Mencegah form dikirim
                    }
                    return true; // Mengizinkan form dikirim
                }
            </script>
        <?php else: ?>
            <p style="text-align: center;">No items selected</p>
        <?php endif; ?>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>