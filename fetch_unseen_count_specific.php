<?php
header('Content-Type: application/json');

include 'admin.php';
$admin = new Admin();
include("db_connect.php");

// Assuming the user's session ID is stored in the session
$currentUserId = $_SESSION['user_id']; // Adjust this according to your session setup

$unseenCounts = [];
$query = "SELECT sender_id, COUNT(*) AS unseen_count FROM messages WHERE receiver_id = $currentUserId AND seen = 0 GROUP BY sender_id";
$result = $admin->conn->query($query);

while ($row = $result->fetch_assoc()) {
    $unseenCounts[$row['sender_id']] = $row['unseen_count'];
}

// Return unseen counts as JSON
echo json_encode($unseenCounts);
?>
