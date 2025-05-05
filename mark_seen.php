<?php
include("db_connect.php");

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$chat_user_id = isset($_POST['chat_user_id']) ? intval($_POST['chat_user_id']) : 0;

if ($user_id && $chat_user_id) {
    $query = "UPDATE messages 
              SET seen = 1 
              WHERE receiver_id = '$user_id' 
              AND sender_id = '$chat_user_id' 
              AND seen = 0";
    $conn->query($query);
}
?>
