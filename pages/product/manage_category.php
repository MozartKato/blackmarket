<?php
require "../../includes/connectdb.php";

// Proses tambah kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];
    $stmt = $database->prepare("INSERT INTO categories (Category) VALUES (?)");
    $stmt->execute([$category_name]);
}

// Proses hapus kategori
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    $stmt = $database->prepare("DELETE FROM categories WHERE Id = ?");
    $stmt->execute([$category_id]);
}

// Ambil semua kategori
$categories = $database->query("SELECT * FROM categories ORDER BY Id DESC")->fetchAll();
?>

<?php include "../../includes/header.php"; ?>

<h2>Kelola Kategori</h2>

<div class="form-add-category">
    <h3>Tambah Kategori</h3>
    <form action="manage_category" method="POST">
        <label for="category_name">Nama Kategori:</label>
        <input type="text" name="category_name" id="category_name" required>

        <button type="submit">Tambah Kategori</button>
    </form>
</div>
<button onclick="location.href='/pages/product/index'" class="back-button">Kembali</button>

<h3>Daftar Kategori</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Kategori</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= $category['Id'] ?></td>
                <td><?= $category['Category'] ?></td>
                <td>
                    <a href="?delete=<?= $category['Id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<?php include "../../includes/footer.php"; ?>