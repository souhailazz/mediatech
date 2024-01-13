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