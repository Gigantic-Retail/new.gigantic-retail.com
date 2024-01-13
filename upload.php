<?php
require 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

$uploadStatus = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['productName'];
    $productPrice = $_POST['productPrice'];
    $productDescription = $_POST['productDescription'];
    $productCategory = $_POST['productCategory'];
    $productImage = $_FILES['productImage'];

    // Validation
    if (!empty($productName) && !empty($productPrice) && !empty($productDescription) && $productImage['error'] == 0) {
        // Initialize Google Cloud Storage client with token
        $storage = new StorageClient([
            'keyFilePath' => '/var/www/storage/token.json' // outside of web root is secure
        ]);
        $bucket = $storage->bucket('gr-web');

        // Upload the file to the bucket
        $file = fopen($productImage['tmp_name'], 'r');
        $object = $bucket->upload($file, [
            'name' => 'products/' . $productImage['name']
        ]);

        $uploadStatus = "Product '$productName' uploaded successfully!";
    } else {
        $uploadStatus = "Error: Invalid product data or image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Product Management</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; overflow: hidden; }
        form { background: #fff; padding: 15px; border-radius: 5px; box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 8px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #ddd; }
        input[type="submit"] { display: block; width: 100%; padding: 10px; border: none; background-color: #333; color: white; cursor: pointer; border-radius: 4px; }
        input[type="submit"]:hover { background-color: #555; }
        .status { margin-top: 20px; padding: 10px; background-color: #eaeaea; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Product</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="productName">Product Name:</label>
            <input type="text" id="productName" name="productName" required>

            <label for="productPrice">Product Price:</label>
            <input type="number" id="productPrice" name="productPrice" required>

            <label for="productDescription">Description:</label>
            <textarea id="productDescription" name="productDescription" required></textarea>

            <label for="productCategory">Category:</label>
            <select id="productCategory" name="productCategory">
                <option value="electronics">Electronics</option>
                <option value="clothing">Clothing</option>
                <option value="accessories">Accessories</option>
                <!-- Add more categories as needed -->
            </select>

            <label for="productImage">Product Image:</label>
            <input type="file" id="productImage" name="productImage" required>

            <input type="submit" value="Add Product">
        </form>

        <?php if ($uploadStatus != ''): ?>
            <div class="status">
                <?php echo $uploadStatus; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
