<?php

class favorites
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addToFavorites($userMail, $productId)
    {
        $query = "INSERT INTO favorites (usermail, product_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $userMail, $productId); 
    
        if (mysqli_stmt_execute($stmt)) {
            echo "Product added to favorites successfully!";
        } else {
            echo "Error adding product to favorites: " . mysqli_error($this->conn);
        }
    
        mysqli_stmt_close($stmt);
    }
    
    public function isProductInFavorites($productId, $userId)
{
    $sql = "SELECT * FROM favorites WHERE usermail = ? AND product_id = ?";
    $stmt = mysqli_prepare($this->conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $userId, $productId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_num_rows($result) > 0;
}

}

?>
