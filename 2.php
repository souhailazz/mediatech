<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "mediatech";
include 'user.php';
include 'order.php';
$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$createDbQuery = "CREATE DATABASE IF NOT EXISTS mediatech";
if (!mysqli_query($conn, $createDbQuery)) {
    die("Error creating database: " . mysqli_error($conn));
}
mysqli_select_db($conn, $database);
$query = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    email VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$query2 = "CREATE TABLE IF NOT EXISTS product (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT(6) NOT NULL,
    image_url VARCHAR(255),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$query3 = "CREATE TABLE IF NOT EXISTS orders (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    product_id INT(6) UNSIGNED,
    quantity INT(6) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    full_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES product(id),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$query4 = "CREATE TABLE IF NOT EXISTS favorites (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usermail VARCHAR(255) NOT NULL,
    product_id INT(6) UNSIGNED,
    FOREIGN KEY (usermail) REFERENCES users(email),
    FOREIGN KEY (product_id) REFERENCES product(id),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$query5 = "CREATE TABLE IF NOT EXISTS website_feedback (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255)  NOT NULL,
    rating INT(1) NOT NULL,
    comments TEXT,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!mysqli_query($conn, $query5)) {
    die("Error creating website_feedback table: " . mysqli_error($conn));
}

if (!mysqli_query($conn, $query4)) {
    die("Error creating favorites table: " . mysqli_error($conn));
}
if (!mysqli_query($conn, $query3)) {
    die("Error creating table: " . mysqli_error($conn));
}
if (!mysqli_query($conn, $query2)) {
    die("Error creating table: " . mysqli_error($conn));
}

if (!mysqli_query($conn, $query)) {
    die("Error creating table: " . mysqli_error($conn));
}
if (!mysqli_query($conn, $query5)) {
    die("Error creating website_feedback table: " . mysqli_error($conn));
}
include 'Product.php';
$productHandler = new Product($conn);
include 'favorites.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["addToFavorites"])) {
        $favoritesHandler = new favorites($conn);
        $productId = $_POST["productId"];
        $userMail = $_SESSION['email'];

        $favoritesHandler->addToFavorites($userMail, $productId);
    }
}
include 'WebsiteFeedback.php';
$feedbackHandler = new WebsiteFeedback($conn);
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitFeedback'])) {
    $fullname = isset($_POST["name"]) ? $_POST["name"] : '';
    $email = isset($_POST["email"]) ? $_POST["email"] : '';
    $rating = isset($_POST["rating"]) ? $_POST["rating"] : '';
    $comments = isset($_POST['comments']) ? mysqli_real_escape_string($conn, $_POST['comments']) : '';

    $feedbackHandler->addFeedback($fullname, $email, $rating, $comments);
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: homepage.php");
    exit();
}
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$userHandler = new User($conn);

if (isset($_POST['deleteUser'])) {
    $deleteResult = $userHandler->deleteUser();

    if ($deleteResult) {
        header("Location: homepage.php");
        exit();
    } else {
        echo "Error deleting user.";
    }
}

if (isset($_POST['editUser'])) {
    $newName = $_POST['newName'];
    $newPassword = $_POST['newPassword'];
    $editResult = $userHandler->editUser($newName, $newPassword);
    if ($editResult) {
        echo "User information updated successfully.";
    } else {
        echo "Error updating user information.";
    }
}
if (isset($_POST['buybutton'])) {
    $productIdToBuy = $_POST['productId'];
    $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
    if (!$productHandler->isProductBought($productIdToBuy, $userId)) {
        $productHandler->buyProduct($productIdToBuy, $userId);
    }
}
if (isset($_POST['favbutton'])) {
    $productIdToAddToFavorites = $_POST['productId'];
    $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

    if (!$favoritesHandler->isProductInFavorites($productIdToAddToFavorites, $userId)) {
        $favoritesHandler->addToFavorites($userId, $productIdToAddToFavorites);
    }
}
if (isset($_POST['editProduct'])) {
    $productId = $_POST['editProduct'];
    $newName = mysqli_real_escape_string($conn, $_POST['newName']);
    $newDescription = mysqli_real_escape_string($conn, $_POST['newDescription']);
    $newPrice = $_POST['newPrice'];
    $newQuantity = $_POST['newQuantity'];
    $newImageUrl = mysqli_real_escape_string($conn, $_POST['newImageUrl']);
    $productHandler->editProduct($productId, $newName, $newDescription, $newPrice, $newQuantity, $newImageUrl);
}

if (isset($_POST['deleteProduct'])) {
    $productId = $_POST['deleteProduct'];
    $productHandler->deleteProduct($productId);
}
if (isset($_POST['addProduct'])) {
    if (isset($_POST['productName'], $_POST['productDescription'], $_POST['productPrice'], $_POST['productQuantity'])) {
        $productName = mysqli_real_escape_string($conn, $_POST['productName']);
        $productDescription = mysqli_real_escape_string($conn, $_POST['productDescription']);
        $productPrice = $_POST['productPrice'];
        $productQuantity = $_POST['productQuantity'];
        $productImageUrl = mysqli_real_escape_string($conn, $_POST['productImageUrl']);
        $productExistsQuery = "SELECT id FROM product WHERE name = ?";
        $stmt = mysqli_prepare($conn, $productExistsQuery);
        mysqli_stmt_bind_param($stmt, "s", $productName);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 0) {
            $productHandler->addProduct($productName, $productDescription, $productPrice, $productQuantity, $productImageUrl);
        } else {
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Please fill out all required fields.";
    }
    
}
if (isset($_SESSION['id'])) {
    $orderHandler = new Order($conn);

    if (isset($_GET['orders']) && $_GET['orders'] === 'true') {
        $userId = $_SESSION['id'];
        $userOrders = $orderHandler->getOrdersByUserId($userId);

        echo '<div id="orders-content" class="orders-list">';
        if ($userOrders && mysqli_num_rows($userOrders) > 0) {
            while ($order = mysqli_fetch_assoc($userOrders)) {
                
            }
        } else {
            echo '<p>No orders found.</p>';
        }
        echo '</div>';  
    }
}



?>
<?php
$displayOrders = isset($_GET['orders']) && $_GET['orders'] === 'true' && basename($_SERVER['PHP_SELF']) === '2.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/3639809534.js" crossorigin="anonymous"></script>
    <title>MediaTech admin</title>
</head>
<body>
    <header>
        <h1>Mediatech</h1>
    </header>

    <nav id="navbar">
    
    <a href="#" onclick="changeContent('services')">Services</a>
    <a href="#" onclick="changeContent('contact')">Contact</a>
    <a href="#" onclick="changeContent('profile')">Profile</a>
    <a href="?orders=true" <?php if (isset($_GET['orders']) && $_GET['orders'] === 'true') echo 'class="active"'; ?> onclick="changeContent('orders')">order</a>
    <a href="?logout=true"><i class="fa-solid fa-arrow-right"></i> Logout</a>
</nav>



    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="topnav" style="margin-top: 15px";>
        <input type="text" name="search" placeholder="exemple: Gpro,zowie...">
        <button type="submit" name="searchButton"><i class="fa-solid fa-magnifying-glass"></i>  Search</button>
    </div>
</form>
<?php
$query = "SELECT type FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $userType);
    if (mysqli_stmt_fetch($stmt)) {
        $_SESSION['usertype'] = $userType;
    }
    mysqli_stmt_close($stmt); 
    $is_admin = isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'admin';

?>
<div class="content">
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
                    echo '<button type="submit" name="addToFavorites" onclick="addToFavorites(\'' . $row['name'] . '\')"> <i class="fa-solid fa-heart"></i> Add to Favorites</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo "No products found.";
            }

            if (isset($_POST['searchButton'])) {
                $searchQuery = mysqli_real_escape_string($conn, $_POST['search']);
                $sql = "SELECT * FROM product WHERE name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%'";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $showProductSection = false;
                    echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
                    echo '<input type="hidden" name="productId" value="' . $row['id'] . '">';
                    echo '<img src="' . $row['image_url'] . '" alt="' . $row['name'] . '">';
                    echo '<h3>' . $row['name'] . '</h3>';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '<p class="price">' . $row['price'] . 'DH</p>';
                    echo '<p class="quantity">Available Quantity: ' . $row['quantity'] . '</p>';
                    echo '<button type="submit" name="buybutton">Buy</button>';
                    echo '<button type="submit" name="addToFavorites" onclick="addToFavorites(\'' . $row['name'] . '\')"> <i class="fa-solid fa-heart"></i> Add to Favorites</button>';
                    echo '</form>';
                } else {
                    echo "No products found.";
                }
            }
            ?>
            <?php if ($is_admin): ?>
                <div class="product-card">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="text" name="productName" placeholder="Product Name" required>
                        <textarea name="productDescription" placeholder="Product Description" required></textarea>
                        <input type="number" name="productPrice" placeholder="Product Price" step="0.01" required>
                        <input type="number" name="productQuantity" placeholder="Product Quantity" required>
                        <input type="text" name="productImageUrl" placeholder="Product Image URL">
                        <button type="submit" name="addProduct"><i class="fa-solid fa-plus"></i>Add Product</button>

                        <button type="button" onclick="deleteProduct(<?php echo isset($productId) ? $productId : 0; ?>)"> <i class="fa-solid fa-trash"></i> Delete</button>
                        <button type="button" onclick="editProduct(<?php echo isset($productId) ? $productId : 0; ?>)"> <i class="fa-regular fa-pen-to-square"></i> Edit</button>
                    </form>
                </div>
            <?php endif; ?>
            <script>
            </script>
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
        <div id="profile-content" style="display: none; color:white">
            <h2>User Profile</h2>
            <?php
            if (isset($_SESSION['email'])) {
                echo '<p>Email: ' . $_SESSION['email'] . '</p>';
                echo ' <p> name : ' . $_SESSION['firstname'] . '</p>';
                echo ' <p> lastname : ' . $_SESSION['lastname'] . '</p>';
            } else {
                echo '<p>No user information available. </p>';
            }
            ?>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="deleteUserId" value="<?php echo $_SESSION['email']; ?>">
                <button type="submit" name="deleteUser">Delete Account</button>
            </form>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="text" name="newName" placeholder="New Name" required>
                <input type="password" name="newPassword" placeholder="New Password" required>
                <?php  ?>
                <button type="submit" name="editUser">Edit Account</button>
            </form>
        





            
            <div id="orders-content" class="orders-list">
    <?php
    if ($displayOrders) {
        if (isset($_SESSION['id'])) {
            $userId = $_SESSION['id'];
            $userOrders = $orderHandler->getOrdersByUserId($userId);

            if ($userOrders && mysqli_num_rows($userOrders) > 0) {
                while ($order = mysqli_fetch_assoc($userOrders)) {
                    $productId = $order['product_id'];
                    $productQuery = "SELECT * FROM product WHERE id = $productId";
                    $productResult = mysqli_query($conn, $productQuery);
                    $product = mysqli_fetch_assoc($productResult);

                    echo '<div class="order-box">';
                    echo '<div class="order-card">';
                    echo '<p>Order ID: ' . $order['id'] . '</p>';
                    echo '<p>Product ID: ' . $order['product_id'] . '</p>';
                    echo '<p>Product Name: ' . $product['name'] . '</p>';
                    echo '<p>Quantity: ' . $order['quantity'] . '</p>';
                    echo '<p>Price: ' . $order['price'] . '</p>';
                    echo '<p>Full Price: ' . $order['full_price'] . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No orders found.</p>';
            }
        } else {
            echo '<p>Please log in to view your orders.</p>';
        }
    } else {
        echo '<p>No orders found.</p>';
    }
    ?>
        </div>
 </div>
   
    
    
                <button id="feedbackButton" onclick="showFeedbackForm()">Give Feedback </button>
                <video id="video-background" autoplay muted loop>
                    <source src="pexels_videos_2759477 (2160p).mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div id="feedbackForm" style="display: none;">
                    <h2>Website Feedback</h2>
                    <form method="post" action="2.php">
                        <label for="name">Your Name:</label>
                        <input type="text" name="name" required>
                        <label for="email">Your Email:</label>
                        <input type="email" name="email" required>
                        <label for="rating">Rating:</label>
                        <select name="rating" required>
                            <option value="5">&#9733;&#9733;&#9733;&#9733;&#9733;</option>
                            <option value="4">&#9733;&#9733;&#9733;&#9733;</option>
                            <option value="3">&#9733;&#9733;&#9733;</option>
                            <option value="2">&#9733;&#9733;</option>
                            <option value="1">&#9733;</option>
                        </select>
                        <label for="comments">Comments:</label>
                        <textarea name="comments" rows="4" required></textarea>
                        <button type="submit" name="submitFeedback">Submit Feedback</button>
                    </form>
                </div>
                
                <footer>
                    <p>Follow us on social media:</p>
                    <a href="https://web.facebook.com/saad.saadoonix/" target="_blank" style="text-decoration:none ">
                        <i class="fab fa-facebook" style="color: #f0ece5; "></i> facebook
                    </a>
                    <a href="https://twitter.com/Thorfinnnn69" target="_blank" style="text-decoration:none">
                        <i class="fab fa-twitter" style="color: #f0ece5;"></i> twitter
                    </a>
                    <a href="https://www.instagram.com/saadjdoua/" target="_blank" style="text-decoration:none">
                        <i class="fab fa-instagram" style="color: #f0ece5;"></i> instagram
                    </a>
                    <p>&copy; 2024 mediatech copyrights</p>
                    <div class="elementor-element elementor-element-4275d59 e-con-full e-flex e-con e-child" data-id="4275d59"
                        data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;,&quot;container_type&quot;:&quot;flex&quot;}">
                        <div class="elementor-element elementor-element-27cb6ae elementor-icon-list--layout-inline elementor-align-right elementor-mobile-align-center elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                            data-id="27cb6ae" data-element_type="widget" data-widget_type="icon-list.default">
                            <div class="elementor-widget-container">
                                <ul class="elementor-icon-list-items elementor-inline-items">
                                    <li class="elementor-icon-list-item elementor-inline-item">
                                        <!-- Additional elements or modifications can be added here -->
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </footer>
                
                <script>
               function setInitialState() {
    var ordersContent = document.getElementById('orders-content');
    var urlParams = new URLSearchParams(window.location.search);
    var ordersParam = urlParams.get('orders');

    // Hide ordersContent if the "orders" parameter is not present or set to 'false'
    if (ordersParam !== 'true') {
        ordersContent.style.display = 'none';
    }
}

// Function to change content based on the selected page
function changeContent(page) {
    var servicesContent = document.getElementById('services-content');
    var contactContent = document.getElementById('contact-content');
    var profileContent = document.getElementById('profile-content');
    var ordersContent = document.getElementById('orders-content');

    var newUrl = window.location.href.split('?')[0];

    switch (page) {
        case 'services':
            servicesContent.style.display = 'grid';
            contactContent.style.display = 'none';
            profileContent.style.display = 'none';
            ordersContent.style.display = 'none'; // Add this line
            break;
        case 'contact':
            servicesContent.style.display = 'none';
            contactContent.style.display = 'grid';
            profileContent.style.display = 'none';
            ordersContent.style.display = 'none'; // Add this line
            break;
        case 'profile':
            servicesContent.style.display = 'none';
            contactContent.style.display = 'none';
            profileContent.style.display = 'block';
            ordersContent.style.display = 'none'; // Add this line
            break;
        case 'orders':
            servicesContent.style.display = 'none';
            contactContent.style.display = 'none';
            profileContent.style.display = 'none';
            ordersContent.style.display = 'grid';
            if (!newUrl.includes('orders=true')) {
                newUrl += (newUrl.includes('?') ? '&' : '?') + 'orders=true';
            }
            break;
    }

    history.pushState({}, '', newUrl);
}

window.addEventListener('DOMContentLoaded', setInitialState);


// Set the initial state when the page loads
window.addEventListener('DOMContentLoaded', setInitialState);
                
                    function buyProduct(productName) {
                        alert('You bought ' + productName);
                    }
                
                    function addToFavorites(productName) {
                        alert('Added ' + productName + ' to Favorites');
                    }
                
                    function showFeedbackForm() {
                        var feedbackForm = document.getElementById('feedbackForm');
                        feedbackForm.style.display = 'block';
                    }
                
                    function toggleFeedbackForm() {
                        var feedbackForm = document.getElementById('feedbackForm');
                        feedbackForm.style.display = (feedbackForm.style.display === 'none' || feedbackForm.style.display === '') ? 'block' : 'none';
                    }
                </script>
    <style>
        #navbar a.active {
    background-color: #310000;
    color: white;
    /* Add any additional styles for the active state */
}
           .order-box {
    border: 1px solid #ccc;
    padding: 10px;
    margin-bottom: 10px;
}
        form {
    color: white;
}
#feedbackButton {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #161A30;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #feedbackButton:hover {
            background-color: #310000;}
            #feedbackForm {
            display: none;
            position: fixed;
            bottom: 0;
            right: 0;
            padding: 20px;
            background-color: #161A30;
            color: white;
        }
form label,
form input,
form select,
form textarea {
    color: white;
}

form button {
    color: white;
    background-color: #161A30; 
}
   body {
    display: flex;
    flex-direction: column;
    min-height: 100vh; 
    margin: 0;
}
main {
    flex: 1; 
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
footer {
    background-color: #161A30;
    color: white;
    text-align: center;
    padding: 20px 0;
    margin-top: auto;
}
footer p {
    margin: 10px 0;
}
footer a {
    text-decoration: none;
    color: white;
    margin: 0 30px;
}
footer i {
    font-size: 24px;
    color: blue;
    transition: color 0.2s ease;
}
footer a:hover i {
    color: #b6bbc4;
}
.elementor-icon-list-items {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
}
.elementor-icon-list-item {
    margin-right: 10px;
}
.elementor-icon-list-icon {
    display: inline-block;
    margin-right: 5px;
}
.elementor-icon-list-text {
    display: none;
}
.e-font-icon-svg {
    width: 20px;
    height: 20px;}
    
    form {
        max-width: 300px;
        margin: 0 auto;
    }

    input {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        box-sizing: border-box;
    }

    button {
        background-color: #161A30;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #310000;
    }
    .product-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin: 20px;
    }

    .product-card {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    .product-card img {
        max-width: 100%;
        height: auto;
    }

    .product-card h3 {
        margin: 10px 0;
    }

    .product-card .description {
        color: #555;
    }

    .product-card .price {
        font-weight: bold;
    }

    .product-card .quantity {
        margin-bottom: 10px;
    }

    .product-card button {
        background-color: #161A30;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
    }

    .product-card button:hover {
        background-color: #310000;
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
    </style>
</style>

</div>
</body>
</html>