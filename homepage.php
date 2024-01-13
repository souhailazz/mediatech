<?php
session_start();
include 'user.php';

$servername = "localhost";
$username = "root";
$password = "";
$database = "mediatech";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$userHandler = new User($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["signin"])) {
        $emailValue = $_POST["emailName"];
        $passwordValue = $_POST["passwordName"];

        $signInResult = $userHandler->signIn($emailValue, $passwordValue);

        if ($signInResult) {
            header("Location: 2.php");
            exit();
        } else {
            header("Location: homepage.php");
            exit();
        }
    } elseif (isset($_POST["signup"])) {
        $emailValue = $_POST["emailName"];
        $passwordValue = $_POST["passwordName"];
        $usernameValue = $_POST["signup-username"];
        $nameValue = $_POST["fullname"];

        $signUpResult = $userHandler->signUp($usernameValue, $nameValue, $emailValue, $passwordValue);

        if ($signUpResult) {
            echo "Sign-up successful!";
        } else {
            echo "Error: Sign-up failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>MediaTech</title>
</head>

<style>
     body {
        margin: 0;
        overflow: hidden;
    }

    #video-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
    }

    .content {
        position: relative;
        z-index: 1;
    }
    .product-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around; 
    }

    .product-card {
        width: 300px;
        margin: 10px;
        text-align: center;
    }
    
</style>
<body>
<video id="video-background" autoplay muted loop>
        <source src="pexels_videos_2759477 (2160p).mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <header>
        <h1>Mediatech</h1>
    </header>

    <nav id="navbar">
        <a href="#" onclick="changeContent('signin')">Sign In</a>
        <a href="#" onclick="changeContent('signup')">Sign Up</a>
        <a href="#" onclick="changeContent('services')">Services</a>
        <a href="#" onclick="changeContent('contact')">Contact</a>
    </nav>

    <div class="content">
        <div id="signin-form" class="auth-form">
            <h2>Sign In</h2>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <label for="username">Username:</label>
                <input type="email" id="username" name="emailName" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="passwordName" required>

                <button type="submit" name="signin">Sign In</button>
            </form>
        </div>

        <div id="signup-form" class="auth-form" style="display: none;">
            <h2>Sign Up</h2>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="emailName" required>

                <label for="signup-username">Username:</label>
                <input type="text" id="signup-username" name="signup-username" required>

                <label for="signup-password">Password:</label>
                <input type="password" id="signup-password" name="passwordName" required>

                <button type="submit" name="signup">Sign Up</button>
            </form>
        </div>
</div>

    <div id="page-content">
        <div id="services-content" class="product-list">
            <?php
            $result = mysqli_query($conn, "SELECT * FROM product");

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product-card">';
                    echo '<img src="' . $row['image_url'] . '" alt="' . $row['name'] . '">';
                    echo '<h3>' . $row['name'] . '</h3>';
                    echo '<p class="description">' . $row['description'] . '</p>';
                    echo '<p class="price">Price: ' . $row['price'] . ' DH</p>';
                    echo '<p class="quantity">Available Quantity: ' . $row['quantity'] . '</p>';
                    echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
                    echo '<input type="hidden" name="productId" value="' . $row['id'] . '">';
                    echo '<button type="submit" name="buybutton">Buy</button>';
                    echo '<button type="button" onclick="addToFavorites(\'' . $row['name'] . '\')">Add to Favorites</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo "No products found.";
            }
            ?>
</div>
            
            </div>

            <div id="contact-content" class="contact-info" style="display: none;">
                <div class="contact-card">
                    <img src="souhail.jpg" alt="Souhail Azzimani">
                    <h3>Souhail Azzimani</h3>
                    <p>Email: souhailazzimani@gmail.com</p>
                    <p>Phone: 0767442245</p>
                </div>
                <div class="contact-card">
                    <img src="saad.jpg" alt="Saad Jdoua">
                    <h3>Saad Jdoua</h3>
                    <p>Email: saadjdoua11@gmail.com</p>
                    <p>Phone: 070760</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        
        function changeContent(page) {
        var signinForm = document.getElementById('signin-form');
        var signupForm = document.getElementById('signup-form');
        var servicesContent = document.getElementById('services-content');
        var contactContent = document.getElementById('contact-content');
        var profileContent = document.getElementById('profile-content');

        switch (page) {
            case 'signin':
                signinForm.style.display = 'block';
                signupForm.style.display = 'none';
                servicesContent.style.display = 'none';
                contactContent.style.display = 'none';
                if (profileContent) profileContent.style.display = 'none';
                break;
            case 'signup':
                signinForm.style.display = 'none';
                signupForm.style.display = 'block';
                servicesContent.style.display = 'none';
                contactContent.style.display = 'none';
                if (profileContent) profileContent.style.display = 'none';
                break;
            case 'services':
                signinForm.style.display = 'none';
                signupForm.style.display = 'none';
                servicesContent.style.display = 'grid';
                contactContent.style.display = 'none';
                if (profileContent) profileContent.style.display = 'none';
                break;
            case 'contact':
                signinForm.style.display = 'none';
                signupForm.style.display = 'none';
                servicesContent.style.display = 'none';
                contactContent.style.display = 'grid';
                if (profileContent) profileContent.style.display = 'none';
                break;
        }
    }

    changeContent('signin');

        

        function buyProduct(productName) {
            alert('You bought ' + productName);
        }

        function addToFavorites(productName) {
        <?php
        if (!isset($_SESSION['email'])) {
            echo "alert('Please sign in to add to Favorites');";
            echo "changeContent('signin');"; 
        } else {
            echo "alert('Added ' + productName + ' to Favorites');";
           
        }
        ?>
    }
    </script>
    
</body>
</html>
