<?php
class WebsiteFeedback

{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addFeedback($userId, $rating, $comment)
    {
        $insertQuery = "INSERT INTO website_feedback (user_id, rating, comment, reg_date) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt = mysqli_prepare($this->conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "iis", $userId, $rating, $comment);

        if (mysqli_stmt_execute($stmt)) {
            echo "Feedback added successfully!";
        } else {
            echo "Error adding feedback: " . mysqli_error($this->conn);
        }

        mysqli_stmt_close($stmt);
    }

    public function getAllFeedback()
    {
        $selectQuery = "SELECT * FROM website_feedback";
        $result = mysqli_query($this->conn, $selectQuery);

        if ($result) {
            $feedback = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $feedback;
        } else {
            echo "Error retrieving feedback: " . mysqli_error($this->conn);
            return [];
        }
    }

    public function deleteFeedback($feedbackId)
    {
        $deleteQuery = "DELETE FROM website_feedback WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $feedbackId);

        if (mysqli_stmt_execute($stmt)) {
            echo "Feedback deleted successfully!";
        } else {
            echo "Error deleting feedback: " . mysqli_error($this->conn);
        }

        mysqli_stmt_close($stmt);
    }
}
?>
