<?php

class Product
{
    private $conn;
    private $id;
    private $name;
    private $description;
    private $price;
    private $quantity;
    private $imageUrl;
    private $regDate;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function getRegDate()
    {
        return $this->regDate;
    }
   

    // Getter methods...

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    public function setRegDate($regDate)
    {
        $this->regDate = $regDate;
    }
    

    public function getProductDetails($productId)
    {
        $sql = "SELECT * FROM product WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->quantity = $row['quantity'];
            $this->imageUrl = $row['image_url'];
            $this->regDate = $row['reg_date'];
        }
    }

    public function displayProductHTML()
    {
        echo '<div class="product-details">';
        echo '<h2>' . $this->name . '</h2>';
        echo '<p>Description: ' . $this->description . '</p>';
        echo '<p>Price: ' . $this->price . '</p>';
        echo '<p>Quantity: ' . $this->quantity . '</p>';
        echo '<img src="' . $this->imageUrl . '" alt="' . $this->name . '">';
        echo '</div>';
    }
   

    public function editProduct($productId, $newName, $newDescription, $newPrice, $newQuantity, $newImageUrl)
    {
        $updateQuery = "UPDATE product SET 
                        name = ?,
                        description = ?,
                        price = ?,
                        quantity = ?,
                        image_url = ?
                        WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ssdiss", $newName, $newDescription, $newPrice, $newQuantity, $newImageUrl, $productId);

        if (mysqli_stmt_execute($stmt)) {
            echo "Product information updated!";
        } else {
            echo "Error updating product information: " . mysqli_error($this->conn);
        }
    }

    public function deleteProduct($productId)
    {
        $deleteQuery = "DELETE FROM product WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $productId);

        if (mysqli_stmt_execute($stmt)) {
            echo "Product deleted successfully!";
        } else {
            echo "Error deleting product: " . mysqli_error($this->conn);
        }
    }

    public function addProduct($name, $description, $price, $quantity, $imageUrl)
    {
        $insertQuery = "INSERT INTO product (name, description, price, quantity, image_url)
                        VALUES (?, ?, ?, ?, ?)";
    
        $stmt = mysqli_prepare($this->conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "ssdis", $name, $description, $price, $quantity, $imageUrl);

        if (mysqli_stmt_execute($stmt)) {
            echo "Product added successfully!";
        } else {
            echo "Error adding product: " . mysqli_error($this->conn);
        }
    }

    public function buyProduct($productId, $userId)
{
    $this->getProductDetails($productId); 

    if ($this->quantity > 0) {
        $this->quantity--;

        $totalPrice = $this->price;

        $insertOrderQuery = "INSERT INTO orders (user_id, product_id, quantity, price, full_price)
                            VALUES (?, ?, 1, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $insertOrderQuery);
        mysqli_stmt_bind_param($stmt, "iidd", $userId, $productId, $this->price, $totalPrice);

        if (mysqli_stmt_execute($stmt)) {
            echo "Product purchased successfully!";
        } else {
            echo "Error purchasing product: " . mysqli_error($this->conn);
        }

        $updateProductQuery = "UPDATE product SET quantity = ? WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $updateProductQuery);
        mysqli_stmt_bind_param($stmt, "ii", $this->quantity, $productId);

        if (!mysqli_stmt_execute($stmt)) {
            echo "Error updating product quantity: " . mysqli_error($this->conn);
        }
    } else {
        echo "Error: Product out of stock!";
    }
}

public function isProductBought($productId, $userId)
{
    $sql = "SELECT * FROM orders WHERE user_id = ? AND product_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}


    }


