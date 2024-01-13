<?php
class Order
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function placeOrder($userId, $productId, $quantity, $price, $fullPrice)
    {
        $insertOrderQuery = "INSERT INTO orders (user_id, product_id, quantity, price, full_price) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $insertOrderQuery);
        mysqli_stmt_bind_param($stmt, "iiidd", $userId, $productId, $quantity, $price, $fullPrice);

        if (mysqli_stmt_execute($stmt)) {
            // Update product quantity in the product table
            $this->updateProductQuantity($productId, $quantity);

            echo "Order placed successfully!";
        } else {
            echo "Error placing order: " . mysqli_error($this->conn);
        }
    }

    private function updateProductQuantity($productId, $quantity)
    {
        $updateQuantityQuery = "UPDATE product SET quantity = quantity - ? WHERE id = ?";
        
        $stmt = mysqli_prepare($this->conn, $updateQuantityQuery);
        mysqli_stmt_bind_param($stmt, "ii", $quantity, $productId);

        if (!mysqli_stmt_execute($stmt)) {
            echo "Error updating product quantity: " . mysqli_error($this->conn);
        }
    }

    public function getOrdersByUserId($userId)
    {
        $selectOrdersQuery = "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC";
        $stmt = mysqli_prepare($this->conn, $selectOrdersQuery);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }


    public function createOrder($userId, $productId, $quantity, $price, $fullPrice)
    {
        $insertOrderQuery = "INSERT INTO orders (user_id, product_id, quantity, price, full_price) 
                             VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $insertOrderQuery);
        mysqli_stmt_bind_param($stmt, "iiidd", $userId, $productId, $quantity, $price, $fullPrice);

        if (mysqli_stmt_execute($stmt)) {
            // Update product quantity in the product table
            $this->updateProductQuantity($productId, $quantity);

            return mysqli_insert_id($this->conn);
        } else {
            echo "Error creating order: " . mysqli_error($this->conn);
            return false;
        }
    }
}
?>
