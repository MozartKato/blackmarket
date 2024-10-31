<?php
session_start();

// Cek apakah session status ada atau tidak
if (!isset($_SESSION['status']) || $_SESSION['status'] == false) {
    header('Location: ../login');
    exit();
}

require __DIR__ . "/../../includes/connectdb.php";

// Query untuk mengambil semua produk
$products = $database->query("
    SELECT p.*, c.Category 
    FROM products p
    JOIN categories c ON p.category_id = c.Id
    ORDER BY p.Id DESC
")->fetchAll();

?>

<?php include "../../includes/header.php"; ?>
<div class="function-container">
    <button onclick="location.href='add_product.php'">Tambah Produk</button>
    <button onclick="location.href='manage_category.php'">Atur Kategori</button>
</div>

<div class="Product">
    <?php foreach ($products as $product): ?>
        <div class="card-product">
            <img src="<?= $product['image_path'] ?>" alt="<?= $product['Name'] ?>" class="product-image">
            <h3><?= $product['Name'] ?></h3>
            <p>Kategori: <?= $product['Category'] ?></p>
            <p class="price">Rp. <?= number_format($product['Price'], 0, ',', '.') ?></p>
            <p>Stock: <?= $product['Stock'] ?></p>
            <button onclick="editProduct(<?= $product['Id'] ?>, '<?= addslashes($product['Name']) ?>', <?= $product['Stock'] ?>, <?= $product['Price'] ?>)" class="back-button">Edit</button>
            <button onclick="deleteProduct(<?= $product['Id'] ?>)" class="back-button">Hapus</button>
        </div>
    <?php endforeach; ?>
</div>

<?php include "../../includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteProduct(productId) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('delete_product.php?id=' + productId, {
                        method: 'POST' // Menggunakan POST jika DELETE tidak didukung
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Your file has been deleted.",
                                icon: "success"
                            });
                            location.reload(); // Muat ulang halaman untuk melihat perubahan
                        } else {
                            alert('Gagal menghapus produk');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    }

    function editProduct(productId, name, stock, price) {
        Swal.fire({
            title: "Edit Product",
            html: `
                <input id="product-name" class="swal2-input" placeholder="Name" value="${name}">
                <input id="product-stock" class="swal2-input" type="number" placeholder="Stock" value="${stock}" min="0">
                <input id="product-price" class="swal2-input" type="number" placeholder="Price" value="${price}" min="0">
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Save',
            preConfirm: () => {
                const updatedName = document.getElementById('product-name').value;
                const updatedStock = document.getElementById('product-stock').value;
                const updatedPrice = document.getElementById('product-price').value;

                // Validasi input
                if (!updatedName || updatedStock < 0 || updatedPrice < 0) {
                    Swal.showValidationMessage(`Please enter valid values.`);
                }

                return {
                    name: updatedName,
                    stock: updatedStock,
                    price: updatedPrice
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const { name, stock, price } = result.value;
                
                // Kirim data ke server
                fetch('edit_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: productId, name: name, stock: stock, price: price })
                })
                .then(response => {
                    if (response.ok) {
                        Swal.fire({
                            title: "Updated!",
                            text: "Product has been updated.",
                            icon: "success"
                        });
                        location.reload(); // Muat ulang halaman untuk melihat perubahan
                    } else {
                        alert('Gagal mengupdate produk');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }
</script>
