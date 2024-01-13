<?php
class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function signIn($email, $password)
    {
        $selectQuery = "SELECT * FROM users WHERE email=?";
        $stmt = mysqli_prepare($this->conn, $selectQuery);

        mysqli_stmt_bind_param($stmt, "s", $email);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                if (password_verify($password, $row['password'])) {
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['firstname'] = $row['firstname'];
                    $_SESSION['lastname'] = $row['lastname'];
                    $_SESSION['id'] = $row['id'];
                    $iduser = $_SESSION['id'];
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function signUp($firstname, $lastname, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertQuery = "INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $insertQuery);

        mysqli_stmt_bind_param($stmt, "ssss", $firstname, $lastname, $email, $hashedPassword);

        if (mysqli_stmt_execute($stmt)) {
            return true;
        } else {
            return false;
        }
    }
    public function deleteUser()
{
    $deleteQuery = "DELETE FROM users WHERE email = ?";
    $stmt = mysqli_prepare($this->conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
    if (mysqli_stmt_execute($stmt)) {
        session_destroy();
        return true;
    } else {
        return false;
    }
}

public function editUser($newName, $newPassword)
{
    $newName = mysqli_real_escape_string($this->conn, $newName);
    $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    if (!empty($newName) && !empty($newPassword)) {
        $updateQuery = "UPDATE users SET firstname=?, password=? WHERE email=?";

        $stmt = mysqli_prepare($this->conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "sss", $newName, $newPassword, $_SESSION['email']);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['firstname'] = $newName;
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
}


?>
