<?php
require "../../includes/connectdb.php";

$categories = $database->query("SELECT * FROM categories order by Id desc");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];

    // Proses upload gambar
    $targetDir = "../../uploads/";
    $fileName = basename($_FILES['image']['name']);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Validasi format gambar
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            // Simpan informasi produk ke database
            $imagePath = "/uploads/" . $fileName;
            $stmt = $database->prepare("INSERT INTO products (Name, Price, image_path, category_id, Stock) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $price, $imagePath, $category_id, $stock]);
            echo "Product added successfully!";
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "Only JPG, JPEG, PNG, & GIF files are allowed.";
    }
}
include "../../includes/header.php";
?>

<form action="add_product.php" method="POST" enctype="multipart/form-data" class="form-add-product">
    <label for="name">Product Name:</label>
    <input type="text" name="name" id="name" required>

    <label for="price">Price:</label>
    <input type="number" name="price" id="price" required>
    
    <label for="stock">Stock:</label>
    <input type="number" name="stock" id="stock" required>

    <label for="category">Category:</label>
    <select name="category_id" id="category" required>
        <option value="" disabled selected>Select Category</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['Id'] ?>"><?= $category['Category'] ?></option>
        <?php endforeach; ?>
    </select>

    <label for="image">Product Image:</label>
    <input type="file" name="image" id="image" accept="image/*" required>

    <button type="submit">Add Product</button>
</form>

<?php
include "../../includes/footer.php";
?>