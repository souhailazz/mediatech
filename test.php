<?php
// Include or require the file containing your Product class definition
require_once 'Product.php';

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "mediatech";

// Create a connection to the database
$conn = mysqli_connect($servername, $username, $password, $database);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create an instance of the Product class
$product = new Product($conn);

// Retrieve all products
$sql = "SELECT * FROM product";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $currentProduct = new Product($conn);
        $currentProduct->setId($row['id']);
        $currentProduct->setName($row['name']);
        $currentProduct->setDescription($row['description']);
        $currentProduct->setPrice($row['price']);
        $currentProduct->setQuantity($row['quantity']);
        $currentProduct->setImageUrl($row['image_url']);
        $currentProduct->setRegDate($row['reg_date']);
        $currentProduct->displayProductHTML();
    }
} else {
    echo "No products found.";
}

// Close the database connection
mysqli_close($conn);
?>
