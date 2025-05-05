<?php
header('Content-Type: application/json');

include 'admin.php';
$admin = new Admin();
include("db_connect.php");

$currentidd = $_SESSION['user_id']; // Assuming this is the current user ID

// SQL query to get unread message count
$query = "SELECT COUNT(*) AS unread_messages FROM messages WHERE receiver_id = $currentidd AND seen = 0";
$result = $admin->conn->query($query);

$currentData = [];
if ($result) {
    $dataRow = $result->fetch_assoc();
    $currentData['unread_messages'] = $dataRow['unread_messages'];
} else {
    $currentData['unread_messages'] = 0; // Default to 0 if there's an error
}

// Output JSON data
echo json_encode($currentData);
