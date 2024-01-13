<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "mediatech";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailValue = $_POST["emailName"];
    $passwordValue = $_POST["passwordName"];

    $selectQuery = "SELECT * FROM users WHERE email='$emailValue'";
    $result = mysqli_query($conn, $selectQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Use password_verify to check if the entered password matches the hashed password
        if (password_verify($passwordValue, $row['password'])) {
           $_SESSION['mail']= $emailValue ;
           $sql="SELECT id FROM user WHERE email=  $emailValue ";
           $result = mysqli_query($conn, $sql);
           if ($row = mysqli_fetch_assoc($result)){
            $_SESSION['id']=$row['id'];
           }
            header("Location: 2.php");
            exit();
        } else {
            // Incorrect password
            header("Location: 1.php");
            exit();
        }
    } else {
        // Email not found
        header("Location: 1.php?error=email_not_found");
        exit();
    }
}
?>

mysqli_close($conn);
?>
